<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    protected $table = "post";

 /*   public function users()
    {
        return $this->belongsToMany(Users::class, "post_user")->with(['title', 'file']);
    }*/

    public  function users(){
        return $this->morphedByMany(Users::class,"postable")->with(['title','file']);
     /*   return $this->hasMany(UserPost::class);*/
       /* return $this->belongsToMany(UserPost::class,"user_post");*/

    }
    public  function cstudents(){
        return $this->morphedByMany(Student::class,"postable");
        /*   return $this->hasMany(UserPost::class);*/
        /* return $this->belongsToMany(UserPost::class,"user_post");*/

    }

    public function files()
    {
        return $this->belongsToMany(Files::class, "post_file");

    }

    public function views()
    {
        return $this->belongsToMany(Users::class, "post_view")->with('file');

    }

    public function comments()
    {
        return $this->hasMany(Comment::class, "post_id", "id")->with(['files', 'user', 'likes']);
    }

    public function likes()
    {
        return $this->belongsToMany(Users::class, "post_like")->with('file');
    }

    public function user()
    {
        return $this->belongsTo(Users::class, "user_id", "id")->with(['title', 'file']);;
    }

    public  function  lesson(){
        return $this->belongsTo(Lessons::class, "lesson_id", "id");

    }

    public function posttag()
    {
        return $this->belongsTo(PostTag::class, "tag_id", "id");

    }

    public function posttype()
    {
        return $this->belongsTo(PostType::class, "post_type_id", "id");
    }

    public function category()
    {
        return $this->belongsTo(HomeWorkType::class, "category_id", "id");
    }
    public  static  function  boot(){
        parent::boot();
        static::created(function($data){
            return $data;
            print_r("Selam");
        });
    }

  /*  public static function boot()
    {*/
  /*      parent::boot();*/
     /*   static::deleting(function ($post) {*/
   /*         $post->likes()->detach();*/

            /*        $user->getspContents()->delete();
                    UsersSchools::where("users_id", $user->id)->delete();
                    UsersSchoolsClases::where("user_id", $user->id)->delete();
                    UsersSchoolsClasesBranches::where("user_id", $user->id)->delete();
                    UsersSchoolsLessons::where("user_id", $user->id)->delete();
                    UserSporClub::where("users_id", $user->id)->delete();
                    UserSporClubTeamBranch::where("user_id", $user->id)->delete();
                    UserUTypes::where("users_id", $user->id)->delete();
                    ActivityProgramContentUserPivot::where("user_id", $user->id)->delete();
                    ActivityUser::where("users_id", $user->id)->delete();
                    ActivityUserClases::where("user_id", $user->id)->delete();
                    ActivityUserLessons::where("user_id", $user->id)->delete();
                    ActivityUserPeriod::where("user_id", $user->id)->delete();
                    StudentUserPivot::where("users_id", $user->id)->delete();
                    UsersPasswords::where("user_id", $user->id)->delete();*/
 /*       });*/
   /* }*/
    //
}
