@extends('admin.app')
@section('content')

<!-- DataTables -->
<link rel="stylesheet" href="{{ _asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ _asset('backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Store Detail</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item active">Store</li>
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
                        <h3 class="card-title float-sm-left">Store Detail</h3>
                        <a class="btn btn-primary float-sm-right" href="{{ url('/admin/stores') }}">Back</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body custom-detail-table">


                        <ul>
                            <li class="bold-heading-s">user </li>
                            <li>{{ $store->user->username ?? ''}} </li>
                        </ul>
                        <ul>
                            <li class="bold-heading-s">Category </li>
                            <li>{{$store->storeCategory->title ?? ''}} </li>
                        </ul>
                        <ul>
                        <li class="bold-heading-s">Store Title </li>
                        <li>{{$store->store_title ?? ''}} </li>
                        </ul>
                        <ul>
                            <li class="bold-heading-s">Store Sub Title </li>
                            <li>{{$store->sub_title ?? ''}} </li>
                        </ul>
                        <ul>
                            <li class="bold-heading-s">Store Tags </li>
                            <li>{{$store->store_tags ?? ''}} </li>
                        </ul>
                        <ul>
                            <li class="bold-heading-s">Total Products </li>
                            <li>{{count($store->products) ?? ''}} </li>
                        </ul>
                        <ul>
                            <li class="bold-heading-s">Store Description </li>
                            <li>{{$store->description ?? ''}} </li>
                        </ul>
                        <ul>
                            <li class="bold-heading-s">URL </li>
                            <li><a href="{{ env('FRONT_BASE_URL').$store->slug}}" target="blank">{{ env('FRONT_BASE_URL').$store->slug}}</a> </li>
                        </ul>
                        <ul>
                            <li class="bold-heading-s">Image </li>
                            <li> <img src="{!! checkImage(asset('storage/uploads/stores/'.$store->id.'/'.$store->photo)) !!}" class="image-display " id="image" style="width:  150px;border:  1px solid #ccc;" /> </li>
                        </ul>


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
