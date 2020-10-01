<?php

namespace App\Http\Controllers;

use App\Http\Resources\ConnectedNotificationChannelResource;
use App\Models\ConnectedNotificationChannel;
use Illuminate\Http\Request;

class ConnectedNotificationChannelController extends Controller
{
    public function index()
    {
        return ConnectedNotificationChannelResource::collection(
            $this->getUser()->notificationChannels
        );
    }
}
