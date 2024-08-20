<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Ajifatur\Helpers\DateTimeExt;
use App\Models\Result;
use App\Models\Project;

class ResultController extends Controller
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

        // Get the project
        $project = Project::find($request->query('project'));

        if(Auth::user()->role_id == role('super-admin')) {
            // Get results
            if($project)
                $results = Result::has('user')->where('project_id','=',$project->id)->orderBy('created_at','desc')->get();
            else
                $results = Result::has('user')->orderBy('created_at','desc')->get();

            // Get projects
            $projects = Project::latest()->get();
        }
        elseif(Auth::user()->role_id == role('hrd')) {
            // Get user
            $user = Auth::user();

            // Get results
            if($project) {
                $results = Result::has('user')->whereHas('project', function (Builder $query) use ($project, $user) {
                    return $query->where('id','=',$project->id)->where('user_id','=',$user->id);
                })->orderBy('created_at','desc')->get();
            }
            else {
                $results = Result::has('user')->whereHas('project', function (Builder $query) use ($user) {
                    return $query->where('user_id','=',$user->id);
                })->orderBy('created_at','desc')->get();
            }

            // Get projects
            $projects = Project::where('user_id','=',Auth::user()->id)->latest()->get();
        }

        // View
        return view('admin/result/index', [
            'results' => $results,
            'projects' => $projects
        ]);
    }

    /**
     * Show the detail of the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function detail($id)
    {
        // Check the access
        // has_access(method(__METHOD__), Auth::user()->role_id);

        // Get result
        if(Auth::user()->role_id == role('super-admin'))
            $result = Result::has('user')->findOrFail($id);
        elseif(Auth::user()->role_id == role('hrd'))
            $result = Result::has('user')->findOrFail($id);

        // Decode
        $result->result = json_decode($result->result, true);
        
        // View
        if($result->test->code == 'disc-24')
            return \App\Http\Controllers\Test\DISC24Controller::detail($result);
        elseif($result->test->code == 'disc-40')
            return \App\Http\Controllers\Test\DISC40Controller::detail($result);
        elseif($result->test->code == 'msdt')
            return \App\Http\Controllers\Test\MSDTController::detail($result);
        elseif($result->test->code == 'papikostick')
            return \App\Http\Controllers\Test\PapikostickController::detail($result);
        elseif($result->test->code == 'rmib')
            return \App\Http\Controllers\Test\RMIBController::detail($result);
        elseif($result->test->code == 'sdi')
            return \App\Http\Controllers\Test\SDIController::detail($result);
        elseif($result->test->code == 'ist')
            return \App\Http\Controllers\Test\ISTController::detail($result);
        else
            abort(404);
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
        
        // Delete the result
        $result = Result::find($request->id);
        $result->delete();

        // Redirect
        return redirect()->route('admin.result.index')->with(['message' => 'Berhasil menghapus data.']);
    }

    /**
     * Print to PDF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function print(Request $request)
    {
        // Check the access
        // has_access(method(__METHOD__), Auth::user()->role_id);
		
        ini_set('max_execution_time', '300');
		
        // DISC 1.0
        if($request->path == 'disc-24')
            return \App\Http\Controllers\Test\DISC24Controller::print($request);
        // DISC 2.0
        elseif($request->path == 'disc-40')
            return \App\Http\Controllers\Test\DISC40Controller::print($request);
        // IST
        elseif($request->path == 'ist')
            return \App\Http\Controllers\Test\ISTController::print($request);
        // MSDT
        elseif($request->path == 'msdt')
            return \App\Http\Controllers\Test\MSDTController::print($request);
        // Papikostick
        elseif($request->path == 'papikostick')
            return \App\Http\Controllers\Test\PapikostickController::print($request);
        // SDI
        elseif($request->path == 'sdi')
            return \App\Http\Controllers\Test\SDIController::print($request);
        // RMIB
        elseif($request->path == 'rmib')
            return \App\Http\Controllers\Test\RMIBController::print($request);
    }
}
