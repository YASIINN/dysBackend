<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Title;
use Validator;
use App\Helpers\Parser;
use Illuminate\Support\Facades\Cache;

class TitleController extends Controller
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

    public  function  getAlltitles()
    {
        try {
            if (Cache::has("titles")) {
                $titles = Cache::get("titles");
                return response()->json($titles, 200);
            } else {
                Cache::set("titles", Title::all());
                $titles = Cache::get("titles");
                return response()->json($titles, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function getTitles(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $titles = Title::where($parser)->latest()->paginate(2);
                return response()->json($titles, 200);
            } else {
                $titles = Title::latest()->paginate(2);
                return response()->json($titles, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'code' => 'required|unique:titles,tCode',
                'name' => 'required|unique:titles,tName',
            ]);


            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $title = new Title();
                $title->tName = $request->name;
                $title->tCode = $request->code;
                if ($title->save()) {
                    Cache::forget("titles");
                    return response()->json($title, 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }
    public function show($id)
    {

        try {
            $title = Title::find($id);
            return response()->json($title, 200);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function update(Request $request, $id)
    {

        try {
            $valid = Validator::make($request->all(), [
                'code' => 'required|unique:titles,tCode,' . $id,
                'name' => 'required|unique:titles,tName,' . $id,
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $title = Title::find($id);
                $title->tName = $request->name;
                $title->tCode = $request->code;
                if ($title->update()) {
                    Cache::forget("titles");
                    return response()->json($title);
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
            if (Title::destroy($id)) {
                Cache::forget("titles");
                return response()->json('Ürün başarıyla silindi.', 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}
