<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
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
            $sum = array_sum($sales);
            if ($sum > 0) {
                $diff = $cabang->target_reguler_2026 - $sum;
                $targetPerbulan = $remainingMonths > 0 ? (int)round($diff / $remainingMonths) : 0;
                $cabang->update([
                    'act_ytd_jan_2026' => $sum,
                    'target_perbulan_utk_2026' => $targetPerbulan,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No action needed for rollback
    }
};
