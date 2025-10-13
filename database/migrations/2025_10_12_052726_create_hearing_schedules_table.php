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
        Schema::create('hearing_schedules', function (Blueprint $table) {
            $table->id();

            // Auto-generated hearing record number (e.g., HS001)
            $table->string('record_no')->unique();

            // Foreign key: respondent (from students table)
            $table->unsignedBigInteger('respondent_id');
            $table->foreign('respondent_id')
                ->references('student_id') // since your students table uses student_id as PK
                ->on('students')
                ->onDelete('cascade');

            // Foreign key: violation (from violation_categories table)
            $table->unsignedBigInteger('violation_id');
            $table->foreign('violation_id')
                ->references('id')
                ->on('violation_categories')
                ->onDelete('cascade');

            // Hearing details
            $table->string('complainant');
            $table->date('date_of_hearing');
            $table->time('time');
            $table->string('venue');
            $table->string('officer_panel')->nullable();

            // Default status = Pending
            $table->string('status')->default('Pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hearing_schedules');
    }
};