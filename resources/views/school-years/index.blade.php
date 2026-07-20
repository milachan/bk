@extends('layouts.app')
@section('title', 'Tahun Ajaran')
@section('content')

<div class="page-header">
    <div>
        <h4><i class="bi bi-calendar3 me-2 text-primary"></i>Tahun Ajaran</h4>
        <small class="text-muted">Total: {{ $schoolYears->total() }} data</small>
    </div>
    @if(auth()->user()->canEdit())
    <a href="{{ route('school-years.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Tahun Ajaran
    </a>
    @endif
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Nama Tahun Ajaran</th>
                    <th>Tanggal Mulai</th>
                    <th>Tanggal Selesai</th>
                    <th>Status</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schoolYears as $i => $sy)
                <tr>
                    <td class="ps-3 text-muted small">{{ $schoolYears->firstItem() + $i }}</td>
                    <td class="fw-semibold small">{{ $sy->name }}</td>
                    <td class="small">{{ $sy->start_date?->format('d/m/Y') }}</td>
                    <td class="small">{{ $sy->end_date?->format('d/m/Y') }}</td>
                    <td>
                        @if($sy->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Tidak Aktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            @if(auth()->user()->canEdit())
                            <a href="{{ route('school-years.edit', $sy) }}" class="btn btn-light" title="Edit">
                                <i class="bi bi-pencil-fill text-warning"></i>
                            </a>
                            @endif
                            @if(auth()->user()->canDelete())
                            <button type="button" class="btn btn-light" title="Hapus"
                                onclick="confirmDelete('{{ route('school-years.destroy', $sy) }}', '{{ addslashes($sy->name) }}')">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data tahun ajaran</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($schoolYears->hasPages())
    <div class="px-3 py-2 border-top">{{ $schoolYears->links() }}</div>
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
                <p class="mb-1">Yakin ingin menghapus tahun ajaran:</p>
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
