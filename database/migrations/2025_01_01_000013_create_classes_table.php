<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g. X IPA 1
            $table->string('level'); // X, XI, XII
            $table->foreignId('homeroom_teacher_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('school_year_id')->nullable()->constrained('school_years')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
