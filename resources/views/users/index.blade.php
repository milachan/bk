@extends('layouts.app')
@section('title', 'Data User')
@section('content')

<div class="page-header">
    <div>
        <h4><i class="bi bi-person-badge-fill me-2 text-primary"></i>Data Guru / User</h4>
        <small class="text-muted">Total: {{ $users->total() }} data</small>
    </div>
    @if(auth()->user()->canEdit())
    <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah User
    </a>
    @endif
</div>

<div class="form-card mb-3">
    <form method="GET" class="row g-2">
        <div class="col-12 col-md-6">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama atau email..." value="{{ request('search') }}"/>
        </div>
        <div class="col-6 col-md-3">
            <select name="role_id" class="form-select form-select-sm">
                <option value="">Semua Role</option>
                @foreach($roles as $r)
                <option value="{{ $r->id }}" {{ request('role_id') == $r->id ? 'selected' : '' }}>{{ $r->label ?? $r->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm flex-fill">Cari</button>
            <a href="{{ route('users.index') }}" class="btn btn-light btn-sm">Reset</a>
        </div>
    </form>
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Jabatan</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $i => $u)
                <tr>
                    <td class="ps-3 text-muted small">{{ $users->firstItem() + $i }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div style="width:30px;height:30px;background:#0d6efd;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.75rem;font-weight:700;flex-shrink:0">
                                {{ strtoupper(substr($u->name, 0, 1)) }}
                            </div>
                            <span class="fw-semibold small">{{ $u->name }}</span>
                        </div>
                    </td>
                    <td class="small text-muted">{{ $u->email }}</td>
                    <td class="small">{{ $u->jabatan ?? '-' }}</td>
                    <td>
                        @php $roleName = $u->role?->name ?? '' @endphp
                        @if($roleName == 'admin')
                            <span class="badge bg-danger">Admin</span>
                        @elseif($roleName == 'guru_bk')
                            <span class="badge bg-success">Guru BK</span>
                        @elseif($roleName == 'guru_piket')
                            <span class="badge bg-warning text-dark">Guru Piket</span>
                        @elseif($roleName == 'kepala_sekolah')
                            <span class="badge bg-primary">Kepala Sekolah</span>
                        @else
                            <span class="badge bg-secondary">{{ $u->role?->label ?? '-' }}</span>
                        @endif
                    </td>
                    <td>
                        @if($u->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            @if(auth()->user()->canEdit())
                            <a href="{{ route('users.edit', $u) }}" class="btn btn-light" title="Edit">
                                <i class="bi bi-pencil-fill text-warning"></i>
                            </a>
                            @endif
                            @if(auth()->user()->canDelete() && $u->id !== auth()->id())
                            <button type="button" class="btn btn-light" title="Hapus"
                                onclick="confirmDelete('{{ route('users.destroy', $u) }}', '{{ addslashes($u->name) }}')">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data user</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
    <div class="px-3 py-2 border-top">{{ $users->links() }}</div>
    @endif
</div>
@endsection

{{-- Modal Konfirmasi Hapus --}}
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h6 class="modal-title fw-bold text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Konfirmasi Hapus</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="mb-1">Yakin ingin menghapus user:</p>
                <p class="fw-bold" id="deleteTargetName"></p>
                <p class="text-muted small mb-0">Data yang dihapus tidak dapat dikembalikan.</p>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-danger btn-sm" id="deleteConfirmBtn">Ya, Hapus</button>
            </div>
        </div>
    </div>
</div>
<form id="deleteForm" method="POST" style="display:none">
    @csrf @method('DELETE')
</form>

@push('scripts')
<script>
function confirmDelete(url, name) {
    document.getElementById('deleteTargetName').textContent = name;
    document.getElementById('deleteForm').action = url;
    document.getElementById('deleteConfirmBtn').onclick = () => document.getElementById('deleteForm').submit();
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
