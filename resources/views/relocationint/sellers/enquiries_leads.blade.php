@inject('commonComponent', 'App\Components\CommonComponent')
@inject('sellercomponent', 'App\Components\Relocationint\RelocationIntSellerComponent')
@inject('oceansellercomponent', 'App\Components\RelocationInt\OceanInt\RelocationOceanSellerComponent')

<div class="table-div">

    <!-- Table Head Starts Here -->
        <div class="table-heading inner-block-bg">
            <div class="col-md-2 padding-left-none">
                <!-- <input type="checkbox" /><span class="lbl padding-8"></span> -->
                Buyer Name<i class="fa  fa-caret-down"></i>
            </div>
            <div class="col-md-2 padding-left-none">Dispatch Date <i class="fa  fa-caret-down"></i></div>
            <div class="col-md-2 padding-left-none">Delivery Date <i class="fa  fa-caret-down"></i></div>
            @if($lkp_international_type_id==1)
                <div class="col-md-2 padding-left-none">Total Weight</div>
                <div class="col-md-2 padding-left-none">Volume</div>
            @elseif($lkp_international_type_id==2)
                <div class="col-md-4 padding-left-none">Total CFT</div>
            @endif
            <div class="col-md-2 padding-none"></div>
        </div>
    <!-- Table Head Ends Here -->

    <div class="table-data">
        @if(count($enquiries) > 0)
            @foreach($enquiries as $enquiry)
                {{--*/  $id = $enquiry->id /*--}}
                {{--*/  $buyerbussinessname = $enquiry->username /*--}}
                {{--*/  $fromlocation = $commonComponent::getCityName($enquiry->from_location_id)  /*--}}
                {{--*/  $tolocation = $commonComponent::getCityName($enquiry->to_location_id)  /*--}}
                {{--*/  $dispatchdate = $enquiry->dispatch_date /*--}}
                {{--*/  $deliverydate = $enquiry->delivery_date /*--}}
                {{--*/  $weight = $commonComponent::getCartonsTotalWeight($id) /*--}}
                @if($enquiry->lkp_international_type_id==1)
                    {{--*/  $volume = number_format(($weight * 3000)/1728,2) /*--}}
                @else
                    {{--*/  $totalcft = $oceansellercomponent::getVolumeCft($id) /*--}}
                    {{--*/  $volume = number_format($totalcft/35.5,2) /*--}}
                @endif    
                {{--*/  $fromlocation = $commonComponent::getCityName($enquiry->from_location_id) /*--}}
                {{--*/  $transid = $enquiry->transaction_id /*--}}
                {{--*/  $int_type_id = $enquiry->lkp_international_type_id /*--}}
                {{--*/  $buyerid = $enquiry->buyer_id /*--}}
                {{--*/ $viewcount = $commonComponent::viewCountForBuyer(Auth::User ()->id,$id,'relocationoffice_buyer_post_views') /*--}}
                {{--*/ $office_buyer_post_inventory_details = '' /*--}}
                                 <!-- Table Row Starts Here -->
                    <div class="table-row inner-block-bg">
                        <div class="col-md-2 padding-left-none">
                            <!-- <input type="checkbox" /><span class="lbl padding-8"></span> -->
                           {{$buyerbussinessname}}
                            <div class="red">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                        </div>
                        <div class="col-md-2 padding-left-none">{{$commonComponent->checkAndGetDate($dispatchdate)}}</div>
                        <div class="col-md-2 padding-left-none">{{$commonComponent->checkAndGetDate($deliverydate)}}</div>
                        @if($lkp_international_type_id==1)
                            <div class="col-md-2 padding-left-none">{{$weight}} KGs</div>
                            <div class="col-md-2 padding-left-none">{{$volume}} CFT</div>
                        @else
                            <div class="col-md-4 padding-left-none">{{$volume}} CFT</div>
                        @endif
                         {{--*/ $submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User ()->id,$id,$seller_post->id) /*--}}
            			 {{--*/ $submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted"  /*--}}
                        <div class="col-md-2 padding-none">
                            <button class="btn red-btn submit-data pull-right detailsslide-office" id="{{$id}}" rel="{{$id}}_{{$buyerid}}">{{$submitedquotetext}}</button>
                        </div>
                        
                        <div class="clearfix"></div>
                        <div class="pull-right text-right">
                            <div class="info-links">
                                <a class="show-data-link detailsslide-office" rel="{{$buyerid}}_{{$id}}"><span class="show-icon">+</span><span class="hide-icon">-</span> Details</a>
                                 <a class="underline_link new_message" data-buyer-transaction="{{$transid}}" data-userid='{{ $buyerid }}' data-id="{{ Session::get('seller_post_item') }}" data-buyerquoteitemidforseller="{{ $id }}" href="#"><i class="fa fa-envelope-o"></i></a>
                            </div>
                        </div>

                            <div class="col-md-12 submit-data-div padding-none padding-top">
                           
                            @include('relocationint.sellers.seller_search_submit_quote', array(
                            'submittedquote' => $submittedquote,
                            'id' => $id,
                            'international_type' => $int_type_id
                            ))
                            </div>

                        <div class="col-md-12 show-data-div padding-top">
                            <div class="col-md-12 padding-none">
                                <div>
                                    <div class="clearfix"></div>
                                    @if($lkp_international_type_id==1)
                                    <div class="col-md-12 padding-none">
                                    <!-- Table Starts Here -->
                                        <div class="table-div table-style1">
                                            <div class="table-div table-style1 margin-none">
                                            
                                                <!-- Table Head Starts Here -->
                                                <div class="table-heading inner-block-bg">
                                                    <div class="col-md-8 padding-left-none">Carton Type</div>
                                                    <div class="col-md-4 padding-left-none">No of Items</div>
                                                </div>
                                                <!-- Table Head Ends Here -->

                                                <div class="table-data">    
                                                    {{--*/ $buyer_post_inventory = $sellercomponent->getBuyerInventaryParticulars($id)  /*--}}
                                                    <!-- Table Row Starts Here -->
                                                    @foreach($buyer_post_inventory as $inventory_details)
                                                    <div class="table-row inner-block-bg">
                                                        <div class="col-md-8 padding-left-none">{{$inventory_details->carton_type}} ({{$inventory_details->carton_description}})</div>
                                                        <div class="col-md-4 padding-left-none">{{$inventory_details->number_of_cartons}}</div>
                                                    </div>
                                                    @endforeach
                                                    <!-- Table Row Ends Here -->
                                                </div>
                                            </div> 
                                            <div class="clearfix"></div>
                                        </div>  
                                    </div>
                                    @else
                                        @include('relocationint.ocean.buyers.buyerpost_inventory_details', array(
                                            'buyerpost_id' => $id,
                                        ))
                                    @endif
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                <!-- Table Row Ends Here -->   
            @endforeach
        @else
            <div class="col-md-12 padding-left-none padding-right-none table-data">
                <div class="table-row inner-block-bg text-center"><span class="nocontent">No Records Found</span></div>
            </div>
        @endif
    <!-- Table Row Ends Here -->
    </div>
</div>