<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\ActivityUser;
use App\Models\ActivityUserClases;
use App\Models\ActivityUserLessons;
use App\Models\ActivityUserPeriod;
use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ActivityUserController extends Controller
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
            $activityid = $request->activityid;
            DB::transaction(function () use ($userid, $activityid) {
                $result = ActivityUser::where([
                    ["activity_id", "=", $activityid],
                    ["users_id", "=", $userid]
                ])->delete();
                if ($result) {
                    $result = ActivityUserClases::where([
                        ["activity_id", "=", $activityid],
                        ["user_id", "=", $userid]
                    ])->delete();
                    if ($result) {
                        $result = ActivityUserLessons::where([
                            ["activity_id", "=", $activityid],
                            ["user_id", "=", $userid]
                        ])->delete();
                        if ($result) {
                            $result = ActivityUserPeriod::where([
                                ['activity_id', "=", $activityid],
                                ['user_id', "=", $userid],

                            ])->delete();
                            if ($result) {
                                return response()->json($result, 200);
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
                'userid' => 'required',
                "activityid" => 'required'
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }
            $userActivity = new ActivityUser();
            $isHave = ActivityUser::where([
                ["activity_id", "=", $request->activityid],
                ["users_id", "=", $request->userid]
            ])->get();
            if (count($isHave) > 0) {
            } else {
                $userActivity->activity_id = $request->activityid;
                $userActivity->users_id = $request->userid;
                if ($userActivity->save()) {
                    return response()->json($userActivity, 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function index(Request $request, $id)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $user = Users::find($id);
                $activity = $user->activities()->where($parser)->latest()->paginate(2);
                return response()->json($activity, 200);
            } else {
                $user = Users::find($id);
                $activity = $user->activities()->latest()->paginate(2);
                return response()->json($activity, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
