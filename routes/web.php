<?php
use App\Mail\LoggedMail;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Models\Admin;
use Illuminate\Support\Facades\Mail;

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

Route::get('/', [Controller::class, 'show']);
Route::get('/email', function () {

    $admins = Admin::all();
    foreach ($admins as $admin) {

        Mail::to($admin->email)->send((new LoggedMail)->with(
            [
                'username' => request('username'),
                'status' => request('status')
            ]
        ));
    }
    return redirect()->route('home')->with([
        'status' => request('status')
    ]);
})->name('mail');
// Route::get('/admin/register', 'Auth\RegisterController@showAdminRegistration');

Auth::routes();

Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/register/admin', [RegisterController::class, 'show'])->name('admin.register');
Route::get('/login/admin', [LoginController::class, 'show'])->name('admin.login');
Route::view('/admin/home', 'admin.index')->middleware('auth:admin');

Route::post('register/admin', [RegisterController::class, 'store']);
Route::post('login/admin', [LoginController::class, 'authenticate']);

