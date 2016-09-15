
@if($serviceId==RELOCATION_OFFICE_MOVE)
  @inject('sellercomponent', 'App\Components\RelocationOffice\RelocationOfficeSellerComponent')
@endif

@if(isset($buyer_quote_id) && $buyer_quote_id!='')

	{{--*/ $post_items =   $commonComponent->getBuyerRelocationDetailsGSA($buyer_quote_id); /*--}}
	
	@if($post_items)
			@if($serviceId==RELOCATION_DOMESTIC)
               
               @if($post_items[0]->lkp_post_ratecard_type_id==1)
								<li>
								    <span>Property Type<span class="right-doted">:</span></span>
									<span>{{$commonComponent->getPropertyType($post_items[0]->lkp_property_type_id)}}</span>
								</li>
							   <li>
									<span>Volume<span class="right-doted">:</span></span>
									<span>
									{{--*/ $volume_total = $commonComponent->getVolumeCft($post_items[0]->id)+$commonComponent->getCratingVolumeCft($post_items[0]->id) /*--}}
                                                                        {{$commonComponent->number_format($volume_total,false)}} CFT

									</span>
								</li>
								<li>
									<span>Load Type<span class="right-doted">:</span></span>
									<span>
									@if($post_items[0]->lkp_load_category_id==1)
                                         Full Load
                                        @else
                                        Part Load
                                        @endif
                                    </span>
								</li>
								
								@else
								<li>
								
										<span>Vehicle Category<span class="right-doted">:</span></span>
                                        <span>
                                         {{--*/ $vehcat=$commonComponent->getVehicleCategoryById($post_items[0]->lkp_vehicle_category_id) /*--}}
                                         @if($vehcat!="")
                                         {{$vehcat}}
                                         @else
                                         N/A
                                         @endif
                                        </span>
                                       </li>
                                      <li>  
                                        <span>Vehicle Category Type<span class="right-doted">:</span></span>
                                        <span>
                                         {{--*/ $vehcattype=$commonComponent->getVehicleCategorytypeById($post_items[0]->lkp_vehicle_category_type_id) /*--}}
                                         @if($vehcattype!="")
                                         {{$vehcattype}}
                                         @else
                                         N/A
                                         @endif
                                        </span>
                                    </li>

                                <li>
                                        <span>Vehicle Model<span class="right-doted">:</span></span>
                                        <span>
                                         {{$post_items[0]->vehicle_model}}
                                        </span>
                                    </li>
							   @endif
						   
			@elseif($serviceId==RELOCATION_PET_MOVE)
			
			        <li>
                            <span>Pet Type<span class="right-doted">:</span></span>
                            <span>
                            {{$commonComponent->getPetType($post_items[0]->lkp_pet_type_id)}}  
                            </span>
                    </li>
                    <li>
                            <span>Breed<span class="right-doted">:</span></span>
                            <span>
                            @if($post_items[0]->lkp_breed_type_id==0)
                             N/A
                            @else
                            {{$commonComponent->getBreedType($post_items[0]->lkp_breed_type_id)}} 
                           	@endif
                            </span>
                    </li>
                     <li>
                            <span>Cage Type<span class="right-doted">:</span></span>
                            <span>
                            {{$commonComponent->getCageType($post_items[0]->lkp_cage_type_id)}} 
                             
                            </span>
                    </li>
                    <li>
                            <span>Cage Weight<span class="right-doted">:</span></span>
                            <span>
                            {{$commonComponent->getCageWeight($post_items[0]->lkp_cage_type_id)}} KGs
                            </span>
                    </li>
                    
			
			
			@elseif($serviceId==RELOCATION_OFFICE_MOVE)
			
			     {{--*/ $office_buyer_post_inventory_particulars = $sellercomponent->getBuyerInventaryParticulars($post_items[0]->id)  /*--}}
                 @foreach($office_buyer_post_inventory_particulars as $buyer_particulars)
                   <li>
                   <span>{{$buyer_particulars->office_particular_type}}
                   <span class="right-doted">:</span></span>
                   <span>{{$buyer_particulars->number_of_items}}</span>
                   </li>
              @endforeach  
              
              @elseif($serviceId==RELOCATION_INTERNATIONAL)
              
              @if($post_items[0]->lkp_international_type_id==1)
              {{--*/ $buyer_post_inventory_details = $commonComponent->getCartonDetails($post_items[0]->id)  /*--}}
              @foreach($buyer_post_inventory_details as $buyer_cartons)
				<li>
				<span>{{$buyer_cartons->carton_type}}
				<span class="right-doted">:</span></span>
				<span>
                {{$buyer_cartons->number_of_cartons}}
				</span>
				</li> 
				@endforeach 
              @endif
              
             @if($post_items[0]->lkp_international_type_id==2)
               
                <li>
                   <span>Property Type<span class="right-doted">:</span></span>
                   <span>
                    {{$commonComponent->getPropertyType($post_items[0]->lkp_property_type_id)}}  
                   </span>
					
               </li>
               <li>
                   <span>Volume<span class="right-doted">:</span></span>
                   <span>
                      {{--*/ $totalCFT=$relOceanSellerCComponent->getVolumeCft($post_items[0]->id) /*--}} 
                      {{--*/ $volume=round($totalCFT/35.5, 2) /*--}}                                  
                       {{$volume}} CBM
                    </span>
				</li>
             @endif
             
           @elseif($serviceId==RELOCATION_GLOBAL_MOBILITY)
              
             @foreach($post_items as $post_item)
             @if($post_item->lkp_gm_service_id)
             <li>
                <span>{{$commonComponent->getAllGMServiceTypesById($post_item->lkp_gm_service_id)}}<span class="right-doted">:</span></span>
                <span>
                  @if(isset($post_item->measurement_units) && $post_item->measurement_units!='')
                    {{$post_item->measurement}} {{$post_item->measurement_units}}
                  @ENDIF
                </span>
             </li> 
             @endif     
             @endforeach   
                    
		  @endif
	  @endif	
	  
	  @else
	    @if($serviceId==RELOCATION_DOMESTIC)
             @if(Session::get('searchMod.household_items')!='' && Session::get('searchMod.household_items') == 1)
               <li>
								    <span>Property Type<span class="right-doted">:</span></span>
									<span>{{ $commonComponent->getPropertyType(Session::get('searchMod.property_type')) }}</span>
								</li>
							   <li>
									<span>Volume<span class="right-doted">:</span></span>
									
									@if(Session::has('session_total_hidden_volume') && Session::get('searchMod.total_hidden_volume')!="")
									<span class="search-result">{{Session::get('searchMod.total_hidden_volume')}}  CFT</span>
									@else
									<span class="search-result">{{Session::get('searchMod.volume')}}  CFT</span>
									@endif

								</li>
								<li>
									<span>Load Type<span class="right-doted">:</span></span>
									<span>
									@if(Session::get('searchMod.load_type')!='')
									@if(Session::get('searchMod.load_type')==1)
									Full Load
									@else
									Part Load
									@endif
									@else
									-
									@endif
                                    </span>
								</li>
								
								@else
								<li>
								
										<span>Vehicle Category<span class="right-doted">:</span></span>
                                        <span>
                                        @if(Session::get('searchMod.vehicle_category')!='')
                                         {{ $commonComponent->getVehicleCategoryById(Session::get('searchMod.vehicle_category')) }}
                                        @else
                                        N/A
                                        @endif 
                                        </span>
                                       </li>
                                      <li>  
                                        <span>Vehicle Category Type<span class="right-doted">:</span></span>
                                        <span>
                                        @if(Session::get('searchMod.vehicle_category_type')!='')
                                         {{ $commonComponent->getVehicleCategorytypeById(Session::get('searchMod.vehicle_category_type')) }}
                                        @else
                                        N/A
                                        @endif 
                                        </span>
                                    </li>

                                <li>
                                        <span>Vehicle Model<span class="right-doted">:</span></span>
                                        <span>
                                         {{ Session::get('searchMod.vehicle_model') }}
                                        </span>
                                    </li>
							   @endif
		@elseif($serviceId==RELOCATION_PET_MOVE)
		
					<li>
                            <span>Pet Type<span class="right-doted">:</span></span>
                            <span>
                            {{ $commonComponent->getPetType(Session::get('searchMod.pet_type_reslocation')) }}
                            </span>
                    </li>
                    <li>
                            <span>Breed<span class="right-doted">:</span></span>
                            <span>
                            @if(Session::get('searchMod.selBreedtype')!=0)
                            {{ $commonComponent->getBreedType(Session::get('searchMod.breed_type_reslocation')) }}
                            @else
                            N/A
                            @endif
                            </span>
                    </li>
                     <li>
                            <span>Cage Type<span class="right-doted">:</span></span>
                            <span>
                            {{ $commonComponent->getCageType(Session::get('searchMod.cage_type_reslocation')) }}
                             
                            </span>
                    </li>
                   
         @elseif($serviceId==RELOCATION_OFFICE_MOVE) 
         
             {{--*/ $office_items =   Session::get('searchMod.particulars_buyer'); /*--}}      			   
			  @for($i=1;$i<=count($office_items);$i++)
			  @if(isset($office_items[$i]) && $office_items[$i]!="")
			  <li>
			  	   	
                   <span>{{$commonComponent->getOfficeParticularsByid($i)}}
                   <span class="right-doted">:</span></span>
                   <span>{{$office_items[$i]}}</span>
              </li>
			  @endif
			  @endfor
		@elseif($serviceId==RELOCATION_INTERNATIONAL)
		
		
		@if(Session::get('searchMod.service_type_buyer')==1)
		 @if(Session::get('searchMod.cartons_1')!='') 	
		<li>
				
				<span>
				Carton 1
				<span class="right-doted">:</span></span>
				<span>
                {{Session::get('searchMod.cartons_1')}}
				</span>
		</li> 
		@endif
		@if(Session::get('searchMod.cartons_2')!='')
		<li>
				
				<span>
				Carton 2
				<span class="right-doted">:</span></span>
				<span>
                {{Session::get('searchMod.cartons_2')}}
				</span>
		</li> 
		@endif
		@if(Session::get('searchMod.cartons_3')!='')
		<li>
				
				<span>
				Carton 3
				<span class="right-doted">:</span></span>
				<span>
                {{Session::get('searchMod.cartons_3')}}
				</span>
				
		</li> 
		@endif
		@else
		
		<li>
           <span>Property Type<span class="right-doted">:</span></span>
                   <span>
                    {{$commonComponent->getPropertyType(Session::get('searchMod.property_type_buyer'))}}  
                   </span>
					
               </li>
               <li>
                   <span>Volume<span class="right-doted">:</span></span>
                   <span>
                       {{Session::get('session_ocean_search_volume')}} CBM
                    </span>
				</li>
		
		@endif  
		
		@elseif($serviceId==RELOCATION_GLOBAL_MOBILITY)
		
		{{--*/ $global_items =   Session::get('relocbuyerrequest'); /*--}}
		<li>
                <span>{{$commonComponent->getAllGMServiceTypesById($global_items['relgm_service_type'])}}<span class="right-doted">:</span></span>
                <span>
                @if(isset($global_items['measurement_unit']) && $global_items['measurement_unit']!='')
                {{$global_items['measurement']}} {{$global_items['measurement_unit']}}
                @endif
                </span>
             </li> 			   
               
     @endif     	
	@endif