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
                    <h1 class="m-0">All Users</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                        <li class="breadcrumb-item active">All Users</li>
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
                            <h3 class="card-title float-sm-left">All Users</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="datatable" class="table table-bordered table-hover">
                                <thead>
                                    <tr>

                                        <th>User Name</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Mobile</th>
                                        <th>Address</th>
                                        <th>Address2</th>
                                        <th>City</th>
                                        <th>State</th>
                                        <th>Country</th>
                                        <th>ZipCode</th>
                                        <th>Bob</th>
                                        <th>Wallet Address</th>
                                        <th>Twitter</th>
                                        <th>Instagram</th>
                                        <th>Reedit</th>
                                        <th>About</th>
                                        <th>Cent</th>
                                        <th>Facebook</th>
                                        <th>Last Login</th>
                                        <th>Status</th>
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
    <script src="{{ _asset('backend/plugins/datatable-button.html5/datatables.buttons.min.js') }}"></script>
    <script src="{{ _asset('backend/plugins/datatable-button.html5/buttons.html5.min.js') }}"></script>
    <!-- /.content -->
    <script type="text/javascript">
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
                buttons: [
                    'excel',
                ],
                aLengthMenu: [
                    [10, 50, 100, -1],
                    [10, 50, 100, "All"]
                ],

                serverSide: true,
                ajax: "{{ route('admin.all-users.index') }}",
                fnDrawCallback: function(oSettings) {
                    $('[data-toggle="popover"]').popover();
                    $('[data-toggle="tooltip"]').tooltip();
                },
                columns: [

                    {
                        data: 'user_type',
                        name: 'user_type'
                    },
                    {
                        data: 'firstname',
                        name: 'firstname'
                    },
                    {
                        data: 'lastname',
                        name: 'lastname'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'mobile',
                        name: 'mobile'
                    },
                    {
                        data: 'address',
                        name: 'address'
                    },
                    {
                        data: 'address2',
                        name: 'address2'
                    },
                    {
                        data: 'city',
                        name: 'city'
                    },
                    {
                        data: 'state',
                        name: 'state'
                    },
                    {
                        data: 'country',
                        name: 'country'
                    },
                    {
                        data: 'zipcode',
                        name: 'zipcode'
                    },
                    {
                        data: 'dob',
                        name: 'dob'
                    },
                    {
                        data: 'wallet_address',
                        name: 'wallet_address'
                    },
                    {
                        data: 'twitter',
                        name: 'twitter'
                    },
                    {
                        data: 'instagram',
                        name: 'instagram'
                    },
                    {
                        data: 'reedit',
                        name: 'reedit'
                    },
                    {
                        data: 'about',
                        name: 'about'
                    },
                    {
                        data: 'cent',
                        name: 'cent'
                    },
                    {
                        data: 'facebook',
                        name: 'facebook'
                    },
                    {
                        data: 'last_login_on',
                        name: 'last_login_on'
                    },
                    {
                        data: 'status',
                        name: 'status'
                    },

                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }

                ]
            });
        });

    </script>
@endsection
