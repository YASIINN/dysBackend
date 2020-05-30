<?php

namespace App\Observers;

use App\Models\ActivityStudentPivot;
use App\Models\Group;
use App\Models\ActivityPeriodPivot as AP;

class ASObserver
{
    /**
     * Handle the activity student pivot "created" event.
     *
     * @param  \App\ActivityStudentPivot  $activityStudentPivot
     * @return void
     */
    public function created(ActivityStudentPivot $asp)
    {
            //okul grubu
            $gs = Group::where("groupable_id", $asp->activity_id)->first();
            $gs->students()->attach([$asp->student_id]);
    
            //okul sınıf grubu işlemleri
            $sc = AP::where(
                ["activity_id"=>$asp->activity_id, "period_id"=>$asp->period_id,  "grade_id"=>null])
                ->first();
            $gsc = Group::where("groupable_id", $sc->id)->first();
            $gsc->students()->attach([$asp->student_id]);
    
            //okul sınıf sube grubu
    
            //okul sınıf grubu işlemleri
            $scb = AP::where(
                ["activity_id"=>$asp->activity_id, "period_id"=>$asp->period_id, "grade_id"=>$asp->grade_id]
            )->first();
            $gscb = Group::where("groupable_id", $scb->id)->first();
            $gscb->students()->attach([$asp->student_id]);
        
    }

    /**
     * Handle the activity student pivot "updated" event.
     *
     * @param  \App\ActivityStudentPivot  $activityStudentPivot
     * @return void
     */
    public function updated(ActivityStudentPivot $activityStudentPivot)
    {
        //
    }

    /**
     * Handle the activity student pivot "deleted" event.
     *
     * @param  \App\ActivityStudentPivot  $activityStudentPivot
     * @return void
     */
    public function deleted(ActivityStudentPivot $activityStudentPivot)
    {
        //
    }

    /**
     * Handle the activity student pivot "restored" event.
     *
     * @param  \App\ActivityStudentPivot  $activityStudentPivot
     * @return void
     */
    public function restored(ActivityStudentPivot $activityStudentPivot)
    {
        //
    }

    /**
     * Handle the activity student pivot "force deleted" event.
     *
     * @param  \App\ActivityStudentPivot  $activityStudentPivot
     * @return void
     */
    public function forceDeleted(ActivityStudentPivot $activityStudentPivot)
    {
        //
    }
}
