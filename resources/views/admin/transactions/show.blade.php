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
                <h1 class="m-0">Transaction Detail</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item active">Transaction</li>
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
                        <h3 class="card-title float-sm-left">Transaction Detail</h3>
                        <a class="btn btn-primary float-sm-right" href="{{ url('/admin/transactions') }}">Back</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body">
                        <div class="custom-detail-table">
                            <ul>
                                <li>User</li>
                                <li>
                                    @if(!empty($transaction->user->username))
                                    {{ $transaction->user->username }}
                                    @endif
                                </li>
                            </ul>

                            <ul>
                                <li>Type</li>
                                <li>

                                    {{ ($transaction->type ==1) ? 'Mint' : 'Transfer' }}

                                </li>
                            </ul>
                            <ul>
                                <li>Transaction of</li>
                                <li>
                                    @php
                                    $transaction_of="";
                                    if($transaction->transaction_of ===0 )
                                    $transaction_of="AD";
                                    elseif($transaction->transaction_of ==1)
                                    $transaction_of="Product";
                                    elseif($transaction->transaction_of ==2)
                                    $transaction_of="Order";
                                  
                                    @endphp
                                    {{ $transaction_of}}

                                </li>
                            </ul>
                            @if ($transaction->transaction_of === 0)
                            <ul>
                                <li>AD</li>
                                <li>
                                    <a href="{{url('admin/ads/'.encode($transaction->ad->id).'')}}">{{ $transaction->ad->title }}</a>
                                </li>
                            </ul>
                            @elseif ($transaction->transaction_of ==1)
                            <ul>
                                <li>Product</li>
                                <li><a href="{{url('admin/products/'.encode($transaction->product->id).'')}}">{{ $transaction->product->title }}</a></li>
                            </ul>
                            @elseif($transaction->transaction_of ==2)
                            <ul>
                                <li>Order ID</li>
                                <li><a href="{{url('admin/orders/'.encode($transaction->order->id).'')}}">{{ $transaction->order->id }}</a></li>
                            </ul>

                            @endif

                            @if ($transaction->type ==1)
                            <ul>
                                <li>Earned Price</li>
                                <li>${{ $transaction->earned_price ?? ''}}</li>
                            </ul>
                            @elseif ($transaction->type ==0)
                            <ul>
                                <li>Paid Price</li>
                                <li>${{ $transaction->paid_price ?? ''}}</li>
                            </ul>
                            @endif


                            <ul>
                                <li>From Address</li>
                                <li>{{ $transaction->from_address ?? ''}}</li>
                            </ul>

                            <ul>
                                <li>To Address</li>
                                <li>{{ $transaction->to_address ?? ''}}</li>
                            </ul>

                            <ul>
                                <li>Transaction Hash</li>
                                <li>{{ $transaction->transaction_hash ?? ''}}</li>
                            </ul>
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