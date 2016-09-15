@inject('commoncomponent', 'App\Components\CommonComponent')
@inject('sellercomponent', 'App\Components\RelocationPet\RelocationPetSellerComponent')


<div class="table-div">

    <!-- Table Head Starts Here -->

    <div class="table-heading inner-block-bg">
        <div class="col-md-3 padding-left-none">
           
            Buyer Name<i class="fa  fa-caret-down"></i>
        </div>
        <div class="col-md-3 padding-left-none">Dispatch Date <i class="fa  fa-caret-down"></i></div>
        
        <div class="col-md-3 padding-left-none">Post For</div>
        
        <div class="col-md-3 padding-none"></div>
    </div>

    <!-- Table Head Ends Here -->

    <div class="table-data">
        @if(count($enquiries) > 0)
        @foreach($enquiries as $enquiry)
        <?php
        $id = $enquiry->id;
        $buyerbussinessname = $enquiry->username;
        $dispatchdate = $enquiry->dispatch_date;
        //$post = $enquiry->ratecard_type;
        $pettype = $commoncomponent::getPetType($enquiry->lkp_pet_type_id);
        $cagetype = $commoncomponent::getCageType($enquiry->lkp_cage_type_id);
        $cageweight = $commoncomponent::getCageWeight($enquiry->lkp_cage_type_id);
        $breedtype = $commoncomponent::getBreedType($enquiry->lkp_breed_type_id);
        //$volume = $commoncomponent::getVolumeCft($id);
        
        $fromlocation = $commoncomponent::getCityName($enquiry->from_location_id);
        $tolocation = $commoncomponent::getCityName($enquiry->to_location_id);
        //$loadtype = $enquiry->load_category;
        $transid = $enquiry->transaction_id;
        $buyerid = $enquiry->buyer_id;

        if($pettype!="" || $pettype!=0) {
            $pettype = $pettype;
        } else {
            $pettype = '---';
        }

        if($cagetype!="" || $cagetype!=0) {
            $cagetype = $cagetype;
        } else {
            $cagetype = 'NA';
        }

?>

        {{--*/ $viewcount = $commoncomponent::viewCountForBuyer(Auth::User ()->id,$id,'relocationpet_buyer_post_views') /*--}}
                <!-- Table Row Starts Here -->
        <div class='table-row inner-block-bg'>
            <div class='col-md-3 padding-left-none'>
                <span class='lbl padding-8'></span>
                {{$buyerbussinessname}}
                <div class='red'>
                    <i class='fa fa-star'></i>
                    <i class='fa fa-star'></i>
                    <i class='fa fa-star'></i>
                </div>
            </div>
            <input type="hidden" id="from_loc_{{$id}}" name="from_loc_{{$id}}" value="{{$enquiry->from_location_id}}"/>
            <input type="hidden" id="to_loc_{{$id}}" name="to_loc_{{$id}}" value="{{$enquiry->to_location_id}}"/>
        
            <div class='col-md-3 padding-left-none'>{{$commoncomponent->checkAndGetDate($dispatchdate)}}</div>
            
            <div class='col-md-3 padding-none'>{{$pettype}}</div>
            
            {{--*/ $submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User ()->id,$id,$seller_post->id) /*--}}
            {{--*/ $submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted"  /*--}}
            <div class='col-md-3 padding-none'><button class='detailsslide-term btn red-btn pull-right submit-data' id ='{{$id}}'>{{$submitedquotetext}}</button></div>

            <div class='clearfix'></div>
            <div class='pull-right text-right'>
                <div class='info-links'>
                    <span class='detailsslide underline_link' id='{{$id}}' data-buyersearchlistid='{{$buyerid}}_{{$id}}'>
                        <span class='show_details'>+</span><span class='hide_details'>-</span> Details</span>
                    <a class="underline_link new_message" data-buyer-transaction="{{$transid}}" data-userid='{{ $buyerid }}' data-id="{{ Session::get('seller_post_item') }}" data-buyerquoteitemidforseller="{{ $id }}" href="#"><i class="fa fa-envelope-o"></i></a>
                </div>
            </div>
            <div class='col-md-12  padding-none padding-top term_quote_details_{{$id}}' style='display:none'>
                @include('relocationpet.sellers.submit_quote', array(
                    'submittedquote' => $submittedquote,
                    'id' => $id,
                ))
            </div>

            <div class='col-md-12 show-data-div  padding-top spot_transaction_details_view_list quote_details_1_{{$buyerid}}_{{$id}}'  id='spot_transaction_details_view_{{$id}}'>
                <div class='margin-top'>

                        <h3>
                            <i class="fa fa-map-marker"></i> {{$fromlocation}} to {{$tolocation}}
                            <span class="close-icon">x</span>
                        </h3>
                    
                    <div class='table-heading inner-block-bg'>
                        <div class='col-md-3 padding-left-none'>Pet Type</div>
                        <div class='col-md-3 padding-left-none'>Breed</div>
                        <div class='col-md-3 padding-left-none'>Cage Type</div>
                        <div class='col-md-3 padding-left-none'>Cage Weight</div>
                    </div>
                    <div class='table-data'>

                            <!-- Table Row Starts Here -->
                            <div class='table-row inner-block-bg'>
                                <div class='col-md-3 padding-left-none '>{{$pettype}}</div>
                                <div class='col-md-3 padding-left-none '>@if($breedtype!=''){{$breedtype}}@else NA @endif</div>
                                <div class='col-md-3 padding-left-none '>{{$cagetype}}</div>
                                <div class='col-md-3 padding-left-none '>{{$cageweight}} KGs</div>
                                
                            </div>
                            <!-- Table Row Ends Here -->
                        </div>
                </div>
                
                </div>
            
        </div>
        @endforeach
        @else
            <div class="col-md-12 padding-left-none padding-right-none table-data">
                <div class="table-row inner-block-bg text-center"><span class="nocontent">No Records Found</span></div>
            </div>
        @endif
                
    </div>
</div>