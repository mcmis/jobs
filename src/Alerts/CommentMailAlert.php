<?php

namespace MCMIS\Jobs\Alerts;

use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MCMIS\Jobs\Job;

class CommentMailAlert extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $comment, $type, $from, $to;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ComplainCommentContract $comment, $to = null, $type = 'update', $from = 'support')
    {
        $this->comment = $comment;
        $this->type = $type;
        $this->from = $from;
        $this->to = $to;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $comment = $this->comment;
        $complaint = $comment->complaint;
        $user = $complaint->user; /** TODO: Employee also needed to add. */
        $contents = app('GetEmailTemplate')->template($this->type)->filter(['complain' => $complaint, 'comment' => $comment, 'user' => $user]);
        $subject = $contents->get('subject');
        $from = $this->from;
        $to = $this->to;
        Mail::send('templates.email.main', ['body' => $contents->get('body')], function ($m) use ($subject, $to, $from){
            $m->from(config('csys.emailAddress.sender.'. $from .'.email'), config('csys.emailAddress.sender.'. $from .'.name'));
            $m->replyTo(config('csys.emailAddress.sender.'. $from .'.email'), config('csys.emailAddress.sender.'. $from .'.name'));
            Log::info('CommentEmailJob to:'.$to);
            $m->to($to->email, isset($to->name)? $to->name : $to->first_name);
            $m->subject($subject);
        });

        Log::info('Email sent to:'. $to . ', subject: '. $subject);
    }
}
