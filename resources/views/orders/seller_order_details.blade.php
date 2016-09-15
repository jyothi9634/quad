@inject('messagesComponent', 'App\Components\MessagesComponent')
@inject('commonComponent', 'App\Components\CommonComponent')
@extends('app') @section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
{{--*/ $countMessages = 0 /*--}}
{{--*/ $countMessages = $messagesComponent->getPerticularMessageDetailsCount(null,$orderDetails->orderid) /*--}}

{{--*/ $serviceId = Session::get('service_id'); /*--}}
{{--*/ $docs_seller    =   $commonComponent->getGsaDocuments(3,$serviceId,0); /*--}}
{{--*/ $docCount = count($docs_seller) /*--}}

<div class="main">
	<div class="container">
		<span class="pull-left"><h1 class="page-title">Order - {!! $orderDetails->order_no !!}</h1></span>
		<a onclick="return checkSession({{$serviceId}},'/createseller');" href="#"><button class="btn post-btn pull-right">+ Post</button></a>



        
		<div class="clearfix"></div>
		<div class="col-md-12 padding-none">
                    <div class="main-inner">
				<!-- Right Section Starts Here -->
                        <div class="main-right">
                                <div class="inner-block-bg inner-block-bg1">
                                        <div class="col-md-12 tab-modal-head">
                                                <h3>
                                                        <i class="fa fa-map-marker"></i>
                                                        @if(isset($orderDetails->from_city)) {!!
                                                        $orderDetails->from_city !!} @else &nbsp; @endif
                                                        @if(isset($orderDetails->to_city)) to {!! $orderDetails->to_city
                                                        !!} @else &nbsp; @endif
                                                </h3>
                                        </div>
                                        <div class="col-md-8 data-div">
                                                <div class="col-md-4 padding-left-none data-fld">
                                                        <span class="data-head">
                                                            @if(Session::get('service_id')==ROAD_FTL)
                                                            Full
                                                            @elseif(Session::get('service_id')==RELOCATION_DOMESTIC)
                                                            Relocation Domestic
                                                            @elseif(Session::get('service_id')==ROAD_TRUCK_LEASE)
                                                            TruckLease
                                                            @elseif(Session::get('service_id')==RELOCATION_PET_MOVE)
                                                            Relocation Pet Move
                                                            @elseif(Session::get('service_id')==RELOCATION_INTERNATIONAL)
                                                            Relocation International
                                                            @elseif(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
                                                            Relocation Global Mobility
                                                            @endif
                                                            @if(Session::get('service_id')!=RELOCATION_OFFICE_MOVE)
                                                            Post No
                                                            @endif
                                                            </span>

                                                    @if(Session::get('service_id')!=RELOCATION_OFFICE_MOVE)                                                            
                                                        <span class="data-value">
                                                        @if(isset($orderDetails->trans_id))
                                                            {!!$orderDetails->trans_id !!}
                                                        @else
                                                             @if(isset($post_items[0]->transaction_id))
                                                                {!!$post_items[0]->transaction_id !!}
                                                             @endif
                                                        @endif</span>
                                                    @endif

                                                </div>
                                                <div class="col-md-4 padding-left-none data-fld">
                                                @if(Session::get('service_id')==RELOCATION_DOMESTIC)
                                                        <span class="data-head">Post Type</span> <span
                                                                class="data-value">
                                                                @if(isset($orderDetails->lkp_post_ratecard_type_id))
                                                                @if($orderDetails->lkp_post_ratecard_type_id==1)
                                                                HHG
                                                                @else
                                                                Vehicle
                                                                @endif
                                                                @else
                                                                @if(isset($post_items[0]->lkp_post_ratecard_type))
                                                                @if($post_items[0]->lkp_post_ratecard_type==1)
                                                                HHG
                                                                @else
                                                                Vehicle
                                                                @endif
                                                                @endif
                                                                @endif
                                                                </span>
                                                @else
                                                    @if(isset($orderDetails->vehicle_type))
                                                        <span class="data-head">Vehicle Type</span> <span
                                                                class="data-value"> {!!
                                                                $orderDetails->vehicle_type !!}  </span>
                                                @endif
                                                @endif
                                                </div>
                                                <div class="col-md-4 padding-left-none data-fld">
                                                @if(Session::get('service_id')==RELOCATION_DOMESTIC)
                                                @if(isset($orderDetails->lkp_post_ratecard_type_id))
                                                @if($orderDetails->lkp_post_ratecard_type_id==1)
                                                        <span class="data-head">Load Type</span> <span
                                                                class="data-value">
                                                                @if(isset($orderDetails->lkp_post_ratecard_type_id))
                                                                @if($orderDetails->lkp_load_category_id==1)
                                                                Full Load
                                                                @else
                                                                Part Load
                                                                @endif
                                                                @endif
                                                                </span>


                                                @else
                                                        <span class="data-head">Vehicle Catagoery</span> <span
                                                                class="data-value">
                                                                @if(isset($orderDetails->lkp_vehicle_category_id))
                                                                @if($orderDetails->lkp_vehicle_category_id==1)
                                                                Car
                                                                @else
                                                                Bike / Scooter / Scooty
                                                                @endif
                                                                @endif

                                                                </span>
                                                @endif
                                                @else
                                                @if(isset($post_items[0]->lkp_post_ratecard_type))
                                                @if($post_items[0]->lkp_post_ratecard_type==1)
                                                <span class="data-head">Load Type</span> <span
                                                                class="data-value">
                                                                @if(isset($post_items[0]->lkp_post_ratecard_type))
                                                                @if($post_items[0]->lkp_load_type_id==1)
                                                                Full Load
                                                                @else
                                                                Part Load
                                                                @endif
                                                                @endif
                                                                </span>
                                                @else
                                                <span class="data-head">Vehicle Catagoery</span> <span
                                                                class="data-value">
                                                                @if(isset($post_items[0]->lkp_vehicle_category_id))
                                                                @if($post_items[0]->lkp_vehicle_category_id==1)
                                                                Car
                                                                @else
                                                                Bike / Scooter / Scooty
                                                                @endif
                                                                @endif

                                                                </span>
                                                @endif
                                                @endif
                                                @endif
                                                @else
                                                @if(isset($orderDetails->load_type))
                                                        <span class="data-head">Load Type</span> <span
                                                                class="data-value"> {!!
                                                                $orderDetails->load_type !!} </span>
                                                @endif
                                                @endif
                                                </div>

                                                <div class="clearfix"></div>

                                                <div class="col-md-4 padding-left-none data-fld">
                                                        @if(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
                                                             <span class="data-head">Date</span> 
                                                             <span class="data-value">
                                                               @if(isset($orderDetails->dispatch_date)
                                                                    && $orderDetails->dispatch_date != 01/01/1970)
                                                                        {{date("d/m/Y", strtotime($orderDetails->dispatch_date))}}
                                                                @else 
                                                                        &nbsp; 
                                                                @endif    
                                                                 </span>
                                                        @else
                                                            <span class="data-head">Dispatch Date</span> 
                                                            <span class="data-value">
                                                                @if(isset($orderDetails->buyer_consignment_pick_up_date)
                                                                && $orderDetails->buyer_consignment_pick_up_date != 01/01/1970)
                                                                    {{date("d/m/Y", strtotime($orderDetails->buyer_consignment_pick_up_date))}}
                                                                @else 
                                                                    &nbsp; 
                                                                @endif
                                                            </span>
                                                        @endif
                                                        

                                                            
                                                </div>
                                                @if(Session::get('service_id')==RELOCATION_INTERNATIONAL)
                                                    <div class="col-md-4 padding-left-none data-fld">
                                                        <span class="data-head">Delivery Date</span>
                                                    <span class="data-result">
                                                        {{date("d/m/Y", strtotime($orderDetails->delivery_date))}}
                                                    </span>
                                                    </div>
                                                @endif
                                                 @if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY)
                                                <div class="col-md-4 padding-left-none data-fld">
                                                        <span class="data-head">Consignee</span> <span
                                                                class="data-value">
                                                                @if(isset($orderDetails->buyer_consignee_name)) {!!
                                                                $orderDetails->buyer_consignee_name !!} @else NA @endif</span>
                                                </div>
                                                @endif
                                                @if(Session::get('service_id')==RELOCATION_PET_MOVE)
                                                <div class="col-md-4 padding-left-none data-fld">
                                                    <span class="data-head">Pet Type</span> <span
                                                                class="data-value">
                                                        @if(isset($orderDetails->lkp_pet_type_id) && $orderDetails->lkp_pet_type_id!='0') {!!
                                                                $commonComponent->getPetType($orderDetails->lkp_pet_type_id) !!} @else NA @endif</span>
                                                </div>
                                                <div class="col-md-4 padding-left-none data-fld">
                                                    <span class="data-head">Breed</span> <span
                                                                class="data-value">                                                         
                                                        @if(isset($orderDetails->lkp_breed_type_id) && $orderDetails->lkp_breed_type_id!='0') {!!
                                                                $commonComponent->getBreedType($orderDetails->lkp_breed_type_id) !!} @else NA @endif</span>
                                                </div>
                                                <div class="col-md-4 padding-left-none data-fld">
                                                    <span class="data-head">Cage Type</span> <span
                                                                class="data-value">
                                                        @if(isset($orderDetails->lkp_cage_type_id) && $orderDetails->lkp_cage_type_id!='0') {!!
                                                                $commonComponent->getCageType($orderDetails->lkp_cage_type_id) !!} @else NA @endif</span>
                                                </div>
                                                @endif


                                                @if(Session::get('service_id')!=RELOCATION_INTERNATIONAL)
                                                    <div class="col-md-4 padding-left-none data-fld">
                                                        @if(Session::get('service_id')==RELOCATION_DOMESTIC)
                                                            <span class="data-head">Volume</span>
                                                            <span class="data-value">
                                                                {{--*/ $volume_total = $commonComponent->getVolumeCft($orderDetails->buyer_quote_id)+$commonComponent->getCratingVolumeCft($orderDetails->buyer_quote_id) /*--}}
                                                                @if($volume_total!=0)
                                                                    {{$volume_total}}
                                                                @else
                                                                    @if(isset($post_items[0]->volume))
                                                                        {{$post_items[0]->volume}}
                                                                    @else
                                                                        N/A 
                                                                    @endif  
                                                                @endif
                                                            </span>
                                                        @else
                                                            @if(Session::get('service_id')!=RELOCATION_PET_MOVE && Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY &&  Session::get('service_id')!= RELOCATION_OFFICE_MOVE)
                                                                <span class="data-head">Quantity</span>
                                                                <span class="data-value">
                                                                    @if(isset($orderDetails->quantity) && $orderDetails->quantity!='')
                                                                        {!! $orderDetails->quantity !!} @else  N/A 
                                                                    @endif

                                                                    @if(isset($orderDetails->units) && $orderDetails->units!='')
                                                                        {!! $orderDetails->units !!}
                                                                    @endif
                                                                </span>
                                                            @endif

<!--                                                             @if( Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                {{--*/ $paymentMode = $orderDetails->payment_mode /*--}}

                                                                    <span class="data-head">Payment Terms</span>
                                                                    <span class="data-value">@if ($paymentMode == 'Advance')
                                                               <i class="fa fa-credit-card"></i> Online Payment
                                                                @else
                                                                <i class="fa fa-rupee"></i> {!! $orderDetails->payment_mode !!}
                                                                @endif
                                                                    </span>
                                                            </div>
                                                            @endif -->


                                                        @endif
                                                    </div>

                                                @endif
                                                @if(Session::get('service_id')==RELOCATION_INTERNATIONAL)
                                                 <div class="col-md-4 padding-left-none data-fld">
                                                    <span class="data-head">Consignor</span>
                                                    <span class="data-value">
                                                    @if(isset($orderDetails->buyer_consignor_name)) {!!
                                                        $orderDetails->buyer_consignor_name !!} @else &nbsp; @endif
                                                    </span>
                                                </div>
                                                @if($orderDetails->lkp_order_type_id == 1)
                                                 @if($orderDetails->lkp_international_type_id == 1)
                                                 <div class="col-md-4 padding-left-none data-fld">
                                                    <span class="data-head">No of Cartons</span>
                                                    <span class="data-value">
                                                        {{$commonComponent::getCartonsTotal($orderDetails->buyer_quote_id)}}
                                                    </span>
                                                </div>
                                                 @endif
                                                 @endif
                                                @endif

                                                @if($orderDetails->lkp_order_type_id == 2 && Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY)
                                                <div class="col-md-12">
                                                      <div class="table-div table-style1 padding-none">

                                                              <!-- Table Head Starts Here -->
                                                              <div class="table-heading inner-block-bg">
                                                                      <div class="col-md-8 padding-left-none">No of Moves</div>
                                                                      <div class="col-md-4 padding-left-none">Average KG/Move</div>
                                                              </div>
                                                              <!-- Table Head Ends Here -->

                                                              <div class="table-data">
                                                                      <!-- Table Row Starts Here -->
                                                                      @foreach($post_items as $buyerall_item)
                                                                       <div class="table-row inner-block-bg">
                                                                              <div class="col-md-8 padding-left-none">{{$buyerall_item->number_loads}}</div>
                                                                              <div class="col-md-4 padding-left-none">
                                                                                  {{$buyerall_item->avg_kg_per_move}}
                                                                              </div>
                                                                      </div>
                                                                      @endforeach
                                                                      <!-- Table Row Ends Here -->
                                                              </div>

                                                      </div>
                                                  </div>
                                                @endif

                                                @if(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
                                                    @include('relocationglobal.buyers._buyerserviceslist',['buyerpost_id' => $orderDetails->buyer_quote_id])
                                                @endif



                                                <div class="clearfix"></div>
                                                

                                                @if($orderDetails->lkp_order_type_id==1)
                                                <div class="col-md-4 padding-left-none data-fld">
                                                    {{--*/ $paymentMode = $orderDetails->payment_mode /*--}}

                                                        <span class="data-head">Payment Terms</span>
                                                        <span class="data-value">@if ($paymentMode == 'Advance')
                                                   <i class="fa fa-credit-card"></i> Online Payment
                                                    @else
                                                    <i class="fa fa-rupee"></i> {!! $orderDetails->payment_mode !!}
                                                    @endif
                                                        </span>
                                                </div>
                                                @endif

                                                @if(Session::get('service_id')!=RELOCATION_PET_MOVE && Session::get('service_id')!=RELOCATION_INTERNATIONAL && Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY && Session::get('service_id')!=RELOCATION_OFFICE_MOVE)
                                                <div class="col-md-4 padding-left-none data-fld">
                                                        <span class="data-head">No of Loads</span>
                                                        <span class="data-value">
                                                            @if(isset($orderDetails->number_loads) && $orderDetails->number_loads!='' && $orderDetails->number_loads!='undefined')
                                                                 {!! $orderDetails->number_loads !!} @else  N/A
                                                                  @endif
                                                        </span>
                                                </div>
                                                @endif
                                                @if(Session::get('service_id')!=RELOCATION_INTERNATIONAL && Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY)
                                                    <div class="col-md-4 padding-left-none data-fld">
                                                        <span class="data-head">Delivery Date</span>
                                                    <span class="data-result">
                                                        @if($orderDetails->delivery_date =="" || $orderDetails->delivery_date=="0000-00-00")
                                                            N/A
                                                        @else
                                                            <i class="fa fa-calendar-o"></i>
                                                            {{date("d/m/Y", strtotime($orderDetails->delivery_date))}}
                                                        @endif

                                                    </span>
                                                    </div>
                                                @endif

                                                @if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY)
                                                <div class="col-md-10 padding-none pull-left">
                                                        <div class="info-links">
                                                                <a href="{{ url('/getmessagedetails/0/'.$orderDetails->orderid.'/0')}}"><i class="fa fa-envelope-o"></i> Messages<span class="badge">{!! $countMessages !!}</span></a> <a
                                                                        href="#"><i class="fa fa-hourglass-1"></i> Status<span class="badge">0</span></a> <a
                                                                        href="#" data-toggle="modal" data-target="#ftl-doc-popup"><i class="fa fa-file-text-o"></i> Documents<span class="badge">{{$docCount}}</span></a>
                                                        </div>
                                                </div>
                                                @endif

                                        </div>
                                        <div class="col-md-4 order-detail-price-block">
                                                <div>
                     <span class="vendor-name">{{$orderDetails->username}}</span>
                                                </div>

                                                <div>
                                                        <span class="data-head">Total Price</span> <span
                                                                class="data-value big-value">Rs. @if($orderDetails->inv_price) {!!
                                                                $commonComponent->getPriceType($orderDetails->inv_price) !!}
                                                                @else{!! $commonComponent->getPriceType($orderDetails->price) !!} @endif </span>
                                                </div>




                                                <!-- -status bar check variables and conditions -->
                            {{--*/ $SellerPickupDate 	= 		$orderDetails->seller_pickup_date; /*--}}
                            {{--*/ $buyerPickupDate 	=  		$orderDetails->buyer_consignment_pick_up_date; /*--}}
                            {{--*/ $SellerDeliveryDate 	=  		$orderDetails->seller_delivery_date; /*--}}
                            {{--*/ $DeliveryDate 		=  		$orderDetails->delivery_date; /*--}}
                            {{--*/ $DispatchDate 		=  		$orderDetails->dispatch_date; /*--}}
                            {{--*/ $current_date_seller	=  		date("Y-m-d");  /*--}}
                            {{--*/ $str				=		'' /*--}}
                            {{--*/ $strdelivery				=		'' /*--}}

                            @if($SellerPickupDate == '0000-00-00 00:00:00' && $SellerDeliveryDate == '0000-00-00 00:00:00')
                                @if($current_date_seller < $DispatchDate)
                                {{--*/ $str				=		'' /*--}}
                                {{--*/ $strdelivery		=		'' /*--}}
                                @elseif($current_date_seller > $DispatchDate)
                                {{--*/ $str				=		'<div class="status-bar-left"></div>' /*--}}
                                {{--*/ $strdelivery		=		'' /*--}}
                                @elseif($current_date_seller == $DispatchDate)
                                {{--*/ $str				=		'' /*--}}
                                {{--*/ $strdelivery		=		'' /*--}}
                                @endif
                            @elseif($SellerPickupDate != '0000-00-00 00:00:00' && $SellerDeliveryDate == '0000-00-00 00:00:00')
                                @if($SellerPickupDate <= $DispatchDate." 00:00:00")
                                {{--*/ $sellerpickupcolor=		'green' /*--}}
                                {{--*/ $str				=		'<div class="status-bar-left-green"></div>'  /*--}}
                                {{--*/ $strdelivery		=		'' /*--}}
                                @elseif($SellerPickupDate > $DispatchDate." 00:00:00")
                                {{--*/ $sellerpickupcolor=		'red' /*--}}
                                {{--*/ $str				=		'<div class="status-bar-left"></div>'    /*--}}
                                {{--*/ $strdelivery		=		'' /*--}}
                                @endif

                                @if($current_date_seller < $DeliveryDate)
                                {{--*/ $strdelivery		=		'' /*--}}
                                @elseif($current_date_seller > $DeliveryDate)
                                {{--*/ $strdelivery		=		'<div class="status-bar-right-red"></div>'  /*--}}
                                @elseif($current_date_seller == $DeliveryDate)
                                {{--*/ $strdelivery		=		'' /*--}}
                                @endif
                            @else
                                         @if($DispatchDate == $current_date_seller)
                                                                        {{--*/ $sellerpickupcolor=		'green' /*--}}
                                {{--*/ $str				=		'<div class="status-bar-left-green"></div>' /*--}}
                                {{--*/ $strdelivery		=		'' /*--}}
                                @elseif($SellerPickupDate <= $DispatchDate." 00:00:00")
                                {{--*/ $sellerpickupcolor=		'green' /*--}}
                                {{--*/ $str				=		'<div class="status-bar-right-full"></div>' /*--}}
                                {{--*/ $strdelivery		=		'' /*--}}
                                @elseif($SellerPickupDate > $DispatchDate." 00:00:00")
                                {{--*/ $sellerpickupcolor=		'red' /*--}}
                                {{--*/ $str				=		'<div class="status-bar-left"></div>' /*--}}
                                {{--*/ $strdelivery		=		'' /*--}}
                                @endif

                                @if($SellerDeliveryDate <= $DeliveryDate." 00:00:00")
                                        @if($sellerpickupcolor!="")
                                        {{--*/ $strdelivery		=		'<div class="status-bar-right"></div>' /*--}}
                                        @endif
                                @elseif($SellerDeliveryDate > $DeliveryDate." 00:00:00")
                                        @if($sellerpickupcolor!="")
                                        {{--*/ $strdelivery='<div class="status-bar-right-red"></div>'  /*--}}
                                        @endif
                                @endif
                            @endif

                            <div class="col-md-4 padding-none">
                                <span class="data-head">Status</span>
                                
                            @if(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY) 
                                <div class="status-block1 pull-left">
                            @else
                                <div class="status-bar">
                            @endif
                                        <div class="status-bar">
                                                {!! $str !!}{!! $strdelivery !!}
                                                @if(Session::get('service_id') != RELOCATION_GLOBAL_MOBILITY)                                                            
                                                                  <span class="status-text">{!! $orderDetails->order_status !!}</span>
                                                            @else
                                                                  @if($orderDetails->order_status == 'Pickup due')
                                                                  <span class="status-text">Commencement Due</span>
                                                                  @elseif($orderDetails->order_status=="Consignment pickup")
                                                                  <span class="status-text">Commencement Started</span>
                                                                  @else
                                                                  <span class="status-text">Commencement Completed</span>
                                                                  @endif
                                                           @endif
                                        </div>
                                </div>
                            </div>






                                        <!-- 	<div class="col-md-4 padding-none">
                                                        <span class="data-head">Status</span>
                                                        <div class="status-bar">
                                                                <div class="status-bar-left"></div>
                                                                <span class="status-text">@if(isset($orderDetails->order_status))
                                                                        {!! $orderDetails->order_status !!} @else &nbsp; @endif</span>
                                                        </div>

                                                </div>-->
                                        </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="col-md-12 inner-block-bg inner-block-bg1">
                                        <div class="col-md-4 padding-none">
                                                <div class="center-block pull-left">
                                                        <i class="fa fa-print"></i> <span>Print Order</span>
                                                </div>
                                        </div>

                                        <div class="col-md-4 padding-none">
                                                <div class="center-block">
                                                        <i class="fa fa-file-text-o"></i> <span>Email Invoice</span>
                                                </div>
                                        </div>

                                        <div class="col-md-4 padding-none">
                                                <div class="center-block pull-right">
                                                        <i class="fa fa-phone"></i> <span>Contact Us</span>
                                                </div>
                                        </div>
                                </div>

                                <div class="clearfix"></div>

                                <div class="accordian-blocks">
                                        <div class="inner-block-bg inner-block-bg1 detail-head">Order No.
                                                {!!$orderDetails->order_no !!}</div>
                                        <div class="detail-data" style="display: none;">
                                                <div class="col-md-12 margin-top margin-bottom">
                                                        @if(!empty($vehicles))
                                                        <h4 class="data-head">Vehicle Placement</h4>
                                                        <div class="col-md-12 padding-left-none data-fld">
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        @foreach($vehicles as $vehicle) <span class="data-head">Vehicle
                                                                                No</span> <span class="data-value">{{$vehicle->vehicle_no}}</span>
                                                                        @endforeach
                                                                </div>

                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        @foreach($vehicles as $vehicle) <span class="data-head">Driver
                                                                                Name</span> <span class="data-value">{{$vehicle->driver_name}}</span>
                                                                        @endforeach
                                                                </div>
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        @foreach($vehicles as $vehicle) <span class="data-head">Driver
                                                                                Mobile</span> <span class="data-value">{{$vehicle->mobile}}</span>
                                                                        @endforeach
                                                                </div>
                                                        </div>
                                                        @endif
                                                        <div class="clearfix"></div>
                                                        @if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY)
                                                        <div class="col-md-12 padding-left-none data-fld">
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Pickup Date</span> <span
                                                                                class="data-value">@if($orderDetails->seller_pickup_lr_date!=
                                                                                '0000-00-00 00:00:00') {{date("d/m/Y",
                                                                                strtotime($orderDetails->seller_pickup_date))}} @else{!! '-'
                                                                                !!} @endif</span>
                                                                </div>
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">
                                                                            @if(Session::get('service_id')==RELOCATION_PET_MOVE)
                                                                                AWB
                                                                            @else
                                                                                LR
                                                                            @endif
                                                                            Number</span> <span
                                                                                class="data-value">@if($orderDetails->seller_pickup_lr_number)
                                                                                {!! $orderDetails->seller_pickup_lr_number !!} @else{!! '-'
                                                                                !!} @endif</span>
                                                                </div>

                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Transporter bill no.</span> <span
                                                                                class="data-value">@if($orderDetails->seller_pickup_transport_bill_no)
                                                                                {!! $orderDetails->seller_pickup_transport_bill_no !!}
                                                                                @else{!! '-' !!} @endif</span>
                                                                </div>
                                                        </div>
                                                        @endif



                                                      @if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY)
                                                        <div class="col-md-12 padding-left-none data-fld">                                                                
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Delivery on</span> <span
                                                                                class="data-value">@if($orderDetails->seller_delivery_date !=
                                                                                '0000-00-00 00:00:00') {{date("d/m/Y",
                                                                                strtotime($orderDetails->seller_delivery_date))}} @else{!!
                                                                                '-' !!} @endif</span>
                                                                </div>
                                                                
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">
                                                                            @if(Session::get('service_id')==RELOCATION_PET_MOVE)
                                                                                Recipient Name
                                                                            @else
                                                                                Acknowledge by
                                                                            @endif

                                                                            </span> <span
                                                                                class="data-value">@if($orderDetails->buyer_consignee_name)
                                                                                {!! $orderDetails->buyer_consignee_name !!} @else{!! '-' !!}
                                                                                @endif</span>
                                                                </div>

                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Mobile</span> <span class="data-value">@if($orderDetails->buyer_consignee_mobile)
                                                                                {!! $orderDetails->buyer_consignee_mobile !!} @else{!! '-'
                                                                                !!} @endif</span>
                                                                </div>
                                                        </div>
                                                      @endif
                                                        
                                                        <div class="col-md-12 padding-left-none data-fld">
                                                        @if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY)
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Consignment Value</span> <span
                                                                                class="data-value">@if($orderDetails->buyer_consignment_value)
                                                            {!! number_format($orderDetails->buyer_consignment_value,2) !!}
                                                            @else{!! '-' !!}
                                                            @endif</span>
                                                                  </div>
                                                         @endif
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Source address</span> <span
                                                                                class="data-value">@if($orderDetails->buyer_consignor_address)
                                                                  {!! $orderDetails->buyer_consignor_address !!}
                                                                  @else{!! '-' !!}
                                                                  @endif</span>
                                                                </div>

                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Pin code</span> <span
                                                                                class="data-value">@if($orderDetails->buyer_consignor_pincode)
                                                                  {!! $orderDetails->buyer_consignor_pincode !!}
                                                                  @else{!! '-' !!}
                                                                  @endif</span>
                                                                </div>
                                                        </div>




                                                      @if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY)

                                                        <div class="col-md-12 padding-left-none data-fld">
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Consignee / Destination address</span> <span
                                                                                class="data-value">@if($orderDetails->buyer_consignee_address)
                                                                  {!! $orderDetails->buyer_consignee_address !!}
                                                                  @else{!! '-' !!}
                                                                  @endif</span>
                                                                </div>
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Pincode</span> <span
                                                                                class="data-value">@if($orderDetails->buyer_consignee_pincode)
                                                                  {!! $orderDetails->buyer_consignee_pincode !!}
                                                                  @else{!! '-' !!}
                                                                  @endif</span>
                                                                </div>

                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Consignee Mobile</span> <span
                                                                                class="data-value">@if($orderDetails->buyer_consignee_mobile)
                                                                  {!! $orderDetails->buyer_consignee_mobile !!}
                                                                  @else{!! '-' !!}
                                                                  @endif</span>
                                                                </div>
                                                        </div>
                                                      @endif







                                                </div>
                                                <div class="clearfix"></div>
                                        </div>
                                </div>
                                @if(isset($vehicles))
                                @if(count($vehicles) > 0)
                                <div class="accordian-blocks">
                                        <div class="inner-block-bg inner-block-bg1 detail-head">Vehicle Details</div>
                                        <div class="detail-data">
                                        <div class="col-md-12 margin-top margin-bottom">
                                                <div class="col-md-6 padding-left-none data-fld">
                                                        @foreach($vehicles as $vehicle)
                                                        <?php
                                                                //echo "<pre>";
                                                                //print_r($vehicle);
                                                        ?>
                                                                <div class="col-md-12 padding-left-none data-fld">
                                                                        <div class="col-md-4"><strong>{{$vehicle->vehicle_no}}</strong></div>
                                                                        <div class="col-md-8 padding-none text-right">
                                                                            @include('partials._tracking')
                                                                        </div>
                                                                        <div class="clearfix"></div>
                                                                </div>
                                                        @endforeach
                                                </div>
                                        </div>
                                        <!--div class="col-md-12 padding-left-none data-fld">
                                                <div id="gmap" class="gmap" style="width: 800px; height: 800px; position: relative;">
                                                </div>
                                        </div-->
                                        <div class="clearfix"></div>
                                        </div>
            </div>
            @endif
            @endif
                            @if(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
                                 <div class="accordian-blocks">
                                    <div class="inner-block-bg inner-block-bg1 detail-head">Documents</div>
                                    <div class="detail-data table-slide-document">
                                        <div class="col-md-12 margin-top margin-bottom">
                                           @if($docCount>0)
                                            <div class="col-md-12 padding-none">
                                                <h3>List of documents </h3>
                                                <ul class="popup-list">

                                                    @foreach($docs_seller as $doc)
                                                    <li>{{$doc}}</li>
                                                    @endforeach

                                                </ul>
                                            </div>
                                           @else
                                           No Documents Found
                                           @endif
                                        </div>
                                        
                                        <div class="clearfix"></div>
                                    </div>
                                </div>


                                
                            <div class="accordian-blocks">
                                <div class="inner-block-bg inner-block-bg1 detail-head">Approval</div>
                                <div class="detail-data">
                                    <div class="col-md-12 margin-top margin-bottom">
                                    Data Not Found 
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            @endif

                             @if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY)
                                <div class="accordian-blocks">
                                        <div class="inner-block-bg inner-block-bg1 detail-head">Invoice
                                                Trails</div>
                                        <div class="detail-data">
                                        <div class="col-md-12 margin-top margin-bottom">
                                        <div class="col-md-12 padding-left-none data-fld">
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Invoice No.</span> <span
                                                                                class="data-value">@if($orderDetails->invoice)
                                        {!! $orderDetails->invoice !!}
                                        @else{!! '-' !!}
                                        @endif</span>
                                                                </div>
                                            @if(SHOW_SERVICE_TAX)
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Services Charges</span> <span
                                                                                class="data-value">
                                                                                @if($orderDetails->inv_service_charge)
                                        {!! number_format($orderDetails->inv_service_charge,2)!!}
                                        @else{!! '-' !!}
                                        @endif</span>
                                                                </div>


                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Service Tax</span> <span
                                                                                class="data-value">@if($orderDetails->inv_service_tax)
                                                                        {!! number_format($orderDetails->inv_service_tax,2) !!}
                                                                        @else{!! '-' !!}
                                                                        @endif</span>
                                                                </div>
                                                            @endif
                                                        </div>


                                                                <div class="col-md-12 padding-left-none data-fld">
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Total Amount</span> <span
                                                                                class="data-value">@if($orderDetails->inv_total)
                                        {!! $commonComponent->getPriceType($orderDetails->inv_total) !!}
                                        @else{!! '-' !!}
                                        @endif
                                        <br>
                                        @if(!SHOW_SERVICE_TAX)
                                        <span class="small serviceTax">(* Service Tax not included )</span>
                                                                @endif

                                                                        </span>
                                                                </div>


                                                                </div>

                                        </div>
                                        <div class="clearfix"></div>
                                </div>
                                </div>
                            @endif

                             @if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY)
                                <div class="accordian-blocks">
                                        <div class="inner-block-bg inner-block-bg1 detail-head">Receipt</div>
                                        <div class="detail-data">
                                        <div class="col-md-12 margin-top margin-bottom">
                                        <div class="col-md-12 padding-left-none data-fld">
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Receipt No</span> <span
                                                                                class="data-value">@if($orderDetails->receipt)
                                        {!! $orderDetails->receipt !!}
                                        @else{!! '-' !!}
                                        @endif</span>
                                                                </div>
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Payment Mode</span> <span
                                                                                class="data-value">
                                                                                @if($orderDetails->payment_mode)
                                        {!! $orderDetails->payment_mode !!}
                                        @else{!! '-' !!}
                                        @endif</span>
                                                                </div>

                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Freight Amount</span> <span
                                                                                class="data-value">@if($orderDetails->receipt_frieght)
                                        {!! number_format($orderDetails->receipt_frieght,2) !!}
                                        @else{!! '-' !!}
                                        @endif</span>
                                                                </div>
                                                        </div>


                                                                <div class="col-md-12 padding-left-none data-fld">
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Insurance</span> <span
                                                                                class="data-value">@if($orderDetails->receipt_insurance)
                                        {!! number_format($orderDetails->receipt_insurance,2) !!}
                                        @else{!! '-' !!}
                                        @endif</span>
                                                                </div>

                                                                    @if(SHOW_SERVICE_TAX)

                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Services Charges</span> <span
                                                                                class="data-value">@if($orderDetails->receipt_service_charge)
                                        {!!  number_format($orderDetails->receipt_service_charge,2) !!}
                                        @else{!! '-' !!}
                                        @endif</span>
                                                                </div>


                                                                                            <div class="col-md-4 padding-left-none data-fld">
                                                                                                    <span class="data-head">Service Tax</span> <span
                                                                                                            class="data-value">@if($orderDetails->receipt_service_tax)
                                                                    {!! number_format($orderDetails->receipt_service_tax,2) !!}
                                                                    @else{!! '-' !!}
                                                                    @endif</span>
                                                                                            </div>
                                                                @endif
                                                                </div>

                                                                <div class="col-md-12 padding-left-none data-fld">
                                                                <div class="col-md-4 padding-left-none data-fld">
                                                                        <span class="data-head">Total Amount</span> <span
                                                                                class="data-value">@if($orderDetails->receipt_total)
                                        {!! $commonComponent->getPriceType($orderDetails->receipt_total) !!}
                                        @else{!! '-' !!}
                                        @endif
                                        <br>
                                        @if(!SHOW_SERVICE_TAX)
                                        <span class="small serviceTax">(* Service Tax not included )</span>
                                        @endif

                                                                        </span>
                                                                </div>

                                                                </div>

                                        </div>
                                        <div class="clearfix"></div>



                                        </div>
                                </div>
                                @endif

                        </div>

				<!-- Right Section Ends Here -->

                    </div>
		</div>

		<div class="clearfix"></div>

	</div>
</div>



<footer>
	<div class="container">
		Logistiks.com &copy; 2016. <a href="#">Privacy Policy</a>
	</div>
</footer>




<!-- Modal -->
<div class="modal fade" id="modify-search" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<div class="modal-body">
				<div class="col-md-12 modal-form">
					<div class="col-md-4 padding-none">
						<div class="col-md-12 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-user"></i></span> <select
									class="selectpicker">
									<option value="0">Select Service</option>
									<option value="1">Full Truck (FTL)</option>
									<option value="2">Full Truck (LTL)</option>
								</select>
							</div>
						</div>
					</div>
					<div class="col-md-8 padding-none">
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span> <input
									class="form-control" id="" type="text"
									placeholder="From Location">
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-map-marker"></i></span> <input
									class="form-control" id="" type="text"
									placeholder="To Location">
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span> <input
									class="form-control" id="" type="text"
									placeholder="Dispatch Date">
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-calendar-o"></i></span> <input
									class="form-control" id="" type="text"
									placeholder="Delivery Date">
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-archive"></i></span> <select
									class="selectpicker">
									<option value="0">Select Load Type</option>
								</select>
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="col-md-6 form-control-fld padding-left-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-balance-scale"></i></span>
									<input class="form-control" id="" type="text" placeholder="Qty">
								</div>
							</div>
							<div class="col-md-6 form-control-fld padding-right-none">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-balance-scale"></i></span>
									<input class="form-control" id="" type="text"
										placeholder="Capacity">
								</div>
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<div class="input-prepend">
								<span class="add-on"><i class="fa fa-archive"></i></span> <select
									class="selectpicker">
									<option value="0">Select Vehicle Type</option>
								</select>
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
							<img src="../images/truck.png" class="truck-type" /> <span
								class="truck-type-text">Vehicle Dimensions *</span>
						</div>

						<div class="col-md-6 form-control-fld">
							<button class="btn theme-btn btn-block">Search</button>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>

@include('partials.footer')
@include('partials.volty_gps_includes')

</div>


<!-----model pop up for FTl docuemnts dispaly ----------->

<div class="modal fade" id="ftl-doc-popup" role="dialog">
    <div class="modal-dialog">
      <!-- Modal content-->
      <div class="modal-content">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
    <div class="modal-body">
        <div class="col-md-12 padding-none">

                @if($docCount>0)
                <div class="col-md-12 padding-none">
                    <h3>List of documents </h3>
                    <ul class="popup-list">

                        @foreach($docs_seller as $doc)
                        <li>{{$doc}}</li>
                        @endforeach

                    </ul>
                </div>
               @else
               No Documents Found
               @endif

        </div>
    </div>
  </div>
</div>
</div>


@endsection
