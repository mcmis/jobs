<?php

namespace MCMIS\Jobs\Subscribers\Status;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MCMIS\Contracts\Foundation\Model\Complain;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\SystemSubscribers\ForceChangeStateDelay;

class OnStateInProcess implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs;

    public $queue = 'default';

    public function handle(Complain $complaint)
    {
        Log::info('OnComplaintInProcess triggered on behalf of complain#'.$complaint->complain_no);
        $this->dispatch((new ForceChangeStateDelay($complaint))->delay(Carbon::now()->addDay(5)));
    }

    public function failed(Complain $complaint, $exception)
    {
        Log::info('Failed to change status to pending event for complain#'.$complaint->complain_no.'. \n Exception:: '.$exception);
    }

}