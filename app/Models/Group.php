<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = ["name", "code"];
    public function groupable()
    {
        return $this->morphTo();
    }

    public function students()
    {
        return $this->morphedByMany(Student::class, 'groupable');
    }
    public function users()
    {
        return $this->morphedByMany(Users::class, 'groupable');
    }
}
