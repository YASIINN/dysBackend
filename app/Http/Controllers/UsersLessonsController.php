<?php

namespace App\Http\Controllers;

use App\Models\UsersLessons;
use Illuminate\Http\Request;
use Validator;

class UsersLessonsController extends Controller
{
    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'userslessonslist' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }
            $allSaved = false;
            foreach ($request->userslessonslist as $item) {
                $userlessons = new UsersLessons();
                $isHave = UsersLessons::where([
                    ["users_id", "=", $item['userid']],
                    ["lesson_id", "=", $item['lessonid']]
                ])->get();
                if (count($isHave) > 0) {
                    $allSaved = true;
                } else {
                    $userlessons->users_id = $item['userid'];
                    $userlessons->lesson_id = $item['lessonid'];
                    if ($userlessons->save()) {
                        $allSaved = true;
                    } else {
                        $allSaved = false;
                    }
                }
            }
            if ($allSaved) {
                return response()->json($userlessons, 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
