{{-- Thông báo hệ thống mới nhất (do Admin đăng). Dùng được ở trang chủ và trang "Khóa học của tôi": @include('partials.latest-notifications') --}}
@php
    $latestNotifications = \App\Models\Notification::latest()->limit($limit ?? 3)->get();
    $typeLabels = ['system' => 'Hệ thống', 'course' => 'Khóa học', 'promotion' => 'Khuyến mãi'];
@endphp

@if($latestNotifications->isNotEmpty())
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-6">
    <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5">
        <h2 class="text-sm font-bold uppercase tracking-wider text-amber-800 mb-3">Thông báo mới</h2>
        <ul class="space-y-3">
            @foreach($latestNotifications as $notification)
                <li>
                    <p class="text-sm font-semibold text-gray-900">
                        {{ $notification->title }}
                        <span class="ml-2 text-xs font-medium text-amber-700">{{ $typeLabels[$notification->type] ?? $notification->type }} · {{ $notification->created_at?->diffForHumans() }}</span>
                    </p>
                    <p class="text-sm text-gray-600">{{ \Illuminate\Support\Str::limit($notification->content, 160) }}</p>
                </li>
            @endforeach
        </ul>
    </div>
</section>
@endif
