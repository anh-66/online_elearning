@props(['name' => 'code', 'length' => 6, 'autofocus' => false, 'invalid' => false])

{{--
    Ô nhập mã OTP: mỗi chữ số một ô, tự nhảy ô, dán cả mã một lần cũng được.
    Giá trị gộp lại nằm trong input ẩn "{{ $name }}". Nhập đủ số thì phát sự kiện "otp-complete".
--}}
<div
    x-data="{
        length: {{ (int) $length }},
        digits: Array({{ (int) $length }}).fill(''),
        get code() { return this.digits.join(''); },
        focusBox(index) {
            const box = this.$refs['box' + index];
            if (box) { box.focus(); box.select(); }
        },
        fill(value, start) {
            const chars = value.slice(0, this.length - start).split('');
            chars.forEach((char, offset) => { this.digits[start + offset] = char; });
            this.focusBox(Math.min(start + chars.length, this.length - 1));
            if (this.code.length === this.length) {
                this.$nextTick(() => this.$dispatch('otp-complete'));
            }
        },
        onInput(index, event) {
            const value = event.target.value.replace(/\D/g, '');
            if (value.length > 1) {
                this.fill(value, index);
            } else {
                this.digits[index] = value;
                if (value && index < this.length - 1) { this.focusBox(index + 1); }
                if (this.code.length === this.length) { this.$nextTick(() => this.$dispatch('otp-complete')); }
            }
            event.target.value = this.digits[index];
        },
        onKeydown(index, event) {
            if (event.key === 'Backspace' && ! this.digits[index] && index > 0) {
                event.preventDefault();
                this.digits[index - 1] = '';
                this.focusBox(index - 1);
            } else if (event.key === 'ArrowLeft' && index > 0) {
                event.preventDefault();
                this.focusBox(index - 1);
            } else if (event.key === 'ArrowRight' && index < this.length - 1) {
                event.preventDefault();
                this.focusBox(index + 1);
            }
        },
        onPaste(event) {
            const text = (event.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '');
            if (text) {
                event.preventDefault();
                this.fill(text, 0);
            }
        },
    }"
    x-on:paste="onPaste($event)"
    {{ $attributes->merge(['class' => 'flex items-center justify-between gap-2 sm:gap-3']) }}
>
    <input type="hidden" name="{{ $name }}" x-bind:value="code">

    @for ($i = 0; $i < $length; $i++)
        <input
            x-ref="box{{ $i }}"
            type="text"
            inputmode="numeric"
            pattern="[0-9]*"
            maxlength="{{ $length }}"
            x-bind:value="digits[{{ $i }}]"
            x-on:input="onInput({{ $i }}, $event)"
            x-on:keydown="onKeydown({{ $i }}, $event)"
            x-on:focus="$event.target.select()"
            aria-label="Chữ số thứ {{ $i + 1 }} của mã"
            @if ($i === 0)
                autocomplete="one-time-code"
                @if ($autofocus) autofocus @endif
            @else
                autocomplete="off"
            @endif
            @class([
                'h-12 w-full min-w-0 max-w-[3.25rem] sm:h-14 rounded-xl border bg-white text-center text-xl sm:text-2xl font-bold text-neutral-900 shadow-sm transition focus:outline-none focus:ring-2',
                'border-neutral-300 focus:border-[#019D91] focus:ring-[#019D91]/20' => ! $invalid,
                'border-rose-400 focus:border-rose-500 focus:ring-rose-500/20' => $invalid,
            ])
        >
    @endfor
</div>
