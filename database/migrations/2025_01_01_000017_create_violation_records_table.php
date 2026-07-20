<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('violation_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('violation_category_id')->constrained('violation_categories')->cascadeOnDelete();
            $table->date('date');
            $table->integer('points');
            $table->text('description')->nullable(); // kronologi
            $table->foreignId('reporter_id')->nullable()->constrained('users')->nullOnDelete(); // guru pelapor
            $table->text('notes')->nullable();
            $table->string('evidence_photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violation_records');
    }
};
