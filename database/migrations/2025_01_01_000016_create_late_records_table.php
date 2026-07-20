<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('late_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('date');
            $table->time('arrive_time')->nullable();
            $table->time('entry_time')->nullable();
            $table->integer('duration_minutes')->nullable(); // lama keterlambatan
            $table->string('reason')->nullable();
            $table->foreignId('officer_id')->nullable()->constrained('users')->nullOnDelete(); // guru piket
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('late_records');
    }
};
