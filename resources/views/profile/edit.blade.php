@extends('layouts.public')

@section('title', 'Hồ sơ cá nhân')

@section('content')
<div class="bg-neutral-50 py-8 sm:py-12 dark:bg-neutral-950">
    <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
        <section class="relative overflow-hidden rounded-2xl border border-brand-100 bg-gradient-to-br from-brand-50 via-white to-brand-100/60 dark:from-neutral-900 dark:via-neutral-950 dark:to-brand-950/40 p-6 text-neutral-900 dark:border-neutral-800 dark:text-white sm:p-8">
            <div class="absolute -right-16 -top-24 h-64 w-64 rounded-full bg-brand-300/30 blur-3xl dark:bg-brand-700/20"></div>
            <div class="absolute -bottom-32 right-40 h-64 w-64 rounded-full bg-brand-200/40 blur-3xl dark:bg-brand-500/10"></div>
            <div class="relative flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.16em] text-brand-700 dark:text-brand-400">Tài khoản</p>
                    <h1 class="mt-2 text-2xl font-bold tracking-tight sm:text-3xl">Xin chào, {{ $user->name }}</h1>
                    <p class="mt-2 max-w-xl text-sm leading-6 text-neutral-600 dark:text-neutral-300">Cập nhật thông tin, ảnh đại diện và bảo mật tài khoản tại một nơi.</p>
                </div>
                <div class="flex h-20 w-20 shrink-0 items-center justify-center overflow-hidden rounded-2xl bg-brand-700 text-2xl font-semibold text-white ring-4 ring-white dark:ring-neutral-800">
                    @if ($user->avatar)
                        <img src="{{ asset('storage/'.$user->avatar) }}" alt="Ảnh đại diện của {{ $user->name }}" class="h-full w-full object-cover">
                    @else
                        {{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}
                    @endif
                </div>
            </div>
        </section>

        <div class="grid gap-6 lg:grid-cols-[minmax(0,1.25fr)_minmax(18rem,0.75fr)]">
            <section class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900 sm:p-8">
                @include('profile.partials.update-profile-information-form')
            </section>

            <section class="rounded-2xl border border-neutral-200 bg-white p-5 shadow-sm dark:border-neutral-800 dark:bg-neutral-900 sm:p-8">
                @include('profile.partials.update-password-form')
            </section>
        </div>

        <section class="rounded-2xl border border-red-100 bg-white p-5 shadow-sm dark:border-red-500/20 dark:bg-neutral-900 sm:p-8">
            @include('profile.partials.delete-user-form')
        </section>
    </div>
</div>
@endsection
