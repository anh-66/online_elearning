<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Wishlist;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function toggle($course_id)
    {
        $user = Auth::user();
        $course = Course::findOrFail($course_id);
        
        $wishlist = Wishlist::where('user_id', $user->id)->where('course_id', $course->id)->first();
        
        if ($wishlist) {
            $wishlist->delete();
            return redirect()->back()->with('success', 'Đã xóa khỏi danh sách yêu thích.');
        } else {
            Wishlist::create([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ]);
            return redirect()->back()->with('success', 'Đã thêm vào danh sách yêu thích.');
        }
    }
}