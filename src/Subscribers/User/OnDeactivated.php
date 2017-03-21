<?php

namespace MCMIS\Jobs\Subscribers\User;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MCMIS\Contracts\Foundation\Model\User;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\Alerts\UserMailAlert; //TODO: Future use to send email to user

class OnDeactivated implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs;

    public $queue = 'default';

    public function handle(User $user)
    {
        Log::info('Email: User deactivate:'. $user);
    }

    public function failed(User $user, $exception)
    {
        Log::info('Failed to on user deactivated event for user#'.$user->email.'. \n Exception:: '.$exception);
    }

}