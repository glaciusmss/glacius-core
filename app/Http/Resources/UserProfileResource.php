<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class UserProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $userProfileData = parent::toArray($request);
        $userProfileData['gender'] = (string) $userProfileData['gender'];

        return Arr::except($userProfileData, 'user_id');
    }
}
