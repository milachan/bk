<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Violation records: points jadi nullable (tidak wajib)
        Schema::table('violation_records', function (Blueprint $table) {
            $table->integer('points')->nullable()->change();
        });

        // Counseling: hapus follow_up, tambah attachment
        Schema::table('counselings', function (Blueprint $table) {
            $table->dropColumn('follow_up');
            $table->string('attachment')->nullable()->after('solution');
        });

        // Parent meetings: hapus agreement, tambah attachment
        Schema::table('parent_meetings', function (Blueprint $table) {
            $table->dropColumn('agreement');
            $table->string('attachment')->nullable()->after('meeting_result');
        });

        // Home visits: tambah attachment
        Schema::table('home_visits', function (Blueprint $table) {
            $table->string('attachment')->nullable()->after('conclusion');
        });
    }

    public function down(): void
    {
        Schema::table('violation_records', function (Blueprint $table) {
            $table->integer('points')->nullable(false)->change();
        });

        Schema::table('counselings', function (Blueprint $table) {
            $table->dropColumn('attachment');
            $table->text('follow_up')->nullable()->after('solution');
        });

        Schema::table('parent_meetings', function (Blueprint $table) {
            $table->dropColumn('attachment');
            $table->text('agreement')->nullable()->after('meeting_result');
        });

        Schema::table('home_visits', function (Blueprint $table) {
            $table->dropColumn('attachment');
        });
    }
};