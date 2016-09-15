@inject('commoncomponent', 'App\Components\CommonComponent')
@inject('sellercomponent', 'App\Components\RelocationOffice\RelocationOfficeSellerComponent')

<div class="table-div">

    <!-- Table Head Starts Here -->
        <div class="table-heading inner-block-bg">
            <div class="col-md-4 padding-left-none">
                <!-- <input type="checkbox" /><span class="lbl padding-8"></span> -->
                Buyer Name<i class="fa  fa-caret-down"></i>
            </div>
            <div class="col-md-3 padding-left-none">Dispatch Date <i class="fa  fa-caret-down"></i></div>
            <div class="col-md-2 padding-left-none">Total CFT</div>
            <div class="col-md-3 padding-none"></div>
        </div>
    <!-- Table Head Ends Here -->

    <div class="table-data">
        @if(count($enquiries) > 0)
            @foreach($enquiries as $enquiry)
                {{--*/  $id = $enquiry->id /*--}}
                {{--*/  $buyerbussinessname = $enquiry->username /*--}}
                {{--*/  $dispatchdate = $enquiry->dispatch_date /*--}}
                {{--*/  $distance =  $enquiry->distance /*--}}
                {{--*/  $volume = '---' /*--}}
                {{--*/  $volume = $commoncomponent::getOfficeBuyerVolume($id) /*--}}
                {{--*/  $fromlocation = $commoncomponent::getCityName($enquiry->from_location_id) /*--}}
                {{--*/  $transid = $enquiry->transaction_id /*--}}
                {{--*/  $buyerid = $enquiry->buyer_id /*--}}
                {{--*/ $viewcount = $commoncomponent::viewCountForBuyer(Auth::User ()->id,$id,'relocationoffice_buyer_post_views') /*--}}
                {{--*/ $office_buyer_post_inventory_details = '' /*--}}
                                 <!-- Table Row Starts Here -->
                    <div class="table-row inner-block-bg">
                        <div class="col-md-4 padding-left-none">
                            <!-- <input type="checkbox" /><span class="lbl padding-8"></span> -->
                           {{$buyerbussinessname}}
                            <div class="red">
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                                <i class="fa fa-star"></i>
                            </div>
                        </div>
                        <div class="col-md-3 padding-left-none">{{$commoncomponent->checkAndGetDate($dispatchdate)}}</div>
                        <div class="col-md-2 padding-left-none">{{$volume}}</div>
                         {{--*/ $submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User ()->id,$id,$seller_post->id) /*--}}
            			 {{--*/ $submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted"  /*--}}
                        <div class="col-md-3 padding-none">
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
                            @include('relocationoffice.sellers.submit_quote', array(
                            'submittedquote' => $submittedquote,
                            'id' => $id
                            ))
                            </div>

                        <div class="col-md-12 show-data-div padding-top">
                            <div class="col-md-12 padding-none">
                                <div>
                                    <div class="clearfix"></div>

                                    <div class="col-md-12">
                                        <span class="data-head">Approximate Distance : {{$distance}} KM</span>
                                    </div>

                                    <div class="col-md-12 padding-none">
                                    <!-- Table Starts Here -->
                                        <div class="table-div table-style1">
                                            <div class="table-div table-style1 margin-none">
                                            
                                                <!-- Table Head Starts Here -->
                                                <div class="table-heading inner-block-bg">
                                                    <div class="col-md-8 padding-left-none">Particulars</div>
                                                    <div class="col-md-4 padding-left-none">No of Items</div>
                                                </div>
                                                <!-- Table Head Ends Here -->

                                                <div class="table-data">    
                                                    {{--*/ $office_buyer_post_inventory_particulars = $sellercomponent->getBuyerInventaryParticulars($id)  /*--}}
                                                    <!-- Table Row Starts Here -->
                                                    @foreach($office_buyer_post_inventory_particulars as $buyer_particulars)
                                                    <div class="table-row inner-block-bg">
                                                        <div class="col-md-8 padding-left-none">{{$buyer_particulars->office_particular_type}}</div>
                                                        <div class="col-md-4 padding-left-none">{{$buyer_particulars->number_of_items}}</div>
                                                    </div>
                                                    @endforeach
                                                    <!-- Table Row Ends Here -->
                                                </div>
                                            </div> 
                                            <div class="clearfix"></div>
                                        </div>  
                                    </div>
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