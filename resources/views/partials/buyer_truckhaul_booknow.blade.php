@if(isset($booknow_flag) && !empty($booknow_flag))
       {{--*/ $location_type = 'buyersearch_booknow_offer_source_location_type_' /*--}}
       {{--*/ $reporting_date = 'buyersearch_booknow_offer_reporting_date_' /*--}}
       {{--*/ $reporting_time_from = 'buyersearch_booknow_offer_reporting_fromtime_' /*--}}
       {{--*/ $reporting_time_to = 'buyersearch_booknow_offer_reporting_totime_' /*--}}
       
       {{--*/ $consignor_name = 'buyersearch_booknow_offer_consignor_name_' /*--}}
       {{--*/ $consignor_number = 'buyersearch_booknow_offer_consignor_number_' /*--}}
       {{--*/ $consignor_email = 'buyersearch_booknow_offer_consignor_email_' /*--}}
       {{--*/ $consignor_address = 'buyersearch_booknow_offer_consignor_address_' /*--}}
       {{--*/ $consignor_pincode = 'buyersearch_booknow_offer_consignor_pincode_' /*--}}
       {{--*/ $additional_details = 'buyersearch_booknow_offer_additional_details_' /*--}}
       {{--*/ $addtocartbutton = 'buyersearch_booknow_counter_offer_addtocart_' /*--}}
       {{--*/ $checkoutbutton = 'buyersearch_booknow_counter_offer_checkout_' /*--}}
@else
       {{--*/ $location_type = 'buyer_counter_offer_source_location_type_' /*--}}
       {{--*/ $reporting_date = 'buyer_counter_offer_reporting_date_' /*--}}
       {{--*/ $reporting_time_from = 'buyer_counter_offer_reporting_fromtime_' /*--}}
       {{--*/ $reporting_time_to = 'buyer_counter_offer_reporting_totime_' /*--}}
       
       {{--*/ $consignor_name = 'buyer_counter_offer_consignor_name_' /*--}}
       {{--*/ $consignor_number = 'buyer_counter_offer_consignor_number_' /*--}}
       {{--*/ $consignor_email = 'buyer_counter_offer_consignor_email_' /*--}}
       {{--*/ $consignor_address = 'buyer_counter_offer_consignor_address_' /*--}}
       {{--*/ $consignor_pincode = 'buyer_counter_offer_consignor_pincode_' /*--}}
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

<div class="col-md-12 inner-block-bg single-layout1  buyer_book_now_content buyer_book_now_details_{{ $buyerQuoteId }}">
    <div class="col-md-12 padding-none">
        
        <div class="col-md-3 form-control-fld">
            <div class="normal-select">
                {!! Form::select($location_type.$buyerQuoteId, $sourceLocation, '', 
                    array('id' =>$location_type.$buyerQuoteId, 'class' => "selectpicker $location_type"))!!}
            </div>
            <label class="error" id="buyer_counter_offer_source_location_type_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-3 form-control-fld" style="display:none;">
            <div >
                {!! Form::text($location_type."text",  '', 
                    array('id' =>$location_type."text", 'class' => "form-control form-control1 clsAlphaSpace"))!!}
            </div>
            <label class="error" id="buyer_counter_offer_source_location_type_text_error_{!! $buyerQuoteId !!}"></label>
        </div>
        
        <div class="col-md-3 form-control-fld">
            <div class="input-prepend">
                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                {!! Form::text($reporting_date.$buyerQuoteId, '',
                    array('id' => $reporting_date.$buyerQuoteId, 'class'=>' form-control buyer_counter_offer_reporting_date clsBooknowReportingDate','placeholder'=>'Reporting Date *','readonly' => true)) !!}
            </div>
            <label class="error error_calendar" id="buyer_counter_offer_reporting_date_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-6 form-control-fld">
            <div class="col-md-3 padding-left-none padding-top-8 text-right">
                    <span class="data-head">Reporting Time</span>
            </div>
            <div class="col-md-9 padding-none">
                <div class="col-md-6 padding-right-none">		
                        <div class="error_align_div">
                          <div class="input-prepend date" id="booknw_reporting_time">
                            <span class="add-on"><i class="fa fa-clock-o"></i></span>
                                {!! Form::text($reporting_time_from.$buyerQuoteId, '',['id' => $reporting_time_from.$buyerQuoteId,'class'=>'form-control  timepicker_from buyer_counter_offer_reporting_fromtime clsBooknowReportingTime', 'placeholder' =>'Reporting From *', 'data-default-time'=>'false','readonly' => true]) !!}	
                          </div>
                      </div>
                    <label class="error error_time" id="buyer_counter_offer_reporting_fromtime_error_{!! $buyerQuoteId !!}"></label>
                </div>
                <div class="col-md-6 padding-right-none">
                        <div class="error_align_div">
                          <div class="input-prepend date" id="booknw_reporting_to_time">
                                <span class="add-on"><i class="fa fa-clock-o"></i></span>
                                {!! Form::text($reporting_time_to.$buyerQuoteId, '',['id' => $reporting_time_to.$buyerQuoteId,'class'=>'form-control timepicker_to buyer_counter_offer_reporting_totime', 'placeholder' =>'Reporting To *', 'data-default-time'=>'false','readonly' => true]) !!}	
                        </div></div>
                    <label class="error" id="buyer_counter_offer_reporting_totime_error_{!! $buyerQuoteId !!}"></label>
                </div>
            </div>
            
        </div>
        
        <div class="clearfix"></div>
        <h4 class="mid-heading">Reporting Details</h4>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
             
                {!! Form::text($consignor_name.$buyerQuoteId, $buyerDetails->username,
                    array('class'=>'form-control form-control1 clsReportingTo','id' => $consignor_name.$buyerQuoteId,'placeholder'=>'Report to *')) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignor_name_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
            
                {!! Form::text($consignor_number.$buyerQuoteId, $mobileNo,
                    array('class'=>'form-control form-control1 clsMobileno','id' => $consignor_number.$buyerQuoteId,'placeholder'=>'Mobile *')) !!}
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
                    array('id' => $consignor_address.$buyerQuoteId,'class'=>'form-control form-control1 clsReportingAddr','placeholder'=>'Address *', 'rows' => 2)) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignor_address_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
                {!! Form::text($consignor_pincode.$buyerQuoteId,  $buyerDetails->pincode,
                    array('class'=>'form-control form-control1 clsPinCode','id' => $consignor_pincode.$buyerQuoteId,'placeholder'=>'Pin code *')) !!}
            </div>
            <label class="error" id="buyer_counter_offer_consignor_pincode_error_{!! $buyerQuoteId !!}"></label>
        </div>
        <div class="clearfix"></div>
        {{--*/ $serviceId = Session::get('service_id') /*--}} 
        {!! Form::hidden('service_id',$serviceId ,['id' =>'service_id', 'class' => 'service_id']) !!}
       
        <div class="clearfix"></div>
        
        
        <div class="col-md-4 form-control-fld">
            <div class="input-prepend">
                {!! Form::textarea($additional_details.$buyerQuoteId, '',
                    array('id' => $additional_details.$buyerQuoteId,'class'=>'form-control form-control1 clsAdditionalInfo','placeholder'=>'Additional Details', 'rows' => 2)) !!}
            </div>
        </div>
    </div>
</div>
<div class="col-md-12 padding-none text-right">
    {!! Form::button('Add to Cart',array('id'=>$addtocartbutton.$buyerQuoteId, 'class'=>'btn add-btn flat-btn add_buyer_addtocart_details booknow_buyer')) !!}
    {!! Form::button('Checkout',array('id'=>$checkoutbutton.$buyerQuoteId, 'class'=>'btn red-btn flat-btn add_buyer_checkout_details')) !!}
</div>