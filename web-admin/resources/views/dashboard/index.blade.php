@extends('layouts.app')

@section('title', 'Dashboard')
@section('heading', 'Dashboard')

@section('content')
<div class="row g-3">
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
@endsection
