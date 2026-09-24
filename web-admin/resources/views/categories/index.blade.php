@extends('layouts.app')

@section('title', 'Categories')
@section('heading', 'Categories')

@section('content')
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h6 class="mb-3">Tambah Kategori</h6>
        <form method="POST" action="{{ route('categories.store') }}" class="row g-2">
            @csrf
            <div class="col-md-4">
                <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror" placeholder="Nama kategori" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <input type="text" name="description" value="{{ old('description') }}" class="form-control" placeholder="Deskripsi (opsional)">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Tambah</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Deskripsi</th>
                    <th>Modul</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($categories as $category)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td class="fw-semibold">{{ $category->name }}</td>
                    <td class="text-muted">{{ $category->description }}</td>
                    <td>{{ $category->modules_count }}</td>
                    <td>
                        <span class="badge {{ $category->status === 'active' ? 'text-bg-success' : 'text-bg-secondary' }}">{{ $category->status }}</span>
                    </td>
                    <td class="text-end">
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada kategori.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
