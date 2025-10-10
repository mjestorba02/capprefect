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
        Schema::create('behaviors', function (Blueprint $table) {
            $table->id();
            $table->string('behavior_id')->unique(); // B001, B002, etc.
            $table->string('student_id'); // FK to students table
            $table->date('date_recorded');
            $table->enum('behavior_type', ['Positive', 'Negative']);
            $table->string('behavior_category');
            $table->text('description');
            $table->string('recorded_by');
            $table->string('action_taken')->nullable();
            $table->integer('points')->default(0);
            $table->enum('status', ['Active', 'Resolved'])->default('Active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('behaviors');
    }
};
