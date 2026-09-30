@extends('layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    @foreach ($stats as $label => $value)
        <div class="col-md-4 col-xl-2">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="text-muted small">{{ $label }}</div>
                    <div class="fs-2 fw-bold">{{ $value }}</div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Qdrant</div>
                <div class="fw-bold {{ $health['qdrant']['ok'] ? 'text-success' : 'text-danger' }}">
                    {{ $health['qdrant']['ok'] ? '? Hidup' : '? Mati' }} - {{ $health['qdrant']['message'] }}
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="text-muted small">Gemini API</div>
                <div class="fw-bold {{ $health['gemini']['ok'] ? 'text-success' : 'text-danger' }}">
                    {{ $health['gemini']['ok'] ? '? Siap' : '? Bermasalah' }} - {{ $health['gemini']['message'] }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <h6 class="mb-3">Pertanyaan 7 Hari Terakhir</h6>
                <canvas id="trendChart" height="90"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <h6 class="mb-3">Tingkat Tidak Terjawab</h6>
                <div class="fs-1 fw-bold {{ $unansweredRate > 20 ? 'text-danger' : 'text-success' }}">{{ $unansweredRate }}%</div>
                <div class="text-muted small">{{ $unansweredCount }} dari {{ $totalChats }} total percakapan</div>
                <a href="{{ route('chat-logs.unanswered') }}" class="btn btn-sm btn-outline-warning mt-2">Lihat Unanswered Questions</a>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <h6 class="mb-3">Modul Paling Sering Ditanya</h6>
        <table class="table mb-0">
            <thead class="table-light">
                <tr><th>Modul</th><th>Jumlah Pertanyaan</th></tr>
            </thead>
            <tbody>
            @forelse ($topModules as $row)
                <tr>
                    <td>{{ $row->module->code ?? '-' }} - {{ $row->module->name ?? '-' }}</td>
                    <td>{{ $row->total }}</td>
                </tr>
            @empty
                <tr><td colspan="2" class="text-center text-muted py-3">Belum ada data.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script>
new Chart(document.getElementById('trendChart'), {
    type: 'line',
    data: {
        labels: {!! json_encode($trend->pluck('label')) !!},
        datasets: [
            {
                label: 'Total Pertanyaan',
                data: {!! json_encode($trend->pluck('total')) !!},
                borderColor: '#0d6efd',
                tension: 0.3,
            },
            {
                label: 'Terjawab',
                data: {!! json_encode($trend->pluck('answered')) !!},
                borderColor: '#198754',
                tension: 0.3,
            },
        ],
    },
    options: { scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } },
});
</script>
@endsection
