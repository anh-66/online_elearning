<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center rounded-full bg-[#019D91] px-6 py-2.5 text-xs font-medium text-white transition hover:bg-[#008b80] focus:outline-none focus:ring-2 focus:ring-[#019D91]/40 active:scale-[0.98]']) }}>
    {{ $slot }}
</button>
