<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;

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

Route::get('/', function () {
    return view('welcome');
});

// for Customer Registration Form
Route::GET('register-form', [UserController::class, 'getRegister'])->name('customer.form');

//for Employee Registration Form
Route::GET('employee-register', [UserController::class, 'getEmployee'])->name('employee.form');

//customer index
Route::GET('customer', [CustomerController::class, 'index'])->name('customer.index');

//employee index
Route::GET('employee', [EmployeeController::class, 'index'])->name('employee.index');
// Sign up Forms
Route::GET('signup-forms', [UserController::class, 'getForm'])->name('user.form');




