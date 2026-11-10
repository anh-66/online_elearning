@extends('layouts.admin')

@php
    $isEdit = $banner->exists;
    $hasImage = !empty($banner->image_path);
    $defaultColor = $banner->bg_color ?: '#019D91';
    $showText = old('show_text', $banner->show_text ?? false);
@endphp

@section('title', $isEdit ? 'Sửa banner' : 'Thêm banner')
@section('page_title', $isEdit ? 'Sửa Banner' : 'Thêm Banner Mới')

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.css" />
    <style>
        /* Custom Purple Handles & Frame exactly matching user reference */
        .cropper-view-box {
            outline: 2px solid #7c3aed !important;
            outline-color: #7c3aed !important;
        }
        .cropper-line {
            background-color: #7c3aed !important;
        }
        .cropper-dashed {
            border-color: rgba(255, 255, 255, 0.7) !important;
        }
        /* Corner & Edge Handles: large, prominent, easily draggable in & out */
        .cropper-point {
            background-color: #7c3aed !important;
            width: 14px !important;
            height: 14px !important;
            opacity: 1 !important;
        }
        .cropper-point.point-ne,
        .cropper-point.point-nw,
        .cropper-point.point-se,
        .cropper-point.point-sw {
            width: 18px !important;
            height: 18px !important;
            background-color: #7c3aed !important;
            border: 2.5px solid #ffffff !important;
            border-radius: 4px !important;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.5) !important;
        }
        .cropper-point.point-se {
            right: -6px !important;
            bottom: -6px !important;
            cursor: se-resize !important;
        }
        .cropper-point.point-sw {
            left: -6px !important;
            bottom: -6px !important;
            cursor: sw-resize !important;
        }
        .cropper-point.point-nw {
            left: -6px !important;
            top: -6px !important;
            cursor: nw-resize !important;
        }
        .cropper-point.point-ne {
            right: -6px !important;
            top: -6px !important;
            cursor: ne-resize !important;
        }
        .cropper-point.point-e { right: -5px !important; cursor: e-resize !important; }
        .cropper-point.point-w { left: -5px !important; cursor: w-resize !important; }
        .cropper-point.point-n { top: -5px !important; cursor: n-resize !important; }
        .cropper-point.point-s { bottom: -5px !important; cursor: s-resize !important; }
    </style>
@endpush

@section('content')
<div class="max-w-4xl space-y-6" x-data="bannerForm({
    initialMode: '{{ $hasImage ? 'image' : 'color' }}',
    initialBgColor: '{{ $defaultColor }}',
    initialImage: '{{ $hasImage ? asset('storage/' . $banner->image_path) : '' }}',
    initialTitle: '{{ addslashes($banner->title ?? 'Tiêu đề banner mẫu') }}',
    initialShowText: {{ $showText ? 'true' : 'false' }},
    initialUrl: '{{ addslashes($banner->link_url ?? '/courses') }}'
})">
    {{-- Top Header --}}
    <div class="glass-card p-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-gray-900">{{ $isEdit ? 'Chỉnh sửa banner' : 'Thêm banner mới' }}</h2>
                <p class="text-xs text-gray-500 mt-0.5">Banner sẽ lấp đầy tràn viền toàn bộ dải billboard đầu trang chủ.</p>
            </div>
            <a href="{{ route('admin.banners.index') }}" class="text-xs font-semibold text-gray-500 hover:text-gray-700 flex items-center gap-1">
                <iconify-icon icon="lucide:arrow-left" class="text-sm"></iconify-icon>
                Quay lại
            </a>
        </div>
    </div>

    {{-- Live Preview Section (Mô phỏng tràn viền trang chủ) --}}
    <div class="glass-card p-6 space-y-3">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider flex items-center gap-1.5">
                <iconify-icon icon="lucide:eye" class="text-base text-[#019D91]"></iconify-icon>
                Xem trước giao diện tràn viền trên trang chủ
            </span>
            <span class="text-[11px] font-semibold text-[#019D91]" x-text="mode === 'image' ? (currentImage ? 'Chế độ: Ảnh tràn viền' : 'Chưa chọn ảnh') : 'Chế độ: Màu nền tràn viền'"></span>
        </div>

        <div class="relative w-full rounded-2xl overflow-hidden min-h-[200px] sm:min-h-[260px] flex items-center justify-center text-center p-6 sm:p-8 transition-all duration-300 shadow-lg border border-gray-200/80"
             :style="mode === 'color' || !currentImage ? 'background: ' + bgColor : ''">
            
            {{-- Image Mode Background (Edge to Edge) --}}
            <template x-if="mode === 'image' && currentImage">
                <img :src="currentImage" alt="Preview Banner" class="absolute inset-0 w-full h-full object-cover object-center">
            </template>
            
            {{-- Optional Overlay if showText is ON or Color Mode --}}
            <div x-show="(mode === 'image' && currentImage && showText)" class="absolute inset-0 bg-gradient-to-t from-black/85 via-black/45 to-transparent"></div>

            {{-- Banner Text & CTA Content --}}
            <div x-show="mode === 'color' || showText" class="relative z-10 max-w-3xl mx-auto space-y-3">
                <h3 class="banner-headline font-anton text-2xl sm:text-4xl font-extrabold text-white uppercase tracking-wide leading-[1.2] sm:leading-[1.28] drop-shadow-md"
                    x-text="title || 'Tiêu đề banner quảng bá...'"></h3>
                <p class="text-xs sm:text-sm text-white/90 max-w-xl mx-auto drop-shadow leading-relaxed">
                    Khám phá các khóa học chuyên sâu được cập nhật liên tục theo chuẩn doanh nghiệp.
                </p>
                <div class="pt-2">
                    <span class="inline-flex items-center justify-center rounded-full bg-white text-[#161b22] px-6 py-2.5 text-xs sm:text-sm font-bold shadow-lg transition pointer-events-none">
                        Khám phá chương trình &rarr;
                    </span>
                </div>
            </div>

            {{-- Text info if image only without text overlay --}}
            <div x-show="mode === 'image' && currentImage && !showText" class="absolute bottom-3 right-3 px-3 py-1 bg-black/60 backdrop-blur-md rounded-lg text-white text-[11px] font-medium z-10">
                Chế độ hiển thị nguyên bản ảnh banner (Toàn bộ banner có thể nhấp chuột)
            </div>
        </div>
    </div>

    {{-- Main Form --}}
    <form method="POST" enctype="multipart/form-data" id="banner-edit-form"
          action="{{ $isEdit ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" 
          class="glass-card p-6 space-y-6">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        {{-- Hidden Inputs --}}
        <input type="hidden" name="cropped_image" id="cropped_image" x-model="croppedImage">
        <input type="hidden" name="remove_image" id="remove_image" :value="removeImage ? 1 : 0">
        <input type="hidden" name="bg_color" id="bg_color" :value="bgColor">

        {{-- Tiêu đề --}}
        <div>
            <label for="title" class="block text-sm font-semibold text-gray-700">Tiêu đề Banner <span class="text-red-500">*</span></label>
            <input id="title" name="title" x-model="title" required autofocus
                   placeholder="Ví dụ: Nâng tầm kỹ năng lập trình cùng chuyên gia..."
                   class="mt-1.5 block w-full rounded-xl border-gray-200 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Chọn kiểu hiển thị: Ảnh nền hoặc Màu nền --}}
        <div class="pt-2 border-t border-gray-100">
            <label class="block text-sm font-semibold text-gray-700 mb-2">Kiểu hiển thị nền</label>
            <div class="grid grid-cols-2 gap-3 sm:gap-4 max-w-md">
                <button type="button" 
                        @click="setMode('image')"
                        class="flex items-center justify-center gap-2 p-3 rounded-xl border text-sm font-semibold transition-all cursor-pointer"
                        :class="mode === 'image' 
                            ? 'border-[#019D91] bg-[#019D91]/10 text-[#019D91] ring-2 ring-[#019D91]/20' 
                            : 'border-gray-200 hover:bg-gray-50 text-gray-600'">
                    <iconify-icon icon="lucide:image" class="text-lg"></iconify-icon>
                    <span>Dùng hình ảnh</span>
                </button>

                <button type="button" 
                        @click="setMode('color')"
                        class="flex items-center justify-center gap-2 p-3 rounded-xl border text-sm font-semibold transition-all cursor-pointer"
                        :class="mode === 'color' 
                            ? 'border-[#019D91] bg-[#019D91]/10 text-[#019D91] ring-2 ring-[#019D91]/20' 
                            : 'border-gray-200 hover:bg-gray-50 text-gray-600'">
                    <iconify-icon icon="lucide:palette" class="text-lg"></iconify-icon>
                    <span>Dùng màu nền (Không cần ảnh)</span>
                </button>
            </div>
        </div>

        {{-- KHU VỰC 1: DÙNG HÌNH ẢNH (Upload & Cắt ảnh) --}}
        <div x-show="mode === 'image'" x-cloak class="space-y-4 pt-2">
            <div class="flex items-center justify-between">
                <label class="block text-sm font-semibold text-gray-700">Hình ảnh banner</label>
                <span class="text-xs text-gray-400">Khuyên dùng kéo thả khung cắt theo ý muốn</span>
            </div>
            
            {{-- Current Image Preview & Actions --}}
            <div x-show="currentImage" class="p-4 rounded-2xl bg-gray-50 border border-gray-200/80 space-y-3">
                <div class="relative w-full max-w-lg aspect-[3/1] rounded-xl overflow-hidden bg-gray-200 border border-gray-300 shadow-sm">
                    <img :src="currentImage" alt="Banner current" class="w-full h-full object-cover">
                </div>
                <div class="flex flex-wrap items-center gap-2.5">
                    <button type="button" @click="triggerCropModal(currentImage)" 
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-[#7c3aed] hover:bg-[#6d28d9] text-xs font-bold text-white shadow-sm transition active:scale-95">
                        <iconify-icon icon="lucide:crop" class="text-sm"></iconify-icon>
                        Kéo thả & Cắt lại vùng mong muốn
                    </button>
                    <button type="button" @click="removeCurrentPhoto()" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-white border border-rose-200 hover:bg-rose-50 text-xs font-semibold text-rose-600 shadow-sm transition">
                        <iconify-icon icon="lucide:trash-2" class="text-sm"></iconify-icon>
                        Xóa ảnh (Chỉ dùng màu nền)
                    </button>
                </div>
            </div>

            {{-- File Input & Cropper trigger --}}
            <div class="p-4 border-2 border-dashed border-gray-200 hover:border-[#019D91] rounded-2xl bg-white transition-colors">
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-[#019D91]/10 text-[#019D91] flex items-center justify-center shrink-0">
                        <iconify-icon icon="lucide:upload-cloud" class="text-2xl"></iconify-icon>
                    </div>
                    <div class="flex-1 text-center sm:text-left">
                        <p class="text-sm font-semibold text-gray-800">Tải ảnh mới từ máy tính</p>
                        <p class="text-xs text-gray-400 mt-0.5">Khung cắt ảnh có thể kéo to/nhỏ tùy ý từ 4 góc và 4 cạnh. Định dạng: JPG, PNG, WEBP (tối đa 5MB).</p>
                    </div>
                    <label class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 bg-[#019D91] hover:bg-[#008b80] text-white text-xs font-bold rounded-xl shadow-sm transition active:scale-95 shrink-0">
                        <iconify-icon icon="lucide:file-up" class="text-base"></iconify-icon>
                        <span>Chọn ảnh</span>
                        <input id="image_file_input" type="file" accept="image/*" class="hidden" @change="onFileSelected($event)">
                    </label>
                </div>
            </div>

            {{-- Checkbox: Hiển thị chữ đè lên ảnh --}}
            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-200">
                <label class="flex items-start gap-2.5 cursor-pointer">
                    <input type="checkbox" name="show_text" value="1" x-model="showText"
                           class="mt-0.5 w-4 h-4 rounded border-gray-300 text-[#019D91] focus:ring-[#019D91]">
                    <div>
                        <span class="text-sm font-semibold text-gray-800">Hiển thị chữ (Tiêu đề, mô tả và nút bấm) đè lên ảnh banner</span>
                        <p class="text-xs text-gray-500 mt-0.5">Nếu ảnh banner của bạn đã có sẵn thiết kế chữ, hãy bỏ chọn mục này để ảnh hiển thị nguyên bản, sắc nét không bị che khuất.</p>
                    </div>
                </label>
            </div>
            @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- KHU VỰC 2: CHỌN MÀU NỀN (Color & Presets) --}}
        <div class="space-y-4 pt-2 border-t border-gray-100">
            <div class="flex items-center justify-between">
                <label class="block text-sm font-semibold text-gray-700">Màu nền chủ đạo</label>
                <span class="text-xs font-mono font-bold text-gray-600 px-2 py-0.5 bg-gray-100 rounded-md" x-text="bgColor"></span>
            </div>

            {{-- Color Presets --}}
            <div>
                <p class="text-xs text-gray-400 mb-2">Bảng màu gợi ý hiện đại:</p>
                <div class="flex flex-wrap items-center gap-2.5">
                    @php
                        $presets = [
                            ['label' => 'Teal EduLearn', 'val' => '#019D91'],
                            ['label' => 'Dark Slate', 'val' => '#0f172a'],
                            ['label' => 'Indigo Modern', 'val' => '#4f46e5'],
                            ['label' => 'Sky Blue', 'val' => '#0284c7'],
                            ['label' => 'Emerald Green', 'val' => '#059669'],
                            ['label' => 'Amber Orange', 'val' => '#d97706'],
                            ['label' => 'Crimson Red', 'val' => '#dc2626'],
                            ['label' => 'Purple Tech', 'val' => '#7c3aed'],
                            ['label' => 'Gradient Teal', 'val' => 'linear-gradient(135deg, #019D91 0%, #0f172a 100%)'],
                            ['label' => 'Gradient Indigo', 'val' => 'linear-gradient(135deg, #4f46e5 0%, #06b6d4 100%)'],
                            ['label' => 'Gradient Sunset', 'val' => 'linear-gradient(135deg, #f43f5e 0%, #f59e0b 100%)'],
                        ];
                    @endphp
                    @foreach($presets as $p)
                        <button type="button" 
                                @click="selectPreset('{{ $p['val'] }}')"
                                title="{{ $p['label'] }}"
                                class="w-8 h-8 rounded-full border-2 transition-transform hover:scale-110 shadow-sm"
                                :class="bgColor === '{{ $p['val'] }}' ? 'border-gray-900 ring-2 ring-[#019D91]' : 'border-white'"
                                style="background: {{ $p['val'] }};">
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Custom Color Picker --}}
            <div class="flex items-center gap-3">
                <input type="color" x-model="colorPicker" @input="onCustomColorChange($event.target.value)"
                       class="w-10 h-10 rounded-xl border border-gray-200 cursor-pointer p-0.5 bg-white">
                <input type="text" x-model="bgColor" placeholder="Mã màu HEX hoặc linear-gradient(...)"
                       class="flex-1 rounded-xl border-gray-200 text-xs font-mono focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20">
            </div>
        </div>

        {{-- URL khi click --}}
        <div class="pt-2 border-t border-gray-100">
            <label for="link_url" class="block text-sm font-semibold text-gray-700">Liên kết URL khi bấm vào (tùy chọn)</label>
            <input id="link_url" name="link_url" value="{{ old('link_url', $banner->link_url) }}" placeholder="/courses hoặc https://..."
                   class="mt-1.5 block w-full rounded-xl border-gray-200 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('link_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Thứ tự --}}
        <div>
            <label for="order" class="block text-sm font-semibold text-gray-700">Thứ tự hiển thị (số nhỏ xếp trước)</label>
            <input id="order" type="number" min="0" max="9999" name="order" value="{{ old('order', $banner->order ?? 0) }}"
                   class="mt-1.5 block w-full rounded-xl border-gray-200 text-sm focus:border-[#019D91] focus:ring-2 focus:ring-[#019D91]/20 transition-all">
            @error('order') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        {{-- Trạng thái --}}
        <label class="flex items-center gap-2.5 text-sm font-semibold text-gray-700 cursor-pointer pt-2">
            <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $banner->is_active ?? true))
                   class="w-4 h-4 rounded border-gray-300 text-[#019D91] focus:ring-[#019D91]">
            <span>Hiển thị banner này trên slider trang chủ</span>
        </label>

        {{-- Buttons --}}
        <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
            <button type="submit" class="rounded-xl bg-[#019D91] px-6 py-2.5 text-sm font-bold text-white hover:bg-[#008b80] shadow-sm transition-colors active:scale-95">
                {{ $isEdit ? 'Lưu thay đổi' : 'Thêm banner' }}
            </button>
            <a href="{{ route('admin.banners.index') }}" class="text-sm font-semibold text-gray-500 hover:text-gray-700 px-3 py-2">
                Hủy
            </a>
        </div>
    </form>

    {{-- ======================================================== --}}
    {{-- MODAL CẮT ẢNH BANNER (KÉO RA KÉO VÀO TỰ DO NHƯ THIẾT KẾ) --}}
    {{-- ======================================================== --}}
    <div x-show="cropModalOpen" x-cloak
         class="fixed inset-0 z-50 overflow-y-auto bg-black/85 backdrop-blur-sm flex items-center justify-center p-3 sm:p-5">
        <div class="bg-white rounded-3xl max-w-4xl w-full p-5 sm:p-6 shadow-2xl space-y-4" @click.outside="closeCropModal()">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-[#7c3aed]/10 text-[#7c3aed] flex items-center justify-center">
                        <iconify-icon icon="lucide:crop" class="text-lg"></iconify-icon>
                    </span>
                    <div>
                        <h3 class="text-base font-bold text-gray-900">Kéo các góc & cạnh để chọn vùng hiển thị banner</h3>
                        <p class="text-xs text-gray-400">Bạn có thể bấm giữ và kéo các góc màu tím để mở rộng hoặc thu nhỏ vùng cắt tự do.</p>
                    </div>
                </div>
                <button type="button" @click="closeCropModal()" class="text-gray-400 hover:text-gray-600 p-1.5 rounded-lg hover:bg-gray-100 transition">
                    <iconify-icon icon="lucide:x" class="text-xl"></iconify-icon>
                </button>
            </div>

            {{-- Cropper Container --}}
            <div class="w-full h-80 sm:h-[420px] bg-neutral-900 rounded-2xl overflow-hidden flex items-center justify-center relative">
                <img id="cropper_image_el" src="" alt="To Crop" class="max-w-full max-h-full block">
            </div>

            {{-- Cropper Controls Bar --}}
            <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
                {{-- Aspect Ratio options --}}
                <div class="flex items-center gap-1.5">
                    <span class="text-xs font-semibold text-gray-500 mr-1">Chế độ cắt:</span>
                    <button type="button" @click="setAspectRatio(NaN)" 
                            :class="isNaN(aspectRatio) ? 'bg-[#7c3aed] text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-1">
                        <iconify-icon icon="lucide:move" class="text-xs"></iconify-icon>
                        Tự do kéo thả
                    </button>
                    <button type="button" @click="setAspectRatio(3 / 1)" 
                            :class="aspectRatio === 3 ? 'bg-[#7c3aed] text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                        3:1 (Banner ngang)
                    </button>
                    <button type="button" @click="setAspectRatio(16 / 9)" 
                            :class="aspectRatio === 16/9 ? 'bg-[#7c3aed] text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200'"
                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
                        16:9
                    </button>
                </div>

                {{-- Action tools: Zoom, Rotate, Reset --}}
                <div class="flex items-center gap-2">
                    <button type="button" @click="cropper?.zoom(0.1)" title="Phóng to ảnh"
                            class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold flex items-center gap-1">
                        <iconify-icon icon="lucide:zoom-in" class="text-base"></iconify-icon>
                        <span class="hidden sm:inline">Phóng to</span>
                    </button>
                    <button type="button" @click="cropper?.zoom(-0.1)" title="Thu nhỏ ảnh"
                            class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold flex items-center gap-1">
                        <iconify-icon icon="lucide:zoom-out" class="text-base"></iconify-icon>
                        <span class="hidden sm:inline">Thu nhỏ</span>
                    </button>
                    <button type="button" @click="cropper?.rotate(90)" title="Xoay 90 độ"
                            class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold flex items-center gap-1">
                        <iconify-icon icon="lucide:rotate-cw" class="text-base"></iconify-icon>
                        <span class="hidden sm:inline">Xoay</span>
                    </button>
                    <button type="button" @click="cropper?.reset()" title="Đặt lại ban đầu"
                            class="p-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold flex items-center gap-1">
                        <iconify-icon icon="lucide:refresh-cw" class="text-base"></iconify-icon>
                        <span class="hidden sm:inline">Đặt lại</span>
                    </button>
                </div>
            </div>

            {{-- Modal Action Buttons --}}
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <button type="button" @click="closeCropModal()" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-100 transition">
                    Hủy bỏ
                </button>
                <button type="button" @click="applyCrop()" class="px-6 py-2.5 rounded-xl bg-[#7c3aed] hover:bg-[#6d28d9] text-white text-xs font-bold shadow-lg transition active:scale-95 flex items-center gap-1.5">
                    <iconify-icon icon="lucide:check" class="text-base"></iconify-icon>
                    Cắt & Dùng vùng này
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.6.2/cropper.min.js"></script>
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('bannerForm', (config) => ({
        mode: config.initialMode || 'image',
        bgColor: config.initialBgColor || '#019D91',
        colorPicker: config.initialBgColor.startsWith('#') ? config.initialBgColor : '#019D91',
        currentImage: config.initialImage || '',
        croppedImage: '',
        removeImage: false,
        showText: config.initialShowText ?? false,
        title: config.initialTitle || '',
        cropModalOpen: false,
        cropper: null,
        aspectRatio: NaN, // Mặc định tự do kéo ra kéo vào theo mọi hướng

        setMode(newMode) {
            this.mode = newMode;
            if (newMode === 'color') {
                this.removeImage = true;
            } else {
                this.removeImage = false;
            }
        },

        selectPreset(color) {
            this.bgColor = color;
            if (color.startsWith('#')) {
                this.colorPicker = color;
            }
        },

        onCustomColorChange(hex) {
            this.bgColor = hex;
        },

        removeCurrentPhoto() {
            this.currentImage = '';
            this.croppedImage = '';
            this.removeImage = true;
            this.mode = 'color';
            const fileInput = document.getElementById('image_file_input');
            if (fileInput) fileInput.value = '';
        },

        onFileSelected(event) {
            const file = event.target.files?.[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                this.triggerCropModal(e.target.result);
            };
            reader.readAsDataURL(file);
        },

        triggerCropModal(imageSrc) {
            this.cropModalOpen = true;
            this.$nextTick(() => {
                const imgEl = document.getElementById('cropper_image_el');
                imgEl.src = imageSrc;

                if (this.cropper) {
                    this.cropper.destroy();
                }

                // Initialize with free aspect ratio and full movable/resizable handles
                this.cropper = new Cropper(imgEl, {
                    aspectRatio: this.aspectRatio, // NaN cho phép kéo thả tự do mọi góc
                    viewMode: 1,
                    dragMode: 'crop',
                    autoCropArea: 0.9,
                    cropBoxMovable: true,
                    cropBoxResizable: true,
                    toggleDragModeOnDblclick: false,
                    responsive: true,
                    guides: true,
                    center: true,
                    background: true,
                });
            });
        },

        setAspectRatio(ratio) {
            this.aspectRatio = ratio;
            if (this.cropper) {
                this.cropper.setAspectRatio(ratio);
            }
        },

        applyCrop() {
            if (!this.cropper) return;

            const canvas = this.cropper.getCroppedCanvas({
                maxWidth: 2560,
                maxHeight: 1440,
                fillColor: '#fff',
                imageSmoothingEnabled: true,
                imageSmoothingQuality: 'high',
            });

            const base64 = canvas.toDataURL('image/jpeg', 0.92);
            this.currentImage = base64;
            this.croppedImage = base64;
            this.removeImage = false;
            this.mode = 'image';
            this.closeCropModal();
        },

        closeCropModal() {
            this.cropModalOpen = false;
            if (this.cropper) {
                this.cropper.destroy();
                this.cropper = null;
            }
        }
    }));
});
</script>
@endpush
