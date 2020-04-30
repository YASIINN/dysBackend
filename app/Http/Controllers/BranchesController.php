<?php

namespace App\Http\Controllers;

use App\Models\Branches;
use Illuminate\Http\Request;
use Validator;
use App\Helpers\Parser;

class BranchesController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function getAllBranches()
    {
        try {
            $branches = Branches::all();
            return response()->json($branches, 200);
        } catch (\Exception $e) {
            return response()->json($branches, 500);
        }

    }

    public function getBranches(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $branches = Branches::where($parser)->latest()->paginate(2);
                return response()->json($branches, 200);
            } else {
                $branches = Branches::latest()->paginate(2);
                return response()->json($branches, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }

    }

    public function show($id)
    {
        try {
            $branches = Branches::find($id);
            return response()->json($branches);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }

    }

    public function destroy($id)
    {
        try {
            if (Branches::destroy($id)) {
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
                'name' => 'required',
                'code' => 'required|unique:branches,bCode'
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            }
            $branch = new Branches();
            $branch->bName = $request->name;
            $branch->bCode = $request->code;
            if ($branch->save()) {
                return response()->json($branch, 200);
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
                'name' => 'required',
                'code' => 'required|unique:branches,bCode,' . $id
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            }
            $branche = Branches::find($id);
            $branche->bName = $request->name;
            $branche->bCode = $request->code;
            if ($branche->update()) {
                return response()->json($branche);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }

    }
}
