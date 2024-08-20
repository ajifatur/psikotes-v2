@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola Project')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Kelola Project</h1>
    <div class="btn-group">
        <a href="{{ route('admin.project.create') }}" class="btn btn-sm btn-primary"><i class="bi-plus me-1"></i> Tambah Project</a>
    </div>
</div>
<div class="row">
	<div class="col-12">
		<div class="card">
            <div class="card-body">
                @if(Session::get('message'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="alert-message">{{ Session::get('message') }}</div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <div class="table-responsive">
                    <table class="table table-sm table-hover table-bordered" id="datatable">
                        <thead class="bg-light">
                            <tr>
                                <th width="30"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                <th>Nama</th>
                                <th width="100">Tes</th>
                                <th width="80">Mulai Tanggal</th>
                                <th width="80">Sampai Tanggal</th>
                                @if(Auth::user()->role_id == role('super-admin'))
                                <th width="120">HRD</th>
                                @endif
                                <th width="60">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($projects as $project)
                            <tr>
                                <td align="center"><input type="checkbox" class="form-check-input checkbox-one"></td>
                                <td>
                                    {{ $project->name }}
                                    <div class="text-muted"><strong>Token:</strong> {{ $project->token }}</div>
                                </td>
                                <td>
                                    @foreach($project->tests()->orderBy('num_order','asc')->get() as $key=>$test)
                                        {{ $test->name }}
                                        @if($key < count($project->tests)-1)
                                        <hr class="my-1">
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    <span class="d-none">{{ $project->date_from }}</span>
                                    {{ date('d/m/Y', strtotime($project->date_from)) }}
                                    <br>
                                    <small class="text-muted">{{ date('H:i', strtotime($project->date_from)) }} WIB</small>
                                </td>
                                <td>
                                    <span class="d-none">{{ $project->date_to }}</span>
                                    {{ date('d/m/Y', strtotime($project->date_to)) }}
                                    <br>
                                    <small class="text-muted">{{ date('H:i', strtotime($project->date_to)) }} WIB</small>
                                </td>
                                @if(Auth::user()->role_id == role('super-admin'))
                                <td>{{ $project->user->name }}</td>
                                @endif
                                <td align="center">
                                    <div class="btn-group">
                                        <a href="{{ route('admin.project.edit', ['id' => $project->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a>
                                        <a href="#" class="btn btn-sm btn-danger btn-delete" data-id="{{ $project->id }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
		</div>
	</div>
</div>

<form class="form-delete d-none" method="post" action="{{ route('admin.project.delete') }}">
    @csrf
    <input type="hidden" name="id">
</form>

@endsection

@section('js')

<script type="text/javascript">
    // DataTable
    Spandiv.DataTable("#datatable");

    // Button Delete
    Spandiv.ButtonDelete(".btn-delete", ".form-delete");
</script>

@endsection

@section('css')

<style>
    #datatable tr td {vertical-align: top!important;}
</style>

@endsection