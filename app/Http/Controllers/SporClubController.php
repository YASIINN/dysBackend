<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\SporClub;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Validator;

class SporClubController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function show($id)
    {
        try {
            $scporClub = SporClub::find($id);
            return response()->json($scporClub, 200);
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function getUserExport(Request $request, $id)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $club = SporClub::find($id);
                $user = $club->users()->latest()->get();
                return response()->json($user, 200);
            } else {
                $club = SporClub::find($id);
                $user = $club->users()->latest()->get();
                foreach ($user as $item) {
                    if ($item->uGender == 1) {
                        $item->uGender = "Erkek";
                    } else {
                        $item->uGender = "Kadın";
                    }
                    if ($item->uİsActive == 1) {
                        $item->uİsActive = "Aktif";
                    } else {
                        $item->uİsActive = "Pasif";
                    }
                    $item->file;
                }
                return response()->json($user, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getUser(Request $request, $id)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $club = SporClub::find($id);
                $user = $club->users()->where($parser)->latest()->paginate(2);
                foreach ($user as $item) {
                    if ($item->uGender == 1) {
                        $item->uGender = "Erkek";
                    } else {
                        $item->uGender = "Kadın";
                    }
                    if ($item->uİsActive == 1) {
                        $item->uİsActive = "Aktif";
                    } else {
                        $item->uİsActive = "Pasif";
                    }
                    $item->file;
                }
                return response()->json($user, 200);
            } else {
                $club = SporClub::find($id);
                $user = $club->users()->where("uİsActive", 1)->latest()->paginate(2);
                foreach ($user as $item) {
                    if ($item->uGender == 1) {
                        $item->uGender = "Erkek";
                    } else {
                        $item->uGender = "Kadın";
                    }
                    if ($item->uİsActive == 1) {
                        $item->uİsActive = "Aktif";
                    } else {
                        $item->uİsActive = "Pasif";
                    }
                    $item->file;
                }
                return response()->json($user, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getAllExport(Request $request)
    {
        try {
            /*$request["club_id"]*/
            $students = Student::with(["clubs"])
                ->whereHas("clubs", function ($q) use ($request) {
                    $q->where("spor_club_id", $request->clubid);
                })->get();
            return $students;
        } catch (\Error $e) {
            return response()->json($e, 500);
        }
    }

    public function getStudents(Request $request)
    {
        try {

            $students = Student::with(["clubs", "file"])
                ->where("s_fullname", "like", $request->fullname . '%')
                ->whereHas("clubs", function ($q) use ($request) {
                    $q->where("spor_club_id", $request->club_id);
                })
                ->paginate(2);
            return $students;
        } catch (\Error $e) {
            return response()->json($e, 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $valid = Validator::make($request->all(), [
                'code' => 'required|unique:spor_club,scCode,' . $id,
                'name' => 'required|unique:spor_club,scName,' . $id,
                "company" => "required"
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $scporClub = SporClub::find($id);
                $scporClub->scName = $request->name;
                $scporClub->scCode = $request->code;
                $scporClub->company_id = $request->company;
                if ($scporClub->update()) {
                    Cache::forget("sporClub");
                    return response()->json($scporClub);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'code' => 'required|unique:spor_club,scCode',
                'name' => 'required|unique:spor_club,scName',
                "cid" => "required"
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $scporClub = new SporClub();
                $scporClub->scName = $request->name;
                $scporClub->scCode = $request->code;
                $scporClub->company_id = $request->cid;
                if ($scporClub->save()) {
                    Cache::forget("sporClub");
                    return response()->json($scporClub, 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function delete($id)
    {
        try {
            if (SporClub::destroy($id)) {
                Cache::forget("sporClub");
                return response()->json('Silme İşlemi Başarılı', 200);
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
            if (Cache::has("sporClub")) {
                $sporclubs = Cache::get("sporClub");
                return response()->json($sporclubs, 200);
            } else {
                Cache::set("sporClub", SporClub::all());
                $sporclubs = Cache::get("sporClub");
                return response()->json($sporclubs, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
    public function getSporClubs(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $sporclub = SporClub::where($parser)->latest()->paginate(2);
                foreach ($sporclub as $club) {
                    $club->getCompanies;
                }
                return response()->json($sporclub, 200);
            } else {
                $sporclub = SporClub::latest()->paginate(2);
                foreach ($sporclub as $club) {
                    $club->getCompanies;
                }
                return response()->json($sporclub, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
