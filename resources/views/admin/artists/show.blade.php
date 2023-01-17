@extends('admin.app')
@section('content')

<!-- DataTables -->
<link rel="stylesheet" href="{{ _asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ _asset('backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Artist Detail</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item active">Artist</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title float-sm-left">Artist Detail</h3>
                        <a class="btn btn-primary float-sm-right" href="{{ url('/admin/artists') }}">Back</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="custom-detail-table">
                            <ul>
                                <li>UserName </li>
                                <li>{{ $artist->username ?? ''}} </li>
                            </ul>
                            <ul>
                                <li>Email </li>
                                <li>{{$artist->email ?? ''}} </li>
                            </ul>
                            <ul>
                            <li>Wallet Address </li>
                            <li>{{$artist->wallet_address ?? ''}} </li>
                            </ul>
                            <ul>
                                <li>Total Product </li>
                                <li>{{count($artist->products) ?? ''}} </li>
                            </ul>
                            <ul>
                                <li>Twitter </li>
                                <li><a href="{{$artist->twitter}}" target="_blank">{{$artist->twitter ?? ''}}</a> </li>
                            </ul>
                            <ul>
                                <li>Instagram </li>
                                <li><a href="{{$artist->instagram}}" target="_blank">{{$artist->instagram ?? ''}}</a> </li>
                            </ul>
                            <ul>
                                <li>Youtube </li>
                                <li><a href="{{$artist->youtube}}" target="_blank">{{$artist->youtube ?? ''}}</a> </li>
                            </ul>
                            <ul>
                                <li>Facebook </li>
                                <li><a href="{{$artist->facebook}}" target="_blank">{{$artist->facebook ?? ''}}</a> </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="mt-3">User Profile Image And Banner Image</h4>
                            <hr>
                            <div class="row text-center media-files">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <img src="{!! checkImage(asset('storage/uploads/users/'.$artist->photo)) !!}" class="image-display " id="image" style="width:  150px;border:  1px solid #ccc;" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <img src="{!! checkImage(asset('storage/uploads/users/'.$artist->banner_image)) !!}" class="image-display " id="image" style="width:  150px;border:  1px solid #ccc;" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                            </div>
                            <!-- /.card-body -->
                        </div>
                        <!-- /.card-body -->
                    </div>

                    <!-- /.card -->

                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
</section>


@endsection
