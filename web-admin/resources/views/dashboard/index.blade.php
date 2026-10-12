@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    /* ─── LUXURY STAT CARDS DENGAN GLASS HIGHLIGHT & GLOW HOVER ─── */
    .stat-card {
        border-radius: 18px !important;
        padding: 22px 20px;
        color: white;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.2) !important;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.12), inset 0 1px 0 rgba(255, 255, 255, 0.4) !important;
        transition: all 0.28s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: default;
    }

    .stat-card:hover { 
        transform: translateY(-5px) scale(1.02); 
        box-shadow: 0 20px 38px -8px rgba(0, 0, 0, 0.28), inset 0 1px 0 rgba(255, 255, 255, 0.55) !important; 
    }

    .stat-card .stat-icon-wrap {
        position: absolute;
        right: 14px;
        top: 14px;
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.16);
        border: 1px solid rgba(255, 255, 255, 0.28);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(8px);
        font-size: 1.45rem;
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-icon-wrap {
        transform: scale(1.12) rotate(6deg);
        background: rgba(255, 255, 255, 0.25);
    }

    .stat-card .stat-value { 
        font-size: 2.35rem; 
        font-weight: 800; 
        line-height: 1; 
        letter-spacing: -0.02em;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .stat-card .stat-label { 
        font-size: 0.8rem; 
        font-weight: 600; 
        opacity: 0.95; 
        margin-top: 8px; 
        letter-spacing: 0.02em;
    }

    .stat-card .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.68rem;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.18);
        padding: 3px 8px;
        border-radius: 20px;
        margin-top: 12px;
        backdrop-filter: blur(4px);
        letter-spacing: 0.04em;
        text-transform: uppercase;
    }

    /* GRADIENT PALETTES MODERN */
    .bg-grad-blue    { background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); }
    .bg-grad-green   { background: linear-gradient(135deg, #059669 0%, #10b981 100%); }
    .bg-grad-orange  { background: linear-gradient(135deg, #ea580c 0%, #f59e0b 100%); }
    .bg-grad-purple  { background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%); }
    .bg-grad-teal    { background: linear-gradient(135deg, #0284c7 0%, #06b6d4 100%); }
    .bg-grad-rose    { background: linear-gradient(135deg, #e11d48 0%, #f43f5e 100%); }

    /* STATUS LAYANAN */
    .health-pill {
        display: inline-flex; 
        align-items: center; 
        gap: 8px;
        padding: 8px 16px; 
        border-radius: 30px; 
        font-size: .84rem; 
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        transition: all 0.2s ease;
    }
    .health-ok   { background: #f0fdf4; color: #15803d; border: 1.5px solid #bbf7d0; }
    .health-fail { background: #fef2f2; color: #b91c1c; border: 1.5px solid #fecaca; }
    .health-dot  { width: 8px; height: 8px; border-radius: 50%; animation: pulseDot 1.6s infinite ease-in-out; }
    .health-ok .health-dot   { background: #22c55e; box-shadow: 0 0 8px #22c55e; }
    .health-fail .health-dot { background: #ef4444; box-shadow: 0 0 8px #ef4444; }

    @keyframes pulseDot {
        0%, 100% { opacity: 1; transform: scale(1); } 
        50% { opacity: 0.4; transform: scale(1.25); }
    }

    .rate-ring {
        font-size: 2.75rem; 
        font-weight: 800; 
        line-height: 1; 
        letter-spacing: -0.02em;
    }
    .section-title {
        font-size: .74rem; 
        text-transform: uppercase; 
        letter-spacing: .09em;
        color: #64748b; 
        font-weight: 700; 
        margin-bottom: 8px;
    }
</style>
@endpush

@section('content')

{{-- ── STAT CARDS BER EFEK MEWAH ── --}}
<div class="row g-3 mb-4">
@php
    $gradients = ['bg-grad-blue','bg-grad-green','bg-grad-orange','bg-grad-purple','bg-grad-teal','bg-grad-rose'];
    $icons      = ['bi-folder2-open','bi-file-earmark-text','bi-puzzle-fill','bi-chat-left-dots-fill','bi-cpu-fill','bi-hand-thumbs-up-fill'];
    $badges     = ['Modul Aktif','Dokumen SOP','Vektor Database','Interaksi Hari Ini','Status Antrean','Feedback Tim'];
    $i = 0;
@endphp
@foreach ($stats as $label => $value)
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card {{ $gradients[$i % 6] }}">
            <div class="stat-icon-wrap">
                <i class="bi {{ $icons[$i % 6] }}"></i>
            </div>
            <div class="stat-value">{{ $value }}</div>
            <div class="stat-label">{{ $label }}</div>
            <div class="stat-pill">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ $badges[$i % 6] }}</span>
            </div>
        </div>
    </div>
    @php $i++; @endphp
@endforeach
</div>

{{-- ── HEALTH STATUS ── --}}
<div class="card mb-4">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-activity text-primary fs-5"></i>
        <span>Status Layanan Inti</span>
    </div>
    <div class="card-body d-flex flex-wrap gap-4 py-3">
        <div>
            <div class="section-title">Qdrant Vector Database</div>
            <span class="health-pill {{ $health['qdrant']['ok'] ? 'health-ok' : 'health-fail' }}">
                <span class="health-dot"></span>
                <span>{{ $health['qdrant']['ok'] ? 'Aktif' : 'Mati' }} — {{ $health['qdrant']['message'] }}</span>
            </span>
        </div>
        <div>
            <div class="section-title">Google Gemini AI Engine</div>
            <span class="health-pill {{ $health['gemini']['ok'] ? 'health-ok' : 'health-fail' }}">
                <span class="health-dot"></span>
                <span>{{ $health['gemini']['ok'] ? 'Siap' : 'Bermasalah' }} — {{ $health['gemini']['message'] }}</span>
            </span>
        </div>
    </div>
</div>

{{-- ── CHART + UNANSWERED ── --}}
<div class="row g-3 mb-4">
    <div class="col-md-8">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-graph-up-arrow text-primary fs-5"></i>
                <span>Tren Pertanyaan 7 Hari Terakhir</span>
            </div>
            <div class="card-body">
                <canvas id="trendChart" height="95"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-header d-flex align-items-center gap-2">
                <i class="bi bi-pie-chart text-primary fs-5"></i>
                <span>Tingkat Tidak Terjawab</span>
            </div>
            <div class="card-body d-flex flex-column justify-content-center align-items-center text-center gap-2 py-4">
                <div class="rate-ring {{ $unansweredRate > 20 ? 'text-danger' : 'text-success' }}">
                    {{ $unansweredRate }}%
                </div>
                <div class="text-muted small fw-medium">{{ $unansweredCount }} dari {{ $totalChats }} percakapan</div>
                <a href="{{ route('chat-logs.unanswered') }}" class="btn btn-sm btn-outline-warning mt-2 px-3">
                    <i class="bi bi-arrow-right-circle me-1"></i> Lihat Unanswered
                </a>
            </div>
        </div>
    </div>
</div>

{{-- ── TOP MODULES ── --}}
<div class="card">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-trophy-fill text-warning fs-5"></i>
        <span>Modul Paling Sering Ditanya</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th class="ps-3">#</th>
                        <th>Kode Modul</th>
                        <th>Nama Modul</th>
                        <th class="text-end pe-3">Jumlah Pertanyaan</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($topModules as $idx => $row)
                    <tr>
                        <td class="ps-3"><span class="badge bg-secondary">{{ $idx + 1 }}</span></td>
                        <td><span class="badge" style="background:#eff6ff;color:#2563eb;font-weight:700;">{{ $row->module->code ?? '-' }}</span></td>
                        <td class="fw-medium">{{ $row->module->name ?? '-' }}</td>
                        <td class="text-end pe-3"><strong>{{ $row->total }}</strong></td>
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
                borderColor: '#2563eb',
                backgroundColor: 'rgba(37,99,235,.08)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#2563eb',
                pointRadius: 4,
            },
            {
                label: 'Terjawab',
                data: {!! json_encode($trend->pluck('answered')) !!},
                borderColor: '#10b981',
                backgroundColor: 'rgba(16,185,129,.05)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#10b981',
                pointRadius: 4,
            }
        ]
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'top' } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});
</script>
@endpush
