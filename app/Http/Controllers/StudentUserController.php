<?php

namespace App\Http\Controllers;


use Unlu\Laravel\Api\RequestCreator;
use Unlu\Laravel\Api\QueryBuilder;
use Illuminate\Http\Request;
use App\Models\UserUTypes;
use App\Models\Users;
use App\Models\Student;
use App\Models\Files;
use Validator;
use Storage;

class StudentUserController extends Controller
{

    public function getUsersOrStudents(Request $request){
          $results;
          if($request->type === 'users'){
              $st = Student::find($request->id);
              $results = $st->users;
          } else {
              $user = Users::find($request->id);
              $results = $user->students;
          }
          return $results;
    }
    public function searchUsers(Request $request){
        $users = [];
        if($request->status){
            $users = Users::with("proximity")->where("uName", $request->fullname)->orWhere("uSurname", $request->fullname)->orWhere("uFullName", $request->fullname)->paginate(10);
        } else {
            $users = Users::with("proximity")->paginate(10);
        }
        // $users=Users::latest()->paginate(10);
       return $users;
        // $request = RequestCreator::createWithParameters([
        //     'uEmail' => $request->email,
        //     'uFullName' => "$request->fullname*",
        //   ]);
        //   $queryBuilder = new QueryBuilder(new Users, $request);
        //   return response()->json($queryBuilder->build()->paginate(), 200);
    }
    public function saveimage(Request $request){
            
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
    if($f){
        if(file_exists($f->path)) {
            Storage::delete($f->path);
            // @unlink($f->path);
        }
        $size = $request->file->getSize();
        $type = $request->file->getMimeType();
        $imageName = $request->tc.'.'.$request->file->getClientOriginalExtension();
        $path = $request->file('file')->storeAs(
            'public/parents', $imageName
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
        $imageName = $request->tc.'.'.$request->file->getClientOriginalExtension();
        $path = $request->file('file')->storeAs(
            'public/parents', $imageName
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
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'name' => 'required|min:1|',
                'surname' => 'required|min:1|',
                'phone' => 'required|unique:users,uPhone|min:1|',
                'email' => 'required|unique:users,uEmail|min:1|',
                'tc' => 'required|unique:users,uTC|min:11|max:11',
                'address' => 'required|min:1|',
                'isActive' => 'required',
                'gender' => 'required',
                'proximities' => 'required',
                'title' => 'required',
                'province' => 'required',
                'unit' => 'required',
                "file_id" => "required",
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
                $user->uJob = $request->job;
                $user->uAdress = $request->address;
                $user->uİsActive = $request->isActive;
                $user->uGender = $request->gender["code"];
                // $user->uStatus = $request->status;
                $user->uEmailNotification = $request->emailnotification;
                $user->uSmsNotification = $request->smsnotification;
                $user->ufile_id = $request->file_id;
                $user->uproximities_id = $request->proximities["code"];
                $user->utitle_id = $request->title;
                $user->uprovince_id = $request->province;
                $user->uunits_id = $request->unit;
                $user->uBirthDay = $request->birthday;
                $user->uFullName = $request->name . " " . $request->surname;
                if ($user->save()) {
                    $student = Student::find($request->student_id);
                    $exists = $student->users()->where('users.id', $user->id)->exists();
                    if(!$exists){
                        $student->users()->attach($user->id);
                    }
                    return response()->json($user, 201);

                } else {
                    return response()->json(["message"=>"Kayıt eklenirken hata oluştu."]);
                }
            }
        } catch (\Exception $e) {
            return response()->json(["message"=>"Kayıt eklenirken hata oluştu."]);
        }
    }
    public function assignUserStudent(Request $request){
       try {
        $student = Student::find($request->student_id);
        $exists = $student->users()->where('users.id', $request->user_id)->exists();
        if(!$exists){
            $student->users()->attach($request->user_id);
            $utypes = UserUTypes::where("users_id", $request->user_id)
                    ->where("user_types_id", 2)
                    ->get();
            if($utypes->count() === 0){
                $userutype = new UserUTypes();
                $userutype->users_id = $request->user_id;
                $userutype->user_types_id = 2;
                $userutype->save();
            }
        }
        return response()->json(["message"=>"Öğrenci velisi başarıyla eklendi"], 200);
       } catch (\Throwable $th) {
           return response()->json($th->getMessage(), 203);
       }
    }
    public function deleteUserStudent(Request $request){
        try {
         $student = Student::find($request->student_id);
         $exists = $student->users()->where('users.id', $request->user_id)->exists();
         if($exists){
             $student->users()->detach($request->user_id);

             $veli = Users::find($request->user_id);
             $userstudents = $veli->students;

             if($userstudents->count() === 0){
                UserUTypes::where("users_id", $request->user_id)
                        ->where("user_types_id", 2)
                        ->delete();
            }
         }
         return response()->json(["message"=>"Öğrenci velisi başarıyla silindi"], 200);
        } catch (\Throwable $th) {
            return response()->json($th->getMessage(), 203);
        }
     }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $id = $request->id;
        try {
            $user = Users::findOrFail($id);
        } catch(\Exception $exception){
            $errormsg = 'Veli Bulunamadı';
            return response()->json(['errormsg'=>$errormsg]);
        }
        try {
            $valid = Validator::make($request->all(), [
                'name' => 'required|min:1|',
                'surname' => 'required|min:1|',

                'email'=>'required|min:1|unique:users,uEmail,'.$id.',id,deleted_at,NULL',
                'phone'=>'required|min:1|unique:users,uPhone,'.$id.',id,deleted_at,NULL',
                'tc'=>'required|min:1|unique:users,uTC,'.$id.',id,deleted_at,NULL',

                'address' => 'required|min:1|',
                'isActive' => 'required',
                'gender' => 'required',
                'proximities' => 'required',
                "file_id" => "required",
            ]);
            if ($valid->fails()) {
                return response()->json($valid->errors(), 200);
            } else {
                $user->uName = $request->name;
                $user->uSurname = $request->surname;
                $user->uPhone = $request->phone;
                $user->uPhoneOther = $request->otherphone;
                $user->uEmail = $request->email;
                $user->uTC = $request->tc;
                $user->uJob = $request->job;
                $user->uAdress = $request->address;
                $user->uİsActive = $request->isActive;
                $user->uGender = $request->gender["code"];
                // $user->uStatus = $request->status;
                $user->uEmailNotification = $request->emailnotification;
                $user->uSmsNotification = $request->smsnotification;
                $user->ufile_id = $request->file_id;
                $user->uproximities_id = $request->proximities["code"];
                // $user->utitle_id = $request->title;
                // $user->uprovince_id = $request->province;
                // $user->uunits_id = $request->unit;
                $user->uBirthDay = $request->birthday;
                $user->uFullName = $request->name . " " . $request->surname;
                if ($user->update()) {
                    return response()->json(["message"=>"Veli başarıyla güncellendi"], 201);
                } else {
                    return response()->json("Veli Güncellenemedi", 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
