<?php

namespace App\Http\Controllers;

use App\Helpers\Parser;
use App\Models\SpeacialNotes;
use App\Models\Users;
use App\Models\UserSpeacialNotes;
use Illuminate\Http\Request;
use Validator;

class SpecialNotesController extends Controller
{
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->uriParser = $parser;
    }

    public function destroy($id)
    {
        try {
            if (SpeacialNotes::destroy($id)) {
                return response()->json(['msg' => "Success"], 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function readMsg(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            $parser = $this->uriParser->queryparser($queryparse);
            $specialNotes = SpeacialNotes::where($parser)->update(['status' => 1]);
            if ($specialNotes) {
                return response()->json($specialNotes, 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function deleteMsgBox(Request $request)
    {
        try {
            $deleted = UserSpeacialNotes::whereIn("id", $request->notes)->delete();
            if ($deleted) {
                return response()->json(['msg' => 'Success'], 200);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function msgBox(Request $request)
    {
        try {
            //TODO USERID
            $data = UserSpeacialNotes::with('msgcontent')
                ->where("user_id", 2)
                ->get();
            $response = $data->map(function ($item) {
                $data['id'] = $item->msgcontent->id;
                $data['created_at'] = $item->msgcontent->created_at;
                $data['updated_at'] = $item->msgcontent->updated_at;
                $data['to_user_id'] = $item->msgcontent->to_user_id;
                $data['from_user_id'] = $item->msgcontent->from_user_id;
                $data['status'] = $item->msgcontent->status;
                $data['content'] = $item->msgcontent->content;
                $data['userspecialnoteid'] = $item->id;
                $data["fromuser"] = $item->msgcontent->fromuser;
                $data["touser"] = $item->msgcontent->touser;
                return $data;
            });
            return $response;
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function showUserMsg(Request $request)
    {
        try {
            $users = SpeacialNotes::with(['touser', 'fromuser'])
                ->WhereHas("touser", function ($q) use ($request) {
                    $q->where('to_user_id', $request->touserid);
                })
                ->WhereHas("fromuser", function ($q) use ($request) {
                    $q->where('from_user_id', $request->userid);
                })
                ->orWhereHas("touser", function ($q) use ($request) {
                    $q->where('to_user_id', $request->userid);
                })
                ->orWhereHas("fromuser", function ($q) use ($request) {
                    $q->where('from_user_id', $request->touserid);

                })
                ->get();
            return $users;
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);

        }
    }

    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'touserid' => 'required',
                'fromuserid' => 'required',
                "ncontent" => "required"
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }
            $specialNotes = new SpeacialNotes();
            $specialNotes->to_user_id = $request->touserid;
            $specialNotes->from_user_id = $request->fromuserid;
            $specialNotes->status = 0;
            $specialNotes->content = $request->ncontent;
            if ($specialNotes->save()) {
                $userspecialnotesto = new UserSpeacialNotes();
                $userspecialnotesto->user_id = $request->touserid;
                $userspecialnotesto->special_note_id = $specialNotes->id;
                $userspecialnotesfrom = new UserSpeacialNotes();
                $userspecialnotesfrom->user_id = $request->fromuserid;
                $userspecialnotesfrom->special_note_id = $specialNotes->id;
                if ($userspecialnotesfrom->save() && $userspecialnotesto->save()) {
                    $data = UserSpeacialNotes::with('msgcontent')
                        ->where([
                            ["user_id", "=", 2],
                            ['special_note_id', "=", $specialNotes->id]
                        ])
                        ->get();

                    $response = $data->map(function ($item) {
                        $data['id'] = $item->msgcontent->id;
                        $data['created_at'] = $item->msgcontent->created_at;
                        $data['updated_at'] = $item->msgcontent->updated_at;
                        $data['to_user_id'] = $item->msgcontent->to_user_id;
                        $data['from_user_id'] = $item->msgcontent->from_user_id;
                        $data['status'] = $item->msgcontent->status;
                        $data['content'] = $item->msgcontent->content;
                        $data['userspecialnoteid'] = $item->id;
                        $data["fromuser"] = $item->msgcontent->fromuser;
                        $data["touser"] = $item->msgcontent->touser;
                        return $data;
                    });
                    return response()->json($response, 200);
                }
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function index(Request $request)
    {
        //TODO USERID
        try {
            $users = SpeacialNotes::with(['touser', 'fromuser'])
                ->orWhereHas("touser", function ($q) use ($request) {
                    $q->where('to_user_id', 2);
                })
                ->orWhereHas("fromuser", function ($q) use ($request) {
                    $q->where('from_user_id', 2);

                })->get();
            /*  $users=Users::with(['file','title','tomessage','frommessage'])
                  ->orWhereHas("tomessage",function ($q) use ($request) {
                          $q->where('to_user_id',$request->userid);
                  })
                  ->orWhereHas("frommessage",function($q) use ($request) {
                      $q->where('from_user_id',$request->userid);

                  })
                  ->where("uİsActive",1)->get();*/
            return response()->json($users, 200);

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
