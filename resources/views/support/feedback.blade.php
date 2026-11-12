@extends('layouts.public')

@section('title', 'Trung tâm Phản hồi & Báo cáo sự cố - EduLearn')

@push('scripts')
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
@endpush

@section('content')
<div class="min-h-[calc(100vh-140px)] bg-white text-gray-900 dark:bg-[#0d1117] dark:text-gray-100 flex flex-col items-center justify-start py-12 sm:py-16 lg:py-20 px-4 sm:px-6 lg:px-8 border-b border-gray-100 dark:border-neutral-800"
     x-data="{
         fileName: null,
         fileSize: null,
         submitted: false,
         handleFile(event) {
             const file = event.target.files[0];
             if (file) {
                 this.fileName = file.name;
                 this.fileSize = Math.round(file.size / 1024) + ' KB';
             } else {
                 this.fileName = null;
                 this.fileSize = null;
             }
         },
         removeFile() {
             this.fileName = null;
             this.fileSize = null;
             const input = document.getElementById('file-upload');
             if (input) input.value = '';
         }
     }">
    <div class="w-full max-w-2xl">
        <!-- Breadcrumb -->
        <nav class="sticky top-[72px] z-30 mb-6 flex items-center gap-2 bg-white/95 py-4 text-base text-gray-500 backdrop-blur-md dark:bg-[#0d1117]/95 dark:text-gray-400 sm:top-[80px]">
            <a href="{{ route('support.index') }}" class="hover:text-black dark:hover:text-white transition">Trung tâm Hỗ trợ</a>
            <span>/</span>
            <span class="text-gray-900 dark:text-gray-200 font-medium">Báo cáo sự cố & Góp ý</span>
        </nav>

        <!-- Header Section -->
        <header class="mb-10 text-center">
            <h1 class="text-3xl font-semibold text-gray-900 dark:text-white tracking-tight mb-3">Trung tâm Phản hồi</h1>
            <p class="text-gray-500 dark:text-gray-400 text-sm max-w-md mx-auto leading-relaxed">Chúng tôi luôn lắng nghe ý kiến của bạn để cải thiện dịch vụ tốt hơn mỗi ngày.</p>
        </header>

        <!-- Flash Success Notification -->
        @if (session('success'))
            <div class="mb-8 p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 flex items-start gap-3 text-emerald-800 dark:text-emerald-200"
                 x-data="{ show: true }" x-show="show">
                <iconify-icon icon="lucide:check-circle-2" class="text-xl text-emerald-600 dark:text-emerald-400 mt-0.5 shrink-0"></iconify-icon>
                <div class="flex-1 text-sm leading-relaxed">
                    {{ session('success') }}
                </div>
                <button type="button" @click="show = false" class="text-emerald-600 hover:text-emerald-800 dark:text-emerald-400 dark:hover:text-emerald-200">
                    <iconify-icon icon="lucide:x" class="text-base"></iconify-icon>
                </button>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-8 p-4 rounded-xl bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200">
                <p class="text-sm font-semibold mb-1">Vui lòng kiểm tra lại thông tin:</p>
                <ul class="list-disc list-inside text-xs space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Section -->
        <form method="POST" action="{{ route('support.feedback.submit') }}" enctype="multipart/form-data" class="space-y-8" @submit="submitted = true">
            @csrf

            @auth
                <div class="p-3.5 bg-gray-50 dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg flex items-center justify-between text-xs">
                    <div class="flex items-center gap-2.5 text-gray-600 dark:text-gray-300 min-w-0">
                        <iconify-icon icon="lucide:user-check" class="text-lg text-emerald-600 dark:text-emerald-400 shrink-0"></iconify-icon>
                        <span class="truncate">Gửi với tư cách: <strong class="text-gray-900 dark:text-white">{{ Auth::user()->name }}</strong> ({{ Auth::user()->email }})</span>
                    </div>
                    <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 shrink-0">Đã đăng nhập</span>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 bg-gray-50 dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg">
                    <div>
                        <label for="sender_name" class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Họ và tên của bạn</label>
                        <input type="text" id="sender_name" name="sender_name" value="{{ old('sender_name') }}" placeholder="Ví dụ: Nguyễn Văn A"
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-[#0d1117] border border-gray-200 dark:border-[#30363d] rounded-md text-sm text-gray-900 dark:text-white outline-none focus:border-black dark:focus:border-white">
                    </div>
                    <div>
                        <label for="sender_email" class="block text-xs font-medium text-gray-600 dark:text-gray-300 mb-1">Email nhận phản hồi <span class="text-red-500">*</span></label>
                        <input type="email" id="sender_email" name="sender_email" value="{{ old('sender_email') }}" placeholder="email@example.com" required
                               class="w-full px-3.5 py-2.5 bg-white dark:bg-[#0d1117] border border-gray-200 dark:border-[#30363d] rounded-md text-sm text-gray-900 dark:text-white outline-none focus:border-black dark:focus:border-white">
                    </div>
                </div>
            @endauth

            <!-- Issue Title -->
            <div class="group">
                <label for="title-input" class="block text-[13px] font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">Tiêu đề vấn đề <span class="text-red-500">*</span></label>
                <input type="text" id="title-input" name="title" value="{{ old('title') }}" placeholder="Tóm tắt ngắn gọn vấn đề của bạn..."
                       class="w-full px-4 py-3 bg-white dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg outline-none transition-all duration-200 focus:border-black dark:focus:border-white focus:ring-0 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Issue Category (Combobox Animate-UI) -->
                <div>
                    <label class="block text-[13px] font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">Loại phản hồi <span class="text-red-500">*</span></label>
                    @php
                        $feedbackCategories = [
                            'bug' => 'Lỗi kỹ thuật',
                            'feature' => 'Góp ý tính năng',
                            'question' => 'Câu hỏi',
                            'other' => 'Khác',
                        ];
                        $initialCategory = old('category', 'bug');
                        $initialCategoryLabel = $feedbackCategories[$initialCategory] ?? 'Lỗi kỹ thuật';
                    @endphp
                    <div class="relative"
                         x-data="{
                             open: false,
                             selectedValue: '{{ $initialCategory }}',
                             selectedLabel: '{{ $initialCategoryLabel }}',
                             options: [
                                 { value: 'bug', label: 'Lỗi kỹ thuật' },
                                 { value: 'feature', label: 'Góp ý tính năng' },
                                 { value: 'question', label: 'Câu hỏi' },
                                 { value: 'other', label: 'Khác' }
                             ],
                             select(val, label) {
                                 this.selectedValue = val;
                                 this.selectedLabel = label;
                                 this.open = false;
                             }
                         }">
                        <!-- Hidden Input for Form Submission -->
                        <input type="hidden" name="category" :value="selectedValue">

                        <!-- Trigger Button -->
                        <button type="button"
                                @click="open = !open"
                                :aria-expanded="open"
                                data-slot="combobox-trigger"
                                class="w-full h-11 px-4 py-2.5 bg-white dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg text-sm text-gray-900 dark:text-white flex items-center justify-between text-left focus:outline-none focus:ring-1 focus:ring-gray-300 dark:focus:ring-gray-600 focus:border-gray-400 dark:focus:border-gray-500 transition-all cursor-pointer select-none group/combobox-trigger shadow-xs">
                            <span x-text="selectedLabel" class="truncate font-medium text-gray-800 dark:text-gray-200"></span>
                            <!-- ArrowDown01Icon -->
                            <svg class="w-4 h-4 text-gray-400 dark:text-gray-500 transition-transform duration-200 group-hover/combobox-trigger:text-gray-700 dark:group-hover/combobox-trigger:text-gray-300 shrink-0 pointer-events-none"
                                 :class="open ? 'rotate-180 text-gray-700 dark:text-gray-300' : ''"
                                 fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m19 9-7 7-7-7" />
                            </svg>
                        </button>

                        <!-- Content Popup -->
                        <div x-show="open"
                             x-cloak
                             @click.outside="open = false"
                             @keydown.escape.window="open = false"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 -translate-y-1 scale-98"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                             x-transition:leave-end="opacity-0 -translate-y-1 scale-98"
                             data-slot="combobox-content"
                             class="absolute left-0 right-0 top-full mt-1.5 z-50 rounded-xl border border-gray-200 dark:border-[#30363d] bg-white dark:bg-[#161b22] shadow-xl ring-1 ring-black/5 dark:ring-white/5 p-1 overflow-hidden space-y-0.5">
                            <template x-for="item in options" :key="item.value">
                                <button type="button"
                                        @click="select(item.value, item.label)"
                                        data-slot="combobox-item"
                                        class="w-full relative flex items-center justify-between px-3 py-2 text-sm rounded-lg transition-colors cursor-pointer select-none text-left"
                                        :class="selectedValue === item.value 
                                            ? 'bg-neutral-100 dark:bg-neutral-800 text-neutral-900 dark:text-white font-medium' 
                                            : 'text-neutral-700 dark:text-neutral-300 hover:bg-neutral-50 dark:hover:bg-neutral-800/60'">
                                    <span x-text="item.label"></span>
                                    <svg x-show="selectedValue === item.value"
                                         class="w-4 h-4 text-neutral-900 dark:text-white shrink-0 pointer-events-none transition-transform duration-200"
                                         fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Priority Level (@animate-ui / shadcn RadioGroup Style) -->
                <div>
                    <label class="block text-[13px] font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">Mức độ ưu tiên <span class="text-red-500">*</span></label>
                    <div data-slot="radio-group" class="flex flex-wrap items-center gap-6 py-2.5">
                        <label class="flex items-center gap-3 cursor-pointer group select-none relative">
                            <input type="radio" name="priority" value="low" {{ old('priority') === 'low' ? 'checked' : '' }} class="peer sr-only">
                            <span data-slot="radio-group-item" class="relative size-4 rounded-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900/50 flex items-center justify-center shrink-0 aspect-square outline-none transition-all duration-200 group-hover:border-neutral-400 peer-checked:border-neutral-900 dark:peer-checked:border-white peer-checked:bg-neutral-900 dark:peer-checked:bg-white peer-checked:[&>span>span]:opacity-100 peer-checked:[&>span>span]:scale-100 peer-focus-visible:ring-2 peer-focus-visible:ring-neutral-900/20">
                                <span data-slot="radio-group-indicator" class="relative z-10 flex size-4 items-center justify-center pointer-events-none">
                                    <span class="size-2 rounded-full bg-white dark:bg-neutral-950 opacity-0 scale-0 transition-all duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] pointer-events-none"></span>
                                </span>
                            </span>
                            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors peer-checked:text-neutral-950 dark:peer-checked:text-white peer-checked:font-semibold">Thấp</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer group select-none relative">
                            <input type="radio" name="priority" value="medium" {{ old('priority', 'medium') === 'medium' ? 'checked' : '' }} class="peer sr-only">
                            <span data-slot="radio-group-item" class="relative size-4 rounded-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900/50 flex items-center justify-center shrink-0 aspect-square outline-none transition-all duration-200 group-hover:border-neutral-400 peer-checked:border-neutral-900 dark:peer-checked:border-white peer-checked:bg-neutral-900 dark:peer-checked:bg-white peer-checked:[&>span>span]:opacity-100 peer-checked:[&>span>span]:scale-100 peer-focus-visible:ring-2 peer-focus-visible:ring-neutral-900/20">
                                <span data-slot="radio-group-indicator" class="relative z-10 flex size-4 items-center justify-center pointer-events-none">
                                    <span class="size-2 rounded-full bg-white dark:bg-neutral-950 opacity-0 scale-0 transition-all duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] pointer-events-none"></span>
                                </span>
                            </span>
                            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors peer-checked:text-neutral-950 dark:peer-checked:text-white peer-checked:font-semibold">Trung bình</span>
                        </label>

                        <label class="flex items-center gap-3 cursor-pointer group select-none relative">
                            <input type="radio" name="priority" value="high" {{ old('priority') === 'high' ? 'checked' : '' }} class="peer sr-only">
                            <span data-slot="radio-group-item" class="relative size-4 rounded-full border border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900/50 flex items-center justify-center shrink-0 aspect-square outline-none transition-all duration-200 group-hover:border-neutral-400 peer-checked:border-neutral-900 dark:peer-checked:border-white peer-checked:bg-neutral-900 dark:peer-checked:bg-white peer-checked:[&>span>span]:opacity-100 peer-checked:[&>span>span]:scale-100 peer-focus-visible:ring-2 peer-focus-visible:ring-neutral-900/20">
                                <span data-slot="radio-group-indicator" class="relative z-10 flex size-4 items-center justify-center pointer-events-none">
                                    <span class="size-2 rounded-full bg-white dark:bg-neutral-950 opacity-0 scale-0 transition-all duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] pointer-events-none"></span>
                                </span>
                            </span>
                            <span class="text-sm font-medium text-neutral-700 dark:text-neutral-300 group-hover:text-neutral-900 dark:group-hover:text-white transition-colors peer-checked:text-neutral-950 dark:peer-checked:text-white peer-checked:font-semibold">Cao</span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Detailed Description -->
            <div>
                <label for="description-area" class="block text-[13px] font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">Mô tả chi tiết <span class="text-red-500">*</span></label>
                <textarea id="description-area" name="description" rows="6" placeholder="Vui lòng cung cấp thêm chi tiết để chúng tôi có thể hỗ trợ bạn tốt nhất..."
                          class="w-full px-4 py-3 bg-white dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg outline-none transition-all duration-200 focus:border-black dark:focus:border-white focus:ring-0 text-sm text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 resize-none" required>{{ old('description') }}</textarea>
            </div>

            <!-- File Upload -->
            <div>
                <label class="block text-[13px] font-medium text-gray-500 dark:text-gray-400 mb-2 uppercase tracking-wider">Tệp đính kèm (Hình ảnh/Log)</label>
                <div class="relative">
                    <input type="file" id="file-upload" name="attachment" class="hidden" accept="image/*,.log,.txt,.pdf" @change="handleFile($event)">
                    
                    <template x-if="!fileName">
                        <label for="file-upload" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-gray-200 dark:border-[#30363d] rounded-lg cursor-pointer hover:border-gray-400 dark:hover:border-gray-500 transition-colors duration-200 bg-gray-50/50 dark:bg-[#161b22]/50">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <iconify-icon icon="lucide:upload-cloud" class="text-2xl text-gray-400 dark:text-gray-500 mb-2"></iconify-icon>
                                <p class="text-xs text-gray-500 dark:text-gray-400">Kéo thả hoặc nhấp để chọn tệp</p>
                                <p class="text-[10px] text-gray-400 dark:text-gray-500 mt-1">PNG, JPG, LOG, PDF lên đến 10MB</p>
                            </div>
                        </label>
                    </template>

                    <template x-if="fileName">
                        <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-[#161b22] border border-gray-200 dark:border-[#30363d] rounded-lg">
                            <div class="flex items-center gap-3 min-w-0">
                                <iconify-icon icon="lucide:file-text" class="text-2xl text-emerald-600 dark:text-emerald-400 shrink-0"></iconify-icon>
                                <div class="truncate">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate" x-text="fileName"></p>
                                    <p class="text-xs text-gray-400" x-text="fileSize"></p>
                                </div>
                            </div>
                            <button type="button" @click="removeFile()" class="p-1.5 text-gray-400 hover:text-red-500 dark:hover:text-red-400 transition">
                                <iconify-icon icon="lucide:trash-2" class="text-lg"></iconify-icon>
                            </button>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 flex justify-end items-center space-x-4">
                <a href="{{ route('support.index') }}" id="cancel-btn" class="px-6 py-2.5 text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                    Hủy bỏ
                </a>
                <button type="submit" id="submit-btn" :disabled="submitted"
                        class="px-8 py-2.5 bg-black text-white dark:bg-white dark:text-black text-sm font-medium rounded-lg hover:bg-gray-800 dark:hover:bg-gray-200 active:scale-[0.98] transition-all duration-200 shadow-sm disabled:opacity-60 flex items-center gap-2">
                    <span x-show="!submitted">Gửi báo cáo</span>
                    <span x-show="submitted" class="flex items-center gap-2">
                        <iconify-icon icon="lucide:loader-2" class="animate-spin"></iconify-icon>
                        Đang gửi...
                    </span>
                </button>
            </div>
        </form>

        <!-- Footer Note -->
        <footer class="mt-20 border-t border-gray-100 dark:border-neutral-800 pt-8 text-center">
            <p class="text-[11px] text-gray-400 dark:text-gray-500 uppercase tracking-widest">Hỗ trợ 24/7 &bull; Bảo mật thông tin &bull; Thời gian phản hồi dự kiến 24h</p>
        </footer>
    </div>
</div>
@endsection
