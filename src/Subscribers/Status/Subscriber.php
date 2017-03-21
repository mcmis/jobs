<?php

namespace MCMIS\Jobs\Subscribers\Status;


class Subscriber
{

    public function subscribe($events)
    {
        $events->listen(
            'complaint.status.pending',
            OnStatePending::class
        );

        $events->listen(
            'complaint.status.inProcess',
            OnStateInProcess::class
        );

        $events->listen(
            'complaint.status.forwarded',
            OnStateForward::class
        );
    }
}
