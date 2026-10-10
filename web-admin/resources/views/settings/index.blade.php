@extends('layouts.app')
@section('title', 'Pengaturan Sistem')
@section('content')
<div class="row">
    <div class="col-12 col-md-8 mx-auto">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0">Konfigurasi AI & RAG</h5>
            </div>
            <div class="card-body">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                <form action="{{ route('settings.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="form-label fw-bold">AI System Prompt</label>
                        <textarea class="form-control" name="ai_system_prompt" rows="4">{{ old('ai_system_prompt', $settings['ai_system_prompt']->value ?? '') }}</textarea>
                        <div class="form-text text-muted">{{ $settings['ai_system_prompt']->description ?? '' }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Minimum RAG Score</label>
                        <input type="number" step="0.01" class="form-control" name="rag_min_score" value="{{ old('rag_min_score', $settings['rag_min_score']->value ?? '0.60') }}">
                        <div class="form-text text-muted">{{ $settings['rag_min_score']->description ?? '' }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Maximum Results (Top K)</label>
                        <input type="number" class="form-control" name="rag_max_results" value="{{ old('rag_max_results', $settings['rag_max_results']->value ?? '3') }}">
                        <div class="form-text text-muted">{{ $settings['rag_max_results']->description ?? '' }}</div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Gemini Chat Model</label>
                        <input type="text" class="form-control" name="ai_chat_model" value="{{ old('ai_chat_model', $settings['ai_chat_model']->value ?? 'gemini-3.5-flash-lite') }}">
                        <div class="form-text text-muted">{{ $settings['ai_chat_model']->description ?? '' }}</div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
