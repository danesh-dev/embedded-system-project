<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationViewController extends Controller
{
    public function show(Request $request, $deviceId = null)
    {
        $deviceId = $deviceId ?? $request->get('deviceId');
        $location = null;

        if ($deviceId) {
            $location = Location::where('deviceId', $deviceId)
                ->orderBy('date', 'desc')
                ->first();
        }

        return view('location', compact('location', 'deviceId'));
    }
}
