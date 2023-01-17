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
                <h1 class="m-0">Order Detail</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item active">Order</li>
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
                        <h3 class="card-title float-sm-left">Order Detail</h3>
                        <a class="btn btn-primary float-sm-right" href="{{ url('/admin/products') }}">Back</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body custom-detail-table">


                        <ul>
                            <li class="bold-heading-s">Seller</li>
                            <li>
                                @if(!empty($order->seller->username))
                                {{ $order->seller->username }}
                                @endif
                            </li>
                        </ul>
                        <ul>
                            <li class="bold-heading-s">buyer</li>
                            <li>
                                @if(!empty($order->buyer->username))
                                {{ $order->buyer->username }}
                                @endif
                            </li>
                        </ul>
                        <ul>
                            <li class="bold-heading-s">Products Title</li>
                            <li>{{ $order->product->title ?? ''}}</li>
                        </ul>
                        <ul>
                            <li class="bold-heading-s">Is Auction Product</li>
                            <li>{{ ($order->is_auction_product ==1) ? 'Yes' : 'No'  }}</li>
                        </ul>
                        <ul>
                            <li class="bold-heading-s">Bid Price Usd</li>
                            <li>{{ number_format( $order->bid_price_usd,2,'.',',') ?? ''}}</li>
                        </ul>
                        <ul>
                            <li class="bold-heading-s">From Address</li>
                            <li>{{ $order->from_address ?? ''}}</li>
                        </ul>

                        <ul>
                            <li class="bold-heading-s">To Address</li>
                            <li>${{ $order->to_address ?? ''}}</li>
                        </ul>

                        <ul>
                            <li class="bold-heading-s">Transaction Hash</li>
                            <li>{{ $order->transaction_hash ?? ''}}</li>
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
