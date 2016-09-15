@extends('app') @section('content')
@include('partials.page_top_navigation')
<script src="{{ asset('/js/editableGrid/editablegrid.js') }}"></script>
<script src="{{ asset('/js/editableGrid/editTier.js') }}"></script>
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
		<span class="pull-left"><h1 class="page-title">Add Tier
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

				<div class="container padding-none">
					<div class="main-inner">


						<!-- Right Section Starts Here -->

						<div class="main-right">

							<div class="gray-bg">
								{!! Form::open(array('url' => 'ptlmasters/tier', 'id' =>'ptl-add-tier', 'class'=>'form-group','enctype' =>'multipart/form-data' )) !!}
								
								<div class="col-md-12 padding-none filter">
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
										{!!
					Form::text('tier_name', '',['id' => 'tier_name',
					'class'=>'form-control alphanumericonly_strVal form-control1','maxlength'=>'10','placeholder' => 'Tier Name'])
					!!}
					
					</div>
									</div>
									<div class="col-md-3 form-control-fld">
										<div class="input-prepend">
			{!! Form::text('tier_code', '',['id' => 'tier_code', 'placeholder'
					=> 'Tier Code','maxlength'=>'10','class'=>'form-control alphanumeric_strVal form-control1']) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										{!! Form::submit('ADD', ['name' => 'confirm','class'=>'btn add-btn pull-right']) !!}
					
									</div>
								</div>
{!! Form::Close() !!}
							</div>


							<div
								class="page-results pull-left col-md-2 padding-none results-full">
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

								<!-- <div id="table-data" class=""></div> -->
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
	</div>


	<!-- Right Starts Here -->
	@include('partials.footer')
	<!-- Right Ends Here -->
	</div>

	
<div class="modal fade" tabindex="-1" role="dialog" id="editTierModal">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title table-heading">Update Tier Details</h4>
      </div>
      <form id="updateTierForm">
      <div class="modal-body">
   <div class="col-md-12 padding-none filter">
									<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
											<input type="text" name="tierName" id="tierName" maxlength="10" placeholder="Tier Name" class="form-control alphanumericonly_strVal form-control1">
											<span id="tierName_error" class="red error"></span>
											</div>
										</div>
										 <div class="col-md-6 form-control-fld">
											<div class="input-prepend">
											<input type="text" name="tierCode" id="tierCode" maxlength="10" placeholder="Tier Code" class="form-control alphanumeric_strVal form-control1">
											<span id="tierCode_error" class="red error"></span>
											</div>
											<input type="hidden" class="displayNone" id="hiddenTierId">
										</div>
										
									</div>
      </div>
      <div class="modal-footer">
        <input type="button" class="btn add-btn pull-right" id="updateTierButton" value="Update">
      </div></form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>
	
	<!-- Confirm Box -->
<div class="modal fade" tabindex="-1" role="dialog" id="confirmDeleteBox">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        
      </div>
      <div class="modal-body">
        <p>Are you sure, you want to delete this Tier?</p>
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