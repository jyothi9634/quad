{!! Form::hidden('crating_items', '1', array('id' => 'crating_items')) !!}
<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-map-marker"></i></span>
		{!! Form::text('from_location_intre',  request('from_location_intre'), ['id' => 'from_location_intre','class' => 'form-control', 'placeholder' => 'From Location (Only Major Cities) *']) !!}
        {!! Form::hidden('from_location_id_intre', request('from_location_id_intre'), array('id' => 'from_location_id_intre')) !!}
        {!! Form::hidden('seller_district_id_intre', '', array('id' => 'seller_district_id_intre')) !!}
	</div>
</div>

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-map-marker"></i></span>
		{!! Form::text('to_location_intre',request('to_location_intre'), ['id' => 'to_location_intre','class' => 'form-control', 'placeholder' => 'To Location (Only Major Cities) *']) !!}
        {!! Form::hidden('to_location_id_intre', request('to_location_id_intre'), array('id' => 'to_location_id_intre')) !!}
	</div>
</div>

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-calendar-o"></i></span>
		{!! Form::text('valid_from', request('valid_from'), ['id' => 'ptlDispatchDate','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date*']) !!}
        <input type="hidden" name="dispatch_flexible_hidden_relocint" id="ptlFlexiableDispatch_hidden" value="0">
	</div>
</div>

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-calendar-o"></i></span>
		{!! Form::text('valid_to', request('valid_to'), ['id' => 'ptlDeliveryhDate','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date']) !!}
        <input type="hidden" name="delivery_flexible_hidden_relocint" id="delivery_flexible_hidden_relocint" value="0">
	</div>
</div>

<div class="col-md-4 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-home"></i></span>
		{!!	Form::select('property_type',(['' => 'Property Type *'] +$property_types), request('property_type') ,['class' =>'selectpicker','id'=>'property_type']) !!}
	</div>
</div>

<div class="clearfix"></div>

@if(request('chkOrgServ') == 1)
{{-- */  $chkOrgServ='checked';  /* --}}
@else
{{-- */ $chkOrgServ=''; /* --}}
@endif

@if(request('origin_handy_serivce') == 1)
{{-- */  $origin_handy_serivce='checked';  /* --}}
@else
{{-- */ $origin_handy_serivce=''; /* --}}
@endif

@if(request('insurance_serivce') == 1)
{{-- */  $insurance_serivce='checked';  /* --}}
@else
{{-- */ $insurance_serivce=''; /* --}}
@endif

@if(request('destination_storage_serivce') == 1)
{{-- */  $destination_storage_serivce='checked';  /* --}}
@else
{{-- */ $destination_storage_serivce=''; /* --}}
@endif

@if(request('destination_handy_serivce') == 1)
{{-- */  $destination_handy_serivce='checked';  /* --}}
@else
{{-- */ $destination_handy_serivce=''; /* --}}
@endif
	<div class="advanced-search-details">
			
		<div class="col-md-4 form-control-fld">
			<div class="radio-block"><input type="checkbox" {{ $chkOrgServ }}  name="chkOrgServ" value="1"/> <span class="lbl padding-8" name="origin_storage_serivce" id="origin_storage_serivce">Storage</span></div>
			<div class="radio-block"><input type="checkbox"  {{ $origin_handy_serivce }} value="1" name="origin_handy_serivce" id="origin_handy_serivce"> <span class="lbl padding-8">Handyman Services</span></div>
			<div class="radio-block"><input type="checkbox"   {{ $insurance_serivce }} value="1" name="insurance_serivce" id="insurance_serivce"> <span class="lbl padding-8">Insurance</span></div>
		</div>
		<div class="col-md-4 form-control-fld">
			<div class="radio-block"><input type="checkbox"  {{ $destination_storage_serivce }} value="1" name="destination_storage_serivce" id="destination_storage_serivce"> <span class="lbl padding-8">Storage</span></div>
			<div class="radio-block"><input type="checkbox"  value="1" name="destination_handy_serivce" id="destination_handy_serivce"> <span class="lbl padding-8">Handyman Services</span></div>
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
				<input type="button" class="btn add-btn pull-right save-continue-rel-intocean-search" name="savecontinue" id="savecontinue" value="Save & Continue">
			</div>

			<div class="clearfix"></div>

			<div class="after-inventory-block margin-top">
				<div class="table-div table-style1">
                    <div name="inventory_count_div" id="inventory_count_div"></div>
                </div>
			</div>

		</div>

</div>

<div class="col-md-12 form-control-fld text-right margin-none">
	<span class="red spl-link advanced-search-link"><span class="more-search">+</span><span class="less-search">-</span> Advanced Search</span>
</div>		

<div class="col-md-4 col-md-offset-4">
	<input type="hidden" name="total_hidden_volume" id="total_hidden_volume" value="">
	<button class="btn theme-btn btn-block" type="submit">Search</button>
</div>