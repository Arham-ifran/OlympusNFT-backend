@extends('admin.app')
@section('content')
<link rel="stylesheet" href="{{ _asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ _asset('backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
<link rel="stylesheet" href="{{ _asset('backend/plugins/datatable-button.html5/buttons.dataTables.min.css') }}">

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>

                </ol>
            </div>
        </div>
    </div>
</div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box">
                    <span class="info-box-icon bg-info elevation-1"><i class="fas fa-users-cog"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Admin Users</span>
                        <span class="info-box-number">
                            {{$admin_users ?? '0'}}
                        </span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success elevation-1"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Investors</span>
                        <span class="info-box-number">{{$investors ?? '0'}}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon  bg-dark  elevation-1"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Artists</span>
                        <span class="info-box-number">{{$artists ?? '0'}}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-users"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Musicians</span>
                        <span class="info-box-number">{{$musicians ?? '0'}}</span>
                    </div>
                </div>
            </div>
           <div class="clearfix hidden-md-up"></div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-primary elevation-1"> <i class="fas fa-cart-arrow-down"></i>
                    </span>
                    <div class="info-box-content">

                        <span class="info-box-text">Orders</span>
                        <span class="info-box-number">{{$orders ?? '0'}}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-box-open"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Products</span>
                        <span class="info-box-number">{{$products ?? '0'}}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon  bg-secondary   elevation-1"><i class="fas fa-file-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">CMS Pages</span>
                        <span class="info-box-number">{{$cmspages ?? '0'}}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-info elevation-1"><i class="fab fa-blogger" ></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Blogs</span>
                        <span class="info-box-number">{{$blogs ?? '0'}}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-dark elevation-1"><i class="fas fa-ad"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Ads</span>
                        <span class="info-box-number">{{$ads ?? '0'}}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-success  elevation-1"><i class="fas fa-store"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Stores</span>
                        <span class="info-box-number">{{$stores ?? '0'}}</span>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-md-3">
                <div class="info-box mb-3">
                    <span class="info-box-icon bg-danger elevation-1"><i class="fas fa-list-alt"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Categories</span>
                        <span class="info-box-number">{{$categories ?? '0'}}</span>
                    </div>
                </div>
            </div>


        </div>
        @if (auth()->user()->can('View Dashboard Latest Orders') || auth()->user()->hasRole('Super Admin'))
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header border-transparent">
                        <h3 class="card-title">Latest Orders</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse">
                                <i class="fas fa-minus"></i>
                            </button>
                            <!-- <button type="button" class="btn btn-tool" data-card-widget="remove">
                                <i class="fas fa-times"></i>
                            </button> -->
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table   id="datatable" class="table m-0">
                                <thead>
                                    <tr>

                                        <th>Product Name</th>
                                        <th>Auction Product</th>
                                        <th>Price Usd</th>
                                        <th>Total</th>
                                        <th>Buyer</th>
                                        <th>Order Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer clearfix">
                        <a href="{{ url('/admin/orders') }}" class="btn btn-sm btn-secondary float-right">View All Orders</a>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@if (auth()->user()->can('View Dashboard Latest Orders') || auth()->user()->hasRole('Super Admin'))
<script src="{{ _asset('backend/plugins/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ _asset('backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ _asset('backend/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ _asset('backend/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
<script src="{{ _asset('backend/plugins/jszip/jszip.min.js') }}"></script>
<script src="{{ _asset('backend/plugins/datatable-button.html5/dataTables.buttons.min.js') }}"></script>
<script src="{{ _asset('backend/plugins/datatable-button.html5/buttons.html5.min.js') }}"></script>
<script type="text/javascript">

    $(document).ready(function() {
        var table = $('#datatable').DataTable({
            lengthChange: false,
            processing: false,
            drawCallback: function() {
                $('.delete-form-btn').on('click', function() {
                    var submitBtn = $(this).next('.deleteSubmit');
                    Swal.fire({
                        title: "Are you sure?",
                        text: "You will not be able to recover this record!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#DD6B55",
                        confirmButtonText: "Yes, delete it!",
                        cancelButtonText: "No, cancel!",
                        showCloseButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            submitBtn.click();
                        } else if (result.isDenied) {

                        }
                    });
                });
            },
            responsive: true,
            dom: 'Bfrtip',
            buttons: [
            'excel',
            ],
            aLengthMenu: [
            [10, 50, 100, -1],
            [10, 50, 100, "All"]
            ],
            aaSorting: [],
            "language": {
                "emptyTable": "No record found"
            },
            serverSide: true,
            ajax: "{{ route('admin.dashboard') }}",
            fnDrawCallback: function(oSettings) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columns: [

            {
                data: 'product_name',
                name: 'product_name'
            },
            {
                data: 'is_auction_product',
                name: 'is_auction_product'
            },
            {
                data: 'price_usd',
                name: 'price_usd'
            },
            {
                data: 'total',
                name: 'total'
            },
            {
                data: 'buyer',
                name: 'buyer'
            },
            {
                data: 'order_status',
                name: 'order_status'
            },
            {
                data: 'action',
                name: 'action',
                orderable: false,
                searchable: false
            },
            // {
            //     data: 'approve_etl',
            //     name: 'approve_etl',
            //     orderable: false,
            //     searchable: false
            // }
            ]
        });
    });

</script>
@endif
@endsection
