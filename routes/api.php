<?php

use App\Http\Controllers\ComplaintController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\StudentController;

// Public routes
Route::post('LoginSuperAdmin', [AuthController::class, 'login']);
Route::post('login', [AuthController::class, 'login']);
Route::post('registerGuest', [AuthController::class, 'registerGuest']);


// Super Admin routes
Route::middleware(['auth:api', 'role:SuperAdmin'])->prefix('super-admin')->group(function () {
    Route::post('register', [AuthController::class, 'register']);

    Route::get('showUserInfo/{id}', [AuthController::class, 'showUserInfo']);

    Route::get('showComplaint/{id}', [ComplaintController::class, 'showComplaint']);

    Route::get('showAllComplaint', [ComplaintController::class, 'showAllComplaint']);

    Route::get('showTeacherComplaints/{teacherId}', [ComplaintController::class, 'showTeacherComplaints']);

    Route::get('showPendingComplaints', [ComplaintController::class, 'showPendingComplaints']);

    Route::get('showSolvedComplaints', [ComplaintController::class, 'showSolvedComplaints']);

    Route::post('checkComplaint/{complaintId}', [ComplaintController::class, 'checkComplaint']);
});


Route::middleware(['auth:api', 'role:Teacher|SuperAdmin'])->prefix('teacher')->group(function () {
    Route::post('addFlashcard', [StaffController::class, 'addFlashCard']);

    Route::post('editFlashcard', [StaffController::class, 'editFlashCard']);

    Route::post('deleteFlashcard', [StaffController::class, 'deleteFlashCard']);

    Route::post('enterBonus', [StaffController::class, 'enterBonus']);

    Route::post('markAttendance', [StaffController::class, 'markAttendance']);

    Route::get('reviewMyCourses', [StaffController::class, 'reviewMyCourses']);

    Route::get('reviewSchedule', [StaffController::class, 'reviewSchedule']);

    Route::get('reviewStudentsNames/{courseId}', [StaffController::class, 'reviewStudentsNames']);

    //Route::post('addTest', [StaffController::class, 'addTest']);

    //Route::post('editTest', [StaffController::class, 'editTest']);

    //Route::post('deleteTest', [StaffController::class, 'deleteTest']);

    Route::post('editComplaint/{complaint}', [ComplaintController::class, 'editComplaint']);

    Route::get('deleteComplaint/{id}', [ComplaintController::class, 'deleteComplaint']);

    Route::get('showTeacherOwnComplaints', [ComplaintController::class, 'showTeacherOwnComplaints']);

    Route::get('showPendingComplaintsTeacher', [ComplaintController::class, 'showPendingComplaintsTeacher']);

    Route::get('showSolvedComplaintsTeacher', [ComplaintController::class, 'showSolvedComplaintsTeacher']);

    Route::post('submitComplaint', [ComplaintController::class, 'submitComplaint']);
});

Route::middleware(['auth:api', 'role:Secretarya|SuperAdmin'])->prefix('secretarya')->group(function () {
    Route::post("enroll", [StaffController::class, "enrollStudent"]);

    Route::post("addCourse", [StaffController::class, "addCourse"]);

    Route::post("editCourse", [StaffController::class,"editCourse"]);

    Route::delete("deleteCourse/{course}", [StaffController::class,"deleteCourse"]);

    Route::get("viewEnrolledStudentsInCourse/{course}", [StaffController::class,"viewEnrolledStudentsInCourse"]);

    Route::get("getAllEnrolledStudents", [StaffController::class,"getAllEnrolledStudents"]);
});

Route::middleware(['auth:api' , 'role:Student|SuperAdmin'])->prefix('student')->group(function() {
    Route::get("viewEnrolledCourses", [StudentController::class,"viewEnrolledCourses"]);

    Route::get("viewMyLessons/{course}", [StudentController::class,"viewMyLessons"]);

    Route::get("viewTeachers", [StudentController::class,"viewTeachers"]);

    Route::post("addNote", [StudentController::class,"addNote"]);

    Route::post("editNote/{noteId}", [StudentController::class,"editNote"]);

    Route::get("deleteNote/{noteId}", [StudentController::class,"deleteNote"]);

    Route::get("viewMyNotes", [StudentController::class,"viewMyNotes"]);

    Route::get("calculateAttendance", [StudentController::class,"calculateAttendance"]);

    Route::get("viewProgress", [StudentController::class,"viewProgress"]);
});

// Authenticated routes (all logged-in users)
Route::middleware(['auth:api'])->group(function () {
    // Logout for all authenticated users
    Route::post('logout', [AuthController::class, 'logout']);
});
