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
use App\Models\Group;
use App\Models\StudentUserPivot as SUP;

use App\Models\SchoolClasesPivot as SC;
use App\Models\SchoolClasesBranchesPivot as SCB;

use Unlu\Laravel\Api\QueryBuilder;


class TestController extends Controller
{

public function test(Request $ssp){
         
         //okul grubu
         $gs = Group::where("groupable_id", $ssp->school_id)->first();
         $gs->students()->attach([$ssp->student_id]);
 
         //okul sınıf grubu işlemleri
         $sc = SC::where(
             ["school_id"=>$ssp->school_id, "clases_id"=>$ssp->clases_id]
         )->first();
         $gsc = Group::where("groupable_id", $sc->scid)->first();
         $gsc->students()->attach([$ssp->student_id]);
 
         //okul sınıf sube grubu
 
         //okul sınıf grubu işlemleri
         $scb = SCB::where(
             ["school_id"=>$ssp->school_id, "clases_id"=>$ssp->clases_id, "branches_id"=>$ssp->branches_id]
         )->first();
         $gscb = Group::where("groupable_id", $scb->scbid)->first();
         $gscb->students()->attach([$ssp->student_id]);
         $a = "adem"; 

}
}
