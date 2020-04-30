<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Validator;
use Illuminate\Support\Facades\Cache;
use App\Helpers\Parser;


class SchoolsController extends Controller
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
                'name' => 'required|unique:schools,sName,' . $id,
                'code' => 'required|unique:schools,sCode,' . $id,
                "cid" => "required"
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            }
            $schools = School::find($id);
            $schools->sName = $request->name;
            $schools->sCode = $request->code;
            $schools->company_id = $request->cid;
            if ($schools->update()) {
                Cache::forget("allschool");
                return response()->json($schools, 200);
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
            if (School::destroy($id)) {
                Cache::forget("allschool");

                return response()->json('Success.', 200);
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
                'name' => 'required|unique:schools,sName',
                'code' => 'required|unique:schools,sCode',
                "cid" => "required"
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            }
            $schools = new School();
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $schools->sName = $request->name;
                $schools->sCode = $request->code;
                $schools->company_id = $request->cid;
                if ($schools->save()) {
                    Cache::forget("allschool");

                    return response()->json($schools, 200);
                } else {
                    return response()->json([], 500);
                }
            } else {
                $schools->sName = $request->name;
                $schools->sCode = $request->code;
                $schools->company_id = $request->cid;
                if ($schools->save()) {
                    Cache::forget("allschool");
                    return response()->json($schools, 200);
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
            $school = School::find($id);
            return response()->json($school, 200);
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function getAllSchool()
    {
        try {
            if (Cache::has("allschool")) {
                $data = Cache::get("allschool");
                return response()->json($data, 200);
            } else {
                $schools = School::all();
                foreach ($schools as $item) {
                    $item->getCompanies;
                }
                return response()->json($schools, 200);
                Cache::set("allschool", $schools);
                $data = Cache::get("allschool");
                return response()->json($data, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }

    public function getAllStudentExportExcel(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('school_student')
                    ->join('schools', 'school_student.school_id', '=', 'schools.id')
                    ->join('clases', 'school_student.clases_id', '=', 'clases.id')
                    ->join('branches', 'school_student.branches_id', '=', 'branches.id')
                    ->join('students', 'school_student.student_id', '=', 'students.id')
                    ->select("school_student.*", "schools.*", "clases.*", "branches.*", "students.*")
                    ->where($parser)
                    ->latest("school_student.id")
                    ->get();
                return response()->json($data, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getStudents(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('school_student')
                    ->join('schools', 'school_student.school_id', '=', 'schools.id')
                    ->join('clases', 'school_student.clases_id', '=', 'clases.id')
                    ->join('branches', 'school_student.branches_id', '=', 'branches.id')
                    ->join('students', 'school_student.student_id', '=', 'students.id')
                    ->select("school_student.*", "schools.*", "clases.*", "branches.*", "students.*")
                    ->where($parser)
                    ->latest("school_student.id")
                    ->paginate(2);
                return response()->json($data, 200);
            } else {
                $data = DB::table('school_student')
                    ->join('schools', 'school_student.school_id', '=', 'schools.id')
                    ->join('clases', 'school_student.clases_id', '=', 'clases.id')
                    ->join('branches', 'school_student.branches_id', '=', 'branches.id')
                    ->join('students', 'school_student.student_id', '=', 'students.id')
                    ->select("school_student.*", "schools.*", "clases.*", "branches.*", "students.*")
                    ->latest("school_student.id")->get();
                return response()->json($data, 200);
            }
        } catch (\Error $e) {
            return response()->json([], 500);
        }
    }
    public function getAllSchoolUser(Request $request, $id)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('users_schools')
                    ->join('schools', 'users_schools.school_id', '=', 'schools.id')
                    ->join('users', 'users_schools.users_id', '=', 'users.id')
                    ->join('user_u_types', 'users.id', '=', 'user_u_types.users_id')
                    ->select("users_schools.*", "schools.*", "users.*", "user_u_types.*")
                    ->where($parser)
                    ->whereNull('users.deleted_at')
                    ->latest("users_schools.id")
                    ->get();
                return response()->json($data, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getUser(Request $request, $id)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);

                $data = DB::table('users_schools')
                    ->join('schools', 'users_schools.school_id', '=', 'schools.id')
                    ->join('users', 'users_schools.users_id', '=', 'users.id')
                    ->join('user_u_types', 'users.id', '=', 'user_u_types.users_id')
                    ->select("users_schools.*", "schools.*", "users.*", "user_u_types.*")
                    ->where($parser)
                    ->whereNull('users.deleted_at')
                    ->latest("users_schools.id")
                    ->paginate(2);
                /* $school = School::find($id);
                 $user = $school->users()->where($parser)->latest()->paginate(2);*/
                return response()->json($data, 200);
                //   $school = $user->schools()->latest()->paginate(2);
            } else {
                /*             $data = DB::table('users_schools')
                                 ->join('schools', 'users_schools.school_id', '=', 'schools.id')
                                 ->join('users', 'users_schools.users_id', '=', 'users.id')
                                 ->join('user_u_types', 'users.id', '=', 'user_u_types.users_id')
                                 ->select("users_schools.*", "schools.*", "users.*", "user_u_types.*")
                                 ->where($parser)
                                 ->latest("users_schools.id")
                                 ->paginate(2);
                             return $data;*/
                /*                $school = School::find($id);
                                $user = $school->users;
                                return $user;*/
                $school = School::where("id", $id)->with(["users"])->has("users")->where("users.uName", "deneme")->get();

                return $school;
                $user = $school->users()->whereHas("user_u_types", function ($q) {
                    $q->where("user_u_types.user_types_id", 1);
                })->latest()->get();
                return response()->json($user, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getSchools(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $schools = School::with(['students'])->where($parser)->latest()->paginate(2);
                foreach ($schools as $school) {
                    $school->getCompanies;
                }
                return response()->json($schools, 200);
            } else {
                $schools = School::with(['students'])->latest()->paginate(2);
                foreach ($schools as $school) {
                    $school->getCompanies;
                    $school->studentCount=count($school->students)>0?count($school->students):'-';
                }
                return response()->json($schools, 200);
            }
        } catch (\Exception $e) {
            return response()->json([], 500);
        }
    }
}
