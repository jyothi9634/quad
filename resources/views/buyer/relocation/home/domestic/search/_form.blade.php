{!! Form::hidden('household_items', (request('household_items'))?request('household_items'):1, array('id' => 'household_items')) !!}
{!! Form::hidden('crating_items', (request('crating_items'))?request('crating_items'):0, array('id' => 'crating_items')) !!}
{!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
{!!	Form::hidden('total_hidden_volume',request('total_hidden_volume'),array('class'=>'form-control','id'=>'total_hidden_volume')) !!}

<div class="col-md-12 form-control-fld">
	<div class="radio-block">
		<input type="radio" id="post_rate_card_type_1" name="post_rate_card_type" class="ratetype_selection_buyer" value="1" {{((request('post_rate_card_type')=='1') || !request('post_rate_card_type'))?'checked="checked"':''}} />
		<label for="post_rate_card_type_1"><span></span>House Hold</label>
			
		<input type="radio" id="post_rate_card_type_2" name="post_rate_card_type" class="ratetype_selection_buyer" value="2" {{((request('post_rate_card_type')=='2'))?'checked="checked"':''}}>
		<label for="post_rate_card_type_2"><span></span>Vehicle</label>
	</div>
</div>
<div class="col-md-12 padding-none">
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-map-marker"></i></span>
					{!! Form::text('from_location', request('from_location') , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From Location*']) !!}
	                {!! Form::hidden('from_location_id', request('from_location_id') , array('id' => 'from_location_id')) !!}
				</div>
			</div>
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-map-marker"></i></span>
					{!! Form::text('to_location', request('to_location'),  ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To Location*']) !!}
	               	{!! Form::hidden('to_location_id', request('to_location_id') , array('id' => 'to_location_id')) !!}
				</div>
			</div>

			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-calendar-o"></i></span>
					{!! Form::text('from_date', request('from_date'),  ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date*']) !!}
					<input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="<?php echo isset($_REQUEST['dispatch_flexible_hidden']) ? $_REQUEST['dispatch_flexible_hidden'] : ""; ?>">
				</div>
			</div>
			<div class="col-md-4 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-calendar-o"></i></span>
					{!! Form::text('to_date', request('to_date') , ['id' => 'delivery_date1','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date']) !!}
					<input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="<?php echo isset($_REQUEST['delivery_flexible_hidden']) ? $_REQUEST['delivery_flexible_hidden'] : ""; ?>">
				</div>
			</div>
			@if(request('household_items') == 2)
				<div class="relocation_house_hold_buyer_create" style="display:none;">
			@else
				<div class="relocation_house_hold_buyer_create">
			@endif
			<div class="col-md-3 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-home"></i></span>
					{!!	Form::select('property_type',(['' => 'Property Type *'] +$property_types), request('property_type') ,['class' =>'selectpicker','id'=>'property_type','onchange'=>'return getPropertyCft()']) !!}
				</div>
			</div>
			<div class="col-md-2 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-balance-scale"></i></span>
						{!! Form::text('volume', request('volume'), ['id' => 'volume','class' => 'form-control','readonly' => true, 'placeholder' => 'Volume*']) !!}
						<span class="add-on unit1 manage">
							CFT
						</span>
					</div>
			</div>
			<div class="col-md-3 form-control-fld">
				<div class="input-prepend">
					<span class="add-on"><i class="fa fa-truck"></i></span>
					{!!	Form::select('load_type',(['' => 'Load Type *'] +$load_types), request('load_type') ,['class' =>'selectpicker','id'=>'load_type']) !!}
				</div>
			</div>
			<div class="col-md-12 form-control-fld text-right margin-none">
				<span class="red spl-link advanced-search-link"><span class="more-search">+</span><span class="less-search">-</span> Advanced Search</span>
			</div>	
			<div class="advanced-search-details">
				<div class="col-md-4 form-control-fld margin-top">
					<div class="radio-block">
					
						<span class="padding-right-15">Origin Elevator</span> 
						<input type="radio" id="elevator1_a" name="elevator1" value="1" {{((request('elevator1')==1) || !request('elevator1'))?'checked="checked"':''}}>
						<label for="elevator1_a"><span></span>Yes</label>
							
						<input type="radio" id="elevator1_b" name="elevator1" value="0" {{((request('elevator1')==0))?'checked="checked"':''}}>
						<label for="elevator1_b"><span></span>No</label>
					</div>
				</div>
				<div class="col-md-4 form-control-fld margin-top">
					<div class="radio-block">
						<span class="padding-right-15">Destination Elevator</span> 
						<input type="radio" id="elevator2_a" name="elevator2" value="1" {{((request('elevator2')==1) || !request('elevator2'))?'checked="checked"':''}}>
						<label for="elevator2_a"><span></span>Yes</label>
							
						<input type="radio" id="elevator2_b" name="elevator2" value="0" {{((request('elevator2')==0))?'checked="checked"':''}}>
						<label for="elevator2_b"><span></span>No</label>
					</div>
				</div>
				<div class="clearfix"></div>
				<div class="col-md-4 form-control-fld">
					<div class="radio-block"><input type="checkbox" name="origin_storage_serivce" id="origin_storage_serivce" {{(request('origin_storage_serivce'))?"checked":""}}> <span class="lbl padding-8">Storage</span></div>
					<div class="radio-block"><input type="checkbox" name="origin_handy_serivce" id="origin_handy_serivce" {{(request('origin_handy_serivce'))?"checked":""}}> <span class="lbl padding-8">Handyman Services</span></div>
					<div class="radio-block"><input type="checkbox" name="insurance_serivce" id="insurance_serivce" {{(request('insurance_serivce'))?"checked":""}}> <span class="lbl padding-8">Insurance</span></div>
					<div class="radio-block"><input type="checkbox" name="escort_serivce" id="escort_serivce" {{(request('escort_serivce'))?"checked":""}}> <span class="lbl padding-8">Escort</span></div>
					<div class="radio-block"><input type="checkbox" name="mobilty_serivce" id="mobilty_serivce" {{(request('mobilty_serivce'))?"checked":""}}> <span class="lbl padding-8">Mobility</span></div>
					<div class="radio-block"><input type="checkbox" name="property_serivce" id="property_serivce" {{(request('property_serivce'))?"checked":""}}> <span class="lbl padding-8">Property</span></div>
					<div class="radio-block"><input type="checkbox" name="setting_serivce" id="setting_serivce" {{(request('setting_serivce'))?"checked":""}}> <span class="lbl padding-8">Setting Service</span></div>
					<div class="radio-block"><input type="checkbox" name="insurance_domestic" id="insurance_domestic" {{(request('insurance_domestic'))?"checked":""}}> <span class="lbl padding-8">Insurance Domestic</span></div>
				</div>
				<div class="col-md-4 form-control-fld">
					<div class="radio-block"><input type="checkbox" name="destination_storage_serivce" id="destination_storage_serivce" {{(request('destination_storage_serivce'))?"checked":""}}> <span class="lbl padding-8">Storage</span></div>
					<div class="radio-block"><input type="checkbox" name="destination_handy_serivce" id="destination_handy_serivce" {{(request('destination_handy_serivce'))?"checked":""}}> <span class="lbl padding-8">Handyman Services</span></div>
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
			@if(request('household_items') == 2)
				<div class="relocation_vehicle_buyer_create" style="display:block;">
			@else
				<div class="relocation_vehicle_buyer_create" style="display:none;">
			@endif
				<div class="col-md-3 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-home"></i></span>
						{!!	Form::select('vehicle_category',(['' => 'Vehicle Category *'] +$vehicletypecategories), request('vehicle_category') ,['class' =>'selectpicker','id'=>'vehicle_category','onchange'=>'return getVehicleTypes()']) !!}
					</div>								
				</div>
				<div class="col-md-3 form-control-fld vehicle_type_car" style="display:{{(request('vehicle_category') == 1) ? 'block' : 'none'}}">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-home"></i></span>
						{!!	Form::select('vehicle_category_type',(['' => 'Vehicle Category Type *'] +$vehicletypecategorietypes), request('vehicle_category_type') ,['class' =>'selectpicker','id'=>'vehicle_category_type']) !!}
					</div>								
				</div>
				<div class="col-md-2 form-control-fld">
					<div class="input-prepend">
						<span class="add-on"><i class="fa fa-balance-scale"></i></span>
						{!! Form::text('vehicle_model', request('vehicle_model'), ['id' => 'vehicle_model','class' => 'form-control', 'placeholder' => 'Vehicle Model*']) !!}										
					</div>
				</div>							
			</div>
			
						
	</div>
</div>
