{{--*/ $exclude_transit=array(RELOCATION_PET_MOVE,RELOCATION_INTERNATIONAL,RELOCATION_GLOBAL_MOBILITY,RELOCATION_OFFICE_MOVE) /*--}}
{{--*/ $serviceId = Session::get('service_id') /*--}}
@if(in_array($serviceId,$exclude_transit))
    {{--*/ $trackingExist = 1 /*--}}
@endif

@if($serviceId==RELOCATION_GLOBAL_MOBILITY)
    @if((empty($deliveryExist) ||  $deliveryExist=="0000-00-00 00:00:00") && $order->gsa_accepted==1 && (!empty($pickExist) && $pickExist!="0000-00-00 00:00:00"))
       <div class="accordian-blocks">
               <div class="inner-block-bg inner-block-bg1 detail-head">
                   <h2 class="filter-head1">Completion</h2>
               </div>
               <div class="detail-data">
                   <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                       {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-delivery']) !!}
                       <div class="col-md-12 padding-none">

                           <div class="col-md-3 form-control-fld">
                               <div class="input-prepend">
                                   <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                   {!! Form::text('delivery_date', '', ['id' => 'cdelivery_date','class'=>"form-control calendar", 'placeholder' => 'End Date*','max-date'=>$order->dispatch_date,'readonly' => true]) !!}
                                   <input type='hidden' id='cpick' value="{{date("d/m/Y", strtotime($order->seller_pickup_date))}}">
                               </div>
                           </div>
                           <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                {!! Form::text('delivery_info', '', ['id' => 'delivery_info', 'maxlength'=>'500','class'=>"form-control", 'placeholder' => 'Additional Information (Optional)']) !!}
                            </div>
                        </div>
                        <div class="col-md-6 form-control-fld text-right">
                            <input type="submit" class="btn add-btn" value="Confirm">
                        </div>   
                       </div>
                       {!! Form::close() !!}
                       </div>
               </div>
           </div>
    @endif
    @if((!empty($deliveryExist) && $deliveryExist!="0000-00-00 00:00:00") && $order->gsa_accepted==1 && (!empty($pickExist) && $pickExist!="0000-00-00 00:00:00") )
        <div class="accordian-blocks">
            <div class="inner-block-bg inner-block-bg1 detail-head">
                <h2 class="filter-head1">Completion</h2>
            </div>
            <div class="detail-data">
                <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                    <div class="col-md-12 padding-none">
                        <div class="col-md-12">
                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">End Date</span>
                                <span class="data-value">{{date("d/m/Y", strtotime($order->seller_delivery_date))}}</span>
                            </div>
                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Additional Informantion</span>
                                <span class="data-value">{{$order->seller_delivery_additional_details}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@else
    @if(empty($deliveryExist) && $order->gsa_accepted==1 && ($trackingExist==1 ||  (isset($tracking) && $tracking==2))&& !empty($pickExist) )
        <div class="accordian-blocks">
            <div class="inner-block-bg inner-block-bg1 detail-head">
                <h2 class="filter-head1">Consignment Delivery Details</h2>
            </div>
            <div class="detail-data">
                <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                    {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-delivery']) !!}
                    <div class="col-md-12 padding-none">

                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                {!! Form::text('delivery_date', '', ['id' => 'cdelivery_date','class'=>"form-control calendar", 'placeholder' => 'Delivery Date*','max-date'=>$order->dispatch_date,'readonly' => true]) !!}
                                <input type='hidden' id='cpick' value="{{date("d/m/Y", strtotime($order->seller_pickup_date))}}">
                            </div>
                        </div>

                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-wheelchair"></i></span>
                                {!! Form::text('delivery_driver', '', ['id' => 'delivery_driver','class'=>"form-control clsDrivername", 'placeholder' => 'Recipient Name*']) !!}
                            </div>
                        </div>

                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-phone"></i></span>
                                {!! Form::text('delivery_mobile', '', ['id' => 'delivery_mobile','class'=>"form-control clsMobileno", 'placeholder' => 'Recipient Mobile Number*']) !!}
                            </div>
                        </div>

                        <div class="clearfix"></div>

                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                @if(Session::get('service_id') == RELOCATION_INTERNATIONAL)
                                    {!! Form::text('freight_amt', '', ['id' => 'freight_amt','class'=>"form-control clsRIASFreightAmount", 'placeholder' => 'Freight Amount (Optional)']) !!}
                                @else
                                    {!! Form::text('freight_amt', '', ['id' => 'freight_amt','class'=>"form-control fivedigitstwodecimals_deciVal", 'placeholder' => 'Freight Amount (Optional)']) !!}
                                @endif
                            </div>
                        </div>

                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                {!! Form::text('delivery_info', '', ['id' => 'delivery_info', 'maxlength'=>'500','class'=>"form-control", 'placeholder' => 'Additional Information (Optional)']) !!}
                            </div>
                        </div>

                        <div class="col-md-6 form-control-fld text-right">
                            <!--button class="btn add-btn">Confirm</button-->
                            <input type="submit" class="btn add-btn" value="Confirm">
                        </div>

                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    @endif

    @if(!empty($deliveryExist) && $order->gsa_accepted==1 && ($trackingExist==1 ||  (isset($tracking) && $tracking==2))&& !empty($pickExist) )
        <div class="accordian-blocks">
            <div class="inner-block-bg inner-block-bg1 detail-head">
                <h2 class="filter-head1">Consignment Delivery Details</h2>
            </div>
            <div class="detail-data">
                <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                    <div class="col-md-12 padding-none">
                        <div class="col-md-12">
                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Delivery Date</span>
                                <span class="data-value">{{date("d/m/Y", strtotime($order->seller_delivery_date))}}</span>
                            </div>

                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Recipient Name</span>
                                <span class="data-value">{{$order->seller_delivery_driver_name}}</span>
                            </div>

                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Recipient Mobile</span>
                                <span class="data-value">{{$order->seller_delivery_recipient_mobile}}</span>
                            </div>

                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Freight Amount</span>
                                <span class="data-value">{{$order->seller_delivery_frieght_amt}}</span>
                            </div>

                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Additional Informantion</span>
                                <span class="data-value">{{$order->seller_delivery_additional_details}}</span>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    @endif


@endif