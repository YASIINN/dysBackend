<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Validator;
use App\Models\Activity;
use App\Models\Period;
use App\Models\Student;
use App\Models\ActivityPeriodPivot as AP;

class ActivityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $acts = Activity::all();
        return response()->json($acts, 200);
    }

    public function withperiods()
    {
        $actwithperiods = Activity::with("periods")->get();
        $collection = collect($actwithperiods);
        $unique_periods = $collection->unique()->values()->all();
        return $unique_periods;
    }

    public function uniqaperiods(Request $request)
    {
        $acts = Activity::with(["uniqaperiods"])
            ->get();
        return $acts;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //burada soft delete kontrolü var
        $valid = Validator::make($request->all(), [
            'name' => 'required|min:1|unique:activities,aName,NULL,id',
            'code' => 'required|min:1|unique:activities,aCode,NULL,id',

        ]);
        if ($valid->fails()) {
            return response()->json($valid->errors(), 200);
        }
        $act = new Activity();
        $act->aName = $request->name;
        $act->aCode = $request->code;
        if ($act->save()) {
            return response()->json($act, 201);
        }
    }

    public function show($id)
    {
        $act = Activity::find($id);
        $collection = collect($act->uniqperiods);
        $unique_periods = $collection->unique()->values()->all();
        return $unique_periods;

    }

    public function edit($id)
    {

    }


    /*Yasin*/

    public function activityPeriodClass(Request $request)
    {
        try {
            $acts = Grade::with([])
                // ->doesntHave('apschedules')
                ->whereHas("activities", function ($q) use ($request) {
                    $q->where("activity_id", $request->actid);
                })
                ->whereHas("periods", function ($q) use ($request) {
                    $q->where("period_id", $request->perid);
                })
                ->get();
            return $acts;
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);

        }
    }

    public function activityWithPeriods($id)
    {
        try {
            $periods = Activity::with(['uniqperiods'])->where('id', $id)->get();
            $collection = collect($periods[0]->uniqperiods);
            $unique = $collection->unique('pName');
            $uniquePeriods = $unique->values()->all();
            return $uniquePeriods;
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);

        }

    }

    /**/
    public function update(Request $request, $id)
    {
        try {
            $act = Activity::findOrFail($id);
        } catch (\Exception $exception) {
            $errormsg = 'Faaliyet Bulunamadı';
            return response()->json(['errormsg' => $errormsg]);
        }
        $valid = Validator::make($request->all(), [
            'name' => 'required|min:1|unique:activities,aName,' . $id . ',id',
            'code' => 'required|min:1|unique:activities,aCode,' . $id . ',id',

        ]);
        if ($valid->fails()) {
            return response()->json($valid->errors(), 200);
        }

        $act->aName = $request->name;
        $act->aCode = $request->code;
        if ($act->update()) {
            return response()->json($act, 201);
        }
    }

    public function destroy($id)
    {
        try {
            $act = Activity::findOrFail($id);
        } catch (\Exception $exception) {
            $errormsg = 'Faaliyet Bulunamadı';
            return response()->json(['errormsg' => $errormsg]);
        }
        if (count($act->uniqperiods) > 0) {
            $aps = AP::where("activity_id", $act->id)->get();
            foreach ($aps as $key => $ap) {
               $ap->delete();
            }
            // $act->uniqperiods()->detach();
        }
        if ($act->delete()) {
            return response()->json(["message" => 'Faliyet başarıyla silindi.'], 200);
        }
    }
}
