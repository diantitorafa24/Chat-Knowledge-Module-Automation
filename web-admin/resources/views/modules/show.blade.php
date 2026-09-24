@extends('layouts.app')

@section('title', $module->name)
@section('heading', $module->name)

@section('content')
<div class="card shadow-sm mb-4" style="max-width: 900px;">
    <div class="card-body">
        <dl class="row mb-0">
            <dt class="col-sm-3">Kode</dt>
            <dd class="col-sm-9">{{ $module->code }}</dd>

            <dt class="col-sm-3">Kategori</dt>
            <dd class="col-sm-9">{{ $module->category->name ?? '-' }}</dd>

            <dt class="col-sm-3">Deskripsi</dt>
            <dd class="col-sm-9">{{ $module->description ?: '-' }}</dd>

            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9">{{ $module->status }}</dd>

            <dt class="col-sm-3">Versi aktif</dt>
            <dd class="col-sm-9">{{ $module->current_version ?? '-' }}</dd>
        </dl>
    </div>
    <div class="card-footer bg-white">
        <a href="{{ route('modules.edit', $module) }}" class="btn btn-outline-primary btn-sm">Ubah</a>
        <a href="{{ route('modules.index') }}" class="btn btn-outline-secondary btn-sm">Kembali</a>
    </div>
</div>

<div class="card shadow-sm mb-4" style="max-width: 900px;">
    <div class="card-body">
        <h6 class="mb-3">Dokumen</h6>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Versi</th>
                        <th>File</th>
                        <th>Ukuran</th>
                        <th>Status</th>
                        <th>Diunggah</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($versions as $version)
                    <tr>
                        <td class="fw-semibold">{{ $version->version }}</td>
                        <td>{{ $version->document->original_filename ?? '-' }}</td>
                        <td>{{ $version->document ? number_format($version->document->file_size / 1024, 1) . ' KB' : '-' }}</td>
                        <td><span class="badge text-bg-secondary">{{ $version->document->processing_status ?? '-' }}</span></td>
                        <td class="text-muted">{{ $version->created_at?->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-3">Belum ada dokumen.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow-sm" style="max-width: 900px;">
    <div class="card-body">
        <h6 class="mb-3">Upload Versi Baru</h6>
        <form method="POST" action="{{ route('modules.documents.store', $module) }}" enctype="multipart/form-data" class="row g-2">
            @csrf
            <div class="col-md-3">
                <label class="form-label">Versi</label>
                <input type="text" name="version" value="{{ old('version', $suggestedVersion) }}" class="form-control @error('version') is-invalid @enderror" required>
                @error('version')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
                <label class="form-label">File DOCX (maks. 10 MB)</label>
                <input type="file" name="file" accept=".docx" class="form-control @error('file') is-invalid @enderror" required>
                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Upload</button>
            </div>
        </form>
    </div>
</div>
@endsection
