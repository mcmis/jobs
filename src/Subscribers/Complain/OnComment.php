<?php

namespace MCMIS\Jobs\Subscribers\Complain;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MCMIS\Contracts\Foundation\Model\ComplainCommentContract;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\Alerts\CommentMailAlert;

class OnComment implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs;

    public $queue = 'default';

    public function handle(ComplainCommentContract $comment)
    {
        Log::info('OnComment triggered on behalf of complain comment#'.$comment->serial .' on complaint#'. $comment->complaint->complain_no);
        $comment_type = 'update';
        if($comment->status != $comment->last_status){
            switch($comment->state->short_code){
                case 'validate':
                    $comment_type .= '.status.validate';
                    break;
                case 'pending':
                    //event('complaint.status.pending', $comment->complaint);
                    $comment_type .= '.status.pending';
                    break;
                case 'discard':
                    $comment_type .= '.status.cancelled';
                    break;
                case 'forwarded.department':
                    $comment_type .= '.status.forwarded';
                    break;
                case 'assigned.staff':
                    $comment_type .= '.status.assigned';
                    break;
                case 'in.process':
                    $comment_type .= '.status.inprocess';
                    break;
                case 'reschedule':
                    $comment_type .= '.status.reschedule';
                    break;
                case 'staff.attended':
                    $comment_type .= '.status.attended';
                    break;
                case 'staff.delayed':
                    $comment_type .= '.status.delayed';
                    break;
                case 'resolved':
                    $comment_type .= '.status.resolved';
                    break;
            }
        }
        if($comment->user_id != $comment->complaint->user_id)
            $this->dispatch((new CommentMailAlert($comment, $comment->complaint->user, $comment_type)));

        foreach($comment->complaint->assignments as $assignment){
            if(!in_array($comment->user_id, $assignment->employee->users->lists('id')->toArray()))
                $this->dispatch((new CommentMailAlert($comment, $assignment->employee, 'update.department')));
        }
    }

    public function failed(ComplainCommentContract $comment, $exception)
    {
        Log::info('Failed to comment event for complain#'.$comment->complaint->complain_no.'. \n Exception:: '.$exception);
    }

}