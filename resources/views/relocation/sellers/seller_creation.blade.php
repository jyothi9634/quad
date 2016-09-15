@extends('app')
@section('content')
        <!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
{{--*/ $serviceId = Session::get('service_id') /*--}}
    <div class="main">

        <div class="container">
            <span class="pull-left"><h1 class="page-title">Post (Relocation)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
            @if ($url_search_search == 'buyersearchresults')
            <span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">Back to Search</a></span>
			@endif

            <div class="clearfix"></div>

            <div class="col-md-12 padding-none">
                <div class="main-inner">


                    <!-- Right Section Starts Here -->

                    <div class="main-right">

                        {!! Form::open(['url' => 'relocationsellerpostcreation','id'=>'posts-form_relocation', 'autocomplete'=>'off']) !!}
                        {!! Form::hidden('sellerpoststatus', '1', array('id' => 'sellerpoststatus')) !!}
                        {!! Form::hidden('household_items', '0', array('id' => 'household_items')) !!}
                        {!! Form::hidden('vehicle_items', '0', array('id' => 'vehicle_items')) !!}
                        {!! Form::hidden('household_items_mandatory', '0', array('id' => 'household_items_mandatory')) !!}
                        {!! Form::hidden('vehicle_items_mandatory', '0', array('id' => 'vehicle_items_mandatory')) !!}
                        {!! Form::hidden('subscription_start_date_start', $subscription_start_date_start, array('id' => 'subscription_start_date_start')) !!}
                        {!! Form::hidden('subscription_end_date_end', $subscription_end_date_end, array('id' => 'subscription_end_date_end')) !!}
                        {!! Form::hidden('current_date_seller', $current_date_seller, array('id' => 'current_date_seller')) !!}
                        {!! Form::hidden('valid_from_val', '', array('id' => 'valid_from_val')) !!}
                    	{!! Form::hidden('valid_to_val', '', array('id' => 'valid_to_val')) !!}
                    	{!!	Form::hidden('update_reloc_seller_line',0,array('class'=>'','id'=>'update_reloc_seller_line'))!!}
						{!!	Form::hidden('update_reloc_seller_row_count','',array('class'=>'','id'=>'update_reloc_seller_row_count'))!!}
						{!!	Form::hidden('update_reloc_seller_row_unique','',array('class'=>'','id'=>'update_reloc_seller_row_unique'))!!}
						
                    	<input type="hidden" id='next_add_more_id_reloc' value='0'>
                    	<input type="hidden" id='next_add_more_veh_id_reloc' value='0'>
                        <div class="gray-bg">
                            <div class="col-md-12 padding-none filter">

                                <div class="col-md-12 form-control-fld">
                                    <div class="radio-block">
                                        @foreach($ratecardtypes as $key => $ratecardType)
                                            {{--*/ $selected = ($session_search_values_create[7] == $key) ? 'checked="checked"' : ''; /*--}}
                                            <div class="radio_inline">
                                                <input {{$selected}} class="ratetype_selection" type="radio" value="{{$key}}" name="post_rate_card_type" id="post_rate_card_type_{{$key}}">
                                                <label for="post_rate_card_type_{{$key}}"><span></span>{{$ratecardType}}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>

                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                        {!! Form::text('from_location', $session_search_values_create[0], ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*']) !!}
                                        {!! Form::hidden('from_location_id', $session_search_values_create[1], array('id' => 'from_location_id')) !!}
                                        {!! Form::hidden('seller_district_id', $session_search_values_create[2], array('id' => 'seller_district_id')) !!}
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                        {!! Form::text('to_location', $session_search_values_create[3], ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*']) !!}
                                        {!! Form::hidden('to_location_id', $session_search_values_create[4], array('id' => 'to_location_id')) !!}
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                        {!! Form::text('valid_from', $session_search_values_create[5], ['id' => 'datepicker','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Valid From*']) !!}
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                        {!! Form::text('valid_to', $session_search_values_create[6], ['id' => 'datepicker_to_location','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Valid To*']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{--*/ $display = ($session_search_values_create[7] == 1) ? 'block' : 'none'; /*--}}
                        <div class="gray-bg relocation_house_hold_create" style="display: {{$display}}">
                            <div class="col-md-3 form-control-fld">
                                <div class="normal-select">
                                    {!! Form::select('propertytypes', (['' => 'Select Property Type*'] + $propertytypes), $session_search_values_create[3], ['class' => 'selectpicker form-control','id' => 'propertytypes','onchange'=>'return getSellerPropertyCft()']) !!}
                                </div>
                            </div>

                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-balance-scale"></i></span>
                                    {!! Form::text('volume',null,['class'=>'form-control clsRDSVolumeCFT','id'=>'volume','placeholder'=>'Volume*','readonly'=>true,]) !!}
                                    <span class="add-on unit1 manage">CFT</span>
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld">
                                <div class="normal-select">
                                    {!! Form::select('loadtypes', (['' => 'Select Load Type*'] + $loadtypes), $session_search_values_create[3], ['class' => 'selectpicker form-control','id' => 'load_types']) !!}
                                </div>
                            </div>
                            
                            

                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    {!! Form::text('rate_per_cft',null,['class'=>'form-control form-control1 clsRDSODChargespCFT','id'=>'rate_per_cft','placeholder'=>'O & D Charges (Rate / CFT)*']) !!}
                                </div>
                            </div>

                            <div class="clearfix"></div>
                            
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    {!! Form::text('transport_charges',null,['class'=>'form-control form-control1 clsRDSTransportCharges','id'=>'transport_charges','placeholder'=>'Transportation Charges *']) !!}
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld">
                                <div class="col-md-8 padding-none">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-hourglass-1"></i></span>
                                        {!! Form::text('transit_days',null,['class'=>'form-control clsIDtransitdaysHHG clsCOURTransitDays','id'=>'transit_days','placeholder'=>'Transit Days*']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4 padding-none">
                                    <div class="input-prepend">
										<span class="add-on unit-days">
											<div class="normal-select">
                                                {!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelTransitDaysTypeHHG','id'=>'transitdays_units_relocation', 'data-serviceId' => $serviceId, 'data-posttype' => 'HHG']) !!}
                                            </div>
										</span>
                                    </div>
                                </div>

                            </div>




                            <div class="col-md-4 form-control-fld">
                                <input type="button" id="add_more_relocation" value="Add" class="btn add-btn">
                            </div>


                            <div class="clearfix"></div>

                            <div class="table-div table-style1">

                                <!-- Table Head Starts Here -->

                                <div class="table-heading inner-block-bg">
                                    <div class="col-md-2 padding-left-none">Property Type</div>
                                    <div class="col-md-1 padding-left-none">Volume</div>
                                    <div class="col-md-2 padding-left-none">O & D Charges (per CFT)</div>
                                    <div class="col-md-2 padding-left-none">Transit Days</div>
                                    <div class="col-md-2 padding-left-none">Load Type</div>
                                    <div class="col-md-2 padding-left-none">Transport Charges</div>
                                    <div class="col-md-1 padding-left-none"></div>
                               

                                </div>

                                <!-- Table Head Ends Here -->

                                <div class="table-data request_rows" id="household_row_items">


                                </div>


                                <!-- Table Ends Here -->




                            </div>

                        </div>

                        {{--*/ $display = ($session_search_values_create[7] == 2) ? 'block' : 'none'; /*--}}
                        <div class="gray-bg relocation_vehicle_create" style="display: {{$display}}">
                            <div class="col-md-3 form-control-fld">
                                <div class="normal-select">
                                    {!! Form::select('vehicle_types', (['' => 'Select Vehicle Type*'] + $vehicletypes), $session_search_values_create[3], ['class' => 'selectpicker form-control','id' => 'vehicle_types','onchange'=>'return getSellerVehicleTypes()']) !!}
                                </div>
                            </div>

                            <div class="col-md-3 form-control-fld vehicle_type_car">
                                <div class="normal-select">
                                    {!! Form::select('vehicle_type_category', (['' => 'Select Car Size*'] + $vehicletypecategories), $session_search_values_create[3], ['class' => 'selectpicker form-control','id' => 'vehicle_type_category']) !!}
                                </div>
                            </div>

                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    {!! Form::text('cost',null,['class'=>'form-control form-control1 clsRDVCost','id'=>'cost','placeholder'=>'Cost*']) !!}
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    {!! Form::text('transport_charges_vehicle',null,['class'=>'form-control form-control1 clsRDVTransportCharges','id'=>'transport_charges_vehicle','placeholder'=>'Transportation Charges*']) !!}
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld">
                                <div class="col-md-8 padding-none">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-hourglass-1"></i></span>
                                        {!! Form::text('transit_days_vehicle',null,['class'=>'form-control clsIDtransitdaysVEH clsCOURTransitDays','id'=>'transit_days_vehicle','placeholder'=>'Transit Days*']) !!}
                                    </div>
                                </div>
                                <div class="col-md-4 padding-none">
                                    <div class="input-prepend">
												<span class="add-on unit-days">
													<div class="normal-select">
                                                        {!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelTransitDaysTypeVEH','id'=>'transitdays_units_relocation_vehicle', 'data-serviceId' => $serviceId, 'data-posttype' => 'VEH']) !!}
                                                    </div>
												</span>
                                    </div>
                                </div>

                            </div>



                            <div class="col-md-1 form-control-fld">
                            {!!	Form::hidden('update_reloc_veh_seller_line',0,array('class'=>'','id'=>'update_reloc_veh_seller_line'))!!}
							{!!	Form::hidden('update_reloc_veh_seller_row_count','',array('class'=>'','id'=>'update_reloc_veh_seller_row_count'))!!}	
							{!!	Form::hidden('update_reloc_veh_seller_row_unique','',array('class'=>'','id'=>'update_reloc_veh_seller_row_unique'))!!}
                                <input type="button" id="add_more_relocation_vehicle" value="Add" class="btn add-btn">
                            </div>

							
                            <div class="clearfix"></div>

                            <div class="table-div table-style1">

                                <!-- Table Head Starts Here -->

                                <div class="table-heading inner-block-bg">
                                    <div class="col-md-3 padding-left-none">Vehicle Type</div>
                                    <div class="col-md-2 padding-left-none">Car Type</div>
                                    <div class="col-md-1 padding-left-none">Cost</div>
                                    <div class="col-md-2 padding-left-none">Transit Days</div>
                                    <div class="col-md-2 padding-none">Transport Charges</div>


                                </div>

                                <!-- Table Head Ends Here -->

                                <div class="table-data request_rows" id="vehicle_row_items">


                                </div>
                                <!-- Table Ends Here -->

                            </div>
                        </div>


                        <div class="col-md-12 inner-block-bg inner-block-bg1">
                            <div class="col-md-12 padding-none filter">
                                <h2 class="filter-head1 margin-bottom">Additional Charges</h2>
                                <div class="col-md-3 form-control-fld vehicle_not_display">
                                    <div class="input-prepend">
                                        {!! Form::text('crating_charges',null,['class'=>'form-control form-control1 clsRDSCratingChargespCFT','id'=>'crating_charges','placeholder'=>'Crating Charges']) !!}
                                        		<span class="add-on unit1 manage">
													Per CFT
												</span>
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld ">
                                    <div class="input-prepend">
                                        {!! Form::text('storate_charges',null,['class'=>'form-control form-control1 clsRDSStorageChargespCFTpDay','id'=>'storate_charges','placeholder'=>'Storage Charges']) !!}
                                        		<span class="add-on unit1 manage storate_charges">
													Per CFT/Day
												</span>
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld vehicle_not_display">
                                    <div class="input-prepend">
                                        {!! Form::text('escort_charges',null,['class'=>'form-control form-control1 clsRDSEscortChargespDay','id'=>'escort_charges','placeholder'=>'Escort Charges']) !!}
                                        		<span class="add-on unit1 manage">
													Per Day
												</span>
                                    </div>
                                </div>
                                <div class="col-md-3 form-control-fld vehicle_not_display">
                                    <div class="input-prepend">
                                        {!! Form::text('handyman_charges',null,['class'=>'form-control form-control1 clsRDSHandymanChargespHour','id'=>'handyman_charges','placeholder'=>'Handyman Charges']) !!}
                                        		<span class="add-on unit1 manage">
													Per Hour
												</span>
                                    </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-md-3 form-control-fld vehicle_not_display">
                                    <div class="input-prepend">
                                        {!! Form::text('property_search',null,['class'=>'form-control form-control1 clsRDSPropertySearchCharges','id'=>'property_search','placeholder'=>'Property Search']) !!}
                                		<span class="add-on unit1 manage">Rs</span>
                                    </div>
                                </div>

                                <div class="col-md-3 form-control-fld vehicle_not_display">
                                    <div class="input-prepend">
                                        {!! Form::text('brokerage',null,['class'=>'form-control form-control1 clsRDSBrokerageCharges','id'=>'brokerage','placeholder'=>'Brokerage']) !!}
                                        		<span class="add-on unit1 manage">
													%
												</span>
                                    </div>
                                </div>
                                <div class="clearfix"></div>

                                <div class="terms-and-conditions-block">
                                    {!! Form::hidden('next_terms_count_search', '0', array('id' => 'next_terms_count_search')) !!}
                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                            {!! Form::text('cancellation_charge_price','',['class'=>'form-control form-control1 clsRDSCancellationCharges','id'=>'cancellation_charge_price','placeholder'=>'Cancellation Charges']) !!}
                                            <span class="add-on unit1 manage">Rs</span>
                                        </div>
                                    </div>
                                    <div class="col-md-3 tc-block-btn"></div>
                                </div>
                                <div class="my-form">
                                    <div class=" text-box form-control-fld terms-and-conditions-block padding-none">
                                        <div class="col-md-3 form-control-fld">
                                            <div class="input-prepend">
                                                {!! Form::text('docket_charge_price','',['class'=>'form-control form-control1 clsRDSOtherCharges','id'=>'docket_charge_price','placeholder'=>'Other Charges']) !!}
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

                            <div class="col-md-3 form-control-fld margin-top">
                                <div class="normal-select">
                                    {!! Form::select('tracking',(['' => 'Tracking*']+$trackingtypes), null, ['id' => 'tracking_ptl','class' => 'selectpicker']) !!}
                                </div>
                            </div>

                            <div class="clearfix"></div>

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
                                {!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => 'add_quote_seller_id_relocation','onclick'=>"updatepoststatus(1)"]) !!}
                                {!! Form::submit('Save as draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_relocation','onclick'=>"updatepoststatus(0)"]) !!}
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



