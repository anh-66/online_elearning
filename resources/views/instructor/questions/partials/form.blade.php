@php
    $answerTexts = $question->exists
        ? $question->answers->pluck('answer_text')->all()
        : ['', '', '', ''];
    $correctIndex = $question->exists
        ? $question->answers->search(fn ($answer) => $answer->is_correct)
        : null;
    $correctValue = old('correct_answer', $correctIndex === false ? null : $correctIndex);
@endphp

<div class="space-y-5"
     x-data="{
        answers: @js(array_values(old('answers', $answerTexts))),
        correct: @js($correctValue === null || $correctValue === '' ? null : (int) $correctValue),
        addAnswer() {
            if (this.answers.length < 6) { this.answers.push(''); }
        },
        removeAnswer(index) {
            if (this.answers.length <= 2) { return; }
            this.answers.splice(index, 1);
            if (this.correct === index) { this.correct = null; }
            else if (this.correct !== null && this.correct > index) { this.correct--; }
        },
     }">
    <div>
        <label for="question_text" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-1">Nội dung câu hỏi <span class="text-rose-500">*</span></label>
        <textarea id="question_text" name="question_text" rows="3" required maxlength="2000"
                  class="w-full rounded-xl border-gray-300 dark:border-neutral-700 focus:border-brand-600 focus:ring-brand-600"
                  placeholder="VD: Laravel sử dụng mô hình kiến trúc nào?">{{ old('question_text', $question->question_text) }}</textarea>
        @error('question_text') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <p class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-1">Các đáp án <span class="text-rose-500">*</span></p>
        <p class="text-xs text-gray-500 dark:text-neutral-400 mb-3">Chọn nút tròn bên trái để đánh dấu đáp án đúng (2–6 đáp án).</p>

        <div class="space-y-2">
            <template x-for="(answer, index) in answers" :key="index">
                <div class="flex items-center gap-3 p-2.5 rounded-xl border transition"
                     :class="correct === index ? 'border-neutral-900 dark:border-white bg-neutral-50 dark:bg-white/[0.04] ring-1 ring-neutral-900/10 dark:ring-white/10' : 'border-gray-200 dark:border-neutral-800'">
                    
                    {{-- Animate UI Radio Indicator --}}
                    <label class="relative flex items-center cursor-pointer group shrink-0 p-1"
                           @click="correct = index">
                        <input type="radio" name="correct_answer" :value="index" :checked="correct === index"
                               class="sr-only" title="Đánh dấu đáp án đúng">
                        <span data-slot="radio-group-item"
                              class="relative size-4 rounded-full border flex items-center justify-center shrink-0 aspect-square outline-none transition-all duration-200 group-hover:border-neutral-400"
                              :class="correct === index 
                                    ? 'border-neutral-900 dark:border-white bg-neutral-900 dark:bg-white' 
                                    : 'border-neutral-300 dark:border-neutral-600 bg-white dark:bg-neutral-900/50'">
                            <span data-slot="radio-group-indicator" class="relative z-10 flex size-4 items-center justify-center pointer-events-none">
                                <span class="size-2 rounded-full bg-white dark:bg-neutral-950 transition-all duration-200 ease-[cubic-bezier(0.22,1,0.36,1)] pointer-events-none"
                                      :class="correct === index ? 'scale-100 opacity-100' : 'scale-0 opacity-0'"></span>
                            </span>
                        </span>
                    </label>

                    <button type="button" @click="correct = index" class="text-sm font-bold w-5 cursor-pointer text-left transition-colors"
                            :class="correct === index ? 'text-neutral-950 dark:text-white font-extrabold' : 'text-gray-500 dark:text-neutral-400'"
                            x-text="String.fromCharCode(65 + index)"></button>
                    <input type="text" :name="'answers[' + index + ']'" x-model="answers[index]" required maxlength="1000"
                           placeholder="Nội dung đáp án"
                           class="flex-1 rounded-lg border-gray-300 dark:border-neutral-700 text-sm focus:border-brand-600 focus:ring-brand-600">
                    <button type="button" @click="removeAnswer(index)" x-show="answers.length > 2"
                            class="p-1.5 rounded-lg text-gray-400 dark:text-neutral-500 hover:text-rose-600 dark:hover:text-rose-300 hover:bg-rose-50 dark:hover:bg-rose-500/15" title="Xóa đáp án">✕</button>
                </div>
            </template>
        </div>

        <button type="button" @click="addAnswer()" x-show="answers.length < 6"
                class="mt-3 px-3 py-1.5 rounded-lg bg-gray-100 dark:bg-neutral-800 text-gray-700 dark:text-neutral-200 text-xs font-semibold hover:bg-gray-200 dark:hover:bg-neutral-800">+ Thêm đáp án</button>

        @error('answers') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
        @error('answers.*') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
        @error('correct_answer') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="explanation" class="block text-sm font-semibold text-gray-700 dark:text-neutral-200 mb-1">Giải thích đáp án</label>
        <textarea id="explanation" name="explanation" rows="2" maxlength="2000"
                  class="w-full rounded-xl border-gray-300 dark:border-neutral-700 focus:border-brand-600 focus:ring-brand-600"
                  placeholder="Hiển thị cho học viên sau khi nộp bài (không bắt buộc)">{{ old('explanation', $question->explanation) }}</textarea>
        @error('explanation') <p class="text-xs text-rose-600 dark:text-rose-400 mt-1">{{ $message }}</p> @enderror
    </div>
</div>
