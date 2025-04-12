<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\StaffController;

// Public routes
Route::post('LoginSuperAdmin', [AuthController::class, 'login']);
Route::post('login', [AuthController::class, 'login']);
Route::post('registerGuest', [AuthController::class, 'registerGuest']);

// Super Admin routes
Route::middleware(['auth:api', 'role:SuperAdmin'])->prefix('super-admin')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::get('showUserInfo/{id}', [AuthController::class, 'showUserInfo']);
});


Route::middleware(['auth:api', 'role:Teacher'])->prefix('teacher')->group(function () {
    Route::post('addFlashcard', [StaffController::class, 'addFlashCard']);
});

Route::middleware(['auth:api', 'role:Secretarya'])->prefix('secretarya')->group(function () {
    Route::post("enroll", [StaffController::class, "enrollStudent"]);

    Route::post("addCourse", [StaffController::class, "addCourse"]);

    Route::post("editCourse", [StaffController::class,"editCourse"]);

    Route::get("viewEnrolledStudentsInCourse/{courseId}", [StaffController::class,"viewEnrolledStudentsInCourse"]);

    Route::get("getAllEnrolledStudents", [StaffController::class,"getAllEnrolledStudents"]);
});

// Authenticated routes (all logged-in users)
Route::middleware(['auth:api'])->group(function () {
    // Logout for all authenticated users
    Route::post('logout', [AuthController::class, 'logout']);

});
