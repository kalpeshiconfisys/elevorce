<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\API;


Route::middleware('auth:sanctum')->get('/user', fn(Request $request) => $request->user());

Route::get('test', [API\SiteManager\AttendanceManagementController::class, 'testing']);
Route::get('py-employee-null-emedding-list/{id}', [API\SiteManager\EmployeeController::class, 'pyEmployeeList']);
Route::get('py-all-employee-list/{id}', [API\SiteManager\EmployeeController::class, 'pyAllEmployeeList']);
Route::post('py-update-embedding-employee', [API\SiteManager\EmployeeController::class, 'pyUpdateEmbeddingEmployee']);

Route::get('py-sitemanager-list', [API\Employer\SiteManagerController::class, 'pySiteManagerList']);
Route::get('manage-app-constants', [API\Employee\AuthController::class, 'manageAppConstants']);

Route::post('check-in-employee',  [API\SiteManager\AttendanceManagementController::class, 'checkInEmployee']);
Route::post('contact-us',  [API\FrontendController::class, 'contactUs']);
Route::post('create-payment', [API\PaymentController::class, 'createPayment']);
Route::post('mark-payment-success', [API\PaymentController::class, 'markPaymentSuccess']);
Route::get('country-list', [API\SiteManager\AuthController::class, 'countryList']);

Route::prefix('employer')->group(function () {

    // Public Auth
    Route::post('register', [API\Employer\AuthController::class, 'register']);
    Route::post('login',    [API\Employer\AuthController::class, 'login']);
    Route::post('forgot-password', [API\Employer\AuthController::class, 'forgotPassword']);
    Route::post('phone-verification', [API\Employer\AuthController::class, 'phoneVerification']);

    Route::group(['middleware' => ['assign.guard:employer', 'jwt.verify:employer']], function () {

        // Profile
        Route::get('logout',         [API\Employer\AuthController::class, 'logout']);
        Route::get('delete',         [API\Employer\AuthController::class, 'deleteAccount']);
        Route::get('get-profile',    [API\Employer\AuthController::class, 'getProfile']);
        Route::post('edit-profile',  [API\Employer\AuthController::class, 'editProfile']);
        Route::post('change-password', [API\Employer\AuthController::class, 'changeEmployerPassword']);

        // Sites
        Route::prefix('site')->group(function () {
            Route::post('add',         [API\Employer\SiteController::class, 'store']);
            Route::get('list',         [API\Employer\SiteController::class, 'index']);
            Route::get('show/{id}',    [API\Employer\SiteController::class, 'show']);
            Route::post('update/{id}', [API\Employer\SiteController::class, 'update']);
            Route::get('delete/{id}',  [API\Employer\SiteController::class, 'destroy']);
        });

        // Shifts
        Route::prefix('shift')->group(function () {
            Route::post('add',         [API\Employer\ShiftController::class, 'store']);
            Route::get('list',         [API\Employer\ShiftController::class, 'index']);
            Route::get('show/{id}',    [API\Employer\ShiftController::class, 'show']);
            Route::post('update/{id}', [API\Employer\ShiftController::class, 'update']);
            Route::get('delete/{id}',  [API\Employer\ShiftController::class, 'destroy']);
        });

        // Site Managers
        Route::prefix('site-manager')->group(function () {
            Route::post('add',         [API\Employer\SiteManagerController::class, 'store']);
            Route::get('list',         [API\Employer\SiteManagerController::class, 'index']);
            Route::get('show/{id}',    [API\Employer\SiteManagerController::class, 'show']);
            Route::post('update/{id}', [API\Employer\SiteManagerController::class, 'update']);
            Route::get('delete/{id}',  [API\Employer\SiteManagerController::class, 'deleteSiteManager']);
        });

        // Employees
        Route::prefix('employees')->group(function () {
            Route::post('add',         [API\SiteManager\EmployeeController::class, 'store']);
            Route::get('list/{id}',    [API\SiteManager\EmployeeController::class, 'index']);
            Route::get('show/{id}',    [API\SiteManager\EmployeeController::class, 'show']);
            Route::post('update/{id}', [API\SiteManager\EmployeeController::class, 'update']);
            Route::get('delete/{id}',  [API\SiteManager\EmployeeController::class, 'deleteEmployee']);
        });

        // Holidays
        Route::prefix('holiday')->group(function () {
            Route::post('add',         [API\Employer\HolidayController::class, 'store']);
            Route::get('list',         [API\Employer\HolidayController::class, 'index']);
            Route::get('show/{id}',    [API\Employer\HolidayController::class, 'show']);
            Route::post('update/{id}', [API\Employer\HolidayController::class, 'update']);
            Route::get('delete/{id}',  [API\Employer\HolidayController::class, 'destroy']);
        });

        // Leave Types
        Route::prefix('leave-type')->group(function () {
            Route::post('add',         [API\Employer\LeaveTypeController::class, 'store']);
            Route::get('list',         [API\Employer\LeaveTypeController::class, 'index']);
            Route::get('show/{id}',    [API\Employer\LeaveTypeController::class, 'show']);
            Route::post('update/{id}', [API\Employer\LeaveTypeController::class, 'update']);
            Route::get('delete/{id}',  [API\Employer\LeaveTypeController::class, 'destroy']);
        });


        Route::get('task-list',         [API\Employer\TaskManagementController::class, 'index']);
        Route::post('add-task',         [API\Employer\TaskManagementController::class, 'addTask']);
        Route::get('task-details/{id}', [API\Employer\TaskManagementController::class, 'taskDetails']);
        Route::post('employee-promotion',  [API\Employer\EmployeePromotionController::class, 'employeePromotion']);
        Route::get('get-announcement',  [API\Employer\EmployerAnnouncementController::class, 'getEmployerAnnouncement']);
        Route::post('add-announcement', [API\Employer\EmployerAnnouncementController::class, 'addAnnouncement']);
        Route::post('get-sitemanager-attendance-management',   [API\Employer\AttendanceManagementController::class, 'getAttendanceList']);
        Route::get('get-employer-dashboard-count',   [API\Employer\AttendanceManagementController::class, 'getSiteManagerTodayAttendanceCount']);

        Route::get('employee-request-correction-list', [API\Employer\RequestCorrectionController::class, 'requestCorrectionList']);
        Route::post('update-request-correction-status', [API\Employer\RequestCorrectionController::class, 'updateRequestCorrectionStatus']);

        // promotion employee
        Route::get('get-all-site-employee-list',   [API\Employer\EmployeePromotionController::class, 'getEmployeeForPromotion']);
        Route::get('employee-visa-expiry-list',   [API\Employer\AttendanceManagementController::class, 'employeeVisaExpiryList']);
        Route::get('get-notifications',   [API\Employee\NotificationController::class, 'getEmployerNotifications']);
        Route::post('read-notification',   [API\Employee\NotificationController::class, 'readNotification']);
    });
});


/*
|--------------------------------------------------------------------------
| SITE MANAGER ROUTES
|--------------------------------------------------------------------------
| Site Managers can manage employees, attendance, leaves, and corrections.
*/


Route::prefix('site-manager')->group(function () {

    Route::post('login', [API\SiteManager\AuthController::class, 'login']);

    Route::post('forgot-password', [API\SiteManager\AuthController::class, 'forgotPassword']);
    Route::post('phone-verification', [API\SiteManager\AuthController::class, 'phoneVerification']);

    Route::group(['middleware' => ['assign.guard:site-manager', 'jwt.verify:site-manager']], function () {

        // Profile
        Route::get('logout',                     [API\SiteManager\AuthController::class, 'logout']);
        Route::get('delete',                     [API\SiteManager\AuthController::class, 'deleteAccount']);
        Route::get('get-site-manager-profile',   [API\SiteManager\AuthController::class, 'getSiteManagerProfile']);
        Route::post('edit-profile', [API\SiteManager\AuthController::class, 'editSiteManagerProfile']);
        Route::post('change-password', [API\SiteManager\AuthController::class, 'changeSiteManagerPassword']);

        // Employees
        Route::prefix('employees')->group(function () {
            Route::post('add',         [API\SiteManager\EmployeeController::class, 'store']);
            Route::get('list/{id}',    [API\SiteManager\EmployeeController::class, 'index']);
            Route::get('show/{id}',    [API\SiteManager\EmployeeController::class, 'show']);
            Route::post('update/{id}', [API\SiteManager\EmployeeController::class, 'update']);
            Route::get('delete/{id}',  [API\SiteManager\EmployeeController::class, 'deleteEmployee']);
        });

        // Attendance & Leaves
        Route::get('get-leave-request',             [API\SiteManager\LeaveManagementController::class, 'getLeaveRequest']);
        Route::get('get-emergency-leave-request',             [API\SiteManager\LeaveManagementController::class, 'getEmergencyLeaveRequest']);
        Route::post('leave-status-update',          [API\SiteManager\LeaveManagementController::class, 'leaveStatusUpdate']);

        Route::post('check-out',                    [API\SiteManager\AttendanceManagementController::class, 'checkOut']);
        Route::post('get-attendance-list-employee', [API\SiteManager\AttendanceManagementController::class, 'getAttendanceListEmployee']);

        // Shift Replacements & Corrections
        Route::post('shift-replace',                   [API\SiteManager\ShiftReplacementController::class, 'shiftReplace']);
        Route::get('shift-replace-list',               [API\SiteManager\ShiftReplacementController::class, 'shiftReplacementList']);

        Route::post('add-request-correction',     [API\SiteManager\RequestCorrectionController::class, 'addRequestCorrection']);
        Route::get('employee-request-correction-list', [API\SiteManager\RequestCorrectionController::class, 'requestCorrectionList']);
        Route::post('update-request-correction-status', [API\SiteManager\RequestCorrectionController::class, 'updateRequestCorrectionStatus']);
        Route::get('request-correction-type',     [API\Employee\RequestCorrectionController::class, 'requestCorrectionType']);

        Route::get('get-site-manager-dashboard-count', [API\SiteManager\AttendanceManagementController::class, 'getSiteManagerCurrentMonthAttendanceCount']);

        Route::post('check-in-check-out-sitemanager', [API\SiteManager\AttendanceManagementController::class, 'checkInCheckOutSiteManager']);
        Route::post('get-site-manager-attendance-management',   [API\SiteManager\AttendanceManagementController::class, 'getSiteManagerAttendanceManagement']);
        Route::get('holiday/list',         [API\Employer\HolidayController::class, 'getHolidayList']);

        Route::get('shift/list', [API\Employer\ShiftController::class, 'getShiftListSiteManager']);
        Route::post('get-employee-non-leave-list', [API\SiteManager\ShiftReplacementController::class, 'getEmployeeWithoutLeave']);
        Route::get('get-notifications',   [API\Employee\NotificationController::class, 'getSiteManagerNotifications']);
        Route::post('read-notification',   [API\Employee\NotificationController::class, 'readNotification']);
        Route::get('get-announcement',  [API\Employer\EmployerAnnouncementController::class, 'getSiteManagerAnnouncement']);
    });
});

/*
|--------------------------------------------------------------------------
| EMPLOYEE ROUTES
|--------------------------------------------------------------------------
| Employees can manage their own profile, tasks, leaves, and corrections.
*/

Route::prefix('employee')->group(function () {

    Route::post('login', [API\Employee\AuthController::class, 'login']);
    Route::post('forgot-password', [API\Employee\AuthController::class, 'forgotPassword']);
    Route::post('phone-verification', [API\Employee\AuthController::class, 'phoneVerification']);

    Route::group(['middleware' => ['assign.guard:employee', 'jwt.verify:employee']], function () {
        // Profile
        Route::get('logout',                 [API\Employee\AuthController::class, 'logout']);
        Route::get('delete',                 [API\Employee\AuthController::class, 'deleteAccount']);
        Route::get('get-employee-profile',   [API\Employee\AuthController::class, 'getEmployeeProfile']);
        Route::post('edit-profile', [API\Employee\AuthController::class, 'editEmployeeProfile']);
        Route::post('change-password',       [API\Employee\AuthController::class, 'changeEmployeePassword']);

        // Tasks
        Route::get('task-list',             [API\Employee\TaskManagementController::class, 'index']);
        Route::post('task-status-update',   [API\Employee\TaskManagementController::class, 'updateTaskStatus']);

        // Leaves
        Route::get('get-leaves',            [API\Employee\LeaveManagementController::class, 'getLeaves']);
        Route::post('add-leave',            [API\Employee\LeaveManagementController::class, 'addLeave']);
        Route::get('cancel-leave/{id}',     [API\Employee\LeaveManagementController::class, 'cancelLeave']);
        Route::get('leave-type-sub-type',   [API\Employee\LeaveManagementController::class, 'leaveType']);
        Route::get('leave-used-count',     [API\Employee\LeaveManagementController::class, 'leaveUsed']);


        Route::post('emergency-leave-accept', [API\Employee\LeaveManagementController::class, 'emergencyLeaveAccept']);

        Route::post('get-employee-attendance-management',     [API\Employee\AttendanceManagementController::class, 'getAttendanceManagement']);
        Route::post('get-current-month-attendance-count',     [API\Employee\AttendanceManagementController::class, 'getCurrentMonthAttendanceCount']);

        // Shift Replacements & Corrections
        Route::get('shift-replace-list', [API\Employee\ShiftReplacementController::class, 'employeeShiftReplacementList']);
        Route::post('add-request-correction',     [API\Employee\RequestCorrectionController::class, 'addRequestCorrection']);
        Route::get('request-correction-type',     [API\Employee\RequestCorrectionController::class, 'requestCorrectionType']);

        Route::get('holiday/list',         [API\Employer\HolidayController::class, 'getHolidayList']);
        Route::get('leave-sub-type',         [API\Employee\LeaveManagementController::class, 'leaveSubType']);

        Route::get('get-notifications',   [API\Employee\NotificationController::class, 'getNotifications']);
        Route::post('read-notification',   [API\Employee\NotificationController::class, 'readNotification']);
        Route::get('shift/list', [API\Employer\ShiftController::class, 'getShiftList']);
        Route::get('get-announcement',  [API\Employer\EmployerAnnouncementController::class, 'getEmployeeAnnouncement']);
    });
});
