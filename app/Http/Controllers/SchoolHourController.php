<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\SchoolHour;
use Illuminate\Http\Request;
use Validator;

class SchoolHourController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function update(Request $request, $id)
    {
        try {
            $hour = SchoolHour::where(
                [
                    ["shName", "=", $request->shName],
                    ["beginDate", "=", $request->begdt],
                    ["endDate", "=", $request->enddt],
                    ["school_p_type_id", "=", $request->ptypeid],
                ]
            )->get();
            if (count($hour) > 0) {
                return response()->json([], 204);
            } else {
                $schoolhour = SchoolHour::find($id);
                $schoolhour->shName = $request->shName;
                $schoolhour->beginDate = $request->begdt;
                $schoolhour->endDate = $request->enddt;
                $schoolhour->school_p_type_id = $request->ptypeid;
                if ($schoolhour->update()) {
                    return response()->json($schoolhour, 200);
                } else {
                    return response()->json([], 500);

                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);

        }

    }

    public function delete(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $result = SchoolHour::where($parser)->delete();
                if ($result) {
                    return response()->json('Succes.', 200);
                } else {
                    return response()->json("", 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);

        }
    }

    public function index(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $schoolhour = SchoolHour::where($parser)->get();
                return response()->json($schoolhour, 200);
            } else {
                $schoolhour = SchoolHour::all();
                return response()->json($schoolhour, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'shName' => 'required',
                'begdt' => "required",
                'enddt' => "required",
                "ptypeid" => "required",
            ]);


            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                /*   foreach ($request->daysList as $item) {*/
                $shour = new SchoolHour();
                $hour = SchoolHour::where(
                    [
                        ["shName", "=", $request->shName],
                        ["beginDate", "=", $request->begdt],
                        ["endDate", "=", $request->enddt],
                        ["school_p_type_id", "=", $request->ptypeid],
                    ]
                )->get();
                if (count($hour) > 0) {
                    return response()->json([], 204);
                } else {
                    $shour->shName = $request->shName;
                    $shour->beginDate = $request->begdt;
                    $shour->endDate = $request->enddt;
                    $shour->school_p_type_id = $request->ptypeid;
                    if ($shour->save()) {
                        return response()->json($shour, 200);
                    } else {
                        return response()->json([], 500);
                    }
                }
            }
        } catch
        (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
