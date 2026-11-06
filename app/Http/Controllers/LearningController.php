<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LearningController extends Controller
{
    public function index($id)
    {
        
        return view('courses.learn'); 
    }
}
