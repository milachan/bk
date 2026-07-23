<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\SchoolClass;
use App\Models\SchoolYear;
use App\Models\Student;
use App\Models\User;
use App\Models\ViolationCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Roles
        $roles = [
            ['name' => 'admin',           'label' => 'Administrator'],
            ['name' => 'guru_bk',         'label' => 'Guru BK'],
            ['name' => 'guru_piket',      'label' => 'Guru Piket'],
            ['name' => 'kepala_sekolah',  'label' => 'Kepala Sekolah'],
        ];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role['name']], $role);
        }

        $adminRole   = Role::where('name', 'admin')->first();
        $bkRole      = Role::where('name', 'guru_bk')->first();
        $piketRole   = Role::where('name', 'guru_piket')->first();
        $kepsekRole  = Role::where('name', 'kepala_sekolah')->first();

        // Users
        User::firstOrCreate(['email' => 'admin@bkdigital.id'], [
            'name'     => 'Administrator',
            'email'    => 'admin@bkdigital.id',
            'password' => Hash::make('admin123'),
            'role_id'  => $adminRole->id,
            'jabatan'  => 'Administrator',
        ]);

        User::firstOrCreate(['email' => 'bk@bkdigital.id'], [
            'name'     => 'Ibu Sari (Guru BK)',
            'email'    => 'bk@bkdigital.id',
            'password' => Hash::make('bk123'),
            'role_id'  => $bkRole->id,
            'jabatan'  => 'Guru BK',
        ]);

        $piketUser = User::firstOrCreate(['email' => 'piket@bkdigital.id'], [
            'name'     => 'Pak Budi (Guru Piket)',
            'email'    => 'piket@bkdigital.id',
            'password' => Hash::make('piket123'),
            'role_id'  => $piketRole->id,
            'jabatan'  => 'Guru Piket',
        ]);

        User::firstOrCreate(['email' => 'kepsek@bkdigital.id'], [
            'name'     => 'Bapak Kepala Sekolah',
            'email'    => 'kepsek@bkdigital.id',
            'password' => Hash::make('kepsek123'),
            'role_id'  => $kepsekRole->id,
            'jabatan'  => 'Kepala Sekolah',
        ]);

        // Wali kelas guru
        $wali1 = User::firstOrCreate(['email' => 'wali1@bkdigital.id'], [
            'name'     => 'Ibu Dewi (Wali Kelas X-A)',
            'email'    => 'wali1@bkdigital.id',
            'password' => Hash::make('wali123'),
            'role_id'  => $bkRole->id,
            'jabatan'  => 'Guru / Wali Kelas',
        ]);
        $wali2 = User::firstOrCreate(['email' => 'wali2@bkdigital.id'], [
            'name'     => 'Pak Rudi (Wali Kelas XI-B)',
            'email'    => 'wali2@bkdigital.id',
            'password' => Hash::make('wali123'),
            'role_id'  => $bkRole->id,
            'jabatan'  => 'Guru / Wali Kelas',
        ]);

        // School Year
        $sy = SchoolYear::firstOrCreate(['name' => '2024/2025'], [
            'name'       => '2024/2025',
            'start_date' => '2024-07-15',
            'end_date'   => '2025-06-30',
            'is_active'  => true,
        ]);

        // Classes
        $classes = [
            ['name' => 'VII A',  'level' => 'VII',  'homeroom_teacher' => 'Ibu Dewi',               'school_year_id' => $sy->id],
            ['name' => 'VII B',  'level' => 'VII',  'homeroom_teacher' => 'Pak Rudi',               'school_year_id' => $sy->id],
            ['name' => 'VIII A', 'level' => 'VIII', 'homeroom_teacher' => null,                     'school_year_id' => $sy->id],
            ['name' => 'VIII B', 'level' => 'VIII', 'homeroom_teacher' => null,                     'school_year_id' => $sy->id],
            ['name' => 'IX A',   'level' => 'IX',   'homeroom_teacher' => null,                     'school_year_id' => $sy->id],
            ['name' => 'IX B',   'level' => 'IX',   'homeroom_teacher' => null,                     'school_year_id' => $sy->id],
        ];
        foreach ($classes as $cls) {
            SchoolClass::firstOrCreate(['name' => $cls['name'], 'school_year_id' => $sy->id], $cls);
        }

        $class1 = SchoolClass::where('name', 'X IPA 1')->first();
        $class2 = SchoolClass::where('name', 'X IPA 2')->first();
        $class3 = SchoolClass::where('name', 'XI IPA 1')->first();

        // Violation Categories
        $violations = [
            ['name' => 'Tidak memakai atribut lengkap', 'category' => 'ringan',  'points' => 5,  'description' => 'Siswa tidak memakai atribut seragam secara lengkap'],
            ['name' => 'Rambut panjang / tidak rapi',   'category' => 'ringan',  'points' => 5,  'description' => 'Rambut melebihi batas ketentuan atau tidak rapi'],
            ['name' => 'Tidak mengerjakan tugas',        'category' => 'ringan',  'points' => 5,  'description' => 'Siswa tidak mengumpulkan atau mengerjakan tugas'],
            ['name' => 'Keluar kelas tanpa izin',        'category' => 'ringan',  'points' => 10, 'description' => 'Meninggalkan kelas tanpa seizin guru'],
            ['name' => 'Membawa HP ke sekolah',          'category' => 'sedang',  'points' => 15, 'description' => 'Membawa handphone tanpa izin'],
            ['name' => 'Membolos',                       'category' => 'sedang',  'points' => 20, 'description' => 'Tidak hadir tanpa keterangan yang sah'],
            ['name' => 'Merokok di lingkungan sekolah',  'category' => 'berat',   'points' => 35, 'description' => 'Merokok di dalam atau sekitar area sekolah'],
            ['name' => 'Berkelahi',                      'category' => 'berat',   'points' => 50, 'description' => 'Terlibat perkelahian dengan siswa lain'],
        ];
        foreach ($violations as $v) {
            ViolationCategory::firstOrCreate(['name' => $v['name']], $v);
        }

        // Sample students
        $students = [
            ['nis' => '2024001', 'nisn' => '1234567801', 'name' => 'Ahmad Fauzi',        'gender' => 'L', 'birth_place' => 'Jakarta',   'birth_date' => '2008-03-15', 'religion' => 'Islam',  'address' => 'Jl. Merdeka No.1 Jakarta',    'phone' => '08111111111', 'parent_name' => 'Bapak Fauzi',    'parent_phone' => '08122222222', 'class_id' => $class1->id, 'status' => 'aktif'],
            ['nis' => '2024002', 'nisn' => '1234567802', 'name' => 'Siti Rahayu',        'gender' => 'P', 'birth_place' => 'Bandung',   'birth_date' => '2008-07-22', 'religion' => 'Islam',  'address' => 'Jl. Sudirman No.5 Bandung',   'phone' => '08211111111', 'parent_name' => 'Ibu Rahayu',     'parent_phone' => '08222222222', 'class_id' => $class1->id, 'status' => 'aktif'],
            ['nis' => '2024003', 'nisn' => '1234567803', 'name' => 'Budi Santoso',       'gender' => 'L', 'birth_place' => 'Surabaya',  'birth_date' => '2008-01-10', 'religion' => 'Islam',  'address' => 'Jl. Pahlawan No.3 Surabaya',  'phone' => '08311111111', 'parent_name' => 'Bapak Santoso',  'parent_phone' => '08322222222', 'class_id' => $class1->id, 'status' => 'aktif'],
            ['nis' => '2024004', 'nisn' => '1234567804', 'name' => 'Dewi Lestari',       'gender' => 'P', 'birth_place' => 'Yogyakarta', 'birth_date' => '2008-05-30', 'religion' => 'Kristen','address' => 'Jl. Malioboro No.7 Yogyakarta','phone' => '08411111111', 'parent_name' => 'Ibu Lestari',    'parent_phone' => '08422222222', 'class_id' => $class2->id, 'status' => 'aktif'],
            ['nis' => '2024005', 'nisn' => '1234567805', 'name' => 'Reza Pratama',       'gender' => 'L', 'birth_place' => 'Semarang',  'birth_date' => '2007-11-05', 'religion' => 'Islam',  'address' => 'Jl. Pemuda No.12 Semarang',   'phone' => '08511111111', 'parent_name' => 'Bapak Pratama',  'parent_phone' => '08522222222', 'class_id' => $class2->id, 'status' => 'aktif'],
            ['nis' => '2024006', 'nisn' => '1234567806', 'name' => 'Rina Wulandari',     'gender' => 'P', 'birth_place' => 'Malang',    'birth_date' => '2007-09-18', 'religion' => 'Islam',  'address' => 'Jl. Ijen No.3 Malang',        'phone' => '08611111111', 'parent_name' => 'Bapak Wulandari','parent_phone' => '08622222222', 'class_id' => $class2->id, 'status' => 'aktif'],
            ['nis' => '2024007', 'nisn' => '1234567807', 'name' => 'Doni Setiawan',      'gender' => 'L', 'birth_place' => 'Bekasi',    'birth_date' => '2007-04-25', 'religion' => 'Islam',  'address' => 'Jl. Ahmad Yani No.8 Bekasi',  'phone' => '08711111111', 'parent_name' => 'Ibu Setiawan',   'parent_phone' => '08722222222', 'class_id' => $class3->id, 'status' => 'aktif'],
            ['nis' => '2024008', 'nisn' => '1234567808', 'name' => 'Maya Indah Sari',    'gender' => 'P', 'birth_place' => 'Depok',     'birth_date' => '2007-06-12', 'religion' => 'Islam',  'address' => 'Jl. Margonda No.15 Depok',    'phone' => '08811111111', 'parent_name' => 'Bapak Indah',    'parent_phone' => '08822222222', 'class_id' => $class3->id, 'status' => 'aktif'],
            ['nis' => '2024009', 'nisn' => '1234567809', 'name' => 'Fajar Nugroho',      'gender' => 'L', 'birth_place' => 'Tangerang', 'birth_date' => '2008-08-20', 'religion' => 'Islam',  'address' => 'Jl. MH Thamrin No.4 Tangerang','phone' => '08911111111', 'parent_name' => 'Bapak Nugroho',  'parent_phone' => '08922222222', 'class_id' => $class1->id, 'status' => 'aktif'],
            ['nis' => '2024010', 'nisn' => '1234567810', 'name' => 'Laila Nur Fadilah',  'gender' => 'P', 'birth_place' => 'Bogor',     'birth_date' => '2008-02-14', 'religion' => 'Islam',  'address' => 'Jl. Pajajaran No.6 Bogor',    'phone' => '09011111111', 'parent_name' => 'Ibu Fadilah',    'parent_phone' => '09022222222', 'class_id' => $class1->id, 'status' => 'aktif'],
        ];
        foreach ($students as $s) {
            Student::firstOrCreate(['nis' => $s['nis']], $s);
        }

        // Sample late records
        $student1 = Student::where('nis', '2024001')->first();
        $student2 = Student::where('nis', '2024003')->first();
        $student3 = Student::where('nis', '2024005')->first();

        $today = now()->format('Y-m-d');
        $bkUser = User::where('email', 'bk@bkdigital.id')->first();

        $lates = [
            ['student_id' => $student1->id, 'date' => $today,              'arrive_time' => '07:30:00', 'entry_time' => '08:00:00', 'duration_minutes' => 30, 'reason' => 'Macet',           'officer_id' => $piketUser->id],
            ['student_id' => $student2->id, 'date' => $today,              'arrive_time' => '07:45:00', 'entry_time' => '08:00:00', 'duration_minutes' => 45, 'reason' => 'Bangun kesiangan', 'officer_id' => $piketUser->id],
            ['student_id' => $student1->id, 'date' => now()->subDays(2)->format('Y-m-d'), 'arrive_time' => '07:35:00', 'entry_time' => '08:00:00', 'duration_minutes' => 35, 'reason' => 'Kendaraan rusak', 'officer_id' => $piketUser->id],
            ['student_id' => $student3->id, 'date' => now()->subDays(3)->format('Y-m-d'), 'arrive_time' => '07:50:00', 'entry_time' => '08:00:00', 'duration_minutes' => 50, 'reason' => 'Terlambat bangun', 'officer_id' => $piketUser->id],
            ['student_id' => $student2->id, 'date' => now()->subDays(5)->format('Y-m-d'), 'arrive_time' => '07:40:00', 'entry_time' => '08:00:00', 'duration_minutes' => 40, 'reason' => 'Macet parah',     'officer_id' => $piketUser->id],
        ];
        foreach ($lates as $late) {
            \App\Models\LateRecord::firstOrCreate(
                ['student_id' => $late['student_id'], 'date' => $late['date']],
                $late
            );
        }

        // Sample violations
        $vc1 = ViolationCategory::where('name', 'Membolos')->first();
        $vc2 = ViolationCategory::where('name', 'Membawa HP ke sekolah')->first();
        $vc3 = ViolationCategory::where('name', 'Tidak memakai atribut lengkap')->first();

        $violationRecords = [
            ['student_id' => $student1->id, 'violation_category_id' => $vc2->id, 'date' => now()->subDays(1)->format('Y-m-d'),  'points' => $vc2->points, 'description' => 'Ketahuan membawa HP saat pelajaran berlangsung', 'reporter_id' => $bkUser->id],
            ['student_id' => $student2->id, 'violation_category_id' => $vc1->id, 'date' => now()->subDays(3)->format('Y-m-d'),  'points' => $vc1->points, 'description' => 'Tidak hadir tanpa keterangan selama 2 hari',         'reporter_id' => $bkUser->id],
            ['student_id' => $student3->id, 'violation_category_id' => $vc3->id, 'date' => now()->subDays(7)->format('Y-m-d'),  'points' => $vc3->points, 'description' => 'Tidak memakai dasi dan topi saat upacara',            'reporter_id' => $bkUser->id],
            ['student_id' => $student1->id, 'violation_category_id' => $vc1->id, 'date' => now()->subDays(10)->format('Y-m-d'), 'points' => $vc1->points, 'description' => 'Membolos saat jam pelajaran ke-5 dan ke-6',           'reporter_id' => $bkUser->id],
        ];
        foreach ($violationRecords as $vr) {
            \App\Models\ViolationRecord::firstOrCreate(
                ['student_id' => $vr['student_id'], 'date' => $vr['date'], 'violation_category_id' => $vr['violation_category_id']],
                $vr
            );
        }

        // Sample counselings (without follow_up — column dropped)
        \App\Models\Counseling::firstOrCreate(
            ['student_id' => $student1->id, 'date' => now()->subDays(5)->format('Y-m-d')],
            ['student_id' => $student1->id, 'date' => now()->subDays(5)->format('Y-m-d'), 'problem' => 'Sering terlambat dan motivasi belajar menurun', 'result' => 'Siswa mengakui kesulitan bangun pagi dan merasa kurang semangat', 'solution' => 'Diberikan motivasi dan jadwal belajar terstruktur', 'counselor_id' => $bkUser->id]
        );
        \App\Models\Counseling::firstOrCreate(
            ['student_id' => $student2->id, 'date' => now()->subDays(2)->format('Y-m-d')],
            ['student_id' => $student2->id, 'date' => now()->subDays(2)->format('Y-m-d'), 'problem' => 'Membolos karena masalah dengan teman sekelas', 'result' => 'Terdapat konflik dengan teman, siswa merasa tidak nyaman', 'solution' => 'Mediasi dengan teman yang bersangkutan', 'counselor_id' => $bkUser->id]
        );

        // Sample parent meeting (without agreement — column dropped)
        \App\Models\ParentMeeting::firstOrCreate(
            ['student_id' => $student2->id, 'meeting_date' => now()->subDays(1)->format('Y-m-d')],
            ['student_id' => $student2->id, 'meeting_date' => now()->subDays(1)->format('Y-m-d'), 'reason' => 'Siswa sering membolos, perlu perhatian dari orang tua', 'parent_attended' => true, 'meeting_result' => 'Orang tua berjanji akan lebih memantau anaknya', 'handler_id' => $bkUser->id]
        );

        // Sample home visit
        \App\Models\HomeVisit::firstOrCreate(
            ['student_id' => $student1->id, 'visit_date' => now()->subDays(7)->format('Y-m-d')],
            ['student_id' => $student1->id, 'visit_date' => now()->subDays(7)->format('Y-m-d'), 'address' => 'Jl. Merdeka No.1 Jakarta', 'purpose' => 'Menindaklanjuti ketidakhadiran siswa selama 3 hari', 'result' => 'Siswa mengalami sakit dan kondisi rumah kurang kondusif untuk belajar', 'conclusion' => 'Perlu pendampingan lebih intensif', 'follow_up' => 'Koordinasi dengan wali kelas', 'visitor_id' => $bkUser->id]
        );
    }
}
