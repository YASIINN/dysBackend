<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\UsersSchoolsClases;
use App\Models\UsersSchoolsClasesBranches;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Validator;

class UsersSchoolsClasesController extends Controller
{
    //
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function delete(Request $request, $id)
    {
        $queryparse = $request->urlparse;
        if ($queryparse) {
            $parser = $this->uriParser->queryparser($queryparse);
            DB::transaction(function () use ($parser, $id) {
                $result = UsersSchoolsClases::where("uscid", $id)->delete();
                if ($result) {
                    $res = UsersSchoolsClasesBranches::where($parser)->delete();
                    if ($res) {
                        return response()->json('Ürün başarıyla silindi.', 200);
                    } else {
                        return response()->json([], 500);
                    }
                } else {
                    return response()->json([], 500);
                }
            });
        }
    }


    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'usersclist' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }
            $allSaved = false;
            foreach ($request->usersclist as $item) {
                $userschoolclases = new UsersSchoolsClases();
                $isHave = UsersSchoolsClases::where([
                    ["user_id", "=", $item['userid']],
                    ["school_id", "=", $item['schoolid']],
                    ["clases_id", "=", $item['classid']]
                ])->get();
                if (count($isHave) > 0) {
                    $allSaved = true;
                } else {
                    $userschoolclases->user_id = $item['userid'];
                    $userschoolclases->school_id = $item['schoolid'];
                    $userschoolclases->clases_id = $item['classid'];
                    if ($userschoolclases->save()) {
                        $allSaved = true;
                    } else {
                        $allSaved = false;
                    }
                }
            }
            if ($allSaved) {
                return response()->json($userschoolclases, 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
