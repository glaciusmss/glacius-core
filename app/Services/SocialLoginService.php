<?php
/**
 * Created by PhpStorm.
 * User: Neoson Lam
 * Date: 10/21/2019
 * Time: 10:59 AM.
 */

namespace App\Services;


use App\Enums\SocialProvider;
use App\SocialLogin;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Str;
use Laravel\Socialite\Contracts\Factory as SocialiteFactory;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialLoginService
{
    protected $socialite;

    public function __construct(SocialiteFactory $socialite)
    {
        $this->socialite = $socialite;
    }

    public function getProviderRedirectUrl($provider)
    {
        if (!$provider = $this->validateProvider($provider)) {
            return false;
        }

        return $this->socialite->driver($provider->value)->stateless()->redirect()->getTargetUrl();
    }

    public function handleProviderCallback($provider)
    {
        if (!$provider = $this->validateProvider($provider)) {
            return false;
        }

        return $this->createOrGetUserFromProvider(
            $provider,
            $this->socialite->driver($provider->value)->stateless()->user()
        );
    }

    protected function validateProvider($provider)
    {
        if (!SocialProvider::hasValue($provider)) {
            return false;
        }

        return SocialProvider::getInstance($provider);
    }

    protected function createOrGetUserFromProvider(SocialProvider $provider, ProviderUser $providerUser)
    {
        $socialLoginRecord = SocialLogin::whereProvider($provider->value)
            ->whereProviderUserId($providerUser->getId())
            ->first();

        if ($socialLoginRecord) {
            return $socialLoginRecord->user;
        }

        $userRecord = User::whereEmail($providerUser->getEmail())->first();

        if (!$userRecord) {
            $userRecord = User::create([
                'name' => $providerUser->getName(),
                'email' => $providerUser->getEmail(),
                'password' => Str::random()
            ]);

            //fire a registered event here due to user has been created
            event(new Registered($userRecord));
        }

        $userRecord->socialLogins()->create([
            'provider_user_id' => $providerUser->getId(),
            'provider' => $provider
        ]);

        return $userRecord;
    }
}
