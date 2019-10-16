<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\ChangePasswordRequest;
use App\Http\Requests\User\LoginRequest;
use App\Http\Requests\User\RegisterRequest;
use App\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class UserController extends Controller
{
    public function __construct(AuthManager $auth)
    {
        parent::__construct($auth);
        $this->middleware('auth:api')->only('logout');
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!$token = $this->auth->attempt($credentials)) {
            throw new UnauthorizedHttpException('email-password', 'incorrect email or password');
        }

        event(new Login($this->auth->guard(), $this->auth->user(), null));

        return response()->json(compact('token'));
    }

    public function register(RegisterRequest $request)
    {
        $registerData = Arr::except($request->validated(), 'confirm_password');

        $createdUser = User::create($registerData);

        event(new Registered($createdUser));

        return response()->json($createdUser);
    }

    public function password(ChangePasswordRequest $request)
    {
        //validate against existing password
        if (!\Hash::check($request->input('old_password'), $this->getUser()->password)) {
            throw new UnauthorizedHttpException('password', 'incorrect password');
        }

        $this->getUser()->update([
            'password' => $request->input('password')
        ]);

        return response()->noContent();
    }

    public function logout()
    {
        $guard = $this->auth->guard();
        $user = $this->auth->user();

        $this->auth->logout();

        event(new Logout($guard, $user));

        return response()->noContent();
    }

    public function me()
    {
        return response()->json($this->auth->user());
    }

    public function verifyEmailVerification(Request $request)
    {
        if (!$user = User::find($request->route('id'))) {
            throw new AccessDeniedHttpException('this action is prohibited');
        }

        if (!hash_equals((string)$request->route('hash'), sha1($user->getEmailForVerification()))) {
            throw new AccessDeniedHttpException('this action is prohibited');
        }

        if ($user->hasVerifiedEmail()) {
            throw new ConflictHttpException('user already verified');
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        return response()->redirectTo(
            config('app.frontend_url') . '/login'
        );
    }

    public function resendEmailVerification(Request $request)
    {
        if ($this->auth->user()->hasVerifiedEmail()) {
            throw new ConflictHttpException('user already verified');
        }

        $this->auth->user()->sendEmailVerificationNotification();

        return response()->json([], 202);
    }
}
