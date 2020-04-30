<?php

namespace App\Http\Controllers;

use App\Models\SchoolDay;
use Illuminate\Http\Request;
use Validator;
use App\Helpers\Parser;

class SchoolDayController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function index(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $schooldays = SchoolDay::where($parser)->get();
                return response()->json($schooldays, 200);
            } else {
                $schooldays = SchoolDay::all();
                return response()->json($schooldays, 200);
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
                $result = SchoolDay::where($parser)->delete();
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

    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'daysList' => 'required',
            ]);


            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $allsaved = false;
                foreach ($request->daysList as $item) {
                    $sdays = new SchoolDay();
                    $sday = SchoolDay::where(
                        [
                            ["sdName", "=", $item['dayName']],
                            ["school_p_type_id", "=", $item['sptypeid']],
                        ]
                    )->get();
                    if (count($sday) > 0) {
                        $allsaved = true;
                    } else {
                        $sdays->sdName = $item['dayName'];
                        $sdays->school_p_type_id = $item['sptypeid'];
                        if ($sdays->save()) {
                            $allsaved = true;
                        } else {
                            $allsaved = false;
                        }
                    }
                }
                if ($allsaved) {
                    return response()->json($allsaved, 200);
                } else {
                    return response()->json([], 500);
                }

            }
        } catch
        (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
