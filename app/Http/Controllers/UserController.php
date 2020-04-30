<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\SchoolProgram;
use App\Models\Student;
use App\Models\Users;
use http\Client\Curl\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Validator;
use function foo\func;

class UserController extends Controller
{
    //
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }


    public function getActivityProgram($id)
    {


        try {
//id dışardan alınacak TODO
            $activityprogramArray = array();
            $user = Users::where("id", 2)->with(['getapContent'])->has("getapContent.getProgram")->get();
            $collection = collect($user[0]['getapContent']);
            $unique = $collection->unique('activity_program_id');
            foreach ($unique as $key => $item) {
                if (in_array([
                    "programid" => $item->getProgram->getActivityProgramType->id,
                    "activityid" => $item->getProgram->getActivityProgramType->activity->id,
                    "activityname" => $item->getProgram->getActivityProgramType->activity->aName,
                    "periodid" => $item->getProgram->getActivityProgramType->period->id,
                    "periodname" => $item->getProgram->getActivityProgramType->period->pName,
                    "typeid" => $item->getProgram->getActivityProgramType->p_type->id,
                    "programname" => $item->getProgram->getActivityProgramType->p_type->ptName,
                    "schoolprogramname" => $item->getProgram->getActivityProgramType->activity->aName . ' ' . $item->getProgram->getActivityProgramType->period->pName . ' ' . $item->getProgram->getActivityProgramType->p_type->ptName
                ], $activityprogramArray)) {
                } else {
                    array_push($activityprogramArray, [
                        "programid" => $item->getProgram->getActivityProgramType->id,
                        "activityid" => $item->getProgram->getActivityProgramType->activity->id,
                        "activityname" => $item->getProgram->getActivityProgramType->activity->aName,
                        "periodid" => $item->getProgram->getActivityProgramType->period->id,
                        "periodname" => $item->getProgram->getActivityProgramType->period->pName,
                        "typeid" => $item->getProgram->getActivityProgramType->p_type->id,
                        "programname" => $item->getProgram->getActivityProgramType->p_type->ptName,
                        "schoolprogramname" => $item->getProgram->getActivityProgramType->activity->aName . ' ' . $item->getProgram->getActivityProgramType->period->pName . ' ' . $item->getProgram->getActivityProgramType->p_type->ptName
                    ]);
                }
            }
            return response()->json($activityprogramArray, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getSporClubProgram($id)
    {
        try {
            $schoolprogramArray = array();
            $user = Users::where("id", $id)->with(['uclubcontents'])
                ->has("uclubcontents.getProgram")->get();
            $collection = collect($user[0]['uclubcontents']);
            $unique = $collection->unique('club_program_id');
            foreach ($unique as $key => $item) {
                if (in_array([
                    "programid" => $item->club_program_id,
                    "clubprogramcontentid" => $item->id,
                    "description" => $item->description,
                    "programid" => $item->getProgram->getClubProgramType->id,
                    "clubid" => $item->getProgram->getClubProgramType->spor_club_id,
                    "clubname" => $item->getProgram->getClubProgramType->spor_club->scName,
                    "typeid" => $item->getProgram->getClubProgramType->p_type->id,
                    "programname" => $item->getProgram->getClubProgramType->p_type->ptName,
                    "schoolprogramname" => $item->getProgram->getClubProgramType->spor_club->scName . ' ' . $item->getProgram->getClubProgramType->p_type->ptName
                ], $schoolprogramArray)) {
                } else {
                    array_push($schoolprogramArray, [
                        "programid" => $item->club_program_id,
                        "clubprogramcontentid" => $item->id,
                        "description" => $item->description,
                        "programid" => $item->getProgram->getClubProgramType->id,
                        "clubid" => $item->getProgram->getClubProgramType->spor_club_id,
                        "clubname" => $item->getProgram->getClubProgramType->spor_club->scName,
                        "typeid" => $item->getProgram->getClubProgramType->p_type->id,
                        "programname" => $item->getProgram->getClubProgramType->p_type->ptName,
                        "schoolprogramname" => $item->getProgram->getClubProgramType->spor_club->scName . ' ' . $item->getProgram->getClubProgramType->p_type->ptName
                    ]);
                }
            }
            $collection = collect($schoolprogramArray);
            $unique = $collection->unique('schoolprogramname');
            $data = $unique->values()->all();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);

        }
    }

    public function getProgram($id)
    {
        try {
            $schoolprogramArray = array();
            $user = Users::where("id", $id)->with(['getspContents'])
                ->has("getspContents.getProgram")->get();
            $collection = collect($user[0]['getspContents']);

            $unique = $collection->unique('school_program_id');
            foreach ($unique as $key => $item) {
                if (in_array([
                    "schoolprogramid" => $item->school_program_id,
                    "schoolprogramcontentid" => $item->id,
                    "programid" => $item->getProgram->getSchoolProgramType->id,
                    "schoolid" => $item->getProgram->getSchoolProgramType->school->id,
                    "schoolname" => $item->getProgram->getSchoolProgramType->school->sName,
                    "typeid" => $item->getProgram->getSchoolProgramType->p_type->id,
                    "programname" => $item->getProgram->getSchoolProgramType->p_type->ptName,
                    "schoolprogramname" => $item->getProgram->getSchoolProgramType->school->sName . ' ' . $item->getProgram->getSchoolProgramType->p_type->ptName

                ], $schoolprogramArray)) {
                } else {
                    array_push($schoolprogramArray, [
                        "schoolprogramcontentid" => $item->id,
                        "schoolprogramid" => $item->school_program_id,
                        "programid" => $item->getProgram->getSchoolProgramType->id,
                        "schoolid" => $item->getProgram->getSchoolProgramType->school->id,
                        "schoolname" => $item->getProgram->getSchoolProgramType->school->sName,
                        "typeid" => $item->getProgram->getSchoolProgramType->p_type->id,
                        "programname" => $item->getProgram->getSchoolProgramType->p_type->ptName,
                        "schoolprogramname" => $item->getProgram->getSchoolProgramType->school->sName . ' ' . $item->getProgram->getSchoolProgramType->p_type->ptName
                    ]);
                }
            }
            $collection = collect($schoolprogramArray);
            $unique = $collection->unique('schoolprogramname');
            $data = $unique->values()->all();
            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function getSporClubTeam(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('user_spor_club_team_branch')
                    ->join('team', 'user_spor_club_team_branch.team_id', '=', 'team.id')
                    ->join('users', 'user_spor_club_team_branch.user_id', '=', 'users.id')
                    ->join('spor_club', 'user_spor_club_team_branch.spor_club_id', '=', 'spor_club.id')
                    ->join("spor_club_branch", "user_spor_club_team_branch.spor_club_team_branch_id", "=", "spor_club_branch.id")
                    ->select("user_spor_club_team_branch.*", "team.*", "users.*", "spor_club.*", "spor_club_branch.*")
                    ->where($parser)
                    ->latest("usctbid")
                    ->paginate(2);
                return response()->json($data, 200);
            } else {
                $data = DB::table('user_spor_club_team_branch')
                    ->join('team', 'user_spor_club_team_branch.team_id', '=', 'team.id')
                    ->join('users', 'user_spor_club_team_branch.user_id', '=', 'users.id')
                    ->join('spor_club', 'user_spor_club_team_branch.spor_club_id', '=', 'spor_club.id')
                    ->join("spor_club_branch", "user_spor_club_team_branch.spor_club_team_branch_id", "=", "spor_club_branch.id")
                    ->select("user_spor_club_team_branch.*", "team.*", "users.*", "spor_club.*", "spor_club_branch.*")
                    ->latest("usctbid")
                    ->paginate(2);
                return response()->json($data, 200);
            }
        } catch (\Error $e) {
            return response()->json([], 500);
        }
    }

    public function getSporClub(Request $request, $id)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $user = Users::find($id);
                $sporclub = $user->club()->where($parser)->latest()->paginate(2);
                return response()->json($sporclub, 200);
                //   $school = $user->schools()->latest()->paginate(2);
            } else {
                $user = Users::find($id);
                $sporclub = $user->club()->latest()->paginate(2);
                return response()->json($sporclub, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function updatePersons(Request $request, $id)
    {

        try {
            $valid = Validator::make($request->all(), [
                'name' => 'required|min:1|',
                'surname' => 'required|min:1|',
                'phone' => 'required|unique:users,uPhone,' . $id,
                'email' => 'required|unique:users,uEmail,' . $id,
                'tc' => 'required|unique:users,uTC,' . $id,
                'adress' => 'required|min:1|',
                'active' => 'required',
                'gender' => 'required',
                'proximities' => 'required',
                'title' => 'required',
                'province' => 'required',
                'unit' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json($valid->errors(), 204);
            } else {
                $user = Users::find($id);
                $user->uName = $request->name;
                $user->uSurname = $request->surname;
                $user->uPhone = $request->phone;
                $user->uPhoneOther = $request->otherphone;
                $user->uEmail = $request->email;
                $user->uTC = $request->tc;
                $user->uAdress = $request->adress;
                $user->uİsActive = $request->active;
                $user->uGender = $request->gender;
                /*              $user->uStatus = $request->status;*/
                $user->uEmailNotification = $request->emailnotification;
                $user->uSmsNotification = $request->smsnotification;
                $user->uproximities_id = $request->proximities;
                $user->utitle_id = $request->title;
                $user->uprovince_id = $request->province;
                $user->uunits_id = $request->unit;
                $user->uBirthDay = $request->birthday;
                $user->uFullName = $request->name . " " . $request->surname;
                if ($user->update()) {

                    return response()->json($user, 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function updatePersonImg(Request $request, $id)
    {

        try {
            $user = Users::find($id);
            $user->ufile_id = $request->fileid;
            if ($user->update()) {

                return response()->json($user, 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getAllStudentAndUsers(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            $sparser = $request->surlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $users = Users::where($parser)->get();
                $squery = $this->uriParser->queryparser($sparser);
                $students = Student::where($squery)->get();
                return response()->json([
                    ['user' => $users,
                        'student' => $students
                    ]
                ], 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getAllUsers(Request $request)
    {
        try {

            $queryparse = $request->urlparse;
            if ($queryparse) {
                if ($request->paginate) {
                    $parser = $this->uriParser->queryparser($queryparse);
                    $users = Users::with('file')->where($parser)
                        ->whereNotIn('id', $request->users)
                        ->orderBy('uName')->paginate(3);;
                    return response()->json($users, 200);
                } else {
                    $parser = $this->uriParser->queryparser($queryparse);
                    $users = Users::where($parser)->get();
                    return response()->json($users, 200);
                }

            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getAllPersonsExportExcel(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $users = Users::where($parser)->whereHas("user_u_types", function ($q) {
                    $q->where("user_u_types.user_types_id", 1);
                })->get();
                foreach ($users as $item) {
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
                    $item->user_u_types;
                    $item->province;
                    $item->title;
                    $item->file;
                }
                return response()->json($users, 200);
            } else {
                $users = Users::whereHas("user_u_types", function ($q) {
                    $q->where("user_u_types.user_types_id", 1);
                })->get();
                foreach ($users as $item) {
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
                    $item->province;
                    $item->title;
                    $item->user_u_types;
                    $item->file;
                }
                return response()->json($users, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function getPersons(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $users = Users::where($parser)->whereHas("user_u_types", function ($q) {
                    $q->where("user_u_types.user_types_id", 1);
                })->latest()->paginate(2);
                foreach ($users as $item) {
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
                    $item->user_u_types;
                    $item->province;
                    $item->title;
                    $item->file;
                    $item->unit;
                }
                return response()->json($users, 200);
            } else {
                return Users::all();
                $users = Users::whereHas("user_u_types", function ($q) {
                    $q->where("user_u_types.user_types_id", 1);
                })->latest()->paginate(2);
                foreach ($users as $item) {
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
                    $item->province;
                    $item->title;
                    $item->user_u_types;
                    $item->file;
                    $item->unit;
                }
                return response()->json($users, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    /*getSchoolProgram*/


    public function getUserProgram(Request $request)
    {
        try {
            $result = array();
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $user = Users::with([])->has("getspContents");
                return $user;
                /*   $parser = $this->uriParser->queryparser($queryparse);
                   $schoolProgram = SchoolProgram::where($parser)->get();
                   if (count($schoolProgram) > 0) {
                       $programContent = $schoolProgram[0]->getContent;

                       foreach ($programContent as $key => $item) {

                           $get_teachers = $item->getUsers->map(function ($u) {
                               $d["uFullName"] = $u["uFullName"];
                               $d["user_id"] = $u["id"];
                               return $d;
                           });

                           array_push($result,
                               [
                                   "schoolprogramid" => $schoolProgram[0]->id,
                                   "school_p_type_id" => $schoolProgram[0]->school_p_type_id,
                                   "school_id" => $schoolProgram[0]->school_id,
                                   "clases_id" => $schoolProgram[0]->clases_id,
                                   "branches_id" => $schoolProgram[0]->branches_id,
                                   "scoolprogramcontentid" => $item->id,
                                   "school_day_id" => $item->school_day_id,
                                   "school_hour_id" => $item->school_hour_id,
                                   "lessons" => [
                                       "lesson_id" => $item->lesson_id,
                                       "lName" => $item->getLesson->lName
                                   ],
                                   "users" => $get_teachers
                               ]
                           );
                       }
                       return response()->json($result, 200);*/
                /* } else {
                     return [];
                 }*/
            } else {
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getSchoolProgram(Request $request)
    {
        try {
            $user = Users::with([])->has("getspContents")->whereHas("schools", function ($q) use ($request) {
                $q->where("school_id", $request->schoolid);
            })->whereHas("user_u_types", function ($q) {
                $q->where("user_types_id", 1);
            })->get();
            if (count($user) > 0) {
                return response()->json($user, 200);
            } else {
                return response()->json([], 204);
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function show($id)
    {
        try {
            $user = Users::find($id);
            if ($user) {
                $user['file'] = $user->file;
                $user->title;
                $user->province;
                $user->user_u_types;
                $user->unit;
                return response()->json([$user], 200);
            } else {
                return response()->json([], 204);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function destroy($id)
    {
        try {
            $user = Users::find($id);
            $user->file;
            $fileDeleted = Storage::delete($user->file['name']);
            if ($fileDeleted != "") {
                if (Users::destroy($id)) {
                    return response()->json('Success.', 200);
                } else {
                    return response()->json([], 500);
                }
            } else {
                if (Users::destroy($id)) {
                    return response()->json('Success.', 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getLesson(Request $request, $id)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $user = Users::find($id);
                $lessons = $user->lessons()->where($parser)->latest()->paginate(2);
                return response()->json($lessons, 200);
                //   $school = $user->schools()->latest()->paginate(2);
            } else {
                $user = Users::find($id);
                $lessons = $user->lessons()->latest()->paginate(2);
                return response()->json($lessons, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function getStudents(Request $request)
    {
        try {
            return Users::find($request->userid)->students;
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getUserWithLesson(Request $request)
    {
        try {
            return Users::with('lessons')->where("id", $request->userid)->get();
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);

        }
    }

    public function getSchoolLessons(Request $request)
    {

        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('users_schools_lessons')
                    ->join('schools', 'users_schools_lessons.school_id', '=', 'schools.id')
                    ->join('users', 'users_schools_lessons.user_id', '=', 'users.id')
                    ->join('lessons', 'users_schools_lessons.lesson_id', '=', 'lessons.id')
                    ->select("users_schools_lessons.*", "schools.*", "users.*", "lessons.*")
                    ->where($parser)
                    ->latest("uslid")
                    ->paginate(2);
                return response()->json($data, 200);
            } else {
                $data = DB::table('users_schools_lessons')
                    ->join('schools', 'users_schools_lessons.school_id', '=', 'schools.id')
                    ->join('users', 'users_schools_lessons.user_id', '=', 'users.id')
                    ->join('lessons', 'users_schools_lessons.lesson_id', '=', 'lessons.id')
                    ->select("users_schools_lessons.*", "schools.*", "users.*", "lessons.*")
                    ->latest("uslid")
                    ->paginate(2);
                return response()->json($data, 200);
            }
        } catch (\Error $e) {
            return response()->json([], 500);
        }
    }

    public function getSchoolClasesBranches(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('users_schools_clases_branches')
                    ->join('schools', 'users_schools_clases_branches.school_id', '=', 'schools.id')
                    ->join('clases', 'users_schools_clases_branches.clases_id', '=', 'clases.id')
                    ->join('users', 'users_schools_clases_branches.user_id', '=', 'users.id')
                    ->join('branches', 'users_schools_clases_branches.branches_id', '=', 'branches.id')
                    ->select("users_schools_clases_branches.*", "schools.*", "clases.*", "users.*", "branches.*")
                    ->where($parser)
                    ->latest("uscbid")
                    ->paginate(2);
                return response()->json($data, 200);
            } else {
                $data = DB::table('users_schools_clases_branches')
                    ->join('schools', 'users_schools_clases_branches.school_id', '=', 'schools.id')
                    ->join('clases', 'users_schools_clases_branches.clases_id', '=', 'clases.id')
                    ->join('users', 'users_schools_clases_branches.user_id', '=', 'users.id')
                    ->join('branches', 'users_schools_clases_branches.branches_id', '=', 'branches.id')
                    ->select("users_schools_clases_branches.*", "schools.*", "clases.*", "users.*", "branches.*")
                    ->latest("uscbid")
                    ->paginate(2);
                return response()->json($data, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getSchoolClases(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $data = DB::table('users_schools_clases')
                    ->join('schools', 'users_schools_clases.school_id', '=', 'schools.id')
                    ->join('clases', 'users_schools_clases.clases_id', '=', 'clases.id')
                    ->join('users', 'users_schools_clases.user_id', '=', 'users.id')
                    ->select("users_schools_clases.*", "schools.*", "clases.*", "users.*")
                    ->where($parser)
                    ->latest("uscid")
                    ->paginate(2);
                return response()->json($data, 200);
            } else {
                $data = DB::table('users_schools_clases')
                    ->join('schools', 'users_schools_clases.school_id', '=', 'schools.id')
                    ->join('clases', 'users_schools_clases.clases_id', '=', 'clases.id')
                    ->join('users', 'users_schools_clases.user_id', '=', 'users.id')
                    ->select("users_schools_clases.*", "schools.*", "clases.*", "users.*")
                    ->latest("uscid")
                    ->paginate(2);
                return response()->json($data, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function getSchool(Request $request, $id)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $user = Users::find($id);
                $school = $user->schools()->where($parser)->latest()->paginate(2);
                return response()->json($school, 200);
                //   $school = $user->schools()->latest()->paginate(2);
            } else {
                $user = Users::find($id);
                $school = $user->schools()->latest()->paginate(2);
                return response()->json($school, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'name' => 'required|min:1|',
                'surname' => 'required|min:1|',
                'phone' => 'required|unique:users,uPhone|min:1|',
                'email' => 'required|unique:users,uEmail|min:1|',
                'tc' => 'required|unique:users,uTC|min:11|max:11',
                'adress' => 'required|min:1|',
                'active' => 'required',
                'gender' => 'required',
                'proximities' => 'required',
                'title' => 'required',
                'province' => 'required',
                'unit' => 'required',
                "file" => "required",
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 204);
            } else {
                $user = new Users();
                $user->uName = $request->name;
                $user->uSurname = $request->surname;
                $user->uPhone = $request->phone;
                $user->uPhoneOther = $request->otherphone;
                $user->uEmail = $request->email;
                $user->uTC = $request->tc;
                $user->uAdress = $request->adress;
                $user->uİsActive = $request->active;
                $user->uGender = $request->gender;
                $user->uEmailNotification = $request->emailnotification;
                $user->uSmsNotification = $request->smsnotification;
                $user->ufile_id = $request->file;
                $user->uproximities_id = $request->proximities;
                $user->utitle_id = $request->title;
                $user->uprovince_id = $request->province;
                $user->uunits_id = $request->unit;
                $user->uBirthDay = $request->birthday;
                $user->uFullName = $request->name . " " . $request->surname;
                if ($user->save()) {

                    return response()->json($user, 200);
                } else {
                    return response()->json([], 500);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }
}
