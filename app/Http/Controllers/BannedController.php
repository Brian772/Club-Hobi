<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BannedController extends Controller
{
    public function index()
    {
        if (auth()->user()->status !== 'banned') {
            return redirect()->route('dashboard');
        }

        return view('auth.banned');
    }
}
