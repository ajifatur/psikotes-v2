<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Ajifatur\Helpers\DateTimeExt;
use Ajifatur\FaturHelper\Models\UserAttribute;
use Ajifatur\FaturHelper\Models\Visitor;
use App\Models\User;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        // View
        return view('auth/register');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'gender' => 'required',
            'birthdate' => 'required',
            'occupation' => 'required',
            'position' => 'required',
            'institution' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Save the user
            $user = new User;
            $user->role_id = role('member');
            $user->name = $request->name;
            $user->username = $request->email;
            $user->email = $request->email;
            $user->email_verified_at = null;
            $user->password = bcrypt($request->password);
            $user->remember_token = null;
            $user->access_token = access_token();
            $user->avatar = null;
            $user->status = 1;
            $user->last_visit = date('Y-m-d H:i:s');
            $user->save();

            // Save the user attribute
            $user_attribute = new UserAttribute;
            $user_attribute->user_id = $user->id;
            $user_attribute->birthdate = DateTimeExt::change($request->birthdate);
            $user_attribute->gender = $request->gender;
            $user_attribute->country_code = $request->country_code;
            $user_attribute->dial_code = dial_code($request->country_code);
            $user_attribute->phone_number = $request->phone_number;
            $user_attribute->occupation = $request->occupation;
            $user_attribute->position = $request->position;
            $user_attribute->institution = $request->institution;
            $user_attribute->save();

			if($user && $user_attribute) {
				// Login
				Auth::login($user);

				// Add to visitors
				if(Schema::hasTable('visitors')) {
					$visitor = new Visitor;
					$visitor->user_id = $user->id;
					$visitor->ip_address = $request->ip();
					$visitor->device = device_info();
					$visitor->browser = browser_info();
					$visitor->platform = platform_info();
					$visitor->location = location_info($request->ip());
					$visitor->save();
				}

				// Set projects session
				session()->put('projects', []);

				// Redirect
				return redirect()->route('member.dashboard');
			}
			else {
				// Redirect
				return redirect()->route('auth.register');
			}
        }
    }
}
