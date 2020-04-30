<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Users extends Model
{
    use SoftDeletes;

    public function post()
    {
        return $this->hasMany(Post::class, "user_id", "id");
    }

    public function tomessage()
    {
        return $this->hasMany(SpeacialNotes::class, "to_user_id", "id")->with(['touser']);

    }

    public function frommessage()
    {
        return $this->hasMany(SpeacialNotes::class, "from_user_id", 'id')->with(['fromuser']);

    }

    public function msgbox()
    {
        return $this->belongsToMany(UserSpeacialNotes::class, "user_special_notes");
    }

    public function user_u_types()
    {
        return $this->belongsToMany(UserTypes::class, "user_u_types");
    }

    public function title()
    {
        return $this->belongsTo(Title::class, "utitle_id", "id");
    }

    public function unit()
    {
        return $this->belongsTo(Units::class, "uunits_id", "id");
    }

    public function province()
    {
        return $this->belongsTo(Province::class, "uprovince_id", "id");
    }

    public function proximity()
    {
        return $this->belongsTo(Proximity::class, "uproximities_id", "id");
    }

    public function file()
    {
        return $this->belongsTo(Files::class, "ufile_id", "id");
    }

    public function activities()
    {
        return $this->belongsToMany(Activity::class, "activity_users");
    }

    public function lessons()
    {
        return $this->belongsToMany(Lessons::class, "users_lessons", "users_id", "lesson_id");
    }

    public function userschoollessons()
    {
        return $this->belongsToMany(Lessons::class, "users_lessons", "users_id", "lesson_id");
    }

    public function club()
    {
        return $this->belongsToMany(SporClub::class, "user_spor_club");
    }

    public function schools()
    {
        return $this->belongsToMany(School::class, "users_schools", "users_id", "school_id");
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, "student_user")->with(["schools", "clases", "branches", 'file']);
    }

    public function getapContent()
    {
        return $this
            ->belongsToMany(ActivityProgramContent::class, "activity_program_content_user", "user_id", "ap_content_id")
            ->with(["getProgram", "getLesson"]);
    }

    public function getspContents()
    {
        return $this
            ->belongsToMany(SchoolProgramContent::class, "school_program_content_user", "user_id", "school_program_content_id")
            ->with(["getProgram", "getLesson", 'day']);
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($user) {
            $user->lessons()->detach();
            $user->getspContents()->delete();
            UsersSchools::where("users_id", $user->id)->delete();
            UsersSchoolsClases::where("user_id", $user->id)->delete();
            UsersSchoolsClasesBranches::where("user_id", $user->id)->delete();
            UsersSchoolsLessons::where("user_id", $user->id)->delete();
            UserSporClub::where("users_id", $user->id)->delete();
            UserSporClubTeamBranch::where("user_id", $user->id)->delete();
            UserUTypes::where("users_id", $user->id)->delete();
            ActivityProgramContentUserPivot::where("user_id", $user->id)->delete();
            ActivityUser::where("users_id", $user->id)->delete();
            ActivityUserClases::where("user_id", $user->id)->delete();
            ActivityUserLessons::where("user_id", $user->id)->delete();
            ActivityUserPeriod::where("user_id", $user->id)->delete();
            StudentUserPivot::where("users_id", $user->id)->delete();
            UsersPasswords::where("user_id", $user->id)->delete();
        });
    }

    public function supost()
    {
        return $this->morphToMany(Post::class, 'postable');
    }
    /*    public function supost()
        {
            return $this->morphMany(UserPost::class, 'contentable');
        }*/
    //adem
    public function pactivities()
    {
        return $this->belongsToMany(Activity::class, "activity_user_periods", "user_id");
    }

    public function periods()
    {
        return $this->belongsToMany(Period::class, "activity_user_periods", "user_id");
    }

    public function uschools()
    {
        return $this->belongsToMany(School::class, "users_schools", "user_id", "id");
    }

    public function utypes()
    {
        return $this->belongsToMany(UserTypes::class, "user_u_types");
    }

    public function uclubs()
    {
        return $this->belongsToMany(SporClub::class, "user_spor_club", "users_id");
    }

    //dersi veren öğretmenler
    public function ulactivities()
    {
        return $this->belongsToMany(Activity::class, "activity_user_lessons", "user_id");
    }

    public function ulperiods()
    {
        return $this->belongsToMany(Period::class, "activity_user_lessons", "user_id");
    }

    public function uaplessons()
    {
        return $this->belongsToMany(Lessons::class, "activity_user_lessons", "user_id", "lesson_id");
    }

    /*    public static function boot() {

            parent::boot();

            static::deleting(function($user) { // before delete() method call this
                 $user->ucontents()->delete();
                 UsersLessons::where("user_id", $user->id)->delete();
                //  $user->lessons()->detach();
                 // do the rest of the cleanup...
            });
        }*/

    public function ucontents()
    {
        return $this->belongsToMany
        (ActivityProgramContent::class,
            "activity_program_content_user", "user_id",
            "ap_content_id")
            ->with(["lesson", "grade", 'day', 'hour', 'activityprogram']);
    }

    public function uclubcontents()
    {
        return $this->
        belongsToMany(ClubProgramContent::class,
            "club_program_content_user",
            "user_id",
            "club_content_id")
            ->with(['getProgram', 'hour', 'day']);
    }
}
