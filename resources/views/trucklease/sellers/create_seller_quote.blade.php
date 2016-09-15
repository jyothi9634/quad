@extends('app')
@section('content')

<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

<!-- This code for display the back to search link ,if user navigate from seller search resuls page ends-->

        <div class="main">

            <div class="container">
                <span class="pull-left"><h1 class="page-title">Post (Truck Lease)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
               @if ($url_search_search == 'buyersearchresults')
                <span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">Back to Search</a></span>
               @endif

                <div class="clearfix"></div>

				<div class="col-md-12 inner-block-bg single-layout padding-none margin-bottom-none">
                    
                   <div class="col-md-12 inner-block-bg inner-block-bg1 margin-bottom-none border-bottom-none padding-bottom-none">
                    <div class="col-md-12 padding-none inner-form margin-top">
					{!! Form::open(['url' => 'trucklease/addsellerpost','id'=>'trucklease-posts-form-lines']) !!}
						
                            <div class="col-md-3 form-control-fld padding-left-none">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                    {!! Form::text('valid_from', $session_search_values_create[1], ['id' => 'datepicker','class' => 'clsFromDate form-control from-date-control','readonly' => true, 'placeholder' => 'Valid From*']) !!}
                                    <input type="hidden" name ='need_driver' id='need_driver' value='0'>
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                    {!! Form::text('valid_to', $session_search_values_create[0], ['id' => 'datepicker_to_location','class' => 'clsToDate form-control to-date-control','readonly' => true, 'placeholder' => 'Valid To *']) !!}
                                </div>
                            </div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									 <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                    {!! Form::text('from_location', $session_search_values_create[6], ['id' => 'from_location','class' => 'form-control clsLocation', 'placeholder' => 'Location *']) !!}
                                    {!! Form::hidden('from_location_id', $session_search_values_create[4], array('id' => 'from_location_id')) !!}
                                    {!! Form::hidden('seller_district_id', $session_search_values_create[8], array('id' => 'seller_district_id')) !!}
                                </div>
							</div>
							
                            <div class="clearfix"></div>

                            <div class="col-md-3 form-control-fld  padding-left-none">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-archive"></i></span>
                                    {!! Form::select('LeaseTerms', (['' => 'Select Lease Term*'] + $leasetypemasters), $session_search_values_create[9], ['class' => 'selectpicker leaseterm','id' => 'lease_type', 'onchange' => "changeRateClass(this.value)"]) !!}
                                </div>
                            </div>
                            
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
									{!! Form::text('MinimumLeasePeriod', '', ['class' => 'form-control form-control1 clsTLMinLeasePeriod','id' => 'minimum_lease_period', 'placeholder' => ' Minimum Lease Period']) !!}
									<span class="add-on unit1 manage minunit">Term Unit</span>                                    
                                </div>
                            </div>
                            
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend multi_select">
                                    <span class="add-on"><i class="fa fa-archive"></i></span>
                                    {!! Form::select('LoadTypeMasters', ($loadtypemasters), $session_search_values_create[3], ['class' => 'm_select','id' => 'load_type','multiple' => 'multiple']) !!}
                                </div>
                            </div>

                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-truck"></i></span>
                                    {!! Form::select('VehicleTypeMasters', (['' => 'Select Vehicle Type*'] + $vehicletypemasters), $session_search_values_create[2], ['class' => 'selectpicker form-control','id' => 'vechile_type']) !!}
                                </div>
                            </div>
                            <div class="clearfix"></div>
                            
                            <div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									 {!! Form::text('VehicleNumber',null,['class'=>'form-control clsVehicleModel','id'=>'vehiclenumber','placeholder'=>'Vehicle Make & Model & Year *']) !!}
								</div>
							</div>

							<div class="col-md-2 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-tint"></i></span>
									<select class="selectpicker FuelNeed"  id="fuel_need">
										<option>Fuel</option>
										<option value="1">Included</option>
										<option value="0">Not Included</option>
									</select>
								</div>
							</div>

							<div class="col-md-3 form-control-fld">
								<div class="input-prepend multi_select national-permit">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::select('SelectStatesPermit', ($allstates), $session_search_values_create[3], ['class' => 'm_select','id' => 'permitstates','multiple' => 'multiple']) !!}
									<!--{!! Form::select('SelectStatesPermit', (['' => 'Select Permit 	*'] + $allstates), '', ['class' => 'selectpicker form-control','id' => 'states']) !!} -->
								</div>
							</div>

							<div class="col-md-1 form-control-fld">
								<div>
									<img src="../images/truck.png">
	                                <span id="dimensions"> 7x4</span>
								</div>
							</div>
                            <div class="clearfix"></div>
                            <div class="col-lg-3 col-md-4 form-control-fld">
								<div>
	                            	<div class="checkbox_inline padding-top-6">
										{!! Form::checkbox('check_driver_availablity', 1, false,array('id'=>'check_driver_availablity')) !!}
										<span class="lbl padding-8 padding-top-3"><b>Need Driver</b></span>
									</div>
								
									{!! Form::text('driver_cost',null,['class'=>'form-control form-control1 driver_cost clsTLDriverCost','id'=>'driver_cost','disabled'=>'disabled','placeholder'=>'Driver Cost']) !!}
								</div>
							</div>
                           

                                <div class="col-md-2 form-control-fld">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="fa fa-rupee "></i></span>
                                        {!! Form::text('price',null,['class'=>'form-control numberVal fourdigitstwodecimals_deciVal','id'=>'price','placeholder'=>'Rate*']) !!}
                                    </div>   
                                </div>
                               


                           
                            <div class="col-md-3 form-control-fld">
                                <input type="button" id="add_more_tl" value="Add" class="btn add-btn">
                            </div>
                            {!!	Form::hidden('update_lease_seller_line',0,array('class'=>'','id'=>'update_lease_seller_line'))!!}
							{!!	Form::hidden('update_lease_seller_row_count','',array('class'=>'','id'=>'update_lease_seller_row_count'))!!}
							{!!	Form::hidden('update_lease_seller_row_unique','',array('class'=>'','id'=>'update_lease_seller_row_unique'))!!}
                            {!! Form::close() !!}
                    </div>
                    
                    </div>
                    
                    
 					<div class="clearfix"></div>
                   {!! Form::open(['url' => '/truckleaseaddseller','id'=>'trucklease-posts-form']) !!}
                    <div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none padding-bottom-none">
                        <div class="main-inner">
                            <!-- Right Section Starts Here -->
                            <div class="main-right">
                                <!-- Table Starts Here -->
                                <div class="table-div table-style1 padding-none">
                                    <!-- Table Head Starts Here -->
                                    <div class="table-heading inner-block-bg">
                                       <div class="col-md-2 padding-left-none">Location<i class="fa  fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">Lease Term<i class="fa  fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">Min. Lease Period<i class="fa  fa-caret-down"></i></div>
										<div class="col-md-2 padding-left-none">Vehicle Type<i class="fa  fa-caret-down"></i></div>
                                        <div class="col-md-2 padding-left-none">Preferred Goods<i class="fa  fa-caret-down"></i></div>
										<div class="col-md-1 padding-left-none">Rate<i class="fa  fa-caret-down"></i></div>
										<div class="col-md-1 padding-none"></div>
                                    </div>
                                    <!-- Table Head Ends Here -->
                                    <div class="table-data">
                                      
                                        <input type="hidden" id='next_add_more_id' value='0'>
                                        <div id ="multi-line-itemes">
                                            <div class="table-data request_rows" id=""></div>
                                        </div>

                                    </div>
                                </div>   
                            </div>
                        </div>
                    <div class="clearfix"></div>
                   </div>
                   
                    
                    {!! Form::hidden('service_id', '1', array('id' => 'service_id')) !!}
                    {!! Form::hidden('valid_from_val', '', array('id' => 'valid_from_val')) !!}
                    {!! Form::hidden('labeltext[]', 'Cancellation Charges', array('id' => '')) !!}
                    {!! Form::hidden('labeltext[]', 'Other Charges', array('id' => '')) !!}
                    {!! Form::hidden('valid_to_val', '', array('id' => 'valid_to_val')) !!}
                    {!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
                    {!! Form::hidden('subscription_start_date_start', $subscription_start_date_start, array('id' => 'subscription_start_date_start')) !!}
                    {!! Form::hidden('subscription_end_date_end', $subscription_end_date_end, array('id' => 'subscription_end_date_end')) !!}
                    {!! Form::hidden('current_date_seller', $current_date_seller, array('id' => 'current_date_seller')) !!}
                    <input type="hidden" name ='next_terms_count_search' id='next_terms_count_search' value='0'>
        

            <div class="col-md-12 inner-block-bg inner-block-bg1">

                    <h2 class="filter-head1">Payment Terms</h2>

                    <div class="col-md-3 form-control-fld">
                        <div class="normal-select">
                            {!! Form::select('Payment Terms', ($PaymentTerms), null, ['class' => 'selectpicker','id' => 'payment_options']) !!}
                        </div>
                    </div>

                    <div class="col-md-12 form-control-fld" id="show_advanced_period">
                        <div class="check-block">
                            <div class="checkbox_inline">{!! Form::checkbox('accept_payment[]', 1, '', false, ['class' => 'accept_payment']) !!}<span class="lbl padding-8">NEFT/RTGS</span></div>
                            <div class="checkbox_inline">{!! Form::checkbox('accept_payment[]', 2, '', false, ['class' => 'accept_payment']) !!}<span class="lbl padding-8">Credit Card</span></div>
                            <div class="checkbox_inline">{!! Form::checkbox('accept_payment[]', 3, '', false, ['class' => 'accept_payment']) !!}<span class="lbl padding-8">Debit Card</span></div>

                        </div>
                    </div>

                    <div class="col-md-12 form-control-fld" style="display: none;" id="show_credit_period">
                        <div class="col-md-3 form-control-fld padding-left-none">
                        	
                        	<div class="col-md-7 padding-none">
                        		<div class="input-prepend">
                                {!! Form::text('credit_period',null,['class'=>'form-control form-control1 clsIDCredit_period clsCreditPeriod','placeholder'=>'Credit Period']) !!}
                                
                            </div>
                        	</div>
                        	<div class="col-md-5 padding-none">
                        		<div class="input-prepend">
                        			<span class="add-on unit-days">
                                            <div class="normal-select">
                                                {!! Form::select('credit_period_units',['Days' => 'Days','Weeks' => 'Weeks'], null, ['class' => 'selectpicker clsSelPaymentCreditType bs-select-hidden']) !!}       
                                            </div>
                                        </span>
                        		</div>
                        	</div>
                        
                            
                            
                        </div>

                        <div class="col-md-12 padding-none">
                            <div class="checkbox_inline">
                                {!! Form::checkbox('accept_credit_netbanking[]', 1, false) !!}<span class="lbl padding-8">Net Banking</span>
                                {!! Form::checkbox('accept_credit_netbanking[]', 2, false) !!}<span class="lbl padding-8">Cheque / DD</span>
                            </div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                </div> 
<div class="col-md-12 inner-block-bg inner-block-bg1">

                    <h2 class="filter-head1">Additional Charges</h2>

                    <div class="form-control-fld terms-and-conditions-block">
                        <div class="col-md-3 padding-none tc-block-fld">
                            <div class="input-prepend">
                                <input type="text" name="terms_condtion_types1" class="form-control form-control1 clsTLCancelCharges" value="" placeholder ='Cancellation Charges *' id="cancellation1" />
                                <span class="add-on unit">Rs</span>
                            </div>
                        </div>
                        <div class="col-md-3 tc-block-btn"></div>
                    </div>

                    <div class="my-form">
                        <div class="text-box form-control-fld terms-and-conditions-block">
                            <div class="col-md-3 padding-none tc-block-fld">
                                <div class="input-prepend">
                                    <input type="text" name="terms_condtion_types2"  placeholder ='Other Charges *' class="form-control form-control1 clsTLOtherCharges" id="cancellation2" />
                                    <span class="add-on unit">Rs</span>
                                </div>
                            </div>
                            <div class="col-md-3 tc-block-btn"><input type="button" class="add-box btn add-btn" value="Add"></div>
                        </div>
                    </div>



                    <div class="col-md-6 form-control-fld">
                          {!! Form::textarea('terms_conditions',null,['class'=>'form-control form-control1 clsTermsConditions','id'=>'terms_conditions', 'rows' => 5,'placeholder' => 'Notes to Terms & Conditions (Optional)']) !!}
                    </div>
                    <div class="clearfix"></div>
</div> 
<div class="col-md-12 inner-block-bg inner-block-bg1">
                    <div class="col-md-12 form-control-fld">
                        <div class="radio-block">
                            <div class="radio_inline"><input type="radio" name="optradio" id="post-public" value="1" checked="checked" class="create-posttype-service" /> <label for="post-public"><span></span>Post Public</label></div>
                            <div class="radio_inline"><input type="radio" name="optradio" id="post-private" value="2" class="create-posttype-service" /> <label for="post-private"><span></span>Post Private</label></div>
                        </div>
                    </div>

                    <div class="col-md-3 form-control-fld demo-input_buyers padding-right-none" style="display:none">
                    <div class="input-prepend">
                        <input type="hidden" id="demo-input" name="buyer_list_for_sellers" />
                    </div>
                    </div>

                    <div class="clearfix"></div>
                    <div class="check-box form-control-fld margin-none">
                    {!! Form::checkbox('agree','', '',array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
                    </div>
                </div>

                <div class="clearfix"></div>

                <div class="col-md-12 padding-none">
                    {!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => 'add_quote_seller_id_tl','onclick'=>"updatepoststatus(1)"]) !!}
                    {!! Form::submit('Save as Draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_tl','onclick'=>"updatepoststatus(0)"]) !!}
                   
                </div>


            </div>
        </div>
        </div>
        {!! Form::close() !!}

        @include('partials.footer')
        
        
        
        <script>
        $(document).ready(function(){
	        $('#load_type').multiselect({
	            enableClickableOptGroups: true,
	            nonSelectedText: 'Preferred Goods *'
	        });

	        $('#permitstates').multiselect({
        	    enableClickableOptGroups: true,
        	    includeSelectAllOption: true,
        		nonSelectedText: 'Select Permit *'
        	});
	        

	        /*$(".national-permit .multiselect-container > li:last-child input[type='checkbox']").click(function(){	
	        if ($(this).is(":checked")) {
	        	$(".national-permit .multiselect-container > li:not(:last-child)").addClass("active");	
	        	$(".national-permit .multiselect-container > li:not(:last-child)").attr("checked", true);
	        	$(".national-permit .multiselect-container > li:not(:last-child) input[type='checkbox']").attr("disabled", true);
	        	//$(this).trigger("click");
	        }else{
	        	$(".national-permit .multiselect-container > li:not(:last-child) input[type='checkbox']").attr("disabled", false);
	        	
	        	$(".national-permit .multiselect-container > li:not(:last-child) input[type='checkbox']").attr("checked", false);
	        	$(".national-permit .multiselect-container > li").removeClass("active");
		        }
	        });*/

	        	        
        });
        </script>
@endsection