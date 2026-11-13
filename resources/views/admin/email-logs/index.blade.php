@extends('layouts.admin')

@section('title', 'Email đã gửi')
@section('page_title', 'Email Đã Gửi')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div>
        <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Nhật ký gửi email</h2>
        <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5">
            Mỗi lần web gửi mã xác thực email hoặc mã quên mật khẩu. "Thành công" nghĩa là Brevo đã nhận và gửi đi; mail vẫn có thể nằm trong mục Spam của người nhận.
        </p>
    </div>

    @if ($onlyLogging)
        <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-300" role="status">
            <iconify-icon icon="lucide:triangle-alert" class="text-lg shrink-0 mt-0.5"></iconify-icon>
            <p>
                Web đang dùng <code class="font-mono font-semibold">MAIL_MAILER={{ $mailer }}</code> nên mail chỉ được ghi vào log, chưa tới hộp thư thật.
                Đổi thành <code class="font-mono font-semibold">MAIL_MAILER=brevo</code> và thêm <code class="font-mono font-semibold">BREVO_API_KEY</code> vào file <code class="font-mono font-semibold">.env</code> để gửi thật.
            </p>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 pb-6 border-b border-gray-100 dark:border-neutral-800">
        <div class="flex items-center gap-3">
            <iconify-icon icon="lucide:mail" class="text-2xl text-gray-400 dark:text-neutral-500 shrink-0"></iconify-icon>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Gửi hôm nay</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-neutral-100">{{ number_format($stats['today']) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <iconify-icon icon="lucide:circle-check" class="text-2xl text-emerald-500 dark:text-emerald-400 shrink-0"></iconify-icon>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Thành công (7 ngày)</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['sent']) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <iconify-icon icon="lucide:circle-x" class="text-2xl text-red-500 dark:text-red-400 shrink-0"></iconify-icon>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Thất bại (7 ngày)</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['failed']) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <iconify-icon icon="lucide:gauge" class="text-2xl text-[#019D91] shrink-0"></iconify-icon>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Tỉ lệ thành công (7 ngày)</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-neutral-100">{{ $stats['success_rate'] === null ? '—' : $stats['success_rate'].'%' }}</p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <form method="GET" action="{{ route('admin.email-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pb-6 border-b border-gray-100 dark:border-neutral-800">
        <div class="sm:col-span-2 lg:col-span-2 relative">
            <iconify-icon icon="lucide:search" class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 dark:text-neutral-500 text-lg"></iconify-icon>
            <input type="text" name="keyword" value="{{ $keyword }}"
                   placeholder="Tìm theo email hoặc tên người nhận..."
                   class="w-full h-10 pl-10 pr-4 bg-gray-50 dark:bg-neutral-800/40 border border-gray-200 dark:border-neutral-800 rounded-lg text-sm text-gray-800 dark:text-neutral-200 focus:outline-none focus:ring-2 focus:ring-[#019D91]/20 focus:border-[#019D91] transition">
        </div>

        @include('admin.email-logs.partials.filter-select', [
            'name' => 'status',
            'value' => $status,
            'options' => ['all' => 'Tất cả trạng thái', 'sent' => 'Thành công', 'failed' => 'Thất bại'],
        ])

        <div class="flex items-center gap-2">
            <div class="flex-1 min-w-0">
                @include('admin.email-logs.partials.filter-select', [
                    'name' => 'purpose',
                    'value' => $purpose,
                    'options' => ['all' => 'Tất cả loại mail', 'verify_email' => 'Xác thực email', 'reset_password' => 'Quên mật khẩu'],
                ])
            </div>
            <button type="submit" class="px-4 h-10 bg-[#019D91] text-white text-sm font-medium rounded-lg hover:bg-[#008b80] transition shrink-0">
                Lọc
            </button>
        </div>
    </form>

    {{-- Table --}}
    <div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-neutral-800 text-xs font-semibold text-gray-400 dark:text-neutral-500 uppercase tracking-wider">
                        <th class="py-3.5 px-4 whitespace-nowrap">Thời gian (giờ VN)</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Người nhận</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Loại mail</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Trạng thái</th>
                        <th class="py-3.5 px-4 whitespace-nowrap">Gửi qua</th>
                        <th class="py-3.5 px-4">Lỗi / cách sửa</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-neutral-800 text-gray-700 dark:text-neutral-200">
                    @forelse ($logs as $log)
                        <tr class="align-top hover:bg-gray-50/60 dark:hover:bg-neutral-800/40 transition-colors">
                            <td class="py-3.5 px-4 whitespace-nowrap text-xs text-gray-500 dark:text-neutral-400">
                                <div class="font-medium text-gray-800 dark:text-neutral-200">{{ $log->created_at->timezone($timezone)->format('H:i:s') }}</div>
                                <div>{{ $log->created_at->timezone($timezone)->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-3.5 px-4 min-w-48">
                                @if ($log->user)
                                    <a href="{{ route('admin.users.edit', $log->user) }}" class="font-medium text-gray-900 dark:text-neutral-100 hover:text-[#019D91] transition">{{ $log->user->name }}</a>
                                @else
                                    <span class="font-medium text-gray-400 dark:text-neutral-500">Tài khoản đã xoá</span>
                                @endif
                                <div class="text-xs text-gray-400 dark:text-neutral-500 break-all">{{ $log->to_email }}</div>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap text-gray-600 dark:text-neutral-300">{{ $log->purposeLabel() }}</td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                @if ($log->isSent())
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800">
                                        <iconify-icon icon="lucide:check" class="text-sm"></iconify-icon> Thành công
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border bg-red-50 text-red-700 border-red-200 dark:bg-red-950/40 dark:text-red-400 dark:border-red-800">
                                        <iconify-icon icon="lucide:x" class="text-sm"></iconify-icon> Thất bại
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span @class([
                                    'text-xs font-medium',
                                    'text-amber-700 dark:text-amber-400' => $log->onlyLogged(),
                                    'text-gray-600 dark:text-neutral-300' => ! $log->onlyLogged(),
                                ])>{{ $log->mailerLabel() }}</span>
                            </td>
                            <td class="py-3.5 px-4 min-w-64 max-w-md">
                                @if ($log->isSent())
                                    <span class="text-gray-300 dark:text-neutral-600">—</span>
                                @else
                                    <p class="text-xs text-red-600 dark:text-red-400 break-words">{{ $log->error ?? 'Không rõ lỗi.' }}</p>
                                    @if ($hint = $log->errorHint())
                                        <p class="mt-1.5 flex items-start gap-1.5 text-xs text-gray-600 dark:text-neutral-300">
                                            <iconify-icon icon="lucide:lightbulb" class="text-sm text-amber-500 shrink-0 mt-px"></iconify-icon>
                                            <span>{{ $hint }}</span>
                                        </p>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-gray-400 dark:text-neutral-500">
                                <iconify-icon icon="lucide:mail-x" class="text-4xl text-gray-300 dark:text-neutral-600 mb-2"></iconify-icon>
                                <p class="text-sm">Chưa có mail nào khớp bộ lọc.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($logs->hasPages())
            <div class="pt-6 border-t border-gray-100 dark:border-neutral-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                {{ $logs->links() }}
            </div>
        @endif

        <p class="pt-4 text-xs text-gray-400 dark:text-neutral-500">Nhật ký được giữ {{ \App\Models\EmailLog::KEEP_DAYS }} ngày. Không lưu mã OTP.</p>
    </div>
</div>
@endsection
