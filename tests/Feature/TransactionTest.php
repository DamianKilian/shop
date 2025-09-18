<?php

namespace Tests\Feature;

use App\Models\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use function PHPUnit\Framework\assertTrue;

class TransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_transactions(): void
    {
        $subWeeks7 = now()->subWeeks(7);
        $subWeeks6 = now()->subWeeks(6);
        $subWeeks4 = now()->subWeeks(4);
        $subWeeks3 = now()->subWeeks(3);
        $subWeeks1 = now()->subWeeks(1);
        Transaction::factory()->create([
            'amount' => 1,
            'created_at' => $subWeeks7,
        ]);
        Transaction::factory()->create([
            'amount' => 40,
            'created_at' => $subWeeks4
        ]);
        Transaction::factory()->create([
            'amount' => 40,
            'created_at' => $subWeeks3
        ]);
        Transaction::factory()->create([
            'amount' => 1,
            'created_at' => $subWeeks1
        ]);

        $response = $this->withHeaders([
            'my-access-token' => config('my.access_token'),
        ])->get(route('api-transactions', [
            'cost_to' => $subWeeks6->toString()
        ]));

        assertTrue(80 === $response['sumAmount']);
    }

    public function test_transactions_unauthorized(): void
    {
        $subWeeks6 = now()->subWeeks(6);

        $response = $this->get(route('api-transactions', [
            'cost_to' => $subWeeks6->toString()
        ]));
        $responseInvalidToken = $this->withHeaders([
            'my-access-token' => 'invalidToken',
        ])->get(route('api-transactions', [
            'cost_to' => $subWeeks6->toString()
        ]));

        $response->assertForbidden();
        $responseInvalidToken->assertForbidden();
    }
}
