@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    .stat-card {
        border-radius: 14px !important;
        padding: 20px;
        color: white;
        position: relative;
        overflow: hidden;
        transition: transform .2s, box-shadow .2s;
    }
    .stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.15) !important; }
    .stat-card .stat-icon {
        position: absolute; right: 16px; top: 16px;
        font-size: 2.2rem; opacity: .25;
    }
    .stat-card .stat-value { font-size: 2rem; font-weight: 700; line-height: 1; }
    .stat-card .stat-label { font-size: .78rem; opacity: .85; margin-top: 4px; }

    .bg-grad-blue    { background: linear-gradient(135deg, #3b82f6, #6366f1); }
    .bg-grad-green   { background: linear-gradient(135deg, #10b981, #14b8a6); }
    .bg-grad-orange  { background: linear-gradient(135deg, #f59e0b, #ef4444); }
    .bg-grad-purple  { background: linear-gradient(135deg, #8b5cf6, #ec4899); }
    .bg-grad-teal    { background: linear-gradient(135deg, #06b6d4, #3b82f6); }
    .bg-grad-rose    { background: linear-gradient(135deg, #f43f5e, #f97316); }

    .health-pill {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; border-radius: 20px; font-size: .8rem; font-weight: 600;
    }
    .health-ok   { background: #f0fdf4; color: #166534; border: 1.5px solid #bbf7d0; }
    .health-fail { background: #fef2f2; color: #991b1b; border: 1.5px solid #fecaca; }
    .health-dot  { width: 7px; height: 7px; border-radius: 50%; animation: pulse 1.5s infinite; }
    .health-ok .health-dot   { background: #22c55e; }
    .health-fail .health-dot { background: #ef4444; }
    @keyframes pulse {
        0%, 100% { opacity: 1; } 50% { opacity: .4; }
    }

    .rate-ring {
        font-size: 2.5rem; font-weight: 800; line-height: 1;
    }
    .section-title {
        font-size: .75rem; text-transform: uppercase; letter-spacing: .08em;
        color: #64748b; font-weight: 600; margin-bottom: 14px;
    }
</style>
@endpush

@section('content')

{{-- ── STAT CARDS ── --}}
<div class="row g-3 mb-4">
@php
    $gradients = ['bg-grad-blue','bg-grad-green','bg-grad-orange','bg-grad-purple','bg-grad-teal','bg-grad-rose'];
    $icons      = ['bi-folder2-open','bi-people','bi-file-earmark-text','bi-puzzle','bi-chat-left-text','bi-hand-thumbs-up'];
    $i = 0;
@endphp
@foreach ($stats as $label => $value)
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card {{ $gradients[$i % 6] }}">
            <i class="bi {{ $icons[$i % 6] }} stat-icon"></i>
            <div class="stat-value">{{ $value }}</div>
            <div class="stat-label">{{ $label }}</div>
        </div>
    </div>
    @php $i++; @endphp
@endforeach
</div>

{{-- ── HEALTH STATUS ── --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-activity text-primary"></i> Status Layanan
    </div>
    <div class="card-body d-flex flex-wrap gap-3">
        <div>
            <div class="section-title">Qdrant Vector DB</div>
            <span class="health-pill {{ $health['qdrant']['ok'] ? 'health-ok' : 'health-fail' }}">
                <span class="health-dot"></span>
                {{ $health['qdrant']['ok'] ? 'Hidup' : 'Mati' }} — {{ $health['qdrant']['message'] }}
            </span>
        </div>
        <div>
            <div class="section-title">Gemini AI API</div>
            <span class="health-pill {{ $health['gemini']['ok'] ? 'health-ok' : 'health-fail' }}">
                <span class="health-dot"></span>
                {{ $health['gemini']['ok'] ? 'Siap' : 'Bermasalah' }} — {{ $health['gemini']['message'] }}
            </span>
        </div>
    </div>
</div>

{{-- ── CHART + UNANSWERED ── --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-bar-chart-line text-primary"></i> Pertanyaan 7 Hari Terakhir
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="95"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-pie-chart text-primary"></i> Tingkat Tidak Terjawab
            </div>
            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center gap-2">
                <div class="rate-ring {{ $unansweredRate > 20 ? 'text-danger' : 'text-success' }}">
                    {{ $unansweredRate }}%
                </div>
                <div class="text-muted small">{{ $unansweredCount }} dari {{ $totalChats }} percakapan</div>
                <a href="{{ route('chat-logs.unanswered') }}" class="btn btn-sm btn-outline-warning mt-1">
                    <i class="bi bi-arrow-right-circle"></i> Lihat Unanswered
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── TOP MODULES ── --}}
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-trophy text-primary"></i> Modul Paling Sering Ditanya
    </div>
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Kode Modul</th>
                    <th>Nama Modul</th>
                    <th>Jumlah Pertanyaan</th>
                </tr>
            </thead>
            <tbody>
            @forelse ($topModules as $idx => $row)
                <tr>
                    <td><span class="badge bg-secondary">{{ $idx + 1 }}</span></td>
                    <td><span class="badge" style="background:#eff6ff;color:#3b82f6;">{{ $row->module->code ?? '-' }}</span></td>
                    <td>{{ $row->module->name ?? '-' }}</td>
                    <td><strong>{{ $row->total }}</strong></td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center text-muted py-4">
                    <i class="bi bi-inbox fs-3 d-block mb-2"></i> Belum ada data percakapan.
                </td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
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
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59,130,246,.08)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#3b82f6',
                pointRadius: 4,
            },
            {
                label: 'Terjawab',
                data: {!! json_encode($trend->pluck('answered')) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,.08)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#10b981',
                pointRadius: 4,
            },
        ],
    },
    options: {
        plugins: { legend: { labels: { font: { family: 'Inter' }, boxWidth: 12 } } },
        scales: {
            y: { beginAtZero: true, ticks: { stepSize: 1, font: { family: 'Inter', size: 11 } }, grid: { color: '#f1f5f9' } },
            x: { ticks: { font: { family: 'Inter', size: 11 } }, grid: { display: false } }
        }
    },
});
</script>
@endpush
