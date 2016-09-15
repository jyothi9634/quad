@inject('common', 'App\Components\CommonComponent')
@extends('app')
@section('content')
@if(!empty($_REQUEST) )

    {{--*/ $type = $_REQUEST['type'] /*--}}
@else
    {{--*/ $type = 'enquiries' /*--}}
    @endif
@if(isset($allMessagesList['result']) && !empty($allMessagesList['result']))
    {{--*/ $countMessages = count($allMessagesList['result']) /*--}}
@else
    {{--*/ $countMessages = 0 /*--}}
@endif
	{{--*/ $now_date = date('Y-m-d') /*--}}

	{{--*/ $serviceId = Session::get('service_id') /*--}}
        {{--*/ $str_perkg='' /*--}} 
        @if($serviceId==ROAD_PTL || $serviceId==RAIL)
        {{--*/ $str_perkg=' CFT' /*--}}
        @elseif($serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL)
        {{--*/ $str_perkg=' CCM' /*--}}
        @elseif($serviceId==OCEAN)
        {{--*/ $str_perkg=' CBM' /*--}}
        @endif
@include('partials.page_top_navigation')
{{--*/ $cls="" /*--}}
{{--*/ $serviceId = Session::get('service_id'); /*--}}
{{--*/ $docs_seller_ptl    =   $common->getGsaDocuments(SELLER,$serviceId,$seller_post[0]->id); /*--}}      
{{--*/ $docCount_ptl = count($docs_seller_ptl) /*--}}

@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('sellerlist') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif  

<div class="main">
	<div class="container">
		@if(Session::has('updatedseller')) <div class="alert alert-info"> {{Session::get('updatedseller')}} </div> @endif
		
                @include('partials.content_top_navigation_links')
		<div class="clearfix"></div>

			<span class="pull-left"><h1 class="page-title">Spot Transaction - {{ $seller_post[0]->transaction_id }}</h1></span>
		<span class="pull-right">
			<a href="#" class="view-icon red"><i class="fa fa-eye" title="Views"></i> {{ $viewcount }}</a>
			@if($seller_post_items[0]->is_cancelled != 1)
				@if($seller_post_items[0]->is_private != 1)
					<a href="/ptl/updatesellerpost/{!! $seller_post_id !!}" class="edit-icon"><i class="fa fa-edit" title="Edit"></i></a>
				@endif
				<a  href="javascript:void(0)" data-target="#cancelsellerpostmodal" data-toggle="modal" onclick="javascript:setcancelpostid('items',{{ $seller_post_items[0]->id }})" class="delete-icon">
					<i class="fa fa-trash red" title="Delete"></i>
				</a>
			@endif
			<a href="{{ $backToPostsUrl }}" class="back-link1">Back to Posts</a>
		</span>

		<?php if($seller_post[0]->lkp_payment_mode_id == 1){
			$payment_type = 'Advance';
			if($seller_post[0]->accept_payment_netbanking == 1)
				$payment_type .= ' | NEFT/RTGS';
			if($seller_post[0]->accept_payment_credit == 1)
				$payment_type .= ' | Credit Card';
			if($seller_post[0]->accept_payment_debit == 1)
				$payment_type .= ' | Debit Card';
		}
		elseif($seller_post[0]->lkp_payment_mode_id == 2)
			$payment_type = 'Cash on delivery';
		elseif($seller_post[0]->lkp_payment_mode_id == 3)
			$payment_type = 'Cash on pickup';
		else{
			$payment_type = 'Credit';
			if($seller_post[0]->accept_credit_netbanking == 1)
				$payment_type .= ' | Net Banking';
			if($seller_post[0]->accept_credit_cheque == 1)
				$payment_type .= ' | Cheque / DD';
			
			$payment_type .= ' | ';
			$payment_type .= $seller_post[0]->credit_period;
			$payment_type .= ' ';
			$payment_type .= $seller_post[0]->credit_period_units;
			
			
		}
		?>

		<div class="filter-expand-block">

			<div class="search-block inner-block-bg margin-bottom-less-1">
				<div class="date-area">
					<div class="col-md-6 padding-none">
						<p class="search-head">Valid From</p>
						<span class="search-result">
							<i class="fa fa-calendar-o"></i>
							{{ $common->checkAndGetDate($seller_post[0]->from_date) }}
						</span>
					</div>
					<div class="col-md-6 padding-none">
						<p class="search-head">Valid To</p>
						<span class="search-result">
							<i class="fa fa-calendar-o"></i>
							{{ $common->checkAndGetDate($seller_post[0]->to_date) }}
						</span>
					</div>
				</div>
				@if(Session::get('service_id') == ROAD_PTL || Session::get('service_id') == RAIL || Session::get('service_id') == AIR_DOMESTIC) 
				<div class="date-area">
					<div class="col-md-6 padding-none">
						<p class="search-head">From</p>
						<span class="search-result">
							@if($seller_post[0]->lkp_ptl_post_type_id == 2)
							{{ $common->getZonePin($seller_post_items[0]->from_location_id) }}
							@else
							{{ $common->getZoneName($seller_post_items[0]->from_location_id) }}
							@endif
						</span>
					</div>
					<div class="col-md-6 padding-none">
						<p class="search-head">To</p>
						<span class="search-result">
							@if($seller_post[0]->lkp_ptl_post_type_id == 2)
							{{ $common->getZonePin($seller_post_items[0]->to_location_id) }}
							@else
							{{ $common->getZoneName($seller_post_items[0]->to_location_id) }}
							@endif
						</span>
					</div>
				</div>
				@endif
				@if((Session::get('service_id') != COURIER) )
					<div>
						<p class="search-head">Payment</p>
						<span class="search-result">{{ $payment_type }}</span>
					</div>
					<div>
						<p class="search-head">						
                               {{ $common->getQuoteAccessById($seller_post[0]->lkp_access_id) }}
						</p>
						<span class="search-result">
						<?php if($seller_post[0]->lkp_access_id == 1)
							echo 'Yes';
						else{
							foreach($privatebuyers as $pdetails){
								echo  $pdetails->username.' | ';
							}
						}
							 
						?>
						</span>
					</div>
					<div>
						<p class="search-head">Tracking</p>
						<span class="search-result">
						<?php 
							if($seller_post[0]->tracking == 1)
								echo  'Milestone';
							else
								echo 'Real Time';
						?>
						</span>
					</div>	
				@else
					<div>
						<p class="search-head">From</p>
						<span class="search-result">
							
							@if($seller_post[0]->lkp_ptl_post_type_id == 1 )
								{{--*/ $fromlocation = $common->getZoneName($seller_post_items[0]->from_location_id) /*--}}
							@else
								{{--*/ $fromlocation = $common->getZonePin($seller_post_items[0]->from_location_id)  /*--}}
							@endif
							{{ $fromlocation }}
						</span>
					</div>
					<div>
						<p class="search-head">To</p>
						<span class="search-result">
						
						@if($seller_post[0]->lkp_ptl_post_type_id == 1 )
							@if($seller_post[0]->lkp_courier_delivery_type_id == 1)
								{{--*/ $tolocation = $common->getZoneName($seller_post_items[0]->to_location_id) /*--}}
							@else 
								{{--*/ $tolocation = $common->getCountry($seller_post_items[0]->to_location_id) /*--}}
							@endif
						
						@else
							@if($seller_post[0]->lkp_courier_delivery_type_id == 1)
								{{--*/ $tolocation = $common->getZonePin($seller_post_items[0]->to_location_id)  /*--}}
							@else
								{{--*/ $tolocation = $common->getCountry($seller_post_items[0]->to_location_id) /*--}}
							@endif
						
						@endif
						{{ $tolocation }}
						</span>
					</div>
					<div>
						<p class="search-head">Transit Days</p>
						<span class="search-result">{{ $seller_post_items[0]->transitdays }}</span>
					</div>
				@endif					
				<div class="text-right filter-details">
					<div class="info-links">
						<a class="transaction-details-expand"><span class="show-icon">+</span>
							<span class="hide-icon">-</span> Details
						</a>
					</div>
				</div>

			</div>

			<!-- Search Block Ends Here -->

			<!--toggle div starts-->
			<div class="show-trans-details-div-expand trans-details-expand"> 
			   	<div class="expand-block">
			   		<div class="col-md-12">
			   			@if((Session::get('service_id') != COURIER) )
					   		<!-- Kgpercft -->
					   		@if($seller_post[0]->kg_per_cft!='' && $seller_post[0]->kg_per_cft!=0)
					   		<div class="col-md-2 padding-right-none data-fld">
								<span class="data-head">
								Kg per{{$str_perkg}}
								</span>
								<span class="data-value">
								<?php 
								if($seller_post[0]->kg_per_cft!='' )
								echo  $seller_post[0]->kg_per_cft;
								?>
								</span>
							</div>
							@endif
							@if($seller_post[0]->pickup_charges!='' && $seller_post[0]->pickup_charges!=0)
							<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">
								Pick up charges
								</span>
								<span class="data-value">
								<?php 
								if($seller_post[0]->pickup_charges!='' )
								echo  $common->getPriceType($seller_post[0]->pickup_charges);
								?>
								</span>
							</div>
							@endif
							@if($seller_post[0]->delivery_charges!='' && $seller_post[0]->delivery_charges!=0)
							<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">
								Delivery charges
								</span>
								<span class="data-value">
								<?php 
								if($seller_post[0]->delivery_charges!='' )
								echo  $common->getPriceType($seller_post[0]->delivery_charges);
								?>
								</span>
							</div>
							@endif
							@if($seller_post[0]->oda_charges!='' && $seller_post[0]->oda_charges!=0)
							<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">
								ODA charges
								</span>
								<span class="data-value">
								<?php 
								if($seller_post[0]->oda_charges!='' )
								echo  $common->getPriceType($seller_post[0]->oda_charges);
								?>
								</span>
							</div>
					   		@endif
				   		@endif
				   		
				   		<!-- Kgpercft -->
				   		@if((Session::get('service_id') == COURIER) )
					   		<?php 
					   		if(isset($seller_post[0]->lkp_ict_weight_uom_id) && $seller_post[0]->lkp_ict_weight_uom_id!=''){
							?>
					   			{{--*/ $weight = $common->getWeight($seller_post[0]->lkp_ict_weight_uom_id) /*--}}
					   		<?php 
					   		}
					   		else{
					   			$weight ='';
					   		}
					   		?>
					   		
					   		<div class="col-md-3 form-control-fld">Conversion Factor (CCM/KG): {{ $seller_post[0]->conversion_factor }} </div>
						   	<div class="col-md-3 form-control-fld">Maximum Weight Accepted: {{ $seller_post[0]->max_weight_accepted}} {{$weight}}</div>
						   	<div class="col-md-3 form-control-fld">Courier type: 
						   	@if($seller_post[0]->lkp_courier_type_id == 1)
						   	Documents
						   	@else
						   	Parcel
						   	@endif</div>
						   	<div class="col-md-3 form-control-fld">Destination type: 
						   	@if($seller_post[0]->lkp_courier_delivery_type_id == 1)
						   	Domestic
						   	@else
						   	International
						   	@endif</div>
						   	
						   
							@if(count($seller_post_slab_values)>0)
							<div class="table-div table-style1">
								<h2 class="filter-head1 margin-left-none">Pricing Details</h2>
						
								<!-- Table Head Starts Here -->
		
								<div class="table-heading inner-block-bg">
									<div class="col-md-2 padding-left-none">Minimum Weight<i class="fa fa-caret-down"></i></div>
									<div class="col-md-2 padding-left-none">Maximum Weight<i class="fa fa-caret-down"></i></div>
									<div class="col-md-3 padding-left-none">Price (<i class="fa fa-inr fa-1x"></i>)<i class="fa fa-caret-down"></i></div>
								</div>
								
								<!-- Table Head Ends Here -->
		
								<div class="table-data">
		
									<?php 
									if(count($seller_post_slab_values)>0){	
										foreach($seller_post_slab_values as $slab){
										?>
											<!-- Table Row Ends Here -->	
											<div class="table-row inner-block-bg">
												<div class="col-md-2 padding-left-none">{{$slab->slab_min_rate}}</div>
												<div class="col-md-2 padding-left-none">{{$slab->slab_max_rate}}</div>
												<div class="col-md-3 padding-left-none">{{$slab->price}}</div>
											</div>
				
											<!-- Table Row Ends Here -->
										<?php }
									} ?>
								</div>
							</div>
							@endif
							<?php 
							if(isset($seller_post[0]->is_incremental) && $seller_post[0]->is_incremental==1){ ?>
								<div class="col-md-3 form-control-fld">Incremental Weight: {{$seller_post[0]->increment_weight }} {{$weight}}</div>
								<div class="col-md-3">Rate Per Incremental Weight: {{ $seller_post[0]->rate_per_increment }} Rs</div>
							<?php 
							}
							?>
							
							<div class="col-md-12 padding-left-none data-fld">
								<h5 class="caption-head margin-left-none">Additional Charges</h5>
							</div>
							<div class="clear-fix"></div>
							<div class="col-md-3 form-control-fld padding-none">Fuel Surcharge: {{ $seller_post[0]->fuel_surcharge }}</div>	
							<div class="col-md-9 form-control-fld">
								<div class="col-md-3 form-control-fld padding-none">COD: {{ $seller_post[0]->cod_charge }}</div>	
								<div class="col-md-3 form-control-fld padding-none">Freight Collect: {{ $seller_post[0]->freight_collect_charge }}</div>
								<div class="col-md-3 form-control-fld padding-none">ARC: {{ $seller_post[0]->arc_charge }}</div>
								<div class="col-md-3 form-control-fld padding-none">Maximum Value: {{ $seller_post[0]->maximum_value }}</div>
							</div>
							
					   	</div>
					   	@endif
				   		
				   		@if($seller_post_items[0]->is_private!=1)
				   		<div class="col-md-12">
				   		@if($seller_post[0]->cancellation_charge_price!='' && $seller_post[0]->cancellation_charge_text !='')
						<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">
							<?php if($seller_post[0]->cancellation_charge_text !='')
								echo  $seller_post[0]->cancellation_charge_text; 
							?>
							</span>
							<span class="data-value">
							<?php 
							if($seller_post[0]->cancellation_charge_price!='' && $seller_post[0]->cancellation_charge_text !='')
							echo  $common->getPriceType($seller_post[0]->cancellation_charge_price);
							?>
							</span>
						</div>
						@endif
						@if($seller_post[0]->docket_charge_price!='' && $seller_post[0]->docket_charge_text !='')
						<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">
							<?php if($seller_post[0]->docket_charge_text !='')
								echo  $seller_post[0]->docket_charge_text; 
							?>
							</span>
							<span class="data-value">
							<?php 
							if($seller_post[0]->docket_charge_price!='' && $seller_post[0]->docket_charge_text !='')
							echo  $common->getPriceType($seller_post[0]->docket_charge_price);
							?>
							</span>
						</div>
						@endif
						@if($seller_post[0]->other_charge1_text !='' && $seller_post[0]->other_charge1_price!='')
						<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">
							<?php if($seller_post[0]->other_charge1_text !='')
								echo  $seller_post[0]->other_charge1_text; 
							?>
							</span>
							<span class="data-value">
							<?php 
							if($seller_post[0]->other_charge1_text !='' && $seller_post[0]->other_charge1_price!='')
							echo  $common->getPriceType($seller_post[0]->other_charge1_price);
							?>
							</span>
						</div>
						@endif
						@if($seller_post[0]->other_charge2_text !='' && $seller_post[0]->other_charge2_price!='')
						<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">
							<?php if($seller_post[0]->other_charge2_text !='')
								echo  $seller_post[0]->other_charge2_text; 
							?>
							</span>
							<span class="data-value">
							<?php 
							if($seller_post[0]->other_charge2_text !='' && $seller_post[0]->other_charge2_price!='')
							echo  $common->getPriceType($seller_post[0]->other_charge2_price);
							?>
							</span>
						</div>
						@endif
						@if($seller_post[0]->other_charge3_text !='' && $seller_post[0]->other_charge3_price!='')
						<div class="col-md-2 padding-left-none data-fld">
							<span class="data-head">
							<?php if($seller_post[0]->other_charge3_text !='')
								echo  $seller_post[0]->other_charge3_text; 
							?>
							</span>
							<span class="data-value">
							<?php 
							if($seller_post[0]->other_charge3_text !='' && $seller_post[0]->other_charge3_price!='')
							echo  $common->getPriceType($seller_post[0]->other_charge3_price);
							?>
							</span>
						</div>
						@endif
						
						
						
						</div>
						@endif
						<div class="clearfix"></div>
						@if((Session::get('service_id') == COURIER) )

						<div class="col-md-12">
							<div class="col-md-4 padding-left-none data-fld">
								<span class="data-head">Payment</span>
								<span class="data-value">{{ $payment_type }}</span>
							</div>
							<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">
									{{ $common->getQuoteAccessById($seller_post[0]->lkp_access_id) }}
								</span>
								<span class="data-value">
									<?php if($seller_post[0]->lkp_access_id == 1)
										echo 'Yes';
									else{
										foreach($privatebuyers as $pdetails){
											echo  $pdetails->username.' | ';
										}
									} 
									?>
								</span>
							</div>
							<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">
								Documents
								</span>
								<span class="data-value">{!! $docCount_ptl !!}
								</span>
							</div>
							@if($seller_post_items[0]->is_private==1)
							<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">
								Rate per KG 
								</span>
								<span class="data-value">Rs {{$seller_post_items[0]->price}} /-
								</span>
							</div>
							@endif
							<div class="col-md-2 padding-left-none data-fld">
								<span class="data-head">Tracking</span>
								<span class="data-value">
									<?php 
										if($seller_post[0]->tracking == 1)
											echo  'Milestone';
										else
											echo 'Real Time';
									?>
								</span>
							</div>
						</div>
						@endif
						@if($seller_post[0]->terms_conditions!='')
						<div class="col-md-12 data-fld">
							<span class="data-head">Terms &amp; Conditions</span>
							<span class="data-value">{{ $seller_post[0]->terms_conditions }}</span>
						</div>
						@endif
					</div>
					<div class="clearfix"></div>
				</div>


	  		</div>


			<!--toggle div ends-->
		
			<div class="clearfix"></div>
		

			<div class="main-inner margin-top"> 
				<!-- Right Section Starts Here -->
				<div class="main-right">
					<div class="pull-left">
						<div class="info-links" id="seller_post_info_links">
							<?php echo $gridtopnav ; ?>
						</div>
					</div>

					
						<input type="hidden" id="serviceid" value={{ Session::get('service_id') }}>
                                                       
                            {{--*/ $msg_style   =($type=="messages")?"style=display:block":"style=display:none" /*--}} 
							<div class="clearfix"></div>
							<div class="table-data text-left tabs-group" id="ftl-seller-messages" {{$msg_style}}>
								
                                	{!! $allMessagesList['grid'] !!}
                                
							</div>	

<div class="clearfix"></div>
							<div class="table-data text-left tabs-group" id="ftl-seller-marketanalytics" style="display:none;">
								<div class="table-row inner-block-bg text-center">
									No records founds
								</div>
							</div>	

							
							 {{--*/ $docu_style   =($type=="documentation")?"style=display:block":"style=display:none" /*--}} 
                                <div id="ftl-seller-documentation" class="table-data text-left tabs-group" {{$docu_style}}>
                                    <div class="table-data inner-block-bg">                                       
                                        
                                        @if($docCount_ptl>0)                                     
                                        <div class="col-sm-12 padding-right-none">
                                            <h3>List of documents </h3> 
                                            <ul class="popup-list">                                               
                                                
                                                @foreach($docs_seller_ptl as $doc)
                                                <li>{{$doc}}</li>
                                                @endforeach
                                                
                                            </ul>
                                        </div>
                                       @else
                                       No Documents Found
                                       @endif
                                       
                                    </div>
                                </div>
							
							
                        {{--*/ $leads_style   =($type=="leads")?"style=display:block":"style=display:none" /*--}}
						<div class="clearfix"></div>
						<div class="table-data" id="ftl-seller-leads" {{$leads_style}}>
							<div class="table-div margin-none">
								
						<!-- Table Head Starts Here -->

						<div class="table-heading inner-block-bg">
							<div class="col-md-2 padding-left-none">Buyer Name<i class="fa fa-caret-down"></i></div>
							<div class="col-md-3 padding-left-none">Dispatch Date - DeliveryDate<i class="fa fa-caret-down"></i></div>
							<div class="col-md-2 padding-left-none">From Location<i class="fa fa-caret-down"></i></div>
							<div class="col-md-2 padding-left-none">To Location<i class="fa fa-caret-down"></i></div>
							<div class="col-md-1 padding-left-none">Status<i class="fa fa-caret-down"></i></div>
							<div class="col-md-2 padding-left-none"></div>
						</div>
							<!-- Table Row Starts Here -->
							@if(isset($buyerleadquoteid))
								@if(count($buyerleadquoteid)==0)
								<div class="table-data">
									<div class="table-row inner-block-bg text-center">
										No records founds
									</div>
								</div>
								@endif
							@endif
							{{--*/ $i = 0 /*--}}
							@if(isset($buyerleadquoteid[0]->username))
							@foreach($buyerleadquoteid as $buyerleadquoteid)
							
							{{--*/ $odacheck = $common->sellerODACheck($buyerleadquoteid->to_location_id,Session::get('service_id')) /*--}}
							<div class="table-row inner-block-bg">
								<div class="col-md-2 padding-left-none">
									{!! $buyerleadquoteid->username !!}
									<div class="red">
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
									</div>
								</div>
								<div class="col-md-3 padding-left-none">{!! $common->checkAndGetDate($buyerleadquoteid->dispatch_date) !!} -
											@if($common->checkAndGetDate($buyerleadquoteid->delivery_date) == '')
												N/A
											@else
												{!! $common->checkAndGetDate($buyerleadquoteid->delivery_date) !!}
											@endif</div>
								
								@if((Session::get('service_id') == AIR_INTERNATIONAL) )
									<div class="col-md-2 padding-left-none">{{ $common->getAirportName($buyerleadquoteid->from_location_id) }}</div>									
									<div class="col-md-2 padding-left-none">{{ $common->getAirportName($buyerleadquoteid->to_location_id) }}</div>
								@elseif((Session::get('service_id') == OCEAN) )
									<div class="col-md-2 padding-left-none">{{ $common->getSeaportName($buyerleadquoteid->from_location_id) }}</div>									
									<div class="col-md-2 padding-left-none">{{ $common->getSeaportName($buyerleadquoteid->to_location_id) }}</div>

								@else
									<div class="col-md-2 padding-left-none">
									{{ $common->getZonePin($buyerleadquoteid->from_location_id) }}</div>									
									<div class="col-md-2 padding-left-none">
									@if((Session::get('service_id') == COURIER) )
										@if($buyerleadquoteid->lkp_courier_delivery_type_id == 1)
											{{ $common->getZonePin($buyerleadquoteid->to_location_id) }}
										@else
											{{ $common->getCountry($buyerleadquoteid->to_location_id) }}
										@endif
									@else
										{{ $common->getZonePin($buyerleadquoteid->to_location_id) }}
									@endif
									</div>

								@endif
									
																	
								<div class="col-md-1 padding-left-none">{{ $common->getSellerPostStatuss($buyerleadquoteid->lkp_post_status_id) }}</div>
								<!--div class="col-md-2 padding-none"--><!--button class="btn red-btn pull-right submit-data"></button-->
								@if($buyerleadquoteid->lkp_post_status_id==2)
									@if(isset($buyerleadquoteid->initial_quote_price) &&
                                                    $buyerleadquoteid->initial_quote_price =='0.0000' &&
                                                    isset($buyerleadquoteid->counter_quote_price) &&
                                                    $buyerleadquoteid->counter_quote_price =='0.0000')
                                               @if($buyerleadquoteid->lkp_post_status_id==OPEN)     
											<div class="col-md-2 padding-none">
												<span class="btn red-btn pull-right underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}" id="click-link" >Submit Quote <i class="fa fa-rupee"></i>
												</span>
											</div>
											<div class="clearfix"></div>
											@endif
										@endif
										@if(isset($buyerleadquoteid->initial_quote_price) &&
                                                    $buyerleadquoteid->initial_quote_price !='0.0000' &&
                                                    isset($buyerleadquoteid->counter_quote_price) &&
                                                    $buyerleadquoteid->counter_quote_price =='0.0000')
											<div class="col-md-2 padding-none">
											<span class="btn red-btn pull-right underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}" id="click-link" >Initial Quote Submitted <i class="fa fa-rupee"></i>
											</span>
											</div>
											<div class="clearfix"></div>
										@endif

										@if(isset($buyerleadquoteid->initial_quote_price) &&
                                                            $buyerleadquoteid->initial_quote_price !='0.0000' &&
                                                            isset($buyerleadquoteid->counter_quote_price) &&
                                                            $buyerleadquoteid->counter_quote_price !='0.0000' &&
                                                            isset($buyerleadquoteid->final_quote_price) &&
                                                            $buyerleadquoteid->final_quote_price !='0.0000' )
											<div class="col-md-2 padding-none">
											<span class="btn red-btn pull-right underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}" id="click-link" >Final Quote Submitted <i class="fa fa-rupee"></i>
											</span>
											</div>
											<div class="clearfix"></div>
										@endif

										
											@if(isset($buyerleadquoteid->initial_quote_price) &&
                                                            $buyerleadquoteid->initial_quote_price !='0.0000' &&
                                                            isset($buyerleadquoteid->counter_quote_price) &&
                                                            $buyerleadquoteid->counter_quote_price !='0.0000' &&
                                                            isset($buyerleadquoteid->final_quote_price) &&
                                                            $buyerleadquoteid->final_quote_price =='0.0000' )
												<div class="col-md-2 padding-none">
													<span class="btn red-btn pull-right underline_link seller_submit_quote margin-bottom" data-buyernbuyerquoteid="{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}" id="click-link" >Submit Final Quote <i class="fa fa-rupee"></i>
													</span>
												
													<span class="btn red-btn pull-right underline_link seller_submit_quote seller_counter" data-buyernbuyerquoteid="{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}" id="click-link" >Accept Counter offer <i class="fa fa-rupee"></i>
													</span>
												</div>
												<div class="clearfix"></div>
							
											@endif
										@endif

								<!--/div-->


								<div class="pull-right text-right">
									<div class="info-links">
										<span class="detailsslide  underline_link" data-buyersearchlistid="{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"><span class="show_details" style="display: inline;">+</span><span class="hide_details" style="display: none;">-</span> Details</span>
											<!--span class="show-icon" style="display: inline;">+</span><span class="hide-icon" style="display: none;">-</span> Details</a-->
                                                                                <a href="#" class="red underline_link new_message" data-userid='{{ $buyerleadquoteid->buyer_id }}' data-buyer-transaction="{{$buyerleadquoteid->transaction_no}}" data-id="{{ Session::get('seller_post_item') }}" data-buyerquoteitemidforseller="{{ $buyerleadquoteid->ptlquoteid }}"><i class="fa fa-envelope-o"></i></a>
									</div>
								</div>
								@if(Session::get('service_id') != COURIER)
									{!! Form::open(array('url' => 'sellersubmitquote', 'id' => 'leadptlsellerpostquoteoffer', 'name' => 'leadptlsellerpostquoteoffer' ,'class'=>'formquoteid_'.$buyerleadquoteid->ptlquoteid)) !!}
								@else
									{!! Form::open(array('url' => 'sellersubmitquote', 'id' => 'leadptlsellercounterquoteoffer', 'name' => 'leadptlsellercounterquoteoffer' ,'class'=>'formquoteid_'.$buyerleadquoteid->ptlquoteid)) !!}
								
								@endif
										@if(Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL)
											{{--*/ $cft ='Conversion Kg/CCM' /*--}}
										@elseif(Session::get('service_id') == OCEAN)
											{{--*/ $cft ='Conversion Kg/CBM' /*--}}
										@elseif(Session::get('service_id') == COURIER)
											{{--*/ $cft ='Conversion Factor (CCM/KG)' /*--}}
										@else
											{{--*/ $cft ='Conversion Kg/CFT' /*--}}
										@endif

								<div class="col-md-12 show-data-div padding-none padding-top quote_details_1_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }} margin-top" style="display:none">
									
									<h2 class="sub-head"><span class="from-head">
									@if((Session::get('service_id') == AIR_INTERNATIONAL) )
									<div class="col-md-8 padding-left-none">{{ $common->getAirportName($buyerleadquoteid->from_location_id) }} - {{ $common->getAirportName($buyerleadquoteid->to_location_id) }}</div>
								@elseif((Session::get('service_id') == OCEAN) )
									<div class="col-md-8 padding-left-none">{{ $common->getSeaportName($buyerleadquoteid->from_location_id) }} - {{ $common->getSeaportName($buyerleadquoteid->to_location_id) }}</div>

								@else
									<div class="col-md-8 padding-left-none">{{ $common->getPinName($buyerleadquoteid->from_location_id) }} -
									@if(Session::get('service_id') == COURIER)
									@if($buyerleadquoteid->lkp_courier_delivery_type_id == 1)
										{{ $common->getPinName($buyerleadquoteid->to_location_id) }}
									@else
										{{ $common->getCountry($buyerleadquoteid->to_location_id) }}
									@endif
									@else
									{{ $common->getPinName($buyerleadquoteid->to_location_id) }}
									@endif
									</div>

								@endif
									
									</span></h2>
									
									<div class="table-div table-style1">
											
											<!-- Table Head Starts Here -->

											<div class="table-heading inner-block-bg">
												@if((Session::get('service_id') != COURIER) )
													<div class="col-md-3 padding-left-none">Load type<i class="fa fa-caret-down"></i></div>
												@else
													<div class="col-md-3 padding-left-none">Courier type<i class="fa fa-caret-down"></i></div>
												@endif
												<div class="col-md-2 padding-left-none">Volume<i class="fa fa-caret-down"></i></div>
												<div class="col-md-2 padding-left-none">Unit Weight<i class="fa fa-caret-down"></i></div>
												<div class="col-md-3 padding-left-none">No of Packages<i class="fa fa-caret-down"></i></div>
												@if((Session::get('service_id') != COURIER) )
													<div class="col-md-2 padding-left-none">Package Type<i class="fa fa-caret-down"></i></div>
												@else
													<div class="col-md-2 padding-left-none">Courier Delivery Type<i class="fa fa-caret-down"></i></div>
												@endif
											</div>

											<!-- Table Head Ends Here -->

											<div class="table-data">
												

												<!-- Table Row Starts Here -->
												@for($j=0;$j<count($buyerleadquotedetails[0]);$j++)
												@if( $buyerleadquotedetails[0][$j]->ptlquoteid == $buyerleadquoteid->ptlquoteid )	
													<div class="table-row inner-block-bg">
														@if((Session::get('service_id') != COURIER) )
														<div class="col-md-3 padding-left-none">{!! $buyerleadquotedetails[0][$j]->load_type !!}</div>
														@else
															@if($buyerleadquotedetails[0][$j]->lkp_courier_type_id == 1)
															<div class="col-md-3 padding-left-none">Document</div>
															@else
															<div class="col-md-3 padding-left-none">Parcel</div>
															@endif
														@endif
														@if((Session::get('service_id') == AIR_DOMESTIC) || (Session::get('service_id') == AIR_INTERNATIONAL) || (Session::get('service_id') == COURIER))
															<div class="col-md-2 padding-left-none">{!! round($buyerleadquotedetails[0][$j]->calculated_volume_weight,4) !!} CCM</div>
														@elseif((Session::get('service_id') == OCEAN))
															<div class="col-md-2 padding-left-none">{!! round($buyerleadquotedetails[0][$j]->calculated_volume_weight,4) !!} CBM</div>
														@else
															<div class="col-md-2 padding-left-none">{!! round($buyerleadquotedetails[0][$j]->calculated_volume_weight,4) !!} CFT</div>
														@endif

														@if($buyerleadquotedetails[0][$j]->weight_type == 'Gms')
														{{--*/ $buyerleadquotedetails[0][$j]->units = $buyerleadquotedetails[0][$j]->units * 0.001  /*--}}
														<div class="col-md-2 padding-left-none">{!! $buyerleadquotedetails[0][$j]->units !!} Kgs</div>
														@elseif($buyerleadquotedetails[0][$j]->weight_type == 'MTs')
														{{--*/ $buyerleadquotedetails[0][$j]->units = $buyerleadquotedetails[0][$j]->units * 1000  /*--}}
														<div class="col-md-2 padding-left-none">{!! $buyerleadquotedetails[0][$j]->units !!} Kgs</div>
													@else
														<div class="col-md-2 padding-left-none">{!! $buyerleadquotedetails[0][$j]->units !!} {!! $buyerleadquotedetails[0][$j]->weight_type !!}</div>
													@endif
														
														<div class="col-md-3 padding-left-none">{!! $buyerleadquotedetails[0][$j]->number_packages !!}</div>
														@if((Session::get('service_id') != COURIER) )
														<div class="col-md-2 padding-left-none">{!! $buyerleadquotedetails[0][$j]->packaging_type_name !!}</div>
														@else
															@if($buyerleadquotedetails[0][$j]->lkp_courier_delivery_type_id == 1)
																<div class="col-md-2 padding-left-none">Domestic</div>
															@else
																<div class="col-md-2 padding-left-none">International</div>
															@endif
														@endif
														<input type='hidden' name='volumetric_{{ $j }}' id='volumetric_{{ $j }}' value="{{ $buyerleadquotedetails[0][$j]->calculated_volume_weight }}">
														<input type='hidden' name='units_{{ $j }}' id='units_{{ $j }}' value="{{ $buyerleadquotedetails[0][$j]->units }}">
														<input type='hidden' name='weighttype_{{ $j }}' id='weighttype_{{ $j }}' value="{{ $buyerleadquotedetails[0][$j]->weight_type }}">
														<input type='hidden' name='packagenos_{{ $j }}' id='packagenos_{{ $j }}' value="{{ $buyerleadquotedetails[0][$j]->number_packages }}">
														@if((Session::get('service_id') == COURIER) )
														<input type='hidden' name='packagevalue_{{ $j }}' id='packagevalue_{{ $j }}' value="{{ $buyerleadquotedetails[0][$j]->package_value }}">	
													@endif
													</div>
												@endif
												@endfor



												<!-- Table Row Ends Here -->

												<input type='hidden' name='incrementcount_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}' id='incrementcount_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}' value="{{ $j }}">

												<input type="hidden" name="buyerquoteid_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}" id="buyerquoteid_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}" value="{{ $buyerleadquoteid->ptlquoteid }}">
											</div> <!-- Details section endds -->

										</div>

								</div>
								<input type='hidden' name='seller_post_item_id' id='seller_post_item_id' value="{{ Session::get('seller_post_item') }}">
								<input type='hidden' name='volumetric_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}' id='volumetric_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}' value="{{ $buyerleadquoteid->calculated_volume_weight }}">
								<input type='hidden' name='packagenos_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}' id='packagenos_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}' value="{{ $buyerleadquoteid->number_packages }}">
								<input type='hidden' name='units_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}' id='units_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}' value="{{ $buyerleadquoteid->units }}">
								<input type='hidden' name='sellerkgpercft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}' id='sellerkgpercft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}' value="{{ $kgpercft }}">




								<div class="col-md-12 padding-none quote_details_2_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"  style="display:none">
										<div class="col-md-12 border-top margin-top">
											<div class="col-md-12 form-control-fld padding-left-none margin-top">
												<b>Seller Quote</b>
											</div>
											
											<div class="col-md-12 padding-none">

											@if($buyerleadquoteid->initial_quote_price=='0.0000')
												

													@if($buyerleadquoteid->initial_rate_per_kg!='')
													<div class="col-md-3 padding-left-none form-control-fld">
														<input type="text"
															   name="initial_lead_quote_rateperkg_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
															   id="initial_lead_quote_rateperkg_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   placeholder="Rate per Kg *" class="ptl_initial_rate_per_kg form-control form-control1 numberVal fourdigitstwodecimals_deciVal" value="{{ $buyerleadquoteid->initial_rate_per_kg }}" readonly>
													</div>
													@else
													<div class="col-md-3 padding-left-none form-control-fld">
														<input type="text"
															   name="initial_lead_quote_rateperkg_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
															   id="initial_lead_quote_rateperkg_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   placeholder="Rate per Kg *" class="ptl_initial_rate_per_kg form-control form-control1 numberVal fourdigitstwodecimals_deciVal">
													</div>
													@endif

												
												@if((Session::get('service_id') != COURIER) )
													@if($buyerleadquoteid->initial_kg_per_cft!='')
														<div class="col-md-3 form-control-fld">
															@if(Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL )
                                                                                                                            @if(Session::get('service_id') == AIR_DOMESTIC)
                                                                                                                            {{--*/ $cls="clsAirDomKGperCCM" /*--}}
                                                                                                                            @elseif(Session::get('service_id') == AIR_INTERNATIONAL)
                                                                                                                            {{--*/ $cls="clsAirIntKGperCCM" /*--}}
                                                                                                                            @endif
                                                                                                                        <input type="text"
																   name="initial_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="initial_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="{{$cft }} *" class="ptl_initial_conversion form-control form-control1 numberVal fourdigitsfourdecimals_deciVal {{$cls}}" value="{{ $buyerleadquoteid->initial_kg_per_cft }}" readonly>
															@else
															<input type="text"
																   name="initial_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="initial_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="{{$cft }} *" class="ptl_initial_conversion form-control form-control1 numberVal fourdigitsthreedecimals_deciVal" value="{{ $buyerleadquoteid->initial_kg_per_cft }}" readonly>
															@endif
														</div>
													@else
														<div class="col-md-3 form-control-fld">
															@if(Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL )
                                                                                                                            @if(Session::get('service_id') == AIR_DOMESTIC)
                                                                                                                            {{--*/ $cls="clsAirDomKGperCCM" /*--}}
                                                                                                                            @elseif(Session::get('service_id') == AIR_INTERNATIONAL)
                                                                                                                            {{--*/ $cls="clsAirIntKGperCCM" /*--}}
                                                                                                                            @endif
                                                                                                                        <input type="text"
																   name="initial_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="initial_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="{{$cft }} *" class="ptl_initial_conversion form-control form-control1 numberVal fourdigitsfourdecimals_deciVal {{$cls}}" value="">
															@else
															<input type="text"
																   name="initial_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="initial_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="{{$cft }} *" class="ptl_initial_conversion form-control form-control1 numberVal fourdigitsthreedecimals_deciVal" value="">
															@endif
														</div>
														<input type="hidden" id="calculatoropen1" style="border:none;">
													@endif
												@else
													@if($buyerleadquoteid->initial_conversion_factor!='')
														<div class="col-md-3 form-control-fld">
	
															<input type="text"
																   name="initial_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="initial_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="{{$cft }} *" class="ptl_initial_conversion form-control form-control1 numberVal twodigitstwodecimals_deciVal" value="{{ $buyerleadquoteid->initial_conversion_factor }}" readonly>
	
														</div>
													@else
														<div class="col-md-3 form-control-fld">
	
															<input type="text"
																   name="initial_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="initial_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="{{$cft }} *" class="ptl_initial_conversion form-control form-control1 numberVal twodigitstwodecimals_deciVal" value="">
	
														</div>
														<input type="hidden" id="calculatoropen1" style="border:none;">
													@endif
												@endif

											@else
												<div class="col-md-3 padding-left-none"><span class="data-head">Rate per Kg </span><span class="data-value"> Rs {{ $common->getPriceType($buyerleadquoteid->initial_rate_per_kg) }} </span></div>
												@if((Session::get('service_id') != COURIER) )
												<div class="col-md-3 padding-left-none"><span class="data-head">{{ $cft }} </span><span class="data-value"> {{ $buyerleadquoteid->initial_kg_per_cft }}</span></div>
												@else
												<div class="col-md-3 padding-left-none"><span class="data-head">{{ $cft }} </span><span class="data-value"> {{ $buyerleadquoteid->initial_conversion_factor }}</span></div>
												@endif
												<div class="clearfix"></div>
											@endif		
										

											@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN  && Session::get('service_id') != COURIER )
											
												@if($buyerleadquoteid->initial_pick_up_rupees!='')
												<div class="col-md-3 padding-left-none form-control-fld">
													<span class="data-head">Pickup </span><span class="data-value">Rs. {{ $common->getPriceType($buyerleadquoteid->initial_pick_up_rupees) }} </span>

													<input type="hidden"
														   name="initial_lead_quote_pickup_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="initial_lead_quote_pickup_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   placeholder="Pickup Rs *" class="ptl_initial_pickup form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="{{ $buyerleadquoteid->initial_pick_up_rupees }}">
												</div>
												@endif
											
											
												@if($buyerleadquoteid->initial_delivery_rupees!='')
												<div class="col-md-3 form-control-fld">
													<span class="data-head">Delivery </span><span class="data-value">Rs. {{ $common->getPriceType($buyerleadquoteid->initial_delivery_rupees) }} </span>
													<input type="hidden"
														   name="initial_lead_quote_delivery_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="initial_lead_quote_delivery_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   placeholder="Delivery Rs *" class="ptl_initial_delivery form-control form-group" value="{{ $buyerleadquoteid->initial_delivery_rupees }}" >
												</div>
												@endif
											
											
												@if($buyerleadquoteid->initial_oda_rupees!='')
												<div class="col-md-3 form-control-fld">
													<span class="data-head">ODA </span><span class="data-value">Rs. {{ $common->getPriceType($buyerleadquoteid->initial_oda_rupees) }} </span>
													<input type="hidden"
														   name="initial_lead_quote_oda_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="initial_lead_quote_oda_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   placeholder="ODA Rs *" class="form-control form-group" value="{{ $buyerleadquoteid->initial_oda_rupees }}" >
												</div>
												@endif
											
											@endif		
					

											@if(Session::get('service_id') != COURIER) 
												@if($buyerleadquoteid->initial_transit_days!='')
												<div class="col-md-3 padding-right-none form-control-fld">
													<span class="data-head">Transit Days </span><span class="data-value"> {{ $buyerleadquoteid->initial_transit_days }}</span>
													<input type="hidden"
														   name="initial_lead_quote_transit_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="initial_lead_quote_transit_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   placeholder="Transit Days" class="form-control form-group" value="{{ $buyerleadquoteid->initial_transit_days }}">
	
												</div>
												@endif
											@endif
											@if(Session::get('service_id') ==  COURIER)
											
												@if($buyerleadquoteid->initial_quote_price!='0.00')
													<div class="col-md-3 padding-left-none form-control-fld">
														<span class="data-head">Fuel Surcharge </span> <span class="data-value">{{ $buyerleadquoteid->initial_fuel_surcharge_rupees }} %</span>
													</div>
													<input type="hidden"
															   name="initial_fuel_surcharge_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
															   id="initial_fuel_surcharge_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   placeholder="Fuel Surcharge (%) *" class="form-control form-control1 numberVal " value="{{ $buyerleadquoteid->initial_fuel_surcharge_rupees }}">
															
													<div class="col-md-9 padding-left-none form-control-fld">
														<div class="col-md-2 form-control-fld padding-left-none">
															<span class="data-head">COD </span> <span class="data-value"> Rs {{ $buyerleadquoteid->initial_cod_rupees }} %</span>
														</div>
														<input type="hidden"
															   name="initial_cod_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
															   id="initial_cod_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   placeholder="COD (%) *" class="form-control form-control1 numberVal "  value="{{ $buyerleadquoteid->initial_cod_rupees }}">
														<div class="col-md-3 form-control-fld">
															<span class="data-head">Freight Collect </span> <span class="data-value">{{ $buyerleadquoteid->initial_freight_collect_rupees }} /-</span>
														</div>
														<input type="hidden"
															   name="initial_freight_collect_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
															   id="initial_freight_collect_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   placeholder="Freight Collect *" class="form-control form-control1 numberVal "  value="{{ $buyerleadquoteid->initial_freight_collect_rupees }}">
														<div class="col-md-3 form-control-fld">
															<span class="data-head">ARC </span> <span class="data-value">{{ $buyerleadquoteid->initial_arc_rupees }} %</span>
														</div>
														<input type="hidden"
															   name="initial_arc_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
															   id="initial_arc_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   placeholder="ARC (%) *" class="form-control form-control1 numberVal twodigitstwodecimals_deciVal  "  value="{{ $buyerleadquoteid->initial_arc_rupees }}">
														<div class="col-md-3 form-control-fld">
															<span class="data-head">Transit Days </span> <span class="data-value">{{ $buyerleadquoteid->initial_transit_days }} {{ $buyerleadquoteid->initial_transit_units }}</span>
														</div>
														<input type="hidden"
															   name="initial_transit_days_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
															   id="initial_transit_days_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   placeholder="Transit Days *" class="form-control form-control1 numberVal "  value="{{ $buyerleadquoteid->initial_transit_days }}">
														
													</div>
												@endif
											@endif
										</div>
										@if($buyerleadquoteid->initial_freight_amount!='')
										<div class="col-md-3 padding-left-none form-control-fld"><span class="data-head">Freight Amount </span><span class="data-value">Rs
											{{ $common->moneyFormat($buyerleadquoteid->initial_freight_amount,true) }} /-</span>
										</div>
										@endif
										@if($buyerleadquoteid->initial_quote_price!='0.0000')
										<div class="col-md-3 padding-left-none form-control-fld"><span class="data-head">Total Amount </span><span class="data-value" >Rs  
											{{ $common->moneyFormat($buyerleadquoteid->initial_quote_price,true) }} /-</span>
										</div>
										@endif
										
											@if($buyerleadquoteid->initial_quote_price!='0.0000')
												@if($buyerleadquoteid->counter_rate_per_kg !='')
													<div class="col-md-12 padding-left-none form-control-fld margin-top"><b> Buyer Counter Offer </b></div>
													
													<div class="col-md-3 padding-left-none"><span class="data-head">Rate per Kg </span><span class="data-value"> Rs {{ $common->getPriceType($buyerleadquoteid->counter_rate_per_kg) }} </span></div>
													@if(Session::get('service_id') != COURIER)	
													<div class="col-md-3 padding-left-none"><span class="data-head">{{ $cft }} </span><span class="data-value"> {{ $buyerleadquoteid->counter_kg_per_cft }}</span></div>
													@else
													<div class="col-md-3 padding-left-none"><span class="data-head">{{ $cft }} </span><span class="data-value"> {{ $buyerleadquoteid->counter_conversion_factor }}</span></div>
													@endif
													
													<div class="clearfix"></div>

													<input class="form-control " type="hidden"
														   name="counter_lead_quote_rateperkg_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="counter_lead_quote_rateperkg_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   placeholder="Rate per Kg" class="form-control" value="{{ $buyerleadquoteid->counter_rate_per_kg }}">
													@if(Session::get('service_id') != COURIER)	
													<input class="form-control " type="hidden"
														   name="counter_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="counter_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   placeholder="{{$cft}}" class="form-control" value="{{ $buyerleadquoteid->counter_kg_per_cft }}">
													@else
													<input class="form-control " type="hidden"
														   name="counter_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="counter_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   placeholder="{{$cft}}" class="form-control" value="{{ $buyerleadquoteid->counter_conversion_factor }}">
													@endif

													
												@endif
											@endif
										
										@if($buyerleadquoteid->initial_freight_amount!='' && $buyerleadquoteid->counter_freight_amount !='')
										<div class="col-md-3 padding-left-none form-control-fld"><span class="data-head">Freight Amount </span><span class="data-value" >Rs
											{{ $common->moneyFormat($buyerleadquoteid->counter_freight_amount,true) }} /-</span>
										</div>
										@endif
										@if($buyerleadquoteid->initial_quote_price!='0.0000' && $buyerleadquoteid->counter_quote_price !='0.0000' ) 
										<div class="col-md-3 padding-left-none form-control-fld"><span class="data-head">Total Amount </span><span class="data-value" >Rs  
											{{ $common->moneyFormat($buyerleadquoteid->counter_quote_price,true) }}  /-</span>
										</div>
										@endif
									
											<div class="col-md-12 padding-none hide-final">
											@if($buyerleadquoteid->initial_quote_price!='0.0000')
												@if($buyerleadquoteid->counter_quote_price!='0.0000')
													@if($buyerleadquoteid->final_quote_price=='0.0000')
														<div class="col-md-12 form-control-fld padding-left-none margin-top"><b> Seller Final Quote </b></div>
														<div class="col-md-12 padding-none">
														<div class="col-md-3 padding-left-none form-control-fld">
															<input  type="text"
																   name="final_lead_quote_rateperkg_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="final_lead_quote_rateperkg_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="Rate per Kg *" class="ptl_final_rate_per_kg form-control form-control1 numberVal fourdigitstwodecimals_deciVal ">
														</div>
														<div class="col-md-3 form-control-fld">
															@if(Session::get('service_id') ==  AIR_DOMESTIC || Session::get('service_id') ==  AIR_INTERNATIONAL)
                                                                                                                            @if(Session::get('service_id') == AIR_DOMESTIC)
                                                                                                                            {{--*/ $cls="clsAirDomKGperCCM" /*--}}
                                                                                                                            @elseif(Session::get('service_id') == AIR_INTERNATIONAL)
                                                                                                                            {{--*/ $cls="clsAirIntKGperCCM" /*--}}
                                                                                                                            @endif
                                                                                                                        <input  type="text"
																   name="final_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="final_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="{{$cft}} *" class="ptl_final_conversion form-control form-control1 numberVal fourdigitsfourdecimals_deciVal {{$cls}}" value="">
															@else
															<input  type="text"
																   name="final_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="final_lead_quote_kgperdft_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="{{$cft}} *" class="ptl_final_conversion form-control form-control1 numberVal fourdigitsthreedecimals_deciVal " value="">
															@endif
															
														</div>
														 <input type="hidden" id="calculatoropen2" style="border:none;" class="cla_class">
														</div>
													@else
														<div class="col-md-12 form-control-fld padding-left-none margin-top"><b> Seller Final Quote </b></div>
														
														<div class="col-md-3  form-control-fld margin-none padding-left-none"><span class="data-head">Rate per Kg </span><span class="data-value"> Rs {{ $buyerleadquoteid->final_rate_per_kg }} /-</span></div>
														@if(Session::get('service_id') != COURIER)
														<div class="col-md-3 form-control-fld margin-none padding-left-none"><span class="data-head">{{$cft}} </span><span class="data-value"> {{ $buyerleadquoteid->final_kg_per_cft }}</span></div>
														@else
														<div class="col-md-3 form-control-fld margin-none padding-left-none"><span class="data-head">{{$cft}} </span><span class="data-value"> {{ $buyerleadquoteid->final_conversion_factor }}</span></div>
														@endif
														<div class="clearfix"></div>
	
														
													@endif
												@endif
											@endif

											
											@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN  &&  Session::get('service_id') != COURIER)
											
												@if($buyerleadquoteid->initial_pick_up_rupees!='')
													@if($buyerleadquoteid->final_quote_price=='0.0000')
														@if($buyerleadquoteid->counter_quote_price!='0.0000')
														<div class="col-md-3 padding-left-none form-control-fld">
														@if($buyerleadquoteid->is_door_pickup ==1)
															<input type="text"
																   name="final_lead_quote_pickup_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="final_lead_quote_pickup_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="Pickup Rs *" class="ptl_final_pickup form-control form-control1 fourdigitstwodecimals_deciVal numberVal ">
														@else
														<input type="text"
																   name="final_lead_quote_pickup"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="final_lead_quote_pickup" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="No Pickup Rs *" readonly>
														<input type="hidden"
																   name="final_lead_quote_pickup_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="final_lead_quote_pickup_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="Pickup Rs *" class="ptl_final_pickup form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="0">
														@endif
														</div>
														@endif
													@else
													<div class="col-md-3 padding-left-none form-control-fld">
														<span class="data-head">Pickup </span><span class="data-value">Rs. {{ $common->getPriceType($buyerleadquoteid->final_pick_up_rupees) }} </span>
													</div>
													@endif
												@else
												<div class="col-md-3 padding-left-none form-control-fld">
													@if($buyerleadquoteid->is_door_pickup ==1)
													<input type="text"
														   name="initial_lead_quote_pickup_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="initial_lead_quote_pickup_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   placeholder="Pickup Rs *" class="ptl_initial_pickup form-control form-control1 fourdigitstwodecimals_deciVal numberVal " >
													@else
													<input type="text"
														   name="initial_lead_quote_pickup"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="initial_lead_quote_pickup" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="No Pickup Rs *" readonly>
													<input type="hidden"
														   name="initial_lead_quote_pickup_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="initial_lead_quote_pickup_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   placeholder="Pickup Rs *" class="ptl_initial_pickup form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="0">
													@endif
												</div>
												@endif

											
												@if($buyerleadquoteid->initial_delivery_rupees!='')
													@if($buyerleadquoteid->final_quote_price=='0.0000')
														@if($buyerleadquoteid->counter_quote_price!='0.0000')
														<div class="col-md-3 form-control-fld">
															@if($buyerleadquoteid->is_door_delivery ==1)
															<input type="text"
																   name="final_lead_quote_delivery_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="final_lead_quote_delivery_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="Delivery Rs *" class="ptl_final_delivery form-control form-control1 fourdigitstwodecimals_deciVal numberVal ">
															@else
															<input type="text"
																   name="final_lead_quote_delivery"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="final_lead_quote_delivery" class="form-control form-control1 fourdigitsthreedecimals_deciVal numberVal " value="No Delivery Rs *" readonly>
															<input type="hidden"
																   name="final_lead_quote_delivery_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="final_lead_quote_delivery_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="Delivery Rs *" class="ptl_final_delivery form-control form-control1 numberVal " value="0">
															@endif
														</div>
														@endif
													@else
													<div class="col-md-3 padding-left-none form-control-fld ">
														<span class="data-head">Delivery </span><span class="data-value">Rs. {{ $common->getPriceType($buyerleadquoteid->final_delivery_rupees) }} </span>
													</div>
													@endif
												@else
												<div class="col-md-3 form-control-fld">
													@if($buyerleadquoteid->is_door_delivery ==1)
													<input type="text"
														   name="initial_lead_quote_delivery_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="initial_lead_quote_delivery_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   placeholder="Delivery Rs *" class="ptl_initial_delivery form-control form-control1 fourdigitstwodecimals_deciVal numberVal " >
													@else
													<input type="text"
														   name="initial_lead_quote_delivery"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="initial_lead_quote_delivery" class="form-control form-control1 fourdigitsthreedecimals_deciVal numberVal " value="No Delivery Rs *" readonly>
													<input type="hidden"
														   name="initial_lead_quote_delivery_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="initial_lead_quote_delivery_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   placeholder="Delivery Rs *" class="ptl_initial_delivery form-control form-control1 numberVal " value="0">
													@endif
												</div>
												@endif
											

											
												@if($buyerleadquoteid->initial_oda_rupees!='')
													@if($buyerleadquoteid->final_quote_price=='0.0000')
														@if($buyerleadquoteid->counter_quote_price!='0.0000')
														<div class="col-md-3 form-control-fld">
															@if($odacheck == 1)
															<input type="text"
																   name="final_lead_quote_oda_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="final_lead_quote_oda_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="ODA Rs *" class="ptl_final_oda form-control form-control1 fourdigitstwodecimals_deciVal numberVal ">
															@else
															<input type="text"
																   name="final_lead_quote_oda"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="final_lead_quote_oda" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="No ODA Rs *" readonly>
															<input type="hidden"
																   name="final_lead_quote_oda_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="final_lead_quote_oda_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="ODA Rs *" class="ptl_final_oda form-control form-control1 numberVal " value="0">
															@endif
														</div>
														@endif
													@else
													<div class="col-md-3 form-control-fld">
														<span class="data-head">ODA </span><span class="data-value">Rs. {{ $common->getPriceType($buyerleadquoteid->final_oda_rupees) }} </span>
													</div>
													@endif
												@else
												<div class="col-md-3 form-control-fld">
													@if($odacheck == 1)
													<input type="text"
														   name="initial_lead_quote_oda_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="initial_lead_quote_oda_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   placeholder="ODA Rs *" class="ptl_initial_oda form-control fourdigitstwodecimals_deciVal form-control1 numberVal " >
													@else
													<input type="text"
														   name="initial_lead_quote_oda"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="initial_lead_quote_oda" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="No ODA Rs *" readonly>
													<input type="hidden"
														   name="initial_lead_quote_oda_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
														   id="initial_lead_quote_oda_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
														   placeholder="ODA Rs *" class="ptl_initial_oda form-control form-control1 numberVal " value="0">
													@endif
												</div>
												@endif
											
											@endif
											
											
											@if(Session::get('service_id') ==  COURIER)
											
												@if($buyerleadquoteid->initial_quote_price!='0.00')
													
													@if($buyerleadquoteid->counter_quote_price!='0.00')
														@if($buyerleadquoteid->final_quote_price=='0.00')
															<div class="col-md-12 padding-none">
																<div class="col-md-3 form-control-fld padding-left-none">
																	<input type="text"
																	   name="final_fuel_surcharge_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																	   id="final_fuel_surcharge_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																	   placeholder="Fuel Surcharge (%) *" class="ptl_final_fuel form-control form-control1 numberVal twodigitstwodecimals_deciVal" >
																</div>	
																<div class="col-md-9 form-control-fld">
																	<div class="col-md-2 padding-left-none form-control-fld">
																		
																		<input type="text"
																	   name="final_cod_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																	   id="final_cod_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																	   placeholder="COD (%) *" class="ptl_final_cod form-control form-control1 numberVal twodigitstwodecimals_deciVal" >
																	</div>	
																	<div class="col-md-3 form-control-fld">
																		<input type="text"
																	   name="final_freight_collect_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																	   id="final_freight_collect_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																	   placeholder="Freight Collect *" class="ptl_final_freight form-control form-control1 numberVal fivedigitstwodecimals_deciVal" >
																	</div>
																	<div class="col-md-2 form-control-fld">
																		<input type="text"
																	   name="final_arc_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																	   id="final_arc_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																	   placeholder="ARC (%) *" class="ptl_final_arc form-control form-control1 numberVal  twodigitstwodecimals_deciVal " >
																	</div>
																	<div class="col-md-3 form-control-fld padding-none">
																		<div class="input-prepend">
																			<span class="add-on"><i class="fa fa-hourglass-1"></i></span>
																			<input type="text"
																		   name="final_transit_days_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																		   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																		   id="final_transit_days_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																		   placeholder="Transit Days *" maxlength= '3' class="form-control form-control1 twodigitstwodecimals_deciVal numericvalidation" >
																		</div>
																	</div>
					
																	<div class="col-md-2 padding-none">
																		<div class="normal-select">
																			<select class="selectpicker"  id="dayspicker_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}" name="dayspicker_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}">
																				<option value="1">Days</option>
																				<option value="2">Weeks</option>
																			</select>
																		</div>							
																	</div>
																</div>	
															</div>
															@else
																<div class="col-md-3 padding-left-none form-control-fld">
																	<span class="data-head">Fuel Surcharge </span> <span class="data-value">{{ $buyerleadquoteid->final_fuel_surcharge_rupees }} %</span>
																</div>
																
																<div class="col-md-9 padding-left-none form-control-fld">
																	<div class="col-md-2 form-control-fld padding-left-none">
																		<span class="data-head">COD </span> <span class="data-value"> Rs {{ $buyerleadquoteid->final_cod_rupees }} %</span>
																	</div>
																	<div class="col-md-3 form-control-fld">
																		<span class="data-head">Freight Collect </span> <span class="data-value">{{ $buyerleadquoteid->final_freight_collect_rupees }} /-</span>
																	</div>
																	<div class="col-md-3 form-control-fld">
																		<span class="data-head">ARC </span> <span class="data-value">{{ $buyerleadquoteid->final_arc_rupees }} %</span>
																	</div>
																	<div class="col-md-3 form-control-fld">
																		<span class="data-head">Transit Days </span> <span class="data-value">{{ $buyerleadquoteid->final_transit_days }} {{ $buyerleadquoteid->final_transit_units }}</span>
																	</div>
																	
																</div>
															@endif
													@endif
												@else
												<div class="col-md-12 padding-none">
														<div class="col-md-3 form-control-fld padding-left-none">
															<input type="text"
															   name="initial_fuel_surcharge_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
															   id="initial_fuel_surcharge_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   placeholder="Fuel Surcharge (%) *" class="ptl_initial_fuel form-control form-control1 numberVal twodigitstwodecimals_deciVal" >
														</div>	
														<div class="col-md-9 form-control-fld">
															<div class="col-md-2 padding-left-none form-control-fld">
																
																<input type="text"
															   name="initial_cod_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
															   id="initial_cod_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   placeholder="COD (%) *" class="ptl_initial_cod form-control form-control1 numberVal twodigitstwodecimals_deciVal" >
															</div>	
															<div class="col-md-3 form-control-fld">
																<input type="text"
															   name="initial_freight_collect_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
															   id="initial_freight_collect_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   placeholder="Freight Collect *" class="ptl_initial_freight form-control form-control1 numberVal fivedigitstwodecimals_deciVal" >
															</div>
															<div class="col-md-2 form-control-fld">
																<input type="text"
															   name="initial_arc_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
															   id="initial_arc_rupees_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   placeholder="ARC (%) *" class="ptl_initial_arc form-control form-control1 numberVal  twodigitstwodecimals_deciVal " >
															</div>
															<div class="col-md-3 form-control-fld padding-none">
																<div class="input-prepend">
																	<span class="add-on"><i class="fa fa-hourglass-1"></i></span>
																	<input type="text"
																   name="initial_transit_days_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																   id="initial_transit_days_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																   placeholder="Transit Days *" maxlength= '3' class="form-control form-control1 twodigitstwodecimals_deciVal numericvalidation" >
																</div>
															</div>
			
															<div class="col-md-2 padding-none">
																<div class="normal-select">
																	<select class="selectpicker"  id="dayspicker_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}" name="dayspicker_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}">
																		<option value="1">Days</option>
																		<option value="2">Weeks</option>
																	</select>
																</div>							
															</div>
														</div>	
													</div>
												@endif
											
											@endif
											
											@if(Session::get('service_id') !=  COURIER)
											
												@if($buyerleadquoteid->initial_transit_days!='')
													@if($buyerleadquoteid->final_quote_price=='0.0000')
														@if($buyerleadquoteid->counter_quote_price!='0.0000')
														@if(Session::get('service_id') ==  ROAD_PTL)
															<div class="col-md-3 padding-left-none form-control-fld">
																<input type="text"
																	   name="final_lead_quote_transit_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																	   id="final_lead_quote_transit_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																	   placeholder="Transit Days *" maxlength= '3' class="form-control form-control1 maxlimitthree_lmtVal numericvalidation" >
															</div>
														@else
															<div class="col-md-3 padding-left-none form-control-fld">
																<input type="text"
																	   name="final_lead_quote_transit_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
																	   id="final_lead_quote_transit_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
																	   placeholder="Transit Days *" maxlength= '2' class="form-control form-control1 maxlimitthree_lmtVal numericvalidation" >
															</div>
														@endif
														@endif
													@else
													<div class="col-md-3 padding-left-none form-control-fld">
														<span class="data-head">Transit Days </span><span class="data-value"> {{ $buyerleadquoteid->final_transit_days }}</span>
													</div>
													@endif
												@else
												@if(Session::get('service_id') ==  ROAD_PTL)
													<div class="col-md-3 padding-left-none form-control-fld">
														<input type="text"
															   name="initial_lead_quote_transit_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
															   id="initial_lead_quote_transit_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   placeholder="Transit Days *" maxlength= '3' class="form-control form-control1 maxlimitthree_lmtVal numericvalidation">
													</div>
												@else
													<div class="col-md-3 padding-left-none form-control-fld">
														<input type="text"
															   name="initial_lead_quote_transit_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerleadquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerleadquoteid->ptlquoteid }}"
															   id="initial_lead_quote_transit_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}"
															   placeholder="Transit Days *" maxlength= '2' class="form-control form-control1 maxlimitthree_lmtVal numericvalidation">
													</div>
												
												@endif
												@endif
											@endif
											
											
											
										@if($buyerleadquoteid->initial_freight_amount=='' && $buyerleadquoteid->counter_freight_amount=='')
											<div class="clearfix"></div>
											<div class="clearfix margin-none"></div>
											<div class="col-md-3 padding-none margin-top-none">
												<span  class="data-head padding-top1">Freight Amount (Rs) : </span>
												<span class="data-value" id="freight_charges_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}">
													0.00 /-
												</span>
												
											</div>	
										@elseif($buyerleadquoteid->final_freight_amount =='' && $buyerleadquoteid->counter_freight_amount !='')
											<div class="clearfix"></div>
											<div class="clearfix margin-none"></div>
											<div class="col-md-3 padding-none margin-top-none">
												<span  class="data-head padding-top1">Freight Amount (Rs) : </span>
												<span class="data-value" id="freight_charges_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}">
												0.00 /-
												</span>
												
											</div>	
										@elseif($buyerleadquoteid->final_freight_amount !='')
											<div class="clearfix"></div>
											<div class="clearfix margin-none"></div>
											<div class="col-md-3 padding-none margin-top-none">
												<span  class="data-head padding-top1">Freight Amount (Rs) : </span>
												<span class="data-value" id="freight_charges_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}">
													{{ $common->moneyFormat($buyerleadquoteid->final_freight_amount,true) }} /-
												</span>
												
											</div>	
										@endif
										
										
										
										
										
										@if($buyerleadquoteid->initial_quote_price=='0.0000' && $buyerleadquoteid->counter_quote_price=='0.0000')
											<div class="col-md-3 form-control-fld padding-left-none margin-none">
											<span class="data-head" >Total Amount (Rs) :</span> <span class="data-value" id="total_charges_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}">
												0.00 /-</span>
											</div>
										@elseif($buyerleadquoteid->final_quote_price=='0.0000' && $buyerleadquoteid->counter_quote_price!='0.0000')
											<div class="col-md-3 form-control-fld padding-left-none margin-none">
											<span class="data-head" >Total Amount (Rs) :</span> <span class="data-value" id="total_charges_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}">
												0.00 /-</span>
											</div>
										@elseif($buyerleadquoteid->final_quote_price!='0.0000')
											<div class="col-md-3 form-control-fld padding-left-none margin-none">
											<span class="data-head" >Total Amount (Rs) :</span> <span class="data-value" id="total_charges_{{ $buyerleadquoteid->buyer_id }}_{{ $buyerleadquoteid->ptlquoteid }}">
												{{ $common->moneyFormat($buyerleadquoteid->final_quote_price) }} /-</span>
											</div>
										@endif
											
										</div>	
										<div class="hide-submit">
											@if($buyerleadquoteid->initial_freight_amount=='')
												{!! Form::button('Submit', array('id'=>'ptl_lead_initial_quote_submit_'.$buyerleadquoteid->buyer_id.'_'.$buyerleadquoteid->ptlquoteid,'class'=>'btn add-btn margin-top pull-right  ptl_lead_initial_quote_submit margin-bottom', 'name'=>$buyerleadquoteid->ptlquoteid)) !!}
											@elseif($buyerleadquoteid->counter_freight_amount!='' && $buyerleadquoteid->final_freight_amount=='')
												{!! Form::button('Submit', array('id'=>'ptl_lead_final_quote_submit_'.$buyerleadquoteid->buyer_id.'_'.$buyerleadquoteid->ptlquoteid,'class'=>'btn add-btn margin-top pull-right  ptl_lead_final_quote_submit margin-bottom', 'name'=>$buyerleadquoteid->ptlquoteid)) !!}
											@endif
										</div>


											

										</div>
										<div class="show-submit">
											
											{!! Form::button('Accept', array('id'=>'ptl_lead_counter_quote_submit_'.$buyerleadquoteid->buyer_id.'_'.$buyerleadquoteid->ptlquoteid,'class'=>'btn add-btn margin-top pull-right  ptl_lead_counter_quote_submit margin-bottom', 'name'=>$buyerleadquoteid->ptlquoteid)) !!}
										</div>
								</div>
								{!! Form::close() !!}
								
							</div>

							@endforeach
						@endif
						{{--*/ $i++  /*--}}

						</div> <!-- Table row -->
                                                </div>  

                        {{--*/ $enquiry_style   =($type=="enquiries")?"style=display:block":"style=display:none" /*--}}    
						@if($seller_post[0]->lkp_post_status_id != 1)
					<div class="table-data" id="ftl-seller-enquiry" {{$enquiry_style}}>
                        <div class="table-div margin-none">
								
						<!-- Table Head Starts Here -->

						<div class="table-heading inner-block-bg">
							<div class="col-md-2 padding-left-none">Buyer Name<i class="fa fa-caret-down"></i></div>
							<div class="col-md-3 padding-left-none">Dispatch Date - Delivery Date<i class="fa fa-caret-down"></i></div>
							<div class="col-md-2 padding-left-none">From Location<i class="fa fa-caret-down"></i></div>
							<div class="col-md-2 padding-left-none">To Location<i class="fa fa-caret-down"></i></div>
							<div class="col-md-1 padding-left-none">Status<i class="fa fa-caret-down"></i></div>
							<div class="col-md-2 padding-left-none"></div>
						</div>
							<!-- Table Row Starts Here -->
							@if(isset($buyerquoteid))
								@if(count($buyerquoteid)==0)
								<div class="table-data">
									<div class="table-row inner-block-bg text-center">
										No records founds
									</div>
								</div>
								@endif
							@endif


							{{--*/ $i = 0 /*--}}
							@if(isset($buyerquoteid[0]->username))
								@foreach($buyerquoteid as $buyerquoteid)
								{{--*/ $odacheck = $common->sellerODACheck($buyerquoteid->to_location_id,Session::get('service_id')) /*--}}
							<div class="table-row inner-block-bg">
								<div class="col-md-2 padding-left-none">
									{!! $buyerquoteid->username !!}
									<div class="red">
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
										<i class="fa fa-star"></i>
									</div>
								</div>
								<div class="col-md-3 padding-left-none">{!! $common->checkAndGetDate($buyerquoteid->dispatch_date) !!} -
											@if($common->checkAndGetDate($buyerquoteid->delivery_date) == '')
												N/A
											@else
												{!! $common->checkAndGetDate($buyerquoteid->delivery_date) !!}
											@endif</div>


								@if((Session::get('service_id') == AIR_INTERNATIONAL) )
									<div class="col-md-2 padding-none">
										{{ $common->getAirportName($buyerquoteid->from_location_id) }}
									</div>
									<div class="col-md-2 padding-none">
										{{ $common->getAirportName($buyerquoteid->to_location_id) }}
									</div>
									@elseif((Session::get('service_id') == OCEAN) )
									<div class="col-md-2 padding-none">
											{{ $common->getSeaportName($buyerquoteid->from_location_id) }}
									</div>
									<div class="col-md-2 padding-none">
												{{ $common->getSeaportName($buyerquoteid->to_location_id) }}
									</div>
									@else
									<div class="col-md-2 padding-none">
												{{ $common->getZonePin($buyerquoteid->from_location_id) }}
									</div>
									<div class="col-md-2 padding-none">
										@if((Session::get('service_id') == COURIER) )
											@if($buyerquoteid->lkp_courier_delivery_type_id == 1)
												{{ $common->getZonePin($buyerquoteid->to_location_id) }}
											@else
												{{ $common->getCountry($buyerquoteid->to_location_id) }}
											@endif
										@else
										{{ $common->getZonePin($buyerquoteid->to_location_id) }}
										@endif	
									</div>
								@endif
																	
								<div class="col-md-1 padding-left-none">{{ $common->getSellerPostStatuss($buyerquoteid->lkp_post_status_id) }}</div>
								
									@if($buyerquoteid->lkp_post_status_id==2)
										@if(isset($buyerquoteid->initial_quote_price) &&
                                                    $buyerquoteid->initial_quote_price =='0.0000' &&
                                                    isset($buyerquoteid->counter_quote_price) &&
                                                    $buyerquoteid->counter_quote_price =='0.0000')
											<div class="col-md-2 padding-none">
												<span class="btn red-btn pull-right  underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}" id="click-link" >Submit Quote <i class="fa fa-rupee"></i>
												</span>
											</div>
											<div class="clearfix"></div>
										@endif
										@if(isset($buyerquoteid->initial_quote_price) &&
                                                    $buyerquoteid->initial_quote_price !='0.0000' &&
                                                    isset($buyerquoteid->counter_quote_price) &&
                                                    $buyerquoteid->counter_quote_price =='0.0000')
											<div class="col-md-2 padding-none">
												<span class="btn red-btn pull-right  underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}" id="click-link" >Initial Quote Submitted <i class="fa fa-rupee"></i>
											</span>
											</div>
											<div class="clearfix"></div>
										@endif

										@if(isset($buyerquoteid->initial_quote_price) &&
                                                            $buyerquoteid->initial_quote_price !='0.0000' &&
                                                            isset($buyerquoteid->counter_quote_price) &&
                                                            $buyerquoteid->counter_quote_price !='0.0000' &&
                                                            isset($buyerquoteid->final_quote_price) &&
                                                            $buyerquoteid->final_quote_price !='0.0000' )
											<div class="col-md-2 padding-none">
											<span class="btn red-btn pull-right  underline_link seller_submit_quote" data-buyernbuyerquoteid="{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}" id="click-link" >Final Quote Submitted <i class="fa fa-rupee"></i>
											</span>
											</div>
											<div class="clearfix"></div>
										@endif

										
										@if(isset($buyerquoteid->initial_quote_price) &&
                                                            $buyerquoteid->initial_quote_price !='0.0000' &&
                                                            isset($buyerquoteid->counter_quote_price) &&
                                                            $buyerquoteid->counter_quote_price !='0.0000' &&
                                                            isset($buyerquoteid->final_quote_price) &&
                                                            $buyerquoteid->final_quote_price =='0.0000' )
												<div class="col-md-2 padding-none">
													<span class="btn red-btn pull-right  underline_link seller_submit_quote margin-bottom" data-buyernbuyerquoteid="{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}" id="click-link" >Submit Final Quote <i class="fa fa-rupee"></i>
													</span>
												
													<span class="btn red-btn pull-right  underline_link seller_submit_quote seller_counter" data-buyernbuyerquoteid="{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}" id="click-link" >Accept Counter offer <i class="fa fa-rupee"></i>
													</span>
												</div>
												<div class="clearfix"></div>
												
										@endif
										
									@endif
								

								<div class="pull-right text-right">
									<div class="info-links">
										<span class="detailsslide  underline_link" data-buyersearchlistid="{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"><span class="show_details" style="display: inline;">+</span><span class="hide_details" style="display: none;">-</span> Details</span>
										<a href="#" class="red underline_link new_message" data-userid='{{ $buyerquoteid->buyer_id }}' data-buyer-transaction-leads="{{$buyerquoteid->transaction_no}}" data-id="{{ Session::get('seller_post_item') }}" data-buyerquoteitemidforsellerleads="{{ $buyerquoteid->ptlquoteid }}" ><i class="fa fa-envelope-o"></i></a>
									</div>
								</div>
								@if(Session::get('service_id') != COURIER)
									{!! Form::open(array('url' => 'sellersubmitquote', 'id' => 'addptlsellerpostquoteoffer', 'name' => 'addptlsellerpostquoteoffer' ,'class'=>'formquoteid_'.$buyerquoteid->ptlquoteid)) !!}
								@else
									{!! Form::open(array('url' => 'sellersubmitquote', 'id' => 'addcouriersellerpostquoteoffer', 'name' => 'addcouriersellerpostquoteoffer' ,'class'=>'formquoteid_'.$buyerquoteid->ptlquoteid)) !!}
								@endif

										@if(Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL)
											{{--*/ $cft ='Conversion Kg/CCM' /*--}}
										@elseif(Session::get('service_id') == OCEAN)
											{{--*/ $cft ='Conversion Kg/CBM' /*--}}
										@elseif(Session::get('service_id') == COURIER)
											{{--*/ $cft ='Conversion Factor (CCM/KG)' /*--}}
										@else
											{{--*/ $cft ='Conversion Kg/CFT' /*--}}
										@endif
								<div class="col-md-12 show-data-div padding-none padding-top quote_details_1_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }} margin-top" style="display:none">

									
									
									
									
									@if((Session::get('service_id') == AIR_INTERNATIONAL) )
										<h2 class="sub-head"><span class="from-head">
											{{ $common->getAirportName($buyerquoteid->from_location_id) }}</span> - <span class="to-head">{{ $common->getAirportName($buyerquoteid->to_location_id) }}
										</span></h2>
										@elseif((Session::get('service_id') == OCEAN) )
										<h2 class="sub-head"><span class="from-head">
											{{ $common->getSeaportName($buyerquoteid->from_location_id) }}</span> - <span class="to-head">{{ $common->getSeaportName($buyerquoteid->to_location_id) }}
										</span></h2>
										@else
										<h2 class="sub-head"><span class="from-head">
													{{ $common->getPinName($buyerquoteid->from_location_id) }}</span> - <span class="to-head">
													@if((Session::get('service_id') == COURIER) )
														@if($buyerquoteid->lkp_courier_delivery_type_id == 1)
															{{ $common->getPinName($buyerquoteid->to_location_id) }}
														@else
															{{ $common->getCountry($buyerquoteid->to_location_id) }}
														@endif
													@else
														{{ $common->getPinName($buyerquoteid->to_location_id) }}
													@endif
													
										</span></h2>
									@endif
									
									
									<div class="table-div table-style1">
							
											<!-- Table Head Starts Here -->

											<div class="table-heading inner-block-bg">
											@if((Session::get('service_id') != COURIER) )
												<div class="col-md-3 padding-left-none">Load type<i class="fa fa-caret-down"></i></div>
											@else
												<div class="col-md-3 padding-left-none">Courier type<i class="fa fa-caret-down"></i></div>
											@endif
												<div class="col-md-2 padding-left-none">Volume<i class="fa fa-caret-down"></i></div>
												<div class="col-md-2 padding-left-none">Unit Weight<i class="fa fa-caret-down"></i></div>
												<div class="col-md-3 padding-left-none">No of Packages<i class="fa fa-caret-down"></i></div>
											@if((Session::get('service_id') != COURIER) )
												<div class="col-md-2 padding-left-none">Package Type<i class="fa fa-caret-down"></i></div>
											@else
												<div class="col-md-2 padding-left-none">Courier Delivery Type<i class="fa fa-caret-down"></i></div>
											@endif
												
												
											</div>

											<!-- Table Head Ends Here -->

											<div class="table-data">
												
											@for($j=0;$j<count($buyerpublicquotedetails[0]);$j++)

												@if( $buyerpublicquotedetails[0][$j]->ptlquoteid == $buyerquoteid->ptlquoteid )
												<!-- Table Row Starts Here -->

												<div class="table-row inner-block-bg">
													@if((Session::get('service_id') != COURIER) )
													<div class="col-md-3 padding-left-none">{!! $buyerpublicquotedetails[0][$j]->load_type !!}</div>
													@else
														@if($buyerpublicquotedetails[0][$j]->lkp_courier_type_id == 1)
														<div class="col-md-3 padding-left-none">Document</div>
														@else
														<div class="col-md-3 padding-left-none">Parcel</div>
														@endif
													@endif
													<div class="col-md-2 padding-left-none">
													
													@if((Session::get('service_id') == AIR_DOMESTIC) || (Session::get('service_id') == AIR_INTERNATIONAL))
														{!! round($buyerpublicquotedetails[0][$j]->calculated_volume_weight,4) !!} CCM
													@elseif((Session::get('service_id') == OCEAN))
														{!! round($buyerpublicquotedetails[0][$j]->calculated_volume_weight,4) !!} CBM
													@elseif((Session::get('service_id') == COURIER))
														{!! round($buyerpublicquotedetails[0][$j]->calculated_volume_weight,4) !!} CCM
													@else
														{!! round($buyerpublicquotedetails[0][$j]->calculated_volume_weight,4) !!} CFT
													@endif
													</div>
													<div class="col-md-2 padding-left-none">
														@if($buyerpublicquotedetails[0][$j]->weight_type == 'Gms')
															{{--*/ $buyerpublicquotedetails[0][$j]->units = $buyerpublicquotedetails[0][$j]->units * 0.001  /*--}}
															{!! $buyerpublicquotedetails[0][$j]->units !!} Kgs
															@elseif($buyerpublicquotedetails[0][$j]->weight_type == 'MTs')
															{{--*/ $buyerpublicquotedetails[0][$j]->units = $buyerpublicquotedetails[0][$j]->units * 1000  /*--}}
															{!! $buyerpublicquotedetails[0][$j]->units !!} Kgs
														@else
															{!! $buyerpublicquotedetails[0][$j]->units !!} {!! $buyerpublicquotedetails[0][$j]->weight_type !!}
														@endif
													</div>
													<div class="col-md-3 padding-left-none">{!! $buyerpublicquotedetails[0][$j]->number_packages !!}</div>
													@if((Session::get('service_id') != COURIER) )
														<div class="col-md-2 padding-left-none">{!! $buyerpublicquotedetails[0][$j]->packaging_type_name !!}</div>
													@else
														@if($buyerpublicquotedetails[0][$j]->lkp_courier_delivery_type_id == 1)
															<div class="col-md-2 padding-left-none">Domestic</div>
														@else
															<div class="col-md-2 padding-left-none">International</div>
														@endif
													@endif
													<input type='hidden' name='volumetric_{{ $j }}' id='volumetric_{{ $j }}' value="{{ $buyerpublicquotedetails[0][$j]->calculated_volume_weight }}">
													<input type='hidden' name='units_{{ $j }}' id='units_{{ $j }}' value="{{ $buyerpublicquotedetails[0][$j]->units }}">
													<input type='hidden' name='weighttype_{{ $j }}' id='weighttype_{{ $j }}' value="{{ $buyerpublicquotedetails[0][$j]->weight_type }}">
													<input type='hidden' name='packagenos_{{ $j }}' id='packagenos_{{ $j }}' value="{{ $buyerpublicquotedetails[0][$j]->number_packages }}">
													@if((Session::get('service_id') == COURIER) )
													
														<input type='hidden' name='courier_{{ $j }}' id='courier_{{ $j }}' value="{{ $buyerpublicquotedetails[0][$j]->lkp_courier_type_id }}">
														<input type='hidden' name='packagevalue_{{ $j }}' id='packagevalue_{{ $j }}' value="{{ $buyerpublicquotedetails[0][$j]->package_value }}">	
													@endif
												</div>

												<!-- Table Row Ends Here -->
												@endif
											@endfor
											<input type='hidden' name='incrementcount_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}' id='incrementcount_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}' value="{{ $j }}">

											<input type="hidden" name="buyerquoteid_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}" id="buyerquoteid_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}" value="{{ $buyerquoteid->ptlquoteid }}">
												
											<input type='hidden' name='seller_post_item_id' id='seller_post_item_id' value="{{ Session::get('seller_post_item') }}">
											<input type='hidden' name='volumetric_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}' id='volumetric_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}' value="{{ $buyerquoteid->calculated_volume_weight }}">
											<input type='hidden' name='packagenos_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}' id='packagenos_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}' value="{{ $buyerquoteid->number_packages }}">
											<input type='hidden' name='units_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}' id='units_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}' value="{{ $buyerquoteid->units }}">
											<input type='hidden' name='sellerkgpercft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}' id='sellerkgpercft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}' value="{{ $kgpercft }}">
											@if((Session::get('service_id') == COURIER) )
											<input type='hidden' name='packagevalue_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}' id='packagevalue_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}' value="{{ $buyerquoteid->package_value }}">
											@endif
											</div>

										</div>

								</div>

								<div class="col-md-12 padding-none quote_details_2_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"  style="display:none">
										<div class="col-md-12 border-top margin-top">
											<div class="col-md-12 form-control-fld padding-left-none margin-top">
												<b>Seller Quote</b>
											</div>
											<div class="col-md-12 padding-none">
											@if($buyerquoteid->initial_quote_price=='0.0000')
												<div class="col-md-3 padding-left-none form-control-fld">

													@if($buyerquoteid->initial_rate_per_kg!='')
														<input type="text"
															   name="initial_quote_rateperkg_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_quote_rateperkg_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="Rate per Kg *" class="ptl_initial_rate_per_kg form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="{{ $buyerquoteid->initial_rate_per_kg }}" readonly>
													@else
														<input type="text"
															   name="initial_quote_rateperkg_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_quote_rateperkg_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="Rate per Kg *" class="ptl_initial_rate_per_kg form-control form-control1 fourdigitstwodecimals_deciVal numberVal ">
													@endif

												</div>
												@if((Session::get('service_id') != COURIER) )
													@if($buyerquoteid->initial_kg_per_cft!='')
														<div class="col-md-3 padding-left-none form-control-fld">
	
															<input type="text"
																   name="initial_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																   id="initial_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   placeholder="{{$cft }} *" class="ptl_initial_conversion form-control form-control1 fourdigitsthreedecimals_deciVal numberVal " value="{{ $buyerquoteid->initial_kg_per_cft }}" readonly>
	
														</div>
													@else
														<div class="col-md-3 padding-left-none form-control-fld">                                           @if(Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL)                                                                    	{{--*/ $strcls="" /*--}}                                                                @else
                                                          {{--*/ $strcls="fourdigitsthreedecimals_deciVal" /*--}}
                                                        @endif                                                                                    @if(Session::get('service_id') == AIR_DOMESTIC)
                                                        	{{--*/ $cls="clsAirDomKGperCCM" /*--}}
                                                        @elseif(Session::get('service_id') == AIR_INTERNATIONAL)
                                                            {{--*/ $cls="clsAirIntKGperCCM" /*--}}
                                                        @endif
															<input type="text"
																   name="initial_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																   id="initial_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   placeholder="{{$cft }} *" class="ptl_initial_conversion form-control form-control1 {{$strcls}} {{$cls}}" value="">
	
														</div>
														<div class="col-md-3 padding-left-none form-control-fld">
															<input type="hidden" id="calculatoropen3" style="border:none;">
														</div>
													@endif
												@else
													@if($buyerquoteid->initial_conversion_factor!='')
														<div class="col-md-3 form-control-fld">
	
															<input type="text"
																   name="initial_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																   id="initial_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   placeholder="{{$cft }} *" class="ptl_initial_conversion form-control form-control1 numberVal fourdigitsthreedecimals_deciVal" value="{{ $buyerquoteid->initial_conversion_factor }}" readonly>
	
														</div>
													@else
														<div class="col-md-3  form-control-fld">
	
															<input type="text"
																   name="initial_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																   id="initial_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   placeholder="{{$cft }} *" class="ptl_initial_conversion form-control form-control1 numberVal twodigitstwodecimals_deciVal" value="">
	
														</div>
														<div class="col-md-3 padding-left-none form-control-fld">
															<input type="hidden" id="calculatoropen3" style="border:none;">
														</div>
													@endif
												@endif
											@else
												<div class="col-md-3 padding-left-none form-control-fld margin-none"><span class="data-head">Rate per Kg </span> <span class="data-value"> Rs {{ $buyerquoteid->initial_rate_per_kg }} /-</span></div>
												@if((Session::get('service_id') != COURIER) )
												<div class="col-md-3  form-control-fld margin-none"><span class="data-head">{{ $cft }} </span> <span class="data-value"> {{ $buyerquoteid->initial_kg_per_cft }}</span></div>
												@else
												<div class="col-md-3 padding-left-none form-control-fld margin-none"><span class="data-head">{{ $cft }} </span> <span class="data-value"> {{ $buyerquoteid->initial_conversion_factor }}</span></div>
												@endif
												<div class="clearfix"></div>
												
											@endif
											
											
											@if(Session::get('service_id') != COURIER)
												@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN)
													<div class="col-md-3 padding-left-none form-control-fld margin-none">
													@if($buyerquoteid->initial_pick_up_rupees!='')
														<span class="data-head">Pickup </span> <span class="data-value">Rs {{ $common->getPriceType($buyerquoteid->initial_pick_up_rupees) }} </span>
		
														<input type="hidden"
															   name="initial_quote_pickup_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_quote_pickup_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="Pickup Rs *" class="form-control form-group" value="{{ $buyerquoteid->initial_pick_up_rupees }}">
		
													@endif
													</div>
													<div class="col-md-3 form-control-fld margin-none">
													@if($buyerquoteid->initial_delivery_rupees!='')
													<span class="data-head">Delivery </span> <span class="data-value">Rs {{ $common->getPriceType($buyerquoteid->initial_delivery_rupees) }} </span>
													<input type="hidden"
														   name="initial_quote_delivery_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
														   id="initial_quote_delivery_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   placeholder="Delivery Rs *" class="form-control form-group" value="{{ $buyerquoteid->initial_delivery_rupees }}" >
													@endif
													</div>
													<div class="col-md-3 form-control-fld margin-none">
													@if($buyerquoteid->initial_oda_rupees!='')
													<span class="data-head">ODA </span> <span class="data-value">Rs {{ $common->getPriceType($buyerquoteid->initial_oda_rupees) }} </span>
													<input type="hidden"
														   name="initial_quote_oda_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
														   id="initial_quote_oda_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   placeholder="ODA Rs *" class="form-control form-group" value="{{ $buyerquoteid->initial_oda_rupees }}" >
	
													@endif
													</div>
												@endif
												<div class="col-md-3 padding-none form-control-fld margin-none">
												@if($buyerquoteid->initial_transit_days!='')
													<span class="data-head">Transit Days </span> <span class="data-value"> {{ $buyerquoteid->initial_transit_days }}</span>
													<input type="hidden"
														   name="initial_quote_transit_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
														   id="initial_quote_transit_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   placeholder="Transit Days *" class="form-control form-group" value="{{ $buyerquoteid->initial_transit_days }}">
	
												@endif
												</div>
											@else
												@if($buyerquoteid->initial_quote_price!='0.0000')
													
													<div class="col-md-3 padding-left-none form-control-fld">
														<span class="data-head">Fuel Surcharge </span> <span class="data-value">{{ $buyerquoteid->initial_fuel_surcharge_rupees }} %</span>
													</div>
													<input type="hidden"
															   name="initial_fuel_surcharge_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_fuel_surcharge_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="Pickup Rs *" class="form-control form-group" value="{{ $buyerquoteid->initial_fuel_surcharge_rupees }}">
													<div class="col-md-9 form-control-fld padding-left-none">
														<div class="col-md-2 form-control-fld padding-left-none">
															<span class="data-head">COD </span> <span class="data-value"> Rs {{ $buyerquoteid->initial_cod_rupees }} %</span>
														</div>
														
														<input type="hidden"
															   name="initial_cod_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_cod_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="Pickup Rs *" class="form-control form-group" value="{{ $buyerquoteid->initial_cod_rupees }}">
														<div class="col-md-3 form-control-fld">
															<span class="data-head">Freight Collect </span> <span class="data-value">{{ $buyerquoteid->initial_freight_collect_rupees }} /-</span>
														</div>
														
														<input type="hidden"
															   name="initial_freight_collect_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_freight_collect_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="Pickup Rs *" class="form-control form-group" value="{{ $buyerquoteid->initial_freight_collect_rupees }}">
														<div class="col-md-3 form-control-fld">
															<span class="data-head">ARC </span> <span class="data-value">{{ $buyerquoteid->initial_arc_rupees }} %</span>
														</div>
														
														<input type="hidden"
															   name="initial_arc_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_arc_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="Pickup Rs *" class="form-control form-group" value="{{ $buyerquoteid->initial_arc_rupees }}">
														<div class="col-md-3 form-control-fld">
															<span class="data-head">Transit Days </span> <span class="data-value">{{ $buyerquoteid->initial_transit_days }} {{ $buyerquoteid->initial_transit_units }}</span>
														</div>
														
														<input type="hidden"
															   name="initial_transit_days_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_transit_days_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="Pickup Rs *" class="form-control form-group" value="{{ $buyerquoteid->initial_transit_days }}">
														
													</div>
												@endif
											@endif
										</div>
										@if($buyerquoteid->initial_freight_amount!='')
											<div class="clearfix"></div>
											<div class="clearfix margin-none"></div>
											<div class="col-md-3 padding-none margin-top-none">
												<span  class="data-head padding-top1">Freight Amount (Rs) : </span>
												<span class="data-value" >
												
													{{ $common->moneyFormat($buyerquoteid->initial_freight_amount,true) }} 
												
												/-</span>
												
											</div>
										@endif	
										@if($buyerquoteid->initial_quote_price!='0.0000')
											<div class="col-md-3 form-control-fld padding-left-none margin-none">
											<span class="data-head" >Total Amount (Rs) :</span> <span class="data-value" >
												{{ $common->moneyFormat($buyerquoteid->initial_quote_price,true) }} 
												
											/-</span>
											</div>
										@endif
										@if($buyerquoteid->initial_quote_price!='0.0000')
											@if($buyerquoteid->counter_rate_per_kg !='')
												<div class="col-md-12 padding-left-none form-control-fld margin-top"><b> Buyer Counter Offer </b></div>
												
												<div class="col-md-3 padding-left-none form-control-fld margin-none"><span class="data-head">Rate per Kg </span> <span class="data-value"> Rs {{ $buyerquoteid->counter_rate_per_kg }} /-</span></div>
												@if(Session::get('service_id') != COURIER)
													<div class="col-md-3 padding-left-none form-control-fld margin-none"><span class="data-head">{{ $cft }} </span> <span class="data-value"> {{ $buyerquoteid->counter_kg_per_cft }}</span></div>
												@else
													<div class="col-md-3 padding-left-none form-control-fld margin-none"><span class="data-head">{{ $cft }} </span> <span class="data-value"> {{ $buyerquoteid->counter_conversion_factor }}</span></div>
												@endif
												<div class="clearfix"></div>
	
												<input class="form-control form-group" type="hidden"
													   name="counter_quote_rateperkg_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
													   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
													   id="counter_quote_rateperkg_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
													   placeholder="Rate per Kg *" class="form-control" value="{{ $buyerquoteid->counter_rate_per_kg }}">
												@if(Session::get('service_id') != COURIER)
												<input class="form-control form-group" type="hidden"
													   name="counter_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
													   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
													   id="counter_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
													   placeholder="{{$cft}} *" class="form-control" value="{{ $buyerquoteid->counter_kg_per_cft }}">
												@else
												<input class="form-control form-group" type="hidden"
													   name="counter_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
													   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
													   id="counter_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
													   placeholder="{{$cft}} *" class="form-control" value="{{ $buyerquoteid->counter_conversion_factor }}">
												@endif

												
											@endif
										@endif
										
										@if($buyerquoteid->counter_freight_amount !='')
											<div class="clearfix"></div>
											<div class="clearfix margin-none"></div>
											<div class="col-md-3 padding-none margin-top-none">
												<span  class="data-head padding-top1">Freight Amount (Rs) : </span>
												<span class="data-value" >
													{{ $common->moneyFormat($buyerquoteid->counter_freight_amount,true) }}/-
												</span>
											</div>	
										@endif
										@if($buyerquoteid->counter_quote_price !='0.0000')
											<div class="col-md-3 form-control-fld padding-left-none margin-none">
											<span class="data-head" >Total Amount (Rs) :</span> <span class="data-value" >
												{{ $common->moneyFormat($buyerquoteid->counter_quote_price,true) }}/-
											</span>
											</div>
										@endif	
								
										<div class="hide-final">
											@if($buyerquoteid->initial_quote_price!='0.0000')
												@if($buyerquoteid->counter_quote_price!='0.0000')
													@if($buyerquoteid->final_quote_price=='0.0000')
														<div class="col-md-12 form-control-fld padding-left-none margin-top "><b> Seller Final Quote </b></div>
														<div class="col-md-12 padding-none">
														<div class="col-md-3 padding-left-none form-control-fld">
															<input  type="text"
																   name="final_quote_rateperkg_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																   id="final_quote_rateperkg_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   placeholder="Rate per Kg *" class="ptl_final_rate_per_kg form-control form-control1 numberVal fourdigitstwodecimals_deciVal">
														</div>
														<div class="col-md-3 form-control-fld">
															@if(Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL)
                                                                                                                            @if(Session::get('service_id') == AIR_DOMESTIC)
                                                                                                                            {{--*/ $cls="clsAirDomKGperCCM" /*--}}
                                                                                                                            @elseif(Session::get('service_id') == AIR_INTERNATIONAL)
                                                                                                                            {{--*/ $cls="clsAirIntKGperCCM" /*--}}
                                                                                                                            @endif
                                                                                                                        <input  type="text"
																   name="final_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																   id="final_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   placeholder="{{$cft}} *" class="ptl_final_conversion form-control form-control1 numberVal fourdigitsfourdecimals_deciVal {{$cls}}" value="">
															@else
															<input  type="text"
																   name="final_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																   id="final_quote_kgperdft_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																   placeholder="{{$cft}} *" class="ptl_final_conversion form-control form-control1 numberVal fourdigitsthreedecimals_deciVal" value="">
															@endif	   
														
														</div>
														<input type="hidden" id="calculatoropen" style="border:none;" class="cla_class">
														</div>
													@else
														<div class="col-md-12 form-control-fld padding-left-none margin-top"><b> Seller Final Quote </b></div>
														
														<div class="col-md-3 padding-left-none form-control-fld margin-none"><span class="data-head">Rate per Kg </span> <span class="data-value"> Rs {{ $buyerquoteid->final_rate_per_kg }} /-</span></div>
														@if(Session::get('service_id') != COURIER)
														<div class="col-md-3 padding-left-none form-control-fld margin-none"><span class="data-head">{{$cft}} </span> <span class="data-value"> {{ $buyerquoteid->final_kg_per_cft }}</span></div>
														@else
														<div class="col-md-3 padding-left-none form-control-fld margin-none"><span class="data-head">{{$cft}} </span> <span class="data-value"> {{ $buyerquoteid->final_conversion_factor }}</span></div>
														@endif
														<div class="clearfix"></div>


													@endif
												@endif
											@endif

											@if(Session::get('service_id') != COURIER)
												@if(Session::get('service_id') != AIR_INTERNATIONAL && Session::get('service_id') != OCEAN)
												
													@if($buyerquoteid->initial_pick_up_rupees!='')
														@if($buyerquoteid->final_quote_price=='0.0000')
															@if($buyerquoteid->counter_quote_price!='0.0000')
															<div class="col-md-3 padding-left-none form-control-fld">
																@if($buyerquoteid->is_door_pickup==1)
																<input type="text"
																	   name="final_quote_pickup_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_quote_pickup_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   placeholder="Pickup Rs *" class="ptl_final_pickup form-control form-control1 fourdigitstwodecimals_deciVal numberVal ">
																@else
																<input type="text"
																	   name="final_quote_pickup"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_quote_pickup" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="No Pickup Rs *" readonly>
																<input type="hidden"
																	   name="final_quote_pickup_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_quote_pickup_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   placeholder="Pickup Rs *" class="ptl_final_pickup form-control form-control1 numberVal " value="0">
																@endif
															</div>
															@endif
														@else
														<div class="col-md-3 padding-left-none form-control-fld margin-none">
															<span class="data-head">Pickup </span> <span class="data-value">Rs {{ $common->getPriceType($buyerquoteid->final_pick_up_rupees) }} </span>
														</div>
														@endif
													@else
													<div class="col-md-3 padding-left-none form-control-fld">
														@if($buyerquoteid->is_door_pickup==1)
														<input type="text"
															   name="initial_quote_pickup_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_quote_pickup_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="Pickup Rs *" class="ptl_initial_pickup form-control form-control1 fourdigitstwodecimals_deciVal numberVal " >
														@else
														<input type="text"
															   name="initial_quote_pickup"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_quote_pickup" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="No Pickup Rs *" readonly>
														<input type="hidden" class="ptl_initial_pickup" id="initial_quote_pickup_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}" 
																name="initial_quote_pickup_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}" value="0">	   
														@endif
													</div>
													@endif
												
	
												
													@if($buyerquoteid->initial_delivery_rupees!='')
														@if($buyerquoteid->final_quote_price=='0.0000')
															@if($buyerquoteid->counter_quote_price!='0.0000')
															<div class="col-md-3 padding-left-none form-control-fld">
																@if($buyerquoteid->is_door_delivery ==1)
																<input type="text"
																	   name="final_quote_delivery_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_quote_delivery_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   placeholder="Delivery Rs *" class="ptl_final_delivery form-control form-control1 fourdigitstwodecimals_deciVal numberVal ">
																@else
																<input type="text"
																	   name="final_quote_delivery"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_quote_delivery" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="No Delivery Rs *" readonly>
																<input type="hidden"
																	   name="final_quote_delivery_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_quote_delivery_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   placeholder="Delivery Rs *" class="ptl_final_delivery form-control form-control1 numberVal " value="0">
																@endif
															</div>
															@endif
														@else
														<div class="col-md-3 padding-left-none form-control-fld  margin-none">
															<span class="data-head">Delivery </span> <span class="data-value">Rs {{ $common->getPriceType($buyerquoteid->final_delivery_rupees) }} </span>
														</div>
														@endif
													@else
													<div class="col-md-3 padding-left-none form-control-fld">
														@if($buyerquoteid->is_door_delivery ==1)
														<input type="text"
															   name="initial_quote_delivery_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_quote_delivery_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="Delivery Rs *" class="ptl_initial_delivery form-control form-control1 fourdigitstwodecimals_deciVal numberVal " >
														@else
														<input type="text"
															   name="initial_quote_delivery"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_quote_delivery" class=" form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="No Delivery Rs *" readonly >
														<input type="hidden" id="initial_quote_delivery_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}" 
																name="initial_quote_delivery_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}" class= "ptl_initial_delivery"value="0">
														@endif
													</div>
													@endif
												
	
												
													@if($buyerquoteid->initial_oda_rupees!='')
														@if($buyerquoteid->final_quote_price=='0.0000')
															@if($buyerquoteid->counter_quote_price!='0.0000')
															<div class="col-md-3 padding-left-none form-control-fld">
																@if($odacheck == 1)
																<input type="text"
																	   name="final_quote_oda_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_quote_oda_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   placeholder="ODA Rs *" class="ptl_final_oda form-control form-control1 fourdigitstwodecimals_deciVal numberVal ">
																@else
																<input type="text"
																	   name="final_quote_oda"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_quote_oda" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="No ODA Rs *" readonly>
																<input type="hidden"
																	   name="final_quote_oda_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_quote_oda_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   placeholder="ODA Rs *" class="ptl_final_oda form-control form-control1 numberVal " value="0">
																@endif
															</div>
															@endif
														@else
														<div class="col-md-3 padding-left-none form-control-fld margin-none">
															<span class="data-head">ODA </span> <span class="data-value">Rs {{ $buyerquoteid->final_oda_rupees }} /-</span>
														</div>
														@endif
													@else
													<div class="col-md-3 padding-left-none form-control-fld">
														@if($odacheck == 1)
														<input type="text"
															   name="initial_quote_oda_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_quote_oda_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="ODA Rs *" class="ptl_initial_oda form-control form-control1 fourdigitstwodecimals_deciVal numberVal " >
														@else
														<input type="text"
															   name="initial_quote_oda"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_quote_oda" class="form-control form-control1 fourdigitstwodecimals_deciVal numberVal " value="No ODA Rs *" readonly >
														<input type="hidden"
															   name="initial_quote_oda_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_quote_oda_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="ODA Rs *" class="ptl_initial_oda form-control form-control1 numberVal " value="0" >	   
														@endif
													</div>
													@endif
												
												@endif
												
												@if($buyerquoteid->initial_transit_days!='')
													@if($buyerquoteid->final_quote_price=='0.0000')
														@if($buyerquoteid->counter_quote_price!='0.0000')
														@if(Session::get('service_id') ==  ROAD_PTL)
															<div class="col-md-3 padding-left-none form-control-fld">
																<input type="text"
																	   name="final_quote_transit_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_quote_transit_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   placeholder="Transit Days *" class="form-control form-control1  numericvalidation" maxlength="3" >
															</div>
														@else
															<div class="col-md-3 padding-left-none form-control-fld">
																<input type="text"
																	   name="final_quote_transit_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_quote_transit_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   placeholder="Transit Days *" class="form-control form-control1  numericvalidation" maxlength="2" >
															</div>
														@endif
														@endif
													@else
													<div class="col-md-3 padding-left-none form-control-fld margin-none">
														<span class="data-head">Transit Days </span> <span class="data-value"> {{ $buyerquoteid->final_transit_days }}</span>
													</div>
													@endif
												@else
												@if(Session::get('service_id') ==  ROAD_PTL)
													<div class="col-md-3 padding-left-none form-control-fld">
														<input type="text"
															   name="initial_quote_transit_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_quote_transit_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="Transit Days *" class="form-control form-control1 numericvalidation" maxlength="3">
													</div>
												@else
													<div class="col-md-3 padding-left-none form-control-fld">
														<input type="text"
															   name="initial_quote_transit_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_quote_transit_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="Transit Days *" class="form-control form-control1 numericvalidation" maxlength="2">
													</div>
												@endif
												@endif
											
											@else
											
											
											
												@if($buyerquoteid->initial_quote_price!='0.00')
													@if($buyerquoteid->counter_quote_price!='0.00')
														@if($buyerquoteid->final_quote_price=='0.00')
															<div class="col-md-12 padding-none">
																<div class="col-md-3 form-control-fld padding-left-none">
																	<input type="text"
																	   name="final_fuel_surcharge_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_fuel_surcharge_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   placeholder="Fuel Surcharge (%) *" class="ptl_final_fuel form-control form-control1 numberVal twodigitstwodecimals_deciVal" >
																</div>	
																<div class="col-md-9 form-control-fld">
																	<div class="col-md-2 padding-left-none form-control-fld">
																		
																		<input type="text"
																	   name="final_cod_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_cod_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   placeholder="COD (%) *" class="ptl_final_cod form-control form-control1 numberVal twodigitstwodecimals_deciVal" >
																	</div>	
																	<div class="col-md-3 form-control-fld">
																		<input type="text"
																	   name="final_freight_collect_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_freight_collect_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   placeholder="Freight Collect *" class="ptl_ifinal_freight form-control form-control1 numberVal fivedigitstwodecimals_deciVal " >
																	</div>
																	<div class="col-md-2 form-control-fld">
																		<input type="text"
																	   name="final_arc_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																	   id="final_arc_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																	   placeholder="ARC (%) *" class="ptl_final_arc form-control form-control1 numberVal twodigitstwodecimals_deciVal " >
																	</div>
																	<div class="col-md-3 form-control-fld padding-none">
																		<div class="input-prepend">
																			<input type="text"
																		   name="final_transit_days_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																		   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
																		   id="final_transit_days_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
																		   placeholder="Transit Days *" maxlength= "3" class="form-control form-control1 maxlimitthree_lmtVal numericvalidation" >
																		</div>
																	</div>
					
																	<div class="col-md-2 padding-none">
																		<div class="normal-select">
																			<select class="selectpicker"  id="dayspicker_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}" name="dayspicker_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}">
																				<option value="1">Days</option>
																				<option value="2">Weeks</option>
																			</select>
																		</div>							
																	</div>
																</div>	
															</div>
															@else
																<div class="col-md-3 padding-left-none form-control-fld">
																	<span class="data-head">Fuel Surcharge </span> <span class="data-value">{{ $buyerquoteid->final_fuel_surcharge_rupees }} %</span>
																</div>
																
																<div class="col-md-9 padding-left-none form-control-fld">
																	<div class="col-md-2 form-control-fld padding-left-none">
																		<span class="data-head">COD </span> <span class="data-value"> Rs {{ $buyerquoteid->final_cod_rupees }} %</span>
																	</div>
																	<div class="col-md-3 form-control-fld">
																		<span class="data-head">Freight Collect </span> <span class="data-value">{{ $buyerquoteid->final_freight_collect_rupees }} /-</span>
																	</div>
																	<div class="col-md-3 form-control-fld">
																		<span class="data-head">ARC </span> <span class="data-value">{{ $buyerquoteid->final_arc_rupees }} %</span>
																	</div>
																	<div class="col-md-3 form-control-fld">
																		<span class="data-head">Transit Days </span> <span class="data-value">{{ $buyerquoteid->final_transit_days }} {{ $buyerquoteid->final_transit_units }}</span>
																	</div>
																	
																</div>
															@endif
													@endif
												@else
												
												
												
												<div class="col-md-12 padding-none">
													<div class="col-md-3 form-control-fld padding-left-none">
														<input type="text"
														   name="initial_fuel_surcharge_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
														   id="initial_fuel_surcharge_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   placeholder="Fuel Surcharge (%) *" class="ptl_initial_fuel form-control form-control1 numberVal twodigitstwodecimals_deciVal" >
													</div>	
													<div class="col-md-9 form-control-fld">
														<div class="col-md-2 padding-left-none form-control-fld">
															
															<input type="text"
														   name="initial_cod_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
														   id="initial_cod_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   placeholder="COD (%) *" class="ptl_initial_cod form-control form-control1 numberVal twodigitstwodecimals_deciVal" >
														</div>	
														<div class="col-md-3 form-control-fld">
															<input type="text"
														   name="initial_freight_collect_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
														   id="initial_freight_collect_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   placeholder="Freight Collect *" class="ptl_initial_freight form-control form-control1 numberVal fivedigitstwodecimals_deciVal " >
														</div>
														<div class="col-md-2 form-control-fld">
															<input type="text"
														   name="initial_arc_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
														   id="initial_arc_rupees_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
														   placeholder="ARC (%) *" class="ptl_initial_arc form-control form-control1 numberVal  twodigitstwodecimals_deciVal " >
														</div>
														<div class="col-md-3 form-control-fld padding-none">
															<div class="input-prepend">
																<span class="add-on"><i class="fa fa-hourglass-1"></i></span>
																<input type="text"
															   name="initial_transit_days_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   data-buyerid="{{ $buyerquoteid->buyer_id }}" dat-buyerqouteit="{{ $buyerquoteid->ptlquoteid }}"
															   id="initial_transit_days_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}"
															   placeholder="Transit Days *" maxlength = '3' class="form-control form-control1 numericvalidation" >
															</div>
														</div>
		
														<div class="col-md-2 padding-none">
															<div class="normal-select">
																<select class="selectpicker"  id="dayspicker_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}" name="dayspicker_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}">
																	<option value="1">Days</option>
																	<option value="2">Weeks</option>
																</select>
															</div>							
														</div>
													</div>	
												</div>
												
												
												
												@endif
											
												
											@endif
											@if($buyerquoteid->initial_freight_amount=='' && $buyerquoteid->counter_freight_amount=='')
											<div class="clearfix"></div>
											<div class="clearfix margin-none"></div>
											<div class="col-md-3 padding-none margin-top-none">
												<span  class="data-head padding-top1">Freight Amount (Rs) : </span>
												<span class="data-value" id="freight_charges_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}">
													0.00 /-
												</span>
												
											</div>	
											@elseif($buyerquoteid->final_freight_amount =='' && $buyerquoteid->counter_freight_amount !='')
											<div class="clearfix"></div>
											<div class="clearfix margin-none"></div>
											<div class="col-md-3 padding-none margin-top-none">
												<span  class="data-head padding-top1">Freight Amount (Rs) : </span>
												<span class="data-value" id="freight_charges_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}">
												0.00 /-
												</span>
												
											</div>	
											@elseif($buyerquoteid->final_freight_amount !='')
											<div class="clearfix"></div>
											<div class="clearfix margin-none"></div>
											<div class="col-md-3 padding-none margin-top-none">
												<span  class="data-head padding-top1">Freight Amount (Rs) : </span>
												<span class="data-value" id="freight_charges_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}">
													{{ $common->moneyFormat($buyerquoteid->final_freight_amount,true) }} /-
												</span>
												
											</div>	
											@endif
											
											@if($buyerquoteid->initial_quote_price=='0.0000' && $buyerquoteid->counter_quote_price=='0.0000')
											<div class="col-md-3 form-control-fld padding-left-none margin-none">
											<span class="data-head" >Total Amount (Rs) :</span> <span class="data-value" id="total_charges_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}">
												0.00 /-</span>
											</div>
											@elseif($buyerquoteid->final_quote_price=='0.0000' && $buyerquoteid->counter_quote_price!='0.0000')
											<div class="col-md-3 form-control-fld padding-left-none margin-none">
											<span class="data-head" >Total Amount (Rs) :</span> <span class="data-value" id="total_charges_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}">
												0.00 /-</span>
											</div>
											@elseif($buyerquoteid->final_quote_price!='0.0000')
											<div class="col-md-3 form-control-fld padding-left-none margin-none">
											<span class="data-head" >Total Amount (Rs) :</span> <span class="data-value" id="total_charges_{{ $buyerquoteid->buyer_id }}_{{ $buyerquoteid->ptlquoteid }}">
												{{ $common->moneyFormat($buyerquoteid->final_quote_price) }} /-</span>
											</div>
											@endif
											<div class="hide-submit">
												@if($buyerquoteid->initial_freight_amount=='')
													{!! Form::button('Submit', array('id'=>'ptl_initial_quote_submit_'.$buyerquoteid->buyer_id.'_'.$buyerquoteid->ptlquoteid,'class'=>'btn add-btn margin-top pull-right  ptl_initial_quote_submit margin-bottom', 'name'=>$buyerquoteid->ptlquoteid)) !!}
												@elseif($buyerquoteid->counter_freight_amount!='' && $buyerquoteid->final_freight_amount=='')
													{!! Form::button('Submit', array('id'=>'ptl_final_quote_submit_'.$buyerquoteid->buyer_id.'_'.$buyerquoteid->ptlquoteid,'class'=>'btn add-btn margin-top pull-right  ptl_final_quote_submit margin-bottom', 'name'=>$buyerquoteid->ptlquoteid)) !!}
												@endif
											</div>
											</div>
											
											<div class="show-submit">
												{!! Form::button('Accept', array('id'=>'ptl_counter_quote_submit_'.$buyerquoteid->buyer_id.'_'.$buyerquoteid->ptlquoteid,'class'=>'btn add-btn margin-top pull-right  ptl_counter_quote_submit margin-bottom', 'name'=>$buyerquoteid->ptlquoteid)) !!}
											</div>
										
											
										</div>
										
								</div>

								{!! Form::close() !!}	
							</div>
							@endforeach
						@endif
						{{--*/ $i++  /*--}}
                                                        </div>
						</div> <!-- Table row -->
						@endif

					</div> <!-- table-div margin-none -->






				</div>
				<!-- EOD Right Section Starts Here -->
			</div>

	</div> <!-- Container -->
</div> <!-- Main  -->
			@include('partials.footer')	


@endsection
