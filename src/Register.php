<?php

namespace MCMIS\Workflow;

use Illuminate\Contracts\Foundation\Application;
use MCMIS\Jobs\SubscriptionsRepository;

class Register
{

    /**
     * Bootstrap script
     *
     * @param Application $app
     * @return void
     */
    public function bootstrap(Application $app){
        (new SubscriptionsRepository(sys('Illuminate\Contracts\Events\Dispatcher')))->register();
    }

}