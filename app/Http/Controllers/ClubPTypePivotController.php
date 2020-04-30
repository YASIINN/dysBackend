<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Models\ClubPTypePivot as CPType;

class ClubPTypePivotController extends Controller
{
    public function checkCPT($request){
        $club_id = $request["club_id"];
        $p_type_id= $request["p_type_id"];
        $pv = CPType::where("spor_club_id",$club_id)->where("p_type_id",$p_type_id)->get();
        return $pv->count();
    }
    public function create(Request $request){
        $valid = Validator::make($request->all(), [
            'club_id'=>'required',
            'p_type_id'=>'required'
        ]);
        if ($valid->fails()) {
             return response()->json($valid->errors(), 200);
        }
        try {
            $c = $this->checkCPT($request);
            if($c > 0){
              return response()->json(["message" =>'Bu kayıt sistemde vardır.'], 200);
            }
            $apt = new CPType();
            $apt->spor_club_id = $request->club_id;
            $apt->p_type_id = $request->p_type_id;
            if($apt->save()){
                return response()->json(["message" =>'Kayıt başarılı bir şekilde eklendi.'], 201);
            }
         
       } catch(\Exception $exception){
        $errormsg = 'Faaliyet ekleme sırasında hata meydana geldi.';
        return response()->json(['errormsg'=>$exception->getMessage()]);
       }
    }
    public function getClubPTypes(Request $request){
        try {
            if($request->type === "ALL"){
             $pv = CPType::all();
            } else {
             $pv = CPType::where("spor_club_id", $request->club_id)
             ->where("p_type_id", $request->p_type_id)
             ->get();
            }
            $data = [
            ];
            foreach ($pv as $key=>$p) {
             $stdact = [
                 "id"=>$p->id,
                 "club"=>$p->spor_club->scName,
                 "club_id"=>$p->spor_club->id,
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
            $apd = CPType::findOrFail($id);
        } catch(\Exception $exception){
            $errormsg = 'Veri Bulunamadı';
            return response()->json(['errormsg'=>$exception->getMessage()]);
        }
        if($apd->delete()){
            return response()->json(["message" =>'Veri başarıyla silindi.'], 200);
        }
    }
}
