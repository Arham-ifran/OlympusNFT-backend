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
                <h1 class="m-0">Bidding History</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item active">Bidding History</li>
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
                        <h3 class="card-title float-sm-left">All Bidding History</h3>

                    </div>
                    <!-- /.card-header -->

                    <div class="row" style="margin-top: 20px">
                        <div class="col-sm-3">
                            <h4 style="margin-left: 20px;">Action Panel</h4>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                 <select name="bidder" id="bidder_id" class="form-control">
                                    <option value="">---{{ __('Select a bidder')}}---</option>
                                    @foreach ($bidders as $bidder)
                                            <option value="{{$bidder->id}}"

                                                data-bidder_id="{{$bidder['id']}}" >
                                                {{$bidder['username']}}
                                            </option>
                                    @endforeach
                            </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                 <select name="product" id="product-id" class="form-control">
                                    <option value="">---{{ __('Select a product')}}---</option>
                                    @foreach ($products as $prdt)
                                            <option value="{{$prdt->id}}"
                                                @if ($product && $prdt->id == decode($product))
                                                    selected
                                                @endif
                                                data-product_id="{{$prdt['id']}}" >
                                                {{$prdt['title']}}
                                            </option>
                                    @endforeach
                            </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable" class="table table-bordered table-hover">
                            <thead>
                                <tr>

                                    <th>Bidder</th>
                                    <th>Product</th>
                                    <th>Price</th>
                                    <th>Auction Time</th>
                                    <th>Is Winner Bid</th>
                                    <!-- <th>Actions</th> -->
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
        var ajax_data = [];
            ajax_data['id'] = '';
            ajax_data['bidder_id'] = '';
    $(document).ready(function() {
        var product = "{{ $product }}";
        var q = '';
        if ("undefined" !== typeof product && product != '') {
            q = '?product=' + product;
        }

        var url = "{{ route('admin.bidding-history.index') }}";
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
                'excel','pdf'
            ],
            aLengthMenu: [
                [10, 50, 100, -1],
                [10, 50, 100, "All"]
            ],

            serverSide: true,
            "ajax":{
                "url":url + q,
                "data":function(d){
                        d.prdt = ajax_data['id'];
                        d.bidder = ajax_data['bidder_id'];
                    }
                },
            fnDrawCallback: function(oSettings) {
                $('[data-toggle="popover"]').popover();
                $('[data-toggle="tooltip"]').tooltip();
            },
            columns: [

                {
                    data: 'bidder',
                    name: 'bidder'
                },
                {
                    data: 'product',
                    name: 'product'
                },
                {
                    data: 'price',
                    name: 'price'
                },
                {
                    data: 'auction_time',
                    name: 'auction_time'
                },

                {
                    data: 'is_winner_bid',
                    name: 'is_winner_bid'
                },

                // ,

                // {
                //     data: 'action',
                //     name: 'action',
                //     orderable: false,
                //     searchable: false
                // }

            ]
        });

        $('body').on('change','select[name="product"]',function(){
                 ajax_data['id'] = $('option:selected',this).attr('data-product_id');
                 refreshDataTable();
              });
        $('body').on('change','select[name="bidder"]',function(){
            ajax_data['bidder_id'] = $('option:selected').attr('data-bidder_id');
            refreshDataTable();
        })
        function refreshDataTable(){
                table.ajax.reload();
        }
        if(product = "{{ $product }}"){
            $('#product-id,#bidder_id').addClass('product_var');
        }
        $('.product_var').on('click',function(){
            window.location.href = "{{url('/admin/bidding-history')}}";
            $('product-id').removeClass('product_var');

        })
    });
</script>
@endsection
