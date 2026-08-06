<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn([
                'target_stu', 'actual_stu', 'lm_stu', 'tgt_daily',
                'atm_target', 'atm_actual', 'atm_lm',
                'classy_target', 'classy_actual', 'classy_lm',
                'premium_target', 'premium_actual', 'premium_lm',
                'sport_target', 'sport_actual', 'sport_lm',
                'moped_target', 'moped_actual', 'moped_lm'
            ]);

            // Add new columns
            $table->integer('target_tantangan')->default(0)->after('spreadsheet_url');
            $table->integer('acv')->default(0)->after('target_tantangan');
            $table->integer('target_reguler')->default(0)->after('acv');
            $table->integer('lm')->default(0)->after('target_reguler');
            $table->integer('target_reguler_2026')->default(0)->after('lm');
            $table->integer('act_ytd_jan_2026')->default(0)->after('target_reguler_2026');
            $table->integer('target_perbulan_utk_2026')->default(0)->after('act_ytd_jan_2026');
            $table->integer('stock_2024')->default(0)->after('target_perbulan_utk_2026');
            $table->integer('stock_2025')->default(0)->after('stock_2024');
            $table->integer('stock_2026')->default(0)->after('stock_2025');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn([
                'target_tantangan', 'acv', 'target_reguler', 'lm',
                'target_reguler_2026', 'act_ytd_jan_2026', 'target_perbulan_utk_2026',
                'stock_2024', 'stock_2025', 'stock_2026'
            ]);

            // Recreate old columns
            $table->integer('target_stu')->default(0);
            $table->integer('actual_stu')->default(0);
            $table->integer('lm_stu')->default(0);
            $table->integer('tgt_daily')->default(0);
            $table->integer('atm_target')->default(0);
            $table->integer('atm_actual')->default(0);
            $table->integer('atm_lm')->default(0);
            $table->integer('classy_target')->default(0);
            $table->integer('classy_actual')->default(0);
            $table->integer('classy_lm')->default(0);
            $table->integer('premium_target')->default(0);
            $table->integer('premium_actual')->default(0);
            $table->integer('premium_lm')->default(0);
            $table->integer('sport_target')->default(0);
            $table->integer('sport_actual')->default(0);
            $table->integer('sport_lm')->default(0);
            $table->integer('moped_target')->default(0);
            $table->integer('moped_actual')->default(0);
            $table->integer('moped_lm')->default(0);
        });
    }
};
