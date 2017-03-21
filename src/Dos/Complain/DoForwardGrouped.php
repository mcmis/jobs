<?php
namespace MCMIS\Jobs\Dos\Complain;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\Dos\Complain\Traits\AssignTrait;

class DoForwardGrouped implements ShouldQueue
{


    use InteractsWithQueue, DispatchesJobs, AssignTrait;

    public $queue = 'default';

    public function handle($complaints, $department_id = false, $requested_by = false)
    {
        Log::info('Grouped Complaints assignment to department: '. $department_id);
        foreach($complaints as $complaint){
            $this->doAssign($complaint, $department_id, $requested_by);
        }
    }

    public function failed($complaints, $exception)
    {
        Log::info('Failed to assign manually complain to department event for complain#'.$complaints.'. \n Exception:: '.$exception);
    }

}