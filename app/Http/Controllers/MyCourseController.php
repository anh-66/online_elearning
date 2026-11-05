<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class MyCourseController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $enrolledCourses = $user->enrolledCourses;
        $wishlistCourses = $user->wishlistCourses;

        return view('my-courses', compact('enrolledCourses', 'wishlistCourses'));
    }
}