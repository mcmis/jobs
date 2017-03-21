<?php

namespace MCMIS\Jobs;

use Illuminate\Contracts\Foundation\Application;

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