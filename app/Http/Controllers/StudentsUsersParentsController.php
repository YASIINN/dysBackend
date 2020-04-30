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
use App\Models\ActivityPeriodPivot as AP;
use App\Models\SporClubTeamBranch as CTB;
use App\Models\Files;
use App\Models\SporClub;
use Validator;
use App\Models\StudentUserPivot as SUP;

use Unlu\Laravel\Api\QueryBuilder;


class StudentsUsersParentsController extends Controller
{
  public function testt(Request $request){
      $datas = CTB::all();
      return $this->_group_by($datas, "activity_id");
  }
  public function _group_by($array, $key) {
    $return = array();
    foreach($array as $val) {
        $return[$val[$key]][] = $val;
    }
    return $return;
}
public function filterDetailSearch(Request $request){
  if($request->type === 1){
      return School::all();
  } else if($request->type === 2) {
      $aps = AP::where("grade_id", "=", null)->get();

      $datas=[];
      foreach ($aps as $key => $ap) {
          $d = [
              "actper"=>$ap->activity->aName .' '. $ap->period->pName,
              "activity_id"=>$ap->activity->id,
              "period_id"=>$ap->period->id
          ];
          array_push($datas, $d);
      }
      return $datas;
  } else {
      return SporClub::all();
  }
}
public function clubStudents(Request $request){

    
  if($request->email != ''){
    $students = Student::with(["schools","clubs","teams", "sbranches", "activities", "periods", "grades", "clases", "sdetail", "file", "branches"])
->where("s_fullname", "like",$request->fullname.'%')
->whereHas("users", function($q) use ($request)
{
$q->where("uEmail","like", $request["email"].'%');
})
->whereHas("clubs", function($q) use ($request)
{
$q->where("spor_club_id", $request["club_id"]);
})
->paginate(10);

}
else {
$students = Student::with(["schools","clubs","teams", "sbranches", "activities", "periods", "grades", "clases", "sdetail", "file", "branches"])
->where("s_fullname", "like",$request->fullname.'%')
->whereHas("clubs", function($q) use ($request)
{
$q->where("spor_club_id", $request["club_id"]);
})
->paginate(10);
}
return $students;







    // $students = Student::with(["schools", "activities","clubs","teams","sbranches", "periods", "grades", "clases", "sdetail", "file", "branches"])
    // ->where("s_fullname", "like",$request->fullname.'%')
    // ->whereHas("users", function($q) use ($request)
    // {
    //   $q->where("uEmail","like", $request["email"].'%');
    // })->whereHas("clubs", function($q) use ($request)
    // {
    //   $q->where("spor_club_id", $request["club_id"]);
    // })
    // ->paginate(10);
    // return $students;
}
public function test(Request $request){
    if($request->detailSearch){
        if($request->type === 2){
            // return "actperpersonal";
             return $this->actperPersonals($request);
        } else if($request->type === 1){
            // return "okulpersonal";
             return $this->schoolPersonals($request);
        } else {
            // return "kulüppersonal";
        //  return $this->clubStudents($request)->count();
        }
      } else {
          return "nodetail";
      }

}
public function parentlist(Request $request){
    if($request->detailSearch){
    if($request->type === 2){
        return $this->actperParents($request);
    } else if($request->type === 1){
         return $this->schoolParents($request);
    } else {
        return $this->clubParents($request);
    }
  } else {
            $parents = Users::with(["file","unit","province","title", "students"])
        ->where("uFullName", "like",$request->fullname.'%')
        ->where("uEmail", "like",$request->email.'%')
            ->whereHas("utypes", function($q) use ($request)
    {
      $q->where("user_types_id", 2);
    })
    ->has("students")
    ->paginate(10);
    return $parents;
  }

}

public function actperParents(Request $request){
         $parents = Users::with(["file","unit","province","title", "students"])
        ->where("uFullName", "like",$request->fullname.'%')
        ->where("uEmail", "like",$request->email.'%')
            ->whereHas("utypes", function($q) use ($request)
    {
      $q->where("user_types_id", 2);
    })
    ->has("students")
    ->whereHas("students.periods", function($q) use ($request)
    {
      $q->where("period_id", $request["period_id"]);
    })
    ->whereHas("students.sactivities", function($q) use ($request)
    {
      $q->where("activity_id", $request["activity_id"]);
    })
    ->paginate(10);
    return $parents;
}
public function schoolParents(Request $request){
         $parents = Users::with(["file","unit","province","title", "students"])
        ->where("uFullName", "like",$request->fullname.'%')
        ->where("uEmail", "like",$request->email.'%')
            ->whereHas("utypes", function($q) use ($request)
    {
      $q->where("user_types_id", 2);
    })
    ->has("students")
    ->whereHas("students.schools", function($q) use ($request)
    {
      $q->where("school_id", $request["school_id"]);
    })
    ->paginate(10);
    return $parents;
}
public function clubParents(Request $request){
         $parents = Users::with(["file","unit","province","title", "students"])
        ->where("uFullName", "like",$request->fullname.'%')
        ->where("uEmail", "like",$request->email.'%')
            ->whereHas("utypes", function($q) use ($request)
    {
      $q->where("user_types_id", 2);
    })
    ->has("students")
    ->whereHas("students.clubs", function($q) use ($request)
    {
     $q->where("spor_club_id", $request["club_id"]);
    })
    ->paginate(10);
    return $parents;
}

public function employeelist(Request $request){
  if($request->detailSearch){
    if($request->type === 2){
        return $this->actperPersonals($request);
    } else if($request->type === 1){
         return $this->schoolPersonals($request);
    } else {
        return $this->clubPersonals($request);
    }
  } else {
        if($request->status){
        $employees = Users::with(["file","unit","province","title"])
        ->where("uFullName", "like",$request->fullname.'%')
        ->where("uEmail", "like",$request->email.'%')
        ->paginate(10);
        } else {
               $employees = Users::with(["file","unit","province","title"])->latest()->paginate(10);
        }
        return response()->json($employees);
  }
}
public function studentlist(Request $request){
  if($request->detailSearch){
    if($request->type === 2){
        return $this->actperStudents($request);
    } else if($request->type === 1){
         return $this->schoolStudents($request);
    } else {
        return $this->clubStudents($request);
    }
  } else {
        if($request->status){
        $students = Student::with(["schools", "activities", "periods","users", "grades", "clases", "sdetail", "file", "branches"])
              ->where("s_fullname", "like",$request->fullname.'%')
              ->whereHas("users", function($q) use ($request)
               {
                 $q->where("uEmail","like", $request["email"].'%');
               })
               ->paginate(10);
        } else {
               $students = Student::with(["schools", "activities", "periods", "grades", "clases", "sdetail", "file", "branches"])->latest()->paginate(10);
        }
        return response()->json($students);
  }
}
public function assignstudentlist(Request $request){
        // return $request;
        if($request->type === 1){
              $students = Student::with(["users","file"])
              ->where("s_fullname", "like",$request->fullname.'%')
              ->orWhereHas("users", function($q) use ($request)
               {
                 $q->where("uEmail","like", $request["email"].'%');
               })
               ->paginate(5);
               return response()->json($students);
        } else if($request->type === 2){
          $students = Student::with(["users","file"])
          ->where("s_fullname", "like",$request->fullname.'%')
          ->orWhereHas("users", function($q) use ($request)
           {
             $q->where("uEmail","like", $request["email"].'%');
           })
           ->paginate(5);
           return response()->json($students);
           return response()->json($students);
        }
        else if($request->type === 3){
          $students = Student::with(["users","file"])
          ->where("s_fullname", "like",$request->fullname.'%')
          ->orWhereHas("users", function($q) use ($request)
           {
             $q->where("uEmail","like", $request["email"].'%');
           })
           ->paginate(5);
           return response()->json($students);
        }
}
public function schoolStudents(Request $request){
    if($request->email != ''){
            $students = Student::with(["schools", "activities", "periods", "grades", "clases", "sdetail", "file", "branches"])
    ->where("s_fullname", "like",$request->fullname.'%')
    ->whereHas("users", function($q) use ($request)
    {
      $q->where("uEmail","like", $request["email"].'%');
    })
    ->whereHas("schools", function($q) use ($request)
    {
      $q->where("school_id", $request["school_id"]);
    })
    ->paginate(10);

    }
    else {
     $students = Student::with(["schools", "activities", "periods", "grades", "clases", "sdetail", "file", "branches"])
    ->where("s_fullname", "like",$request->fullname.'%')
    ->whereHas("schools", function($q) use ($request)
    {
      $q->where("school_id", $request["school_id"]);
    })
    ->paginate(10);
    }
      return $students;
}
public function actperStudents(Request $request){
      if($request->email != ''){
              $students = Student::with(["schools", "activities","periods", "grades", "clases", "sdetail", "file", "branches"])
    ->where("s_fullname", "like",$request->fullname.'%')
    ->whereHas("users", function($q) use ($request)
    {
      $q->where("uEmail","like", $request["email"].'%');
    })
    ->whereHas("periods", function($q) use ($request)
    {
      $q->where("period_id", $request["period_id"]);
    })
     ->whereHas("sactivities", function($q) use ($request)
     {
       $q->where("activity_id", $request["activity_id"]);
     })
    ->paginate(10);
      } else {
          $students = Student::with(["schools", "activities","periods", "grades", "clases", "sdetail", "file", "branches"])
    ->where("s_fullname", "like",$request->fullname.'%')
    ->whereHas("periods", function($q) use ($request)
    {
      $q->where("period_id", $request["period_id"]);
    })
     ->whereHas("sactivities", function($q) use ($request)
     {
       $q->where("activity_id", $request["activity_id"]);
     })
    ->paginate(10);
      }

      return $students;


}

public function actperPersonals(Request $request){
  /*    $user = Users::with(["pactivities", "periods", "utypes"])->find(3);
     return $user; */
    try {
      $employees = Users::with(["file","unit","province","title"])
        ->where("uFullName", "like",$request->fullname.'%')
        ->where("uEmail", "like",$request->email.'%')
            ->whereHas("utypes", function($q) use ($request)
    {
      $q->where("user_types_id", 1);
    })
    ->whereHas("periods", function($q) use ($request)
    {
      $q->where("period_id", $request["period_id"]);
    })
    ->whereHas("pactivities", function($q) use ($request)
    {
      $q->where("activity_id", $request["activity_id"]);
    })
    ->paginate(10);
    return $employees;
    } catch (\Throwable $th) {
      return response()->json($th->getMessage());
    }
}

public function schoolPersonals(Request $request){
   $employees = Users::with(["file","unit","province","title"])
  ->where("uFullName", "like",$request->fullname.'%')
  ->where("uEmail", "like",$request->email.'%')
    // ->whereHas("utypes", function($q) use ($request)
    // {
    //   $q->where("user_types_id", $request["utype_id"]);
    // })
    ->whereHas("uschools", function($q) use ($request)
    {
      $q->where("school_id", $request["school_id"]);
    })
    ->paginate(10);
    return $employees;
}
public function clubPersonals(Request $request){
    $personals = Users::with(["uclubs", "proximity", "utypes", "unit","province","title"])
    ->where("uFullName", "like",$request->fullname.'%')
    ->where("uEmail","like", $request["email"].'%')
    ->whereHas("utypes", function($q) use ($request)
    {
      $q->where("user_types_id", $request["utype_id"]);
    })
    ->whereHas("uclubs", function($q) use ($request)
    {
      $q->where("spor_club_id", $request["club_id"]);
    })
    ->paginate(10);
    return $personals;
}
public function testtfd(Request $request){
    if($request->type === 1){
        return School::all();
    } else if($request->type === 2) {
        $aps = AP::where("grade_id", "=", null)->get();
        $datas=[];
        foreach ($aps as $key => $ap) {
            $d = [
                "actper"=>$ap->activity->aName .' '. $ap->period->pName,
                "activity_id"=>$ap->activity->id,
                "period_id"=>$ap->period->id
            ];
            array_push($datas, $d);
            return $datas;
        }
    } else {
        return SporClub::all();
    }
     return $request;

    // $students = Student::with(["schools", "activities", "periods", "grades", "clases", "sdetail", "file", "branches"])->whereHas("periods", function($q) use ($request)
    // {
    //   $q->where("period_id", 2);
    // })->whereHas("activities", function($q) use ($request)
    // {
    //   $q->where("activity_id", 2);
    // })->paginate(10);

    // return $students;
}
}