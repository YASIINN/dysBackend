<?php

namespace App\Http\Controllers;

use App\Events\MsgTest;
use App\Helpers\Parser;
use App\Models\Comment;
use App\Models\Files;
use App\Models\Post;
use App\Models\PostFile;
use App\Models\PostLike;
use App\Models\PostNotification;
use App\Models\PostUser;
use App\Models\PostView;
use App\Models\Student;
use App\Models\SUPost;
use App\Models\UserPost;
use App\Models\Users;
use GraphQL\Examples\Blog\Data\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Validator;
use function foo\func;

class PostController extends Controller
{
    //
    public $imageType;
    public $uriParser;

    public function __construct(Parser $parser)
    {
        $this->imageType = $parser;
        $this->uriParser = $parser;
    }


    public function testet(Request $request)
    {
        try {
            /*    return Post::with(['cusers','cstudents'])
                    ->whereHas("cusers",function ($q){
                        $q->where("postable_id",7);
                    })
                    ->latest()
                    ->paginate(2);

                $data = SUPost::all();
                foreach ($data as $item) {
                    $item->contentable;
                }
                return $data;*/

            if ($request->type == "1") {
                $supost = new UserPost();
                $content = Users::find(8);
                $post = Post::find(7);
                if ($content->supost()->save($post)) {
                    return "tamam";
                }
            } else {
                $supost = new UserPost();
                $content = Student::find(1);
                $supost->post_id = 1;
                if ($content->supost()->save($supost)) {
                    return "tamam";
                }
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getpostview($id)
    {
        try {
            $postLikes = Post::with(['views'])
                ->where("id", $id)
                ->get();
            return response()->json($postLikes[0]->views, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getComments(Request $request)
    {
        try {
            $comments = Post::with(['comments'])
                ->where("id", $request->postid)
                ->get();
            return response()->json($comments, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function mesaj(Request $request)
    {
        try {
            event(new MsgTest($request->msg));
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function show($id)
    {
        try {
            return Post::with(['user', 'users', 'files','lesson', 'comments', 'posttype', 'category', 'likes', 'views', 'posttag'])
                ->where("id", $id)->get();
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);

        }
    }

    public function getpostlikeuser($id)
    {
        try {
            $postLikes = Post::with(['likes'])
                ->where("id", $id)
                ->get();
            return response()->json($postLikes[0]->likes, 200);
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function postlike(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'postid' => 'required',
                'userid' => 'required',
                'liketype' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }
            //1 beğendi
            if ($request->liketype == 1) {
                $pnotification = new PostNotification();
                $post = Post::with([])->where("id", $request->postid)->get();
                foreach ($post as $item) {
                    if ($item->user_id != $request->userid) {
                        $pnotification->isRead = "0";
                        // 1 beğeni 0 yorum
                        $pnotification->ntype = "1";
                        $pnotification->to_user_id = $request->userid;
                        $pnotification->from_user_id = $item->user_id;
                        $pnotification->post_id = $request->postid;
                        if ($pnotification->save()) {
                        } else {
                            return response()->json(['error' => "Bildirim Beğenme Hatası"], 500);
                        }
                    }
                }
                $plike = new PostLike();
                $plike->post_id = $request->postid;
                $plike->users_id = $request->userid;
                if ($plike->save()) {
                    return response()->json($plike, 200);
                }
            } else {
                $dnotification = PostNotification::where([
                    ["post_id", "=", $request->postid],
                    ["to_user_id", "=", $request->userid]
                ])->delete();
                if ($dnotification) {
                } else {
                    return response()->json(['error' => "Bildirim Beğeniyi Silme Hatası"], 500);

                }
                $unlike = PostLike::where([
                    ['post_id', '=', $request->postid],
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


    public function delete($id)
    {
        try {

            $post = Post::find($id);
            $post->files;
            $allfiledeleted = false;
            if (count($post->files) > 0) {
                foreach ($post->files as $file) {
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
                    if (Post::destroy($id)) {
                        return response()->json('Success.', 200);
                    }
                }
            } else {
                if (Post::destroy($id)) {
                    return response()->json('Success.', 200);
                }
            }

        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function createpostusers($data, $ustudents, $post)
    {
        try {
            $allsaveuser = false;
            /*       if ($request->type == "1") {
                       $supost = new UserPost();
                       $content = Users::find(8);
                       $post=Post::find(7);
                       if ($content->supost()->save($post)) {
                           return "tamam";
                       }
                   } else {
                       $supost = new UserPost();
                       $content = Student::find(1);
                       $supost->post_id = 1;
                       if ($content->supost()->save($supost)) {
                           return "tamam";
                       }
                   }*/
            foreach ($ustudents as $item) {
                $upost = new UserPost();
                $content = Student::find($item['id']);
                $post = Post::find($post->id);
                /* $userpost = new PostUser();
                 $userpost->post_id = $post->id;
                 $userpost->users_id = $item['id'];*/
                if ($content->supost()->save($post)) {
                    $allsaveuser = true;
                } else {
                    return response()->json(['error' => 'Post Kullanıcılara Eklenirken Hata'], 500);
                }
                /*            if ($userpost->save()) {
                                $allsaveuser = true;
                            } else {
                                return response()->json(['error' => 'Post Kullanıcılara Eklenirken Hata'], 500);
                            }*/
            }
            foreach ($data as $item) {
                $upost = new UserPost();
                $content = Users::find($item['id']);
                $post = Post::find($post->id);
                /*   $userpost = new PostUser();*/
                /*             $userpost->post_id = $post->id;
                             $userpost->users_id = $item['id'];*/
                if ($content->supost()->save($post)) {
                    $allsaveuser = true;
                } else {
                    return response()->json(['error' => 'Post Kullanıcılara Eklenirken Hata'], 500);
                }
                /*         if ($userpost->save()) {
                             $allsaveuser = true;
                         } else {
                             return response()->json(['error' => 'Post Kullanıcılara Eklenirken Hata'], 500);
                         }*/
            }
            if ($allsaveuser) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function createpostfiles($data, $post)
    {
        try {
            $allfilesave = false;
            foreach ($data as $item) {
                $postFile = new PostFile();
                $postFile->post_id = $post->id;
                $postFile->files_id = $item['id'];
                if ($postFile->save()) {
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

    public function create(Request $request)
    {
        try {
            $valid = Validator::make($request->all(), [
                'postdata' => 'required',
            ]);
            if ($valid->fails()) {
                return response()->json(['error' => $valid->errors()], 400);
            }
            //1 ise kişilerle değilse gruplarda paylaş
            if ($request->postdata['sharetype'] == 1) {
                //post oluştur
                $allsavefile = "";
                $allsaveuser = "";
                $post = new Post();
                $post->content = $request->postdata['pcontent'];
                $post->sharedtype = $request->postdata['sharedtype'];
                // 1 kaydı boş kayıt homework tablosunda - kaydı
                $post->category_id = $request->postdata['categoryid'];
                $post->exam_name = $request->postdata['ename'];
                $post->tag_id = $request->postdata['tag'];
                $post->post_type_id = $request->postdata['posttype'];
                $post->isstatus = $request->postdata['isstatus'];
                $post->iscomment = $request->postdata['iscomment'];
                $post->user_id = $request->postdata['userid'];
                $post->lesson_id = $request->postdata['lessonid'];
                if ($request->postdata['posttypename'] == "Ödev"
                    || $request->postdata['posttypename'] == "Sınav Tarihi" ||
                    $request->postdata['posttypename'] == "Etkinlik") {
                    $post->delivery_date = $request->postdata['postdate'];
                    //category_id
                }
                if ($request->postdata['posttypename'] == "Ödev") {
                    $post->estimated_time = $request->postdata['etime'];
                }
                if ($request->postdata['posttypename'] == "Etkinlik") {
                    $post->hour = $request->postdata['hour'];
                }
                if ($post->save()) {

                    $userSave = $this->createpostusers($request->users, $request->students, $post);
                    if ($userSave) {
                        if (count($request->fileList) > 0) {
                            $filesave = $this->createpostfiles($request->fileList, $post);
                            if ($userSave == true && $filesave == true) {
                                $addedpost = Post::with(['user','lesson', 'users', 'files', 'comments', 'posttype', 'category', 'likes', 'views', 'posttag'])
                                    ->where("id", $post->id)
                                    /*         ->whereHas("users", function ($q) use ($request) {
                                                 $q->where("users_id", 2);
                                                 //owner id
                                             })
                                             ->whereHas("user", function ($q) {
                                                 $q->where("user_id", 2);
                                             })*/
                                    ->get();
                                return response()->json($addedpost, 200);
                            } else {
                                return response()->json(['error' => 'Dosyalar Atanma Sırasında Hata'], 500);
                            }
                        } else {
                            $addedpost = Post::with(['user', 'users', 'files', 'comments', 'posttype', 'category', 'likes', 'views', 'posttag'])
                                ->where("id", $post->id)
                                /*     ->whereHas("users", function ($q) use ($request) {
                                         $q->where("users_id", 2);
                                         //owner id
                                     })
                                     ->whereHas("user", function ($q) {
                                         $q->where("user_id", 2);
                                     })*/
                                ->get();
                            //user id de gönderilecek
                            return response()->json($addedpost, 200);
                            //post dosya yok
                        }
                    } else {
                        return response()->json(['error' => 'Kişilere Atanma Sırasında Hata'], 500);
                        //kullanıcılar eklenemedi
                    }

                } else {
                    return response()->json(['error' => 'Post Oluşturulmadı'], 500);

                    // post kayıt eklenmedi
                }

            } else {

            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function getUserPosts(Request $request)
    {
        //TODO USERİD
        try {
            if ($request->filterType == 0) {
                return Post::with(['user', 'users', 'files','lesson', 'comments', 'posttype', 'category', 'likes', 'views', 'posttag'])
                    ->whereHas("user", function ($q) use ($request) {
                        $q->where("user_id", 2);
                    })->latest()
                    ->paginate(2);
            } else {
                return Post::with(['user', 'users', 'files','lesson', 'comments', 'posttype', 'category', 'likes', 'views', 'posttag'])
                    ->where("post_type_id", $request->filterType)
                    ->whereHas("user", function ($q) use ($request) {
                        $q->where("user_id", 2);
                    })
                    ->latest()
                    ->paginate(2);
            }
        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $queryparse = $request->urlparse;
            $parser = $this->uriParser->queryparser($queryparse);
            /*        if($queryparse){

                    }*/

//TODO user id gelecek
            if (count($request->users) > 0) {
                $data = Post::with(['user', 'users','lesson', 'files', 'comments', 'posttype', 'category', 'likes', 'views', 'posttag'])
                    ->where($parser)
                    ->orWhereHas("user", function ($q) use ($request) {
                        $q->whereIn("user_id", $request->users);
                    })
                    ->whereHas("users", function ($q) use ($request) {
                        $q->where("postable_id", 2);
                    })
                    ->latest()
                    ->paginate(2);
            } else {
                $data = Post::with(['user', 'users','lesson', 'files', 'comments', 'posttype', 'category', 'likes', 'views', 'posttag'])
                    ->where($parser)
                    ->whereHas("users", function ($q) use ($request) {
                        //users_id
                        $q->where("postable_id", 2);
                    })
                    ->latest()
                    ->paginate(2);
            }


            //Görüntüleme Olayına Bak
            foreach ($data as $item) {
                //kendi postum değilse
                //TODO USER ID
                if ($item->user->id != 2) {
                    $postView = PostView::where([
                        ['post_id', "=", $item->id],
                        ['users_id', "=", 2]
                    ])->get();
                    // ben gördüysem
                    if (count($postView) > 0) {
                        //Ben Postu Gördüm İşlem Yapma
                    } else {

                        //Benim ıd ile Ekle
                        $postv = new PostView();
                        $postv->post_id = $item->id;
                        $postv->users_id = 2;
                        if ($postv->save()) {

                        } else {

                        }
                    }
                }
            }


            if (count($request->users) > 0) {
                return Post::with(['user', 'users', 'files','lesson', 'comments', 'posttype', 'category', 'likes', 'views', 'posttag'])
                    ->orWhereHas("user", function ($q) use ($request) {
                        $q->where("user_id", 2);
                    })
                    ->orWhereHas("user", function ($q) use ($request) {
                        $q->whereIn("user_id", $request->users);
                    })
                    ->WhereHas("users", function ($q) {
                        $q->where("postable_id", 2);
                    })
                    ->latest()
                    ->paginate(2);
            } else {
                return Post::with(['user', 'users','lesson', 'files', 'comments', 'posttype', 'category', 'likes', 'views', 'posttag'])
                    ->where($parser)
                    ->orWhereHas("user", function ($q) use ($request) {
                        $q->where("user_id", 2);
                    })
                    ->WhereHas("users", function ($q) {
                        $q->where("postable_id", 2);
                    })
                    ->latest()
                    ->paginate(2);
                /*      return Post::with(['user', 'users', 'files', 'comments', 'posttype', 'category', 'likes', 'views', 'posttag'])
                          ->where("post_type_id", $request->ptype)
                          ->whereHas("users", function ($q) use ($request) {
                              $q->where("users_id", 2);
                          })
                          ->orWhereHas("user", function ($q) {
                              $q->where("user_id", 2);
                          })
                          ->latest()
                          ->paginate(2);*/

            }


        } catch (\Exception $e) {
            return response()->json($e->getMessage(), 500);
        }
    }

    public function test()
    {
        try {
            return Post::with(['user', 'files', 'comments', 'posttype', 'category', 'likes', 'views', 'posttag'])
                ->whereHas("user", function ($q) {
                    $q->where("user_id", 2);
                })
                ->get();
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }
}
