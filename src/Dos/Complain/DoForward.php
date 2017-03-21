<?php
namespace MCMIS\Jobs\Dos\Complain;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MCMIS\Contracts\Foundation\Model\ComplainContract;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\Dos\Complain\Traits\AssignTrait;

class DoForward implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs, AssignTrait;

    public $queue = 'default';

    public function handle(ComplainContract $complaint, $department_id, $operator = false)
    {
        Log::info('Complaint assignment manually to department: '. $department_id);
        if($complaint->hasParent()){
            $this->doAssign($complaint = $complaint->parent->first(), $department_id, $operator);
        }else $this->doAssign($complaint, $department_id, $operator);
        if($complaint->hasChild()) event('complaints.group.assignment', [$complaint->children, $department_id, $operator]);
    }

    public function failed(ComplainContract $complaint, $exception)
    {
        Log::info('Failed to assign manually complain to department event for complain#'.$complaint->complain_no.'. \n Exception:: '.$exception);
    }

}