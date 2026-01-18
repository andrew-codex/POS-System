@php
    $sizeClasses = [
        'sm' => 'spinner-sm',
        'md' => 'spinner-md',
        'lg' => 'spinner-lg',
        'xl' => 'spinner-xl',
    ];
    
    $spinnerSize = $sizeClasses[$size] ?? 'spinner-md';
@endphp

<div class="custom-spinner-container">
    <div class="custom-spinner {{ $spinnerSize }}">
        <div class="spinner-ring"></div>
        <div class="spinner-ring"></div>
        <div class="spinner-ring"></div>
        <div class="spinner-dot"></div>
    </div>
    
    @if($text)
        <p class="spinner-text">{{ $text }}</p>
    @endif
</div>