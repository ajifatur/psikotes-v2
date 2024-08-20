<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ajifatur\Helpers\DateTimeExt;
use App\Models\Project;

class DashboardController extends Controller
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
        // has_access(method(__METHOD__), Auth::user()->role_id);

        if(Auth::user()->role_id == role('member')) {
            if(Auth::user()->attribute && Auth::user()->attribute->birthdate != null && Auth::user()->attribute->gender != null) {
                // Get projects
                $projects = Project::where('date_from','<=',date('Y-m-d H:i:s'))->where('date_to','>=',date('Y-m-d H:i:s'))->latest()->get();
                
                // View
                return view('member/dashboard/index', [
                    'projects' => $projects
                ]);
            }
            else
                return redirect()->route('member.profile.edit');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // Check the access
        // has_access(method(__METHOD__), Auth::user()->role_id);

        // View
        return view('admin/project/create');
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
            'token' => 'required|max:255',
            'date' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Save the project
            $project = new Project;
            $project->user_id = Auth::user()->id;
            $project->name = $request->name;
            $project->token = $request->token;
            $project->date_from = DateTimeExt::split($request->date)[0];
            $project->date_to = DateTimeExt::split($request->date)[1];
            $project->save();

            // Redirect
            return redirect()->route('admin.project.index')->with(['message' => 'Berhasil menambah data.']);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // Check the access
        // has_access(method(__METHOD__), Auth::user()->role_id);

        // Project
        if(Auth::user()->role_id == role('super-admin'))
            $project = Project::findOrFail($id);
        elseif(Auth::user()->role_id == role('hrd'))
            $project = Project::where('user_id','=',Auth::user()->id)->findOrFail($id);

        // View
        return view('admin/project/edit', [
            'project' => $project
        ]);
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
            'token' => 'required|max:255',
            'date' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Update the project
            $project = Project::find($request->id);
            $project->name = $request->name;
            $project->token = $request->token;
            $project->date_from = DateTimeExt::split($request->date)[0];
            $project->date_to = DateTimeExt::split($request->date)[1];
            $project->save();

            // Redirect
            return redirect()->route('admin.project.index')->with(['message' => 'Berhasil mengupdate data.']);
        }
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
        // has_access(method(__METHOD__), Auth::user()->role_id);
        
        // Delete the project
        $project = Project::find($request->id);
        $project->delete();

        // Redirect
        return redirect()->route('admin.project.index')->with(['message' => 'Berhasil menghapus data.']);
    }
}