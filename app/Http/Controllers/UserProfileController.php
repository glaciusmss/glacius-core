<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserProfile\UpdateRequest;
use App\Http\Resources\UserProfileResource;
use App\UserProfile;

class UserProfileController extends Controller
{
    public function index()
    {
        return new UserProfileResource($this->getUser()->userProfile);
    }

    public function show(UserProfile $userProfile)
    {
        //
    }

    public function update(UpdateRequest $request, UserProfile $userProfile)
    {
        $userProfileData = $request->validated();

        $userProfile->update($userProfileData);

        return response()->noContent();
    }
}
