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

class ActivityPeriodPivot extends Pivot
{
    protected $table = 'activity_period';
    protected $fillable = ["begin", "end"];

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

    public static function boot()
    {

        parent::boot();
        static::deleting(function ($actper) {
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

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}