<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\SchoolClasesBranchesPivot;
use App\Models\SchoolClasesPivot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class SchoolClasesPivotController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function getClases(Request $request)
    {

        $queryparse = $request->urlparse;
        if ($queryparse) {
            $parser = $this->uriParser->queryparser($queryparse);
            $schoolClases = SchoolClasesPivot::with(["school", "clases"])->whereHas("school", function ($query) use ($parser) {
                $query->where($parser);
            })->get();
            return response()->json($schoolClases);
        } else {
            return response()->json([], 500);
        }

    }
    public  function getAllSchoolClases(){

    }

    public function store(Request $request)
    {

        try {
            $valid = Validator::make($request->all(), [
                'school_id' => 'required',
                'clases_id' => "required"
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 401);
            }
            $queryparse = $request->urlparse;
            $scpivot = new SchoolClasesPivot();
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $scpivots = SchoolClasesPivot::where($parser)->get();
                if (count($scpivots) > 0) {
                    return response()->json([], 200);
                } else {
                    $scpivot->school_id = $request->school_id;
                    $scpivot->clases_id = $request->clases_id;
                    if ($scpivot->save()) {
                        return response()->json($scpivot, 200);
                    } else {
                        return response()->json([], 500);
                    }
                }
            } else {
                $scpivot->school_id = $request->school_id;
                $scpivot->clases_id = $request->clases_id;
                if ($scpivot->save()) {
                    return response()->json($scpivot, 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);

                $data = DB::table('school_clases_pivots')
                    ->join('schools', 'school_clases_pivots.school_id', '=', 'schools.id')
                    ->join('clases', 'school_clases_pivots.clases_id', '=', 'clases.id')
                    ->select("school_clases_pivots.*", "schools.*", "clases.*")
                    ->where($parser)
                    ->latest("scid")
                    ->paginate(2);
                return response()->json($data, 200);


                /*$data = SchoolClasesPivot::with(['school', 'clases'])
                    ->whereHas("clases", function ($query) use ($parser) {
                        $query->where($parser);
                    })->latest()->paginate(2);*/
                //   return response()->json($data, 200);
            } else {
                // $data = SchoolClasesPivot::with(['school', 'clases'])->latest()->paginate(2);
                $data = DB::table('school_clases_pivots')
                    ->join('schools', 'school_clases_pivots.school_id', '=', 'schools.id')
                    ->join('clases', 'school_clases_pivots.clases_id', '=', 'clases.id')
                    ->select("school_clases_pivots.*", "schools.*", "clases.*")
                    ->latest("scid")
                    ->paginate(2);
                return response()->json($data, 200);
                //return response()->json($data, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }

    }

    public function destroy(Request $request, $id)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                DB::transaction(function () use ($parser, $id) {
                    $result = SchoolClasesPivot::where("scid", $id)->delete();
                    if ($result) {
                        $res = SchoolClasesBranchesPivot::where($parser)->delete();
                        if ($res) {
                            return response()->json('Ürün başarıyla silindi.', 200);
                        } else {
                            return response()->json([], 500);
                        }
                    } else {
                        return response()->json([], 500);
                    }
                });


            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }


    }
}
