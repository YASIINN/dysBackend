<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Grade;
use Validator;

class GradeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $grades = Grade::all();
       return response()->json($grades, 200);  
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
             //burada soft delete kontrolü var
             $valid = Validator::make($request->all(), [
                'name'=>'required|min:1|unique:grades,gName,NULL,id',
                'code'=>'required|min:1|unique:grades,gCode,NULL,id',
             
            ]);
             if ($valid->fails()) {
                 return response()->json($valid->errors(), 200);
            }
            $gr = new Grade();
            $gr->gName = $request->name;
            $gr->gCode = $request->code;
            if($gr->save()){
              return response()->json($gr, 201);
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
    public function update(Request $request, $id)
    {
        try {
            $grade = Grade::findOrFail($id);
        } catch(\Exception $exception){
            $errormsg = 'Faaliyet Bulunamadı';
            return response()->json(['errormsg'=>$errormsg]);
        }
        $valid = Validator::make($request->all(), [
            'name'=>'required|min:1|unique:grades,gName,'.$id.',id',
            'code'=>'required|min:1|unique:grades,gCode,'.$id.',id',
            
        ]);
         if ($valid->fails()) {
             return response()->json($valid->errors(), 200);
        }

        $grade->gName = $request->name;
        $grade->gCode = $request->code;
        if($grade->update()){
            return response()->json($grade, 201);
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
        try {
            $grade = Grade::findOrFail($id);
        } catch(\Exception $exception){
            $errormsg = 'Faaliyet Bulunamadı';
            return response()->json(['errormsg'=>$errormsg]);
        }
        if(count($grade->activities) > 0){
            $grade->activities()->detach();
        }
        if($grade->delete()){
            return response()->json(["message" =>'Grup başarıyla silindi.'], 200);
        }
    }
}
