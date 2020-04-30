<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\UsersSchools;
use App\Models\UsersSchoolsClases;
use App\Models\UsersSchoolsClasesBranches;
use App\Models\UsersSchoolsLessons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class UsersSchoolsController extends Controller
{

    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }
    public function delete(Request $request)
    {
        try {
            $userid = $request->userid;
            $schoolid = $request->schoolid;
            DB::transaction(function () use ($userid, $schoolid) {
                $result = UsersSchools::where([
                    ['users_id', "=", $userid],
                    ['school_id', "=", $schoolid]
                ])->delete();
                if ($result) {
                    $res = UsersSchoolsLessons::where([
                        ['user_id', "=", $userid],
                        ['school_id', "=", $schoolid]
                    ])->delete();
                    if ($res) {
                        $res = UsersSchoolsClases::where([
                            ['users_id', "=", $userid],
                            ['school_id', "=", $schoolid]
                        ])->delete();
                        if ($res) {
                            $res = UsersSchoolsClasesBranches::where([
                                ['user_id', "=", $userid],
                                ['school_id', "=", $schoolid]
                            ])->delete();
                            if ($res) {
                                return response()->json('Ürün başarıyla silindi.', 200);
                            } else {
                                return response()->json([], 500);
                            }
                        } else {
                            return response()->json([], 500);
                        }
                    } else {
                        return response()->json([], 500);
                    }
                } else {
                    return response()->json([], 500);
                }
            });
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'userschoollist' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }
            $allSaved = false;
            foreach ($request->userschoollist as $item) {
                $userschool = new UsersSchools();
                $isHave = UsersSchools::where([
                    ["users_id", "=", $item['userid']],
                    ["school_id", "=", $item['schoolid']],
                ])->get();
                if (count($isHave) > 0) {
                    $allSaved = true;
                } else {
                    $userschool->users_id  = $item['userid'];
                    $userschool->school_id = $item['schoolid'];
                    if ($userschool->save()) {
                        $allSaved = true;
                    } else {
                        $allSaved = false;
                    }
                }
            }
            if ($allSaved) {
                return response()->json($userschool, 200);
            } else {
                return response()->json("allsaveddan hata", 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
