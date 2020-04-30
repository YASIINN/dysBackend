<?php

namespace App\Http\Controllers;

use App\Models\HomeWorkType;
use Illuminate\Http\Request;

class HomeWorkTypeController extends Controller
{
    public function index()
    {
        try {
            return response()->json(HomeWorkType::where([['hwtName', '!=', '-']])->get(), 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
