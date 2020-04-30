<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpeacialNotes extends Model
{
    protected  $table="special_notes";
    public function touser()
    {
        return $this->belongsTo(Users::class, "to_user_id", "id")->with(['file']);
    }
    public function fromuser()
    {
        return $this->belongsTo(Users::class, "from_user_id", "id")->with(['file']);
    }

}
