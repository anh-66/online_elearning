<?php

namespace App\Http\Controllers\Instructor\Concerns;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

trait ReordersSiblings
{
    /**
     * Đổi chỗ $item với phần tử liền trước/sau trong danh sách anh em,
     * sau đó đánh lại cột `order` liên tục từ 1 để tránh trùng thứ tự.
     *
     * @param  Collection<int, Model>  $siblings
     */
    protected function moveWithinSiblings(Collection $siblings, Model $item, string $direction): bool
    {
        $siblings = $siblings->values();
        $index = $siblings->search(fn (Model $sibling) => $sibling->is($item));
        $swapIndex = $direction === 'up' ? $index - 1 : $index + 1;

        if ($index === false || ! $siblings->has($swapIndex)) {
            return false;
        }

        $items = $siblings->all();
        [$items[$index], $items[$swapIndex]] = [$items[$swapIndex], $items[$index]];

        foreach ($items as $position => $sibling) {
            if ($sibling->order !== $position + 1) {
                $sibling->update(['order' => $position + 1]);
            }
        }

        return true;
    }
}
