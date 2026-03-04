<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TokenTestEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_not_found_when_no_user_exists(): void
    {
        $response = $this->getJson('/api/token-test');

        $response
            ->assertNotFound()
            ->assertJson([
                'message' => 'No users found. Create a user first.',
            ]);
    }

    public function test_it_returns_a_plain_text_token_when_a_user_exists(): void
    {
        User::factory()->create();

        $response = $this->getJson('/api/token-test');

        $response->assertOk();

        $content = $response->getContent();

        $this->assertIsString($content);
        $this->assertNotEmpty($content);
        $this->assertStringContainsString('|', $content);
    }
}
