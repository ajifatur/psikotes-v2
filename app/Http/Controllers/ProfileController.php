<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ajifatur\Helpers\DateTimeExt;
use Ajifatur\FaturHelper\Models\UserAttribute;
use App\Models\User;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit()
    {
        // Check the access
        // has_access(method(__METHOD__), Auth::user()->role_id);
		
        // View
        return view('member/dashboard/edit');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'gender' => 'required',
            'birthdate' => 'required',
            'country_code' => 'required',
            'phone_number' => 'required',
            'occupation' => 'required',
            'position' => 'required',
            'institution' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
			// Update the user
			$user = User::find(Auth::user()->id);
			$user->name = $request->name;
			$user->save();
			
            // Save / update the user attribute
			$user_attribute = UserAttribute::where('user_id','=',$user->id)->first();
            if(!$user_attribute) $user_attribute = new UserAttribute;
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
			
			// Redirect
			return redirect()->route('member.dashboard')->with(['message' => 'Berhasil memperbarui profil']);
        }
    }
}
