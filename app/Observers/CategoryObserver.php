<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Spatie\ResponseCache\Facades\ResponseCache;

class CategoryObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        ResponseCache::clear();
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        ResponseCache::clear();
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        ResponseCache::clear();
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        ResponseCache::clear();
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        ResponseCache::clear();
    }
}
