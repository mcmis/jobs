<?php

namespace MCMIS\Jobs\Subscribers\Notice;


class Subscriber
{

    public function subscribe($events)
    {
        $events->listen(
            'notice.created',
            OnCreated::class
        );

        $events->listen(
            'notice.sent',
            OnForwarded::class
        );
    }

}