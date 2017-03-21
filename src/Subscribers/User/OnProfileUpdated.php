<?php

namespace MCMIS\Jobs\Subscribers\User;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MCMIS\Contracts\Foundation\Model\User;
use Illuminate\Support\Facades\Log;

class OnProfileUpdated implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs;

    public $queue = 'default';

    public function handle(User $user)
    {
        Log::info('Email: User changed profile:'. $user);
        $this->dispatch((new UserMailAlert($user, $user, 'profile.changed'))->onQueue('emails'));
    }

    public function failed(User $user, $exception)
    {
        Log::info('Failed to on profile updated event for user#'.$user->email.'. \n Exception:: '.$exception);
    }

}