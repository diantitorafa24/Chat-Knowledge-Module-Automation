@extends('layouts.app')

@section('title', 'Feedback')
@section('heading', 'Feedback')

@section('content')
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <div class="text-muted small">Total Feedback</div>
            <div class="fs-2 fw-bold">{{ $total }}</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <div class="text-muted small">Positif (??)</div>
            <div class="fs-2 fw-bold text-success">{{ $positif }}</div>
        </div></div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm"><div class="card-body">
            <div class="text-muted small">Tingkat Kepuasan</div>
            <div class="fs-2 fw-bold {{ $rate >= 70 ? 'text-success' : 'text-warning' }}">{{ $rate }}%</div>
        </div></div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-top mb-0">
            <thead class="table-light">
                <tr>
                    <th>Waktu</th>
                    <th>Pertanyaan</th>
                    <th>Modul</th>
                    <th>Rating</th>
                    <th>Komentar</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($feedbacks as $fb)
                <tr>
                    <td class="text-muted small">{{ $fb->created_at?->format('d M Y H:i') }}</td>
                    <td style="max-width: 300px;">{{ $fb->chatLog->question ?? '-' }}</td>
                    <td>{{ $fb->chatLog->module->code ?? '-' }}</td>
                    <td>
                        @if ($fb->rating === 1)
                            <span class="badge text-bg-success">?? Positif</span>
                        @else
                            <span class="badge text-bg-danger">?? Negatif</span>
                        @endif
                    </td>
                    <td>{{ $fb->comment ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada feedback.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $feedbacks->links() }}
@endsection
