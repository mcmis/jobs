<?php

namespace MCMIS\Jobs\Subscribers\Notice;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MCMIS\Contracts\Foundation\Model\User;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\Alerts\UserMailAlert;

class OnForwarded implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs;

    public $queue = 'default';

    public function handle($item, User $user)
    {
        $this->dispatch((new UserMailAlert($user, $user, 'notice.alert'))->onQueue('emails')->delay(5));
    }

    public function failed($item, $exception)
    {
        Log::info('Failed to on notice forwarded event for notice#'.$item->subject.'. \n Exception:: '.$exception);
    }

}