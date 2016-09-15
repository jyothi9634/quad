{{--*/ $serviceId = Session::get('service_id') /*--}}
@if($serviceId!=RELOCATION_PET_MOVE && $serviceId!=RELOCATION_INTERNATIONAL)
{{--*/ $str_service='LR' /*--}}
@else
{{--*/ $str_service='AWB' /*--}}
@endif
@if($serviceId==RELOCATION_GLOBAL_MOBILITY)
    @if((empty($pickExist) ||  $pickExist=="0000-00-00 00:00:00") && $order->gsa_accepted==1)
        <div class="accordian-blocks">
            <div class="inner-block-bg inner-block-bg1 detail-head">
                <h2 class="filter-head1">Start</h2>
            </div>
            <div class="detail-data">
                <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                    <div class="col-md-12 padding-none">
                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-pickup']) !!}
                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                <!-- input type="text" placeholder="Pickup Date" id="" class="form-control"-->
                                {!! Form::text('pick_date', '', ['id' => 'pick_date','class'=>"form-control calendar", 'placeholder' => 'Start Date*','min-date'=>date('Y-m-d', strtotime('-1 day', strtotime($order->dispatch_date))),'max-date'=>$order->delivery_date,'readonly' => true]) !!}
                                <input type='hidden' id='cpick' value="{{date('d/m/Y', strtotime('-1 day', strtotime($order->dispatch_date)))}}">
                                <input type='hidden' id='cdelivery' value="{{date('d/m/Y', strtotime($order->delivery_date))}}">
                            </div>
                        </div>
                        <div class="col-md-6 form-control-fld text-right">
                            <input type="submit" class="btn add-btn" value="Confirm">
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if((!empty($pickExist) && $pickExist!="0000-00-00 00:00:00") && $order->gsa_accepted==1)
        <div class="accordian-blocks">
            <div class="inner-block-bg inner-block-bg1 detail-head">
                <h2 class="filter-head1">Start</h2>
            </div>
            <div class="detail-data">
                <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                    <div class="col-md-12 padding-none">
                        <div class="col-md-12">
                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Start Date</span>
                                <span class="data-value">{{date("d/m/Y", strtotime($order->seller_pickup_date))}}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@else
    @if(empty($pickExist) && $order->gsa_accepted==1)
        <div class="accordian-blocks">
            <div class="inner-block-bg inner-block-bg1 detail-head">
                <h2 class="filter-head1">Consignment Pickup</h2>
            </div>
            <div class="detail-data">
                <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                    <div class="col-md-12 padding-none">
                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-pickup']) !!}
                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                <!-- input type="text" placeholder="Pickup Date" id="" class="form-control"-->
                                {!! Form::text('pick_date', '', ['id' => 'pick_date','class'=>"form-control calendar", 'placeholder' => 'Pickup Date*','min-date'=>date('Y-m-d', strtotime('-1 day', strtotime($order->dispatch_date))),'max-date'=>$order->delivery_date,'readonly' => true]) !!}
                                <input type='hidden' id='cpick' value="{{date('d/m/Y', strtotime('-1 day', strtotime($order->dispatch_date)))}}">
                                <input type='hidden' id='cdelivery' value="{{date('d/m/Y', strtotime($order->delivery_date))}}">
                            </div>
                        </div>
                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-gg"></i></span>
                                <!-- input type="text" placeholder="LR Number" id="" class="form-control" -->
                                {!! Form::text('lr_no', '', ['id' => 'lr_no','class'=>"form-control clsLRnumber", 'placeholder' => "$str_service Number*"]) !!}
                            </div>
                        </div>

                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                <!-- input type="text" placeholder="LR Date" id="" class="form-control"-->
                                {!! Form::text('lr_date', '', ['id' => 'lr_date','class'=>"form-control calendar", 'placeholder' => "$str_service Date*",'readonly' => true]) !!}
                            </div>
                        </div>
                        @if($serviceId!=RELOCATION_PET_MOVE && $serviceId!=RELOCATION_INTERNATIONAL)
                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-gg"></i></span>
                                <!-- input type="text" placeholder="Transporter Bill Number" id="" class="form-control" -->
                                {!! Form::text('bill_no', '', ['id' => 'bill_no','class'=>"form-control clsTransporterBill", 'placeholder' => 'Transporter bill no.*']) !!}
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                <!--input type="text" placeholder="Customer Document 1 (Optional)" id="" class="form-control"-->
                                {!! Form::text('info1', '', ['id' => 'info1', 'maxlength'=>'50','class'=>"form-control clsCustDocs", 'placeholder' => 'Customer Document 1 (Optional)']) !!}
                            </div>
                        </div>
                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-file-text-o"></i></span>
                                <!--input type="text" placeholder="Customer Document 2 (Optional)" id="" class="form-control"-->
                                {!! Form::text('info2', '', ['id' => 'info2', 'maxlength'=>'50','class'=>"form-control clsCustDocs", 'placeholder' => 'Customer Document 2 (Optional)']) !!}
                            </div>
                        </div>
                        @endif
                        @if($serviceId!=RELOCATION_PET_MOVE && $serviceId!=RELOCATION_INTERNATIONAL)
                            <div class="col-md-6 form-control-fld text-right">
                        @else
                            <div class="col-md-3 form-control-fld text-right">
                        @endif
                            <!--button class="btn add-btn">Confirm</button-->
                            <input type="submit" class="btn add-btn" value="Confirm">
                        </div>
                        {!! Form::close() !!}

                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(!empty($pickExist) && $order->gsa_accepted==1)
        <div class="accordian-blocks">
            <div class="inner-block-bg inner-block-bg1 detail-head">
                <h2 class="filter-head1">Consignment Pickup</h2>
            </div>
            <div class="detail-data">
                <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                    <div class="col-md-12 padding-none">
                        <div class="col-md-12">
                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Pickup Date</span>
                                <span class="data-value">{{date("d/m/Y", strtotime($order->seller_pickup_date))}}</span>
                            </div>

                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">{{$str_service}} Number:</span>
                                <span class="data-value">{{$order->seller_pickup_lr_number}}</span>
                            </div>

                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">{{$str_service}} Date:</span>
                                <span class="data-value">{{date("d/m/Y", strtotime($order->seller_pickup_lr_date))}}</span>
                            </div>
                            @if($serviceId!=RELOCATION_PET_MOVE && $serviceId!=RELOCATION_INTERNATIONAL)
                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Transporter bill no:</span>
                                <span class="data-value">{{$order->seller_pickup_transport_bill_no}}</span>
                            </div>

                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Customer Document 1 (Optional):</span>
                                <span class="data-value">{{$order->seller_pickup_customer_doc_one}}</span>
                            </div>

                            <div class="col-md-2 padding-left-none data-fld">
                                <span class="data-head">Customer Document 2 (Optional)</span>
                                <span class="data-value">{{$order->seller_pickup_customer_doc_two}}</span>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif    
    

