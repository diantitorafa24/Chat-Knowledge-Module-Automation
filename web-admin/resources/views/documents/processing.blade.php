@extends('layouts.app')

@section('title', 'Processing')
@section('heading', 'Processing')

@section('content')
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Modul</th>
                    <th>Versi</th>
                    <th>File</th>
                    <th>Status</th>
                    <th style="width: 200px;">Progress</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($documents as $document)
                <tr>
                    <td>
                        <span class="text-muted small">{{ $document->module->code }}</span><br>
                        {{ $document->module->name }}
                    </td>
                    <td class="fw-semibold">{{ $document->versions->first()->version ?? '-' }}</td>
                    <td>{{ $document->original_filename }}</td>
                    <td>@include('documents._status', ['status' => $document->processing_status])</td>
                    <td>
                        <div class="progress" style="height: 18px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $document->progress }}%">{{ $document->progress }}%</div>
                        </div>
                    </td>
                    <td class="text-danger small">{{ $document->processing_error }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Tidak ada dokumen yang menunggu atau sedang diproses.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
