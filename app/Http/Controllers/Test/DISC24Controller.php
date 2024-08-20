<?php

namespace App\Http\Controllers\Test;

use Auth;
use PDF;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dompdf\FontMetrics;
use App\Models\Packet;
use App\Models\Result;
use App\Models\Description;

class DISC24Controller extends Controller
{    
    /**
     * Display
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  string $path
     * @param  object $test
     * @return \Illuminate\Http\Response
     */
    public static function index(Request $request, $path, $test)
    {
        // Get the packet and questions
        $packet = Packet::where('test_id','=',$test->id)->where('status','=',1)->first();
        $questions = $packet ? $packet->questions()->orderBy('number','asc')->get() : [];

        // View
        return view('member/test/'.$path, [
            'packet' => $packet,
            'path' => $path,
            'questions' => $questions,
            'test' => $test,
        ]);
    }

    /**
     * Store
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function store(Request $request)
    {
        // Get the packet
        $packet = Packet::where('test_id','=',$request->test_id)->where('status','=',1)->first();
        
        // Set array
        $array = [
            'dm' => $request->Dm,
            'im' => $request->Im,
            'sm' => $request->Sm,
            'cm' => $request->Cm,
            'bm' => $request->Bm,
            'dl' => $request->Dl,
            'il' => $request->Il,
            'sl' => $request->Sl,
            'cl' => $request->Cl,
            'bl' => $request->Bl
        ];
        $array['answers']['m'] = $request->y;
        $array['answers']['l'] = $request->n;

        // Save the result
        $result = new Result;
        $result->user_id = Auth::user()->id;
        $result->project_id = $request->project_id;
        $result->test_id = $request->test_id;
        $result->packet_id = $request->packet_id;
        $result->result = json_encode($array);
        $result->save();

        // Redirect
        return redirect()->route('member.project', ['id' => $request->project_id])->with(['message' => 'Berhasil mengerjakan tes '.$packet->test->name]);
    }
    
    /**
     * Display the specified resource.
     *
     * @param  object  $result
     * @return \Illuminate\Http\Response
     */
    public static function detail($result)
    {
        // Check the access
        // has_access(method(__METHOD__), Auth::user()->role_id);

        // Set the diff
        $differenceArray = [
            'D' => $result->result['dm'] - $result->result['dl'],
            'I' => $result->result['im'] - $result->result['il'],
            'S' => $result->result['sm'] - $result->result['sl'],
            'C' => $result->result['cm'] - $result->result['cl'],
        ];

        // Array 1
        $array_1 = [
            0   => [-6, -7, -5.7, -6],
            1   => [-5.3, -4.6, -4.3, -4.7],
            2   => [-4, -2.5, -3.5, -3.5],
            3   => [-2.5, -1.3, -1.5, -1.5],
            4   => [-1.7, 1, -0.7, 0.5],
            5   => [-1.3, 3, 0.5, 2],
            6   => [0, 3.5, 1, 3],
            7   => [0.5, 5.3, 2.5, 5.3],
            8   => [1, 5.7, 3, 5.7],
            9   => [2, 6, 4, 6],
            10  => [3, 6.5, 4.6, 6.3],
            11  => [3.5, 7, 5, 6.5],
            12  => [4, 7, 5.7, 6.7],
            13  => [4.7, 7, 6, 7],
            14  => [5.3, 7, 6.5, 7.3],
            15  => [6.5, 7, 6.5, 7.3],
            16  => [7, 7.5, 7, 7.3],
            17  => [7, 7.5, 7, 7.5],
            18  => [7, 7.5, 7, 8],
            19  => [7.5, 7.5, 7.5, 8],
            20  => [7.5, 8, 7.5, 8],
        ];

        // Array 2
        $array_2 = [
            0   => [7.5, 7, 7.5, 7.5],
            1   => [6.5, 6, 7, 7],
            2   => [4.3, 4, 6, 5.6],
            3   => [2.5, 2.5, 4, 4],
            4   => [1.5, 0.5, 2.5, 2.5],
            5   => [0.5, 0, 1.5, 1.5],
            6   => [0, -2, 0.5, 0.5],
            7   => [-1.3, -3.5, -1.3, 0],
            8   => [-1.5, -4.3, -2, -1.3],
            9   => [-2.5, -5.3, -3, -2.5],
            10  => [-3, -6, -4.3, -3.5],
            11  => [-3.5, -6.5, -5.3, -5.3],
            12  => [-4.3, -7, -6, -5.7],
            13  => [-5.3, -7.2, -6.5, -6],
            14  => [-5.7, -7.2, -6.7, -6.5],
            15  => [-6, -7.2, -6.7, -7],
            16  => [-6.5, -7.3, -7, -7.3],
            17  => [6.7, -7.3, -7.2, -7.5],
            18  => [7, -7.3, -7.3, -7.7],
            19  => [-7.3, -7.5, -7.5, -7.9],
            20  => [-7.5, -8, -8, -8],
        ];

        // Array 3
        $array_3 = [
            -22 => [-8, -8, -8, -7.5],
            -21 => [-7.5, -8, -8, -7.3],
            -20 => [-7, -8, -8, -7.3],
            -19 => [-6.8, -8, -8, -7],
            -18 => [-6.75, -7, -7.5, -6.7],
            -17 => [-6.7, -6.7, -7.3, -6.7],
            -16 => [-6.5, -6.7, -7.3, -6.7],
            -15 => [-6.3, -6.7, -7, -6.5],
            -14 => [-6.1, -6.7, -6.5, -6.3],
            -13 => [-5.9, -6.7, -6.5, -6],
            -12 => [-5.7, -6.7, -6.5, -5.85],
            -11 => [-5.3, -6.7, -6.5, -5.85],
            -10 => [-4.3, -6.5, -6, -5.7],
            -9  => [-3.5, -6, -4.7, -4.7],
            -8  => [-3.25, -5.7, -4.3, -4.3],
            -7  => [-3, -4.7, -3.5, -3.5],
            -6  => [-2.75, -4.3, -3, -3],
            -5  => [-2.5, -3.5, -2, -2.5],
            -4  => [-1.5, -3, -1.5, -0.5],
            -3  => [-1, -2, -1, 0],
            -2  => [-0.5, -1.5, -0.5, 0.3],
            -1  => [-0.25, 0, 0, 0.5],
            0   => [0, 0.5, 1, 1.5],
            1   => [0.5, 1, 1.5, 3],
            2   => [0.7, 1.5, 2, 4],
            3   => [1, 3, 3, 4.3],
            4   => [1.3, 4, 3.5, 5.5],
            5   => [1.5, 4.3, 4, 5.7],
            6   => [2, 5, 0, 6], // S is empty
            7   => [2.5, 5.5, 4.7, 6.3],
            8   => [3.5, 6.5, 5, 6.5],
            9   => [4, 6.7, 5.5, 6.7],
            10  => [4.7, 7, 6, 7],
            11  => [4.85, 7.3, 6.2, 7.3],
            12  => [5, 7.3, 6.3, 7.3],
            13  => [5.5, 7.3, 6.5, 7.3],
            14  => [6, 7.3, 6.7, 7.3],
            15  => [6.3, 7.3, 7, 7.3],
            16  => [6.5, 7.3, 7.3, 7.3],
            17  => [6.7, 7.3, 7.3, 7.5],
            18  => [7, 7.5, 7.3, 8],
            19  => [7.3, 8, 7.3, 8],
            20  => [7.3, 8, 7.5, 8],
            21  => [7.5, 8, 8, 8],
            22  => [8, 8, 8, 8],
        ];

        // Graph
        $graph = [
            1 => [
                'D' => $array_1[$result->result['dm']][0],
                'I' => $array_1[$result->result['im']][1],
                'S' => $array_1[$result->result['sm']][2],
                'C' => $array_1[$result->result['cm']][3],
            ],
            2 => [
                'D' => $array_2[$result->result['dl']][0],
                'I' => $array_2[$result->result['il']][1],
                'S' => $array_2[$result->result['sl']][2],
                'C' => $array_2[$result->result['cl']][3],
            ],
            3 => [
                'D' => $array_3[$differenceArray['D']][0],
                'I' => $array_3[$differenceArray['I']][1],
                'S' => $array_3[$differenceArray['S']][2],
                'C' => $array_3[$differenceArray['C']][3],
            ],
        ];

        // Set the personality
        $array_kepribadian = [
            'most' => [],
            'least' => [],
            'change' => [],
        ];
        for($i = 0; $i < 40; $i++) {
            array_push($array_kepribadian['most'], self::analyze($i + 1, $graph[1]['D'], $graph[1]['I'], $graph[1]['S'], $graph[1]['C']));
            array_push($array_kepribadian['least'], self::analyze($i + 1, $graph[2]['D'], $graph[2]['I'], $graph[2]['S'], $graph[2]['C']));
            array_push($array_kepribadian['change'], self::analyze($i + 1, $graph[3]['D'], $graph[3]['I'], $graph[3]['S'], $graph[3]['C']));
        }

        // Index
        $index = [
            'most' => [],
            'least' => [],
            'change' => [],
        ];
        foreach($array_kepribadian['most'] as $key=>$value) {
            if($value == 1) array_push($index['most'], $key);
        }
        foreach($array_kepribadian['least'] as $key=>$value) {
            if($value == 1) array_push($index['least'], $key);
        }
        foreach($array_kepribadian['change'] as $key=>$value) {
            if($value == 1) array_push($index['change'], $key);
        }

        // Set the description
        $description = Description::where('packet_id','=',$result->packet_id)->first();
        $description->description = json_decode($description->description, true);

        // View
        return view('admin/result/disc-24/detail', [
            'result' => $result,
            'differenceArray' => $differenceArray,
            'graph' => $graph,
            'index' => $index,
            'description' => $description,
        ]);
    }

    /**
     * Print to PDF.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function print(Request $request)
    {
        // Set the DISC
        $disc = array('D', 'I', 'S','C');
		
		// Set the index
		$index = json_decode($request->index, true);
		
        // Set the description
        $description = Description::where('packet_id','=',$request->packet_id)->first();
        $description->description = json_decode($description->description, true);
		
		// Set the MOST, LEAST, CHANGE
		$most = $description->description[$index['most'][0]];
		$least = $description->description[$index['least'][0]];
		$change = $description->description[$index['change'][0]];
        
        // PDF
        $pdf = PDF::loadview('admin/result/disc-24/pdf', [
            'mostChartImage' => $request->mostChartImage,
            'leastChartImage' => $request->leastChartImage,
            'changeChartImage' => $request->changeChartImage,
            'name' => $request->name,
            'age' => $request->age,
            'gender' => $request->gender,
            'position' => $request->position,
            'test' => $request->test,
            'result' => $request->result,
            'differenceArray' => $request->differenceArray,
            'index' => $request->index,
            'disc' => $disc,
            'most' => $most,
            'least' => $least,
            'change' => $change,
        ]);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream($request->name . '_' . $request->test . '.pdf');
    }
    
    /**
     * Analyze.
     *
     * @param  int  $x, $d, $i, $s, $c
     * @return \Illuminate\Http\Response
     */
    public static function analyze($x, $d, $i, $s, $c)
    {
        if($x == 1) return ($d <= 0 && $i <= 0 && $s <= 0 && $c > 0) ? 1 : 0;
        elseif($x == 2) return ($d > 0 && $i <= 0 && $s <= 0 && $c <= 0) ? 1 : 0;
        elseif($x == 3) return ($d > 0 && $i <= 0 && $s <= 0 && $c > 0 && $c >= $d) ? 1 : 0;
        elseif($x == 4) return ($d > 0 && $i > 0 && $s <= 0 && $c <= 0 && $i >= $d) ? 1 : 0;
        elseif($x == 5) return ($d > 0 && $i > 0 && $s < $c && $i && $d && $c > 0 && $i >= $d && $d >= $c) ? 1 : 0;
        elseif($x == 6) return ($d > 0 && $i > 0 && $s > 0 && $c < $i && $d && $s && $i >= $d && $d >= $s) ? 1 : 0;
        elseif($x == 7) return ($d > 0 && $i > 0 && $s > 0 && $c < $i && $d && $s && $i >= $s && $s >= $d) ? 1 : 0;
        elseif($x == 8) return ($d > 0 && $i <= 0 && $s > 0 && $c > 0 && $s >= $d && $d >= $c) ? 1 : 0;
        elseif($x == 9) return ($d > 0 && $i > 0 && $s <= 0 && $c <= 0 && $d >= $i) ? 1 : 0;
        elseif($x == 10) return ($d > 0 && $i > 0 && $s > 0 && $c < $i && $d && $s && $d >= $i && $i >= $s) ? 1 : 0;
        elseif($x == 11) return ($d > 0 && $i <= 0 && $s > 0 && $c <= 0 && $d >= $s) ? 1 : 0;
        elseif($x == 12) return ($d <= 0 && $i > 0 && $s > 0 && $c > 0 && $c >= $i && $i >= $s) ? 1 : 0;
        elseif($x == 13) return ($d <= 0 && $i > 0 && $s > 0 && $c > 0 && $c >= $s && $s >= $i) ? 1 : 0;
        elseif($x == 14) return ($d <= 0 && $i > 0 && $s > 0 && $c > 0 && $i >= $s && $i >= $c) ? 1 : 0;
        elseif($x == 15) return ($d <= 0 && $i <= 0 && $s > 0 && $c <= 0) ? 1 : 0;
        elseif($x == 16) return ($d <= 0 && $i <= 0 && $s > 0 && $c > 0 && $c >= $s) ? 1 : 0;
        elseif($x == 17) return ($d <= 0 && $i <= 0 && $s > 0 && $c > 0 && $s >= $c) ? 1 : 0;
        elseif($x == 18) return ($d > 0 && $i <= 0 && $s <= 0 && $c > 0 && $d >= $c) ? 1 : 0;
        elseif($x == 19) return ($d > 0 && $i > 0 && $c > 0 && $s < $c && $i && $d && $d >= $i && $i >= $c) ? 1 : 0;
        elseif($x == 20) return ($d > 0 && $i > 0 && $s > 0 && $c < $i && $d && $s && $d >= $s && $s >= $i) ? 1 : 0;
        elseif($x == 21) return ($d > 0 && $i <= 0 && $s > 0 && $c > 0 && $d >= $s && $s >= $c) ? 1 : 0;
        elseif($x == 22) return ($d > 0 && $i > 0 && $c > 0 && $s < $c && $i && $d && $d >= $c && $c >= $i) ? 1 : 0;
        elseif($x == 23) return ($d > 0 && $i <= 0 && $s > 0 && $c > 0 && $d >= $c && $c >= $i) ? 1 : 0;
        elseif($x == 24) return ($d <= 0 && $i > 0 && $s <= 0 && $c <= 0) ? 1 : 0;
        elseif($x == 25) return ($d <= 0 && $i > 0 && $s > 0 && $c <= 0 && $i >= $s) ? 1 : 0;
        elseif($x == 26) return ($d <= 0 && $i > 0 && $s <= 0 && $c > 0 && $i >= $c) ? 1 : 0;
        elseif($x == 27) return ($d > 0 && $i > 0 && $c > 0 && $s < $c && $i && $d && $i >= $c && $c >= $d) ? 1 : 0;
        elseif($x == 28) return ($d <= 0 && $i > 0 && $s > 0 && $c < 0 && $i >= $c && $c >= $s) ? 1 : 0;
        elseif($x == 29) return ($d > 0 && $i <= 0 && $s > 0 && $c <= 0 && $s >= $d) ? 1 : 0;
        elseif($x == 30) return ($d <= 0 && $i > 0 && $s > 0 && $c <= 0 && $s >= $i) ? 1 : 0;
        elseif($x == 31) return ($d > 0 && $i > 0 && $s > 0 && $c < $i && $d && $s && $s >= $d && $d >= $i) ? 1 : 0;
        elseif($x == 32) return ($d > 0 && $i > 0 && $s > 0 && $c < $i && $d && $s && $s >= $i && $i >= $d) ? 1 : 0;
        elseif($x == 33) return ($d <= 0 && $i > 0 && $s > 0 && $c > 0 && $s >= $i && $i >= $c) ? 1 : 0;
        elseif($x == 34) return ($d > 0 && $i <= 0 && $s > 0 && $c > 0 && $s >= $c && $c >= $d) ? 1 : 0;
        elseif($x == 35) return ($d <= 0 && $i > 0 && $s > 0 && $c > 0 && $s >= $c && $c >= $i) ? 1 : 0;
        elseif($x == 36) return ($d <= 0 && $i > 0 && $s <= 0 && $c > 0 && $c >= $i) ? 1 : 0;
        elseif($x == 37) return ($d > 0 && $i > 0 && $c > 0 && $s < $c && $i && $d && $c >= $d && $d >= $i) ? 1 : 0;
        elseif($x == 38) return ($d > 0 && $s > 0 && $c > 0 && $i < $c && $s && $d && $c >= $d && $d >= $s) ? 1 : 0;
        elseif($x == 39) return ($d > 0 && $i > 0 && $c > 0 && $s < $c && $i && $d && $c >= $i && $i >= $d) ? 1 : 0;
        elseif($x == 40) return ($d > 0 && $s > 0 && $c > 0 && $i < $c && $s && $d && $c >= $s && $s >= $d) ? 1 : 0;
    }
}