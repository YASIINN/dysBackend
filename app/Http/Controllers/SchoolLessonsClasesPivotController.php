<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\SchoolLessonsClasesPivot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class SchoolLessonsClasesPivotController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function index(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('school_lessons_clases_pivots')
                    ->join('schools', 'school_lessons_clases_pivots.school_id', '=', 'schools.id')
                    ->join('clases', 'school_lessons_clases_pivots.clases_id', '=', 'clases.id')
                    ->join('lessons', 'school_lessons_clases_pivots.lesson_id', '=', 'lessons.id')
                    ->select("school_lessons_clases_pivots.*", "schools.*", "clases.*", "lessons.*")
                    ->where($parser)
                    ->latest("slcid")
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
                $data = DB::table('school_lessons_clases_pivots')
                    ->join('schools', 'school_lessons_clases_pivots.school_id', '=', 'schools.id')
                    ->join('clases', 'school_lessons_clases_pivots.clases_id', '=', 'clases.id')
                    ->join('lessons', 'school_lessons_clases_pivots.lesson_id', '=', 'lessons.id')
                    ->select("school_lessons_clases_pivots.*", "schools.*", "clases.*", "lessons.*")
                    ->latest("slcid")
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
        } catch (\Error $e) {
            return response()->json([], 500);
        }


    }

    public function destroy($id)
    {
        try {
            $res = SchoolLessonsClasesPivot::where("slcid", $id)->delete();
            if ($res) {
                return response()->json('Ürün başarıyla silindi.', 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }

    }

    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'dataList' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 401);
            }

            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $allSaved = false;
                $allContains = false;
                if (count($request->dataList) > 0) {
                    foreach ($request->dataList as $item) {
                        $sclpivot = new SchoolLessonsClasesPivot();
                        $sclpivots = SchoolLessonsClasesPivot::where([
                            ["school_id", "=", $item['school_id']],
                            ["clases_id", "=", $item['clases_id']],
                            ["lesson_id", "=", $item['lesson_id']]
                        ])->get();
                        if (count($sclpivots) > 0) {
                            $allContains = true;
                        } else {
                            $allContains = false;
                            $sclpivot->school_id = $item['school_id'];
                            $sclpivot->clases_id = $item['clases_id'];
                            $sclpivot->lesson_id = $item['lesson_id'];
                            if ($sclpivot->save()) {
                                $allSaved = true;
                            } else {
                                $allSaved = false;
                            }
                        }
                    }
                    if ($allSaved || $allContains) {
                        return response()->json($sclpivot);
                    } else {
                        return response()->json([], 500);
                    }

                } else {
                    return response()->json([], 500);
                }


            } else {
                $allSaved = false;
                if (count($request->dataList) > 0) {
                    foreach ($request->dataList as $item) {
                        $sclpivot = new SchoolLessonsClasesPivot();
                        $sclpivot->school_id = $item['school_id'];
                        $sclpivot->clases_id = $item['clases_id'];
                        $sclpivot->lesson_id = $item['lesson_id'];
                        if ($sclpivot->save()) {
                            $allSaved = true;
                        } else {
                            $allSaved = false;
                        }
                    }
                    if ($allSaved) {
                        return response()->json($sclpivot);
                    } else {
                        return response()->json([], 500);
                    }
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }


    }
}
