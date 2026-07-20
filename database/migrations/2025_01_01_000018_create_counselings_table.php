<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('counselings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('date');
            $table->text('problem');
            $table->text('result')->nullable();
            $table->text('solution')->nullable();
            $table->text('follow_up')->nullable();
            $table->foreignId('counselor_id')->nullable()->constrained('users')->nullOnDelete(); // guru BK
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('counselings');
    }
};
