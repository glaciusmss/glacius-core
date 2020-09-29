<?php


namespace Tests\Feature\Http\Controllers;


use App\NotificationChannel;
use App\User;
use Tests\TestCase;

class ConnectedNotificationChannelControllerTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->has(NotificationChannel::factory()->count(3))
            ->create();

        $this->actingAs($this->user);
    }

    public function testGetConnectedNotificationChannelsSuccess()
    {
        $response = $this->getJson('/notification');

        $response->assertOk()
            ->assertJsonCount(3);
    }
}
