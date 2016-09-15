<?php $booknow_flag=1; ?>
@if(isset($booknow_flag) && !empty($booknow_flag))
       {{--*/ $packaging_type = 'buyersearch_booknow_offer_packaging_type_' /*--}}
       {{--*/ $consignment_pickup_date = 'buyersearch_booknow_offer_consignment_pickup_date_' /*--}}
       {{--*/ $consignment_pickup_time = 'buyersearch_booknow_offer_consignment_pickup_time_' /*--}}
       {{--*/ $consignor_name = 'buyersearch_booknow_offer_consignor_name_' /*--}}
       {{--*/ $consignor_number = 'buyersearch_booknow_offer_consignor_number_' /*--}}
       {{--*/ $consignor_email = 'buyersearch_booknow_offer_consignor_email_' /*--}}
       {{--*/ $consignor_address = 'buyersearch_booknow_offer_consignor_address_' /*--}}
       {{--*/ $consignor_pincode = 'buyersearch_booknow_offer_consignor_pincode_' /*--}}      
       {{--*/ $addtocartbutton = 'buyersearch_booknow_counter_offer_addtocart_' /*--}}
       {{--*/ $checkoutbutton = 'buyersearch_booknow_counter_offer_checkout_' /*--}}
       {{--*/ $need_insurance = 'buyersearch_booknow_offer_need_insurance_' /*--}}
       
@else
       {{--*/ $packaging_type = 'buyer_counter_offer_packaging_type_' /*--}}
       {{--*/ $consignment_pickup_date = 'buyer_counter_offer_consignment_pickup_date_' /*--}}
       {{--*/ $consignment_pickup_time = 'buyer_counter_offer_consignment_pickup_time_' /*--}}
       {{--*/ $consignor_name = 'buyer_counter_offer_consignor_name_' /*--}}
       {{--*/ $consignor_number = 'buyer_counter_offer_consignor_number_' /*--}}
       {{--*/ $consignor_email = 'buyer_counter_offer_consignor_email_' /*--}}
       {{--*/ $consignor_address = 'buyer_counter_offer_consignor_address_' /*--}}
       {{--*/ $consignor_pincode = 'buyer_counter_offer_consignor_pincode_' /*--}}
     
       {{--*/ $addtocartbutton = 'add_buyer_counter_offer_addtocart_' /*--}}
       {{--*/ $checkoutbutton = 'add_buyer_counter_offer_checkout_' /*--}}
       {{--*/ $need_insurance = 'buyer_counter_offer_need_insurance_' /*--}}
@endif
 
<div class="col-md-12 col-sm-12 col-xs-12 padding-none  buyer_book_now_content buyer_book_now_details_{{ $buyerQuoteId }}">
    <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none mobile-margin-none text-left">
        <h5>Optional</h5>
    </div>
    <div class="clearfix"></div>    
    <div class="col-md-4 col-sm-4 col-xs-12 mobile-padding-none padding-left-none mobile-padding-none">
        {!! Form::select($packaging_type.$buyerQuoteId, $packagingType, '',
        array('id' => $packaging_type.$buyerQuoteId, 'class' => 'form-control'))!!}
        <p class="error text-left margin-top-5" id="buyer_counter_offer_packaging_type_error_{!! $buyerQuoteId !!}"></p>
    </div>
     <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none form-group mobile-padding-none">
        {!! Form::text($consignment_pickup_date.$buyerQuoteId, '',
            array('id' => $consignment_pickup_date.$buyerQuoteId, 'class'=>'calendar form-control buyer_counter_offer_consignment_pickup_date','placeholder'=>'Consignment Pickup Date')) !!}
        <p class="error text-left margin-top-5" id="buyer_counter_offer_consignment_pickup_date_error_{!! $buyerQuoteId !!}"></p>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none form-group mobile-padding-none">
        {!! Form::text($consignment_pickup_time.$buyerQuoteId, '',
            array('id' => $consignment_pickup_time.$buyerQuoteId, 'class'=>'clock timepicker form-control','placeholder'=>'Consignment Pickup Time')) !!}
        <p class="error text-left margin-top-5" id="buyer_counter_offer_consignment_pickup_time_error_{!! $buyerQuoteId !!}"></p>
    </div>
    <div class="clearfix"></div>
   	
    
    <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none form-group mobile-padding-none">
        {!! Form::text($consignor_name.$buyerQuoteId, '',
            array('class'=>'form-control','id' => $consignor_name.$buyerQuoteId,'placeholder'=>'Consignor Name')) !!}
        <p class="error text-left margin-top-5" id="buyer_counter_offer_consignor_name_error_{!! $buyerQuoteId !!}"></p>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none form-group mobile-padding-none">
        {!! Form::text($consignor_number.$buyerQuoteId, '',
            array('class'=>'form-control','id' => $consignor_number.$buyerQuoteId,'placeholder'=>'Mobile')) !!}
        <p class="error text-left margin-top-5" id="buyer_counter_offer_consignor_number_error_{!! $buyerQuoteId !!}"></p>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none form-group mobile-padding-none">
        {!! Form::text($consignor_email.$buyerQuoteId, '',
            array('class'=>'form-control','id' => $consignor_email.$buyerQuoteId,'placeholder'=>'Email id')) !!}
        <p class="error text-left margin-top-5" id="buyer_counter_offer_consignor_email_error_{!! $buyerQuoteId !!}"></p>
    </div>
    <div class="clearfix"></div>
    <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none form-group mobile-padding-none">
        {!! Form::textarea($consignor_address.$buyerQuoteId, '',
            array('id' => $consignor_address.$buyerQuoteId,'class'=>'form-control','placeholder'=>'Consignor Address', 'rows' => 2)) !!}
        <p class="error text-left margin-top-5" id="buyer_counter_offer_consignor_address_error_{!! $buyerQuoteId !!}"></p>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none form-group mobile-padding-none">
        {!! Form::text($consignor_pincode.$buyerQuoteId, '',
            array('class'=>'form-control','id' => $consignor_pincode.$buyerQuoteId,'placeholder'=>'Pin code')) !!}
        <p class="error text-left margin-top-5" id="buyer_counter_offer_consignor_pincode_error_{!! $buyerQuoteId !!}"></p>
    </div>
    <div class="col-md-4 col-sm-4 col-xs-12 padding-left-none low-top-padding text-left form-group mobile-padding-none">
        {!! Form::checkbox($need_insurance.$buyerQuoteId, '1', 
                null, ['id' => $need_insurance.$buyerQuoteId, 'class' => 'buyer_counter_offer_insurance buyer_search_insurance']) !!}
        Need Insurance
    </div>
    <div class="clearfix"></div>
    <div class="col-md-4 col-sm-4 col-xs-12 pull-right padding-right-none form-group mobile-padding-none">
        {!! Form::button('Book Now',array('id'=>$addtocartbutton.$buyerQuoteId, 'class'=>'btn black-btn add_buyer_addtocart_details booknow_buyer')) !!}
        
    </div>
    <div class="clearfix"></div>
</div>