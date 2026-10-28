@props(['level'])

@php
    $badgeClass = match (strtolower($level)) {
        'platinum' => 'level-platinum',
        'gold' => 'level-gold',
        'silver' => 'level-silver',
        default => 'level-basic',
    };
@endphp

<span {{ $attributes->class(['level-badge', $badgeClass]) }}>{{ $level }}</span>
