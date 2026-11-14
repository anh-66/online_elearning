@extends('layouts.admin')

@section('title', 'Email đã gửi')
@section('page_title', 'Email Đã Gửi')

@section('content')
<div class="space-y-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
        <div>
            <h2 class="text-xl font-bold text-gray-900 dark:text-neutral-100 uppercase tracking-wide">Nhật ký gửi email</h2>
            <p class="text-sm text-gray-500 dark:text-neutral-400 mt-0.5 max-w-3xl">
                Mỗi lần web gửi mã xác thực email hoặc mã quên mật khẩu. Trạng thái lấy từ Brevo:
                "Chờ xác nhận" là Brevo mới nhận, "Đã tới hộp thư" là đã giao (có thể nằm trong Spam), "Bị từ chối" là Brevo không giao được.
            </p>
        </div>

        @if ($trackerEnabled)
            <form method="POST" action="{{ route('admin.email-logs.sync') }}" class="shrink-0">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-4 h-10 rounded-lg border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-900 text-sm font-medium text-gray-700 dark:text-neutral-200 hover:border-[#019D91] hover:text-[#019D91] transition">
                    <iconify-icon icon="lucide:refresh-cw" class="text-base"></iconify-icon>
                    Cập nhật trạng thái
                    @if ($awaiting > 0)
                        <span class="rounded-full bg-sky-100 px-1.5 text-xs font-semibold text-sky-700 dark:bg-sky-950 dark:text-sky-300">{{ $awaiting }}</span>
                    @endif
                </button>
            </form>
        @endif
    </div>

    @if ($onlyLogging)
        <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-300" role="status">
            <iconify-icon icon="lucide:triangle-alert" class="text-lg shrink-0 mt-0.5"></iconify-icon>
            <p>
                Web đang dùng <code class="font-mono font-semibold">MAIL_MAILER={{ $mailer }}</code> nên mail chỉ được ghi vào log, chưa tới hộp thư thật.
                Đổi thành <code class="font-mono font-semibold">MAIL_MAILER=brevo</code> và thêm <code class="font-mono font-semibold">BREVO_API_KEY</code> trong Render &gt; Environment để gửi thật.
            </p>
        </div>
    @elseif ($mailer === 'brevo' && ! $trackerEnabled)
        <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-300" role="status">
            <iconify-icon icon="lucide:triangle-alert" class="text-lg shrink-0 mt-0.5"></iconify-icon>
            <p>Chưa có <code class="font-mono font-semibold">BREVO_API_KEY</code> nên không gửi được mail và không đọc được trạng thái từ Brevo.</p>
        </div>
    @endif

    @if ($trackerProblem)
        <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-950/40 dark:text-amber-300" role="status">
            <iconify-icon icon="lucide:cloud-off" class="text-lg shrink-0 mt-0.5"></iconify-icon>
            <p>Trạng thái có thể chưa mới nhất: {{ $trackerProblem }}</p>
        </div>
    @endif

    {{-- Stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-6 pb-6 border-b border-gray-100 dark:border-neutral-800">
        <div class="flex items-center gap-3">
            <iconify-icon icon="lucide:mail" class="text-2xl text-gray-400 dark:text-neutral-500 shrink-0"></iconify-icon>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Gửi hôm nay</p>
                <p class="text-2xl font-bold text-gray-900 dark:text-neutral-100">{{ number_format($stats['today']) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <iconify-icon icon="lucide:mail-check" class="text-2xl text-emerald-500 dark:text-emerald-400 shrink-0"></iconify-icon>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Đã tới hộp thư (7 ngày)</p>
                <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ number_format($stats['delivered']) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <iconify-icon icon="lucide:hourglass" class="text-2xl text-sky-500 dark:text-sky-400 shrink-0"></iconify-icon>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Chờ xác nhận (7 ngày)</p>
                <p class="text-2xl font-bold text-sky-600 dark:text-sky-400">{{ number_format($stats['awaiting']) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <iconify-icon icon="lucide:circle-x" class="text-2xl text-red-500 dark:text-red-400 shrink-0"></iconify-icon>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Lỗi / bị từ chối (7 ngày)</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ number_format($stats['problem']) }}</p>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <iconify-icon icon="lucide:gauge" class="text-2xl text-[#019D91] shrink-0"></iconify-icon>
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-neutral-400">Tỉ lệ tới hộp thư (7 ngày)</p>
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
            'options' => [
                'all' => 'Tất cả trạng thái',
                'delivered' => 'Đã tới hộp thư',
                'sent' => 'Chờ xác nhận / đã gửi đi',
                'deferred' => 'Đang thử lại',
                'problem' => 'Lỗi + bị từ chối',
                'rejected' => 'Bị từ chối',
                'failed' => 'Thất bại',
            ],
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
                        <th class="py-3.5 px-4">Chi tiết / cách sửa</th>
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
                                @php
                                    $tone = $log->statusTone();
                                    $icon = match ($tone) {
                                        'green' => 'lucide:check',
                                        'red' => 'lucide:x',
                                        'amber' => 'lucide:rotate-cw',
                                        'sky' => 'lucide:hourglass',
                                        default => 'lucide:file-text',
                                    };
                                @endphp
                                <span @class([
                                    'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold border',
                                    'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-400 dark:border-emerald-800' => $tone === 'green',
                                    'bg-red-50 text-red-700 border-red-200 dark:bg-red-950/40 dark:text-red-400 dark:border-red-800' => $tone === 'red',
                                    'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-950/40 dark:text-amber-400 dark:border-amber-800' => $tone === 'amber',
                                    'bg-sky-50 text-sky-700 border-sky-200 dark:bg-sky-950/40 dark:text-sky-400 dark:border-sky-800' => $tone === 'sky',
                                    'bg-gray-50 text-gray-600 border-gray-200 dark:bg-neutral-800/60 dark:text-neutral-300 dark:border-neutral-700' => $tone === 'gray',
                                ])>
                                    <iconify-icon icon="{{ $icon }}" class="text-sm"></iconify-icon> {{ $log->statusLabel() }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span @class([
                                    'text-xs font-medium',
                                    'text-amber-700 dark:text-amber-400' => $log->onlyLogged(),
                                    'text-gray-600 dark:text-neutral-300' => ! $log->onlyLogged(),
                                ])>{{ $log->mailerLabel() }}</span>
                            </td>
                            <td class="py-3.5 px-4 min-w-64 max-w-md text-xs">
                                @if ($log->isDelivered())
                                    <p class="text-gray-600 dark:text-neutral-300">Tới hộp thư lúc {{ ($log->delivered_at ?? $log->updated_at)->timezone($timezone)->format('H:i:s d/m') }}</p>
                                    @if ($log->opened_at)
                                        <p class="mt-0.5 text-gray-400 dark:text-neutral-500">Người nhận mở lúc {{ $log->opened_at->timezone($timezone)->format('H:i:s d/m') }}</p>
                                    @endif
                                    @if ($log->error)
                                        <p class="mt-0.5 text-amber-700 dark:text-amber-400">{{ $log->error }}</p>
                                    @endif
                                @elseif ($log->isProblem() || $log->status === \App\Models\EmailLog::STATUS_DEFERRED)
                                    <p @class([
                                        'break-words',
                                        'text-red-600 dark:text-red-400' => $log->isProblem(),
                                        'text-amber-700 dark:text-amber-400' => ! $log->isProblem(),
                                    ])>{{ $log->error ?? 'Không rõ lỗi.' }}</p>
                                    @if ($hint = $log->errorHint())
                                        <p class="mt-1.5 flex items-start gap-1.5 text-gray-600 dark:text-neutral-300">
                                            <iconify-icon icon="lucide:lightbulb" class="text-sm text-amber-500 shrink-0 mt-px"></iconify-icon>
                                            <span>{{ $hint }}</span>
                                        </p>
                                    @endif
                                @elseif ($log->isAwaitingDelivery())
                                    <p class="text-gray-500 dark:text-neutral-400">
                                        @if ($log->created_at->lt(now()->subDays(\App\Models\EmailLog::TRACK_DAYS)))
                                            Brevo không báo kết quả cho mail này.
                                        @else
                                            Brevo đã nhận, đang chờ báo đã giao.
                                            @if ($log->checked_at)
                                                <span class="text-gray-400 dark:text-neutral-500">Hỏi lần cuối {{ $log->checked_at->timezone($timezone)->format('H:i:s') }}.</span>
                                            @endif
                                        @endif
                                    </p>
                                @elseif ($log->onlyLogged())
                                    <p class="text-gray-400 dark:text-neutral-500">Mã nằm trong storage/logs/laravel.log.</p>
                                @else
                                    <span class="text-gray-300 dark:text-neutral-600">—</span>
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

        <p class="pt-4 text-xs text-gray-400 dark:text-neutral-500">
            Nhật ký được giữ {{ \App\Models\EmailLog::KEEP_DAYS }} ngày. Không lưu mã OTP.
            Trạng thái mail gửi trong {{ \App\Models\EmailLog::TRACK_DAYS }} ngày gần nhất tự cập nhật khi mở trang này
            (hoặc chạy <code class="font-mono">php artisan email-logs:sync</code>).
        </p>
    </div>
</div>
@endsection
