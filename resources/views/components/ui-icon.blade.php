@props(['name'])

<svg {{ $attributes->class(['ui-icon']) }} viewBox="0 0 24 24" aria-hidden="true" focusable="false" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round">
    <use href="{{ asset('img/ui-icons.svg') }}#{{ $name }}"></use>
</svg>
