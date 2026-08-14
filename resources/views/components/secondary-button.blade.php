<button {{ $attributes->merge(['class' => 'inline-flex justify-center items-center p-[12px] w-full rounded-md text-primary border-solid border border-primary hover:bg-primary hover:text-white transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</button>
