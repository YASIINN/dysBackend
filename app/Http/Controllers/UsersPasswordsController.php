<?php

namespace App\Http\Controllers;

use App\Models\UsersPasswords;
use Illuminate\Http\Request;
use Validator;

class UsersPasswordsController extends Controller
{
    //
    public function store(Request $request)
    {
        try {
            $upass = new UsersPasswords();
            $upass->user_id = $request->userid;
            $upass->uPassword = "3c8c073fcd455e907033a8efd06d59b6";
            if ($upass->save()) {
                return response()->json($upass, 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }

        //
    }
}
