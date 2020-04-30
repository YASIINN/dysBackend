<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\ActivityUserClases;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ActivityUserClasesController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function delete(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $deleted = ActivityUserClases::where($parser)->delete();
                if ($deleted) {
                    return response()->json($deleted, 200);
                } else {
                    return response()->json([], 500);
                }
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 200);
        }
    }


    public function index(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('activity_user_clases')
                    ->join('activities', 'activity_user_clases.activity_id', '=', 'activities.id')
                    ->join('users', 'activity_user_clases.user_id', '=', 'users.id')
                    ->join('periods', 'activity_user_clases.period_id', '=', 'periods.id')
                    ->join('grades', 'activity_user_clases.grade_id', '=', 'grades.id')
                    ->select("activity_user_clases.*", "activities.*", "users.*", "periods.*", "grades.*")
                    ->where($parser)
                    ->latest("activity_user_clases.id")
                    ->paginate(2);
                return response()->json($data, 200);
            } else {
                $data = DB::table('activity_user_clases')
                    ->join('activities', 'activity_user_clases.activity_id', '=', 'activities.id')
                    ->join('users', 'activity_user_clases.user_id', '=', 'users.id')
                    ->join('periods', 'activity_user_clases.period_id', '=', 'periods.id')
                    ->join('grades', 'activity_user_clases.grade_id', '=', 'grades.id')
                    ->select("activity_user_clases.*", "activities.*", "users.*", "periods.*", "grades.*")
                    ->latest("activity_user_clases.id")
                    ->paginate(2);
                return response()->json($data, 200);
            }
        } catch (\Error $e) {
            return response()->json([], 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                "userList" => 'required'
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }
            $allsaved = false;
            foreach ($request->userList as $item) {
                $userActivity = new ActivityUserClases();
                $isHave = ActivityUserClases::where([
                    ["activity_id", "=", $item['activityid']],
                    ["user_id", "=", $item['userid']],
                    ["grade_id", "=", $item['gradeid']],
                    ["period_id", "=", $item['periodid']]
                ])->get();
                if (count($isHave) > 0) {
                    $allsaved = true;
                } else {
                    $userActivity->activity_id = $item['activityid'];
                    $userActivity->user_id = $item['userid'];
                    $userActivity->grade_id = $item['gradeid'];
                    $userActivity->period_id = $item['periodid'];
                    if ($userActivity->save()) {
                        $allsaved = true;
                        //return response()->json($userActivity, 200);
                    } else {
                        $allsaved = false;
                        return response()->json([], 500);
                    }
                }
            }
            if ($allsaved) {
                return response()->json($userActivity, 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
