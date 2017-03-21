<?php

namespace MCMIS\Jobs\Subscribers\Complain;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MCMIS\Contracts\Foundation\Model\Complain;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\Alerts\ComplainMailAlert;

class OnRegistered implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs;

    public $queue = 'default';

    public function handle(Complain $complaint)
    {
        Log::info('On complain registered, triggered on behalf of complain#'.$complaint->complain_no);
        $this->dispatch((new ComplainMailAlert($complaint, $complaint->user))->onQueue('emails'));
    }

    public function failed(Complain $complaint, $exception)
    {
        Log::info('Failed to complete event for complain#'.$complaint->complain_no.'. \n Exception:: '.$exception);
    }

}