<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
     public function dashboard()
    {
        $recentMembers = Member::latest()->take(5)->get();

        return view('dashboard', compact('recentMembers'));
    }
}
