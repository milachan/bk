<!DOCTYPE html>
<html lang="id" data-bs-theme="{{ session('theme', 'light') }}">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}"/>
    <title>@yield('title', 'BK Digital') - BK Digital</title>

    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"/>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>

    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 60px;
            --primary: #0d6efd;
        }

        body { font-family: 'Segoe UI', sans-serif; background-color: #f0f2f5; }
        [data-bs-theme="dark"] body { background-color: #1a1d21; }

        /* Sidebar */
        #sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, #1e3a5f 0%, #0d2137 100%);
            position: fixed;
            top: 0; left: 0;
            z-index: 1040;
            transition: transform .3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }
        #sidebar.collapsed { transform: translateX(calc(-1 * var(--sidebar-width))); }

        .sidebar-brand {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .sidebar-brand h5 { color: #fff; font-weight: 700; margin: 0; font-size: 1.1rem; }
        .sidebar-brand small { color: rgba(255,255,255,.6); font-size: .72rem; }

        .sidebar-section {
            padding: .4rem 1rem .1rem;
            font-size: .68rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: rgba(255,255,255,.35);
            margin-top: .5rem;
        }

        .sidebar-nav .nav-link {
            color: rgba(255,255,255,.75);
            padding: .5rem 1.25rem;
            border-radius: .4rem;
            margin: .1rem .5rem;
            font-size: .875rem;
            display: flex;
            align-items: center;
            gap: .6rem;
            transition: all .2s;
        }
        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active {
            background: rgba(255,255,255,.12);
            color: #fff;
        }
        .sidebar-nav .nav-link i { font-size: 1rem; width: 1.25rem; text-align: center; flex-shrink: 0; }

        .sidebar-nav .collapse-toggle { cursor: pointer; }
        .sidebar-nav .collapse .nav-link {
            padding-left: 2.5rem;
            font-size: .83rem;
        }

        /* Topbar */
        #topbar {
            height: var(--header-height);
            position: fixed;
            top: 0;
            left: var(--sidebar-width);
            right: 0;
            z-index: 1030;
            background: #fff;
            border-bottom: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            padding: 0 1.25rem;
            gap: .75rem;
            transition: left .3s ease;
        }
        [data-bs-theme="dark"] #topbar { background: #212529; border-color: #495057; }
        #topbar.expanded { left: 0; }

        /* Main content */
        #main-content {
            margin-left: var(--sidebar-width);
            margin-top: var(--header-height);
            padding: 1.5rem;
            min-height: calc(100vh - var(--header-height));
            transition: margin-left .3s ease;
        }
        #main-content.expanded { margin-left: 0; }

        /* Page header */
        .page-header {
            background: #fff;
            border-radius: .75rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
            border: 1px solid #e9ecef;
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: .5rem;
        }
        [data-bs-theme="dark"] .page-header { background: #212529; border-color: #495057; }
        .page-header h4 { margin: 0; font-weight: 700; font-size: 1.1rem; }

        /* Stats cards */
        .stat-card {
            border-radius: .75rem;
            border: none;
            padding: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,.06);
            transition: transform .2s, box-shadow .2s;
        }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,.1); }
        .stat-card .icon {
            width: 52px; height: 52px;
            border-radius: .6rem;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }
        .stat-card .label { font-size: .78rem; color: #6c757d; margin-bottom: .1rem; }
        .stat-card .value { font-size: 1.6rem; font-weight: 700; line-height: 1; }

        /* Tables */
        .table-card {
            background: #fff;
            border-radius: .75rem;
            border: 1px solid #e9ecef;
            overflow: hidden;
        }
        [data-bs-theme="dark"] .table-card { background: #212529; border-color: #495057; }
        .table-card .card-header-custom {
            padding: .85rem 1.25rem;
            border-bottom: 1px solid #e9ecef;
            display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: .5rem;
            font-weight: 600;
        }
        [data-bs-theme="dark"] .table-card .card-header-custom { border-color: #495057; }

        /* Timeline */
        .timeline { position: relative; padding-left: 2rem; }
        .timeline::before { content: ''; position: absolute; left: .7rem; top: 0; bottom: 0; width: 2px; background: #dee2e6; }
        .timeline-item { position: relative; margin-bottom: 1.25rem; }
        .timeline-dot {
            position: absolute; left: -1.95rem; top: .25rem;
            width: 1.1rem; height: 1.1rem;
            border-radius: 50%;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px currentColor;
        }
        .timeline-content {
            background: #fff;
            border: 1px solid #e9ecef;
            border-radius: .6rem;
            padding: .85rem 1rem;
        }
        [data-bs-theme="dark"] .timeline-content { background: #2c3034; border-color: #495057; }

        /* Badges */
        .badge-ringan  { background-color: #d1e7dd; color: #0f5132; }
        .badge-sedang  { background-color: #fff3cd; color: #664d03; }
        .badge-berat   { background-color: #f8d7da; color: #842029; }

        /* Form cards */
        .form-card {
            background: #fff;
            border-radius: .75rem;
            border: 1px solid #e9ecef;
            padding: 1.5rem;
        }
        [data-bs-theme="dark"] .form-card { background: #212529; border-color: #495057; }

        /* Responsive */
        @media (max-width: 991.98px) {
            #sidebar { transform: translateX(calc(-1 * var(--sidebar-width))); }
            #sidebar.show { transform: translateX(0); }
            #topbar { left: 0 !important; }
            #main-content { margin-left: 0 !important; }
            .sidebar-overlay { display: block !important; }
        }
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,.5);
            z-index: 1039;
        }

        /* Select2 style fix */
        select.form-select { cursor: pointer; }

        /* Print */
        @media print {
            #sidebar, #topbar, .no-print { display: none !important; }
            #main-content { margin: 0 !important; padding: 0 !important; }
        }
    </style>

    @stack('styles')
</head>
<body>

<!-- Sidebar overlay (mobile) -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- Sidebar -->
<nav id="sidebar">
    <div class="sidebar-brand">
        <div class="d-flex align-items-center gap-2">
            <div style="width:38px;height:38px;background:rgba(255,255,255,.15);border-radius:.5rem;display:flex;align-items:center;justify-content:center;">
                <i class="bi bi-journal-bookmark-fill text-white fs-5"></i>
            </div>
            <div>
                <h5>BK Digital</h5>
                <small>Bimbingan Konseling</small>
            </div>
        </div>
    </div>

    <ul class="sidebar-nav nav flex-column py-2">
        <!-- Dashboard -->
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>

        @if(auth()->user()->hasRole(['admin', 'guru_bk', 'guru_piket', 'kepala_sekolah']))
        <!-- Master Data -->
        <li><div class="sidebar-section">Master Data</div></li>
        <li class="nav-item">
            <a href="{{ route('students.index') }}" class="nav-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Data Siswa
            </a>
        </li>

        @if(auth()->user()->hasRole(['admin']))
        <li class="nav-item">
            <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="bi bi-person-badge-fill"></i> Data Guru / User
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('school-classes.index') }}" class="nav-link {{ request()->routeIs('school-classes.*') ? 'active' : '' }}">
                <i class="bi bi-door-open-fill"></i> Data Kelas
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('school-years.index') }}" class="nav-link {{ request()->routeIs('school-years.*') ? 'active' : '' }}">
                <i class="bi bi-calendar3"></i> Tahun Ajaran
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('violation-categories.index') }}" class="nav-link {{ request()->routeIs('violation-categories.*') ? 'active' : '' }}">
                <i class="bi bi-exclamation-triangle-fill"></i> Master Pelanggaran
            </a>
        </li>
        @endif
        @endif

        @if(auth()->user()->hasRole(['admin', 'guru_bk', 'guru_piket']))
        <!-- Transaksi -->
        <li><div class="sidebar-section">Transaksi</div></li>
        <li class="nav-item">
            <a href="{{ route('quick-entry.create') }}" class="nav-link {{ request()->routeIs('quick-entry.*') ? 'active' : '' }}" style="background:rgba(255,193,7,.15);">
                <i class="bi bi-lightning-charge-fill text-warning"></i> Input Cepat
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('late-records.index') }}" class="nav-link {{ request()->routeIs('late-records.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i> Keterlambatan
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('violation-records.index') }}" class="nav-link {{ request()->routeIs('violation-records.*') ? 'active' : '' }}">
                <i class="bi bi-shield-exclamation"></i> Pelanggaran
            </a>
        </li>
        @endif

        @if(auth()->user()->hasRole(['admin', 'guru_bk']))
        <li class="nav-item">
            <a href="{{ route('counselings.index') }}" class="nav-link {{ request()->routeIs('counselings.*') ? 'active' : '' }}">
                <i class="bi bi-chat-heart-fill"></i> Konseling
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('parent-meetings.index') }}" class="nav-link {{ request()->routeIs('parent-meetings.*') ? 'active' : '' }}">
                <i class="bi bi-people-fill"></i> Pemanggilan Orang Tua
            </a>
        </li>
        <li class="nav-item">
            <a href="{{ route('home-visits.index') }}" class="nav-link {{ request()->routeIs('home-visits.*') ? 'active' : '' }}">
                <i class="bi bi-house-fill"></i> Home Visit
            </a>
        </li>
        @endif

        <!-- Laporan -->
        <li><div class="sidebar-section">Laporan</div></li>
        <li class="nav-item">
            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan
            </a>
        </li>

        <!-- Pengaturan -->
        <li><div class="sidebar-section">Akun</div></li>
        <li class="nav-item">
            <a href="{{ route('profile.edit') }}" class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}">
                <i class="bi bi-person-gear"></i> Profil Saya
            </a>
        </li>
        <li class="nav-item">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-link w-100 border-0 bg-transparent text-start" style="color:rgba(255,100,100,.8)">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            </form>
        </li>
    </ul>
</nav>

<!-- Topbar -->
<header id="topbar">
    <button class="btn btn-sm btn-light" onclick="toggleSidebar()" title="Toggle Sidebar">
        <i class="bi bi-list fs-5"></i>
    </button>

    <!-- Search bar -->
    <form action="{{ route('search') }}" method="GET" class="d-flex flex-grow-1 gap-2" style="max-width:400px;">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="q" class="form-control bg-light border-start-0" placeholder="Cari siswa, NIS, riwayat..." value="{{ request('q') }}"/>
        </div>
    </form>

    <div class="ms-auto d-flex align-items-center gap-2">
        <!-- Dark mode toggle -->
        <button class="btn btn-sm btn-light" onclick="toggleTheme()" title="Ganti tema">
            <i class="bi bi-moon-fill" id="themeIcon"></i>
        </button>

        <!-- User dropdown -->
        <div class="dropdown">
            <button class="btn btn-sm btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" data-bs-toggle="dropdown">
                <div style="width:28px;height:28px;background:#0d6efd;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;font-weight:700">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <span class="d-none d-md-block text-truncate" style="max-width:120px">{{ auth()->user()->name }}</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li><h6 class="dropdown-header">{{ auth()->user()->role?->label ?? 'User' }}</h6></li>
                <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person-gear me-2"></i>Profil Saya</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</header>

<!-- Main Content -->
<main id="main-content">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</main>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Sidebar toggle
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const topbar  = document.getElementById('topbar');
        const main    = document.getElementById('main-content');
        const overlay = document.getElementById('sidebarOverlay');
        const isMobile = window.innerWidth < 992;

        if (isMobile) {
            sidebar.classList.toggle('show');
            overlay.style.display = sidebar.classList.contains('show') ? 'block' : 'none';
        } else {
            sidebar.classList.toggle('collapsed');
            topbar.classList.toggle('expanded');
            main.classList.toggle('expanded');
        }
    }

    // Theme toggle
    function toggleTheme() {
        const html = document.documentElement;
        const icon = document.getElementById('themeIcon');
        const isDark = html.getAttribute('data-bs-theme') === 'dark';
        html.setAttribute('data-bs-theme', isDark ? 'light' : 'dark');
        icon.className = isDark ? 'bi bi-moon-fill' : 'bi bi-sun-fill';
        fetch('/profile/theme', { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }, body: JSON.stringify({ theme: isDark ? 'light' : 'dark' }) }).catch(() => {});
    }

    // Auto-dismiss alerts
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(el => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
            bsAlert.close();
        });
    }, 4000);
</script>

{{-- Global staff dropdown helpers --}}
<script>
function staffToggleManual(sel, manualId) {
    const el = document.getElementById(manualId);
    if (!el) return;
    if (sel.value === 'other') {
        el.classList.remove('d-none');
        el.focus();
    } else {
        el.classList.add('d-none');
        el.value = '';
    }
}
function staffToggleManualMulti(sel, manualId) {
    const el = document.getElementById(manualId);
    if (!el) return;
    const vals = Array.from(sel.selectedOptions).map(o => o.value);
    if (vals.includes('other')) {
        el.classList.remove('d-none');
        el.focus();
    } else {
        el.classList.add('d-none');
        el.value = '';
    }
}
</script>

@stack('scripts')
</body>
</html>
