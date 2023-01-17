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
                <h1 class="m-0">Auction Products</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item active">Auction Products</li>
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
                        <h3 class="card-title float-sm-left">Auction Products</h3>
                        {{-- <a class="btn btn-primary float-sm-right" href="{{url('/admin/products/create')}}">Add Product</a> --}}
                    </div>
                    <!-- /.card-header -->
                    {{-- <div class="row">
                        <div class="col-sm">
                          One of three columns
                        </div>
                        <div class="col-sm">
                          One of three columns
                        </div>
                        <div class="col-sm">
                          One of three columns
                        </div>
                      </div> --}}
                    <div class="row" style="margin-top: 20px;">
                        <div class="col-sm-3">
                            <h4 style="margin-left: 20px;">Action Panel</h4>
                        </div>

                        <div class="col-md-3">
                            <select name="user_id" id="users-id" class="form-control">
                                <option value="">---{{ __('Select a User')}}---</option>
                                @foreach ($users as $user)
                                <option value="{{$user->id}}" data-user_id="{{$user['id']}}">
                                    {{$user->username}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="store_id" id="stor-id" class="form-control">
                                <option value="">---{{ __('Select a Store')}}---</option>
                                @foreach ($stores as $stor)
                                <option value="{{$stor['id']}}" @if ($store && $stor->id == decode($store))
                                    selected
                                    @endif
                                    data-stor_id="{{$stor['id']}}">
                                    {{$stor['store_title']}}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Users</th>
                                    <th>Store</th>
                                    <th>Category</th>
                                    <th>Title</th>
                                    <th>Price Usd</th>
                                    <th>View Count</th>
                                    <th>Aunction Time</th>
                                    <th>Total Bids</th>
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
        var store = "{{ $store }}";

        var url = "{{ route('admin.auction-products.index') }}";
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
            buttons: [
                'excel', 'pdf'
            ],
            aLengthMenu: [
                [10, 50, 100, -1],
                [10, 50, 100, "All"]
            ],

            serverSide: true,
            "ajax": {
                "url": url,
                "data": function(d) {

                    d.store_id = $('select[name=store_id]').find(':selected').val();
                    d.user_id = $('select[name=user_id]').find(':selected').val();
                    // d.stor = ajax_data['stor_id']
                }
            },

            fnDrawCallback: function(oSettings) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columns: [
                {
                    data: 'user',
                    name: 'user'
                },
                {
                    data: 'store',
                    name: 'store'
                },
                {
                    data: 'category',
                    name: 'category'
                },
                {
                    data: 'title',
                    name: 'title'
                },


                {
                    data: 'price_usd',
                    name: 'price_usd'
                },

                {
                    data: 'view_count',
                    name: 'view_count'
                },
                {
                    data: 'auction_end_time',
                    name: 'auction_end_time'
                },
                {
                    data: 'total_bids',
                    name: 'total_bids'
                },
               

                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }

            ]
        });
        $('body').on('change', 'select[name="user_id"]', function() {

            refreshDataTable();
        });
        $('body').on('change', 'select[name="store_id"]', function() {

            refreshDataTable();
        });

        function refreshDataTable() {
            table.ajax.reload();

        }
        if (store = "{{ $store }}") {
            $('#stor-id').addClass('store_var');
        }
        $('.store_var').on('click', function() {
            window.location.href = "{{url('/admin/auction-products')}}";
            $('stor-id').removeClass('store_var');

        })
    });
</script>
@endsection