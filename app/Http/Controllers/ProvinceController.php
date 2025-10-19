<?php

namespace App\Http\Controllers;

use App\Models\Province;
use App\Models\City;
use Illuminate\Http\Request;

class ProvinceController extends Controller
{
    public function index()
    {
        $provinces = Province::orderBy('name')->get();
        return response()->json($provinces);
    }

    public function show($id)
    {
        $province = Province::findOrFail($id);
        return response()->json($province);
    }

    public function getCities($provinceId)
    {
        $cities = City::where('province_id', $provinceId)->orderBy('name')->get();
        return response()->json($cities);
    }
}
