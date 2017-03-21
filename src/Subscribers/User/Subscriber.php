<?php

namespace MCMIS\Jobs\Subscribers\User;


class Subscriber
{
    public function subscribe($events)
    {
        $events->listen(
            'user.registered',
            OnRegistered::class
        );

        $events->listen(
            'user.profile.updated',
            OnProfileUpdated::class
        );

        $events->listen(
            'user.password.changed',
            OnPasswordChanged::class
        );

        $events->listen(
            'user.deactivated',
            OnDeactivated::class
        );

        $events->listen(
            'user.activated',
            OnActivated::class
        );
    }
}