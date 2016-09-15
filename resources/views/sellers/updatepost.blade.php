@extends('app')
@section('content')
@include('partials.page_top_navigation')
{{--*/ $serviceId = Session::get('service_id') /*--}}

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
<div class="main">
	<div class="container">
		@if(Session::has('message_update_post') && Session::get('message_update_post')!='')
			<div class="alert alert-info">
				{{Session::get('message_update_post')}}
			</div>
		@endif

		<span class="pull-left"><h1 class="page-title">Edit Post (FTL)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
		<span class="pull-right"><a href="{{$backToPostsUrl}}" class="back-link">Back to Posts</a></span>

		<div class="clearfix"></div>


		<div class="col-md-12 inner-block-bg single-layout padding-none">
			<div class="col-md-12 inner-block-bg inner-block-bg1 margin-bottom-none border-bottom-none padding-bottom-none">
			<div class="col-md-12 padding-none inner-form">
			{!! Form::open(['url' => 'updateseller/'.$seller_post_edit->id,'id'=>'posts-form-lines']) !!}	
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('valid_from', \App\Components\CommonComponent::convertDateDisplay($seller_post_edit->from_date), ['id' => 'datepicker','class' => 'calendar form-control from-date-control', 'placeholder' => 'Valid From*','disabled' => true]) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('valid_to',\App\Components\CommonComponent::convertDateDisplay($seller_post_edit->to_date), ['id' => 'datepicker_to_location','class' => 'calendar form-control to-date-control','readonly'=>true, 'placeholder' => 'Valid To*']) !!}
						</div>
					</div>

					<div class="clearfix"></div>

					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('from_location', '', ['id' => 'from_location','class' => 'form-control clsFTLFromLocation', 'placeholder' => 'From Location*', 'disabled' => true]) !!}
							{!! Form::hidden('from_location_id', '', array('id' => 'from_location_id')) !!}
							{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}							
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('to_location', '', ['id' => 'to_location','class' => 'form-control clsFTLtoLocation', 'placeholder' => 'To Location*', 'disabled' => true]) !!}
							{!! Form::hidden('to_location_id', '', array('id' => 'to_location_id')) !!}
						</div>
					</div>
					
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-archive"></i></span>
							{!! Form::select('LoadTypeMasters', (['11'=>'Load Type (Any)'] + $loadtypemasters ), null, ['class' => 'selectpicker form-control','id' => 'load_type']) !!}
						</div>
					</div>

					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-truck"></i></span>
							{!! Form::select('VehicleTypeMasters', (['' => 'Select Vehicle Type*'] + $vehicletypemasters), null, ['class' => 'selectpicker form-control','id' => 'vechile_type']) !!}
						</div>
					</div>
					
					<div class="clearfix"></div>
					
					<!--Vehicle Dimensions *<span id ='dimension'></span-->
					<div class="col-md-3 form-control-fld">
					<div class="col-md-7 padding-none">
						<div class="input-prepend">
								<span class="add-on"><i class="fa fa-hourglass-1"></i></span>
								{!! Form::text('transitdays','',['class'=>'form-control clsIDtransitdays clsCOURTransitDays','id'=>'transitdays','placeholder'=>'Transit Days*']) !!}
								
							</div>
					</div>
					<div class="col-md-5 padding-none">
						<div class="input-prepend">
							<span class="add-on unit-days">
											<div class="normal-select">
												{!! Form::select('units',['Days' => 'Days','Weeks' => 'Weeks'], '', ['class' => 'selectpicker clsSelTransitDaysType','id'=>'transitdays_units', 'data-serviceId' => $serviceId]) !!}	
											</div>
										</span>
						</div>
					</div>
						
					</div>
					


					<div class="col-md-3 padding-none">
						<!--Capacity : <span id ='capacity'></span><span id ='units'></span-->
						<div class="col-md-12 form-control-fld padding-left-none">
							<div class="input-prepend">
							<span class="add-on"><i class="fa fa-rupee "></i></span>
							{!! Form::text('price','',['class'=>'form-control clsFTLRate','id'=>'price','placeholder'=>'Rate*']) !!}
							{!! Form::hidden('current_row_id', '0', array('id' => 'current_row_id')) !!}
						</div>
						</div>
						
					</div>

					<div class="col-md-6 form-control-fld padding-left-none">
						<input type="button" id="add_more_update" value="Update" class="btn add-btn" style="display:none;">
					</div>
					{!! Form::close() !!}
			</div>
			
			</div>


			{!! Form::open(['url' => 'updateseller/'.$seller_post_edit->id,'id'=>'posts-form']) !!}
			<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none padding-bottom-none">

				{!! Form::hidden('service_id', '1', array('id' => 'service_id')) !!}
				{!! Form::hidden('valid_from_val', $seller_post_edit->from_date, array('id' => 'valid_from_val')) !!}
				{!! Form::hidden('valid_to_val', $seller_post_edit->to_date, array('id' => 'valid_to_val')) !!}
				{!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
				{!! Form::hidden('subscription_start_date_start', $subscription_start_date_start, array('id' => 'subscription_start_date_start')) !!}
				{!! Form::hidden('subscription_end_date_end', $subscription_end_date_end, array('id' => 'subscription_end_date_end')) !!}
				{!! Form::hidden('current_date_seller', $current_date_seller, array('id' => 'current_date_seller')) !!}

				<div class="main-inner"> 
					<div class="main-right">
						<div class="table-div table-style1 padding-none">
							<div class="table-heading inner-block-bg">
								<div class="col-md-2 padding-left-none">From</div>
								<div class="col-md-2 padding-left-none">To</div>
								<div class="col-md-3 padding-left-none">Load Type</div>
								<div class="col-md-2 padding-left-none">Vehicle Type</div>
								<div class="col-md-2 padding-left-none">Price</div>
								<div class="col-md-1 padding-left-none"></div>
							</div>

							<input type="hidden" id='next_add_more_id' value='0'>
							<div id ="multi-line-itemes">
								<div class="table-data request_rows" id="">
								@foreach($seller_post_edit_action_lines as $key=>$seller_post_edit_action_line)
									<div class="table-row inner-block-bg request_row_{!! $seller_post_edit_action_line->id !!}" id="single_post_item_{!! $seller_post_edit_action_line->id !!}">
										<div class="col-md-2 padding-left-none from_location_text">{!! $seller_post_edit_action_line->from_locationcity !!}</div>
										<div class="col-md-2 padding-left-none to_location_text">{!! $seller_post_edit_action_line->to_locationcity !!}</div>
										<div class="col-md-3 padding-left-none load_type_text hidden-xs">{!! $seller_post_edit_action_line->load_type !!}</div>
										<div class="col-md-2 padding-left-none vehicle_type_text hidden-xs">{!! $seller_post_edit_action_line->vehicle_type !!}</div>
										<div class="col-md-2 padding-none price_text">{!! $seller_post_edit_action_line->price !!}</div>
										<div class="col-md-1 padding-none">
										@if($seller_post_edit_action_line->lkp_post_status_id != 5 )
											<a href='javascript:void(0)' onclick="updatepostlineitem({!! $seller_post_edit_action_line->id !!});" row_id="{!! $seller_post_edit_action_line->id !!}"  style="cursor:pointer;"><i class="fa fa-edit red" title="Edit"></i></a>
											<a row_id="1" data-string="{!! $seller_post_edit_action_line->from_location_id !!}{!! $seller_post_edit_action_line->to_location_id !!}{!! $seller_post_edit_action_line->lkp_load_type_id !!}{!! $seller_post_edit_action_line->lkp_vehicle_type_id !!}" class="remove_this_line remove" style="cursor:pointer;"></a>
											@else
											Deleted
											@endif
										</div>

										<input type="hidden" value="{!! $seller_post_edit_action_line->from_location_id !!}" name="from_location[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->to_location_id !!}" name="to_location[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->lkp_load_type_id !!}" name="load_type[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->lkp_vehicle_type_id !!}" name="vechile_type[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->transitdays !!}" name="transitdays[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->units !!}" name="units[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->lkp_district_id !!}" name="sellerdistrict[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->price !!}" name="price[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->id !!}" name="post_id[]">

									
									</div>
								@endforeach	
								</div>
							</div>

						</div>	
					</div>
				</div>
			</div>





			<div class="clearfix"></div>
		<div class="col-md-12 inner-block-bg inner-block-bg1">
			<div class="col-md-3 form-control-fld">
				<div class="normal-select">
                         {!! Form::select('tracking',(['' => 'Tracking*']+$trackingtypes), $seller_post_edit->tracking, ['id' => 'tracking_vechile','class' => 'selectpicker form-control', ($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
                         @if($seller_post_edit->lkp_post_status_id == 2)
						<input type="hidden" value="{!! $seller_post_edit->tracking !!}" name="tracking">
					@endif
                         
				</div>
			</div>

			<div class="clearfix"></div>

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
						{!! Form::text('terms_condtion_types1',$seller_post_edit->cancellation_charge_price,['class'=>'form-control form-control1 update_txt numberVal clsFTLCancelCharges', 'placeholder' => 'Cancellation Charges']) !!}
					</div>
					<div class="col-md-3 tc-block-btn"></div>
				</div>
			</div>	

			<div class="my-form">
				<div class="text-box form-control-fld terms-and-conditions-block">
					<div class="col-md-3 padding-none tc-block-fld">
						{!! Form::text('terms_condtion_types2',$seller_post_edit->docket_charge_price,['class'=>'form-control form-control1 update_txt clsGMSOtherCharges', 'placeholder' => 'Other Charges']) !!}
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
								{!! Form::text("terms_condtion_types_$i",$seller_post_edit->$price,['placeholder' => '0.00','class'=>'form-control form-control1 pricebox update_txt_test update_txt dynamic_validations numberVal']) !!}
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
				{!! Form::textarea('terms_conditions', $seller_post_edit->terms_conditions,['class'=>'form-control form-control1 clsTermsConditions','id'=>'terms_conditions', 'rows' => 5,($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '', 'placeholder' => 'Notes to Terms &amp; Conditions (Optional)']) !!}
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
			{!! Form::submit('Save as draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller','onclick'=>"updatepoststatus(0)"]) !!} 
			@endif
		</div>


	</div>
	{!! Form::close() !!}
	</div>
	</div>
</div>
@include('partials.footer')
@endsection
