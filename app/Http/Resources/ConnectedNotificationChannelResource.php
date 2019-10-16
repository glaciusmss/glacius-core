<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;

class ConnectedNotificationChannelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $notificationData = parent::toArray($request);

        $notificationData['created_at'] = $this->pivot->created_at->toDateTimeString();
        $notificationData['updated_at'] = $this->pivot->updated_at->toDateTimeString();

        return Arr::except($notificationData, ['pivot']);
    }
}
