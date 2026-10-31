<?php

namespace App\View\Components;

use App\Models\User;
use Illuminate\View\Component;
use Illuminate\View\View;

class GuestLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     */
    public function render(): View
    {
        return view('layouts.guest', [
            'instructorCount' => User::where('role', User::ROLE_INSTRUCTOR)->count(),
            'studentCount' => User::where('role', User::ROLE_STUDENT)->count(),
        ]);
    }
}
