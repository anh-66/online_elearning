<button {{ $attributes->merge(['type' => 'button', 'class' => 'inline-flex items-center justify-center rounded-full bg-[#E8E8ED] dark:bg-neutral-800 text-[#1D1D1F] dark:text-neutral-100 px-5 py-2.5 text-xs font-medium hover:bg-[#DEDEE3] dark:hover:bg-neutral-700 transition focus:outline-none focus:ring-2 focus:ring-[#0071E3]/30 active:scale-[0.98]']) }}>
    {{ $slot }}
</button>
