<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentFile;
use App\Models\CommentLike;
use App\Models\Files;
use App\Models\Post;
use App\Models\PostNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;

class CommentController extends Controller
{
    public function createCommentFile($data, $commentid)
    {
        try {
            $allfilesave = false;
            foreach ($data as $item) {
                $cfile = new CommentFile();
                $cfile->comment_id = $commentid;
                $cfile->files_id = $item['id'];
                if ($cfile->save()) {
                    $allfilesave = true;
                }
            }
            if ($allfilesave) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function delete($id)
    {
        $comment = Comment::find($id);
        $comment->files;
        $allfiledeleted = false;
        if (count($comment->files) > 0) {
            foreach ($comment->files as $file) {
                if (Files::destroy($file->id)) {
                    $allfiledeleted = true;
                } else {
                    return response()->json(['error' => "Dosyalar VeriTabanından  Silinemedi"], 500);
                }
                if ($file->name != "") {
                    $fileDeleted = Storage::delete($file->name);
                    if ($fileDeleted) {
                        $allfiledeleted = true;
                    } else {
                        return response()->json(['error' => "Dosyalar Fiziksel Olarak Silinemedi"], 500);
                    }
                }
            }
            if ($allfiledeleted) {
                if (Comment::destroy($id)) {
                    return response()->json('Success.', 200);
                }
            }
        } else {
            if (Comment::destroy($id)) {
                return response()->json('Success.', 200);
            }
        }
    }

    public function store(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'postid' => 'required',
                'userid' => 'required',
                "ccontent" => "required"
            ]);
            $comment = new Comment();
            if (count($request->fileList) > 0) {
                $comment->content = $request->ccontent;
                $comment->post_id = $request->postid;
                $comment->user_id = $request->userid;

                if ($comment->save()) {
                    $pnotification = new PostNotification();
                    $post = Post::with([])->where("id", $request->postid)->get();
                    foreach ($post as $item) {
                        if ($item->user_id != $request->userid) {
                            $pnotification->isRead = "0";
                            // 1 beğeni 0 yorum
                            $pnotification->ntype = "0";
                            $pnotification->to_user_id = $request->userid;
                            $pnotification->from_user_id = $item->user_id;
                            $pnotification->post_id = $request->postid;
                            if ($pnotification->save()) {
                            } else {
                                return response()->json(['error' => "Bildirim Yorum Hatası"], 500);
                            }
                        }
                    }
                    $fileSave = $this->createCommentFile($request->fileList, $comment->id);
                    if ($fileSave) {
                        $comments = Post::with(['comments'])
                            ->where("id", $request->postid)
                            ->get();
                        return response()->json($comments, 200);
                    } else {
                        return response()->json(['error' => 'Dosya Oluşturulamadı'], 500);

                    }
                } else {
                    return response()->json(['error' => 'Yorum Oluşturulamadı'], 500);

                }
                //dosya var

            } else {
                $comment->content = $request->ccontent;
                $comment->post_id = $request->postid;
                $comment->user_id = $request->userid;
                if ($comment->save()) {
                    $pnotification = new PostNotification();
                    $post = Post::with([])->where("id", $request->postid)->get();
                    foreach ($post as $item) {
                        if ($item->user_id != $request->userid) {
                            $pnotification->isRead = "0";
                            // 1 beğeni 0 yorum
                            $pnotification->ntype = "0";
                            $pnotification->to_user_id = $request->userid;
                            $pnotification->from_user_id = $item->user_id;
                            $pnotification->post_id = $request->postid;
                            if ($pnotification->save()) {
                            } else {
                                return response()->json(['error' => "Bildirim Yorum Hatası"], 500);
                            }
                        }
                    }
                    $comments = Post::with(['comments'])
                        ->where("id", $request->postid)
                        ->get();
                    return response()->json($comments, 200);

                } else {
                    return response()->json(['error' => 'Yorum Oluşturulamadı'], 500);
                }

            }
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function commentlike(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'commentid' => 'required',
                'userid' => 'required',
                'liketype' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }
            //1 beğendi
            if ($request->liketype == 1) {
                $clike = new CommentLike();
                $clike->comment_id = $request->commentid;
                $clike->users_id = $request->userid;
                if ($clike->save()) {
                    return response()->json($clike, 200);
                }
            } else {
                $unlike = CommentLike::where([
                    ['comment_id', '=', $request->commentid],
                    ['users_id', '=', $request->userid],
                ])->delete();
                if ($unlike) {
                    return response()->json("Success", 200);
                }
            }

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }
}
