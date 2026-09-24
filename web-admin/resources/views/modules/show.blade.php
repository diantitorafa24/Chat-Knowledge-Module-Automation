@extends('layouts.app')

@section('title', $module->name)
@section('heading', $module->name)

@section('content')
<div class="card shadow-sm mb-4" style="max-width: 800px;">
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

<div class="card shadow-sm" style="max-width: 800px;">
    <div class="card-body">
        <h6 class="mb-2">Dokumen</h6>
        <p class="text-muted mb-0">Upload file DOCX untuk modul ini ditambahkan di langkah 4.3.</p>
    </div>
</div>
@endsection
