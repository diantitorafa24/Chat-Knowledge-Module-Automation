@extends('layouts.app')
@section('title', 'Manajemen Pengguna')
@section('content')
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0 fw-semibold text-dark">Daftar Pengguna</h5>
                <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addModal">
                    <i class="bi bi-person-plus-fill me-1"></i> Tambah Pengguna
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
                                <th class="ps-3">Nama</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $u)
                            <tr>
                                <td class="ps-3 fw-medium">{{ $u->name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    <span class="badge {{ $u->role === 'superadmin' ? 'bg-danger' : 'bg-primary' }}">
                                        {{ ucfirst($u->role) }}
                                    </span>
                                </td>
                                <td class="text-end pe-3">
                                    <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editModal{{ $u->id }}">
                                        <i class="bi bi-pencil-square"></i> Edit
                                    </button>
                                    <form action="{{ route('users.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus pengguna ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" {{ $u->id === auth()->id() ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── MODALS DILETAKKAN DI LUAR TABLE & CARD AGAR TIDAK BENTROK Z-INDEX ─── --}}
@foreach($users as $u)
<div class="modal fade" id="editModal{{ $u->id }}" tabindex="-1" aria-labelledby="editModalLabel{{ $u->id }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content shadow border-0" action="{{ route('users.update', $u) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="editModalLabel{{ $u->id }}">Edit Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama</label>
                    <input type="text" name="name" class="form-control" value="{{ $u->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $u->email }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Role</label>
                    <select name="role" class="form-select">
                        <option value="admin" {{ $u->role === 'admin' ? 'selected' : '' }}>Admin (Pengelola Modul & Dokumen)</option>
                        <option value="superadmin" {{ $u->role === 'superadmin' ? 'selected' : '' }}>Superadmin (Akses Penuh Sistem)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password Baru</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="8">
                    <div class="form-text text-muted">Minimal 8 karakter jika diisi.</div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-3">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<div class="modal fade" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content shadow border-0" action="{{ route('users.store') }}" method="POST">
            @csrf
            <div class="modal-header bg-light">
                <h5 class="modal-title fw-bold" id="addModalLabel">Tambah Pengguna Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 bg-white">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Nama</label>
                    <input type="text" name="name" class="form-control" placeholder="Masukkan nama lengkap" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" placeholder="email@perusahaan.com" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Role</label>
                    <select name="role" class="form-select">
                        <option value="admin">Admin (Pengelola Modul & Dokumen)</option>
                        <option value="superadmin">Superadmin (Akses Penuh Sistem)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Password Awal</label>
                    <input type="password" name="password" class="form-control" placeholder="Minimal 8 karakter" required minlength="8">
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary btn-sm px-3">Tambah Pengguna</button>
            </div>
        </form>
    </div>
</div>
@endsection
