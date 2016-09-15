@inject('commoncomponent', 'App\Components\CommonComponent')
@inject('sellercomponent', 'App\Components\Relocation\RelocationSellerComponent')


<div class="table-div">

    <!-- Table Head Starts Here -->

    <div class="table-heading inner-block-bg">
        <div class="col-md-2 padding-left-none">
            <input type="checkbox" /><span class="lbl padding-8"></span>
            Buyer Name<i class="fa  fa-caret-down"></i>
        </div>
        <div class="col-md-2 padding-left-none">Dispatch Date <i class="fa  fa-caret-down"></i></div>
        <div class="col-md-1 padding-left-none">Post For<i class="fa  fa-caret-down"></i></div>
        <div class="col-md-2 padding-left-none">Property Type</div>
        <div class="col-md-1 padding-left-none">Volume CFT</div>
        <div class="col-md-1 padding-left-none">Vehicle Category</div>
        <div class="col-md-1 padding-left-none">Load Type</div>
        <div class="col-md-2 padding-none"></div>
    </div>

    <!-- Table Head Ends Here -->

    <div class="table-data">
        @if(count($enquiries) > 0)
        @foreach($enquiries as $enquiry)
        <?php
        $id = $enquiry->id;
        $buyerbussinessname = $enquiry->username;
        $dispatchdate = $enquiry->dispatch_date;
        $post = $enquiry->ratecard_type;
        $propertytype = $enquiry->property_type;
        $volume = $commoncomponent::getVolumeCft($id)+$commoncomponent::getCratingVolumeCft($id);
        $vehicletype =  $commoncomponent::getVehicleCategoryById($enquiry->lkp_vehicle_category_id);

        $fromlocation = $commoncomponent::getCityName($enquiry->from_location_id);
        $tolocation = $commoncomponent::getCityName($enquiry->to_location_id);

        $loadtype = $enquiry->load_category;
        $ratecardtypeId = $enquiry->lkp_post_ratecard_type_id;
        $destinationelevator = $enquiry->destination_elevator;
        $origineleavtor = $enquiry->origin_elevator;
        $originstorage = $enquiry->origin_storage;
        $originhandymanservice = $enquiry->origin_handyman_services;
        $insurance = $enquiry->insurance;
        $escort = $enquiry->escort;
        $mobility = $enquiry->mobility;
        $property= $enquiry->property;
        $setting_service = $enquiry->setting_service;
        $insurance_industry = $enquiry->insurance_industry;
        $origin_destination = $enquiry->origin_destination;
        $destination_handyman_services = $enquiry->destination_handyman_services;;
        $transid = $enquiry->transaction_id;
        $buyerid = $enquiry->buyer_id;

        if($propertytype!="" || $propertytype!=0) {
            $propType = $propertytype;
        } else {
            $propType = '---';
        }

        if($volume!="" && $volume!=0) {
            $vol = $volume;
        } else {
            $vol = '---';
        }

        if($vehicletype != "") {
            $vehicle = $vehicletype;
        } else {
            $vehicle = 'N/A';
        }

        if($loadtype!="") {
            $loadcat = $loadtype;
        } else {
            $loadcat = '---';
        }

        if (isset($destinationelevator) &&  $destinationelevator==1) {
            $destelev= 'checked=checked';
        } else {
            $destelev= 'disabled=disabled';
        }
        if (isset($destinationelevator) &&  $destinationelevator==0) {
            $destelevno= 'checked=checked';
        } else {
            $destelevno= 'disabled=disabled';
        }
        //Check origin and destination elevator and checkbox checkeing
        if (isset($origineleavtor) &&  $origineleavtor==1) {
            $origdev= 'checked=checked';
        } else {
            $origdev= 'disabled=disabled';
        }
        if (isset($origineleavtor) &&  $origineleavtor==0) {
            $origdevno= 'checked=checked';
        } else {
            $origdevno= 'disabled=disabled';
        }
        if (isset($originstorage) &&  $originstorage==1) {
            $origin_storage= 'checked=checked';
        } else {
            $origin_storage= 'disabled=disabled';
        }
        //checkbox checking value set or not
        if (isset($originstorage) &&  $originstorage==1) {
            $origin_storage= 'checked=checked';
        } else {
            $origin_storage= 'disabled=disabled';
        }
        if (isset($originhandymanservice) &&  $originhandymanservice==1) {
            $originhandy_manservice= 'checked=checked';
        } else {
            $originhandy_manservice= 'disabled=disabled';
        }
        if (isset($insurance) &&  $insurance==1) {
            $originhandy_insurance= 'checked=checked';
        } else {
            $originhandy_insurance= 'disabled=disabled';
        }
        if (isset($escort) &&  $escort==1) {
            $originhandy_escort= 'checked=checked';
        } else {
            $originhandy_escort= 'disabled=disabled';
        }
        if (isset($mobility) &&  $mobility==1) {
            $originhandy_mobility= 'checked=checked';
        } else {
            $originhandy_mobility= 'disabled=disabled';
        }
        if (isset($property) &&  $property==1) {
            $originhandy_property= 'checked=checked';
        } else {
            $originhandy_property= 'disabled=disabled';
        }
        if (isset($setting_service) &&  $setting_service==1) {
            $originhandy_setting_service = 'checked=checked';
        } else {
            $originhandy_setting_service= 'disabled=disabled';
        }
        if (isset($insurance_industry) &&  $insurance_industry==1) {
            $originhandy_insurance_industrye = 'checked=checked';
        } else {
            $originhandy_insurance_industrye= 'disabled=disabled';
        }

        if (isset($origin_destination) &&  $origin_destination==1) {
            $destdestination_storage = 'checked=checked';
        } else {
            $destdestination_storage= 'disabled=disabled';
        }
        if (isset($destination_handyman_services) &&  $destination_handyman_services==1) {
            $destination_handyman = 'checked=checked';
        } else {
            $destination_handyman = 'disabled=disabled';
        }?>


        {{--*/ $viewcount = $commoncomponent::viewCountForBuyer(Auth::User ()->id,$id,'relocation_buyer_post_views') /*--}}

        <?php  //Check Query for count no of room items in details section.
        $masterdata = '--';
        $bedroom1 = '--';
        $bedroom2 = '--';
        $bedroom3 = '--';
        $lobby = '--';
        $kitchen = '--';
        $bathroom = '--';
        $drawingroom = '--';
        $getrooms  = DB::table('relocation_buyer_post_inventory_particulars as rebip')
                ->leftjoin ( 'lkp_inventory_rooms as itr', 'itr.id', '=', 'rebip.lkp_inventory_room_id' )
                ->groupBy('rebip.lkp_inventory_room_id')
                ->where('rebip.buyer_post_id',$id)->select('lkp_inventory_room_id')->get();
        foreach($getrooms as $getroom){
            $getroomsdata  = DB::table('relocation_buyer_post_inventory_particulars as rebip')
                    ->leftjoin ( 'lkp_inventory_rooms as itr', 'itr.id', '=', 'rebip.lkp_inventory_room_id' )
                    ->where('itr.id',$getroom->lkp_inventory_room_id)
                    ->where('rebip.buyer_post_id',$id)
                    ->select('rebip.lkp_inventory_room_id',DB::raw('sum(rebip.number_of_items) AS totalItems'))
                    ->get();
            //echo "<pre>"; print_r($getroomsdata);
            foreach($getroomsdata as $getdata){
                //echo $getdata->lkp_inventory_room_id;
                if ($getdata->lkp_inventory_room_id == 1) {
                    $masterdata = $getdata->totalItems;
                }
                if ($getdata->lkp_inventory_room_id == 2) {
                    $bedroom1 = $getdata->totalItems;
                }
                if ($getdata->lkp_inventory_room_id == 3) {
                    $bedroom2 = $getdata->totalItems;
                }
                if ($getdata->lkp_inventory_room_id == 4) {
                    $bedroom3 = $getdata->totalItems;
                }
                if ($getdata->lkp_inventory_room_id == 5) {
                    $lobby = $getdata->totalItems;
                }
                if ($getdata->lkp_inventory_room_id == 6) {
                    $kitchen = $getdata->totalItems;
                }
                if ($getdata->lkp_inventory_room_id == 7) {
                    $bathroom = $getdata->totalItems;
                }
                if ($getdata->lkp_inventory_room_id == 8) {
                    $drawingroom = $getdata->totalItems;
                }
            }
        }
        ?>
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
            <input type="hidden" id="from_loc_{{$id}}" name="from_loc_{{$id}}" value="{{$enquiry->from_location_id}}"/>
            <input type="hidden" id="to_loc_{{$id}}" name="to_loc_{{$id}}" value="{{$enquiry->to_location_id}}"/>
            <input type="hidden" id="post_rate_{{$id}}" name="post_rate_{{$id}}" value="{{$enquiry->lkp_post_ratecard_type_id}}"/>
			<input type="hidden" id="property_type_{{$id}}" name="property_type_{{$id}}" value="{{$enquiry->property_type}}"/>
			<input type="hidden" id="load_category_{{$id}}" name="load_category_{{$id}}" value="{{$enquiry->load_category}}"/>
			<input type="hidden" id="car_size_{{$id}}" name="car_size_{{$id}}" value="{{$enquiry->lkp_vehicle_category_type_id}}"/>
            
            <div class='col-md-2 padding-left-none'>{{$commoncomponent->checkAndGetDate($dispatchdate)}}</div>
            <div class='col-md-1 padding-none'>{{$post}}</div>
            <div class='col-md-2 padding-none'>{{$propType}}</div>
            <div class='col-md-1 padding-none'>{{$vol}}<input type="hidden" id="enquiry_volume_{{$id}}" name="enquiry_volume_{{$id}}" value="{{$vol}}"/></div>
            <div class='col-md-1 padding-none'>{{$vehicle}}<input type="hidden" id="vehicle_type_{{$id}}" name="vehicle_type_{{$id}}" value="{{$enquiry->lkp_vehicle_category_id}}"/></div>
            <div class='col-md-1 padding-none'>{{$loadcat}}</div>
            {{--*/ $submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User ()->id,$id,$seller_post->id) /*--}}
            {{--*/ $submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted"  /*--}}
            <div class='col-md-2 padding-none'><button class='detailsslide-term btn red-btn pull-right submit-data' id ='{{$id}}'>{{$submitedquotetext}}</button></div>

            <div class='clearfix'></div>
            <div class='pull-right text-right'>
                <div class='info-links'>
                    <span class="detailsslide  underline_link" data-buyersearchlistid="{{ $buyerid }}_{{ $id }}"><span class="show_details">+</span><span class="hide_details">-</span> Details</span>
<!--                    <a class='show-data-link' id='{{$id}}'><span class='show-icon'>+</span><span class='hide-icon'>-</span> Details</a>-->
                    <a class="underline_link new_message" data-buyer-transaction="{{$transid}}" data-userid='{{ $buyerid }}' data-id="{{ Session::get('seller_post_item') }}" data-buyerquoteitemidforseller="{{ $id }}" href="#"><i class="fa fa-envelope-o"></i></a>
                </div>
            </div>
            <div class='col-md-12  padding-none padding-top term_quote_details_{{$id}}' style='display:none'>
                @include('relocation.sellers.submit_quote', array(
                    'submittedquote' => $submittedquote,
                    'id' => $id,
                    'ratecard_type' => $ratecardtypeId,

                ))
            </div>

            <div class='col-md-12 show-data-div padding-top quote_details_1_{{ $buyerid }}_{{ $id }}' id='spot_transaction_details_view_{{$id}}'>
                <div class='margin-top'>

                        <h3>
                            <i class="fa fa-map-marker"></i> {{$fromlocation}} to {{$tolocation}}
                            <span class="close-icon">x</span>
                        </h3>
                    @if($ratecardtypeId == 1)

                        <!-- Table Head Starts Here -->
                        <div class='table-heading inner-block-bg'>
                            <div class='col-md-2 padding-left-none'>Particulars</div>
                            <div class='col-md-1 padding-left-none'>Master Bedroom</div>
                            <div class='col-md-1 padding-left-none'>Bedroom 1</div>
                            <div class='col-md-1 padding-left-none'>Bedroom 2</div>
                            <div class='col-md-1 padding-left-none'>Bedroom 3</div>
                            <div class='col-md-2 padding-left-none'>Lobby / Garrage / Store Room</div>
                            <div class='col-md-1 padding-left-none'>Kitchen / Dinning</div>
                            <div class='col-md-1 padding-left-none'>Bathroom</div>
                            <div class='col-md-2 padding-left-none'>Living / Drawing Room</div>
                        </div>

                        <!-- Table Head Ends Here -->

                        <div class='table-data'>

                            <!-- Table Row Starts Here -->
                            <div class='table-row inner-block-bg'>
                                <div class='col-md-2 padding-left-none medium-text'>No of Items</div>
                                <div class='col-md-1 padding-left-none text-center'>{{$masterdata}}</div>
                                <div class='col-md-1 padding-left-none text-center'>{{$bedroom1}}</div>
                                <div class='col-md-1 padding-left-none text-center'>{{$bedroom2}}</div>
                                <div class='col-md-1 padding-left-none text-center'>{{$bedroom3}}</div>
                                <div class='col-md-2 padding-left-none text-center'>{{$lobby}}</div>
                                <div class='col-md-1 padding-left-none text-center'>{{$kitchen}}</div>
                                <div class='col-md-1 padding-left-none text-center'>{{$bathroom}}</div>
                                <div class='col-md-2 padding-left-none text-center'>{{$drawingroom}}</div>
                            </div>
                            <!-- Table Row Ends Here -->
                        </div>
                    @endif
                </div>
                @if($ratecardtypeId == 1)
                    <div class="margin-top">
                        <div class='col-md-4 form-control-fld margin-none'>
                            <div class='radio-block'>
                                <span class='padding-right-15'>Origin Elevator</span>
                                <input type='radio' {{$origdev}} name='elevator1' id='elevator1_a'>
                                <label for='elevator1_a'><span></span>Yes</label>

                                <input type='radio' {{$origdevno}} name='elevator1' id='elevator1_b'>
                                <label for='elevator1_b'><span></span>No</label>
                            </div>
                        </div>
                        <div class='col-md-4 form-control-fld margin-none'>
                            <div class='radio-block'>
                                <span class='padding-right-15'>Destination Elevator</span>
                                <input type='radio' {{$destelev}} name='elevator2' id='elevator2_a'>
                                <label for='elevator2_a'><span></span>Yes</label>

                                <input type='radio' {{$destelevno}} name='elevator2' id='elevator2_b'>
                                <label for='elevator2_b'><span></span>No</label>
                            </div>
                        </div>
                        <div class='clearfix'></div>

                        <div class='col-md-4 form-control-fld'>
                            <div class='radio-block'><input type='checkbox' {{$origin_storage}} > <span class='lbl padding-8'>Storage</span></div>
                            <div class='radio-block'><input type='checkbox' {{$originhandy_manservice}}> <span class='lbl padding-8'>Handyman Services</span></div>
                            <div class='radio-block'><input type='checkbox' {{$originhandy_insurance}}> <span class='lbl padding-8'>Insurance</span></div>
                            <div class='radio-block'><input type='checkbox' {{$originhandy_escort}}> <span class='lbl padding-8'>Escort</span></div>
                            <div class='radio-block'><input type='checkbox' {{$originhandy_mobility}}> <span class='lbl padding-8'>Mobility</span></div>
                            <div class='radio-block'><input type='checkbox' {{$originhandy_property}}> <span class='lbl padding-8'>Property</span></div>
                            <div class='radio-block'><input type='checkbox' {{$originhandy_setting_service}}> <span class='lbl padding-8'>Setting Service</span></div>
                            <div class='radio-block'><input type='checkbox' {{$originhandy_insurance_industrye}}> <span class='lbl padding-8'>Insurance Domestic</span></div>
                        </div>
                        <div class='col-md-4 form-control-fld'>
                            <div class='radio-block'><input type='checkbox' {{$destdestination_storage}}> <span class='lbl padding-8'>Storage</span></div>
                            <div class='radio-block'><input type='checkbox' {{$destination_handyman}}> <span class='lbl padding-8'>Handyman Services</span></div>
                        </div>
                    </div>
                </div>
            @endif
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