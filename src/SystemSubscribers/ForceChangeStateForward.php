<?php

namespace MCMIS\Jobs\SystemSubscribers;

use MCMIS\Contracts\Foundation\Model\ComplainContract;
use MCMIS\Contracts\Foundation\Model\UserContract;
use Illuminate\Foundation\Auth\User;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\Job;

class ForceChangeStateForward extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $complaint, $user;

    public function __construct(ComplainContract $complaint, UserContract $user = null)
    {
        $this->complaint = $complaint;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $last_status = $this->complaint->status;
        $expected_completed_on = $this->complaint->expected_completed_on;
        $reschedule_on = $this->complaint->reschedule_on;
        if($this->complaint->state->short_code == 'validate' || $this->complaint->state->short_code == 'pending'){
            if($this->complaint->update(['status' => $status = sys('model.status')->where('short_code', '=', 'forwarded.department')->first()->id])){
                //send email to user and staff
                Log::info('Complaint#'. $this->complaint->complain_no .' status changed to forwarded to department from '. $last_status . '.');
                $comment = $this->complaint->comments()->create([
                    'msg' => 'System successfully forwarded complaint to concerned department on request.',
                    'user_id' => ($this->user ? $this->user->id : 1),
                    'status' => $status,
                    'last_status' => $last_status,
                    'expected_completed_on' => $expected_completed_on,
                    'last_expected_completed_on' => $expected_completed_on,
                    'reschedule_on' => $reschedule_on,
                    'last_reschedule_on' => $reschedule_on,
                ]);
                event('complaint.comment', $comment);
            }else{
                Log::info('Complaint#'. $this->complaint->complain_no .' failed to change status to forwarded to department.');
            }
        }else{
            Log::info('Complaint#'. $this->complaint->complain_no .' status is not validate or pending, nothing to do.');
        }
    }
}