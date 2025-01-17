<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AuthzServiceProvider extends ServiceProvider
{

    public function register(): void {}

    public function boot(): void
    {
        Gate::define('admin', function (User $user) {
            $usersCount = Permission::whereName('admin')->withCount(['users' => function (Builder $query) use ($user) {
                $query->whereId($user->id);
            }])->first()->users_count;
            return 0 !== $usersCount;
        });
    }
}
