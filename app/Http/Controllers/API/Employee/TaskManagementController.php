<?php

namespace App\Http\Controllers\API\Employee;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class TaskManagementController extends Controller
{

    public function index(Request $request)
    {
        $employeeId = auth()->user()->id;
        $limit = $request->limit;
        $offset = $request->offset;
        $query = Task::with('employee')->where('employee_id', $employeeId)->orderBy('created_at', 'desc');
        if ($request->has('status') && $request->status !== null) {
            $query->where('status', $request->status);
        }
        if(!empty($request->limit)){
            $query->skip($offset * $limit)->take($limit);
        }
         
        $tasks = $query->get();
        return $this->success(true, 'Task list retrieved successfully.', $tasks);
    }

    public function updateTaskStatus(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'task_id' => 'required',
            'status'  => 'required|in:pending,inprogress,complete,overdue',
        ]);
        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $task = Task::where('id', $request->task_id)
            ->where('employee_id', auth()->user()->id)
            ->first();
        if (!$task) {
            return $this->success(false, 'Task not found or not assigned to you.', 404);
        }

        $task->status = $request->status;
        $task->start_date = \Carbon\Carbon::now()->format('Y-m-d');
        $task->save();

        if ($task) {
            $employer = User::find($task->employer_id);

            if ($employer && !empty($employer->device_token)) {

                $notifications = [
                    'device_token' => $employer->device_token,
                    'device_type'  => $employer->device_type,
                    'title'        => 'Task Status Updated',
                    'message'      =>  auth()->user()->name . " has updated the task to '" . $request->status . "'.",
                    'type'         => 'update-task',
                ];
                $this->sendPushNotification($notifications['device_token'], $notifications['device_type'], $notifications['title'], $notifications['message'], $notifications['type']);

                Notification::create([
                    'employer_id' => $employer->id,
                    'employee_id' => null,
                    'type'        => $notifications['type'],
                    'message'     => $notifications['title'],
                    'description' => $notifications['message'],
                ]);
            }
        }

        return $this->success(true, 'Task status updated successfully.', $task);
    }
}
