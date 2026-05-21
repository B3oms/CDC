@props([
    'href' => null,
    'label' => 'Back',
    'history' => false,
])

@if($history)
<a href="javascript:history.back()" {{ $attributes->merge(['class' => 'btn-back']) }} onclick="if (typeof goBack === 'function') { goBack(); } return false;">
    <i class="fas fa-arrow-left" aria-hidden="true"></i>
    <span>{{ $label }}</span>
</a>
@else
<a href="{{ $href }}" {{ $attributes->merge(['class' => 'btn-back']) }}>
    <i class="fas fa-arrow-left" aria-hidden="true"></i>
    <span>{{ $label }}</span>
</a>
@endif
