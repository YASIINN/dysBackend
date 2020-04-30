<?php

namespace App\Http\Controllers;

use App\Models\UserUTypes;
use Illuminate\Http\Request;
use Validator;

class UserUTypesController extends Controller
{
    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'user_id' => 'required',
                'users_types_id' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }

            $userutype = new UserUTypes();
            $userutype->users_id = $request->user_id;
            $userutype->user_types_id = $request->users_types_id;
            if ($userutype->save()) {
                return response()->json($userutype, 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
