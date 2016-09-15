{{--*/ $service_type=''; /*--}}
{{--*/ $measurement=''; /*--}}
@if(Session::has('searchMod.service_type_relocation'))
{{--*/ $service_type=Session::get('searchMod.service_type_relocation'); /*--}}
  {{--*/ $measurement=Session::get('searchMod.measurement_relocation'); /*--}}
@endif
{{--*/ $term='' /*--}}
{{--*/ $clsPrefix='' /*--}}
@if($is_term)
	{{--*/ $term='term_' /*--}}
	{{--*/ $clsPrefix='Term' /*--}}
@endif
<div class="col-md-12 inner-block-bg inner-block-bg1">

<div class="col-md-3 form-control-fld">
	<div class="input-prepend">
		<span class="add-on"><i class="fa fa-cog"></i></span>
	
		{!!	Form::select($term.'relgm_service_type',$lkp_relgm_services, $service_type ,['class' =>'selectpicker','id'=>$term.'relgm_service_type','onchange'=>'return getServiceTypeMeasurementUnit()']) !!}
	</div>
</div>

<div class="col-md-3 form-control-fld">
	<div class="input-prepend" id="{{$term}}measures_div">
	<input class="form-control form-control1 clsGMSNoOfDays{{$clsPrefix}}" id="{{$term}}measurement" name="{{$term}}measurement" type="text" value="{{$measurement}}" >
		<span class="add-on unit1 manage"><input type="text" name="{{$term}}measurement_unit" readonly="readonly" placeholder="Day(s)" value="Day(s)" id="{{$term}}measurement_unit" class="form-control form-control1 valid"></span>
	</div>
	<label for="{{$term}}measurement" id="err_{{$term}}measurement" class="error" style="display: none;"></label>
</div>	

<div class="col-md-3 form-control-fld">
	<input type="hidden" name ='{{$term}}service_slab_hidden_value' id='{{$term}}service_slab_hidden_value' value='0'>	
    {!! Form::hidden($term.'update_reloc_seller_line', '', array('id' => $term.'update_reloc_seller_line')) !!}
    {!! Form::hidden($term.'update_reloc_seller_row_count', '', array('id' => $term.'update_reloc_seller_row_count')) !!}
    {!! Form::hidden($term.'update_reloc_seller_row_unique', '', array('id' => $term.'update_reloc_seller_row_unique')) !!}

	<input type="button" class="btn add-btn service-box" value="Add">
</div>

<div class="clearfix"></div>

	<div class="table-div table-style1 ">
	
		<!-- Table Head Starts Here -->

		<div class="table-heading inner-block-bg">
			<div class="col-md-5 padding-left-none">Service</div>
			<div class="col-md-5 padding-left-none">Numbers</div>
			<div class="col-md-2 padding-left-none"></div>
		</div>

		<!-- Table Head Ends Here -->

		<div class="{{$term}}servicetable table-data">
			<!-- Table Row Starts Here -->
			<div class="{{$term}}servicedata"></div>
			<!-- Table Row Ends Here -->
		</div>
	
		<!-- Table Ends Here -->
	</div>
</div>