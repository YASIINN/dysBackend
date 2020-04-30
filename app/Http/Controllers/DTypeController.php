<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\DType;
use Illuminate\Http\Request;
use Validator;

class DTypeController extends Controller
{

    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function update(Request $request, $id)
    {
        try {
            $valid = Validator::make($request->all(), [
                'name' => 'required|unique:d_types,dtName,' . $id,
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $dtypes = DType::find($id);
                $dtypes->dtName = $request->name;
                if ($dtypes->update()) {
                    return response()->json($dtypes);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function destroy($id)
    {
        try {
            if (DType::destroy($id)) {
                return response()->json(['msg' => "Success"], 200);
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
                'name' => 'required|unique:d_types,dtName',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $dtype = new DType();
                $dtype->dtName = $request->name;
                if ($dtype->save()) {
                    return response()->json($dtype, 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getall(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $dtype = DType::where($parser)->latest()->paginate(2);
                return response()->json($dtype, 200);
            } else {
                $dtype = DType::latest()->paginate(2);
                return response()->json($dtype, 200);
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
                $types = DType::where($parser)->latest()->get();
                return response()->json($types, 200);
            } else {
                $types = DType::latest()->get();
                return response()->json($types, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
