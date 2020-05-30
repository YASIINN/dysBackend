<?php

namespace App\Observers;

use App\Models\SchoolStudentPivot;
use App\Models\Group;
use App\Models\SchoolClasesPivot as SC;
use App\Models\SchoolClasesBranchesPivot as SCB;

class SSObserver
{
    /**
     * Handle the school student pivot "created" event.
     *
     * @param  \App\SchoolStudentPivot  $schoolStudentPivot
     * @return void
     */
    public function created(SchoolStudentPivot $ssp)
    {   
        //okul grubu
        $gs = Group::where("groupable_id", $ssp->school_id)->first();
        $gs->students()->attach([$ssp->student_id]);

        //okul sınıf grubu işlemleri
        $sc = SC::where(
            ["school_id"=>$ssp->school_id, "clases_id"=>$ssp->clases_id]
        )->first();
        $gsc = Group::where("groupable_id", $sc->scid)->first();
        $gsc->students()->attach([$ssp->student_id]);

        //okul sınıf sube grubu

        //okul sınıf grubu işlemleri
        $scb = SCB::where(
            ["school_id"=>$ssp->school_id, "clases_id"=>$ssp->clases_id, "branches_id"=>$ssp->branches_id]
        )->first();
        $gscb = Group::where("groupable_id", $scb->scbid)->first();
        $gscb->students()->attach([$ssp->student_id]);

    }

    /**
     * Handle the school student pivot "updated" event.
     *
     * @param  \App\SchoolStudentPivot  $schoolStudentPivot
     * @return void
     */
    public function updated(SchoolStudentPivot $schoolStudentPivot)
    {
        //
    }

    /**
     * Handle the school student pivot "deleted" event.
     *
     * @param  \App\SchoolStudentPivot  $schoolStudentPivot
     * @return void
     */
    public function deleted(SchoolStudentPivot $schoolStudentPivot)
    {
        //
    }

    /**
     * Handle the school student pivot "restored" event.
     *
     * @param  \App\SchoolStudentPivot  $schoolStudentPivot
     * @return void
     */
    public function restored(SchoolStudentPivot $schoolStudentPivot)
    {
        //
    }

    /**
     * Handle the school student pivot "force deleted" event.
     *
     * @param  \App\SchoolStudentPivot  $schoolStudentPivot
     * @return void
     */
    public function forceDeleted(SchoolStudentPivot $schoolStudentPivot)
    {
        //
    }
}
