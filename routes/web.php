<?php

declare(strict_types=1);

use App\Http\Controllers\ServerController;
use Illuminate\Support\Facades\Route;

Route::static('terms-of-service', 'app.terms-of-service');

Route::view('/', 'app.home')->name('home');
Route::view('/support', 'app.support')->name('support');
Route::view('/dashboard', 'app.dashboard')->name('dashboard');
Route::get('/server/{server:id}', ServerController::class)->name('server');

Route::view('/user/settings/teams', 'app.teams')->name('user.teams');
// Route::view('/user/notifications', 'app.user.notifications')->name('user.notifications');

Route::view('/servers/import', 'app.servers-import')->middleware(['permission:import server-configuration'])->name('servers.import');
