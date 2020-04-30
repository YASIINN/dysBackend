<?php

namespace App\Http\Controllers;

use App\Models\HomeWorkCategory;
use Illuminate\Http\Request;
use Validator;

class HomeWorkCategoryController extends Controller
{
    public function index()
    {
        try {
            return HomeWorkCategory::all();
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function delete($id)
    {
        try {
            if (HomeWorkCategory::destroy($id)) {
                return response()->json("Success", 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);

        }
    }

    public function update(Request $request, $id)
    {
        try {
            $valid = Validator::make($request->all(), [
                'hcatname' => 'required|unique:home_work_category,hCatName,' . $id
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            }
            $hcat = HomeWorkCategory::find($id);
            $hcat->hCatName = $request->hcatname;
            if ($hcat->update()) {
                return response()->json($hcat, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);

        }
    }

    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'hcatname' => 'required|unique:home_work_category,hCatName'
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            }
            $hcat = new HomeWorkCategory();
            $hcat->hCatName = $request->hcatname;
            if ($hcat->save()) {
                return response()->json($hcat, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
