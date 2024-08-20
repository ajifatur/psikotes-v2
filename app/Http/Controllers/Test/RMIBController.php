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

class RMIBController extends Controller
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
        
        // Answers
        $array = [];
        $array['answers'] = $request->score;
        $array['occupations'] = $request->occupations;

        // Sort array answers by key
        foreach($array['answers'] as $key=>$answer) {
            ksort($array['answers'][$key]);
        }

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

        // Set the description
        $description = Description::where('packet_id','=',$result->packet_id)->first();
        $description->description = json_decode($description->description, true);

        // Get the questions
        $questions = $result->packet->questions()->orderBy('number','asc')->get();

        // Set categories
        $categories = ['Out','Me','Comp','Sci','Prs','Aesth','Lit','Mus','So. Se','Cler','Prac','Med'];

        // Set letters
        $letters = ['A','B','C','D','E','F','G','H','I'];

        // Set the sheet and sum
        $sheets = [];
        $sums = [];
        foreach($categories as $keyc=>$category) {
            $sums[$keyc] = 0;
            $i = $keyc;
            foreach($letters as $keyl=>$letter) {
                $sheets[$keyc][] = $result->result['answers'][($keyl+1)][$i];
                $sums[$keyc] += $result->result['answers'][($keyl+1)][$i];
                $i--;
                $i = $i < 0 ? 11 : $i;
            }
        }

        // Set the category ranks by ordered sums
        $ordered_sums = $sums;
        sort($ordered_sums);
		$occurences = array_count_values($sums);
        $category_ranks = [];
        foreach($sums as $keys=>$sum) {
            foreach($ordered_sums as $keyo=>$ordered_sum) {
                if($sum === $ordered_sum) {
					if($occurences[$sum] <= 1)
                    	$category_ranks[$keys] = $keyo + 1;
					else
                    	$category_ranks[$keys] = $keyo;
				}
            }
        }

        // Get interests
        $interests = [];
        foreach($category_ranks as $keyc=>$category_rank) {
            if($category_rank <= 3) {
                foreach($description->description as $note) {
                    if($note['code'] == $categories[$keyc]) {
						if(!array_key_exists($category_rank, $interests))
                        	$interests[$category_rank] = $note;
						else
                        	$interests[$category_rank + 1] = $note;
					}
                }
            }
        }
        ksort($interests);

        // View
        return view('admin/result/rmib/detail', [
            'result' => $result,
            // 'keterangan' => $keterangan,
            'questions' => $questions,
            'categories' => $categories,
            'letters' => $letters,
            'sheets' => $sheets,
            'sums' => $sums,
            'category_ranks' => $category_ranks,
            'interests' => $interests,
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
        // Set the result
        $result = Result::find($request->id);
        $result->result = json_decode($result->result, true);
        
        // Set the description
        $description = Description::where('packet_id','=',$result->packet_id)->first();
        $description->description = json_decode($description->description, true);
		
        // Get the questions
        $questions = $result->packet->questions()->orderBy('number','asc')->get();

        // Get the questions     
		/*
        $paket = PaketSoal::where('id_tes','=',$result->id_tes)->where('status','=',1)->first();
        $questions = Soal::join('paket_soal','soal.id_paket','=','paket_soal.id_paket')->where('soal.id_paket','=',$paket->id_paket)->orderBy('nomor','asc')->get();
		*/

        // Set categories
        $categories = ['Out','Me','Comp','Sci','Prs','Aesth','Lit','Mus','So. Se','Cler','Prac','Med'];

        // Set letters
        $letters = ['A','B','C','D','E','F','G','H','I'];

        // Set the sheet and sum
        $sheets = [];
        $sums = [];
        foreach($categories as $keyc=>$category) {
            $sums[$keyc] = 0;
            $i = $keyc;
            foreach($letters as $keyl=>$letter) {
                $sheets[$keyc][] = $result->result['answers'][($keyl+1)][$i];
                $sums[$keyc] += $result->result['answers'][($keyl+1)][$i];
                $i--;
                $i = $i < 0 ? 11 : $i;
            }
        }

        // Set the category ranks by ordered sums
        $ordered_sums = $sums;
        sort($ordered_sums);
		$occurences = array_count_values($sums);
        $category_ranks = [];
        foreach($sums as $keys=>$sum) {
            foreach($ordered_sums as $keyo=>$ordered_sum) {
                if($sum === $ordered_sum) {
					if($occurences[$sum] <= 1)
                    	$category_ranks[$keys] = $keyo + 1;
					else
                    	$category_ranks[$keys] = $keyo;
				}
            }
        }

        // Get interests
        $interests = [];
        foreach($category_ranks as $keyc=>$category_rank) {
            if($category_rank <= 3) {
                foreach($description->description as $note) {
                    if($note['code'] == $categories[$keyc]) {
						if(!array_key_exists($category_rank, $interests))
                        	$interests[$category_rank] = $note;
						else
                        	$interests[$category_rank + 1] = $note;
					}
                }
            }
        }
        ksort($interests);
        
        // PDF
        $pdf = PDF::loadview('admin/result/rmib/pdf', [
            'result' => $result,
            'image' => $request->image,
            'name' => $request->name,
            'age' => $request->age,
            'gender' => $request->gender,
            'position' => $request->position,
            'test' => $request->test,
            'questions' => $questions,
            'categories' => $categories,
            'letters' => $letters,
            'sheets' => $sheets,
            'sums' => $sums,
            'category_ranks' => $category_ranks,
            'interests' => $interests,
        ]);
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->stream($request->name . '_' . $request->test . '.pdf');
    }
}