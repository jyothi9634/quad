@extends('app')
@section('content')
@inject('validationcomponent', 'App\Components\ValidationComponent')
@inject('common', 'App\Components\CommonComponent')
        <!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

    <div class="main">

        <div class="container">
                <span class="pull-left"><h1 class="page-title">Post (Relocation Global Mobility)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
            @if ($url_search_search == 'buyersearchresults')
                <span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">Back to Search</a></span>
            @endif

            <div class="clearfix"></div>

            <div class="col-md-12 padding-none">
                <div class="main-inner">
                    <!-- Right Section Starts Here -->
                    @if(isset($private) || isset($public))
                        <div class="main-right">
                        
                        {!! Form::open(['url' => 'relocation/updatesellerpost/'.$seller_post_edit->id,'id'=>'posts-form_relocationgm']) !!}
                        {!! Form::hidden('sellerpoststatus_previous', $seller_post_edit->lkp_post_status_id, array('id' => 'sellerpoststatus_previous')) !!}
                        {!! Form::hidden('sellerpoststatus', $seller_post_edit->lkp_post_status_id, array('id' => 'sellerpoststatus')) !!}
                        
                        
                        {!! Form::hidden('subscription_start_date_start', $subscription_start_date_start, array('id' => 'subscription_start_date_start')) !!}
                        {!! Form::hidden('subscription_end_date_end', $subscription_end_date_end, array('id' => 'subscription_end_date_end')) !!}
                        {!! Form::hidden('current_date_seller', $current_date_seller, array('id' => 'current_date_seller')) !!}
                        <div class="gray-bg">
                            <div class="col-md-12 padding-none filter">

                                
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                        {!! Form::text('to_location', $common->getCityName($seller_post_edit->location_id), ['id' => 'to_location','class' => 'form-control clsGMSToLocation', 'placeholder' => 'To Location*','disabled' => true]) !!}
                                        {!! Form::hidden('to_location_id', $seller_post_edit->location_id, array('id' => 'to_location_id')) !!}
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                        {!! Form::text('valid_from', $common->checkAndGetDate($seller_post_edit->from_date), ['id' => 'datepicker','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Valid From*','disabled' => true]) !!}
                                        {!! Form::hidden('valid_from_hidden', $seller_post_edit->from_date, array('id' => 'valid_from_hidden')) !!}
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                        {!! Form::text('valid_to', $common->checkAndGetDate($seller_post_edit->to_date), ['id' => 'datepicker_to_location','class' => 'calendar form-control  to-date-control','readonly' => true, 'placeholder' => 'Valid To*']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="gray-bg" >
                            {{--*/ $i=4 /*--}}{{--*/ $j=0 /*--}}
                            @foreach($gmServiceTypes as $gmServiceType)
                                {{--*/ $str_name    =   strtolower(str_replace(' ','_',$gmServiceType->service_type)); /*--}}
                                {{--*/ $cls  =$validationcomponent->getGmSellerClass($gmServiceType->id) /*--}}
                            
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    {!! Form::text("$str_name",($seller_post_edit->$str_name!=0)?$seller_post_edit->$str_name:'',['class'=>"form-control form-control1 gm_service_rates $cls",'id'=>'volume','placeholder'=>"$gmServiceType->service_type"]) !!}
                                    <span class="add-on unit1 manage">{{$gmServiceType->measurement_units}}</span>
                                </div>
                            </div>
                                {{--*/ $j++ /*--}}
                                @if($j==$i)
                                    <div class="clearfix"></div>
                                @endif
                            @endforeach

                        </div>


                        <div class="col-md-12 inner-block-bg inner-block-bg1">
                            <div class="col-md-12 padding-none filter">
                                <h2 class="filter-head1 margin-bottom">Additional Charges</h2>
                                
                                
                                    <div class="terms-and-conditions-block">
                                        {!! Form::hidden('next_terms_count_search', '0', array('id' => 'next_terms_count_search')) !!}
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                {!! Form::text('cancellation_charge_price',$seller_post_edit->cancellation_charge_price,['class'=>'form-control form-control1 clsGMSCancelCharges','id'=>'cancellation_charge_price','placeholder'=>'Cancellation Charges' ]) !!}
                                                <span class="add-on unit1 manage">Rs</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 tc-block-btn"></div>
                                    </div>
                                    <div class="my-form">
                                        <div class=" text-box form-control-fld terms-and-conditions-block">
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    {!! Form::text('docket_charge_price',$seller_post_edit->docket_charge_price,['class'=>'form-control form-control1 clsGMSOtherCharges','id'=>'docket_charge_price','placeholder'=>'Other Charges'] ) !!}
                                                    <span class="add-on unit1 manage">Rs</span>
                                                </div>
                                            </div>
                                            <div class="col-md-3 tc-block-btn"><input type="button" class="add-box btn add-btn" value="Add"></div>
                                        </div>

                                        @for ($i = 1; $i <= 3; $i++)
                                            {{--*/ $text =  "other_charge{$i}_text" /*--}}
                                            {{--*/ $price = "other_charge{$i}_price" /*--}}
                                            @if(($seller_post_edit->$text != "" || $seller_post_edit->$price != "") && ($seller_post_edit->$text != "" || $seller_post_edit->$price != "0.00"))
                                                <div class="text-box form-control-fld terms-and-conditions-block" style="">
                                                    <div class="col-md-3 padding-none">
                                                        <div class="input-prepend">
                                                            {!! Form::text("labeltext_$i",$seller_post_edit->$text,['placeholder' => 'Other Charges','class'=>'form-control form-control1 labelcharges dynamic_labelcharges',($seller_post_edit->lkp_post_status_id == 2) ? '' : '']) !!}
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="input-prepend">
                                                            {!! Form::text("terms_condtion_types_$i",$seller_post_edit->$price,['placeholder' => '0.00','class'=>'form-control form-control1 pricebox update_txt_test update_txt dynamic_validations  clsGMSOtherCharges',($seller_post_edit->lkp_post_status_id == 2) ? '' : '']) !!}
                                                            <span class="add-on unit">Rs</span>
                                                        </div>
                                                    </div>
                                                    
                                                </div>
                                            @endif
                                        @endfor
                                        <input type="hidden" name ='next_terms_count_search' id='next_terms_count_search' value='{{$i-1}}'>

                                    </div>

                                 
                                <div class="col-md-6 form-control-fld">
                                    {!! Form::textarea('terms_conditions',$seller_post_edit->terms_conditions,['class'=>'form-control form-control1 clsTermsConditions','id'=>'terms_conditions', 'rows' => 5,'placeholder' => 'Notes to Terms & Conditions (Optional)',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
                                </div>

                            </div>
                        </div>

                        <div class="col-md-12 inner-block-bg inner-block-bg1">

                            <h2 class="filter-head1">Payment Terms</h2>
                            <div class="col-md-3 form-control-fld">
                                <div class="normal-select">
                                    {!! Form::select('paymentterms', ($paymentterms), $seller_post_edit->lkp_payment_mode_id, ['class' => 'selectpicker','id' => 'payment_options',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
                                </div>
                            </div>

                            <div class="col-md-12 form-control-fld" id = 'show_advanced_period' style='display: <?php echo ($seller_post_edit->lkp_payment_mode_id == 1) ? "block" : "none"; ?>'>
                                <div class="check-block">
                                    <div class="checkbox_inline">
                                        {{--*/ $checked = ($seller_post_edit->accept_payment_netbanking == 1) ? true: false; /*--}}
                                        {!! Form::checkbox('accept_payment_ptl[]', 1, $checked, ['class' => 'accept_payment_ptl',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!} <span class="lbl padding-8">NEFT/RTGS</span>
                                    </div>
                                    <div class="checkbox_inline">
                                        {{--*/ $checked = ($seller_post_edit->accept_payment_credit == 1) ? true: false; /*--}}
                                        {!! Form::checkbox('accept_payment_ptl[]', 2, $checked, ['class' => 'accept_payment_ptl',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!} <span class="lbl padding-8">Credit Card</span>
                                    </div>
                                    <div class="checkbox_inline">
                                        {{--*/ $checked = ($seller_post_edit->accept_payment_debit == 1) ? true: false; /*--}}
                                        {!! Form::checkbox('accept_payment_ptl[]', 3, $checked, ['class' => 'accept_payment_ptl',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!} <span class="lbl padding-8">Debit Card</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 form-control-fld" style='display: <?php echo ($seller_post_edit->lkp_payment_mode_id == 4) ? "block" : "none"; ?>' id = 'show_credit_period'>
                                <div class="col-md-3 form-control-fld padding-left-none">

                                    <div class="col-md-7 padding-none">
                                        <div class="input-prepend">
                                            {!! Form::text('credit_period_ptl',$seller_post_edit->credit_period,['class'=>'form-control form-control1 clsGMSCreditPeriod','placeholder'=>'Credit Period',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-5 padding-none">
                                        <div class="input-prepend">
								<span class="add-on unit-days manage">
											<div class="normal-select">
                                                {!! Form::select('credit_period_units',['Days' => 'Days','Weeks' => 'Weeks'], $seller_post_edit->credit_period_units, ['class' => 'selectpicker bs-select-hidden',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
                                            </div>
										</span>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-12 padding-none">
                                    <div class="checkbox_inline">
                                        {{--*/ $checked = ($seller_post_edit->accept_credit_netbanking == 1) ? true: false; /*--}}
                                        {!! Form::checkbox('accept_credit_netbanking[]', 1,$checked, false,[($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!} <span class="lbl padding-8">Net Banking</span>
                                    </div>
                                    <div class="checkbox_inline">
                                        {{--*/ $checked = ($seller_post_edit->accept_credit_cheque == 1) ? true: false; /*--}}
                                        {!! Form::checkbox('accept_credit_netbanking[]', 2,$checked, false,[($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!} <span class="lbl padding-8">Cheque / DD</span>
                                    </div>

                                </div>
                            </div>

                            <div class="clearfix"></div>

                        </div>

                        <div class="col-md-12 inner-block-bg inner-block-bg1">
                            <div class="col-md-12 form-control-fld margin-none">
                                <div class="radio-block">
                                    <div class="radio_inline">
                                        {{--*/ $checked = ($seller_post_edit->lkp_access_id == 1) ? true: false; /*--}}
                                        {!! Form::radio('optradio', 1, $checked, ['id' => 'post-public','class' => 'create-posttype-service',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
                                        <label for="post-public"><span></span>Post Public</label>
                                    </div>
                                    <div class="radio_inline">
                                        {{--*/ $checked = ($seller_post_edit->lkp_access_id == 2) ? true: false; /*--}}
                                        {!! Form::radio('optradio', 2, $checked, ['id' => 'post-private','class' => 'create-posttype-service',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
                                         <label for="post-private"><span></span>Post Private</label>
                                    </div>
                                    @if($seller_post_edit->lkp_post_status_id == 2)
                                        <input type="hidden" value="{!! $seller_post_edit->lkp_access_id !!}" name="optradio">
                                    @endif
                                </div>

                                <div class="col-md-3 form-control-fld">
                                    <div class="demo-input_buyers" style='display:<?php echo ($private == true) ? "block" : "none"; ?>'>
                                        <?php
                                        $selected  = "";
                                        foreach($selectedbuyers as $selectedbuyer){
                                            $selected .= ",".$selectedbuyer->buyer_id;
                                        } ?>
                                        <input type="hidden" id="demo_input_select_hidden" name="buyer_list_for_sellers_hidden" value="<?php echo $selected; ?>" />
                                        <select id="demo_input_select" class="tokenize-sample" name="buyer_list_for_sellers" multiple="multiple">
                                            <?php foreach($selectedbuyers as $selectedbuyer){ ?>
                                            @if($selectedbuyer->principal_place != '')
                                                <option value="<?php echo $selectedbuyer->buyer_id ?>" selected="selected"><?php echo $selectedbuyer->username.' '.$selectedbuyer->principal_place.' '.$selectedbuyer->buyer_id; ?></option>
                                            @else
                                                <option value="<?php echo $selectedbuyer->buyer_id ?>" selected="selected"><?php echo $selectedbuyer->username.' '.$selectedbuyer->buyer_id; ?></option>
                                            @endif
                                            <?php } ?>
                                        </select>

                                    </div>
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            <div class="check-box form-control-fld margin-none">
                                {!! Form::checkbox('agree', 1, false,array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
                            </div>
                        </div>

                        <div class="col-md-12 padding-none">
                            <div class="col-md-12 padding-none">
                                {!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => 'add_quote_seller_id_relocation_update','onclick'=>"updatepoststatus(1)"]) !!}
                                @if($seller_post_edit->lkp_post_status_id == 1)
                                    {!! Form::submit('Save as draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_relocationgm','onclick'=>"updatepoststatus(0)"]) !!}
                                @endif
                            </div>
                        </div>
                        {!! Form::close() !!}

                    </div>
                    @else
                    <div class="main-right">

                        {!! Form::open(['url' => 'relocationsellerpostcreation','id'=>'posts-form_relocationgm', 'autocomplete'=>'off']) !!}
                        {!! Form::hidden('sellerpoststatus', '1', array('id' => 'sellerpoststatus')) !!}
                        
                        {!! Form::hidden('subscription_start_date_start', $subscription_start_date_start, array('id' => 'subscription_start_date_start')) !!}
                        {!! Form::hidden('subscription_end_date_end', $subscription_end_date_end, array('id' => 'subscription_end_date_end')) !!}
                        {!! Form::hidden('current_date_seller', $current_date_seller, array('id' => 'current_date_seller')) !!}
                        {!! Form::hidden('valid_from_val', '', array('id' => 'valid_from_val')) !!}
                    	{!! Form::hidden('valid_to_val', '', array('id' => 'valid_to_val')) !!}
			
                        <div class="gray-bg">
                            <div class="col-md-12 padding-none filter">

                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                        {!! Form::text('to_location', $session_search_values_create[0], ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'Location*']) !!}
                                        {!! Form::hidden('to_location_id', $session_search_values_create[1], array('id' => 'to_location_id')) !!}
                                        {!! Form::hidden('seller_district_id', $session_search_values_create[5], array('id' => 'seller_district_id')) !!}
                                    </div>
                                </div>
                                
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                        {!! Form::text('valid_from', $session_search_values_create[2], ['id' => 'datepicker','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Valid From*']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                        {!! Form::text('valid_to', $session_search_values_create[3], ['id' => 'datepicker_to_location','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Valid To*']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="gray-bg" >
                            {{--*/ $i=4 /*--}}{{--*/ $j=0 /*--}}
                            @foreach($gmServiceTypes as $gmServiceType)
                                {{--*/ $str_name    =   strtolower(str_replace(' ','_',$gmServiceType->service_type)); /*--}}
                                {{--*/ $cls  =$validationcomponent->getGmSellerClass($gmServiceType->id) /*--}}
                            
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    {!! Form::text("$str_name",null,['class'=>"form-control form-control1 gm_service_rates $cls",'id'=>'volume','placeholder'=>"$gmServiceType->service_type"]) !!}
                                    <span class="add-on unit1 manage">{{$gmServiceType->measurement_units}}</span>
                                </div>
                            </div>
                                {{--*/ $j++ /*--}}
                                @if($j==$i)
                                    <div class="clearfix"></div>
                                @endif
                            @endforeach


                        </div>

                        <div class="col-md-12 inner-block-bg inner-block-bg1">
                            <div class="col-md-12 padding-none filter">
                                <h2 class="filter-head1 margin-bottom">Additional Charges</h2>
                                
                                <div class="terms-and-conditions-block">
                                    {!! Form::hidden('next_terms_count_search', '0', array('id' => 'next_terms_count_search')) !!}
                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                            {!! Form::text('cancellation_charge_price','',['class'=>'form-control form-control1 clsGMSCancelCharges','id'=>'cancellation_charge_price','placeholder'=>'Cancellation Charges']) !!}
                                            <span class="add-on unit1 manage">Rs</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 tc-block-btn"></div>
                                </div>
                                <div class="my-form">
                                    <div class=" text-box form-control-fld terms-and-conditions-block padding-none">
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                {!! Form::text('docket_charge_price','',['class'=>'form-control form-control1 clsGMSOtherCharges','id'=>'docket_charge_price','placeholder'=>'Other Charges']) !!}
                                                <span class="add-on unit1 manage">Rs</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 tc-block-btn"><input type="button" class="add-box btn add-btn" value="Add"></div>
                                    </div>
                                </div>
                                <div class="col-md-6 form-control-fld">
                                    {!! Form::textarea('terms_conditions',null,['class'=>'form-control form-control1 clsTermsConditions','id'=>'terms_conditions', 'rows' => 5,'placeholder' => 'Notes to Terms & Conditions (Optional)']) !!}
                                </div>

                            </div>
                        </div>

                        <div class="col-md-12 inner-block-bg inner-block-bg1">

                            <h2 class="filter-head1">Payment Terms</h2>

                            <div class="col-md-3 form-control-fld">
                                <div class="normal-select">
                                    {!! Form::select('paymentterms', ($paymentterms), null, ['class' => 'selectpicker','id' => 'payment_options']) !!}
                                </div>
                            </div>
                            <div class="col-md-12 form-control-fld" id = 'show_advanced_period'>
                                <div class="check-block">
                                    <div class="checkbox_inline">
                                        {!! Form::checkbox('accept_payment_ptl[]', 1, '', false, ['class' => 'accept_payment_ptl']) !!} <span class="lbl padding-8">NEFT/RTGS</span>
                                    </div>
                                    <div class="checkbox_inline">
                                        {!! Form::checkbox('accept_payment_ptl[]', 2, '', false, ['class' => 'accept_payment_ptl']) !!} <span class="lbl padding-8">Credit Card</span>
                                    </div>
                                    <div class="checkbox_inline">
                                        {!! Form::checkbox('accept_payment_ptl[]', 3, '', false, ['class' => 'accept_payment_ptl']) !!} <span class="lbl padding-8">Debit Card</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 form-control-fld" style ='display: none;' id = 'show_credit_period'>
                                <div class="col-md-3 form-control-fld padding-left-none">

                                    <div class="col-md-7 padding-none">
                                        <div class="input-prepend">
                                            {!! Form::text('credit_period_ptl',null,['class'=>'form-control form-control1 clsIDCredit_period clsCreditPeriod','placeholder'=>'Credit Period']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-5 padding-none">
                                        <div class="input-prepend">
                                            <span class="add-on unit-days manage">
                                            <div class="normal-select">
                                                {!! Form::select('credit_period_units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelPaymentCreditType bs-select-hidden']) !!}
                                            </div>
                                            </span>
                                        </div>
                                    </div>

                                </div>
                                <div class="col-md-12 padding-none">
                                    <div class="checkbox_inline">
                                        {!! Form::checkbox('accept_credit_netbanking[]', 1, false) !!} <span class="lbl padding-8">Net Banking</span>
                                    </div>
                                    <div class="checkbox_inline">
                                        {!! Form::checkbox('accept_credit_netbanking[]', 2, false) !!} <span class="lbl padding-8">Cheque / DD</span>
                                    </div>

                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>

                        <div class="col-md-12 inner-block-bg inner-block-bg1">
                            <div class="col-md-12 form-control-fld margin-none">
                                <div class="radio-block">
                                    <div class="radio_inline"><input type="radio" name="optradio" id="post-public" value="1" checked="checked" class="create-posttype-service" /> <label for="post-public"><span></span>Post Public</label></div>
                                    <div class="radio_inline"><input type="radio" name="optradio" id="post-private" value="2" class="create-posttype-service" /> <label for="post-private"><span></span>Post Private</label></div>
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld demo-input_buyers" style="display:none">
                                <div class="input-prepend">
                                    <input type="hidden" id="demo-input" name="buyer_list_for_sellers" class="form-control" />
                                </div>
                            </div>


                            <div class="clearfix"></div>
                            <div class="check-box form-control-fld margin-none">
                                {!! Form::checkbox('agree', '', '',array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
                            </div>
                        </div>

                        <div class="col-md-12 padding-none">
                            <div class="col-md-12 padding-none">
                                {!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => 'add_quote_seller_id_relocationgm','onclick'=>"updatepoststatus(1)"]) !!}
                                {!! Form::submit('Save as draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_relocationgm','onclick'=>"updatepoststatus(0)"]) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}

                    </div>
                    @endif
                    
                    <!-- Right Section Ends Here -->

                </div>
            </div>

            <div class="clearfix"></div>

        </div>
    </div>

@include('partials.footer')
@endsection



