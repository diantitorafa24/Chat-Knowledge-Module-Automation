@extends('layouts.app')

@section('title', 'Tambah Modul')
@section('heading', 'Tambah Modul')

@section('content')
<div class="card shadow-sm" style="max-width: 640px;">
    <div class="card-body">
        <form method="POST" action="{{ route('modules.store') }}">
            @csrf
            @include('modules._form')
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('modules.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
