<?php

namespace App\Observers;

use App\Models\ClubTeamBranchStudentPivot;
use App\Models\Group;
use App\Models\SporClub;
use App\Models\Team;
use App\Models\SporClubTeamBranch as CTB;  //club team branch

class CTBSObserver
{
    /**
     * Handle the club team branch student pivot "created" event.
     *
     * @param  \App\ClubTeamBranchStudentPivot  $clubTeamBranchStudentPivot
     * @return void
     */
    public function created(ClubTeamBranchStudentPivot $ctbs)
    {
                //spor kulubu grubu
                $gs = Group::where("groupable_id", $ctbs->spor_club_id)->first();
                $gs->students()->attach([$ctbs->student_id]);
        
                //takım grubu işlemleri
                $gsc = Group::where("groupable_id", $ctbs->team_id)->first();
                $gsc->students()->attach([$ctbs->student_id]);
        
        
                //kulup takım sube grubu işlemleri
                $scb = CTB::where(
                    ["spor_club_id"=>$ctbs->spor_club_id, "team_id"=>$ctbs->team_id, "sbranch_id"=>$ctbs->spor_club_branch_id]
                )->first();
                $gscb = Group::where("groupable_id", $scb->sctbid)->first();
                $gscb->students()->attach([$ctbs->student_id]);
                $a = "adem";
    }

    /**
     * Handle the club team branch student pivot "updated" event.
     *
     * @param  \App\ClubTeamBranchStudentPivot  $clubTeamBranchStudentPivot
     * @return void
     */
    public function updated(ClubTeamBranchStudentPivot $clubTeamBranchStudentPivot)
    {
        //
    }

    /**
     * Handle the club team branch student pivot "deleted" event.
     *
     * @param  \App\ClubTeamBranchStudentPivot  $clubTeamBranchStudentPivot
     * @return void
     */
    public function deleted(ClubTeamBranchStudentPivot $clubTeamBranchStudentPivot)
    {
        //
    }

    /**
     * Handle the club team branch student pivot "restored" event.
     *
     * @param  \App\ClubTeamBranchStudentPivot  $clubTeamBranchStudentPivot
     * @return void
     */
    public function restored(ClubTeamBranchStudentPivot $clubTeamBranchStudentPivot)
    {
        //
    }

    /**
     * Handle the club team branch student pivot "force deleted" event.
     *
     * @param  \App\ClubTeamBranchStudentPivot  $clubTeamBranchStudentPivot
     * @return void
     */
    public function forceDeleted(ClubTeamBranchStudentPivot $clubTeamBranchStudentPivot)
    {
        //
    }
}
