<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Models\ActivityPTypePivot as APType;

class ActivityPTypePivotController extends Controller
{
    public function checkAPT($request){
        $activity_id = $request["activity_id"];
        $p_type_id= $request["p_type_id"];
        $pv = APType::where("activity_id",$activity_id)->where("p_type_id",$p_type_id)->where("period_id", $request["period_id"])->get();
        return $pv->count();
    }
    public function create(Request $request){
        $valid = Validator::make($request->all(), [
            'activity_id'=>'required',
            'period_id'=>'required',
            'p_type_id'=>'required'
        ]);
        if ($valid->fails()) {
             return response()->json($valid->errors(), 200);
        }
        try {
            $c = $this->checkAPT($request);
            if($c > 0){
              return response()->json(["message" =>'Bu kayıt sistemde vardır.'], 200);
            }
            $apt = new APType();
            $apt->activity_id = $request->activity_id;
            $apt->period_id = $request->period_id;
            $apt->p_type_id = $request->p_type_id;
            if($apt->save()){
                return response()->json(["message" =>'Kayıt başarılı bir şekilde eklendi.'], 201);
            }
         
       } catch(\Exception $exception){
        $errormsg = 'Faaliyet ekleme sırasında hata meydana geldi.';
        return response()->json(['errormsg'=>$errormsg]);
       }
    }
    public function getActivityPTypes(Request $request){
    
        try {
            if($request->type === "ALL"){
             $pv = APType::all();
            } else {
             $pv = APType::where("activity_id", $request->activity_id)
             ->where("period_id", $request->period_id)
             ->where("p_type_id", $request->p_type_id)
             ->get();
            }
            $data = [
            ];
            foreach ($pv as $key=>$p) {
             $stdact = [
                 "id"=>$p->id,
                 "activity"=>$p->activity->aName,
                 "activity_id"=>$p->activity->id,
                 "period"=>$p->period->pName,
                 "period_id"=>$p->period->id,
                 "p_type"=>$p->p_type->ptName,
                 "p_type_id"=>$p->p_type->id
             ];
             array_push($data, $stdact);
            }
            return response()->json($data, 200);
           }catch(\Exception $exception){
            $errormsg = 'İşlem sırasında hata meydana geldi. Lütfen sonra deneyiniz.';
            return response()->json(['errormsg'=>$exception->$errormsg]);
        }
    }
    public function destroy($id)
    {
        try {
            $apd = APType::findOrFail($id);
        } catch(\Exception $exception){
            $errormsg = 'Veri Bulunamadı';
            return response()->json(['errormsg'=>$exception->getMessage()]);
        }
        if($apd->delete()){
            return response()->json(["message" =>'Veri başarıyla silindi.'], 200);
        }
    }
}
