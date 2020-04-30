<?php

namespace App\Http\Controllers;

use App\Models\PType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PTypeController extends Controller
{
    public function getPTypes()
    {
        return PType::all();
    }

    public function store(Request $request)
    {
    }

    public function index()
    {
        try {
            return response()->json(PType::all(), 200);
            // if (Cache::has("sptypes")) {
            //     $programtypes = Cache::get("sptypes");
            //     return response()->json($programtypes, 200);
            // } else {
            //     Cache::set("sptypes", PType::all());
            //     $programtypes = Cache::get("sptypes");
            //     return response()->json($programtypes, 200);
            // }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}
