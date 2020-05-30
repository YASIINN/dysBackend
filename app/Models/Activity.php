<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


use App\Models\Period;

class Activity extends Model
{ 
  
   protected $fillable = ["aName","aCode", "company_id"];
   public function periods(){
       return $this->belongsToMany(Period::class)->using(ActivityPeriodPivot::class)->withPivot('begin', 'end', 'grade_id');
   }

   public function lessons(){
    return $this->belongsToMany(Lessons::class, "lessons_id")->using(ActivityPeriodLessonPivot::class);
  }
    public  function users()
    {
        return $this->belongsToMany(Users::class, "activity_users");
    }


   public function uniqperiods(){
    return $this->belongsToMany(Period::class);
   }
   public function students(){
    return $this->belongsToMany(Student::class);
   }

   public function apgrades($id){
     $results = $this->periods()->where("period_id", $id)->wherePivot('grade_id', '!=', null)->get();
     $grades = [];
     foreach ($results as $period) {
         array_push($grades, $period->pivot->grade);
     }
     $collection = collect($grades);
     $unique_grades = $collection->unique()->values()->all();
     return $unique_grades;
   }
   public function apothers($request){
    if($request["other_id"] === "NULL"){
      $results = $this->periods()->where("period_id", $request["period_id"])->wherePivot("$request[other_where]", "!=", $request["other_id"])->get();
    }else {
      $results = $this->periods()->where("period_id", $request["period_id"])->wherePivot("$request[other_where]", $request["other_id"])->get();
    }
    $others = [];
    foreach ($results as $other) {
        switch ($request["type"]) {
          case 'grade':
            array_push($others, $other->pivot->grade);
            break;
          case 'student':
          array_push($others, $other->pivot->student);
          default:
            # code...
            break;
        }
    }
    $collection = collect($others);
    $unique_others = $collection->unique()->values()->all();
    return $unique_others;
  }
  public function grades(){
    return $this->belongsToMany(Grade::class, "activity_period");
  }
  public function uniqaperiods(){
    return $this->periods()->wherePivot('grade_id',null);
  }


     //adem
     public static function boot()
     {
         parent::boot();
         static::created(function ($act) {
             $g = new Group;
             $g->name = $act->aName;
             $g->code = $act->aCode;
             $act->group()->save($g);
         });
         static::deleting(function ($act) { // before delete() method call this
             $act->group()->delete();
         });
     }

  public function group()
  {
      return $this->morphOne(Group::class, 'groupable');
  }

}
