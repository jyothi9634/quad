@inject('commoncomponent', 'App\Components\CommonComponent')
@inject('sellercomponent', 'App\Components\Relocation\RelocationSellerComponent')

@if(count($submittedquote))
    {{--*/ $submittedquote = $submittedquote[0] /*--}}
   
    <div class="col-md-12 form-control-fld padding-left-none margin-top">
        <b>Seller Quote</b>
    </div>
    <div class="col-md-12 padding-none">
        <div class="clearfix"></div>

        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">O & D Charges (per CFT) </span> <span class="data-value">{{$submittedquote->rate_per_cft}} /-</span>
        </div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Transport Charges </span> <span class="data-value">{{$submittedquote->transport_charges}} /-</span>
        </div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Transit Days </span> <span class="data-value">{{$submittedquote->transit_days}}</span>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Crating Charges (per CFT)</span> <span class="data-value">Rs {{$submittedquote->creating_charges}} /-</span>
        </div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Storage Charges (CFT/Day)</span> <span class="data-value">Rs {{$submittedquote->storage_charges}} /-</span>
        </div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Escort Charges (per Day)</span> <span class="data-value">Rs {{$submittedquote->escort_charges}} /-</span>
        </div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Handyman Charges (per Hour)</span> <span class="data-value">Rs {{$submittedquote->handyman_charges}} /-</span>
        </div>
        <div class="clearfix"></div>    
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Property Search (Rs)</span> <span class="data-value">Rs {{$submittedquote->property_search}} /-</span>
        </div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Brokerage (Rs)</span> <span class="data-value">Rs {{$submittedquote->brokerage_charges}} /-</span>
        </div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Total (Rs)</span> <span class="data-value">Rs {{$submittedquote->total_price}} /-</span>
        </div>


    </div>

@else
    <form  id="reloc_submit_quote_{{$id}}" name="reloc_submit_quote_{{$id}}" class="relocation_submit_quote" method="get">
        @if(isset($is_search))
            <input type="hidden" id="from_location_id_{{$id}}"  name="from_location_id_{{$id}}" value="{{$search_params['from_location_id']}}"/>
            <input type="hidden" id="to_location_id_{{$id}}" name="to_location_id_{{$id}}" value="{{$search_params['to_location_id']}}"/>
            <input type="hidden" id="valid_from_{{$id}}" name="valid_from_{{$id}}" value="{{$search_params['valid_from']}}"/>
            <input type="hidden" id="valid_to_{{$id}}" name="valid_to_{{$id}}" value="{{$search_params['valid_to']}}"/>
            <input type="hidden" id="vehicle_type_{{$id}}" name="vehicle_type_{{$id}}" value="{{$enquiry->lkp_vehicle_category_id}}"/>
            <input type="hidden" id="car_size_{{$id}}" name="car_size_{{$id}}" value="{{$enquiry->lkp_vehicle_category_type_id}}"/>
			<input type="hidden" id="property_type_{{$id}}" name="property_type_{{$id}}" value="{{$enquiry->property_type}}"/>
			<input type="hidden" id="load_category_{{$id}}" name="load_category_{{$id}}" value="{{$enquiry->load_category_id}}"/>
        @endif
        <input type="hidden" name="buyerquoteid_{{$id}}" value="{{$id}}" />
        <input type='hidden' name='buyer_id' id='buyer_id_{{$id}}' value="{!! $enquiry->created_by !!}">
        <input type="hidden" name="crating_cft_{{$id}}" id="crating_cft_{{$id}}"  value="{!! $commoncomponent->getCratingVolumeCft($id) !!}"/>
        <input type="hidden" name="total_cft_{{$id}}" id="total_cft_{{$id}}"  value="{!! $commoncomponent->getVolumeCft($id) !!}"/>
        <input type="hidden" name="total_price_{{$id}}" id="total_price_{{$id}}"  value=""/>
        <input type="hidden" name="post_rate_card_type_{{$id}}" id="post_rate_card_type_{{$id}}"  value="{{$ratecard_type}}"/>

        <div class='col-md-3 padding-left-none'>
            {{--*/ $chargestype = ($ratecard_type == 1) ? 'O & D Charges (per CFT)' : 'Cost'; /*--}}
            <input type='text' class='form-control form-control1 clsRDSODChargespCFT' placeholder='{{$chargestype}}' id='od_charges_{{$id}}' name='od_charges_{{$id}}'/>
        </div>

        <div class='col-md-3 padding-left-none'>
            <input type='text' class='form-control form-control1 sixdigitstwodecimals_deciVal numberVal' placeholder='Transport Charges' id='transport_charges_{{$id}}' name='transport_charges_{{$id}}'/>
        </div>
        
        <div class='col-md-3 padding-left-none'>
            <input type='text' class='form-control form-control1 clsRDVTransitDays' placeholder='Transit Days' id='transport_days_{{$id}}' name='transport_days_{{$id}}' />
        </div>
        <div class='clearfix'></div>
        <div class='col-md-3 padding-left-none'>
            <span class='data-head'><u>Additional Charges</u></span>
        </div>
        <div class='clearfix'></div>
        <div class='col-md-3 padding-left-none'>
            <input type='text' class='form-control form-control1 clsRDSCratingChargespCFT' placeholder='Crating Charges (per CFT)' id='creating_charges_{{$id}}' name='creating_charges_{{$id}}'/>
        </div>
        <div class='col-md-3 padding-left-none'>
            <input type='text' class='form-control form-control1 clsRDSStorageChargespCFTpDay' placeholder='Storage Charges (CFT/Day)' id='storage_charges_{{$id}}' name='storage_charges_{{$id}}'/>
        </div>
        <div class='col-md-3 padding-left-none'>
            <input type='text' class='form-control form-control1 clsRDSEscortChargespDay' placeholder='Escort Charges (per Day)' id='escort_charges_{{$id}}' name='escort_charges_{{$id}}' />
        </div>
        <div class='col-md-3 padding-left-none'>
            <input type='text' class='form-control form-control1 clsRDSHandymanChargespHour' placeholder='Handyman Charges (per Hour)' id='handyman_charges_{{$id}}' name='handyman_charges_{{$id}}' />
        </div>
        <div class='clearfix'></div>
        <div class='col-md-3 padding-left-none'>
            <input type='text' class='form-control form-control1 clsRDSPropertySearchCharges' placeholder='Property Search (Rs)' id='property_search_{{$id}}' name='property_search_{{$id}}' />
        </div>
        <div class='col-md-3 padding-left-none'>
            <input type='text' class='form-control form-control1 clsRDSBrokerageCharges' placeholder='Brokerage (Rs)' id='brokerage_charges_{{$id}}' name='brokerage_charges_{{$id}}' />
        </div>
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
            <button type="button" class='btn pull-right btn add-btn relocation_quote_submit' name='submitform_quote_{{$id}}' id='submitform_quote_{{$id}}'>Submit</button>
        </div>
    </form>
@endif