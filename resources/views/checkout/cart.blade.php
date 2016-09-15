@extends('app')
@section('content')
@inject('common', 'App\Components\CommonComponent')

<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<div class="main">
    <div class="container">
        @if(Session::has('message')  && Session::get('message')!='') 
         <div class="flash">
        <p class="text-success col-sm-12 text-center flash-txt alert-success">
        {{ Session::get('message') }}
        </p>
        </div>
        @endif
        @if(isset($cart_items_count) && ($cart_items_count > 0))
        <span class="pull-left"><h1 class="page-title">Shopping Cart</h1> <b>({{$cart_items_count}} Items)</b></span>
        <!--span class="pull-right"><button class="btn red-btn flat-btn top-btn">Checkout</button></span-->
         <div class="pull-right margin-bottom"><a href="{{url('checkout')}}" class="btn red-btn flat-btn pull-right">Checkout</a></div>
{{--*/  $serveId = $common->getServiceName(Session::get('service_id'))  /*--}}
        <div class="clearfix"></div>
        <div class="col-md-12 padding-none">
            <div class="main-inner"> 
                <!-- Right Section Starts Here -->
                <div class="main-right">
                    <!-- Table Starts Here -->
                    <div class="table-div padding-none">
                        <!-- Table Head Starts Here -->

                        <div class="table-heading inner-block-bg">
                            
                            <div class="col-md-2 padding-left-none">Vendor Name</div>
                             @if(Session::get('service_id') == RELOCATION_OFFICE_MOVE || Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY)
                                <div class="col-md-4 padding-left-none">City</div>
                             @else
                                <div class="col-md-2 padding-left-none">From</div>
                                <div class="col-md-2 padding-left-none">To</div>
                            @endif
                                                      
                            <div class="col-md-2 padding-left-none">Dispatch Date</div>
                            <div class="col-md-1 padding-left-none">Service</div>
                            <div class="col-md-1 padding-left-none">Price</div>
                            <div class="col-md-1 padding-left-none"></div>
                        </div>

                        <!-- Table Head Ends Here -->

                        <div class="table-data">
                            
                          
                            <!-- Table Row Starts Here -->
                             @foreach($cart_items as $key=>$cart_item)
                            <div class="table-row inner-block-bg">
                                
                                <div class="col-md-2 padding-left-none">{!! $cart_item->username !!}</div>
                               
                                 @if(Session::get('service_id') == RELOCATION_OFFICE_MOVE )
                                    <div class="col-md-4 padding-left-none">{!! $cart_item->from_location !!}</div>
                                 @elseif(Session::get('service_id') != RELOCATION_GLOBAL_MOBILITY )
                                    <div class="col-md-2 padding-left-none">{!! $cart_item->from_location !!}</div>
                                 @endif

                                @if(Session::get('service_id') != RELOCATION_OFFICE_MOVE)
                                    @if(Session::get('service_id') != ROAD_TRUCK_LEASE)
                                        @if(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY)
                                        <div class="col-md-4 padding-left-none">{!! $cart_item->to_location !!}</div>
                                        @else
                                        <div class="col-md-2 padding-left-none">{!! $cart_item->to_location !!}</div>
                                        @endif
                                    @else
                                     <div class="col-md-2 padding-left-none">N/A</div>
                                    @endif
                               
                                @endif
                                <div class="col-md-2 padding-left-none">{!! $common->checkAndGetDate($cart_item->dispatch_date) !!}</div>
                                                               
                                <div class="col-md-1 padding-left-none">{!! $cart_item->service_name !!}</div>
                                <div class="col-md-2 padding-none">{!! $common->moneyFormat($cart_item->price) !!}/-</div>
                                <div class="col-md-1 padding-none text-center">
                                <a data-toggle="modal" data-target="#deletecartitemmodal" onclick="setcartitem({!! $cart_item->id !!})" >
                                    <i class="fa fa-trash red" title="Delete"></i></a></div>
                            </div>
                            @endforeach
                            <!-- Table Row Ends Here -->

                           

                        </div>
                        <div class="table-heading inner-block-bg total-payment">
                            <div class="col-md-12 padding-none text-right">
                                Amount Payable <span class="big-value"> Rs. {{$order_total}} /-</span>    
                            </div>
                        </div>
                    </div>  

                    <!--button class="btn add-btn flat-btn pull-left margin-none">Continue Shopping</button-->  
                     <a href="{{url('buyerposts')}}" class="btn add-btn flat-btn pull-left margin-none">Continue Shopping</a> 
                    <!--button class="btn add-btn flat-btn pull-left">Clear Cart</button-->  
                     <a href="{{url('clearcart')}}" class="btn add-btn flat-btn pull-left">Clear Cart</a>
                    
                    <!--button class="btn red-btn flat-btn pull-right">Checkout</button-->   
                    <a href="{{url('checkout')}}" class="btn red-btn flat-btn pull-right">Checkout</a>
                    <!-- Table Starts Here -->

                </div>  

                <!-- Right Section Ends Here -->

            </div>
        </div>
        @else 
            <div class="col-md-12 col-sm-12 col-xs-12 text-center padding-none">
                <p>You have no items in your shopping cart.</p>
                <!--<p>Click <a href="{{url('buyerposts')}}"> here</a> to continue shopping.</p>-->
            </div>
        @endif
<!-- end of main divs -->
    </div><!--container -->
</div><!-- main -->        
@include('partials.footer')



 
@endsection