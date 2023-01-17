@extends('admin.app')
@section('content')

    <link rel="stylesheet" href="{{ _asset('backend/plugins/summernote/summernote-bs4.min.css') }}">

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">Video Guides</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                        <li class="breadcrumb-item"><a href="{{ url('/admin/video-guides') }}">Video Guides</a></li>
                        <li class="breadcrumb-item active">{!! $action !!} Video Guide</li>
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
                                {!! $action !!} Video Guide
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

                            <form id="profile-form" name="profile-form" method="POST"
                                action="{{ url('/admin/video-guides') }}"
                                class="form-horizontal form-validate setting-form" novalidate="novalidate"
                                enctype="multipart/form-data">
                                <input type="hidden" name="id" value="{!! @$videoGuide->id !!}">
                                <input type="hidden" name="action" value="{!! $action !!}">
                                {{ csrf_field() }}
                                <div class="card-body">

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Title</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" name="title" id="title"
                                                placeholder="Enter Title" data-rule-required="true" aria-required="true"
                                                value="{!! @$videoGuide['title'] !!}" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Url</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control youtube-url-cls" name="url"
                                                id="url youtube-url-id" placeholder="Enter URL" data-rule-required="true"
                                                aria-required="true" value="{!! @$videoGuide['url'] !!}" />
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Status</label>
                                        <div class="col-sm-9">

                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" name="is_active"
                                                    id="is_active1" value="1" @if (isset($videoGuide->is_active) && $videoGuide->is_active == 1) checked @endif required>
                                                <label for="is_active1" class="custom-control-label">Active</label>
                                            </div>

                                            <div class="custom-control custom-radio">
                                                <input class="custom-control-input" type="radio" name="is_active"
                                                    id="is_active2" value="0" @if (isset($videoGuide->is_active) && $videoGuide->is_active == 0) checked @endif>
                                                <label for="is_active2" class="custom-control-label">Inactive</label>
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-actions text-right">

                                        <a href="{{ url('/admin/video-guides') }}" class="btn btn-default btn-cancel"> <i
                                                class="icons icon-arrow-left-circle"></i> Cancel</a>

                                        @if (isset($action) && $action == 'Add')
                                            <button type="submit" class="btn btn-primary youtube-url"
                                                onclick="validateYouTubeUrl()"><i class="icons icon-check"></i>
                                                Save</button>
                                        @else
                                            <button type="submit" class="btn btn-primary youtube-url" onclick="validateYouTubeUrl()"><i
                                                    class="icons icon-check"></i>
                                                Update</button>
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


@endsection
@section('scripts')
    <script>

        function validateYouTubeUrl() {
            var url = $('.youtube-url-cls').val();
            if (url != undefined || url != '') {
                var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
                var match = url.match(regExp);
                if (match && match[2].length == 11) {
                        return true;
                } else {
                    $('.youtube-url').attr('disabled', true);
                    // Do anything for not being valid
                    Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'URL not valid!',
                    })


                    $('.youtube-url-cls').on('keydown',function(){
                        $('.youtube-url').attr('disabled', false);

                    });

                }


            }
        }

    </script>
@endsection
