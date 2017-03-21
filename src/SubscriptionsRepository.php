<?php
namespace MCMIS\Jobs;

use Illuminate\Contracts\Events\Dispatcher;
use MCMIS\Contracts\Foundation\Repository;
class SubscriptionsRepository implements Repository
{

    protected $dispatcher;

    protected $subscribers = [
        'MCMIS\Jobs\Subscribers\Complain\Subscriber',
        'MCMIS\Jobs\Subscribers\User\Subscriber',
        'MCMIS\Jobs\Subscribers\Status\Subscriber',
        'MCMIS\Jobs\Subscribers\Notice\Subscriber',
    ];

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function load($repo){
        //
    }

    public function register(){
        foreach ($this->subscribers as $subscriber){
            $this->dispatcher->subscribe($subscriber);
        }
    }

}