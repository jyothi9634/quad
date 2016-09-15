@extends('app')
@section('content')
@inject('commoncomponent', 'App\Components\CommonComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('/sellerlist') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif


@if(Session::has('transactionId') && Session::get('transactionId')!='')

	{{--*/ $transactionId = Session::get('transactionId') /*--}}
			<script>
			$(document).ready(function(){ 
			$("#erroralertmodal .modal-body").html("Your request for post has been successfully posted to the relevant buyers. Your transacton id is <?php echo $transactionId;?>. You would be getting the enquiries from the buyers online.");
     	   $("#erroralertmodal").modal({
               show: true
           }).one('click','.ok-btn',function (e){
        	   window.location="/sellerlist";
        	 
           });
		 });
			


</script>
				
		
@endif


<!-- This code for display the back to search link ,if user navigate from seller search resuls page starts-->

<!-- This code for display the back to search link ,if user navigate from seller search resuls page ends-->

        <div class="main">

            <div class="container">
                <span class="pull-left"><h1 class="page-title">Post (Truck Lease)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
               
                <span class="pull-right"><a href="{{$backToPostsUrl}}" class="back-link">Back to Posts</a></span>
               

                <div class="clearfix"></div>

				<div class="col-md-12 inner-block-bg single-layout padding-none margin-bottom-none">
                    
                   <div class="col-md-12 inner-block-bg inner-block-bg1 margin-bottom-none border-bottom-none padding-bottom-none">
                    <div class="col-md-12 padding-none inner-form margin-top">
					{!! Form::open(['url' => 'trucklease/addsellerpost','id'=>'trucklease-posts-form-lines']) !!}
						
                            <div class="col-md-3 form-control-fld padding-left-none">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                    {!! Form::text('valid_from', \App\Components\CommonComponent::convertDateDisplay($seller_post_edit->from_date), ['id' => 'datepicker_tl','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Valid From*','disabled' => 'disabled']) !!}
                                    <input type="hidden" name ='need_driver' id='need_driver' value='0'>
                                </div>
                            </div>
                            <div class="col-md-3 form-control-fld">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                    {!! Form::text('valid_to', \App\Components\CommonComponent::convertDateDisplay($seller_post_edit->to_date), ['id' => 'datepicker_to_location_tl','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Valid To*']) !!}
                                </div>
                            </div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									 <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                    {!! Form::text('from_location', '', ['id' => 'from_location','class' => 'form-control clsLocation', 'placeholder' => 'Location*','disabled' => 'disabled']) !!}
                                    {!! Form::hidden('from_location_id', '', array('id' => 'from_location_id')) !!}
                                    {!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}
                                </div>
							</div>
							
                            <div class="clearfix"></div>

                            <div class="col-md-3 form-control-fld  padding-left-none">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-archive"></i></span>
                                    {!! Form::select('LeaseTerms', (['' => 'Select Lease Term*'] + $leasetypemasters), '', ['class' => 'selectpicker leaseterm','id' => 'lease_type','onchange' => 'changeRateClass(this.value)']) !!}
                                </div>
                            </div>
                            
                            <div class="col-md-3 form-control-fld  padding-left-none">
                                <div class="input-prepend">
									{!! Form::text('minimum_lease_period', '', ['class' => 'form-control form-control1 numericvalidation','id' => 'minimum_lease_period','placeholder' => 'Minimum Lease Period','maxlength' => 3]) !!}
									<span class="add-on unit1 manage minunit">Term Unit</span>                                    
                                </div>
                            </div>
                            
                            <div class="col-md-3 form-control-fld  padding-left-none">
                                <div class="input-prepend multi_select">
                                    <span class="add-on"><i class="fa fa-archive"></i></span>
                                    {!! Form::select('LoadTypeMasters', ($loadtypemasters), '', ['class' => 'm_select','id' => 'load_type','multiple' => 'multiple']) !!}
                                </div>
                            </div>

                            <div class="col-md-3 form-control-fld padding-right-none">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="fa fa-truck"></i></span>
                                    {!! Form::select('VehicleTypeMasters', (['' => 'Select Vehicle Type*'] + $vehicletypemasters), '', ['class' => 'selectpicker form-control','id' => 'vechile_type']) !!}
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
									<span class="add-on"><i class="fa fa-tint"></i></span>
									{!! Form::select('SelectStatesPermit', ($allstates), '', ['class' => 'm_select','id' => 'permitstates','multiple' => 'multiple']) !!}
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
                               


                           
                            {!!	Form::hidden('update_lease_seller_line',0,array('class'=>'','id'=>'update_ftl_seller_line'))!!}
							{!!	Form::hidden('update_lease_seller_row_count','',array('class'=>'','id'=>'update_ftl_seller_row_count'))!!}
							{!! Form::hidden('current_row_id', '0', array('id' => 'current_row_id')) !!}
							<div class="col-md-3 form-control-fld padding-left-none">
							<input type="button" id="add_more_update_tl" value="Update" class="btn add-btn" style="display:none;">
					</div>
                            {!! Form::close() !!}
                    </div>
                    
                    </div>
                    
                    
 					<div class="clearfix"></div>
                   {!! Form::open(['url' => 'updateseller/'.$seller_post_edit->id,'id'=>'trucklease-posts-form']) !!}
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
                                        <div class="table-data request_rows" id="">
										@foreach($seller_post_edit_action_lines as $key=>$seller_post_edit_action_line)
										<div class="table-row inner-block-bg request_row_{!! $seller_post_edit_action_line->id !!}" id="single_post_item_{!! $seller_post_edit_action_line->id !!}">
										<div class="col-md-2 padding-left-none from_location_text">{!! $seller_post_edit_action_line->from_locationcity !!}</div>
										<div class="col-md-2 padding-left-none lease_term_text hidden-xs">{!! $seller_post_edit_action_line->lease_term !!}</div>
										<div class="col-md-2 padding-left-none lease_period_text hidden-xs">{!! $seller_post_edit_action_line->minimum_lease_period !!}</div>
										<div class="col-md-2 padding-left-none vehicle_type_text hidden-xs">{!! $seller_post_edit_action_line->vehicle_type !!}</div>
										<div class="col-md-2 padding-left-none goods_text hidden-xs">{!! $commoncomponent->getPreferedGoods($seller_post_edit_action_line->id) !!}</div>
										<div class="col-md-1 padding-none price_text">{!! $seller_post_edit_action_line->price !!}</div>
										<div class="col-md-1 padding-none">
										@if($seller_post_edit_action_line->lkp_post_status_id != 5 )
											<a href='javascript:void(0)' onclick="updatepostlineitem({!! $seller_post_edit_action_line->id !!});" row_id="{!! $seller_post_edit_action_line->id !!}"  style="cursor:pointer;"><i class="fa fa-edit red" title="Edit"></i></a>
											<a row_id="1" data-string="{!! $seller_post_edit_action_line->from_location_id !!}{!! $seller_post_edit_action_line->lkp_vehicle_type_id !!}" class="remove_this_line remove" style="cursor:pointer;"></a>
											@else
											Deleted
											@endif
										</div>

										<input type="hidden" value="{!! $seller_post_edit_action_line->from_location_id !!}" name="from_location[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->lkp_vehicle_type_id !!}" name="vechile_type[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->lkp_trucklease_lease_term_id  !!}" name="lease_term[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->minimum_lease_period  !!}" name="minimum_lease_period[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->vehicle_make_model_year  !!}" name="vehicle_make_model_year[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->fuel_included  !!}" name="fuel_included[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->driver_availability  !!}" name="driver_availability[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->driver_charges  !!}" name="driver_charges[]">
										<input type="hidden" value="{!! $commoncomponent->getPreferedGoodids($seller_post_edit_action_line->id) !!}" name="prefered_goods[]">
										<input type="hidden" value="{!! $commoncomponent->getPermitStates($seller_post_edit_action_line->id) !!}" name="prermit_states[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->lkp_district_id !!}" name="sellerdistrict[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->price !!}" name="price[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->permit_item_id !!}" name="permit_item[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->id !!}" name="post_id[]">
										<input type="hidden" name="update_lease_seller_line" id="update_lease_seller_line" value="">

									
									</div>
								@endforeach	
								</div>
                                    </div>
                                </div>   
                            </div>
                        </div>
                    <div class="clearfix"></div>
                   </div>
                   
                    
                    {!! Form::hidden('service_id', '1', array('id' => 'service_id')) !!}
                   
                    
                    {!! Form::hidden('valid_from_val', $seller_post_edit->from_date, array('id' => 'valid_from_val')) !!}
					{!! Form::hidden('valid_to_val', $seller_post_edit->to_date, array('id' => 'valid_to_val')) !!}
				
                    {!! Form::hidden('labeltext[]', 'Cancellation Charges', array('id' => '')) !!}
                    {!! Form::hidden('labeltext[]', 'Other Charges', array('id' => '')) !!}
                    {!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
                    {!! Form::hidden('subscription_start_date_start', $subscription_start_date_start, array('id' => 'subscription_start_date_start')) !!}
                    {!! Form::hidden('subscription_end_date_end', $subscription_end_date_end, array('id' => 'subscription_end_date_end')) !!}
                    {!! Form::hidden('current_date_seller', $current_date_seller, array('id' => 'current_date_seller')) !!}
                    <input type="hidden" name ='next_terms_count_search' id='next_terms_count_search' value='0'>
        

          <div class="clearfix"></div>
		<div class="col-md-12 inner-block-bg inner-block-bg1">

			<h2 class="filter-head1">Payment Terms</h2>

			<div class="col-md-3 form-control-fld">
				<div class="normal-select">
					{!! Form::select('Payment Terms', ($PaymentTerms), $seller_post_edit->lkp_payment_mode_id, ['class' => 'selectpicker','id' => 'payment_options',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
					@if($seller_post_edit->lkp_post_status_id == 2)
						<input type="hidden" value="{!! $seller_post_edit->lkp_payment_mode_id !!}" name="Payment Terms">
					@endif
				</div>
			</div>

			<div class="col-md-12 form-control-fld" id='show_advanced_period' style='display: <?php echo ($seller_post_edit->lkp_payment_mode_id == 1) ? "block" : "none"; ?>'>
				<div class="check-block">
					<div class="checkbox_inline">
						<span class="erorradio">
							@if($seller_post_edit->lkp_post_status_id == 2)
							<input type="hidden" value="{!! $seller_post_edit->accept_payment_netbanking !!}" name="accept_payment[]">
								@if($seller_post_edit->accept_payment_netbanking == 1)
								{!! Form::checkbox('accept_payment[]', 1,  true, ['class' => 'accept_payment','disabled'=>'disabled']) !!}<span class="lbl padding-8">NEFT/RTGS</span>
								@else
								{!! Form::checkbox('accept_payment[]', 1, false, ['class' => 'accept_payment','disabled'=>'disabled']) !!}<span class="lbl padding-8">NEFT/RTGS</span>
								@endif
							@else
							{!! Form::checkbox('accept_payment[]', 1, $seller_post_edit->accept_payment_netbanking, false, ['class' => 'accept_payment']) !!}<span class="lbl padding-8">NEFT/RTGS</span>
							@endif
						</span>
					</div>

					<div class="checkbox_inline">
						<span class="erorradio"> 
							@if($seller_post_edit->lkp_post_status_id == 2)
							<input type="hidden" value="{!! $seller_post_edit->accept_payment_credit !!}" name="accept_payment[]">
								@if($seller_post_edit->accept_payment_credit == 1)
								{!! Form::checkbox('accept_payment[]', 2,true, ['class' => 'accept_payment','disabled'=>'disabled']) !!}<span class="lbl padding-8">Credit Card</span>
								@else
								{!! Form::checkbox('accept_payment[]', 2, false, ['class' => 'accept_payment','disabled'=>'disabled']) !!}<span class="lbl padding-8">Credit Card</span>
								@endif
							@else
								{!! Form::checkbox('accept_payment[]', 2, $seller_post_edit->accept_payment_credit, false, ['class' => 'accept_payment']) !!}<span class="lbl padding-8">Credit Card</span>
							@endif
						</span>
					</div>

					<div class="checkbox_inline">
						<span class="erorradio">
							@if($seller_post_edit->lkp_post_status_id == 2)
							<input type="hidden" value="{!! $seller_post_edit->accept_payment_debit !!}" name="accept_payment[]">
								@if($seller_post_edit->accept_payment_debit == 1)
								{!! Form::checkbox('accept_payment[]', 3, true, ['class' => 'accept_payment','disabled'=>'disabled']) !!}<span class="lbl padding-8">Debit Card</span>
								@else
								{!! Form::checkbox('accept_payment[]', 3, false, ['class' => 'accept_payment','disabled'=>'disabled']) !!}<span class="lbl padding-8">Debit Card</span>
								@endif
							@else
								{!! Form::checkbox('accept_payment[]', 3, $seller_post_edit->accept_payment_debit, false, ['class' => 'accept_payment']) !!}<span class="lbl padding-8">Debit Card</span>
								@endif
						</span>
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
					{!! Form::text('credit_period', $seller_post_edit->credit_period,['class'=>$creditPeriodClass,'placeholder'=>'',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
					
				</div>
				</div>
				<div class="col-md-5 padding-none">
					<div class="input-prepend">
						<span class="add-on unit-days">
											<div class="normal-select">
												{!! Form::select('credit_period_units',['Days' => 'Days','Weeks' => 'Weeks'], $seller_post_edit->credit_period_units, ['class' => 'selectpicker clsSelPaymentCreditType bs-select-hidden',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
						@if($seller_post_edit->lkp_post_status_id == 2)
						<input type="hidden" value="{!! $seller_post_edit->credit_period_units !!}" name="credit_period_units">
						@endif		
											</div>
										</span>					
					</div>
				</div>
				
				
				
				</div>
				<!-- div class="col-md-2 padding-right-none">
					<div class="normal-select">
						
					</div>	
				</div> -->
				<div class="col-md-12 padding-none">

					<div class="checkbox_inline">
						@if($seller_post_edit->lkp_post_status_id == 2)
						<input type="hidden" value="{!! $seller_post_edit->accept_credit_netbanking !!}" name="accept_credit_netbanking">
							@if($seller_post_edit->accept_credit_netbanking == 1)
							{!! Form::checkbox('accept_credit_netbanking[]', 1, true, ['disabled'=>'disabled']) !!}<span class="lbl padding-8">Net Banking</span>
							@else
							{!! Form::checkbox('accept_credit_netbanking[]', 1, false, ['disabled'=>'disabled']) !!}<span class="lbl padding-8">Net Banking</span>
							@endif
						@else
							{!! Form::checkbox('accept_credit_netbanking[]', 1,$seller_post_edit->accept_credit_netbanking, false) !!}<span class="lbl padding-8">Net Banking</span>
						@endif
					</div>

					<div class="checkbox_inline">
						@if($seller_post_edit->lkp_post_status_id == 2)
						<input type="hidden" value="{!! $seller_post_edit->accept_credit_cheque !!}" name="accept_credit_cheque">
							@if($seller_post_edit->accept_credit_cheque == 1)
							{!! Form::checkbox('accept_credit_netbanking[]', 2, true, ['disabled'=>'disabled']) !!}<span class="lbl padding-8">Cheque / DD</span>
							@else
							{!! Form::checkbox('accept_credit_netbanking[]', 2, false, ['disabled'=>'disabled']) !!}<span class="lbl padding-8">Cheque / DD</span>
							@endif
						@else
							{!! Form::checkbox('accept_credit_netbanking[]', 2,$seller_post_edit->accept_credit_cheque, false) !!}<span class="lbl padding-8">Cheque / DD</span>
						@endif
					</div>		


				</div>
			</div>
</div>
			<div class="clearfix"></div>

<div class="col-md-12 inner-block-bg inner-block-bg1">

			<h2 class="filter-head1">Additional Charges</h2>

			<div class="my-form">
				<div class="form-control-fld terms-and-conditions-block">
					<div class="col-md-3 padding-none tc-block-fld">
						{!! Form::text('terms_condtion_types1',$seller_post_edit->cancellation_charge_price,['class'=>'form-control form-control1 update_txt numberVal fivedigitstwodecimals_deciVal', 'placeholder' => 'Cancellation Charges']) !!}
					</div>
					<div class="col-md-3 tc-block-btn"></div>
				</div>
			</div>	

			<div class="my-form">
				<div class="text-box form-control-fld terms-and-conditions-block">
					<div class="col-md-3 padding-none tc-block-fld">
						{!! Form::text('terms_condtion_types2',$seller_post_edit->docket_charge_price,['class'=>'form-control form-control1 update_txt numberVal fourdigitstwodecimals_deciVal', 'placeholder' => 'Other Charges']) !!}
					</div>
					<div class="col-md-3 tc-block-btn">
							<input type="button" class="add-box btn add-btn" value="Add">
					</div>
				</div>
				@for ($i = 1; $i <= 3; $i++)
					{{--*/ $text =  "other_charge{$i}_text" /*--}}
					{{--*/ $price = "other_charge{$i}_price" /*--}}
					@if(($seller_post_edit->$text != "" || $seller_post_edit->$price != "") && ($seller_post_edit->$text != "" || $seller_post_edit->$price != "0.00"))
						<div class="text-box form-control-fld terms-and-conditions-block" style="">
							<div class="col-md-3 padding-none">
							<div class="input-prepend">
								{!! Form::text("labeltext_$i",$seller_post_edit->$text,['placeholder' => 'Other Charges','class'=>'form-control form-control1 labelcharges dynamic_labelcharges']) !!}
							</div>
							</div>
							<div class="col-md-3">
							<div class="input-prepend">
								{!! Form::text("terms_condtion_types_$i",$seller_post_edit->$price,['placeholder' => '0.00','class'=>'form-control form-control1 pricebox update_txt_test update_txt dynamic_validations numberVal fourdigitstwodecimals_deciVal']) !!}
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
				{!! Form::textarea('terms_conditions', $seller_post_edit->terms_conditions,['class'=>'form-control form-control1 clsTermsConditions','id'=>'terms_conditions', 'rows' => 5,($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
			</div>
			
		</div>

			<div class="clearfix"></div>	
			
			<div class="col-md-12 inner-block-bg inner-block-bg1">
			<div class="col-md-12 form-control-fld">
				@if($seller_post_edit->lkp_post_status_id == 1)
				<div class="radio-block">
					<div class="radio_inline">
					@if($seller_post_edit->lkp_access_id == 1)
						{!! Form::radio('optradio', 1, true, ['id' => 'post-public','class' => 'create-posttype-service']) !!} <label for="post-public"><span></span>Post Public</label>
						@else
						{!! Form::radio('optradio', 1, false, ['id' => 'post-public','class' => 'create-posttype-service']) !!} <label for="post-public"><span></span>Post Public</label>
					@endif
					</div>
					<div class="radio_inline">
					@if($seller_post_edit->lkp_access_id == 2)
						{!! Form::radio('optradio', 2, true, ['id' => 'post-private','class' => 'create-posttype-service']) !!} <label for="post-private"><span></span>Post Private</label>
						@else
						{!! Form::radio('optradio', 2, false, ['id' => 'post-private','class' => 'create-posttype-service']) !!} <label for="post-private"><span></span>Post Private</label>
					@endif
					</div>
				</div>
				@endif

				 @if($seller_post_edit->lkp_post_status_id == 2)
				<div class="radio-block">
					<div class="radio_inline">
					@if($seller_post_edit->lkp_access_id == 1)
						{!! Form::radio('optradio', 1, true, ['id' => 'post-public','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-public"><span></span>Post Public</label>
						@else
						{!! Form::radio('optradio', 1, false, ['id' => 'post-public','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-public"><span></span>Post Public</label>
					@endif
					</div>
					<div class="radio_inline">
					@if($seller_post_edit->lkp_access_id == 2)
						{!! Form::radio('optradio', 2, true, ['id' => 'post-private','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-private"><span></span>Post Private</label>
					@else
						{!! Form::radio('optradio', 2, false, ['id' => 'post-private','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-private"><span></span>Post Private</label>
					@endif
					</div>
				</div>
				@endif

			</div>

            @if($seller_post_edit->lkp_post_status_id == 2)
				<input type="hidden" value="{!! $seller_post_edit->lkp_access_id !!}" name="optradio">
			@endif



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

			<div class="clearfix"></div>
			<div class="check-box form-control-fld">
				<div class="spacing erorradio space-margin terms_chk_error">
					@if($seller_post_edit->lkp_post_status_id == 1)
						{!! Form::checkbox('agree', 1, false,array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
					@else
						{!! Form::checkbox('agree', 1, true,array('id'=>'agree',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
					@endif
					<div class="clearfix"></div>
					<img src="{{asset('/images/loading_buyers.png')}}" style ='display:none;' id = 'img-responsive-buyer' class="img-responsive">
				</div>
			</div>
		
</div>
		<div class="clearfix"></div>

		<div class="col-md-12 padding-none">
			
			{!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => 'add_quote_seller_id_confirm','onclick'=>"updatepoststatus(1)"]) !!} 
			@if($seller_post_edit->lkp_post_status_id == 1)
			{!! Form::submit('Save as draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_tl','onclick'=>"updatepoststatus(0)"]) !!} 
			@endif
		</div>


	</div>
	{!! Form::close() !!}
	</div>
	</div>
</div>
@include('partials.footer')

  <script>
        $(document).ready(function(){
	        $('#load_type').multiselect({
	            enableClickableOptGroups: true,
	            nonSelectedText: 'Select Load Type *'
	        });
	        $('#permitstates').multiselect({
        	    enableClickableOptGroups: true,
        	    includeSelectAllOption: true,
        		nonSelectedText: 'Select Permit *'
        	});
        });
  </script>
@endsection