@extends('app')
@section('content')
@inject('common', 'App\Components\CommonComponent')
        <!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
{{--*/ $serviceId = Session::get('service_id') /*--}}

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('/sellerlist') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif

    <div class="main">

        <div class="container">
            <span class="pull-left"><h1 class="page-title">Post (Relocation Pet)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
            <span class="pull-right"><a href="{{$backToPostsUrl}}" class="back-link">Back to Posts</a></span>
            <div class="clearfix"></div>
            <div class="col-md-12 padding-none">
                <div class="main-inner">
                    <!-- Right Section Starts Here -->
                    <div class="main-right">
                        {{--*/ $householdItems = 0; /*--}}
                        @foreach($seller_post_edit_action_lines as $key=>$seller_post_edit_action_line)
                                {{--*/ $householdItems = $householdItems + 1 /*--}}
                        @endforeach
                        {!! Form::open(['url' => 'relocation/updatesellerpost/'.$seller_post_edit->id,'id'=>'posts-form_relocation']) !!}
                        {!! Form::hidden('sellerpoststatus_previous', $seller_post_edit->lkp_post_status_id, array('id' => 'sellerpoststatus_previous')) !!}
                        {!! Form::hidden('sellerpoststatus', $seller_post_edit->lkp_post_status_id, array('id' => 'sellerpoststatus')) !!}
                        {!! Form::hidden('pet_items', $householdItems, array('id' => 'household_items')) !!}
                        {!! Form::hidden('pet_items_mandatory', '0', array('id' => 'household_items_mandatory')) !!}
                        {!! Form::hidden('subscription_start_date_start', $subscription_start_date_start, array('id' => 'subscription_start_date_start')) !!}
                        {!! Form::hidden('subscription_end_date_end', $subscription_end_date_end, array('id' => 'subscription_end_date_end')) !!}
                        {!! Form::hidden('current_date_seller', $current_date_seller, array('id' => 'current_date_seller')) !!}
                        <div class="gray-bg">
                            <div class="col-md-12 padding-none filter">

                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                        {!! Form::text('from_location', $common->getCityName($seller_post_edit->from_location_id), ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*','disabled' => true]) !!}
                                        {!! Form::hidden('from_location_id', $seller_post_edit->from_location_id, array('id' => 'from_location_id')) !!}
                                        {!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                        {!! Form::text('to_location', $common->getCityName($seller_post_edit->to_location_id), ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*','disabled' => true]) !!}
                                        {!! Form::hidden('to_location_id', $seller_post_edit->to_location_id, array('id' => 'to_location_id')) !!}
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

                        <div class="gray-bg relocation_house_hold_create" >
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    <span class="add-on">
                                    <i class="fa fa-paw"></i>
                                    </span>
                                <div class="normal-select">
                                    {!! Form::select('pettype', (['' => 'Pet Type*'] + $petTypes), '', ['class' => 'selectpicker form-control','id' => 'pettypes']) !!}
                                </div>
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    <span class="add-on">
                                    <i class="fa fa-chain"></i>
                                    </span>
                                    <div class="normal-select">
                                    {!! Form::select('cagetype', (['' => 'Cage Type*'] + $cagetypes), '', ['class' => 'selectpicker form-control','id' => 'cagetypes','onchange'=>'return getCageWeight()']) !!}
                                    </div>
                                    <span class="add-on unit1 manage cage-weight">50 KGs</span>
                                </div>
                            </div>
                        
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    {!! Form::text('od_charges',null,['class'=>'form-control form-control1 clsRPetODChargesFlat','id'=>'od_charges','placeholder'=>'O & D Charges (Flat Charge)*']) !!}
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-2 form-control-fld">
                                <div class="input-prepend">
                                    {!! Form::text('freight',null,['class'=>'form-control form-control1 clsRPetFreightFlat','id'=>'freight','placeholder'=>'Freight (per KG)*']) !!}
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld">
                                <div class="col-md-8 padding-none">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-hourglass-1"></i></span>
                                        {!! Form::text('transit_days',null,['class'=>'form-control clsIDtransitdays clsCOURTransitDays','id'=>'transit_days','placeholder'=>'Transit Days*']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4 padding-none">
                                    <div class="input-prepend">
                                        <span class="add-on unit-days">
                                            <div class="normal-select">
                                            {!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelTransitDaysType','id'=>'transitdays_units_relocation', 'data-serviceId' => $serviceId]) !!}
                                            </div>
					</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 form-control-fld">
                                <input type="button" id="update_more_relocationpet_property" value="Update" class="btn add-btn" style="display:none;">
                            </div>

                            <div class="clearfix"></div>
                            <div class="table-div table-style1 margin-top">
                                <!-- Table Head Starts Here -->
                                <div class="table-heading inner-block-bg">
                                    <div class="col-md-2 padding-left-none">From Location</div>
                                    <div class="col-md-1 padding-left-none">To Location</div>
                                    <div class="col-md-1 padding-left-none">Pet Type</div>
                                    <div class="col-md-1 padding-left-none">Cage Type</div>
                                    <div class="col-md-2 padding-left-none">O & D Charges</div>
                                    <div class="col-md-1 padding-left-none">Freight</div>
                                    <div class="col-md-2 padding-left-none">Transit Days</div>
                                </div>

                                <!-- Table Head Ends Here -->

                                <div class="table-data request_rows" id="household_row_items">
                                    @foreach($seller_post_edit_action_lines as $key=>$seller_post_edit_action_line)
                                        
                                            <div class="table-row inner-block-bg" id="single_property_post_item_{!! $seller_post_edit_action_line->id !!}">
                                                <div class="col-md-2 padding-left-none">{{$common->getCityName($seller_post_edit->from_location_id)}}</div>
                                                <div class="col-md-1 padding-left-none">{{$common->getCityName($seller_post_edit->to_location_id)}}</div>
                                                <div class="col-md-1 padding-left-none">{{$common->getPetType($seller_post_edit_action_line->lkp_pet_type_id)}}</div>
                                                <div class="col-md-1 padding-none">{{$common->getCageType($seller_post_edit_action_line->lkp_cage_type_id)}}</div>
                                                <div class="col-md-2 padding-left-none">{{$seller_post_edit_action_line->od_charges}} /-</div>
                                                <div class="col-md-1 padding-left-none">{{$seller_post_edit_action_line->rate_per_cft}} /-</div>
                                                <div class="col-md-2 padding-left-none">{{$seller_post_edit_action_line->transitdays}} {{$seller_post_edit_action_line->units}}</div>
                                                <div class="col-md-1 padding-left-none">
                                                    <a href='javascript:void(0)' onclick="updaterelocationpetpropertypostlineitem({!! $seller_post_edit_action_line->id !!});" row_id="{!! $seller_post_edit_action_line->id !!}"  style="cursor:pointer;"><i class="fa fa-edit red" title="Edit"></i></a>
                                                    <a row_id="1" data-string="{!! $seller_post_edit_action_line->lkp_pet_type_id !!}{!! $seller_post_edit_action_line->lkp_cage_type_id !!}" class="remove_this_line remove" style="cursor:pointer;"><!--<i class="fa fa-trash" title="Delete"></i>--></a>
                                                </div>

                                                <input type="hidden" name="pettypes_hidden[]" value="{{$seller_post_edit_action_line->lkp_pet_type_id}}">
                                                <input type="hidden" class="volume" name="cagetypes_hidden[]" value="{{$seller_post_edit_action_line->lkp_cage_type_id}}">
                                                <input type="hidden" class="freight"  name="freight_hidden[]" value="{{$seller_post_edit_action_line->rate_per_cft}}">
                                                <input type="hidden" class="transit_days"  name="transit_days_hidden[]" value="{{$seller_post_edit_action_line->transitdays}}">
                                                <input type="hidden" name="transitdays_units_relocation_hidden[]" value="{{$seller_post_edit_action_line->units}}">
                                                <input type="hidden" class="od_charges"  name="od_charges_hidden[]" value="{{$seller_post_edit_action_line->od_charges}}">
                                                <input type="hidden" value="{!! $seller_post_edit_action_line->id !!}" name="property_post_id[]">
                                            </div>
                                       
                                    @endforeach
                                    <input type="hidden" name="current_household_row_id" value="" id="current_household_row_id"/>
                                </div>
                                <!-- Table Ends Here -->
                            </div>

                        </div>

                        <div class="col-md-12 inner-block-bg inner-block-bg1">
                            <div class="col-md-12 padding-none filter">
                                <h2 class="filter-head1 margin-bottom">Additional Charges</h2>
                                
                                    <div class="terms-and-conditions-block">
                                        {!! Form::hidden('next_terms_count_search', '0', array('id' => 'next_terms_count_search')) !!}
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                {!! Form::text('cancellation_charge_price',$seller_post_edit->cancellation_charge_price,['class'=>'form-control form-control1 clsRPetCancelCharges','id'=>'cancellation_charge_price','placeholder'=>'Cancellation Charges' ]) !!}
                                                <span class="add-on unit1 manage">Rs</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 tc-block-btn"></div>
                                    </div>
                                    <div class="my-form">
                                        <div class=" text-box form-control-fld terms-and-conditions-block">
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    {!! Form::text('docket_charge_price',$seller_post_edit->docket_charge_price,['class'=>'form-control form-control1 clsRPetDocketCharges','id'=>'docket_charge_price','placeholder'=>'Other Charges'] ) !!}
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
                                                            {!! Form::text("terms_condtion_types_$i",$seller_post_edit->$price,['placeholder' => '0.00','class'=>'form-control form-control1 pricebox update_txt_test update_txt dynamic_validations clsRPetCancelCharges',($seller_post_edit->lkp_post_status_id == 2) ? '' : '']) !!}
                                                            <span class="add-on unit">Rs</span>
                                                        </div>
                                                    </div>
                                                    @if($seller_post_edit->lkp_post_status_id == 1)
                                                        <a href="#" class="remove-box col-md-2 margin-top-6" data-string="'+num+'"><i class="fa fa-trash red" title="Delete"></i></a></a>
                                                    @endif
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
                            <div class="col-md-3 form-control-fld margin-top">
                                <div class="normal-select">

                                    {!! Form::select('tracking',(['' => 'Tracking*']+$trackingtypes), $seller_post_edit->tracking, ['id' => 'tracking_ptl','class' => 'selectpicker form-control', ($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
                                </div>
                            </div>

                            <div class="clearfix"></div>
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
                                        {!! Form::checkbox('accept_payment_ptl[]', 1, $checked, ['class' => 'accept_payment_ptl',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!} <span class="lbl padding-8">Net Banking</span>
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
                            
                            @if($seller_post_edit->credit_period_units == 'Days')
                              {{--*/ $creditPeriodClass = 'form-control form-control1 clsIDCredit_period clsCreditPeriod' /*--}}
                          @elseif($seller_post_edit->credit_period_units == 'Weeks')
                              {{--*/ $creditPeriodClass = 'form-control form-control1 clsIDCredit_period clsCreditPeriodWeeks' /*--}}
                          @else
                              {{--*/ $creditPeriodClass = 'form-control form-control1 clsIDCredit_period clsCreditPeriod' /*--}}
                          @endif

                            <div class="col-md-12 form-control-fld" style='display: <?php echo ($seller_post_edit->lkp_payment_mode_id == 4) ? "block" : "none"; ?>' id = 'show_credit_period'>
                                <div class="col-md-3 form-control-fld padding-left-none">

                                    <div class="col-md-7 padding-none">
                                        <div class="input-prepend">
                                            {!! Form::text('credit_period_ptl',$seller_post_edit->credit_period,['class'=>$creditPeriodClass,'placeholder'=>'Credit Period',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
                                        </div>
                                    </div>
                                    <div class="col-md-5 padding-none">
                                        <div class="input-prepend">
                                            <span class="add-on unit-days manage">
                                                <div class="normal-select">
                                                {!! Form::select('credit_period_units',['Days' => 'Days','Weeks' => 'Weeks'], $seller_post_edit->credit_period_units, ['class' => 'selectpicker clsSelPaymentCreditType bs-select-hidden',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
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
                                    {!! Form::submit('Save as draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_relocation','onclick'=>"updatepoststatus(0)"]) !!}
                                @endif
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                    <!-- Right Section Ends Here -->
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>

@include('partials.footer')
@endsection



