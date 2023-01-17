@extends('admin.app')
@section('content')
<!-- DataTables -->
<!-- DataTables -->
<link rel="stylesheet" href="{{ _asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ _asset('backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Ads Products Detail</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item active">Products</li>
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
                        <h3 class="card-title float-sm-left">Ads Product Detail</h3>
                        <a class="btn btn-primary float-sm-right" href="{{ url('/admin/ads') }}">Back</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="custom-detail-table">
                            <ul>
                                <li>Ad Title </li>
                                <li>{{ !empty($ads->title) ? $ads->title : ''}} </li>
                            </ul>
                            <ul>
                                <li>Start Date </li>
                                <li>{{ !empty($ads->start_date) ? Carbon\Carbon::parse($ads->start_date) : ''}} </li>
                            </ul>
                            <ul>
                                <li>End Date </li>
                                <li>{{ !empty($ads->end_date) ? Carbon\Carbon::parse($ads->end_date) : ''}} </li>
                            </ul>
                            <ul>
                                <li>Impression </li>
                                <li>{{ $ads->impression}} </li>
                            </ul>
                            <ul>
                                <li>Bid Type </li>
                                <li>{{ ($ads->bid_type == 1)?'Set My Own Bid' : 'Automatic'}} </li>
                            </ul>
                            <ul>
                                <li>Cpc </li>
                                <li>{{ $ads->cpc }} </li>
                            </ul>
                            <ul>
                                <li>Total Budget </li>
                                <li>${{ $ads->total_budget }} </li>
                            </ul>
                            <ul>
                                <li>Total Spent </li>
                                <li>${{ $ads->total_spent }} </li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="mt-3">AD Products</h4>
                            <hr>
                            <div class="row ml-5 text-center  media-files">

                                @foreach ($ads->products as $product)
                                @php  $main_media_file= $product->mediaFiles->where('is_token_image', 1)->first()->ipfs_image_hash @endphp
                                @if($main_media_file)
                                @php $hash= mediaHash($main_media_file); @endphp
                                @if($hash[0]=="image")
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <img src="{{$hash[1]}}" class="image-display " />
                                        </div>
                                    </div>
                                </div>
                                @elseif($hash[0]=="video")
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <video class="video-display" controls>
                                                <source src="{{$hash[1]}}">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                    </div>
                                </div>

                                @else
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        <div class="col-sm-12">
                                            <a href="{{$hash[1]}}">{{$hash[1]}}</a>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @endif
                                <p><a href="{{url("admin/products")}}/{{encode($product->id)}}" >{{$product->title}}</a></p>
                                @endforeach

                            </div>
                        </div>

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
