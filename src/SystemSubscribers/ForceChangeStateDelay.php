<?php

namespace MCMIS\Jobs\SystemSubscribers;

use MCMIS\Contracts\Foundation\Model\ComplainContract;
use MCMIS\Contracts\Foundation\Model\UserContract;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\Job;

class ForceChangeStateDelay extends Job implements ShouldQueue
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
        if($this->complaint->state->short_code == 'in.process'){
            if($this->complaint->update(['status' => $status = sys('model.status')->where('short_code', '=', 'staff.delayed')->first()->id])){
                //send email to user and staff
                Log::info('Complaint#'. $this->complaint->complain_no .' status changed to delay from in-process.');
                $comment = $this->complaint->comments()->create([
                    'msg' => 'System changed status on behalf of administrator.',
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
                Log::info('Complaint#'. $this->complaint->complain_no .' failed to change status to delay.');
            }
        }else{
            Log::info('Complaint#'. $this->complaint->complain_no .' status is not in process, nothing to do.');
        }
    }
}
