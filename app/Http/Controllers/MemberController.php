<?php

namespace App\Http\Controllers;

use Auth;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Ajifatur\Helpers\DateTimeExt;
use Ajifatur\FaturHelper\Models\UserAttribute;
use App\Imports\MemberImport;
use App\Models\User;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Check the access
        // has_access(__METHOD__, Auth::user()->role_id);

        // Get users
        $users = User::where('role_id','=',role('member'))->orderBy('role_id','asc')->orderBy('created_at','desc')->get();

        // View
        return view('admin/member/index', [
            'users' => $users
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        // Check the access
        // has_access(__METHOD__, Auth::user()->role_id);
        
        // Delete the user
        $user = User::find($request->id);
        $user->delete();

        // Delete the user attribute
        if($user->attribute) {
            $user->attribute->delete();
        }

        // Delete the user avatars
        if(count($user->avatars) > 0) {
            $user_avatars = UserAvatar::where('user_id','=',$user->id)->delete();
        }

        // Redirect
        return redirect()->route('admin.member.index')->with(['message' => 'Berhasil menghapus data.']);
    }

    /**
     * Remove the selected resource from storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function deleteBulk(Request $request)
    {
        // Check the access
        // has_access(__METHOD__, Auth::user()->role_id);

        // Explode ids
        $ids = explode(",", $request->ids);

        if(count($ids) > 0) {
            foreach($ids as $id) {
                if($id != 1) {
                    // Get the user
                    $user = User::find($id);

                    // Delete the user
                    $user->delete();

                    // Delete the user attribute
                    if($user->attribute) {
                        $user->attribute->delete();
                    }

                    // Delete the user avatars
                    if(count($user->avatars) > 0) {
                        $user_avatars = UserAvatar::where('user_id','=',$user->id)->delete();
                    }
                }
            }

            // Redirect
            if(in_array(1, $ids) && count($ids) > 1) {
                return redirect()->route('admin.member.index')->with(['message' => 'Berhasil menghapus data, tetapi tidak bisa menghapus akun default.']);
            }
            elseif(in_array(1, $ids) && count($ids) == 1) {
                return redirect()->route('admin.member.index')->with(['message' => 'Tidak bisa menghapus akun default.']);
            }
            else {
                return redirect()->route('admin.member.index')->with(['message' => 'Berhasil menghapus data.']);
            }
        }
    }

    /**
     * Import from Excel.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        // Check the access
        // has_access(__METHOD__, Auth::user()->role_id);

		ini_set("memory_limit", "-1");
        ini_set("max_execution_time", "-1");

        // Get array
		$array = Excel::toArray(new MemberImport, public_path('assets/excel/DesktopIP Employee Training List.xlsx'));
        if(count($array)>0) {
            foreach($array[0] as $key=>$data) {
                if($data[0] != null) {
                    // Save the user
                    $user = User::where('email','=',$data[2])->first();
                    if(!$user) $user = new User;
                    $user->role_id = role('member');
                    $user->name = $data[0];
                    $user->username = $data[1];
                    $user->email = $data[2];
                    $user->email_verified_at = null;
                    $user->password = bcrypt($data[3]);
                    $user->remember_token = null;
                    $user->access_token = access_token();
                    $user->avatar = null;
                    $user->status = 1;
                    $user->last_visit = null;
                    $user->save();

                    // Save the user attribute
                    $user_attribute = new UserAttribute;
                    $user_attribute->user_id = $user->id;
                    $user_attribute->birthdate = null;
                    $user_attribute->gender = null;
                    $user_attribute->country_code = null;
                    $user_attribute->dial_code = null;
                    $user_attribute->phone_number = null;
                    $user_attribute->occupation = $data[4];
                    $user_attribute->position = $data[5];
                    $user_attribute->institution = $data[6];
                    $user_attribute->save();
                }
            }
        }
    }
}
