<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function category(Request $request, $slug)
    {
        $category = Category::where('slug', $slug)->first();
        return view('category', [
            'category' => $category,
            'categories' => Category::with('children')->get(),
        ]);
    }

    public function index()
    {
        return view('home', [
            'categories' => Category::with('children')->get(),
        ]);
    }
}
