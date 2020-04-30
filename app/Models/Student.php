<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\ActivityPeriodLessonStudentPivot as APLS;
use App\Models\SchoolClassLessonStudentPivot as SCLS;


class Student extends Model
{
    use Softdeletes;

    // protected $casts = [
    //     's_status' => 'array'
    // ];
    protected $fillable = ["s_name", "s_surname", "school_no", "s_phone", "s_gsm", "s_email", "s_birthday", "s_tc",
        "file_id", "s_address"];

/*    public function supost()
    {
        return $this->morphMany(UserPost::class, 'contentable');
    }*/
    public function supost()
    {
        return $this->morphToMany(Post::class, 'postable');
    }
    public function schools()
    {
        return $this->belongsToMany(School::class);
    }

    public function clases()
    {
        return $this->belongsToMany(Clases::class, "school_student");
    }

    public function branches()
    {
        return $this->belongsToMany(Branches::class, "school_student");
    }

    public function grades()
    {
        return $this->belongsToMany(Grade::class, "activity_student");
    }

    public function clubs()
    {
        return $this->belongsToMany(SporClub::class, "club_team_branch_student");
    }

    public function discont()
    {
        return $this->hasMany(Discontinuity::class);
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, "club_team_branch_student");
    }

    public function sbranches()
    {
        return $this->belongsToMany(SporClubBranch::class, "club_team_branch_student");
    }

    public function sdetail()
    {
        return $this->hasOne(SDetail::class);
    }

    public function file()
    {
        return $this->belongsTo(Files::class, "file_id", "id");
    }

    public function users()
    {
        return $this->belongsToMany(Users::class, 'student_user')->with("file");
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class);
    }

    public function periods()
    {
        return $this->belongsToMany(Period::class, "activity_student");
    }

    public function sactivities()
    {
        return $this->belongsToMany(Activity::class, "activity_student");
    }

    public static function boot()
    {

        parent::boot();
        static::deleting(function ($student) { // before delete() method call this
            $student->sactivities()->detach();
            $student->clubs()->detach();
            $student->schools()->detach();
            $student->users()->detach();
            APLS::where("student_id", $student->id)
                ->delete();
            $student->file()->delete();
            SCLS::where("student_id", $student->id)
                ->delete();
        });
    }
}
