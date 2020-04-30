<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\UserSporClubTeamBranch;
use Illuminate\Http\Request;
use Validator;

class UserSporClubTeamBranchController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function delete(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'userid' => 'required',
                'sporclubid' => 'required',
                'teamid' => 'required',
                'branchid' => 'required',
            ]);
            $result = UserSporClubTeamBranch::where([
                ["spor_club_id", "=", $request->sporclubid],
                ["user_id", "=", $request->userid],
                ["team_id", "=", $request->teamid],
                ["spor_club_team_branch_id", "=", $request->branchid],
            ])->delete();
            if ($result) {
                return response()->json("Success", 200);
            } else {
                return response()->json([], 500);
            }

        } catch (\Exception $e) {
            return response()->json($e, 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'usersporclubteamlist' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }
            $allSaved = false;
            foreach ($request->usersporclubteamlist as $item) {
                $usersporclub = new UserSporClubTeamBranch();
                $isHave = UserSporClubTeamBranch::where([
                    ["spor_club_id", "=", $item['sporclubid']],
                    ["user_id", "=", $item['userid']],
                    ["team_id", "=", $item['teamid']],
                    ["spor_club_team_branch_id", "=", $item['branchid']],
                ])->get();
                if (count($isHave) > 0) {
                    $allSaved = true;
                } else {
                    $usersporclub->user_id = $item['userid'];
                    $usersporclub->spor_club_id = $item['sporclubid'];
                    $usersporclub->team_id = $item['teamid'];
                    $usersporclub->spor_club_team_branch_id = $item['branchid'];
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
