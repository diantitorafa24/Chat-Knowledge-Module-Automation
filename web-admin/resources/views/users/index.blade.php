@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Daftar Pengguna</h5>
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-person-plus"></i> Tambah
                </button>
            </div>
            <div class="card-body p-0">
                @if(session('success'))
                    <div class="alert alert-success m-3">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger m-3">{{ session('error') }}</div>
                @endif
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th class="text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $u)
                            <tr>
                                <td>{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    <span class="badge {{ $u->role === 'superadmin' ? 'bg-danger' : 'bg-primary' }}">
                                        {{ ucfirst($u->role) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $u->id }}">Edit</button>
                                    <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus pengguna ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" {{ $u->id === auth()->id() ? 'disabled' : '' }}>Hapus</button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Modal -->
                            <div class="modal fade" id="editModal{{ $u->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form class="modal-content" action="{{ route('users.update', $u) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title">Edit Pengguna</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label>Nama</label>
                                                <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Email</label>
                                                <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                                            </div>
                                            <div class="mb-3">
                                                <label>Role</label>
                                                <select name="role" class="form-select">
                                                    <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin</option>
                                                    <option value="superadmin" {{ $u->role === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label>Password (Kosongkan jika tidak diubah)</label>
                                                <input type="password" name="password" class="form-control" minlength="8">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="submit" class="btn btn-primary">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Tambah Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label>Nama</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Role</label>
                    <select name="role" class="form-select">
                        <option value="admin">Admin</option>
                        <option value="superadmin">Superadmin</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Password Baru</label>
                    <input type="password" name="password" class="form-control" required minlength="8">
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
