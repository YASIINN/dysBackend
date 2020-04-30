<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\Lessons;
use App\Models\UsersSchoolsLessons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class UsersSchoolsLessonsController extends Controller
{
    //
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function destroy($id)
    {
        try {
            if (UsersSchoolsLessons::where("uslid", $id)->delete()) {
                return response()->json('Ürün başarıyla silindi.', 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getSchoolUserLesson(Request $request)
    {

        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $userdata = array();
                $getLesson = Lessons::where("parent_id", $request->lessonid)->get();
                if (count($getLesson) > 0) {
                    foreach ($getLesson as $item) {
                        $data = DB::table('users_schools_lessons')
                            ->join('schools', 'users_schools_lessons.school_id', '=', 'schools.id')
                            ->join('users', 'users_schools_lessons.user_id', '=', 'users.id')
                            ->join('lessons', 'users_schools_lessons.lesson_id', '=', 'lessons.id')
                            ->select("users_schools_lessons.*", "schools.*", "users.*", "lessons.*")
                            ->where([["school_id", "=", $request->schoolid], ["lesson_id", "=", $item->id]])
                            ->latest("uslid")
                            ->get();
                        foreach ($data as $subitem) {
                            array_push($userdata, $subitem);
                        }
                    }
                    $collection = collect($userdata);
                    $unique = $collection->unique('user_id');
                    return $unique->values()->all();
                } else {
                    $parser = $this->uriParser->queryparser($queryparse);
                    $data = DB::table('users_schools_lessons')
                        ->join('schools', 'users_schools_lessons.school_id', '=', 'schools.id')
                        ->join('users', 'users_schools_lessons.user_id', '=', 'users.id')
                        ->join('lessons', 'users_schools_lessons.lesson_id', '=', 'lessons.id')
                        ->select("users_schools_lessons.*", "schools.*", "users.*", "lessons.*")
                        ->where($parser)
                        ->latest("uslid")
                        ->get();
                    return response()->json($data, 200);
                }

            } else {
                $data = DB::table('users_schools_lessons')
                    ->join('schools', 'users_schools_lessons.school_id', '=', 'schools.id')
                    ->join('users', 'users_schools_lessons.user_id', '=', 'users.id')
                    ->join('lessons', 'users_schools_lessons.lesson_id', '=', 'lessons.id')
                    ->select("users_schools_lessons.*", "schools.*", "users.*", "lessons.*")
                    ->latest("uslid")
                    ->get();
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
                'usersllist' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }
            $allSaved = false;
            foreach ($request->usersllist as $item) {
                $userschoollesson = new UsersSchoolsLessons();
                $isHave = UsersSchoolsLessons::where([
                    ["user_id", "=", $item['userid']],
                    ["school_id", "=", $item['schoolid']],
                    ["lesson_id", "=", $item['lessonid']]
                ])->get();
                if (count($isHave) > 0) {
                    $allSaved = true;
                } else {
                    $userschoollesson->user_id = $item['userid'];
                    $userschoollesson->school_id = $item['schoolid'];
                    $userschoollesson->lesson_id = $item['lessonid'];
                    if ($userschoollesson->save()) {
                        $allSaved = true;
                    } else {
                        $allSaved = false;
                    }
                }
            }
            if ($allSaved) {
                return response()->json($userschoollesson, 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
