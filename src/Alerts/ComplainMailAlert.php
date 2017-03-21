<?php

namespace MCMIS\Jobs\Alerts;

use MCMIS\Contracts\Foundation\Model\Complain;
use MCMIS\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ComplainMailAlert extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $complaint, $type, $from, $to;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Complain $complaint, $to = null, $type = 'new.complain', $from = 'support')
    {
        $this->complaint = $complaint;
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
        $complaint = $this->complaint;
        $user = $complaint->user;
        $contents = app('GetEmailTemplate')->template($this->type)->filter(['complain' => $complaint, 'user' => $user]);
        $subject = $contents->get('subject');
        $from = $this->from;
        $to = $this->to;
        Mail::send('templates.email.main', ['body' => $contents->get('body')], function ($m) use ($subject, $to, $from){
            $m->from(config('csys.emailAddress.sender.'. $from .'.email'), config('csys.emailAddress.sender.'. $from .'.name'));
            $m->replyTo(config('csys.emailAddress.sender.'. $from .'.email'), config('csys.emailAddress.sender.'. $from .'.name'));
            Log::info('ComplainEmailJob to:'.$to);
            $m->to($to->email, ($to->name)? $to->name : $to->first_name);
            $m->subject($subject);
        });

        Log::info('Email sent to:'. $to . ', subject: '. $subject);
    }

    public function failed(Complain $complaint, $exception)
    {
        Log::info('Failed to email for complain#'.$complaint->complain_no.'. \n Exception:: '.$exception);
    }
}
