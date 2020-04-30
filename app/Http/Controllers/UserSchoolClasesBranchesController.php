<?php

namespace App\Http\Controllers;

use App\Models\UsersSchoolsClasesBranches;
use Validator;
use Illuminate\Http\Request;

class UserSchoolClasesBranchesController extends Controller
{
    public function destroy($id)
    {
        try {
            $data = UsersSchoolsClasesBranches::where("uscbid", $id);
            if ($data->delete()) {
                return response()->json('Ürün başarıyla silindi.', 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function store(Request $request)
    {
        //   try {
        $valid = Validator::make($request->all(), [
            'userscblist' => 'required',
        ]);
        if ($valid->fails()) {
            return response()->json(['error' => $valid->errors()], 400);
        }
        $allSaved = false;
        foreach ($request->userscblist as $item) {
            $userschoolclasesbranches = new UsersSchoolsClasesBranches();
            $isHave = UsersSchoolsClasesBranches::where([
                ["user_id", "=", $item['userid']],
                ["school_id", "=", $item['schoolid']],
                ["clases_id", "=", $item['classid']],
                ["branches_id", "=", $item['branchid']],
            ])->get();
            if (count($isHave) > 0) {
                if ($isHave[0]['type'] == $item['type']) {
                    $allSaved = true;
                } else {
                    $updateType = UsersSchoolsClasesBranches::where([
                        ["user_id", "=", $item['userid']],
                        ["school_id", "=", $item['schoolid']],
                        ["clases_id", "=", $item['classid']],
                        ["branches_id", "=", $item['branchid']],
                    ])->update(["type" => $item['type']]);
                    if ($updateType) {
                        $allSaved = true;
                    } else {
                        $allSaved = false;
                        /*TODO*/
                        /*$allSaved = false;*/
                    }

                }

            } else {
                $userschoolclasesbranches->user_id = $item['userid'];
                $userschoolclasesbranches->school_id = $item['schoolid'];
                $userschoolclasesbranches->clases_id = $item['classid'];
                $userschoolclasesbranches->branches_id = $item['branchid'];
                $userschoolclasesbranches->type = $item['type'];
                if ($userschoolclasesbranches->save()) {
                    $allSaved = true;
                } else {
                    $allSaved = false;
                }
            }
        }
        if ($allSaved) {
            return response()->json($userschoolclasesbranches, 200);
        } else {
            return response()->json([], 500);
        }
        /*      } catch (\Exception $e) {
            return response()->json($e, 500);
        }*/
    }
}
