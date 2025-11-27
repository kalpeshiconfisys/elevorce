<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\admin;


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/admin', function () {
    //  return view("welcome");
    if (Session::has('admin_id')) {
        return redirect(url('admin/dashboard'));
    }
    return redirect(url('admin/login'));
});


Route::get('/', function () {
    //  return view("welcome");
    if (Session::has('admin_id')) {
        return redirect(url('admin/dashboard'));
    }
    return redirect(url('admin/login'));
});


/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('Admin.view.index');
// })->name('home');



Route::prefix('admin')->group(function () {

    Route::get('/login', [admin\Auth\AuthController::class, 'login'])->name('login');
    Route::post('login', [admin\Auth\AuthController::class, 'loginSubmit'])->name('admin.login.submit');

    Route::get('/register', [admin\Auth\AuthController::class, 'register']);
    Route::post('register', [admin\Auth\AuthController::class, 'registerSubmit'])->name('admin.register.submit');

    Route::get('/logout', [admin\Auth\AuthController::class, 'logout']);

    Route::middleware(['auth:admin'])->group(function () {
        Route::get('dashboard', [admin\DashboardController::class, 'index'])->name('home');
        Route::get('contact-us', [admin\DashboardController::class, 'contactUsList']);

        Route::get('profile', [admin\ProfileController::class, 'index'])->name('profile');
        Route::get('welcome', [admin\ProfileController::class, 'index'])->name('welcome');
        Route::resource('company', admin\Company\CompanyController::class);
        // Route::get('company', [admin\Company\CompanyController::class,'index'])->name('company.index');
        // Route::get('company/create', [admin\Company\CompanyController::class,'create'])->name('company.create');
        // Route::post('company', [admin\Company\CompanyController::class,'store'])->name('company.store');
        // Route::get('company/{hash}', [admin\Company\CompanyController::class,'show'])->name('company.show');
        // Route::get('company/{hash}/edit', [admin\Company\CompanyController::class,'edit'])->name('company.edit');
        // Route::put('company/{hash}', [admin\Company\CompanyController::class,'update'])->name('company.update');
        // Route::delete('company/{hash}', [admin\Company\CompanyController::class,'destroy'])->name('company.destroy');
        // Route::get('company-dashboard/{hash}', [admin\Company\CompanyController::class, 'companyDashboard'])->name('company.dashboard');
        // Route::get('visa-expiry-list/{hash}', [admin\Company\CompanyController::class, 'visaExpiryList'])->name('company.visaExpiryList');
        Route::get('site/{id}', [admin\Site\SiteController::class, 'index']);
        Route::get('site/create/{id}', [admin\Site\SiteController::class, 'create']);
        Route::get('site/edit/{id}/{employer_id}', [admin\Site\SiteController::class, 'edit']);
        Route::get('site/show/{id}/{employer_id}', [admin\Site\SiteController::class, 'show'])->name('site.show');
        Route::resource('site', admin\Site\SiteController::class)->except(['index', 'create', 'edit', 'show']);
        Route::get('employee-list/{id}', [admin\Employee\EmployeeController::class, 'index']);
        Route::put('employee-update-shift/{id}', [admin\Employee\EmployeeController::class, 'updateShift']);
        Route::get('site-manager-list/{id}', [admin\SiteManager\SiteManagerController::class, 'index']);
        Route::get('company-dashboard/{id}', [admin\Company\CompanyController::class, 'companyDashboard']);
        Route::get('visa-expiry-list/{id}', [admin\Company\CompanyController::class, 'visaExpiryList']);

        Route::get('site-manager/show/{id}/{employer_id}', [admin\SiteManager\SiteManagerController::class, 'show']);
        Route::get('site-manager/create/{id}', [admin\SiteManager\SiteManagerController::class, 'create']);
        Route::post('site-manager/add', [admin\SiteManager\SiteManagerController::class, 'store']);
        Route::get('site-manager/edit/{id}/{employer_id}', [admin\SiteManager\SiteManagerController::class, 'edit']);
        Route::post('site-manager/update/{id}',       [admin\SiteManager\SiteManagerController::class, 'update']);
        Route::delete('site-manager/delete/{id}/{site_id}', [admin\SiteManager\SiteManagerController::class, 'destroy']);

        Route::prefix('leave-type')->group(function () {
            Route::get('list/{employer_id}', [admin\LeaveType\LeaveTypeController::class, 'index']);
            Route::get('show/{id}/{employer_id}',[admin\LeaveType\LeaveTypeController::class, 'show']);
            Route::get('create/{id}',        [admin\LeaveType\LeaveTypeController::class, 'create']);
            Route::post('add',               [admin\LeaveType\LeaveTypeController::class, 'store']);
            Route::get('edit/{id}/{employer_id}', [admin\LeaveType\LeaveTypeController::class, 'edit']);
            Route::post('update/{id}',       [admin\LeaveType\LeaveTypeController::class, 'update']);
            Route::delete('delete/{id}/{employer_id}', [admin\LeaveType\LeaveTypeController::class, 'destroy']);
        });

        Route::prefix('shift')->group(function () {
            Route::get('list/{employer_id}', [admin\Shift\ShiftController::class, 'index']);
            Route::get('show/{id}/{employer_id}',          [admin\Shift\ShiftController::class, 'show']);
            Route::get('create/{id}',        [admin\Shift\ShiftController::class, 'create']);
            Route::post('add',               [admin\Shift\ShiftController::class, 'store']);
            Route::get('edit/{id}/{employer_id}', [admin\Shift\ShiftController::class, 'edit']);
            Route::post('update/{id}',       [admin\Shift\ShiftController::class, 'update']);
            Route::delete('delete/{id}/{employer_id}',        [admin\Shift\ShiftController::class, 'destroy']);
        });
    });
});
