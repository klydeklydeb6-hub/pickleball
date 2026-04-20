@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-2xl border border-sky-200 bg-sky-50 px-4 py-3 text-start text-base font-semibold text-sky-700 transition duration-200 ease-in-out'
            : 'block w-full rounded-2xl border border-transparent px-4 py-3 text-start text-base font-medium text-slate-600 transition duration-200 ease-in-out hover:border-slate-200 hover:bg-slate-50 hover:text-slate-900 focus:outline-none focus:border-slate-200 focus:bg-slate-50 focus:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
