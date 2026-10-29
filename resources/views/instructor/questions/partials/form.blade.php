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
        <label for="question_text" class="block text-sm font-semibold text-gray-700 mb-1">Nội dung câu hỏi <span class="text-rose-500">*</span></label>
        <textarea id="question_text" name="question_text" rows="3" required maxlength="2000"
                  class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="VD: Laravel sử dụng mô hình kiến trúc nào?">{{ old('question_text', $question->question_text) }}</textarea>
        @error('question_text') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <p class="block text-sm font-semibold text-gray-700 mb-1">Các đáp án <span class="text-rose-500">*</span></p>
        <p class="text-xs text-gray-500 mb-3">Chọn nút tròn bên trái để đánh dấu đáp án đúng (2–6 đáp án).</p>

        <div class="space-y-2">
            <template x-for="(answer, index) in answers" :key="index">
                <div class="flex items-center gap-3 p-2 rounded-xl border transition"
                     :class="correct === index ? 'border-emerald-300 bg-emerald-50' : 'border-gray-200'">
                    <input type="radio" name="correct_answer" :value="index" :checked="correct === index" @change="correct = index"
                           class="w-5 h-5 text-emerald-600 border-gray-300 focus:ring-emerald-500" title="Đáp án đúng">
                    <span class="text-sm font-bold text-gray-500 w-5" x-text="String.fromCharCode(65 + index)"></span>
                    <input type="text" :name="'answers[' + index + ']'" x-model="answers[index]" required maxlength="1000"
                           placeholder="Nội dung đáp án"
                           class="flex-1 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <button type="button" @click="removeAnswer(index)" x-show="answers.length > 2"
                            class="p-1.5 rounded-lg text-gray-400 hover:text-rose-600 hover:bg-rose-50" title="Xóa đáp án">✕</button>
                </div>
            </template>
        </div>

        <button type="button" @click="addAnswer()" x-show="answers.length < 6"
                class="mt-3 px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-xs font-semibold hover:bg-gray-200">+ Thêm đáp án</button>

        @error('answers') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        @error('answers.*') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
        @error('correct_answer') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="explanation" class="block text-sm font-semibold text-gray-700 mb-1">Giải thích đáp án</label>
        <textarea id="explanation" name="explanation" rows="2" maxlength="2000"
                  class="w-full rounded-xl border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                  placeholder="Hiển thị cho học viên sau khi nộp bài (không bắt buộc)">{{ old('explanation', $question->explanation) }}</textarea>
        @error('explanation') <p class="text-xs text-rose-600 mt-1">{{ $message }}</p> @enderror
    </div>
</div>
