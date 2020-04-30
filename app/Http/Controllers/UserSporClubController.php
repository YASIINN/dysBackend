<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\Users;
use App\Models\UserSporClub;
use App\Models\UserSporClubTeamBranch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;

class UserSporClubController extends Controller
{

    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }


    public function delete(Request $request)
    {
        try {
            $userid = $request->userid;
            $sporclubid = $request->sporclubid;
            DB::transaction(function () use ($userid, $sporclubid) {
                $result = UserSporClub::where([
                    ['spor_club_id', "=", $sporclubid],
                    ['users_id', "=", $userid],
                ])->delete();
                if ($result) {
                    $res = UserSporClubTeamBranch::where([
                        ['spor_club_id', "=", $sporclubid],
                        ['user_id', "=", $userid],
                    ])->delete();
                    if ($res) {
                        return response()->json('Ürün başarıyla silindi.', 200);
                    } else {
                        return response()->json([], 500);
                    }
                } else {
                    return response()->json([], 500);
                }
            });
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'usersporclublist' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }
            $allSaved = false;
            foreach ($request->usersporclublist as $item) {
                $usersporclub = new UserSporClub();
                $isHave = UserSporClub::where([
                    ["spor_club_id", "=", $item['sporclubid']],
                    ["users_id", "=", $item['userid']],
                ])->get();
                if (count($isHave) > 0) {
                    $allSaved = true;
                } else {
                    $usersporclub->users_id = $item['userid'];
                    $usersporclub->spor_club_id = $item['sporclubid'];
                    if ($usersporclub->save()) {
                        $allSaved = true;
                    } else {
                        $allSaved = false;
                    }
                }
            }
            if ($allSaved) {
                return response()->json($usersporclub, 200);
            } else {
                return response()->json([], 500);
            }
        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }


}
