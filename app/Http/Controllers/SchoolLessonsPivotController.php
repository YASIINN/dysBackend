<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\Lessons;
use App\Models\SchoolLessonsClasesPivot;
use App\Models\SchoolLessonsPivot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class SchoolLessonsPivotController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function store(Request $request)
    {


        $valid = Validator::make($request->all(), [
            'dataList' => "required"
        ]);
        if ($valid->fails()) {
            return response()->json(['error' => $valid->errors()], 401);
        }
        $queryparse = $request->urlparse;
        $slpivot = new SchoolLessonsPivot();
        if ($queryparse) {
            $parser = $this->uriParser->queryparser($queryparse);
            $allSaved = false;
            $allContains = false;
            if (count($request->dataList) > 0) {
                foreach ($request->dataList as $item) {
                    $slpivot = new SchoolLessonsPivot();
                    $slpivots = SchoolLessonsPivot::where([
                        ["school_id", "=", $item['school_id']],
                        ["lesson_id", "=", $item['lesson_id']],
                    ])->get();
                    if (count($slpivots) > 0) {
                        $allContains = true;
                    } else {
                        $allContains = false;
                        $slpivot->school_id = $item['school_id'];
                        $slpivot->lesson_id = $item['lesson_id'];
                        if ($slpivot->save()) {
                            $allSaved = true;
                        } else {
                            $allSaved = false;
                        }
                    }
                }
                if ($allSaved || $allContains) {
                    return response()->json($slpivot);
                } else {
                    return response()->json([], 500);
                }
            } else {
                return response()->json([], 500);
            }


            /*   $scpivots = SchoolClasesPivot::where($parser)->get();
               if (count($scpivots) > 0) {
                   return response()->json([], 200);
               } else {
                   $slpivot->school_id = $request->school_id;
                   $slpivot->lesson_id = $request->lesson_id;
                   if ($slpivot->save()) {
                       return response()->json($slpivot, 200);
                   } else {
                       return response()->json([], 500);
                   }
               }*/
        } else {
            $allSaved = false;
            if (count($request->dataList) > 0) {
                foreach ($request->dataList as $item) {
                    $slpivot = new SchoolLessonsPivot();
                    $slpivot->school_id = $item['school_id'];
                    $slpivot->lesson_id = $item['lesson_id'];
                    if ($slpivot->save()) {
                        $allSaved = true;
                    } else {
                        $allSaved = false;
                    }
                }
                if ($allSaved) {
                    return response()->json($slpivot);
                } else {
                    return response()->json([], 500);
                }
            } else {
                return response()->json([], 500);
            }
        }

    }

    public function getall(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);

                $data = DB::table('school_lessons_pivots')
                    ->join('schools', 'school_lessons_pivots.school_id', '=', 'schools.id')
                    ->join('lessons', 'school_lessons_pivots.lesson_id', '=', 'lessons.id')
                    ->select("school_lessons_pivots.*", "schools.*", "lessons.*")
                    ->where($parser)
                    ->latest("slid")
                    ->get();
                foreach ($data as $item) {
                    if ($item->type == "0") {
                        $item->type = "Normal";
                    } else {
                        $item->type = "Seçmeli";
                    }
                }
                return response()->json($data, 200);
            } else {
                $data = DB::table('school_lessons_pivots')
                    ->join('schools', 'school_lessons_pivots.school_id', '=', 'schools.id')
                    ->join('lessons', 'school_lessons_pivots.lesson_id', '=', 'lessons.id')
                    ->select("school_lessons_pivots.*", "schools.*", "lessons.*")
                    ->latest("slid")
                    ->get();
                foreach ($data as $item) {
                    if ($item->type == "0") {
                        $item->type = "Normal";
                    } else {
                        $item->type = "Seçmeli";
                    }
                }
                return response()->json($data, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function getAllSL(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $response = array();
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('school_lessons_pivots')
                    ->join('schools', 'school_lessons_pivots.school_id', '=', 'schools.id')
                    ->join('lessons', 'school_lessons_pivots.lesson_id', '=', 'lessons.id')
                    ->select("school_lessons_pivots.*", "schools.*", "lessons.*")
                    ->where($parser)
                    ->latest("slid")->get();
                foreach ($data as $item) {
                    if ($item->type == "0") {
                        $item->type = "Normal";
                        $response[] = array(
                            "id" => $item->id,
                            "lName" => $item->lName,
                            "lCode" => $item->lCode,
                            "parent_id" => $item->parent_id,
                            "type" => $item->type
                        );
                    } else {
                        $subLessons = Lessons::where([
                            [
                                "parent_id", "=", $item->id
                            ]
                        ])->get();
                        $item->sub = $subLessons;
                        foreach ($subLessons as $sub) {
                            $response[] = array(
                                "id" => $sub->id,
                                "lName" => $sub->lName,
                                "lCode" => $sub->lCode,
                                "parent_id" => $sub->parent_id,
                                "type" => $sub->type
                            );
                        }
                        $item->type = "Seçmeli";
                    }
                }
                return response()->json($response, 200);
            } else {
                $data = DB::table('school_lessons_pivots')
                    ->join('schools', 'school_lessons_pivots.school_id', '=', 'schools.id')
                    ->join('lessons', 'school_lessons_pivots.lesson_id', '=', 'lessons.id')
                    ->select("school_lessons_pivots.*", "schools.*", "lessons.*")
                    ->latest("slid")->get();

                foreach ($data as $item) {
                    if ($item->type == "0") {
                        $item->type = "Normal";
                    } else {
                        $item->type = "Seçmeli";
                    }
                }
                return response()->json($data, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);

                $data = DB::table('school_lessons_pivots')
                    ->join('schools', 'school_lessons_pivots.school_id', '=', 'schools.id')
                    ->join('lessons', 'school_lessons_pivots.lesson_id', '=', 'lessons.id')
                    ->select("school_lessons_pivots.*", "schools.*", "lessons.*")
                    ->where($parser)
                    ->latest("slid")
                    ->paginate(2);
                foreach ($data as $item) {
                    if ($item->type == "0") {
                        $item->type = "Normal";
                    } else {
                        $item->type = "Seçmeli";
                    }
                }
                return response()->json($data, 200);
            } else {
                $data = DB::table('school_lessons_pivots')
                    ->join('schools', 'school_lessons_pivots.school_id', '=', 'schools.id')
                    ->join('lessons', 'school_lessons_pivots.lesson_id', '=', 'lessons.id')
                    ->select("school_lessons_pivots.*", "schools.*", "lessons.*")
                    ->latest("slid")
                    ->paginate(2);
                foreach ($data as $item) {
                    if ($item->type == "0") {
                        $item->type = "Normal";
                    } else {
                        $item->type = "Seçmeli";
                    }
                }
                return response()->json($data, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }


    public function destroy(Request $request, $id)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                DB::transaction(function () use ($parser, $id) {
                    $result = SchoolLessonsPivot::where("slid", $id)->delete();
                    if ($result) {
                        $res = SchoolLessonsClasesPivot::where($parser)->delete();
                        if ($res) {
                            return response()->json('Ürün başarıyla silindi.', 200);
                        } else {
                            return response()->json([], 500);
                        }
                    } else {
                        return response()->json([], 500);
                    }
                });
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}
