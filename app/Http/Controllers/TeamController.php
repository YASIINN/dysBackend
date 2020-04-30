<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Validator;

class TeamController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function delete($id)
    {
        try {
            if (Team::destroy($id)) {
                Cache::forget("team");
                return response()->json('Silme İşlemi Başarılı', 200);

            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $valid = Validator::make($request->all(), [
                'code' => 'required|unique:team,stCode,' . $id,
                'name' => 'required|unique:team,stName,' . $id,
                "club" => "required"
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $team = Team::find($id);
                $team->stName = $request->name;
                $team->stCode = $request->code;
                $team->spor_club_id = $request->club;
                if ($team->update()) {
                    Cache::forget("team");
                    return response()->json($team);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getall()
    {
        try {
            if (Cache::has("team")) {
                $teams = Cache::get("team");
                return response()->json($teams, 200);
            } else {
                $allData = Team::all();
                foreach ($allData as $item) {
                    $item->sporclub;
                }
                Cache::set("team", $allData);
                $teams = Cache::get("team");
                return response()->json($teams, 200);
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
                $teams = Team::where($parser)->latest()->paginate(2);
                foreach ($teams as $team) {
                    $team->sporclub;
                }
                return response()->json($teams, 200);
            } else {
                $teams = Team::latest()->paginate(2);
                foreach ($teams as $team) {
                    $team->sporclub;
                }
                return response()->json($teams, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'code' => 'required|unique:team,stCode',
                'name' => 'required|unique:team,stName',
                "club" => "required"
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $teams = new Team();
                $teams->stName = $request->name;
                $teams->stCode = $request->code;
                $teams->spor_club_id = $request->club;
                if ($teams->save()) {
                    Cache::forget("team");
                    return response()->json($teams, 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }

    }
}
