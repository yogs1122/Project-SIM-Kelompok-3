<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_clusters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('cluster_id');
            $table->json('features')->nullable(); // Data untuk clustering
            $table->json('centroid_distance')->nullable();
            $table->timestamps();
        });

        Schema::create('financial_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('model_type'); // time_series, regression, clustering
            $table->json('parameters')->nullable();
            $table->json('results')->nullable();
            $table->decimal('accuracy', 5, 2)->nullable();
            $table->date('analysis_date');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('financial_analytics');
        Schema::dropIfExists('user_clusters');
    }
};