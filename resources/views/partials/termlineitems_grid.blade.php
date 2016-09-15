 @if($serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN)
 
                    <div class="col-sm-12">        
                        <div class="gray-bg">
                            <div class="table-div table-style1 margin-top form-control-grid">
                                <div class="table-heading inner-block-bg">
                                    <div class="col-md-2 padding-left-none">L(Unit)</div>
                                    <div class="col-md-2 padding-left-none">B(Unit)</div>
                                    <div class="col-md-2 padding-left-none">H(Unit)</div>
                                    <div class="col-md-2 padding-left-none">Volume</div>
                                    <div class="col-md-2 padding-left-none">Unit Weight</div>
                                    <div class="col-md-2 padding-left-none">No of packages</div>
                                    
                                </div>
                                @if(isset($order_id))
                                <div class="table-data">
                                    @if(isset($termindentData) && $termindentData)	
                                    	<div class="table-row inner-block-bg">
						<div class="col-md-2 padding-left-none">{{$termindentData->length}} {{$commonComponent->getLengthWeight($termindentData->lkp_ptl_length_uom_id)}}</div>
                                                <div class="col-md-2 padding-left-none">{{$termindentData->breadth}} {{$commonComponent->getLengthWeight($termindentData->lkp_ptl_length_uom_id)}}</div>
                                                <div class="col-md-2 padding-left-none">{{$termindentData->height}} {{$commonComponent->getLengthWeight($termindentData->lkp_ptl_length_uom_id)}}</div>
                                                <div class="col-md-2 padding-left-none">{{$termindentData->volume}}</div>
						<div class="col-md-2 padding-left-none">{{$termindentData->unitweight}} {{$commonComponent->getWeight($termindentData->lkp_ict_weight_type_id)}}</div>
                                                <div class="col-md-2 padding-left-none">{{$termindentData->noofpackages}}</div>
                                                
                                            </div>
                                          
                                    @endif
                                </div>
                                @else
                                <div class="table-data">
                                    @if(isset($indentData) && $indentData)
										{{--*/ $con_id = $indentData['contract_id'] /*--}}
                                    	<div class="table-row inner-block-bg">
												<div class="col-md-2 padding-left-none">{{$indentData['term_length_'.$con_id]}} {{$commonComponent->getLengthWeight($indentData['term_weighttype_'.$con_id])}}</div>
                                                <div class="col-md-2 padding-left-none">{{$indentData['term_width_'.$con_id]}} {{$commonComponent->getLengthWeight($indentData['term_weighttype_'.$con_id])}}</div>
                                                <div class="col-md-2 padding-left-none">{{$indentData['term_height_'.$con_id]}} {{$commonComponent->getLengthWeight($indentData['term_weighttype_'.$con_id])}}</div>
                                                <div class="col-md-2 padding-left-none">{{$indentData['volume_hidden_ltl_'.$con_id]}}</div>
												<div class="col-md-2 padding-left-none">{{$indentData['ptlUnitsWeight_'.$con_id]}} {{$commonComponent->getWeight($indentData['ptlCheckUnitWeight_'.$con_id])}}</div>
                                                <div class="col-md-2 padding-left-none">{{$indentData['term_noofpackages_'.$con_id]}}</div>
                                                
                                            </div>
                                          
                                    @endif
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    
             @elseif($serviceId==COURIER)  
           
               <div class="col-sm-12">        
                        <div class="gray-bg">
                            <div class="table-div table-style1 margin-top form-control-grid">
                                @if(isset($order_id))
                                    <div class="table-heading inner-block-bg">
                                    @if(!isset($termindentData->length) || $termindentData->length=='')
                                        <div class="col-md-4 padding-left-none">Unit Weight</div>
                                        <div class="col-md-4 padding-left-none">No of packages</div>
                                        <div class="col-md-4 padding-left-none">Package Value</div>
                                    @else
                                        <div class="col-md-2 padding-left-none">L(Unit)</div>
                                        <div class="col-md-2 padding-left-none">B(Unit)</div>
                                        <div class="col-md-2 padding-left-none">H(Unit)</div>
                                        <div class="col-md-2 padding-left-none">Volume</div>
                                        <div class="col-md-2 padding-left-none">Unit Weight</div>
                                        <div class="col-md-2 padding-left-none">No of packages</div>
                                    @endif    
                                    </div>
                                @else
                                    <div class="table-heading inner-block-bg">
                                    @if(isset($indentData['courier_type']) && $indentData['courier_type']==1)
                                        <div class="col-md-4 padding-left-none">Unit Weight</div>
                                        <div class="col-md-4 padding-left-none">No of packages</div>
                                        <div class="col-md-4 padding-left-none">Package Value</div>
                                    @else
                                        <div class="col-md-2 padding-left-none">L(Unit)</div>
                                        <div class="col-md-2 padding-left-none">B(Unit)</div>
                                        <div class="col-md-2 padding-left-none">H(Unit)</div>
                                        <div class="col-md-2 padding-left-none">Unit Weight</div>
                                        <div class="col-md-2 padding-left-none">No of packages</div>
                                        <div class="col-md-2 padding-left-none">Package Value</div>
                                    @endif    
                                    </div>
                                @endif
                                <div class="table-data">
                                    @if(isset($indentData) && $indentData)
                                        {{--*/ $con_id = $indentData['contract_id'] /*--}}
                                            <div class="table-row inner-block-bg">
                                            @if(isset($indentData['courier_type']) && $indentData['courier_type']==1)
                           
                                                <div class="col-md-4 padding-left-none">@if(isset($indentData['ptlUnitsWeight_'.$con_id])){{$indentData['ptlUnitsWeight_'.$con_id]}} {{$commonComponent->getWeight($indentData['courier_CheckWeightUnit_'.$con_id])}} @endif</div>
                                                <div class="col-md-4 padding-left-none">@if(isset($indentData['term_noofpackages_'.$con_id])){{$indentData['term_noofpackages_'.$con_id]}} @else - @endif</div>
                                                <div class="col-md-4 padding-left-none">@if(isset($indentData['package_value'.$con_id])){{$indentData['package_value'.$con_id]}} @else - @endif</div>
                                            @else
                                                <div class="col-md-2 padding-left-none">@if(isset($indentData['courier_term_length_'.$con_id])){{$indentData['courier_term_length_'.$con_id]}} {{$commonComponent->getLengthWeight($indentData['term_weighttype_'.$con_id])}}@endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($indentData['courier_term_width_'.$con_id])){{$indentData['courier_term_width_'.$con_id]}} {{$commonComponent->getLengthWeight($indentData['term_weighttype_'.$con_id])}}@endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($indentData['courier_term_height_'.$con_id])){{$indentData['courier_term_height_'.$con_id]}} {{$commonComponent->getLengthWeight($indentData['term_weighttype_'.$con_id])}}@endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($indentData['ptlUnitsWeight_'.$con_id])){{$indentData['ptlUnitsWeight_'.$con_id]}} @endif @if(isset($indentData['courier_CheckWeightUnit_'.$con_id])){{$commonComponent->getWeight($indentData['courier_CheckWeightUnit_'.$con_id])}} @endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($indentData['term_noofpackages_'.$con_id])){{$indentData['term_noofpackages_'.$con_id]}} @else - @endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($indentData['package_value'.$con_id])){{$indentData['package_value'.$con_id]}} @else - @endif</div>
                                             @endif    
                                            </div>
                                    @elseif(isset($termindentData) && $termindentData)
                                            <div class="table-row inner-block-bg">
                                            @if(!isset($termindentData->length) || $termindentData->length=='')
                                                <div class="col-md-4 padding-left-none">@if(isset($termindentData->unitweight)){{$termindentData->unitweight}} {{$commonComponent->getWeight($termindentData->lkp_ict_weight_type_id)}} @endif</div>
                                                <div class="col-md-4 padding-left-none">@if(isset($termindentData->noofpackages)){{$termindentData->noofpackages}} @else - @endif</div>
                                                <div class="col-md-4 padding-left-none">@if(isset($termindentData->package_value)){{$termindentData->package_value}} @else - @endif</div>
                                            @else
                                                <div class="col-md-2 padding-left-none">@if(isset($termindentData->length)){{$termindentData->length}} {{$commonComponent->getLengthWeight($termindentData->lkp_ptl_length_uom_id)}}@endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($termindentData->breadth)){{$termindentData->breadth}} {{$commonComponent->getLengthWeight($termindentData->lkp_ptl_length_uom_id)}}@endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($termindentData->height)){{$termindentData->height}} {{$commonComponent->getLengthWeight($termindentData->lkp_ptl_length_uom_id)}}@endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($termindentData->unitweight)){{$termindentData->unitweight}} @endif @if(isset($termindentData->lkp_ict_weight_type_id)){{$commonComponent->getWeight($termindentData->lkp_ict_weight_type_id)}} @endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($termindentData->noofpackages)){{$termindentData->noofpackages}} @else - @endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($termindentData->package_value)){{$termindentData->package_value}} @else - @endif</div>
                                             @endif    
                                            </div>

                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>        
            @endif
	