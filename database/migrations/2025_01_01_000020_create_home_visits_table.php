<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('home_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('visit_date');
            $table->text('address');
            $table->text('purpose');
            $table->text('result')->nullable();
            $table->text('conclusion')->nullable();
            $table->text('follow_up')->nullable();
            $table->foreignId('visitor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('home_visits');
    }
};
