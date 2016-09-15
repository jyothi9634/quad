@inject('commoncomponent', 'App\Components\CommonComponent')
@inject('sellercomponent', 'App\Components\RelocationGlobal\RelocationGlobalSellerComponent')


<div class="table-div">

    <!-- Table Head Starts Here -->

    <div class="table-heading inner-block-bg">
        <div class="col-md-2 padding-left-none">
            <span class="lbl padding-8"></span>
            Buyer Name<i class="fa  fa-caret-down"></i>
        </div>
        <div class="col-md-2 padding-left-none">Location <i class="fa  fa-caret-down"></i></div>
        <div class="col-md-2 padding-left-none">Dispatch Date <i class="fa  fa-caret-down"></i></div>
        <div class="col-md-2 padding-left-none">No of Services</div>
        <div class="col-md-4 padding-none"></div>
    </div>

    <!-- Table Head Ends Here -->

    <div class="table-data">
        
        @if(count($enquiries) > 0)
        @foreach($enquiries as $enquiry)
        <?php
        $id = $enquiry->id;
        $bqid = $enquiry->bqid;
        $buyerbussinessname = $enquiry->username;
        $dispatchdate = $enquiry->dispatch_date;
        
        //$services = $enquiry->measurement;
        
        $fromlocation = $commoncomponent::getCityName($enquiry->location_id);
        
        $transid = $enquiry->transaction_id;
        $buyerid = $enquiry->buyer_id;

        ?>
{{--*/ $buyerquote = $sellercomponent->getBuyerQuoteItems($id) /*--}}

        {{--*/ $viewcount = $commoncomponent::viewCountForBuyer(Auth::User ()->id,$id,'relocationgm_buyer_post_views') /*--}}
{{--*/ $ltype = 'spot' /*--}}
    {{--*/ $buyerSelectedservices = $commoncomponent->getBuyerPostServicesList($id,$ltype) /*--}}

    {{--*/ $buyerSelectedServicesCount= count($buyerSelectedservices) /*--}}

        
                <!-- Table Row Starts Here -->

        <div class='table-row inner-block-bg'>
            <div class='col-md-2 padding-left-none'>
                <span class='lbl padding-8'></span>
                {{$buyerbussinessname}}
                <div class='red'>
                    <i class='fa fa-star'></i>
                    <i class='fa fa-star'></i>
                    <i class='fa fa-star'></i>
                </div>
            </div>
            
            <input type="hidden" id="to_loc_{{$id}}" name="to_loc_{{$id}}" value="{{$enquiry->location_id}}"/>
            <div class='col-md-2 padding-none'>{{$fromlocation}}</div>
            <div class='col-md-2 padding-left-none'>{{$commoncomponent->checkAndGetDate($dispatchdate)}}</div>
            <div class='col-md-2 padding-none'>{{$buyerSelectedServicesCount}}</div>
            
            {{--*/ $submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User ()->id,$id,$seller_post->id) /*--}}
            {{--*/ $submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted"  /*--}}
            <div class='col-md-4 padding-none'><button class='detailsslide-term btn red-btn pull-right submit-data' id ='{{$id}}'>{{$submitedquotetext}}</button></div>

            <div class='clearfix'></div>
            <div class='pull-right text-right'>
                <div class='info-links'>
                    <span class="detailsslide  underline_link" data-buyersearchlistid="{{ $buyerid }}_{{ $id }}"><span class="show_details">+</span><span class="hide_details">-</span> Details</span>
<!--                    <a class='show-data-link' id='{{$id}}'><span class='show-icon'>+</span><span class='hide-icon'>-</span> Details</a>-->
                    <a class="underline_link new_message" data-buyer-transaction="{{$transid}}" data-userid='{{ $buyerid }}' data-id="{{ Session::get('seller_post_item') }}" data-buyerquoteitemidforseller="{{ $id }}" href="#"><i class="fa fa-envelope-o"></i></a>
                </div>
            </div>

            
            <div class='col-md-12 show-data-div padding-top quote_details_1_{{ $buyerid }}_{{ $id }}' id='spot_transaction_details_view_{{$id}}'>
                @include('relocationglobal.buyers._buyerserviceslist',['buyerpost_id' => $id])
                
                </div>
            <div class='col-md-12  padding-none padding-top term_quote_details_{{$id}}' style='display:none'>
                @include('relocationglobal.sellers.submit_quote', array(
                    'submittedquote' => $submittedquote,
                    'id' => $id,
                    'location_id'=>$enquiry->location_id

                ))
            </div>
            
        </div>
        @endforeach
        @else
            <div class="col-md-12 padding-left-none padding-right-none table-data">
                <div class="table-row inner-block-bg text-center"><span class="nocontent">No Records Found</span></div>
            </div>
        @endif
                <!-- Table Row Ends Here -->


    </div>
</div>