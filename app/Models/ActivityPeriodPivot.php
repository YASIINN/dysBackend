<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Models\ActivityPeriodLessonPivot as APL;
use App\Models\ActivityPeriodLessonStudentPivot as APLS;
use App\Models\ActivityPTypePivot as APPType;
use App\Models\ActivityStudentPivot as APS;
use App\Models\ActivityUserClases as APUC;
use App\Models\ActivityUserLessons as APUL;
use App\Models\ActivityUserPeriod as APU;
use App\Models\Group;


class ActivityPeriodPivot extends Pivot
{
    protected $table = 'activity_period';
    protected $fillable = ["begin", "end"];
    public $incrementing = true;
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }


    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }

      public function group()
    {
        return $this->morphOne(Group::class, 'groupable');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public static function boot()
    {
         parent::boot();

         static::created(function ($actper) {
            $grade = $actper->grade;
            $aName = $actper->activity->aName;
            $aCode = $actper->activity->aCode;
            $pName = $actper->period->pName;
            $pCode = $actper->period->pCode;

            $gName = $grade ? $grade->gName : "";
            $gCode = $grade ? $grade->gCode : "";
            $g = new Group;
            $g->name = $aName . ' ' . $pName . ' ' . $gName;
            $g->code = $aCode . ' ' . $pCode . ' ' . $gCode;
            $actper->group()->save($g);
        });




        static::deleting(function ($actper) {
            //grubu silme
            $actper->group()->delete();

            //yaz okulu 1 hafta dersleri
            APL::where("activity_id", $actper->activity_id)
                ->where("period_id", $actper->period_id)
                ->delete();
            //yaz okulu 1 hafta ilgili dersin öğrencileri
            APLS::where("activity_id", $actper->activity_id)
                ->where("period_id", $actper->period_id)
                ->delete();

            //yaz okulu 1 hafta programları
            APPType::where("activity_id", $actper->activity_id)
            ->where("period_id", $actper->period_id)
            ->delete();


               //yaz okulu 1 hafta öğrencileri
               APS::where("activity_id", $actper->activity_id)
               ->where("period_id", $actper->period_id)
               ->delete();

                 //yaz okulu 1 hafta user sınıfları
                 APUC::where("activity_id", $actper->activity_id)
                 ->where("period_id", $actper->period_id)
                 ->delete();

                     //yaz okulu 1 hafta user dersleri
                     APUL::where("activity_id", $actper->activity_id)
                     ->where("period_id", $actper->period_id)
                     ->delete();

                         //yaz okulu 1 hafta userları
                         APU::where("activity_id", $actper->activity_id)
                         ->where("period_id", $actper->period_id)
                         ->delete();
            
        });
    }

  
}