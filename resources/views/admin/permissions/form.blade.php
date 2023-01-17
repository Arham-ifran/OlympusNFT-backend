@extends('admin.app')
@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Permissions</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/permissions') }}">Permissions</a></li>
                    <li class="breadcrumb-item active">{!!$action!!} Permission</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <!-- jquery validation -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                        {!!$action!!} Permission
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <form action="{!! url('/admin/permissions') !!}" method="post" name="settingsForm" id="settingsForm">
                        <div class="card-body">

                            @csrf
                            <input type="hidden" name="id" value="{!!@$permission->id!!}">
                            <input type="hidden" name="action" value="{!!$action!!}">

                            <div class="form-group">
                                <label for="name">Name</label>
                                <div class="col-sm-4">
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ $permission->name ?? '' }}" maxlength="255" aria-describedby="name" required />
                                    @error('name')
                                    <div id="name-error" class="invalid-feedback animated fadeInDown">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <input type="hidden" class="form-control" name="guard_name" value="admin"  />

                            @if(!$roles->isEmpty())
                            <div class="row">
                                <div class="card-header">
                                    <h5 class="card-title">Assign Permission to Roles</h5>
                                </div>
                            </div>
                            <div class="form-group">
                                @foreach ($roles as $role)
                                @if(isset($assignedRoles) && in_array($role->id, $assignedRoles))
                                @php
                                $check = 'checked';
                                @endphp
                                @else
                                @php
                                $check = '';
                                @endphp
                                @endif
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="roles" value="{{$role->id ?? '' }}" name="roles[]" {{$check}}>
                                    <label class="form-check-label" for="{{$role->name}}">
                                        {{ucfirst($role->name)}}
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @endif

                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">
                                Submit
                            </button>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
            <!--/.col (left) -->
            <!-- right column -->
            <div class="col-md-6"></div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

@endsection