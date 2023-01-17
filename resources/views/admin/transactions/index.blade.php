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
                    <h1 class="m-0">All Transations</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                        <li class="breadcrumb-item active">Transations</li>
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
                            <h3 class="card-title float-sm-left">Transations</h3>
                            {{-- <a class="btn btn-primary float-sm-right" href="{{url('/admin/products/create')}}">Add Product</a> --}}
                        </div>
                        <!-- /.card-header -->
                        <div class="row" style="margin-top: 20px;">
                            <div class="col-sm-3">
                                <h4 style="margin-left: 20px;">Action Panel</h4>
                               </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <select name="user" id="users-id" class="form-control">
                                        <option value="">---{{ __('Select a user')}}---</option>
                                        @foreach ($users as $user)
                                                <option value="{{$user->id}}"
                                                    data-user_id="{{$user['id']}}" >
                                                    {{$user->username}}
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
                                        <th>User</th>
                                        <th>Type</th>
                                        <th>Transaction of</th>
                                        <th>Ad</th>
                                        <th>Product</th>
                                        <th>Order Id</th>
                                        <th>Transaction Status</th>
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
        var ajax_data = [];
            ajax_data['id'] = '';
        $(document).ready(function() {
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
                "url": "{{ url('admin/transactions') }}",
                "data":function(d){
                         d.user = ajax_data['id']
                        },
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
                        data: 'type',
                        name: 'type'
                    },
                    {
                        data: 'transaction_of',
                        name: 'transaction_of'
                    },
                    {
                        data: 'ad',
                        name: 'ad'
                    },
                    {
                        data: 'product',
                        name: 'product'
                    },
                    {
                        data: 'order',
                        name: 'order'
                    },
                   
                    {
                        data: 'transaction_status',
                        name: 'transaction_status'
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }

                ]
            });

            $('body').on('change','select[name="user"]',function(){
                ajax_data['id'] = $('option:selected',this).attr('data-user_id');
                refreshDataTable();
            });
            function refreshDataTable(){
                    table.ajax.reload();
            }
        });

    </script>
@endsection
