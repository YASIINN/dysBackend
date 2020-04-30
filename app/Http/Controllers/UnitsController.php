<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\Units;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Validator;

class UnitsController extends Controller
{
    //
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function getUnits(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $units = Units::where($parser)->latest()->paginate(2);
                return response()->json($units, 200);
            } else {
                $units = Units::latest()->paginate(2);
                return response()->json($units, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'code' => 'required|unique:units,uCode',
                'name' => 'required|unique:units,uName',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $unit = new Units();
                $unit->uName = $request->name;
                $unit->uCode = $request->code;
                if ($unit->save()) {
                    Cache::forget("units");
                    return response()->json($unit, 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }


    public function update(Request $request, $id)
    {
        try {
            $valid = Validator::make($request->all(), [
                'code' => 'required|unique:units,uCode,' . $id,
                'name' => 'required|unique:units,uName,' . $id,
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $unit = Units::find($id);
                $unit->uName = $request->name;
                $unit->uCode = $request->code;
                if ($unit->update()) {
                    Cache::forget("units");
                    return response()->json($unit);
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
            if (Units::destroy($id)) {
                Cache::forget("units");
                return response()->json('Ürün başarıyla silindi.', 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function show($id)
    {
        try {
            $units = Units::find($id);
            return response()->json($units);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function getAllUnits()
    {
        try {
            if (Cache::has("units")) {
                $units = Cache::get("units");
                return response()->json($units, 200);
            } else {
                Cache::set("units", Units::all());
                $units = Cache::get("units");
                return response()->json($units, 200);
            }
        } catch (\Exception $e) {
            return response()->json($units, 500);
        }
    }
}
