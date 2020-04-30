<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\Lessons;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class LessonController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $valid = Validator::make($request->all(), [
                'name' => 'required',
                'code' => 'required',
                "pid" => 'required',
                "type" => "required"
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 401);
            }
            $lesson = new Lessons();
            $queryparse = $request->urlparse;
            $orqueryparse = $request->orqparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $orparser = $this->uriParser->queryparser($orqueryparse);
                $lessons = Lessons::where($parser)->orWhere($orparser)->get();
                if (count($lessons) > 0) {
                    return response()->json([], 204);
                } else {
                    if ($request->subData) {
                        $lesson->lName = $request->name;
                        $lesson->lCode = $request->code;
                        $lesson->parent_id = $request->pid;
                        $lesson->type = $request->type;
                        if ($lesson->save()) {
                            $sublesSaved = false;
                            $allContains = false;

                            foreach ($request->subData as $item) {
                                $subless = new Lessons();
                                $subles = Lessons::where(
                                    [
                                        ["lName", "=", $item['name']],
                                        ["lCode", "=", $item['code']],
                                    ]
                                )->orWhere([
                                    ["lCode", "=", $item['code']]
                                ])->get();
                                if (count($subles) > 0) {
                                    DB::rollback();
                                    return response()->json([], 204);
                                } else {
                                    $allContains = false;
                                    $subless->lName = $item['name'];
                                    $subless->lCode = $item['code'];
                                    $subless->parent_id = $lesson['id'];
                                    $subless->type = 1;
                                    if ($subless->save()) {
                                        $sublesSaved = true;
                                    } else {
                                        $sublesSaved = false;
                                    }
                                }
                            }
                            if ($sublesSaved === true) {
                                DB::commit();
                                return response()->json($lesson, 200);
                            } else {
                                DB::rollback();
                                return response()->json([], 500);
                            }
                        } else {
                            return response()->json([], 500);
                        }
                    } else {
                        $lesson->lName = $request->name;
                        $lesson->lCode = $request->code;
                        $lesson->parent_id = $request->pid;
                        $lesson->type = $request->type;
                        if ($lesson->save()) {
                            DB::commit();
                            return response()->json($lesson, 200);
                        } else {
                            DB::rollback();
                            return response()->json([], 500);
                        }
                    }
                }
            } else {
                $lesson->lName = $request->name;
                $lesson->lCode = $request->code;
                $lesson->parent_id = $request->pid;
                $lesson->type = $request->type;
                if ($lesson->save()) {
                    DB::commit();
                    return response()->json($lesson, 200);
                } else {
                    DB::rollback();
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([], 500);
        }
    }

    public function show($id)
    {

        try {
            $title = Lessons::find($id);
            return response()->json($title);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function addSubLessons(Request $request)
    {
        $lesson = new Lessons();
        $lessonss = Lessons::where(
            [
                ["lName", "=", $request->name],
                ["lCode", "=", $request->code]
            ]
        )->orWhere(
            [
                ["lCode", "=", $request->code]
            ]
        )
            ->get();

        if (count($lessonss) > 0) {
            return response()->json([], 204);
        } else {
            $lesson->lName = $request->name;
            $lesson->lCode = $request->code;
            $lesson->parent_id = $request->parent_id;
            $lesson->type = $request->type;
            if ($lesson->save()) {
                return response()->json($lesson, 200);
            } else {
                return response()->json([], 500);
            }
        }
    }

    public function deleteSubLesson($id)
    {

        try {
            if (Lessons::destroy($id)) {
                return response()->json('Ürün başarıyla silindi.', 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function updateSubLesson(Request $request, $id)
    {
        $subless = Lessons::find($id);
        $subles = Lessons::where(
            [
                ["lName", "=", $request->name],
                ["lCode", "=", $request->code],
            ]
        )->orWhere([
            ["lCode", "=", $request->code]
        ])->get();
        if (count($subles) > 0) {
            foreach ($subles as $item) {
                if ($item->id != $id) {
                    return response()->json([], 204);
                } else {
                    $subless->lName = $request->name;
                    $subless->lCode = $request->code;
                    $subless->parent_id = $request->parent_id;
                    $subless->type = $request->type;
                    if ($subless->update()) {
                        return response()->json($subless, 200);
                    } else {
                        return response()->json([], 500);
                    }
                }
            }
        } else {
            $subless->lName = $request->name;
            $subless->lCode = $request->code;
            $subless->parent_id = $request->parent_id;
            $subless->type = $request->type;
            if ($subless->update()) {
                return response()->json($subless, 200);
            } else {
                return response()->json([], 500);
            }
        }
    }

    public function update(Request $request, $id)
    {
        if ($request->subData) {
            //seçmeli olan ders güncelleniyor
            $lesson = Lessons::find($id);
            $lessonRecord = Lessons::where([
                [
                    "lName", "=", $request->name
                ],
                [
                    "lCode", "=", $request->code
                ]
            ])->orWhere([
                [
                    "lCode", "=", $request->code
                ]
            ])->get();
            if (count($lessonRecord) > 0) {
                $owner = true;
                foreach ($lessonRecord as $item) {
                    if ($item->id != $id) {
                        $owner = false;
                    } else {
                        $owner = true;
                    }
                    //kayıt kendisimi değil mi
                    if ($owner == true) {
                        //kendisiyse
                        $lesson->lName = $request->name;
                        $lesson->lCode = $request->code;
                        $lesson->parent_id = $request->pid;
                        $lesson->type = $request->type;
                        if ($lesson->update()) {
                            return response()->json($lesson);
                        } else {
                            return response()->json([], 500);
                        }
                    } else {
                        return response()->json([], 204);
                    }
                }
            } else {
                $lesson->lName = $request->name;
                $lesson->lCode = $request->code;
                $lesson->parent_id = $request->pid;
                $lesson->type = $request->type;
                if ($lesson->update()) {
                    return response()->json($lesson);
                } else {
                    return response()->json([], 500);
                }
            }
        } else {
            //alt dersleri varmı tipi değişiyor
            $sublessonHave = Lessons::where("parent_id", $id)->get();
            if (count($sublessonHave) > 0) {
                $lessonsDeleted = false;
                //alt dersler tek tek silinsin seçmeliden normal ders dönüştürüldü
                foreach ($sublessonHave as $item) {
                    if (Lessons::destroy($item['id'])) {
                        $lessonsDeleted = true;
                    } else {
                        $lessonsDeleted = false;
                    }
                }
                if ($lessonsDeleted) {
                    //dersi bul
                    $lesson = Lessons::find($id);
                    //eşleşen kayıt varmı bak
                    $lessonRecord = Lessons::where([
                        [
                            "lName", "=", $request->name
                        ],
                        [
                            "lCode", "=", $request->code
                        ]
                    ])->orWhere([
                        [
                            "lCode", "=", $request->code
                        ]
                    ])->get();
                    if (count($lessonRecord) > 0) {
                        //kayıt var
                        $owner = true;
                        foreach ($lessonRecord as $item) {
                            if ($item->id != $id) {
                                $owner = false;
                            } else {
                                $owner = true;
                            }
                            //kayıt kendisimi değil mi
                            if ($owner == true) {
                                //kendisiyse
                                $lesson->lName = $request->name;
                                $lesson->lCode = $request->code;
                                $lesson->parent_id = $request->pid;
                                $lesson->type = $request->type;
                                if ($lesson->update()) {
                                    return response()->json($lesson);
                                } else {
                                    return response()->json([], 500);
                                }
                            } else {
                                return response()->json([], 204);
                            }
                        }
                    } else {
                        //kayıt yo kders kodu da değişti
                        $lesson->lName = $request->name;
                        $lesson->lCode = $request->code;
                        $lesson->parent_id = $request->pid;
                        $lesson->type = $request->type;
                        if ($lesson->update()) {
                            return response()->json($lesson);
                        } else {
                            return response()->json([], 500);
                        }
                    }
                } else {
                    return response()->json(["Hata Var Alt Derslerin Silinmesinden Gelen"], 500);
                }
            } else {
                $lesson = Lessons::find($id);
                //bu kayıt kendisi
                $lessonRecord = Lessons::where([
                    [
                        "lName", "=", $request->name
                    ],
                    [
                        "lCode", "=", $request->code
                    ]
                ])->orWhere([
                    [
                        "lCode", "=", $request->code
                    ]
                ])->get();
                if (count($lessonRecord) > 0) {
                    $owner = true;
                    foreach ($lessonRecord as $item) {
                        if ($item->id != $id) {
                            $owner = false;
                        } else {
                            $owner = true;
                        }
                        //kayıt kendisimi değil mi
                        if ($owner == true) {
                            //kendisiyse
                            $lesson->lName = $request->name;
                            $lesson->lCode = $request->code;
                            $lesson->parent_id = $request->pid;
                            $lesson->type = $request->type;
                            if ($lesson->update()) {
                                return response()->json($lesson);
                            } else {
                                return response()->json([], 500);
                            }
                        } else {
                            return response()->json([], 204);
                        }
                    }
                } else {
                    $lesson->lName = $request->name;
                    $lesson->lCode = $request->code;
                    $lesson->parent_id = $request->pid;
                    $lesson->type = $request->type;
                    if ($lesson->update()) {
                        return response()->json($lesson);
                    } else {
                        return response()->json([], 500);
                    }
                }
            }
        }
    }

    public function getAllLessons()
    {

        try {
            $lessons = Lessons::where([
                ['parent_id', "=", 0],
                ['lName',"!=","-"]
            ])->get();
            return response()->json($lessons, 200);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function getLesson(Request $request)
    {

        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $lessons = Lessons::where($parser)->latest()->paginate(2);
                foreach ($lessons as $lesson) {
                    if ($lesson['type'] == 0) {
                        $lesson['type'] = "Normal";
                    } else {
                        $lesson['type'] = "Seçmeli";
                        $lesson['subData'] = Lessons::where("parent_id", $lesson['id'])->get();
                    }
                }
                return response()->json($lessons, 200);
            } else {

                $lessons = Lessons::latest()->paginate(2);
                foreach ($lessons as $lesson) {
                    if ($lesson['type'] == 1) {
                        $lesson['type'] = "Seçmeli";
                    } else {
                        $lesson['type'] = "Normal";
                    }
                }
                return response()->json($lessons, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }


    public function destroy($id)
    {

        try {
            $lessons = Lessons::where("parent_id", $id)->get();
            if (count($lessons) > 0) {
                $lessonsDeleted = false;
                foreach ($lessons as $lesson) {
                    if (Lessons::destroy($lesson['id'])) {
                        $lessonsDeleted = true;
                    } else {
                        $lessonsDeleted = false;
                    }
                }
                if ($lessonsDeleted) {
                    if (Lessons::destroy($id)) {
                        return response()->json('Ürün başarıyla silindi.', 200);
                    } else {
                        return response()->json([], 500);
                    }
                } else {
                    return response()->json([], 500);
                }
            } else {
                if (Lessons::destroy($id)) {
                    return response()->json('Ürün başarıyla silindi.', 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}
