<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('violation_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category'); // ringan, sedang, berat
            $table->integer('points')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('violation_categories');
    }
};
