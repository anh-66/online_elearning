{{-- Thông báo hệ thống mới nhất (do Admin đăng). Dùng được ở trang chủ và trang "Khóa học của tôi": @include('partials.latest-notifications') --}}
@php
    $notifications = $latestNotifications ?? \App\Models\Notification::latest()->limit($limit ?? 3)->get();
    $typeLabels = ['system' => 'Hệ thống', 'course' => 'Khóa học', 'promotion' => 'Khuyến mãi'];
@endphp

@if(isset($notifications) && $notifications->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 my-6">
    <div class="rounded-2xl border border-amber-200 bg-amber-50/80 p-5 shadow-sm">
        <div class="flex items-center gap-2 mb-3">
            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
            <h2 class="text-xs font-bold uppercase tracking-wider text-amber-900">Thông báo từ ban quản trị</h2>
        </div>
        <ul class="space-y-3">
            @foreach($notifications as $notification)
                <li class="border-b border-amber-100 last:border-b-0 pb-2.5 last:pb-0">
                    <p class="text-sm font-semibold text-gray-900 flex items-center justify-between flex-wrap gap-2">
                        <span>{{ $notification->title }}</span>
                        <span class="text-xs font-medium text-amber-700 bg-amber-100/70 px-2 py-0.5 rounded-full">
                            {{ $typeLabels[$notification->type] ?? $notification->type }} · {{ $notification->created_at?->diffForHumans() }}
                        </span>
                    </p>
                    <p class="text-xs text-gray-600 mt-1 leading-relaxed">{{ \Illuminate\Support\Str::limit($notification->content, 200) }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endif
