<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Illuminate\Support\Facades;

use App\Models\Student;
use App\Models\School;
use App\Models\Users;
use App\Models\ActivityStudentPivot as ASP;
use App\Models\Activity;
use App\Models\Grade;
use App\Models\Lessons;
use App\Models\ActivityProgram as ACTP;
use App\Models\SchoolProgram as SP;
use App\Models\SchoolProgramContent as SPC;
use App\Models\ActivityProgramContent as APC;
use App\Models\ActivityProgramContentUserPivot as APCU;
use App\Models\ActivityPeriodPivot as AP;
use App\Models\SporClubTeamBranch as CTB;
use App\Models\Files;
use App\Models\SporClub;
use App\Models\Discontinuity;
use App\Models\SchoolProgramContent;
use Validator;
use App\Models\StudentUserPivot as SUP;

use Unlu\Laravel\Api\QueryBuilder;


class TestController extends Controller
{

public function test(Request $request){

   return Storage::url('public/students/96440121108.jpeg');


  return Storage::download('students/96440121108.jpeg');
  return $url;

 $sp = SPC::with(["discounts"])
            ->where("school_program_id", 1)
            ->whereHas("discounts", function($q){
               $q->where("d_type", 2);
            })
         ->get();
 return $sp;


  $contents = Discontinuity::all();	
  // return $content->discounts;
  $data = [];
  foreach ($contents as $key => $discont) {
    array_push($data, $discont->contentable);
  }
 
  return $data;
$d = new Discontinuity;
$d->discontDate = "deneme";
$d->student_id = 1;
$d->d_type_id = 1;
 
$content->discounts()->save($d);
}
}
