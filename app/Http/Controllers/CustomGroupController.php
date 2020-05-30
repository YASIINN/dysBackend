<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CustomGroup;
use Validator;

class CustomGroupController extends Controller
{
    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'name' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 401);
            }
            $cgs = CustomGroup::where("name", $request->name)->get();
            if (count($cgs) > 0) {
                    return response()->json(["message"=>"Bu kayıt sistemde mevcuttur."], 200);
            } else {
                    $cg = new CustomGroup();
                    $cg->name = $request->name;
                    if ($cg->save()) {
                        return response()->json($cg, 201);
                    } else {
                        return response()->json([], 500);
                    }
                }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $cg = CustomGroup::find($id);
            $cg->name = $request->name;
            if ($cg->update()) {
                return response()->json($cg, 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            if (CustomGroup::destroy($id)) {
                return response()->json('Success.');
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
    public function customgroup(Request $request){
        try {
         if($request->search){
             $schools = CustomGroup::with("group")->where("name", "like", $request->txt."%")->has("group")->latest()->paginate(5);
         } else {
             $schools = CustomGroup::with("group")->has("group")->latest()->paginate(5);
         }
         if (!$schools) {
             return [];
         }
         $map = $schools->map(function ($content) {
             $data["id"] = $content->group->id;
             $data["name"] = $content->group->name;
             $data["code"] = $content->group->code;
             $data["groupable_id"] = $content->group->groupable_id;
             $data["groupable_type"] = $content->group->groupable_type;
             return $data;
         });
         $collection = collect($schools);
         $collection->forget('data');
         $schools = $collection->all();
         $schools["data"] = $map;  
         return response()->json($schools, 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 200);
        }
     }
}
