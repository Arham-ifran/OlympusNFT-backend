@extends('admin.app')
@section('content')

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Contact Us Log</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/contactus-log') }}">Contact Us Log</a></li>
                    <li class="breadcrumb-item active">Log</li>
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
                            Log Detail
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class=" col-md-10 col-md-offset-1  p-t-30 ">
                        <br />
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-4 col-md-4">
                                    <label class="input-item-label">Full Name:</label>
                                    <span>{{$contact->fullname}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-md-4">
                                    <label class="input-item-label">Email:</label>
                                    <span>{{$contact->email}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-md-4">
                                    <label class="input-item-label">Phone:</label>
                                    <span>{{$contact->phone}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-md-4">
                                    <label class="input-item-label">Subject:</label>
                                    <span>{{$contact->subject}}</span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xl-4 col-md-4">
                                    <label class="input-item-label">Message:</label>
                                    <span>{!!nl2br($contact->message)!!}</span>
                                </div>
                            </div>

                        </div>




                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Reply User
                                </h3>
                            </div>

                            <form id="profile-form" name="profile-form" class="form-horizontal form-validate setting-form" novalidate="novalidate" name="msg-form" id="msg-form" action="{{route('admin.contactus-log.send_email')}}" method="post">
                                <div class="card-body">
                                    @csrf

                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <label class="col-sm-12 control-label">Message</label>

                                            <textarea rows="5" cols="5" class="form-control" name="message" id="message" placeholder="Type Message here" data-rule-required="true" aria-required="true" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="form-group">
                                            <input type="hidden" name="id" value="{{$contact->id}}" />
                                            <button type="submit" id="submitBttn" class="btn btn-success btn-block">Send Email</button>
                                        </div>
                                    </div>

                                </div>
                            </form>


                        </div>

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