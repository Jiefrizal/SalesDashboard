<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use Illuminate\Http\Request;

class CabangController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::all();

        return view('cabang.index', compact('cabangs'));
    }

    public function edit(Cabang $cabang)
    {
        return view('cabang.edit', compact('cabang'));
    }

    public function update(Request $request, Cabang $cabang)
    {
        $validated = $request->validate([
            'target_tantangan' => 'required|integer|min:0',
            'target_reguler' => 'required|integer|min:0',
            'target_reguler_2026' => 'required|integer|min:0',
            'spreadsheet_url' => 'nullable|url',
        ]);

        $validated['stock_url'] = $validated['spreadsheet_url'];
        $validated['lm_url'] = $validated['spreadsheet_url'];

        $currentMonth = \Carbon\Carbon::now()->month;
        $remainingMonths = 12 - $currentMonth + 1;
        $actYtd = empty($validated['spreadsheet_url']) ? 0 : $cabang->act_ytd_jan_2026;
        $diff = $validated['target_reguler_2026'] - $actYtd;
        $validated['target_perbulan_utk_2026'] = $remainingMonths > 0 ? (int)round($diff / $remainingMonths) : 0;

        if (empty($validated['spreadsheet_url'])) {
            $validated['acv'] = 0;
            $validated['lm'] = 0;
            $validated['act_ytd_jan_2026'] = 0;
            $validated['stock_2024'] = 0;
            $validated['stock_2025'] = 0;
            $validated['stock_2026'] = 0;
        }

        $cabang->update($validated);

        if (!empty($cabang->spreadsheet_url)) {
            $spreadsheetService = app(\App\Services\SpreadsheetService::class);
            $spreadsheetService->syncCabang($cabang);
            \Illuminate\Support\Facades\Cache::forget('spreadsheet_last_sync');
        }

        return redirect()->route('cabang.index')->with('success', "Data target dan URL Spreadsheet untuk cabang {$cabang->nama} berhasil diperbarui dan disinkronisasi!");
    }

    public function updateYtd(Request $request, Cabang $cabang)
    {
        $validated = $request->validate([
            'act_ytd_jan_2026' => 'required|integer|min:0',
        ]);

        $currentMonth = \Carbon\Carbon::now()->month;
        $remainingMonths = 12 - $currentMonth + 1;
        $diff = $cabang->target_reguler_2026 - $validated['act_ytd_jan_2026'];
        $validated['target_perbulan_utk_2026'] = $remainingMonths > 0 ? (int)round($diff / $remainingMonths) : 0;

        $cabang->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'ACT YTD JAN 2026 successfully updated!',
            'act_ytd_jan_2026' => $cabang->act_ytd_jan_2026,
            'target_perbulan_utk_2026' => $cabang->target_perbulan_utk_2026,
        ]);
    }
}