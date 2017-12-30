<?php

namespace MCMIS\Jobs\Subscribers\Company;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use MCMIS\Contracts\Foundation\Model\Employee;
use Illuminate\Support\Facades\Log;
use MCMIS\Jobs\Alerts\EmployeeMailAlert;

class OnEmployeeRegistered implements ShouldQueue
{

    use InteractsWithQueue, DispatchesJobs;

    public $queue = 'default';

    public function handle(Employee $employee)
    {
        Log::info('Email: New employee registered:'. $employee);
        $this->dispatch((new EmployeeMailAlert($employee, $employee, 'new.employee'))->onQueue('emails'));
    }

    public function failed(Employee $employee, $exception)
    {
        Log::info('Failed to on registered event for employee#'.$employee->email.'. \n Exception:: '.$exception);
    }

}