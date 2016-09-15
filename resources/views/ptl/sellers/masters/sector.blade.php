@extends('app') @section('content')

<script src="{{ asset('/js/editableGrid/editablegrid.js') }}"></script>
<script src="{{ asset('/js/editableGrid/editSector.js') }}"></script>
@include('partials.page_top_navigation')

		<div class="main">

			<div class="container">
				@if(Session::has('ptl_success_message') &&
		Session::get('ptl_success_message')!='')
		<div class="flash">
			<p class="text-success col-sm-12 text-center flash-txt alert-success">
				{{ Session::get('ptl_success_message') }}</p>
		</div>
		@endif @if(Session::has('ptl_error_message') &&
		Session::get('ptl_error_message')!='')
		<div class="flash">
			<p class="text-success col-sm-12 text-center flash-txt alert-danger">
				{{ Session::get('ptl_error_message') }}</p>
		</div>
		@endif
				<span class="pull-left"><h1 class="page-title">Add Sector
				@if(Session::get('service_id') == ROAD_PTL)
				(LTL)
				@elseif(Session::get('service_id') == RAIL)
				(RAIL)
				@elseif(Session::get('service_id') == AIR_DOMESTIC)
				(AIR DOMESTIC)
				@elseif(Session::get('service_id') == AIR_INTERNATIONAL)
				(AIR INTERNATIONAL)
				@elseif(Session::get('service_id') == COURIER)
				(COURIER)
				@elseif(Session::get('service_id') == OCEAN)
				(OCEAN)
				@endif
				</h1> </span>



				<div class="clearfix"></div>

				<div class="col-md-12 padding-none">
					<div class="main-inner">


						<!-- Right Section Starts Here -->

						<div class="main-right">

							<div class="gray-bg">
								{!! Form::open(array('url' => 'ptlmasters/sector', 'id' =>
				'ptl-add-sector', 'class'=>'form-group','enctype' =>
				'multipart/form-data' )) !!}
				<div class="col-md-12 padding-none filter">
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
			{!! Form::text('sector_name', '',['id' => 'sector_name', 'placeholder'=> 'Sector Name','class'=>'form-control alphanumericspace_strVal form-control1','maxlength'=>'10']) !!}
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
			{!! Form::text('sector_code', '',['id' => 'sector_code','placeholder' => 'Sector Code','class'=>'form-control alphanumeric_strVal form-control1','maxlength'=>'10'])
					!!}
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="normal-select">
											{!! Form::select('zone_id',array('' => 'Select Zone')+$zonesList,'',['class'=>'selectpicker', 'id'=>'zone_id']) !!}
										</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="normal-select">
											{!! Form::select('tier_id',array('' => 'Select Tier')+$tiersList,'',['class'=>'selectpicker', 'id'=>'tier_id']) !!}
										</div>
									</div>
									<div class="col-md-12 form-control-fld">
										{!! Form::submit('ADD', ['name' => 'confirm','class'=>'btn add-btn pull-right']) !!}
									
									</div>
								</div>
								{!! Form::Close() !!}

							</div>


							<div class="page-results pull-left col-md-2 padding-none results-full">
								<div class="form-control-fld">
									<div class="normal-select">
										<select class="selectpicker">
											<option value="0">10 Records Per page</option>
										</select>
									</div>
								</div>
							</div>

							<!-- Table Starts Here -->

							<div class="table-div">

<!-- 								<div id="table-data" class="margin-top"></div> -->
							{!! $grid !!}
							</div>

							<!-- Table Starts Here -->

						</div>

						<!-- Right Section Ends Here -->

					</div>
				</div>

				<div class="clearfix"></div>

			</div>
		</div>
	<!-- Modal -->

	@include('partials.footer')
</div>

<div class="modal fade" tabindex="-1" role="dialog" id="editSectorModal">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title table-heading">Update Sector Details</h4>
      </div>
      
      <div class="modal-body">
   <div class="col-md-12 padding-none filter">
   
	<div class="col-md-6 form-control-fld padding-left-none">
			<div class="input-prepend">
			<input type="text" name="sectorName" id="sectorName" placeholder="Sector Name" maxlength="10" class="form-control alphanumericspace_strVal form-control1">
			<span id="sectorName_error" class="red error"></span>
			</div>
		</div>
		 <div class="col-md-6 form-control-fld padding-right-none">
			<div class="input-prepend">
			<input type="text" name="sectorCode" id="sectorCode" placeholder="Sector Code" maxlength="10" class="form-control alphanumeric_strVal form-control1">
			<span id="sectorCode_error" class="red error"></span>
			</div>
			<input type="hidden" class="displayNone" id="hiddenSectorId">
		</div>
		<div class="clearfix"></div>
		<div class="col-md-6 form-control-fld padding-left-none">
			<div class="normal-select">
			<!--input type="text" title="zone name" id="zoneName" readonly="readonly" placeholder="Zone Name" class="form-control form-control1 cursor-not-allowed " -->
                        {!! Form::select('zone_id',$zonesList,'',['class'=>'selectpicker', 'id'=>'zoneName']) !!}
			</div>
		</div>
		<div class="col-md-6 form-control-fld padding-right-none">
			<div class="normal-select">
			<!--input type="text" title="tier name"  id="tierName" readonly="readonly" placeholder="Tier Name" class="form-control form-control1 cursor-not-allowed " -->
			{!! Form::select('tier_id',$tiersList,'',['class'=>'selectpicker', 'id'=>'tierName']) !!}
			</div>
		</div>
	</div>
      </div>
      <div class="modal-footer">
        <input type="button" class="btn add-btn pull-right" id="updateSectorButton" value="Update">
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
<!-- Confirm delete box-->
<div class="modal fade" tabindex="-1" role="dialog" id="confirmDeleteBox">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
       
      </div>
      <div class="modal-body">
        <p>Are you sure, you want to delete this Sector?</p>
      </div>
      <div class="modal-footer">
		<button type="button" class="btn add-btn flat-btn" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn red-btn flat-btn" data-dismiss="modal" id="trueDelete">Delete</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

<!--  DELETE SUCCESS -->
<div class="modal fade" tabindex="-1" role="dialog" id="successDeleteBox">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      
      </div>
      <div class="modal-body">
        <p id="displayMessage">&nbsp;</p>
      </div>
      <div class="modal-footer">
    <button type="button" class="btn add-btn flat-btn" data-dismiss="modal"> OK </button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
	@endsection