<button {{ $attributes->merge(['type' => 'submit', 'class' => 'flex justify-center items-center gap-2 px-4 py-2 bg-primary border border-primary text-body-mid text-white rounded-lg cursor-pointer hover:shadow-md transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</button>
