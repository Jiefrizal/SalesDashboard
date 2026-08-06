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
    Schema::create('cabangs', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->integer('target_stu')->default(0);
        $table->integer('actual_stu')->default(0);
        $table->integer('lm_stu')->default(0);
        $table->integer('tgt_daily')->default(0);

        // Category: ATM
        $table->integer('atm_target')->default(0);
        $table->integer('atm_actual')->default(0);
        $table->integer('atm_lm')->default(0);

        // Category: AT Classy
        $table->integer('classy_target')->default(0);
        $table->integer('classy_actual')->default(0);
        $table->integer('classy_lm')->default(0);

        // Category: AT Premium
        $table->integer('premium_target')->default(0);
        $table->integer('premium_actual')->default(0);
        $table->integer('premium_lm')->default(0);

        // Category: Sport
        $table->integer('sport_target')->default(0);
        $table->integer('sport_actual')->default(0);
        $table->integer('sport_lm')->default(0);

        // Category: Moped
        $table->integer('moped_target')->default(0);
        $table->integer('moped_actual')->default(0);
        $table->integer('moped_lm')->default(0);

        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cabangs');
    }
};
