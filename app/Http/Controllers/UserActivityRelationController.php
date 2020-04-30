<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\Activity;
use App\Models\Lessons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserActivityRelationController extends Controller
{
    //
    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function getAll()
    {
        try {
            $activity = Activity::all();
            return response()->json($activity, 200);
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getActivityWeekLesson(Request $request)
    {
        $queryparse = $request->urlparse;
        if ($queryparse) {
            $parser = $this->uriParser->queryparser($queryparse);
            $response = array();
            $data = DB::table('activity_period_lesson')
                ->join('activities', 'activity_period_lesson.activity_id', '=', 'activities.id')
                ->join('periods', 'activity_period_lesson.period_id', '=', 'periods.id')
                ->join('lessons', 'activity_period_lesson.lessons_id', '=', 'lessons.id')
                ->select("activity_period_lesson.*", "activities.*", "periods.*", "lessons.*")
                ->where($parser)
                ->get();
            foreach ($data as $item) {
                $subLessons = Lessons::where([
                    [
                        "parent_id", "=", $item->lessons_id
                    ]
                ])->get();
                if (count($subLessons) > 0) {
                    foreach ($subLessons as $sub) {
                        $sub->lessons_id=$sub->id;
                        array_push($response, $sub);
                    }
                } else {
                    array_push($response, $item);
                }
                $item->type = "Seçmeli";
            }
            return response()->json($response, 200);
        } else {
            $response = array();
            $data = DB::table('activity_period_lesson')
                ->join('activities', 'activity_period_lesson.activity_id', '=', 'activities.id')
                ->join('periods', 'activity_period_lesson.period_id', '=', 'periods.id')
                ->join('lessons', 'activity_period_lesson.lessons_id', '=', 'lessons.id')
                ->select("activity_period_lesson.*", "activities.*", "periods.*", "lessons.*")
                ->get();
            foreach ($data as $item) {
                $subLessons = Lessons::where([
                    [
                        "parent_id", "=", $item->lessons_id
                    ]
                ])->get();
                if (count($subLessons) > 0) {
                    foreach ($subLessons as $sub) {
                        //lessons_id
                        $sub->lessons_id=$sub->id;
                        array_push($response, $sub);
                    }
                } else {
                    array_push($response, $item);
                }
                $item->type = "Seçmeli";
            }
            return response()->json($response, 200);
        }
    }

    public function getActivityInfo(Request $request)
    {
        $queryparse = $request->urlparse;
        if ($queryparse) {
            $parser = $this->uriParser->queryparser($queryparse);
            $data = DB::table('activity_period')
                ->join('activities', 'activity_period.activity_id', '=', 'activities.id')
                ->join('periods', 'activity_period.period_id', '=', 'periods.id')
                ->join('grades', 'activity_period.grade_id', '=', 'grades.id')
                ->select("activity_period.*", "activities.*", "periods.*", "grades.*")
                ->where($parser)
                ->get();
            return response()->json($data, 200);
        } else {
            $data = DB::table('activity_period')
                ->join('activities', 'activity_period.activity_id', '=', 'activities.id')
                ->join('periods', 'activity_period.period_id', '=', 'periods.id')
                ->join('grades', 'activity_period.grade_id', '=', 'grades.id')
                ->select("activity_period.*", "activities.*", "periods.*", "grades.*")
                ->get();
            return response()->json($data, 200);
        }
    }
}
