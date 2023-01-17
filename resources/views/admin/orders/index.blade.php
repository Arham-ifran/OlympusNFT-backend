@extends('admin.app')
@section('content')
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ _asset('backend/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ _asset('backend/plugins/datatables-responsive/css/responsive.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ _asset('backend/plugins/datatable-button.html5/buttons.dataTables.min.css') }}">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">All Orders</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                        <li class="breadcrumb-item active">Orders</li>
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
                            <h3 class="card-title float-sm-left">Orders</h3>
                            {{-- <a class="btn btn-primary float-sm-right" href="{{url('/admin/products/create')}}">Add Product</a> --}}
                        </div>
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-2">
                                <h4 style="margin-left: 20px;">Action Panel</h4>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                     <select name="seller" id="seller-id" class="form-control">
                                        <option value="">---{{ __('Select a Seller')}}---</option>
                                        @foreach ($users as $user)
                                                <option value="{{$user->id}}" >
                                                    {{$user['username']}}
                                                </option>
                                        @endforeach
                                </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                     <select name="buyer" id="buyer-id" class="form-control">
                                        <option value="">---{{ __('Select a buyer')}}---</option>
                                        @foreach ($users as $user)
                                                <option value="{{$user->id}}" >
                                                    {{$user['username']}}
                                                </option>
                                        @endforeach
                                </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                     <select name="product" id="product-id" class="form-control">
                                        <option value="">---{{ __('Select a product')}}---</option>
                                        @foreach ($products as $product)
                                                <option value="{{$product->id}}"
                                                    data-product_id="{{$product['id']}}" >
                                                    {{$product['title']}}
                                                </option>
                                        @endforeach
                                </select>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">

                            <table id="datatable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Seller</th>
                                        <th>Buyer</th>
                                        <th>Product Title </th>
                                        <th>Price Usd</th>
                                        {{-- <th>From Address</th>
                                        <th>To Address</th> --}}
                                        <th>Total</th>
                                        <th>Created At</th>
                                        <th>Order Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>

                            </table>
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

    <!-- DataTables  & Plugins -->
    <script src="{{ _asset('backend/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ _asset('backend/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ _asset('backend/plugins/datatables-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ _asset('backend/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') }}"></script>
    <script src="{{ _asset('backend/plugins/jszip/jszip.min.js') }}"></script>
    <script src="{{ _asset('backend/plugins/datatable-button.html5/dataTables.buttons.min.js') }}"></script>
    <script src="{{ _asset('backend/plugins/datatable-button.html5/buttons.html5.min.js') }}"></script>
    <script src="{{ _asset('backend/plugins/datatable-button.html5/pdfmake.min.js') }}"></script>
    <script src="{{ _asset('backend/plugins/datatable-button.html5/vfs_fonts.js') }}"></script>
    <!-- /.content -->
    <script type="text/javascript">
        $(document).ready(function() {
            var url = "{{ route('admin.orders.index') }}";
            var table = $('#datatable').DataTable({
                lengthChange: false,
                // scrollX: true,
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
                            /* Read more about isConfirmed, isDenied below */
                            if (result.isConfirmed) {
                                submitBtn.click();
                            } else if (result.isDenied) {
                                // Swal.fire('Changes are not saved', '', 'info')
                            }
                        });

                    });
                },
                responsive: true,
                dom: 'Bfrtip',
                buttons: ['excel','pdf'],
                aLengthMenu: [
                    [10, 50, 100, -1],
                    [10, 50, 100, "All"]
                ],
                serverSide: true,
                "ajax":{
                "url":url,
                "data":function(d){
                    d.product = $('select[name=product]').find(':selected').val();
                    d.buyer = $('select[name=buyer]').find(':selected').val();
                    d.seller = $('select[name=seller]').find(':selected').val();
                    }
                },
                fnDrawCallback: function(oSettings) {
                    $('[data-toggle="popover"]').popover();
                    $('[data-toggle="tooltip"]').tooltip();
                },
                columns: [
                    {
                        data: 'seller',
                        name: 'seller'
                    },
                    {
                        data: 'buyer',
                        name: 'buyer'
                    },
                    {
                        data: 'product',
                        name: 'product'
                    },

                    {
                        data: 'price_usd',
                        name: 'price_usd'
                    },
                    // {
                    //     data: 'from_address',
                    //     name: 'from_address'
                    // },
                    // {
                    //     data: 'to_address',
                    //     name: 'to_address'
                    // },
                    {
                        data: 'total',
                        name: 'total'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
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
                    }

                ]
            });

            $('body').on('change','select[name="product"]',function(){
                 refreshDataTable();
              });
            $('body').on('change','select[name="buyer"]',function(){
                refreshDataTable();

            })
            $('body').on('change','select[name="seller"]',function(){
               refreshDataTable();

            })
            function refreshDataTable(){
                    table.ajax.reload();
            }
        });

    </script>
@endsection
