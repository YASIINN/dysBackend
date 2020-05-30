<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Users;
use App\Models\Group;

class GroupUserController extends Controller
{
    public function groupUsers(Request $request)
    {
        try {
            if($request->search){
                $students = Users::where("uFullName", "like", $request->fullname."%")->where("uEmail", "like", $request->email."%")
                ->whereDoesntHave('groups', function($q) use ($request){
                      $q->where("id", $request["group_id"]);
                 })
                     ->whereHas("utypes", function($q)
                {
                  $q->where("user_types_id", 1);
                })
                ->latest()
                ->paginate(5);
            } else {
                $students = Users::whereDoesntHave('groups', function($q) use ($request){
                    $q->where("id", $request["group_id"]);
                })
                ->whereHas("utypes", function($q)
                {
                  $q->where("user_types_id", 1);
                })
                ->latest()->paginate(5);
            }
            if (!$students) {
                return [];
            }
            $map = $students->map(function ($content) {
                $data["id"] = $content->id;
                $data["fullname"] = $content->uFullName;
                $data["email"] = $content->uEmail;
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
    
    public function groupParents(Request $request)
    {
        try {
            if($request->search){
                $students = Users::where("uFullName", "like", $request->fullname."%")->where("uEmail", "like", $request->email."%")
                ->whereDoesntHave('groups', function($q) use ($request){
                      $q->where("id", $request["group_id"]);
                 })
                     ->whereHas("utypes", function($q)
                {
                  $q->where("user_types_id", 2);
                })
                ->latest()
                ->paginate(5);
            } else {
                $students = Users::whereDoesntHave('groups', function($q) use ($request){
                    $q->where("id", $request["group_id"]);
                })
                ->whereHas("utypes", function($q)
                {
                  $q->where("user_types_id", 2);
                })
                ->latest()->paginate(5);
            }
            if (!$students) {
                return [];
            }
            $map = $students->map(function ($content) {
                $data["id"] = $content->id;
                $data["fullname"] = $content->uFullName;
                $data["email"] = $content->uEmail;
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
    public function groupParentMembers(Request $request)
    {
        try {
            if($request->search){
                $students = Users::where("uFullName", "like", $request->fullname."%")->where("uEmail", "like", $request->email."%")
                ->whereHas('groups', function($q) use ($request){
                      $q->where("id", $request["group_id"]);
                 })
              ->whereHas("utypes", function($q)
                {
                  $q->where("user_types_id", 2);
                })
                ->latest()
                ->paginate(5);
            } else {
                $students = Users::whereHas('groups', function($q) use ($request){
                    $q->where("id", $request["group_id"]);
                })
                ->whereHas("utypes", function($q)
                {
                  $q->where("user_types_id", 2);
                })
                ->latest()->paginate(5);
            }
            if (!$students) {
                return [];
            }
            $map = $students->map(function ($content) {
                $data["id"] = $content->id;
                $data["fullname"] = $content->uFullName;
                $data["email"] = $content->uEmail;
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

    public function groupUserMembers(Request $request)
    {
        try {
            if($request->search){
                $students = Users::where("uFullName", "like", $request->fullname."%")->where("uEmail", "like", $request->email."%")
                ->whereHas('groups', function($q) use ($request){
                      $q->where("id", $request["group_id"]);
                 })
              ->whereHas("utypes", function($q)
                {
                  $q->where("user_types_id", 1);
                })
                ->latest()
                ->paginate(5);
            } else {
                $students = Users::whereHas('groups', function($q) use ($request){
                    $q->where("id", $request["group_id"]);
                })
                ->whereHas("utypes", function($q)
                {
                  $q->where("user_types_id", 1);
                })
                ->latest()->paginate(5);
            }
            if (!$students) {
                return [];
            }
            $map = $students->map(function ($content) {
                $data["id"] = $content->id;
                $data["fullname"] = $content->uFullName;
                $data["email"] = $content->uEmail;
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



    public function assignParentToGroup(Request $request){
        try {
          $g = Group::findOrFail($request->group_id);
          $g->users()->attach($request->members);
          return response()->json(["message"=>"Kayıt başarılı"], 201);
        } catch (\Throwable $th) {
          return response()->json($th->getMessage(), 200);
        }
      }
      public function removeParentFromGroup(Request $request) {
          try {
              $g = Group::findOrFail($request->group_id);
              $g->users()->detach($request->members);
              return response()->json(["message"=>"Kayıt başarılı"], 201);
            } catch (\Throwable $th) {
              return response()->json($th->getMessage(), 200);
            }
      }
}
