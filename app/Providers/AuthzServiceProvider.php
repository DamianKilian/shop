<?php

namespace App\Providers;

use App\Models\Order;
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
            return $this->hasPermission('admin', $user);
        });
        Gate::define('usersManagement', function (User $user) {
            return $this->hasPermission('usersManagement', $user);
        });
    }

    protected function hasPermission($name, $user)
    {
        $usersCount = Permission::whereName($name)->withCount(['users' => function (Builder $query) use ($user) {
            $query->whereId($user->id);
        }])->first()->users_count;
        return 0 !== $usersCount;
    }
}
