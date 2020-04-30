<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Models\ActivityDay as APD;
use App\Models\ActivityPTypePivot as APType;

class ActivityDayController extends Controller
{
    public function checkAPD($request)
    {
        $name = $request["name"];
        $a_p_type_id = $request["a_p_type_id"];
        $pv = APD::where("adName", $name)->where("activity_p_type_id", $a_p_type_id)->get();
        return $pv->count();
    }

    public function create(Request $request)
    {
        $message = "";
        try {
            foreach ($request->data as $key => $value) {
                $valid = Validator::make($value, [
                    'name' => 'required',
                    'a_p_type_id' => 'required',
                ]);
                if ($valid->fails()) {
                    return response()->json(["message" => 'Eksik data gönderimi.'], 200);
                }
                $c = $this->checkAPD($value);
                if ($c == 0) {
                    $apd = new APD();
                    $apd->adName = $value["name"];
                    $apd->activity_p_type_id = $value["a_p_type_id"];
                    if ($apd->save()) {
                        $message = "Seçilen datalar başarıyla eklendi.";
                    }
                } else {
                    $message = "Sistemde olmayan datalar başarıyla eklendi.";
                }
            }
            return response()->json(["message" => $message], 201);
        } catch (\Exception $exception) {
            $errormsg = 'Ekleme sırasında hata meydana geldi.';
            return response()->json(['errormsg' => $errormsg]);
        }
    }

    public function getActivityPDays(Request $request)
    {

        try {
            if ($request->type === "ALL") {
                $pv = APD::all();
            } else {
                $atype = APType::find($request->id);
                if ($atype) {
                    $pv = APD::where("$request->where", $request->id)->get();
                } else {
                    return response()->json(["message" => "Sistemde olmayan dataya erişim."], 203);
                }
            }
            $data = [
            ];
            foreach ($pv as $key => $p) {
                $a_name = $p->activity_p_type->activity->aName;
                $p_name = $p->activity_p_type->period->pName;
                $pt_name = $p->activity_p_type->p_type->ptName;
                $title = $a_name . ' ' . $p_name . ' ' . $pt_name;
                $stdact = [
                    "id" => $p->id,
                    "adname" => $p->adName,
                    "title" => $title
                ];
                array_push($data, $stdact);
            }
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            $errormsg = 'İşlem sırasında hata meydana geldi. Lütfen sonra deneyiniz.';
            return response()->json(['errormsg' => $exception->$errormsg]);
        }
    }

    public function destroy($id)
    {
        try {
            $apd = APD::findOrFail($id);
        } catch (\Exception $exception) {
            $errormsg = 'Veri Bulunamadı';
            return response()->json(['errormsg' => $errormsg]);
        }
        if ($apd->delete()) {
            return response()->json(["message" => 'Veri başarıyla silindi.'], 200);
        }
    }
}
