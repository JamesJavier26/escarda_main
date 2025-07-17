<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yajra\Address\Entities\Barangay;
use Yajra\Address\Entities\City;

class AddressController extends Controller
{
 public function getCitiesByProvince(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'province_id' => 'required|string|exists:provinces,province_id'
        ]);

        $cities = City::where('province_id', $request->province_id)
                     ->orderBy('name')
                     ->get(['id','city_id', 'name']);

        return response()->json($cities);
    }

    public function getBarangaysByCity(Request $request)
    {
        $request->validate([
            'city_id' => 'required|string|exists:cities,city_id'
        ]);

        $barangays = Barangay::where('city_id', $request->city_id)
                           ->orderBy('name')
                           ->get(['id','code', 'name']);

        return response()->json($barangays);
    }
    public function getCity(City $city)
    {
        return response()->json($city);
    }

    public function getBarangay(Barangay $barangay)
    {
        return response()->json($barangay);
    }
}
