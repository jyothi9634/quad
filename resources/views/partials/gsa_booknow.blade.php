{{--*/ $routeName = strtolower(substr(class_basename(Route::currentRouteAction()), (strpos(class_basename(Route::currentRouteAction()), '@') + 1))) /*--}}
{{--*/ $serviceId = Session::get('service_id') /*--}}
@inject('sellerComp', 'App\Components\SellerComponent')
@inject('buyerComp', 'App\Components\BuyerComponent')
<div id="booknow-popup" class="modal fade" role="dialog" aria-hidden="true">
		<div class="modal-dialog">
  <!-- Modal content-->
			<div class="modal-content registeration">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">×</button>
					<h2 class="modal-title text-center">General Services Agreement</h2>
				</div>
				<div class="modal-body">

                                    
                                    <h3>Disclaimer</h3>

                <p>Logistiks.com is acting as the market place” means the central online portal established for providing logistics service offerings like but not limited to transportation, warehouse, handling, packaging & packers and movers services, on a pan india level. Both the parties to this GSA that means the buyer and the seller should ensure that the information provided by them is accurate to do the transaction on the portal</p>
                <p>The information given by buyer and seller on the sign up page is true and fair to the best of their knowledge and logistiks.com is not responsible in case of discrepancy if any.</p>
                                
					<div class=" margin-top">
						<div class="col-sm-6 padding-left-none">
							<h3>Buyer Details</h3>
							<ul class="popup-list">
								<li><span>Buyer Name<span class="right-doted">:</span></span><span id="buyer_user"></span></li>
								<li><span>Consignment Type<span class="right-doted">:</span></span><span>
                                @if($serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_PET_MOVE || $serviceId == RELOCATION_INTERNATIONAL || $serviceId == RELOCATION_GLOBAL_MOBILITY || $serviceId == RELOCATION_OFFICE_MOVE)
                                    Non-Commercial
                                @else
                                    @if(isset($buyer_quote_id)) 
    									{{--*/ $commercial=$commonComponent->getCommercialBooknow($buyer_quote_id) /*--}}
    									@if($commercial==1)
    									   Commercial
    									@else
    									   Non-Commercial
    									@endif
    								@else
    									@if($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC || $serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN || $serviceId == COURIER )
    										@if($request_blade['is_commercial']==1)
    											Commercial
    										@else
    											Non-Commercial
    										@endif
    								
    									@else
    										@if(Session::has('searchMod.is_commercial_date_buyer') && Session::get('searchMod.is_commercial_date_buyer')==1)
    											Commercial
    										@else
    											Non-Commercial
    										@endif
    									@endif
                              		@endif
                          		@endif
								</span></li>
                                @if($serviceId != RELOCATION_GLOBAL_MOBILITY)
                                    <li><span>From Location<span class="right-doted">:</span></span>
                                    @if($serviceId == RELOCATION_DOMESTIC)
                                        @if(isset($buyer_post_details[0]))
                                            <span>{{$commonComponent->getCityName($buyer_post_details[0]->from_location_id)}}</span>
                                        @else
                                            <span>{{$fromCity}}</span>
                                        @endif
                                    @else
                                        <span>{{$fromCity}}</span>
                                    @endif
                                    </li>
                                @endif    

                                @if($serviceId != ROAD_TRUCK_LEASE && $serviceId !=RELOCATION_OFFICE_MOVE)
								    @if($serviceId == RELOCATION_GLOBAL_MOBILITY)
                                        <li><span>Location<span class="right-doted">:</span></span>
                                    @else
                                        <li><span>To Location<span class="right-doted">:</span></span>
                                    @endif
    								@if($serviceId == RELOCATION_GLOBAL_MOBILITY)
                                        @if(!empty($buyer_post_details))
                                            <span>{{$commonComponent->getCityName($buyer_post_details[0]->location_id)}}</span>
                                        @elseif(isset($toCity))
                                            <span>{{$toCity}}</span>
                                        @endif
			 					    @elseif($serviceId == RELOCATION_DOMESTIC)
								        @if(isset($buyer_post_details[0]))
            								<span>{{$commonComponent->getCityName($buyer_post_details[0]->to_location_id)}}</span>
        								@else
        	       							<span>{{$toCity}}</span>
        								@endif
    								@else
                                        <span>{{$toCity}}</span>
    								@endif
								    </li>
                                @endif
                                <li>
                                    @if($serviceId != ROAD_TRUCK_HAUL && $serviceId != ROAD_TRUCK_LEASE)
                                        @if($serviceId == RELOCATION_GLOBAL_MOBILITY)
                                            <span>Date<span class="right-doted">:</span></span>
                                        @else
                                            <span>Dispatch Date<span class="right-doted">:</span></span>
                                        @endif
                                    @else
                                        <span>Reporting Date<span class="right-doted">:</span></span>
                                    @endif
								
    								@if($serviceId == RELOCATION_GLOBAL_MOBILITY)
                                        @if(Session::has('searchMod.dispatch_date_buyer'))
                                            <span>{!! Session::get('searchMod.dispatch_date_buyer') !!}</span>
                                        @elseif(isset($dispatchDate) && $dispatchDate!='')
                                            <span>{!! $dispatchDate !!}</span>
                                        @endif
                                    @else
                                        <span id="pickup_con_date"></span>
    								@endif
								</li>
								@if($serviceId != ROAD_TRUCK_HAUL && $serviceId != RELOCATION_GLOBAL_MOBILITY)
	   		  					    <li>
                                        @if($serviceId != ROAD_TRUCK_LEASE)
        							 	   <span>Delivery Date<span class="right-doted">:</span></span>
        								@else
        								    <span>Reporting Till<span class="right-doted">:</span></span>
        								@endif
        								@if($serviceId == RELOCATION_DOMESTIC  || $serviceId == RELOCATION_PET_MOVE || $serviceId == RELOCATION_INTERNATIONAL || $serviceId == RELOCATION_GLOBAL_MOBILITY || $serviceId == RELOCATION_OFFICE_MOVE)
            								@if($routeName=='buyerbooknowfromsearchlist')
                								<span>
                								@if(Session::get('session_delivery_date_buyer') == "0000-00-00" || Session::get('session_delivery_date_buyer') == "" )
                                                    N/A
                                                @else
                                                    {!! Session::get('session_delivery_date_buyer') !!}
                                                    </span>
                                                @endif
            								@else
                								@if(isset($buyer_post_details[0]))
                    								<span>{{$commonComponent->convertDateDisplay($buyer_post_details[0]->delivery_date)}} </span>
                								@elseif(isset($deliveryDate) && ($deliveryDate!='' || $deliveryDate!="0000-00-00"))
                    								<span>{!! $deliveryDate !!}</span>
                								@else
                    								<span>
                                                    @if(Session::get('searchMod.session_delivery_date_buyer') == "0000-00-00" || Session::get('searchMod.session_delivery_date_buyer') == "" )
                                                        N/A
                                                    @else
                                                        {!! Session::get('searchMod.session_delivery_date_buyer') !!}
                                                    @endif
                                                    </span>
                								@endif
            								@endif
        								@else
                                                @if($serviceId != RELOCATION_GLOBAL_MOBILITY)
                                                    @if(!empty($deliveryDate) && $deliveryDate!="0000-00-00")
                                                        <span>{!! $deliveryDate !!}</span>
                                                    @else 
                                                        <span>N/A</span> 
                                                    @endif
                                             	@endif    
        								@endif
    								</li>
                                @endif
                             	@if($serviceId == ROAD_FTL || $serviceId == ROAD_TRUCK_HAUL)
    								<li>
                                        <span>Load Type<span class="right-doted">:</span></span>
            								@if(Session::get('searchMod.session_load_type_buyer'))
                								<span>{!! $commonComponent->getLoadType(Session::get('searchMod.session_load_type_buyer')) !!}</span>
            								@else
                								<span>{!! $loadType !!}</span>
            								@endif
        								</span>
                                    </li>
                                    @if(isset($quantity) && $quantity!='')
                                        <li><span>Quantity<span class="right-doted">:</span></span><span>{!! $quantity !!} {!! $units !!}</span></li>
                                    @else
                                        <li><span>Quantity<span class="right-doted">:</span></span><span>{!! Session::get('searchMod.quantity_buyer') !!}</span></li>
                                    @endif
								    <li><span>Vehicle Type<span class="right-doted">:</span></span><span>{!! $vehicleType !!}</span></li>
                                @elseif($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC) 
                                    <li><span>Door Pickup<span class="right-doted">:</span></span><span>{!! $isDoorPickup !!}</span></li>
                                    <li><span>Door Delivery<span class="right-doted">:</span></span><span>{!! $idDoorDelivery !!}</span></li>
                                @elseif($serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN)
                                    <li><span>Shipment Type<span class="right-doted">:</span></span><span>{!! $shipment_type !!}</span></li>
                                    <li><span>Sender Identity<span class="right-doted">:</span></span><span>{!! $sender_identity !!}</span></li>
                                    <li><span>IE Code<span class="right-doted">:</span></span><span>{!! $ie_code !!}</span></li>
                                    <li><span>Product Made<span class="right-doted">:</span></span><span>{!! $product_made !!}</span></li>
                                @endif
                                @if($serviceId == COURIER)
                                    <li><span>Destination Type<span class="right-doted">:</span></span><span>
                                        @if(isset($courier_delivery_type) && $courier_delivery_type!='')
                                         {!! $courier_delivery_type !!}
                                        @else
                                            @if($request_blade['post_delivery_types'][0] == 1)
                                             Domestic
                                             @else
                                             International
                                             @endif
                                        @endif
                                    </span></li>
                                    <li><span>Courier Type<span class="right-doted">:</span></span><span>
                                        @if(isset($courier_type) && $courier_type!='')
                                             {!! $courier_type !!}
                                        @else
                                            @if($request_blade['courier_types'][0] == 1)
                                             Documents
                                            @else
                                             Parcel
                                            @endif
                                        @endif
                                    </span></li>
                                @endif
                                @if($serviceId == ROAD_FTL || $serviceId == ROAD_TRUCK_HAUL || $serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_OFFICE_MOVE || $serviceId == RELOCATION_PET_MOVE || $serviceId == RELOCATION_INTERNATIONAL)
								    <li><span>Location Type<span class="right-doted">:</span></span><span id="source_location"></span></li>
								@endif
								@if($serviceId == ROAD_FTL || $serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_OFFICE_MOVE || $serviceId == RELOCATION_PET_MOVE || $serviceId == RELOCATION_INTERNATIONAL)
								    <li><span>Destination Type<span class="right-doted">:</span></span><span id="destination_location"></span></li>
                                @endif 
                                @if($serviceId == ROAD_TRUCK_LEASE)
    								 <li><span>Vehicle Type<span class="right-doted">:</span></span><span>{!! $vehicleType !!}</span></li>
                                     <li><span>Lease Term<span class="right-doted">:</span></span><span>{!! $lease_term !!}</span></li>
                                @endif 
                                    <li><span>Consignor Name<span class="right-doted">:</span></span><span id="consignor"></span></li>
    								<li><span>Consignor Mobile<span class="right-doted">:</span></span><span id="consignor_mobile"></span></li>
    								<li><span>Consignor Address<span class="right-doted">:</span> </span><span id="consignor_adddress"></span></li>
                                @if($serviceId != ROAD_TRUCK_HAUL && $serviceId != ROAD_TRUCK_LEASE && $serviceId != RELOCATION_GLOBAL_MOBILITY)
    								<li><span>Consignee Name<span class="right-doted">:</span></span><span id="consignee_name"></span></li>
    								<li><span>Consignee Mobile<span class="right-doted">:</span></span><span id="consignee_mobile"></span></li>
    								<li><span>Consignee Address<span class="right-doted">:</span> </span><span id="consignee_address"></span></li>
								@endif
								
								
							@if($serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_PET_MOVE || $serviceId == RELOCATION_OFFICE_MOVE || $serviceId==RELOCATION_INTERNATIONAL || $serviceId==RELOCATION_GLOBAL_MOBILITY)
    							@if(isset($buyerQuoteId))
        							@if($routeName=='buyerbooknowfromsearchlist')
        	       						{{--*/ $buyer_quote='' /*--}}
        							@else
            							{{--*/ $buyer_quote=$buyerQuoteId /*--}}
        							@endif
        							@include('partials.relocation_details',array("buyer_quote_id" => $buyer_quote))
    							@endif
							@endif
							</ul>
						</div>
                        @if(isset($buyerQuoteSellersQuotesDetails->seller_id) && $buyerQuoteSellersQuotesDetails->seller_id!='')
                            {{--*/ $sellerQuoteId = $seller_id/*--}}
                        @else
                            @if($serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_GLOBAL_MOBILITY || $serviceId == RELOCATION_PET_MOVE || $serviceId == RELOCATION_OFFICE_MOVE || $serviceId==RELOCATION_INTERNATIONAL)
                                @if(isset($seller_quote_details[0]))
                                    {{--*/ $sellerQuoteId = $seller_id/*--}}
                                @else
                                    {{--*/ $sellerQuoteId = $seller_post[0]->seller_id/*--}}
                                @endif
                            @else
                                {{--*/ $sellerQuoteId = $seller_id/*--}}
                            @endif
                        @endif                      
						 {{--*/ $userdetails =   $sellerComp->getUserDetBooknow($sellerQuoteId); /*--}}
                         {{--*/ $address     =   $userdetails->address1 . ' ' .$userdetails->address2 .' '.$userdetails->address3 /*--}}
		                    <div class="col-sm-6 border-left-right custam-height">
		                        <h3>Seller Details</h3>
		                        <ul class="popup-list">
		                            <li><span>Seller Name<span class="right-doted">:</span></span><span>{{$userdetails->username}}</span></li>
		                            <li><span>Year of ESTD.<span class="right-doted">:</span></span><span>{{$userdetails->est}}</span></li>
		                            <li><span>Seller Address<span class="right-doted">:</span></span><span>{{$address}}</span></li>
		                            <li><span>GTA Number<span class="right-doted">:</span></span><span>{{$userdetails->gat}}</span></li>
		                            <li><span>Service Tax Number<span class="right-doted">:</span></span><span>{{$userdetails->service}}</span></li>
		                            <li><span>TIN Number<span class="right-doted">:</span></span><span>{{$userdetails->tin}}</span></li>
		                            <li><span>Place of Business  <span class="right-doted">:</span></span><span>{{$userdetails->principal_place}}</span></li>
		                            <li><span>Contact Number<span class="right-doted">:</span></span><span>@if($userdetails->land=="") N/A @else{{$userdetails->land}}@endif</span></li>
		                            <li><span>Mobile Number<span class="right-doted">:</span></span><span>{{$userdetails->phone}}</span></li>
		                            <li><span>Email ID<span class="right-doted">:</span></span><span>{{$userdetails->email}}</span></li>
		                        
                                {{-- Price Display : Start --}}    
                                    @if($serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_GLOBAL_MOBILITY || $serviceId == RELOCATION_PET_MOVE || $serviceId == RELOCATION_OFFICE_MOVE || $serviceId == RELOCATION_INTERNATIONAL)
                                        @if(isset($rprice))
                                            <li><span>Order Total<span class="right-doted">:</span></span><span>Rs. {!! $commonComponent->number_format($rprice) !!}</span></li>
                                        @endif
                                    @else
                                        @if(isset($price))
                                            <li><span>Order Total<span class="right-doted">:</span></span><span>Rs. {!! $commonComponent->number_format($price) !!}</span></li>
                                        @endif
                                    @endif
                                {{-- Price Display : End --}}    

                                @if(isset($buyerQuoteSellersQuotesDetails->seller_post_item_id) && $buyerQuoteSellersQuotesDetails->seller_post_item_id!='')
                                    {{--*/ $sellerQuoteItemId = $buyerQuoteSellersQuotesDetails->seller_post_item_id /*--}}
                                @else
                                    @if($serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_GLOBAL_MOBILITY || $serviceId == RELOCATION_PET_MOVE || $serviceId == RELOCATION_OFFICE_MOVE || $serviceId == RELOCATION_INTERNATIONAL)
                                        @if(isset($seller_quote_details[0]))
                                            {{--*/ $sellerQuoteItemId = $seller_quote_details[0]->seller_post_id/*--}}
                                        @else
                                            @if($serviceId == RELOCATION_PET_MOVE)
                                                {{--*/ $sellerQuoteItemId = $postid/*--}}
                                            @else
                                                {{--*/ $sellerQuoteItemId = $seller_post[0]->id/*--}}
                                            @endif
                                        @endif
                                    @else
                                        {{--*/ $sellerQuoteItemId = $sellerData->id/*--}}
                                    @endif 
                                @endif   
                                                    
		                    	{{--*/ $seller_charges =   $commonComponent->getSellerOtherCharges($sellerQuoteItemId); /*--}}
		                        @if($serviceId == RELOCATION_DOMESTIC)
                                            @if($seller_charges->rate_card_type==1)
                                                 <li><span>Crating charges<span class="right-doted">:</span></span><span>Rs. {{$seller_charges->crating_charges}}/-</span></li>
                                                 <li><span>Storage charges<span class="right-doted">:</span></span><span>Rs. {{$seller_charges->storate_charges}}/-</span></li>
                                                 <li><span>Escort charges<span class="right-doted">:</span></span><span>Rs. {{$seller_charges->escort_charges }}/-</span></li>
                                                 <li><span>Handyman charges<span class="right-doted">:</span></span><span>Rs. {{$seller_charges->handyman_charges }}/-</span></li>
                                                 <li><span>Property charges<span class="right-doted">:</span></span><span>Rs. {{$seller_charges->property_search }}/-</span></li>
                                                 <li><span>Brokerage charges<span class="right-doted">:</span></span><span>Rs. {{$seller_charges->brokerage }}/-</span></li>
                                            @else
                                                <li><span>Storage charges<span class="right-doted">:</span></span><span>Rs. {{$seller_charges->storate_charges}}/-</span></li>
                                            @endif
		                        @endif
                                        @if(isset($seller_charges->cancellation_charge_price) && $seller_charges->cancellation_charge_price!=0.00)
                                            <li><span>Cancellation Charges<span class="right-doted">:</span></span><span>Rs. {{$seller_charges->cancellation_charge_price}}/-</span></li>
                                        @endif
                                        @if(isset($seller_charges->docket_charge_price) && $seller_charges->docket_charge_price!=0.00)
                                            <li><span>Other Charges<span class="right-doted">:</span></span><span>Rs. {{$seller_charges->docket_charge_price}}/-</span></li>
                                        @endif
                                        @if(isset($seller_charges->other_charge1_text) && $seller_charges->other_charge1_price!=0.00)
                                            <li><span>{{$seller_charges->other_charge1_text}}<span class="right-doted"> :</span></span><span>Rs. {{$seller_charges->other_charge1_price}}/-</span></li>
                                        @endif 
                                        @if(isset($seller_charges->other_charge2_text) && $seller_charges->other_charge2_price!=0.00)
                                            <li><span>{{$seller_charges->other_charge2_text}}<span class="right-doted"> :</span></span><span>Rs. {{$seller_charges->other_charge2_price}}/-</span></li>
                                        @endif 
                                        @if(isset($seller_charges->other_charge3_text) && $seller_charges->other_charge3_price!=0.00)
                                            <li><span>{{$seller_charges->other_charge3_text}}<span class="right-doted"> :</span></span><span>Rs. {{$seller_charges->other_charge3_price}}/-</span></li>
                                        @endif 
		                           
										<li><h3 class="text-left">Seller Terms & Conditions </h3></li>
                                        <li>
                                        @if(isset($seller_charges->terms_conditions))
                                            {{$seller_charges->terms_conditions}}
                                        @endif
                                       </li>
								<ul>
		       </div>

						
			</div>
			@if($serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN || $serviceId==COURIER)
    			@if(isset($buyer_quote_id))
        			{{--*/ $buyer_quote=$buyer_quote_id /*--}}
    	       		@include('partials.lineitems_grid',array("buyer_quote_id" => $buyer_quote))
    			@else
                    {{--*/ $buyer_quote='' /*--}}
        			@include('partials.lineitems_grid',array("buyer_quote_id" => $buyer_quote))
    			@endif
			@endif
			
			@if(isset($from_location_id) && !empty($from_location_id))
                {{--*/ $from_location_id = $from_location_id /*--}}
			@else
                {{--*/ $from_location_id = 0 /*--}}
			@endif
			
			@if(isset($to_location_id) && !empty($to_location_id))
                {{--*/ $to_location_id = $to_location_id /*--}}
			@else
                {{--*/ $to_location_id = 0 /*--}}
			@endif
            @if(isset($buyer_quote_id))
                {{--*/ $commercialdocs=$commonComponent->getCommercialBooknow($buyer_quote_id) /*--}}
            @else
                @if($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC || $serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN || $serviceId == COURIER)
                    @if($request_blade['is_commercial']==1)
                        {{--*/ $commercialdocs=1 /*--}}
                    @else
                        {{--*/ $commercialdocs=0 /*--}}
                    @endif
                @else
                    @if(Session::get('session_is_commercial_date_buyer')==1)
                        {{--*/ $commercialdocs=1 /*--}}
                    @else
                        {{--*/ $commercialdocs=0 /*--}}
                    @endif
                @endif
            @endif
			
			@if($serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_PET_MOVE || $serviceId == RELOCATION_OFFICE_MOVE || $serviceId == RELOCATION_INTERNATIONAL || $serviceId == RELOCATION_GLOBAL_MOBILITY)
    			{{--*/ $docs_buyer    =   $commonComponent->getGsaDocuments(1,$serviceId,$buyerQuoteId,$from_location_id,$to_location_id,0); /*--}}
                {{--*/ $docs_seller    =   $commonComponent->getGsaDocuments(2,$serviceId,$buyerQuoteId,0); /*--}}
			@else
    			{{--*/ $docs_buyer    =   $commonComponent->getGsaDocuments(1,$serviceId,$buyerQuoteId,$from_location_id,$to_location_id,$commercialdocs); /*--}}
                {{--*/ $docs_seller    =   $commonComponent->getGsaDocuments(2,$serviceId,$buyerQuoteId,$commercialdocs); /*--}}
            @endif
                   <div class="clearfix"></div>
					<div class="col-sm-12 padding-none">
					<h3 class="margin-top text-left">Documentation</h3>
					<p>
						Any commercial shipment picked up for transit on Indian Ground network should have the following documents:		<br>		
					1. TIN / CST no. of shipper & consignee in case of commercial transaction is mandatory in all states<br>					
					2. Shipper is under obligation to mention valid TIN / CST no of self and consignee on the commercial invoice and regulatory paperwork at the time of handing over the shipment to Transport service provider<br>					
					3. Shipments consigned to individuals who do not have TIN no, a declaration from consignee / shipper that the goods are not for sale and for personal consumption apart from other conditions as laid down in respective States VAT Regulations.				
					<br>
					4. E-waybill generation has been implemented in most of the states. Consignee/ shipper is expected to comply registration process and follow online process for e-waybill generation
					<br></p>									
					<h4>Disclaimer:</h4>
					<p> 					
					State VAT Rules & Regulations are subject to change from time to time.  Shippers / Consignees are, therefore, advised to seek independent verification before tendering any consignment					
					Regulatory paperwork is based on the Rules and Regulations of the State concerned.  Practice could be different than the Rules & Regulations in some of the States		
					</p>
					<p>For this transaction from 
                    @if($serviceId != RELOCATION_GLOBAL_MOBILITY)    
                        @if($serviceId == RELOCATION_DOMESTIC)
                            @if(isset($buyer_post_details[0]))
                                <span>{{$commonComponent->getCityName($buyer_post_details[0]->from_location_id)}}</span>
                            @else
                                <span>{{$fromCity}}</span>
                            @endif
                        @else
                            <span>{{$fromCity}}</span>
                        @endif
                        to
                    @endif

					@if($serviceId != ROAD_TRUCK_LEASE && $serviceId != RELOCATION_OFFICE_MOVE)
    					@if($serviceId == RELOCATION_GLOBAL_MOBILITY)
                            @if(!empty($buyer_post_details))
                                <span>{{$commonComponent->getCityName($buyer_post_details[0]->location_id)}}</span>
                            @endif
    					@elseif($serviceId == RELOCATION_DOMESTIC)
                            @if(isset($buyer_post_details[0]))
                                <span>{{$commonComponent->getCityName($buyer_post_details[0]->to_location_id)}}</span>
                            @else
                                <span>{{$toCity}}</span>
                            @endif
    					@else
    					<span>{{$toCity}}</span>
    	   				@endif
   				    @endif 
					following documents are needed :</p>
					</div>
					
	            <div class="col-sm-4 padding-none">
	            						
                    <ul class="popup-list">
                        @if(count($docs_seller)>0)
                            <li><h4>To be provided by seller : </h4></li>
                            @foreach($docs_seller as $doc)
                                <li> <i class="fa fa-check"></i>
                                   {{$doc}}
                                </li>
                            @endforeach
                        @endif
                        @if(count($docs_buyer)>0)
                            <li><h4>To be provided by buyer : </h4></li>
                            @foreach($docs_buyer as $doc)
                                <li> <i class="fa fa-check"></i>
                                    {{$doc}}
                                </li>
                            @endforeach
                        @endif
                    </ul>
	            </div>
					
					<div class="clearfix"></div>
					
					
					</div>
				<div class="modal-footer">
				<input type="hidden" name="toggle_seller_to_role_id" id="toggle_seller_to_role_id">
				<input type="hidden" name="alldata" id="alldata">
				<input type="hidden" name="ajaxurl" id="ajaxurl">
				<input type="hidden" name="ischeckout" id="ischeckout">
				<input type="hidden" name="service_id" id="service_id" value={{$serviceId}}>
				<button type="button" class="btn flat-btn red-btn" id="acceptterms" name="acceptterms">I Accept</button>
					
				</div>
			</div>

		</div>
	</div>