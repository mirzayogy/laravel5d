
@extends('layouts.app')

@section('content')

<div class="container-fluid">

    <!-- Tabel User Directory -->
    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">

                <div class="card-header">
                    <div class="row g-2 align-items-center">
                        <div class="col-12 col-md-4">
                            <h3 class="card-title">User Directory</h3>
                        </div>

                        <div class="col-12 col-md-8">
                            <div class="d-flex justify-content-md-end">
                                <button type="button" class="btn btn-sm btn-success"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modal-add-user">
                                    <i class="bi bi-plus-circle me-1"></i>
                                    New User
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Task</th>
                                    <th>Progress</th>
                                    <th style="width: 100px">Aksi</th>
                                </tr>
                            </thead>

                            <tbody>
                                <tr class="align-middle">
                                    <td>1.</td>
                                    <td>Update software</td>
                                    <td>50%</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('edit') }}"
                                                class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-user">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="align-middle">
                                    <td>2.</td>
                                    <td>Clean database</td>
                                    <td>70%</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('edit') }}"
                                                class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-user">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="align-middle">
                                    <td>3.</td>
                                    <td>Cron job running</td>
                                    <td>30%</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('edit') }}"
                                                class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-user">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <tr class="align-middle">
                                    <td>4.</td>
                                    <td>Fix and squish bugs</td>
                                    <td>90%</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('edit') }}"
                                                class="btn btn-outline-secondary">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button"
                                                class="btn btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modal-delete-user">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<!-- Modal Add User -->
<div class="modal fade" id="modal-add-user" tabindex="-1"
    aria-labelledby="modal-add-user-label" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">

            <form>
                <div class="modal-header">
                    <h5 class="modal-title" id="modal-add-user-label">
                        Add New User
                    </h5>
                    <button type="button" class="btn-close"
                        data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" class="form-control"
                            placeholder="e.g. Jane Doe" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" class="form-control"
                            placeholder="name@example.com" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select class="form-select">
                            <option>Subscriber</option>
                            <option>Author</option>
                            <option>Editor</option>
                            <option>Administrator</option>
                        </select>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox"
                            id="welcome-email" checked>
                        <label class="form-check-label" for="welcome-email">
                            Send a welcome email with login details
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary"
                        data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Create User
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Modal Delete User -->
<div class="modal fade" id="modal-delete-user" tabindex="-1"
    aria-labelledby="modal-delete-user-label" aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="modal-delete-user-label">
                    Delete User
                </h5>
                <button type="button" class="btn-close"
                    data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to delete this user?
                This action cannot be undone.
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-danger"
                    data-bs-dismiss="modal">
                    Delete User
                </button>
            </div>

        </div>
    </div>
</div>

@endsection
