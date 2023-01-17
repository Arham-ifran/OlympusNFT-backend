@extends('admin.app')
@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Update Profile</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/users') }}">Users</a></li>
                    <li class="breadcrumb-item active">Update Profile</li>
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
                            Update Profile
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class=" col-md-10 col-md-offset-1  p-t-30 ">
                        <form id="profile-form" name="profile-form" method="POST" action="{{url('/admin/update-profile')}}" class="form-horizontal form-validate setting-form" novalidate="novalidate" enctype="multipart/form-data">
                            {{ csrf_field() }}
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-sm-4">
                                        <div class="form-group">
                                            <label for="photo">Profile Image</label>
                                            <div class="col-sm-12">
                                                <img src="{!! checkImage(asset('storage/uploads/admins/'.@$user['id'].'/'.@$user['photo'])) !!}" class="image-display " id="profile_image" style="width:  150px;border:  1px solid #ccc;" />
                                                <input type="file" accept="image/*" onchange="change_image(this, 'profile_image', 'imgShow')" class="form-control" name="photo" id="photo">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label for="first_name" class="col-sm-3 control-label">First Name</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="firstname" name="firstname" class='form-control' placeholder="Enter First Name" data-rule-required="true" aria-required="true" value="{!!@$user['firstname']!!}" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="last_name" class="col-sm-3 control-label">Last Name</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="lastname" name="lastname" class='form-control' placeholder="Enter First Name" data-rule-required="true" aria-required="true" value="{!!@$user['lastname']!!}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="email" class="col-sm-3 control-label">Email</label>
                                            <div class="col-sm-9">
                                                <input type="text" readonly class='form-control' value="{!!@$user['email']!!}">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="phone" class="col-sm-3 control-label">Phone</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="mobile" name="mobile" class='form-control' placeholder="Enter Phone" data-rule-required="true" data-rule-minlength="10" aria-required="true" value="{!!@$user['mobile']!!}" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="address" class="col-sm-3 control-label">Address</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="address" name="address" class='form-control' placeholder="Enter Address" data-rule-required="true" data-rule-minlength="5" aria-required="true" value="{!!@$user['address']!!}" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="zipcode" class="col-sm-3 control-label">Zip</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="address" name="zipcode" class='form-control' placeholder="Enter Zip" data-rule-required="true" data-rule-minlength="5" aria-required="true" value="{!!@$user['zipcode']!!}" />
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="country" class="col-sm-3 control-label">Country</label>
                                            <div class="col-sm-9">
                                                <select name="country" id="country" class='select2-me form-control'>
                                                    @foreach($countries as $country)
                                                    <option @if($country->id == $user['country']) selected @endif value="{!!$country->id!!}">{!!$country->name!!}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="city" class="col-sm-3 control-label">City</label>
                                            <div class="col-sm-9">
                                                <input type="text" id="city" name="city" class='form-control' placeholder="Enter City" data-rule-required="true" aria-required="true" value="{!!@$user['city']!!}">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="password" class="col-sm-3 control-label">New Password</label>
                                            <div class="col-sm-9">
                                                <input type="password" id="password" name="password" class='form-control' placeholder="Enter new password" data-rule-minlength="8" value="" autocomplete="off" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="confirm_password" class="col-sm-3 control-label">Confirm Password</label>
                                            <div class="col-sm-9">
                                                <input type="password" id="confirm_password" name="confirm_password" class='form-control' placeholder="Retype new password" data-rule-equalto="#password" data-rule-minlength="8" value="" autocomplete="off" />
                                            </div>
                                        </div>

                                        <div class="form-actions text-right">

                                            <a href="{{ url('/admin') }}" class="btn btn-default btn-cancel"> <i
                                                class="icons icon-arrow-left-circle"></i> Cancel</a>
                                            <input type="submit" class='btn btn-primary' value="Save Changes">
                                        </div>
                                    </div>
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
