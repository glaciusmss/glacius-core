<?php


namespace Tests\Feature\Http\Controllers;


use App\Notifications\VerifyEmailNotification;
use App\Services\SocialLoginService;
use App\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Arr;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use WithFaker;

    public function testLoginSuccessWithCorrectCredentials()
    {
        \Event::fake();

        $user = User::factory()->create();

        $response = $this->postJson('/user/login', [
            'email' => $user->email,
            'password' => 'demo1234'
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'token'
            ]);

        \Event::assertDispatched(Login::class);
    }

    public function testLoginFailedWithIncorrectCredentials()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/user/login', [
            'email' => $user->email,
            'password' => 'demo12345'
        ]);

        $response->assertUnauthorized()
            ->assertJsonFragment([
                'message' => 'incorrect email or password'
            ]);
    }

    public function testGetSocialLoginRedirectUrlSuccess()
    {
        $socialLoginServiceMock = \Mockery::mock(SocialLoginService::class);
        $socialLoginServiceMock->shouldReceive('getProviderRedirectUrl')
            ->with('facebook')
            ->once()
            ->andReturn('http://example.com');

        $this->swap(SocialLoginService::class, $socialLoginServiceMock);

        $response = $this->postJson('/user/login/facebook');

        $response->assertOk()
            ->assertJsonFragment([
                'url' => 'http://example.com'
            ]);
    }

    public function testGetSocialLoginRedirectUrlFailedWithUnknownProvider()
    {
        $socialLoginServiceMock = \Mockery::mock(SocialLoginService::class);
        $socialLoginServiceMock->shouldReceive('getProviderRedirectUrl')
            ->with('unknown')
            ->once()
            ->andReturnFalse();

        $this->swap(SocialLoginService::class, $socialLoginServiceMock);

        $response = $this->postJson('/user/login/unknown');

        $response->assertNotFound()
            ->assertJsonFragment([
                'message' => 'provider not found'
            ]);
    }

    public function testSocialLoginCallbackSuccess()
    {
        \Event::fake();

        $user = User::factory()->create();

        $socialLoginServiceMock = \Mockery::mock(SocialLoginService::class);
        $socialLoginServiceMock->shouldReceive('handleProviderCallback')
            ->with('facebook')
            ->once()
            ->andReturn($user);

        $this->swap(SocialLoginService::class, $socialLoginServiceMock);

        $response = $this->postJson('/user/login/facebook/callback');

        $response->assertOk()
            ->assertJsonStructure([
                'token'
            ]);

        \Event::assertDispatched(Login::class);
    }

    public function testSocialLoginCallbackFailedWithUnknownProvider()
    {
        $socialLoginServiceMock = \Mockery::mock(SocialLoginService::class);
        $socialLoginServiceMock->shouldReceive('handleProviderCallback')
            ->with('unknown')
            ->once()
            ->andReturnFalse();

        $this->swap(SocialLoginService::class, $socialLoginServiceMock);

        $response = $this->postJson('/user/login/unknown/callback');

        $response->assertNotFound()
            ->assertJsonFragment([
                'message' => 'provider not found'
            ]);
    }

    public function testRegisterSuccess()
    {
        \Event::fake();

        $params = [
            'email' => $this->faker->safeEmail,
            'name' => $this->faker->name,
            'password' => $password = $this->faker->password,
            'confirm_password' => $password,
        ];

        $response = $this->postJson('/user/register', $params);

        $response->assertOk();

        \Event::assertDispatched(Registered::class);

        $this->assertDatabaseHas('users', Arr::except($params, ['password', 'confirm_password']));
    }

    public function testShouldSendVerificationEmailOnRegister()
    {
        \Notification::fake();

        $params = [
            'email' => $this->faker->safeEmail,
            'name' => $this->faker->name,
            'password' => $password = $this->faker->password,
            'confirm_password' => $password,
        ];

        $response = $this->postJson('/user/register', $params);

        $response->assertOk();

        \Notification::assertSentTo(
            [User::whereEmail($params['email'])->first()],
            VerifyEmailNotification::class
        );
    }

    public function testChangePasswordSuccess()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->patchJson('/user/password', [
            'old_password' => 'demo1234',
            'password' => $password = $this->faker->password,
            'confirm_password' => $password
        ]);

        $response->assertNoContent();

        // login should fail after change password
        $loginResponse = $this->postJson('/user/login', [
            'email' => $user->email,
            'password' => 'demo1234'
        ]);

        $loginResponse->assertUnauthorized();
    }

    public function testChangePasswordFailedIfOldPasswordIsIncorrect()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->patchJson('/user/password', [
            'old_password' => 'demo12345',
            'password' => $password = $this->faker->password,
            'confirm_password' => $password
        ]);

        $response->assertUnauthorized()
            ->assertJsonFragment([
                'message' => 'incorrect password'
            ]);
    }

    public function testLogoutSuccess()
    {
        \Event::fake();

        $user = User::factory()->create();
        $token = \JWTAuth::fromUser($user);

        $response = $this->postJson('/user/logout', compact('token'));

        $response->assertNoContent();

        \Event::assertDispatched(Logout::class);

        $this->assertFalse($this->isAuthenticated());
    }

    public function testGetMeSuccess()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson('/user/me');

        $response->assertOk()
            ->assertJsonFragment([
                'name' => $user->name,
                'email' => $user->email
            ]);
    }

    public function testVerifyEmailVerificationSuccess()
    {
        \Event::fake();

        $user = User::factory()->withoutEmailVerified()->create();
        $hash = sha1($user->email);

        $response = $this->getJson('/user/email/verify/' . $user->id . '/' . $hash);

        $response->assertRedirect();

        $user->refresh();

        $this->assertNotNull($user->email_verified_at);

        \Event::assertDispatched(Verified::class);
    }

    public function testVerifyEmailVerificationFailIfUserIdNotFound()
    {
        $response = $this->getJson('/user/email/verify/100/randomhash');

        $response->assertForbidden()
            ->assertJsonFragment([
                'message' => 'this action is prohibited'
            ]);
    }

    public function testVerifyEmailVerificationFailIfIncorrectHash()
    {
        $user = User::factory()->withoutEmailVerified()->create();

        $response = $this->getJson('/user/email/verify/' . $user->id . '/randomhash');

        $response->assertForbidden()
            ->assertJsonFragment([
                'message' => 'this action is prohibited'
            ]);
    }

    public function testVerifyEmailVerificationFailIfUserEmailIsVerified()
    {
        $user = User::factory()->create();
        $hash = sha1($user->email);

        $response = $this->getJson('/user/email/verify/' . $user->id . '/' . $hash);

        $response->assertStatus(409)
            ->assertJsonFragment([
                'message' => 'user already verified'
            ]);
    }

    public function testResendEmailVerificationSuccess()
    {
        \Notification::fake();

        $user = User::factory()->withoutEmailVerified()->create();

        $this->actingAs($user);

        $response = $this->postJson('/user/email/resend');

        $response->assertStatus(202);

        \Notification::assertSentTo(
            [$user],
            VerifyEmailNotification::class
        );
    }

    public function testResendEmailVerificationFailIfEmailVerfied()
    {
        \Notification::fake();

        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/user/email/resend');

        $response->assertStatus(409)
            ->assertJsonFragment([
                'message' => 'user already verified'
            ]);

        \Notification::assertNothingSent();
    }
}
