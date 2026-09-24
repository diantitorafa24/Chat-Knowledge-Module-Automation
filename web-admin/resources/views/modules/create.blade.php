@extends('layouts.app')

@section('title', 'Upload Module')
@section('heading', 'Upload Module')

@section('content')
<div class="card shadow-sm" style="max-width: 640px;">
    <div class="card-body">
        <form method="POST" action="{{ route('modules.store') }}" enctype="multipart/form-data">
            @csrf
            @include('modules._form')
            <div class="mb-3">
                <label class="form-label">File Module (DOCX, maks. 10 MB)</label>
                <input type="file" name="file" accept=".docx" class="form-control @error('file') is-invalid @enderror" required>
                @error('file')<div class="invalid-feedback">{{ $message }}</div>@enderror
                <div class="form-text">Versi pertama otomatis diberi nomor v1.0.</div>
            </div>
            <button type="submit" class="btn btn-primary">Upload Module</button>
            <a href="{{ route('modules.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
