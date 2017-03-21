<?php

namespace MCMIS\Jobs\Subscribers\Complain;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MCMIS\Contracts\Foundation\Model\Complain;
use MCMIS\Contracts\Foundation\Model\ComplainAssignment;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\Alerts\ComplainMailAlert;
use MCMIS\Jobs\Alerts\ForwardedComplainMailAlert;


class OnAssignedFieldworker implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs;

    public $queue = 'default';

    public function handle(Complain $complaint, ComplainAssignment $assignment)
    {
        Log::info('Complaint assigned to employee: '. $assignment->employee);
        $this->dispatch((new ComplainMailAlert($complaint, $complaint->user, 'update.status.assigned'))->onQueue('emails'));
        $this->dispatch((new ForwardedComplainMailAlert($assignment->department, $complaint, $assignment->employee, 'update.department', 'donotreply'))->onQueue('alerts'));
    }

    public function failed(Complain $complaint, $exception)
    {
        Log::info('Failed to assign complain to fieldworker event for complain#'.$complaint->complain_no.'. \n Exception:: '.$exception);
    }

}