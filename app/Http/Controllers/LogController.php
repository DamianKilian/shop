<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class LogController extends Controller
{
    public function __construct()
    {
        // $this->middleware('auth');
    }

    public function logJs(Request $request)
    {
        if(config('my.log_js')){
            $msg = 'Javascript error:' . json_encode($request->all());
            self::log($msg, 'log_js');
        }
    }

    public static function log($msg, $type)
    {
        $jsError = DB::table('logs_data')->where('data_key', $type)->first();
        $now = now();
        $diffInHours = $now->diffInHours($jsError->updated_at) * -1;
        if (1 < $diffInHours) {
            Log::emergency($msg);
        }
        DB::table('logs_data')->where('data_key', $type)->update([
            'error_num' => $jsError->error_num + 1,
            'updated_at' => $now
        ]);
    }

}
