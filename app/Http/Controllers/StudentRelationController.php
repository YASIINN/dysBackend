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
use App\Models\ClubTeamBranchStudentPivot as CTBS;
use App\Models\SporClubTeamBranch as CTB;
use App\Models\School;
use App\Models\SDetail;
use App\Models\Proximity;

class StudentRelationController extends Controller
{
    public function checkOther($request){
        $activity_id = $request["activity_id"];
        $student_id= $request["student_id"];
        $pv = StuActPivot::where("activity_id",$activity_id)->where("student_id",$student_id)->where("period_id", $request["period_id"])->get();
        return $pv->count();
    }
    public function checkClub($request){
        $club_id = $request["club_id"];
        $student_id= $request["student_id"];
        $pv = CTBS::where("spor_club_id",$club_id)->where("student_id",$student_id)->where("team_id", $request["team_id"])->get();
        return $pv->count();
    }
    public function createActivity(Request $request){
        $valid = Validator::make($request->all(), [
            'activity_id'=>'required',
            'period_id'=>'required',
            'grade_id'=>'required',
            'student_id'=>'required',
        ]);
        if ($valid->fails()) {
             return response()->json($valid->errors(), 200);
        }
        try {
            $c = $this->checkOther($request);
            if($c > 0){
              return response()->json(["message" =>'Öğrenciyi aynı faliyetin farklı sınıflarına eklemeye çalışıyorsunuz.'], 200);
            }
           $st = Student::findOrFail($request->student_id);
           $act = Activity::findOrFail($request->activity_id);
          $attributes = [
              "period_id"=>$request->period_id,
              "grade_id"=>$request->grade_id
          ];
          $st->activities()->save($act, $attributes);
          return response()->json(["message" =>'Öğrenci faaliyete başarılı bir şekilde eklendi.'], 201);
       } catch(\Exception $exception){
        $errormsg = 'Faaliyet ekleme sırasında hata meydana geldi.';
        return response()->json(['errormsg'=>$errormsg]);
       }
    }
    public function getActivities(Request $request){
        try {
            if($request->type === "ALL"){
             $pv = StuActPivot::all();
            } else {
             $pv = StuActPivot::where("$request->where", $request->id)->get();
            }
            $data = [
            ];
            foreach ($pv as $key=>$p) {
            //  return $data = [
            //      "a"=>$p->activity->periods[$key]->pivot->isActive ? true:false,
            //      "b"=>"adem"
            //  ];
             $stdact = [
                 "isActive"=>$p->activity->periods[$key]->pivot->isActive ? true:false,
                "student"=>$p->student->s_name,
                "student_id"=>$p->student->id,
                 "activity"=>$p->activity->aName,
                 "activity_id"=>$p->activity->id,
                 "period"=>$p->period->pName,
                 "period_id"=>$p->period->id,
                 "grade"=>$p->grade->gName,
                 "grade_id"=>$p->grade->id
             ];
             array_push($data, $stdact);
            }
            return response()->json($data, 200);
           }catch(\Exception $exception){
            $errormsg = 'İşlem sırasında hata meydana geldi. Lütfen sonra deneyiniz.';
            return response()->json(['errormsg'=>$errormsg]);
        }
    }
    public function deleteActivity(Request $request){
        try {
            $pv = StuActPivot::where("activity_id",$request->activity_id)->where("student_id",$request->student_id)->where("period_id", $request->period_id)->where("grade_id", $request->grade_id)->first();
            $c = $pv->count();
            if($c > 0){
                $pv->delete();
                return response()->json(["message" =>'Öğrenci faaliyet ataması başarılı bir şekilde silindi.'], 200);
            } else {
                return response()->json(["message" =>'Sistemde olmayan bir atamayı silmeye çalışıyorsunuz.'], 200);
            }
        } catch(\Exception $exception){
         $errormsg = 'Faaliyet silme sırasında hata meydana geldi.';
         return response()->json(['errormsg'=>$errormsg]);
        }

    }
    public function createSchool(Request $request){
        $valid = Validator::make($request->all(), [
            'student_id'=>'required',
            'school_id'=>'required',
            'class_id'=>'required',
            'branch_id'=>'required',
        ]);
        if ($valid->fails()) {
             return response()->json($valid->errors(), 200);
        }
        try {
            // $c = $this->checkSchool($request);
            $st = Student::findOrFail($request->student_id);
            $c =  $st->schools()->count();
            if($c > 0){
              return response()->json(["message" =>'Öğrenciyi bir dönemde tek bir okula ekleyebilirsiniz.'], 200);
            }
           $sc = School::findOrFail($request->school_id);
           $attributes = [
              "clases_id"=>$request->class_id,
              "branches_id"=>$request->branch_id
          ];
          $st->schools()->save($sc, $attributes);
          return response()->json(["message" =>'Öğrenci okula başarılı bir şekilde eklendi.'], 201);
       } catch(\Exception $exception){
        $errormsg = 'Faaliyet ekleme sırasında hata meydana geldi.';
        return response()->json(['errormsg'=>$errormsg]);
       }
    }
    public function getSchool(Request $request){
        try {
            if($request->type === "ALL"){
             $pv = SSCBPivot::all();
            } else {
             $pv = SSCBPivot::where("$request->where", $request->id)->get();
            }
            $data = [
            ];
       
            foreach ($pv as $p) {
             $stdact = [
                "student"=>$p->student->s_name,
                "student_id"=>$p->student->id,
                 "school"=>$p->school->sName,
                 "school_id"=>$p->school->id,
                 "class"=>$p->clases->cName,
                 "class_id"=>$p->clases->id,
                 "branch"=>$p->branches->bName,
                 "branch_id"=>$p->branches->id
             ];
             array_push($data, $stdact);
            }
            return response()->json($data, 200);
           }catch(\Exception $exception){
            $errormsg = 'İşlem sırasında hata meydana geldi. Lütfen sonra deneyiniz.';
            return response()->json(['errormsg'=>$errormsg]);
        }
    }
    public function deleteSchool(Request $request){
        try {
            $pv = SSCBPivot::where("school_id",$request->school_id)->where("student_id",$request->student_id)->first();
            $c = $pv->count();
            if($c > 0){
                $pv->delete();
                return response()->json(["message" =>'Öğrenci okul ataması başarılı bir şekilde silindi.'], 200);
            } else {
                return response()->json(["message" =>'Sistemde olmayan bir atamayı silmeye çalışıyorsunuz.'], 200);
            }
        } catch(\Exception $exception){
         $errormsg = 'Faaliyet silme sırasında hata meydana geldi.';
         return response()->json(['errormsg'=>$errormsg]);
        }

    }
    public function createDetails(Request $request){

              //burada soft delete kontrolü var
            $valid = Validator::make($request->all(), [
                'illness'=>'required',
                'medicines'=>'required',
                'allergy'=>'required',
                'chronic_disease'=>'required',
                'scholarship'=>'required',
                'blood_group'=>'required',
                's_height'=>'required',
                's_weight'=>'required',
            ]);
             if ($valid->fails()) {
                 return response()->json($valid->errors(), 200);
            }
            try {

                $student = Student::find($request->student_id);
                $sd = null;
                // return $student->sdetail;
                if($student->sdetail){
                    $sd = SDetail::find($student->sdetail->id);
                }else {
                    $sd = new SDetail();
                }
                $sd->illness = $request->illness;
                $sd->medicines = $request->medicines;
                $sd->allergy = $request->allergy;
                $sd->chronic_disease = $request->chronic_disease;
                $sd->scholarship = $request->scholarship;
                $sd->photo_perm = $request->photo_perm ? 1 : 0;
                $sd->health_report = $request->health_report ? 1 : 0;
                $sd->blood_group = $request->blood_group;
                $sd->s_height = $request->s_height;
                $sd->s_weight = $request->s_weight;
                $sd->student_id = $request->student_id;
                if($sd->save()){
                  return response()->json($sd, 201);
                }
            } catch (\Throwable $exception) {
                return response()->json(['errormsg'=> $exception->getMessage()]);
            }
    }
    public function getClubs(Request $request){
        try {
            if($request->type === "ALL"){
             $pv = CTB::all();
            } else {
             $pv = CTB::where("$request->where", $request->id)->get();
            }
            $data = [
            ];
            foreach ($pv as $p) {
             $teambranchname = $p->team->stName . ' ' . $p->branch->sbName;
             $teambranch = [
                 "club_id"=>$p->club->id,
                 "teambranch"=>$teambranchname,
                 "team_id"=>$p->team->id,
                 "branch_id"=>$p->branch->id
               ];
               $club = [
                  "id"=>$p->club->id,
                  "name"=>$p->club->scName,
                  "code"=>$p->club->scCode,
                  "teambranch"=>$teambranch
               ];
             array_push($data, $club);
            }
            return response()->json($data, 200);
           }catch(\Exception $exception){
            $errormsg = 'İşlem sırasında hata meydana geldi. Lütfen sonra deneyiniz.';
            return response()->json(['errormsg'=>$errormsg]);
        }
    }
    public function createClub(Request $request){
        $valid = Validator::make($request->all(), [
            'student_id'=>'required',
            'club_id'=>'required',
            'team_id'=>'required',
            'branch_id'=>'required',
        ]);
        if ($valid->fails()) {
             return response()->json($valid->errors(), 200);
        }
        try {
            $c = $this->checkClub($request);
            if($c > 0){
              return response()->json(["message" =>'Öğrenciyi aynı takımın farklı şubelerine eklemeye çalışıyorsunuz.'], 200);
            }
            $sc = new CTBS();
            $sc->spor_club_id = $request->club_id;
            $sc->spor_club_branch_id = $request->branch_id;
            $sc->team_id = $request->team_id;
            $sc->student_id = $request->student_id;
            if($sc->save()){
                return response()->json(["message" =>'Öğrenci spor kulübüne başarılı bir şekilde eklendi.'], 201);
            }

       } catch(\Exception $exception){
        $errormsg = 'Spor Kulübüne ekleme sırasında hata meydana geldi.';
        return response()->json(['errormsg'=>$exception->getMessage()]);
       }
    }
    public function getStdClubs(Request $request){

        try {
            if($request->type === "ALL"){
             $pv = CTBS::all();
            } else {
             $pv = CTBS::where("$request->where", $request->id)->get();
            }
            $data = [
            ];
            foreach ($pv as $key=>$p) {
             $stdact = [
                "student"=>$p->student->s_name,
                "student_id"=>$p->student->id,
                 "club"=>$p->club->scName,
                 "club_id"=>$p->club->id,
                 "team"=>$p->team->stName,
                 "team_id"=>$p->team->id,
                 "branch"=>$p->branch->sbName,
                 "branch_id"=>$p->branch->id
             ];
             array_push($data, $stdact);
            }
            return response()->json($data, 200);
           }catch(\Exception $exception){
            $errormsg = 'İşlem sırasında hata meydana geldi. Lütfen sonra deneyiniz.';
            return response()->json(['errormsg'=>$exception->getMessage()]);
        }
    }
    public function deleteClub(Request $request){
        try {
            $pv = CTBS::where("spor_club_id",$request->club_id)->where("student_id",$request->student_id)->where("team_id", $request->team_id)->where("spor_club_branch_id", $request->branch_id)->first();
            $c = $pv->count();
            if($c > 0){
                $pv->delete();
                return response()->json(["message" =>'Öğrenci kulüp ataması başarılı bir şekilde silindi.'], 200);
            } else {
                return response()->json(["message" =>'Sistemde olmayan bir atamayı silmeye çalışıyorsunuz.'], 200);
            }
        } catch(\Exception $exception){
         $errormsg = 'Faaliyet silme sırasında hata meydana geldi.';
         return response()->json(['errormsg'=>$errormsg]);
        }

    }
    public function getDetails(){
        $sd = SDetail::all();
        return $sd;
    }
    public function proximities(){
        return Proximity::all();
    }
    public function createUsers(Request $request){
                     //burada soft delete kontrolü var
                     $valid = Validator::make($request->all(), [
                        "email"=>'unique:students,s_email,NULL,id,deleted_at,NULL',
                        'tc'=>'required|min:1|unique:students,s_tc,NULL,id,deleted_at,NULL',
                    ]);
                     if ($valid->fails()) {
                         return response()->json($valid->errors(), 200);
                    }
                    $s = new Student();
                    $s->s_name = $request->name;
                    $s->s_surname = $request->surname;
                    $s->s_email = $uniqueFakeEmail;
                    $s->s_birthday = $request->birthday;
                    $s->school_no = $request->schoolNo;
                    $s->s_tc = $request->tc;
                    $s->s_phone = $request->h_phone;
                    $s->s_gsm = $request->gsm;
                    $s->is_active = $request->isActive;
                    $s->s_gender = $request->gender;
                    $s->s_family = $request->family;
                    $s->s_address = $request->address;
                    if($s->save()){
                      return response()->json($s, 201);
                    }
    }
}
