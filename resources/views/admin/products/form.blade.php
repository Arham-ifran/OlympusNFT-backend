@extends('admin.app')
@section('styles')
<link rel="stylesheet" href="{{ _asset('backend/plugins/datepicker/datepicker.css') }}">
@endsection
@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Products</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/admin') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/admin/stores') }}">Products</a></li>
                    <li class="breadcrumb-item active">{!!$action!!} Product</li>
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
                            {!!$action!!} Product
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

                        <form id="profile-form" name="profile-form" method="POST" action="{{url('/admin/products')}}" class="form-horizontal form-validate setting-form" novalidate="novalidate" enctype="multipart/form-data">
                            <input type="hidden" name="action" value="{!!$action!!}">
                            <input type="hidden" name="id" value="{!!@$product['id']!!}">
                            {{ csrf_field() }}
                            <div class="card-body">
                            <div class="form-group">
                                    <label for="store_id " class="control-label">Store</label>
                                    <select class="form-control" name="store_id" id="store_id">
                                        <option>--Select Store--</option>
                                        @foreach ($stores as $store)
                                            
                                        @if(@$product->store_id== $store->id  and $action=="Edit")
                                        @php
                                        $selected = 'selected=selected';
                                        @endphp
                                        @else
                                        @php
                                        $selected = '';
                                        @endphp
                                        @endif
                                        <option  {{$selected}} value="{!!@$store['id']!!}">{{$store->store_title}}</option>
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
                                        <option  {{$selected}} value="{!!@$category['id']!!}">{{$category->title}}</option>
                                        @endforeach
                                        </select>
                                </div>        
                                <div class="form-group">
                                    <label for="title" class="control-label">Title</label>
                                    <div class="col-sm-12">
                                        <input type="text" id="title" name="title" class='form-control' placeholder="Enter Title" value="{!!@$product['title']!!}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="sub_title" class="control-label">Sub Title</label>
                                    <div class="col-sm-12">
                                        <input type="text" id="sub_title" name="sub_title" class='form-control' placeholder="Enter Sub Title" data-rule-required="true" aria-required="true" value="{!!@$product['sub_title']!!}" />
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Description</label>
                                    <div class="col-sm-8">

                                        <textarea data-rule-required="true" aria-required="true" id="description" name="description" class='form-control ' rows="10">{!!@$product['description']!!}</textarea>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Price Type</label>
                                    <div class="col-sm-9">
                                    <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="price_type" id="price_type1" value="0" @if(isset($product->price_type) && $product->price_type == 0) checked @endif required>
                                            <label for="price_type1" class="custom-control-label">Fixed</label>
                                    </div>

                                    <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="price_type" id="price_type2" value="1" @if(isset($product->price_type) && $product->price_type == 1) checked @endif>
                                            <label for="price_type2" class="custom-control-label">Auction</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="price_type" id="price_type3" value="2" @if(isset($product->price_type) && $product->price_type == 2) checked @endif>
                                            <label for="price_type3" class="custom-control-label">Auction with Buy Now	</label>
                                    </div>
                                        
                                    </div>
                                </div>
                                <div class="auction">
                                    <div class="form-group auction">
                                        <label for="store_id " class="control-label">Auction Lengths</label>
                                        <select class="form-control" name="store_id " id="store_id ">
                                            <option>--Select Auction Length--</option>
                                            @foreach ($auction_lengths as $auction_length)
                                                
                                            @if(@$product->auction_length_id == $auction_length->id  and $action=="Edit")
                                            @php
                                            $selected = 'selected=selected';
                                            @endphp
                                            @else
                                            @php
                                            $selected = '';
                                            @endphp
                                            @endif
                                            <option  {{$selected}} value="{!!@$auction_length['id']!!}">{{$auction_length->title}}</option>
                                            @endforeach
                                            </select>
                                    </div> 
                                </div>
                                <div class="fixed">
                                <div class="form-group">
                                    <label for="price_usd" class="control-label">Price USD</label>
                                    <div class="col-sm-12">
                                        <input type="text" id="price_usd" name="price_usd" class='form-control' placeholder="Price USD" data-rule-required="true" aria-required="true" value="{!!@$product['price_usd']!!}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="price_eth" class="control-label">Price ETH</label>
                                    <div class="col-sm-12">
                                        <input type="text" id="price_eth" name="price_eth" class='form-control' placeholder="Price ETH" data-rule-required="true" aria-required="true" value="{!!@$product['price_eth']!!}" />
                                    </div>
                                </div>
                                </div>
                                <div class="auction">
                                <div class="form-group">
                                    <label for="price_usd" class="control-label">Bid Price USD</label>
                                    <div class="col-sm-12">
                                        <input type="text" id="bid_price_usd" name="bid_price_usd" class='form-control' placeholder="Price USD" data-rule-required="true" aria-required="true" value="{!!@$product['bid_price_usd']!!}" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="price_eth" class="control-label">Bid Price ETH</label>
                                    <div class="col-sm-12">
                                        <input type="text" id="price_eth" name="price_eth" class='form-control' placeholder="Price ETH" data-rule-required="true" aria-required="true" value="{!!@$product['bid_price_eth']!!}" />
                                    </div>
                                </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Transfer Copyright When Purchased</label>
                                    <div class="col-sm-9">

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="transfer_copyright_when_purchased" id="transfer_copyright_when_purchased1" value="1" @if(isset($product->transfer_copyright_when_purchased) && $product->transfer_copyright_when_purchased == 1) checked @endif required>
                                            <label for="transfer_copyright_when_purchased1" class="custom-control-label">yes</label>
                                        </div>

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="transfer_copyright_when_purchased" id="transfer_copyright_when_purchased2" value="0" @if(isset($product->transfer_copyright_when_purchased) && $product->transfer_copyright_when_purchased == 0) checked @endif>
                                            <label for="transfer_copyright_when_purchased2" class="custom-control-label">No</label>
                                        </div>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-3 control-label">Status</label>
                                    <div class="col-sm-9">

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="is_active" id="is_active1" value="1" @if(isset($product->is_active) && $product->is_active == 1) checked @endif required>
                                            <label for="is_active1" class="custom-control-label">Active</label>
                                        </div>

                                        <div class="custom-control custom-radio">
                                            <input class="custom-control-input" type="radio" name="is_active" id="is_active2" value="0" @if(isset($product->is_active) && $product->is_active == 0) checked @endif>
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
<script>
$(function() {
    $(".auction").hide();
   $("input[name='price_type']").click(function() {
    
     if ($("#price_type2").is(":checked")) {
        $(".auction").show();
        $(".fixed").hide();
     } else if ($("#price_type3").is(":checked")) {
        $(".auction").show();
        $(".fixed").show();
     }
    else {
       $(".auction").hide();
       $(".fixed").show();
     }
   });
 });


</script>
@endsection