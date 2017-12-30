<?php

namespace MCMIS\Jobs\Subscribers\Company;


class Subscriber
{

    public function subscribe($events)
    {
        $events->listen(
            'employee.registered',
            OnEmployeeRegistered::class
        );
    }
}
