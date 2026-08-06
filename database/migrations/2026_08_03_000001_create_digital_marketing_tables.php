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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabang_id')->constrained('cabangs')->onDelete('cascade');
            $table->date('tanggal');
            $table->integer('ig_feed')->default(0);
            $table->integer('ig_reels')->default(0);
            $table->integer('ig_story')->default(0);
            $table->integer('ig_followers_gained')->default(0);
            $table->integer('fb_post')->default(0);
            $table->integer('fb_marketplace')->default(0);
            $table->integer('fb_followers_gained')->default(0);
            $table->integer('tiktok_post')->default(0);
            $table->integer('tiktok_live')->default(0);
            $table->integer('tiktok_followers_gained')->default(0);
            $table->float('google_rating')->default(0);
            $table->integer('google_review_gained')->default(0);
            $table->timestamps();
        });

        Schema::create('monthly_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabang_id')->constrained('cabangs')->onDelete('cascade');
            $table->integer('tahun');
            $table->integer('bulan');
            $table->bigInteger('ig_views')->default(0);
            $table->bigInteger('fb_views')->default(0);
            $table->bigInteger('tiktok_views')->default(0);
            $table->timestamps();
        });

        Schema::create('kpis', function (Blueprint $table) {
            $table->id();
            $table->integer('tahun');
            $table->integer('bulan');
            $table->timestamps();
        });

        Schema::create('kpi_targets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kpi_id')->constrained('kpis')->onDelete('cascade');
            $table->integer('ig_feed_target')->default(0);
            $table->integer('ig_reels_target')->default(0);
            $table->integer('fb_post_target')->default(0);
            $table->integer('tiktok_post_target')->default(0);
            $table->integer('ig_followers_target')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kpi_targets');
        Schema::dropIfExists('kpis');
        Schema::dropIfExists('monthly_insights');
        Schema::dropIfExists('daily_reports');
    }
};
