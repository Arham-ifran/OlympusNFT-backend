@extends('admin.app')
@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Languages</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/languages') }}">Languages</a></li>
                    <li class="breadcrumb-item active">{!!$action!!} Languages</li>
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
                            {!!$action!!}  Language
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

                        <form id="profile-form" name="profile-form" method="POST" action="{{url('/admin/languages')}}" class="form-horizontal form-validate setting-form" novalidate="novalidate" enctype="multipart/form-data">
                            <input type="hidden" name="id" value="{!!@$language->id!!}">
                            <input type="hidden" name="action" value="{!!$action!!}">
                            {{ csrf_field() }}
                            <div class="card-body">

                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Name</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" data-rule-required="true" aria-required="true" value="{!!@$language['name']!!}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Code</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="code" id="code" placeholder="Enter Code" data-rule-required="true" aria-required="true" value="{!!@$language['code']!!}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Symbol</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="symbol" id="symbol" placeholder="Enter Symbol" data-rule-required="true" aria-required="true" value="{!!@$language['symbol']!!}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="flag">Profile Image</label>
                                    <div class="col-sm-12">
                                        <img src="{!! checkImage(asset('storage/uploads/flag/'.@$language['id'].'/'.@$language['flag'])) !!}" class="image-display " id="flag_image" style="width:  150px;border:  1px solid #ccc;" />
                                        <input type="file" accept="image/*" onchange="change_image(this, 'flag_image', 'imgShow')" class="form-control" name="flag" id="flag">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Status</label>
                                    <div class="col-sm-9">

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="is_active" id="is_active1" value="1" @if(isset($language->is_active) && $language->is_active == 1) checked @endif required>
                                            <label for="is_active1" class="custom-control-label">Active</label>
                                        </div>

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="is_active" id="is_active2" value="0" @if(isset($cmsPage->is_active) && $cmsPage->is_active == 0) checked @endif>
                                            <label for="is_active2" class="custom-control-label">Inactive</label>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-actions text-right">

                                    <a href="{{url('/admin/languages')}}" class="btn btn-default btn-cancel"> <i class="icons icon-arrow-left-circle"></i> Cancel</a>

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


@endsection
