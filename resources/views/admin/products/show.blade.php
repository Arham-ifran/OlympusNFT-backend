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
                <h1 class="m-0">Products Detail</h1>
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
                        <h3 class="card-title float-sm-left">Product Detail</h3>
                        <a class="btn btn-primary float-sm-right" href="{{ url('/admin/products') }}">Back</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="custom-detail-table">
                            <ul>
                                <li class="bold-heading-s">Store</li>
                                <li>
                                    @if(!empty($product->store))
                                    {{ $product->store->store_title }}
                                    @else
                                    NFT
                                    @endif
                                </li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">User</li>
                                <li>
                                    @if(!empty($product->user->username))
                                    {{ $product->user->username }}
                                    @endif
                                </li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">Category</li>
                                <li>
                                    @if(!empty($product->category->title))
                                    {{ $product->category->title }}
                                    @endif
                                </li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s" >Title</li>
                                <li>{{ $product->title ?? ''}}</li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">SubTitle</li>
                                <li>{{ $product->sub_title ?? ''}}</li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">Listing Tag</li>
                                <li>{{ $product->listing_tag ?? ''}}</li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">Description</li>
                                <li>{{ $product->description ?? ''}}</li>
                            </ul>

                            <ul>
                                <li class="bold-heading-s">Price In USD</li>
                                <li>${{ $product->price_usd ?? ''}}</li>
                            </ul>

                            <ul>
                                <li class="bold-heading-s">TokenID</li>
                                <li>{{ $product->productTokens() ?? ''}}</li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">Original Creator</li>
                                <li>{{ $product->original_creator ?? ''}}</li>
                            </ul>
                            <ul>
                                <li class="bold-heading-s">Token Metadata</li>
                                <li>{{ $product->token_metadata ?? ''}}</li>
                            </ul>
                        </div>
                        <div>
                            <h4 class="mt-3">Product Media Files</h4>
                            <hr>
                            <div class="row text-center media-files">
                                @foreach ($product->mediaFiles as $file)

                                @php $hash= mediaHash($file->ipfs_image_hash); @endphp
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
                                @endforeach

                            </div>
                        </div>
                        <hr>

                        <div>
                            <h4 class="mt-3">Product Reviews</h4>
                            <hr>

                            <table id="datatable" class="table table-bordered table-hover">
                                <thead>
                                    <ul>
                                        <th>User</th>
                                        <th>Title</th>
                                        <th>Review</th>
                                        <th>Rating</th>



                                    </ul>
                                </thead>
                                <tbody>

                                    @foreach ($product->reviews as $review)
                                    <ul>
                                        <li>{{ $review->reviewer_user->username ?? ''}}</li>
                                        <li>{{ $review->review_title ?? ''}}</li>
                                        <li>{{ $review->review ?? ''}}</li>
                                        <li>
                                            @for($i=0; $i<5; ++$i)
                                            @if($review->rating <= $i)
                                            <i style="color:#f4c006;" class="fas fa-star-o" aria-hidden="true"></i>
                                            @else
                                            <i style="color:#f4c006;" class="fas fa-star" aria-hidden="true"></i>
                                            @endif
                                            @endfor

                                        </li>

                                    </ul>
                                    @endforeach
                                </tbody>
                            </table>




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
<script src="{{ _asset('backend/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ _asset('backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ _asset('backend/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ _asset('backend/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#datatable').DataTable();
    });
</script>
@endsection
