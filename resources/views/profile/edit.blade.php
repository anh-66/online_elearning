<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="text-sm font-semibold uppercase tracking-[0.16em] text-indigo-600">Tài khoản</p>
            <h1 class="mt-1 text-2xl font-semibold tracking-tight text-slate-900">{{ __('Hồ sơ cá nhân') }}</h1>
        </div>
    </x-slot>

    <div class="bg-slate-50 py-8 sm:py-12">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-700 via-indigo-600 to-violet-600 p-6 text-white shadow-lg shadow-indigo-900/10 sm:p-8">
                    <div class="absolute -right-12 -top-24 h-64 w-64 rounded-full border border-white/10"></div>
                    <div class="absolute -bottom-32 right-32 h-64 w-64 rounded-full bg-cyan-300/10 blur-3xl"></div>
                    <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-sm font-medium text-indigo-100">Xin chào, {{ $user->name }}</p>
                            <h2 class="mt-2 text-2xl font-semibold tracking-tight sm:text-3xl">Quản lý hồ sơ của bạn</h2>
                            <p class="mt-2 max-w-xl text-sm leading-6 text-indigo-100/90">Cập nhật thông tin, ảnh đại diện và bảo mật tài khoản tại một nơi.</p>
                        </div>
                        <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl border border-white/20 bg-white/15 text-2xl font-semibold text-white shadow-inner">
                            @if ($user->avatar)
                                <img src="{{ asset('storage/'.$user->avatar) }}" alt="Ảnh đại diện của {{ $user->name }}" class="h-full w-full object-cover">
                            @else
                                {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                            @endif
                        </div>
                    </div>
            </section>

            <div class="grid gap-6 lg:grid-cols-[minmax(0,1.25fr)_minmax(18rem,0.75fr)]">
                    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-8">
                        @include('profile.partials.update-profile-information-form')
                    </section>

                    <section class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm sm:p-8">
                        @include('profile.partials.update-password-form')
                    </section>
            </div>

            <section class="rounded-2xl border border-rose-100 bg-white p-5 shadow-sm sm:p-8">
                    @include('profile.partials.delete-user-form')
            </section>
        </div>
    </div>
</x-app-layout>
