<?php

namespace Tests;

use App\Models\User;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    private static $admin = null;

    protected function getAllUsersExceptAdmin()
    {
        return User::where('name', '!=', 'admin')->get();
    }

    public static function getAdmin()
    {
        if (null === self::$admin) {
            self::$admin = User::whereName('admin')->first();
        }
        return self::$admin;
    }
}
