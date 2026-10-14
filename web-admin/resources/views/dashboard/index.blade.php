@extends('layouts.app')
@section('title', 'Dashboard')

@push('styles')
<style>
    /* ─── ULTRA-LUXURY STAT CARDS DENGAN GLASS SHEEN & AMBIENT GLOW ─── */
    .stat-card {
        border-radius: 20px !important;
        padding: 24px 22px;
        color: white;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.24) !important;
        box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.15), inset 0 1.5px 0 rgba(255, 255, 255, 0.45) !important;
        transition: all 0.32s cubic-bezier(0.34, 1.56, 0.64, 1);
        cursor: default;
        user-select: none;
    }

    /* Efek Kilau Kaca Mengalir (Light Sweep Sheen) */
    .stat-card .card-glass-sheen {
        position: absolute;
        top: 0;
        left: -120%;
        width: 65%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.24), transparent);
        transform: skewX(-25deg);
        transition: left 0.75s ease;
        pointer-events: none;
    }

    .stat-card:hover .card-glass-sheen {
        left: 150%;
    }

    /* Watermark Ikon Raksasa Mengambang di Latar Belakang Kartu */
    .stat-card .stat-watermark {
        position: absolute;
        right: -10px;
        bottom: -16px;
        font-size: 5.4rem;
        opacity: 0.13;
        color: #ffffff;
        pointer-events: none;
        transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .stat-card:hover .stat-watermark {
        transform: scale(1.18) rotate(-8deg);
        opacity: 0.22;
    }

    .stat-card:hover { 
        transform: translateY(-7px) scale(1.025); 
    }

    /* Kotak Ikon Frosted Glass di Kanan Atas */
    .stat-card .stat-icon-wrap {
        position: absolute;
        right: 16px;
        top: 16px;
        width: 46px;
        height: 46px;
        background: rgba(255, 255, 255, 0.18);
        border: 1.5px solid rgba(255, 255, 255, 0.32);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        font-size: 1.45rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
        transition: all 0.35s ease;
    }

    .stat-card:hover .stat-icon-wrap {
        transform: scale(1.14) rotate(8deg);
        background: rgba(255, 255, 255, 0.28);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.2);
    }

    /* Angka Statistik Besar & Tegas */
    .stat-card .stat-value { 
        font-size: 2.65rem; 
        font-weight: 900; 
        line-height: 1; 
        letter-spacing: -0.03em;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        position: relative;
        z-index: 2;
    }

    .stat-card .stat-label { 
        font-size: 0.82rem; 
        font-weight: 700; 
        opacity: 0.95; 
        margin-top: 8px; 
        letter-spacing: 0.02em;
        text-shadow: 0 1px 3px rgba(0, 0, 0, 0.15);
        position: relative;
        z-index: 2;
    }

    /* Micro Status Pill di Bawah Kartu */
    .stat-card .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.68rem;
        font-weight: 700;
        background: rgba(255, 255, 255, 0.2);
        border: 1px solid rgba(255, 255, 255, 0.28);
        padding: 3px 10px;
        border-radius: 20px;
        margin-top: 14px;
        backdrop-filter: blur(6px);
        letter-spacing: 0.04em;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
    }

    .mini-pulse-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #ffffff;
        box-shadow: 0 0 6px #ffffff;
        animation: pulseDotSmall 1.8s infinite ease-in-out;
    }

    @keyframes pulseDotSmall {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.45; transform: scale(1.3); }
    }

    /* GRADIENT PALETTES MEWAH DENGAN INDIVIDUAL COLOR GLOW */
    .bg-grad-blue { 
        background: linear-gradient(135deg, #1d4ed8 0%, #2563eb 50%, #4f46e5 100%); 
        box-shadow: 0 12px 28px -6px rgba(37, 99, 235, 0.38) !important;
    }
    .bg-grad-blue:hover {
        box-shadow: 0 20px 40px -6px rgba(37, 99, 235, 0.55), inset 0 1.5px 0 rgba(255, 255, 255, 0.6) !important;
    }

    .bg-grad-green { 
        background: linear-gradient(135deg, #047857 0%, #059669 50%, #10b981 100%); 
        box-shadow: 0 12px 28px -6px rgba(5, 150, 105, 0.38) !important;
    }
    .bg-grad-green:hover {
        box-shadow: 0 20px 40px -6px rgba(5, 150, 105, 0.55), inset 0 1.5px 0 rgba(255, 255, 255, 0.6) !important;
    }

    .bg-grad-orange { 
        background: linear-gradient(135deg, #c2410c 0%, #ea580c 50%, #f59e0b 100%); 
        box-shadow: 0 12px 28px -6px rgba(234, 88, 12, 0.38) !important;
    }
    .bg-grad-orange:hover {
        box-shadow: 0 20px 40px -6px rgba(234, 88, 12, 0.55), inset 0 1.5px 0 rgba(255, 255, 255, 0.6) !important;
    }

    .bg-grad-purple { 
        background: linear-gradient(135deg, #6b21a8 0%, #7c3aed 50%, #a855f7 100%); 
        box-shadow: 0 12px 28px -6px rgba(124, 58, 237, 0.38) !important;
    }
    .bg-grad-purple:hover {
        box-shadow: 0 20px 40px -6px rgba(124, 58, 237, 0.55), inset 0 1.5px 0 rgba(255, 255, 255, 0.6) !important;
    }

    .bg-grad-teal { 
        background: linear-gradient(135deg, #0e7490 0%, #0284c7 50%, #06b6d4 100%); 
        box-shadow: 0 12px 28px -6px rgba(2, 132, 199, 0.38) !important;
    }
    .bg-grad-teal:hover {
        box-shadow: 0 20px 40px -6px rgba(2, 132, 199, 0.55), inset 0 1.5px 0 rgba(255, 255, 255, 0.6) !important;
    }

    .bg-grad-rose { 
        background: linear-gradient(135deg, #be123c 0%, #e11d48 50%, #f43f5e 100%); 
        box-shadow: 0 12px 28px -6px rgba(225, 29, 72, 0.38) !important;
    }
    .bg-grad-rose:hover {
        box-shadow: 0 20px 40px -6px rgba(225, 29, 72, 0.55), inset 0 1.5px 0 rgba(255, 255, 255, 0.6) !important;
    }

    /* STATUS LAYANAN TILE */
    .health-pill {
        display: inline-flex; 
        align-items: center; 
        gap: 8px;
        padding: 8px 18px; 
        border-radius: 30px; 
        font-size: .85rem; 
        font-weight: 600;
        box-shadow: 0 2px 6px rgba(0,0,0,0.04);
        transition: all 0.25s ease;
    }
    .health-pill:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
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
        font-size: 2.85rem; 
        font-weight: 900; 
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

    .chart-box { position: relative; height: 300px; }

    @media (max-width: 767.98px) {
        .stat-card { padding: 16px 14px; border-radius: 16px !important; }
        .stat-card:hover { transform: none; }
        .stat-card .stat-value { font-size: 1.9rem; }
        .stat-card .stat-label { font-size: .74rem; margin-top: 6px; padding-right: 30px; }
        .stat-card .stat-icon-wrap { width: 34px; height: 34px; font-size: 1.05rem; right: 10px; top: 10px; border-radius: 10px; }
        .stat-card .stat-watermark { font-size: 4rem; }
        .stat-card .stat-pill { font-size: .6rem; padding: 2px 8px; margin-top: 10px; max-width: 100%; }
        .stat-card .stat-pill span:last-child { white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .health-wrap { gap: 14px !important; }
        .health-wrap > div { width: 100%; }
        .health-pill { width: 100%; border-radius: 14px; font-size: .8rem; padding: 8px 14px; }
        .health-pill span:last-child { word-break: break-word; }
        .chart-box { height: 240px; }
        .rate-ring { font-size: 2.3rem; }
    }
</style>
@endpush

@section('content')

{{-- ── STAT CARDS ULTRA-MEWAH ── --}}
<div class="row g-3 mb-4">
@php
    $gradients = ['bg-grad-blue','bg-grad-green','bg-grad-orange','bg-grad-purple','bg-grad-teal','bg-grad-rose'];
    $icons      = ['bi-folder2-open','bi-file-earmark-text','bi-puzzle-fill','bi-chat-left-dots-fill','bi-cpu-fill','bi-hand-thumbs-up-fill'];
    $badges     = ['Modul Aktif','Dokumen SOP','Vektor Database','Pertanyaan Hari Ini','Status Antrean','Feedback Tim'];
    $i = 0;
@endphp
@foreach ($stats as $label => $value)
    <div class="col-6 col-md-4 col-xl-2">
        <div class="card stat-card {{ $gradients[$i % 6] }}">
            {{-- Efek Kilau Kaca Mengalir --}}
            <div class="card-glass-sheen"></div>

            {{-- Watermark Ikon Raksasa di Latar Belakang --}}
            <i class="bi {{ $icons[$i % 6] }} stat-watermark"></i>

            {{-- Badge Ikon Frosted Glass di Kanan Atas --}}
            <div class="stat-icon-wrap">
                <i class="bi {{ $icons[$i % 6] }}"></i>
            </div>

            {{-- Nilai & Label --}}
            <div class="stat-value">{{ $value }}</div>
            <div class="stat-label">{{ $label }}</div>

            {{-- Pill Status Mini --}}
            <div class="stat-pill">
                <span class="mini-pulse-dot"></span>
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
    <div class="card-body d-flex flex-wrap gap-4 py-3 health-wrap">
        <div>
            <div class="section-title">Qdrant Vector Database</div>
            <span class="health-pill {{ $health['qdrant']['ok'] ? 'health-ok' : 'health-fail' }}">
                <span class="health-dot"></span>
                <span>{{ $health['qdrant']['ok'] ? 'Aktif Terhubung' : 'Mati' }} — {{ $health['qdrant']['message'] }}</span>
            </span>
        </div>
        <div>
            <div class="section-title">Google Gemini AI Engine</div>
            <span class="health-pill {{ $health['gemini']['ok'] ? 'health-ok' : 'health-fail' }}">
                <span class="health-dot"></span>
                <span>{{ $health['gemini']['ok'] ? 'Siap Operasional' : 'Bermasalah' }} — {{ $health['gemini']['message'] }}</span>
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
                <div class="chart-box"><canvas id="trendChart"></canvas></div>
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
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top', labels: { boxWidth: 12, font: { size: window.innerWidth < 768 ? 11 : 12 } } } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});
</script>
@endpush
