<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('parent_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->date('meeting_date');
            $table->text('reason');
            $table->boolean('parent_attended')->default(false);
            $table->text('meeting_result')->nullable();
            $table->text('agreement')->nullable();
            $table->text('follow_up')->nullable();
            $table->foreignId('handler_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parent_meetings');
    }
};
