<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Users;
use App\Models\Group;

class GroupMemberController extends Controller
{
    public function doesntMembers(Request $request){
        if($request->type === "students"){
            $students = new GroupStudentsController();
            return $students->groupStudents($request);
        } else if($request->type === "parents"){
            $parents = new GroupUserController;
            return $parents->groupParents($request);
        } else if($request->type === "users"){
            $users = new GroupUserController;
            return $users->groupUsers($request);
        }
    }

    public function hasMembers(Request $request){
        if($request->type === "students"){
            $students = new GroupStudentsController();
            return $students->groupMembers($request);
        } else if($request->type === "parents"){
            $parents = new GroupUserController;
            return $parents->groupParentMembers($request);
        } else if($request->type === "users"){
            $users = new GroupUserController;
            return $users->groupUserMembers($request);
        }
    }
    public function assignMemberToGroup(Request $request){
        if($request->type === "students"){
            $students = new GroupStudentsController();
            return $students->assignStudentToGroup($request);
        } else if($request->type === "parents"){
            $parents = new GroupUserController;
            return $parents->assignParentToGroup($request);
        } else if($request->type === "users"){
            $users = new GroupUserController;
            return $users->assignParentToGroup($request);
        }
      }
      public function removeMemberFromGroup(Request $request) {
        if($request->type === "students"){
            $students = new GroupStudentsController();
            return $students->removeStudentFromGroup($request);
        } else if($request->type === "parents"){
            $parents = new GroupUserController;
            return $parents->removeParentFromGroup($request);
        } else if($request->type === "users"){
            $users = new GroupUserController;
            return $users->removeParentFromGroup($request);
        }
      }
}
