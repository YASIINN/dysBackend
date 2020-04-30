<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Http\Request;
use App\Models\ClubDay as CPD;
use App\Models\ClubPTypePivot as CPType;

class ClubDayController extends Controller
{
    public function checkCPD($request)
    {
        $name = $request["name"];
        $c_p_type_id = $request["c_p_type_id"];
        $pv = CPD::where("cdName", $name)->where("club_p_type_id", $c_p_type_id)->get();
        return $pv->count();
    }

    public function create(Request $request)
    {
        $message = "";
        try {
            foreach ($request->data as $key => $value) {
                $valid = Validator::make($value, [
                    'name' => 'required',
                    'c_p_type_id' => 'required',
                ]);
                if ($valid->fails()) {
                    return response()->json(["message" => 'Eksik data gönderimi.'], 200);
                }
                $c = $this->checkCPD($value);
                if ($c == 0) {
                    $apd = new CPD();
                    $apd->cdName = $value["name"];
                    $apd->club_p_type_id = $value["c_p_type_id"];
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
            return response()->json(['message' => $exception->getMessage()]);
        }
    }

    public function getClubPDays(Request $request)
    {

        try {
            if ($request->type === "ALL") {
                $pv = CPD::all();
            } else {
                $atype = CPType::find($request->id);
                if ($atype) {
                    $pv = CPD::where("$request->where", $request->id)->get();
                } else {
                    return response()->json(["message" => "Sistemde olmayan dataya erişim."], 203);
                }
            }
            $data = [
            ];
            foreach ($pv as $key => $p) {
                $a_name = $p->club_p_type->spor_club->scName;
                $pt_name = $p->club_p_type->p_type->ptName;
                $title = $a_name . ' ' . $pt_name;
                $stdact = [
                    "id" => $p->id,
                    "adname" => $p->cdName,
                    "title" => $title
                ];
                array_push($data, $stdact);
            }
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            $errormsg = 'İşlem sırasında hata meydana geldi. Lütfen sonra deneyiniz.';
            return response()->json(['errormsg' => $exception->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $apd = CPD::findOrFail($id);
        } catch (\Exception $exception) {
            $errormsg = 'Veri Bulunamadı';
            return response()->json(['errormsg' => $errormsg]);
        }
        if ($apd->delete()) {
            return response()->json(["message" => 'Veri başarıyla silindi.'], 200);
        }
    }
}
