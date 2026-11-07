{{-- Khung Hỏi đáp & Bình luận bài học (TV4) --}}
@php
    $commentsList = $lesson->comments()
        ->whereNull('parent_id')
        ->with(['user', 'replies.user'])
        ->latest()
        ->get();
    $courseInstructorId = $lesson->chapter->course->instructor_id ?? null;
@endphp

<section id="qa" class="mt-10 pt-8 border-t border-[#E5E5EA] dark:border-[#30363d]"
         x-data="{ replyTo: null, editingId: null }">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-lg font-bold text-[#1D1D1F] dark:text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-[#019D91]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <span>Hỏi đáp &amp; Thảo luận</span>
            </h3>
            <p class="text-xs text-[#86868B] dark:text-slate-400 mt-1">Đặt câu hỏi để được giảng viên và các bạn học viên khác hỗ trợ.</p>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-slate-100 dark:bg-[#21262d] text-[#1D1D1F] dark:text-slate-300 border border-[#E5E5EA] dark:border-[#30363d]">
            {{ $lesson->comments()->count() }} bình luận
        </span>
    </div>

    {{-- Form gửi câu hỏi mới --}}
    @auth
        <form action="{{ route('comments.store', $lesson) }}" method="POST" class="mb-8">
            @csrf
            <div class="rounded-2xl border border-[#E5E5EA] dark:border-[#30363d] bg-white dark:bg-[#161b22] p-4 focus-within:border-[#019D91] transition shadow-sm">
                <label for="new_comment_content" class="sr-only">Nội dung câu hỏi</label>
                <textarea id="new_comment_content" name="content" rows="3" required maxlength="2000"
                          placeholder="Bạn có thắc mắc gì về bài học này? Hãy nhập câu hỏi tại đây..."
                          class="w-full bg-transparent text-sm text-[#1D1D1F] dark:text-[#e6edf3] placeholder-[#86868B] dark:placeholder-[#7d8590] border-0 focus:ring-0 resize-none"></textarea>
                <div class="flex items-center justify-between pt-3 border-t border-[#F0F0F2] dark:border-[#21262d] mt-2">
                    <span class="text-[11px] text-[#86868B] dark:text-slate-500">Tối đa 2000 ký tự</span>
                    <button type="submit"
                            class="px-5 py-2 text-xs font-semibold rounded-xl bg-[#019D91] hover:bg-[#008b80] text-white transition shadow-sm">
                        Gửi câu hỏi
                    </button>
                </div>
            </div>
            @error('content')
                <p class="mt-1.5 text-xs text-rose-500 dark:text-rose-400">{{ $message }}</p>
            @enderror
        </form>
    @else
        <div class="mb-8 p-4 rounded-2xl bg-white dark:bg-[#161b22] border border-[#E5E5EA] dark:border-[#30363d] text-xs text-[#86868B] dark:text-slate-400 flex items-center justify-between shadow-sm">
            <span>Đăng nhập để tham gia hỏi đáp cùng cộng đồng.</span>
            <a href="{{ route('login') }}" class="text-[#019D91] hover:underline font-semibold">Đăng nhập &rarr;</a>
        </div>
    @endauth

    {{-- Danh sách bình luận --}}
    <div class="space-y-4">
        @forelse($commentsList as $comment)
            <article id="comment-{{ $comment->id }}"
                     class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#161b22] border border-[#E5E5EA] dark:border-[#30363d] space-y-3 transition shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-slate-900 text-white text-xs font-semibold flex items-center justify-center overflow-hidden shrink-0 border border-slate-700">
                            @if($comment->user?->avatar_url)
                                <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" class="w-full h-full object-cover">
                            @else
                                <span>{{ $comment->user?->initialLetter() ?? '?' }}</span>
                            @endif
                        </div>
                        <div>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-base font-bold text-[#1D1D1F] dark:text-white">{{ $comment->user?->name ?? 'Người dùng' }}</span>
                                @if($comment->user_id === $courseInstructorId)
                                    <span class="text-xs uppercase font-bold px-2 py-0.5 rounded-full bg-[#019D91]/15 text-[#018076] dark:bg-[#019D91]/20 dark:text-[#02b3a5] border border-[#019D91]/30">
                                        Giảng viên
                                    </span>
                                @endif
                            </div>
                            <span class="text-xs text-[#86868B] dark:text-slate-400">{{ $comment->created_at->locale('vi')->diffForHumans() }}</span>
                        </div>
                    </div>

                    {{-- Thao tác Sửa / Xóa cho chủ bình luận --}}
                    @if(auth()->id() === $comment->user_id)
                        <div class="flex items-center gap-2 text-xs sm:text-sm">
                            <button type="button" @click="editingId = (editingId === {{ $comment->id }} ? null : {{ $comment->id }})"
                                    class="text-[#86868B] hover:text-[#019D91] dark:text-slate-400 dark:hover:text-[#02b3a5] transition font-medium">
                                Sửa
                            </button>
                            <span class="text-slate-300 dark:text-slate-700">·</span>
                            <form action="{{ route('comments.destroy', $comment) }}" method="POST"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa bình luận này? Các câu trả lời cũng sẽ bị xóa.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-500 dark:text-rose-400 hover:underline font-medium">Xóa</button>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- Nội dung bình luận hoặc Form Sửa --}}
                <div x-show="editingId !== {{ $comment->id }}">
                    <p class="text-sm sm:text-base text-[#1D1D1F] dark:text-[#e6edf3] leading-relaxed whitespace-pre-line">{{ $comment->content }}</p>
                    <div class="pt-2 flex items-center gap-4 text-xs sm:text-sm">
                        @auth
                            <button type="button" @click="replyTo = (replyTo === {{ $comment->id }} ? null : {{ $comment->id }})"
                                    class="text-[#019D91] hover:text-[#008b80] dark:text-[#02b3a5] font-semibold flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                                <span>Trả lời</span>
                            </button>
                        @endauth
                    </div>
                </div>

                {{-- Form Sửa Inline --}}
                @if(auth()->id() === $comment->user_id)
                    <form x-show="editingId === {{ $comment->id }}" x-cloak
                          action="{{ route('comments.update', $comment) }}" method="POST" class="space-y-2">
                        @csrf
                        @method('PUT')
                        <textarea name="content" rows="2" required maxlength="2000"
                                  class="w-full text-sm rounded-xl bg-[#F9F9FB] dark:bg-[#0d1117] border border-[#E5E5EA] dark:border-slate-700 text-[#1D1D1F] dark:text-gray-100 focus:border-[#019D91] focus:ring-[#019D91]">{{ old('content', $comment->content) }}</textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="editingId = null" class="px-3 py-1.5 text-xs sm:text-sm text-[#86868B] dark:text-slate-400 hover:text-[#1D1D1F] dark:hover:text-white">Hủy</button>
                            <button type="submit" class="px-4 py-1.5 text-xs sm:text-sm font-semibold rounded-lg bg-[#019D91] text-white hover:bg-[#008b80]">Cập nhật</button>
                        </div>
                    </form>
                @endif

                {{-- Form Trả lời Inline --}}
                @auth
                    <form x-show="replyTo === {{ $comment->id }}" x-cloak
                          action="{{ route('comments.store', $lesson) }}" method="POST" class="mt-3 pl-4 border-l-2 border-[#019D91] space-y-2">
                        @csrf
                        <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                        <textarea name="content" rows="2" required maxlength="2000"
                                  placeholder="Nhập câu trả lời của bạn..."
                                  class="w-full text-sm rounded-xl bg-[#F9F9FB] dark:bg-[#0d1117] border border-[#E5E5EA] dark:border-slate-700 text-[#1D1D1F] dark:text-gray-100 placeholder-[#86868B] dark:placeholder-slate-500 focus:border-[#019D91] focus:ring-[#019D91]"></textarea>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="replyTo = null" class="px-3 py-1.5 text-xs sm:text-sm text-[#86868B] dark:text-slate-400 hover:text-[#1D1D1F] dark:hover:text-white">Hủy</button>
                            <button type="submit" class="px-4 py-1.5 text-xs sm:text-sm font-semibold rounded-lg bg-[#019D91] text-white hover:bg-[#008b80]">Gửi trả lời</button>
                        </div>
                    </form>
                @endauth

                {{-- Danh sách câu trả lời con (Replies) --}}
                @if($comment->replies->isNotEmpty())
                    <div class="mt-3 pl-4 sm:pl-6 border-l-2 border-[#E5E5EA] dark:border-slate-800 space-y-3 pt-2">
                        @foreach($comment->replies as $reply)
                            <div id="comment-{{ $reply->id }}" class="space-y-1.5 bg-[#F9F9FB] dark:bg-[#0d1117]/70 p-3 sm:p-3.5 rounded-xl border border-[#E5E5EA] dark:border-slate-800/80"
                                 x-data="{ editingReply: false }">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-slate-800 text-xs font-bold text-white flex items-center justify-center shrink-0">
                                            @if($reply->user?->avatar_url)
                                                <img src="{{ $reply->user->avatar_url }}" alt="{{ $reply->user->name }}" class="w-full h-full object-cover rounded-full">
                                            @else
                                                <span>{{ $reply->user?->initialLetter() ?? '?' }}</span>
                                            @endif
                                        </div>
                                        <span class="text-sm font-bold text-[#1D1D1F] dark:text-white">{{ $reply->user?->name ?? 'Người dùng' }}</span>
                                        @if($reply->user_id === $courseInstructorId)
                                            <span class="text-[10px] uppercase font-bold px-1.5 py-0.2 rounded-full bg-[#019D91]/15 text-[#018076] dark:bg-[#019D91]/20 dark:text-[#02b3a5] border border-[#019D91]/30">
                                                Giảng viên
                                            </span>
                                        @endif
                                        <span class="text-xs text-[#86868B] dark:text-slate-500">{{ $reply->created_at->locale('vi')->diffForHumans() }}</span>
                                    </div>

                                    @if(auth()->id() === $reply->user_id)
                                        <div class="flex items-center gap-2 text-xs">
                                            <button type="button" @click="editingReply = !editingReply" class="text-[#86868B] hover:text-[#019D91] dark:text-slate-400 dark:hover:text-[#02b3a5] font-medium">Sửa</button>
                                            <span class="text-slate-300 dark:text-slate-700">·</span>
                                            <form action="{{ route('comments.destroy', $reply) }}" method="POST"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn xóa phản hồi này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-500 dark:text-rose-400 hover:underline font-medium">Xóa</button>
                                            </form>
                                        </div>
                                    @endif
                                </div>

                                <div x-show="!editingReply">
                                    <p class="text-sm text-[#1D1D1F]/90 dark:text-gray-200 leading-relaxed whitespace-pre-line">{{ $reply->content }}</p>
                                </div>

                                @if(auth()->id() === $reply->user_id)
                                    <form x-show="editingReply" x-cloak action="{{ route('comments.update', $reply) }}" method="POST" class="space-y-2 mt-2">
                                        @csrf
                                        @method('PUT')
                                        <textarea name="content" rows="2" required maxlength="2000"
                                                  class="w-full text-sm rounded-xl bg-white dark:bg-slate-900 border border-[#E5E5EA] dark:border-slate-700 text-[#1D1D1F] dark:text-gray-100 focus:border-[#019D91] focus:ring-[#019D91]">{{ old('content', $reply->content) }}</textarea>
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="editingReply = false" class="px-3 py-1 text-xs text-[#86868B] dark:text-slate-400 hover:text-[#1D1D1F] dark:hover:text-white">Hủy</button>
                                            <button type="submit" class="px-3.5 py-1 text-xs font-semibold rounded-lg bg-[#019D91] text-white hover:bg-[#008b80]">Lưu</button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </article>
        @empty
            <div class="p-8 text-center rounded-2xl bg-white/60 dark:bg-[#161b22]/40 border border-dashed border-[#E5E5EA] dark:border-[#30363d]">
                <p class="text-sm font-medium text-[#86868B] dark:text-slate-400">Chưa có câu hỏi nào cho bài học này.</p>
                <p class="text-xs text-[#86868B]/80 dark:text-slate-500 mt-1">Hãy là người đầu tiên đặt câu hỏi để cùng thảo luận nhé!</p>
            </div>
        @endforelse
    </div>
</section>
