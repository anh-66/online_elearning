@extends('layouts.instructor')

@section('title', 'Thu nhập & Rút tiền')

@section('content')
<div class="space-y-8" x-data="{ openRequestModal: false }">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 dark:text-white tracking-tight uppercase tracking-wide">Thu nhập & Rút tiền</h1>
        </div>
        <button type="button"
                @click="openRequestModal = true"
                class="inline-flex items-center justify-center px-4 py-2.5 rounded-xl bg-brand-700 text-white font-medium text-sm hover:bg-brand-800 shadow-sm transition active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed"
                {{ $stats['available_balance'] < 50000 ? 'disabled' : '' }}>
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Yêu cầu rút tiền
        </button>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-sm text-emerald-800 dark:text-emerald-200 flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error') || $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-sm text-rose-800 dark:text-rose-200">
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Số dư khả dụng -->
        <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-sm">
            <span class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider block">Số dư khả dụng</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-brand-600 dark:text-brand-400">
                    {{ number_format($stats['available_balance'], 0, ',', '.') }} ₫
                </span>
            </div>
            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-2">Số tiền có thể tạo lệnh rút ngay</p>
        </div>

        <!-- Tổng thu nhập thực nhận (70%) -->
        <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-sm">
            <span class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider block">Tổng thu nhập (70%)</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-neutral-900 dark:text-white">
                    {{ number_format($stats['instructor_share'], 0, ',', '.') }} ₫
                </span>
            </div>
            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-2">Từ {{ number_format($stats['total_sales'], 0, ',', '.') }} ₫ tổng doanh số bán</p>
        </div>

        <!-- Đang chờ duyệt -->
        <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-sm">
            <span class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider block">Đang chờ xử lý</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-amber-600 dark:text-amber-400">
                    {{ number_format($stats['pending_payouts'], 0, ',', '.') }} ₫
                </span>
            </div>
            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-2">Admin đang kiểm tra chuyển khoản</p>
        </div>

        <!-- Đã rút thành công -->
        <div class="p-5 rounded-2xl bg-white dark:bg-neutral-900 border border-neutral-200 dark:border-neutral-800 shadow-sm">
            <span class="text-xs font-semibold text-neutral-500 dark:text-neutral-400 uppercase tracking-wider block">Đã thanh toán</span>
            <div class="mt-2 flex items-baseline justify-between">
                <span class="text-2xl sm:text-3xl font-extrabold text-emerald-600 dark:text-emerald-400">
                    {{ number_format($stats['paid_payouts'], 0, ',', '.') }} ₫
                </span>
            </div>
            <p class="text-xs text-neutral-400 dark:text-neutral-500 mt-2">Đã nhận về tài khoản ngân hàng</p>
        </div>
    </div>

    <!-- Section 1: Lịch sử yêu cầu rút tiền -->
    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-200 dark:border-neutral-800 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-neutral-200 dark:border-neutral-800 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-neutral-900 dark:text-white uppercase tracking-wide">Lịch sử yêu cầu rút tiền</h2>
            </div>
        </div>

        @if($payoutRequests->isEmpty())
            <div class="p-8 text-center text-sm text-neutral-400">
                Bạn chưa tạo yêu cầu rút tiền nào.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-300">
                    <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-xs uppercase text-neutral-500 dark:text-neutral-400 font-semibold border-b border-neutral-200 dark:border-neutral-800">
                        <tr>
                            <th class="px-6 py-4">Mã lệnh</th>
                            <th class="px-6 py-4">Số tiền</th>
                            <th class="px-6 py-4">Ngân hàng thụ hưởng</th>
                            <th class="px-6 py-4">Thời gian</th>
                            <th class="px-6 py-4">Trạng thái</th>
                            <th class="px-6 py-4">Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800 font-medium">
                        @foreach($payoutRequests as $req)
                            <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-800/30 transition">
                                <td class="px-6 py-4 font-mono font-bold text-neutral-900 dark:text-white">
                                    #PO-{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4 font-bold text-neutral-900 dark:text-white text-base">
                                    {{ number_format($req->amount, 0, ',', '.') }} ₫
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-neutral-900 dark:text-white font-semibold">{{ $req->bank_name }}</div>
                                    <div class="text-xs text-neutral-500 font-mono">{{ $req->bank_account_number }} • {{ $req->bank_account_name }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs text-neutral-500">
                                    {{ $req->created_at->format('H:i d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($req->isPending())
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                            Chờ duyệt
                                        </span>
                                    @elseif($req->isApproved())
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                            Đã chuyển khoản
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-50 text-rose-700 dark:bg-rose-950/40 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                            Bị từ chối
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-neutral-500 max-w-xs truncate">
                                    {{ $req->admin_note ?? ($req->note ?? '—') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payoutRequests->hasPages())
                <div class="p-4 border-t border-neutral-200 dark:border-neutral-800">
                    {{ $payoutRequests->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Section 2: Bảng chi tiết doanh thu từng đơn hàng (70% - 30%) -->
    <div class="bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-200 dark:border-neutral-800 overflow-hidden shadow-sm">
        <div class="p-6 border-b border-neutral-200 dark:border-neutral-800">
            <h2 class="text-lg font-bold text-neutral-900 dark:text-white uppercase tracking-wide">Chi tiết thu nhập từng đơn hàng</h2>
        </div>

        @if($earnings->isEmpty())
            <div class="p-8 text-center text-sm text-neutral-400">
                Chưa có đơn mua khóa học nào được ghi nhận.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-300">
                    <thead class="bg-neutral-50 dark:bg-neutral-800/50 text-xs uppercase text-neutral-500 dark:text-neutral-400 font-semibold border-b border-neutral-200 dark:border-neutral-800">
                        <tr>
                            <th class="px-6 py-4">Khóa học</th>
                            <th class="px-6 py-4">Học viên</th>
                            <th class="px-6 py-4">Giá bán (100%)</th>
                            <th class="px-6 py-4 text-brand-600 dark:text-brand-400">Thực nhận (70%)</th>
                            <th class="px-6 py-4 text-neutral-400">Phí sàn (30%)</th>
                            <th class="px-6 py-4">Thời gian</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-neutral-200 dark:divide-neutral-800 font-medium">
                        @foreach($earnings as $earning)
                            <tr class="hover:bg-neutral-50/50 dark:hover:bg-neutral-800/30 transition">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-neutral-900 dark:text-white">{{ $earning->course->title ?? 'Khóa học' }}</div>
                                    <div class="text-xs text-neutral-400">ID: #C-{{ $earning->course_id }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-neutral-900 dark:text-white">{{ $earning->enrollment->user->name ?? 'Học viên' }}</div>
                                    <div class="text-xs text-neutral-400">{{ $earning->enrollment->user->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-neutral-700 dark:text-neutral-300">
                                    {{ number_format($earning->order_amount, 0, ',', '.') }} ₫
                                </td>
                                <td class="px-6 py-4 font-bold text-brand-600 dark:text-brand-400 text-base">
                                    +{{ number_format($earning->instructor_share, 0, ',', '.') }} ₫
                                </td>
                                <td class="px-6 py-4 text-xs text-neutral-400 font-mono">
                                    -{{ number_format($earning->admin_share, 0, ',', '.') }} ₫
                                </td>
                                <td class="px-6 py-4 text-xs text-neutral-500">
                                    {{ $earning->created_at->format('H:i d/m/Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($earnings->hasPages())
                <div class="p-4 border-t border-neutral-200 dark:border-neutral-800">
                    {{ $earnings->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Modal Yêu cầu rút tiền -->
    <template x-teleport="body">
        <div x-show="openRequestModal" x-cloak
             x-transition.opacity
             class="fixed inset-0 z-[9999] overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @keydown.escape.window="openRequestModal = false">
            <div class="relative w-full max-w-lg bg-white dark:bg-neutral-900 rounded-3xl border border-neutral-200 dark:border-neutral-800 shadow-2xl p-6 sm:p-8 my-auto"
                 @click.outside="openRequestModal = false">
                <div class="flex items-center justify-between pb-4 border-b border-neutral-200 dark:border-neutral-800">
                    <h3 class="text-xl font-bold text-neutral-900 dark:text-white">Tạo yêu cầu rút tiền</h3>
                    <button type="button" @click="openRequestModal = false" class="text-neutral-400 hover:text-neutral-700 dark:hover:text-white">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form action="{{ route('instructor.payouts.store') }}" method="POST" class="mt-6 space-y-4">
                    @csrf
                    <!-- Số dư khả dụng nhắc nhở -->
                    <div class="p-3.5 rounded-2xl bg-brand-50 dark:bg-brand-950/40 border border-brand-200 dark:border-brand-800 flex items-center justify-between text-xs">
                        <span class="text-brand-800 dark:text-brand-300 font-medium">Số dư khả dụng hiện tại:</span>
                        <span class="font-bold text-brand-700 dark:text-brand-400 text-sm">{{ number_format($stats['available_balance'], 0, ',', '.') }} ₫</span>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-1.5">Số tiền muốn rút (₫) *</label>
                        <input type="number" name="amount" required min="50000" max="{{ $stats['available_balance'] }}" step="1000"
                               placeholder="Ví dụ: 500000"
                               class="w-full rounded-2xl border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm focus:ring-brand-500 focus:border-brand-500 py-3">
                        <p class="text-[11px] text-neutral-400 mt-1">Tối thiểu: 50.000 ₫ — Tối đa: {{ number_format($stats['available_balance'], 0, ',', '.') }} ₫</p>
                    </div>

                    <div class="relative"
                         x-data="{
                             open: false,
                             selectedValue: '',
                             selectedLabel: '-- Chọn ngân hàng thụ hưởng --',
                             options: [
                                 { value: 'Vietcombank', label: 'Vietcombank (VCB)' },
                                 { value: 'MB Bank', label: 'MB Bank (Quân Đội)' },
                                 { value: 'Techcombank', label: 'Techcombank (TCB)' },
                                 { value: 'ACB', label: 'ACB (Á Châu)' },
                                 { value: 'VietinBank', label: 'VietinBank (CTG)' },
                                 { value: 'BIDV', label: 'BIDV (Đầu tư & Phát triển)' },
                                 { value: 'VPBank', label: 'VPBank' },
                                 { value: 'TPBank', label: 'TPBank (Tiên Phong)' },
                                 { value: 'Sacombank', label: 'Sacombank' },
                                 { value: 'HDBank', label: 'HDBank' },
                                 { value: 'MoMo', label: 'Ví điện tử MoMo' },
                             ],
                             select(val, label) {
                                 this.selectedValue = val;
                                 this.selectedLabel = label;
                                 this.open = false;
                             }
                         }">
                        <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-1.5">Tên Ngân Hàng *</label>
                        <input type="hidden" name="bank_name" :value="selectedValue" required>

                        <!-- Combobox Trigger Button -->
                        <button type="button"
                                @click="open = !open"
                                :aria-expanded="open"
                                data-slot="combobox-trigger"
                                class="w-full rounded-2xl border border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm py-3 px-4 flex items-center justify-between text-left focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 transition cursor-pointer select-none group/combobox-trigger shadow-2xs">
                            <span x-text="selectedLabel" class="truncate font-medium text-neutral-800 dark:text-neutral-200"></span>
                            <svg class="w-4 h-4 text-neutral-400 dark:text-neutral-500 transition-transform duration-200 group-hover/combobox-trigger:text-neutral-700 dark:group-hover/combobox-trigger:text-neutral-300 shrink-0 pointer-events-none"
                                 :class="open ? 'rotate-180 text-neutral-700 dark:text-neutral-300' : ''"
                                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Combobox Content Popup -->
                        <div x-show="open"
                             x-cloak
                             @click.outside="open = false"
                             @keydown.escape.window="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
                             data-slot="combobox-content"
                             class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-2xl border border-neutral-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 shadow-xl ring-1 ring-black/5 dark:ring-white/5 p-1 max-h-60 overflow-y-auto space-y-0.5 custom-scrollbar">
                            <template x-for="item in options" :key="item.value">
                                <button type="button"
                                        @click="select(item.value, item.label)"
                                        data-slot="combobox-item"
                                        class="w-full relative flex items-center justify-between px-3.5 py-2.5 text-sm rounded-xl transition-colors cursor-pointer select-none text-left"
                                        :class="selectedValue === item.value 
                                            ? 'bg-neutral-100 dark:bg-neutral-700/70 text-neutral-900 dark:text-white font-medium' 
                                            : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-700/40'">
                                    <span x-text="item.label"></span>
                                    <svg x-show="selectedValue === item.value"
                                         class="w-4 h-4 text-neutral-900 dark:text-white shrink-0 pointer-events-none"
                                         fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-1.5">Số Tài Khoản / Số Thẻ *</label>
                        <input type="text" name="bank_account_number" required placeholder="Nhập số tài khoản ngân hàng"
                               class="w-full rounded-2xl border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm focus:ring-brand-500 focus:border-brand-500 py-3">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-1.5">Tên Chủ Tài Khoản (Viết hoa không dấu) *</label>
                        <input type="text" name="bank_account_name" required placeholder="NGUYEN VAN A"
                               class="w-full uppercase rounded-2xl border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm focus:ring-brand-500 focus:border-brand-500 py-3">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-neutral-500 mb-1.5">Ghi chú (Tùy chọn)</label>
                        <input type="text" name="note" placeholder="Lời nhắn gửi Admin..."
                               class="w-full rounded-2xl border-neutral-300 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-sm focus:ring-brand-500 focus:border-brand-500 py-3">
                    </div>

                    <div class="pt-4 flex items-center justify-end gap-3">
                        <button type="button" @click="openRequestModal = false"
                                class="px-5 py-2.5 rounded-xl border border-neutral-300 dark:border-neutral-700 text-sm font-semibold hover:bg-neutral-100 dark:hover:bg-neutral-800 transition">
                            Hủy
                        </button>
                        <button type="submit"
                                class="px-6 py-2.5 rounded-xl bg-brand-700 text-white text-sm font-semibold hover:bg-brand-800 transition shadow-sm">
                            Xác nhận gửi yêu cầu
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
