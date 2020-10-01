<?php

namespace Tests\Feature\Http\Controllers;

use App\User;
use App\UserProfile;
use Tests\TestCase;

class UserProfileControllerTest extends TestCase
{
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()
            ->has(UserProfile::factory())
            ->create();

        $this->actingAs($this->user);
    }

    public function testGetUserProfileSuccess()
    {
        $response = $this->getJson('/user_profile');

        $response->assertOk()
            ->assertJsonFragment([
                'phone_number' => $this->user->userProfile->phone_number,
                'gender' => (string) $this->user->userProfile->gender,
                'date_of_birth' => $this->user->userProfile->date_of_birth->format('Y-m-d'),
            ]);
    }

    public function testUpdateUserProfileSuccess()
    {
        $response = $this->patchJson('/user_profile/'.$this->user->userProfile->id, [
            'phone_number' => '0123456789',
        ]);

        $response->assertNoContent();

        $this->user->unsetRelations();

        $this->assertEquals('0123456789', $this->user->userProfile->phone_number);
    }
}
