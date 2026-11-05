<section id="password" class="scroll-mt-24">
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Thay đổi mật khẩu</h2>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Sử dụng mật khẩu mạnh tối thiểu 8 ký tự bao gồm chữ cái và số.</p>
    </div>

    <form method="post" action="{{ route('password.update') }}" class="space-y-4 max-w-md">
        @csrf
        @method('put')

        <div class="space-y-2">
            <label for="update_password_current_password" class="text-sm font-medium text-gray-900 dark:text-gray-200">Mật khẩu hiện tại</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password"
                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg text-sm text-gray-900 dark:text-white transition-all focus:bg-white dark:focus:bg-[#0d1117] focus:border-black dark:focus:border-white focus:outline-none">
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
        </div>

        <div class="space-y-2">
            <label for="update_password_password" class="text-sm font-medium text-gray-900 dark:text-gray-200">Mật khẩu mới</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password"
                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg text-sm text-gray-900 dark:text-white transition-all focus:bg-white dark:focus:bg-[#0d1117] focus:border-black dark:focus:border-white focus:outline-none">
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
        </div>

        <div class="space-y-2">
            <label for="update_password_password_confirmation" class="text-sm font-medium text-gray-900 dark:text-gray-200">Xác nhận mật khẩu mới</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password"
                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg text-sm text-gray-900 dark:text-white transition-all focus:bg-white dark:focus:bg-[#0d1117] focus:border-black dark:focus:border-white focus:outline-none">
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
        </div>

        <div class="pt-3 flex items-center gap-3">
            <button type="submit" class="px-6 py-2.5 text-sm font-medium bg-black text-white dark:bg-white dark:text-black rounded-lg hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors shadow-sm">
                Cập nhật mật khẩu
            </button>

            @if (session('status') === 'password-updated')
                <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
                    Đã cập nhật mật khẩu.
                </span>
            @endif
        </div>
    </form>
</section>
