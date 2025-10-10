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
        Schema::create('incident_reports', function (Blueprint $table) {
            $table->id();
            $table->string('incident_id')->unique();           // IR001
            $table->string('student_id');                      // ST001
            $table->string('category_id');                     // VC001
            $table->date('incident_date');                     // 2025-10-05
            $table->string('location');                        // Computer Lab
            $table->text('description');                       // Summary of incident
            $table->string('reported_by');                     // Prof. Dela Cruz
            $table->text('action_taken')->nullable();          // Confiscated exam paper
            $table->string('status')->default('Under Investigation');
            $table->date('date_report_per_status')->nullable(); // Date Report Per Status
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incident_reports');
    }
};
