@extends('admin.app')
@section('content')

<link rel="stylesheet" href="{{ _asset('backend/plugins/summernote/summernote-bs4.min.css') }}">

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">CMS Pages</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/cms-pages') }}">CMS Pages</a></li>
                    <li class="breadcrumb-item active">{!!$action!!} CMS Page</li>
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
                            {!!$action!!} CMS Page
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

                        <form id="profile-form" name="profile-form" method="POST" action="{{url('/admin/cms-pages')}}" class="form-horizontal form-validate setting-form" novalidate="novalidate" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="{!!@$user->id!!}">
                            <input type="hidden" name="action" value="{!!$action!!}">
                            {{ csrf_field() }}
                            <div class="card-body">

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Title</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="title" id="title" placeholder="Enter Title" data-rule-required="true" aria-required="true" value="{!!@$cmsPage['title']!!}" />
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Description</label>
                                    <div class="col-sm-8">

                                        <textarea data-rule-required="true" aria-required="true" id="description" name="description" class='form-control ' rows="10">{!!@$cmsPage['description']!!}</textarea>

                                    </div>
                                </div>



                                <input type="hidden" name="action" value="{!!$action!!}">
                                <input type="hidden" name="id" value="{!!@$cmsPage['id']!!}">
                                {{ csrf_field() }}
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Tracking Code:</label>
                                    <div class="col-sm-8">
                                        <textarea class="form-control tracking_code" name="tracking_code" id="tracking_code" placeholder="Enter Tracking Code">{!!@$cmsPage['tracking_code']!!}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Slug</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="seo_url" id="seo_url" readonly="readonly" value="{!!@$cmsPage['seo_url']!!}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Order By</label>
                                    <div class="col-sm-8">
                                        <input placeholder="Enter Order By" type="number" min="0" class="form-control" id="sort_by" name="sort_by" value="{!!@$cmsPage['sort_by']!!}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Meta Title</label>
                                    <div class="col-sm-8">
                                        <input placeholder="Enter Meta Title" type="text" class="form-control" id="meta_title" name="meta_title" value="{!!@$cmsPage['meta_title']!!}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Meta Keywords</label>
                                    <div class="col-sm-8">
                                        <input placeholder="Enter Meta Keywords" type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{!!@$cmsPage['meta_keywords']!!}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Meta Description</label>
                                    <div class="col-sm-8">
                                        <textarea placeholder="Enter Meta Description" class="form-control" style="height:100px;" id="meta_title" name="meta_description">{!!@$cmsPage['meta_description']!!}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Show in Header</label>
                                    <div class="col-sm-9">

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="show_in_header" id="show_in_header1" value="1" @if(isset($cmsPage->show_in_header) && $cmsPage->show_in_header == 1) checked @endif required>
                                            <label for="show_in_header1" class="custom-control-label">Yes</label>
                                        </div>

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="show_in_header" id="show_in_header2" value="0" @if(isset($cmsPage->show_in_header) && $cmsPage->show_in_header == 0) checked @endif>
                                            <label for="show_in_header2" class="custom-control-label">No</label>
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Show in Footer</label>
                                    <div class="col-sm-9">

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="show_in_footer" id="show_in_footer1" value="1" @if(isset($cmsPage->show_in_footer) && $cmsPage->show_in_footer == 1) checked @endif required>
                                            <label for="show_in_footer1" class="custom-control-label">Yes</label>
                                        </div>

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="show_in_footer" id="show_in_footer2" value="0" @if(isset($cmsPage->show_in_footer) && $cmsPage->show_in_footer == 0) checked @endif>
                                            <label for="show_in_footer2" class="custom-control-label">No</label>
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Status</label>
                                    <div class="col-sm-9">

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="is_active" id="is_active1" value="1" @if(isset($cmsPage->is_active) && $cmsPage->is_active == 1) checked @endif required>
                                            <label for="is_active1" class="custom-control-label">Active</label>
                                        </div>

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="is_active" id="is_active2" value="0" @if(isset($cmsPage->is_active) && $cmsPage->is_active == 0) checked @endif>
                                            <label for="is_active2" class="custom-control-label">Inactive</label>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-actions text-right">

                                    <a href="{{url('/admin/cms-pages')}}" class="btn btn-default btn-cancel"> <i class="icons icon-arrow-left-circle"></i> Cancel</a>

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
        // Summernote
        $('#description').summernote({
            height: 200,
            placeholder: 'Enter Description'
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