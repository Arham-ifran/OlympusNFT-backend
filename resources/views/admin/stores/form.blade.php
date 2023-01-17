@extends('admin.app')
@section('styles')
<link rel="stylesheet" href="{{ _asset('backend/plugins/datepicker/datepicker.css') }}">
@endsection
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Stores</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/stores') }}">Stores</a></li>
                    <li class="breadcrumb-item active">{!!$action!!} Store</li>
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
                            {!!$action!!} Store
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

                        <form id="profile-form" name="profile-form" method="POST" action="{{url('/admin/stores')}}" class="form-horizontal form-validate setting-form" novalidate="novalidate" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="{!!$action!!}">
                            <input type="hidden" name="id" value="{!!@$store['id']!!}">
                            {{ csrf_field() }}
                            <div class="card-body">
                            <div class="form-group">
                                <label for="category_id" class="control-label">User</label>
                                <select class="form-control" name="user_id" id="category_id">
                                    <option>--Select User--</option>
                                    @foreach ($users as $user)
                                        
                                    @if(@$store->user_id== $user->id and $action=="Edit")
                                    @php
                                    $selected = 'selected=selected';
                                    @endphp
                                    @else
                                    @php
                                    $selected = '';
                                    @endphp
                                    @endif
                                      <option  {{$selected}} value="{{$user->id}}">{{$user->username}}</option>
                                    @endforeach
                                    </select>
                                </div>
                            <div class="form-group">
                                <label for="category_id" class="control-label">Category</label>
                                <select class="form-control" name="category_id" id="category_id">
                                    <option>--Select Category--</option>
                                    @foreach ($categories as $category)
                                        
                                    @if(@$store->category_id== $category->id and $action=="Edit")
                                    @php
                                    $selected = 'selected=selected';
                                    @endphp
                                    @else
                                    @php
                                    $selected = '';
                                    @endphp
                                    @endif
                                      <option  {{$selected}} value="{{$category->id}}">{{$category->title}}</option>
                                    @endforeach
                                    </select>
                            </div>
                               
                                <div class="form-group">
                                    <label for="store_title" class="control-label">Store Title</label>
                                    <div class="col-sm-12">
                                        <input type="text" id="store_title" name="store_title" class='form-control' placeholder="Enter Store Title" value="{!!@$store['store_title']!!}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sub_title" class="control-label">Sub Title</label>
                                    <div class="col-sm-12">
                                        <input type="text" id="sub_title" name="sub_title" class='form-control' placeholder="Enter Sub Title" data-rule-required="true" aria-required="true" value="{!!@$store['sub_title']!!}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                <label for="store_tags" class="control-label">Tag (add comma sepereted)</label>
                                <div class="col-sm-12">
                                    <input type="text" id="store_tags" name="store_tags" class='form-control' placeholder="Enter Tags" data-rule-required="true" aria-required="true" value="{!!@$store['store_tags']!!}">
                                </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Description</label>
                                    <div class="col-sm-8">

                                        <textarea data-rule-required="true" aria-required="true" id="description" name="description" class='form-control ' rows="10">{!!@$store['description']!!}</textarea>

                                    </div>
                                </div>
                                
                                <div class="form-group">
                                <label for="store_your_data" class="control-label">Store your Data</label>
                                <div class="col-sm-12">
                                    <select class="form-control" name="store_your_data" id="store_your_data">
                                        <option>--Select Option--</option>
                                        <option value="0">Mintable</option>
                                        <option value="1">IPFS (Cannot batch mint)</option>
                                        <option value="2">My own servers (Super Advanced, please read the docs)</option>
                                    </select>
                                </div>

                                </div

                               
                                <div class="form-group">
                                <label for="royalty_amount" class="control-label">Royalty Amount</label>
                                <div class="col-sm-12">
                                    <select class="form-control" name="royalty_amount" id="royalty_amount">
                                        <option>--Select Option--</option>
                                        <option value="0">Mintable</option>
                                        <option value="10">10%</option>
                                        <option value="40">40%</option>
                                        <option value="60">60%</option>
                                        <option value="80">80%</option>
                                        <option value="90">90%</option>
                                    </select>
                                </div>


                                
                               
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Increase Batch Minting</label>
                                    <div class="col-sm-9">

                                        <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" name="increase_batch_minting" id="increase_batch_minting1" value="1" @if(isset($store->increase_batch_minting) && $seller->increase_batch_minting == 1) checked @endif >
                                        <label for="increase_batch_minting1" class="custom-control-label">Yes</label>
                                        </div>

                                        <div class="custom-control custom-radio">
                                        <input class="custom-control-input" type="radio" name="increase_batch_minting" id="increase_batch_minting2" value="0" @if(isset($store->increase_batch_minting) && $seller->increase_batch_minting == 0) checked @endif>
                                        <label for="increase_batch_minting2" class="custom-control-label">No</label
                                        </div>

                                    </div>
                                </div>

                                <div class="form-group">
                                            <label for="photo">Image</label>
                                            <div class="col-sm-12">
                                                <img src="{!! checkImage(asset('storage/uploads/stores/'.@$store['id'].'/'.@$store['photo'])) !!}" class="image-display " id="image" style="width:  150px;border:  1px solid #ccc;" />
                                                <input type="file" accept="image/*" onchange="change_image(this, 'image', 'imgShow')" class="form-control" name="image" id="image">
                                            </div>
                                        </div>
                                    </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Status</label>
                                    <div class="col-sm-9">

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="is_active" id="is_active1" value="1" @if(isset($store->is_active) && $store->is_active == 1) checked @endif required>
                                            <label for="is_active1" class="custom-control-label">Active</label>
                                        </div>

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="is_active" id="is_active2" value="0" @if(isset($store->is_active) && $store->is_active == 0) checked @endif>
                                            <label for="is_active2" class="custom-control-label">Inactive</label>
                                        </div>

                                    </div>
                                </div>

                                <div class="form-actions text-right">

                                    <a href="{{url('/admin/stores')}}" class="btn btn-default btn-cancel"> <i class="icons icon-arrow-left-circle"></i> Cancel</a>

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
@endsection
@section('scripts')




@endsection