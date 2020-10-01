<?php

namespace App\Http\Controllers;

use App\Models\Marketplace;
use App\Models\Setting;
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
}
