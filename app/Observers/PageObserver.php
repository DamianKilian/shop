<?php

namespace App\Observers;

use App\Models\Page;
use Illuminate\Contracts\Events\ShouldHandleEventsAfterCommit;
use Spatie\ResponseCache\Facades\ResponseCache;

class PageObserver implements ShouldHandleEventsAfterCommit
{
    /**
     * Handle the Page "created" event.
     */
    public function created(Page $page): void
    {
        ResponseCache::clear();
    }

    /**
     * Handle the Page "updated" event.
     */
    public function updated(Page $page): void
    {
        ResponseCache::clear();
    }

    /**
     * Handle the Page "deleted" event.
     */
    public function deleted(Page $page): void
    {
        ResponseCache::clear();
    }

    /**
     * Handle the Page "restored" event.
     */
    public function restored(Page $page): void
    {
        ResponseCache::clear();
    }

    /**
     * Handle the Page "force deleted" event.
     */
    public function forceDeleted(Page $page): void
    {
        ResponseCache::clear();
    }
}
