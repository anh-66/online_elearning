@php
    $statusStyles = [
        'draft' => ['Bản nháp', 'bg-slate-100 dark:bg-neutral-800 text-slate-700 dark:text-neutral-200 ring-slate-200 dark:ring-neutral-800'],
        'pending' => ['Chờ duyệt', 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 ring-amber-200 dark:ring-amber-500/30'],
        'published' => ['Đã xuất bản', 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 ring-emerald-200 dark:ring-emerald-500/30'],
        'rejected' => ['Bị từ chối', 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 ring-rose-200 dark:ring-rose-500/30'],
    ];
    [$statusLabel, $statusClass] = $statusStyles[$status] ?? [ucfirst($status), 'bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200 ring-gray-200 dark:ring-neutral-800'];
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">{{ $statusLabel }}</span>
