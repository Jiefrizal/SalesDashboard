<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index(Request $request)
    {
        $cabangs = Cabang::all();
        $activeTab = $request->query('tab', 'all');

        $kotaMapping = [
            'Pekanbaru' => 'PEKANBARU',
            'Sei Pagar' => 'KAMPAR',
            'Air Molek' => 'INHU',
            'Sorek' => 'PELALAWAN',
            'Kandis' => 'SIAK',
            'Medan' => 'MEDAN',
        ];

        // Service analytics metrics per cabang
        $serviceData = [];
        $totalUe = 0;
        $totalRevenue = 0;
        $totalSpareparts = 0;
        $totalIncomeBengkel = 0;
        $totalKsg = 0;

        foreach ($cabangs as $cabang) {
            $nama = $cabang->nama;
            $acv = (int)$cabang->acv;
            $target = (int)$cabang->target_reguler;

            // Derived Service Analytics
            $ueCount = (int)round(($acv > 0 ? $acv : 15) * 4.2);
            $serviceRevenue = $ueCount * 125000;
            $sparepartsRevenue = $ueCount * 285000;
            $csatRating = 98.2 + (($ueCount % 5) * 0.3);

            // Specific Sub-menu Metrics
            $ksgCount = (int)round($ueCount * 0.45); // KSG (Kartu Servis Gratis)
            $rutRatio = $acv > 0 ? round(($ueCount / $acv), 2) : 3.8;
            $jasaPerUnit = $ueCount > 0 ? (int)round($serviceRevenue / $ueCount) : 125000;
            $operationalCost = (int)round(($serviceRevenue + $sparepartsRevenue) * 0.35);
            $netIncomeBengkel = ($serviceRevenue + $sparepartsRevenue) - $operationalCost;

            $totalUe += $ueCount;
            $totalRevenue += $serviceRevenue;
            $totalSpareparts += $sparepartsRevenue;
            $totalIncomeBengkel += $netIncomeBengkel;
            $totalKsg += $ksgCount;

            $serviceData[] = [
                'cabang' => $cabang,
                'kota' => $kotaMapping[$nama] ?? strtoupper($nama),
                'ue_count' => $ueCount,
                'ksg_count' => $ksgCount,
                'rut_ratio' => $rutRatio,
                'service_revenue' => $serviceRevenue,
                'spareparts_revenue' => $sparepartsRevenue,
                'jasa_per_unit' => $jasaPerUnit,
                'operational_cost' => $operationalCost,
                'net_income' => $netIncomeBengkel,
                'csat' => min(99.9, number_format($csatRating, 1)),
                'mechanics' => max(4, (int)round($ueCount / 20)),
            ];
        }

        // Complete RUT KSG & UE Spreadsheet Dataset
        $rutKsgTable = [
            // PEKANBARU
            ['cabang' => 'PEKANBARU', 'bengkel' => 'RIAU 1', 'is_subtotal' => false, 'rut' => 4.3, 'ksg' => ['hk' => 0, 'aug' => 12, 'jul' => 9, 'target' => 84, 'vs_lm' => 1.33, 'vs_target' => 0.1433, 'prop_target' => 14], 'total' => ['hk' => 7, 'aug' => 68, 'jul' => 55, 'target' => 419, 'vs_lm' => 1.24, 'vs_target' => 0.1624, 'prop_target' => 70]],
            ['cabang' => 'PEKANBARU', 'bengkel' => 'RIAU 2', 'is_subtotal' => false, 'rut' => 3.1, 'ksg' => ['hk' => 2, 'aug' => 6, 'jul' => 5, 'target' => 72, 'vs_lm' => 1.20, 'vs_target' => 0.0836, 'prop_target' => 12], 'total' => ['hk' => 14, 'aug' => 49, 'jul' => 44, 'target' => 359, 'vs_lm' => 1.11, 'vs_target' => 0.1366, 'prop_target' => 60]],
            ['cabang' => 'PEKANBARU', 'bengkel' => 'GARUDA SAKTI', 'is_subtotal' => false, 'rut' => 2.6, 'ksg' => ['hk' => 0, 'aug' => 4, 'jul' => 1, 'target' => 24, 'vs_lm' => 4.00, 'vs_target' => 0.1672, 'prop_target' => 4], 'total' => ['hk' => 3, 'aug' => 21, 'jul' => 23, 'target' => 120, 'vs_lm' => 0.91, 'vs_target' => 0.1756, 'prop_target' => 20]],
            ['cabang' => 'PEKANBARU', 'bengkel' => 'PEKANBARU', 'is_subtotal' => true, 'rut' => 3.5, 'ksg' => ['hk' => 2, 'aug' => 22, 'jul' => 15, 'target' => 179, 'vs_lm' => 1.47, 'vs_target' => 0.1226, 'prop_target' => 30], 'total' => ['hk' => 24, 'aug' => 138, 'jul' => 122, 'target' => 897, 'vs_lm' => 1.13, 'vs_target' => 0.1538, 'prop_target' => 150]],

            // PELALAWAN
            ['cabang' => 'PELALAWAN', 'bengkel' => 'SOREK', 'is_subtotal' => false, 'rut' => 5.2, 'ksg' => ['hk' => 4, 'aug' => 25, 'jul' => 23, 'target' => 72, 'vs_lm' => 1.09, 'vs_target' => 0.3484, 'prop_target' => 12], 'total' => ['hk' => 21, 'aug' => 83, 'jul' => 79, 'target' => 359, 'vs_lm' => 1.05, 'vs_target' => 0.2313, 'prop_target' => 60]],
            ['cabang' => 'PELALAWAN', 'bengkel' => 'KERINCI', 'is_subtotal' => false, 'rut' => 4.1, 'ksg' => ['hk' => 8, 'aug' => 21, 'jul' => 13, 'target' => 72, 'vs_lm' => 1.62, 'vs_target' => 0.2926, 'prop_target' => 12], 'total' => ['hk' => 15, 'aug' => 66, 'jul' => 54, 'target' => 359, 'vs_lm' => 1.22, 'vs_target' => 0.1839, 'prop_target' => 60]],
            ['cabang' => 'PELALAWAN', 'bengkel' => 'PELALAWAN', 'is_subtotal' => true, 'rut' => 4.7, 'ksg' => ['hk' => 12, 'aug' => 46, 'jul' => 36, 'target' => 144, 'vs_lm' => 1.28, 'vs_target' => 0.3205, 'prop_target' => 24], 'total' => ['hk' => 36, 'aug' => 149, 'jul' => 133, 'target' => 718, 'vs_lm' => 1.12, 'vs_target' => 0.2076, 'prop_target' => 120]],

            // INHU
            ['cabang' => 'INHU', 'bengkel' => 'AIR MOLEK', 'is_subtotal' => false, 'rut' => 1.6, 'ksg' => ['hk' => 4, 'aug' => 16, 'jul' => 12, 'target' => 24, 'vs_lm' => 1.33, 'vs_target' => 0.6689, 'prop_target' => 4], 'total' => ['hk' => 4, 'aug' => 19, 'jul' => 21, 'target' => 120, 'vs_lm' => 0.90, 'vs_target' => 0.1589, 'prop_target' => 20]],
            ['cabang' => 'INHU', 'bengkel' => 'BELILAS', 'is_subtotal' => false, 'rut' => 0.1, 'ksg' => ['hk' => 0, 'aug' => 1, 'jul' => 4, 'target' => 24, 'vs_lm' => 0.25, 'vs_target' => 0.0418, 'prop_target' => 4], 'total' => ['hk' => 0, 'aug' => 1, 'jul' => 7, 'target' => 120, 'vs_lm' => 0.14, 'vs_target' => 0.0084, 'prop_target' => 20]],
            ['cabang' => 'INHU', 'bengkel' => 'INHU', 'is_subtotal' => true, 'rut' => 1.0, 'ksg' => ['hk' => 4, 'aug' => 17, 'jul' => 16, 'target' => 48, 'vs_lm' => 1.06, 'vs_target' => 0.3554, 'prop_target' => 8], 'total' => ['hk' => 4, 'aug' => 20, 'jul' => 28, 'target' => 239, 'vs_lm' => 0.71, 'vs_target' => 0.0836, 'prop_target' => 40]],

            // SIAK
            ['cabang' => 'SIAK', 'bengkel' => 'KANDIS', 'is_subtotal' => false, 'rut' => 3.0, 'ksg' => ['hk' => 3, 'aug' => 8, 'jul' => 5, 'target' => 30, 'vs_lm' => 1.60, 'vs_target' => 0.2635, 'prop_target' => 5], 'total' => ['hk' => 6, 'aug' => 24, 'jul' => 24, 'target' => 152, 'vs_lm' => 1.00, 'vs_target' => 0.1581, 'prop_target' => 25]],
            ['cabang' => 'SIAK', 'bengkel' => 'PERAWANG', 'is_subtotal' => false, 'rut' => 8.4, 'ksg' => ['hk' => 7, 'aug' => 25, 'jul' => 22, 'target' => 54, 'vs_lm' => 1.14, 'vs_target' => 0.4606, 'prop_target' => 9], 'total' => ['hk' => 15, 'aug' => 67, 'jul' => 64, 'target' => 271, 'vs_lm' => 1.05, 'vs_target' => 0.2469, 'prop_target' => 45]],
            ['cabang' => 'SIAK', 'bengkel' => 'SIAK', 'is_subtotal' => true, 'rut' => 5.7, 'ksg' => ['hk' => 10, 'aug' => 33, 'jul' => 27, 'target' => 85, 'vs_lm' => 1.22, 'vs_target' => 0.3899, 'prop_target' => 14], 'total' => ['hk' => 21, 'aug' => 91, 'jul' => 88, 'target' => 423, 'vs_lm' => 1.03, 'vs_target' => 0.2150, 'prop_target' => 71]],

            // KAMPAR
            ['cabang' => 'KAMPAR', 'bengkel' => 'SUNGAI PAGAR', 'is_subtotal' => false, 'rut' => 3.5, 'ksg' => ['hk' => 2, 'aug' => 10, 'jul' => 10, 'target' => 30, 'vs_lm' => 1.00, 'vs_target' => 0.3294, 'prop_target' => 5], 'total' => ['hk' => 4, 'aug' => 28, 'jul' => 19, 'target' => 152, 'vs_lm' => 1.47, 'vs_target' => 0.1845, 'prop_target' => 25]],
            ['cabang' => 'KAMPAR', 'bengkel' => 'BANGKINANG', 'is_subtotal' => false, 'rut' => 8.0, 'ksg' => ['hk' => 3, 'aug' => 14, 'jul' => 8, 'target' => 30, 'vs_lm' => 1.75, 'vs_target' => 0.4611, 'prop_target' => 5], 'total' => ['hk' => 13, 'aug' => 32, 'jul' => 18, 'target' => 152, 'vs_lm' => 1.78, 'vs_target' => 0.2108, 'prop_target' => 25]],
            ['cabang' => 'KAMPAR', 'bengkel' => 'KAMPAR', 'is_subtotal' => true, 'rut' => 5.0, 'ksg' => ['hk' => 5, 'aug' => 24, 'jul' => 18, 'target' => 61, 'vs_lm' => 1.33, 'vs_target' => 0.3953, 'prop_target' => 10], 'total' => ['hk' => 17, 'aug' => 60, 'jul' => 37, 'target' => 304, 'vs_lm' => 1.62, 'vs_target' => 0.1976, 'prop_target' => 51]],

            // MEDAN
            ['cabang' => 'MEDAN', 'bengkel' => 'MEDAN', 'is_subtotal' => false, 'rut' => 5.1, 'ksg' => ['hk' => 11, 'aug' => 36, 'jul' => 40, 'target' => 155, 'vs_lm' => 0.90, 'vs_target' => 0.2315, 'prop_target' => 26], 'total' => ['hk' => 36, 'aug' => 123, 'jul' => 145, 'target' => 777, 'vs_lm' => 0.85, 'vs_target' => 0.1582, 'prop_target' => 130]],
            ['cabang' => 'MEDAN', 'bengkel' => 'MEDAN', 'is_subtotal' => true, 'rut' => 5.1, 'ksg' => ['hk' => 11, 'aug' => 36, 'jul' => 40, 'target' => 155, 'vs_lm' => 0.90, 'vs_target' => 0.2315, 'prop_target' => 26], 'total' => ['hk' => 36, 'aug' => 123, 'jul' => 145, 'target' => 777, 'vs_lm' => 0.85, 'vs_target' => 0.1582, 'prop_target' => 130]],
        ];

        $rutKsgGrandTotal = [
            'rut' => 5.2,
            'ksg' => ['hk' => 44, 'aug' => 178, 'jul' => 152, 'target' => 672, 'vs_lm' => 1.17, 'vs_target' => 0.2650, 'prop_target' => 112],
            'total' => ['hk' => 138, 'aug' => 581, 'jul' => 553, 'target' => 3358, 'vs_lm' => 1.05, 'vs_target' => 0.1730, 'prop_target' => 560]
        ];

        // Complete JASA MEKANIK Spreadsheet Dataset
        $jasaMekanikTable = [
            // PEKANBARU
            ['cabang' => 'PEKANBARU', 'bengkel' => 'RIAU 1', 'is_subtotal' => false, 'hk' => 1170000, 'ksg' => 402000, 'ksb' => 5103000, 'aug' => 5505000, 'jul' => 5606000, 'target' => 25116000, 'vs_lm' => 0.98, 'vs_target' => 0.2192, 'prop_target' => 4186000],
            ['cabang' => 'PEKANBARU', 'bengkel' => 'RIAU 2', 'is_subtotal' => false, 'hk' => 1261000, 'ksg' => 206000, 'ksb' => 4204000, 'aug' => 4410000, 'jul' => 4014000, 'target' => 21528000, 'vs_lm' => 1.10, 'vs_target' => 0.2048, 'prop_target' => 3588000],
            ['cabang' => 'PEKANBARU', 'bengkel' => 'GARUDA SAKTI', 'is_subtotal' => false, 'hk' => 130000, 'ksg' => 1299000, 'ksb' => 1429000, 'aug' => 2728000, 'jul' => 1135000, 'target' => 7176000, 'vs_lm' => 2.40, 'vs_target' => 0.3802, 'prop_target' => 1196000],
            ['cabang' => 'PEKANBARU', 'bengkel' => 'PEKANBARU', 'is_subtotal' => true, 'hk' => 2561000, 'ksg' => 1907000, 'ksb' => 10736000, 'aug' => 12643000, 'jul' => 10755000, 'target' => 53820000, 'vs_lm' => 1.18, 'vs_target' => 0.2349, 'prop_target' => 8970000],

            // PELALAWAN
            ['cabang' => 'PELALAWAN', 'bengkel' => 'SOREK', 'is_subtotal' => false, 'hk' => 1757000, 'ksg' => 867000, 'ksb' => 5076000, 'aug' => 5943000, 'jul' => 5766000, 'target' => 22080000, 'vs_lm' => 1.03, 'vs_target' => 0.2692, 'prop_target' => 3680000],
            ['cabang' => 'PELALAWAN', 'bengkel' => 'KERINCI', 'is_subtotal' => false, 'hk' => 647000, 'ksg' => 677000, 'ksb' => 3090000, 'aug' => 3767000, 'jul' => 3139000, 'target' => 21528000, 'vs_lm' => 1.20, 'vs_target' => 0.1750, 'prop_target' => 3588000],
            ['cabang' => 'PELALAWAN', 'bengkel' => 'PELALAWAN', 'is_subtotal' => true, 'hk' => 2404000, 'ksg' => 1544000, 'ksb' => 8166000, 'aug' => 9710000, 'jul' => 8905000, 'target' => 43608000, 'vs_lm' => 1.09, 'vs_target' => 0.2227, 'prop_target' => 7268000],

            // INHU
            ['cabang' => 'INHU', 'bengkel' => 'AIR MOLEK', 'is_subtotal' => false, 'hk' => 139000, 'ksg' => 527000, 'ksb' => 50000, 'aug' => 577000, 'jul' => 687000, 'target' => 7176000, 'vs_lm' => 0.84, 'vs_target' => 0.0804, 'prop_target' => 1196000],
            ['cabang' => 'INHU', 'bengkel' => 'BELILAS', 'is_subtotal' => false, 'hk' => 38000, 'ksg' => 38000, 'ksb' => 0, 'aug' => 38000, 'jul' => 229000, 'target' => 7176000, 'vs_lm' => 0.17, 'vs_target' => 0.0053, 'prop_target' => 1196000],
            ['cabang' => 'INHU', 'bengkel' => 'INHU', 'is_subtotal' => true, 'hk' => 177000, 'ksg' => 565000, 'ksb' => 50000, 'aug' => 615000, 'jul' => 916000, 'target' => 14352000, 'vs_lm' => 0.67, 'vs_target' => 0.0429, 'prop_target' => 2392000],

            // SIAK
            ['cabang' => 'SIAK', 'bengkel' => 'KANDIS', 'is_subtotal' => false, 'hk' => 144000, 'ksg' => 304000, 'ksb' => 789000, 'aug' => 1093000, 'jul' => 1724000, 'target' => 9246000, 'vs_lm' => 0.63, 'vs_target' => 0.1182, 'prop_target' => 1541000],
            ['cabang' => 'SIAK', 'bengkel' => 'PERAWANG', 'is_subtotal' => false, 'hk' => 669000, 'ksg' => 915000, 'ksb' => 2722000, 'aug' => 3637000, 'jul' => 4207000, 'target' => 16422000, 'vs_lm' => 0.86, 'vs_target' => 0.2215, 'prop_target' => 2737000],
            ['cabang' => 'SIAK', 'bengkel' => 'SIAK', 'is_subtotal' => true, 'hk' => 813000, 'ksg' => 1219000, 'ksb' => 3511000, 'aug' => 4730000, 'jul' => 5931000, 'target' => 25668000, 'vs_lm' => 0.80, 'vs_target' => 0.1843, 'prop_target' => 4278000],

            // KAMPAR
            ['cabang' => 'KAMPAR', 'bengkel' => 'SUNGAI PAGAR', 'is_subtotal' => false, 'hk' => 197000, 'ksg' => 323000, 'ksb' => 1843000, 'aug' => 2166000, 'jul' => 804000, 'target' => 9246000, 'vs_lm' => 2.69, 'vs_target' => 0.2343, 'prop_target' => 1541000],
            ['cabang' => 'KAMPAR', 'bengkel' => 'BANGKINANG', 'is_subtotal' => false, 'hk' => 513000, 'ksg' => 488000, 'ksb' => 7390000, 'aug' => 7878000, 'jul' => 711000, 'target' => 9246000, 'vs_lm' => 11.08, 'vs_target' => 0.8520, 'prop_target' => 1541000],
            ['cabang' => 'KAMPAR', 'bengkel' => 'KAMPAR', 'is_subtotal' => true, 'hk' => 710000, 'ksg' => 811000, 'ksb' => 9233000, 'aug' => 10044000, 'jul' => 1515000, 'target' => 18492000, 'vs_lm' => 6.63, 'vs_target' => 0.5432, 'prop_target' => 3082000],

            // MEDAN
            ['cabang' => 'MEDAN', 'bengkel' => 'MEDAN', 'is_subtotal' => false, 'hk' => 3439000, 'ksg' => 1205000, 'ksb' => 9035000, 'aug' => 10240000, 'jul' => 12691000, 'target' => 57408000, 'vs_lm' => 0.81, 'vs_target' => 0.1784, 'prop_target' => 9568000],
            ['cabang' => 'MEDAN', 'bengkel' => 'MEDAN', 'is_subtotal' => true, 'hk' => 3439000, 'ksg' => 1205000, 'ksb' => 9035000, 'aug' => 10240000, 'jul' => 12691000, 'target' => 57408000, 'vs_lm' => 0.81, 'vs_target' => 0.1784, 'prop_target' => 9568000],
        ];

        $jasaMekanikGrandTotal = [
            'hk' => 10104000,
            'ksg' => 7251000,
            'ksb' => 40731000,
            'aug' => 47982000,
            'jul' => 40713000,
            'target' => 213348000,
            'vs_lm' => 1.18,
            'vs_target' => 0.225,
            'prop_target' => 35558000,
        ];

        // Complete JASA / UNIT Spreadsheet Dataset (Target 75k)
        $jasaUnitTable = [
            // PEKANBARU
            ['cabang' => 'PEKANBARU', 'bengkel' => 'RIAU 1', 'is_subtotal' => false, 'aug' => 80956, 'jul' => 101927],
            ['cabang' => 'PEKANBARU', 'bengkel' => 'RIAU 2', 'is_subtotal' => false, 'aug' => 90000, 'jul' => 91227],
            ['cabang' => 'PEKANBARU', 'bengkel' => 'GARUDA SAKTI', 'is_subtotal' => false, 'aug' => 129905, 'jul' => 49348],
            ['cabang' => 'PEKANBARU', 'bengkel' => 'PEKANBARU', 'is_subtotal' => true, 'aug' => 91616, 'jul' => 88156],

            // PELALAWAN
            ['cabang' => 'PELALAWAN', 'bengkel' => 'SOREK', 'is_subtotal' => false, 'aug' => 71602, 'jul' => 72987],
            ['cabang' => 'PELALAWAN', 'bengkel' => 'KERINCI', 'is_subtotal' => false, 'aug' => 57076, 'jul' => 58130],
            ['cabang' => 'PELALAWAN', 'bengkel' => 'PELALAWAN', 'is_subtotal' => true, 'aug' => 65168, 'jul' => 66955],

            // INHU
            ['cabang' => 'INHU', 'bengkel' => 'AIR MOLEK', 'is_subtotal' => false, 'aug' => 30368, 'jul' => 32714],
            ['cabang' => 'INHU', 'bengkel' => 'BELILAS', 'is_subtotal' => false, 'aug' => 38000, 'jul' => 32714],
            ['cabang' => 'INHU', 'bengkel' => 'INHU', 'is_subtotal' => true, 'aug' => 30750, 'jul' => 32714],

            // SIAK
            ['cabang' => 'SIAK', 'bengkel' => 'KANDIS', 'is_subtotal' => false, 'aug' => 45542, 'jul' => 71833],
            ['cabang' => 'SIAK', 'bengkel' => 'PERAWANG', 'is_subtotal' => false, 'aug' => 54284, 'jul' => 65734],
            ['cabang' => 'SIAK', 'bengkel' => 'SIAK', 'is_subtotal' => true, 'aug' => 51978, 'jul' => 67398],

            // KAMPAR
            ['cabang' => 'KAMPAR', 'bengkel' => 'SUNGAI PAGAR', 'is_subtotal' => false, 'aug' => 77357, 'jul' => 42316],
            ['cabang' => 'KAMPAR', 'bengkel' => 'BANGKINANG', 'is_subtotal' => false, 'aug' => 246188, 'jul' => 39500],
            ['cabang' => 'KAMPAR', 'bengkel' => 'KAMPAR', 'is_subtotal' => true, 'aug' => 167400, 'jul' => 40946],

            // MEDAN
            ['cabang' => 'MEDAN', 'bengkel' => 'MEDAN', 'is_subtotal' => false, 'aug' => 83252, 'jul' => 87524],
            ['cabang' => 'MEDAN', 'bengkel' => 'MEDAN', 'is_subtotal' => true, 'aug' => 83252, 'jul' => 87524],
        ];

        $jasaUnitGrandTotal = [
            'aug' => 82585,
            'jul' => 73622,
        ];

        // Complete INCOME BENGKEL Spreadsheet Dataset (Target 200k)
        $incomeBengkelTable = [
            // PEKANBARU
            ['cabang' => 'PEKANBARU', 'bengkel' => 'RIAU 1', 'is_subtotal' => false, 'part' => 14355150, 'income' => 19860150, 'target' => 75348000, 'income_per_unit' => 292061],
            ['cabang' => 'PEKANBARU', 'bengkel' => 'RIAU 2', 'is_subtotal' => false, 'part' => 8739775, 'income' => 13149775, 'target' => 68218000, 'income_per_unit' => 268363],
            ['cabang' => 'PEKANBARU', 'bengkel' => 'GARUDA SAKTI', 'is_subtotal' => false, 'part' => 2582000, 'income' => 5310000, 'target' => 20516000, 'income_per_unit' => 252857],
            ['cabang' => 'PEKANBARU', 'bengkel' => 'PEKANBARU', 'is_subtotal' => true, 'part' => 25676925, 'income' => 38319925, 'target' => 164082000, 'income_per_unit' => 277681],

            // PELALAWAN
            ['cabang' => 'PELALAWAN', 'bengkel' => 'SOREK', 'is_subtotal' => false, 'part' => 9732050, 'income' => 15675050, 'target' => 66240000, 'income_per_unit' => 188856],
            ['cabang' => 'PELALAWAN', 'bengkel' => 'KERINCI', 'is_subtotal' => false, 'part' => 8072000, 'income' => 11839000, 'target' => 64584000, 'income_per_unit' => 179379],
            ['cabang' => 'PELALAWAN', 'bengkel' => 'PELALAWAN', 'is_subtotal' => true, 'part' => 17804050, 'income' => 27514050, 'target' => 130824000, 'income_per_unit' => 184658],

            // INHU
            ['cabang' => 'INHU', 'bengkel' => 'AIR MOLEK', 'is_subtotal' => false, 'part' => 1480000, 'income' => 2057000, 'target' => 50232000, 'income_per_unit' => 108263],
            ['cabang' => 'INHU', 'bengkel' => 'BELILAS', 'is_subtotal' => false, 'part' => 94000, 'income' => 132000, 'target' => 20516000, 'income_per_unit' => 132000],
            ['cabang' => 'INHU', 'bengkel' => 'INHU', 'is_subtotal' => true, 'part' => 1574000, 'income' => 2189000, 'target' => 70748000, 'income_per_unit' => 109450],

            // SIAK
            ['cabang' => 'SIAK', 'bengkel' => 'KANDIS', 'is_subtotal' => false, 'part' => 2425500, 'income' => 3518500, 'target' => 25921000, 'income_per_unit' => 146604],
            ['cabang' => 'SIAK', 'bengkel' => 'PERAWANG', 'is_subtotal' => false, 'part' => 7458500, 'income' => 11095500, 'target' => 49266000, 'income_per_unit' => 165604],
            ['cabang' => 'SIAK', 'bengkel' => 'SIAK', 'is_subtotal' => true, 'part' => 9884000, 'income' => 14614000, 'target' => 75187000, 'income_per_unit' => 160593],

            // KAMPAR
            ['cabang' => 'KAMPAR', 'bengkel' => 'SUNGAI PAGAR', 'is_subtotal' => false, 'part' => 8141000, 'income' => 10307000, 'target' => 25921000, 'income_per_unit' => 368107],
            ['cabang' => 'KAMPAR', 'bengkel' => 'BANGKINANG', 'is_subtotal' => false, 'part' => 3042000, 'income' => 10920000, 'target' => 22586000, 'income_per_unit' => 341250],
            ['cabang' => 'KAMPAR', 'bengkel' => 'KAMPAR', 'is_subtotal' => true, 'part' => 11183000, 'income' => 21227000, 'target' => 48507000, 'income_per_unit' => 353783],

            // MEDAN
            ['cabang' => 'MEDAN', 'bengkel' => 'MEDAN', 'is_subtotal' => false, 'part' => 20259400, 'income' => 30499400, 'target' => 150788000, 'income_per_unit' => 247963],
            ['cabang' => 'MEDAN', 'bengkel' => 'MEDAN', 'is_subtotal' => true, 'part' => 20259400, 'income' => 30499400, 'target' => 150788000, 'income_per_unit' => 247963],
        ];

        $incomeBengkelGrandTotal = [
            'part' => 86381375,
            'income' => 134363375,
            'target' => 640136000,
            'income_per_unit' => 231262,
        ];

        return view('service.index', compact(
            'cabangs',
            'serviceData',
            'totalUe',
            'totalRevenue',
            'totalSpareparts',
            'totalIncomeBengkel',
            'totalKsg',
            'activeTab',
            'rutKsgTable',
            'rutKsgGrandTotal',
            'jasaMekanikTable',
            'jasaMekanikGrandTotal',
            'jasaUnitTable',
            'jasaUnitGrandTotal',
            'incomeBengkelTable',
            'incomeBengkelGrandTotal'
        ));
    }
}
