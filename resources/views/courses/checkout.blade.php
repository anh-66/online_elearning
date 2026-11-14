@extends('layouts.public')

@section('title', 'Thanh toán học phí - ' . $course->title)

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    /* Strict font unification */
    .checkout-root, .checkout-root * {
        font-family: 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif !important;
    }
    .price-num {
        font-variant-numeric: tabular-nums;
        font-feature-settings: "tnum";
        font-style: normal !important;
        letter-spacing: 0 !important;
    }
    /* Remove scrollbars */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }
    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
    @media print {
        body * {
            visibility: hidden;
        }
        #printable-invoice, #printable-invoice * {
            visibility: visible;
        }
        #printable-invoice {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
@endpush

@section('content')
<div class="checkout-root min-h-screen bg-gray-50/70 dark:bg-[#0d1117] text-gray-900 dark:text-gray-100 flex flex-col transition-colors"
     x-data="{
         currentStep: 1,
         hasSavedMethod: @js((bool) $savedPaymentMethod),
         selectedPayment: @js($savedPaymentMethod ? 'saved' : 'card'),
         showOtherMethods: @js(! (bool) $savedPaymentMethod),
         isSubmitting: false,
         errorMessage: '',
         invoiceModalOpen: false,

         // Input fields
         card_name: '{{ auth()->user()->name ?? '' }}',
         card_number: '',
         card_exp: '',
         card_cvc: '',
         momo_phone: '',
         momo_name: '{{ auth()->user()->name ?? '' }}',

         orderData: {
             orderCode: '#ACD-{{ strtoupper(substr(md5(auth()->id() . '-' . $course->id . '-sample'), 0, 5)) }}',
             paidAt: '{{ now()->format('H:i, d/m/Y') }}',
             paymentTypeLabel: 'Thẻ tín dụng (Kết thúc bằng •••• 4242)',
             playerUrl: '{{ route('learning.player', $course->slug) }}',
             courseUrl: '{{ route('courses.show', $course->slug) }}',
             courseTitle: @js($course->title),
             coursePrice: '{{ number_format($course->price, 0, ',', '.') }} ₫'
         },

         getPaymentLabel() {
             switch (this.selectedPayment) {
                 case 'card':
                     const last4 = this.card_number.replace(/\D/g, '').slice(-4) || '4242';
                     return 'Thẻ tín dụng (Kết thúc bằng **** ' + last4 + ')';
                 case 'momo':
                     const cleanPhone = this.momo_phone.replace(/\D/g, '') || '0988123456';
                     const masked = cleanPhone.length >= 7
                         ? cleanPhone.slice(0, 4) + '***' + cleanPhone.slice(-3)
                         : this.momo_phone || '0988***456';
                     return 'Ví MoMo (' + masked + ')';
                 case 'bank':
                     return 'Chuyển khoản VietQR (MB Bank)';
                 case 'cod':
                     return 'Thanh toán sau (COD)';
                 case 'saved':
                     return @js($savedPaymentMethod ? $savedPaymentMethod->title : 'Phương thức đã lưu');
                 default:
                     return 'Thanh toán trực tuyến';
             }
         },

         goToStep(step) {
             if (step === 2) {
                 this.currentStep = 2;
                 window.scrollTo({ top: 0, behavior: 'smooth' });
             } else if (step === 1) {
                 this.currentStep = 1;
                 window.scrollTo({ top: 0, behavior: 'smooth' });
             }
         },

         async submitPayment(event) {
             this.isSubmitting = true;
             this.errorMessage = '';

             const form = event.target;
             const formData = new FormData(form);
             formData.set('payment_type', this.selectedPayment);

             try {
                 const res = await fetch(form.action, {
                     method: 'POST',
                     headers: {
                         'Accept': 'application/json',
                         'X-Requested-With': 'XMLHttpRequest',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                     },
                     body: formData
                 });

                 if (!res.ok) {
                     const errData = await res.json().catch(() => ({}));
                     throw new Error(errData.message || 'Thanh toán thất bại. Vui lòng kiểm tra lại thông tin.');
                 }

                 const data = await res.json();
                 this.orderData = {
                     orderCode: data.order_code || '#ACD-99281',
                     paidAt: data.paid_at || '{{ now()->format('H:i, d/m/Y') }}',
                     paymentTypeLabel: this.getPaymentLabel(),
                     playerUrl: data.player_url || '{{ route('learning.player', $course->slug) }}',
                     courseUrl: data.course_url || '{{ route('courses.show', $course->slug) }}',
                     courseTitle: data.course_title || @js($course->title),
                     coursePrice: data.course_price || '{{ number_format($course->price, 0, ',', '.') }}đ'
                 };

                 this.currentStep = 3;
                 window.scrollTo({ top: 0, behavior: 'smooth' });
             } catch (err) {
                 console.error('Checkout error:', err);
                 // Nếu API JSON gặp lỗi mạng bất thường, fallback submit chuẩn
                 if (err.message && err.message.includes('Thanh toán thất bại')) {
                     this.errorMessage = err.message;
                 } else {
                     form.submit();
                 }
             } finally {
                 this.isSubmitting = false;
             }
         }
     }">

    {{-- Global Sub-Header --}}
    <div class="bg-white dark:bg-[#161b22] border-b border-gray-100 dark:border-neutral-800 transition-colors">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between">
            <a href="{{ route('courses.show', $course->slug) }}" class="flex items-center gap-2 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors group">
                <iconify-icon icon="lucide:arrow-left" class="text-base group-hover:-translate-x-1 transition-transform"></iconify-icon>
                <span>Quay lại khóa học</span>
            </a>
            <div class="flex items-center gap-3">
                <span class="text-xs text-gray-400 dark:text-gray-500 font-medium hidden sm:inline">Thanh toán an toàn bởi EduLearn Pay</span>
                <iconify-icon icon="lucide:shield-check" class="text-emerald-600 text-lg"></iconify-icon>
            </div>
        </div>
    </div>

    {{-- Main Checkout Content --}}
    <main class="flex-1 py-8 sm:py-12">
        <div class="max-w-7xl xl:max-w-[1360px] mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1" :class="currentStep === 3 ? 'max-w-2xl mx-auto' : 'lg:grid-cols-12 gap-8 lg:gap-10 items-start'">

                {{-- Left Side: Order Summary (5 cols - ẩn khi ở Bước 3 Hoàn tất để giao diện gọn gàng) --}}
                <div x-show="currentStep < 3"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-x-2"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     class="lg:col-span-5 flex flex-col gap-6 order-2 lg:order-1">
                    <section class="bg-white dark:bg-[#161b22] p-6 sm:p-8 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm transition-colors">
                        {{-- Top Header aligned with Stepper --}}
                        <div class="flex items-center justify-between border-b border-gray-200 dark:border-neutral-800 mb-8 pb-3 select-none">
                            <span class="text-xs sm:text-sm font-bold text-gray-900 dark:text-white uppercase tracking-wider">Thông tin đơn hàng</span>
                            <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">1 khóa học</span>
                        </div>

                        <div class="mb-6">
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Tóm tắt đơn hàng</h2>
                            <p class="text-gray-500 dark:text-gray-400 mt-1.5 text-sm">Thông tin khóa học và chi tiết thanh toán.</p>
                        </div>

                        {{-- Course Card --}}
                        <div class="flex gap-4 pb-6 border-b border-gray-100 dark:border-neutral-800">
                            <div class="w-20 h-20 sm:w-24 sm:h-24 bg-gray-100 dark:bg-neutral-800 rounded-xl overflow-hidden shrink-0 border border-gray-100 dark:border-neutral-700">
                                @if($course->thumbnail)
                                    <img src="{{ Storage::disk('public')->url($course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <iconify-icon icon="lucide:book-open" class="text-3xl text-[#019D91]"></iconify-icon>
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-col justify-between py-0.5 min-w-0">
                                <div>
                                    <span class="inline-block text-[11px] font-bold uppercase tracking-wider text-[#019D91] dark:text-[#02b3a5] mb-1">
                                        {{ $course->category->name ?? 'Khóa học' }}
                                    </span>
                                    <h3 class="font-bold text-gray-900 dark:text-white line-clamp-2 leading-relaxed text-sm sm:text-base">
                                        {{ $course->title }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        Giảng viên: <strong class="text-gray-700 dark:text-gray-300">{{ $course->instructor->name ?? 'EduLearn' }}</strong>
                                    </p>
                                </div>
                                <span class="text-base font-bold text-gray-900 dark:text-white mt-2 price-num">
                                    {{ number_format($course->price, 0, ',', '.') }} đ
                                </span>
                            </div>
                        </div>

                        {{-- Pricing Breakdown --}}
                        <div class="space-y-3 pt-6 text-sm text-gray-600 dark:text-gray-400">
                            <div class="flex justify-between">
                                <span>Tạm tính</span>
                                <span class="font-bold text-gray-900 dark:text-white price-num">{{ number_format($course->price, 0, ',', '.') }} đ</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Ưu đãi học viên</span>
                                <span class="text-emerald-600 dark:text-emerald-400 font-bold price-num">-0 đ</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Thuế VAT (0%)</span>
                                <span class="font-bold text-gray-900 dark:text-white price-num">0 đ</span>
                            </div>
                            <div class="flex justify-between pt-4 border-t border-gray-100 dark:border-neutral-800 items-baseline">
                                <span class="text-base font-bold text-gray-900 dark:text-white">Tổng thanh toán</span>
                                <span class="text-2xl font-bold text-[#019D91] dark:text-[#02b3a5] price-num tracking-normal">{{ number_format($course->price, 0, ',', '.') }} đ</span>
                            </div>
                        </div>
                    </section>

                    {{-- Security & Trust Badges --}}
                    <div class="flex items-center justify-around px-2 py-3 bg-white dark:bg-[#161b22] rounded-xl border border-gray-100 dark:border-neutral-800 text-gray-400 text-xs">
                        <div class="flex items-center gap-1.5 font-medium">
                            <iconify-icon icon="lucide:shield-check" class="text-emerald-600 text-base"></iconify-icon>
                            <span>Bảo mật SSL 256-bit</span>
                        </div>
                        <div class="h-3 w-px bg-gray-200 dark:border-neutral-800"></div>
                        <div class="flex items-center gap-1.5 font-medium">
                            <iconify-icon icon="lucide:lock" class="text-blue-600 text-base"></iconify-icon>
                            <span>Thanh toán an toàn</span>
                        </div>
                    </div>
                </div>

                {{-- Right Side: Checkout Flow (7 cols khi < 3, full-width khi = 3) --}}
                <div :class="currentStep === 3 ? 'w-full' : 'lg:col-span-7'"
                     class="bg-white dark:bg-[#161b22] p-6 sm:p-8 rounded-2xl border border-gray-100 dark:border-neutral-800 shadow-sm order-1 lg:order-2 transition-all">

                    {{-- Stepper (ĐÃ LOẠI BỎ HOÀN TOÀN THANH TRƯỢT NGANG - GIAO DIỆN MƯỢT MÀ KHÔNG BỊ TRÀN) --}}
                    <div class="flex items-center justify-between border-b border-gray-200 dark:border-neutral-800 mb-8 pb-3 select-none">
                        <div class="flex items-center gap-3 sm:gap-6 text-xs sm:text-sm font-semibold">
                            {{-- Step 1 Tab --}}
                            <button type="button"
                                    @click="if (currentStep === 2) currentStep = 1"
                                    :disabled="currentStep === 3"
                                    class="pb-2 transition-all border-b-2 flex items-center gap-1.5"
                                    :class="currentStep === 1
                                        ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white font-bold'
                                        : (currentStep > 1 ? 'border-transparent text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 cursor-pointer' : 'border-transparent text-gray-400')">
                                <template x-if="currentStep > 1">
                                    <iconify-icon icon="lucide:check-circle-2" class="text-base text-emerald-600"></iconify-icon>
                                </template>
                                <span class="inline-block" :class="currentStep === 3 ? '' : 'sm:hidden'">01. Phương thức</span>
                                <span class="hidden sm:inline" x-show="currentStep !== 3">01. Phương thức thanh toán</span>
                            </button>

                            {{-- Step 2 Tab --}}
                            <button type="button"
                                    @click="if (currentStep === 1) currentStep = 2"
                                    :disabled="currentStep === 3"
                                    class="pb-2 transition-all border-b-2 flex items-center gap-1.5"
                                    :class="currentStep === 2
                                        ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white font-bold'
                                        : (currentStep > 2 ? 'border-transparent text-emerald-600 dark:text-emerald-400' : 'border-transparent text-gray-400')">
                                <template x-if="currentStep > 2">
                                    <iconify-icon icon="lucide:check-circle-2" class="text-base text-emerald-600"></iconify-icon>
                                </template>
                                <span class="inline-block" :class="currentStep === 3 ? '' : 'sm:hidden'">02. Thông tin</span>
                                <span class="hidden sm:inline" x-show="currentStep !== 3">02. Thông tin thanh toán</span>
                            </button>

                            {{-- Step 3 Tab --}}
                            <div class="pb-2 transition-all border-b-2 flex items-center gap-1.5"
                                 :class="currentStep === 3
                                     ? 'border-gray-900 dark:border-white text-gray-900 dark:text-white font-bold'
                                     : 'border-transparent text-gray-400'">
                                <template x-if="currentStep === 3">
                                    <iconify-icon icon="lucide:check-circle-2" class="text-base text-emerald-600"></iconify-icon>
                                </template>
                                <span class="inline-block" :class="currentStep === 3 ? '' : 'sm:hidden'">03. Hoàn tất</span>
                                <span class="hidden sm:inline" x-show="currentStep !== 3">03. Hoàn tất đơn hàng</span>
                            </div>
                        </div>

                        {{-- Step Counter --}}
                        <div class="text-xs text-gray-400 font-semibold uppercase tracking-wider shrink-0 pb-2"
                             x-text="'BƯỚC ' + currentStep + ' / 3'">
                        </div>
                    </div>

                    {{-- Form xử lý thanh toán --}}
                    <form method="POST" action="{{ route('courses.checkout.process', $course->slug) }}" @submit.prevent="submitPayment($event)">
                        @csrf

                        {{-- Error message notification --}}
                        <div x-show="errorMessage" x-cloak class="mb-6 p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-900 text-red-600 dark:text-red-300 text-sm flex items-center gap-3">
                            <iconify-icon icon="lucide:alert-circle" class="text-xl shrink-0"></iconify-icon>
                            <span x-text="errorMessage"></span>
                        </div>

                        {{-- ======================================================== --}}
                        {{-- BƯỚC 1: LỰA CHỌN PHƯƠNG THỨC THANH TOÁN (currentStep === 1) --}}
                        {{-- ======================================================== --}}
                        <div x-show="currentStep === 1"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-6">

                            <div class="mb-6">
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Lựa chọn phương thức thanh toán</h1>
                                <p class="text-gray-500 dark:text-gray-400 mt-1.5 text-sm">Vui lòng chọn hình thức thuận tiện nhất cho bạn để tiếp tục.</p>
                            </div>

                            {{-- Banner Phương thức đã lưu (nếu đã từng thanh toán trước đó) --}}
                            @if($savedPaymentMethod)
                                <div class="p-5 rounded-2xl border-2 transition-all cursor-pointer"
                                     @click="selectedPayment = 'saved'"
                                     :class="selectedPayment === 'saved' ? 'border-[#0071e3] bg-blue-50/40 dark:bg-blue-950/20 shadow-sm' : 'border-gray-200 dark:border-neutral-800 bg-gray-50/50 dark:bg-neutral-900/20 hover:border-gray-300'">
                                    <div class="flex items-center justify-between">
                                        <label class="flex items-center gap-4 cursor-pointer flex-1 min-w-0">
                                            <input type="radio" name="payment_type_radio" value="saved"
                                                   @change="selectedPayment = 'saved'"
                                                   :checked="selectedPayment === 'saved'"
                                                   class="sr-only">
                                            <span data-slot="radio-group-item"
                                                  class="relative size-4 rounded-full border flex items-center justify-center shrink-0 aspect-square outline-none transition-all duration-200"
                                                  :class="selectedPayment === 'saved' 
                                                        ? 'border-[#0071e3] bg-[#0071e3]' 
                                                        : 'border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900/50'">
                                                <span data-slot="radio-group-indicator" class="relative z-10 flex size-4 items-center justify-center pointer-events-none">
                                                    <span class="size-2 rounded-full bg-white transition-all duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] pointer-events-none"
                                                          :class="selectedPayment === 'saved' ? 'scale-100 opacity-100' : 'scale-0 opacity-0'"></span>
                                                </span>
                                            </span>
                                            <div class="flex items-center gap-1.5 shrink-0 py-0.5">
                                                @if($savedPaymentMethod->type === 'momo')
                                                    <iconify-icon icon="logos:momo" class="text-2xl"></iconify-icon>
                                                @else
                                                    <iconify-icon icon="logos:visa" class="text-2xl"></iconify-icon>
                                                    <iconify-icon icon="logos:mastercard" class="text-2xl"></iconify-icon>
                                                @endif
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="font-bold text-sm sm:text-base text-gray-900 dark:text-white">{{ $savedPaymentMethod->title }}</span>
                                                    <span class="px-2.5 py-0.5 text-[10px] font-bold bg-[#0071e3] text-white rounded-full shrink-0">Đã lưu</span>
                                                </div>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $savedPaymentMethod->subtitle }}</p>
                                            </div>
                                        </label>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between pt-2">
                                    <span class="text-xs font-semibold uppercase tracking-wider text-gray-400">Hoặc chọn phương thức khác</span>
                                    <button type="button" @click="showOtherMethods = !showOtherMethods"
                                            class="text-xs font-semibold text-[#0071e3] hover:underline flex items-center gap-1">
                                        <span x-text="showOtherMethods ? 'Thu gọn' : 'Thay đổi phương thức thanh toán'"></span>
                                        <iconify-icon icon="lucide:chevron-down" :class="showOtherMethods ? 'rotate-180' : ''" class="transition-transform"></iconify-icon>
                                    </button>
                                </div>
                            @endif

                            {{-- Lưới các phương thức thanh toán --}}
                            <div x-show="showOtherMethods" x-transition class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    {{-- Option 1: Credit / Debit Card --}}
                                    <label class="relative flex flex-col p-5 cursor-pointer rounded-xl border-2 transition-all group"
                                           :class="selectedPayment === 'card' ? 'border-neutral-900 dark:border-white bg-neutral-50/80 dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 hover:border-gray-300 dark:hover:border-neutral-700'">
                                        <input type="radio" name="payment_type_radio" value="card"
                                               @change="selectedPayment = 'card'"
                                               :checked="selectedPayment === 'card'"
                                               class="absolute opacity-0">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2.5">
                                                <span data-slot="radio-group-item"
                                                      class="relative size-4 rounded-full border flex items-center justify-center shrink-0 aspect-square outline-none transition-all duration-200 group-hover:border-neutral-400"
                                                      :class="selectedPayment === 'card' 
                                                            ? 'border-neutral-900 dark:border-white bg-neutral-900 dark:bg-white' 
                                                            : 'border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900/50'">
                                                    <span data-slot="radio-group-indicator" class="relative z-10 flex size-4 items-center justify-center pointer-events-none">
                                                        <span class="size-2 rounded-full bg-white dark:bg-neutral-950 transition-all duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] pointer-events-none"
                                                              :class="selectedPayment === 'card' ? 'scale-100 opacity-100' : 'scale-0 opacity-0'"></span>
                                                    </span>
                                                </span>
                                                <iconify-icon icon="lucide:credit-card" class="text-2xl text-gray-900 dark:text-white"></iconify-icon>
                                            </div>
                                            <div class="flex gap-1">
                                                <iconify-icon icon="logos:visa" class="text-xl"></iconify-icon>
                                                <iconify-icon icon="logos:mastercard" class="text-xl"></iconify-icon>
                                            </div>
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-white text-sm">Thẻ tín dụng / Ghi nợ</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Visa, Mastercard, JCB, Amex</span>
                                    </label>

                                    {{-- Option 2: E-Wallet MoMo --}}
                                    <label class="relative flex flex-col p-5 cursor-pointer rounded-xl border-2 transition-all group"
                                           :class="selectedPayment === 'momo' ? 'border-neutral-900 dark:border-white bg-neutral-50/80 dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 hover:border-gray-300 dark:hover:border-neutral-700'">
                                        <input type="radio" name="payment_type_radio" value="momo"
                                               @change="selectedPayment = 'momo'"
                                               :checked="selectedPayment === 'momo'"
                                               class="absolute opacity-0">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2.5">
                                                <span data-slot="radio-group-item"
                                                      class="relative size-4 rounded-full border flex items-center justify-center shrink-0 aspect-square outline-none transition-all duration-200 group-hover:border-neutral-400"
                                                      :class="selectedPayment === 'momo' 
                                                            ? 'border-neutral-900 dark:border-white bg-neutral-900 dark:bg-white' 
                                                            : 'border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900/50'">
                                                    <span data-slot="radio-group-indicator" class="relative z-10 flex size-4 items-center justify-center pointer-events-none">
                                                        <span class="size-2 rounded-full bg-white dark:bg-neutral-950 transition-all duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] pointer-events-none"
                                                              :class="selectedPayment === 'momo' ? 'scale-100 opacity-100' : 'scale-0 opacity-0'"></span>
                                                    </span>
                                                </span>
                                                <iconify-icon icon="lucide:wallet" class="text-2xl text-[#A50064]"></iconify-icon>
                                            </div>
                                            <div class="flex gap-1.5 items-center">
                                                <iconify-icon icon="simple-icons:momo" class="text-xl text-[#A50064]"></iconify-icon>
                                            </div>
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-white text-sm">Ví điện tử MoMo</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">MoMo, ZaloPay, ShopeePay</span>
                                    </label>

                                    {{-- Option 3: Bank Transfer VietQR --}}
                                    <label class="relative flex flex-col p-5 cursor-pointer rounded-xl border-2 transition-all group"
                                           :class="selectedPayment === 'bank' ? 'border-neutral-900 dark:border-white bg-neutral-50/80 dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 hover:border-gray-300 dark:hover:border-neutral-700'">
                                        <input type="radio" name="payment_type_radio" value="bank"
                                               @change="selectedPayment = 'bank'"
                                               :checked="selectedPayment === 'bank'"
                                               class="absolute opacity-0">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2.5">
                                                <span data-slot="radio-group-item"
                                                      class="relative size-4 rounded-full border flex items-center justify-center shrink-0 aspect-square outline-none transition-all duration-200 group-hover:border-neutral-400"
                                                      :class="selectedPayment === 'bank' 
                                                            ? 'border-neutral-900 dark:border-white bg-neutral-900 dark:bg-white' 
                                                            : 'border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900/50'">
                                                    <span data-slot="radio-group-indicator" class="relative z-10 flex size-4 items-center justify-center pointer-events-none">
                                                        <span class="size-2 rounded-full bg-white dark:bg-neutral-950 transition-all duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] pointer-events-none"
                                                              :class="selectedPayment === 'bank' ? 'scale-100 opacity-100' : 'scale-0 opacity-0'"></span>
                                                    </span>
                                                </span>
                                                <iconify-icon icon="lucide:landmark" class="text-2xl text-blue-600"></iconify-icon>
                                            </div>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-300">VietQR</span>
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-white text-sm">Chuyển khoản ngân hàng</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Quét mã QR MB Bank nhanh 24/7</span>
                                    </label>

                                    {{-- Option 4: COD --}}
                                    <label class="relative flex flex-col p-5 cursor-pointer rounded-xl border-2 transition-all group"
                                           :class="selectedPayment === 'cod' ? 'border-neutral-900 dark:border-white bg-neutral-50/80 dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 hover:border-gray-300 dark:hover:border-neutral-700'">
                                        <input type="radio" name="payment_type_radio" value="cod"
                                               @change="selectedPayment = 'cod'"
                                               :checked="selectedPayment === 'cod'"
                                               class="absolute opacity-0">
                                        <div class="flex items-center justify-between mb-3">
                                            <div class="flex items-center gap-2.5">
                                                <span data-slot="radio-group-item"
                                                      class="relative size-4 rounded-full border flex items-center justify-center shrink-0 aspect-square outline-none transition-all duration-200 group-hover:border-neutral-400"
                                                      :class="selectedPayment === 'cod' 
                                                            ? 'border-neutral-900 dark:border-white bg-neutral-900 dark:bg-white' 
                                                            : 'border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900/50'">
                                                    <span data-slot="radio-group-indicator" class="relative z-10 flex size-4 items-center justify-center pointer-events-none">
                                                        <span class="size-2 rounded-full bg-white dark:bg-neutral-950 transition-all duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] pointer-events-none"
                                                              :class="selectedPayment === 'cod' ? 'scale-100 opacity-100' : 'scale-0 opacity-0'"></span>
                                                    </span>
                                                </span>
                                                <iconify-icon icon="lucide:truck" class="text-2xl text-gray-400"></iconify-icon>
                                            </div>
                                            <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-gray-100 text-gray-600 dark:bg-neutral-800 dark:text-gray-400">Tư vấn</span>
                                        </div>
                                        <span class="font-bold text-gray-900 dark:text-white text-sm">Thanh toán sau (COD)</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-1">Nhận tài liệu & tư vấn trực tiếp</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Nút tiếp tục sang Bước 2 --}}
                            <div class="pt-6 border-t border-gray-100 dark:border-neutral-800">
                                <button type="button" @click="goToStep(2)"
                                        class="w-full py-4 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-bold hover:bg-black dark:hover:bg-neutral-100 transition-all flex items-center justify-center gap-2 shadow-lg shadow-gray-200 dark:shadow-none text-base">
                                    <span>Tiếp tục đến thông tin thanh toán</span>
                                    <iconify-icon icon="lucide:arrow-right" class="text-lg"></iconify-icon>
                                </button>
                                <p class="text-center text-xs text-gray-400 dark:text-gray-500 mt-3">
                                    Bước tiếp theo: Nhập hoặc xác nhận chi tiết thanh toán của bạn.
                                </p>
                            </div>
                        </div>

                        {{-- ======================================================== --}}
                        {{-- BƯỚC 2: THÔNG TIN THANH TOÁN (currentStep === 2) --}}
                        {{-- ======================================================== --}}
                        <div x-show="currentStep === 2"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="space-y-6">

                            {{-- Header & Selected Method Summary Badge --}}
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-4 border-b border-gray-100 dark:border-neutral-800">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Thông tin thanh toán</h1>
                                    <p class="text-gray-500 dark:text-gray-400 mt-1 text-sm">Vui lòng điền thông tin để hoàn tất đơn hàng.</p>
                                </div>
                                <button type="button" @click="goToStep(1)"
                                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#0071e3] hover:underline self-start sm:self-auto py-1.5 px-3 rounded-lg bg-blue-50 dark:bg-blue-950/40">
                                    <iconify-icon icon="lucide:arrow-left"></iconify-icon>
                                    <span>Đổi phương thức</span>
                                </button>
                            </div>

                            {{-- Khung hiển thị phương thức hiện tại --}}
                            <div class="p-4 rounded-xl bg-gray-50 dark:bg-neutral-800/50 border border-gray-200 dark:border-neutral-700 flex items-center gap-3">
                                <template x-if="selectedPayment === 'card'">
                                    <div class="flex items-center gap-2">
                                        <iconify-icon icon="lucide:credit-card" class="text-xl text-gray-800 dark:text-white"></iconify-icon>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">Thẻ tín dụng / Ghi nợ quốc tế</span>
                                    </div>
                                </template>
                                <template x-if="selectedPayment === 'momo'">
                                    <div class="flex items-center gap-2">
                                        <iconify-icon icon="simple-icons:momo" class="text-xl text-[#A50064]"></iconify-icon>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">Ví điện tử MoMo</span>
                                    </div>
                                </template>
                                <template x-if="selectedPayment === 'bank'">
                                    <div class="flex items-center gap-2">
                                        <iconify-icon icon="lucide:landmark" class="text-xl text-blue-600"></iconify-icon>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">Chuyển khoản VietQR MB Bank</span>
                                    </div>
                                </template>
                                <template x-if="selectedPayment === 'cod'">
                                    <div class="flex items-center gap-2">
                                        <iconify-icon icon="lucide:truck" class="text-xl text-gray-600"></iconify-icon>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">Thanh toán sau (COD)</span>
                                    </div>
                                </template>
                                <template x-if="selectedPayment === 'saved'">
                                    <div class="flex items-center gap-2">
                                        <iconify-icon icon="lucide:check-circle" class="text-xl text-emerald-600"></iconify-icon>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">Phương thức đã lưu</span>
                                    </div>
                                </template>
                            </div>

                            {{-- FIELDS: CARD --}}
                            <div x-show="selectedPayment === 'card'" class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tên trên thẻ</label>
                                    <input type="text" name="card_name" x-model="card_name" placeholder="NGUYEN VAN A"
                                           class="w-full px-4 py-3 bg-white dark:bg-[#161b22] border border-gray-200 dark:border-neutral-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all uppercase">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Số thẻ</label>
                                    <div class="relative">
                                        <input type="text" name="card_number" x-model="card_number" placeholder="4242 •••• •••• 4242"
                                               class="w-full px-4 py-3 bg-white dark:bg-[#161b22] border border-gray-200 dark:border-neutral-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all font-mono">
                                        <div class="absolute right-4 top-1/2 -translate-y-1/2 flex gap-1">
                                            <iconify-icon icon="logos:visa" class="text-xl"></iconify-icon>
                                            <iconify-icon icon="logos:mastercard" class="text-xl"></iconify-icon>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Ngày hết hạn</label>
                                        <input type="text" name="card_exp" x-model="card_exp" placeholder="MM/YY" maxlength="5"
                                               class="w-full px-4 py-3 bg-white dark:bg-[#161b22] border border-gray-200 dark:border-neutral-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">CVC / CVV</label>
                                        <input type="password" name="card_cvc" x-model="card_cvc" placeholder="•••" maxlength="4"
                                               class="w-full px-4 py-3 bg-white dark:bg-[#161b22] border border-gray-200 dark:border-neutral-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all font-mono">
                                    </div>
                                </div>
                            </div>

                            {{-- FIELDS: MOMO --}}
                            <div x-show="selectedPayment === 'momo'" class="space-y-4">
                                <div class="p-4 rounded-xl bg-pink-50 dark:bg-pink-950/30 border border-pink-100 dark:border-pink-900/40 flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-[#A50064] text-white flex items-center justify-center shrink-0">
                                        <iconify-icon icon="simple-icons:momo" class="text-2xl"></iconify-icon>
                                    </div>
                                    <div class="text-xs text-pink-950 dark:text-pink-200 leading-relaxed">
                                        Nhập số điện thoại đăng ký MoMo của bạn. Thông tin ví sẽ được lưu tự động vào tài khoản để dùng cho các khóa học tiếp theo.
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Số điện thoại MoMo</label>
                                    <input type="tel" name="momo_phone" x-model="momo_phone" placeholder="0988 123 456"
                                           class="w-full px-4 py-3 bg-white dark:bg-[#161b22] border border-gray-200 dark:border-neutral-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#A50064] transition-all font-mono">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tên chủ tài khoản MoMo</label>
                                    <input type="text" name="momo_name" x-model="momo_name" placeholder="NGUYEN VAN A"
                                           class="w-full px-4 py-3 bg-white dark:bg-[#161b22] border border-gray-200 dark:border-neutral-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#A50064] transition-all uppercase">
                                </div>
                            </div>

                            {{-- FIELDS: BANK --}}
                            <div x-show="selectedPayment === 'bank'" class="space-y-4">
                                <div class="p-5 rounded-2xl bg-blue-50/60 dark:bg-blue-950/30 border border-blue-100 dark:border-blue-900/40 text-xs sm:text-sm space-y-2.5">
                                    <div class="flex justify-between items-center pb-2 border-b border-blue-100 dark:border-blue-900/40">
                                        <span class="text-gray-500 dark:text-gray-400">Ngân hàng:</span>
                                        <strong class="text-blue-700 dark:text-blue-300 font-bold">MB Bank (Quân Đội)</strong>
                                    </div>
                                    <div class="flex justify-between items-center pb-2 border-b border-blue-100 dark:border-blue-900/40">
                                        <span class="text-gray-500 dark:text-gray-400">Số tài khoản:</span>
                                        <strong class="font-mono text-base font-bold text-gray-900 dark:text-white">0388 999 888</strong>
                                    </div>
                                    <div class="flex justify-between items-center pb-2 border-b border-blue-100 dark:border-blue-900/40">
                                        <span class="text-gray-500 dark:text-gray-400">Chủ tài khoản:</span>
                                        <strong class="font-semibold text-gray-900 dark:text-white">CONG TY CO PHAN EDULEARN</strong>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500 dark:text-gray-400">Nội dung chuyển khoản:</span>
                                        <span class="font-mono font-bold px-2 py-0.5 rounded bg-blue-200/80 dark:bg-blue-900 text-blue-900 dark:text-blue-100">EDUPAY {{ $course->id }}{{ auth()->id() }}</span>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-400 italic text-center">Hệ thống tự động kích hoạt tài khoản ngay sau khi nhận được chuyển khoản.</p>
                            </div>

                            {{-- FIELDS: COD --}}
                            <div x-show="selectedPayment === 'cod'" class="space-y-4">
                                <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-100 dark:border-amber-900/40 text-xs text-amber-900 dark:text-amber-200">
                                    Bạn sẽ thanh toán khi nhận bộ giáo trình và tài liệu in ấn gửi về tận nhà.
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Địa chỉ giao nhận giáo trình</label>
                                    <input type="text" name="cod_address" placeholder="Số nhà, tên đường, phường/xã, quận/huyện..."
                                           class="w-full px-4 py-3 bg-white dark:bg-[#161b22] border border-gray-200 dark:border-neutral-700 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-gray-900 dark:focus:ring-white transition-all">
                                </div>
                            </div>

                            {{-- FIELDS: SAVED METHOD --}}
                            <div x-show="selectedPayment === 'saved'" class="space-y-3">
                                @if($savedPaymentMethod)
                                    <div class="p-5 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-100 dark:border-emerald-900/40 flex items-center gap-4">
                                        <div class="flex items-center gap-1.5 shrink-0 py-0.5">
                                            @if($savedPaymentMethod->type === 'momo')
                                                <iconify-icon icon="logos:momo" class="text-2xl"></iconify-icon>
                                            @else
                                                <iconify-icon icon="logos:visa" class="text-2xl"></iconify-icon>
                                                <iconify-icon icon="logos:mastercard" class="text-2xl"></iconify-icon>
                                            @endif
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="font-bold text-gray-900 dark:text-white text-base">{{ $savedPaymentMethod->title }}</h3>
                                                <span class="px-2.5 py-0.5 text-[10px] font-bold bg-emerald-600 text-white rounded-full shrink-0">Khuyên dùng</span>
                                            </div>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $savedPaymentMethod->subtitle }}</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Actions in Step 2: Back button & Submit payment button --}}
                            <div class="pt-6 border-t border-gray-100 dark:border-neutral-800 flex flex-col sm:flex-row items-center gap-3">
                                <button type="button" @click="goToStep(1)"
                                        class="w-full sm:w-auto px-6 py-4 border border-gray-200 dark:border-neutral-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold hover:bg-gray-50 dark:hover:bg-neutral-800 transition-all text-sm flex items-center justify-center gap-2">
                                    <iconify-icon icon="lucide:arrow-left"></iconify-icon>
                                    <span>Quay lại</span>
                                </button>

                                <button type="submit"
                                        :disabled="isSubmitting"
                                        class="flex-1 w-full py-4 bg-gray-900 hover:bg-black dark:bg-white dark:hover:bg-neutral-100 text-white dark:text-gray-900 rounded-xl font-bold transition-all shadow-lg shadow-gray-200 dark:shadow-none flex items-center justify-center gap-2 text-base disabled:opacity-50">
                                    <template x-if="isSubmitting">
                                        <div class="flex items-center gap-2">
                                            <svg class="animate-spin h-5 w-5 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                            </svg>
                                            <span>Đang xử lý đơn hàng...</span>
                                        </div>
                                    </template>
                                    <template x-if="!isSubmitting">
                                        <span class="price-num">Thanh toán ngay {{ number_format($course->price, 0, ',', '.') }} đ</span>
                                    </template>
                                </button>
                            </div>

                            <p class="text-center text-xs text-gray-400 dark:text-gray-500 pt-2">
                                Bằng việc nhấn nút thanh toán, bạn đồng ý với <a href="#" class="underline text-gray-500">Điều khoản dịch vụ</a> và <a href="#" class="underline text-gray-500">Chính sách bảo mật</a> của EduLearn.
                            </p>
                        </div>

                    </form>

                    {{-- ======================================================== --}}
                    {{-- BƯỚC 3: HOÀN TẤT ĐƠN HÀNG (currentStep === 3) --}}
                    {{-- Chuẩn 100% theo mẫu thiết kế trong hình ảnh của người dùng --}}
                    {{-- ======================================================== --}}
                    <div x-show="currentStep === 3"
                         x-transition:enter="transition ease-out duration-400"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="py-6 sm:py-8 text-center max-w-lg mx-auto">

                        {{-- Checkmark Icon tròn xanh lá --}}
                        <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border-2 border-emerald-100 dark:border-emerald-800 flex items-center justify-center">
                            <iconify-icon icon="lucide:check" class="text-4xl text-emerald-500 font-extrabold"></iconify-icon>
                        </div>

                        {{-- Tiêu đề & Thông điệp hoàn tất --}}
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-2">
                            Thanh toán thành công!
                        </h2>
                        <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">
                            Cảm ơn bạn đã đăng ký khóa học. Mã đơn hàng của bạn là <strong class="text-gray-900 dark:text-white font-bold" x-text="orderData.orderCode">#ACD-99281</strong>
                        </p>

                        {{-- Khung thông tin giao dịch bo tròn --}}
                        <div class="bg-gray-50 dark:bg-neutral-800/60 rounded-2xl p-6 my-6 text-sm text-left space-y-3.5 border border-gray-100 dark:border-neutral-700/60">
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Thời gian:</span>
                                <span class="font-semibold text-gray-900 dark:text-white" x-text="orderData.paidAt">14:30, 24/05/2024</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <span class="text-gray-500 dark:text-gray-400">Hình thức:</span>
                                <span class="font-semibold text-gray-900 dark:text-white" x-text="orderData.paymentTypeLabel">Thẻ tín dụng (Visa **** 1234)</span>
                            </div>
                        </div>

                        {{-- 2 Nút hành động: Vào học ngay & Xem hóa đơn --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <a :href="orderData.playerUrl"
                               class="w-full py-4 bg-[#111827] dark:bg-white hover:bg-black dark:hover:bg-neutral-100 text-white dark:text-gray-900 font-bold rounded-xl flex items-center justify-center gap-2.5 transition shadow-lg shadow-gray-200 dark:shadow-none text-sm sm:text-base">
                                <iconify-icon icon="lucide:play-circle" class="text-xl"></iconify-icon>
                                <span>Vào học ngay</span>
                            </a>
                            <button type="button" @click="invoiceModalOpen = true"
                                    class="w-full py-4 bg-white dark:bg-neutral-800 hover:bg-gray-50 dark:hover:bg-neutral-700 text-gray-900 dark:text-white font-bold rounded-xl border border-gray-200 dark:border-neutral-700 flex items-center justify-center gap-2.5 transition text-sm sm:text-base">
                                <iconify-icon icon="lucide:file-text" class="text-lg text-gray-500"></iconify-icon>
                                <span>Xem hóa đơn</span>
                            </button>
                        </div>

                        {{-- Footer thông điệp: TIẾP THEO --}}
                        <div class="mt-8 pt-6 border-t border-gray-100 dark:border-neutral-800 text-center">
                            <span class="text-[11px] font-bold uppercase tracking-widest text-gray-400 dark:text-gray-500 block mb-1">TIẾP THEO</span>
                            <p class="text-xs text-gray-500 dark:text-gray-400 italic">Kiểm tra email của bạn để nhận hướng dẫn chi tiết và tài liệu đính kèm.</p>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </main>

    {{-- MODAL XEM HÓA ĐƠN CHI TIẾT --}}
    <div x-show="invoiceModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="invoiceModalOpen = false"></div>
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="relative bg-white dark:bg-[#161b22] rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-gray-100 dark:border-neutral-800 z-10"
                 id="printable-invoice"
                 @click.stop>
                
                {{-- Header hóa đơn --}}
                <div class="flex items-center justify-between pb-6 border-b border-gray-100 dark:border-neutral-800">
                    <div>
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-lg flex items-center justify-center font-bold text-sm">E</div>
                            <span class="font-extrabold text-lg tracking-tight">EduLearn</span>
                        </div>
                        <p class="text-xs text-gray-400 mt-1">Hóa đơn điện tử hợp lệ</p>
                    </div>
                    <div class="text-right">
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 rounded-full font-bold text-xs uppercase tracking-wider">Đã thanh toán</span>
                        <p class="text-xs font-mono font-semibold text-gray-600 dark:text-gray-400 mt-1" x-text="orderData.orderCode"></p>
                    </div>
                </div>

                {{-- Chi tiết hóa đơn --}}
                <div class="py-6 space-y-4 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Khách hàng:</span>
                        <strong class="text-gray-900 dark:text-white">{{ auth()->user()->name }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Email:</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ auth()->user()->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Thời gian tạo:</span>
                        <span class="text-gray-700 dark:text-gray-300" x-text="orderData.paidAt"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Hình thức thanh toán:</span>
                        <span class="text-gray-900 dark:text-white font-medium" x-text="orderData.paymentTypeLabel"></span>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-neutral-800">
                        <p class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-2">Khóa học đăng ký</p>
                        <div class="flex justify-between items-start gap-4">
                            <span class="font-semibold text-gray-900 dark:text-white" x-text="orderData.courseTitle"></span>
                            <span class="font-bold text-gray-900 dark:text-white shrink-0" x-text="orderData.coursePrice"></span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-neutral-800 flex justify-between items-baseline">
                        <span class="font-bold text-base text-gray-900 dark:text-white">Tổng tiền đã trả</span>
                        <span class="text-2xl font-extrabold text-[#019D91] dark:text-[#02b3a5]" x-text="orderData.coursePrice"></span>
                    </div>
                </div>

                {{-- Action buttons --}}
                <div class="pt-4 border-t border-gray-100 dark:border-neutral-800 flex gap-3 print:hidden">
                    <button type="button" onclick="window.print()" class="flex-1 py-3 bg-gray-900 dark:bg-white text-white dark:text-gray-900 rounded-xl font-bold text-sm hover:bg-black transition flex items-center justify-center gap-2">
                        <iconify-icon icon="lucide:printer"></iconify-icon>
                        <span>In hóa đơn</span>
                    </button>
                    <button type="button" @click="invoiceModalOpen = false" class="px-5 py-3 border border-gray-200 dark:border-neutral-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold text-sm hover:bg-gray-50 dark:hover:bg-neutral-800 transition">
                        Đóng
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
