@inject('commoncomponent', 'App\Components\CommonComponent')
@inject('sellercomponent', 'App\Components\RelocationPet\RelocationPetSellerComponent')

@if(count($submittedquote))
    {{--*/ $submittedquote = $submittedquote[0] /*--}}
   @if($submittedquote->seller_post_id!=0)
    <div class="col-md-12 form-control-fld padding-left-none margin-top">
        <b>Seller Quote</b>
    </div>
    <div class="col-md-12 padding-none">
        <div class="clearfix"></div>

        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">O & D Charges (Flat Charge) </span> <span class="data-value">{{$submittedquote->doortodoor_charges}} /-</span>
        </div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Freight (Rate per KG)</span> <span class="data-value">{{$submittedquote->rate_per_cft}} /-</span>
        </div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Transit Days </span> <span class="data-value">{{$submittedquote->transit_days}} Days</span>
        </div>
        
    </div>
    @if(Session::get('service_id') == RELOCATION_PET_MOVE )
     <div class='col-md-3 padding-left-none padding-top-8'>
            <span class='data-head'>Total Charges : <span id="total_price_display_{{$id}}">{{$submittedquote->total_price}} /-</span></span>
     </div>
    @endif
   @endif
@else
    <form  id="reloc_submit_quote_{{$id}}" name="reloc_submit_quote_{{$id}}" class="relocation_submit_quote" method="get">
    <input type="hidden" id="serviceid" value={{ Session::get('service_id') }}>
    @if(Session::get('service_id') == RELOCATION_PET_MOVE)
    <input type="hidden" id="cageweight_{{$id}}" value={{ $cageweight }}>
    @endif
    
        @if(isset($is_search))
            <input type="hidden" id="from_location_id_{{$id}}"  name="from_location_id_{{$id}}" value="{{$search_params['from_location_id']}}"/>
            <input type="hidden" id="to_location_id_{{$id}}" name="to_location_id_{{$id}}" value="{{$search_params['to_location_id']}}"/>
            <input type="hidden" id="valid_from_{{$id}}" name="valid_from_{{$id}}" value="{{$search_params['valid_from']}}"/>
            <input type="hidden" id="valid_to_{{$id}}" name="valid_to_{{$id}}" value="{{$search_params['valid_to']}}"/>
            <input type="hidden" id="pet_type_{{$id}}" name="pet_type_{{$id}}" value="{{$enquiry->lkp_pet_type_id}}"/>
            <input type="hidden" id="cage_type_{{$id}}" name="cage_type_{{$id}}" value="{{$enquiry->lkp_cage_type_id}}"/>
            
        @endif
        <input type="hidden" id="pet_type_{{$id}}" name="pet_type_{{$id}}" value="{{$enquiry->lkp_pet_type_id}}"/>
        <input type="hidden" id="cage_type_{{$id}}" name="cage_type_{{$id}}" value="{{$enquiry->lkp_cage_type_id}}"/>
        <input type="hidden" name="buyerquoteid_{{$id}}" value="{{$id}}" />
        <input type='hidden' name='buyer_id' id='buyer_id_{{$id}}' value="{!! $enquiry->buyer_id !!}">
        <input type="hidden" name="total_cft_{{$id}}" id="total_cft_{{$id}}"  value="{!! $commoncomponent->getVolumeCft($id) !!}"/>
        <input type="hidden" name="total_price_{{$id}}" id="total_price_{{$id}}"  value=""/>

        <div class='col-md-3 padding-left-none'>
            {{--*/ $chargestype = 'O & D Charges (Flat Charge)'; /*--}}
            <input type='text' class='form-control form-control1 clsRPetODChargesFlat' placeholder='{{$chargestype}}' id='od_charges_{{$id}}' name='od_charges_{{$id}}'/>
        </div>
        <div class='col-md-3 padding-left-none'>
            <input type='text' class='form-control form-control1 clsRPetFreightFlat' placeholder='Freight (Rate per KG)' id='transport_charges_{{$id}}' name='transport_charges_{{$id}}'/>
        </div>
        <div class='col-md-3 padding-left-none'>
            <input type='text' class='form-control form-control1 clsRPetTransitDays' placeholder='Transit Days' id='transport_days_{{$id}}' name='transport_days_{{$id}}' />
        </div>
        <div class='clearfix'></div>
       
        <div class='col-md-3 padding-left-none padding-top-8'>
            <span class='data-head'>Total Charges : <span id="total_price_display_{{$id}}">--</span></span>
        </div>
        
         @if(isset($is_search))
           
           
				
			<div class="clearfix"></div>
			<div class="col-md-3 padding-left-none track-margin">
				<div class="normal-select">
                          {{--*/ $trackingOptionsHtml = \App\Components\CommonComponent::getTrackingTypeOptionsHtml() /*--}}
					<select class="selectpicker"  id="tracking_{{$id}}" name="tracking">
						<option value="">Tracking</option>
						{!! $trackingOptionsHtml !!}
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
            <button type="button" class='btn pull-right btn add-btn relocationpet_quote_submit' name='submitform_quote_{{$id}}' id='submitform_quote_{{$id}}'>Submit</button>
        </div>
    </form>
@endif