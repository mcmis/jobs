<?php

namespace MCMIS\Jobs\Subscribers\Complain;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MCMIS\Contracts\Foundation\Model\Complain;
use MCMIS\Contracts\Foundation\Model\Department;
use Illuminate\Support\Facades\Log;

class OnAssigned implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs;

    public $queue = 'default';

    public function handle(Complain $complaint, Department $department, $assignment)
    {
        Log::info('OnAssigned Complaint, assigned to department:'.$department);
        event('complaint.status.forwarded', $complaint, ($assignment->assigner_id ? $assignment->assignee->users->first() : null));
        sys('model.complain.unassigned')->where('complaint_id', '=', $complaint->id)->delete();
    }

    public function failed(Complain $complaint, $exception)
    {
        Log::info('Failed to assigned complain to department event for complain#'.$complaint->complain_no.'. \n Exception:: '.$exception);
    }

}