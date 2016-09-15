@if(isset($booknow_flag) && !empty($booknow_flag))
       {{--*/ $location_type = 'buyersearch_booknow_offer_source_location_type_' /*--}}
       {{--*/ $destination_type = 'buyersearch_booknow_offer_destination_location_type_' /*--}}
       {{--*/ $packaging_type = 'buyersearch_booknow_offer_packaging_type_' /*--}}
       {{--*/ $consignment_pickup_date = 'buyersearch_booknow_offer_consignment_pickup_date_' /*--}}
       {{--*/ $consignment_value = 'buyersearch_booknow_offer_consignment_value_' /*--}}
       {{--*/ $need_insurance = 'buyersearch_booknow_offer_need_insurance_' /*--}}
       {{--*/ $is_fragile = 'buyersearch_booknow_offer_is_fragile_' /*--}}
       {{--*/ $consignor_name = 'buyersearch_booknow_offer_consignor_name_' /*--}}
       {{--*/ $consignor_number = 'buyersearch_booknow_offer_consignor_number_' /*--}}
       {{--*/ $consignor_email = 'buyersearch_booknow_offer_consignor_email_' /*--}}
       {{--*/ $consignor_address = 'buyersearch_booknow_offer_consignor_address_' /*--}}
       {{--*/ $consignor_pincode = 'buyersearch_booknow_offer_consignor_pincode_' /*--}}
       {{--*/ $consignee_name = 'buyersearch_booknow_offer_consignee_name_' /*--}}
       {{--*/ $consignee_number = 'buyersearch_booknow_offer_consignee_number_' /*--}}
       {{--*/ $consignee_email = 'buyersearch_booknow_offer_consignee_email_' /*--}}
       {{--*/ $consignee_address = 'buyersearch_booknow_offer_consignee_address_' /*--}}
       {{--*/ $consignee_pin = 'buyersearch_booknow_offer_consignee_pin_' /*--}}
       {{--*/ $additional_details = 'buyersearch_booknow_offer_additional_details_' /*--}}
       {{--*/ $addtocartbutton = 'buyersearch_booknow_counter_offer_addtocart_' /*--}}
       {{--*/ $checkoutbutton = 'buyersearch_booknow_counter_offer_checkout_' /*--}}
@else
       {{--*/ $location_type = 'buyer_counter_offer_source_location_type_' /*--}}
       {{--*/ $destination_type = 'buyer_counter_offer_destination_location_type_' /*--}}
       {{--*/ $packaging_type = 'buyer_counter_offer_packaging_type_' /*--}}
       {{--*/ $consignment_pickup_date = 'buyer_counter_offer_consignment_pickup_date_' /*--}}
       {{--*/ $consignment_value = 'buyer_counter_offer_consignment_value_' /*--}}
       {{--*/ $need_insurance = 'buyer_counter_offer_need_insurance_' /*--}}
       {{--*/ $is_fragile = 'buyer_counter_offer_is_fragile_' /*--}}
       {{--*/ $consignor_name = 'buyer_counter_offer_consignor_name_' /*--}}
       {{--*/ $consignor_number = 'buyer_counter_offer_consignor_number_' /*--}}
       {{--*/ $consignor_email = 'buyer_counter_offer_consignor_email_' /*--}}
       {{--*/ $consignor_address = 'buyer_counter_offer_consignor_address_' /*--}}
       {{--*/ $consignor_pincode = 'buyer_counter_offer_consignor_pincode_' /*--}}
       {{--*/ $consignee_name = 'buyer_counter_offer_consignee_name_' /*--}}
       {{--*/ $consignee_number = 'buyer_counter_offer_consignee_number_' /*--}}
       {{--*/ $consignee_email = 'buyer_counter_offer_consignee_email_' /*--}}
       {{--*/ $consignee_address = 'buyer_counter_offer_consignee_address_' /*--}}
       {{--*/ $consignee_pin = 'buyer_counter_offer_consignee_pin_' /*--}}
       {{--*/ $additional_details = 'buyer_counter_offer_additional_details_' /*--}}
       {{--*/ $addtocartbutton = 'add_buyer_counter_offer_addtocart_' /*--}}
       {{--*/ $checkoutbutton = 'add_buyer_counter_offer_checkout_' /*--}}
       
@endif

@inject('Buyer', 'App\Components\CommonComponent')
 
{{--*/ $buyerDetails = $Buyer->getBuyerDetails() /*--}}
{{--*/ $mobileNo = '' /*--}}

 @if(Auth::User()->is_business == 1)
 {{--*/  $mobileNo = $buyerDetails->contact_mobile /*--}}
   @else
  {{--*/   $mobileNo = $buyerDetails->mobile /*--}}
  
  @endif 
{{--*/ $serviceId = Session::get('service_id') /*--}} 
{!! Form::hidden('Service_ID', $serviceId, array('id' => 'Service_ID')) !!}
{{--*/ $districtid = 0 /*--}}
@if(!isset($toCityid))
{{--*/ $toCityid = 0 /*--}}
@endif
{{--*/ $districtid  = $Buyer::getDistrict($toCityid,$serviceId) /*--}}
{!! Form::hidden('districtid', $districtid, array('id' => 'districtid')) !!}


<div class="col-md-12 inner-block-bg single-layout1  buyer_book_now_content buyer_book_now_details_{{ $buyerQuoteId }}">
    <div class="col-md-12 padding-none">
        @if(isset($isltl) && $isltl == 0)
        <div class="col-md-4 form-control-fld">
            <div class="normal-select">
                {!! Form::select($location_type.$buyerQuoteId, $sourceLocation, '', 
                    array('id' =>$location_type.$buyerQuoteId, 'class' => "selectpicker $location_type"))!!}
            </div>
            <label class="error" id="buyer_counter_offer_source_location_type_error_{!! $buyerQuoteId !!}"></label>
        </div>
        @if($serviceId!=RELOCATION_GLOBAL_MOBILITY)
        @if(isset($destinationLocation))
        <div class="col-md-4 form-control-fld">
            <div class="normal-select">
                {!! Form::select($destination_type.$buyerQuoteId, $destinationLocation, '',
                    array('id' => $destination_type.$buyerQuoteId, 'class' => "selectpicker $destination_type"))!!}
            </div>
            <label class="error" id="buyer_counter_offer_destination_location_type_error_{!! $buyerQuoteId !!}"></label>
        </div>
        @endif
        @endif
        @if($serviceId!=RELOCATION_GLOBAL_MOBILITY)
            @if(isset($packagingType))
                @if($serviceId==ROAD_FTL)
                    @if(Session::has('session_load_type_buyer'))
                    {{--*/ $packagingType =   $Buyer::getLoadBasedAllPackages(Session::get('session_load_type_buyer')) /*--}}
                    @endif
                <div class="col-md-4 form-control-fld">
                    <div class="normal-select">
                        {!! Form::select($packaging_type.$buyerQuoteId, (['' => 'Packaging Type'] +$packagingType), '',
                            array('id' => $packaging_type.$buyerQuoteId, 'class' => "selectpicker $packaging_type"))!!}
                    </div>
                    <label class="error" id="buyer_counter_offer_packaging_type_error_{!! $buyerQuoteId !!}"></label>
                </div>
                @else
                <div class="col-md-4 form-control-fld">
                    <div class="normal-select">
                        {!! Form::select($packaging_type.$buyerQuoteId, $packagingType, '',
                            array('id' => $packaging_type.$buyerQuoteId, 'class' => "selectpicker $packaging_type"))!!}
                    </div>
                    <label class="error" id="buyer_counter_offer_packaging_type_error_{!! $buyerQuoteId !!}"></label>
                </div>
                @endif
            @else
            <div class="clearfix"></div>
            @endif
        @endif
        <div class="col-md-4 form-control-fld" style="display:none;">
            <div >
                {!! Form::text($location_type."text",  '', 
                    array('id' =>$location_type."text", 'class' => "form-control form-control1 clsAlphaSpace"))!!}
            </div>
            <label class="error" id="buyer_counter_offer_source_location_type_text_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-4 form-control-fld" style="display:none;">
            <div >
                {!! Form::text($destination_type."text", '',
                    array('id' => $destination_type."text", 'class' => 'form-control form-control1 clsAlphaSpace'))!!}
            </div>
            <label class="error" id="buyer_counter_offer_destination_location_type_text_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-4 form-control-fld" style="display:none;">
            <div >
                {!! Form::text($packaging_type."text",  '',
                    array('id' => $packaging_type."text", 'class' => 'form-control form-control1 clsAlphaSpace'))!!}
            </div>
            <label class="error" id="buyer_counter_offer_packaging_type_text_error_{!! $buyerQuoteId !!}"></label>
        </div>
        @if(isset($packagingType))
         <div class="clearfix"></div>
        @endif
        @endif
        <div class="clearfix"></div>
        @if($serviceId!=RELOCATION_GLOBAL_MOBILITY)
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                {!! Form::text($consignment_pickup_date.$buyerQuoteId, '',
                    array('id' => $consignment_pickup_date.$buyerQuoteId, 'class'=>'calendar form-control buyer_counter_offer_consignment_pickup_date','placeholder'=>'Consignment Pickup Date *','readonly' => true)) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignment_pickup_date_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
                {!! Form::text($consignment_value.$buyerQuoteId, '',
                    array('class'=>'form-control form-control1 clsConsignValue','id' => $consignment_value.$buyerQuoteId,'placeholder'=>'Consignment Value *')) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignment_value_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-4 form-control-fld">
            {!! Form::checkbox($need_insurance.$buyerQuoteId, '0', 
                    null, ['id' => $need_insurance.$buyerQuoteId, 'class' => 'buyer_counter_offer_insurance buyer_search_insurance']) !!}<span class="lbl padding-8"></span>
            Need Insurance
        </div>
        @endif
        <div class="clearfix"></div>
        <h4 class="mid-heading">Consignor Details</h4>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
             
                {!! Form::text($consignor_name.$buyerQuoteId, $buyerDetails->username,
                    array('class'=>'form-control form-control1 clsConsignorName','id' => $consignor_name.$buyerQuoteId,'placeholder'=>'Consignor Name *')) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignor_name_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
            
                {!! Form::text($consignor_number.$buyerQuoteId, $mobileNo,
                    array('class'=>'form-control form-control1 clsMobileno','id' => $consignor_number.$buyerQuoteId,'placeholder'=>'Mobile *','maxlength'=>10)) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignor_number_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
                {!! Form::text($consignor_email.$buyerQuoteId,  $buyerDetails->email,
                array('class'=>'form-control form-control1 clsEmailAddr','id' => $consignor_email.$buyerQuoteId,'placeholder'=>'Email id')) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignor_email_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-8 form-control-fld">
            <div class="input-prepend">
                {!! Form::textarea($consignor_address.$buyerQuoteId,  $buyerDetails->address,
                    array('id' => $consignor_address.$buyerQuoteId,'class'=>'form-control form-control1 clsAddress','placeholder'=>'Consignor Address *', 'rows' => 2)) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignor_address_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
                {!! Form::text($consignor_pincode.$buyerQuoteId,  $buyerDetails->pincode,
                    array('class'=>'form-control form-control1 clsPinCode','id' => $consignor_pincode.$buyerQuoteId,'placeholder'=>'Pin code *','maxlength'=>6)) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignor_pincode_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="clearfix"></div>
        
        {!! Form::hidden('service_id',$serviceId ,['id' =>'service_id', 'class' => 'service_id']) !!}
        @if($serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL  || $serviceId==OCEAN)
        <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none low-top-padding text-left form-group mobile-padding-none">
            {!! Form::checkbox($is_fragile.$buyerQuoteId, '0', 
                    null, ['id' => $is_fragile.$buyerQuoteId, 'class' => 'buyer_counter_offer_insurance buyer_search_insurance']) !!}<span class="lbl padding-8"></span>
            Is Fragile
        </div>
        @endif
        <div class="clearfix"></div>
        @if($serviceId!=RELOCATION_GLOBAL_MOBILITY)
        <h4 class="mid-heading">Consignee Details</h4>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
                {!! Form::text($consignee_name.$buyerQuoteId, '',
                    array('id' => $consignee_name.$buyerQuoteId,'class'=>'form-control form-control1 clsConsignorName','placeholder'=>'Consignee Name *')) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignee_name_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
                {!! Form::text($consignee_number.$buyerQuoteId, '',
                    array('id' => $consignee_number.$buyerQuoteId,'class'=>'form-control form-control1 clsMobileno','placeholder'=>'Mobile *')) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignee_number_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
                {!! Form::text($consignee_email.$buyerQuoteId, '',
                    array('id' => $consignee_email.$buyerQuoteId,'class'=>'form-control form-control1 clsEmailAddr','placeholder'=>'Email id')) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignee_email_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
                {!! Form::textarea($consignee_address.$buyerQuoteId, '',
                    array('id' => $consignee_address.$buyerQuoteId,'class'=>'form-control form-control1 clsAddress','placeholder'=>'Consignee Address *', 'rows' => 2)) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignee_address_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
            @if($serviceId== AIR_INTERNATIONAL || $serviceId== OCEAN || $serviceId== ROAD_TRUCK_LEASE || $serviceId== RELOCATION_GLOBAL_MOBILITY )
			{!! Form::text($consignee_pin.$buyerQuoteId, '',array('id' => $consignee_pin.$buyerQuoteId,'class'=>'form-control form-control1 clsPinCode enabletextareapincode','placeholder'=>'Pin code *','maxlength'=>6)) !!}
            @else
            {!! Form::text($consignee_pin.$buyerQuoteId, '' , ['id' => $consignee_pin.$buyerQuoteId,'class'=>'form-control form-control1 ptlTocheckbooknowLocation disabletextarepincode numericvalidation_autopop maxlimitsix_lmtVal', 'placeholder' => 'Pin code*']) !!}
			{!! Form::text('ptlTocheckLocationId', '', array('id' => 'ptlTocheckLocationId','class'=>'form-control form-control1 numericvalidation_autopop maxlimitsix_lmtVal ','style'=>'display:none;')) !!}
			@endif
            </div>
            <label class="error booknowtopincodecheck" id="buyer_counter_offer_consignee_pin_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
                {!! Form::textarea($additional_details.$buyerQuoteId, '',
                    array('id' => $additional_details.$buyerQuoteId,'class'=>'form-control form-control1 clsConsignAddInfo','placeholder'=>'Additional Details', 'rows' => 2)) !!}
            </div>
        </div>
        @endif
        
    </div>
</div>
<div class="col-md-12 padding-none text-right">
    {!! Form::button('Add to Cart',array('id'=>$addtocartbutton.$buyerQuoteId, 'class'=>'btn add-btn flat-btn add_buyer_addtocart_details booknow_buyer')) !!}
    {!! Form::button('Checkout',array('id'=>$checkoutbutton.$buyerQuoteId, 'class'=>'btn red-btn flat-btn add_buyer_checkout_details')) !!}
</div>