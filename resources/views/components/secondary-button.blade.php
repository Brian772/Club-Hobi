<button {{ $attributes->merge(['class' => 'inline-flex justify-center items-center px-4 py-2 bg-primary/10 w-full rounded-md text-primary hover:bg-primary hover:text-white transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</button>
