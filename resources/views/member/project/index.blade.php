@extends('member-layouts/main')

@section('content')

<section>
    <div class="bg-theme-1 bg-header">
        <div class="container">
            <div class="d-md-flex align-items-center text-center text-md-start rounded-2 shadow-sm p-3 bg-glass-light">
                <img src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7" width="70" height="70" style="background:url('/assets/images/default/default-man.png'); background-size:70px; background-position:center; border:2px solid #fff" class="me-0 me-md-3 mb-3 mb-md-0 rounded-circle">
                <div>
                    <p class="fw-bold text-capitalize mb-0">{{ Auth::user()->name }}</p>
                    <p class="mb-0">Selamat datang di Tes Online Psikologi.<br>Kamu dapat melakukan tes online dengan memilih project yang ada di bawah ini.</p>
                </div>
            </div>
        </div>
    </div>
</section>
<div class="bg-white position-relative" style="top:-4.5rem; border-radius: 1rem 1rem 0 0">
    <div class="d-flex justify-content-center py-3">
        <div style="height:5px; width:5rem; background-color:#ced4da" class="rounded-1"></div>
    </div>

    <section class="container">
        @if(Session::get('message'))
        <div class="alert alert-warning alert-dismissible fade show mt-3" role="alert">
            <div class="alert-message">{{ Session::get('message') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif
        <div class="w-100 my-3 rounded-1" style="height:3px; background-color:transparent"></div>
        <div class="heading">
            <p class="m-0 fw-bold">Daftar Tes yang Belum Dikerjakan</p>
        </div>
        <div class="content">
            <div class="row justify-content-start">
                @foreach($project->tests()->orderBy('num_order','asc')->get() as $test)
                    @if(!in_array($test->id, $results))
                    <div class="col-md-6 d-flex align-items-stretch col-lg-3">
                        <a href="{{ route('member.test.index', ['path' => $test->code, 'project' => $project->id]) }}" class="btn btn-md btn-block btn-outline-dark rounded-2 d-flex border py-3 my-2 w-100">
                            <img width="60" class="me-3" src="{{ asset('assets/images/icon/'.$test->image.'.svg') }}">
                            <div class="text-start">
                                <p class="m-0 fw-bold">{{ $test->name }}</p>
                            </div>
                        </a>
                    </div>
                    @endif
                @endforeach
            </div>
    </section>
</div>

@endsection