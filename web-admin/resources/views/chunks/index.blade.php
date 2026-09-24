@extends('layouts.app')

@section('title', 'Chunks')
@section('heading', 'Chunks')

@section('content')
<form method="GET" action="{{ route('chunks.index') }}" class="row g-2 mb-3">
    <div class="col-md-3">
        <select name="module" class="form-select">
            <option value="">Semua modul</option>
            @foreach ($modules as $module)
                <option value="{{ $module->id }}" @selected((string) request('module') === (string) $module->id)>{{ $module->code }} - {{ $module->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-5">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari isi chunk...">
    </div>
    <div class="col-md-4">
        <button type="submit" class="btn btn-primary">Cari</button>
        <a href="{{ route('chunks.index') }}" class="btn btn-outline-secondary">Reset</a>
    </div>
</form>

<p class="text-muted small">Total: {{ $chunks->total() }} chunk</p>

<div class="card shadow-sm mb-3">
    <div class="table-responsive">
        <table class="table table-hover align-top mb-0">
            <thead class="table-light">
                <tr>
                    <th>Modul</th>
                    <th>Versi / File</th>
                    <th>#</th>
                    <th>Bagian</th>
                    <th>Sub-bagian</th>
                    <th>Panjang</th>
                    <th>Isi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($chunks as $chunk)
                <tr>
                    <td class="text-muted">{{ $chunk->module->code ?? '-' }}</td>
                    <td>
                        {{ $chunk->metadata['version'] ?? '-' }}<br>
                        <span class="text-muted small">{{ $chunk->document->original_filename ?? '-' }}</span>
                    </td>
                    <td>{{ $chunk->chunk_index }}</td>
                    <td>{{ $chunk->section ?? '-' }}</td>
                    <td>{{ $chunk->subsection ?? '-' }}</td>
                    <td>{{ mb_strlen($chunk->content) }}</td>
                    <td style="min-width: 320px;">
                        <details>
                            <summary class="text-muted">{{ \Illuminate\Support\Str::limit($chunk->content, 90) }}</summary>
                            <pre class="mb-0 mt-2 small" style="white-space: pre-wrap;">{{ $chunk->content }}</pre>
                        </details>
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada chunk yang cocok.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $chunks->links() }}
@endsection
