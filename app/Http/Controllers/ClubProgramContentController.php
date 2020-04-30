<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\School;
use App\Models\Users;
use App\Models\Lessons;
use App\Models\ActivityStudentPivot as ASP;
use App\Models\SchoolProgram as SP;
use App\Models\Activity;
use App\Models\Grade;
use App\Models\ActivityProgram as AP;
use App\Models\ClubProgram as CP;
use App\Models\ActivityProgramContent as APC;
use App\Models\ClubProgramContent as CPC;
use App\Models\ClubProgramContentUserPivot as CPCU;


use Validator;

use Illuminate\Http\Request;

class ClubProgramContentController extends Controller
{

    public function getSporClubUserProgram(Request $request)
    {
        try {
            date_default_timezone_set('Europe/Istanbul');
            $result = array();
            $user = Users::where("id", $request->userid)->with(['uclubcontents'])
                ->whereHas("uclubcontents.getProgram", function ($q) use ($request) {
                    $q->where([['club_p_type_id', "=", $request->programid]]);
                })->get();
            if (count($user) > 0) {
                $get_teachers = array(
                    "user_id" => $user[0]->id,
                    "uFullName" => $user[0]->uFullName
                );

                $contents = $user[0]['uclubcontents'];
                /*         return $contents;*/

                foreach ($contents as $index => $content) {

                    array_push(
                        $result,
                        [
                            "dayname" => $content->day->cdName,
                            "club_program_id" => $content->club_program_id,
                            "club_p_type_id" => $content->getProgram->club_p_type_id,
                            "club_id" => $user[0]['uclubcontents'][0]['getProgram']->spor_club_id,
                            "team_id" => $user[0]['uclubcontents'][0]['getProgram']->team_id,
                            "branches_id" => $user[0]['uclubcontents'][0]['getProgram']->spor_club_branch_id,
                            "clubprogramcontentid" => $content->id,
                            "club_day_id" => $content->club_day_id,
                            "club_hour_id" => $content->club_hour_id,
                            "teamName" => $user[0]['uclubcontents'][$index]['getProgram']['getTeams']->stName,
                            "branchesName" => $user[0]['uclubcontents'][$index]['getProgram']['getBranches']->sbName,
                            "classBranchName" => $user[0]['uclubcontents'][$index]['getProgram']['getTeams']->stName . ' ' . $user[0]['uclubcontents'][$index]['getProgram']['getBranches']->sbName,
                            "lessons" => [
                                "lesson_id" => 0,
                                "lName" => $content->description,
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
            return response()->json($e->getMessage(), 500);
        }
    }

    public function checkCP($request)
    {
        $c_p_type_id = $request["club_p_type_id"];
        $spor_club_id = $request["spor_club_id"];
        $team_id = $request["team_id"];
        $sbranch_id = $request["sbranch_id"];
        $pv = CP::where("club_p_type_id", $c_p_type_id)
            ->where("spor_club_id", $spor_club_id)
            ->where("team_id", $team_id)
            ->where("spor_club_branch_id", $sbranch_id)
            ->get();
        return $pv;
    }

    public function checkCPC($request)
    {
        $a_p_id = $request["club_program_id"];
        $a_d_id = $request["club_day_id"];
        $a_h_id = $request["club_hour_id"];
        $pv = CPC::where("club_program_id", $a_p_id)
            ->where("club_day_id", $a_d_id)
            ->where("club_hour_id", $a_h_id)
            ->get();
        return $pv;
    }

    public function checkCPCU($teacher, $request)
    {
        $id = $teacher["id"];
        $club_content_id = $request["club_content_id"];
        $pv = CPCU::where("user_id", $id)
            ->where("club_content_id", $club_content_id)
            ->get();
        return $pv->count();
    }


    public function getContents(Request $request)
    {
        $cp = CP::where("club_p_type_id", $request->c_p_type_id)
            ->where("spor_club_id", $request->spor_club_id)
            ->where("team_id", $request->team_id)
            ->where("spor_club_branch_id", $request->sbranch_id)
            ->first();
        if (!$cp) {
            return [];
        }
        $result = $cp->ccontents;
        $map = $result->map(function ($content) {
            $data["id"] = $content->id;
            $data["club_program_id"] = $content->club_program_id;
            $data["club_hour_id"] = $content->club_hour_id;
            $data["club_day_id"] = $content->club_day_id;
            $data["description"] = $content->description;
            $teachers = $content["users"]->map(function ($u) {
                $d["name"] = $u->uFullName;
                $d["id"] = $u->id;
                return $d;
            });
            $data["steachers"] = $teachers;
            return $data;
        });
        return $map;
    }


    public function getSchoolContents(Request $request)
    {
        $sp = SP::where("school_p_type_id", $request->s_p_type_id)
            ->where("school_id", $request->school_id)
            ->where("clases_id", $request->class_id)
            ->where("branches_id", $request->branch_id)
            ->first();
        if (!$sp) {
            return [];
        }
        $result = $sp->scontents;
        $map = $result->map(function ($content) {
            $data["id"] = $content->id;
            $data["school_program_id"] = $content->school_program_id;
            $data["school_hour_id"] = $content->school_hour_id;
            $data["school_day_id"] = $content->school_day_id;
            $data["lesson_id"] = $content->lesson_id;
            $data["slesson"] = [
                "name" => $content->lesson->lName,
                "id" => $content->lesson->id
            ];
            $teachers = $content["users"]->map(function ($u) {
                $d["name"] = $u->uFullName;
                $d["id"] = $u->id;
                return $d;
            });
            $data["steachers"] = $teachers;
            return $data;
        });
        return $map;
    }

    public function getClubScheduleTeachers(Request $request)
    {
        try {
            $employees = Users::has("uclubcontents")
                ->whereHas("utypes", function ($q) use ($request) {
                    $q->where("user_types_id", 1);
                })
                ->whereHas("uclubs", function ($q) use ($request) {
                    $q->where("spor_club_id", $request["spor_club_id"]);
                })
                ->get();
            return $employees;
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }

    public function getTeacherContents(Request $request)
    {
        $actp = Users::find($request->id);
        if (!$actp) {
            return [];
        }
        $result = $actp->uclubcontents;
        $map = $result->map(function ($content) use ($actp) {

            $data["id"] = $content->id;
            $data["club_program_id"] = $content->club_program_id;
            $data["club_hour_id"] = $content->club_hour_id;
            $data["club_day_id"] = $content->club_day_id;
            $data["description"] = $content->description;
            $data["sgrade"] = [
                "name" => $content->cp->team->stName . ' ' . $content->cp->spor_club_branch->sbName
            ];
            $teachers = $content["users"]->map(function ($u) use ($actp) {
                // if($actp->id === $u->id){
                $d["name"] = $u->uFullName;
                $d["id"] = $u->id;
                return $d;
                //  }
            });
            $data["steachers"] = $teachers;
            return $data;
        });
        return $map;
    }

    public function createCProgram(Request $request)
    {
        try {
            $c = $this->checkCP($request);
            if ($c->count() === 0) {
                $data = new CP();
                $data->club_p_type_id = $request->club_p_type_id;
                $data->spor_club_id = $request->spor_club_id;
                $data->team_id = $request->team_id;
                $data->spor_club_branch_id = $request->sbranch_id;
                if ($data->save()) {
                    $request['status'] = true;
                    $d = $this->checkCP($request);
                    $request['club_program_id'] = $d[0]->id;
                }
            } else {
                $request['status'] = true;
                $request['club_program_id'] = $c[0]->id;
            }
            $request['status'] = true;
        } catch (\Throwable $th) {
            $request['status'] = false;
        }
        return $request;
    }


    public function getScheduleTeachersCheck(Request $request)
    {
        try {
            foreach ($request->steachers as $key => $teacher) {
                $apc = CPC::with("users")
                    ->where("club_program_id", "!=", $request->club_program_id)
                    ->where("club_day_id", $request->club_day_id)
                    ->where("club_hour_id", $request->club_hour_id)
                    ->whereHas("users", function ($q) use ($teacher) {
                        $q->where("user_id", $teacher["id"]);
                    })
                    ->get();
                if ($apc->count() > 0) {
                    return true;
                }
            }
            return false;
        } catch (\Throwable $th) {
            return response()->json($th->getMessage());
        }
    }

    public function create(Request $request)
    {
        try {

            $valid = Validator::make($request->all(), [
                'club_day_id' => 'required',
                'club_hour_id' => 'required',
                'description' => 'required',
                'club_p_type_id' => 'required',
                'spor_club_id' => 'required',
                'team_id' => 'required',
                'sbranch_id' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json($valid->errors(), 200);
            }
            $this->createCProgram($request);
            if ($request->status) {
                // return  $this->getScheduleTeachersCheck($request);
                $result = $this->getScheduleTeachersCheck($request);
                if ($result) {
                    return response()->json(["message" => 'Seçilen öğretmen başka bir sınıfın aynı gün ve saatine eklenmiş.'], 202);
                }
                $this->createClubContent($request);
                if ($request->status) {
                    $this->createTeachers($request);
                    if ($request->status) {
                        return response()->json($request, 201);
                    } else {
                        return response()->json(["message" => 'Faaliyet programına öğretmen ataması yapılırken hata meydana geldi.'], 200);
                    }
                } else {
                    return response()->json($request, 200);
                }
            } else {
                return response()->json(["message" => 'Faaliyet programı oluşturulurken hata meydana geldi.'], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(["message" => $th->getMessage()], 200);
        }
    }
    // public function teacherCheck(Request $request){
    //     foreach ($request->steachers as $key=>$value) {
    //     }
    // }


    public function createClubContent(Request $request)
    {
        try {
            $c = $this->checkCPC($request);
            if ($c->count() === 0) {
                $apc = new CPC();
                $apc->club_program_id = $request->club_program_id;
                $apc->club_day_id = $request->club_day_id;
                $apc->club_hour_id = $request->club_hour_id;
                $apc->description = $request->description;
                if ($apc->save()) {
                    $request["club_content_id"] = $apc->id;
                    $request["status"] = true;
                }
            } else {
                $request['status'] = true;
                if ($request["type"] === 1) {
                    $c[0]->description = $request->description;
                    $c[0]->save();
                }
                $request['club_content_id'] = $c[0]->id;
            }
            $request['status'] = true;
        } catch (\Throwable $th) {
            $request['status'] = false;
            $request['message'] = $th->getMessage();
        }
        return $request;
    }

    public function createTeachers(Request $request)
    {
        if ($request->type === 1) {
            CPCU::where("club_content_id", $request->club_content_id)->delete();
        }
        try {
            foreach ($request->steachers as $key => $value) {
                $c = $this->checkCPCU($value, $request);
                if ($c == 0) {
                    $apd = new CPCU();
                    $apd->user_id = $value["id"];
                    $apd->club_content_id = $request["club_content_id"];
                    if ($apd->save()) {
                        $request["status"] = true;
                    }
                }
            }
            $request["status"] = true;
        } catch (\Exception $exception) {
            $request["status"] = $exception->getMessage();
        }
        return $request;
    }

    public function destroy($id)
    {
        try {
            $apd = CPC::findOrFail($id);
        } catch (\Exception $exception) {
            $errormsg = 'Veri Bulunamadı';
            return response()->json(['errormsg' => $exception->getMessage()]);
        }
        if ($apd->delete()) {
            return response()->json(["message" => 'Veri başarıyla silindi.'], 200);
        }
    }
}
