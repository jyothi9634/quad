<!------------Added class (col-md-12) srinu 28-04-2016 for design-------------->
<div class="col-md-12 table-div table-style1">									
													<!-- Table Head Starts Here --> 													
													<div class="table-heading inner-block-bg">														
														<div class="col-md-3 padding-left-none">Volume</div>
														<div class="col-md-3 padding-left-none">Unit Weight</div>
														<div class="col-md-3 padding-left-none">No of Packages</div>
														<div class="col-md-3 padding-left-none">Package Price</div>														
													</div>														

													<!-- Table Head Ends Here -->
													<div class="table-data chargeable_checkamnt_class" leads_id="{{$sellerData->id}}">
														<!-- Table Row Starts Here - srinu code starts here 
														calculating seller leads total price same as ltl buyer seach for sellers -->
														
													@if(isset($arraySellerDetails) && !empty($arraySellerDetails))
                                  						@foreach($arraySellerDetails as $key=>$sellersQuotesDetails)   
			                                            
														<div class="table-row inner-block-bg">															
															@if($sellersQuotesDetails->calculated_volume_weight !=0)
																<div class="col-md-3 padding-left-none">{!! round($sellersQuotesDetails->calculated_volume_weight,4) !!}{!! $str !!}</div>
															@else
																<div class="col-md-3 padding-left-none">NA</div>
															@endif
															<div class="col-md-3 padding-left-none">{!! $sellersQuotesDetails->buyerQuoteUnits !!}  {{ $sellersQuotesDetails->weight_type }}</div>
															<div class="col-md-3 padding-left-none">{!! $sellersQuotesDetails->number_packages !!}</div>
															<div class="col-md-3 padding-left-none">{!! $sellersQuotesDetails->package_value !!} </div>
														</div>
														<!--  Courier Slab calculation. -->
														{{--*/   $seller_post_slab_values = $commonComponent->getCourierSlabValues($sellerData->seller_post_id)  /*--}}
															
															{{--*/ $conversion_factor = $sellerData->kg_per_cft /*--}}
															{{--*/ $ptlUnitsWeight    = $sellersQuotesDetails->buyerQuoteUnits /*--}}
															{{--*/ $tot = 0 /*--}}
															{{--*/ $total_slab_amount = 0; /*--}}
															{{--*/ $noOfPackages = $sellersQuotesDetails->number_packages; /*--}}
															{{--*/ $max_weight_accepted = $sellerData->max_weight_accepted; /*--}}
															{{--*/ $fuelsurcharge = $sellerData->fuel_surcharge; /*--}}
															{{--*/ $codcharge = $sellerData->cod_charge; /*--}}
															{{--*/ $arccharge = $sellerData->arc_charge; /*--}}
															{{--*/ $packageValue=$sellersQuotesDetails->package_value; /*--}}
															{{--*/ $incremental_weight = $sellerData->increment_weight;  /*--}}
															{{--*/ $freightcollectcharge=$sellerData->freight_collect_charge;  /*--}}													
															{{--*/ $paymentmodeid=$sellerData->paymentmodeid; /*--}}	
															{{--*/ $is_incremental=$sellerData->is_incremental; /*--}}
															{{--*/ $rate_per_increment=$sellerData->rate_per_increment; /*--}}	
																				
															<?php
															$weightunit = $sellersQuotesDetails->weight_type; 
															if($weightunit=='Mts')
															{
																$ptlUnitsWeight = $ptlUnitsWeight*1000;
															}elseif($weightunit=='Gms'){
																$ptlUnitsWeight = $ptlUnitsWeight*0.001;
															}else{
																$ptlUnitsWeight = $ptlUnitsWeight;
															}
															?>

															@if($PostCourierType=='Parcel')
															{{--*/ $chargableWeight  = ($sellersQuotesDetails->calculated_volume_weight)/$conversion_factor /*--}}															
																@if($chargableWeight > $ptlUnitsWeight)
																{{--*/ $displayChargableweighttotal = $chargableWeight /*--}}
																@else
																{{--*/ $displayChargableweighttotal = $ptlUnitsWeight /*--}}
																@endif
															@else
															{{--*/ $displayChargableweighttotal =  $ptlUnitsWeight /*--}}
															@endif
														
															<?php 															
															for($m=0;$m<count($seller_post_slab_values);$m++){
																$minVal = $seller_post_slab_values[$m]->slab_min_rate;
																$maxVal = $seller_post_slab_values[$m]->slab_max_rate;
																$total_slab_amount = $total_slab_amount + $seller_post_slab_values[$m]->price;
																if($displayChargableweighttotal >= $minVal && $displayChargableweighttotal <= $maxVal){
																	break;
																}
															
															}
															
															if($displayChargableweighttotal > $max_weight_accepted){
																$balance_weight = $max_weight_accepted - $displayChargableweighttotal;
																if($is_incremental == 1){
																	$weight_inc = $balance_weight/$incremental_weight;
																	$additonal_rate = $weight_inc * $rate_per_increment;
																	$total_slab_amount = $total_slab_amount + $additonal_rate;
																}
															
															}
															$totalChargableAmount = ($total_slab_amount*$noOfPackages);
															$fuelsurchargeCalVal = ($fuelsurcharge * $totalChargableAmount)/100;
															$codchargeVal = ($codcharge * $noOfPackages * $packageValue ) /100;
															$arcchargeVal = ($arccharge * $noOfPackages * $packageValue ) /100;
															
															$tot   +=$totalChargableAmount + $fuelsurchargeCalVal + $codchargeVal + $arcchargeVal;
															if($paymentmodeid == CASH_ON_DELIVERY){
																$tot    += $freightcollectcharge;
															}																													
															?>
														<span class="courier_tot_price_calc_{!! $buyerQuoteForLeadId !!}" style="display:none">{{ $tot }}</span>
														<span class="courier_tot_chargeable_calc_{!! $buyerQuoteForLeadId !!}" style="display:none">{{ $totalChargableAmount }}</span>
														
														 @endforeach 
			                                         
                                					@endif
														<!-- Table Row Ends Here -->
													</div>
												</div>