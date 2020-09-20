<?php

namespace App\Http\Controllers;

use App\Marketplace;
use App\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        return $this->getShop()
            ->marketplaces()
            ->with('pivot.settings')
            ->get()
            ->mapWithKeys(function (Marketplace $marketplace) {
                return [$marketplace->name => $marketplace->pivot->getAllSettings()];
            });
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    public function show(Request $request, $identifier)
    {
        $collection = $request->input('collection');

        $marketplace = $this->getShop()
            ->marketplaces()
            ->whereName($identifier)
            ->firstOrFail();

        return $marketplace->pivot->getAllSettingFromCollection($collection);
    }

    public function update(Request $request, $identifier)
    {
        $marketplace = $this->getShop()
            ->marketplaces()
            ->whereName($identifier)
            ->firstOrFail();

        foreach ($request->input('data') as $collection => $data) {
            $marketplace->pivot->updateMultipleSettings($data, $collection);
        }

        return response()->noContent();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Setting $setting
     * @return \Illuminate\Http\Response
     */
    public function destroy(Setting $setting)
    {
        //
    }
}
