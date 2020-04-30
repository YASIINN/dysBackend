<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTypes extends Model
{
    //
    protected $table = "users_types";
    public function users()
    {
        return $this->belongsToMany(Users::class, "user_u_types");
    }
}
