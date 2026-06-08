<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class LocationController extends Controller
{
    public function reverseGeocode(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $latitude = $request->latitude;
        $longitude = $request->longitude;

        $response = Http::withHeaders([
            'User-Agent' => 'Lashedia Reservation App',
        ])->get('https://nominatim.openstreetmap.org/reverse', [
            'format' => 'json',
            'lat' => $latitude,
            'lon' => $longitude,
            'zoom' => 18,
            'addressdetails' => 1,
        ]);

        if (!$response->successful()) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil alamat otomatis.',
            ], 500);
        }

        $data = $response->json();

        return response()->json([
            'success' => true,
            'full_address' => $data['display_name'] ?? 'Koordinat pelanggan: ' . $latitude . ', ' . $longitude,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
    }
}
