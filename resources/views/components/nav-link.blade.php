@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-full border border-sky-200 bg-sky-50 px-4 py-2 text-sm font-semibold text-sky-700 shadow-sm transition duration-200 ease-in-out'
            : 'inline-flex items-center rounded-full border border-transparent px-4 py-2 text-sm font-medium text-slate-600 transition duration-200 ease-in-out hover:border-slate-200 hover:bg-slate-100/80 hover:text-slate-900 focus:outline-none focus:border-slate-200 focus:bg-slate-100 focus:text-slate-900';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
