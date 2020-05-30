<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SporClubTeamBranch extends Model
{
    protected $table = "spor_club_team_branch";
    protected $primaryKey = 'sctbid';

    public function club()
    {
        return $this->belongsTo(SporClub::class, "spor_club_id", "id");
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function branch()
    {
        return $this->belongsTo(SporClubBranch::class, "sbranch_id", "id");
    }

        //adem
    public static function boot()
        {
            parent::boot(); 
            static::created(function ($stb) {
                $cName = $stb->club->scName;
                $cCode = $stb->club->scCode;
                $tName = $stb->team->stName;
                $tCode = $stb->team->stCode;
                $bName = $stb->branch->sbName;
                $bCode = $stb->branch->sbCode;
                $g = new Group;
                $g->name = $cName . ' ' . $tName . ' ' . $bName;
                $g->code = $cCode . ' ' . $tCode . ' ' . $bCode;
                $stb->group()->save($g);
            });
    
            static::deleted(function ($stb) {
                $stb->group()->delete();
            });
        }
        public function group()
        {
            return $this->morphOne(Group::class, 'groupable');
        }
        //adem
}
