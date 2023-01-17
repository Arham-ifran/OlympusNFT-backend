@extends('admin.app')
@section('content')

<style>
    .chat-holder {
        min-height: 400px;
        max-height: 400px;
        overflow-y: auto;
    }


    .chat-holder .sender-detail {
        background: #f1f1ff;
        border-radius: 7px;
    }

    .chat-holder img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .chat-holder .msgesLi {
        justify-content: space-between;
        flex-direction: unset;
        align-items: center;
    }

    .chat-holder .chat-messages .author-reply {
        flex-direction: row-reverse;
        align-items: center;
        justify-content: space-between;
    }
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Messages</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/messages') }}">Messages</a></li>
                    <li class="breadcrumb-item active">{!!$seller!!}'s Messages</li>
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
                            {!!$seller!!}'s Messages
                        </h3>
                    </div>
                    <!-- /.card-header -->
                    <!-- form start -->
                    <div class=" col-md-10 col-md-offset-1  p-t-30 ">
                        <div class="chat-holder p-3 mb-3">
                            <ul class="chat-messages list-unstyled" id="messages">

                                @foreach($current_thread->messages as $msg)
                                @if($msg->is_private == 1)
                                @php continue; @endphp
                                @endif

                                @php $msger = 'sender';@endphp

                                <li class="d-flex mb-2 msgesLi @if($msg->is_admin == 1) author-reply @endif">
                                    <div class="d-flex align-items-center img-name-wrapper">
                                        <div class="image-holder mr-2 mb-2">
                                            @if($msg->is_admin == 1)
                                            <img loading="lazy" src="{{asset('frontend/dashboard/images/admin.png')}}" alt="Admin">
                                            @else
                                            <img loading="lazy" src="{{$msg->$msger->photo()}}" alt="{{$msg->$msger->full_name()}}">
                                            @endif
                                        </div>
                                        <div class="sender-detail inner-chat-box d-flex flex-column p-2">
                                            <strong class="sender-name">
                                                @if($msg->is_admin == 1)
                                                Admin
                                                @else
                                                {{$msg->$msger->full_name()}}
                                                @endif
                                            </strong>
                                            <span class="sender-name">{{$msg->message}}</span>
                                        </div>
                                    </div>
                                    <time class="time-holder">{{$msg->get_date()}}</time>
                                </li>
                                @endforeach

                            </ul>
                        </div>
                        <div class="card card-primary">
                            <div class="card-header">
                                <h3 class="card-title">
                                    Send Message
                                </h3>
                            </div>

                            <form id="profile-form" name="profile-form" class="form-horizontal form-validate setting-form" novalidate="novalidate" name="msg-form" id="msg-form" action="{{route('admin.messages.sendMessage')}}" method="post">
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
                                            <input type="hidden" name="id" value="{{encode($current_thread->product_id)}}" />
                                            <input type="hidden" name="thread_id" value="{{$current_thread->id}}" />
                                            <input type="hidden" name="receiver_id" value="{{$current_thread->buyer_id}}" />
                                            <button type="submit" id="submitBttn" class="btn btn-success btn-block">Send <i class="far fa-paper-plane"></i></button>
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