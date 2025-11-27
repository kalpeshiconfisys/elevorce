<?php

namespace App\Http\Controllers\API\Employee;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{

    public function getNotifications(Request $request)
    {
        $employee = auth()->user();
        $limit = $request->limit;
        $offset = $request->offset;
        $getNotifications = Notification::where('employee_id', $employee->id)->orderBy('id', 'DESC');
        if(!empty($request->limit)){
            $getNotifications = $getNotifications->skip($offset * $limit)->take($limit);
        }
        $getNotifications = $getNotifications->get();

        return $this->success(true, 'Notification get successfully.', $getNotifications);
    }


    public function getSiteManagerNotifications(Request $request)
    {
        $employee = auth()->user();
        $limit = $request->limit;
        $offset = $request->offset;
        $getNotifications = Notification::where('site_manager_id', $employee->id)->orderBy('id', 'DESC');
        if(!empty($request->limit)){
            $getNotifications = $getNotifications->skip($offset * $limit)->take($limit);
        }
        $getNotifications = $getNotifications->get();
        return $this->success(true, 'Notification get successfully.', $getNotifications);
    }


    public function getEmployerNotifications(Request $request)
    {
        $employee = auth()->user();
        $limit = $request->limit;
        $offset = $request->offset;
        $getNotifications = Notification::where('employer_id', $employee->id)->orderBy('id', 'DESC');
        if(!empty($request->limit)){
            $getNotifications = $getNotifications->skip($offset * $limit)->take($limit);
        }
        $getNotifications = $getNotifications->get();
        return $this->success(true, 'Notification get successfully.', $getNotifications);
    }

    public function readNotification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|exists:notifications,id',
        ]);

        if ($validator->fails()) {
            return $this->error($validator->errors()->first());
        }
        $notification = Notification::find($request->notification_id);
        $notification->is_read = 1;
        $notification->save();
        return $this->success(true, 'Notification read successfully.', $notification);
    }
}
