<?php

use App\Http\Controllers\API\AcademicClassController;
use App\Http\Controllers\API\AcademicYearController;
use App\Http\Controllers\API\ApiClientController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ClassParticipantController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\MenuAccessController;
use App\Http\Controllers\API\PermissionController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\ServiceClientController;
use App\Http\Controllers\API\ServiceRegistryController;
use App\Http\Controllers\API\StudentController;
use App\Http\Controllers\API\StudyProgramController;
use App\Http\Controllers\API\UserController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh-token', [AuthController::class, 'refreshToken']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/validate-token', [AuthController::class, 'validateToken']);

    Route::middleware('permission:view-users')->apiResource('users', UserController::class)->only(['index']);
    Route::middleware('permission:create-users')->apiResource('users', UserController::class)->only(['store']);
    Route::middleware('permission:update-users')->apiResource('users', UserController::class)->only(['update']);
    Route::middleware('permission:delete-users')->apiResource('users', UserController::class)->only(['destroy']);

    Route::middleware('permission:view-roles')->apiResource('roles', RoleController::class)->only(['index', 'show']);
    Route::middleware('role:super_admin')->apiResource('roles', RoleController::class)->only(['store', 'update', 'destroy']);

    Route::middleware('permission:view-permissions')->apiResource('permissions', PermissionController::class)->only(['index', 'show']);
    Route::middleware('role:super_admin')->apiResource('permissions', PermissionController::class)->only(['store', 'update', 'destroy']);

    Route::middleware('role:super_admin')->group(function () {
        Route::apiResource('service-clients', ServiceClientController::class);
        Route::apiResource('menu-access', MenuAccessController::class);
        Route::apiResource('service-registry', ServiceRegistryController::class);
        Route::apiResource('api-clients', ApiClientController::class);

        Route::apiResource('academic-years', AcademicYearController::class);
        Route::apiResource('study-programs', StudyProgramController::class);
        Route::apiResource('classes', AcademicClassController::class);
        Route::apiResource('courses', CourseController::class);
        Route::apiResource('students', StudentController::class);
        Route::apiResource('class-participants', ClassParticipantController::class);
        Route::middleware('permission:view-pegawai')->apiResource('pegawai', \App\Http\Controllers\API\PegawaiController::class)->only(['index','show']);
        Route::middleware('permission:create-pegawai')->apiResource('pegawai', \App\Http\Controllers\API\PegawaiController::class)->only(['store']);
        Route::middleware('permission:update-pegawai')->apiResource('pegawai', \App\Http\Controllers\API\PegawaiController::class)->only(['update']);
        Route::middleware('permission:delete-pegawai')->apiResource('pegawai', \App\Http\Controllers\API\PegawaiController::class)->only(['destroy']);
    });
});
