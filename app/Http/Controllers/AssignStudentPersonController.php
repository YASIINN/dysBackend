<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Illuminate\Support\Facades;

use App\Models\Student;
use App\Models\School;
use App\Models\Users;
use App\Models\ActivityStudentPivot as ASP;
use App\Models\SchoolStudentPivot as SCS;
use App\Models\Activity;
use App\Models\ActivityPeriodPivot as AP;
use App\Models\SporClubTeamBranch as CTB;
use App\Models\ClubTeamBranchStudentPivot as CTBS;
use App\Models\Files;
use App\Models\SporClub;
use Validator;
use App\Models\StudentUserPivot as SUP; 

use Unlu\Laravel\Api\QueryBuilder;


class AssignStudentPersonController extends Controller
{
public function checkActPer($request){
    $activity_id = $request["activity_id"];
    $student_id= $request["student_id"];
    $pv = ASP::where("activity_id",$activity_id)->where("student_id",$student_id)->where("period_id", $request["period_id"])->get();
    return $pv->count();
}
public function checkClub($request){
  $club_id = $request["club_id"];
  $student_id= $request["student_id"];
  $pv = CTBS::where("spor_club_id",$club_id)->where("student_id",$student_id)->where("team_id", $request["team_id"])->get();
  return $pv->count();
}
public function assignstudent(Request $request){
  if($request->type === 1){
    return $this->assignStudentToSchool($request);
  }
  else if($request->type === 2){
    return $this->assignStudentToAP($request);
  }else if($request->type === 3){
    return $this->assignStudentToClub($request);
  }
}
public function assignStudentToSchool(Request $request){
  try {
    foreach ($request->data as $key=>$value) {
        $valid = Validator::make($value, [
        'school_id'=>'required',
        'class_id'=>'required',
        'branch_id'=>'required',
        'student_id'=>'required',
        ]);
        if ($valid->fails()) {
         return response()->json(["message" =>'Eksik data gönderimi.'], 200);
        }
        $st = Student::findOrFail($value["student_id"]);
        $c =  $st->schools()->count();

        if($c == 0){
          $scs = new SCS();
          $scs->student_id = $value["student_id"];
          $scs->school_id = $value["school_id"];
          $scs->clases_id = $value["class_id"];
          $scs->branches_id = $value["branch_id"];
          $scs->save();
        }
    }
    return response()->json(["message" =>'Seçilen öğrenciler başarıyla eklendi.'], 201);
   } catch(\Exception $exception){
    $errormsg = 'Faaliyet ekleme sırasında hata meydana geldi.';
    return response()->json(['errormsg'=>$errormsg]);
   }
}
public function assignStudentToAP(Request $request){
  try {
    foreach ($request->data as $key=>$value) {
        $valid = Validator::make($value, [
        'activity_id'=>'required',
        'period_id'=>'required',
        'grade_id'=>'required',
        'student_id'=>'required',
        ]);
        if ($valid->fails()) {
         return response()->json(["message" =>'Eksik data gönderimi.'], 200);
        }
        $c = $this->checkActPer($value);
        if($c == 0){
          $asp = new ASP();
          $asp->student_id = $value["student_id"];
          $asp->activity_id = $value["activity_id"];
          $asp->period_id = $value["period_id"];
          $asp->grade_id = $value["grade_id"];
          $asp->save();
        }
    }
    return response()->json(["message" =>'Seçilen öğrenciler başarıyla eklendi.'], 201);
   } catch(\Exception $exception){
    $errormsg = 'Faaliyet ekleme sırasında hata meydana geldi.';
    return response()->json(['errormsg'=>$errormsg]);
   }
}

public function assignStudentToClub(Request $request){
  try {
    foreach ($request->data as $key=>$value) {
        $valid = Validator::make($value, [
        'club_id'=>'required',
        'team_id'=>'required',
        'branch_id'=>'required',
        'student_id'=>'required',
        ]);
        if ($valid->fails()) {
         return response()->json(["message" =>'Eksik data gönderimi.'], 200);
        }
        $c = $this->checkClub($value);
        if($c == 0){
          $ctbs = new CTBS();
          $ctbs->student_id = $value["student_id"];
          $ctbs->spor_club_id = $value["club_id"];
          $ctbs->team_id = $value["team_id"];
          $ctbs->spor_club_branch_id = $value["branch_id"];
          $ctbs->save();
        }
    }
    return response()->json(["message" =>'Seçilen öğrenciler başarıyla eklendi.'], 201);
   } catch(\Exception $exception){
    $errormsg = 'Faaliyet ekleme sırasında hata meydana geldi.';
    return response()->json(['errormsg'=>$errormsg]);
   }
}
public function assignperson(Request $request){
   return "person";
}
}
