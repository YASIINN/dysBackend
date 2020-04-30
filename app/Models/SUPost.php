<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SUPost extends Model
{
    protected  $table="supost";
    public function contentable()
    {
        return $this->morphTo();
    }
}
