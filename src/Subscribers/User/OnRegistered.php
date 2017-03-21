<?php

namespace MCMIS\Jobs\Subscribers\User;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MCMIS\Contracts\Foundation\Model\User;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\Alerts\UserMailAlert;

class OnRegistered implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs;

    public $queue = 'default';

    public function handle(User $user)
    {
        Log::info('Email: New user registered:'. $user);
        $this->dispatch((new UserMailAlert($user, $user, 'new.user'))->onQueue('emails'));
    }

    public function failed(User $user, $exception)
    {
        Log::info('Failed to on registered event for user#'.$user->email.'. \n Exception:: '.$exception);
    }

}