<?php
namespace App\Http\Controllers\Web;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\URL;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\TemporaryPasswordMail;
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

class UsersController extends Controller {

	use ValidatesRequests;
    public function showLoginLinkForm()
    {
        return view('users.send-login-link');
    }

    // Send the login link to the user's email
    public function sendLoginLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->first();

        // Generate a unique token for the user
        $token = Str::random(60); // You can customize the token length
        $encryptedToken = Crypt::encryptString($token); // Encrypt the token

        // You can optionally store the token in the database (optional) for expiry checks

        // Create a login URL with the token
        $loginUrl = URL::to('/loginn') . '?token=' . $encryptedToken . '&email=' . urlencode($request->email);

        // Send an email with the login URL
        // Send an email with the login URL
        Mail::send([], [], function ($message) use ($user, $loginUrl) {
            $message->to($user->email)
                    ->subject('Login Link')
                    ->html("Click the following link to log in: <a href=\"$loginUrl\">Login</a>");
        });


        return back()->with('status', 'We have emailed you a login link!');
    }

    // Handle the login request using the login link (token validation)
    public function loginWithLink(Request $request)
    {
        if ($request->has('token') && $request->has('email')) {
            $token = $request->token;
            $email = $request->email;

            try {
                // Decrypt the token to validate it
                $decryptedToken = Crypt::decryptString($token);

                // Find the user with the provided email
                $user = User::where('email', $email)->first();

                if ($user) {
                    // Log the user in directly
                    Auth::login($user);

                    // Redirect to the home page or dashboard
                    return redirect('/');
                }

            } catch (\Exception $e) {
                // Token decryption failed or user not found
                return redirect()->route('login')->withErrors('Invalid or expired login link.');
            }
        }

        return redirect()->route('login')->withErrors('Missing or invalid parameters.');
    }

    public function list(Request $request) {
        if (!auth()->user()->hasPermissionTo('show_users')) {
            abort(401);
        }
    
        $query = User::select('*');
    
        if (!auth()->user()->hasPermissionTo('admin_users')) {
            $query->whereHas('roles', function ($q) {
                $q->where('name', 'Customer');
            });
        }
        $query->when($request->keywords, function ($q) use ($request) {
            $q->where("name", "like", "%{$request->keywords}%");
        });
    
        $users = $query->get();
    
        return view('users.list', compact('users'));
    }
       

	public function register(Request $request) {
        return view('users.register');
    }

    public function doRegister(Request $request) {
        try {
            $this->validate($request, [
                'name' => ['required', 'string', 'min:5'],
                'email' => ['required', 'email', 'unique:users'],
                'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->withErrors([$e->getMessage()]);
        }
    
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt($request->password); 
        $user->save();
    
        $user->assignRole('Customer');
    
        Auth::login($user);
    
        return redirect('/products')->with('success', 'Registration successful!');
    }
    
    public function login(Request $request) {
        return view('users.login');
    } 
    
    public function doLogin(Request $request) {
        $credentials = [
            'email'    => $request->email,
            'password' => $request->password,
        ];
    
        if (!Auth::attempt($credentials)) {
            return redirect()->back()
                             ->withInput($request->input())
                             ->withErrors('Invalid login information.');
        }
    
        $user = Auth::user();
    
        // Check if the user logged in with a temporary password.
        if ($user->temp_password) {
            // Redirect to the change password page (e.g., route 'edit_password')
            return redirect()->route('edit_password', $user->id)
                             ->with('info', 'You must change your temporary password.');
        }
    
        return redirect('/');
    }
   

    public function doLogout(Request $request) {
    	
    	Auth::logout();

        return redirect('/');
    }

    public function profile(Request $request, User $user = null) {

        $user = $user??auth()->user();
        if(auth()->id()!=$user->id) {
            if(!auth()->user()->hasPermissionTo('show_users')) abort(401);
        }

        $permissions = [];
        foreach($user->permissions as $permission) {
            $permissions[] = $permission;
        }
        foreach($user->roles as $role) {
            foreach($role->permissions as $permission) {
                $permissions[] = $permission;
            }
        }

        return view('users.profile', compact('user', 'permissions'));
    }

    public function forgotPassword() {
        return view('users.forgot_password');
    }

    public function edit(Request $request, User $user = null) {
   
        $user = $user??auth()->user();
        if(auth()->id()!=$user?->id) {
            if(!auth()->user()->hasPermissionTo('edit_users')) abort(401);
        }
    
        $roles = [];
        foreach(Role::all() as $role) {
            $role->taken = ($user->hasRole($role->name));
            $roles[] = $role;
        }

        $permissions = [];
        $directPermissionsIds = $user->permissions()->pluck('id')->toArray();
        foreach(Permission::all() as $permission) {
            $permission->taken = in_array($permission->id, $directPermissionsIds);
            $permissions[] = $permission;
        }      

        return view('users.edit', compact('user', 'roles', 'permissions'));
    }

    public function save(Request $request, User $user) {

        if(auth()->id()!=$user->id) {
            if(!auth()->user()->hasPermissionTo('show_users')) abort(401);
        }

        $user->name = $request->name;
        $user->save();

        if(auth()->user()->hasPermissionTo('admin_users')) {

            $user->syncRoles($request->roles);
            $user->syncPermissions($request->permissions);

            Artisan::call('cache:clear');
        }

        //$user->syncRoles([1]);
        //Artisan::call('cache:clear');

        return redirect(route('profile', ['user'=>$user->id]));
    }

    public function delete(Request $request, User $user) {

        if(!auth()->user()->hasPermissionTo('delete_users')) abort(401);

        $user->delete();

        return redirect()->route('users');
    }
    public function addCredit(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:1']
        ]);
    
        // Now $user is the target user that the admin/employee wants to add credit for.
        $user->credit += $request->amount;
        $user->save();
    
        return redirect()->back()->with('success', 'Credit added successfully!');
    }
    
    public function giveGift(Request $request, \App\Models\User $user)
    {

        if(!auth()->user()->hasPermissionTo('manage_sales')) abort(401);
        $user->credit += 10000;
        $user->save();
    
        return redirect()->back()->with('success', 'Gift Added successfully!');
    }
    

    public function editPassword(Request $request, User $user = null) {

        $user = $user??auth()->user();
        if(auth()->id()!=$user?->id) {
            if(!auth()->user()->hasPermissionTo('edit_users')) abort(401);
        }

        return view('users.edit_password', compact('user'));
    }

    public function savePassword(Request $request, User $user) {
        if (auth()->id() === $user->id) {
            $request->validate([
                'old_password' => 'required',
                'password' => ['required', 'confirmed', Password::min(8)->numbers()->letters()->mixedCase()->symbols()],
            ]);
    
            if (!Auth::attempt(['email' => $user->email, 'password' => $request->old_password])) {
                Auth::logout();
                return redirect('/')->withErrors('Your old password is incorrect.');
            }
    
            // Update the user's password.
            $user->password = bcrypt($request->password);
            // If you're using a temporary password flag, clear it now.
            $user->temp_password = false;
            $user->save();
    
            return redirect()->route('profile', ['user' => $user->id])
                             ->with('success', 'Password updated successfully.');
        } else {
            abort(403, 'Unauthorized action.');
        }
    }

    public function sendTempPassword(Request $request) {
        // 1. Validate that the email was submitted and is in correct format
        $request->validate(['email' => 'required|email']);
    
        // 2. Find the user with the submitted email
        $user = \App\Models\User::where('email', $request->email)->first();
    
        if (!$user) {
            // 3. If user not found, show error
            return back()->withErrors(['email' => 'No user found with that email.']);
        }
    
        // 4. Generate a temporary password
        $tempPassword = Str::random(10);
    
        // 5. Set it as their password and mark them with a temp flag
        $user->password = bcrypt($tempPassword);
        $user->temp_password = true;
        $user->save();
    
        // 6. Send the email using the Mailable class
        Mail::to($user->email)->send(new TemporaryPasswordMail($tempPassword));
    
        // 7. Redirect to login with success message
        return redirect()->route('login')->with('success', 'Temporary password sent to your email.');
    }
    
    
} 