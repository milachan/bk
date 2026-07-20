@extends('layouts.app')
@section('title', 'Data Kelas')
@section('content')

<div class="page-header">
    <div>
        <h4><i class="bi bi-door-open-fill me-2 text-primary"></i>Data Kelas</h4>
        <small class="text-muted">Total: {{ $classes->total() }} data</small>
    </div>
    @if(auth()->user()->canEdit())
    <a href="{{ route('school-classes.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Kelas
    </a>
    @endif
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Nama Kelas</th>
                    <th>Tingkat</th>
                    <th>Wali Kelas</th>
                    <th>Tahun Ajaran</th>
                    <th>Jumlah Siswa</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($classes as $i => $c)
                <tr>
                    <td class="ps-3 text-muted small">{{ $classes->firstItem() + $i }}</td>
                    <td class="fw-semibold small">{{ $c->name }}</td>
                    <td><span class="badge bg-primary">{{ $c->level }}</span></td>
                    <td class="small">{{ $c->homeroomTeacher?->name ?? '-' }}</td>
                    <td class="small">{{ $c->schoolYear?->name ?? '-' }}</td>
                    <td><span class="badge bg-light text-dark">{{ $c->students_count ?? $c->students?->count() ?? 0 }} siswa</span></td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            @if(auth()->user()->canEdit())
                            <a href="{{ route('school-classes.edit', $c) }}" class="btn btn-light" title="Edit">
                                <i class="bi bi-pencil-fill text-warning"></i>
                            </a>
                            @endif
                            @if(auth()->user()->canDelete())
                            <button type="button" class="btn btn-light" title="Hapus"
                                onclick="confirmDelete('{{ route('school-classes.destroy', $c) }}', '{{ addslashes($c->name) }}')">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">Belum ada data kelas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($classes->hasPages())
    <div class="px-3 py-2 border-top">{{ $classes->links() }}</div>
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
                <p class="mb-1">Yakin ingin menghapus kelas:</p>
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
