<button {{ $attributes->merge(['class' => 'inline-flex justify-center items-center p-2 bg-primary/10 w-full rounded-md text-primary border-solid border border-primary hover:bg-primary hover:text-white transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</button>
