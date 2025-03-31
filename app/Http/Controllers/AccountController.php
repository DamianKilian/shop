<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function addresses(Request $request)
    {
        return view('account.addresses');
    }
}
