<?php

namespace MCMIS\Jobs\Subscribers\Complain;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MCMIS\Contracts\Foundation\Model\ComplainContract;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\Alerts\ComplainMailAlert;

class OnAssignmentFailed implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs;

    public $queue = 'default';

    public function handle(ComplainContract $complaint)
    {
        Log::info('Complaint assignment failed:'.$complaint);
        sys('model.complain.unassigned')->create([
            'complaint_id' => $complaint->id,
        ]);
        if($complaint->user->id == $complaint->creator->first()->id){
            $operators = sys('model.user.role')->with('users')->where('name', '=', 'operator')->first()->users;
            foreach($operators as $operator) //send email to operators about failed assignment to department
                if($employee = $operator->employee->first()) { //check if operator user is employee or not
                    Log::info('Complain user and creator are same and current is employee:' . $employee);
                    $this->dispatch((new ComplainMailAlert($complaint, $employee, 'forward.failed', 'donotreply'))->onQueue('alerts'));
                }
        }else{
            //send email to complaint creator operator about failed assignment to department
            if($employee = $complaint->creator->first()->employee->first()) { //check if operator user is employee or not
                Log::info('Complain user and creator are not same and creator is employee:' . $employee);
                $this->dispatch((new ComplainMailAlert($complaint, $employee, 'forward.failed', 'donotreply'))->onQueue('alerts'));
            }
        }
    }

    public function failed(ComplainContract $complaint, $exception)
    {
        Log::info('Failed to failed assigned complain to department event for complain#'.$complaint->complain_no.'. \n Exception:: '.$exception);
    }

}