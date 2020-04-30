<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SporClub extends Model
{
    protected $fillable = ["scName,scCode,company_id"];
    protected $table = "spor_club";

    public function getCompanies()
    {
        return $this->belongsTo(Company::class, "company_id", "id");
    }

    public function users()
    {
        return $this->belongsToMany(Users::class, "user_spor_club");
    }

    public function team()
    {
        return $this->hasMany(Team::class, "spor_club_id", "id");
    }
}
