<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Activity;
use App\Models\Period;
use App\Models\ActivityPeriodPivot as AP;
use App\Models\ActivityPeriodLessonPivot as APL;
use App\Models\Grade;
use App\Models\Lessons;
use Validator;

class ActPerPivotController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function checkActPer(Request $request){
        $ap = AP::where("activity_id", $request->activity_id)
                 ->where("period_id", $request->period_id)
                 ->get();
        return $ap->count();
    }
    public function index()
    {
        //
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
    public function checkOther($request){
        $activity_id = $request["activity_id"];
        $period_id= $request["period_id"];
        $pv = AP::where("activity_id",$activity_id)->where("period_id",$period_id)->where($request['other_where'], $request["other_id"])->get();
        return $pv->count();
    }

    public function others(Request $request){
         // $act = Activity::find(2);
        // return $act::with("periods")->get();

           try {
            $pv = AP::where("$request->where", $request->id)->get();
            $data = [
            ];
            foreach ($pv as $p) {
                return $p;
            //    $actperiod = [
            //        "activity"=>$p->activity->aName,
            //        "period"=>$p->period->pName,
            //        "activity_id"=>$p->activity->id,
            //        "period_id"=>$p->period->id,
            //        "pivot_table_id"=>$p->id
            //    ];
            //    array_push($data, $actperiod);
            }
            return $data;
           }catch(\Exception $exception){
            $errormsg = 'İşlem sırasında hata meydana geldi. Lütfen sonra deneyiniz.';
            return response()->json(['errormsg'=>$errormsg]);
        }
        }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $act = Activity::find($request->activity_id);
        $pr = Period::find($request->period_id);
        $attributes = [];
        if($request->type == "date"){
            $request["grade_id"] = null;
            $attributes["begin"]=$request->begin;
            $attributes["end"]=$request->end;
        }
        $c = $this->checkActPer($request);
        if($c == 0){
            $ap = new AP();
            $ap->activity_id = $request->activity_id;
            $ap->period_id = $request->period_id;
            $ap->begin = $request->begin;
            $ap->end  = $request->end;
            $ap->save();
            return response()->json(["message"=>"Kayıt atama işlemi başarılıdır."], 201);
        } else {
            return response()->json(["message"=>"Bu kayıt sisteme daha önce eklenmiştir."], 200);
        }
    }
    public function storemultiple(Request $request)
    {
       try {
        foreach ($request->data as $key=>$value) {
            $c = $this->checkOther($value);
            if($c > 0){
              return response()->json(["message" =>'Bu faaliyet ataması daha önce yapılmıştır.'], 200);
            } else {
               $this->addActPerOther($value);
            }
        }
        return response()->json(["message" =>'Faaliyet başarıyla eklendi.'], 201);
       } catch(\Exception $exception){
        $errormsg = 'Faaliyet ekleme sırasında hata meydana geldi.';
        return response()->json(['errormsg'=>$errormsg]);
       }
    }
    public function addActPerOther($request){
        $act = Activity::find($request["activity_id"]);
        $pr = Period::find($request["period_id"]);
        $attributes = [];
        switch ($request["type"]) {
            case 'date':
                $attributes["begin"]=$request["begin"];
                $attributes["end"]=$request["end"];
                break;
            case "grade":
                $attributes["grade_id"] = $request["other_id"];
                break;
            case "student":
                $attributes["student_id"] = $request["other_id"];
                $attributes["grade_id"] = $request["grade_id"];
                break;
            default:
                #
                break;
        }
        // if($request["type"] == "date"){

        // } else if($request["type"] == "student"){
        //     $attributes["student_id"] = $request["other_id"];
        // }
        $act->uniqperiods()->save($pr, $attributes);
        return $request;
    }


    public function actperothers(Request $request)
    {
        $act = Activity::find($request->activity_id);
        $allOthers = [];
        switch ($request->type) {
            case 'grade':
                $allOthers = Grade::all();
                break;
            case 'lesson':
                $allOthers = Lessons::all();
                break;
            default:
                # code...
                break;
        }
        $q = [
            "period_id"=>$request->period_id,
            "other_where"=>$request->other_where,
            "other_id"=>$request->other_id,
            "type"=>$request->type
        ];
       $actperothers = $act->apothers($q);
        $diffOthers = $allOthers->diff($actperothers);
        $result = [
            'actperInOthers'=>$actperothers,
            'actperNoOthers'=>$diffOthers->all()
        ];
        return $result;
    }
    public function addActPerLesson(Request $request){
        // return $request;
        $valid = Validator::make($request->all(), [
            'activity_id'=>'required',
            'period_id'=>'required',
            'lessons_id'=>'required',
        ]);
        if ($valid->fails()) {
             return response()->json($valid->errors(), 200);
        }
        try {
            // $c = $this->checkSchool($request);
            $pv = APL::where("activity_id", $request->activity_id)->where("period_id", $request->period_id)->where("lessons_id", $request->lessons_id)->get();
            $c =  $pv->count();
            if($c > 0){
              return response()->json(["message" =>'Bu faaliyet takvimine bu sınıfı daha önce eklemişsiniz.'], 200);
            }
            $pvl = new APL();
            $pvl->activity_id = $request->activity_id;
            $pvl->period_id = $request->period_id;
            $pvl->lessons_id = $request->lessons_id;
            $pvl->save();
          return response()->json(["message" =>'Ders faaliyet ataması başarıyla gerçekleştirildi.'], 201);
       } catch(\Exception $exception){
        $errormsg = 'Faaliyet ekleme sırasında hata meydana geldi.';
        return response()->json(['errormsg'=>$errormsg]);
       }
    }

    public function deleteActPerLesson(Request $request){
        try {
            $pvl = APL::where("activity_id",$request->activity_id)->where("period_id",$request->period_id)->where("lessons_id",$request->lessons_id)->first();
            $c = $pvl->count();
            if($c > 0){
                $pvl->delete();
                return response()->json(["message" =>'Ders faaliyet ataması başarılı bir şekilde silindi.'], 200);
            } else {
                return response()->json(["message" =>'Sistemde olmayan bir atamayı silmeye çalışıyorsunuz.'], 200);
            }
        } catch(\Exception $exception){
         $errormsg = 'Faaliyet ders atama silme sırasında hata meydana geldi.';
         return response()->json(['errormsg'=>$errormsg]);
        }
    }
    public function aplessons(Request $request){
        try {
         if($request->type === "ALL"){
          $pv = APL::all();
         } else {
          $pv = APL::where("activity_id", $request->activity_id)->where("period_id", $request->period_id)->get();
         }
         $data = [
         ];
         foreach ($pv as $p) {
           array_push($data, $p->lessons);
         }
         $allLessons = Lessons::where("parent_id", 0)->get();

         $diffLessons = $allLessons->diff($data);
         $result = [
            'actperInLessons'=>$data,
            'actperNoLessons'=>$diffLessons->all()
        ];
         return response()->json($result, 200);
        }catch(\Exception $exception){
         $errormsg = 'İşlem sırasında hata meydana geldi. Lütfen sonra deneyiniz.';
         return response()->json($exception->getMessage());
     }
    }
    public function delActPerOther(Request $request){
        try {
            $act = Activity::findOrFail($request->activity_id);
        } catch(\Exception $exception){
            $errormsg = 'Faaliyet Bulunamadı';
            return response()->json(['errormsg'=>$errormsg]);
        }
        $c = $this->checkOther($request);
        if($c > 0){
            $act->uniqperiods()->where('id', $request->period_id)->wherePivot("$request->other_where", $request->other_id)->detach();
            return response()->json(["message" =>'Kayıt başarıyla silindi.'], 200);
        } else {
            return response()->json(["message" =>'Sistemde böyle bir kayıt yok.'], 202);
        }
    }

    public function show($id)
    {
        return "adem";
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function deleteActPer(Request $request){
        try {
            $act = AP::where("activity_id", $request->activity_id)
            ->where("period_id", $request->period_id)
            ->get();
            foreach ($act as $key => $ap) {
                $ap->delete();
            }
        } catch(\Exception $exception){
            $errormsg = 'Data Bulunamadı';
            return response()->json(['errormsg'=>$errormsg]);
        }
    }
    public function getAPList(){
        try {
            $aps = AP::where("grade_id", null)->get();
            $data = [
            ];
            foreach ($aps as $key => $ap) {
              $d = [
                 "activity"=>$ap->activity->aName,
                 "activity_id"=>$ap->activity->id,
                 "period"=>$ap->period->pName,
                 "period_id"=>$ap->period->id,
                 "pivot"=>[
                   "activity_id"=>$ap->activity->id,
                   "period_id"=>$ap->period->id,
                 ]
              ];
              array_push($data, $d);
            }
            return response()->json($data, 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 204);
        }
    }
    public function destroy($id)
    {
    }
}
