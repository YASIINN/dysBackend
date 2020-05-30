<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\ClubTeamBranchStudentPivot;
use App\Models\SporClubTeamBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class SporClubTeamBranchController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
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


            $allSaved = false;
            $allContains = false;
            if (count($request->dataList) > 0) {
                foreach ($request->dataList as $item) {
                    $sctbpivot = new SporClubTeamBranch();
                    $sctbpivots = SporClubTeamBranch::where([
                        ["spor_club_id", "=", $item['clubid']],
                        ["team_id", "=", $item['teamid']],
                        ["sbranch_id", "=", $item['branchid']]
                    ])->get();
                    if (count($sctbpivots) > 0) {
                        $allContains = true;
                    } else {
                        $allContains = false;
                        $sctbpivot->spor_club_id = $item['clubid'];
                        $sctbpivot->team_id = $item['teamid'];
                        $sctbpivot->sbranch_id = $item['branchid'];
                        if ($sctbpivot->save()) {
                            $allSaved = true;
                        } else {
                            $allSaved = false;
                        }
                    }
                }
                if ($allSaved || $allContains) {
                    return response()->json($sctbpivot);
                } else {
                    return response()->json([], 500);
                }

            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }

    }

    public function delete(Request $request, $id)
    {
        
        try {
            $stb = SporClubTeamBranch::find($id);
            if ($stb->delete()) {
                return response()->json('Ürün başarıyla silindi.');
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function deleteStudents(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $result = ClubTeamBranchStudentPivot::where($parser)->delete();
                if ($result) {
                    return response()->json("Success", 200);
                } else {
                    return response()->json([], 500);
                }

            }
        } catch (\Exception $e) {
            return response()->json($e, 500);

        }
    }

    public function getAll(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('spor_club_team_branch')
                    ->join('spor_club', 'spor_club_team_branch.spor_club_id', '=', 'spor_club.id')
                    ->join('team', 'spor_club_team_branch.team_id', '=', 'team.id')
                    ->join('spor_club_branch', 'spor_club_team_branch.sbranch_id', '=', 'spor_club_branch.id')
                    ->select("spor_club_team_branch.*", "spor_club.*", "team.*", "spor_club_branch.*")
                    ->where($parser)
                    ->latest("sctbid")
                    ->get();
                return response()->json($data, 200);
            } else {
                $data = DB::table('spor_club_team_branch')
                    ->join('spor_club', 'spor_club_team_branch.spor_club_id', '=', 'spor_club.id')
                    ->join('team', 'spor_club_team_branch.team_id', '=', 'team.id')
                    ->join('spor_club_branch', 'spor_club_team_branch.sbranch_id', '=', 'spor_club_branch.id')
                    ->select("spor_club_team_branch.*", "spor_club.*", "team.*", "spor_club_branch.*")
                    ->latest("sctbid")
                    ->get();
                return response()->json($data, 200);
            }
        } catch (\Error $e) {
            return response()->json($e, 500);
        }

    }

    public function allExport($id)
    {
        $data = DB::table('spor_club_team_branch')
            ->join('spor_club', 'spor_club_team_branch.spor_club_id', '=', 'spor_club.id')
            ->join('team', 'spor_club_team_branch.team_id', '=', 'team.id')
            ->join('spor_club_branch', 'spor_club_team_branch.sbranch_id', '=', 'spor_club_branch.id')
            ->select("spor_club_team_branch.*", "spor_club.*", "team.*", "spor_club_branch.*")
            ->where("spor_club.id", $id)
            ->latest("sctbid")
            ->get();
        return response()->json($data, 200);
    }

    public function index(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('spor_club_team_branch')
                    ->join('spor_club', 'spor_club_team_branch.spor_club_id', '=', 'spor_club.id')
                    ->join('team', 'spor_club_team_branch.team_id', '=', 'team.id')
                    ->join('spor_club_branch', 'spor_club_team_branch.sbranch_id', '=', 'spor_club_branch.id')
                    ->select("spor_club_team_branch.*", "spor_club.*", "team.*", "spor_club_branch.*")
                    ->where($parser)
                    ->latest("sctbid")
                    ->paginate(2);
                return response()->json($data, 200);
            } else {
                $data = DB::table('spor_club_team_branch')
                    ->join('spor_club', 'spor_club_team_branch.spor_club_id', '=', 'spor_club.id')
                    ->join('team', 'spor_club_team_branch.team_id', '=', 'team.id')
                    ->join('spor_club_branch', 'spor_club_team_branch.sbranch_id', '=', 'spor_club_branch.id')
                    ->select("spor_club_team_branch.*", "spor_club.*", "team.*", "spor_club_branch.*")
                    ->latest("sctbid")
                    ->paginate(2);

                return response()->json($data, 200);
            }
        } catch (\Error $e) {
            return response()->json($e, 500);
        }
    }
}
