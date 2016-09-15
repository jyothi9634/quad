@extends('app')
@section('content')
@inject('common', 'App\Components\CommonComponent')

<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
<div class="main">
    <div class="container">
        <span class="pull-left"><h1 class="page-title">Order Confirmation</h1></span>
        <div class="clearfix"></div>

        <div class="col-md-12 padding-none">
            <div class="main-inner"> 
                <!-- Right Section Starts Here -->
                <div class="main-right">
                    <div class="col-md-12 inner-block-bg confirm-message">
                        <span class="red">Thank you for your {{ $count }} 
                        @if($count==1)
                        Order
                        @else 
                        Orders
                        @endif</span>
                        <br>
                        <span>You can manage your 
                        @if($count==1)
                        Order
                        @else 
                        Orders
                        @endif
                            in the <a href="{{ url('orders/buyer_orders') }}">Orders</a> Section</span>
                    </div>

                    <span class="pull-left"><h1 class="page-title">Order Details</h1></span>

                    <!-- Table Starts Here -->

                    <div class="table-div table-style1 padding-none">
                        
                        <!-- Table Head Starts Here -->

                        <div class="table-heading inner-block-bg">
                            <div class="col-md-2 padding-left-none">Order #</div>
                            <div class="col-md-2 padding-left-none">Vendor Name</div>
                            @if(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY || Session::get('service_id') == RELOCATION_OFFICE_MOVE)
                            <div class="col-md-2 padding-left-none">City</div>
                            @else
                            <div class="col-md-2 padding-left-none">From</div>
                            <div class="col-md-2 padding-left-none">To</div>
                            @endif
                            <div class="col-md-2 padding-left-none">Dispatch Date</div>
                            @if(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY)
                            <div class="col-md-3 padding-left-none">Service</div>
                            @else
                            <div class="col-md-1 padding-left-none">Service</div>
                            @endif
                            <div class="col-md-1 padding-left-none">Price</div>
                        </div>

                        <!-- Table Head Ends Here -->

                        <div class="table-data"> 

                            <!-- Table Row Starts Here -->
                            @foreach($orderData as $key=>$order_data)
                            <div class="table-row inner-block-bg">
                                <div class="col-md-2 padding-left-none word-wrap">{!! $order_data->order_id !!}</div>
                                <div class="col-md-2 padding-left-none">{!! $order_data->username !!}</div>
                                @if(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY)
                                <div class="col-md-2 padding-left-none">{!! $order_data->to_location !!}</div>
                                @else
                                <div class="col-md-2 padding-left-none">{!! $order_data->from_location !!}</div>
                                @endif
                                @if(Session::get('service_id') != RELOCATION_GLOBAL_MOBILITY)
                                @if(Session::get('service_id') != ROAD_TRUCK_LEASE && Session::get('service_id') != RELOCATION_OFFICE_MOVE)
                                <div class="col-md-2 padding-left-none">{!! $order_data->to_location !!}</div>
                                @else
                                        @if(Session::get('service_id') != RELOCATION_OFFICE_MOVE)
                                            <div class="col-md-2 padding-left-none">N/A</div>
                                        @endif
                                @endif
                                @endif
                                <div class="col-md-2 padding-left-none">{!! $common->checkAndGetDate($order_data->dispatch_date) !!}</div>
                                @if(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY)
                                <div class="col-md-3 padding-left-none">{!! $order_data->service_name !!}</div>
                                @else
                                <div class="col-md-1 padding-left-none">{!! $order_data->service_name !!}</div>
                                @endif
                                <div class="col-md-1 padding-none word-wrap">{!! $common->moneyFormat($order_data->order_total_amount) !!}/-</div>                                
                            </div>
                            @endforeach              


                            <!-- Table Row Ends Here -->

                        </div>
                        <div class="table-heading inner-block-bg total-payment">
                            <div class="col-md-12 padding-none text-right">
                                Amount Paid <span class="big-value"> Rs. {{ $common->moneyFormat($order_confirm_total) }} /-</span>    
                            </div>
                        </div>
                    </div>  

                    <!-- Table Starts Here -->


                </div>  

                <!-- Right Section Ends Here -->

            </div>
        </div>

    </div><!-- container -->
</div> <!-- main -->
@include('partials.footer')
@endsection