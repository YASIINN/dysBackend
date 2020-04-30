<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Province;
use Validator;
use App\Helpers\Parser;
use Illuminate\Support\Facades\Cache;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }
    public  function  getAllProvince()
    {
        try {
            if (Cache::has("province")) {
                $provinces = Cache::get("province");
                return response()->json($provinces, 200);
            } else {
                Cache::set("province", Province::all());
                $provinces = Cache::get("province");
                return response()->json($provinces, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
    public function getProvinces(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $provinces = Province::where($parser)->latest()->paginate(2);
                return response()->json($provinces, 200);
            } else {
                $provinces = Province::latest()->paginate(2);
                return response()->json($provinces, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'code' => 'required|unique:provinces,pCode',
                'name' => 'required|unique:provinces,pName',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $province = new Province();
                $province->pName = $request->name;
                $province->pCode = $request->code;
                if ($province->save()) {
                    Cache::forget("province");
                    return response()->json($province, 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
    public function show($id)
    {
        try {
            $province = Province::find($id);
            return response()->json($province);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {

            $valid = Validator::make($request->all(), [
                'code' => 'required|unique:provinces,pCode,' . $id,
                'name' => 'required|unique:provinces,pName,' . $id,
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $province = Province::find($id);
                $province->pName = $request->name;
                $province->pCode = $request->code;
                if ($province->update()) {
                    Cache::forget("province");
                    return response()->json($province, 200);
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
            if (Province::destroy($id)) {
                Cache::forget("province");
                return response()->json('Ürün başarıyla silindi.');
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}
