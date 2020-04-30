<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\Lessons;
use App\Models\SchoolProgram;
use App\Models\SchoolProgramContent;
use App\Models\SchoolProgramContentUserPivot;
use App\Models\Users;
use Illuminate\Http\Request;
use Validator;

class SchoolProgramController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function update(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            $differentPerson = true;
            $allSaved = true;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $updatelesson = SchoolProgramContent::where($parser)->update([
                    "lesson_id" => $request->lessonid
                ]);
                if ($updatelesson) {
                    $deleted = SchoolProgramContentUserPivot::where("school_program_content_id", $request->spcontentid)->delete();
                    if ($deleted) {
                        foreach ($request->userData as $index => $item) {
                            $userhave = SchoolProgramContent::with("getUsers")
                                ->where("school_hour_id", $request->hourid)
                                ->where("school_day_id", $request->dayid)
                                ->whereHas("getUsers", function ($query) use ($item) {
                                    $query->where([
                                        ['user_id', '=', $item['user_id']]
                                    ]);
                                })->get();
                            if (count($userhave) > 0) {
                                $differentPerson = false;
                                // return response()->json([], 204);
                            } else {
                                $schoolProgramContentUser = new SchoolProgramContentUserPivot();
                                $schoolProgramContentUser->user_id = $item['user_id'];
                                $schoolProgramContentUser->school_program_content_id = $request->spcontentid;
                                if ($schoolProgramContentUser->save()) {
                                    $allSaved = true;
                                } else {
                                    return response()->json([], 500);
                                }
                                // $differentPerson = true;
                            }
                        }
                        if ($differentPerson == true) {
                            return response()->json("Success", 200);
                        } else {
                            return response()->json([], 204);
                        }
                    } else {
                        return response()->json([], 500);
                    }
                }

                /*                }*/
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function activitytest(Request $request)
    {
        $user = Users::where("id", 142)->with(['getspContents'])
            ->has("getspContents.getProgram")->get();
    }

    public function test(Request $request)
    {

        $abc = SchoolProgramContentUserPivot::where("school_program_content_id", 20)->delete();
        return $abc;
        $data = SchoolProgramContent::with("getUsers")
            ->where("school_program_id", "!=", 3)
            ->where("school_hour_id", 7)
            ->where("school_day_id", 1)
            ->whereHas("getUsers", function ($query) {
                $query->where([
                    ['user_id', '=', 3]
                ]);
            })->get();

        return $data;
    }
//BAK
    public function getUserSchoolProgramToday(Request $request)
    {
        try {
            date_default_timezone_set('Europe/Istanbul');
            $result = array();
            $queryparse = $request->urlparse;
            /*userid: 2, programid: 2*/
            $user = Users::where("id", $request->userid)->with(['getspContents'])
                ->whereHas("getspContents.getProgram", function ($q) use ($request) {
                    $q->where([['school_p_type_id', "=", $request->programid]]);
                })->get();
            if (count($user) > 0) {
                $get_teachers = array(
                    "user_id" => $user[0]->id,
                    "uFullName" => $user[0]->uFullName
                );
                $contents = $user[0]['getspContents'];

                /*         return $contents;*/
                $daysmap = [
                    "Pazartesi" => 'Monday',
                    "Salı" => 'Tuesday',
                    "Çarşamba" => 'Wednesday',
                    "Perşembe" => 'Thursday',
                    "Cuma" => 'Friday',
                    "Cumartesi" => 'Saturday',
                    "Pazar" => 'Sunday',
                ];
                /* $collection = collect($contents);

                 $filtered = $collection->where('school_p_type_id', $request->programid);

                 $data=$filtered->all();
                 return $data;*/
                foreach ($contents as $index => $content) {

//TODO
                    foreach ($daysmap as $key => $days) {
                        if (date("l") == $days) {
                            if ($key == $content->day->sdName) {

                                array_push(
                                    $result,
                                    [
                                        "dayname" => $content->day->sdName,
                                        "schoolprogramid" => $content->school_program_id,
                                        "school_p_type_id" => $content->getProgram->school_p_type_id,
                                        "school_id" => $user[0]['getspContents'][$index]['getProgram']->school_id,
                                        "clases_id" => $user[0]['getspContents'][$index]['getProgram']['getClases']->id,
                                        "branches_id" => $user[0]['getspContents'][$index]['getProgram']['getBranches']->id,
                                        "scoolprogramcontentid" => $content->id,
                                        "school_day_id" => $content->school_day_id,
                                        "school_hour_id" => $content->school_hour_id,
                                        "clasesName" => $user[0]['getspContents'][$index]['getProgram']['getClases']->cName,
                                        "branchesName" => $user[0]['getspContents'][$index]['getProgram']['getBranches']->bName,
                                        "classBranchName" => $user[0]['getspContents'][$index]['getProgram']['getClases']->cName . ' ' . $user[0]['getspContents'][$index]['getProgram']['getBranches']->bName,
                                        "lessons" => [
                                            "lesson_id" => $content->getLesson->id,
                                            "lName" => $content->getLesson->lName
                                        ],
                                        "users" => $get_teachers
                                    ]
                                );
                            }
                        }
                    }
                }
                $data = array();
                $collection = collect($result);
                $filtered = $collection->where('school_p_type_id', $request->programid);
                foreach ($filtered->all() as $key => $item) {
                    array_push($data, $filtered->all()[$key]);
                }
                return response()->json($data, 200);
            } else {
                return [];
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getUserSchoolProgram(Request $request)
    {
        try {
            date_default_timezone_set('Europe/Istanbul');
            $result = array();
            $queryparse = $request->urlparse;
            $user = Users::where("id", $request->userid)->with(['getspContents'])
                ->whereHas("getspContents.getProgram", function ($q) use ($request) {
                    $q->where([['school_p_type_id', "=", $request->programid]]);
                })->get();
            if (count($user) > 0) {
                $get_teachers = array(
                    "user_id" => $user[0]->id,
                    "uFullName" => $user[0]->uFullName
                );
                $contents = $user[0]['getspContents'];
                /*         return $contents;*/

                foreach ($contents as $index => $content) {


                    array_push(
                        $result,
                        [
                            "dayname" => $content->day->sdName,
                            "schoolprogramid" => $content->school_program_id,
                            "school_p_type_id" => $content->getProgram->school_p_type_id,
                            "school_id" => $user[0]['getspContents'][0]['getProgram']->school_id,
                            "clases_id" => $user[0]['getspContents'][0]['getProgram']->clases_id,
                            "branches_id" => $user[0]['getspContents'][0]['getProgram']->branches_id,
                            "scoolprogramcontentid" => $content->id,
                            "school_day_id" => $content->school_day_id,
                            "school_hour_id" => $content->school_hour_id,
                            "clasesName" => $user[0]['getspContents'][$index]['getProgram']['getClases']->cName,
                            "branchesName" => $user[0]['getspContents'][$index]['getProgram']['getBranches']->bName,
                            "classBranchName" => $user[0]['getspContents'][$index]['getProgram']['getClases']->cName . ' ' . $user[0]['getspContents'][$index]['getProgram']['getBranches']->bName,
                            "lessons" => [
                                "lesson_id" => $content->getLesson->id,
                                "lName" => $content->getLesson->lName
                            ],
                            "users" => $get_teachers
                        ]
                    );
                }
                return response()->json($result, 200);
            } else {
                return [];
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function getSchoolProgram(Request $request)
    {


        try {
            $result = array();
            $user = array();
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $schoolProgram = SchoolProgram::where($parser)->get();
                if (count($schoolProgram) > 0) {
                    $programContent = $schoolProgram[0]->getContent;

                    foreach ($programContent as $key => $item) {
                        $get_teachers = $item->getUsers->map(function ($u) {
                            $d["uFullName"] = $u["uFullName"];
                            $d["user_id"] = $u["id"];
                            return $d;
                        });
                        array_push(
                            $result,
                            [
                                "schoolprogramid" => $schoolProgram[0]->id,
                                "school_p_type_id" => $schoolProgram[0]->school_p_type_id,
                                "school_id" => $schoolProgram[0]->school_id,
                                "clases_id" => $schoolProgram[0]->clases_id,
                                "branches_id" => $schoolProgram[0]->branches_id,
                                "scoolprogramcontentid" => $item->id,
                                "school_day_id" => $item->school_day_id,
                                "school_hour_id" => $item->school_hour_id,
                                "lessons" => [
                                    "lesson_id" => $item->lesson_id,
                                    "lName" => $item->getLesson->lName
                                ],
                                "users" => $get_teachers
                            ]
                        );
                    }
                    return response()->json($result, 200);
                } else {
                    return [];
                }
            } else {
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }


    public function delete(Request $request)
    {

        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $deleted = SchoolProgramContent::where($parser)->delete();
                if ($deleted) {
                    return response()->json("Success", 200);
                }
            } else {
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function store(Request $request)
    {

        try {
            $valid = Validator::make($request->all(), [
                'ptypeid' => 'required',
                'schoolid' => 'required',
                'classid' => 'required',
                'branchid' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            } else {


                $isHave = SchoolProgram::where([
                    ["school_p_type_id", "=", $request->ptypeid],
                    ["school_id", "=", $request->schoolid],
                    ["clases_id", "=", $request->classid],
                    ["branches_id", "=", $request->branchid],
                ])->get();
                if (count($isHave) > 0) {
                    return $this->createSchoolProgramContent($isHave[0]->id, $request->programContentData, $request->programUserData);
                } else {
                    $schoolprogram = new SchoolProgram();
                    $schoolprogram->school_p_type_id = $request->ptypeid;
                    $schoolprogram->school_id = $request->schoolid;
                    $schoolprogram->clases_id = $request->classid;
                    $schoolprogram->branches_id = $request->branchid;
                    if ($schoolprogram->save()) {
                        return $this->createSchoolProgramContent($schoolprogram->id, $request->programContentData, $request->programUserData);
                    } else {
                        return response()->json("okul programa eklenmedi", 500);
                    }
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function createSchoolProgramContent($programid, $programContentData, $programUserData)
    {

        try {
            $isHave = SchoolProgramContent::where([
                ["school_day_id", "=", $programContentData[0]['dayid']],
                ["school_hour_id", "=", $programContentData[0]['hourid']],
            ])->get();
            $differentPerson = false;
            if (count($isHave) > 0) {
                foreach ($programUserData as $index => $item) {
                    $userhave = SchoolProgramContent::with("getUsers")
                        ->where("school_hour_id", $programContentData[0]['hourid'])
                        ->where("school_day_id", $programContentData[0]['dayid'])
                        ->whereHas("getUsers", function ($query) use ($item) {
                            $query->where([
                                ['user_id', '=', $item['user_id']]
                            ]);
                        })->get();
                    if (count($userhave) > 0) {
                        $differentPerson = false;
                        return response()->json([], 204);
                    } else {
                        $differentPerson = true;
                    }
                }
                if ($differentPerson == true) {
                    $schoolprogramContent = new SchoolProgramContent();
                    $schoolprogramContent->school_program_id = $programid;
                    $schoolprogramContent->school_day_id = $programContentData[0]['dayid'];
                    $schoolprogramContent->school_hour_id = $programContentData[0]['hourid'];
                    $schoolprogramContent->lesson_id = $programContentData[0]['lessonid'];
                    if ($schoolprogramContent->save()) {
                        return $this->createSchoolProgramUser($schoolprogramContent->id, $programUserData, $programid);
                    } else {
                        return response()->json(['Error'], 500);
                    }
                }
            } else {
                $schoolprogramContent = new SchoolProgramContent();
                $schoolprogramContent->school_program_id = $programid;
                $schoolprogramContent->school_day_id = $programContentData[0]['dayid'];
                $schoolprogramContent->school_hour_id = $programContentData[0]['hourid'];
                $schoolprogramContent->lesson_id = $programContentData[0]['lessonid'];
                if ($schoolprogramContent->save()) {
                    return $this->createSchoolProgramUser($schoolprogramContent->id, $programUserData, $programid);
                } else {
                    return response()->json("test", 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function createSchoolProgramUser($programcontentid, $programUserData, $programid)
    {
        try {
            $allSaved = false;
            foreach ($programUserData as $item) {

                $schoolProgramContentUser = new SchoolProgramContentUserPivot();
                $schoolProgramContentUser->user_id = $item['user_id'];
                $schoolProgramContentUser->school_program_content_id = $programcontentid;
                if ($schoolProgramContentUser->save()) {
                    $allSaved = true;
                } else {
                    return response()->json([], 500);
                }
            }
            if ($allSaved == true) {
                return response()->json(["contentid" => $programcontentid, "programid" => $programid], 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
