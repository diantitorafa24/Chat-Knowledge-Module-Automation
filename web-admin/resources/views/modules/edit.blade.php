@extends('layouts.app')

@section('title', 'Ubah Modul')
@section('heading', 'Ubah Modul')

@section('content')
<div class="card shadow-sm" style="max-width: 640px;">
    <div class="card-body">
        <form method="POST" action="{{ route('modules.update', $module) }}">
            @csrf
            @method('PUT')
            @include('modules._form')
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('modules.show', $module) }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
</div>
@endsection
