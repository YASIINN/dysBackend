<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Period;
use Validator;

class PeriodController extends Controller
{
    public function index()
    {
       $periods = Period::all();
       return response()->json($periods, 200);
    }
    public function store(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name'=>'required|min:1|unique:periods,pName,NULL,id',
            'code'=>'required|min:1|unique:periods,pCode,NULL,id',
        ]);
         if ($valid->fails()) {
             return response()->json(['error'=>$valid->errors()], 200);
        }
        $per = new Period();
        $per->pName = $request->name;
        $per->pCode = $request->code;
        if($per->save()){
          return response()->json($per, 201);
        }
    }
    public function show($id)
    {
        //
    }
    public function update(Request $request, $id)
    {
        try {
            $period = Period::findOrFail($id);
        } catch(\Exception $exception){
            $errormsg = 'Faaliyet Bulunamadı';
            return response()->json(['errormsg'=>$errormsg]);
        }
        $valid = Validator::make($request->all(), [
            'name'=>'required|min:1|unique:periods,pName,'.$id.',id',
            'code'=>'required|min:1|unique:periods,pCode,'.$id.',id',
            
        ]);
         if ($valid->fails()) {
             return response()->json($valid->errors(), 200);
        }

        $period->pName = $request->name;
        $period->pCode = $request->code;
        if($period->update()){
            return response()->json($period, 201);
        }
    }
    public function destroy($id)
    {
        try {
            $period = Period::findOrFail($id);
        } catch(\Exception $exception){
            $errormsg = 'Faaliyet Bulunamadı';
            return response()->json(['errormsg'=>$errormsg]);
        }
        if(count($period->uniqactivities) > 0){
           $period->uniqactivities()->detach();
        }
        if($period->delete()){
            return response()->json(["message" =>'Takvim başarıyla silindi.'], 200);
        }
    }
}
