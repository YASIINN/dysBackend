<?php

namespace App\Http\Controllers;

use App\Models\PType;
use App\Models\School;
use App\Models\SchoolPType;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Cache;

class SchoolPTypeController extends Controller
{
    public function destroy($id)
    {
        try {
            if (SchoolPType::destroy($id)) {
                Cache::forget("allprogramtype");
                return response()->json('Success.', 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function getAll()
    {

        try {
            $data = array();
            $schools = PType::with(['school'])->has('school')->get();
            foreach ($schools as $sitem) {
                for ($i = 0; $i < count($sitem->school); $i++) {
                    array_push($data, [
                        "pivotid" => $sitem->school[$i]->pivot->id,
                        "programtypeid" => $sitem->id,
                        "programtypename" => $sitem->ptName,
                        "schoolid" => $sitem->school[$i]->id,
                        "schoolname" => $sitem->school[$i]->sName,
                    ]);
                }
            }

            return response()->json($data, 200);
            // if (Cache::has("allprogramtype")) {

            //     $type = Cache::get("allprogramtype");
            //     return response()->json($type, 200);
            // } else {
            //     $data = array();
            //     $schools = PType::with(['school'])->has('school')->get();
            //     foreach ($schools as $sitem) {
            //         for ($i = 0; $i < count($sitem->school); $i++) {
            //             array_push($data, [
            //                 "pivotid" => $sitem->school[$i]->pivot->id,
            //                 "programtypeid" => $sitem->id,
            //                 "programtypename" => $sitem->ptName,
            //                 "schoolid" => $sitem->school[$i]->id,
            //                 "schoolname" => $sitem->school[$i]->sName,
            //             ]);
            //         }
            //     }
            //     Cache::set("allprogramtype", $data);
            //     $type = Cache::get("allprogramtype");
            //     return response()->json($type, 200);
            // }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function show($id)
    {
        try {
            $data = array();
            $schoolptype = SchoolPType::find($id);
            $ptype = $schoolptype->p_type;
            $school = $schoolptype->school;
            array_push($data, [
                "id" => $id,
                "programtypeid" => $ptype->id,
                "programtypename" => $ptype->ptName,
                "schoolid" => $school->id,
                "schoolname" => $school->sName,
            ]);
            return $data;
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'schoolid' => 'required',
                'ptypeid' => 'required'
            ]);
            $isHave = SchoolPType::where([
                ['school_id', "=", $request->schoolid],
                ['p_type_id', "=", $request->ptypeid],
            ])->get();
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            } else if (count($isHave) > 0) {
                return response()->json([], 204);
            } else {
                $schoolptype = new SchoolPType();
                $schoolptype->school_id = $request->schoolid;
                $schoolptype->p_type_id = $request->ptypeid;
                if ($schoolptype->save()) {
                    Cache::forget("allprogramtype");
                    return response()->json($schoolptype, 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
