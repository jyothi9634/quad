@extends('app')
@section('content')
<div class="main-container">	
    <div class="container container-inner">
        <!-- Left Nav Starts Here -->
        @include('partials.seller_leftnav')
        <!-- Left Nav Ends Here -->
        <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">

            <div class="block">
                <div class="tab-nav underline">
                    @include('partials.page_top_navigation')
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12 padding-top">

                    <h5>
                        <div class="form-group col-md-6 col-sm-6 col-xs-6 padding-none"><b>Full Truck Order No</b></div>
                        <div class="form-group col-md-6 col-sm-6 col-xs-6 padding-none">	<b>{{$order->order_no}}</b></div>
                        <div class="form-group col-md-12 col-sm-12 col-xs-12 padding-none"><b>{{$post->name}} | Rs. {{number_format($order->price,2)}}</b></div>
                        <div class="clearfix"></div>
                    </h5>
                    <div class="col-md-3 col-sm-3 col-xs-6 padding-none">

                        <p>Full Truck Post No</p>
                        <p>Vehicle Type</p>
                        <p>Product Type</p>								
                        <p>From Location</p>
                        <p>To Location</p>
                        <p>Date</p>
                        <p>Consignee</p>
                        <p>Status</p>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 padding-none">
                        <p>{{$post->transid}}</p>
                        <p>{{$post->vehicle}}</p>
                        <p>
                            @if($post->load)
                            {{$post->load}}
                            @else &nbsp;
                            @endif                            
                        </p>							
                        <p>{{$post->from}}</p>
                        <p>{{$post->to}}</p>
                        <p>{{date("d/m/Y", strtotime($post->dispatch))}} - {{date("d/m/Y", strtotime($post->delivery))}}</p> 
                        <p>
                            @if($order->buyer_consignee_name)
                            {{$order->buyer_consignee_name}}
                            @else &nbsp;
                            @endif
                        </p>
                        <p>{{$order->order_status}}</p>
                    </div>

                    <div class="clearfix"></div>

                </div>
                <div class="clearfix"></div>
                @if(empty($vehicleExist))
                <div class="col-md-12 col-sm-12 padding-none>

                    <div class="heading margin-bottom"><span><span class="medium-font">Step 1</span> Consignment Vehicle Details</span></div>

                    {!! Form::open(['url' => '','id'=>'posts-form-sellerpickup']) !!}
                    <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-none">
                        {!! Form::text('vehicle', '', ['id' => 'vehicle', 'placeholder' => 'Vehicle Number', 'class' => 'clsVehicleno']) !!}
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-right-none mobile-padding-none">
                        {!! Form::text('driver', '', ['id' => 'driver', 'placeholder' => 'Driver Name', 'class' => 'clsDrivername']) !!}
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-right-none mobile-padding-none">
                        {!! Form::text('mobile', '', ['id' => 'mobile', 'placeholder' => 'Driver Mobile Number', 'class'=>'clsMobileno']) !!}
                    </div>
                    <input type="submit" class="btn pull-right margin-bottom" id="add_vehicle" value="Add Vehicle" >
                    {!! Form::close() !!}
                    <div class="clearfix"></div>
                    <div class="table table-head" width="100%">
                        <div class="col-md-12 col-sm-12 col-xs-12 padding-none">
                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Vehicle No.</div>
                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Driver Name</div>
                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Driver Mobile</div>
                        </div>
                    </div>
                    <div  id="pick_vehicles" >
                        @foreach($vehicles as $vehicle)
                        <div class="table table-row"  width="100%">
                        <div class="col-md-12 padding-none">
                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{$vehicle->vehicle_no}}</div>
                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{$vehicle->driver_name}}</div>
                            <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{$vehicle->mobile}}</div>
                        </div>
                        </div>
                        @endforeach
                    </div>
                    <div class="clearfix"></div>
                    {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-vehicle']) !!}
                    <input type="hidden" name="vehicle_confirm" value="1">
                    <input type="button" class="btn pull-right margin-bottom truckhaul" data-toggle="modal" data-target="#truckhaul" value="Confirm">
                    {!! Form::close() !!}
                    <div class="clearfix"></div>

                </div>
                @endif

                @if(!empty($vehicleExist))

                <div class="col-md-12 col-sm-12 padding-none border-top consign_vehicle">
                    <div class="heading margin-bottom">
                        <span><span class="medium-font">Step 1</span> Consignment Vehicle Details</span>
                    </div>
                    <div class="clearfix"></div>
                    <div class="vehicle">
                        <div class="table table-head" width="100%">
                            <div class="col-md-12 padding-none">
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Vehicle No.</div>
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Driver Name</div>
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">Driver Mobile</div>
                            </div>
                        </div>
                        <div  id="pick_vehicles" >
                            @foreach($vehicles as $vehicle)
                            <div class="table table-row"  width="100%">
                            <div class="col-md-12 padding-none">
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{$vehicle->vehicle_no}}</div>
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{$vehicle->driver_name}}</div>
                                <div class="col-md-4 col-sm-4 col-xs-4 padding-none">{{$vehicle->mobile}}</div>
                            </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>
                @endif
                @if(empty($pickExist) && ($vehicleExist==1))
                <div class="col-md-12 col-sm-12 padding-none border-top">

                    <div class="heading margin-bottom">
                        <span><span class="medium-font">Step 2</span> Consignment Pickup Details</span>
                    </div>

                    <div class="clearfix"></div>
                    {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-pickup']) !!}
                    <div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group mobile-padding-none">
                        {!! Form::text('pick_date', '', ['id' => 'pick_date','class'=>"calendar", 'placeholder' => 'Pickup Date']) !!}

                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none form-group mobile-padding-none">
                        {!! Form::text('lr_no', '', ['id' => 'lr_no', 'placeholder' => 'LR Number', 'class' => 'clsLRnumber']) !!}

                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none form-group mobile-padding-none">
                        {!! Form::text('lr_date', '', ['id' => 'lr_date','class'=>"calendar", 'placeholder' => 'LR Date']) !!}

                    </div>
                    <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none form-group mobile-padding-none">
                        {!! Form::text('bill_no', '', ['id' => 'bill_no', 'placeholder' => 'Transporter bill no.', 'class' => 'clsTransporterBill']) !!}

                    </div>
                    <div class="clearfix"></div>
                    <p class="margin-bottom margin-top">Additional Fields Optional</p>
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-none form-group mobile-padding-none">
                        {!! Form::text('info1', '', ['id' => 'info1', 'placeholder' => 'Additional Fields 1']) !!}

                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-right-none form-group mobile-padding-none">
                        {!! Form::text('info2', '', ['id' => 'info2', 'placeholder' => 'Additional Fields 2']) !!}

                    </div>
                    <input type="submit" class="btn pull-right margin-bottom" value="Confirm">
                    {!! Form::close() !!}
                </div>
                @endif

                @if(!empty($pickExist)&& ($vehicleExist==1))
                <div class="col-md-12 col-sm-12 padding-none border-top consign_pickup">
                    <div class="heading margin-bottom">
                        <span><span class="medium-font">Step 2</span> Consignment Pickup Details</span>
                    </div>
                    <div class="clearfix"></div>
                    <div class="pickup">
                        <div class="clearfix"></div>
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-none">Pickup Date: {{date("d/m/Y", strtotime($order->seller_pickup_date))}}</div>
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none">LR Number: {{$order->seller_pickup_lr_number}}</div>
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none">LR Date: {{date("d/m/Y", strtotime($order->seller_pickup_lr_date))}}</div>
                        <div class="col-md-3 col-sm-3 col-xs-12 padding-right-none">Transporter bill no: {{$order->seller_pickup_transport_bill_no}}</div>
                        <p class="margin-bottom">Additional Fields Optional</p>
                        <div class="col-md-4 col-sm-4 col-xs-12 padding-none">Optional 1: {{$order->seller_pickup_customer_doc_one}}</div>
                        <div class="col-md-4 col-sm-4 col-xs-12 padding-right-none">Optional 2: {{$order->seller_pickup_customer_doc_two}}</div>
                    </div>
                </div>
                @endif

                @if($trackingExist==0 && !empty($pickExist) && ($vehicleExist==1))
                <div class="col-md-12 col-sm-12 padding-none border-top ">

                    <div class="heading margin-bottom"><span><span class="medium-font">Step 3</span> Tracking Details</span></div>

                    {!! Form::open(['url' => 'sellerpickup','id'=>'posts-form-tracklocation']) !!}
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none">

                        {!! Form::text('location', '', ['id' => 'location', 'placeholder' => 'Location']) !!}
                    </div>

                    <div class="col-md-4 col-sm-4 col-xs-12 padding-none">
                        <div class="col-md-8 padding-none">
                            <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                            {!! Form::text('date', '', ['id' => 'date','class'=>"calendar", 'placeholder' => 'Date']) !!}
                        </div>
                    </div>
                    <input type="submit" class="btn" value="Add">

                    {!! Form::close() !!}
                    <div class="clearfix"></div>
                    <div class="table table-head" width="100%">
                        <div class="col-md-12 padding-none">
                            <div class="col-md-4 padding-none">Location</div>
                            <div class="col-md-4 padding-none">Date</div>

                        </div>
                    </div>

                    <div  id="track_locations" >
                        @foreach($locations as $loc)
                        <div class="table table-row"  width="100%">
                        <div class="col-md-12 padding-none">
                            <div class="col-md-4 padding-none">{{$loc->tracking_location}}</div>
                            <div class="col-md-4 padding-none">{{date("d/m/Y", strtotime($loc->tracking_date))}}</div>

                        </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="clearfix"></div>

                    {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-track']) !!}
                    <input type="hidden" name="tracking_confirm" value="1">
                    <input type="submit" class="btn pull-right margin-bottom" value="Confirm">
                    {!! Form::close() !!}
                </div>
                @endif

                @if($trackingExist==1&& !empty($pickExist)&& ($vehicleExist==1))
                <div class="col-md-12 col-sm-12 padding-none border-top consign_track">

                    <div class="heading margin-bottom"><span><span class="medium-font">Step 3</span> Tracking Details</span></div>
                    <div class="track">
                        <div class="table table-head" width="100%">
                            <div class="col-md-12 padding-none">
                                <div class="col-md-4 padding-none">Location</div>
                                <div class="col-md-4 padding-none">Date</div>

                            </div>
                        </div>
                        <div  id="track_locations" >
                            @foreach($locations as $loc)
                            <div class="table table-row"  width="100%">
                            <div class="col-md-12 padding-none">
                                <div class="col-md-4 padding-none">{{$loc->tracking_location}}</div>
                                <div class="col-md-4 padding-none">{{date("d/m/Y", strtotime($loc->tracking_date))}}</div>
                            </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                @endif
                @if(empty($deliveryExist)&& ($trackingExist==1)&& !empty($pickExist) && ($vehicleExist==1))
                <div class="col-md-12 col-sm-12 padding-none border-top">
                    <div class="heading margin-bottom"><span class="medium-font">Step 4</span> <span>Consignment Delivery Details</span></div>

                    {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-delivery']) !!}
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-none">
                        <div class="col-md-8 padding-none">
                            {!! Form::text('delivery_date', '', ['id' => 'delivery_date','class'=>"calendar", 'placeholder' => 'Delivery Date']) !!}

                        </div>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-right-none">
                        {!! Form::text('delivery_driver', '', ['id' => 'delivery_driver', 'placeholder' => 'Driver Name', 'class' => 'form-control clsDrivername']) !!}

                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-right-none">
                        {!! Form::text('delivery_mobile', '', ['id' => 'delivery_mobile', 'placeholder' => 'Recipient Mobile', 'class' => 'clsMobileno']) !!}

                    </div>

                    <div class="clearfix"></div>

                    <p class="margin-bottom">Additional Fields Optional</p>
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-none">
                        {!! Form::text('freight_amt', '', ['id' => 'freight_amt', 'placeholder' => 'Freight Amount']) !!}

                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-right-none">
                        {!! Form::text('delivery_info', '', ['id' => 'delivery_info', 'placeholder' => 'Additional Informantion']) !!}

                    </div>
                    <input type="submit" class="btn pull-right margin-bottom" value="Confirm">
                    {!! Form::close() !!}

                </div>
                @endif
                @if(!empty($deliveryExist)&& ($trackingExist==1)&& !empty($pickExist) && ($vehicleExist==1))
                <div class="col-md-12 col-sm-12 padding-none border-top consign_delivery">
                    <div class="heading margin-bottom"><span class="medium-font">Step 4</span> <span>Consignment Delivery Details</span></div>
                    <div class="delivery">
                        <div class="col-md-4 col-sm-4 col-xs-12 padding-none">
                            <div class="col-md-8 padding-none">Delivery Date: {{date("d/m/Y", strtotime($order->seller_delivery_date))}}</div>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12 padding-right-none">Driver Name: {{$order->seller_delivery_driver_name}}</div>
                        <div class="col-md-4 col-sm-4 col-xs-12 padding-right-none">Recipient Mobile: {{$order->seller_delivery_recipient_mobile}}</div>
                        <div class="clearfix"></div>

                        <p class="margin-bottom">Additional Fields Optional</p>
                        <div class="col-md-4 col-sm-4 col-xs-12 padding-none">Freight Amount: {{$order->seller_delivery_frieght_amt}}</div>
                        <div class="col-md-4 col-sm-4 col-xs-12 padding-right-none">Additional Informantion: {{$order->seller_delivery_additional_details}}</div>
                    </div>
                </div>
                @endif
                @if(!empty($invoiceExist)&& !empty($deliveryExist)&& ($trackingExist==1)&& !empty($pickExist) && ($vehicleExist==1))
                <div class="col-md-12 col-sm-12 padding-none border-top consign_invoice">
                    <div class="heading margin-bottom"><span class="medium-font">Step 5</span> <span>Invoice Generation</span></div>
                    
                    <div class="heading margin-bottom">Invoice No. {{$invoice->invoice_no}}<span> dated {{date("d/m/Y", strtotime($invoice->created_at))}} at {{date("H.i", strtotime($invoice->created_at))}} hours</span></div>
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none">

                        <p>Services Charges<span class="pull-right">:</span></p>
                        @if(SHOW_SERVICE_TAX)
                            <p>Service Tax<span class="pull-right">:</span></p>
                        @endif
                        <p>Total Amount<span class="pull-right">:</span></p>
                       
                    </div>
                    <div class="colmd-4 col-sm-4 col-xs-12 padding-none">
                        <p>{{number_format($invoice->service_charge_amount,2)}}</p>
                        @if(SHOW_SERVICE_TAX)
                            <p>{{number_format($invoice->service_tax_amount,2)}}</p>
                        @endif
                        <p>{{number_format($invoice->total_amount,2)}}
                        
                        <br>
                        @if(!SHOW_SERVICE_TAX)
                        <span class="small serviceTax">(* Service Tax not included )</span>
                        @endif
                        
                        </p>                          
                            
                    </div>
                    @if(empty($receiptExist))
                    <div class="colmd-4 col-sm-4 col-xs-12 padding-none">
                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-receipt']) !!}
                        <input type="button" name="receipt" id="receipt" class="btn pull-right" data-toggle="modal" data-target="#payment" value="Make Payment">
                        <input type="hidden" name="receipts" value="receipts">
                        {!! Form::close() !!}
                    </div>
                    @endif
                    <div class="clearfix"></div>
                    @if(!empty($receiptExist))
                    <div class="heading margin-bottom">Receipt</div>
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none">

                        <p>Receipt No<span class="pull-right">:</span></p>
                        <p>Payment Mode<span class="pull-right">:</span></p>
                        <p>Freight Amount<span class="pull-right">:</span></p>
                        <p>Insurance<span class="pull-right">:</span></p>
                        <!--<p>Services Charges<span class="pull-right">:</span></p>
                        <p>Service Tax<span class="pull-right">:</span></p>-->
                        <p>Total Amount<span class="pull-right">:</span></p>

                    </div>
                    <div class="colmd-8 col-sm-8 col-xs-12 padding-none">

                        <p>{{$receipt->receipt_no}}</p>
                        <p>{{$payment_mode}} xxxx xxxx xxxx 7377 on {{date("d/m/Y", strtotime($receipt->created_at))}}</p>
                        <p>{{number_format($receipt->frieght_amount,2)}}/-</p>
                        <p>{{number_format($receipt->insurance,2)}}/-</p>
                        
                        <p>{{number_format($receipt->total_amount,2)}}/-</p>
                    </div>
                    @endif
                    <div class="clearfix"></div>


                    <div class="colmd-12 col-sm-12 col-xs-12 text-right padding-none form-inline">

                        <!--<input type="button" class="btn pull-right margin-bottom main-right-10" value="Reverse">-->
                        <input type="button" class="btn pull-right margin-bottom main-right-10" value="Confirm">
                    </div>
                    <div class="clearfix"></div>
                    <!--
                    <div class="heading margin-bottom">Receipt Cancellation no. if applicable</div>
                    <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none">

                        <p>Cancellation date<span class="pull-right">:</span></p>
                        <p>Total Amount<span class="pull-right">:</span></p>
                        <p>Cancellation Amount<span class="pull-right">:</span></p>
                        <p>Balance Refund<span class="pull-right">:</span></p>
                        <p>Cancellation Payment Mode<span class="pull-right">:</span></p>
                        <p>Bank Transaction ID<span class="pull-right">:</span></p>

                    </div>
                    <div class="colmd-8 col-sm-8 col-xs-12 padding-none">

                        <p>dd/mm/yyyy</p>
                        <p>xx,xxx/-</p>
                        <p>xx,xxx/-</p>
                        <p>xxxxxxx</p>
                        <p>Process through ___ ____ ___ on dd/mm/yyyy</p>
                        <p>xxxxxxx</p>
                    </div>
                    <div class="clearfix"></div>
                    <div class="colmd-12 col-sm-12 col-xs-12 text-right padding-none form-inline">

                        <input type="button" class="btn pull-right margin-bottom main-right-10" value="Confirm">
                    </div>
                    -->
                    	
                </div>
                @endif




            </div>
        </div>

        <!-- Right Starts Here -->
        @include('partials.seller_rightnav')
        <!-- Right Ends Here -->
    </div>
</div>
</div>
@endsection

