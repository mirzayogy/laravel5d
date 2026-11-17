<div class="modal fade" id="modal-hapus-{{ $periode->id }}" tabindex="-1"
    aria-labelledby="modal-hapus-label-{{ $periode->id }}" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-start">
            <div class="modal-header">
                <h5 class="modal-title" id="modal-hapus-label-{{ $periode->id }}">Hapus periode</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">
                    Yakin ingin menghapus periode <strong>{{ $periode->periode }}</strong>?
                    Data yang sudah dihapus tidak dapat dikembalikan.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('periode.destroy', $periode) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-1" aria-hidden="true"></i> Hapus
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>