<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reformation_programs', function (Blueprint $table) {
            $table->id();
            $table->string('program_id')->unique();
            $table->string('program_name');
            $table->text('description')->nullable();
            $table->string('duration');
            $table->string('responsible_office');
            $table->string('type');
            $table->string('status')->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reformation_programs');
    }
};