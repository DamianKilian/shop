<?php

namespace App\Services;

use Illuminate\Support\Facades\Schema;

class AppService
{
    public static function isSqlite($migrationClass): bool
    {
        return 'sqlite' === Schema::connection($migrationClass->getConnection())
            ->getConnection()
            ->getPdo()
            ->getAttribute(\PDO::ATTR_DRIVER_NAME);
    }
}
