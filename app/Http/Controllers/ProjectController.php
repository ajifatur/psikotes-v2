<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ajifatur\Helpers\DateTimeExt;
use App\Models\Project;
use App\Models\Test;
use App\Models\User;
use App\Models\Result;

class ProjectController extends Controller
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

        // Get projects
        if(Auth::user()->role_id == role('super-admin'))
            $projects = Project::latest()->get();
        elseif(Auth::user()->role_id == role('hrd'))
            $projects = Project::where('user_id','=',Auth::user()->id)->latest()->get();

        // View
        return view('admin/project/index', [
            'projects' => $projects
        ]);
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

        // Get tests
        $tests = Test::orderBy('num_order','asc')->get();

        // Get HRDs
        $hrds = User::where('role_id','=',role('hrd'))->orderBy('name','asc')->get();

        // View
        return view('admin/project/create', [
            'tests' => $tests,
            'hrds' => $hrds
        ]);
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
            'hrd' => Auth::user()->role_id == role('super-admin') ? 'required' : '',
            'name' => 'required|max:255',
            'token' => 'required|max:255',
            'date' => 'required',
            'tests' => 'required',
        ]);
        
        // Check errors
        if($validator->fails()) {
            // Back to form page with validation error messages
            return redirect()->back()->withErrors($validator->errors())->withInput();
        }
        else {
            // Save the project
            $project = new Project;
            $project->user_id = Auth::user()->role_id == role('hrd') ? Auth::user()->id : $request->hrd;
            $project->name = $request->name;
            $project->token = $request->token;
            $project->date_from = DateTimeExt::split($request->date)[0];
            $project->date_to = DateTimeExt::split($request->date)[1];
            $project->save();

            // Save the project tests
            $project->tests()->attach($request->tests);

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

        // Get tests
        $tests = Test::orderBy('num_order','asc')->get();

        // Get HRDs
        $hrds = User::where('role_id','=',role('hrd'))->orderBy('name','asc')->get();

        // View
        return view('admin/project/edit', [
            'project' => $project,
            'tests' => $tests,
            'hrds' => $hrds
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
            'hrd' => Auth::user()->role_id == role('super-admin') ? 'required' : '',
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
            $project->user_id = Auth::user()->role_id == role('hrd') ? Auth::user()->id : $request->hrd;
            $project->name = $request->name;
            $project->token = $request->token;
            $project->date_from = DateTimeExt::split($request->date)[0];
            $project->date_to = DateTimeExt::split($request->date)[1];
            $project->save();

            // Update the project tests
            $project->tests()->sync($request->tests);

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

        // Delete the project tests
        $project->tests()->detach();

        // Redirect
        return redirect()->route('admin.project.index')->with(['message' => 'Berhasil menghapus data.']);
    }

    /**
     * Check.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function check(Request $request)
    {
        // Check the access
        // has_access(method(__METHOD__), Auth::user()->role_id);

        if($request->method() == 'GET') {
            // Check the project
            if(in_array($request->query('id'), session()->get('projects'))) {
                // Get the project
                $project = Project::findOrFail($request->query('id'));

                // Results has been assigned
                $results = Result::where('user_id','=',Auth::user()->id)->where('project_id','=',$project->id)->pluck('test_id')->toArray();

                // View
                return view('member/project/index', [
                    'project' => $project,
                    'results' => $results,
                ]);
            }
            else {
                return redirect()->route('member.dashboard')->with(['message' => 'Anda wajib memasukkan token sebelum menuju ke halaman Tes.']);
            }
        }
        elseif($request->method() == 'POST') {
            // Check the project
            $project = Project::where('token','=',$request->token)->find($request->id);

            if($project) {
                // Push to project session
                if(!in_array($project->id, session()->get('projects')))
                    session()->push('projects', $project->id);
                
                // Results has been assigned
                $results = Result::where('user_id','=',Auth::user()->id)->where('project_id','=',$project->id)->pluck('test_id')->toArray();

                // View
                return view('member/project/index', [
                    'project' => $project,
                    'results' => $results,
                ]);
            }
            else {
                return redirect()->route('member.dashboard')->with(['message' => 'Token yang Anda masukkan salah!']);
            }
        }
    }
}