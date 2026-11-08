<div class="p-4 sm:p-5 bg-gray-50 dark:bg-[#161b22] rounded-xl border border-gray-100 dark:border-neutral-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">Xóa vĩnh viễn tài khoản</h4>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Khi xóa tài khoản, tất cả tiến trình học tập và dữ liệu liên quan sẽ bị xóa vĩnh viễn.</p>
    </div>
    <button type="button"
            x-data=""
            x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
            class="px-5 py-2.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm shrink-0">
        Xóa tài khoản
    </button>

    <template x-teleport="body">
        <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
            <form method="post" action="{{ route('profile.destroy') }}" class="p-6 sm:p-8 space-y-4">
                @csrf
                @method('delete')

                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Bạn có chắc chắn muốn xóa tài khoản?
                </h2>

                <p class="text-xs text-gray-500 dark:text-gray-400 leading-relaxed">
                    Hành động này không thể hoàn tác. Vui lòng nhập mật khẩu của bạn để xác nhận thao tác xóa tài khoản này.
                </p>

                <div class="space-y-1">
                    <label for="password" class="text-xs font-medium text-gray-700 dark:text-gray-300">Mật khẩu</label>
                    <input id="password" name="password" type="password" placeholder="Nhập mật khẩu của bạn"
                           class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg text-sm text-gray-900 dark:text-white transition-all focus:bg-white dark:focus:bg-[#0d1117] focus:border-black dark:focus:border-white focus:outline-none" />
                    <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-1" />
                </div>

                <div class="flex justify-end gap-3 pt-3">
                    <button type="button" x-on:click="$dispatch('close')" class="px-5 py-2 text-xs font-medium text-gray-500 hover:text-gray-900 dark:hover:text-white transition-colors">
                        Hủy bỏ
                    </button>
                    <button type="submit" class="px-5 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm">
                        Xác nhận xóa
                    </button>
                </div>
            </form>
        </x-modal>
    </template>
</div>
