<?php

namespace App\Http\Controllers;

class DashboardController extends Controller
{
    public function index()
    {
        // Angka sementara. Nanti diisi dari database setelah tabelnya ada (Phase 3 dst).
        $stats = [
            'Total Module' => 0,
            'Total Documents' => 0,
            'Total Chunks' => 0,
            'Pertanyaan Hari Ini' => 0,
            'Module Processing' => 0,
        ];

        return view('dashboard.index', compact('stats'));
    }
}
