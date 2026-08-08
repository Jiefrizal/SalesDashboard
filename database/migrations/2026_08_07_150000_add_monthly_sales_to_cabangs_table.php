<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            $table->json('monthly_sales')->nullable()->after('stu_breakdown');
        });

        // Seed initial data from user table specification
        $defaults = [
            'Pekanbaru' => [52, 70, 70, 66, 60, 81, 86, 0, 0, 0, 0, 0],
            'Sei Pagar' => [50, 63, 72, 52, 50, 72, 72, 0, 0, 0, 0, 0],
            'Air Molek' => [99, 93, 98, 68, 100, 88, 127, 0, 0, 0, 0, 0],
            'Sorek' => [106, 139, 173, 143, 137, 158, 153, 0, 0, 0, 0, 0],
            'Kandis' => [84, 96, 98, 72, 98, 100, 105, 0, 0, 0, 0, 0],
            'Medan' => [115, 109, 113, 107, 101, 114, 111, 0, 0, 0, 0, 0],
        ];

        foreach ($defaults as $nama => $sales) {
            DB::table('cabangs')
                ->where('nama', $nama)
                ->update(['monthly_sales' => json_encode($sales)]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cabangs', function (Blueprint $table) {
            $table->dropColumn('monthly_sales');
        });
    }
};
