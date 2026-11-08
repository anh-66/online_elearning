<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function toggle(Request $request, int|string $course_id): JsonResponse|RedirectResponse
    {
        $user = Auth::user();
        $course = Course::findOrFail($course_id);

        $wishlist = Wishlist::where('user_id', $user->id)->where('course_id', $course->id)->first();

        if ($wishlist) {
            $wishlist->delete();

            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'status' => 'success',
                    'wishlisted' => false,
                    'message' => 'Đã xóa khỏi danh sách yêu thích.',
                ]);
            }

            return redirect()->back()->with('success', 'Đã xóa khỏi danh sách yêu thích.');
        }

        Wishlist::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'wishlisted' => true,
                'message' => 'Đã thêm vào danh sách yêu thích.',
            ]);
        }

        return redirect()->back()->with('success', 'Đã thêm vào danh sách yêu thích.');
    }
}
