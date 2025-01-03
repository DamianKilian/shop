<?php

namespace App\Http\Controllers;

use App\Models\Footer;
use Illuminate\Http\Request;

class AdminPanelFooterController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function footer()
    {
        return view('adminPanel.footer', []);
    }

    public function saveFooter(Request $request)
    {
        $html = Footer::whereDataKey('html')->first();
        if ($html) {
            $html->update([
                'value' => $request->footerHtml,
            ]);
        } else {
            $html = Footer::create([
                'data_key' => 'html',
                'value' => $request->footerHtml,
            ]);
        }
    }

    public function getFooter(Request $request)
    {
        $html = Footer::whereDataKey('html')->first();
        return response()->json([
            'footerHtml' => $html->value,
        ]);
    }
}