@extends('layouts.app')
@section('title', 'Master Pelanggaran')
@section('content')

<div class="page-header">
    <div>
        <h4><i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Master Jenis Pelanggaran</h4>
        <small class="text-muted">Total: {{ $categories->total() }} data</small>
    </div>
    @if(auth()->user()->canEdit())
    <a href="{{ route('violation-categories.create') }}" class="btn btn-warning btn-sm">
        <i class="bi bi-plus-circle me-1"></i> Tambah Jenis Pelanggaran
    </a>
    @endif
</div>

<div class="table-card">
    <div class="table-responsive">
        <table class="table table-hover table-sm align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Nama Pelanggaran</th>
                    <th>Kategori</th>
                    <th>Poin</th>
                    <th>Keterangan</th>
                    <th class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $i => $c)
                <tr>
                    <td class="ps-3 text-muted small">{{ $categories->firstItem() + $i }}</td>
                    <td class="fw-semibold small">{{ $c->name }}</td>
                    <td>
                        @if($c->category == 'ringan')
                            <span class="badge badge-ringan">Ringan</span>
                        @elseif($c->category == 'sedang')
                            <span class="badge badge-sedang">Sedang</span>
                        @elseif($c->category == 'berat')
                            <span class="badge badge-berat">Berat</span>
                        @else
                            <span class="badge bg-secondary">{{ $c->category }}</span>
                        @endif
                    </td>
                    <td><span class="badge bg-danger">{{ $c->points }}</span></td>
                    <td class="small text-muted text-truncate" style="max-width:200px" title="{{ $c->description }}">
                        {{ $c->description ?? '-' }}
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            @if(auth()->user()->canEdit())
                            <a href="{{ route('violation-categories.edit', $c) }}" class="btn btn-light" title="Edit">
                                <i class="bi bi-pencil-fill text-warning"></i>
                            </a>
                            @endif
                            @if(auth()->user()->canDelete())
                            <button type="button" class="btn btn-light" title="Hapus"
                                onclick="confirmDelete('{{ route('violation-categories.destroy', $c) }}', '{{ addslashes($c->name) }}')">
                                <i class="bi bi-trash-fill text-danger"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">Belum ada data jenis pelanggaran</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="px-3 py-2 border-top">{{ $categories->links() }}</div>
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
                <p class="mb-1">Yakin ingin menghapus jenis pelanggaran:</p>
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
