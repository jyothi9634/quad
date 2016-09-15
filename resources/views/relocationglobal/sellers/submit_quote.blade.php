@inject('commoncomponent', 'App\Components\CommonComponent')
@inject('sellercomponent', 'App\Components\RelocationGlobal\RelocationGlobalSellerComponent')
@if(!empty($submittedquote))
    {{--*/ $quote=0 /*--}}
    <?php  //echo "<pre>"; print_r($submittedquote);exit; ?>
    @foreach($submittedquote as $sub)
        {{--*/ $quote += $sub->service_quote; /*--}}
    @endforeach
   
    <div class="col-md-12 form-control-fld padding-left-none margin-top">
        <b>Seller Quote</b>
    </div>
    <div class="col-md-3 padding-left-none form-control-fld margin-none">
        <span class="data-head">Total (Rs)</span> <span class="data-value">Rs {{$quote}} /-</span>
    </div>
   
@else
    <form  id="relocgm_submitform_quote_{{$id}}" name="relocgm_submitform_quote_{{$id}}" class="relocation_submit_quote" method="get">
        @if(isset($is_search))
            <input type="hidden" id="to_location_id_{{$id}}" name="to_location_id_{{$id}}" value="{{$search_params['to_location_id']}}"/>
            <input type="hidden" id="valid_from_{{$id}}" name="valid_from_{{$id}}" value="{{$search_params['valid_from']}}"/>
            <input type="hidden" id="valid_to_{{$id}}" name="valid_to_{{$id}}" value="{{$search_params['valid_to']}}"/>
        @endif
        
        <input type="hidden" name="buyerquoteid_{{$id}}" value="{{$id}}" />
        <input type='hidden' name='buyer_id' id='buyer_id_{{$id}}' value="{!! $enquiry->created_by !!}">
        <input type="hidden" name="quote_ids_{{$id}}" id="quote_ids_{{$id}}"  value=""/>
        <div class="table-div table-style1 margin-top">
        <!-- Table Head Starts Here -->
            <div class="table-heading inner-block-bg">
                    <div class="col-md-4 padding-left-none"><!--<input type="checkbox"><span class="lbl padding-8">--></span>Service</div>
                    <div class="col-md-4 padding-left-none">Numbers</div>
                    <div class="col-md-4 padding-left-none">Quote</div>
            </div>
            <div class="table-data">
            {{--*/ $all_services=$commoncomponent->getLkpRelocationGMServices(); /*--}}      
                @foreach($buyerquote as $bq)
                    <div class="table-row inner-block-bg">
                        <div class="col-md-4 padding-left-none">
                            <input type="checkbox" class="" quote-data="{{$id}}" name="lineitem_checkbox_{{$bq->id}}" id="lineitem_checkbox_{{$bq->id}}" value="{{$bq->id}}" onchange='javascript:checkRLGMPostitem(this.id)'><span class="lbl padding-8"></span>{{$bq->service_type}}
                        </div>
                        <div class="col-md-4 padding-left-none">
                        @if($bq->lkp_gm_service_id!=7)  
                            @if($bq->lkp_gm_service_id==3 )
                            {{$bq->measurement}}
                            @else
                            {{(int)$bq->measurement}}
                            @endif 
                        @endif 
                        {{$bq->measurement_units}}</div>
                        <div class="col-md-4 padding-left-none">
                            <input type="text" class="form-control form-control1 input-short clsGMSSubmitQuote" name="relgm_quote_{{$bq->id}}" id="relgm_quote_{{$bq->id}}" disabled>
                            <input type="hidden" name="relgm_quote_service_{{$bq->id}}" value="{{strtolower(str_replace(' ','_',$all_services[$bq->lkp_gm_service_id]))}}">
                        </div>
                    </div>
                @endforeach
            </div>
        <!-- Table Head Ends Here -->
        </div>
         @if(isset($is_search))
        @endif
        <div class='col-md-12 padding-none'>
            <button type="button" class='btn pull-right btn add-btn relocationgm_quote_submit' name='submitform_quote_{{$id}}' id='submitform_quote_{{$id}}' quote-data="{{$id}}">Submit</button>
        </div>
    </form>
@endif