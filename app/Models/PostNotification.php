<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostNotification extends Model
{
    protected $table = "post_notification";
    public  function  touser(){
        return $this->belongsTo(Users::class, "to_user_id", "id")->with(['file']);
    }

    public  function  fromuser(){
        return $this->belongsTo(Users::class, "from_user_id", "id")->with(['file']);
    }

    public  function post(){
        return $this->belongsTo(Post::class, "post_id", "id");
    }
}
