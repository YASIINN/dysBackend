<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    //
    protected $table = "comment";

    public function user()
    {
        return $this->belongsTo(Users::class, "user_id", "id")->with(['file']);

    }

    public function likes()
    {
        return $this->belongsToMany(Users::class, "comment_like");
    }

    public function files()
    {
        return $this->belongsToMany(Files::class, "comment_file");
    }

    public function post()
    {
        return $this->belongsTo(Post::class, "post_id", "id");
    }
}
