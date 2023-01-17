@extends('admin.app')
@section('content')

<link rel="stylesheet" href="{{ _asset('backend/plugins/summernote/summernote-bs4.min.css') }}">

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Templates</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/templates') }}">Templates</a></li>
                    <li class="breadcrumb-item active">{!!$action!!} Template</li>
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
                <!-- jquery validation -->
                <div class="card card-primary">
                    <div class="card-header">
                        <h3 class="card-title">
                            {!!$action!!} Template
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class=" col-md-10 col-md-offset-1  p-t-30 ">

                        @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                        @endif

                        <form id="profile-form" name="profile-form" method="POST" action="{{url('/admin/templates')}}" class="form-horizontal form-validate setting-form" novalidate="novalidate" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="{!!$action!!}">
                            <input type="hidden" id="temp_id" name="id" value="{!!@$template['id']!!}">
                            {{ csrf_field() }}
                            <div class="card-body">

                                <input type="hidden" class="form-control" name="template_type" id="template_type" value="1" />
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Type</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="email_type" id="email_type">

                                            <optgroup label="Email Templates">
                                                <option data-attr="[PRODUCTNAME]&nbsp;[SELLER]&nbsp;[BIDDER]&nbsp;[PRICE]&nbsp;[DATETIME]&nbsp;[LINK]&nbsp;" value="on_bid">Bid Template</option>
                                                <option data-attr="[PRODUCTNAME]&nbsp;[SELLER]&nbsp;[BUYER]&nbsp;[TRANSACTIONHASH]&nbsp;[EARNED]&nbsp;[TYPEOFTRANSACTION]&nbsp;[TIMESPENT]&nbsp;[LINK]&nbsp;" value="on_sold">Order Template</option>
                                                <option data-attr="[PRODUCTNAME]&nbsp;[SELLER]&nbsp;[TRANSACTIONHASH]&nbsp;[PRICEPAID]&nbsp;[TYPEOFTRANSACTION]&nbsp;[TIMESPENT]&nbsp;[LINK]&nbsp;" value="on_nft_creation">NFT Purchased </option>
                                                <option data-attr="[SENDER]&nbsp;[RECEIVER]&nbsp;[LINK]&nbsp;" value="on_message_sent"> Message Template</option>
                                                <option data-attr="[BIDDER]&nbsp;[PRODUCTNAME]&nbsp;[PRICE]&nbsp;[LINK]&nbsp;" value="on_wining_bid"> Winning Bid Template</option>
                                                <option data-attr="[BUYER]&nbsp;[LINK]&nbsp;" value="on_order_completed"> Order Completed Template</option>
                                           
                                            </optgroup>
                                            <optgroup label="Default Email Templates">

                                                <option data-attr="[FIRSTNAME]&nbsp;[LASTNAME]&nbsp;[LINK]" value="register_an_account">Sign Up </option>

                                                <option data-attr="[USERNAME]&nbsp;[LINK]" value="forgot_password">Forgot Password</option>

                                                <!-- <option data-attr="" value="email_verification">Email Verification</option> -->

                                                <option data-attr="[NAME]&nbsp;[EMAIL]&nbsp;[MESSAGE]" value="contact_us">Contact Us</option>
                                                <option data-attr="[NAME]&nbsp;[MESSAGE]" value="contact_us_reply">Contact Us Response Back (By Admin)</option>

                                                <option data-attr="[FIRSTNAME]&nbsp;[LASTNAME]&nbsp;[LINK]&nbsp;[PASSWORD]&nbsp;[EMAIL]" value="create_admin">Admin Creation by Admin</option>

                                                <option data-attr="[NAME]&nbsp;[LINK]&nbsp;[CONTENT]" value="default_email">Default Template</option>

                                            </optgroup>

                                        </select>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Template Description</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="title" id="title" placeholder="Enter Template Description" data-rule-required="true" aria-required="true" value="{!!@$template['title']!!}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Is Default</label>
                                    <div class="col-sm-8">
                                        <select class="form-control" name="is_default" id="is_default">
                                            <option value="1" @if(isset($template['is_default']) && $template['is_default']==1) selected @endif>Yes</option>
                                            <option value="0" @if(isset($template['is_default']) && $template['is_default']==0) selected @endif>No</option>
                                        </select>
                                    </div>
                                </div>

                                <input type="hidden" name="old_attachment" value="{!!@$template['attachment']!!}">
                                @if(isset($template['attachment']) && $template['attachment'] != '')
                                <div class="form-group old_file_attachment">
                                    <label class="col-sm-3 control-label">Old Attachment</label>
                                    <div class="col-sm-8">
                                        <b> <a href="{{url("/uploads/templates")}}/{!!@$template['attachment']!!}" download> {!!@$template['attachment']!!} </a>
                                            <span id="del_file"> <i class="fa fa-trash" style="color: #2c5e7b;"></i> </span>
                                        </b>
                                    </div>
                                </div>
                                @endif
                                <div class="form-group email_subject">
                                    <label class="col-sm-3 control-label">Attachments</label>
                                    <div class="col-sm-3">
                                        <input name="attachment" type="file" class="btn btn-primary" />
                                    </div>
                                </div>




                                <div class="form-group email_subject">
                                    <label class="col-sm-3 control-label">Subject</label>
                                    <div class="col-sm-7">
                                        <input type="text" class="form-control subject" value="{!!@$template['subject']!!}" id="subject" name="subject" />
                                    </div>
                                </div>

                                <div class="alert alert-warning">(NOTE: Please do not remove below keywords in Editor. These can replaced with dynamic data.)</div>
                                <div class="form-group red">
                                    <label class="col-sm-3 control-label">
                                        <strong>Description Keywords:</strong>
                                    </label>
                                    <div class="col-sm-8"><strong class="keywords"></strong>
                                        <div class="clear10"></div>
                                        <small>use in all Header Footer & Content</small>
                                    </div>

                                </div>

                                <div class="form-group headerDiv">
                                    <label class="col-sm-3 control-label">Header *</label>
                                    <div class="col-sm-7">
                                        <textarea name="header" id="headerwithck" class="form-control headerwithck summernote" rows="5">{!!@$template['header']!!}</textarea>



                                    </div>

                                </div>


                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Content *</label>
                                    <div class="col-sm-7">
                                        <textarea name="content" id="contentwithck" class="form-control contentwithck summernote " rows="5">{!!@$template['content']!!}</textarea>


                                    </div>

                                </div>

                                <div class="form-group footerDiv">
                                    <label class="col-sm-3 control-label">Footer *</label>
                                    <div class="col-sm-7">
                                        <textarea name="footer" id="footerwithck" class="form-control footerwithck summernote" rows="5">{!!@$template['footer']!!}</textarea>



                                    </div>

                                </div>


                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Status</label>
                                    <div class="col-sm-9">

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="is_active" id="is_active1" value="1" @if(isset($template->is_active) && $template->is_active == 1) checked @endif required>
                                            <label for="is_active1" class="custom-control-label">Active</label>
                                        </div>

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="is_active" id="is_active2" value="0" @if(isset($template->is_active) && $template->is_active == 0) checked @endif>
                                            <label for="is_active2" class="custom-control-label">Inactive</label>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-actions text-right">

                                    <a href="{{url('/admin/templates')}}" class="btn btn-default btn-cancel"> <i class="icons icon-arrow-left-circle"></i> Cancel</a>

                                    @if(isset($action) && $action == 'Add')
                                    <button type="submit" class="btn btn-primary"><i class="icons icon-check"></i> Save</button>
                                    @else
                                    <button type="submit" class="btn btn-primary"><i class="icons icon-check"></i> Update</button>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <!-- /.card -->
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
<!-- Summernote -->
<script src="{{ _asset('backend/plugins/summernote/summernote-bs4.min.js') }}"></script>
<script>
    $(function() {
        $('#email_type').val('{{@$template["email_type"]}}');
        $('#email_type').change(function() {
            var t = $('#email_type option:selected').data('attr');
            $('.keywords').html(t + '&nbsp;[SITE_URL]&nbsp;[SITE_NAME]&nbsp;[CONTACT_URL]');
        });
        // Summernote
        $('.summernote').summernote({
            height: 200
        });

        $("#title").on('keyup blur change', function() {
            var title = $("#title").val();
            $("#seo_url").val(convertToSlug(title));
        });
    })



    function convertToSlug(Text) {
        var text = Text.toLowerCase().replace(/ /g, '-').replace(/[^\w-]+/g, '');
        return text.replace('--', '-');
    }
</script>
@endsection