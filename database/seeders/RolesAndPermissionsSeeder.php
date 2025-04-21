<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all possible permissions in the application
        $allApplicationPermissions = [
            //user
            'register',
            'login',
            'logout',
            'showUserInfo',
            'registerGuest',
            'LoginSuperAdmin',
            'addFlashcard',
            'editFlashcard',
            'deleteFlashcard',
            'enrollStudent',
            'addCourse',
            'editCourse',
            'deleteCourse',
            'viewEnrolledStudentsInCourse',
            'getAllEnrolledStudents',
            'reviewMyCourses',
            'submitComplaint',
            'editComplaint',
            'showSolvedComplaintsTeacher',
            'showPendingComplaintsTeacher',
            'showComplaintTeacher',
            'deleteComplaint',
            'checkComplaint',
            'showSolvedComplaints',
            'showPendingComplaints',
            'showAllComplaint',
            'showTeacherComplaints',
            'showComplaint',
            'reviewSchedule',
            'reviewStudentsNames',
            'enterBonus',
            'markAttendance',
            'viewEnrolledCourses',
            'viewMyLessons',
            'viewTeachers',
            'viewTeacher',
            'addNote',
            'editNote',
            'deleteNote',
            'viewMyNotes',
            'viewProgress',
            'viewAvailableCourses',
        ];

        // Create all permissions
        foreach ($allApplicationPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'api'
            ]);
        }

        // Define roles with their permissions (SuperAdmin will get all permissions)
        $roles = [
            'SuperAdmin' => $allApplicationPermissions, // SuperAdmin gets ALL permissions
            'Secretarya' => [
               'registerGuest',
                'login',
                'logout',
                'enrollStudent',
                'addCourse',
                'editCourse',
                'deleteCourse',
                'viewEnrolledStudentsInCourse',
                'getAllEnrolledStudents',
            ],
            'Teacher' => [
                'registerGuest',
                'login',
                'logout',
                'addFlashcard',
                'editFlashcard',
                'deleteFlashcard',
                'reviewMyCourses',
                'submitComplaint',
                'editComplaint',
                'showSolvedComplaintsTeacher',
                'showPendingComplaintsTeacher',
                'showComplaintTeacher',
                'deleteComplaint',
                'reviewSchedule',
                'reviewStudentsNames',
                'enterBonus',
                'markAttendance',
            ],
            'Logistic' => [
                'registerGuest',
                'login',
                'logout',
            ],
            'Student' => [
              'registerGuest',
              'login',
              'logout',
              'viewEnrolledCourses',
              'viewMyLessons',
              'viewTeachers',
              'viewTeacher',
              'addNote',
              'editNote',
              'deleteNote',
              'viewMyNotes',
              'viewProgress',
              'viewAvailableCourses',
            ],
            'Guest' => [
                'registerGuest',
                'login',
                'logout',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate([
                'name' => $roleName,
                'guard_name' => 'api'
            ]);
            $role->syncPermissions($rolePermissions);
        }

          // Create SuperAdmin User
          $superAdminUser = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password' => Hash::make('password'),
        ]);

        $superAdminUser->assignRole('SuperAdmin');

        $superAdminUser->givePermissionTo($roles['SuperAdmin']);

    }
}
