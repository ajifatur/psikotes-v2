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

class DISC40Controller extends Controller
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
        foreach($questions as $question) {
            $question->description = json_decode($question->description, true);
        }

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
        // Get the packet and questions
        $packet = Packet::where('test_id','=',$request->test_id)->where('status','=',1)->first();
        $questions = $packet ? $packet->questions()->orderBy('number','asc')->get() : [];
        
        // Declare variables
        $m = $request->get('m');
        $l = $request->get('l');
        $disc = array('D', 'I', 'S','C');
        $disc_m = array();
        $disc_l = array();
        $disc_score_m = array();
        $disc_score_l = array();
        foreach($questions as $question) {
            $json = json_decode($question->description, true);
            array_push($disc_m, $json[0]['disc'][$m[$question->number]]);
            array_push($disc_l, $json[0]['disc'][$l[$question->number]]);
        }

        // MOST dan LEAST
        $array_count_m = array_count_values($disc_m);
        $array_count_l = array_count_values($disc_l);
        foreach($disc as $letter){
            $disc_score_m[$letter] = array_key_exists($letter, $array_count_m) ? self::discScoringM($array_count_m[$letter]) : 0;
            $disc_score_l[$letter] = array_key_exists($letter, $array_count_l) ? self::discScoringL($array_count_l[$letter]) : 0;
        }
        
        // Convert DISC score to JSON
        $array = array('M' => $disc_score_m, 'L' => $disc_score_l);
        $array['answers']['m'] = $request->m;
        $array['answers']['l'] = $request->l;

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
        
        // Set the result
        $disc = array('D', 'I', 'S','C');
        $m_score = $result->result['M'];
        $l_score = $result->result['L'];

        // Set the ranking
        $disc_score_m = self::sortScore($m_score);
        $disc_score_l = self::sortScore($l_score);

        // Set the code
        $code_m = self::setCode($disc_score_m);
        $code_l = self::setCode($disc_score_l);

        // Set the description
        $description = Description::where('packet_id','=',$result->packet_id)->first();
        $description->description = json_decode($description->description, true);
        $description_code = substr($code_l[0],1,1);
        switch($description_code) {
            case 'D':
                $description_result = $description->description[self::searchIndex($description->description, "disc", "D")]["keterangan"];
            break;
            case 'I':
                $description_result = $description->description[self::searchIndex($description->description, "disc", "I")]["keterangan"];
            break;
            case 'S':
                $description_result = $description->description[self::searchIndex($description->description, "disc", "S")]["keterangan"];
            break;
            case 'C':
                $description_result = $description->description[self::searchIndex($description->description, "disc", "C")]["keterangan"];
            break;
        }

        // View
        return view('admin/result/disc-40/detail', [
            'result' => $result,
            'disc' => $disc,
            'disc_score_m' => $disc_score_m,
            'disc_score_l' => $disc_score_l,
            'code_m' => $code_m,
            'code_l' => $code_l,
            'description_code' => $description_code,
            'description_result' => $description_result,
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
        // DISC
        $disc = array('D', 'I', 'S','C');

        // Set the description
        $description = Description::where('packet_id','=',$request->packet_id)->first();
        $description->description = json_decode($description->description, true);
        $description_code = $request->description_code;
        switch($description_code){
            case 'D':
                $desc = $description->description[self::searchIndex($description->description, "disc", "D")]["keterangan"];
            break;
            case 'I':
                $desc = $description->description[self::searchIndex($description->description, "disc", "I")]["keterangan"];
            break;
            case 'S':
                $desc = $description->description[self::searchIndex($description->description, "disc", "S")]["keterangan"];
            break;
            case 'C':
                $desc = $description->description[self::searchIndex($description->description, "disc", "C")]["keterangan"];
            break;
        }
        
        // PDF
        $pdf = PDF::loadview('admin/result/disc-40/pdf', [
            'mostChartImage' => $request->mostChartImage,
            'leastChartImage' => $request->leastChartImage,
            'desc' => $desc,
            'name' => $request->name,
            'age' => $request->age,
            'gender' => $request->gender,
            'test' => $request->test,
            'disc_score_m' => json_decode($request->disc_score_m, true),
            'disc_score_l' => json_decode($request->disc_score_l, true),
            'most' => $request->most,
            'least' => $request->least,
            'disc' => $disc,
        ]);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream($request->name . '_' . $request->test . '.pdf');
    }

    /**
     * Sort score.
     *
     * @param  array  $array
     * @return array  $ordered_array
     */
    public static function sortScore($array)
    {
        $ordered_array = $array;
        arsort($ordered_array);
        $i = 1;
        $last_value = '';
        foreach($ordered_array as $ordered_key=>$ordered_value){
            $ordered_array[$ordered_key] = array();
            $ordered_array[$ordered_key]['rank'] = $ordered_value == $last_value ? ($i-1) : $i;
            $ordered_array[$ordered_key]['score'] = $ordered_value;
            $last_value = $ordered_value;
            $i++;
        }
        return $ordered_array;
    }

    /**
     * Set code.
     *
     * @param  array  $array
     * @return array  $new_array
     */
    public static function setCode($array)
    {
        $new_array = array();
        $i = 1;
        while($i<=4){
            foreach($array as $key=>$value){
                if($array[$key]['rank'] == $i){
                    if($array[$key]['score'] < 50){
                        $new_value = "L".$key;
                        array_push($new_array, $new_value);
                    }
                    else{
                        $new_value = "H".$key;
                        array_push($new_array, $new_value);
                    }
                }
            }
            $i++;
        }
        return $new_array;
    }

    /**
     * Search index.
     *
     * @param  array  $array
     * @param  int    $key
     * @param  string $value
     * @return int  $i
     */
    public static function searchIndex($array, $key, $value)
    {
        for($i = 0; $i < count($array); $i++){
            if($array[$i][$key] == $value){
                return $i;
            }
        }
    }
    
    /**
     * DISC Scoring M
     *
     * @param  int $number
     * @return int $score
     */
    public static function discScoringM($number) {
        $score = round(50 * pow(2, log($number / 10, 4)));
        return $score;
    }
    
    /**
     * DISC Scoring L
     *
     * @param  int $number
     * @return int $score
     */
    public static function discScoringL($number) {
        $score = 100 - round(50 * pow(2, log($number / 10, 4)));
        return $score;
    }
}