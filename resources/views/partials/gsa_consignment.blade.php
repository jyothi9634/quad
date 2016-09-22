@inject('common', 'App\Components\CommonComponent')
<div id="consignment-popup" class="modal fade" role="dialog" aria-hidden="true">
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
{{--*/ $serviceId = Session::get('service_id'); /*--}}
@inject('commonComponent', 'App\Components\CommonComponent')
                <div class=" margin-top">
                    <div class="col-sm-4 padding-none">
                        <h3>Buyer Details</h3>
                        <?php //echo "<pre>";print_r($order);exit;?>
                        <ul class="popup-list">
                            <li><span>Buyer Name<span class="right-doted">:</span></span>{{$commonComponent->getUsername($order->buyer_id)}}</li>
                            
                            <li><span>Consignment Type<span class="right-doted">:</span></span>
                             @if($serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_PET_MOVE || $serviceId == RELOCATION_INTERNATIONAL || $serviceId == RELOCATION_GLOBAL_MOBILITY || $serviceId == RELOCATION_OFFICE_MOVE)
                                Non-Commercial
                             @else
                                @if($commonComponent->getCommercial($order->id)==1)
                                Commercial
                                @else
                                Non Commercial
                                @endif
                             @endif   
                            </li>
                            
                            <li><span>From Location<span class="right-doted">:</span></span>{{$post->from}}</li>
                            @if(isset($post->to))
                            <li><span>To Location<span class="right-doted">:</span></span>
                            @if($serviceId == COURIER)
	                           	@if(isset($post->courier_delivery_type) && $post->courier_delivery_type!='')
	                           		@if($post->courier_delivery_type=='International')
	                           		{{ $common->getCountry($post->to_location_id) }}
	                            	@else
	                            	{{ $common->getPinName($post->to_location_id) }}
	                            	@endif
	                            @endif
	                        @else
	                        {{$post->to}}
	                        @endif
                            </li>
                            @endif
                            <li>
                            @if($serviceId != ROAD_TRUCK_HAUL)
                            	@if($serviceId == RELOCATION_GLOBAL_MOBILITY)		
                            		<span>Date<span class="right-doted">:</span>
                            	@else
                            		<span>Dispatch Date<span class="right-doted">:</span>
                            	@endif
                            @else
                            <span>Reporting Date<span class="right-doted">:</span>
                            @endif	
                            </span>{{date("d/m/Y", strtotime($order->buyer_consignment_pick_up_date))}}</li>
                            @if($serviceId != ROAD_TRUCK_HAUL && $serviceId != RELOCATION_GLOBAL_MOBILITY)
                            	<li><span>Delivery Date<span class="right-doted">:</span></span>
                           	 	@if(date("d/m/Y", strtotime($order->delivery_date)) == '01/01/1970')
                            		N/A
                            	@else
                            		{{date("d/m/Y", strtotime($order->delivery_date))}}
                            	@endif
                            	</li>
                            @endif
                            @if(isset($post->load) && $post->load!='')
                            <li><span>Load Type<span class="right-doted">:</span></span>
                                {{$post->load}}
                            </li>
                            @endif
                            @if(isset($order->quantity) && $order->quantity!='')
                            <li><span>Quantity<span class="right-doted">:</span></span>
                                {{$order->quantity}} {{$order->units}}
                            </li>
                            @endif
                            @if(isset($post->vehicle) && $post->vehicle!='')
                            <li><span>Vehicle Type<span class="right-doted">:</span></span>
                                {{$post->vehicle}}
                            </li>
                            @endif
                            
                            @if($serviceId == ROAD_PTL || $serviceId == RAIL )
                            @if(isset($post->door_pickup)&& $post->door_pickup==1)
                                {{--*/ $door_pickup_status="Yes" /*--}}
                            @else
                                {{--*/ $door_pickup_status="No" /*--}}
                            @endif
                            @if(isset($post->door_delivery)&& $post->door_delivery==1)
                            {{--*/ $door_delivery_status="Yes" /*--}}
                        @else
                            {{--*/ $door_delivery_status="No" /*--}}                        
                        @endif   
                            <li><span>Door Pickup<span class="right-doted">:</span></span>
                                {!! $door_pickup_status !!}</li>
                            <li><span>Door Delivery<span class="right-doted">:</span></span>
                                {!! $door_delivery_status !!}</li>
                            @elseif($serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN)
                            <li><span>Shipment Type<span class="right-doted">:</span></span><span>{!! $post->shipment_type !!}</span></li>
                            <li><span>Sender Identity<span class="right-doted">:</span></span><span>{!! $post->sender_identity !!}</span></li>
                            <li><span>IE Code<span class="right-doted">:</span></span><span>{!! $post->ie_code !!}</span></li>
                            <li><span>Product Made<span class="right-doted">:</span></span><span>{!! $post->product_made !!}</span></li>
                            @endif
                            @if($serviceId == COURIER)
                                <li><span>Destination Type<span class="right-doted">:</span></span>
                                    @if(isset($post->courier_delivery_type) && $post->courier_delivery_type!='')
                                        {!! $post->courier_delivery_type !!}
                                    @endif
                                </li>
                                <li><span>Courier Type<span class="right-doted">:</span></span>
                                    @if(isset($post->courier_type) && $post->courier_type!='')
                                        {!! $post->courier_type !!}
                                    @endif
                                </li>
                            @endif
                            @if($serviceId == ROAD_TRUCK_LEASE)
                                <li><span>Lease Term<span class="right-doted">:</span></span>
                                    {!! $post->lease_term !!}
                                </li>
                            @endif 
                            
                            @if($order->lkp_src_location_type_id!=0)
                            <li><span>
                            @if($serviceId != ROAD_TRUCK_HAUL)
                            Location Type
                            @else
                            Reporting Location Type
                            @endif<span class="right-doted">:</span></span>
                                @if($order->lkp_src_location_type_id!=0 && $order->lkp_src_location_type_id!=11)
                                {{$commonComponent->getLocationType($order->lkp_src_location_type_id)}}
                                @elseif($order->other_src_location_type!='')
                                {{$order->other_src_location_type}}
                                @endif
                            </li>
                            @endif
                            @if($serviceId != ROAD_TRUCK_HAUL && $serviceId != RELOCATION_GLOBAL_MOBILITY)
                            @if($order->lkp_dest_location_type_id!=0)
                            <li><span>Destination Type<span class="right-doted">:</span></span>
                                @if($order->lkp_dest_location_type_id!=0 && $order->lkp_dest_location_type_id!=11)
                                {{$commonComponent->getLocationType($order->lkp_dest_location_type_id)}}
                                @elseif($order->other_src_location_type!='')
                                {{$order->other_dest_location_type}}
                                @endif
                            </li>
                            @endif
                            @endif    
                            <li><span>Consignor Name<span class="right-doted">:</span></span>{{$order->buyer_consignor_name}}</li>
                            <li><span>Consignor Mobile<span class="right-doted">:</span></span>{{$order->buyer_consignor_mobile}}</li>
                            <li><span>Consignor Address<span class="right-doted">:</span> </span>{{$order->buyer_consignor_address}}</li>
                            @if($serviceId != ROAD_TRUCK_HAUL && $serviceId != ROAD_TRUCK_LEASE && $serviceId != RELOCATION_GLOBAL_MOBILITY)
                            <li><span>Consignee Name<span class="right-doted">:</span></span>{{$order->buyer_consignee_name}}</li>
                            <li><span>Consignee Mobile<span class="right-doted">:</span></span>{{$order->buyer_consignee_mobile}}</li>
                            <li><span>Consignee Address<span class="right-doted">:</span> </span>{{$order->buyer_consignee_address}}</li>
                            @endif    
                            @if($order->lkp_order_type_id==1)
                                @if($serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_PET_MOVE || $serviceId == RELOCATION_OFFICE_MOVE || $serviceId==RELOCATION_INTERNATIONAL || $serviceId==RELOCATION_GLOBAL_MOBILITY)		
                                    @if(isset($order->buyer_quote_id))
                                        @include('partials.relocation_details',array("buyer_quote_id" => $order->buyer_quote_id))
                                    @endif
                                @endif
                            @else
                                {{--*/ $termindentData = $commonComponent->getBuyerPostDetailsGSA($order->buyer_quote_id,$order->id); /*--}}
                                @if($serviceId == RELOCATION_DOMESTIC )
                                        {{--*/ $load_Types = array('1'=>'Full Load','2'=>'Part Load')/*--}}
                                        <li><span>Property Type<span class="right-doted">:</span></span><span id="property_type">{{$commonComponent->getPropertyType($termindentData->lkp_property_type_id)}}</span></li>
                                        <li><span>Load Type<span class="right-doted">:</span></span><span id="load_type">{{$termindentData->domestic_load}}</span></li>
                                        <li><span>Volume<span class="right-doted">:</span> </span><span id="volume">{{$termindentData->volume}}</span></li>
                                @elseif($serviceId == RELOCATION_INTERNATIONAL)
                                
                                        @if((isset($termindentData->cartons_one) && $termindentData->cartons_one!=0) || (isset($termindentData->cartons_two) && $termindentData->cartons_two!='') || (isset($termindentData->cartons_three) && $termindentData->cartons_three!=''))
                                           @if(isset($termindentData->cartons_one) && $termindentData->cartons_one!='')
                                                <li><span>Cartoon 1<span class="right-doted">:</span></span><span id="property_type">{{$termindentData->cartons_one}}</span></li>
                                           @endif
                                           @if(isset($termindentData->cartons_two) && $termindentData->cartons_two!='')	
                                                <li><span>Cartoon 2<span class="right-doted">:</span> </span><span id="volume">{{$termindentData->cartons_two}}</span></li>
                                           @endif
                                           @if(isset($termindentData->cartons_three) && $termindentData->cartons_three!='')	
                                                <li><span>Cartoon 3<span class="right-doted">:</span> </span><span id="volume">{{$termindentData->cartons_three}}</span></li>
                                           @endif
                                        @else
                                            <li><span>Property Type<span class="right-doted">:</span></span><span id="property_type">{{$commonComponent->getPropertyType($termindentData->lkp_property_type_id)}}</span></li>
                                            <li><span>Volume (CBM)<span class="right-doted">:</span></span><span id="load_type">{{$termindentData->volume}}</span></li>
                                        @endif	
                                @elseif($serviceId == RELOCATION_GLOBAL_MOBILITY )
                                        {{--*/ $service_name = $commonComponent->getGMTermServiceNameByPostItemId($order->buyer_quote_item_id) /*--}}
                                        <li><span>Service Name<span class="right-doted">:</span></span><span id="property_type">{{$service_name}}</span></li>
                                        <li><span>Number of Days<span class="right-doted">:</span> </span><span id="volume">{{$termindentData->indent_quantity}}</span></li>
                                @endif
                            
                            @endif
                        </ul>

                    </div>
                    {{--*/ $userdetails =   $commonComponent->getUserDetConsignment(); /*--}}
                    {{--*/ $sellerCharges   =   $commonComponent->getSellerPostOrder($order->id);  /*--}}
                    {{--*/ $invoice   =   $commonComponent->orderInvoiceDetails($order->id);  /*--}}
                    @if($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC)
                    {{--*/ $oda   =   $commonComponent->buyerODACheck($post->to_location_id,$order->lkp_service_id,$order->seller_id); /*--}}
                    @endif
                    <div class="col-sm-8 border-left-right custam-height">
                        <h3>Seller Details</h3>
                        <ul class="popup-list">
                            <li><span>Seller Name<span class="right-doted">:</span></span>{{$userdetails->username}}</li>
                            <li><span>Year of ESTD.<span class="right-doted">:</span></span>{{$userdetails->est}}</li>
                            <li><span>Seller Address<span class="right-doted">:</span></span>{{$userdetails->address1}}</li>
                            <li><span>GTA Number<span class="right-doted">:</span></span>{{$userdetails->gat}}</li>
                            <li><span>Service Tax Number<span class="right-doted">:</span></span>{{$userdetails->service}}</li>
                            <li><span>TIN Number<span class="right-doted">:</span></span>{{$userdetails->tin}}</li>
                            <li><span>Place of Business  <span class="right-doted">:</span></span>{{$userdetails->principal_place}}</li>
                            <li><span>Contact Number<span class="right-doted">:</span></span>@if($userdetails->land=="") N/A @else{{$userdetails->land}}@endif</li>
                            <li><span>Mobile Number<span class="right-doted">:</span></span>{{$userdetails->phone}}</li>
                            <li><span>Email ID<span class="right-doted">:</span></span>{{$userdetails->email}}</li>
                            <li><span>Sub Total<span class="right-doted">:</span></span>Rs. @if(!empty($invoice)){{$invoice->frieght_amt}} @else {{$order->price}} @endif/-</li>
                            @if(SHOW_SERVICE_TAX)
                                <li><span>Service Tax<span class="right-doted">:</span></span>Rs. @if(!empty($invoice)){{$invoice->service_tax_amount}} @else 0.00 @endif/-</li>
                            @endif
                            <li><span>Order Total<span class="right-doted">:</span></span>Rs. @if(!empty($invoice)){{$invoice->total_amt}} @else {{$order->price}} @endif/-</li>
                            @if($serviceId == ROAD_PTL || $serviceId == RAIL )
                                @if(isset($post->door_pickup)&& $post->door_pickup==1)
                                    <li><span>Pick up charges<span class="right-doted">:</span></span>Rs. {{$pickup_charges}}/-</li>
                                @endif
                                @if(isset($post->door_delivery)&& $post->door_delivery==1)
                                    <li><span>Delivery charges<span class="right-doted">:</span></span>Rs. {{$delivery_charges}}/-</li>
                                @endif
                            @endif
                            @if($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC)
                                @if(isset($oda)&& $oda==1)
                                    <li><span>ODA charges<span class="right-doted">:</span></span>Rs. {{$oda_charges}}/-</li>
                                @endif
                            @endif
                            @if(!empty($sellerCharges))
                            @if($serviceId == RELOCATION_DOMESTIC)
                                @if($sellerCharges->rate_card_type==1)
                                <li><span>Crating charges<span class="right-doted">:</span></span>Rs. {{$sellerCharges->crating_charges}}/-</li>
                                <li><span>Storage charges<span class="right-doted">:</span></span>Rs. {{$sellerCharges->storate_charges}}/-</li>
                                <li><span>Escort charges<span class="right-doted">:</span></span>Rs. {{$sellerCharges->escort_charges }}/-</li>
                                <li><span>Handyman charges<span class="right-doted">:</span></span>Rs. {{$sellerCharges->handyman_charges }}/-</li>
                                <li><span>Property charges<span class="right-doted">:</span></span>Rs. {{$sellerCharges->property_search }}/-</li>
                                <li><span>Brokerage charges<span class="right-doted">:</span></span>Rs. {{$sellerCharges->brokerage }}/-</li>
                                @else
                                <li><span>Storage charges<span class="right-doted">:</span></span>Rs. {{$sellerCharges->storate_charges}}/-</li>
                                @endif
		            @endif
                            @if(isset($sellerCharges->cancellation_charge_price) && $sellerCharges->cancellation_charge_price!='' && $sellerCharges->cancellation_charge_price!=0)
                            <li><span>{{$sellerCharges->cancellation_charge_text}}<span class="right-doted">:</span></span>Rs. {{$sellerCharges->cancellation_charge_price}}/-</li>
                            @endif
                            @if(isset($sellerCharges->docket_charge_price) && $sellerCharges->docket_charge_price!='' && $sellerCharges->docket_charge_price!=0)
                            <li><span>{{$sellerCharges->docket_charge_text}}<span class="right-doted">:</span></span>Rs. {{$sellerCharges->docket_charge_price}}/-</li>
                            @endif
                            @if(isset($sellerCharges->other_charge1_price) && $sellerCharges->other_charge1_price!='' && $sellerCharges->other_charge1_price!=0)
                            <li><span>{{$sellerCharges->other_charge1_text}}<span class="right-doted">:</span></span>Rs. {{$sellerCharges->other_charge1_price}}/-</li>
                            @endif
                            @if(isset($sellerCharges->other_charge2_price) && $sellerCharges->other_charge2_price!='' && $sellerCharges->other_charge2_price!=0)
                            <li><span>{{$sellerCharges->other_charge2_text}}<span class="right-doted">:</span></span>Rs. {{$sellerCharges->other_charge2_price}}/-</li>
                            @endif
                            @if(isset($sellerCharges->other_charge3_price) && $sellerCharges->other_charge3_price!='' && $sellerCharges->other_charge3_price!=0)
                            <li><span>{{$sellerCharges->other_charge3_text}}<span class="right-doted">:</span></span>Rs. {{$sellerCharges->other_charge3_price}}/-</li>
                            @endif
                            @endif
                            @if(isset($tracking))
                            <li><span>Tracking<span class="right-doted">:</span></span>                                
                                {{ $commonComponent->getTrackingType($tracking) }}
                            </li>
                            
                            @endif
                            @if(!empty($sellerCharges))
                                @if(isset($sellerCharges->terms_conditions) && $sellerCharges->terms_conditions!='')
                                <li><h3 class="text-left">Seller Terms & Conditions</h3></li>
                                <li>{{$sellerCharges->terms_conditions}}</li>
                                @endif
                            @endif
                            
                        <ul>
                    </div>
                    <div class="clearfix"></div>
                    {{--*/ $str_perkg='' /*--}} 
                    @if($serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==COURIER)
                    {{--*/ $str_perkg=' CFT' /*--}}
                    @elseif($serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL)
                    {{--*/ $str_perkg=' CCM' /*--}}
                    @elseif($serviceId==OCEAN)
                    {{--*/ $str_perkg=' CBM' /*--}}
                    @endif
                    
                    @if($serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN || $serviceId==COURIER)
			@if(isset($order->buyer_quote_id))
                                @include('partials.lineitems_grid',array("buyer_quote_id" => $order->buyer_quote_id))
                        @elseif($order->lkp_order_type_id==2)
                            {{--*/ $termindentData = $commonComponent->getBuyerPostDetailsGSA($order->buyer_quote_id,$order->id); /*--}}
                                @include('partials.termlineitems_grid',array("termindentData" => $termindentData,"order_id" => $order->id))
                                
			@endif
                    @endif
                   
                    
                </div>
<div class="clearfix"></div>

                {{--*/ $is_commercial = $commonComponent->getCommercial($order->id); /*--}}
                    @if($serviceId==ROAD_FTL || $serviceId==ROAD_TRUCK_LEASE || $serviceId==ROAD_TRUCK_HAUL || $serviceId==ROAD_INTRACITY)
                    @if($serviceId==ROAD_FTL)
                    {{--*/ $docs_buyer    =   $commonComponent->getGsaDocuments(1,$serviceId,$order->buyer_quote_item_id,$post->from_location_id,$post->to_location_id,$is_commercial); /*--}}
                    {{--*/ $docs_seller    =  $commonComponent->getGsaDocuments(2,$serviceId,$order->buyer_quote_item_id,$post->from_location_id,$post->to_location_id); /*--}}
                    @else
                    {{--*/ $docs_buyer    =   $commonComponent->getGsaDocuments(1,$serviceId,$order->buyer_quote_item_id); /*--}}
                    {{--*/ $docs_seller    =  $commonComponent->getGsaDocuments(2,$serviceId,$order->buyer_quote_item_id); /*--}}
                    @endif
                    @elseif($serviceId==RELOCATION_DOMESTIC || $serviceId==RELOCATION_PET_MOVE ||  $serviceId==RELOCATION_INTERNATIONAL)
                    {{--*/ $docs_buyer    =   $commonComponent->getGsaDocuments(1,$serviceId,$order->buyer_quote_item_id,$post->from_location_id,$post->to_location_id,0); /*--}}
                    {{--*/ $docs_seller    =  $commonComponent->getGsaDocuments(2,$serviceId,$order->buyer_quote_item_id,$post->from_location_id,$post->to_location_id); /*--}}
                    @elseif($serviceId==RELOCATION_OFFICE_MOVE)
                    {{--*/ $docs_buyer    =   $commonComponent->getGsaDocuments(1,$serviceId,$order->buyer_quote_item_id,$post->from_location_id,0); /*--}}
                    {{--*/ $docs_seller    =  $commonComponent->getGsaDocuments(2,$serviceId,$order->buyer_quote_item_id,$post->from_location_id); /*--}}
                    @elseif($serviceId==RELOCATION_GLOBAL_MOBILITY)
                    {{--*/ $docs_buyer    =   $commonComponent->getGsaDocuments(1,$serviceId,$order->buyer_quote_item_id,$post->location_id,0); /*--}}
                    {{--*/ $docs_seller    =  $commonComponent->getGsaDocuments(2,$serviceId,$order->buyer_quote_item_id,$post->location_id); /*--}}
                    @elseif($serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN || $serviceId==COURIER)
                    {{--*/ $docs_buyer    =   $commonComponent->getGsaDocuments(1,$serviceId,$order->buyer_quote_id,$post->from_location_id,$post->to_location_id,$is_commercial); /*--}}
                    {{--*/ $docs_seller    =  $commonComponent->getGsaDocuments(2,$serviceId,$order->buyer_quote_id,$post->from_location_id,$post->to_location_id); /*--}}
                    @else
                    @endif
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
                        <p>For this transaction from @if(isset($post->from)) {{$post->from}} @endif to @if(isset($post->to)) {{$post->to}} @endif the following documents are needed :</p>
                    
                    <div class="col-sm-4 padding-none">
                        <ul class="popup-list">
                            <li><h4>To be provided by seller </h4></li>
                            @if(!empty($docs_seller))
                            @foreach($docs_seller as $doc)
                            <li><i class="fa fa-check"></i>
                            {{$doc}}
                            </li>
                            @endforeach
                            @endif
                            <li><h4>To be provided by buyer</h4></li>
                            @if(!empty($docs_buyer))
                            @foreach($docs_buyer as $doc)
                            <li><i class="fa fa-check"></i>
                            {{$doc}}
                            </li>
                            @endforeach
                            @endif
                            @if($commonComponent->getCommercial($order->id)==0)
                            <li><i class="fa fa-check"></i>
                            Self Declaration
                            </li>
                            @endif
                        </ul>
                    </div>
                    <div class="clearfix"></div>
                
            </div>
            <div class="modal-footer">
              <button type="button" class=" btn flat-btn red-btn" id="gsa_consign_acceptterms" name="acceptterms">I Accept</button>
            </div>
    </div>

</div>
</div>
</div>