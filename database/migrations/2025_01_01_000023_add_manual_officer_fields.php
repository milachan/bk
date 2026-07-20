<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Keterlambatan: nama petugas manual
        Schema::table('late_records', function (Blueprint $table) {
            $table->string('officer_name')->nullable()->after('officer_id');
        });

        // Pelanggaran: nama pelapor manual
        Schema::table('violation_records', function (Blueprint $table) {
            $table->string('reporter_name')->nullable()->after('reporter_id');
        });

        // Konseling: nama guru BK manual + guru tambahan (JSON)
        Schema::table('counselings', function (Blueprint $table) {
            $table->string('counselor_name')->nullable()->after('counselor_id');
            $table->json('extra_counselors')->nullable()->after('counselor_name'); // array nama tambahan
        });

        // Pemanggilan ortu: nama penangani manual + penangani tambahan (JSON)
        Schema::table('parent_meetings', function (Blueprint $table) {
            $table->string('handler_name')->nullable()->after('handler_id');
            $table->json('extra_handlers')->nullable()->after('handler_name');
        });

        // Home visit: nama petugas manual + petugas tambahan (JSON)
        Schema::table('home_visits', function (Blueprint $table) {
            $table->string('visitor_name')->nullable()->after('visitor_id');
            $table->json('extra_visitors')->nullable()->after('visitor_name');
        });
    }

    public function down(): void
    {
        Schema::table('late_records',    fn($t) => $t->dropColumn('officer_name'));
        Schema::table('violation_records', fn($t) => $t->dropColumn('reporter_name'));
        Schema::table('counselings',     fn($t) => $t->dropColumn(['counselor_name', 'extra_counselors']));
        Schema::table('parent_meetings', fn($t) => $t->dropColumn(['handler_name', 'extra_handlers']));
        Schema::table('home_visits',     fn($t) => $t->dropColumn(['visitor_name', 'extra_visitors']));
    }
};
