<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MembersController;

Route::get('/', function () {
    return view('welcome');
});
Route::resource('members', MembersController::class);