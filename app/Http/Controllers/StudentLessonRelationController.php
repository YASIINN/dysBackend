<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Illuminate\Support\Facades;
use Validator;
use App\Models\Student;
use App\Models\Activity;
use App\Models\ActivityStudentPivot as StuActPivot;
use App\Models\SchoolStudentPivot as SSCBPivot;
use App\Models\ActivityPeriodLessonPivot as APL;
use App\Models\ActivityPeriodLessonStudentPivot as APLS;
use App\Models\SchoolLessonsClasesPivot as SCL;
use App\Models\SchoolClassLessonStudentPivot as SCLS;
use App\Models\School;
use App\Models\SDetail;
use App\Models\Lessons;
use DB;

class StudentLessonRelationController extends Controller
{
    public function checkLessonAP($request){
        $activity_id = $request["activity_id"];
        $period_id= $request["period_id"];
        $student_id= $request["student_id"];
        $lessons_id= $request["lessons_id"];
        $apls = APLS::where("activity_id",$activity_id)
              ->where("student_id",$student_id)
              ->where("period_id", $period_id)
              ->where("lessons_id", $lessons_id)->get();
        return $apls->count();
    }
    public function checkLessonSC($request){
        $school_id = $request["school_id"];
        $clases_id= $request["clases_id"];
        $student_id= $request["student_id"];
        $lessons_id= $request["lessons_id"];
        $scls = SCLS::where("school_id",$school_id)
              ->where("student_id",$student_id)
              ->where("clases_id", $clases_id)
              ->where("lessons_id", $lessons_id)->get();
        return $scls->count();
    }

    public function getStdSchoolDiffLessons(Request $request){
        // return $request;
        // return $this->studentsclessons($request);
          $ssclessons = $this->studentsclessons($request);
          $sclessons = $this->schoolclasslessons($request);
          $allLessons = collect($sclessons["lessons"]);
          $stdLessons = collect($ssclessons["lessons"]);
          $diffLessons = $allLessons->diff($stdLessons);
          $stdappdifflessons = [
              "title"=>$sclessons["title"],
              "lessons"=>$diffLessons->values()->all()
          ];
          return $stdappdifflessons;
    }



    //faaliyetlerin dersleri içerisinde öğrencinin olmayan derslerini listeler
    public function getStdActivityDiffLessons(Request $request){
        $saplessons = $this->studentaplessons($request);
        $aplessons = $this->activityperiodlessons($request); 
        $allLessons = collect($aplessons["lessons"]);
        $stdLessons = collect($saplessons["lessons"]);
        $diffLessons = $allLessons->diff($stdLessons);
        $stdappdifflessons = [
            "title"=>$aplessons["title"],
            "lessons"=>$diffLessons->values()->all()
        ];
        return $stdappdifflessons;
    }
    public function activityperiodlessons($request){
        $aplessons = [];
        $actperlessons = APL::with("lessons")->where("activity_id", $request["activity_id"])->where("period_id", $request["period_id"])->whereHas('lessons', function($q) use ($request)
        {
          $q->where("type","=", $request["lessonType"]);
        })->get();
     
        $lessons = [];
        $actper="";
        $mainlesson="";
        foreach ($actperlessons as $acl) {
            $actper =  $acl->activity->aName . " ". $acl->period->pName;

            if($request["lessonType"] === 1){
                $mainlesson = $acl->lessons->lName;
                $optinalLessons = Lessons::where("parent_id", $acl->lessons->id)->get(); 
                $data = [
                    "mainlesson"=>$mainlesson,
                    "optinalLessons"=>$optinalLessons
                ];
                array_push($lessons, $data);
             }else {
                array_push($lessons, $acl->lessons);
             }
        }
        $aplessons = [
            "title"=>$actper,
            "lessons"=>$lessons
        ];
        return $aplessons;
    }
    public function studentaplessons(Request $request){
        $aplessons = [];
        $actperlessons = APLS::with("lessons")->where("activity_id", $request["activity_id"])->where("period_id", $request["period_id"])->where("student_id", $request["student_id"])->whereHas('lessons', function($q) use ($request)
        {
          $q->where("type","=", $request["lessonType"]);
        })->get();
     
        $lessons = [];
        $actper="";
        $mainlesson="";
        foreach ($actperlessons as $acl) {
            $actper =  $acl->activity->aName . " ". $acl->period->pName;

            if($request["lessonType"] === 1){
                $mainlesson = $acl->lessons->lName;
                $optinalLessons = Lessons::where("parent_id", $acl->lessons->id)->get(); 
                $data = [
                    "mainlesson"=>$mainlesson,
                    "optinalLessons"=>$optinalLessons
                ];
                array_push($lessons, $data);
             }else {
                array_push($lessons, $acl->lessons);
             }
        }
        $aplessons = [
            "title"=>$actper,
            "lessons"=>$lessons
        ];
        return $aplessons;
    }


    //post
    public function getstudentlessons(Request $request){

         $saplessons =  $this->getStudentAPlessons($request);  //öğrencinin faaliyet dersleri
         $ssclessons =  $this->getStudentSClessons($request);
        //  return $saplessons;
          
        //  $aplessons = $this->studentactivitylessons($request);  //öğrencinin okul dersleri
         
        $lessons = [
            "activitylessons"=>$saplessons,
            "schoollessons"=> $ssclessons,
        ];
        return response()->json($lessons, 200);
    }
    public function getoptinallessons(Request $request){
        $sclessons = $this->schoolclasslessons($request);
      //  return $sclessons;
       $aplessons = $this->activityperiodlessons($request);
       $lessons = [
          "actper"=>$aplessons,
          "schoolclass"=>$sclessons
      ];
      return $lessons;
    }
    public function studentactivitylessons($request){
        $aplessons = [];
        $actperlessons = APL::with("lessons")->where("activity_id", $request["activity_id"])->where("period_id", $request["period_id"])->whereHas('lessons', function($q) use ($request)
        {
          $q->where("type","=", $request["lessonType"]);
        })->get();
     
        $lessons = [];
        $actper="";
        $mainlesson="";
        foreach ($actperlessons as $acl) {
            $actper =  $acl->activity->aName . " ". $acl->period->pName;

            if($request["lessonType"] === 1){
                $mainlesson = $acl->lessons->lName;
                $optinalLessons = Lessons::where("parent_id", $acl->lessons->id)->get(); 
                $data = [
                    "mainlesson"=>$mainlesson,
                    "optinalLessons"=>$optinalLessons
                ];
                array_push($lessons, $data);
             }else {
                array_push($lessons, $acl->lessons);
             }
        }
        $aplessons = [
            "title"=>$actper,
            "lessons"=>$lessons
        ];
        return $aplessons;
    }
    public function schoolclasslessons($request){
        $sclessons = [];
        $schclslessons = SCL::with("lesson")->where("school_id", $request["school_id"])->where("clases_id", $request["clases_id"])->whereHas('lesson', function($q) use ($request)
        {
          $q->where("type","=", $request["lessonType"]);
        })->get();
        $lessons = [];
        $schoolclass="";
        $mainlesson="";
        foreach ($schclslessons as $scl) {
             $schoolclass =  $scl->school->sName . " ". $scl->clases->cName;
             if($request["lessonType"] === 1){
                $mainlesson = $scl->lesson->lName;
                $optinalLessons = Lessons::where("parent_id", $scl->lesson->id)->get(); 
                $data = [
                    "mainlesson"=>$mainlesson,
                    "optinalLessons"=>$optinalLessons
                ];
                array_push($lessons, $data);
             }else {
                array_push($lessons, $scl->lesson);
             }
        }
        $sclessons = [
            "title"=>$schoolclass,
            "lessons"=>$lessons,
        ];
        return $sclessons;
    }
    public function studentsclessons(Request $request){
        $sclessons = [];
        $schoolclasslessons = SCLS::with("lessons")->where("school_id", $request["school_id"])->where("clases_id", $request["clases_id"])->where("student_id", $request["student_id"])->whereHas('lessons', function($q) use ($request)
        {
          $q->where("type","=", $request["lessonType"]);
        })->get();
     
        $lessons = [];
        $schoolclass="";
        $mainlesson="";
        foreach ($schoolclasslessons as $scl) {
            $schoolclass =  $scl->school->sName . " ". $scl->clases->cName;
            if($request["lessonType"] === 1){
                $mainlesson = $scl->lessons->lName;
                $optinalLessons = Lessons::where("parent_id", $scl->lessons->id)->get(); 
                $data = [
                    "mainlesson"=>$mainlesson,
                    "optinalLessons"=>$optinalLessons
                ];
                array_push($lessons, $data);
             }else {
                array_push($lessons, $scl->lessons);
             }
        }
        $sclessons = [
            "title"=>$schoolclass,
            "lessons"=>$lessons
        ];
        return $sclessons;
    }

    public function getStudentAPlessons(Request $request){
            if($request->type === "ALL"){
             $pv = APLS::all();
            } else {
             $pv = APLS::where("$request->where", $request->id)->get();
            }
            $data = [
            ];
            foreach ($pv as $p) {
             $stdact = [
                "student"=>$p->student->s_name,
                "student_id"=>$p->student->id,
                 "activity"=>$p->activity->aName,
                 "activity_id"=>$p->activity->id,
                 "period"=>$p->period->pName,
                 "period_id"=>$p->period->id,
                 "lesson"=>$p->lessons->lName,
                 "lesson_id"=>$p->lessons->id,
                 "lesson_type"=>$p->lessons->type
             ];
             array_push($data, $stdact);
            }
            return $data;
           
    }
    public function getStudentSClessons(Request $request){
        if($request->type === "ALL"){
         $pv = SCLS::all();
        } else {
         $pv = SCLS::where("$request->where", $request->id)->get();
        }
        $data = [
        ];
        foreach ($pv as $p) {
         $stdact = [
            "student"=>$p->student->s_name,
            "student_id"=>$p->student->id,
             "school"=>$p->school->sName,
             "school_id"=>$p->school->id,
             "clases"=>$p->clases->cName,
             "clases_id"=>$p->clases->id,
             "lesson"=>$p->lessons->lName,
             "lesson_id"=>$p->lessons->id,
             "lesson_type"=>$p->lessons->type
         ];
         array_push($data, $stdact);
        }
        return $data;
       
}
    public function createStudentLessons(Request $request){
        if($request->type === "sc"){
               return $this->createStudentSchoolLesson($request);
        } else if($request->type === "ap") {
            return $this->createStudentActivityLesson($request);
        }
    }


    public function createStudentActivityLesson(Request $request){
        foreach ($request->data as $key=>$value) {
        $valid = Validator::make($value, [
            'student_id'=>'required',
            'activity_id'=>'required',
            'period_id'=>'required',
            'lessons_id'=>'required',
        ]);
        if ($valid->fails()) {
             return response()->json($valid->errors(), 200);
        }
       }
        try {
            foreach ($request->data as $key=>$value) {
                 $c = $this->checkLessonAP($value);
                 if($c == 0){
                    $apls = new APLS();
                    $apls->activity_id=$value["activity_id"];
                    $apls->period_id=$value["period_id"];
                    $apls->student_id=$value["student_id"];
                    $apls->lessons_id=$value["lessons_id"];
                    $apls->save();
                 }
                 
            }
             return response()->json(["message" =>'Öğrencinin faaliyet ana ders ataması başarıyla eklendi.'], 201);
           } catch(\Exception $exception){
            $errormsg = 'Faaliyet ekleme sırasında hata meydana geldi.';
            return response()->json(['errormsg'=>$exception]);
           }
    }
    public function createStudentSchoolLesson(Request $request){
        foreach ($request->data as $key=>$value) {
        $valid = Validator::make($value, [
            'student_id'=>'required',
            'school_id'=>'required',
            'clases_id'=>'required',
            'lessons_id'=>'required',
        ]);
        if ($valid->fails()) {
             return response()->json($valid->errors(), 200);
        }
       }
        try {
            foreach ($request->data as $key=>$value) {
                 $c = $this->checkLessonSC($value);
                 if($c == 0){
                    $scls = new SCLS();
                    $scls->school_id=$value["school_id"];
                    $scls->clases_id=$value["clases_id"];
                    $scls->student_id=$value["student_id"];
                    $scls->lessons_id=$value["lessons_id"];
                    $scls->save();
                 }
                 
            }
             return response()->json(["message" =>'Öğrencinin okul ana ders ataması başarıyla eklendi.'], 201);
           } catch(\Exception $exception){
            $errormsg = 'Faaliyet ekleme sırasında hata meydana geldi.';
            return response()->json(['errormsg'=>$exception->getMessage()]);
           }
    }

    public function deleteStudentMultiLessons(Request $request){
        $result = 0;
        if($request->type === "sc"){
            $result = SCLS::where("school_id",$request->school_id)->where("student_id",$request->student_id)->where("clases_id",$request->clases_id)->delete();
            } else if($request->type === "ap") {
            $result = APLS::where("activity_id",$request->activity_id)->where("student_id",$request->student_id)->where("period_id", $request->period_id)->delete();
          }
          return response()->json($result, 200);
    }
    public function deleteStudentLessons(Request $request){
        //  return $request;
        if($request->type === "sc"){
            return $this->deleteStudentSchoolLesson($request);
            } else if($request->type === "ap") {
         return $this->deleteStudentActivityLesson($request);
          }
    }
    public function deleteStudentActivityLesson(Request $request){
        // return $request;
        try {
            $pv = APLS::where("activity_id",$request->activity_id)->where("student_id",$request->student_id)->where("period_id", $request->period_id)->where("lessons_id", $request->lesson_id)->first();
            $c = $pv->count();
            if($c > 0){
                $pv->delete();
                return response()->json(["message" =>'Öğrenci ders ataması başarılı bir şekilde silindi.'], 200);
            } else {
                return response()->json(["message" =>'Sistemde olmayan bir atamayı silmeye çalışıyorsunuz.'], 200);
            }
        } catch(\Exception $exception){
         $errormsg = 'Faaliyet silme sırasında hata meydana geldi.';
         return response()->json(['errormsg'=>$exception->getMessage()]);
        }
    }
    public function deleteStudentSchoolLesson(Request $request){
        try {
            $pv = SCLS::where("school_id",$request->school_id)->where("student_id",$request->student_id)->where("clases_id",$request->clases_id)->where("lessons_id",$request->lesson_id)->first();
            $c = $pv->count();
            if($c > 0){
                $pv->delete();
                return response()->json(["message" =>'Öğrenci ders ataması başarılı bir şekilde silindi.'], 200);
            } else {
                return response()->json(["message" =>'Sistemde olmayan bir atamayı silmeye çalışıyorsunuz.'], 200);
            }
        } catch(\Exception $exception){
         $errormsg = 'Faaliyet silme sırasında hata meydana geldi.';
         return response()->json(['errormsg'=>$errormsg]);
        }
    }
}
