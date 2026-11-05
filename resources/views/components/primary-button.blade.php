<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center gap-2 min-h-11 px-4 py-2 bg-primary-dark border border-transparent rounded-md font-bold text-xs text-white uppercase tracking-widest hover:brightness-90 focus:brightness-90 active:brightness-75 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
