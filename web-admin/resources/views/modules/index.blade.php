@extends('layouts.app')

@section('title', 'Modules')
@section('heading', 'All Modules')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="GET" action="{{ route('modules.index') }}">
        <select name="category" class="form-select" onchange="this.form.submit()">
            <option value="">Semua kategori</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </form>
    <a href="{{ route('modules.create') }}" class="btn btn-primary">+ Tambah Modul</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Kategori</th>
                    <th>Versi</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($modules as $module)
                @php
                    $badge = match ($module->status) {
                        'active' => 'text-bg-success',
                        'processing' => 'text-bg-warning',
                        'failed' => 'text-bg-danger',
                        default => 'text-bg-secondary',
                    };
                @endphp
                <tr>
                    <td class="text-muted">{{ $module->code }}</td>
                    <td class="fw-semibold">{{ $module->name }}</td>
                    <td>{{ $module->category->name ?? '-' }}</td>
                    <td>{{ $module->current_version ?? '-' }}</td>
                    <td><span class="badge {{ $badge }}">{{ $module->status }}</span></td>
                    <td class="text-end">
                        <a href="{{ route('modules.show', $module) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                        <a href="{{ route('modules.edit', $module) }}" class="btn btn-sm btn-outline-primary">Ubah</a>
                        <form method="POST" action="{{ route('modules.destroy', $module) }}" class="d-inline" onsubmit="return confirm('Hapus modul ini beserta semua dokumennya?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada modul.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
