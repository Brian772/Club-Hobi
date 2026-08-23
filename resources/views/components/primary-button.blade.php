<button {{ $attributes->merge(['type' => 'submit', 'class' => 'flex items-center p-3 items-center justify-center font-semibold bg-primary border border-transparent rounded-md text-xs text-white tracking-widest hover:bg-primary-active focus:bg-primary-active focus:outline-hidden focus:ring-2 focus:ring-primary focus:ring-offset-2 transition ease-in-out duration-150']) }}>
    {{ $slot }}
</button>
