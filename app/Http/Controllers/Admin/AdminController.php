<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\User;
use App\Models\Gallery;
use App\Models\Stylist;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'users' => User::count(),
            'gallery' => Gallery::count(),
            'stylists' => Stylist::count(),
        ]);
    }
}
