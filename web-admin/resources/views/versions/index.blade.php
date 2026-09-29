@extends('layouts.app')

@section('title', 'Versions')
@section('heading', 'Versions')

@section('content')
<form method="GET" action="{{ route('versions.index') }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <select name="module" class="form-select" onchange="this.form.submit()">
            <option value="">Semua modul</option>
            @foreach ($modules as $module)
                <option value="{{ $module->id }}" @selected((string) request('module') === (string) $module->id)>{{ $module->code }} - {{ $module->name }}</option>
            @endforeach
        </select>
    </div>
</form>

<div class="card shadow-sm mb-3">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Modul</th>
                    <th>Versi</th>
                    <th>File</th>
                    <th>Status dokumen</th>
                    <th>Dipakai bot</th>
                    <th>Diunggah</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($versions as $version)
                <tr>
                    <td>
                        <a href="{{ route('modules.show', $version->module) }}" class="text-decoration-none">{{ $version->module->code ?? '-' }}</a><br>
                        <span class="text-muted small">{{ $version->module->name ?? '-' }}</span>
                    </td>
                    <td class="fw-semibold">{{ $version->version }}</td>
                    <td>{{ $version->document->original_filename ?? '-' }}</td>
                    <td>
                        @if ($version->document)
                            @include('documents._status', ['status' => $version->document->processing_status])
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if ($version->status === 'active')
                            <span class="badge text-bg-success">AKTIF</span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-muted">{{ $version->created_at?->format('d M Y H:i') }}</td>
                    <td class="text-end">
                        @if ($version->status === 'active')
                            <form method="POST" action="{{ route('versions.deactivate', [$version->module, $version]) }}" class="d-inline" onsubmit="return confirm('Nonaktifkan versi ini?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-outline-secondary">Nonaktifkan</button>
                            </form>
                        @elseif ($version->document && $version->document->processing_status === 'indexed')
                            <form method="POST" action="{{ route('versions.activate', [$version->module, $version]) }}" class="d-inline" onsubmit="return confirm('Aktifkan versi ini?')">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success">Aktifkan</button>
                            </form>
                        @else
                            <span class="text-muted small">menunggu proses</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center text-muted py-4">Belum ada versi.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $versions->links() }}
@endsection
