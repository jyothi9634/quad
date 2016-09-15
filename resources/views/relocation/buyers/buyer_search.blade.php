@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
	
		<!-- Header Starts Here -->		
		<div class="clearfix"></div>
		<div class="main">
			<div class="container">
				<div class="home-search gray-bg margin-top-none">
					{!! Form::open(['url' => 'byersearchresults','id'=>'posts-form_buyer_relocation','method'=>'get']) !!}
					{!! Form::hidden('household_items', '1', array('id' => 'household_items')) !!}
					{!! Form::hidden('crating_items', '0', array('id' => 'crating_items')) !!}
					{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
                                        
                                        
					<div class="col-md-12 form-control-fld">
						<div class="radio-block">
							<input type="radio" id="post_rate_card_type_1" name="post_rate_card_type" class="ratetype_selection_buyer" value="1" checked />
							<label for="post_rate_card_type_1"><span></span>House Hold</label>
								
							<input type="radio" id="post_rate_card_type_2" name="post_rate_card_type" class="ratetype_selection_buyer" value="2" >
							<label for="post_rate_card_type_2"><span></span>Vehicle</label>
						</div>
					</div>
                                        
                                       <!--  <div class="col-md-12 form-control-fld margin-none">
                                                <div class="radio-block">
                                                        <div class="radio_inline"><input type="radio" name="is_commercial" id="is_commercial"  value="1" checked /> <label for="is_commercial"><span></span>Commercial</label></div>
                                                        <div class="radio_inline"><input type="radio" name="is_commercial" id="non_commercial" value="0" /> <label for="non_commercial"><span></span>Non Commercial</label></div>
                                                </div>
                                        </div> -->
                                        
					<div class="col-md-12 padding-none">
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('from_location', '' , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*']) !!}
	                                {!! Form::hidden('from_location_id', '' , array('id' => 'from_location_id')) !!}
								</div>
							</div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('to_location', '',  ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*']) !!}
	                               	{!! Form::hidden('to_location_id', '' , array('id' => 'to_location_id')) !!}
								</div>
							</div>
	
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('from_date', '',  ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date*']) !!}
									<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('to_date', '' , ['id' => 'delivery_date1','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date']) !!}
									<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="0">
								</div>
							</div>
							
							<div class="relocation_house_hold_buyer_create">
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-home"></i></span>
									{!!	Form::select('property_type',(['' => 'Property Type *'] +$property_types), '' ,['class' =>'selectpicker','id'=>'property_type','onchange'=>'return getPropertyCft()']) !!}
								</div>
							</div>
							<div class="col-md-2 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-balance-scale"></i></span>
										{!! Form::text('volume', '', ['id' => 'volume','class' => 'form-control','readonly' => true, 'placeholder' => 'Volume*']) !!}
										<span class="add-on unit1 manage">
											CFT
										</span>
									</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-truck"></i></span>
									{!!	Form::select('load_type',(['' => 'Load Type *'] +$load_types), '' ,['class' =>'selectpicker','id'=>'load_type']) !!}
								</div>
							</div>
							<div class="col-md-12 form-control-fld text-right margin-none">
								<span class="red spl-link advanced-search-link"><span class="more-search">+</span><span class="less-search">-</span> Advanced Search</span>
							</div>	
							
							<div class="advanced-search-details">
								<div class="col-md-4 form-control-fld margin-top">
									<div class="radio-block">
										<span class="padding-right-15">Origin Elevator</span> 
										<input type="radio" id="elevator1_a" name="elevator1" value="1" checked />
										<label for="elevator1_a"><span></span>Yes</label>
											
										<input type="radio" id="elevator1_b" name="elevator1" value="0" />
										<label for="elevator1_b"><span></span>No</label>
									</div>
								</div>
								<div class="col-md-4 form-control-fld margin-top">
									<div class="radio-block">
										<span class="padding-right-15">Destination Elevator</span> 
										<input type="radio" id="elevator2_a" name="elevator2" value="1" checked />
										<label for="elevator2_a"><span></span>Yes</label>
											
										<input type="radio" id="elevator2_b" name="elevator2" value="0" />
										<label for="elevator2_b"><span></span>No</label>
									</div>
								</div>
								<div class="clearfix"></div>
								<div class="col-md-4 form-control-fld">
									<div class="radio-block"><input type="checkbox" checked /> <span class="lbl padding-8" name="origin_storage_serivce" id="origin_storage_serivce">Storage</span></div>
									<div class="radio-block"><input type="checkbox" name="origin_handy_serivce" id="origin_handy_serivce"> <span class="lbl padding-8">Handyman Services</span></div>
									<div class="radio-block"><input type="checkbox" name="insurance_serivce" id="insurance_serivce"> <span class="lbl padding-8">Insurance</span></div>
									<div class="radio-block"><input type="checkbox" name="escort_serivce" id="escort_serivce"> <span class="lbl padding-8">Escort</span></div>
									<div class="radio-block"><input type="checkbox" name="mobilty_serivce" id="mobilty_serivce"> <span class="lbl padding-8">Mobility</span></div>
									<div class="radio-block"><input type="checkbox" name="property_serivce" id="property_serivce"> <span class="lbl padding-8">Property</span></div>
									<div class="radio-block"><input type="checkbox" name="setting_serivce" id="setting_serivce"> <span class="lbl padding-8">Setting Service</span></div>
									<div class="radio-block"><input type="checkbox" name="insurance_domestic" id="insurance_domestic"> <span class="lbl padding-8">Insurance Domestic</span></div>
								</div>
								<div class="col-md-4 form-control-fld">
									<div class="radio-block"><input type="checkbox" name="destination_storage_serivce" id="destination_storage_serivce"> <span class="lbl padding-8">Storage</span></div>
									<div class="radio-block"><input type="checkbox" name="destination_handy_serivce" id="destination_handy_serivce"> <span class="lbl padding-8">Handyman Services</span></div>
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
							
							<div class="relocation_vehicle_buyer_create" style="display:none;">
								<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-home"></i></span>
										{!!	Form::select('vehicle_category',(['' => 'Vehicle Category *'] +$vehicletypecategories), '' ,['class' =>'selectpicker','id'=>'vehicle_category','onchange'=>'return getVehicleTypes()']) !!}
									</div>								
								</div>
								
								<div class="col-md-3 form-control-fld vehicle_type_car">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-home"></i></span>
										{!!	Form::select('vehicle_category_type',(['' => 'Vehicle Category Type *'] +$vehicletypecategorietypes), '' ,['class' =>'selectpicker','id'=>'vehicle_category_type']) !!}
									</div>								
								</div>
								
								<div class="col-md-2 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-balance-scale"></i></span>
										{!! Form::text('vehicle_model', '', ['id' => 'vehicle_model','class' => 'form-control', 'placeholder' => 'Vehicle Model*']) !!}										
									</div>
								</div>							
							</div>
						
										
					</div>
					
				</div>
				<div class="col-md-4 col-md-offset-4">
						<input type="hidden" name="total_hidden_volume" id="total_hidden_volume" value="">
						<input  type="submit" class="btn theme-btn btn-block"  name="getquote" id="getquote" value="Search">
					</div>	
	{!! Form::close() !!}	

			</div>
			</div>
			<div class="clearfix"></div>
			
@include('partials.footer')
@endsection