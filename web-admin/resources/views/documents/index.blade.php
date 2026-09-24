@extends('layouts.app')

@section('title', 'Documents')
@section('heading', 'Documents')

@section('content')
<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Kode</th>
                    <th>Modul</th>
                    <th>Versi</th>
                    <th>File</th>
                    <th>Ukuran</th>
                    <th>Status</th>
                    <th>Diunggah</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($documents as $document)
                <tr>
                    <td class="text-muted">{{ $document->module->code }}</td>
                    <td>
                        <a href="{{ route('modules.show', $document->module) }}" class="text-decoration-none">{{ $document->module->name }}</a>
                    </td>
                    <td class="fw-semibold">{{ $document->versions->first()->version ?? '-' }}</td>
                    <td>{{ $document->original_filename }}</td>
                    <td>{{ number_format($document->file_size / 1024, 1) }} KB</td>
                    <td>@include('documents._status', ['status' => $document->processing_status])</td>
                    <td class="text-muted">{{ $document->created_at?->format('d M Y H:i') }}</td>
                    <td class="text-end">
                        <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-primary">Download</a>
                        <form method="POST" action="{{ route('documents.destroy', $document) }}" class="d-inline" onsubmit="return confirm('Hapus dokumen ini beserta filenya?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Belum ada dokumen.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
