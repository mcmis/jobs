<?php

namespace MCMIS\Jobs\Subscribers\User;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MCMIS\Contracts\Foundation\Model\UserContract;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\Alerts\UserMailAlert;

class OnPasswordChanged implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs;

    public $queue = 'default';

    public function handle(UserContract $user)
    {
        Log::info('Email: User password changed:'. $user);
        $this->dispatch((new UserMailAlert($user, $user, 'password.changed'))->onQueue('emails'));
    }

    public function failed(UserContract $user, $exception)
    {
        Log::info('Failed to on password changed event for user#'.$user->email.'. \n Exception:: '.$exception);
    }

}