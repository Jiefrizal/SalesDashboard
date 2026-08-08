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
        if (!auth()->user()->canEdit()) {
            return redirect()->route('cabang.index')->with('error', 'Anda tidak memiliki hak akses untuk mengedit data.');
        }
        return view('cabang.edit', compact('cabang'));
    }

    public function update(Request $request, Cabang $cabang)
    {
        if (!auth()->user()->canEdit()) {
            return redirect()->route('cabang.index')->with('error', 'Anda tidak memiliki hak akses untuk mengedit data.');
        }

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
        if (!auth()->user()->canEdit()) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengedit data.'], 403);
        }

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

    public function updateMonthlySales(Request $request, Cabang $cabang)
    {
        if (!auth()->user()->canEdit()) {
            return response()->json(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengedit data.'], 403);
        }
        $validated = $request->validate([
            'monthly_sales' => 'required|array',
            'monthly_sales.*' => 'nullable|integer|min:0',
        ]);

        $sales = [];
        for ($i = 0; $i < 12; $i++) {
            $sales[$i] = (int)($validated['monthly_sales'][$i] ?? 0);
        }

        $salesSum = array_sum($sales);
        $actYtdVal = $salesSum + (int)$cabang->acv;
        $currentMonth = \Carbon\Carbon::now()->month;
        $remainingMonths = 12 - $currentMonth + 1;
        $diff = $cabang->target_reguler_2026 - $actYtdVal;
        $targetPerbulan = $remainingMonths > 0 ? (int)round($diff / $remainingMonths) : 0;

        $cabang->update([
            'monthly_sales' => $sales,
            'act_ytd_jan_2026' => $actYtdVal,
            'target_perbulan_utk_2026' => $targetPerbulan,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Monthly sales for {$cabang->nama} successfully updated!",
            'monthly_sales' => $cabang->getMonthlySalesData(),
            'act_ytd_jan_2026' => $cabang->act_ytd_jan_2026,
            'target_perbulan_utk_2026' => $cabang->target_perbulan_utk_2026,
            'total' => $actYtdVal,
        ]);
    }
}