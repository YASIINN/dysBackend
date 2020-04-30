<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    public function loggable(){
        return $this->morphTo();
    }
}
