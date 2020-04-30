<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\Clases;
use Illuminate\Http\Request;
use Validator;

class ClasesController extends Controller
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
                'name' => 'required',
                'code' => 'required|unique:clases,cCode'
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            }
            $clases = new Clases();
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $clases->cName = $request->name;
                $clases->cCode = $request->code;
                if ($clases->save()) {
                    return response()->json($clases, 200);
                } else {
                    return response()->json([], 500);
                }
            } else {
                $clases->tName = $request->name;
                $clases->tCode = $request->code;
                if ($clases->save()) {
                    return response()->json($clases, 200);
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
            $title = Clases::find($id);
            return response()->json($title, 200);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }

    }

    public function destroy($id)
    {
        try {
            if (Clases::destroy($id)) {
                return response()->json('Ürün başarıyla silindi.', 200);

            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }

    }

    public function update(Request $request, $id)
    {
        try {
            $valid = Validator::make($request->all(), [
                'name' => 'required',
                'code' => 'required|unique:clases,cCode,' . $id
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            }
            $clases = Clases::find($id);
            $clases->cName = $request->name;
            $clases->cCode = $request->code;
            if ($clases->update()) {
                return response()->json($clases);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }

    }

    public function getAllClases()
    {
        try {
            $clases = Clases::all();
            return response()->json($clases, 200);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }

    }

    public function getClases(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $clases = Clases::where($parser)->latest()->paginate(2);
                return response()->json($clases, 200);
            } else {
                $clases = Clases::latest()->paginate(2);
                return response()->json($clases, 200);
            }
        } catch (\Exception $e) {
            return response()->json($clases, 500);
        }

    }
}
