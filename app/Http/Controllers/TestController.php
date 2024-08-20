<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\Test;
use App\Models\Result;

class TestController extends Controller
{    
    /**
     * Display test page
     * 
     * @param  \Illuminate\Http\Request
     * @param  string $path
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $path)
    {
        if(!Auth::user()->attribute)
            return redirect()->route('member.profile.edit');

        // Get the test
        $test = Test::where('code','=',$path)->firstOrFail();

        // Results has been assigned
        $results = Result::where('user_id','=',Auth::user()->id)->where('project_id','=',$request->query('project'))->pluck('test_id')->toArray();

        if(in_array($test->id, $results))
            return redirect()->route('member.project', ['id' => $request->query('project')])->with(['message' => 'Tes ini sudah pernah dikerjakan!']);
            
        // Test DISC 24
        if($path == 'disc-24')
            return \App\Http\Controllers\Test\DISC24Controller::index($request, $path, $test);
        // Test DISC 40
        elseif($path == 'disc-40')
            return \App\Http\Controllers\Test\DISC40Controller::index($request, $path, $test);
        // Test Papikostick
        elseif($path == 'papikostick')
            return \App\Http\Controllers\Test\PapikostickController::index($request, $path, $test);
        // Test SDI
        elseif($path == 'sdi')
            return \App\Http\Controllers\Test\SDIController::index($request, $path, $test);
        // Test MSDT
        elseif($path == 'msdt')
            return \App\Http\Controllers\Test\MSDTController::index($request, $path, $test);
        // Test IST
        elseif($path == 'ist')
            return \App\Http\Controllers\Test\ISTController::index($request, $path, $test);
        // Test RMIB
        elseif($path == 'rmib')
            return \App\Http\Controllers\Test\RMIBController::index($request, $path, $test);
        else
            abort(404);
    }

    /**
     * Store
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Tes DISC 40
        if($request->path == 'disc-40')
            return \App\Http\Controllers\Test\DISC40Controller::store($request);
        // Tes DISC 24
        elseif($request->path == 'disc-24')
            return \App\Http\Controllers\Test\DISC24Controller::store($request);
        // Tes Papikostick
        elseif($request->path == 'papikostick')
            return \App\Http\Controllers\Test\PapikostickController::store($request);
        // Tes SDI
        elseif($request->path == 'sdi')
            return \App\Http\Controllers\Test\SDIController::store($request);
        // Tes MSDT
        elseif($request->path == 'msdt')
            return \App\Http\Controllers\Test\MSDTController::store($request);
        // Tes IST
        elseif($request->path == 'ist')
            return \App\Http\Controllers\Test\ISTController::store($request);
        // Tes RMIB
        elseif($request->path == 'rmib')
            return \App\Http\Controllers\Test\RMIBController::store($request);
    }
}