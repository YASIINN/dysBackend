<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\ActivityProgramContent;
use App\Models\SchoolProgramContent;
use Illuminate\Http\Request;
use Storage;
use Illuminate\Support\Facades;
use App\Models\ActivityPeriodPivot as AP;
use App\Models\Student;
use App\Models\School;
use App\Models\Files;
use App\Models\SporClub;
use Validator;
use App\Models\StudentUserPivot as SUP;

use Unlu\Laravel\Api\QueryBuilder;
use function foo\func;


class StudentsController extends Controller
{


    /** Yasin */

    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function getAllStudents(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            if ($queryparse) {
                $parser = $this->uriParser->queryparser($queryparse);
                $students = Student::where($parser)->get();
                return response()->json($students, 200);
            } else {
                return response()->json([], 400);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function aschollClassBranchStudents(Request $request)
    {
        try {
            $students = Student::with(["activities", "grades", "file", "periods", 'discont'])
                ->whereHas("activities", function ($q) use ($request) {
                    $q->where("activities.id", $request->activityid);
                })->whereHas("grades", function ($q) use ($request) {
                    $q->where("grade_id", $request->gradeid);
                })->whereHas("periods", function ($q) use ($request) {
                    $q->where("period_id", $request->periodid);
                })->whereHas("discont", function ($q) use ($request) {
                    $q->where([
                        ['contentable_id', '=', $request->contentid],
                        ["discontDate", '=', date("Y-m-d")]
                    ]);
                })->get();
            if (count($students) > 0) {
                foreach ($students as $item) {
                    $collection = collect($item['discont']);
                    $filtered = $collection->where('discontDate', date("Y-m-d"))->where("contentable_id", $request->contentid);

                    $uniquedate = $filtered->all();

                    $key = array_keys($uniquedate);
                    $item->filter = $uniquedate[$key[0]];
                }
                return response()->json($students, 200);
            } else {
                $students = Student::with(["activities", "grades", "file", "periods", 'discont'])
                    ->whereHas("activities", function ($q) use ($request) {
                        $q->where("activities.id", $request->activityid);
                    })->whereHas("grades", function ($q) use ($request) {
                        $q->where("grade_id", $request->gradeid);
                    })->whereHas("periods", function ($q) use ($request) {
                        $q->where("period_id", $request->periodid);
                    })->get();
                return response()->json($students, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getAStudentsDiscont(Request $request)
    {
        try {
            /*
             *        day: today,
            clasesid: this.selectedclass.clases_id,
            schoolid: this.selectedschool.id,
            branchid: this.selectedclass.branches_id,
            dtype: 0,
            ptype: this.selectedptype
             *
             * */

            $result = array();
            $data = ActivityProgramContent::with(['discounts',
                'discounts.student',
                'discounts.dtype',
                'day',
                'hour',
                'activityprogram'
            ])->has("discounts")
                ->whereHas("discounts", function ($q) use ($request) {
                    $q->where([
                        ['discontDate', '=', $request->day],
                    ]);
                })->whereHas("discounts.student", function ($q) use ($request) {
                    $q->whereHas("grades", function ($qr) use ($request) {
                        $qr->where("grade_id", $request->gradeid);
                    });
                })
                ->whereHas("discounts.student", function ($q) use ($request) {
                    $q->whereHas("activities", function ($qr) use ($request) {
                        $qr->where("activity_id", $request->activityid);
                    });
                })
                ->whereHas("discounts.student", function ($q) use ($request) {
                    $q->whereHas("periods", function ($qr) use ($request) {
                        $qr->where("period_id", $request->periodid);
                    });
                })
                ->whereHas("activityprogram.getActivityProgramType", function ($q) use ($request) {
                    $q->where("p_type_id", $request->ptype);
                })
                ->get();

            foreach ($data as $key => $item) {

                foreach ($item->discounts as $keys => $discont) {
                    $collection = collect($discont->student[0]->grades);
                    $collectionact = collect($discont->student[0]->activities);
                    $collectionper = collect($discont->student[0]->periods);
                    $filtered = $collection->where('id', $request->gradeid); //grade
                    $filteredper = $collectionper->where('id', $request->periodid); //per
                    $filteredact = $collectionact->where('id', $request->activityid);//activi
                    $dper = $filteredper->all();
                    $dactv = $filteredact->all();
                    $dgrades = $filtered->all();
                    if (count($dgrades) > 0
                        &&
                        count($dactv) > 0
                        &&
                        count($dper) > 0
                    ) {
                        array_push($result,
                            [
                                "dtypeid" => $discont->dtype[0]->id,
                                "activityid" => $dactv,
                                "gradesid" => $dgrades,
                                "dtype" => $discont->dtype[0]->dtName,
                                "id" => $item->id,
                                "dayName" => $item->day->adName,
                                "hourName" => $item->hour->ahName,
                                "startHour" => $item->hour->beginDate,
                                "endHour" => $item->hour->endDate,
                                "activity_program_id" => $item->activity_program_id,
                                "activity_day_id" => $item->activity_day_id,
                                "activity_hour_id" => $item->activity_hour_id,
                                "lesson_id" => $item->lesson_id,
                                "discontid" => $discont->id,
                                "contentable_id" => $discont->contentable_id,
                                "contentable_type" => $discont->contentable_type,
                                "discontDate" => $discont->discontDate,
                                "studentid" => $discont->student[0]->id,
                                "fullname" => $discont->student[0]->s_fullname,
                                "periods" => $dper
                            ]);
                    }
                }
            }
            if (count($result) > 0) {
                $collection = collect($result);
                $grouped = $collection->groupBy('hourName');
                $data = $grouped->toArray();
                if ($request->key && $request->dtype != 0) {
                    foreach ($data as $key => $item) {
                        if ($key == $request->key) {
                            $collection = collect($data[$key]);
                            $filtered = $collection->where('dtypeid', $request->dtype);
                            $datafilter = $filtered->all();
                            $data[$key] = $datafilter;
                        }
                    }
                    return response()->json($data, 200);
                } else {
                    return response()->json($data, 200);
                }
            } else {
                return response()->json($result, 200);
            }
            /*            $collection = collect($result);
                        $grouped = $collection->groupBy('hourName');
                        $data = $grouped->toArray();
                        return $data;*/
            //2 kurs

            //1 ders
            return $data;
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getStudentsDiscont(Request $request)
    {
        try {
            $result = array();
            $data = SchoolProgramContent::with(['discounts', 'discounts.student', 'discounts.dtype', 'day', 'hour', 'getProgram'])->whereHas('discounts', function ($q) use ($request) {
                $q->where([
                    ['discontDate', '=', $request->day],
                ]);
            })->whereHas("discounts.student", function ($q) use ($request) {
                $q->whereHas("schools", function ($qr) use ($request) {
                    $qr->where("schools.id", $request->schoolid);
                });
            })->whereHas("discounts.student", function ($q) use ($request) {
                $q->whereHas("branches", function ($qr) use ($request) {
                    $qr->where("branches.id", $request->branchid);
                });
            })
                ->whereHas("discounts.student", function ($q) use ($request) {
                    $q->whereHas("clases", function ($qr) use ($request) {
                        $qr->where("clases.id", $request->clasesid);
                    });
                })->whereHas("getProgram.getSchoolProgramType", function ($q) use ($request) {
                    $q->where("p_type_id", $request->ptype);
                })
                ->get();
            foreach ($data as $key => $item) {

                foreach ($item->discounts as $keys => $discont) {
                    if ($discont->student[0]->schools[0]->id == $request->schoolid
                        &&
                        $discont->student[0]->clases[0]->id == $request->clasesid
                        &&
                        $discont->student[0]->branches[0]->id == $request->branchid
                    ) {

                        array_push($result,
                            [
                                "dtypeid" => $discont->dtype[0]->id,
                                "dtype" => $discont->dtype[0]->dtName,
                                "id" => $item->id,
                                "dayName" => $item->day->sdName,
                                "hourName" => $item->hour->shName,
                                "startHour" => $item->hour->beginDate,
                                "endHour" => $item->hour->endDate,
                                "school_program_id" => $item->school_program_id,
                                "school_day_id" => $item->school_day_id,
                                "school_hour_id" => $item->school_hour_id,
                                "lesson_id" => $item->lesson_id,
                                "discontid" => $discont->id,
                                "contentable_id" => $discont->contentable_id,
                                "contentable_type" => $discont->contentable_type,
                                "discontDate" => $discont->discontDate,
                                "studentid" => $discont->student[0]->id,
                                "fullname" => $discont->student[0]->s_fullname,
                            ]

                        );
                    }
                }
            }
            if (count($result) > 0) {
                $collection = collect($result);
                $grouped = $collection->groupBy('hourName');
                $data = $grouped->toArray();
                if ($request->key && $request->dtype != 0) {
                    foreach ($data as $key => $item) {
                        if ($key == $request->key) {
                            $collection = collect($data[$key]);
                            $filtered = $collection->where('dtypeid', $request->dtype);
                            $datafilter = $filtered->all();
                            $data[$key] = $datafilter;
                        }
                    }
                    return response()->json($data, 200);
                } else {
                    return response()->json($data, 200);
                }
            } else {
                return response()->json($result, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public
    function schollClassBranchStudents(Request $request)
    {
        try {
            $students = Student::with(["schools", "clases", "file", "branches", 'discont'])
                ->whereHas("schools", function ($q) use ($request) {
                    $q->where("school_id", $request->schoolid);
                })->whereHas("clases", function ($q) use ($request) {
                    $q->where("clases_id", $request->classid);
                })->whereHas("branches", function ($q) use ($request) {
                    $q->where("branches_id", $request->branchid);
                })->whereHas("discont", function ($q) use ($request) {
                    $q->where([
                        ['contentable_id', '=', $request->contentid],
                        ["discontDate", '=', date("Y-m-d")]
                    ]);
                })->get();

            if (count($students) > 0) {
                foreach ($students as $item) {
                    $collection = collect($item['discont']);
                    $filtered = $collection->where('discontDate', date("Y-m-d"))->where("contentable_id", $request->contentid);
                    $uniquedate = $filtered->all();
                    $key = array_keys($uniquedate);
                    $item->filter = $uniquedate[$key[0]];
                }
                return response()->json($students, 200);
            } else {
                $students = Student::with(["schools", "clases", "file", "branches", 'discont'])
                    ->whereHas("schools", function ($q) use ($request) {
                        $q->where("school_id", $request->schoolid);
                    })->whereHas("clases", function ($q) use ($request) {
                        $q->where("clases_id", $request->classid);
                    })->whereHas("branches", function ($q) use ($request) {
                        $q->where("branches_id", $request->branchid);
                    })->get();
                return response()->json($students, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    /** Yasin */

    public
    function index(Request $request)
    {
        // $queryBuilder = new QueryBuilder(new Student, $request);
        // return response()->json($queryBuilder->build()->paginate(), 200);
        $students = Student::latest()->paginate(10);
        return response()->json($students);
    }

    public
    function updateStudentStatus(Request $request, $id)
    {
        try {
            $student = Student::find($id);
            $student->s_status = $request->s_status;
            if ($student->update()) {
                return response()->json($student, 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

//post
    public
    function getStudents(Request $request)
    {
        if ($request->status) {
            $students = Student::with(["schools", "activities", "periods", "grades", "clases", "sdetail", "file", "branches"])->where("s_fullname", "like", $request->fullname . '%')->whereHas("users", function ($q) use ($request) {
                $q->where("uEmail", "like", $request["email"] . '%');
            })->paginate(10);
        } else {
            $students = Student::with(["schools", "activities", "periods", "grades", "clases", "sdetail", "file", "branches"])->latest()->paginate(10);
        }

        return response()->json($students);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public
    function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public
    function store(Request $request)
    {
        $valid = Validator::make($request->all(), [
            "email" => 'unique:students,s_email,NULL,id,deleted_at,NULL',
            'tc' => 'required|min:1|unique:students,s_tc,NULL,id,deleted_at,NULL',
        ]);
        if ($valid->fails()) {
            return response()->json($valid->errors(), 200);
        }
        $s = new Student();
        $s->s_name = $request->name;
        $s->s_surname = $request->surname;
        $s->s_fullname = $request->fullname;
        $s->s_email = $request->email;
        $s->s_birthday = $request->birthday;
        $s->school_no = $request->schoolNo;
        $s->s_tc = $request->tc;
        $s->s_phone = $request->h_phone;
        $s->s_gsm = $request->gsm;
        $s->is_active = $request->isActive;
        $s->s_gender = $request->gender["code"];
        $s->s_family = $request->family["code"];
        $s->s_address = $request->address;
        $s->file_id = $request->file_id;
        // return $s;
        if ($s->save()) {
            return response()->json($s, 201);
        } else {
        }
    }

    public
    function saveimage(Request $request)
    {

        // $imagePath = Storage::disk('public/uploads')->put('/students/', $request->file);


        $valid = Validator::make($request->all(), [
            'file' => 'mimes:jpeg,jpg,png|required|max:10000',
        ]);
        if ($valid->fails()) {
            return response()->json(['error' => $valid->errors()], 401);
        }
        try {
            // $imageName = $request->tc.'.'.$request->file->getClientOriginalExtension();
            //  $path = public_path("images/students/$imageName");
            $f = Files::whereName($request->tc)->first();
            if ($f) {
                if (file_exists($f->path)) {
                    Storage::delete($f->path);
                    // @unlink($f->path);
                }
                $size = $request->file->getSize();
                $type = $request->file->getMimeType();
                $imageName = $request->tc . '.' . $request->file->getClientOriginalExtension();
                $path = $request->file('file')->storeAs(
                    'public/students', $imageName
                );
                $spath = Storage::url($path);

                $f->path = env("HOST_URL") . $spath;
                $f->size = $size;
                $f->type = $type;
                $f->name = $request->tc;
                $f->viewname = $request->file->getClientOriginalName();
                $f->viewtype = "img";
                if ($f->save()) {
                    return response()->json($f, 201);
                } else {
                    return response()->json($f, 201);
                }
            } else {
                $size = $request->file->getSize();
                $type = $request->file->getMimeType();
                $imageName = $request->tc . '.' . $request->file->getClientOriginalExtension();
                $path = $request->file('file')->storeAs(
                    'public/students', $imageName
                );
                $spath = Storage::url($path);
                $file = new Files();
                $file->path = env("HOST_URL") . $spath;
                $file->size = $size;
                $file->type = $type;
                $file->name = $request->tc;
                $file->viewname = $request->file->getClientOriginalName();
                $file->viewtype = "img";
                if ($file->save()) {
                    return response()->json($file, 201);
                } else {
                    return response()->json($file, 201);
                }
            }
        } catch (\Throwable $th) {
            return response()->json($th, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function show($id)
    {
        try {
            $student = Student::with(["schools", "activities", "clases", "sdetail", "users", "file"])->findOrFail($id);
            return response()->json($student, 200);
        } catch (\Throwable $th) {
            return response()->json([], 204);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function update(Request $request, $id)
    {

        try {
            $s = Student::findOrFail($id);
        } catch (\Exception $exception) {
            $errormsg = 'Faaliyet Bulunamadı';
            return response()->json(['errormsg' => $errormsg]);
        }
        $valid = Validator::make($request->all(), [
            "email" => 'unique:students,s_email,' . $id . ',id,deleted_at,NULL',
            'tc' => 'required|min:1|unique:students,s_tc,' . $id . ',id,deleted_at,NULL',
        ]);

        if ($valid->fails()) {
            return response()->json($valid->errors(), 200);
        }
        $s->s_name = $request->name;
        $s->s_surname = $request->surname;
        $s->s_fullname = $request->fullname;
        $s->s_email = $request->email;
        $s->s_birthday = $request->birthday;
        $s->school_no = $request->schoolNo;
        $s->s_tc = $request->tc;
        $s->s_phone = $request->h_phone;
        $s->s_gsm = $request->gsm;
        $s->is_active = $request->isActive;
        $s->s_gender = $request->gender["code"];
        $s->s_family = $request->family["code"];
        $s->s_address = $request->address;
        $s->file_id = $request->file_id;
        // return $s;
        if ($s->update()) {
            return response()->json($s, 201);
        } else {
            return response()->json("Güncelleme başarısız", 204);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public
    function destroy($id)
    {
        try {
            $student = Student::findOrFail($id);
        } catch (\Exception $exception) {
            $errormsg = 'Veri Bulunamadı';
            return response()->json(['errormsg' => $exception->getMessage()]);
        }
        if ($student->delete()) {
            return response()->json(["message" => 'Veri başarıyla silindi.'], 200);
        }
    }
}
