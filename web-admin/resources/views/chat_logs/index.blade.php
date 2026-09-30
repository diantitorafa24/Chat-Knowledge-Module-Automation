@extends('layouts.app')

@section('title', $unansweredOnly ? 'Unanswered Questions' : 'Chat Logs')
@section('heading', $unansweredOnly ? 'Unanswered Questions' : 'Chat Logs')

@section('content')
<p class="text-muted small">Total: {{ $logs->total() }} percakapan</p>

<div class="card shadow-sm mb-3">
    <div class="table-responsive">
        <table class="table table-hover align-top mb-0">
            <thead class="table-light">
                <tr>
                    <th>Waktu</th>
                    <th>User</th>
                    <th>Pertanyaan</th>
                    <th>Jawaban</th>
                    <th>Modul</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($logs as $log)
                <tr>
                    <td class="text-muted small">{{ $log->created_at?->format('d M Y H:i') }}</td>
                    <td>
                        {{ $log->username ? '@' . $log->username : '-' }}<br>
                        <span class="text-muted small">{{ $log->telegram_user_id }}</span>
                    </td>
                    <td style="max-width: 260px;">{{ $log->question }}</td>
                    <td style="max-width: 320px;">
                        <details>
                            <summary class="text-muted">{{ \Illuminate\Support\Str::limit($log->answer, 80) }}</summary>
                            <pre class="mb-0 mt-2 small" style="white-space: pre-wrap;">{{ $log->answer }}</pre>
                        </details>
                    </td>
                    <td>{{ $log->module->code ?? '-' }}</td>
                    <td>
                        @if ($log->is_answered)
                            <span class="badge text-bg-success">Terjawab</span>
                        @else
                            <span class="badge text-bg-warning">Tidak ditemukan</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-4">Belum ada percakapan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{ $logs->links() }}
@endsection
