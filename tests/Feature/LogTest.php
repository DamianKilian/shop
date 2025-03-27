<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertTrue;

class LogTest extends TestCase
{
    use RefreshDatabase;

    public function test_logJs(): void
    {
        $pastDate = now()->subYear();
        DB::table('logs_data')->where('data_key', 'log_js')->update([
            'error_num' => 0,
            'updated_at' => $pastDate,
        ]);
        Log::shouldReceive('emergency')
            ->once();

        $this->post('/api/log-js', [
            'message' => "err message"
        ]);
        $log = DB::table('logs_data')->where('data_key', 'log_js')->first();

        assertEquals(1, $log->error_num);
        assertTrue($pastDate->lt($log->updated_at));
    }

    public function test_logJs_log_not_created(): void
    {
        $nowSubHours = now()->subMinutes(1);
        DB::table('logs_data')->where('data_key', 'log_js')->update([
            'error_num' => 0,
            'updated_at' => $nowSubHours,
        ]);
        Log::shouldReceive('emergency')
            ->never();

        $this->post('/api/log-js', [
            'message' => "err message"
        ]);
        $log = DB::table('logs_data')->where('data_key', 'log_js')->first();

        assertEquals(1, $log->error_num);
        assertTrue($nowSubHours->lt($log->updated_at));
    }

    public function test_log_http(): void
    {
        $pastDate = now()->subYear();
        DB::table('logs_data')->where('data_key', 'log_http')->update([
            'error_num' => 0,
            'updated_at' => $pastDate,
        ]);
        Log::shouldReceive('emergency')
            ->once();

        $this->get('/non-existent-url');
        $log = DB::table('logs_data')->where('data_key', 'log_http')->first();

        assertEquals(1, $log->error_num);
        assertTrue($pastDate->lt($log->updated_at));
    }

    public function test_log_http_log_not_created(): void
    {
        $nowSubHours = now()->subMinutes(1);
        DB::table('logs_data')->where('data_key', 'log_http')->update([
            'error_num' => 0,
            'updated_at' => $nowSubHours,
        ]);
        Log::shouldReceive('emergency')
            ->never();

        $this->get('/non-existent-url');
        $log = DB::table('logs_data')->where('data_key', 'log_http')->first();

        assertEquals(1, $log->error_num);
        assertTrue($nowSubHours->lt($log->updated_at));
    }
}
