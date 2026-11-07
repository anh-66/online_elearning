@props(['mode' => 'modal'])

<div x-data="{
    tab: 'outfit',
    bg: 'pink',
    hair: '01',
    outfit: '03',
    head: '01',
    eyes: '01',
    mouth: '01',
    facialHair: '01',
    accessories: '00',
    isExporting: false,

    backgrounds: [
        { id: 'white', name: 'Trắng', bgClass: 'bg-white', hex: '#ffffff' },
        { id: 'pink', name: 'Hồng', bgClass: 'bg-pink-300', hex: '#f9a8d4' },
        { id: 'red', name: 'Đỏ', bgClass: 'bg-red-300', hex: '#fca5a5' },
        { id: 'yellow', name: 'Vàng', bgClass: 'bg-yellow-300', hex: '#fde047' },
        { id: 'green', name: 'Xanh lá', bgClass: 'bg-green-300', hex: '#86efac' },
        { id: 'blue', name: 'Xanh dương', bgClass: 'bg-blue-300', hex: '#93c5fd' },
        { id: 'indigo', name: 'Chàm', bgClass: 'bg-indigo-300', hex: '#a5b4fc' },
        { id: 'purple', name: 'Tím', bgClass: 'bg-purple-300', hex: '#d8b4fe' }
    ],

    partsCount: {
        hair: 32,
        outfit: 25,
        head: 8,
        eyes: 6,
        mouth: 10,
        facialHair: 9,
        accessories: 18
    },

    pad(num) {
        return num.toString().padStart(2, '0');
    },

    get currentBgHex() {
        const item = this.backgrounds.find(b => b.id === this.bg);
        return item ? item.hex : '#f9a8d4';
    },

    get currentBgClass() {
        const item = this.backgrounds.find(b => b.id === this.bg);
        return item ? item.bgClass : 'bg-pink-300';
    },

    randomize() {
        const randomInt = (max) => Math.floor(Math.random() * max) + 1;
        this.bg = this.backgrounds[Math.floor(Math.random() * this.backgrounds.length)].id;
        this.hair = this.pad(randomInt(this.partsCount.hair));
        this.outfit = this.pad(randomInt(this.partsCount.outfit));
        this.head = this.pad(randomInt(this.partsCount.head));
        this.eyes = this.pad(randomInt(this.partsCount.eyes));
        this.mouth = this.pad(randomInt(this.partsCount.mouth));
        // 70% không có râu
        this.facialHair = Math.random() > 0.3 ? '01' : this.pad(randomInt(this.partsCount.facialHair));
        // 70% không có phụ kiện
        this.accessories = Math.random() > 0.3 ? '00' : this.pad(randomInt(this.partsCount.accessories));
    },

    reset() {
        this.bg = 'pink';
        this.hair = '01';
        this.outfit = '03';
        this.head = '01';
        this.eyes = '01';
        this.mouth = '01';
        this.facialHair = '01';
        this.accessories = '00';
    },

    async generateCanvas() {
        const canvas = document.createElement('canvas');
        canvas.width = 306;
        canvas.height = 306;
        const ctx = canvas.getContext('2d');

        // 1. Tô màu nền
        ctx.fillStyle = this.currentBgHex;
        ctx.fillRect(0, 0, 306, 306);

        // 2. Thứ tự lớp (dưới -> trên): Thân -> Áo -> Đầu/cổ -> Tóc -> Mắt -> Miệng -> Râu -> Phụ kiện
        const layers = [
            '/avatar-parts/base/Body.svg',
            `/avatar-parts/outfits/Outfit${this.outfit}.svg`,
            `/avatar-parts/faces/Face${this.head}.svg`,
            `/avatar-parts/hairs/Hair${this.hair}.svg`,
            `/avatar-parts/eyes/Eye${this.eyes}.svg`,
            `/avatar-parts/mouths/Mouth${this.mouth}.svg`
        ];

        if (this.facialHair && this.facialHair !== '01') {
            layers.push(`/avatar-parts/facial-hair/FacialHair${this.facialHair}.svg`);
        }

        if (this.accessories && this.accessories !== '00') {
            layers.push(`/avatar-parts/accessories/Accessory${this.accessories}.svg`);
        }

        const loadImage = (src) => new Promise((resolve) => {
            const img = new Image();
            img.onload = () => resolve(img);
            img.onerror = () => resolve(null);
            img.src = src;
        });

        const images = await Promise.all(layers.map(loadImage));
        for (const img of images) {
            if (img && img.naturalWidth) {
                const imgWidth = 306;
                const imgHeight = (img.naturalHeight / img.naturalWidth) * 306;
                ctx.drawImage(img, 0, 0, imgWidth, imgHeight);
            }
        }

        return canvas;
    },

    async applyAvatar() {
        this.isExporting = true;
        try {
            const canvas = await this.generateCanvas();
            const dataUrl = canvas.toDataURL('image/png');
            // Phát sự kiện ra cả window lẫn component nội bộ
            window.dispatchEvent(new CustomEvent('avatar-saved', { detail: { dataUrl } }));
            this.$dispatch('avatar-saved', { dataUrl });
        } catch (e) {
            console.error('Không thể tạo ảnh avatar', e);
            alert('Có lỗi khi tạo ảnh avatar, vui lòng thử lại.');
        } finally {
            this.isExporting = false;
        }
    },

    async downloadPng() {
        this.isExporting = true;
        try {
            const canvas = await this.generateCanvas();
            const link = document.createElement('a');
            link.download = 'avatar-' + Date.now() + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();
        } catch (e) {
            console.error('Lỗi tải ảnh', e);
        } finally {
            this.isExporting = false;
        }
    }
}" class="w-full flex flex-col lg:flex-row gap-6 p-2 sm:p-4">

    <!-- CỘT TRÁI: LIVE AVATAR PREVIEW & HÀNH ĐỘNG -->
    <div class="w-full lg:w-72 shrink-0 flex flex-col items-center">
        <!-- Khung hiển thị Avatar (Canvas Stack) -->
        <div class="relative w-64 h-64 sm:w-72 sm:h-72 rounded-3xl overflow-hidden shadow-md ring-1 ring-black/5 dark:ring-white/10 transition-colors duration-300"
             :class="currentBgClass">
            <!-- 1. Body Base (z-index: 10) -->
            <img src="/avatar-parts/base/Body.svg" alt="Body"
                 style="z-index: 10;"
                 class="absolute top-0 left-0 w-full pointer-events-none select-none z-[10]" />

            <!-- 2. Outfit: Áo nằm dưới đầu, phần cổ áo được thiết kế ôm quanh cổ (z-index: 20) -->
            <img :src="'/avatar-parts/outfits/Outfit' + outfit + '.svg'" alt="Outfit"
                 style="z-index: 20;"
                 class="absolute top-0 left-0 w-full pointer-events-none select-none" />

            <!-- 3. Face Head: Cổ và khuôn mặt nằm trên áo (z-index: 30) -->
            <img :src="'/avatar-parts/faces/Face' + head + '.svg'" alt="Face"
                 style="z-index: 30;"
                 class="absolute top-0 left-0 w-full pointer-events-none select-none" />

            <!-- 4. Hair: Tóc phủ lên đầu (z-index: 40) -->
            <img :src="'/avatar-parts/hairs/Hair' + hair + '.svg'" alt="Hair"
                 style="z-index: 40;"
                 class="absolute top-0 left-0 w-full pointer-events-none select-none" />

            <!-- 5. Eyes: Mắt (z-index: 50) -->
            <img :src="'/avatar-parts/eyes/Eye' + eyes + '.svg'" alt="Eyes"
                 style="z-index: 50;"
                 class="absolute top-0 left-0 w-full pointer-events-none select-none" />

            <!-- 6. Mouth: Miệng (z-index: 50) -->
            <img :src="'/avatar-parts/mouths/Mouth' + mouth + '.svg'" alt="Mouth"
                 style="z-index: 50;"
                 class="absolute top-0 left-0 w-full pointer-events-none select-none" />

            <!-- 7. Facial Hair: Râu cằm / ria mép (z-index: 60) -->
            <template x-if="facialHair !== '01'">
                <img :src="'/avatar-parts/facial-hair/FacialHair' + facialHair + '.svg'" alt="Facial Hair"
                     style="z-index: 60;"
                     class="absolute top-0 left-0 w-full pointer-events-none select-none z-[60]" />
            </template>

            <!-- 8. Accessories: Kính, mũ, khẩu trang (z-index: 70) -->
            <template x-if="accessories !== '00'">
                <img :src="'/avatar-parts/accessories/Accessory' + accessories + '.svg'" alt="Accessory"
                     style="z-index: 70;"
                     class="absolute top-0 left-0 w-full pointer-events-none select-none z-[70]" />
            </template>
        </div>

        <!-- Các nút bấm điều khiển -->
        <div class="w-full mt-4 flex items-center justify-center gap-2">
            <button type="button" @click="randomize()"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 h-10 px-3 rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-xs sm:text-sm font-semibold text-gray-800 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-neutral-700 transition active:scale-95 shadow-sm"
                    title="Tạo avatar ngẫu nhiên">
                <iconify-icon icon="lucide:dices" class="text-base text-[#019D91]"></iconify-icon>
                <span>Ngẫu nhiên</span>
            </button>

            <button type="button" @click="reset()"
                    class="h-10 w-10 inline-flex items-center justify-center rounded-xl border border-gray-200 dark:border-neutral-700 bg-white dark:bg-neutral-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-neutral-700 transition active:scale-95 shadow-sm"
                    title="Đặt lại">
                <iconify-icon icon="lucide:rotate-ccw" class="text-base"></iconify-icon>
            </button>
        </div>

        <!-- Nút áp dụng hoặc tải về -->
        @if($mode === 'modal')
            <button type="button" @click="applyAvatar()" :disabled="isExporting"
                    class="w-full mt-3 inline-flex items-center justify-center gap-2 h-11 px-4 rounded-xl bg-[#019D91] hover:bg-[#008b80] text-white text-sm font-semibold transition active:scale-95 shadow-md shadow-[#019D91]/20 disabled:opacity-60 cursor-pointer">
                <template x-if="!isExporting">
                    <span class="inline-flex items-center gap-2">
                        <iconify-icon icon="lucide:check-circle" class="text-base"></iconify-icon>
                        <span>Sử dụng avatar này</span>
                    </span>
                </template>
                <template x-if="isExporting">
                    <span class="inline-flex items-center gap-2">
                        <iconify-icon icon="lucide:loader-2" class="text-base animate-spin"></iconify-icon>
                        <span>Đang xử lý...</span>
                    </span>
                </template>
            </button>
        @else
            <button type="button" @click="downloadPng()" :disabled="isExporting"
                    class="w-full mt-3 inline-flex items-center justify-center gap-2 h-11 px-4 rounded-xl bg-[#019D91] hover:bg-[#008b80] text-white text-sm font-semibold transition active:scale-95 shadow-md shadow-[#019D91]/20 disabled:opacity-60 cursor-pointer">
                <iconify-icon icon="lucide:download" class="text-base"></iconify-icon>
                <span>Tải ảnh PNG</span>
            </button>
        @endif
    </div>

    <!-- CỘT PHẢI: BỘ CHỌN CÁC THÀNH PHẦN (TABS & GRID) -->
    <div class="flex-1 min-w-0 flex flex-col border border-gray-100 dark:border-neutral-800 rounded-2xl bg-gray-50/50 dark:bg-neutral-900/50 overflow-hidden">
        <!-- Thanh Tab danh mục (Ẩn thanh cuộn xấu) -->
        <div class="flex items-center gap-1.5 p-2.5 border-b border-gray-200/80 dark:border-neutral-800 bg-white dark:bg-neutral-900 overflow-x-auto"
             style="scrollbar-width: none; -ms-overflow-style: none;">
            <button type="button" @click="tab = 'bg'"
                    :class="tab === 'bg' ? 'bg-[#019D91] text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-neutral-800'"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer">
                <iconify-icon icon="lucide:palette" class="text-sm"></iconify-icon>
                <span>Màu nền</span>
            </button>

            <button type="button" @click="tab = 'outfit'"
                    :class="tab === 'outfit' ? 'bg-[#019D91] text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-neutral-800'"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer">
                <iconify-icon icon="lucide:shirt" class="text-sm"></iconify-icon>
                <span>Trang phục</span>
            </button>

            <button type="button" @click="tab = 'hair'"
                    :class="tab === 'hair' ? 'bg-[#019D91] text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-neutral-800'"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer">
                <iconify-icon icon="lucide:scissors" class="text-sm"></iconify-icon>
                <span>Tóc</span>
            </button>

            <button type="button" @click="tab = 'head'"
                    :class="tab === 'head' ? 'bg-[#019D91] text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-neutral-800'"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer">
                <iconify-icon icon="lucide:smile" class="text-sm"></iconify-icon>
                <span>Khuôn mặt</span>
            </button>

            <button type="button" @click="tab = 'eyes'"
                    :class="tab === 'eyes' ? 'bg-[#019D91] text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-neutral-800'"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer">
                <iconify-icon icon="lucide:eye" class="text-sm"></iconify-icon>
                <span>Mắt</span>
            </button>

            <button type="button" @click="tab = 'mouth'"
                    :class="tab === 'mouth' ? 'bg-[#019D91] text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-neutral-800'"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer">
                <iconify-icon icon="lucide:message-circle" class="text-sm"></iconify-icon>
                <span>Miệng</span>
            </button>

            <button type="button" @click="tab = 'facialHair'"
                    :class="tab === 'facialHair' ? 'bg-[#019D91] text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-neutral-800'"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer">
                <iconify-icon icon="lucide:user" class="text-sm"></iconify-icon>
                <span>Râu</span>
            </button>

            <button type="button" @click="tab = 'accessories'"
                    :class="tab === 'accessories' ? 'bg-[#019D91] text-white' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-neutral-800'"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold whitespace-nowrap transition flex items-center gap-1.5 cursor-pointer">
                <iconify-icon icon="lucide:glasses" class="text-sm"></iconify-icon>
                <span>Phụ kiện</span>
            </button>
        </div>

        <!-- Khung lưới chứa các tùy chọn (Scrollable Options Grid) -->
        <div class="p-3 sm:p-4 max-h-[380px] overflow-y-auto">

            <!-- TAB 1: MÀU NỀN -->
            <div x-show="tab === 'bg'" class="grid grid-cols-4 sm:grid-cols-4 gap-3">
                <template x-for="item in backgrounds" :key="item.id">
                    <button type="button" @click="bg = item.id"
                            class="relative flex flex-col items-center justify-center p-3 rounded-2xl border transition-all cursor-pointer"
                            :class="bg === item.id ? 'border-[#019D91] ring-2 ring-[#019D91] bg-white dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-gray-300 dark:hover:border-neutral-700'">
                        <div class="w-10 h-10 rounded-full border border-black/10 shadow-inner" :class="item.bgClass"></div>
                        <span class="mt-2 text-xs font-medium text-gray-700 dark:text-gray-300" x-text="item.name"></span>
                    </button>
                </template>
            </div>

            <!-- TAB 2: TRANG PHỤC (25 kiểu) -->
            <div x-show="tab === 'outfit'" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2.5">
                <template x-for="i in partsCount.outfit" :key="i">
                    <button type="button" @click="outfit = pad(i)"
                            class="relative aspect-square rounded-xl border p-1 transition-all overflow-hidden flex items-center justify-center cursor-pointer"
                            :class="outfit === pad(i) ? 'border-[#019D91] ring-2 ring-[#019D91] bg-white dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-gray-300 dark:hover:border-neutral-700'">
                        <img :src="'/avatar-parts/outfits/Outfit' + pad(i) + '.svg'" alt="Outfit" class="w-full h-full object-contain pointer-events-none" loading="lazy" />
                    </button>
                </template>
            </div>

            <!-- TAB 3: KIỂU TÓC (32 kiểu) -->
            <div x-show="tab === 'hair'" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2.5">
                <template x-for="i in partsCount.hair" :key="i">
                    <button type="button" @click="hair = pad(i)"
                            class="relative aspect-square rounded-xl border p-1 transition-all overflow-hidden flex items-center justify-center cursor-pointer"
                            :class="hair === pad(i) ? 'border-[#019D91] ring-2 ring-[#019D91] bg-white dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-gray-300 dark:hover:border-neutral-700'">
                        <img :src="'/avatar-parts/hairs/Hair' + pad(i) + '.svg'" alt="Hair" class="w-full h-full object-contain pointer-events-none" loading="lazy" />
                    </button>
                </template>
            </div>

            <!-- TAB 4: KHUÔN MẶT (8 kiểu) -->
            <div x-show="tab === 'head'" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-4 gap-2.5">
                <template x-for="i in partsCount.head" :key="i">
                    <button type="button" @click="head = pad(i)"
                            class="relative aspect-square rounded-xl border p-1 transition-all overflow-hidden flex items-center justify-center cursor-pointer"
                            :class="head === pad(i) ? 'border-[#019D91] ring-2 ring-[#019D91] bg-white dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-gray-300 dark:hover:border-neutral-700'">
                        <img :src="'/avatar-parts/faces/Face' + pad(i) + '.svg'" alt="Face" class="w-full h-full object-contain pointer-events-none" loading="lazy" />
                    </button>
                </template>
            </div>

            <!-- TAB 5: MẮT (6 kiểu) -->
            <div x-show="tab === 'eyes'" class="grid grid-cols-3 sm:grid-cols-3 gap-2.5">
                <template x-for="i in partsCount.eyes" :key="i">
                    <button type="button" @click="eyes = pad(i)"
                            class="relative aspect-square rounded-xl border p-1 transition-all overflow-hidden flex items-center justify-center cursor-pointer"
                            :class="eyes === pad(i) ? 'border-[#019D91] ring-2 ring-[#019D91] bg-white dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-gray-300 dark:hover:border-neutral-700'">
                        <img :src="'/avatar-parts/eyes/Eye' + pad(i) + '.svg'" alt="Eye" class="w-full h-full object-contain pointer-events-none" loading="lazy" />
                    </button>
                </template>
            </div>

            <!-- TAB 6: MIỆNG (10 kiểu) -->
            <div x-show="tab === 'mouth'" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2.5">
                <template x-for="i in partsCount.mouth" :key="i">
                    <button type="button" @click="mouth = pad(i)"
                            class="relative aspect-square rounded-xl border p-1 transition-all overflow-hidden flex items-center justify-center cursor-pointer"
                            :class="mouth === pad(i) ? 'border-[#019D91] ring-2 ring-[#019D91] bg-white dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-gray-300 dark:hover:border-neutral-700'">
                        <img :src="'/avatar-parts/mouths/Mouth' + pad(i) + '.svg'" alt="Mouth" class="w-full h-full object-contain pointer-events-none" loading="lazy" />
                    </button>
                </template>
            </div>

            <!-- TAB 7: RÂU (9 kiểu) -->
            <div x-show="tab === 'facialHair'" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2.5">
                <!-- Tùy chọn 01: Không có râu -->
                <button type="button" @click="facialHair = '01'"
                        class="relative aspect-square rounded-xl border p-1 transition-all overflow-hidden flex flex-col items-center justify-center cursor-pointer"
                        :class="facialHair === '01' ? 'border-[#019D91] ring-2 ring-[#019D91] bg-white dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-gray-300 dark:hover:border-neutral-700'">
                    <iconify-icon icon="lucide:ban" class="text-xl text-gray-400"></iconify-icon>
                    <span class="text-[10px] text-gray-500 font-medium mt-1">Không râu</span>
                </button>

                <template x-for="i in (partsCount.facialHair - 1)" :key="i">
                    <button type="button" @click="facialHair = pad(i + 1)"
                            class="relative aspect-square rounded-xl border p-1 transition-all overflow-hidden flex items-center justify-center cursor-pointer"
                            :class="facialHair === pad(i + 1) ? 'border-[#019D91] ring-2 ring-[#019D91] bg-white dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-gray-300 dark:hover:border-neutral-700'">
                        <img :src="'/avatar-parts/facial-hair/FacialHair' + pad(i + 1) + '.svg'" alt="Facial Hair" class="w-full h-full object-contain pointer-events-none" loading="lazy" />
                    </button>
                </template>
            </div>

            <!-- TAB 8: PHỤ KIỆN (18 kiểu) -->
            <div x-show="tab === 'accessories'" class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-2.5">
                <!-- Tùy chọn 00: Không có phụ kiện -->
                <button type="button" @click="accessories = '00'"
                        class="relative aspect-square rounded-xl border p-1 transition-all overflow-hidden flex flex-col items-center justify-center cursor-pointer"
                        :class="accessories === '00' ? 'border-[#019D91] ring-2 ring-[#019D91] bg-white dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-gray-300 dark:hover:border-neutral-700'">
                    <iconify-icon icon="lucide:ban" class="text-xl text-gray-400"></iconify-icon>
                    <span class="text-[10px] text-gray-500 font-medium mt-1">Không có</span>
                </button>

                <template x-for="i in partsCount.accessories" :key="i">
                    <button type="button" @click="accessories = pad(i)"
                            class="relative aspect-square rounded-xl border p-1 transition-all overflow-hidden flex items-center justify-center cursor-pointer"
                            :class="accessories === pad(i) ? 'border-[#019D91] ring-2 ring-[#019D91] bg-white dark:bg-neutral-800 shadow-sm' : 'border-gray-200 dark:border-neutral-800 bg-white dark:bg-neutral-900 hover:border-gray-300 dark:hover:border-neutral-700'">
                        <img :src="'/avatar-parts/accessories/Accessory' + pad(i) + '.svg'" alt="Accessory" class="w-full h-full object-contain pointer-events-none" loading="lazy" />
                    </button>
                </template>
            </div>

        </div>
    </div>
</div>
