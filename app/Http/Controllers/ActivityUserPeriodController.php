<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\ActivityUserClases;
use App\Models\ActivityUserLessons;
use App\Models\ActivityUserPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ActivityUserPeriodController extends Controller
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
            $periodid = $request->periodid;
            DB::transaction(function () use ($userid, $activityid, $periodid) {
                $result = ActivityUserPeriod::where([
                    ['activity_id', "=", $activityid],
                    ['user_id', "=", $userid],
                    ['period_id', "=", $periodid]
                ])->delete();
                if ($result) {
                    $result = ActivityUserClases::where([
                        ['activity_id', "=", $activityid],
                        ['user_id', "=", $userid],
                        ['period_id', "=", $periodid]
                    ])->delete();
                    if ($result) {
                        $result = ActivityUserLessons::where([
                            ['activity_id', "=", $activityid],
                            ['user_id', "=", $userid],
                            ['period_id', "=", $periodid]
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
                "activityid" => 'required',
                "periodid" => 'required'
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }
            $userActivity = new ActivityUserPeriod();
            $isHave = ActivityUserPeriod::where([
                ["activity_id", "=", $request->activityid],
                ["user_id", "=", $request->userid],
                ["period_id", "=", $request->periodid]
            ])->get();
            if (count($isHave) > 0) {
            } else {
                $userActivity->activity_id = $request->activityid;
                $userActivity->user_id = $request->userid;
                $userActivity->period_id = $request->periodid;
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
                $data = DB::table('activity_user_periods')
                    ->join('activities', 'activity_user_periods.activity_id', '=', 'activities.id')
                    ->join('users', 'activity_user_periods.user_id', '=', 'users.id')
                    ->join('periods', 'activity_user_periods.period_id', '=', 'periods.id')
                    ->select("activity_user_periods.*", "activities.*", "users.*", "periods.*")
                    ->where($parser)
                    ->latest("activity_user_periods.id")
                    ->paginate(2);
                return response()->json($data, 200);
            } else {
                $data = DB::table('activity_user_periods')
                    ->join('activities', 'activity_user_periods.activity_id', '=', 'activities.id')
                    ->join('users', 'activity_user_periods.user_id', '=', 'users.id')
                    ->join('periods', 'activity_user_periods.period_id', '=', 'periods.id')
                    ->select("activity_user_periods.*", "activities.*", "users.*", "periods.*")
                    ->latest("activity_user_periods.id")
                    ->paginate(2);
                return response()->json($data, 200);
            }
        } catch (\Error $e) {
            return response()->json([], 500);
        }
    }
}
