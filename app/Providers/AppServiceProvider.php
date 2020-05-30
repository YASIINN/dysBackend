<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\SchoolClasesBranchesPivot;
use App\Models\ActivityPeriodPivot;
use App\Models\SchoolStudentPivot;
use App\Models\ClubTeamBranchStudentPivot;
use App\Models\ActivityStudentPivot;
use App\Observers\SCBObserver;
use App\Observers\APObserver;
use App\Observers\SSObserver;
use App\Observers\CTBSObserver;
use App\Observers\ASObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
          SchoolStudentPivot::observe(SSObserver::class);
          ClubTeamBranchStudentPivot::observe(CTBSObserver::class);
          ActivityStudentPivot::observe(ASObserver::class);
        // SchoolClasesBranchesPivot::observe(SCBObserver::class);
        // ActivityPeriodPivot::observe(APObserver::class);
    }
}
