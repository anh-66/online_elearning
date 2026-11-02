@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'border border-[#D2D2D7]/70 dark:border-neutral-800 bg-[#F5F5F7] dark:bg-black text-[#1D1D1F] dark:text-white placeholder-[#86868B] rounded-xl text-xs focus:border-[#019D91] focus:ring-1 focus:ring-[#019D91] transition']) }}>
