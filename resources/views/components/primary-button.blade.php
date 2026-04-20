<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-full border border-slate-900 bg-slate-900 px-5 py-2.5 text-xs font-semibold uppercase tracking-[0.24em] text-white shadow-lg shadow-slate-900/10 transition duration-200 ease-in-out hover:-translate-y-0.5 hover:border-sky-600 hover:bg-sky-600 focus:outline-none focus:ring-2 focus:ring-sky-400 focus:ring-offset-2 focus:ring-offset-white active:translate-y-0']) }}>
    {{ $slot }}
</button>
