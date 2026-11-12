@extends('layouts.admin')

@section('title', 'Quản lý Chi trả')
@section('header', 'Quản lý Chi trả Giảng viên')

@section('content')
<div class="space-y-6" x-data="{
    approveModalOpen: false,
    rejectModalOpen: false,
    selectedPayout: null,
    approveUrl: '',
    rejectUrl: '',
    openApprove(payout) {
        this.selectedPayout = payout;
        this.approveUrl = '{{ url('/admin/payouts') }}/' + payout.id + '/approve';
        this.approveModalOpen = true;
    },
    openReject(payout) {
        this.selectedPayout = payout;
        this.rejectUrl = '{{ url('/admin/payouts') }}/' + payout.id + '/reject';
        this.rejectModalOpen = true;
    }
}">
    <!-- Header Summary -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-neutral-100 tracking-tight uppercase">Chi trả Giảng viên</h1>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-1">Cơ chế đối soát doanh thu 70% Giảng viên — 30% Nền tảng EduLearn</p>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-sm text-emerald-800 dark:text-emerald-300 flex items-center justify-between">
            <span>{{ session('success') }}</span>
        </div>
    @endif
    @if(session('error') || $errors->any())
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-500/10 border border-rose-200 dark:border-rose-500/30 text-sm text-rose-800 dark:text-rose-300">
            {{ session('error') ?? $errors->first() }}
        </div>
    @endif

    <!-- 3 Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="glass-card p-5">
            <span class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider block">Doanh thu sàn giữ lại (30%)</span>
            <div class="text-2xl font-bold text-gray-900 dark:text-neutral-100 mt-1.5">
                {{ number_format($stats['total_platform_fee'], 0, ',', '.') }} ₫
            </div>
            <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">Trích từ tất cả đơn hàng đã bán</p>
        </div>

        <div class="glass-card p-5">
            <span class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider block">Đã chi trả thành công</span>
            <div class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1.5">
                {{ number_format($stats['total_paid'], 0, ',', '.') }} ₫
            </div>
            <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">Đã chuyển khoản cho giảng viên</p>
        </div>

        <div class="glass-card p-5">
            <span class="text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider block">Đang chờ xử lý</span>
            <div class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1.5">
                {{ number_format($stats['total_pending'], 0, ',', '.') }} ₫
            </div>
            <p class="text-xs text-gray-400 dark:text-neutral-500 mt-1">{{ $stats['pending_count'] }} lệnh cần Admin xác nhận chuyển khoản</p>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 border-b border-gray-200 dark:border-neutral-800 pb-3 text-sm font-medium">
        <a href="{{ route('admin.payouts.index') }}"
           class="px-4 py-2 rounded-lg transition {{ $status === 'all' ? 'bg-[#019D91] text-white font-semibold shadow-sm' : 'text-gray-600 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-800' }}">
            Tất cả
        </a>
        <a href="{{ route('admin.payouts.index', ['status' => 'pending']) }}"
           class="px-4 py-2 rounded-lg transition {{ $status === 'pending' ? 'bg-[#019D91] text-white font-semibold shadow-sm' : 'text-gray-600 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-800' }}">
            Chờ xử lý ({{ $stats['pending_count'] }})
        </a>
        <a href="{{ route('admin.payouts.index', ['status' => 'approved']) }}"
           class="px-4 py-2 rounded-lg transition {{ $status === 'approved' ? 'bg-[#019D91] text-white font-semibold shadow-sm' : 'text-gray-600 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-800' }}">
            Đã thanh toán
        </a>
        <a href="{{ route('admin.payouts.index', ['status' => 'rejected']) }}"
           class="px-4 py-2 rounded-lg transition {{ $status === 'rejected' ? 'bg-[#019D91] text-white font-semibold shadow-sm' : 'text-gray-600 dark:text-neutral-300 hover:bg-gray-100 dark:hover:bg-neutral-800' }}">
            Bị từ chối
        </a>
    </div>

    <!-- Payout Requests Table -->
    <div class="glass-card overflow-hidden">
        @if($payouts->isEmpty())
            <div class="p-10 text-center text-sm text-gray-400 dark:text-neutral-500">
                Không tìm thấy yêu cầu chi trả nào phù hợp.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600 dark:text-neutral-300">
                    <thead class="bg-gray-50 dark:bg-neutral-800/40 text-xs uppercase text-gray-400 dark:text-neutral-500 font-semibold border-b border-gray-100 dark:border-neutral-800">
                        <tr>
                            <th class="px-6 py-4">Mã lệnh</th>
                            <th class="px-6 py-4">Giảng viên</th>
                            <th class="px-6 py-4">Số tiền rút</th>
                            <th class="px-6 py-4">Tài khoản ngân hàng</th>
                            <th class="px-6 py-4">Ngày yêu cầu</th>
                            <th class="px-6 py-4">Trạng thái</th>
                            <th class="px-6 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-neutral-800 font-medium">
                        @foreach($payouts as $payout)
                            <tr class="hover:bg-gray-50/70 dark:hover:bg-neutral-800/40 transition">
                                <td class="px-6 py-4 font-mono font-bold text-gray-900 dark:text-neutral-100">
                                    #PO-{{ str_pad($payout->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-neutral-100">{{ $payout->instructor->name ?? 'Giảng viên' }}</div>
                                    <div class="text-xs text-gray-400 dark:text-neutral-500">{{ $payout->instructor->email ?? '' }}</div>
                                </td>
                                <td class="px-6 py-4 font-extrabold text-gray-900 dark:text-neutral-100 text-base">
                                    {{ number_format($payout->amount, 0, ',', '.') }} ₫
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 dark:text-neutral-100">{{ $payout->bank_name }}</div>
                                    <div class="text-xs font-mono text-gray-600 dark:text-neutral-300">STK: <span class="font-bold text-gray-900 dark:text-neutral-100">{{ $payout->bank_account_number }}</span></div>
                                    <div class="text-xs text-gray-500 dark:text-neutral-400 uppercase">{{ $payout->bank_account_name }}</div>
                                </td>
                                <td class="px-6 py-4 text-xs text-gray-400 dark:text-neutral-500">
                                    {{ $payout->created_at->format('H:i d/m/Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if($payout->isPending())
                                        <span class="status-badge bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-300 border border-amber-200 dark:border-amber-500/30">
                                            Chờ duyệt
                                        </span>
                                    @elseif($payout->isApproved())
                                        <span class="status-badge bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-500/30">
                                            Đã chuyển khoản
                                        </span>
                                        <div class="text-[10px] text-gray-400 dark:text-neutral-500 mt-1">
                                            Duyệt bởi: {{ $payout->processor->name ?? 'Admin' }}
                                        </div>
                                    @else
                                        <span class="status-badge bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-500/30">
                                            Bị từ chối
                                        </span>
                                        <div class="text-[10px] text-rose-500 dark:text-rose-400 mt-1 truncate max-w-xs" title="{{ $payout->admin_note }}">
                                            {{ $payout->admin_note }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if($payout->isPending())
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button"
                                                    @click="openApprove({{ Js::from($payout) }})"
                                                    class="px-3 py-1.5 rounded-lg bg-emerald-600 text-white text-xs font-semibold hover:bg-emerald-700 transition shadow-sm">
                                                Xác nhận chuyển
                                            </button>
                                            <button type="button"
                                                    @click="openReject({{ Js::from($payout) }})"
                                                    class="px-3 py-1.5 rounded-lg border border-rose-300 dark:border-rose-500/40 text-rose-600 dark:text-rose-400 text-xs font-semibold hover:bg-rose-50 dark:hover:bg-rose-500/10 transition">
                                                Từ chối
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-400 dark:text-neutral-500 italic">Đã xử lý</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($payouts->hasPages())
                <div class="p-4 border-t border-gray-100 dark:border-neutral-800">
                    {{ $payouts->links() }}
                </div>
            @endif
        @endif
    </div>

    <!-- Modal Duyệt Chuyển Tiền -->
    <template x-teleport="body">
        <div x-show="approveModalOpen" x-cloak
             x-transition.opacity
             class="fixed inset-0 z-[9999] overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @keydown.escape.window="approveModalOpen = false">
            <div class="relative w-full max-w-md bg-white dark:bg-neutral-900 rounded-2xl shadow-xl p-6 my-auto"
                 @click.outside="approveModalOpen = false">
                <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100 mb-2">Xác nhận chuyển khoản</h3>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">
                    Vui lòng xác nhận bạn đã chuyển số tiền
                    <span class="font-bold text-emerald-600 dark:text-emerald-400" x-text="new Intl.NumberFormat('vi-VN').format(selectedPayout?.amount || 0) + ' ₫'"></span>
                    tới tài khoản của <strong x-text="selectedPayout?.instructor?.name"></strong>.
                </p>

                <form :action="approveUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 mb-1">Mã giao dịch / Ghi chú</label>
                        <input type="text" name="admin_note" placeholder="Ví dụ: Đã chuyển qua VCB - Mã GD: FT12345678"
                               class="w-full text-sm rounded-xl border-gray-300 dark:border-neutral-700 focus:ring-[#019D91] focus:border-[#019D91]">
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="approveModalOpen = false" class="px-4 py-2 rounded-lg border text-sm font-semibold hover:bg-gray-50 dark:hover:bg-neutral-800">
                            Hủy
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-emerald-600 text-white text-sm font-semibold hover:bg-emerald-700 shadow-sm">
                            Xác nhận đã chuyển
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>

    <!-- Modal Từ Chối Yêu Cầu -->
    <template x-teleport="body">
        <div x-show="rejectModalOpen" x-cloak
             x-transition.opacity
             class="fixed inset-0 z-[9999] overflow-y-auto flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @keydown.escape.window="rejectModalOpen = false">
            <div class="relative w-full max-w-md bg-white dark:bg-neutral-900 rounded-2xl shadow-xl p-6 my-auto"
                 @click.outside="rejectModalOpen = false">
                <h3 class="text-lg font-bold text-gray-900 dark:text-neutral-100 mb-2">Từ chối yêu cầu rút tiền</h3>
                <p class="text-sm text-gray-500 dark:text-neutral-400 mb-4">
                    Số tiền sẽ được hoàn trả lại ví của giảng viên. Vui lòng nêu rõ lý do từ chối.
                </p>

                <form :action="rejectUrl" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')
                    <div>
                        <label class="block text-xs font-semibold uppercase text-gray-500 dark:text-neutral-400 mb-1">Lý do từ chối *</label>
                        <textarea name="admin_note" required rows="3" placeholder="Ví dụ: Số tài khoản và tên chủ tài khoản không trùng khớp..."
                                  class="w-full text-sm rounded-xl border-gray-300 dark:border-neutral-700 focus:ring-rose-500 focus:border-rose-500"></textarea>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button type="button" @click="rejectModalOpen = false" class="px-4 py-2 rounded-lg border text-sm font-semibold hover:bg-gray-50 dark:hover:bg-neutral-800">
                            Hủy
                        </button>
                        <button type="submit" class="px-5 py-2 rounded-lg bg-rose-600 text-white text-sm font-semibold hover:bg-rose-700 shadow-sm">
                            Xác nhận từ chối
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </template>
</div>
@endsection
