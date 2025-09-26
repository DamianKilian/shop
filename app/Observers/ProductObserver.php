<?php

namespace App\Observers;

use App\Models\Product;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Spatie\ResponseCache\Facades\ResponseCache;

class ProductObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        ResponseCache::clear();
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        ResponseCache::clear();
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        ResponseCache::clear();
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        ResponseCache::clear();
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        ResponseCache::clear();
    }
}
