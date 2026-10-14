@extends('layouts.app')

@section('title', 'Processing')
@section('heading', 'Processing')

@section('content')
@php
    $hasPending = $documents->contains(fn ($d) => in_array($d->processing_status, ['uploaded', 'extracting', 'processing_images', 'chunking', 'embedding']));
@endphp

@if ($hasPending)
<div class="alert alert-info d-flex align-items-center mb-3 py-2 px-3 shadow-sm" role="alert">
    <div class="spinner-border spinner-border-sm me-2 text-primary" role="status"></div>
    <div class="small">
        <strong>Sedang memproses dokumen di latar belakang.</strong> Halaman ini akan diperbarui secara otomatis setiap 3 detik.
    </div>
</div>
@endif

<div class="card shadow-sm">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2">
        <span class="text-muted small fw-semibold">Daftar Antrean & Dokumen Non-Aktif</span>
        <a href="{{ route('processing.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-clockwise"></i> Segarkan Halaman
        </a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Modul</th>
                    <th>Versi</th>
                    <th>File</th>
                    <th>Status</th>
                    <th style="width: 220px;">Progress</th>
                    <th>Keterangan</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($documents as $document)
                @php
                    $isProcessing = in_array($document->processing_status, ['uploaded', 'extracting', 'processing_images', 'chunking', 'embedding']);
                @endphp
                <tr>
                    <td>
                        <span class="text-muted small">{{ $document->module->code }}</span><br>
                        <strong>{{ $document->module->name }}</strong>
                    </td>
                    <td class="fw-semibold">{{ $document->versions->first()->version ?? '-' }}</td>
                    <td class="small">{{ $document->original_filename }}</td>
                    <td>@include('documents._status', ['status' => $document->processing_status])</td>
                    <td>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar {{ $isProcessing ? 'progress-bar-striped progress-bar-animated bg-primary' : ($document->processing_status === 'indexed' ? 'bg-success' : 'bg-danger') }}" 
                                 role="progressbar" 
                                 style="width: {{ max(5, $document->progress) }}%">
                                {{ $document->progress }}%
                            </div>
                        </div>
                    </td>
                    <td class="text-danger small">{{ $document->processing_error ?: '-' }}</td>
                    <td class="text-end">
                        @if ($document->processing_status === 'indexed')
                            <a href="{{ route('modules.show', $document->module_id) }}" class="btn btn-sm btn-success">
                                Aktifkan Modul
                            </a>
                        @elseif ($document->processing_status === 'failed')
                            <form method="POST" action="{{ route('documents.reprocess', $document) }}" class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-warning">Proses Ulang</button>
                            </form>
                        @elseif ($isProcessing)
                            <span class="badge text-bg-light border text-muted">Memproses...</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada dokumen yang menunggu atau sedang diproses.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@if ($hasPending)
<script>
    setTimeout(function() {
        window.location.reload();
    }, 3000);
</script>
@endif
@endsection
