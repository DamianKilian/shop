<?php

namespace App\Services;

use App\Models\Suggestion;

class SearchService
{
    public static function addSuggestion($request, $products)
    {
        if (0 === $products->count() || !$request->searchValue) {
            return;
        }
        $suggestion = Suggestion::where('suggestion', $request->searchValue)->first();
        if ($suggestion) {
            $suggestion->last_used = now();
            $suggestion->save();
        } else {
            Suggestion::create([
                'suggestion' => $request->searchValue,
                'last_used' => now(),
            ]);
        }
    }
}
