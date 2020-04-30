<?php

namespace App\Http\Controllers;
use Validator;
use Illuminate\Http\Request;
use App\Models\ClubHour as CPH;
use App\Models\ClubPTypePivot as CPType;

class ClubHourController extends Controller
{
    public function checkCPH($request){
        $name = $request["name"];
        $c_p_type_id = $request["c_p_type_id"];
        $begin = $request["begin"];
        $end = $request["end"];
        $pv = CPH::where("chName",$name)
        ->where("beginDate", $begin)
        ->where("endDate", $end)
        ->where("club_p_type_id",$c_p_type_id)->get();
        return $pv->count();
    }
    public function create(Request $request){
        $valid = Validator::make($request->all(), [
            'name'=>'required',
            'begin'=>'required',
            'end'=>'required',
            'c_p_type_id'=>'required',
        ]);
        if ($valid->fails()) {
             return response()->json($valid->errors(), 200);
        }
        try {
            $c = $this->checkCPH($request);
            if($c > 0){
              return response()->json(["message" =>'Bu kayıt sistemde vardır.'], 200);
            }
            $apt = new CPH();
            $apt->chName = $request->name;
            $apt->beginDate = $request->begin;
            $apt->endDate = $request->end;
            $apt->club_p_type_id = $request->c_p_type_id;
            if($apt->save()){
                return response()->json(["message" =>'Kayıt başarılı bir şekilde eklendi.'], 201);
            }
         
       } catch(\Exception $exception){
        $errormsg = 'Faaliyet ekleme sırasında hata meydana geldi.';
        return response()->json(['errormsg'=>$exception->getMessage()]);
       }
    }
    public function getClubPHours(Request $request){
        try {
            if($request->type === "ALL"){
             $pv = CPH::all();
            } else {
             $atype = CPType::find($request->id);
             if($atype){
                $pv = CPH::where("$request->where", $request->id)->get();
             } else {
                return response()->json(["message" =>"Sistemde olmayan dataya erişim."], 203);
             }
            }
            $data = [
            ];
            foreach ($pv as $key=>$p) {
             $a_name = $p->club_p_type->spor_club->scName;
             $pt_name = $p->club_p_type->p_type->ptName;
             $title = $a_name . ' ' . $pt_name;
             $stdact = [
                 "id"=>$p->id,
                 "chname"=>$p->chName,
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
            $apd = CPH::findOrFail($id);
        } catch(\Exception $exception){
            $errormsg = 'Veri Bulunamadı';
            return response()->json(['errormsg'=>$errormsg]);
        }
        if($apd->delete()){
            return response()->json(["message" =>'Veri başarıyla silindi.'], 200);
        }
    }
}
