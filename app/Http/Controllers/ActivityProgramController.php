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


class ActivityProgramController extends Controller
{
public function getTeachers(Request $request){
      $lesson = Lessons::find($request->lesson_id);
      $teachers = array();
      if($lesson->type === 1){
        $lessons = Lessons::where("parent_id", $lesson->id)->get();
        foreach ($lessons as $key => $ls) {
            $request['lesson_id'] = $ls->id;
            $optteachers = $this->getOptinalLessonTeachers($request);
            $collection = collect($optteachers);
            $merged = $collection->merge($teachers);
            $teachers = $merged->all();
        }
        $collection = collect($teachers);
        $unique = $collection->unique('id');
        $teachers = $unique->values()->all();
      } else {
        $teachers = $this->getMainLessonTeachers($request);
      }
      return $teachers;
}
public function getOptinalLessonTeachers(Request $request){
  $acts = Users::with([])
  ->whereHas("ulactivities", function($q) use ($request) 
  {
    $q->where("activity_id", $request["activity_id"]);
  })
  ->whereHas("ulperiods", function($q) use ($request) 
  {
    $q->where("period_id", $request["period_id"]);
  })
  ->whereHas("uaplessons", function($q) use ($request) 
  {
    $q->where("lesson_id", $request["lesson_id"]);
  })
  ->get();
  $map = $acts->map(function($items)
  {
    $data["name"] = $items->uFullName;
    $data["id"] = $items->id;
    return $data;
 });
 return $map;
}
public function getMainLessonTeachers(Request $request){
  $acts = Users::with([])
  ->whereHas("ulactivities", function($q) use ($request) 
  {
    $q->where("activity_id", $request["activity_id"]);
  })
  ->whereHas("ulperiods", function($q) use ($request) 
  {
    $q->where("period_id", $request["period_id"]);
  })
  ->whereHas("uaplessons", function($q) use ($request) 
  {
    $q->where("lesson_id", $request["lesson_id"]);
  })
  ->get();
  $map = $acts->map(function($items)
  {
    $data["name"] = $items->uFullName;
    $data["id"] = $items->id;
    return $data;
 });
 return $map;
}
public function getGrades(Request $request){
  $acts = Grade::with([])
  // ->doesntHave('apschedules')
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
    $data["name"] = $items->gName;
    $data["id"] = $items->id;
    return $data;
 });
 return $map;
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
