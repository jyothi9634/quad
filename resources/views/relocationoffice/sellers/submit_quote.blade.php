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
            <span class="data-head">Door to Door Charges (per CFT) </span> <span class="data-value">{{$submittedquote->doortodoor_charges}} /-</span>
        </div>
        <div class="col-md-3 padding-left-none form-control-fld margin-none">
            <span class="data-head">Cancellation Charges (per CFT) </span> <span class="data-value">{{$submittedquote->cancellation_charges}} /-</span>
        </div>
        
	</div>

@else
    <form  id="reloc_submit_quote_{{$id}}" name="reloc_submit_quote_{{$id}}" class="relocationoffice_submit_quote" method="get">
        @if(isset($is_search))
            <input type="hidden" id="from_location_id_{{$id}}"  name="from_location_id_{{$id}}" value="{{$search_params['from_location_id']}}"/>
            <input type="hidden" id="valid_from_{{$id}}" name="valid_from_{{$id}}" value="{{$search_params['valid_from']}}"/>
            <input type="hidden" id="valid_to_{{$id}}" name="valid_to_{{$id}}" value="{{$search_params['valid_to']}}"/>
            
        @endif
        <input type="hidden" name="buyerquoteid_{{$id}}" value="{{$id}}" />
        <input type='hidden' name='buyer_id' id='buyer_id_{{$id}}' value="{!! $enquiry->created_by !!}">
        <input type="hidden" name="total_cft_{{$id}}" id="total_cft_{{$id}}"  value="{!! $commoncomponent->getOfficeBuyerVolume($id) !!}"/>
        <input type="hidden" name="total_price_{{$id}}" id="total_price_{{$id}}"  value=""/>
         <input type="hidden" id="from_loc_{{$id}}"  name="from_loc_{{$id}}" value="{!! $enquiry->from_location_id !!}"/>

        <div class="col-md-3 padding-left-none">
		<input type="text" id="doortodoor_charges_{{$id}}" name="doortodoor_charges_{{$id}}" class="form-control form-control1 clsROMDoor2DoorCharges relocationoffice_submit_quote" placeholder="Door to Door Charges" />
		</div>
		<div class="clearfix"></div>
        
        <div class='col-md-3 padding-left-none'>
            <span class='data-head'><u>Additional Charges</u></span>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-3 padding-left-none">
		<input type="text" id="cancellation_charges_{{$id}}" name="cancellation_charges_{{$id}}" class="form-control form-control1 clsROMCancelCharges" placeholder="Cancellation Charges" />
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
            <button type="button" class='btn pull-right btn add-btn relocationoffice_quote_submit' name='submitform_quote_{{$id}}' id='submitform_quote_{{$id}}'>Submit</button>
        </div>
    </form>
@endif