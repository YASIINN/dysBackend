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
use App\Models\ActivityProgramContent as APC;
use App\Models\ActivityProgramContentUserPivot as APCU;


use Validator;

use Illuminate\Http\Request;

class ActivityProgramContentController extends Controller
{

    /*Yasin*/

    public function getTodayActivitUserProgram(Request $request)
    {
        try {
            date_default_timezone_set('Europe/Istanbul');
            $actp = Users::find($request->userid);
            if (!$actp) {
                return [];
            }
            $response = array();
            $result = $actp->ucontents;
            $daysmap = [
                "Pazartesi" => 'Monday',
                "Salı" => 'Tuesday',
                "Çarşamba" => 'Wednesday',
                "Perşembe" => 'Thursday',
                "Cuma" => 'Friday',
                "Cumartesi" => 'Saturday',
                "Pazar" => 'Sunday',
            ];
            $programid = $request->programid;
            $map = $result->map(function ($content) use ($daysmap, $actp, $programid) {
                foreach ($daysmap as $key => $days) {
                    if (date("l") == $days) {
                        if ($key == $content->day->adName) {
                            //buraya TODO
                            if ($programid == $content->grade->activity_p_type_id) {
                                $data["id"] = $content->id;
                                $data['period'] = $content->activityprogram->getActivityProgramType->period->pName;
                                $data['activityid'] = $content->activityprogram->getActivityProgramType->activity_id;
                                $data["hourname"] = $content->hour->ahName;
                                $data['starthour'] = $content->hour->beginDate;
                                $data['endhour'] = $content->hour->endDate;
                                $data["dayname"] = $content->day->adName;
                                $data['fulltext'] = $content->day->adName . " " . $content->hour->ahName . " " . $content->hour->beginDate . "-" . $content->hour->endDate . " " . $content->lesson->lName;
                                $data["activity_program_id"] = $content->activity_program_id;
                                $data["activity_p_type_id"] = $content->grade->activity_p_type_id;
                                $data["activity_hour_id"] = $content->activity_hour_id;
                                $data["activity_day_id"] = $content->activity_day_id;
                                $data["lesson_id"] = $content->lesson_id;
                                $data['gradeBranchName'] = $content->grade->grade->gName;
                                $data["slesson"] = [
                                    "lName" => $content->lesson->lName,
                                    "id" => $content->lesson->id
                                ];
                                $data["sgrade"] = [
                                    "name" => $content->grade->grade->gName,
                                    "id" => $content->grade->grade->id
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
                            }
                        }

                    }
                }

            });

            $collection = collect($map)->filter()->all();
            $result = array();
            if (count($collection) > 1) {
                foreach ($collection as $key => $item) {
                    array_push($result, $collection[$key]);
                }
                return $result;
            } else {
                foreach ($collection as $key => $item) {
                    array_push($result, $collection[$key]);
                }
                return $result;
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getActivitUserProgram(Request $request)
    {
        try {
            date_default_timezone_set('Europe/Istanbul');
            $actp = Users::find($request->userid);
            if (!$actp) {
                return [];
            }
            $response = array();
            $result = $actp->ucontents;

            $programid = $request->programid;
            $map = $result->map(function ($content) use ($actp, $programid) {
                //buraya TODO
                if ($programid == $content->grade->activity_p_type_id) {
                    $data["id"] = $content->id;
                    $data["dayname"] = $content->day->adName;
                    $data["activity_program_id"] = $content->activity_program_id;
                    $data["activity_p_type_id"] = $content->grade->activity_p_type_id;
                    $data["activity_hour_id"] = $content->activity_hour_id;
                    $data["activity_day_id"] = $content->activity_day_id;
                    $data["lesson_id"] = $content->lesson_id;
                    $data['gradeBranchName'] = $content->grade->grade->gName;
                    $data["slesson"] = [
                        "lName" => $content->lesson->lName,
                        "id" => $content->lesson->id
                    ];
                    $data["sgrade"] = [
                        "name" => $content->grade->grade->gName,
                        "id" => $content->grade->grade->id
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

                }

            });

            $collection = collect($map)->filter()->all();
            $result = array();
            if (count($collection) > 1) {
                foreach ($collection as $key => $item) {
                    array_push($result, $collection[$key]);
                }
                return $result;
            } else {
                foreach ($collection as $key => $item) {
                    array_push($result, $collection[$key]);
                }
                return $result;
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    /*Yasin*/
    public function checkAP($request)
    {
        $a_p_type_id = $request["activity_p_type_id"];
        $grade_id = $request["grade_id"];
        $pv = AP::where("activity_p_type_id", $a_p_type_id)
            ->where("grade_id", $grade_id)
            ->get();
        return $pv;
    }

    public function checkAPC($request)
    {
        $a_p_id = $request["activity_program_id"];
        $a_d_id = $request["activity_day_id"];
        $a_h_id = $request["activity_hour_id"];
        $pv = APC::where("activity_program_id", $a_p_id)
            ->where("activity_day_id", $a_d_id)
            ->where("activity_hour_id", $a_h_id)
            ->get();
        return $pv;
    }

    public function checkAPCU($teacher, $request)
    {
        $id = $teacher["id"];
        $ap_content_id = $request["ap_content_id"];
        $pv = APCU::where("user_id", $id)
            ->where("ap_content_id", $ap_content_id)
            ->get();
        return $pv->count();
    }


    public function getContents(Request $request)
    {
        $actp = AP::where("activity_p_type_id", $request->a_p_type_id)
            ->where("grade_id", $request->grade_id)
            ->first();
        if (!$actp) {
            return [];
        }
        $result = $actp->acontents;
        $map = $result->map(function ($content) {
            $data["id"] = $content->id;
            $data["activity_program_id"] = $content->activity_program_id;
            $data["activity_hour_id"] = $content->activity_hour_id;
            $data["activity_day_id"] = $content->activity_day_id;
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

    public function getActScheduleTeachers(Request $request)
    {
        try {
            $employees = Users::with(["file", "unit", "province", "title"])
                ->has("ucontents")
                ->whereHas("utypes", function ($q) use ($request) {
                    $q->where("user_types_id", 1);
                })
                ->whereHas("periods", function ($q) use ($request) {
                    $q->where("period_id", $request["period_id"]);
                })
                ->whereHas("pactivities", function ($q) use ($request) {
                    $q->where("activity_id", $request["activity_id"]);
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
        $result = $actp->ucontents;
        $map = $result->map(function ($content) use ($actp) {

            $data["id"] = $content->id;
            $data["activity_program_id"] = $content->activity_program_id;
            $data["activity_hour_id"] = $content->activity_hour_id;
            $data["activity_day_id"] = $content->activity_day_id;
            $data["lesson_id"] = $content->lesson_id;
            $data["slesson"] = [
                "name" => $content->lesson->lName,
                "id" => $content->lesson->id
            ];
            $data["sgrade"] = [
                "name" => $content->grade->grade->gName,
                "id" => $content->grade->grade->id
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

    public function createAProgram(Request $request)
    {
        try {
            $c = $this->checkAP($request);
            if ($c->count() === 0) {
                $data = new AP();
                $data->activity_p_type_id = $request->activity_p_type_id;
                $data->grade_id = $request->grade_id;
                if ($data->save()) {
                    $request['status'] = true;
                    $d = $this->checkAP($request);
                    $request['activity_program_id'] = $d[0]->id;
                }
            } else {
                $request['status'] = true;
                $request['activity_program_id'] = $c[0]->id;
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
                $apc = APC::with("users")
                    ->where("activity_program_id", "!=", $request->activity_program_id)
                    ->where("activity_day_id", $request->activity_day_id)
                    ->where("activity_hour_id", $request->activity_hour_id)
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
        $valid = Validator::make($request->all(), [
            'activity_day_id' => 'required',
            'activity_hour_id' => 'required',
            'lesson_id' => 'required',
            'activity_p_type_id' => 'required',
            'grade_id' => 'required',
        ]);
        if ($valid->fails()) {
            return response()->json($valid->errors(), 200);
        }
        $this->createAProgram($request);
        if ($request->status) {
            // return  $this->getScheduleTeachersCheck($request);
            $result = $this->getScheduleTeachersCheck($request);
            if ($result) {
                return response()->json(["message" => 'Seçilen öğretmen başka bir sınıfın aynı gün ve saatine eklenmiş.'], 202);
            }
            $this->createaAContent($request);
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
    }
    // public function teacherCheck(Request $request){
    //     foreach ($request->steachers as $key=>$value) {
    //     }
    // }


    public function createaAContent(Request $request)
    {
        try {
            $c = $this->checkAPC($request);
            if ($c->count() === 0) {
                $apc = new APC();
                $apc->activity_program_id = $request->activity_program_id;
                $apc->activity_day_id = $request->activity_day_id;
                $apc->activity_hour_id = $request->activity_hour_id;
                $apc->lesson_id = $request->lesson_id;
                if ($apc->save()) {
                    $request["ap_content_id"] = $apc->id;
                    $request["status"] = true;
                }
            } else {
                $request['status'] = true;
                if ($request["type"] === 1) {
                    $c[0]->lesson_id = $request->lesson_id;
                    $c[0]->save();
                }
                $request['ap_content_id'] = $c[0]->id;
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
            APCU::where("ap_content_id", $request->ap_content_id)->delete();
        }
        try {
            foreach ($request->steachers as $key => $value) {
                $c = $this->checkAPCU($value, $request);
                if ($c == 0) {
                    $apd = new APCU();
                    $apd->user_id = $value["id"];
                    $apd->ap_content_id = $request["ap_content_id"];
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
            $apd = APC::findOrFail($id);
        } catch (\Exception $exception) {
            $errormsg = 'Veri Bulunamadı';
            return response()->json(['errormsg' => $exception->getMessage()]);
        }
        if ($apd->delete()) {
            return response()->json(["message" => 'Veri başarıyla silindi.'], 200);
        }
    }
}
