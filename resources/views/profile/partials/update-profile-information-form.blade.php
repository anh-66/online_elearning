<form method="post" action="{{ route('profile.update') }}" class="space-y-6" enctype="multipart/form-data"
      x-data="{ avatarPickerOpen: false, avatarMode: 'options', avatarGenerated: @js(old('avatar_generated')), avatarPreview: @js($user->avatar_url) }"
      @message.window="if ($event.origin === window.location.origin && $event.data?.type === 'avatar-ready' && typeof $event.data.dataUrl === 'string') { avatarGenerated = $event.data.dataUrl; avatarPreview = avatarGenerated; avatarPickerOpen = false; avatarMode = 'options'; $nextTick(() => $el.requestSubmit()); }">
    @csrf
    @method('patch')

    <!-- Avatar Upload & Camera Trigger -->
    <div class="flex items-center gap-4">
        <div class="relative">
            <div class="w-20 h-20 bg-black text-white dark:bg-black dark:text-white rounded-full flex items-center justify-center overflow-hidden font-bold text-2xl select-none ring-2 ring-gray-100 dark:ring-neutral-800">
                <template x-if="avatarPreview">
                    <img :src="avatarPreview" alt="Ảnh đại diện mới" class="h-full w-full object-cover" x-on:error="avatarPreview = null">
                </template>
                <template x-if="!avatarPreview">
                    <span>{{ $user->initialLetter() }}</span>
                </template>
            </div>
            <button type="button" @click="avatarPickerOpen = true; avatarMode = 'options'"
                    class="absolute bottom-0 right-0 p-1.5 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-full shadow-sm hover:bg-gray-50 dark:hover:bg-neutral-700 transition"
                    title="Đổi ảnh đại diện">
                <iconify-icon icon="lucide:camera" class="text-xs text-gray-700 dark:text-gray-300 block"></iconify-icon>
            </button>
        </div>
        <div>
            <h3 class="font-medium text-gray-900 dark:text-white">Ảnh đại diện</h3>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">PNG hoặc JPG, tối đa 2MB.</p>
            <span x-show="avatarGenerated" x-cloak class="text-xs font-medium text-emerald-600 dark:text-emerald-400 mt-1 block">Đã chọn avatar thiết kế</span>
        </div>
        <input id="avatar" x-ref="avatarInput" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" class="sr-only"
               @change="avatarGenerated = ''; if ($event.target.files[0]) { const reader = new FileReader(); reader.onload = () => avatarPreview = reader.result; reader.readAsDataURL($event.target.files[0]); }">
        <input type="hidden" name="avatar_generated" x-model="avatarGenerated">
    </div>

    <x-input-error class="mt-2" :messages="$errors->get('avatar')" />
    <x-input-error class="mt-2" :messages="$errors->get('avatar_generated')" />

    <!-- Avatar Picker Modal -->
    <template x-teleport="body">
        <div x-show="avatarPickerOpen" x-cloak x-transition.opacity class="fixed inset-0 z-[9999] flex items-start justify-center overflow-y-auto bg-black/65 p-3 backdrop-blur-[2px] sm:p-5" @keydown.escape.window="avatarPickerOpen = false">
            <div class="my-auto flex max-h-[calc(100vh-2rem)] w-full max-w-5xl flex-col overflow-hidden rounded-xl bg-white shadow-2xl dark:bg-neutral-900 border border-gray-100 dark:border-neutral-800" @click.stop>
                <div class="flex shrink-0 items-center justify-between border-b border-gray-100 px-5 py-4 dark:border-neutral-800">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Tùy chỉnh ảnh đại diện</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Chọn ảnh từ thiết bị hoặc thiết kế avatar riêng.</p>
                    </div>
                    <button type="button" @click="avatarPickerOpen = false" class="rounded-full p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-900 dark:hover:bg-neutral-800 dark:hover:text-white" aria-label="Đóng">
                        <iconify-icon icon="lucide:x" class="text-lg"></iconify-icon>
                    </button>
                </div>

                <div x-show="avatarMode === 'options'" class="grid gap-4 p-5 sm:grid-cols-2 sm:p-8">
                    <button type="button" @click="$refs.avatarInput.value = ''; avatarGenerated = ''; $refs.avatarInput.click(); avatarPickerOpen = false"
                            class="group rounded-2xl border border-gray-200 p-6 text-left transition hover:border-black hover:bg-gray-50 dark:border-neutral-700 dark:hover:border-white dark:hover:bg-neutral-800/50">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-900 dark:bg-neutral-800 dark:text-white">
                            <iconify-icon icon="lucide:upload" class="text-xl"></iconify-icon>
                        </span>
                        <span class="mt-4 block text-base font-semibold text-gray-900 dark:text-white">Tải ảnh lên</span>
                        <span class="mt-1 block text-sm text-gray-500 dark:text-gray-400">Chọn JPG, PNG hoặc WebP từ thiết bị.</span>
                    </button>

                    <button type="button" @click="$refs.avatarInput.value = ''; avatarGenerated = ''; avatarMode = 'designer'"
                            class="group rounded-2xl border border-gray-200 p-6 text-left transition hover:border-black hover:bg-gray-50 dark:border-neutral-700 dark:hover:border-white dark:hover:bg-neutral-800/50">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-100 text-gray-900 dark:bg-neutral-800 dark:text-white">
                            <iconify-icon icon="lucide:palette" class="text-xl"></iconify-icon>
                        </span>
                        <span class="mt-4 block text-base font-semibold text-gray-900 dark:text-white">Thiết kế avatar</span>
                        <span class="mt-1 block text-sm text-gray-500 dark:text-gray-400">Kết hợp kiểu tóc, khuôn mặt, quần áo và màu nền.</span>
                    </button>
                </div>

                <div x-show="avatarMode === 'designer'" x-cloak class="min-h-0 overflow-y-auto p-3 sm:p-5">
                    <button type="button" @click="avatarMode = 'options'" class="mb-3 inline-flex items-center gap-1 text-sm font-semibold text-gray-900 hover:underline dark:text-white">← Quay lại lựa chọn</button>
                    <iframe src="{{ route('avatar-generator') }}" title="Thiết kế avatar" class="block h-[460px] w-full rounded-lg border border-gray-200 bg-white dark:border-neutral-700 dark:bg-neutral-900" loading="lazy"></iframe>
                </div>
            </div>
        </div>
    </template>

    <!-- Inputs: Name and Email Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900 dark:text-gray-200">Họ và tên</label>
            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg text-sm text-gray-900 dark:text-white transition-all focus:bg-white dark:focus:bg-[#0d1117] focus:border-black dark:focus:border-white focus:outline-none">
            <x-input-error class="mt-1" :messages="$errors->get('name')" />
        </div>

        <div class="space-y-2">
            <label class="text-sm font-medium text-gray-900 dark:text-gray-200">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                   class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg text-sm text-gray-900 dark:text-white transition-all focus:bg-white dark:focus:bg-[#0d1117] focus:border-black dark:focus:border-white focus:outline-none">
            <x-input-error class="mt-1" :messages="$errors->get('email')" />
        </div>
    </div>

    <!-- Bio Textarea -->
    <div class="space-y-2">
        <label class="text-sm font-medium text-gray-900 dark:text-gray-200">Giới thiệu ngắn</label>
        <textarea name="bio" rows="4"
                  class="w-full px-4 py-2.5 bg-gray-50 dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg text-sm text-gray-900 dark:text-white transition-all focus:bg-white dark:focus:bg-[#0d1117] focus:border-black dark:focus:border-white focus:outline-none resize-none"
                  placeholder="Chia sẻ đôi nét về bạn...">{{ old('bio', $user->bio) }}</textarea>
        <x-input-error class="mt-1" :messages="$errors->get('bio')" />
    </div>

    <!-- Action Buttons -->
    <div class="pt-6 border-t border-gray-100 dark:border-neutral-800 flex items-center justify-end gap-3">
        @if (session('status') === 'profile-updated')
            <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-medium text-emerald-600 dark:text-emerald-400">
                Đã lưu thay đổi thành công.
            </span>
        @endif
        <button type="submit" class="px-8 py-2.5 text-sm font-medium bg-black text-white dark:bg-white dark:text-black rounded-lg hover:bg-gray-800 dark:hover:bg-gray-200 transition-colors shadow-sm">
            Lưu thay đổi
        </button>
    </div>
</form>
