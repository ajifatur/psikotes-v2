@extends('member-layouts/main')

@section('content')

<div class="bg-theme-1 bg-header">
	<div class="container">
		<div class="text-center rounded-2 shadow-sm p-3 bg-glass-light">
			<h3 class="m-0 text-white">{{ $packet->name }}</h3>
			<hr>
			<p class="m-0"><a href="#" class="text-white" data-bs-toggle="modal" data-bs-target="#tutorialModal"><u>Lihat Petunjuk Pengerjaan Disini</u></a></p>
		</div>
	</div>
</div>

<input type="hidden" id="user_id" value="{{ Auth::user()->id }}">
<input type="hidden" id="project_id" value="{{ Request::query('project') }}">

<div class="container main-container">
	<div id="question" class="row" style="margin-bottom: 100px;">
		<!-- Button Navigation -->
		<div class="col-md-3 mb-3 mb-md-0">
			<div class="card">
				<div class="card-header fw-bold text-center">Navigasi Soal</div>
				<div class="card-body"></div>
			</div>
		</div>

		<!-- Card Question -->
		<div class="col-md-9">
			<div class="card card-question">
				<div class="card-header">
					<span class="fw-bold"><i class="fa fa-edit"></i> Soal</span>
				</div>
				<div class="card-body"></div>
				<div class="card-footer bg-white text-center"></div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('js')

<script src="{{ asset('js/app.js') }}" defer></script>

@endsection

@section('css')
<style type="text/css">
	.modal .modal-body {font-size: 14px; overflow-y: auto; max-height: calc(100vh - 200px);}
	.table {margin-bottom: 0;}
	.radio-image {margin-bottom: 1rem; padding-left: 0;}
	.radio-image label {cursor: pointer;}
	.radio-image label.border-primary {border-color: var(--color-1)!important; border-width: 2px!important;}
	/* #form {filter: blur(3px);} */
	.modal-auth .card-question, .modal-auth #nav-button {filter: blur(3px);}
	.modal-open .card-question .card-body {filter: blur(3px);}
	#question .btn:focus {box-shadow: none;}
	#nav-button {text-align: center;}
	#nav-button .btn {font-size: .75rem; width: 3.75rem; margin: .25rem;}
</style>
@endsection