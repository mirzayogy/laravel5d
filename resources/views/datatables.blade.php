
@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <div class="card mb-4">

        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h3 class="card-title">Data Pengguna</h3>
                </div>

                <div class="col-md-6">
                    <div class="d-flex justify-content-md-end">
                        <button type="button" class="btn btn-sm btn-success"
                            data-bs-toggle="modal"
                            data-bs-target="#modal-add-user">
                            <i class="bi bi-plus-circle me-1"></i>
                            Tambah Pengguna
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-body">
            <p class="text-secondary">
                Contoh tabel interaktif menggunakan Tabulator.
            </p>

            <div id="users-table"></div>
        </div>

        <div class="card-footer text-secondary small">
            Menggunakan
            <a href="https://tabulator.info/" target="_blank" rel="noopener">
                Tabulator
            </a>
            untuk menampilkan tabel interaktif.
        </div>

    </div>

</div>

<!-- Modal Tambah Pengguna -->
<div class="modal fade" id="modal-add-user" tabindex="-1"
    aria-labelledby="modal-add-user-label" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">

            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-add-user-label">
                        Tambah Pengguna
                    </h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new-user-name" class="form-label">
                            Nama Lengkap
                        </label>
                        <input type="text" class="form-control"
                            id="new-user-name" required>
                    </div>

                    <div class="mb-3">
                        <label for="new-user-email" class="form-label">
                            Alamat Email
                        </label>
                        <input type="email" class="form-control"
                            id="new-user-email" required>
                    </div>

                    <div class="mb-3">
                        <label for="new-user-role" class="form-label">
                            Role
                        </label>
                        <select id="new-user-role" class="form-select">
                            <option>Customer</option>
                            <option>Admin</option>
                        </select>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Modal Hapus Pengguna -->
<div class="modal fade" id="modal-delete-user" tabindex="-1"
    aria-labelledby="modal-delete-user-label" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modal-delete-user-label">
                    Hapus Pengguna
                </h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Yakin ingin menghapus
                <strong id="delete-user-name"></strong>?
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Batal
                </button>

                <button type="button" class="btn btn-danger"
                    id="confirm-delete">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>

        </div>
    </div>
</div>

@endsection

@push('styles')
<link rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/tabulator-tables@6.4.0/dist/css/tabulator_bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tabulator-tables@6.4.0/dist/js/tabulator.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const data = [
        { id: 1, name: 'Olivia Bennett', email: 'olivia@example.com', role: 'Admin', status: 'Active' },
        { id: 2, name: 'Liam Carter', email: 'liam@example.com', role: 'Customer', status: 'Active' },
        { id: 3, name: 'Emma Dawson', email: 'emma@example.com', role: 'Customer', status: 'Invited' },
        { id: 4, name: 'Noah Evans', email: 'noah@example.com', role: 'Admin', status: 'Suspended' },
        { id: 5, name: 'Ava Foster', email: 'ava@example.com', role: 'Customer', status: 'Active' },
        { id: 6, name: 'Ethan Grant', email: 'ethan@example.com', role: 'Customer', status: 'Active' },
        { id: 7, name: 'Sophia Hayes', email: 'sophia@example.com', role: 'Admin', status: 'Active' },
        { id: 8, name: 'Mason Ingram', email: 'mason@example.com', role: 'Customer', status: 'Invited' }
    ];

    const escapeHtml = (value) => String(value ?? '').replace(/[&<>"']/g, function (char) {
        return {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;'
        }[char];
    });

    const statusBadge = function (cell) {
        const value = cell.getValue();

        const colors = {
            Active: 'success',
            Invited: 'info',
            Suspended: 'secondary'
        };

        return `<span class="badge text-bg-${colors[value] || 'secondary'}">${escapeHtml(value)}</span>`;
    };

    const table = new Tabulator('#users-table', {
        data: data,
        layout: 'fitColumns',
        pagination: true,
        paginationSize: 5,
        paginationSizeSelector: [5, 10, 25],
        movableColumns: true,

        columns: [
            {
                title: '#',
                field: 'id',
                width: 70,
                hozAlign: 'center'
            },
            {
                title: 'Nama',
                field: 'name',
                headerFilter: 'input'
            },
            {
                title: 'Email',
                field: 'email',
                headerFilter: 'input'
            },
            {
                title: 'Role',
                field: 'role',
                headerFilter: 'list',
                headerFilterParams: {
                    values: ['', 'Admin', 'Customer']
                }
            },
            {
                title: 'Status',
                field: 'status',
                formatter: statusBadge,
                headerFilter: 'list',
                headerFilterParams: {
                    values: ['', 'Active', 'Invited', 'Suspended']
                },
                hozAlign: 'center'
            },
            {
                title: 'Aksi',
                field: 'id',
                hozAlign: 'center',
                headerSort: false,
                formatter: function (cell) {
                    const row = cell.getRow().getData();

                    return `
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('edit') }}"
                                class="btn btn-outline-secondary"
                                title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>

                            <button type="button"
                                class="btn btn-outline-danger delete-user"
                                data-id="${row.id}"
                                data-name="${escapeHtml(row.name)}"
                                data-bs-toggle="modal"
                                data-bs-target="#modal-delete-user"
                                title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                }
            }
        ]
    });

    const modalElement = document.getElementById('modal-delete-user');
    let deleteId = null;

    modalElement.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        deleteId = button.dataset.id;

        document.getElementById('delete-user-name').textContent =
            button.dataset.name;
    });

    document.getElementById('confirm-delete').addEventListener('click', function () {
        table.deleteRow(deleteId);

        bootstrap.Modal.getInstance(modalElement).hide();
    });

});
</script>
@endpush
