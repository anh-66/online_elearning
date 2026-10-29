@php
    $statusStyles = [
        'draft' => ['Bản nháp', 'bg-slate-100 text-slate-700 ring-slate-200'],
        'pending' => ['Chờ duyệt', 'bg-amber-50 text-amber-700 ring-amber-200'],
        'published' => ['Đã xuất bản', 'bg-emerald-50 text-emerald-700 ring-emerald-200'],
        'rejected' => ['Bị từ chối', 'bg-rose-50 text-rose-700 ring-rose-200'],
    ];
    [$statusLabel, $statusClass] = $statusStyles[$status] ?? [ucfirst($status), 'bg-gray-100 text-gray-700 ring-gray-200'];
@endphp
<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold ring-1 ring-inset {{ $statusClass }}">{{ $statusLabel }}</span>
