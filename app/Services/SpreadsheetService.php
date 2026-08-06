<?php

namespace App\Services;

use App\Models\Cabang;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SpreadsheetService
{
    /**
     * Sync data from a global Google Sheets CSV.
     *
     * @param string $csvUrl
     * @return bool
     */
    public function syncFromCsv(string $csvUrl)
    {
        try {
            $csvUrl = $this->normalizeUrl($csvUrl);
            $response = Http::timeout(10)->get($csvUrl);
            if (!$response->successful()) {
                Log::error("Failed to fetch global spreadsheet CSV. HTTP Status: " . $response->status());
                return false;
            }

            $csvData = $response->body();
            $rows = $this->parseCsvToRows($csvData);

            if (empty($rows)) {
                Log::warning("Spreadsheet CSV contains no data.");
                return false;
            }

            // Remove header row
            $header = array_shift($rows);
            $isRawList = (count($header) >= 30);
            
            if (count($header) < 20) {
                Log::error("Spreadsheet CSV columns mismatch. Expected >= 20 columns, found: " . count($header));
                return false;
            }

            // Group and aggregate rows by Cabang name (Pivot table grouped by Cabang)
            $aggregated = [];
            foreach ($rows as $row) {
                if (empty($row) || count($row) < 20) {
                    continue;
                }

                $nama = $isRawList ? trim($row[6] ?? '') : trim($row[0]);
                $namaUpper = strtoupper($nama);
                if (empty($nama) || str_contains($namaUpper, 'TOTAL') || str_contains($namaUpper, 'JUMLAH') || str_contains($namaUpper, 'AVERAGE')) {
                    continue;
                }

                if (!isset($aggregated[$nama])) {
                    $aggregated[$nama] = [
                        'target_tantangan' => 0,
                        'acv' => 0,
                        'target_reguler' => 0,
                        'lm' => 0,
                        'target_reguler_2026' => 0,
                        'act_ytd_jan_2026' => 0,
                        'target_perbulan_utk_2026' => 0,
                        'stock_2024' => 0,
                        'stock_2025' => 0,
                        'stock_2026' => 0,
                    ];
                }

                $aggregated[$nama]['target_tantangan'] += (int) $row[1];
                if ($isRawList) {
                    $aggregated[$nama]['acv'] += 1;
                    $aggregated[$nama]['target_reguler'] += (int) $row[5];
                    $aggregated[$nama]['lm'] += (int) $row[8];
                    $aggregated[$nama]['target_reguler_2026'] += (int) $row[12];
                    $aggregated[$nama]['act_ytd_jan_2026'] += (int) $row[13];
                    $aggregated[$nama]['target_perbulan_utk_2026'] += (int) ($row[16] ?? 0);
                    
                    $year = trim($row[30] ?? '');
                    if ($year === '2024') {
                        $aggregated[$nama]['stock_2024']++;
                    } elseif ($year === '2025') {
                        $aggregated[$nama]['stock_2025']++;
                    } elseif ($year === '2026') {
                        $aggregated[$nama]['stock_2026']++;
                    }
                } else {
                    $aggregated[$nama]['acv'] += (isset($row[2]) && (int)$row[2] > 0) ? (int)$row[2] : 0;
                    $aggregated[$nama]['target_reguler'] += (int) $row[5];
                    $aggregated[$nama]['lm'] += (int) $row[8];
                    $aggregated[$nama]['target_reguler_2026'] += (int) $row[12];
                    $aggregated[$nama]['act_ytd_jan_2026'] += (int) $row[13];
                    $aggregated[$nama]['target_perbulan_utk_2026'] += (int) ($row[16] ?? 0);
                    $aggregated[$nama]['stock_2024'] += (int) ($row[17] ?? 0);
                    $aggregated[$nama]['stock_2025'] += (int) ($row[18] ?? 0);
                    $aggregated[$nama]['stock_2026'] += (int) ($row[19] ?? 0);
                }
            }

            $processedNames = [];
            foreach ($aggregated as $nama => $data) {
                // Normalize branch name casing to match DB (e.g. PEKANBARU -> Pekanbaru)
                $dbCabang = Cabang::where('nama', 'like', $nama)->first();
                $actualNama = $dbCabang ? $dbCabang->nama : ucwords(strtolower($nama));
                $processedNames[] = $actualNama;

                $existing = $dbCabang ?: Cabang::where('nama', $actualNama)->first();
                if ($existing) {
                    $currentMonth = \Carbon\Carbon::now()->month;
                    $remainingMonths = 12 - $currentMonth + 1;
                    $diff = $existing->target_reguler_2026 - $data['act_ytd_jan_2026'];
                    $targetPerbulan = $remainingMonths > 0 ? (int)round($diff / $remainingMonths) : 0;

                    $existing->update([
                        'acv' => $data['acv'],
                        'lm' => $data['lm'],
                        'act_ytd_jan_2026' => $data['act_ytd_jan_2026'],
                        'target_perbulan_utk_2026' => $targetPerbulan,
                        'stock_2024' => $data['stock_2024'],
                        'stock_2025' => $data['stock_2025'],
                        'stock_2026' => $data['stock_2026'],
                    ]);
                } else {
                    $currentMonth = \Carbon\Carbon::now()->month;
                    $remainingMonths = 12 - $currentMonth + 1;
                    $diff = $data['target_reguler_2026'] - $data['act_ytd_jan_2026'];
                    $targetPerbulan = $remainingMonths > 0 ? (int)round($diff / $remainingMonths) : 0;

                    Cabang::create([
                        'nama' => $actualNama,
                        'target_tantangan' => $data['target_tantangan'],
                        'acv' => $data['acv'],
                        'target_reguler' => $data['target_reguler'],
                        'lm' => $data['lm'],
                        'target_reguler_2026' => $data['target_reguler_2026'],
                        'act_ytd_jan_2026' => $data['act_ytd_jan_2026'],
                        'target_perbulan_utk_2026' => $targetPerbulan,
                        'stock_2024' => $data['stock_2024'],
                        'stock_2025' => $data['stock_2025'],
                        'stock_2026' => $data['stock_2026'],
                    ]);
                }
            }

            // Clean up old dealers that are no longer in the spreadsheet
            if (!empty($processedNames)) {
                Cabang::whereNotIn('nama', $processedNames)->delete();
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error syncing from CSV: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Sync data for a specific branch (Cabang) from its individual spreadsheet URL.
     *
     * @param Cabang $cabang
     * @return bool
     */
    public function syncCabang(Cabang $cabang)
    {
        $salesUrl = $cabang->spreadsheet_url;
        $stockUrl = $cabang->stock_url;
        $lmUrl = $cabang->lm_url;

        if (empty($salesUrl)) {
            $cabang->update([
                'acv' => 0,
                'lm' => 0,
                'act_ytd_jan_2026' => 0,
                'target_perbulan_utk_2026' => $cabang->target_reguler_2026,
                'stock_2024' => 0,
                'stock_2025' => 0,
                'stock_2026' => 0,
            ]);
            return true;
        }

        // Auto-normalize URLs
        $salesUrl = $this->normalizeUrl($salesUrl);
        $stockUrl = $stockUrl ? $this->normalizeUrl($stockUrl) : null;
        $lmUrl = $lmUrl ? $this->normalizeUrl($lmUrl) : null;

        try {
            $isXlsxSync = str_contains($salesUrl, 'docs.google.com') && !str_ends_with($salesUrl, '.csv');
            if ($isXlsxSync) {
                $xlsxUrl = "https://docs.google.com/spreadsheets/d/17FFWWaFdeq6zK3eh56DSI6Qr8j6MgoQE/export?format=xlsx";
                if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $salesUrl, $matches)) {
                    $xlsxUrl = "https://docs.google.com/spreadsheets/d/{$matches[1]}/export?format=xlsx";
                }

                $response = Http::timeout(20)->get($xlsxUrl);
                if (!$response->successful()) {
                    Log::error("Failed to fetch {$cabang->nama} spreadsheet XLSX. HTTP Status: " . $response->status());
                    return false;
                }

                $tempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('xlsx_') . '.xlsx';
                file_put_contents($tempPath, $response->body());

                // Call python script to parse
                $pythonScript = base_path('app/Services/sync_pekanbaru.py');
                $command = "python " . escapeshellarg($pythonScript) . " " . escapeshellarg($tempPath) . " both";
                $output = shell_exec($command);
                
                // Cleanup temp file
                @unlink($tempPath);

                if (empty($output)) {
                    Log::error("{$cabang->nama} sync Python script returned empty output.");
                    return false;
                }

                $result = json_decode($output, true);
                if (isset($result['error'])) {
                    Log::error("{$cabang->nama} sync Python error: " . $result['error']);
                    return false;
                }

                // Parse LM from lm_url if set
                $lmVal = 0;
                if (!empty($lmUrl)) {
                    if ($lmUrl === $salesUrl) {
                        $lmVal = isset($result['lm']) ? (int)$result['lm'] : 0;
                    } else {
                        $lmXlsxUrl = $lmUrl;
                        if (str_contains($lmUrl, 'docs.google.com')) {
                            if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $lmUrl, $lmMatches)) {
                                $lmXlsxUrl = "https://docs.google.com/spreadsheets/d/{$lmMatches[1]}/export?format=xlsx";
                            }
                        }
                        $lmResponse = Http::timeout(20)->get($lmXlsxUrl);
                        if ($lmResponse->successful()) {
                            $lmTempPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('xlsx_lm_') . '.xlsx';
                            file_put_contents($lmTempPath, $lmResponse->body());
                            $lmCommand = "python " . escapeshellarg($pythonScript) . " " . escapeshellarg($lmTempPath) . " lm";
                            $lmOutput = shell_exec($lmCommand);
                            @unlink($lmTempPath);
                            if (!empty($lmOutput)) {
                                $lmResult = json_decode($lmOutput, true);
                                if (isset($lmResult['lm'])) {
                                    $lmVal = (int)$lmResult['lm'];
                                } elseif (isset($lmResult['acv'])) {
                                    $lmVal = (int)$lmResult['acv'];
                                }
                            }
                        } else {
                            Log::error("Failed to fetch {$cabang->nama} LM spreadsheet XLSX.");
                        }
                    }
                }

                $acvVal = (int)($result['acv'] ?? 0);
                $lmVal = !empty($lmUrl) && $lmUrl !== $salesUrl ? $lmVal : (int)($result['lm'] ?? 0);
                $lmFullVal = isset($result['lm_full']) && (int)$result['lm_full'] > 0 ? (int)$result['lm_full'] : $lmVal;
                
                // ACT YTD JAN 2026 = total STU LM + ACV (STU per hari ini)
                $actYtdVal = isset($result['act_ytd_jan_2026']) && (int)$result['act_ytd_jan_2026'] > 0 
                    ? (int)$result['act_ytd_jan_2026'] 
                    : ($lmFullVal + $acvVal);

                $currentMonth = \Carbon\Carbon::now()->month;
                $remainingMonths = 12 - $currentMonth + 1;
                $diff = $cabang->target_reguler_2026 - $actYtdVal;
                $targetPerbulan = $remainingMonths > 0 ? (int)round($diff / $remainingMonths) : 0;

                // Update database
                $cabang->update([
                    'acv' => $acvVal,
                    'stock_2024' => $result['stock_2024'],
                    'stock_2025' => $result['stock_2025'],
                    'stock_2026' => $result['stock_2026'],
                    'stock_breakdown' => $result['stock_breakdown'] ?? null,
                    'stu_breakdown' => $result['stu_breakdown'] ?? null,
                    'daily_performance' => $result['daily_performance'] ?? null,
                    'leasing_breakdown' => $result['leasing_breakdown'] ?? null,
                    'target_perbulan_utk_2026' => $targetPerbulan,
                    'lm' => $lmVal,
                    'act_ytd_jan_2026' => $actYtdVal,
                ]);

                return true;
            }

            // 1. Fetch & Parse Sales data
            $response = Http::timeout(10)->get($salesUrl);
            if (!$response->successful()) {
                Log::error("Failed to fetch sales spreadsheet CSV for '{$cabang->nama}'. HTTP Status: " . $response->status());
                return false;
            }
            $salesRows = $this->parseCsvToRows($response->body());
            if (empty($salesRows)) {
                Log::warning("Sales Spreadsheet CSV for '{$cabang->nama}' contains no data.");
                return false;
            }
            $salesHeader = array_shift($salesRows);
            $isRawSalesList = (count($salesHeader) >= 30);

            // Find column indices in Sales Header dynamically
            $acvIdx = -1;
            $lmIdx = -1;
            $actYtdIdx = -1;
            $tgtPerbulanIdx = -1;

            foreach ($salesHeader as $idx => $colName) {
                $cleaned = strtoupper(str_replace([' ', '_', '/'], '', trim($colName)));
                if ($cleaned === 'ACV') {
                    $acvIdx = $idx;
                } elseif ($cleaned === 'LM') {
                    $lmIdx = $idx;
                } elseif ($cleaned === 'ACTYTDJAN2026' || $cleaned === 'ACTYTD') {
                    $actYtdIdx = $idx;
                } elseif ($cleaned === 'TARGETPERBULAN' || $cleaned === 'TARGETPERBULANUTK2026' || $cleaned === 'TARGETPERBULAN2026') {
                    $tgtPerbulanIdx = $idx;
                }
            }

            $totalAcv = 0;
            $totalLm = 0;
            $totalActYtdJan2026 = 0;
            $totalTargetPerbulanUtk2026 = 0;

            foreach ($salesRows as $row) {
                if (empty($row) || count($row) < 10) {
                    continue;
                }
                $firstCol = strtoupper(trim($row[0]));
                if ($firstCol === '' || str_contains($firstCol, 'TOTAL') || str_contains($firstCol, 'JUMLAH') || str_contains($firstCol, 'AVERAGE')) {
                    continue;
                }

                if ($isRawSalesList) {
                    $totalAcv += 1;
                    if ($lmIdx !== -1) $totalLm += (int)($row[$lmIdx] ?? 0);
                    if ($actYtdIdx !== -1) $totalActYtdJan2026 += (int)($row[$actYtdIdx] ?? 0);
                    if ($tgtPerbulanIdx !== -1) $totalTargetPerbulanUtk2026 += (int)($row[$tgtPerbulanIdx] ?? 0);
                } else {
                    $totalAcv += ($acvIdx !== -1) ? (int)($row[$acvIdx] ?? 0) : (int)($row[2] ?? 0);
                    $totalLm += ($lmIdx !== -1) ? (int)($row[$lmIdx] ?? 0) : (int)($row[8] ?? 0);
                    $totalActYtdJan2026 += ($actYtdIdx !== -1) ? (int)($row[$actYtdIdx] ?? 0) : (int)($row[13] ?? 0);
                    $totalTargetPerbulanUtk2026 += ($tgtPerbulanIdx !== -1) ? (int)($row[$tgtPerbulanIdx] ?? 0) : (int)($row[16] ?? 0);
                }
            }

            // 2. Fetch & Parse Stock data
            $totalStock2024 = 0;
            $totalStock2025 = 0;
            $totalStock2026 = 0;

            $stockSourceUrl = $stockUrl ?: $salesUrl;
            if ($stockSourceUrl && !($stockSourceUrl === $salesUrl && $isRawSalesList)) {
                if ($stockSourceUrl === $salesUrl) {
                    $stockRows = $salesRows;
                    $stockHeader = $salesHeader;
                } else {
                    $res = Http::timeout(10)->get($stockSourceUrl);
                    if ($res->successful()) {
                        $stockRows = $this->parseCsvToRows($res->body());
                        $stockHeader = !empty($stockRows) ? array_shift($stockRows) : [];
                    } else {
                        Log::error("Failed to fetch stock spreadsheet CSV for '{$cabang->nama}' from URL '{$stockSourceUrl}'.");
                        $stockRows = [];
                        $stockHeader = [];
                    }
                }

                if (!empty($stockRows)) {
                    // Find column indices in Stock Header dynamically
                    $tahunRakitanIdx = -1;
                    $klasifikasiIdx = -1;
                    $stock2024Idx = -1;
                    $stock2025Idx = -1;
                    $stock2026Idx = -1;

                    foreach ($stockHeader as $idx => $colName) {
                        $cleaned = strtoupper(str_replace([' ', '_', '/'], '', trim($colName)));
                        if ($cleaned === 'TAHUNRAKITAN') {
                            $tahunRakitanIdx = $idx;
                        } elseif ($cleaned === 'KLASIFIKASITIPE' || $cleaned === 'KLASIFIKASI') {
                            $klasifikasiIdx = $idx;
                        } elseif ($cleaned === 'STOCK2024' || $cleaned === 'STOK2024') {
                            $stock2024Idx = $idx;
                        } elseif ($cleaned === 'STOCK2025' || $cleaned === 'STOK2025') {
                            $stock2025Idx = $idx;
                        } elseif ($cleaned === 'STOCK2026' || $cleaned === 'STOK2026') {
                            $stock2026Idx = $idx;
                        }
                    }

                    $stockBreakdown = [];
                    foreach ($stockRows as $row) {
                        if (empty($row) || count($row) < 5) {
                            continue;
                        }
                        $firstCol = strtoupper(trim($row[0]));
                        if ($firstCol === '' || str_contains($firstCol, 'TOTAL') || str_contains($firstCol, 'JUMLAH')) {
                            continue;
                        }

                        if ($tahunRakitanIdx !== -1 && !($stockSourceUrl === $salesUrl && $isRawSalesList)) {
                            $yearVal = trim($row[$tahunRakitanIdx] ?? '');
                            if (strpos($yearVal, '.') !== false) {
                                $yearVal = explode('.', $yearVal)[0];
                            }
                            if ($yearVal === '2024') {
                                $totalStock2024++;
                            } elseif ($yearVal === '2025') {
                                $totalStock2025++;
                            } elseif ($yearVal === '2026') {
                                $totalStock2026++;
                            }

                            // Parse classification
                            $classVal = $klasifikasiIdx !== -1 ? strtoupper(trim($row[$klasifikasiIdx] ?? '')) : strtoupper(trim($row[0] ?? ''));
                            if ($classVal !== '' && !str_starts_with($classVal, 'TOTAL') && !str_starts_with($classVal, 'JUMLAH')) {
                                $stockBreakdown[$classVal] = ($stockBreakdown[$classVal] ?? 0) + 1;
                            }
                        } else {
                            $totalStock2024 += ($stock2024Idx !== -1) ? (int)($row[$stock2024Idx] ?? 0) : (int)($row[17] ?? 0);
                            $totalStock2025 += ($stock2025Idx !== -1) ? (int)($row[$stock2025Idx] ?? 0) : (int)($row[18] ?? 0);
                            $totalStock2026 += ($stock2026Idx !== -1) ? (int)($row[$stock2026Idx] ?? 0) : (int)($row[19] ?? 0);
                        }
                    }
                }
            }

            // Parse LM from lm_url if set
            $lmVal = null;
            if (!empty($lmUrl)) {
                $normalizedLmUrl = $this->normalizeUrl($lmUrl);
                $lmResponse = Http::timeout(10)->get($normalizedLmUrl);
                if ($lmResponse->successful()) {
                    $lmRows = $this->parseCsvToRows($lmResponse->body());
                    if (!empty($lmRows)) {
                        $lmHeader = array_shift($lmRows);
                        $isRawLmList = (count($lmHeader) >= 30);
                        $calculatedLm = 0;
                        foreach ($lmRows as $row) {
                            if (empty($row) || count($row) < 20) {
                                continue;
                            }
                            $firstCol = strtoupper(trim($row[0]));
                            if ($firstCol === '' || str_contains($firstCol, 'TOTAL') || str_contains($firstCol, 'JUMLAH') || str_contains($firstCol, 'AVERAGE')) {
                                  continue;
                            }
                            if ($isRawLmList) {
                                $calculatedLm += 1;
                            } else {
                                $calculatedLm += (int)($row[2] ?? 0);
                            }
                        }
                        $lmVal = $calculatedLm;
                    }
                } else {
                    Log::error("Failed to fetch LM spreadsheet CSV for '{$cabang->nama}'.");
                }
            }

            $currentMonth = \Carbon\Carbon::now()->month;
            $remainingMonths = 12 - $currentMonth + 1;
            $diff = $cabang->target_reguler_2026 - $totalActYtdJan2026;
            $targetPerbulan = $remainingMonths > 0 ? (int)round($diff / $remainingMonths) : 0;

            // Save to database
            $cabang->update([
                'acv' => $totalAcv,
                'lm' => $lmVal !== null ? $lmVal : $totalLm,
                'act_ytd_jan_2026' => $totalActYtdJan2026,
                'target_perbulan_utk_2026' => $targetPerbulan,
                'stock_2024' => $totalStock2024,
                'stock_2025' => $totalStock2025,
                'stock_2026' => $totalStock2026,
                'stock_breakdown' => !empty($stockBreakdown) ? $stockBreakdown : null,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error("Error syncing spreadsheet for '{$cabang->nama}': " . $e->getMessage() . "\n" . $e->getTraceAsString());
            return false;
        }
    }

    /**
     * Sync all branches that have spreadsheet URLs configured.
     *
     * @return bool
     */
    public function syncAll()
    {
        $cabangs = Cabang::all();
        $anySuccess = false;

        foreach ($cabangs as $cabang) {
            $success = $this->syncCabang($cabang);
            if ($success) {
                $anySuccess = true;
            }
        }

        return $anySuccess;
    }

    /**
     * Normalize URL for direct CSV download.
     */
    private function normalizeUrl(string $url): string
    {
        if (str_contains($url, 'docs.google.com/spreadsheets')) {
            if (preg_match('/\/d\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
                $spreadsheetId = $matches[1];
                return "https://docs.google.com/spreadsheets/d/{$spreadsheetId}/export?format=csv";
            }
        }
        return $url;
    }

    /**
     * Parse CSV body into rows.
     */
    private function parseCsvToRows(string $csvData): array
    {
        $csvData = str_replace("\r", "", $csvData);
        $lines = explode("\n", $csvData);
        $rows = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $rows[] = str_getcsv($line);
        }
        return $rows;
    }
}
