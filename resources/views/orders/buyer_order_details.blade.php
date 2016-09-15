@extends('app') @section('content')
<div class="container container-inner">
    
    <!-- Left Nav Starts Here -->
    @include('partials.leftnav')
    <!-- Left Nav Ends Here -->
    <!-- Page Center Content Starts Here -->

    <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
        @if (Session::has('cancelsuccessmessage'))
                <div class="flash alert-info">
                    <p class="text-success col-sm-12 text-center flash-txt">{{
                        Session::get('cancelsuccessmessage') }}</p>
                </div>
            @endif

        <div class="block">
            <div class="tab-nav underline">
                <!--<ul id="tabs">
                        <li><a href="#">Message<span class="red superscript">9</span></a></li>
                        <li><a href="#">Posts<span class="red superscript">9</span></a></li>
                        <li class="active"><a href="#">Orders<span class="red superscript">9</span></a></li>
                        <li><a href="#">Network<span class="red superscript">9</span></a></li>
                        <span class="post-but pull-right"><a href="#">+ Post</a></span>
                </ul>-->
                @include('partials.page_top_navigation')
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12 padding-top">

                <h5>
                    <div class="col-md-6 col-sm-6 col-xs-6 padding-none form-group">
                        <b>Full Truck Order No</b>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 padding-none form-group">
                        <b>{!! $orderDetails->order_no !!}</b>
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 padding-none form-group">
                        <b>{{$orderDetails->username}} | Rs. {{number_format($orderDetails->orderprice,2)}}</b>
                    </div>
                        
                    <div class="clearfix"></div>

                </h5>
                <div class="col-md-6 col-sm-6 col-xs-6 padding-none">

                    <p>Full Truck Post No</p>

                    <p>Vehicle Type</p>
                    <p>Product Type</p>
                    <p>From Location</p>
                    <p>To Location</p>
                    <p>Date</p>
                    
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 padding-none">
                    <p>@if($orderDetails->trans_id)
                        {!! $orderDetails->trans_id !!}
                    @else &nbsp;
                            @endif
                    </p>

                    <p>@if($orderDetails->vehicle_type)
                        {!! $orderDetails->vehicle_type !!}
                        @else &nbsp;
                            @endif
                    </p>
                    <p>@if($orderDetails->load_type)
                        {!! $orderDetails->load_type !!}
                        @else &nbsp;
                            @endif
                    </p>
                    <p>@if($orderDetails->from_city)
                        {!! $orderDetails->from_city!!}
                        @else &nbsp;
                            @endif
                    </p>
                    <p>@if($orderDetails->to_city)
                        {!! $orderDetails->to_city !!}
                        @else &nbsp;
                            @endif
                    </p>
                    <p>{{date("d/m/Y", strtotime($orderDetails->dispatch_date))}} - {{date("d/m/Y", strtotime($orderDetails->delivery_date))}}
                        <span class="pull-right spot_transaction_details hidden-xs">Details 
                                <span style="display: inline;" class="show_details">+</span>
                                <span style="display: none;" class="hide_details">-</span>
                        </span>
                    </p>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12 padding-none spot_transaction_details_view">
                    <div class="col-md-6 col-sm-6 col-xs-6 padding-none">
                        <p>Consignee</p>
                        <p>Status</p>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 padding-none">
                    <p>@if($orderDetails->buyer_consignee_name)
                        {!! $orderDetails->buyer_consignee_name !!}
                        @else &nbsp;
                            @endif
                    </p>
                    <p>@if($orderDetails->order_status)
                        {!! $orderDetails->order_status !!}
                        @else &nbsp;
                            @endif
                    </p>
                    </div>
                </div>
                <?php //echo strtotime($cancel_book_date)." erwerwe ".strtotime(date ( 'Y-m-d H:i:s' ));?>
                @if (strtotime($cancel_book_date)<=strtotime(date ( 'Y-m-d H:i:s' )))
                <div class="col-md-12 col-sm-12 col-xs-12 text-right padding-right-none margin-bottom">
                    <a href="/orders/cancel/{{$orderDetails->orderid}}"><span class="red">Cancel Booking</span></a>
                </div>
                @endif
                <div class="clearfix"></div>

            </div>
            <div class="block">
                <div
                    class="col-md-8 col-sm-8 col-xs-12 padding-left-none padding-right-none count_block">
                    <div
                        class="col-md-4 col-sm-4 col-xs-4 padding-none margin-top text-center">
                        <a href="#">
                            <div class="margin-center">
                                <i class="fa fa-envelope"></i> <span
                                    class="red superscript-table">9</span>
                            </div> Messages
                        </a>
                    </div>
                    <div
                        class="col-md-4 col-sm-4 col-xs-4 padding-none margin-top text-center">
                        <a href="#">
                            <div class="margin-center">
                                <i class="fa fa-file-text-o"></i> <span
                                    class="red superscript-table">9</span>
                            </div> Status
                        </a>
                    </div>
                    <div
                        class="col-md-4 col-sm-4 col-xs-4 padding-none margin-top text-center">
                        <a href="#">
                            <div class="margin-center">
                                <i class="fa fa-file-text-o"></i> <span
                                    class="red superscript-table">0</span>
                            </div> Documents
                        </a>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>

            <div class="clearfix"></div>

            <div width="100%" class="table table-data border-top">
                <div class="col-md-12 col-sm-12 col-xs-12 padding-none text-left"><span class="detailsslide-document underline_link">Documents</span></div>
                <div class="clearfix"></div>
                <div class="col-md-12 col-sm-12 col-xs-12 padding-none pull-right table-slide-document" style="display: none;">
                    <div class="col-md-12 col-sm-12 col-xs-12 padding-top text-left">
                        <p><b>Non Commercial Consignment</b></p>
                        Declaration on Nature of Goods <br>
                        and purpose
                    </div>
                    <div class="col-md-12 col-sm-12 col-xs-12 padding-top text-left">
                        <p><b>Commercial Consignment</b></p>
                        Billing Document <br>
                        Stock Transfer Document <br>
                        VAT Forms <br>
                        Other Statutory Forms
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div width="100%" class="table table-data ">
                <div class="col-md-12 col-sm-12 col-xs-12 padding-none text-left"><span class="detailsslide-pricetrial underline_link">Price Trails</span></div>

                <div class="clearfix"></div>
                <div class="col-md-12 col-sm-12 col-xs-12 padding-none pull-right table-slide-pricetrial" style="display: none;">
                    <div class="table table-head" width="100%">
                            <div class="col-md-12 padding-none">
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Quote</div>
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Price</div>
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Date</div>
                            </div>
                        </div>
                        <div class="table table-row" id="pick_vehicles" width="100%">
                            @if($priceDetails->initial_quote_price && $priceDetails->initial_quote_price!=0)
                            <div class="col-md-12 padding-none">
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Initial Quote</div>
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{$priceDetails->initial_quote_price}}</div>
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{($priceDetails->initial_quote_created_at == '0000-00-00 00:00:00') ? '' : date("d/m/Y", strtotime($priceDetails->initial_quote_created_at))}}</div>
                            </div>
                            @endif
                            @if($priceDetails->counter_quote_price && $priceDetails->counter_quote_price!=0)
                            <div class="col-md-12 padding-none">
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Counter Quote</div>
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{$priceDetails->counter_quote_price}}</div>
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{($priceDetails->counter_quote_created_at == '0000-00-00 00:00:00') ? '' : date("d/m/Y", strtotime($priceDetails->counter_quote_created_at))}}</div>
                            </div>
                            @endif
                            @if($priceDetails->final_quote_price && $priceDetails->final_quote_price!=0)
                            <div class="col-md-12 padding-none">
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Final Quote</div>
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{$priceDetails->final_quote_price}}</div>
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{($priceDetails->final_quote_created_at == '0000-00-00 00:00:00') ? '' : date("d/m/Y", strtotime($priceDetails->final_quote_created_at))}}</div>
                            </div>
                            @endif
                            
                        </div>
                </div>
                <div class="clearfix"></div>
            </div>
            <div width="100%" class="table table-data ">
                <div class="col-md-12 col-sm-12 col-xs-12 padding-none text-left underline_link">Approval</div>

                <div class="clearfix"></div>
            </div>





        </div>
    </div>
    <!-- Page Center Content Ends Here -->
    <!-- Right Starts Here -->
    @include('partials.right')
    <!-- Right Ends Here -->

</div>
@endsection
