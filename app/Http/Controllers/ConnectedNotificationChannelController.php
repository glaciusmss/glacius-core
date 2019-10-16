<?php

namespace App\Http\Controllers;

use App\ConnectedNotificationChannel;
use App\Http\Resources\ConnectedNotificationChannelResource;
use Illuminate\Http\Request;

class ConnectedNotificationChannelController extends Controller
{
    public function index()
    {
        return ConnectedNotificationChannelResource::collection(
            $this->getUser()->notificationChannels
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ConnectedNotificationChannel  $connectedNotificationChannel
     * @return \Illuminate\Http\Response
     */
    public function show(ConnectedNotificationChannel $connectedNotificationChannel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ConnectedNotificationChannel  $connectedNotificationChannel
     * @return \Illuminate\Http\Response
     */
    public function edit(ConnectedNotificationChannel $connectedNotificationChannel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ConnectedNotificationChannel  $connectedNotificationChannel
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConnectedNotificationChannel $connectedNotificationChannel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ConnectedNotificationChannel  $connectedNotificationChannel
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConnectedNotificationChannel $connectedNotificationChannel)
    {
        //
    }
}
