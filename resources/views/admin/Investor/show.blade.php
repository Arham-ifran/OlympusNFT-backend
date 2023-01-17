@extends('admin.app')
@section('content')

<!-- DataTables -->
<link rel="stylesheet" href="{{ _asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ _asset('backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Investor Detail</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item active">Investor</li>
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
                        <h3 class="card-title float-sm-left">Investor Detail</h3>
                        <a class="btn btn-primary float-sm-right" href="{{ url('/admin/investors') }}">Back</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="custom-detail-table">
                            <ul>
                                <li class="bold-heading-s">UserName </li>
                                <li>{{ $investor->username  ?? ''}} </li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">Email </li>
                                <li>{{$investor->email ?? ''}} </li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">Wallet Address </li>
                                <li>{{$investor->wallet_address ?? ''}} </li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">Total Product </li>
                                <li>{{count($investor->products) ?? ''}} </li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">Twitter </li>
                                <li><a href="{{$investor->twitter}}" target="_blank">{{$investor->twitter ?? ''}}</a> </li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">Instagram </li>
                                <li><a href="{{$investor->instagram}}" target="_blank">{{$investor->instagram ?? ''}}</a> </li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">Youtube </li>
                                <li><a href="{{$investor->youtube}}" target="_blank">{{$investor->youtube ?? ''}}</a> </li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">Facebook </li>
                                <li><a href="{{$investor->facebook}}" target="_blank">{{$investor->facebook ?? ''}}</a> </li>
                            </ul>

                        </div>
                        <div>
                        <h4 class="mt-3">User Profile Image And Banner Image</h4>
                            <hr>
                            <div class="row text-center media-files">
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <img src="{!! checkImage(asset('storage/uploads/users/'.$investor->photo)) !!}" class="image-display " id="image" style="width:  150px;border:  1px solid #ccc;" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <img src="{!! checkImage(asset('storage/uploads/users/'.$investor->banner_image)) !!}" class="image-display " id="image" style="width:  150px;border:  1px solid #ccc;" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
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
