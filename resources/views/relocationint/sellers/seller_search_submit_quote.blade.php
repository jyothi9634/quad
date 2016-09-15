@inject('commoncomponent', 'App\Components\CommonComponent')
@inject('sellercomponent', 'App\Components\Relocation\RelocationSellerComponent')
{{--*/ $chargestype = ($international_type == 1) ? 'Storage Charges' : 'Cancellation Charges'; /*--}}
@if(!isset($lkp_international_type_id))
	{{--*/ $lkp_international_type_id = $international_type /*--}}
@endif
	
	
@if(count($submittedquote))
    {{--*/ $submittedquote = $submittedquote[0] /*--}}
   
    <div class="col-md-12 form-control-fld padding-left-none margin-top">
        <b>Seller Quote</b>
    </div>
    <div class="col-md-12 padding-none">
        <div class="clearfix"></div>

        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">O & D Charges (Flat) </span> <span class="data-value">{{$submittedquote->od_charges}} /-</span>
        </div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Freight (Flat) </span> <span class="data-value">{{$submittedquote->freight_flat}} /-</span>
        </div>
         <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Transit Days </span> <span class="data-value">{{$submittedquote->transit_days}} @if($submittedquote->transit_units==1) Days @else Weeks @endif </span>
        </div>
       <div class="clearfix"></div>
       <div class='col-md-3 padding-left-none'>
            <span class='data-head'><u>Additional Charges</u></span>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">{{$chargestype}} </span> <span class="data-value">{{$submittedquote->storage_charges}} </span>
        </div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Other Charges </span> <span class="data-value">{{$submittedquote->other_charges}} </span>
        </div>
        
	</div>

@else
    <form  id="reloc_submit_quote_{{$id}}" name="reloc_submit_quote_{{$id}}" class="relocation_submit_quote" method="get">
        @if(isset($is_search))
            <input type="hidden" id="from_location_id_{{$id}}"  name="from_location_id_{{$id}}" value="{{$search_params['from_location_id']}}"/>
            <input type="hidden" id="to_location_id_{{$id}}"  name="to_location_id_{{$id}}" value="{{$search_params['to_location_id']}}"/>
            <input type="hidden" id="valid_from_{{$id}}" name="valid_from_{{$id}}" value="{{$search_params['valid_from']}}"/>
            <input type="hidden" id="valid_to_{{$id}}" name="valid_to_{{$id}}" value="{{$search_params['valid_to']}}"/>
            <input type="hidden" id="international_type_{{$id}}" name="international_type_{{$id}}" value="{{$international_type}}"/>
            
        @endif
        <input type="hidden" name="buyerquoteid_{{$id}}" value="{{$id}}" />
        <input type='hidden' name='buyer_id' id='buyer_id_{{$id}}' value="{!! $enquiry->created_by !!}">
        <input type="hidden" name="total_cft_{{$id}}" id="total_cft_{{$id}}"  value="{!! $commoncomponent->getOfficeBuyerVolume($id) !!}"/>
        <input type="hidden" name="total_price_{{$id}}" id="total_price_{{$id}}"  value=""/>
        <input type="hidden" id="from_loc_{{$id}}"  name="from_loc_{{$id}}" value="{!! $enquiry->from_location_id !!}"/>
        <input type="hidden" id="to_loc_{{$id}}"  name="to_loc_{{$id}}" value="{{$enquiry->to_location_id}}"/> 
        <input type="hidden" id="international_type_{{$id}}" name="international_type_{{$id}}" value="{{$international_type}}"/>

        {{-- Air means 1, Ocean 2 --}}
		@if($lkp_international_type_id == 1)
			<div class='col-md-3 padding-left-none'>
	        	<input type='text' class='form-control clsRIASODChargesFlat form-control1' placeholder='O & D Charges (Flat)' id='od_charges_{{$id}}' name='od_charges_{{$id}}' />
	        </div>

	        <div class='col-md-3 padding-left-none'>
	        	<input type='text' class='form-control clsRIASFreightChargesFlat form-control1' placeholder='Freight Charges(Flat)' id='transport_charges_{{$id}}' name='transport_charges_{{$id}}' />
	        </div>
        @elseif($lkp_international_type_id == 2)
        	<div class='col-md-3 padding-left-none'>
	        	<input type='text' class='form-control clsRIOSODChargesFlat form-control1' placeholder='O & D Charges (Flat)' id='od_charges_{{$id}}' name='od_charges_{{$id}}' />
	        </div>

	        <div class='col-md-3 padding-left-none'>
	        	<input type='text' class='form-control clsRIOSFreightFlat form-control1' placeholder='Freight Charges(Flat)' id='transport_charges_{{$id}}' name='transport_charges_{{$id}}' />
	        </div>
        @endif

       <div class='col-md-3 padding-left-none'>
	       <div class="col-md-8 padding-none">
			
			{{-- Air means 1, Ocean 2 --}}
			@if($lkp_international_type_id == 1)
				<div class="input-prepend">
		        	<input type='text' class='form-control clsRIASTrasitDays form-control1' placeholder='Transit Days' id='transport_days_{{$id}}' name='transport_days_{{$id}}' />
		        </div>
		    @elseif($lkp_international_type_id == 2)
		    	<div class="input-prepend">
		        	<input type='text' class='form-control clsRIOSTransitDays form-control1' placeholder='Transit Days' id='transport_days_{{$id}}' name='transport_days_{{$id}}' />
		        </div>
		    @endif
	    	</div>
        <div class="col-md-4 padding-none">
		<div class="input-prepend">
		<span class="add-on unit-days">
		<div class="normal-select">
		<select class="selectpicker" id='transport_units_{{$id}}' name='transport_units_{{$id}}'>
		<option value="1">Days</option>
		<option value="2">Weeks</option>
		</select>
		</div>
		</span>
		</div></div>
        </div>
		<div class="clearfix"></div>
        
        <div class='col-md-3 padding-left-none'>
            <span class='data-head'><u>Additional Charges</u></span>
        </div>
        <div class="clearfix"></div>
        
        <div class="col-md-3 padding-left-none">
		{{-- Air means 1, Ocean 2 --}}
		@if($lkp_international_type_id == 1)
			<input type="text" id="storage_charges_{{$id}}" name="storage_charges_{{$id}}" class="form-control form-control1 novalidation clsRIASStorageCharges" placeholder="{{$chargestype}}" />
		@elseif($lkp_international_type_id == 2)
			<input type="text" id="storage_charges_{{$id}}" name="storage_charges_{{$id}}" class="form-control form-control1 novalidation clsRIOSStorageCharges" placeholder="{{$chargestype}}" />
		@endif
		</div>
		
		<div class="col-md-3 padding-left-none">
		{{-- Air means 1, Ocean 2 --}}
		@if($lkp_international_type_id == 1)
			<input type="text" id="other_charges_{{$id}}" name="other_charges_{{$id}}" class="form-control form-control1 novalidation clsRIASOtherCharges" placeholder="Other Charges" />
		@elseif($lkp_international_type_id == 2)
			<input type="text" id="other_charges_{{$id}}" name="other_charges_{{$id}}" class="form-control form-control1 novalidation clsRIOSOtherCharges" placeholder="Other Charges" />
		@endif
		</div>

        <div class='clearfix'></div>
        <div class='col-md-3 padding-left-none padding-top-8'>
            <span class='data-head'>Total Charges : <span id="total_price_display_{{$id}}">--</span></span>
        </div>
        
         @if(isset($is_search))
           
           
				
			<div class="clearfix"></div>
			<div class="col-md-3 padding-left-none track-margin">
				<div class="normal-select">
					<select class="selectpicker"  id="tracking_{{$id}}" name="tracking">
						<option value="">Tracking</option>
						<option value="1">Milestone</option>
					</select>
				</div>
			</div>
				
			
			<div class="clearfix"></div>
            <div class="col-md-12 padding-none">
			<h2 class="filter-head1">Payment Terms</h2>
			<div class="col-md-3 padding-left-none track-margin margin-bottom">
				<div class="normal-select">
					<select class="selectpicker ptl_payment payment_options_{{$id}}" id="payment_options" name="paymentterms_{{$id}}">
						<option value="1">Advance</option>
						<option value="2">Cash on Delivery</option>
						<option value="3">Cash on Pickup</option>
						<option value="4">Credit</option>
					</select>
				</div>
			</div>
            </div>
	
			<div class="col-md-12 padding-none" id ="show_advanced_period">
				<div class="checkbox_inline">
					<input class="accept_payment_ptl" type="checkbox" name="accept_payment_ptl[]" id="accept_payment_ptl[]" value="1"><span class="lbl padding-8">NEFT/RTGS</span>
				</div>
				<div class="checkbox_inline">
					<input class="accept_payment_ptl" type="checkbox" name="accept_payment_ptl[]" value="2"><span class="lbl padding-8">Credit Card</span>
				</div>
				<div class="checkbox_inline">
					<input class="accept_payment_ptl" type="checkbox" name="accept_payment_ptl[]" value="3"><span class="lbl padding-8">Debit Card</span>
				</div>
			</div>
	
	
			<div class="col-md-12 form-control-fld padding-left-none" style ="display: none;" id = "show_credit_period">
				<div class="col-md-3 form-control-fld padding-left-none">

				<div class="col-md-7 padding-none">
					<div class="input-prepend">
						<input class="form-control form-control1 numberVal credit_period_ptl_{{$id}}" type="text" name="credit_period_ptl_{{$id}}" id="credit_period_ptl_{{$id}}" value="" placeholder="Credit Period"><span class="lbl padding-8">Credit Card</span>
					</div>
				</div>
				<div class="col-md-5 padding-none">
					<div class="input-prepend">
						<span class="add-on unit-days manage">
							<div class="normal-select">
								<select class="selectpicker bs-select-hidden credit_period_units_{{$id}}"  id="credit_period_units" name="credit_period_units_{{$id}}">
									<option value="Days">Days</option>
									<option value="Weeks">Weeks</option>
								</select>
		
							</div>
						</span>
					</div>
				</div>
				</div>
				<div class="col-md-12 padding-none">
					<div class="checkbox_inline" >
					<input class="accept_payment_ptl" type="checkbox" name="accept_credit_netbanking[]" value="1"><span class="lbl padding-8">Net Banking</span>
					
					</div>
					<div class="checkbox_inline">
					<input class="accept_payment_ptl" type="checkbox" name="accept_credit_netbanking[]" value="2"><span class="lbl padding-8">Cheque / DD</span>
					</div>

				</div>
			</div>
				
        @endif
        
        <div class='col-md-12 padding-none'>
            <button type="button" class='btn pull-right btn add-btn relocation_quote_submit' name='submitform_quote_{{$id}}' id='submitform_quote_{{$id}}'>Submit</button>
        </div>
    </form>
@endif