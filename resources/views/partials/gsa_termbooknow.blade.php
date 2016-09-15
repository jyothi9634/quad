{{--*/ $serviceId = Session::get('service_id') /*--}}
@inject('sellerComp', 'App\Components\SellerComponent')
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
								<li><span>From Location<span class="right-doted">:</span></span><span>{{$displayFromLocationType}}</span></li>
								@if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY)
								<li><span>To Location<span class="right-doted">:</span></span><span>{{$displayToLocationType}}</span></li>
								@endif
								<li>
								@if(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
								<span>Date<span class="right-doted">:</span></span>
								@else
								<span>Dispatch Date<span class="right-doted">:</span></span>
								@endif
								@if(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
								<span id="pickupgm_con_date">
								@if(isset($_REQUEST['global_pickup_date_'.$contractDetails[0]->id]))
								{{$_REQUEST['global_pickup_date_'.$contractDetails[0]->id]}}
								@endif
								</span>
								@else
								<span id="pickup_con_date"></span>
								@endif
								</li>
				                @if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY)
								<li><span>Delivery Date<span class="right-doted">:</span></span>
								<span>
								@if(isset($contractDetails[0]->to_date) && $contractDetails[0]->to_date != '0000-00-00')
				               {{date("d/m/Y", strtotime($contractDetails[0]->to_date))}}
				               @else &nbsp;
				               @endif </span></li>
				               @endif
                                @if($serviceId == ROAD_FTL)
								<li><span>Load Type<span class="right-doted">:</span></span><span>{!! $displayLoadType !!}</span></li>
							    <li><span>Quantity<span class="right-doted">:</span></span><span><?php echo $indentQuantity; ?></span></li>
								<li><span>Vehicle Type<span class="right-doted">:</span></span><span>{!! $displayVehicleType !!}</span></li>
                                @endif
                                @if($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC || $serviceId == AIR_INTERNATIONAL || $serviceId == OCEAN)
								<li><span>Load Type<span class="right-doted">:</span></span><span>{!! $displayLoadType !!}</span></li>
								@endif
								<li><span>Location Type<span class="right-doted">:</span></span><span id="source_location"></span></li>
								@if($serviceId != RELOCATION_GLOBAL_MOBILITY)
								<li><span>Destination Type<span class="right-doted">:</span></span><span id="destination_location"></span></li>
								@endif
								<li><span>Consignor Name<span class="right-doted">:</span></span><span id="consignor"></span></li>
								<li><span>Consignor Mobile<span class="right-doted">:</span></span><span id="consignor_mobile"></span></li>
								<li><span>Consignor Address<span class="right-doted">:</span> </span><span id="consignor_adddress"></span></li>
								@if($serviceId != RELOCATION_GLOBAL_MOBILITY)
								<li><span>Consignee Name<span class="right-doted">:</span></span><span id="consignee_name"></span></li>
								<li><span>Consignee Mobile<span class="right-doted">:</span></span><span id="consignee_mobile"></span></li>
								<li><span>Consignee Address<span class="right-doted">:</span> </span><span id="consignee_address"></span></li>
								@endif
								@if($serviceId == RELOCATION_DOMESTIC && isset($indentData))
									{{--*/ $cont_id = $indentData['contract_id'] /*--}}
									{{--*/ $load_Types = array('1'=>'Full Load','2'=>'Part Load')/*--}}
									<li><span>Property Type<span class="right-doted">:</span></span><span id="property_type">{{$commonComponent->getPropertyType($indentData['property_type_'.$cont_id])}}</span></li>
									<li><span>Load Type<span class="right-doted">:</span></span><span id="load_type">{{$load_Types[$indentData['load_type_'.$cont_id]]}}</span></li>
									<li><span>Volume<span class="right-doted">:</span> </span><span id="volume">{{$indentData['volume_'.$cont_id]}}</span></li>
								@endif
								@if($serviceId == RELOCATION_INTERNATIONAL && isset($indentData))
								<?php //dd($indentData); exit;?>
									@if(isset($indentData['cartons_1']))
									   @if(isset($indentData['cartons_1']) && $indentData['cartons_1']!='')
										<li><span>Cartoon 1<span class="right-doted">:</span></span><span id="property_type">{{$indentData['cartons_1']}}</span></li>
									   @endif
									   @if(isset($indentData['cartons_2']) && $indentData['cartons_2']!='')	
										<li><span>Cartoon 2<span class="right-doted">:</span> </span><span id="volume">{{$indentData['cartons_2']}}</span></li>
									   @endif
									   @if(isset($indentData['cartons_3']) && $indentData['cartons_3']!='')	
										<li><span>Cartoon 3<span class="right-doted">:</span> </span><span id="volume">{{$indentData['cartons_3']}}</span></li>
									   @endif
									@else
										{{--*/ $cont_id = $indentData['contract_id'] /*--}}
										<li><span>Property Type<span class="right-doted">:</span></span><span id="property_type">{{$commonComponent->getPropertyType($indentData['property_type_'.$cont_id])}}</span></li>
										<li><span>Volume (CBM)<span class="right-doted">:</span></span><span id="load_type">{{$indentData['total_hidden_kgs_'.$cont_id]}}</span></li>
									@endif	
								@endif
								@if($serviceId == RELOCATION_GLOBAL_MOBILITY && isset($indentData))
									{{--*/ $cont_id = $indentData['contract_id'] /*--}}
									{{--*/ $service_name = $commonComponent->getGMTermServiceNameByPostItemId($indentData['quote_item_id']) /*--}}
									<li><span>Service Name<span class="right-doted">:</span></span><span id="property_type">{{$service_name}}</span></li>
									<li><span>Number of Days<span class="right-doted">:</span> </span><span id="volume">{{$indentData['number_days_'.$cont_id]}}</span></li>
								@endif
							</ul>
								
						</div>
						 {{--*/ $userdetails =   $sellerComp->getUserDetBooknow($sellerId); /*--}}
		                    <div class="col-sm-6 border-left-right custam-height">
		                        <h3>Seller Details</h3>
		                        <ul class="popup-list">
		                            <li><span>Seller Name<span class="right-doted">:</span></span><span>{{$userdetails->username}}</span></li>
		                            <li><span>Year of ESTD.<span class="right-doted">:</span></span><span>{{$userdetails->est}}</span></li>
		                            <li><span>Seller Address<span class="right-doted">:</span></span><span>{{$userdetails->address}}</span></li>
		                            <li><span>GTA Number<span class="right-doted">:</span></span><span>{{$userdetails->gat}}</span></li>
		                            <li><span>Service Tax Number<span class="right-doted">:</span></span><span>{{$userdetails->service}}</span></li>
		                            <li><span>TIN Number<span class="right-doted">:</span></span><span>{{$userdetails->tin}}<span></li>
		                            <li><span>Place of Business  <span class="right-doted">:</span></span><span>{{$userdetails->principal_place}}</span></li>
		                            <li><span>Contact Number<span class="right-doted">:</span></span><span>@if($userdetails->land=="") N/A @else{{$userdetails->land}}@endif</span></li>
		                            <li><span>Mobile Number<span class="right-doted">:</span></span><span>{{$userdetails->phone}}</span></li>
		                            <li><span>Email ID<span class="right-doted">:</span></span><span>{{$userdetails->email}}</li>
		                            <li><span>Order Total<span class="right-doted">:</span></span><span>Rs. {!! $commonComponent->number_format($price) !!}</span></li>
		                            <li><h3 class="text-left">Seller Terms& Conditions </h3></li>
								  <li>Buyer to pay toll gate charges. Order booked between 10 AM to 4PM will be confirmed by 8 PM. Any booking beyond the above will be confirmed by next working day @ 11 AM
								Cancellation rules : 24 Hours before Pick up date. Cancellation charges applicable</li>
								
		                        <ul>
		                    </div>
		                    
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
			
			@if($serviceId == RELOCATION_DOMESTIC || $serviceId == RELOCATION_INTERNATIONAL || $serviceId == RELOCATION_GLOBAL_MOBILITY)
			{{--*/ $docs_buyer    =   $commonComponent->getGsaDocuments(1,$serviceId,$buyerQuoteId,$from_location_id,$to_location_id,0); /*--}}
            {{--*/ $docs_seller    =   $commonComponent->getGsaDocuments(2,$serviceId,$buyerQuoteId,0); /*--}}
			@else
			{{--*/ $docs_buyer    =   $commonComponent->getGsaDocuments(1,$serviceId,$buyerQuoteId,$from_location_id,$to_location_id,1); /*--}}
            {{--*/ $docs_seller    =   $commonComponent->getGsaDocuments(2,$serviceId,$buyerQuoteId,1); /*--}}
			@endif
			
			@if($serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN || $serviceId==COURIER)
			
			@include('partials.termlineitems_grid')
			
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
					<p>For this transaction from  {{$displayFromLocationType}} to @if(Session::get('service_id')!=RELOCATION_GLOBAL_MOBILITY) {{$displayToLocationType}} @endif following documents are needed :</p>
					</div>
	                    <div class="col-sm-4 padding-none">
	                        
	                        <ul class="popup-list">
	                          @if(count($docs_seller)>0)
	                            <li><h4>To be provided by seller : </h4></li>
	                            @foreach($docs_seller as $doc)
	                            <li><i class="fa fa-check"></i>
	                            {{$doc}}
	                            </li>
	                            @endforeach
	                          @endif
	                          @if(count($docs_buyer)>0)
	                            <li><h4>To be provided by buyer :</h4></li>
	                            @foreach($docs_buyer as $doc)
	                            <li><i class="fa fa-check"></i>
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