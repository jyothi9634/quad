@extends('app') @section('content')
@inject('termbuyer', 'App\Components\Term\TermBuyerComponent')
@inject('commoncomponent', 'App\Components\CommonComponent')
@include('partials.page_top_navigation')
 <div class="main">

			<div class="container">
				
				<div class="clearfix"></div>

				<span class="pull-left"><h1 class="page-title">Contract - {!! $contractDetails[0]->contract_no !!}</h1></span>
				<span class="pull-right">
					<a href="/sellerorderSearch" class="back-link1">Back to Contracts</a>
				</span>
				 
				<?php
				//check the conditions for multi or not items
				if(Session::get('service_id')!=RELOCATION_INTERNATIONAL){
				$loadtype = $termbuyer->checkMulti($serviceId,$contractDetails[0]->term_buyer_quote_id,"lkp_load_type_id");
				}									
				$from = $termbuyer->checkMulti($serviceId,$contractDetails[0]->term_buyer_quote_id,"from_location_id");
				$to = 	$termbuyer->checkMulti($serviceId,$contractDetails[0]->term_buyer_quote_id,"to_location_id");
				
				if(Session::get('service_id')!=RELOCATION_INTERNATIONAL){
				if($loadtype == "multi"){
					$displayLoadType = "Many";
				} else {
					$displayLoadType = $contractDetails[0]->load_type;
				}	
				}
												
				if($from == "multi"){
					$displayFromLocationType = "Many";
				}else {
					$displayFromLocationType = $contractDetails[0]->from;
				}
				if($to == "multi"){
					$displayToLocationType = "Many";
				}else {
					$displayToLocationType = $contractDetails[0]->to;
				}
				?>
				<!-- Search Block Starts Here -->
				<div class="col-md-12 padding-none">
				<div class="search-block inner-block-bg">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">{{$displayFromLocationType}} to {{$displayToLocationType}}</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">Valid From</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								@if(isset($contractDetails[0]->from_date) && $contractDetails[0]->from_date != '0000-00-00')
                                 {{date("d/m/Y", strtotime($contractDetails[0]->from_date))}}  
                                @else &nbsp;
                                @endif
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">Valid To</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								@if(isset($contractDetails[0]->to_date) && $contractDetails[0]->to_date != '0000-00-00')
                                {{date("d/m/Y", strtotime($contractDetails[0]->to_date))}}
                                @else &nbsp;
                                @endif
							</span>
						</div>
					</div>
					<div></div>
					<div class="text-right filter-details">
						<div class="info-links">
							<a class="transaction-details">
								<span class="show-icon">+</span>
								<span class="hide-icon">-</span> Details
							</a>
						</div>
					</div>
				</div>
				<!--Search Block  details div starts here-->
	            		<!--toggle div starts-->
										<div class="col-md-12 show-trans-details-div padding-none white-bg margin-top-1 padding-10"> 
											 
	                                         <!-- Table Starts Here -->

								<div class="table-div table-style1  padding-none">
									
									<!-- Table Head Starts Here -->
									@if(Session::get('service_id')==ROAD_FTL)
									<div class="table-heading inner-block-bg">
										<div class="col-md-2 padding-left-none">Vehicle Type</div>
                                        <div class="col-md-3 padding-left-none">Load Type</div>
                                        <div class="col-md-2 padding-left-none">From Location</div>
                                        <div class="col-md-2 padding-left-none">To Location</div>
                                        <div class="col-md-2 padding-left-none">Quantity</div>
                                        <div class="col-md-1 padding-none">Freight</div>
									</div>
									@elseif(Session::get('service_id')==COURIER)
									<div class="table-heading inner-block-bg">
                                        <div class="col-md-3 padding-left-none">From Location</div>
                                        <div class="col-md-3 padding-left-none">To Location</div>
                                        <div class="col-md-3 padding-left-none">Volume</div>
                                        <div class="col-md-3 padding-left-none">Number of Packages</div>
									</div>
									@elseif(Session::get('service_id')==RELOCATION_INTERNATIONAL)
									<div class="table-heading inner-block-bg">
                                        <div class="col-md-3 padding-left-none">From Location</div>
                                        <div class="col-md-3 padding-left-none">To Location</div>
                                        <div class="col-md-3 padding-left-none">Number of Moves</div>
                                        @if($contractDetails[0]->lkp_lead_type_id==1)
                                        <div class="col-md-3 padding-left-none">Avg/KG per Move</div>
                                        @else
                                        <div class="col-md-3 padding-left-none">Avg/CBM per Move</div>
                                        @endif
									</div>
									@elseif(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
									<div class="table-heading inner-block-bg">
                                        <div class="col-md-4 padding-left-none">From Location</div>
                                        <div class="col-md-4 padding-left-none">Serivce</div>
                                        <div class="col-md-4 padding-left-none">Numbers</div>
                                        
									</div>
									@else
										<div class="table-heading inner-block-bg">
                                        <div class="col-md-2 padding-left-none">From Location</div>
                                        <div class="col-md-2 padding-left-none">To Location</div>
                                        <div class="col-md-2 padding-left-none">Load Type</div>
                                        <div class="col-md-1 padding-left-none">Number of Packages</div>
                                        <div class="col-md-1 padding-left-none">Volume</div>
                                        <div class="col-md-1 padding-left-none">Contract Quantity</div>
                						<div class="col-md-1 padding-left-none">Rate per KG</div>
                						<div class="col-md-2 padding-left-none">KG per CFT</div>
									</div>
									@endif
									<!-- Table Head Ends Here -->

									<div class="table-data">
										
										<!-- Table Row Starts Here -->
										
										@if(Session::get('service_id')==ROAD_FTL)
	                                    @foreach($contractDetails as $contractDetail)
	                                    <div class="table-row inner-block-bg">
										
	                                        <div class="col-md-2 padding-left-none">{{$contractDetail->vehicle_type}}</div>
	                                        <div class="col-md-3 padding-left-none">{{$contractDetail->load_type}}</div>
	                                        <div class="col-md-2 padding-left-none">{{$contractDetail->from}}</div>
	                                        <div class="col-md-2 padding-left-none">{{$contractDetail->to}}</div>
	                                        <div class="col-md-2 padding-left-none">{{$contractDetail->quantity}}</div>
	                                        <div class="col-md-1 padding-none">{{$contractDetail->initial_quote_price}}</div>
	                                    
	                                    </div>
	                                    @endforeach
	                                    @elseif(Session::get('service_id')==RELOCATION_INTERNATIONAL)
	                                    @foreach($contractDetails as $contractDetail)
										<div class="table-heading inner-block-bg">
	                                        <div class="col-md-3 padding-left-none">{{$contractDetail->from}}</div>
	                                        <div class="col-md-3 padding-left-none">{{$contractDetail->to}}</div>
	                                        <div class="col-md-3 padding-left-none">{{$contractDetail->number_loads}}</div>
	                                        <div class="col-md-3 padding-left-none">{{$contractDetail->avg_kg_per_move}}</div>
										</div>
										@endforeach
										@elseif(Session::get('service_id')==RELOCATION_GLOBAL_MOBILITY)
	                                    @foreach($contractDetails as $contractDetail)
										<div class="table-heading inner-block-bg">
	                                       <div class="col-md-4 padding-left-none">{{$contractDetail->from}}</div>
                                           <div class="col-md-4 padding-left-none">{{$commoncomponent->getAllGMServiceTypesById($contractDetail->lkp_gm_service_id)}}</div>
                                           <div class="col-md-4 padding-left-none">{{$contractDetail->measurement}} {{$contractDetail->measurement_units}}</div>
										</div>
										@endforeach
	                                    @elseif(Session::get('service_id')==COURIER)
	                                    @foreach($contractDetails as $contractDetail)
	                                    <div class="table-row inner-block-bg">
	                                    <div class="col-md-3 col-sm-4 col-xs-4 padding-left-none">{{$contractDetail->from}}</div>
                                        <div class="col-md-3 col-sm-4 col-xs-4 padding-left-none">{{$contractDetail->to}}</div>
                                        <div class="col-md-3 col-sm-4 col-xs-4 padding-left-none">{{$contractDetail->volume}}</div>
                                        <div class="col-md-3 col-sm-4 col-xs-4 padding-left-none">{{$contractDetail->number_packages}}</div>
	                                    </div>
	                                    @endforeach
	                                    
	                                    {{--*/ $getslabvalues = $commoncomponent->getQuotePriceDetailsSlabs($contractDetails[0]->term_buyer_quote_id,$contractDetails[0]->created_by) /*--}}
	                                    {{--*/ $maxweight = $commoncomponent->getMaxWeightUnits($contractDetails[0]->term_buyer_quote_id,$contractDetails[0]->created_by) /*--}}
	                                    {{--*/ $slabsprice = $commoncomponent->getQuotePriceDetails($contractDetails[0]->term_buyer_quote_id,$contractDetails[0]->created_by) /*--}}
	                                   
	                                    <div class="col-md-12 inner-block-bg inner-block-bg1 ">
											<div class="col-md-3 form-control-fld">
												<span class="data-value">Maximum Weight: {{$maxweight[0]->max_weight_accepted}} {{$commoncomponent->getWeight($maxweight[0]->lkp_ict_weight_uom_id)}}</span>
											</div>
											<div class="col-md-12 padding-none">
												<div class="col-md-12 padding-none">
				                                    <div class="table-div table-style1">
												
														<!-- Table Head Starts Here -->
			
														<div class="table-heading inner-block-bg">
															<div class="col-md-3 padding-left-none">Min</div>
															<div class="col-md-3 padding-left-none">Max</div>
															<div class="col-md-3 padding-left-none">Quote</div>
														
														</div>
			
														<!-- Table Head Ends Here -->
			
														<div class="table-data form-control-fld padding-none">
													
			
															<!-- Table Row Starts Here -->
															@foreach($getslabvalues as $slabs)
															<div class="table-row inner-block-bg">
																<div class="col-md-3 padding-left-none">{{$slabs->slab_min_rate}}</div>
																<div class="col-md-3 padding-left-none">{{$slabs->slab_max_rate}}</div>
																<div class="col-md-3 padding-left-none">{{$slabs->slab_rate}}</div>
														
																<div class="col-md-1 form-control-fld padding-left-none text-right"></div>
															</div>
															@endforeach
															<!-- Table Row Ends Here -->
														</div>
														@if($maxweight[0]->increment_weight>0)
														<div class="col-md-5 form-control-fld padding-none margin-top">
															<div class="col-md-3 padding-left-none">{{$maxweight[0]->increment_weight}} {{$commoncomponent->getWeight($maxweight[0]->lkp_ict_weight_uom_id)}}</div>
															<div class="col-md-3 padding-left-none">{{$slabsprice[0]->incremental_weight_price}} Rs/-</div>
												 		</div>	
												 		@endif
												 		<div class="col-md-12 form-control-fld padding-none margin-top ">
															<div class="col-md-2 padding-left-none">
																<span class="data-value">Conversion Factor : {{$slabsprice[0]->conversion_factor}}</span>
															</div>
															
															<div class="col-md-3 padding-left-none">
																<span class="data-value">Transit Days: {{$slabsprice[0]->transit_days}} Days</span>
															</div>
														</div>
														<div class="col-md-12 padding-none">
															<h5 class="data-head margin-left-none">Additional Charges</h5>
															<div class="col-md-2 padding-left-none">
																<span class="data-value">Fuel Surcharge: {{$slabsprice[0]->fuel_charges}} /-</span>
															</div>
															<div class="col-md-2 padding-left-none">
																<span class="data-value">Check on Delivry: {{$slabsprice[0]->cod_charges}} /-</span>
															</div>
															<div class="col-md-2 padding-left-none">
																<span class="data-value">Freight Collect: {{$slabsprice[0]->freight_charges}} /-</span>
															</div>
															<div class="col-md-2 padding-left-none">
																<span class="data-value">ARC: {{$slabsprice[0]->arc_charges}} /-</span>
															</div>
															<div class="col-md-2 padding-left-none">
																<span class="data-value">Maximum Value: {{$slabsprice[0]->max_value}} /-</span>
															</div>
														</div>
												 	</div>
												 </div>
											</div>
										</div>
	                                    @else
	                                    @foreach($contractDetails as $contractDetail)
	                                    <div class="table-row inner-block-bg">
										
	                                    <div class="col-md-2 col-sm-4 col-xs-4 padding-left-none">{{$contractDetail->from}}</div>
                                        <div class="col-md-2 col-sm-4 col-xs-4 padding-left-none">{{$contractDetail->to}}</div>
                                        <div class="col-md-2 col-sm-4 col-xs-4 padding-left-none">{{$contractDetail->load_type}}</div>
                                        <div class="col-md-1 col-sm-4 col-xs-4 padding-left-none">{{$contractDetail->number_packages}}</div>
                                        <div class="col-md-1 col-sm-4 col-xs-4 padding-left-none">{{$contractDetail->volume}}</div>
                                        <div class="col-md-1 col-sm-4 col-xs-4 padding-left-none">{{$contractDetail->contract_quantity}}</div>
                                        <div class="col-md-1 col-sm-4 col-xs-4 padding-left-none">{{$contractDetail->contract_rate_per_kg}}</div>
                                        <div class="col-md-2 col-sm-4 col-xs-4 padding-left-none">{{$contractDetail->contract_kg_per_cft}}</div>	
	                                   
	                                    </div>
	                                    @endforeach
	                                    @endif
	                                
									<!-- Table Row Ends Here -->
	 

									</div>
								 
					

								<!-- Table Ends Here -->
	                                    
	                                        </div>
								<!--toggle div ends-->
	                      
	                            
	                            	
	            
				<!--Search Block  details div ends here-->

				<!-- Search Block Ends Here -->

				      </div>
				
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 
						
						
						<!-- Right Section Starts Here -->

						<div class="main-right">

							<div class="pull-left">
								<div class="info-links">
									<a href="#"><i class="fa fa-envelope-o"></i> Messages</a>
									<a href="#"><i class="fa fa-file-text-o"></i> Documentation</a>
								</div>
							</div>

							<div class="inner-block-bg msg-area">
							@if($contractDetails[0]->contract_status==PENDING_ACCEPTANCE)   
                            {!! Form::open(array('url' => "setcontractstatus/".$contractDetails[0]->term_buyer_quote_id, 'id' => 'accept_contract', 'name' => 'accept_contract')) !!}
                            {!! Form::submit('Accept Term Contract', ['class' => 'btn btn-black pull-right','name' => 'submit','id' => 'submit']) !!}	
                            {!! Form::close() !!}
                            @endif
                            <a href="/getcontractdownload/{{$contractDetails[0]->term_buyer_quote_id}}" >Download Contract</a>
                            
                                <div class="clearfix"></div>
                            
							</div>

						</div>

						<!-- Right Section Ends Here -->

					</div>
				</div>

				<div class="clearfix"></div>

			</div>
		</div>
@endsection
