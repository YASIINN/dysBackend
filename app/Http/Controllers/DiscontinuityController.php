<?php

namespace App\Http\Controllers;

use App\Models\ActivityProgramContent;
use App\Models\Discontinuity;
use App\Models\Grade;
use App\Models\SchoolProgram;
use App\Models\SchoolProgramContent;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class DiscontinuityController extends Controller
{
    public function ActivityStatusDiscountStudents(Request $request)
    {
        try {

            $result = array();
            $students = Student::with(['discont', 'discont.contentable', 'discont.dtype'])->
            whereHas("discont", function ($q) use ($request) {
                $q->where([
                    ["contentable_type", "=", ActivityProgramContent::class],
                    ["discontDate", '=', $request->date],
                ]);
            })->get();
            foreach ($students as $key => $item) {
                foreach ($item->discont as $k => $dc) {
                    $content = ActivityProgramContent::with(['day', 'hour', 'activityprogram'])
                        ->where("id", $dc->contentable->id)
                        ->whereHas("activityprogram.getActivityProgramType", function ($q) use ($request) {
                            $q->where([
                                ["p_type_id", "=", $request->type],
                                ['period_id', "=", $request->periodid],
                                ['activity_id', "=", $request->activityid]
                            ]);
                        })
                        ->whereHas("activityprogram.grade", function ($q) use ($request) {
                            $q->where([
                                ["id", "=", $request->gradeid],
                            ]);
                        })
                        ->get();
                    if (count($content) > 0) {
                        array_push($result, [
                            "periodid" => $content[0]->activityprogram->getActivityProgramType->period_id,
                            "periodname" => $content[0]->activityprogram->getActivityProgramType->period->pName,
                            "activityid" => $content[0]->activityprogram->getActivityProgramType->activity_id,
                            "activityname" => $content[0]->activityprogram->getActivityProgramType->activity->aName,
                            "gradeid" => $content[0]->activityprogram->grade->id,
                            "gradename" => $content[0]->activityprogram->grade->gName,
                            "studentid" => $item->id,
                            "studentname" => $item['s_fullname'],
                            "discontid" => $dc->id,
                            "aprogramcontentid" => $dc->contentable_id,
                            "discontDate" => $dc->discontDate,
                            "dtypeid" => $dc->dtype[0]->id,
                            "dtype" => $dc->dtype[0]->dtName,
                            "activity_program_id" => $content[0]->activity_program_id,
                            "dayid" => $content[0]->day->id,
                            "dayname" => $content[0]->day->adName,
                            "hourid" => $content[0]->hour->id,
                            "hourname" => $content[0]->hour->ahName,
                            "shour" => $content[0]->hour->beginDate,
                            "ehour" => $content[0]->hour->endDate,
                        ]);
                    }
                }
            }
            $collection = collect($result);
            $grouped = $collection->groupBy('hourname');
            $data = $grouped->toArray();
            return $data;

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function SchoolStatusDiscountStudents(Request $request)
    {
        try {
            $result = array();
            $students = Student::with(['discont', 'discont.contentable', 'discont.dtype'])
                ->whereHas("schools", function ($q) use ($request) {
                    $q->where("school_id", $request->schoolid);
                })->whereHas("clases", function ($q) use ($request) {
                    $q->where("clases_id", $request->clasid);
                })->whereHas("branches", function ($q) use ($request) {
                    $q->where("branches_id", $request->branchid);
                })->whereHas("discont", function ($q) use ($request) {
                    $q->where([
                        ["contentable_type", "=", SchoolProgramContent::class],
                        ["discontDate", '=', $request->date],
                    ]);
                })->get();
            foreach ($students as $key => $item) {
                foreach ($item->discont as $k => $dc) {
                    $content = SchoolProgramContent::with(['day', 'hour', 'getProgram'])
                        ->where("id", $dc->contentable->id)
                        ->whereHas("getProgram.getSchoolProgramType", function ($q) use ($request) {
                            $q->where("p_type_id", $request->type);
                        })->get();
                    if (count($content) > 0) {
                        array_push($result, [
                            "studentid" => $item->id,
                            "studentname" => $item['s_fullname'],
                            "discontid" => $dc->id,
                            "sprogramcontentid" => $dc->contentable_id,
                            "discontDate" => $dc->discontDate,
                            "dtypeid" => $dc->dtype[0]->id,
                            "dtype" => $dc->dtype[0]->dtName,
                            "school_program_id" => $content[0]->school_program_id,
                            "dayid" => $content[0]->day->id,
                            "dayname" => $content[0]->day->sdName,
                            "hourid" => $content[0]->hour->id,
                            "hourname" => $content[0]->hour->shName,
                            "shour" => $content[0]->hour->beginDate,
                            "ehour" => $content[0]->hour->endDate,
                        ]);
                    }

                }

            }
            $collection = collect($result);
            $grouped = $collection->groupBy('hourname');
            $data = $grouped->toArray();
            return $data;

            //contentable id sine göre

            return $students;
            $program = SchoolProgram::with(['getContent', 'getContent.day', 'getContent.hour'])->where([
                ['branches_id', '=', 1],
                ['clases_id', "=", 9],
                ['school_id', "=", 2],
                ['school_p_type_id', "=", 1]
            ])->get();
            return $program;

            return Discontinuity::with(['contentable', 'student'])->where(
                [
                    ['discontDate', '=', "2020-04-07"],
                    ["contentable_type", "=", SchoolProgramContent::class]
                ]
            )->whereHas("student", function ($q) {
                $q->where("student.id", 1);
            })
                ->get();
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);

        }
    }

    public function studentDiscontA(Request $request)
    {
        try {
            $result = array();
            $studentDisconts = Discontinuity::with(['contentable', 'student', 'dtype'])->where([
                ['student_id', "=", $request->studentid],
                ['discontDate', '>=', $request->sdate],
                ['discontDate', '<=', $request->edate],
                ["contentable_type", "=", ActivityProgramContent::class]
            ])->get();
            foreach ($studentDisconts as $key => $item) {
                $aprogramContent = ActivityProgramContent::with(['hour', 'day', 'lesson', 'activityprogram', 'grade'])->where("id", $item->contentable->id)->get();

                array_push($result, [
                    "programType" => $aprogramContent[0]->activityprogram->getActivityProgramType->p_type->ptName,
                    "programTypeid" => $aprogramContent[0]->activityprogram->getActivityProgramType->p_type->id,
                    "activityid" => $aprogramContent[0]->activityprogram->getActivityProgramType->activity->id,
                    "activityname" => $aprogramContent[0]->activityprogram->getActivityProgramType->activity->aName,
                    "periodid" => $aprogramContent[0]->activityprogram->getActivityProgramType->id,
                    "periodname" => $aprogramContent[0]->activityprogram->getActivityProgramType->period->pName,
                    "gradeid" => $aprogramContent[0]->activityprogram->grade->id,
                    "gradename" => $aprogramContent[0]->activityprogram->grade->gName,
                    "discontid" => $item['id'],
                    "dtypeid" => $item->d_type_id,
                    "dtypename" => $item->dtype[0]->dtName,
                    "discontDate" => $item->discontDate,
                    "studentid" => $item->student[0]->id,
                    "studentname" => $item->student[0]->s_fullname,
                    "hourid" => $aprogramContent[0]->hour->id,
                    "hourname" => $aprogramContent[0]->hour->ahName,
                    "shour" => $aprogramContent[0]->hour->beginDate,
                    "ehour" => $aprogramContent[0]->hour->endDate,
                    "dayid" => $aprogramContent[0]->day->id,
                    "dayname" => $aprogramContent[0]->day->adName,
                    "programcontentid" => $aprogramContent[0]->id,
                    "lessonid" => $aprogramContent[0]->lesson->id,
                    "lessonname" => $aprogramContent[0]->lesson->lName,
                ]);


            }
            $collection = collect($result);
            $grouped = $collection->groupBy('discontDate');
            $data = $grouped->toArray();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function studentDiscont(Request $request)
    {
        try {
            $result = array();
            $studentDisconts = Discontinuity::with(['contentable', 'student', 'dtype'])->where([
                ['student_id', "=", $request->studentid],
                ['discontDate', '>=', $request->sdate],
                ['discontDate', '<=', $request->edate],
                ["contentable_type", "=", SchoolProgramContent::class]
            ])->get();
            foreach ($studentDisconts as $key => $item) {
                $schoolprogramContent = SchoolProgramContent::with(['hour', 'day', 'lesson', 'getProgram'])->where("id", $item->contentable->id)->get();
                array_push($result, [
                    "programType" => $schoolprogramContent[0]->getProgram->getSchoolProgramType->p_type->ptName,
                    "programTypeid" => $schoolprogramContent[0]->getProgram->getSchoolProgramType->p_type->id,
                    "discontid" => $item['id'],
                    "dtypeid" => $item->d_type_id,
                    "dtypename" => $item->dtype[0]->dtName,
                    "discontDate" => $item->discontDate,
                    "studentid" => $item->student[0]->id,
                    "studentname" => $item->student[0]->s_fullname,
                    "schoolid" => $item->student[0]->schools[0]->id,
                    "schoolname" => $item->student[0]->schools[0]->sName,
                    "clasid" => $item->student[0]->clases[0]->id,
                    "clasname" => $item->student[0]->clases[0]->cName,
                    "branchid" => $item->student[0]->branches[0]->id,
                    "branchname" => $item->student[0]->branches[0]->bName,
                    "hourid" => $schoolprogramContent[0]->hour->id,
                    "hourname" => $schoolprogramContent[0]->hour->shName,
                    "shour" => $schoolprogramContent[0]->hour->beginDate,
                    "ehour" => $schoolprogramContent[0]->hour->endDate,
                    "dayid" => $schoolprogramContent[0]->day->id,
                    "dayname" => $schoolprogramContent[0]->day->sdName,
                    "programcontentid" => $schoolprogramContent[0]->id,
                    "lessonid" => $schoolprogramContent[0]->lesson->id,
                    "lessonname" => $schoolprogramContent[0]->lesson->lName,
                ]);


            }
            $collection = collect($result);
            $grouped = $collection->groupBy('discontDate');
            $data = $grouped->toArray();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }

    }

    public function index(Request $request)
    {

        date_default_timezone_set('Europe/Istanbul');


        $weekMap = [
            "Pazartesi" => 'Monday',
            "Salı" => 'Tuesday',
            "Çarşamba" => 'Wednesday',
            "Perşembe" => 'Thursday',
            "Cuma" => 'Friday',
            "Cumartesi" => 'Saturday',
            "Pazar" => 'Sunday',
        ];
        foreach ($weekMap as $key => $days) {
            if (date("l") == $days) {
                return $key;
            }
        }
        //        return date("l");
        $dayOfTheWeek = Carbon::now()->dayOfWeek;
        $weekday = $weekMap[$dayOfTheWeek];
        return $weekday;
        $discont = Discontinuity::where([
            ["contentable_id", "!=", $request->id],
            ["discontDate", "=", date('Y-m-d')]
        ])->get();
        return $discont;
    }

    public function delete(Request $request)
    {
        try {

            $delete = Discontinuity::where([
                ["contentable_id", "!=", $request->id],
                ["discontDate", "=", date('Y-m-d')]
            ])->delete();
            if ($delete) {
                return response()->json(['msg' => 'Success'], 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function update(Request $request)
    {
        date_default_timezone_set('Europe/Istanbul');
        try {
            $valid = Validator::make($request->all(), [
                "studentList" => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json($valid->errors(), 400);
            }
            $allsave = false;
            if ($request->type === 1) {

                foreach ($request->studentList as $key => $student) {
                    $d = Discontinuity::find($student['contentid']);
                    $d->d_type_id = $student['type'];
                    if ($d->update()) {
                        $allsave = true;
                    } else {
                        $allsave = false;
                    }
                }
                if ($allsave == true) {
                    return response()->json(['msg' => "Success"], 200);
                } else {
                    return response()->json([], 500);
                }
            } else if ($request->type === 2) {
                foreach ($request->studentList as $key => $student) {
                    $d = Discontinuity::find($student['contentid']);
                    $d->d_type_id = $student['type'];
                    if ($d->update()) {
                        $allsave = true;
                    } else {
                        $allsave = false;
                    }
                }
                if ($allsave == true) {
                    return response()->json(['msg' => "Success"], 200);
                } else {
                    return response()->json([], 500);
                }
                if ($allsave == true) {
                    return response()->json(['msg' => "Success"], 200);
                } else {
                    return response()->json([], 500);
                }
                //faaliyet
            }
            // $content = SchoolProgramContent::find(1);
            // $d = new Discontinuity;
            // $d->discontDate = date('Y-m-d');
            // $d->student_id = 1;
            // $d->d_type_id = 1;
            // if ($content->discounts()->save($d)) {
            //     return "ekledi";
            // }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function store(Request $request)
    {
        date_default_timezone_set('Europe/Istanbul');
        try {
            $valid = Validator::make($request->all(), [
                "studentList" => 'required',
                "id" => "required"
            ]);
            if ($valid->fails()) {
                return response()->json($valid->errors(), 400);
            }
            //type 1 school
            $allsave = false;
            if ($request->type === 1) {
                $discont = Discontinuity::where([
                    ["contentable_id", "!=", $request->id],
                    ["discontDate", "=", date('Y-m-d')],
                    ['contentable_type', "=", "App\Models\ActivityProgramContent"]
                ])->get();

                if (count($discont) > 0) {
                    return response()->json([], 204);
                } else {
                    $content = SchoolProgramContent::find($request->id);
                    foreach ($request->studentList as $key => $student) {
                        $d = new Discontinuity();
                        $d->discontDate = date('Y-m-d');
                        $d->student_id = $student['id'];
                        $d->d_type_id = $student['type'];
                        if ($content->discounts()->save($d)) {
                            $allsave = true;
                        } else {
                            $allsave = false;
                        }
                    }
                    if ($allsave == true) {
                        return response()->json(['msg' => "Success"], 200);
                    } else {
                        return response()->json([], 500);
                    }
                }
            } else if ($request->type === 2) {
                $discont = Discontinuity::where([
                    ["contentable_id", "!=", $request->id],
                    ["discontDate", "=", date('Y-m-d')],
                    ['contentable_type', "=", "App\Models\SchoolProgramContent"]
                ])->get();

                if (count($discont) > 0) {
                    return response()->json([], 204);
                } else {
                    $content = ActivityProgramContent::find($request->id);
                    foreach ($request->studentList as $key => $student) {
                        $d = new Discontinuity();
                        $d->discontDate = date('Y-m-d');
                        $d->student_id = $student['id'];
                        $d->d_type_id = $student['type'];
                        if ($content->discounts()->save($d)) {
                            $allsave = true;
                        } else {
                            $allsave = false;
                        }
                    }
                    if ($allsave == true) {
                        return response()->json(['msg' => "Success"], 200);
                    } else {
                        return response()->json([], 500);
                    }
                }
                //faaliyet
            }
            // $content = SchoolProgramContent::find(1);
            // $d = new Discontinuity;
            // $d->discontDate = date('Y-m-d');
            // $d->student_id = 1;
            // $d->d_type_id = 1;
            // if ($content->discounts()->save($d)) {
            //     return "ekledi";
            // }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
