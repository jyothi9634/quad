@if(isset($buyer_quote_id) && $buyer_quote_id!='')
			
			{{--*/ $post_items =   $commonComponent->getBuyerPostDetailsGSA($buyer_quote_id); /*--}}
			
			@if($serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN)
                    <div class="col-sm-12">        
                        <div class="gray-bg">
                            <div class="table-div table-style1 margin-top form-control-grid">
                                <div class="table-heading inner-block-bg">
                                    <div class="col-md-1 padding-left-none">L(Unit)</div>
                                    <div class="col-md-1 padding-left-none">B(Unit)</div>
                                    <div class="col-md-1 padding-left-none">H(Unit)</div>
                                    <div class="col-md-1 padding-left-none">Volume</div>
                                    <div class="col-md-2 padding-left-none">Unit Weight</div>
                                    <div class="col-md-2 padding-left-none">No of packages</div>
                                    <div class="col-md-2 padding-left-none">Load Type</div>
                                    <div class="col-md-2 padding-left-none">Packaging Type</div>
                                </div>

                                <div class="table-data">
                                    @if($post_items){{--*/ $i = 1 /*--}}
                                        @foreach($post_items as $post_item)
                                            <div class="table-row inner-block-bg">
<!--                                         <div class="col-md-2 padding-left-none">{{$i}}</div> -->
                                                <div class="col-md-1 padding-left-none">@if(isset($post_item->length)){{$post_item->length}} {{$post_item->length_weight}}@endif</div>
                                                <div class="col-md-1 padding-left-none">@if(isset($post_item->breadth)){{$post_item->breadth}} {{$post_item->length_weight}}@endif</div>
                                                <div class="col-md-1 padding-left-none">@if(isset($post_item->height)){{$post_item->height}} {{$post_item->length_weight}}@endif</div>
                                                <div class="col-md-1 padding-left-none">@if(isset($post_item->calculated_volume_weight)){{$post_item->calculated_volume_weight}}
                                               @if($serviceId==ROAD_PTL || $serviceId==RAIL)  
												CFT
												@elseif($serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL)
												CCM
												@else
												CBM
												@endif
                                                @else - @endif</div>
												<div class="col-md-2 padding-left-none">{{$post_item->units}} @if(isset($post_item->weight_type)){{$post_item->weight_type}} @endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($post_item->number_packages)){{$post_item->number_packages}} @else - @endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($post_item->load_type)){{$post_item->load_type}}@else - @endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($post_item->packaging_type_name)){{$post_item->packaging_type_name}}@else - @endif</div>
                                            </div>
                                            {{--*/ $i++ /*--}}
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
           @elseif($serviceId==COURIER)  
           
               <div class="col-sm-12">        
                        <div class="gray-bg">
                            <div class="table-div table-style1 margin-top form-control-grid">
                            	<div class="table-heading inner-block-bg">
                            	@if($post_items[0]->lkp_courier_type_id==1)
                            	
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

                                <div class="table-data">
                                    @if($post_items){{--*/ $i = 1 /*--}}
                                        @foreach($post_items as $post_item)
                                            <div class="table-row inner-block-bg">
                                            @if($post_items[0]->lkp_courier_type_id==1)
                            	
												<div class="col-md-4 padding-left-none">@if(isset($post_item->units)){{$post_item->units}} {{$post_item->weight_type}}@endif</div>
			                                    <div class="col-md-4 padding-left-none">@if(isset($post_item->number_packages)){{$post_item->number_packages}}@endif</div>
			                                    <div class="col-md-4 padding-left-none">@if(isset($post_item->package_value)){{$post_item->package_value}}@endif</div>
			                            	@else
<!--                                         <div class="col-md-2 padding-left-none">{{$i}}</div> -->
                                                <div class="col-md-2 padding-left-none">@if(isset($post_item->length)){{$post_item->length}} {{$post_item->length_weight}}@endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($post_item->breadth)){{$post_item->breadth}} {{$post_item->length_weight}}@endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($post_item->height)){{$post_item->height}} {{$post_item->length_weight}}@endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($post_item->calculated_volume_weight)){{$post_item->calculated_volume_weight}} CFT @else - @endif</div>
												<div class="col-md-2 padding-left-none">{{$post_item->units}} @if(isset($post_item->weight_type)){{$post_item->weight_type}} @endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($post_item->number_packages)){{$post_item->number_packages}} @else - @endif</div>
                                             @endif    
                                            </div>
                                            {{--*/ $i++ /*--}}
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                       
            @endif
            
            @else
            
              @if($serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN)
                    <div class="col-sm-12">        
                        <div class="gray-bg">
                            <div class="table-div table-style1 margin-top form-control-grid">
                                <div class="table-heading inner-block-bg">
                                    <div class="col-md-1 padding-left-none">L(Unit)</div>
                                    <div class="col-md-1 padding-left-none">B(Unit)</div>
                                    <div class="col-md-1 padding-left-none">H(Unit)</div>
                                    <div class="col-md-1 padding-left-none">Volume</div>
                                    <div class="col-md-2 padding-left-none">Unit Weight</div>
                                    <div class="col-md-2 padding-left-none">No of packages</div>
                                    <div class="col-md-2 padding-left-none">Load Type</div>
                                    <div class="col-md-2 padding-left-none">Packaging Type</div>
                                </div>

                                <div class="table-data">
                                @if(isset($allInput['search_ptl_buyer_from_id_'.$sellerPostId]))
                                {{--*/ $from_ptl=$allInput['search_ptl_buyer_from_id_'.$sellerPostId] /*--}}
                                @else
                                {{--*/ $from_ptl='' /*--}}
                                @endif
                                     @if($request_blade)	
                                        @for($i=0;$i<count($request_blade['ptlLength']);$i++)
                                        @if($request_blade['ptlFromLocation'][$i]==$from_ptl)
                                            <div class="table-row inner-block-bg">
												<div class="col-md-1 padding-left-none">@if(isset($request_blade['ptlLength'][$i])){{$request_blade['ptlLength'][$i]}} {{$commonComponent->getLengthWeight($request_blade['ptlCheckVolWeight'][$i])}}@endif</div>
                                                <div class="col-md-1 padding-left-none">@if(isset($request_blade['ptlWidth'][$i])){{$request_blade['ptlWidth'][$i]}} {{$commonComponent->getLengthWeight($request_blade['ptlCheckVolWeight'][$i])}}@endif</div>
                                                <div class="col-md-1 padding-left-none">@if(isset($request_blade['ptlHeight'][$i])){{$request_blade['ptlHeight'][$i]}} {{$commonComponent->getLengthWeight($request_blade['ptlCheckVolWeight'][$i])}}@endif</div>
                                                <div class="col-md-1 padding-left-none">@if(isset($request_blade['ptlDisplayVolumeWeight'][$i])){{$request_blade['ptlDisplayVolumeWeight'][$i]}} 
                                                @if($serviceId==ROAD_PTL || $serviceId==RAIL)  
												CFT
												@elseif($serviceId==AIR_DOMESTIC || $serviceId==AIR_INTERNATIONAL)
												CCM
												@else
												CBM
												@endif
                                                @else - @endif</div>
												<div class="col-md-2 padding-left-none">@if(isset($request_blade['ptlUnitsWeight'][$i])){{$request_blade['ptlUnitsWeight'][$i]}} @endif @if(isset($request_blade['ptlCheckUnitWeight'][$i])){{$commonComponent->getWeight($request_blade['ptlCheckUnitWeight'][$i])}} @endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($request_blade['ptlNopackages'][$i])){{$request_blade['ptlNopackages'][$i]}} @else - @endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($request_blade['ptlLoadType'][$i])){{$commonComponent->getLoadType($request_blade['ptlLoadType'][$i])}}@else - @endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($request_blade['ptlPackageType'][$i])){{$commonComponent->getPackageType($request_blade['ptlPackageType'][$i])}}@else - @endif</div>
                                            </div>
                                        @endif  
                                        @endfor
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
             @elseif($serviceId==COURIER)  
           
               <div class="col-sm-12">        
                        <div class="gray-bg">
                            <div class="table-div table-style1 margin-top form-control-grid">
                            	<div class="table-heading inner-block-bg">
                            	@if(isset($request_blade['courier_types'][0]) && $request_blade['courier_types'][0]==1)
                            	
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

                                <div class="table-data">
                                @if(isset($allInput['search_ptl_buyer_from_id_'.$sellerPostId]))
                                {{--*/ $from_ptl=$allInput['search_ptl_buyer_from_id_'.$sellerPostId] /*--}}
                                @else
                                {{--*/ $from_ptl='' /*--}}
                                @endif
                                    @if($request_blade)
                                        @for($i=0;$i<count($request_blade['ptlUnitsWeight']);$i++)
                                        @if($request_blade['ptlFromLocation'][$i]==$from_ptl)
                                            <div class="table-row inner-block-bg">
                                            @if(isset($request_blade['courier_types'][0]) && $request_blade['courier_types'][0]==1)
                            	
												<div class="col-md-4 padding-left-none">@if(isset($request_blade['ptlUnitsWeight'][$i])){{$request_blade['ptlUnitsWeight'][$i]}} @endif</div>
			                                    <div class="col-md-4 padding-left-none">@if(isset($request_blade['ptlNopackages'][$i])){{$request_blade['ptlNopackages'][$i]}} @else - @endif</div>
			                                    <div class="col-md-4 padding-left-none">@if(isset($request_blade['packeagevalue'][$i])){{$request_blade['packeagevalue'][$i]}} @else - @endif</div>
			                            	@else
                                         
                                                <div class="col-md-2 padding-left-none">@if(isset($request_blade['ptlLengthCourier'][$i])){{$request_blade['ptlLengthCourier'][$i]}} {{$commonComponent->getLengthWeight($request_blade['ptlCheckVolWeightCourier'][$i])}}@endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($request_blade['ptlWidthCourier'][$i])){{$request_blade['ptlWidthCourier'][$i]}} {{$commonComponent->getLengthWeight($request_blade['ptlCheckVolWeightCourier'][$i])}}@endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($request_blade['ptlHeightCourier'][$i])){{$request_blade['ptlHeightCourier'][$i]}} {{$commonComponent->getLengthWeight($request_blade['ptlCheckVolWeightCourier'][$i])}}@endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($request_blade['ptlDisplayVolumeWeight'][$i])){{$request_blade['ptlDisplayVolumeWeight'][$i]}} CFT @else - @endif</div>
												<div class="col-md-2 padding-left-none">@if(isset($request_blade['ptlUnitsWeight'][$i])){{$request_blade['ptlUnitsWeight'][$i]}} @endif @if(isset($request_blade['ptlCheckUnitWeight'][$i])){{$commonComponent->getWeight($request_blade['ptlCheckUnitWeight'][$i])}} @endif</div>
                                                <div class="col-md-2 padding-left-none">@if(isset($request_blade['ptlNopackages'][$i])){{$request_blade['ptlNopackages'][$i]}} @else - @endif</div>
                                                
                                             @endif    
                                            </div>
                                        @endif   
                                        @endfor
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>        
			@endif
			@endif