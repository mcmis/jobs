<?php

namespace MCMIS\Jobs\Subscribers\Notice;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class OnCreated implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs;

    public $queue = 'default';

    public function handle($item)
    {
        Log::info('User Notice: New notice submitted:'. $item);
        $users = sys('model.user')->whereDoesntHave('notices', function($q) use ($item){
            $q->where('user_notice_id', '=', $item->id);
        })->paginate(1000);

        $user_notice_receiver_failed = false;

        foreach ($users->items() as $user) {
            if(sys('model.user.notice')->create([
                'user_notice_id' => $item->id,
                'user_id' => $user->id,
            ])) event('notice.sent', [$item, $user]);
            else $user_notice_receiver_failed = true;
        }

        if($users->currentPage() < $users->lastPage() || $user_notice_receiver_failed){
            event('notice.created', $item);
        }
    }

    public function failed($item, $exception)
    {
        Log::info('Failed to on notice created event for notice#'.$item->subject.'. \n Exception:: '.$exception);
    }

}