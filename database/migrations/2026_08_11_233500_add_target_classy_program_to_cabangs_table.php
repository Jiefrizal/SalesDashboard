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
            $table->integer('target_classy_program')->default(0)->after('acv');
        });

        // Set default initial targets
        $defaults = [
            'PEKANBARU' => 8,
            'SEI PAGAR' => 2,
            'AIR MOLEK' => 2,
            'SOREK'     => 5,
            'KANDIS'    => 6,
            'MEDAN'     => 10,
        ];

        foreach ($defaults as $nama => $target) {
            \Illuminate\Support\Facades\DB::table('cabangs')
                ->whereRaw('UPPER(nama) = ?', [$nama])
                ->update(['target_classy_program' => $target]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            $table->dropColumn('target_classy_program');
        });
    }
};
