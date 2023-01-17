@extends('admin.app')
@section('styles')
<link rel="stylesheet" href="{{ _asset('backend/plugins/datepicker/datepicker.css') }}">
@endsection
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Investor</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/investors') }}">Investor</a></li>
                    <li class="breadcrumb-item active">{!!$action!!} Investor</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <!-- left column -->
            <div class="col-md-12">
                <form id="profile-form" name="profile-form" method="POST" action="{{url('/admin/investors')}}" class="form-horizontal form-validate setting-form" novalidate="novalidate" enctype="multipart/form-data">
                    <!-- jquery validation -->
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                {!!$action!!} Investor
                            </h3>
                        </div>
                        <!-- /.card-header -->
                        <!-- form start -->


                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif


                        <input type="hidden" name="id" value="{!!@$seller->id!!}">
                        <input type="hidden" name="action" value="{!!$action!!}">
                        {{ csrf_field() }}
                        <div class="card-body">
                            <div class="col-md-10 col-md-offset-1  p-t-30 ">
                                <div class="row">

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="username" class="control-label">Username</label>
                                            <div class="col-sm-12">
                                                <input type="text" id="username" name="username" class='form-control' placeholder="Enter Username" value="{!!@$seller['username']!!}" />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="email" class="control-label">Email</label>
                                            <div class="col-sm-12">
                                                <input type="email" id="email" name="email" class='form-control' placeholder="Enter Email" value="{!!@$seller['email']!!}" data-rule-required="true" aria-required="true">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="about" class="control-label"> About</label>
                                            <div class="col-sm-12">
                                                <textarea class="form-control" rows="3" name="about" placeholder="">{!!@$seller['about']!!}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="wallet_address" class="control-label">Wallet Adress</label>
                                            <div class="col-sm-12">
                                                <input type="text" id="wallet_address" name="wallet_address" class='form-control' placeholder="Enter Wallet Address" value="{!!@$seller['wallet_address']!!}" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password" class="control-label">Password</label>
                                            <div class="col-sm-12">
                                                <input type="password" id="password" name="password" class='form-control' placeholder="Enter password" @if($action=="Add" ) data-rule-required="true" aria-required="true" data-rule-minlength="8" @endif value="" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="password_confirmation" class="control-label">Confirm Password</label>
                                            <div class="col-sm-12">
                                                <input type="password" id="password_confirmation" name="password_confirmation" class='form-control' placeholder="Retype new password" @if($action=="Add" ) data-rule-required="true" aria-required="true" data-rule-equalto="#password" data-rule-minlength="8" @endif value="" autocomplete="off" />
                                            </div>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>
                    </div>


                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                Social Media Links
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="col-md-10 col-md-offset-1  p-t-30 ">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="twitter" class="control-label">Twitter</label>
                                            <div class="col-sm-12">
                                                <input type="text" id="twitter" name="twitter" class='form-control' placeholder="Enter Twitter Address" data-rule-minlength="5" value="{!!@$seller['twitter']!!}" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="instagram" class="control-label">Instagram</label>
                                            <div class="col-sm-12">
                                                <input type="text" id="instagram" name="instagram" class='form-control' placeholder="Enter Instagram" data-rule-minlength="5" value="{!!@$seller['instagram']!!}" />
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label for="facebook" class="control-label">Facebook</label>
                                            <div class="col-sm-12">
                                                <input type="text" id="facebook" name="facebook" class='form-control' placeholder="Enter Facebook" data-rule-minlength="5" value="{!!@$seller['facebook']!!}" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="youtube" class="control-label">YouTube</label>
                                            <div class="col-sm-12">
                                                <input type="text" id="youtube" name="youtube" class='form-control' placeholder="Enter YouTube" data-rule-minlength="5" value="{!!@$seller['youtube']!!}" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Email Notification
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="col-md-10 col-md-offset-1  p-t-30 ">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label">Email Notification</label>
                                                <div class="col-sm-12">

                                                    <div class="custom-control custom-radio">
                                                        <input class="custom-control-input" type="radio" name="email_notification" id="email_notification1" value="1" @if(isset($seller->email_notification) && $seller->email_notification == 1) checked @endif >
                                                        <label for="email_notification1" class="custom-control-label">Enable</label>
                                                    </div>

                                                    <div class="custom-control custom-radio">
                                                        <input class="custom-control-input" type="radio" name="email_notification" id="email_notification2" value="0" @if(isset($seller->email_notification) && $seller->email_notification == 0) checked @endif>
                                                        <label for="email_notification2" class="custom-control-label">Disable</label>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Status
                                </h3>
                            </div>
                            <div class="card-body">
                                <div class="col-md-10 col-md-offset-1  p-t-30 ">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label class="control-label">Status</label>
                                                <div class="col-sm-12">

                                                    <div class="custom-control custom-radio">
                                                        <input class="custom-control-input" type="radio" name="is_active" id="is_active1" value="1" @if(isset($seller->is_active) && $seller->is_active == 1) checked @endif required>
                                                        <label for="is_active1" class="custom-control-label">Active</label>
                                                    </div>

                                                    <div class="custom-control custom-radio">
                                                        <input class="custom-control-input" type="radio" name="is_active" id="is_active2" value="0" @if(isset($seller->is_active) && $seller->is_active == 0) checked @endif>
                                                        <label for="is_active2" class="custom-control-label">Inactive</label>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>



                        <div class="card card-primary">

                            <div class="card-body">
                                <div class="col-md-10 col-md-offset-1  p-t-30 ">
                                    <div class="row">
                                        <div class="form-actions text-right">

                                            <a href="{{ url('/admin/investors') }}" class="btn btn-default btn-cancel"> <i class="icons icon-arrow-left-circle"></i> Cancel</a>
                                            <input type="submit" class='btn btn-primary' value="Save Changes">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <!-- /.card -->
                </form>
            </div>

            <!--/.col (left) -->
            <!-- right column -->
            <div class="col-md-6"></div>
            <!--/.col (right) -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->
@endsection
@section('scripts')
<script src="{{ _asset('backend/plugins/datepicker/datepicker.js') }}"></script>

<script type="text/javascript">
    $(".datepicker").datepicker({
        format: "mm/dd/yyyy",
        autoclose: true,
        todayHighlight: true
    });
</script>

@endsection