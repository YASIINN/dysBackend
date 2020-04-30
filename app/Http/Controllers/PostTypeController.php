<?php

namespace App\Http\Controllers;

use App\Models\PostType;
use Illuminate\Http\Request;

class PostTypeController extends Controller
{
    public function index()
    {
        try {
            return response()->json(PostType::where([['ptName', '!=', '-']])->get(), 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
