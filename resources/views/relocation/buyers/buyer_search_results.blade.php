@extends('app')
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

		<div class="clearfix"></div>

		<div class="main">

			<div class="container">
			
			<h1 class="page-title">Search Results (Relocation)</h1><a href="#" class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a>
				
				<!-- Search Block Starts Here -->

				<div class="search-block inner-block-bg">
					<div class="from-to-area">
						<span class="search-result">
							<i class="fa fa-map-marker"></i>
							<span class="location-text">{{ $request['from_location'] }} to {{ $request['to_location'] }}</span>
						</span>
					</div>
					<div class="date-area">
						<div class="col-md-6 padding-none">
							<p class="search-head">Dispatch Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
								{{ $request['from_date'] }}
							</span>
						</div>
						<div class="col-md-6 padding-none">
							<p class="search-head">Delivery Date</p>
							<span class="search-result">
								<i class="fa fa-calendar-o"></i>
                                                                @if(isset($request['to_date']) && $request['to_date']!='')
								{{ $request['to_date'] }}
                                                                @else
                                                                NA
                                                                @endif
							</span>
						</div>
					</div>
					@if(isset($household_items) && $household_items == 1)
					<div>
						<p class="search-head">Property Type</p>
						<span class="search-result">{{ $commonComponent->getPropertyType($request['property_type']) }}</span>
					</div>
					<div>
						<p class="search-head">CFT</p>
						<?php 
						$searchvolume = (isset($request['total_hidden_volume']) && !empty($request['total_hidden_volume']) && $request['total_hidden_volume']!=1) ? $request['total_hidden_volume'] : $request['volume'];?>
						<span class="search-result">{{ $searchvolume }}</span>
					</div>
					<div>
						<p class="search-head">Load Type</p>
						<span class="search-result">
						@if(isset($request['load_type']))
						@if($request['load_type']==1)
						Full Load
						@else
						Part Load
						@endif
						@else
						-
						@endif
						</span>
					</div>
					@else
					<div>
						<p class="search-head">Vehicle Type</p>
						<span class="search-result">{{ $commonComponent->getVehicleCategoryById($request['vehicle_category']) }}</span>
					</div>
					<div>
						<p class="search-head">Vehicle Model</p>
						<span class="search-result">{{ $vehicle_model }}</span>
					</div>
					<div>
						<p class="search-head">Category Type</p>
						<span class="search-result">
							@if($request['vehicle_category'] == 1)
								{{ $commonComponent->getVehicleCategorytypeById($request['vehicle_category_type']) }}
							@else
								N/A
							@endif	
						</span>
					</div>
					@endif
					
					<div class="search-modify" data-toggle="modal" data-target="#modify-search">
						<span>Modify Search +</span>
					</div>
				</div>

				<!-- Search Block Ends Here -->



				<h2 class="side-head pull-left">Filter Results </h2>
				<div class="page-results pull-left col-md-2 padding-none">
					<div class="form-control-fld">
						<div class="normal-select">
							<select class="selectpicker">
								<option value="0">10 Records Per page</option>
							</select>
						</div>
					</div>
				</div>
				<a href="{{'/relocation/creatbuyerrpost?search=1'}}"><button class="btn post-btn pull-right">Post &amp; get Quote</button></a>

				<div class="clearfix"></div>
				
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 
						
						<!-- Left Section Starts Here -->
					
						<div class="main-left">
						{!! Form::open(['url' =>'#','id' => 'buyer_results_form','class'=>'filter_form','method'=>'get']) !!}
								{!! Form::hidden('from_location_id', $from_location_id) !!}
								{!! Form::hidden('filter_set', 1) !!}
								{!! Form::hidden('to_location_id', $to_location_id) !!}
                                {!! Form::hidden('from_location', $from_location) !!}
                                {!! Form::hidden('to_location', $to_location) !!}		
                                {!! Form::hidden('property_type', $property_type) !!}		
                                {!! Form::hidden('volume', $volume) !!}			
                                {!! Form::hidden('post_rate_card_type', $post_rate_card_type) !!}
                                {!! Form::hidden('load_type', $load_type) !!}		
                                {!! Form::hidden('household_items', $household_items) !!}	
                                {!! Form::hidden('vehicle_category', $vehicle_category) !!}	
                                {!! Form::hidden('vehicle_model', $vehicle_model) !!}	
                                {!! Form::hidden('vehicle_category_type', $vehicle_category_type) !!}
                                {!! Form::hidden('total_hidden_volume', $request['total_hidden_volume']) !!}
                                @if(Session::has('session_elevator1'))
                                	{!! Form::hidden('elevator1', Session::get('session_elevator1')) !!}
								@endif
								@if(Session::has('session_elevator2'))
                                	{!! Form::hidden('elevator2', Session::get('session_elevator2')) !!}
								@endif
                                <input type="hidden" name="filter_set" id="filter_set" value="1">
							<div class="seller-list inner-block-bg">
								<div class="form-control-fld margin-top">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-archive"></i></span>
										<select class="selectpicker">
											<option>Enquiry Type (All)</option>
										</select>
									</div>
								</div>
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										<input type="text" class="form-control" placeholder="From Location" value="{{ $from_location }}" readonly/>
									</div>
								</div>		
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-map-marker"></i></span>
										<input type="text" class="form-control" placeholder="To Location" value="{{ $to_location }}" readonly/>
									</div>
								</div>					
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										<input type="text" class="form-control calendar" name="from_date" placeholder="Dispatch Date" value="{{ $request['from_date'] }}" onChange="this.form.submit()" readonly/>
									</div>									
								</div>
								<div class="form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-calendar-o"></i></span>
										<input type="text" class="form-control calendar" placeholder="Delivery Date" name="to_date" value="{{ $request['to_date'] }}" onChange="this.form.submit()" readonly/>
									</div>									
								</div>								
							</div>

							


							<?php $selectedPayment = isset($_REQUEST['selected_payments']) ? $_REQUEST['selected_payments'] : array(); ?>
							@if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
								@if (Session::has('layered_filter_payments')&& Session::get('layered_filter_payments')!="")
							<h2 class="filter-head">Payment Mode</h2>
							<div class="payment-mode inner-block-bg">
								@if(Session::has('layered_filter_payments') && is_array(Session::get('layered_filter_payments')))
									@foreach (Session::get('layered_filter_payments') as $paymentId => $paymentName)
									<?php $selected = in_array($paymentId, $selectedPayment) ? 'checked="checked"' : ""; ?>
									<div class="check-box"><input type="checkbox" class="filtercheckbox" value="{{$paymentId}}" {{$selected}} name="selected_payments[]" onClick="this.form.submit()"/><span class="lbl padding-8">
																		@if ($paymentName == 'Advance')
																		{{--*/ $paymentType = 'Online Payment' /*--}}
																		@else
																		{{--*/ $paymentType = $paymentName /*--}}
																		@endif
																		{{$paymentType}}
																		</span></div>
									@endforeach
								@endif
							</div>
							@endif
							@endif
							
                           @include("partials.filter._price")

							<?php	$selectedSellers = isset($_REQUEST['selected_users']) ? $_REQUEST['selected_users'] : array();
								?>
								
								@if (Session::has('show_layered_filter')&& Session::get('show_layered_filter')!="")
									@if (Session::has('layered_filter')&& Session::get('layered_filter')!="")
										<h2 class="filter-head"><?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?> List</h2>
										<div class="seller-list inner-block-bg">
											@if(Session::has('layered_filter') && is_array(Session::get('layered_filter')))
												@foreach (Session::get('layered_filter') as $userId => $userName)
													<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
													<div class="check-box"><input type="checkbox" class="checkbox pull-left filtercheckbox" value="{{$userId}}" {{$selected}} name="selected_users[]" onChange="this.form.submit()"><span class="lbl padding-8">{{ $userName }}</span></div>
													<div class="col-xs-12 padding-none"> </div>
												@endforeach
											@endif
										</div>
									@endif
								@endif

							                
							
						{!! Form::close() !!}	
						</div>
					
						<!-- Left Section Ends Here -->


						<!-- Right Section Starts Here -->

						<div class="main-right">
							<!-- Table Starts Here -->
							<div class="table-div">								
								{!! $gridBuyer !!}
							</div>	
						</div>

						<!-- Right Section Ends Here -->

					</div>
				</div>
				
		
     <div class="clearfix"></div>
				<div class="clearfix"></div>
			<a href="/relocation/creatbuyerrpost"><button class="btn post-btn pull-right">Post &amp; get Quote</button></a>	
			</div>
	</div>
		
@include('partials.footer')

	<!-- Modal -->
	  <div class="modal fade" id="modify-search" role="dialog">
	    <div class="modal-dialog">	    
		@if(Session::get('session_household_items') == 1)
			{{--*/ $household_selected = "checked" /*--}}
		@else
			{{--*/ $household_selected = "" /*--}}
		@endif
		@if(Session::get('session_household_items') == 2)
			{{--*/ $vechile_selected = "checked" /*--}}
		@else
			{{--*/ $vechile_selected = "" /*--}}
		@endif

		@if(isset($_REQUEST['total_hidden_volume']))
			{{--*/ $total_hidden_volume = $_REQUEST['total_hidden_volume'] /*--}}
		@else
			{{--*/ $total_hidden_volume = "1" /*--}}
		@endif

		@if(isset($_REQUEST['crating_items']))
			{{--*/ $crating_items = $_REQUEST['crating_items'] /*--}}
		@else
			{{--*/ $crating_items = "0" /*--}}
		@endif

	      <!-- Modal content-->
	      <div class="modal-content">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <div class="modal-body">
	          <div class="col-md-12 modal-form">
				<div class="col-md-12 padding-none">
					
				{!! Form::open(['url' => 'byersearchresults','id'=>'posts-form_buyer_relocation','method'=>'get']) !!}
				{!! Form::hidden('household_items', Session::get('session_household_items'), array('id' => 'household_items')) !!}
                {!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
					{!! Form::hidden('crating_items', $crating_items, array('id' => 'crating_items')) !!}
					{!! Form::hidden('total_hidden_volume', $total_hidden_volume, array('id' => 'total_hidden_volume')) !!}
					{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
					<div class="col-md-12 form-control-fld">
						<div class="radio-block">
							<input type="radio" id="post_rate_card_type_1" name="post_rate_card_type" class="ratetype_selection_buyer" value="1" {{ $household_selected }} />
							<label for="post_rate_card_type_1"><span></span>House Hold</label>
								
							<input type="radio" id="post_rate_card_type_2" name="post_rate_card_type" class="ratetype_selection_buyer" value="2" {{ $vechile_selected }} >
							<label for="post_rate_card_type_2"><span></span>Vehicle</label>
						</div>
					</div>
                                        
                                        <!-- 
                                                        {!! Form::hidden('is_commercial', Session::get('session_is_commercial_date_buyer')) !!}
                                                        @if(Session::get('session_is_commercial_date_buyer') == 1)
                                                                {{--*/ $is_commercial = "checked" /*--}}
                                                        @else
                                                                {{--*/ $is_commercial = "" /*--}}
                                                        @endif
                                                        @if(Session::get('session_is_commercial_date_buyer') == 0)
                                                                {{--*/ $is_noncommercial = "checked" /*--}}
                                                        @else
                                                                {{--*/ $is_noncommercial = "" /*--}}
                                                        @endif -->
                                                        
                                                       <!--  <div class="col-md-12 form-control-fld margin-none">
                                                            <div class="radio-block">
                                                                <div class="radio_inline"><input type="radio" name="is_commercial" id="is_commercial"  value="1" {{ $is_commercial }} /> <label for="is_commercial"><span></span>Commercial</label></div>
                                                                <div class="radio_inline"><input type="radio" name="is_commercial" id="non_commercial" value="0" {{$is_noncommercial}} /> <label for="non_commercial"><span></span>Non Commercial</label></div>
                                                            </div>
                                                        </div> -->
                                        
                                        
                                        
					<div class="col-md-12 padding-none">
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('from_location', Session::get('session_from_location_buyer') , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*']) !!}
	                                {!! Form::hidden('from_location_id', Session::get('session_from_city_id_buyer') , array('id' => 'from_location_id')) !!}
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('to_location', Session::get('session_to_location_buyer'),  ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*']) !!}
	                               	{!! Form::hidden('to_location_id', Session::get('session_to_city_id_buyer') , array('id' => 'to_location_id')) !!}
								</div>
							</div>
	
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('from_date', Session::get('session_dispatch_date_buyer'),  ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date*']) !!}
									<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>">
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('to_date', Session::get('session_delivery_date_buyer') , ['id' => 'delivery_date1','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date']) !!}
									<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="<?php echo isset($_REQUEST['delivery_flexible_hidden']) ? $_REQUEST['delivery_flexible_hidden'] : ""; ?>">
								</div>
							</div>
							@if(Session::get('session_household_items') == 2)
								<div class="relocation_house_hold_buyer_create" style="display:none;">
							@else
								<div class="relocation_house_hold_buyer_create">
							@endif
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-home"></i></span>
									{!!	Form::select('property_type',(['' => 'Property Type *'] +$property_types), Session::get('session_property_type') ,['class' =>'selectpicker','id'=>'property_type','onchange'=>'return getPropertyCft()']) !!}
								</div>
							</div>
							<div class="col-md-2 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-balance-scale"></i></span>
										{!! Form::text('volume', Session::get('session_volume'), ['id' => 'volume','class' => 'form-control','readonly' => true, 'placeholder' => 'Volume*']) !!}
										<span class="add-on unit1 manage">
											CFT
										</span>
									</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!!	Form::select('load_type',(['' => 'Load Type *'] +$load_types), Session::get('session_load_type') ,['class' =>'selectpicker','id'=>'load_type']) !!}
								</div>
							</div>
							<div class="col-md-12 form-control-fld text-right margin-none">
								<span class="red spl-link advanced-search-link"><span class="more-search">+</span><span class="less-search">-</span> Advanced Search</span>
							</div>	
							{{--*/ $elevator1yes = "" /*--}}
							{{--*/ $elevator1no = "" /*--}}
							{{--*/ $elevator2yes = "" /*--}}
							{{--*/ $elevator2no = "" /*--}}
							@if(Session::get('session_elevator1')==1)
									{{--*/ $elevator1yes = "checked" /*--}}
									@else
									{{--*/ $elevator1no = "checked" /*--}}
							@endif
							@if(Session::get('session_elevator2')==1)
									{{--*/ $elevator2yes = "checked" /*--}}
									@else
									{{--*/ $elevator2no = "checked" /*--}}
							@endif
							<div class="advanced-search-details">
								<div class="col-md-4 form-control-fld margin-top">
									<div class="radio-block">
									
										<span class="padding-right-15">Origin Elevator</span> 
										<input type="radio" id="elevator1_a" name="elevator1" {{$elevator1yes}}>
										<label for="elevator1_a"><span></span>Yes</label>
											
										<input type="radio" id="elevator1_b" name="elevator1" {{$elevator1no}}>
										<label for="elevator1_b"><span></span>No</label>
									</div>
								</div>
								<div class="col-md-4 form-control-fld margin-top">
									<div class="radio-block">
										<span class="padding-right-15">Destination Elevator</span> 
										<input type="radio" id="elevator2_a" name="elevator2" {{$elevator2yes}}>
										<label for="elevator2_a"><span></span>Yes</label>
											
										<input type="radio" id="elevator2_b" name="elevator2" {{$elevator2no}}>
										<label for="elevator2_b"><span></span>No</label>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-md-4 form-control-fld">
									<div class="radio-block"><input type="checkbox" name="origin_storage_serivce" id="origin_storage_serivce" <?php if(Session::has('session_origin_storage_serivce')){ echo "checked"; } ?>> <span class="lbl padding-8">Storage</span></div>
									<div class="radio-block"><input type="checkbox" name="origin_handy_serivce" id="origin_handy_serivce" <?php if(Session::has('session_origin_handy_serivce')){ echo "checked"; } ?>> <span class="lbl padding-8">Handyman Services</span></div>
									<div class="radio-block"><input type="checkbox" name="insurance_serivce" id="insurance_serivce" <?php if(Session::has('session_insurance_serivce')){ echo "checked"; } ?>> <span class="lbl padding-8">Insurance</span></div>
									<div class="radio-block"><input type="checkbox" name="escort_serivce" id="escort_serivce" <?php if(Session::has('session_escort_serivce')){ echo "checked"; } ?>> <span class="lbl padding-8">Escort</span></div>
									<div class="radio-block"><input type="checkbox" name="mobilty_serivce" id="mobilty_serivce" <?php if(Session::has('session_mobilty_serivce')){ echo "checked"; } ?>> <span class="lbl padding-8">Mobility</span></div>
									<div class="radio-block"><input type="checkbox" name="property_serivce" id="property_serivce" <?php if(Session::has('session_property_serivce')){ echo "checked"; } ?>> <span class="lbl padding-8">Property</span></div>
									<div class="radio-block"><input type="checkbox" name="setting_serivce" id="setting_serivce" <?php if(Session::has('session_setting_serivce')){ echo "checked"; } ?>> <span class="lbl padding-8">Setting Service</span></div>
									<div class="radio-block"><input type="checkbox" name="insurance_domestic" id="insurance_domestic" <?php if(Session::has('session_insurance_domestic')){ echo "checked"; } ?>> <span class="lbl padding-8">Insurance Domestic</span></div>
								</div>
								<div class="col-md-4 form-control-fld">
									<div class="radio-block"><input type="checkbox" name="destination_storage_serivce" id="destination_storage_serivce" <?php if(Session::has('session_destination_storage_serivce')){ echo "checked"; } ?>> <span class="lbl padding-8">Storage</span></div>
									<div class="radio-block"><input type="checkbox" name="destination_handy_serivce" id="destination_handy_serivce" <?php if(Session::has('session_destination_handy_serivce')){ echo "checked"; } ?>> <span class="lbl padding-8">Handyman Services</span></div>
								</div>
								
							<div class="clearfix"></div>
							<div class="col-md-3 form-control-fld margin-top">
								<div class="normal-select">
									{!!	Form::select('room_type',(['' => 'Select Inventory *'] +$room_types), '' ,['class' =>'selectpicker select-inventory','id'=>'room_type','onchange'=>'return getRoomParticulars()']) !!}
								</div>
							</div>	
							
							<div class="clearfix"></div>
							<!-- Table Starts Here -->
							<div class="table-div table-style1 inventory-block">
								<div class="table-div table-style1 inventory-table">									
									<!-- Table Head Starts Here -->
									<div class="table-heading inner-block-bg">
										<div class="col-md-6 padding-left-none">&nbsp;</div>
										<div class="col-md-2 padding-left-none text-center">No of Items</div>
										<div class="col-md-2 padding-left-none text-center">Packing Required</div>
										<div class="col-md-2 padding-left-none text-center">Crating Required</div>									
									</div>
									<!-- Table Head Ends Here -->
									<div id="inventory_data" name="inventory_data"></div>									
								</div>
								<!-- Table Starts Here -->
								<div class="col-md-12 form-control-fld">
									<input type=button class="btn add-btn pull-right save-continue-search" name="savecontinue" id="savecontinue" value="Save & Continue">
								</div>							
								<div class="clearfix"></div>
								<div class="after-inventory-block margin-top">								
									<div class="table-div table-style1">									
									<div name="inventory_count_div" id="inventory_count_div"></div>
									</div>									
								</div>
							</div>
						</div>	
							
							</div>
							@if(Session::get('session_household_items') == 2)
							<div class="relocation_vehicle_buyer_create" style="display:block;">
							@else
							<div class="relocation_vehicle_buyer_create" style="display:none;">
							@endif
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-home"></i></span>
										{!!	Form::select('vehicle_category',(['' => 'Vehicle Category *'] +$vehicletypecategories), Session::get('session_vehicle_category') ,['class' =>'selectpicker','id'=>'vehicle_category','onchange'=>'return getVehicleTypes()']) !!}
									</div>								
								</div>
								<div class="col-md-3 form-control-fld vehicle_type_car" style="display:<?php echo (Session::get('session_vehicle_category') == 1) ? 'block' : "none" ?>">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-home"></i></span>
										{!!	Form::select('vehicle_category_type',(['' => 'Vehicle Category Type *'] +$vehicletypecategorietypes), Session::get('session_vehicle_category_type') ,['class' =>'selectpicker','id'=>'vehicle_category_type']) !!}
									</div>								
								</div>
								<div class="col-md-2 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-balance-scale"></i></span>
										{!! Form::text('vehicle_model', Session::get('session_vehicle_model'), ['id' => 'vehicle_model','class' => 'form-control', 'placeholder' => 'Vehicle Model*']) !!}										
									</div>
								</div>							
							</div>
							
										
					</div>
					<div class="col-md-4 col-md-offset-4">
						<input  type="submit" class="btn theme-btn btn-block"  name="getquote" id="getquote" value="Search">
					</div>									
				</div>
	      {!! Form::close() !!}




				</div>
			</div>
	        </div>
	      </div>
	      
	    </div>
	  </div>
@endsection