<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Cabang;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $currentMonth = Carbon::now()->month;
        $remainingMonths = 12 - $currentMonth + 1;

        $cabangs = Cabang::all();
        foreach ($cabangs as $cabang) {
            $sales = $cabang->getMonthlySalesData();
            $monthlySum = array_sum($sales);
            $acv = (int) $cabang->acv;
            $actYtdVal = $monthlySum + $acv;

            $diff = $cabang->target_reguler_2026 - $actYtdVal;
            $targetPerbulan = $remainingMonths > 0 ? (int)round($diff / $remainingMonths) : 0;

            $cabang->update([
                'act_ytd_jan_2026' => $actYtdVal,
                'target_perbulan_utk_2026' => $targetPerbulan,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed
    }
};
