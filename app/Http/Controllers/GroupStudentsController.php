<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Users;
use App\Models\Group;

class GroupStudentsController extends Controller
{
    public function groupStudents(Request $request)
    {
        try {
            if($request->search){
                $students = Student::where("s_fullname", "like", $request->fullname."%")->where("s_email", "like", $request->email."%")
                ->whereDoesntHave('groups', function($q) use ($request){
                      $q->where("id", $request["group_id"]);
                 })
                ->latest()
                ->paginate(5);
            } else {
                $students = Student::whereDoesntHave('groups', function($q) use ($request){
                    $q->where("id", $request["group_id"]);
                })->latest()->paginate(5);
            }
            if (!$students) {
                return [];
            }
            $map = $students->map(function ($content) {
                $data["id"] = $content->id;
                $data["fullname"] = $content->s_fullname;
                $data["email"] = $content->s_email;
                $data["image"] = $content->file->path;
                return $data;
            });
            $collection = collect($students);
            $collection->forget('data');
            $students = $collection->all();
            $students["data"] = $map;  
            return response()->json($students, 200);
           } catch (\Throwable $th) {
               return response()->json($th->getMessage(), 200);
           }

    }   

    public function groupMembers(Request $request)
    {
        try {
            if($request->search){
                $students = Student::where("s_fullname", "like", $request->fullname."%")->where("s_email", "like", $request->email."%")
                          ->whereHas('groups', function($q) use ($request){
                                $q->where("id", $request["group_id"]);
                           })
                          ->latest()
                          ->paginate(5);
            } else {
                $students = Student::whereHas('groups', function($q) use ($request){
                    $q->where("id", $request["group_id"]);
                })->latest()->paginate(5);
            }
            if (!$students) {
                return [];
            }
            $map = $students->map(function ($content) {
                $data["id"] = $content->id;
                $data["fullname"] = $content->s_fullname;
                $data["email"] = $content->s_email;
                $data["image"] = $content->file->path;
                return $data;
            });
            $collection = collect($students);
            $collection->forget('data');
            $students = $collection->all();
            $students["data"] = $map;  
            return response()->json($students, 200);
           } catch (\Throwable $th) {
               return response()->json($th->getMessage(), 200);
           }

    }   

    public function assignStudentToGroup(Request $request){
      try {
        $g = Group::findOrFail($request->group_id);
        $g->students()->attach($request->members);
        return response()->json(["message"=>"Kayıt başarılı"], 201);
      } catch (\Throwable $th) {
        return response()->json($th->getMessage(), 200);
      }
    }
    public function removeStudentFromGroup(Request $request) {
        try {
            $g = Group::findOrFail($request->group_id);
            $g->students()->detach($request->members);
            return response()->json(["message"=>"Kayıt başarılı"], 201);
          } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 200);
          }
    }

 
}
