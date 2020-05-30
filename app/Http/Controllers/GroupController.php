<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use App\Models\SporClub;
use App\Models\Team;
use App\Models\Group;
use App\Models\SchoolClasesPivot as SC;
use App\Models\SchoolClasesBranchesPivot as SCB;
use App\Models\SporClubTeamBranch as CTB;
use App\Models\Activity;
use App\Models\ActivityPeriodPivot as AP;

class GroupController extends Controller
{
    //okul grupları
    public function schoolgroup(Request $request){
        if($request->search){
            $schools = School::with("group")->where("sName", "like", $request->txt."%")->orWhere("sCode", "like", $request->txt."%")->has("group")->latest()->paginate(5);
        } else {
            $schools = School::with("group")->has("group")->latest()->paginate(5);
        }
        if (!$schools) {
            return [];
        }
        $map = $schools->map(function ($content) {
            $data["id"] = $content->group->id;
            $data["name"] = $content->group->name;
            $data["code"] = $content->group->code;
            $data["groupable_id"] = $content->group->groupable_id;
            $data["groupable_type"] = $content->group->groupable_type;
            return $data;
        });
        $collection = collect($schools);
        $collection->forget('data');
        $schools = $collection->all();
        $schools["data"] = $map;  
        return response()->json($schools, 200);
    }

    public function classgroup(Request $request){
        if($request->search){
            $schools = SC::with("group")->has("group")
            ->whereHas('group', function ($q) use($request) {
                $q->where('name', 'like', $request->txt."%");
            })
            ->orWhereHas('group', function ($q) use($request) {
                $q->where('code', 'like', $request->txt."%");
            })
            ->latest()->paginate(5);
            
        } else {
            $schools = SC::with("group")->has("group")->latest()->paginate(5);
        }
        if (!$schools) {
            return [];
        }
        $map = $schools->map(function ($content) {
            $data["id"] = $content->group->id;
            $data["name"] = $content->group->name;
            $data["code"] = $content->group->code;
            $data["groupable_id"] = $content->group->groupable_id;
            $data["groupable_type"] = $content->group->groupable_type;
            return $data;
        });
        $collection = collect($schools);
        $collection->forget('data');
        $schools = $collection->all();
        $schools["data"] = $map;  
        return response()->json($schools, 200);
    }

    public function branchgroup(Request $request){
        if($request->search){
            $schools = SCB::with("group")->has("group")
            ->whereHas('group', function ($q) use($request) {
                $q->where('name', 'like', $request->txt."%");
            })
            ->orWhereHas('group', function ($q) use($request) {
                $q->where('code', 'like', $request->txt."%");
            })
            ->latest()->paginate(5);
            
        } else {
            $schools = SCB::with("group")->has("group")->latest()->paginate(5);
        }
        if (!$schools) {
            return [];
        }
        $map = $schools->map(function ($content) {
            $data["id"] = $content->group->id;
            $data["name"] = $content->group->name;
            $data["code"] = $content->group->code;
            $data["groupable_id"] = $content->group->groupable_id;
            $data["groupable_type"] = $content->group->groupable_type;
            return $data;
        });
        $collection = collect($schools);
        $collection->forget('data');
        $schools = $collection->all();
        $schools["data"] = $map;  
        return response()->json($schools, 200);
    }
    //okul grupları son


     //spor kulubü grupları
     public function clubgroup(Request $request){
       try {
        if($request->search){
            $schools = SporClub::with("group")->where("scName", "like", $request->txt."%")->orWhere("scCode", "like", $request->txt."%")->has("group")->latest()->paginate(5);
        } else {
            $schools = SporClub::with("group")->has("group")->latest()->paginate(5);
        }
        if (!$schools) {
            return [];
        }
        $map = $schools->map(function ($content) {
            $data["id"] = $content->group->id;
            $data["name"] = $content->group->name;
            $data["code"] = $content->group->code;
            $data["groupable_id"] = $content->group->groupable_id;
            $data["groupable_type"] = $content->group->groupable_type;
            return $data;
        });
        $collection = collect($schools);
        $collection->forget('data');
        $schools = $collection->all();
        $schools["data"] = $map;  
        return response()->json($schools, 200);
       } catch (\Throwable $th) {
           return response()->json($th->getMessage(), 200);
       }
    }
    public function clubteamgroup(Request $request){
        if($request->search){
            $schools = Team::with("group")->has("group")
            ->whereHas('group', function ($q) use($request) {
                $q->where('name', 'like', $request->txt."%");
            })
            ->orWhereHas('group', function ($q) use($request) {
                $q->where('code', 'like', $request->txt."%");
            })
            ->latest()->paginate(5);
            
        } else {
            $schools = Team::with("group")->has("group")->latest()->paginate(5);
        }
        if (!$schools) {
            return [];
        }
        $map = $schools->map(function ($content) {
            $data["id"] = $content->group->id;
            $data["name"] = $content->group->name;
            $data["code"] = $content->group->code;
            $data["groupable_id"] = $content->group->groupable_id;
            $data["groupable_type"] = $content->group->groupable_type;
            return $data;
        });
        $collection = collect($schools);
        $collection->forget('data');
        $schools = $collection->all();
        $schools["data"] = $map;  
        return response()->json($schools, 200);
    }
    public function clubbranchgroup(Request $request){
        if($request->search){
            $schools = CTB::with("group")->has("group")
            ->whereHas('group', function ($q) use($request) {
                $q->where('name', 'like', $request->txt."%");
            })
            ->orWhereHas('group', function ($q) use($request) {
                $q->where('code', 'like', $request->txt."%");
            })
            ->latest()->paginate(5);
            
        } else {
            $schools = CTB::with("group")->has("group")->latest()->paginate(5);
        }
        if (!$schools) {
            return [];
        }
        $map = $schools->map(function ($content) {
            $data["id"] = $content->group->id;
            $data["name"] = $content->group->name;
            $data["code"] = $content->group->code;
            $data["groupable_id"] = $content->group->groupable_id;
            $data["groupable_type"] = $content->group->groupable_type;
            return $data;
        });
        $collection = collect($schools);
        $collection->forget('data');
        $schools = $collection->all();
        $schools["data"] = $map;  
        return response()->json($schools, 200);
    }
    //spor kulubü grupları son

        //faliyet grupları
        public function activitygroup(Request $request){
            try {
             if($request->search){
                 $schools = Activity::with("group")->where("aName", "like", $request->txt."%")->orWhere("aCode", "like", $request->txt."%")->has("group")->latest()->paginate(5);
             } else {
                 $schools = Activity::with("group")->has("group")->latest()->paginate(5);
             }
             if (!$schools) {
                 return [];
             }
             $map = $schools->map(function ($content) {
                 $data["id"] = $content->group->id;
                 $data["name"] = $content->group->name;
                 $data["code"] = $content->group->code;
                 $data["groupable_id"] = $content->group->groupable_id;
                 $data["groupable_type"] = $content->group->groupable_type;
                 return $data;
             });
             $collection = collect($schools);
             $collection->forget('data');
             $schools = $collection->all();
             $schools["data"] = $map;  
             return response()->json($schools, 200);
            } catch (\Throwable $th) {
                return response()->json($th->getMessage(), 200);
            }
         }
         public function activityperiodgroup(Request $request){
             if($request->search){
                 $schools = AP::where("grade_id", null)->with("group")->has("group")
                 ->whereHas('group', function ($q) use($request) {
                     $q->where('name', 'like', $request->txt."%");
                 })
                 ->orWhereHas('group', function ($q) use($request) {
                     $q->where('code', 'like', $request->txt."%");
                 })
                 ->latest()->paginate(5);
                 
             } else {
                 $schools =  AP::where("grade_id", null)->with("group")->has("group")->latest()->paginate(5);
             }
             if (!$schools) {
                 return [];
             }
             $map = $schools->map(function ($content) {
                 $data["id"] = $content->group->id;
                 $data["name"] = $content->group->name;
                 $data["code"] = $content->group->code;
                 $data["groupable_id"] = $content->group->groupable_id;
                 $data["groupable_type"] = $content->group->groupable_type;
                 return $data;
             });
             $collection = collect($schools);
             $collection->forget('data');
             $schools = $collection->all();
             $schools["data"] = $map;  
             return response()->json($schools, 200);
         }
         public function activitygradegroup(Request $request){
             if($request->search){
                 $schools = AP::where("grade_id","!=", null)->with("group")->has("group")
                 ->whereHas('group', function ($q) use($request) {
                     $q->where('name', 'like', $request->txt."%");
                 })
                 ->orWhereHas('group', function ($q) use($request) {
                     $q->where('code', 'like', $request->txt."%");
                 })
                 ->latest()->paginate(5);
                 
             } else {
                 $schools = AP::where("grade_id","!=", null)->with("group")->has("group")->latest()->paginate(5);
             }
             if (!$schools) {
                 return [];
             }
             $map = $schools->map(function ($content) {
                 $data["id"] = $content->group->id;
                 $data["name"] = $content->group->name;
                 $data["code"] = $content->group->code;
                 $data["groupable_id"] = $content->group->groupable_id;
                 $data["groupable_type"] = $content->group->groupable_type;
                 return $data;
             });
             $collection = collect($schools);
             $collection->forget('data');
             $schools = $collection->all();
             $schools["data"] = $map;  
             return response()->json($schools, 200);
         }
         //faaliyet grupları son




}
