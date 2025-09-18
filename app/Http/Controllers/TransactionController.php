<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;

class TransactionController extends Controller
{
    public function transactions(Request $request)
    {
        Gate::authorize('access-token', $request->header('my-access-token'));
        $costTo = Carbon::parse($request->cost_to);
        $costToAddMonth = $costTo->copy()->addMonth();
        $sumAmount = Transaction::where('created_at', '>', $costTo)
            ->where('created_at', '<=', $costToAddMonth)
            ->sum('amount');
        return response()->json([
            'sumAmount' => $sumAmount,
        ]);
    }
}
