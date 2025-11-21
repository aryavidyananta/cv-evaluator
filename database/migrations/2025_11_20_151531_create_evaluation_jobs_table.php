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
        Schema::create('evaluation_jobs', function (Blueprint $table) {
        $table->id();
        $table->string('job_title');
        $table->unsignedBigInteger('cv_id');
        $table->unsignedBigInteger('report_id');
        $table->string('status')->default('queued');

        // hasil evaluasi
        $table->float('cv_match_rate')->nullable();
        $table->text('cv_feedback')->nullable();

        $table->float('project_score')->nullable();
        $table->text('project_feedback')->nullable();

        $table->text('overall_summary')->nullable();

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('evaluation_jobs');
    }
};
