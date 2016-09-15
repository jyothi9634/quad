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
            <span class="pull-left"><h1 class="page-title">Post (Relocation)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
            <span class="pull-right"><a href="{{ $backToPostsUrl }}" class="back-link">Back to Posts</a></span>


            <div class="clearfix"></div>

            <div class="col-md-12 padding-none">
                <div class="main-inner">


                    <!-- Right Section Starts Here -->

                    <div class="main-right">
                        {{--*/ $householdItems = 0; /*--}}
                        {{--*/ $vehicleItems = 0; /*--}}
                        @foreach($seller_post_edit_action_lines as $key=>$seller_post_edit_action_line)
                            @if($seller_post_edit_action_line->rate_card_type == 1)
                                {{--*/ $householdItems = $householdItems + 1 /*--}}
                            @elseif($seller_post_edit_action_line->rate_card_type == 2)
                                {{--*/ $vehicleItems = $vehicleItems + 1 /*--}}
                            @endif
                        @endforeach
                        {!! Form::open(['url' => 'relocation/updatesellerpost/'.$seller_post_edit->id,'id'=>'posts-form_relocation']) !!}
                        {!! Form::hidden('sellerpoststatus_previous', $seller_post_edit->lkp_post_status_id, array('id' => 'sellerpoststatus_previous')) !!}
                        {!! Form::hidden('sellerpoststatus', $seller_post_edit->lkp_post_status_id, array('id' => 'sellerpoststatus')) !!}
                        {!! Form::hidden('household_items', $householdItems, array('id' => 'household_items')) !!}
                        @if($householdItems<1)
                        {{--*/ $householdItemsM = 0; /*--}}
                        @else
                        {{--*/ $householdItemsM = 0; /*--}}
                        @endif
                        @if($vehicleItems<1)
                        {{--*/ $vehicleItemsM = 0; /*--}}
                        @else
                        {{--*/ $vehicleItemsM = 0; /*--}}
                        @endif
                        {!! Form::hidden('vehicle_items', $vehicleItems, array('id' => 'vehicle_items')) !!}
                        {!! Form::hidden('household_items_mandatory', $householdItemsM, array('id' => 'household_items_mandatory')) !!}
                        {!! Form::hidden('vehicle_items_mandatory', $vehicleItemsM, array('id' => 'vehicle_items_mandatory')) !!}
                        {!! Form::hidden('subscription_start_date_start', $subscription_start_date_start, array('id' => 'subscription_start_date_start')) !!}
                        {!! Form::hidden('subscription_end_date_end', $subscription_end_date_end, array('id' => 'subscription_end_date_end')) !!}
                        {!! Form::hidden('current_date_seller', $current_date_seller, array('id' => 'current_date_seller')) !!}
                        <div class="gray-bg">
                            <div class="col-md-12 padding-none filter">

                                <div class="col-md-12 form-control-fld">
                                    <div class="radio-block">
                                        @foreach($ratecardtypes as $key => $ratecardType)
                                            {{--*/ $selected = ($seller_post_edit->rate_card_type == $key) ? 'checked="checked"' : ''; /*--}}
                                            <div class="radio_inline">
                                                <input {{$selected}} class="ratetype_selection" type="radio" value="{{$key}}" name="post_rate_card_type" id="post_rate_card_type_{{$key}}" disabled = "disabled">
                                                <label for="post_rate_card_type_{{$key}}"><span></span>{{$ratecardType}}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

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

                        <div class="gray-bg relocation_house_hold_create" style='display: <?php echo ($seller_post_edit->rate_card_type != 2) ? "block" : "none"; ?>'>
                            <div class="col-md-3 form-control-fld">
                                <div class="normal-select">
                                    {!! Form::select('propertytypes', (['' => 'Select Property Type*'] + $propertytypes), '', ['class' => 'selectpicker form-control','id' => 'propertytypes','onchange'=>'return getSellerPropertyCft()']) !!}
                                </div>
                            </div>

                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-balance-scale"></i></span>
                                    {!! Form::text('volume',null,['class'=>'form-control','id'=>'volume','placeholder'=>'Volume*','readonly'=>true]) !!}
                                    <span class="add-on unit1 manage">CFT</span>
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld">
                                <div class="normal-select">
                                    {!! Form::select('loadtypes', (['' => 'Select Load Type*'] + $loadtypes), '', ['class' => 'selectpicker form-control','id' => 'load_types']) !!}
                                </div>
                            </div>

                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    {!! Form::text('rate_per_cft',null,['class'=>'form-control form-control1 clsRDSODChargespCFT','id'=>'rate_per_cft','placeholder'=>'O & D Charges (Rate / CFT)*']) !!}
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            <div class="col-md-2 form-control-fld">
                                <div class="input-prepend">
                                    {!! Form::text('transport_charges',null,['class'=>'form-control form-control1 clsRDVTransportCharges','id'=>'transport_charges','placeholder'=>'Transportation Charges*']) !!}
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
                                                        {!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelTransitDaysType','id'=>'transitdays_units_relocation']) !!}
                                                    </div>
												</span>
                                    </div>
                                </div>

                            </div>




                            <div class="col-md-4 form-control-fld">
                                <input type="button" id="update_more_relocation_property" value="Update" class="btn add-btn" style="display:none;">
                            </div>


                            <div class="clearfix"></div>

                            <div class="table-div table-style1 margin-top">

                                <!-- Table Head Starts Here -->

                                <div class="table-heading inner-block-bg">
                                    <div class="col-md-2 padding-left-none">Property Type</div>
                                    <div class="col-md-2 padding-left-none">Volume</div>
                                    <div class="col-md-2 padding-left-none">Load Type</div>
                                    <div class="col-md-2 padding-left-none">O & D Charges (per CFT)</div>
                                    <div class="col-md-2 padding-left-none">Transport Charges</div>
                                    <div class="col-md-1 padding-left-none">Transit Days</div>
                                    <div class="col-md-1 padding-left-none"></div>
                                </div>

                                <!-- Table Head Ends Here -->

                                <div class="table-data request_rows" id="household_row_items">
                                    @foreach($seller_post_edit_action_lines as $key=>$seller_post_edit_action_line)
                                        @if($seller_post_edit_action_line->rate_card_type == 1)
                                            <div class="table-row inner-block-bg" id="single_property_post_item_{!! $seller_post_edit_action_line->id !!}">
                                                <div class="col-md-2 padding-left-none">{{$common->getPropertyType($seller_post_edit_action_line->lkp_property_type_id)}}</div>
                                                <div class="col-md-2 padding-left-none">{{$seller_post_edit_action_line->volume}} CFT</div>
                                                <div class="col-md-2 padding-none">{{$common->getLoadCategoryById($seller_post_edit_action_line->lkp_load_category_id)}}</div>
                                                <div class="col-md-2 padding-left-none">{{$seller_post_edit_action_line->rate_per_cft}} /-</div>
                                                <div class="col-md-2 padding-left-none">{{$seller_post_edit_action_line->transport_charges}} /-</div>
                                                <div class="col-md-1 padding-left-none">{{$seller_post_edit_action_line->transitdays}} {{$seller_post_edit_action_line->units}}</div>
                                                <div class="col-md-1 padding-left-none">
                                                    <a href='javascript:void(0)' onclick="updaterelocationpropertypostlineitem({!! $seller_post_edit_action_line->id !!});" row_id="{!! $seller_post_edit_action_line->id !!}"  style="cursor:pointer;"><i class="fa fa-edit red" title="Edit"></i></a>
                                                    <a row_id="1" data-string="{!! $seller_post_edit_action_line->lkp_property_type_id !!}{!! $seller_post_edit_action_line->lkp_load_category_id !!}" class="remove_this_line remove" style="cursor:pointer;"><!--<i class="fa fa-trash" title="Delete"></i>--></a>
                                                </div>

                                                <input type="hidden" name="propertytypes_hidden[]" value="{{$seller_post_edit_action_line->lkp_property_type_id}}">
                                                <input type="hidden" class="volume" name="volume_hidden[]" value="{{$seller_post_edit_action_line->volume}}">
                                                <input type="hidden" class="rate_per_cft"  name="rate_per_cft_hidden[]" value="{{$seller_post_edit_action_line->rate_per_cft}}">
                                                <input type="hidden" class="transit_days"  name="transit_days_hidden[]" value="{{$seller_post_edit_action_line->transitdays}}">
                                                <input type="hidden" name="transitdays_units_relocation_hidden[]" value="{{$seller_post_edit_action_line->units}}">
                                                <input type="hidden"  name="load_types_hidden[]" value="{{$seller_post_edit_action_line->lkp_load_category_id}}">
                                                <input type="hidden" class="transport_charges"  name="transport_charges_hidden[]" value="{{$seller_post_edit_action_line->transport_charges}}">
                                                <input type="hidden" value="{!! $seller_post_edit_action_line->id !!}" name="property_post_id[]">
                                            </div>
                                        @endif
                                    @endforeach
                                    <input type="hidden" name="current_household_row_id" value="" id="current_household_row_id"/>
                                </div>


                                <!-- Table Ends Here -->




                            </div>

                        </div>


                        <div class="gray-bg relocation_vehicle_create" style='display: <?php echo ($seller_post_edit->rate_card_type != 1) ? "block" : "none"; ?>'>
                            <div class="col-md-3 form-control-fld">
                                <div class="normal-select">
                                    {!! Form::select('vehicle_types', (['' => 'Select Vehicle Type*'] + $vehicletypes), '', ['class' => 'selectpicker form-control','id' => 'vehicle_types','onchange'=>'return getSellerVehicleTypes()']) !!}
                                </div>
                            </div>

                            <div class="col-md-3 form-control-fld vehicle_type_car">
                                <div class="normal-select">
                                    {!! Form::select('vehicle_type_category', (['' => 'Select Car Size*'] + $vehicletypecategories), '', ['class' => 'selectpicker form-control','id' => 'vehicle_type_category']) !!}
                                </div>
                            </div>

                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    {!! Form::text('cost',null,['class'=>'form-control form-control1 sixdigitstwodecimals_deciVal numberVal','id'=>'cost','placeholder'=>'Cost*']) !!}
                                </div>
                            </div>
                            <div class="col-md-2 form-control-fld">
                                <div class="input-prepend">
                                    {!! Form::text('transport_charges_vehicle',null,['class'=>'form-control form-control1 clsRDVTransportCharges','id'=>'transport_charges_vehicle','placeholder'=>'Transportation Charges*']) !!}
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld">
                                <div class="col-md-8 padding-none">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-hourglass-1"></i></span>
                                        {!! Form::text('transit_days_vehicle',null,['class'=>'form-control form-control1 clsIDtransitdays clsCOURTransitDays','id'=>'transit_days_vehicle','placeholder'=>'Transit Days*']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4 padding-none">
                                    <div class="input-prepend">
												<span class="add-on unit-days">
													<div class="normal-select">
                                                        {!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelTransitDaysType','id'=>'transitdays_units_relocation_vehicle',  'data-serviceId' => $serviceId]) !!}
                                                    </div>
												</span>
                                    </div>
                                </div>

                            </div>



                            <div class="col-md-1 form-control-fld">
                                <input type="button" id="update_more_relocation_vehicle" value="Update" class="btn add-btn" style="display:none;">
                            </div>


                            <div class="clearfix"></div>

                            <div class="table-style table-style1 margin-top">

                                <!-- Table Head Starts Here -->

                                <div class="table-heading inner-block-bg">
                                    <div class="col-md-3 padding-left-none">Vehicle Category</div>
                                    <div class="col-md-2 padding-left-none">Car Type</div>
                                    <div class="col-md-2 padding-left-none">Cost</div>
                                    <div class="col-md-2 padding-none">Transport Charges</div>
                                    <div class="col-md-2 padding-left-none">Transit Days</div>
                                    <div class="col-md-2 padding-left-none"></div>
                                </div>

                                <!-- Table Head Ends Here -->

                                <div class="table-data request_rows" id="vehicle_row_items">
                                    @foreach($seller_post_edit_action_lines as $key=>$seller_post_edit_action_line)
                                        @if($seller_post_edit_action_line->rate_card_type == 2)
                                            <div class="table-row inner-block-bg"  id="single_vehicle_post_item_{!! $seller_post_edit_action_line->id !!}">
                                                <div class="col-md-3 padding-left-none">{{$common->getVehicleCategoryById($seller_post_edit_action_line->lkp_vehicle_category_id)}}</div>
                                                <div class="col-md-2 padding-left-none">{{$common->getVehicleCategorytypeById($seller_post_edit_action_line->lkp_car_size)}}</div>
                                                <div class="col-md-2 padding-none">{{$seller_post_edit_action_line->cost}}</div>
                                                <div class="col-md-2 padding-left-none">{{$seller_post_edit_action_line->transport_charges}} /-</div>
                                                <div class="col-md-2 padding-left-none">{{$seller_post_edit_action_line->transitdays}} {{$seller_post_edit_action_line->units}}</div>
                                                <div class="col-md-1 padding-left-none">
                                                    <a href='javascript:void(0)' onclick="updaterelocationvehiclepostlineitem({!! $seller_post_edit_action_line->id !!});" row_id="{!! $seller_post_edit_action_line->id !!}"  style="cursor:pointer;"><i class="fa fa-edit red" title="Edit"></i></a>
                                                    <a row_id="1" data-string="{!! $seller_post_edit_action_line->lkp_vehicle_category_id !!}{!! $seller_post_edit_action_line->lkp_car_size !!}" class="remove_this_line remove" style="cursor:pointer;"><!--<i class="fa fa-trash" title="Delete"></i>--></a>
                                                </div>

                                                <input type="hidden" name="vehicle_types_hidden[]" value="{{$seller_post_edit_action_line->lkp_vehicle_category_id}}">
                                                <input type="hidden" name="vehicle_type_category_hidden[]" value="{{$seller_post_edit_action_line->lkp_car_size}}">
                                                <input type="hidden" class="cost"  name="cost_hidden[]" value="{{$seller_post_edit_action_line->cost}}">
                                                <input type="hidden" class="transit_days_vehicle" name="transit_days_vehicle_hidden[]" value="{{$seller_post_edit_action_line->transitdays}}">
                                                <input type="hidden" name="transitdays_units_relocation_vehicle_hidden[]" value="{{$seller_post_edit_action_line->units}}">
                                                <input type="hidden" class="transport_charges_vehicle" name="transport_charges_vehicle_hidden[]" value="{{$seller_post_edit_action_line->transport_charges}}">
                                                <input type="hidden" value="{!! $seller_post_edit_action_line->id !!}" name="vehicle_post_id[]">
                                            </div>
                                        @endif
                                    @endforeach
                                    <input type="hidden" name="current_vehicle_row_id" value="" id="current_vehicle_row_id"/>
                                </div>
                                <!-- Table Ends Here -->

                            </div>
                        </div>


                        <div class="col-md-12 inner-block-bg inner-block-bg1">
                            <div class="col-md-12 padding-none filter">
                                <h2 class="filter-head1 margin-bottom">Additional Charges</h2>
                                @if($seller_post_edit->rate_card_type != 2)
                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">

                                            {!! Form::text('crating_charges',$seller_post_edit->crating_charges,['class'=>'form-control form-control1 clsRDSCratingChargespCFT','id'=>'crating_charges','placeholder'=>'Crating Charges' ]) !!}
                                                    <span class="add-on unit1 manage">
                                                        Per CFT
                                                    </span>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        {!! Form::text('storate_charges',$seller_post_edit->storate_charges,['class'=>'form-control form-control1 clsRDSStorageChargespCFTpDay','id'=>'storate_charges','placeholder'=>'Storage Charges' ]) !!}
                                        		<span class="add-on unit1 manage">
													@if($seller_post_edit->rate_card_type != 2)
                                                        Per CFT/Day
                                                    @else
                                                        Per Day
                                                    @endif
												</span>
                                    </div>
                                </div>
                                @if($seller_post_edit->rate_card_type != 2)
                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                            {!! Form::text('escort_charges',$seller_post_edit->escort_charges,['class'=>'form-control form-control1 clsRDSEscortChargespDay','id'=>'escort_charges','placeholder'=>'Escort Charges' ]) !!}
                                                    <span class="add-on unit1 manage">
                                                        Per Day
                                                    </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                            {!! Form::text('handyman_charges',$seller_post_edit->handyman_charges,['class'=>'form-control form-control1 clsRDSHandymanChargespHour','id'=>'handyman_charges','placeholder'=>'Handyman Charges' ]) !!}
                                                    <span class="add-on unit1 manage">
                                                        Per Hour
                                                    </span>
                                        </div>
                                    </div>

                                    <div class="clearfix"></div>

                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                            {!! Form::text('property_search',$seller_post_edit->property_search,['class'=>'form-control form-control1 clsRDSPropertySearchCharges','id'=>'property_search','placeholder'=>'Property Search'] ) !!}
                                                    <span class="add-on unit1 manage">
                                                        Rs
                                                    </span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                            {!! Form::text('brokerage',$seller_post_edit->brokerage,['class'=>'form-control form-control1 clsRDSBrokerageCharges','id'=>'brokerage','placeholder'=>'Brokerage' ]) !!}
                                                    <span class="add-on unit1 manage">
                                                        %
                                                    </span>
                                        </div>
                                    </div>
                                @endif
                                <div class="clearfix"></div>
                                @if($seller_post_edit->rate_card_type != 2)
                                    <div class="terms-and-conditions-block">
                                        {!! Form::hidden('next_terms_count_search', '0', array('id' => 'next_terms_count_search')) !!}
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                {!! Form::text('cancellation_charge_price',$seller_post_edit->cancellation_charge_price,['class'=>'form-control form-control1 fourdigitstwodecimals_deciVal numberVal','id'=>'cancellation_charge_price','placeholder'=>'Cancellation Charges' ]) !!}
                                                <span class="add-on unit1 manage">Rs</span>
                                            </div>
                                        </div>
                                        <div class="col-md-3 tc-block-btn"></div>
                                    </div>
                                    <div class="my-form">
                                        <div class=" text-box form-control-fld terms-and-conditions-block">
                                            <div class="col-md-3 form-control-fld">
                                                <div class="input-prepend">
                                                    {!! Form::text('docket_charge_price',$seller_post_edit->docket_charge_price,['class'=>'form-control form-control1 fourdigitstwodecimals_deciVal numberVal','id'=>'docket_charge_price','placeholder'=>'Other Charges'] ) !!}
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
                                                            {!! Form::text("terms_condtion_types_$i",$seller_post_edit->$price,['placeholder' => '0.00','class'=>'form-control form-control1 pricebox update_txt_test update_txt dynamic_validations fourdigitstwodecimals_deciVal numberVal',($seller_post_edit->lkp_post_status_id == 2) ? '' : '']) !!}
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

                                 @endif
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



