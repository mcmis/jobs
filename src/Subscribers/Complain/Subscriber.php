<?php

namespace MCMIS\Jobs\Subscribers\Complain;

use MCMIS\Jobs\Dos\Complain\DoForward;
use MCMIS\Jobs\Dos\Complain\DoForwardGrouped;

class Subscriber
{

    public function subscribe($events)
    {
        $events->listen(
            'complain.registered',
            OnRegistered::class
        );

        $events->listen(
            'complaint.assigned',
            OnAssigned::class
        );

        $events->listen(
            'complaint.assignment.failed',
            OnAssignmentFailed::class
        );

        $events->listen(
            'complaint.assign.manually',
            DoForward::class
        );

        $events->listen(
            'complaints.group.assignment',
            DoForwardGrouped::class
        );

        $events->listen(
            'complaint.assigned.fieldworker',
            OnAssignedFieldworker::class
        );

        $events->listen(
            'complaint.comment',
            OnComment::class
        );
    }
}
