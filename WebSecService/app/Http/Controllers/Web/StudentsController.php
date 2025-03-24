<?php
namespace App\Http\Controllers\Web;

use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Validation\Rules\Password;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Artisan;

use App\Http\Controllers\Controller;
use App\Models\User;

class StudentsController extends Controller {

    use ValidatesRequests;

    /**
     * List only students.
     */
    public function list(Request $request) {
        // Ensure the logged-in user has permission to view students.
        if (!auth()->user()->hasPermissionTo('show_users')) {
            abort(401);
        }

        // Query users with the 'Student' role.
        $query = User::select('*')
            ->whereHas('roles', function($q) {
                $q->where('name', 'Student');
            });

        // Apply an optional search filter.
        $query->when($request->keywords, fn($q) => $q->where("name", "like", "%{$request->keywords}%"));

        $students = $query->get();

        // Return the students list view.
        return view('students.list', compact('students'));
    }

    /**
     * Show the student registration form.
     */


    /**
     * Show the student login form.
     */


    /**
     * Display a student profile.
     */
    public function profile(Request $request, User $student = null) {
        $student = $student ?? auth()->user();
        if (auth()->id() != $student->id) {
            if (!auth()->user()->hasPermissionTo('show_users')) {
                abort(401);
            }
        }

        $permissions = [];
        foreach ($student->permissions as $permission) {
            $permissions[] = $permission;
        }
        foreach ($student->roles as $role) {
            foreach ($role->permissions as $permission) {
                $permissions[] = $permission;
            }
        }

        return view('students.profile', compact('student', 'permissions'));
    }

    /**
     * Show the edit form for a student.
     */
    public function edit(Request $request, User $student = null) {
        $student = $student ?? auth()->user();
        if (auth()->id() != $student?->id) {
            if (!auth()->user()->hasPermissionTo('edit_students')) {
                abort(401);
            }
        }

        // Gather available roles.
        $roles = [];
        foreach (Role::all() as $role) {
            $role->taken = $student->hasRole($role->name);
            $roles[] = $role;
        }

        // Gather available permissions.
        $permissions = [];
        $directPermissionsIds = $student->permissions()->pluck('id')->toArray();
        foreach (Permission::all() as $permission) {
            $permission->taken = in_array($permission->id, $directPermissionsIds);
            $permissions[] = $permission;
        }

        return view('students.edit', compact('student', 'roles', 'permissions'));
    }

    /**
     * Save changes to a student.
     */
    public function save(Request $request, User $student) {
        if (auth()->id() != $student->id) {
            if (!auth()->user()->hasPermissionTo('show_users')) {
                abort(401);
            }
        }

        $student->name = $request->name;
        $student->save();

        // Allow role/permission changes if the current user has administrative rights for students.
        if (auth()->user()->hasPermissionTo('admin_students')) {
            $student->syncRoles($request->roles);
            $student->syncPermissions($request->permissions);
            Artisan::call('cache:clear');
        }

        return redirect(route('students_profile', ['student' => $student->id]));
    }

    /**
     * Delete a student.
     */
    public function delete(Request $request, User $student) {
        if (!auth()->user()->hasPermissionTo('delete_students')) {
            abort(401);
        }

        // $student->delete();
        return redirect()->route('students');
    }

    /**
     * Show the form to edit a student's password.
     */
    public function editPassword(Request $request, User $student = null) {
        $student = $student ?? auth()->user();
        if (auth()->id() != $student?->id) {
            if (!auth()->user()->hasPermissionTo('edit_students')) {
                abort(401);
            }
        }

        return view('students.edit_password', compact('student'));
    }

    /**
     * Save a new password for a student.
     */

}
