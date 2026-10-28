@props(['status'])

<span {{ $attributes->class(['status-badge', 'status-active' => $status === 'Active', 'status-inactive' => $status === 'Inactive']) }}>
    <span class="status-dot"></span>{{ $status }}
</span>
