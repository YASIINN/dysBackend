<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Storage;
use Illuminate\Support\Facades;

use App\Models\Student;
use App\Models\School;
use App\Models\Users;
use App\Models\Lessons;
use App\Models\ActivityStudentPivot as ASP;
use App\Models\Activity;
use App\Models\Grade;
use App\Models\ActivityPeriodPivot as AP;
use App\Models\SporClubTeamBranch as CTB;
use App\Models\Files;
use App\Models\SporClub;
use Validator;
use App\Models\StudentUserPivot as SUP; 

use Unlu\Laravel\Api\QueryBuilder;


class ClubProgramController extends Controller
{
public function getTeachers(Request $request){
    $club = SporClub::find($request->id);
    $map = $club->users->map(function($items)
    {
      $data["name"] = $items->uFullName;
      $data["id"] = $items->id;
      return $data;
   });
   return $map;
}
public function getGrades(Request $request){
  $ctbs = CTB::where("spor_club_id", $request->spor_club_id)->get();
  $datas = [];
  foreach ($ctbs as $key => $ctb) {
      $team = $ctb->team->stName;
      $branch = $ctb->branch->sbName;
      $d = [
          "spor_club_id"=>$ctb->spor_club_id,
          "team_id"=>$ctb->team_id,
          "sbranch_id"=>$ctb->sbranch_id,
          "name"=>$team . ' ' . $branch,
          "id"=>$ctb->sctbid

      ];
      array_push($datas, $d);
  }
  return $datas;
}
public function getLessons(Request $request){
    $acts = Lessons::with([])
    ->whereHas("activities", function($q) use ($request) 
    {
      $q->where("activity_id", $request["activity_id"]);
    })
    ->whereHas("periods", function($q) use ($request) 
    {
      $q->where("period_id", $request["period_id"]);
    })
    ->get();
    $map = $acts->map(function($items)
    {
      $data["name"] = $items->lName;
      $data["id"] = $items->id;
      $data["type"] = $items->type;
      $data["parent_id"] = $items->parent_id;
      return $data;
   });
   return $map;
  }
}
