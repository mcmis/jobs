<?php

namespace MCMIS\Jobs\Alerts;

use MCMIS\Contracts\Foundation\Model\ComplainContract;
use MCMIS\Contracts\Foundation\Model\DepartmentContract;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use MCMIS\Jobs\Job;

class ForwardedComplainMailAlert extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $department, $complaint, $type, $to, $from;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(DepartmentContract $department, ComplainContract $complaint, $to = null, $type = 'forwarded', $from = 'donotreply')
    {
        $this->board = $complaint;
        $this->type = $type;
        $this->from = $from;
        $this->department = $department;
        $this->to = $to;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $department = $this->department;
        $complaint = $this->complaint;
        $contents = app('GetEmailTemplate')->template($this->type)->filter(['complain' => $complaint, 'department' => $department]);
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
