<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLocationRequest;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'lat' => 'required|numeric|between:-90,90',
            'long' => 'required|numeric|between:-180,180',
            'deviceId' => 'required|string|max:255',
            'date' => 'sometimes|date'
        ]);

        $validated['date'] = $validated['date'] ?? now();

        $location = Location::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Location stored successfully',
            'data' => $location
        ], 201);
    }

    /**
     * Get latest coordinate for a device
     */
    public function getLatest(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'deviceId' => 'required|string'
        ]);

        $location = Location::where('deviceId', $validated['deviceId'])
            ->orderBy('date', 'desc')
            ->first();

        if (!$location) {
            return response()->json([
                'success' => false,
                'message' => 'No location found for this device'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'lat' => $location->lat,
                'long' => $location->long,
                'date' => $location->date,
                'deviceId' => $location->deviceId,
                'google_maps_url' => $location->google_maps_url
            ]
        ]);
    }
}
