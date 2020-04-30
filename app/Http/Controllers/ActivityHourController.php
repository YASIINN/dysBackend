<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Models\ActivityHour as APH;
use App\Models\ActivityPTypePivot as APType;

class ActivityHourController extends Controller
{
    public function checkAPH($request){
        $name = $request["name"];
        $a_p_type_id = $request["a_p_type_id"];
        $begin = $request["begin"];
        $end = $request["end"];
        $pv = APH::where("ahName",$name)
        ->where("beginDate", $begin)
        ->where("endDate", $end)
        ->where("activity_p_type_id",$a_p_type_id)->get();
        return $pv->count();
    }
    public function create(Request $request){
        $valid = Validator::make($request->all(), [
            'name'=>'required',
            'begin'=>'required',
            'end'=>'required',
            'a_p_type_id'=>'required',
        ]);
        if ($valid->fails()) {
             return response()->json($valid->errors(), 200);
        }
        try {
            $c = $this->checkAPH($request);
            if($c > 0){
              return response()->json(["message" =>'Bu kayıt sistemde vardır.'], 200);
            }
            $apt = new APH();
            $apt->ahName = $request->name;
            $apt->beginDate = $request->begin;
            $apt->endDate = $request->end;
            $apt->activity_p_type_id = $request->a_p_type_id;
            if($apt->save()){
                return response()->json(["message" =>'Kayıt başarılı bir şekilde eklendi.'], 201);
            }
         
       } catch(\Exception $exception){
        $errormsg = 'Faaliyet ekleme sırasında hata meydana geldi.';
        return response()->json(['errormsg'=>$exception->getMessage()]);
       }
    }
    public function getActivityPHours(Request $request){

        try {
            if($request->type === "ALL"){
             $pv = APH::all();
            } else {
             $atype = APType::find($request->id);
             if($atype){
                $pv = APH::where("$request->where", $request->id)->get();
             } else {
                return response()->json(["message" =>"Sistemde olmayan dataya erişim."], 203);
             }
            }
            $data = [
            ];
            
            foreach ($pv as $key=>$p) {
             $a_name = $p->activity_p_type->activity->aName;
             $p_name = $p->activity_p_type->period->pName;
             $pt_name = $p->activity_p_type->p_type->ptName;
             $title = $a_name . ' ' . $p_name . ' ' . $pt_name;
             $stdact = [
                 "id"=>$p->id,
                 "ahname"=>$p->ahName,
                 "begin"=>$p->beginDate,
                 "end"=>$p->endDate,
                 "title"=>$title
             ];
             array_push($data, $stdact);
            }
            return response()->json($data, 200);
           }catch(\Exception $exception){
            $errormsg = 'İşlem sırasında hata meydana geldi. Lütfen sonra deneyiniz.';
            return response()->json(['errormsg'=>$exception->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {
            $apd = APH::findOrFail($id);
        } catch(\Exception $exception){
            $errormsg = 'Veri Bulunamadı';
            return response()->json(['errormsg'=>$errormsg]);
        }
        if($apd->delete()){
            return response()->json(["message" =>'Veri başarıyla silindi.'], 200);
        }
    }
}
