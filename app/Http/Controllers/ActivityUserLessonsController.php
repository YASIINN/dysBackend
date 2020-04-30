<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\ActivityUserLessons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class ActivityUserLessonsController extends Controller
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
                $deleted = ActivityUserLessons::where($parser)->delete();
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
                $data = DB::table('activity_user_lessons')
                    ->join('activities', 'activity_user_lessons.activity_id', '=', 'activities.id')
                    ->join('users', 'activity_user_lessons.user_id', '=', 'users.id')
                    ->join('periods', 'activity_user_lessons.period_id', '=', 'periods.id')
                    ->join('lessons', 'activity_user_lessons.lesson_id', '=', 'lessons.id')
                    ->select("activity_user_lessons.*", "activities.*", "users.*", "periods.*", "lessons.*")
                    ->where($parser)
                    ->latest("activity_user_lessons.id")
                    ->paginate(2);
                return response()->json($data, 200);
            } else {
                $data = DB::table('activity_user_lessons')
                    ->join('activities', 'activity_user_lessons.activity_id', '=', 'activities.id')
                    ->join('users', 'activity_user_lessons.user_id', '=', 'users.id')
                    ->join('periods', 'activity_user_lessons.period_id', '=', 'periods.id')
                    ->join('lessons', 'activity_user_lessons.lesson_id', '=', 'lessons.id')
                    ->select("activity_user_lessons.*", "activities.*", "users.*", "periods.*", "lessons.*")
                    ->latest("activity_user_lessons.id")
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
                $userActivity = new ActivityUserLessons();
                $isHave = ActivityUserLessons::where([
                    ["activity_id", "=", $item['activityid']],
                    ["user_id", "=", $item['userid']],
                    ["lesson_id", "=", $item['lessonid']],
                    ["period_id", "=", $item['periodid']]
                ])->get();
                if (count($isHave) > 0) {
                    $allsaved = true;
                } else {
                    $userActivity->activity_id = $item['activityid'];
                    $userActivity->user_id = $item['userid'];
                    $userActivity->lesson_id = $item['lessonid'];
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
