<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Web\AuthWebController;
use App\Http\Controllers\Web\UserManagementController;
use App\Http\Controllers\Web\MasterData\AcademicYearController;
use App\Http\Controllers\Web\MasterData\StudyProgramController;
use App\Http\Controllers\Web\MasterData\AcademicClassController;
use App\Http\Controllers\Web\MasterData\CourseController;
use App\Http\Controllers\Web\MasterData\StudentController;
use App\Http\Controllers\Web\MasterData\PegawaiController;
use App\Http\Controllers\Web\MasterData\ClassParticipantController;

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('users.index')
        : redirect()->route('login');
});

Route::redirect('/docs', '/docs/api');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthWebController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthWebController::class, 'login'])->name('login.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthWebController::class, 'logout'])->name('logout');

    Route::middleware('web.role:super_admin,admin_akademik')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    });

    Route::middleware('web.role:super_admin')->group(function () {
        Route::get('/users/create', [UserManagementController::class, 'create'])->name('users.create');
        Route::post('/users', [UserManagementController::class, 'store'])->name('users.store');
        Route::get('/users/{user}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserManagementController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');

        Route::prefix('master-data')->name('master-data.')->group(function () {
            Route::resource('academic-years', AcademicYearController::class)->except(['show']);
            Route::resource('study-programs', StudyProgramController::class)->except(['show']);
            Route::resource('classes', AcademicClassController::class)->except(['show']);
            Route::resource('courses', CourseController::class)->except(['show']);
            Route::resource('students', StudentController::class)->except(['show']);
            // pegawai management (create/update/delete) remains for super_admin only
            Route::resource('pegawai', PegawaiController::class)->except(['show', 'index']);
            Route::resource('class-participants', ClassParticipantController::class)->except(['show']);
        });
    });

    // allow both super_admin and admin_akademik to view pegawai list
    Route::middleware('web.role:super_admin,admin_akademik')->group(function () {
        Route::prefix('master-data')->name('master-data.')->group(function () {
            Route::get('pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
            Route::get('pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');
        });
    });
});
