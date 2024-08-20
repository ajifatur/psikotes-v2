@extends('member-layouts/main')

@section('content')
<div class="bg-theme-1 bg-header">
	<div class="container">
		<div class="d-md-flex align-items-center justify-content-center text-center rounded-2 shadow-sm p-3 bg-glass-light">
			<h3 class="m-0 text-white">{{ $packet->name }}</h3>
		</div>
	</div>
</div>
<div class="container main-container">
	<div class="row" style="margin-bottom:100px">
	    <div class="col-12">
		    <form id="form" method="post" action="{{ route('member.test.store', ['path' => $path]) }}">
        		@csrf
			    <input type="hidden" name="path" value="{{ $path }}">
			    <input type="hidden" name="project_id" value="{{ Request::query('project') }}">
			    <input type="hidden" name="packet_id" value="{{ $packet->id }}">
			    <input type="hidden" name="test_id" value="{{ $test->id }}">
		        <input type="hidden" id="D" name="Dm">
            	<input type="hidden" id="I" name="Im">
            	<input type="hidden" id="S" name="Sm">
            	<input type="hidden" id="C" name="Cm">
            	<input type="hidden" id="B" name="Bm">
            	<input type="hidden" id="K" name="Dl">
            	<input type="hidden" id="O" name="Il">
            	<input type="hidden" id="L" name="Sl">
            	<input type="hidden" id="E" name="Cl">
            	<input type="hidden" id="H" name="Bl">
        		<div class="row">
        			@php
        				$totalsoal = 0;
        			@endphp
        			@foreach($questions as $question)
        			<div class="col-lg-6" style="margin-top: 20px;">
        				<div class="card soal rounded-1">
                            <div class="card-header bg-transparent">
                                <span class="num fw-bold" data-id="{{ $question->number }}"><i class="fad fa-edit"></i> Soal {{ $question->number }}</span>
                            </div>
        					<div class="card-body">
        						<table width="100%">
        							<tr>
        								<td><i style="color:#56DB28" class="bi-hand-thumbs-up-fill"></i></td>
        								<td><i style="color:#E3451E" class="bi-hand-thumbs-down-fill"></i></td>
        								<td><h6 class="card-title" style="font-weight: bold;">Gambaran Diri</h6></td>
        							</tr>
        							@php
										$huruf = ['A', 'B', 'C' , 'D'];
										$num = -1;
										$totalsoal = $totalsoal+1;
										$json = json_decode($question->description);
									@endphp
									@foreach($json as $pilihan)
										@php
											$num++;
											$key = $huruf[$num];
										@endphp
										<tr>
											<td width="30" valign="top">
												<input type="radio" name="y[{{$question->number}}]" id="{{$pilihan->keym}}m" class="form-check-input {{$question->number}}-y" value="{{$key}}">
											</td>
											<td width="30" valign="top">
												<input type="radio" name="n[{{$question->number}}]" id="{{$pilihan->keyl}}l" class="form-check-input {{$question->number}}-n" value="{{$key}}">
											</td>
											<td><p>{{$pilihan->pilihan}}</p></td>
										</tr>
        							@endforeach
        						</table>
        					</div>
        				</div>
        			</div>
        			@endforeach
        		</div>
        	</form>
    	</div>
	</div>
	<nav class="navbar navbar-expand-lg fixed-bottom navbar-light bg-white shadow">
		<div class="container">
			<ul class="navbar nav ms-auto">
				<li class="nav-item">
					<span id="answered">0</span>/<span id="total"></span> Soal Terjawab
				</li>
				<li class="nav-item ms-3">
					<a href="#" class="text-secondary" data-bs-toggle="modal" data-bs-target="#tutorialModal" title="Tutorial"><i class="fad fa-question-circle" style="font-size: 1.5rem"></i></a>
				</li>
				<li class="nav-item ms-3">
					<button class="btn btn-md btn-primary text-uppercase " id="btn-submit" disabled>Submit</button>
				</li>
			</ul>
		</div>
	</nav>
	<div class="modal fade" id="tutorialModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
	    	<div class="modal-content">
	      		<div class="modal-header">
	        		<h5 class="modal-title" id="exampleModalLabel">
                        <span class="bg-warning rounded-1 text-center px-3 py-2 me-2"><i class="fad fa-lightbulb-on" aria-hidden="true"></i></span> 
                        Tutorial Tes
                    </h5>
	        		<button type="button" class="btn" data-bs-dismiss="modal"><i class="fad fa-times"></i></button>
	      		</div>
		      	<div class="modal-body">
    				<p>Tes ini terdiri dari 24 Soal dan 2 jawaban setiap soal. Jawab secara jujur dan spontan. Estimasi waktu pengerjaan adalah 5-10 menit</p>
    				<ul>
    					<li>Pelajari semua jawaban pada setiap pilihan</li>
    					<li>
    						Pilih satu jawaban yang
    						<strong>paling mendekati diri kamu</strong>
    						(
    							<i style="color:#56DB28" class="bi-hand-thumbs-up-fill"></i>
    						)
    					</li>
    					<li>
    						Pilih satu jawaban yang
    						<strong>paling tidak mendekati diri kamu</strong>
    						( 
    							<i style="color:#E3451E" class="bi-hand-thumbs-down-fill"></i>
    						)
    					</li>
    				</ul><br>
    				<p>
    					Pada setiap soal harus memiliki jawaban
    					<ins>satu</ins>
    					<strong>paling mendekati diri kamu</strong>
    					dan hanya
    					<ins>satu</ins>
    					<strong>paling tidak mendekati diri kamu</strong>.
    				</p>
    				<p>
    					Terkadang akan sedikit sulit untuk memutuskan jawaban yang terbaik. Ingat, tidak ada jawaban yang benar atau salah dalam tes ini.
    				</p>
		      	</div>
	      		<div class="modal-footer">
	        		<button type="button" class="btn btn-primary text-uppercase " data-bs-dismiss="modal">MENGERTI</button>
	      		</div>
	    	</div>
	  	</div>
	</div>
</div>
@endsection

@section('js')
<script type="text/javascript">
	$(document).ready(function(){
		$("#tutorialModal").modal("toggle");
	    totalQuestion();
	});
    // Change value
	$(document).on("change", "input[type=radio]", function(){
		var className = $(this).attr("class");
		var currentNumber = className.split("-")[0];
		var currentCode = className.split("-")[1];
		var oppositeCode = currentCode == "y" ? "n" : "y";
		var currentValue = $(this).val();
		var oppositeValue = $("." + currentNumber + "-" + oppositeCode + ":checked").val();
		// Detect if one question has same answer
		if(currentValue == oppositeValue){
			$("." + currentNumber + "-" + oppositeCode + ":checked").prop("checked", false);
			oppositeValue = $("." + currentNumber + "-" + oppositeCode + ":checked").val();
		}
		// Count answer
		document.getElementById('D').value = $('#Dm:checked').length;
		document.getElementById('I').value = $('#Im:checked').length;
		document.getElementById('S').value = $('#Sm:checked').length;
		document.getElementById('C').value = $('#Cm:checked').length;
		document.getElementById('B').value = $('#Bm:checked').length;
		document.getElementById('K').value = $('#Dl:checked').length;
		document.getElementById('O').value = $('#Il:checked').length;
		document.getElementById('L').value = $('#Sl:checked').length;
		document.getElementById('E').value = $('#Cl:checked').length;
		document.getElementById('H').value = $('#Bl:checked').length;
		// Count answered question
		countAnswered();
		// Enable submit button
		var totalQuestion = document.getElementById('total').innerHTML;
		countAnswered() >= totalQuestion ? $("#btn-submit").removeAttr("disabled") : $("#btn-submit").attr("disabled", "disabled");
	});
	// Count answered question
	function countAnswered(){
		var total = 0;
		$(".num").each(function(key, elem){
			var id = $(elem).data("id");
			var mValue = $("." + id + "-y:checked").val();
			var lValue = $("." + id + "-n:checked").val();
			mValue != undefined && lValue != undefined ? total++ : "";
		});
		$("#answered").text(total);
		return total;
	}
	// Total question
	function totalQuestion(){
		var totalRadio = $("input[type=radio]").length;
		var pointPerQuestion = 4;
		var total = totalRadio / pointPerQuestion / 2;
		$("#total").text(total);
		return total;
	}
</script>
@endsection

@section('css')
<style type="text/css">
	.modal .modal-body {font-size: 14px;}
	.table {margin-bottom: 0;}
</style>
@endsection