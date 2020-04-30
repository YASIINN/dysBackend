<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\SchoolClasesBranchesPivot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use function foo\func;

use App\Models\SchoolClasesBranchesPivot as SCB;

class SchoolClasesBranchesPivotController extends Controller
{

    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }


    public function getAllSCB(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('school_clases_branches_pivots')
                    ->join('schools', 'school_clases_branches_pivots.school_id', '=', 'schools.id')
                    ->join('clases', 'school_clases_branches_pivots.clases_id', '=', 'clases.id')
                    ->join('branches', 'school_clases_branches_pivots.branches_id', '=', 'branches.id')
                    ->select("school_clases_branches_pivots.*", "schools.*", "clases.*", "branches.*")
                    ->where($parser)
                    ->latest("scbid")->get();

                return response()->json($data, 200);
            } else {
                // $data = SchoolClasesBranchesPivot::with(['school', 'clases', 'branches'])->latest()->paginate(2);
                $data = DB::table('school_clases_branches_pivots')
                    ->join('schools', 'school_clases_branches_pivots.school_id', '=', 'schools.id')
                    ->join('clases', 'school_clases_branches_pivots.clases_id', '=', 'clases.id')
                    ->join('branches', 'school_clases_branches_pivots.branches_id', '=', 'branches.id')
                    ->select("school_clases_branches_pivots.*", "schools.*", "clases.*", "branches.*")
                    ->latest("scbid")->get();
                return response()->json($data, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }


    public function others(Request $request)
    {
        try {
            if ($request->type === "ALL") {
                $pv = SCB::all();
            } else {
                $pv = SCB::where("$request->where", $request->id)->get();
            }
            $data = [
            ];
            foreach ($pv as $p) {
                $classbranchname = $p->clases->cName . ' ' . $p->branches->bName;
                $classbranch = [
                    "school_id" => $p->school->id,
                    "classbranch" => $classbranchname,
                    "class_id" => $p->clases->id,
                    "branch_id" => $p->branches->id
                ];
                $school = [
                    "id" => $p->school->id,
                    "name" => $p->school->sName,
                    "code" => $p->school->sCode,
                    "classbranch" => $classbranch
                ];
                array_push($data, $school);
            }
            return response()->json($data, 200);
        } catch (\Exception $exception) {
            $errormsg = 'İşlem sırasında hata meydana geldi. Lütfen sonra deneyiniz.';
            return response()->json(['errormsg' => $errormsg]);
        }
    }


    public function index(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('school_clases_branches_pivots')
                    ->join('schools', 'school_clases_branches_pivots.school_id', '=', 'schools.id')
                    ->join('clases', 'school_clases_branches_pivots.clases_id', '=', 'clases.id')
                    ->join('branches', 'school_clases_branches_pivots.branches_id', '=', 'branches.id')
                    ->select("school_clases_branches_pivots.*", "schools.*", "clases.*", "branches.*")
                    ->where($parser)
                    ->latest("scbid")
                    ->paginate(2);
                return response()->json($data, 200);
            } else {
                // $data = SchoolClasesBranchesPivot::with(['school', 'clases', 'branches'])->latest()->paginate(2);
                $data = DB::table('school_clases_branches_pivots')
                    ->join('schools', 'school_clases_branches_pivots.school_id', '=', 'schools.id')
                    ->join('clases', 'school_clases_branches_pivots.clases_id', '=', 'clases.id')
                    ->join('branches', 'school_clases_branches_pivots.branches_id', '=', 'branches.id')
                    ->select("school_clases_branches_pivots.*", "schools.*", "clases.*", "branches.*")
                    ->latest("scbid")
                    ->paginate(2);
                return response()->json($data, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }


    }


    public function destroy($id)
    {
        try {
            $res = SchoolClasesBranchesPivot::where("scbid", $id)->delete();
            if ($res) {
                return response()->json('Ürün başarıyla silindi.', 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }

    }

    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'dataList' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 401);
            }

            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $allSaved = false;
                $allContains = false;
                if (count($request->dataList) > 0) {
                    foreach ($request->dataList as $item) {
                        $scbpivot = new SchoolClasesBranchesPivot();
                        $scbpivots = SchoolClasesBranchesPivot::where([
                            ["school_id", "=", $item['school_id']],
                            ["clases_id", "=", $item['clases_id']],
                            ["branches_id", "=", $item['branches_id']]
                        ])->get();
                        if (count($scbpivots) > 0) {
                            $allContains = true;
                        } else {
                            $allContains = false;
                            $scbpivot->school_id = $item['school_id'];
                            $scbpivot->clases_id = $item['clases_id'];
                            $scbpivot->branches_id = $item['branches_id'];
                            if ($scbpivot->save()) {
                                $allSaved = true;
                            } else {
                                $allSaved = false;
                            }
                        }
                    }
                    if ($allSaved || $allContains) {
                        return response()->json($scbpivot);
                    } else {
                        return response()->json([], 500);
                    }

                } else {
                    return response()->json([], 500);
                }


            } else {
                $allSaved = false;
                if (count($request->dataList) > 0) {
                    foreach ($request->dataList as $item) {
                        $scbpivot = new SchoolClasesBranchesPivot();
                        $scbpivot->school_id = $item['school_id'];
                        $scbpivot->clases_id = $item['clases_id'];
                        $scbpivot->branches_id = $item['branches_id'];
                        if ($scbpivot->save()) {
                            $allSaved = true;
                        } else {
                            $allSaved = false;
                        }
                    }
                    if ($allSaved) {
                        return response()->json($scbpivot);
                    } else {
                        return response()->json([], 500);
                    }
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }


    }
}
