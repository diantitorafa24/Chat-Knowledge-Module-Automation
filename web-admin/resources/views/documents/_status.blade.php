@php
    $class = match ($status) {
        'active', 'indexed' => 'text-bg-success',
        'failed' => 'text-bg-danger',
        'uploaded' => 'text-bg-secondary',
        'extracted', 'chunked' => 'text-bg-info',
        default => 'text-bg-warning',
    };
@endphp
<span class="badge {{ $class }}">{{ $status }}</span>
