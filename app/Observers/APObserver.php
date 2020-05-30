<?php

namespace App\Observers;

use App\Models\ActivityPeriodPivot;
use App\Models\Group;
class APObserver
{
    /**
     * Handle the activity period pivot "created" event.
     *
     * @param  \App\ActivityPeriodPivot  $activityPeriodPivot
     * @return void
     */
    public function created(ActivityPeriodPivot $actper)
    {
    }

    /**
     * Handle the activity period pivot "updated" event.
     *
     * @param  \App\ActivityPeriodPivot  $activityPeriodPivot
     * @return void
     */
    public function updated(ActivityPeriodPivot $activityPeriodPivot)
    {
        //
    }

    /**
     * Handle the activity period pivot "deleted" event.
     *
     * @param  \App\ActivityPeriodPivot  $activityPeriodPivot
     * @return void
     */
    public function deleted(ActivityPeriodPivot $activityPeriodPivot)
    {
        //
    }

    /**
     * Handle the activity period pivot "restored" event.
     *
     * @param  \App\ActivityPeriodPivot  $activityPeriodPivot
     * @return void
     */
    public function restored(ActivityPeriodPivot $activityPeriodPivot)
    {
        //
    }

    /**
     * Handle the activity period pivot "force deleted" event.
     *
     * @param  \App\ActivityPeriodPivot  $activityPeriodPivot
     * @return void
     */
    public function forceDeleted(ActivityPeriodPivot $activityPeriodPivot)
    {
        //
    }
}
