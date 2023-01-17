@extends('admin.app')
@section('content')
@section('styles')
<link rel="stylesheet" href="{{ _asset('backend/plugins/datetimepicker/bootstrap-datetimepicker.min.css') }}">
@endsection
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Settings</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item active">Site Settings</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
        <form action="{!! url('/admin/site-settings/update') !!}" method="post" class="form-horizontal form-validate setting-form" name="settingsForm" id="settingsForm" novalidate="novalidate" enctype="multipart/form-data">
            <div class="row">
                <!-- left column -->
                <div class="col-md-6">
                    <!-- jquery validation -->
                    <!-- form start -->

                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                Site Settings
                            </h3>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">

                            @csrf
                            <input type="hidden" name="id" value="{!!@$settings->id!!}">

                            <div class="form-group">
                                <label for="site_logo">Site Logo</label>
                                <div class="col-sm-8">
                                    <img src="{!! checkImage(asset('storage/uploads/images/'.@$settings->site_logo)) !!}" class="image-display " id="site_logo_image" style="width:  150px;border:  1px solid #ccc;" />
                                    <input type="file" accept="image/*" onchange="change_image(this, 'site_logo_image', 'imgShow')" class="form-control site_logo_change" name="site_logo" id="site_logo">
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="firstname">Site Name *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="site_name" id="site_name" placeholder="Enter Site Name" value="{{ @$settings->site_name }}" required>
                                </div>
                            </div>
                            <div class="form-group">

                                <label for="lastname">Site Title *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="site_title" id="site_title" placeholder="Enter Site Title" value="{{ @$settings->site_title }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="firstname">Site Email *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="site_email" id="site_email" placeholder="Enter Site Email" value="{{ @$settings->site_email }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="firstname">Inquiry Email *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="inquiry_email" id="inquiry_email" placeholder="Enter Inquiry Email" value="{{ @$settings->inquiry_email }}" required>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="lastname">Mobile# *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="site_mobile" id="site_mobile" placeholder="Enter Site Mobile#" value="{{ @$settings->site_mobile }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="lastname">Site Phone# *</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="site_phone" id="site_phone" placeholder="Enter Site Mobile#" value="{{ @$settings->site_phone }}" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="firstname">Site Address </label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="site_address" id="site_address" placeholder="Enter Address" value="{{ @$settings->site_address }}">
                                </div>
                            </div>



                        </div>
                        <!-- /.card-body -->


                    </div>

                    <!-- /.card -->
                </div>

                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                Meta Keywords & Description
                            </h3>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">
                            <div class="form-group">
                                <label for="firstname">Site Keywords</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="site_keywords" style="height: 150px;resize:  none;" id="site_keywords" placeholder="Enter Keywords" value="">{{ @$settings->site_keywords }}</textarea>
                                </div>
                            </div>


                            <div class="form-group">
                                <label for="	site_description">Site Description</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" name="site_description" style="height: 150px;resize:  none;" id="site_description" placeholder="Enter Site Description..." value="">{{ @$settings->site_description }}</textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="suggested-price">Launch Time</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" name="launch_time" id="launch_time" placeholder="launch time" value="{{ Carbon\Carbon::parse(@$settings->launch_time)->format('d/m/Y g:i A') }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="suggested-price">Home Page Floating Video LINK</label>
                                <div class="col-sm-10">
                                    <input required type="url" class="form-control" name="home_page_video" id="home_page_video" placeholder="https://www.youtube.com/watch?v=SkE6LUoSrRU" value="{{@$settings->home_page_video}}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- right column -->
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h3 class="card-title">
                                Social Settings
                            </h3>
                        </div>
                        <!-- /.card-header -->

                        <div class="card-body">

                            <div class="form-group">
                                <label class="control-label" for="firstname">Facebook </label>
                                <input type="url" class="form-control" name="facebook" id="facebook" placeholder="Enter Facebook Link" value="{{ @$settings->facebook }}">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="lastname">Twitter </label>
                                <input type="url" class="form-control" name="twitter" id="twitter" placeholder="Enter Twitter Link" value="{{ @$settings->twitter }}">
                            </div>


                            <div class="form-group">
                                <label class="control-label" for="lastname">Discord </label>
                                <input type="url" class="form-control" name="discord" id="discord" placeholder="Enter Discord Link" value="{{ @$settings->discord }}">
                            </div>
                            
                            <div class="form-group">

                                <label class="control-label" for="lastname">Instagram </label>
                                <input type="url" class="form-control" name="insta" id="insta" placeholder="Enter instagram Link" value="{{ @$settings->insta }}">
                            </div>
                            <div class="form-group">

                                <label class="control-label" for="lastname">Youtube </label>
                                <input type="text" class="form-control" name="youtube" id="youtube" placeholder="Enter Youtube Link" value="{{ @$settings->youtube }}">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="lastname">Tiktok </label>
                                <input type="text" class="form-control" name="tiktok" id="tiktok" placeholder="Enter TikTok" value="{{ @$settings->tiktok }}">
                            </div>
                            <div class="form-group">
                                <label class="control-label" for="firstname">Twitch </label>
                                <input type="url" class="form-control" name="twitch" id="twitch" placeholder="Enter Twitch" value="{{ @$settings->twitch }}">
                            </div>
                            {{--
                            <div class="form-group">

                                <label class="control-label" for="lastname">Skype </label>
                                <input type="text" class="form-control" name="skype" id="skype" placeholder="Enter Skype" value="{{ @$settings->skype }}">
                        </div>
                        <div class="form-group">
                            <label class="control-label" for="firstname">Linkedin </label>
                            <input type="url" class="form-control" name="linkedin" id="linkedin" placeholder="Enter Linkedin" value="{{ @$settings->linkedin }}">
                        </div>
                        --}}
                    </div>


                </div>


            </div>
            <div class="col-md-6">
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            AD Manager Settings
                        </h3>
                    </div>
                    <!-- /.card-header -->

                    <div class="card-body">
                        <div class="form-group">
                            <label for="ad_manager_fee-price">Ad Manager Fee in: ETH</label>
                            <div class="col-sm-10">
                                <input type="number" min="0.0001" class="form-control" name="ad_manager_fee" id="ad_manager_fee" placeholder="0.00" value="{{ @$settings->ad_manager_fee }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="current-price">Current Average CPC: Price</label>
                            <div class="col-sm-10">
                                <input type="number" min="0.0001" class="form-control" name="current_average_cpc_price" id="current_average_cpc_price" placeholder="0.00" value="{{ @$settings->current_average_cpc_price }}">
                            </div>
                        </div>


                        <div class="form-group">
                            <label for="suggested-price">Suggested CPC: Price</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" name="suggested_cpc_price" id="suggested_cpc_price" placeholder="0.00" value="{{ @$settings->suggested_cpc_price }}">
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card card-primary">
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            Save Settings
                        </button>
                    </div>
                </div>
            </div>
            <!--/.col (right) -->
    </div>
    </form>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

@endsection
@section('scripts')
<script src="{{ _asset('backend/js/moment.min.js') }}"></script>
<script src="{{ _asset('backend/plugins/datetimepicker/bootstrap-datetimepicker.min.js') }}"></script>

<script type="text/javascript">
    $('#launch_time').datetimepicker({
        useCurrent: false,
        minDate: moment(),
        format: 'MM/DD/YYYY hh:mm A',
        keepOpen: true
    });
</script>

@endsection