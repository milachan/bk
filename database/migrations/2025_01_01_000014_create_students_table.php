<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->unique();
            $table->string('nisn')->nullable()->unique();
            $table->string('name');
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('religion')->nullable();
            $table->text('address')->nullable();
            $table->string('phone')->nullable();
            $table->string('parent_name')->nullable();
            $table->string('parent_phone')->nullable();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->enum('status', ['aktif', 'lulus', 'pindah', 'keluar'])->default('aktif');
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
