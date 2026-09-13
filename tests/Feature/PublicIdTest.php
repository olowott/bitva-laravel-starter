<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicIdTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_receives_public_id_when_created(): void
    {
        $user = User::factory()->create();

        $this->assertNotNull($user->public_id);

        $this->assertSame(
            26,
            strlen($user->public_id)
        );
    }

    public function test_users_receive_unique_public_ids(): void
    {
        $first = User::factory()->create();
        $second = User::factory()->create();

        $this->assertNotSame(
            $first->public_id,
            $second->public_id
        );
    }

    public function test_user_uses_public_id_for_route_binding(): void
    {
        $user = User::factory()->create();

        $this->assertSame(
            'public_id',
            $user->getRouteKeyName()
        );

        $this->assertSame(
            $user->public_id,
            $user->getRouteKey()
        );
    }
}
