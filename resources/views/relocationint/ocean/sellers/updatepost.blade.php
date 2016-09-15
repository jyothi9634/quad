@extends('app')
@section('content')
@include('partials.page_top_navigation')
{{--*/ $serviceId = Session::get('service_id') /*--}}
@inject('common', 'App\Components\CommonComponent')

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('/sellerlist') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif

@if(Session::has('transId_updated') && Session::get('transId_updated')!='')
	     	{{--*/ $transactionId = Session::get('transId_updated') /*--}}
			<script>
			$(document).ready(function(){
			$("#erroralertmodal .modal-body").html("Your post has been saved successfully. Your transacton id is <?php echo $transactionId;?>. You would be getting the quotes from the buyers online.");
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

		<span class="pull-left"><h1 class="page-title">Edit Post (RELOCATION INTERNATIONAL)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
		<span class="pull-right"><a href="{{ $backToPostsUrl }}" class="back-link">Back to Posts</a></span>

		<div class="clearfix"></div>


		<div class="col-md-12 inner-block-bg single-layout padding-none">
		
			<div class="col-md-12 inner-block-bg inner-block-bg1 margin-bottom-none border-bottom-none padding-bottom-none">
			<div class="col-md-12 padding-none inner-form">
			{!! Form::open(['url' => 'relocation/updatesellerpost/'.$seller_post_edit->id,'id'=>'posts-form-lines-int-ocean']) !!}	
			<div class="col-md-12 form-control-fld">
										<div class="radio-block">
											<input type="radio" name="main_post_type" disabled id="spot">
											<label for="spot"><span></span>Air</label>
												
											<input type="radio" name="main_post_type" checked="true" id="term"/>
											<label for="term"><span></span>Ocean</label>
										</div>
									</div>
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

					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('from_location', '', ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*', 'disabled' => true]) !!}
							{!! Form::hidden('from_location_id', '', array('id' => 'from_location_id')) !!}
							{!! Form::hidden('seller_district_id', '', array('id' => 'seller_district_id')) !!}							
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('to_location', '', ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*', 'disabled' => true]) !!}
							{!! Form::hidden('to_location_id', '', array('id' => 'to_location_id')) !!}
						</div>
					</div>
					
					<div class="clearfix"></div>
						<div class="col-md-3 form-control-fld">
							<div class="normal-select">
							{{--*/ $getRelocationAllShipmentTypes = $common->getRelocationAllShipmentType() /*--}}
							 {!! Form::select('shipment_types', (['' => 'Shipment Type *'] + $getRelocationAllShipmentTypes), '', ['class' => 'selectpicker form-control','id' => 'shipment_types']) !!}
							
								
							</div>
						</div>
						<div class="col-md-3 form-control-fld">
							<div class="normal-select">
							{{--*/ $getRelocationAllVolumeTypes = $common->getRelocationAllVolumeTypes() /*--}}
							 {!! Form::select('volumetype', (['' => 'Vehicle Type *'] + $getRelocationAllVolumeTypes), '', ['class' => 'selectpicker form-control','id' => 'volumetype']) !!}
							</div>
						</div>
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
								{!! Form::text('Odcharges',null,['class'=>'form-control form-control1 clsRIOSODChargespCBM','id'=>'Odcharges','placeholder'=>'O & D Charges (Rs/CBM)*']) !!}
								</div>
						</div>
						
						<div class="col-md-3 form-control-fld">
							<div class="input-prepend">
							{!! Form::text('freightcharge',null,['class'=>'form-control form-control1 clsRIOSFreightFlat','id'=>'freightcharge','placeholder'=>'Freight (Flat)*']) !!}
								
							</div>
						</div>
					<div class="clearfix"></div>
					
					
					<!--Vehicle Dimensions *<span id ='dimension'></span-->
					<div class="col-md-3 form-control-fld">
					<div class="col-md-7 padding-none">
						<div class="input-prepend">
								<span class="add-on"><i class="fa fa-hourglass-1"></i></span>
								{!! Form::text('oceantransitdays','',['class'=>'form-control clsIDtransitdays clsCOURTransitDays','id'=>'oceantransitdays','placeholder'=>'Transit Days*']) !!}
								
							</div>
					</div>
					<div class="col-md-5 padding-none">
						<div class="input-prepend">
							<span class="add-on unit-days">
											<div class="normal-select">
												{!! Form::select('oceantransitdays_units',['Days' => 'Days','Weeks' => 'Weeks'], '', ['class' => 'selectpicker clsSelTransitDaysType','id'=>'oceantransitdays_units', 'data-serviceId' => $serviceId]) !!}	
											</div>
										</span>
						</div>
					</div>
						
					</div>

					{!! Form::hidden('current_row_id', '0', array('id' => 'current_row_id')) !!}

					<div class="col-md-6 form-control-fld padding-left-none">
						<input type="button" id="add_more_update_inter" value="Update" class="btn add-btn" style="display:none;">
					</div>
					{!! Form::close() !!}
			</div>
			
			</div>


			{!! Form::open(['url' => 'relocation/updatesellerpost/'.$seller_post_edit->id,'id'=>'posts-form-lines_oceanint']) !!}
			<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none padding-bottom-none">
				{!! Form::hidden('int_air_coean', '2', array('id' => 'int_air_coean')) !!}
				{!! Form::hidden('service_id', '1', array('id' => 'service_id')) !!}
				{!! Form::hidden('valid_from_val', $seller_post_edit->from_date, array('id' => 'valid_from_val')) !!}
				{!! Form::hidden('valid_to_val', $seller_post_edit->to_date, array('id' => 'valid_to_val')) !!}
				{!! Form::hidden('from_location_id', $seller_post_edit->from_location_id, array('id' => 'from_location_id_inter')) !!}
				{!! Form::hidden('to_location_id', $seller_post_edit->to_location_id, array('id' => 'to_location_id_inter')) !!}
				
				{!! Form::hidden('from_location_text', $common->getCityName($seller_post_edit->from_location_id), array('id' => 'from_location_id_inter_text')) !!}
				{!! Form::hidden('to_location_id_text', $common->getCityName($seller_post_edit->to_location_id), array('id' => 'to_location_id_inter_text')) !!}
				
				{!! Form::hidden('sellerpoststatus', '', array('id' => 'sellerpoststatus')) !!}
				 {!! Form::hidden('oceansellerpoststatus', '', array('id' => 'oceansellerpoststatus')) !!}
				 
				{!! Form::hidden('subscription_start_date_start', $subscription_start_date_start, array('id' => 'ocen_subscription_start_date_start')) !!}
				{!! Form::hidden('subscription_end_date_end', $subscription_end_date_end, array('id' => 'ocen_subscription_end_date_end')) !!}
				{!! Form::hidden('current_date_seller', $current_date_seller, array('id' => 'ocen_current_date_seller')) !!}
				
				{!! Form::hidden('sellerpoststatus_previous', $seller_post_edit->lkp_post_status_id, array('id' => 'sellerpoststatus_previous')) !!}
				<div class="main-inner"> 
					<div class="main-right">
						<div class="table-div table-style1 padding-none">
							<div class="table-heading inner-block-bg">
								<div class="col-md-3 padding-left-none">Shipment Type</div>
								<div class="col-md-2 padding-left-none">Volume</div>
								<div class="col-md-2 padding-left-none">O & D Charges</div>
								<div class="col-md-2 padding-left-none">Freight</div>
								<div class="col-md-2 padding-left-none">Transit Days</div>
								<div class="col-md-1 padding-left-none"></div>
							</div>

							<input type="hidden" id='next_add_more_id' value='0'>
							<div id ="multi-line-itemes">
								<div class="table-data request_rows" id="">
								@foreach($seller_post_edit_action_lines as $key=>$seller_post_edit_action_line)
									<div class="table-row inner-block-bg request_row_{!! $seller_post_edit_action_line->id !!}" id="single_post_item_{!! $seller_post_edit_action_line->id !!}">
										<div class="col-md-3 padding-left-none shipment_text">{{$common->getAirInternationalShipmentType($seller_post_edit_action_line->lkp_relocation_shipment_type_id)}}</div>
										<div class="col-md-2 padding-left-none volume_text">{{$common->getAirInternationalVolumeType($seller_post_edit_action_line->lkp_relocation_shipment_volume_id)}}</div>
										<div class="col-md-2 padding-left-none od_charges_text">{{$seller_post_edit_action_line->od_charges}}</div>
										<div class="col-md-2 padding-left-none freight_charges_text">{{$seller_post_edit_action_line->freight_charges}}</div>
										<div class="col-md-2 padding-left-none transitdays_text">{{$seller_post_edit_action_line->transitdays}} {{$seller_post_edit_action_line->units}}</div>
										<div class="col-md-1 padding-none">
											<a href='javascript:void(0)' onclick="updatepostlineitem({!! $seller_post_edit_action_line->id !!});" row_id="{!! $seller_post_edit_action_line->id !!}"  style="cursor:pointer;"><i class="fa fa-edit red" title="Edit"></i></a>
											<a row_id="1" data-string="{!! $seller_post_edit->from_location_id !!}{!! $seller_post_edit->to_location_id !!}{!! $seller_post_edit_action_line->lkp_relocation_shipment_type_id !!}{!! $seller_post_edit_action_line->lkp_relocation_shipment_volume_id !!}" class="remove_this_line remove" style="cursor:pointer;"></a>
										</div>

										<input type="hidden" value="{!! $seller_post_edit_action_line->lkp_relocation_shipment_type_id !!}" name="shipment_type[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->lkp_relocation_shipment_volume_id !!}" name="shipment_volume[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->od_charges !!}" name="od_charges[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->transitdays !!}" name="transitdays[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->freight_charges !!}" name="freight_charges[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->units !!}" name="units[]">
										<input type="hidden" value="{!! $seller_post_edit_action_line->id !!}" name="post_id[]">

									
									</div>
								@endforeach	
								</div>
							</div>

						</div>	
					</div>
				</div>
				
				
			</div>
				
				
			<div class="col-md-12 inner-block-bg inner-block-bg1 border-top-none padding-top-none">
				<div class="col-md-3 form-control-fld">
					<div class="input-prepend">
						{!! Form::text('crating_charges',$seller_post_edit->crating_charges,['class'=>'form-control form-control1 clsRIOSCratingChargespCFT', 'id'=>'crating_charges','placeholder' => 'Crating Charges ']) !!}
						<span class="add-on unit1 manage">
							per CFT
						</span>
					</div>
				</div>
			
			<div class="clearfix"></div>
			
			<div class="col-md-3 form-control-fld">
				
			    <div class="radio-block">
			    {!! Form::checkbox('origin_storage', '1', $seller_post_edit->origin_storage,array('id'=>'origin_storage')) !!}
			    <span class="lbl padding-8">Storage</span></div>
			 	<div class="radio-block">
			 	{!! Form::checkbox('origin_handyman_services', '1', $seller_post_edit->origin_handyman_services,array('id'=>'origin_handyman_services')) !!}
			 	<span class="lbl padding-8">Handyman Services</span></div>
			                                    
			</div>
			
			<div class="col-md-3 form-control-fld">
				<div class="radio-block">
				{!! Form::checkbox('destination_storage','1', $seller_post_edit->destination_storage,array('id'=>'destination_storage')) !!}
				<span class="lbl padding-8">Storage</span></div>
			    <div class="radio-block">
			    {!! Form::checkbox('destination_handyman_services', '1', $seller_post_edit->destination_handyman_services,array('id'=>'destination_handyman_services')) !!}
			    <span class="lbl padding-8">Handyman Services</span></div>
			                                    
			</div>
			</div>


		
			<div class="clearfix"></div>

	<div class="col-md-12 inner-block-bg inner-block-bg1">

			<h2 class="filter-head1">Additional Charges</h2>

			<div class="my-form">
				<div class="form-control-fld terms-and-conditions-block">
					<div class="col-md-3 padding-none tc-block-fld">
						{!! Form::text('terms_condtion_types1',$seller_post_edit->cancellation_charge_price,['class'=>'form-control form-control1 update_txt clsRIOSCancelCharges', 'placeholder' => 'Cancellation Charges']) !!}
					</div>
					<div class="col-md-3 tc-block-btn"></div>
				</div>
			</div>	

			<div class="my-form-ocen">
				<div class="text-box form-control-fld terms-and-conditions-block">
					<div class="col-md-3 padding-none tc-block-fld">
						{!! Form::text('terms_condtion_types2',$seller_post_edit->other_charge_price,['class'=>'form-control form-control1 update_txt clsRIOSOtherCharges', 'placeholder' => 'Other Charges']) !!}
					</div>
					<div class="col-md-3 tc-block-btn">
							<input type="button" class="add-box-ocen btn add-btn" value="Add">
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
								{!! Form::text("terms_condtion_types_$i",$seller_post_edit->$price,['placeholder' => '0.00','class'=>'form-control form-control1 pricebox update_txt_test_ocen update_txt dynamic_validations clsRIOSOtherCharges']) !!}
								<span class="add-on unit">Rs</span>
							</div>
							</div>
							@if($seller_post_edit->lkp_post_status_id == 1)
								<a href="#" class="remove-box col-md-2 margin-top-6" data-string="'+num+'"><i class="fa fa-trash red" title="Delete"></i></a></a>
							@endif
						</div>
					@endif
				@endfor
				<input type="hidden" name ='next_terms_count_search_ocen' id='next_terms_count_search_ocen' value='{{$i-1}}'>

			</div>	

			<div class="col-md-6 form-control-fld">
				{!! Form::textarea('terms_conditions', $seller_post_edit->terms_conditions,['class'=>'form-control form-control1','id'=>'terms_conditions', 'rows' => 5,($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
			</div>
			
		</div>
	<div class="clearfix"></div>
		<div class="col-md-12 inner-block-bg inner-block-bg1">
			<div class="col-md-3 form-control-fld">
				<div class="normal-select">
                         {!! Form::select('ocen_tracking',(['' => 'Tracking*']+$trackingtypes), $seller_post_edit->tracking, ['id' => 'ocen_tracking_vechile','class' => 'selectpicker form-control', ($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
					@if($seller_post_edit->lkp_post_status_id == 2)
						<input type="hidden" value="{!! $seller_post_edit->tracking !!}" name="tracking">
					@endif
				</div>
			</div>

			<div class="clearfix"></div>

			<h2 class="filter-head1">Payment Terms</h2>

			<div class="col-md-3 form-control-fld">
				<div class="normal-select">
					{!! Form::select('ocean_paymentterms', ($paymentterms), $seller_post_edit->lkp_payment_mode_id, ['class' => 'selectpicker','id' => 'oceanpayment_options',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
					@if($seller_post_edit->lkp_post_status_id == 2)
						<input type="hidden" value="{!! $seller_post_edit->lkp_payment_mode_id !!}" name="ocean_paymentterms">
					@endif
				</div>
			</div>
			<div class="col-md-12 form-control-fld" id='oceanshow_advanced_period' style='display: <?php echo ($seller_post_edit->lkp_payment_mode_id == 1) ? "block" : "none"; ?>'>
				<div class="check-block">
					<div class="checkbox_inline">
						<span class="erorradio">
							@if($seller_post_edit->lkp_post_status_id == 2)
							<input type="hidden" value="{!! $seller_post_edit->accept_payment_netbanking !!}" name="accept_payment_ocen[]">
								@if($seller_post_edit->accept_payment_netbanking == 1)
								{!! Form::checkbox('accept_payment_ocen[]', 1,  true, ['class' => 'accept_payment_ocen','disabled'=>'disabled']) !!}<span class="lbl padding-8">NEFT/RTGS</span>
								@else
								{!! Form::checkbox('accept_payment_ocen[]', 1, false, ['class' => 'accept_payment_ocen','disabled'=>'disabled']) !!}<span class="lbl padding-8">NEFT/RTGS</span>
								@endif
							@else
							{!! Form::checkbox('accept_payment_ocen[]', 1, $seller_post_edit->accept_payment_netbanking, false, ['class' => 'accept_payment_ocen']) !!}<span class="lbl padding-8">NEFT/RTGS</span>
							@endif
						</span>
					</div>

					<div class="checkbox_inline">
						<span class="erorradio"> 
							@if($seller_post_edit->lkp_post_status_id == 2)
							<input type="hidden" value="{!! $seller_post_edit->accept_payment_credit !!}" name="accept_payment_ocen[]">
								@if($seller_post_edit->accept_payment_credit == 1)
								{!! Form::checkbox('accept_payment_ocen[]', 2,true, ['class' => 'accept_payment_ocen','disabled'=>'disabled']) !!}<span class="lbl padding-8">Credit Card</span>
								@else
								{!! Form::checkbox('accept_payment_ocen[]', 2, false, ['class' => 'accept_payment_ocen','disabled'=>'disabled']) !!}<span class="lbl padding-8">Credit Card</span>
								@endif
							@else
								{!! Form::checkbox('accept_payment_ocen[]', 2, $seller_post_edit->accept_payment_credit, false, ['class' => 'accept_payment_ocen']) !!}<span class="lbl padding-8">Credit Card</span>
							@endif
						</span>
					</div>

					<div class="checkbox_inline">
						<span class="erorradio">
							@if($seller_post_edit->lkp_post_status_id == 2)
							<input type="hidden" value="{!! $seller_post_edit->accept_payment_debit !!}" name="accept_payment_ocen[]">
								@if($seller_post_edit->accept_payment_debit == 1)
								{!! Form::checkbox('accept_payment_ocen[]', 3, true, ['class' => 'accept_payment_ocen','disabled'=>'disabled']) !!}<span class="lbl padding-8">Debit Card</span>
								@else
								{!! Form::checkbox('accept_payment_ocen[]', 3, false, ['class' => 'accept_payment_ocen','disabled'=>'disabled']) !!}<span class="lbl padding-8">Debit Card</span>
								@endif
							@else
								{!! Form::checkbox('accept_payment_ocen[]', 3, $seller_post_edit->accept_payment_debit, false, ['class' => 'accept_payment_ocen']) !!}<span class="lbl padding-8">Debit Card</span>
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
			<div class="col-md-12 form-control-fld" style='display: <?php echo ($seller_post_edit->lkp_payment_mode_id == 4) ? "block" : "none"; ?>' id = 'oceanshow_credit_period'>
				<div class="col-md-3 form-control-fld padding-left-none">
				<div class="col-md-7 padding-none">
					<div class="input-prepend">
					{!! Form::text('credit_period_ocen', $seller_post_edit->credit_period,['class'=>$creditPeriodClass,'placeholder'=>'',($seller_post_edit->lkp_post_status_id == 2) ? 'readonly' : '']) !!}
					
				</div>
				</div>
				<div class="col-md-5 padding-none">
					<div class="input-prepend">
						<span class="add-on unit-days">
											<div class="normal-select">
												{!! Form::select('credit_period_units_ocen',['Days' => 'Days','Weeks' => 'Weeks'], $seller_post_edit->credit_period_units, ['class' => 'selectpicker clsSelPaymentCreditType bs-select-hidden',($seller_post_edit->lkp_post_status_id == 2) ? 'disabled' : '']) !!}
						@if($seller_post_edit->lkp_post_status_id == 2)
						<input type="hidden" value="{!! $seller_post_edit->credit_period_units !!}" name="credit_period_units_ocen">
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
						<input type="hidden" value="{!! $seller_post_edit->accept_credit_netbanking !!}" name="accept_credit_netbanking_ocen">
							@if($seller_post_edit->accept_credit_netbanking == 1)
							{!! Form::checkbox('accept_credit_netbanking_ocen[]', 1, true, ['disabled'=>'disabled']) !!}<span class="lbl padding-8">Net Banking</span>
							@else
							{!! Form::checkbox('accept_credit_netbanking_ocen[]', 1, false, ['disabled'=>'disabled']) !!}<span class="lbl padding-8">Net Banking</span>
							@endif
						@else
							{!! Form::checkbox('accept_credit_netbanking_ocen[]', 1,$seller_post_edit->accept_credit_netbanking, false) !!}<span class="lbl padding-8">Net Banking</span>
						@endif
					</div>

					<div class="checkbox_inline">
						@if($seller_post_edit->lkp_post_status_id == 2)
						<input type="hidden" value="{!! $seller_post_edit->accept_credit_cheque !!}" name="accept_credit_cheque">
							@if($seller_post_edit->accept_credit_cheque == 1)
							{!! Form::checkbox('accept_credit_netbanking_ocen[]', 2, true, ['disabled'=>'disabled']) !!}<span class="lbl padding-8">Cheque / DD</span>
							@else
							{!! Form::checkbox('accept_credit_netbanking_ocen[]', 2, false, ['disabled'=>'disabled']) !!}<span class="lbl padding-8">Cheque / DD</span>
							@endif
						@else
							{!! Form::checkbox('accept_credit_netbanking_ocen[]', 2,$seller_post_edit->accept_credit_cheque, false) !!}<span class="lbl padding-8">Cheque / DD</span>
						@endif
					</div>		


				</div>
			</div>
</div>
			<div class="clearfix"></div>	
			
			<div class="col-md-12 inner-block-bg inner-block-bg1">
			<div class="col-md-12 form-control-fld">
				@if($seller_post_edit->lkp_post_status_id == 1)
				<div class="radio-block">
					<div class="radio_inline">
					@if($seller_post_edit->lkp_access_id == 1)
						{!! Form::radio('optradio_ocen', 1, true, ['id' => 'post-public-ocen','class' => 'create-posttype-service-ocen']) !!} <label for="post-public-ocen"><span></span>Post Public</label>
						@else
						{!! Form::radio('optradio_ocen', 1, false, ['id' => 'post-public-ocen','class' => 'create-posttype-service-ocen']) !!} <label for="post-public-ocen"><span></span>Post Public</label>
					@endif
					</div>
					<div class="radio_inline">
					@if($seller_post_edit->lkp_access_id == 2)
						{!! Form::radio('optradio_ocen', 2, true, ['id' => 'post-private-ocen','class' => 'create-posttype-service-ocen']) !!} <label for="post-private-ocen"><span></span>Post Private</label>
						@else
						{!! Form::radio('optradio_ocen', 2, false, ['id' => 'post-private-ocen','class' => 'create-posttype-service-ocen']) !!} <label for="post-private-ocen"><span></span>Post Private</label>
					@endif
					</div>
				</div>
				@endif

				 @if($seller_post_edit->lkp_post_status_id == 2)
				<div class="radio-block">
					<div class="radio_inline">
					@if($seller_post_edit->lkp_access_id == 1)
						{!! Form::radio('optradio_ocen', 1, true, ['id' => 'post-public','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-public-ocen"><span></span>Post Public</label>
						@else
						{!! Form::radio('optradio_ocen', 1, false, ['id' => 'post-public','class' => 'create-posttype-service', 'disabled' => true]) !!} <label for="post-public-ocen"><span></span>Post Public</label>
					@endif
					</div>
					<div class="radio_inline">
					@if($seller_post_edit->lkp_access_id == 2)
						{!! Form::radio('optradio_ocen', 2, true, ['id' => 'post-private-ocen','class' => 'create-posttype-service-ocen', 'disabled' => true]) !!} <label for="post-private-ocen"><span></span>Post Private</label>
					@else
						{!! Form::radio('optradio_ocen', 2, false, ['id' => 'post-private-ocen','class' => 'create-posttype-service-ocen', 'disabled' => true]) !!} <label for="post-private-ocen"><span></span>Post Private</label>
					@endif
					</div>
				</div>
				@endif

			</div>

            @if($seller_post_edit->lkp_post_status_id == 2)
				<input type="hidden" value="{!! $seller_post_edit->lkp_access_id !!}" name="optradio_ocen">
			@endif



			<div class="col-md-3 form-control-fld">
				<div class="demo-input_buyers_ocen" style='display:<?php echo ($private == true) ? "block" : "none"; ?>'>
					<?php 
					$selected  = "";
					foreach($selectedbuyers as $selectedbuyer){ 
						$selected .= ",".$selectedbuyer->buyer_id;
					} ?>
					<input type="hidden" id="demo_input_select_hidden" name="buyer_list_for_sellers_ocen" value="<?php echo $selected; ?>" />
					<select id="demo_input_select" class="tokenize-sample" name="buyer_list_for_sellers_ocens" multiple="multiple">
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
			
			{!! Form::submit('Confirm', ['class' => 'btn theme-btn flat-btn pull-right','name' => 'confirm','id' => 'add_quote_seller_id_confirm_int','onclick'=>"oceanupdatepoststatus(1)"]) !!} 
			@if($seller_post_edit->lkp_post_status_id == 1)
			{!! Form::submit('Save as draft', ['class' => 'btn add-btn flat-btn pull-right','name' => 'draft','id' => 'add_quote_seller_intrelocation','onclick'=>"oceanupdatepoststatus(0)"]) !!} 
			@endif
		</div>


	</div>
	{!! Form::close() !!}
	</div>
	</div>
</div>
@include('partials.footer')
@endsection
