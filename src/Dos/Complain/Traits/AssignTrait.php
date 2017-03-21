<?php

namespace MCMIS\Jobs\Dos\Complain\Traits;

use MCMIS\Contracts\Foundation\Model\Complain;
use MCMIS\Jobs\Alerts\ForwardedComplainMailAlert;

trait AssignTrait
{
    public function doAssign(Complain $complaint, $department_id = false, $requested_by = false){
        $category = $complaint->child_category_id ? $complaint->childCategory : $complaint->category;

        $assignment_failed = false;

        $department = $category->department;
        if($department_id !== false) $department = sys('model.company.department')->findOrFail($department_id);
        if($department && sys('model.complain.assignment')->where('complaint_id', '=', $complaint->id)->whereIn('department_id', [$department->id])->count() < 1){

            $supervisors = $department->employees()
                ->whereHas('designation', function($query){
                    $query->where('title', '=', 'Supervisor');
                })->get();
            $assigned = false;
            foreach($supervisors as $supervisor){
                $assigned = $complaint->assignments()->create([
                    'department_id' => $department->id,
                    'employee_id' => $supervisor->id,
                    'by_system' => ($requested_by == false ? true : false),
                    'assigner_id' => ($requested_by == false ? null : $requested_by->id) //employee id
                ]);
                $this->dispatch((new ForwardedComplainMailAlert($department, $complaint, $supervisor, 'forwarded', 'donotreply'))->onQueue('alerts'));
            }

            if($assigned) event('complaint.assigned', [$complaint, $department, $assigned]);
            else event('complaint.assignment.failed', $complaint);
        }else event('complaint.assignment.failed', $complaint);
    }
}