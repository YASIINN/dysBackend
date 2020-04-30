<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityProgramContentUserPivot extends Model
{
    protected $table = "activity_program_content_user";


    /*Yasin*/
    public function getContent()
    {
        return $this->belongsTo(ActivityProgramContent::class, "ap_content_id");
    }

    public function getUser()
    {
        return $this->belongsTo(Users::class, "user_id");
    }

    /*Yasin*/

    public function content()
    {
        return $this->belongsTo(ActivityProgramContent::class, "id", "ap_content_id");
    }

    public function user()
    {
        return $this->belongsTo(Users::class, "user_id");
    }

}
