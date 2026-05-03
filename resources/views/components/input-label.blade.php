@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label text-sm font-weight-bold text-dark']) }}>
    {{ $value ?? $slot }}
</label>
