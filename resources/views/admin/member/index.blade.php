@extends('faturhelper::layouts/admin/main')

@section('title', 'Kelola Member')

@section('content')

<div class="d-sm-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-2 mb-sm-0">Kelola Member</h1>
    <div class="btn-group">
        <!-- <a href="{{ route('admin.user.create') }}" class="btn btn-sm btn-primary"><i class="bi-plus me-1"></i> Tambah Member</a> -->
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
                                <th width="30" class="notexport"><input type="checkbox" class="form-check-input checkbox-all"></th>
                                <th>Nama</th>
                                <th width="80">Tanggal Lahir</th>
                                <th width="80">Jenis Kelamin</th>
                                <th width="80">Email</th>
                                <th width="80">No.HP</th>
                                <th width="80">Pekerjaan</th>
                                <th width="80">Jabatan</th>
                                <th width="80">Institusi</th>
                                <th width="80">Status</th>
                                <th width="80">Waktu Daftar</th>
                                <th width="40" class="notexport">Opsi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td align="center"><input type="checkbox" class="form-check-input checkbox-one" data-id="{{ $user->id }}"></td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->attribute && $user->attribute->birthdate != null ? date('d/m/Y', strtotime($user->attribute->birthdate)) : '-' }}</td>
                                <td>{{ $user->attribute && $user->attribute->gender != null ? gender($user->attribute->gender) : '-' }}</td>
                                <td>{{ $user->email }}</td>
								@if($user->attribute)
                                <td style="white-space: nowrap;">({{ $user->attribute->dial_code }}) {{ $user->attribute->phone_number }}</td>
								@else
								<td>-</td>
								@endif
                                <td>{{ $user->attribute && $user->attribute->occupation }}</td>
                                <td>{{ $user->attribute && $user->attribute->position }}</td>
                                <td>{{ $user->attribute && $user->attribute->institution }}</td>
                                <td><span class="badge {{ $user->status == 1 ? 'bg-success' : 'bg-danger' }}">{{ status($user->status) }}</span></td>
                                <td>
                                    <span class="d-none">{{ $user->created_at }}</span>
                                    {{ date('d/m/Y', strtotime($user->created_at)) }}<br>
                                    <small class="text-muted">{{ date('H:i:s', strtotime($user->created_at)) }} WIB</small>
                                </td>
                                <td align="center">
                                    <div class="btn-group">
                                        <!-- <a href="{{ route('admin.user.edit', ['id' => $user->id]) }}" class="btn btn-sm btn-warning" data-bs-toggle="tooltip" title="Edit"><i class="bi-pencil"></i></a> -->
                                        <a href="#" class="btn btn-sm btn-danger btn-delete" data-id="{{ $user->id }}" data-bs-toggle="tooltip" title="Hapus"><i class="bi-trash"></i></a>
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

<form class="form-delete d-none" method="post" action="{{ route('admin.member.delete') }}">
    @csrf
    <input type="hidden" name="id">
</form>

<form class="form-delete-bulk d-none" method="post" action="{{ route('admin.member.delete-bulk') }}">
    @csrf
    <input type="hidden" name="ids">
</form>

@endsection

@section('js')

<script type="text/javascript">
    // DataTable
    Spandiv.DataTable("#datatable", {
        buttons: true,
        deleteBulk: true,
        fixedColumns: {
            right: 1
        }
    });

    // Button Delete
    Spandiv.ButtonDelete(".btn-delete", ".form-delete");

    // Button Delete Bulk
    Spandiv.ButtonDeleteBulk(".btn-delete-bulk", ".form-delete-bulk");
</script>

@endsection

@section('css')

<style>
    #datatable tr td {vertical-align: top!important;}
</style>

@endsection
