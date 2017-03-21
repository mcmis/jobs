<?php

namespace MCMIS\Jobs\Alerts;

use MCMIS\Contracts\Foundation\Model\UserContract;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MCMIS\Jobs\Job;

class UserMailAlert extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $user, $type, $to, $from;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserContract $user, $to = null, $type = 'new.user', $from = 'support')
    {
        $this->user = $user;
        $this->type = $type;
        $this->to = $to;
        $this->from = $from;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->user;
        $contents = app('GetEmailTemplate')->template($this->type)->filter(['user' => $user]);
        $subject = $contents->get('subject');
        $from = $this->from;
        $to = $this->to;
        Mail::send('templates.email.main', ['body' => $contents->get('body')], function ($m) use ($subject, $to, $from){
            $m->from(config('csys.emailAddress.sender.'. $from .'.email'), config('csys.emailAddress.sender.'. $from .'.name'));
            $m->replyTo(config('csys.emailAddress.sender.'. $from .'.email'), config('csys.emailAddress.sender.'. $from .'.name'));
            $m->to($to->email, $to->name);
            $m->subject($subject);
        });

        Log::info('Email sent to:'. $to . ', subject: '. $subject);
    }
}
