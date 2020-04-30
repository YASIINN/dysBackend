<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSpeacialNotes extends Model
{
    protected $table = "user_special_notes";

    public function msgcontent()
    {
        return $this->belongsTo(SpeacialNotes::class, "special_note_id", "id")->with(['touser', 'fromuser']);
    }
}
