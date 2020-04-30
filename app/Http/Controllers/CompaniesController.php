<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Validator;
use App\Helpers\Parser;
use Illuminate\Support\Facades\Cache;

class CompaniesController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public  function  getAllCompanies()
    {

        try {
            if (Cache::has("company")) {
                $company = Cache::get("company");
                return response()->json($company, 200);
            } else {
                $companies = Company::all();
                foreach ($companies as $companie) {
                    $companie->getSchool;
                }
                Cache::set("company", $companies);
                $company = Cache::get("company");
                return response()->json($company, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function getCompanies(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $companies = Company::where($parser)->latest()->paginate(2);
                return response()->json($companies, 200);
            } else {
                $companies = Company::latest()->paginate(2);
                return response()->json($companies, 200);
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
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 401);
            }
            $companie = new Company();
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $companies = Company::where($parser)->get();
                if (count($companies) > 0) {
                    return response()->json([], 204);
                } else {
                    $companie->cName = $request->name;
                    if ($companie->save()) {
                        Cache::forget("company");
                        return response()->json($companie, 200);
                    } else {
                        return response()->json([], 500);
                    }
                }
            } else {
                $companie->cName = $request->name;
                if ($companie->save()) {
                    Cache::forget("company");
                    return response()->json($companie, 200);
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
            $companie = Company::find($id);
            return response()->json($companie, 200);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $companie = Company::find($id);
            $companie->cName = $request->name;
            if ($companie->update()) {
                Cache::forget("company");

                return response()->json($companie, 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function destroy($id)
    {
        try {
            if (Company::destroy($id)) {
                Cache::forget("company");
                return response()->json('Success.');
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}
