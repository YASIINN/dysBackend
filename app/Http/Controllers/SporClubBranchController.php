<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\SporClubBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Validator;

class SporClubBranchController extends Controller
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
                'code' => 'required|unique:spor_club_branch,sbCode',
                'name' => 'required|unique:spor_club_branch,sbName',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $scporClubBranch = new SporClubBranch();
                $scporClubBranch->sbName = $request->name;
                $scporClubBranch->sbCode = $request->code;
                if ($scporClubBranch->save()) {
                    Cache::forget("sporClubBranch");
                    return response()->json($scporClubBranch, 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $valid = Validator::make($request->all(), [
                'code' => 'required|unique:spor_club_branch,sbCode,' . $id,
                'name' => 'required|unique:spor_club_branch,sbName,' . $id,
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            }
            $scporClubBranch = SporClubBranch::find($id);
            $scporClubBranch->sbName = $request->name;
            $scporClubBranch->sbCode = $request->code;
            if ($scporClubBranch->update()) {
                Cache::forget("sporClubBranch");
                return response()->json($scporClubBranch);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }

    }

    public function getall()
    {
        try {
            if (Cache::has("sporClubBranch")) {
                $sclubBranch = Cache::get("sporClubBranch");
                return response()->json($sclubBranch, 200);
            } else {
                Cache::set("sporClubBranch", SporClubBranch::all());
                $sclubBranch = Cache::get("sporClubBranch");
                return response()->json($sclubBranch, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function delete($id)
    {
        try {
            if (SporClubBranch::destroy($id)) {
                Cache::forget("sporClubBranch");
                return response()->json('Ürün başarıyla silindi.', 200);
            } else {
                return response()->json([], 500);
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
                $branches = SporClubBranch::where($parser)->latest()->paginate(2);
                return response()->json($branches, 200);
            } else {
                $branches = SporClubBranch::latest()->paginate(2);
                return response()->json($branches, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }

    }

}
