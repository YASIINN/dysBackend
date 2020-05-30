<?php

namespace App\Observers;

use App\Models\SchoolClasesBranchesPivot;

class SCBObserver
{
    /**
     * Handle the school clases branches pivot "created" event.
     *
     * @param  \App\SchoolClasesBranchesPivot  $schoolClasesBranchesPivot
     * @return void
     */
    public function created(SchoolClasesBranchesPivot $schoolClasesBranchesPivot)
    {
        $a = "selam";
    }

    /**
     * Handle the school clases branches pivot "updated" event.
     *
     * @param  \App\SchoolClasesBranchesPivot  $schoolClasesBranchesPivot
     * @return void
     */
    public function updated(SchoolClasesBranchesPivot $schoolClasesBranchesPivot)
    {
        //
    }

    /**
     * Handle the school clases branches pivot "deleted" event.
     *
     * @param  \App\SchoolClasesBranchesPivot  $schoolClasesBranchesPivot
     * @return void
     */
    public function deleted(SchoolClasesBranchesPivot $schoolClasesBranchesPivot)
    {
        $b="selam";
    }

    /**
     * Handle the school clases branches pivot "restored" event.
     *
     * @param  \App\SchoolClasesBranchesPivot  $schoolClasesBranchesPivot
     * @return void
     */
    public function restored(SchoolClasesBranchesPivot $schoolClasesBranchesPivot)
    {
        //
    }

    /**
     * Handle the school clases branches pivot "force deleted" event.
     *
     * @param  \App\SchoolClasesBranchesPivot  $schoolClasesBranchesPivot
     * @return void
     */
    public function forceDeleted(SchoolClasesBranchesPivot $schoolClasesBranchesPivot)
    {
        //
    }
}
