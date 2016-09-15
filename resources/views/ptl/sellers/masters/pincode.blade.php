@extends('app') @section('content')
<script src="{{ asset('/js/editableGrid/editablegrid.js') }}"></script>
<script src="{{ asset('/js/editableGrid/editPincode.js') }}"></script>
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
		
		
		  @if ($errors->has('pincode_upload'))
		  
		  <div class="flash">
			<p class="text-success col-sm-12 text-center flash-txt alert-danger">
			{!! $errors->first('pincode_upload') !!}</p>
		</div>
		
                       @endif
		
		<span class="pull-left"><h1 class="page-title">Add Pincode
		
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
				<span class="pull-right pincode-links-block">
				 {!! Form::open(['url' => 'pincodeupload','id'=>'pincode_form','enctype'=>'multipart/form-data','class'=>'form-inline margin-top']) !!}                                    
				
					 @if(Session::has('ptl_error_message') && Session::get('ptl_error_message')!='')
					<a  href="/downloaderrorstemplate" class="back-link1 pull-left" title="Please download CSV to know the error occured">Download CSV</a>
					 			@endif
		
					<a  href="/downloadtemplate" class="back-link1 pincode-link" id ='download-template'>Download Template</a>
					<a class=" insurance_file_name back-link1 cursor-pointer">
					{!! Form::file('pincode_upload',null,['class'=>'pincode-upload-btn cursor-pointer','id'=>"pincodeUpload"]) !!}Pincode Upload
                     <input type="hidden" class='cursor-pointer' name="in_file" value="2000000000"> 
                       </a>
                     
                   
					<a href="#" class="back-link1 show-addpin-link pincode-link">Add / Edit Pincode</a>
				
 				 {!! Form::close() !!} 
</span>
				<div class="clearfix"></div>
				
				<div class="col-md-12 padding-none">
					<div class="main-inner"> 
						
						
						<!-- Right Section Starts Here -->

						<div class="main-right">

						  <div class="gray-bg show-pincode-wrp">
{!! Form::open(['url' => 'ptlmasters/add_pincode','id'=>'ptl-add-pincode','enctype'=>'multipart/form-data','class'=>'form-group']) !!}                                    
						  
								<div class="col-md-12 padding-none filter">
									<div class="col-md-3 form-control-fld">
											<div class="input-prepend">
											{!! Form::text('ptl_pincode_id', '',['id' => 'ptl_pincode_id',
					'class'=>'form-control numericvalidation form-control1','maxlength'=>'6', 'placeholder' => 'Pincode *']) !!} {!!
					Form::hidden('ptlPincodeId', '', array('id' => 'ptlPincodeId')) !!}
											</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="normal-select">
			{!! Form::select('ptl_sector_id',array('' => 'Select
					Sector')+$sectorsList ,'',['class'=>'selectpicker',
					'id'=>'ptl_pincode_sector_id']) !!}
											</div>
										</div>
										<div class="col-md-3 form-control-fld">
											<div class="normal-select">
{!! Form::select('oda_pincode',array('' =>
					'ODA')+[1=>'Yes',0=>'No'] ,'',['class'=>'selectpicker',
					'id'=>'oda_pincode']) !!}
											</div>
										</div>
										<div class="displayNone">
										<div
					class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
					{!! Form::text('pincode_location', '',['id' => 'pincode_location',
					'class'=>'form-control', 'placeholder' => 'Location Name *']) !!}</div>


				<div class="clearfix"></div>
				<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
					{!! Form::text('pincode_city', '',['id' => 'pincode_city',
					'class'=>'form-control', 'placeholder' => 'City *']) !!}</div>
				<div
					class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
					{!! Form::text('postal_division', '',['id' => 'postal_division',
					'class'=>'form-control', 'placeholder' => 'Postal Division *']) !!}
				</div>
				<div
					class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
					{!! Form::text('pincode_district', '',['id' => 'pincode_district',
					'class'=>'form-control', 'placeholder' => 'District *']) !!}</div>
				<div
					class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
					{!! Form::text('pincode_state', '',['id' => 'pincode_state',
					'class'=>'form-control', 'placeholder' => 'State *']) !!}</div>
				<div class="clearfix"></div>
				<div class="col-md-3 col-sm-3 col-xs-12 padding-none form-group">
					{!! Form::text('lkp_zone_id', '',['id' => 'lkp_zone_id','readonly'
					=> 'readonly', 'diasbled' => 'diasbled', 'class'=>'form-control',
					'placeholder' => 'Zone *']) !!}</div>

				<div
					class="col-md-3 col-sm-3 col-xs-12 padding-right-none mobile-padding-none form-group">
					{!! Form::text('lkp_tier_id', '',['id' => 'lkp_tier_id','readonly'
					=> 'readonly','diasbled' => 'diasbled', 'class'=>'form-control',
					'placeholder' => 'Tier *']) !!}</div>
										
										</div>
										
										<div class="col-md-3 form-control-fld">
										
					{!! Form::submit(' Add / Edit', ['name' => 'confirm','class'=>'btn add-btn pull-right']) !!}
										</div>
									<div class="col-md-12 form-control-fld">
										<label><span id="auto_division_name">Postal Division</span>,&nbsp;&nbsp;<span id="auto_district_name">District</span>,&nbsp;&nbsp;<span id="auto_state_name">State</span></label>

									</div>

									</div>							
{!! Form::Close() !!}
</div>
</div>

						

					</div>
				</div>
				<div class="clearfix"></div>
								<div class="table-div">
								{!! $grid !!}
								</div>
			</div>
		</div>


		@include('partials.footer')
	</div>	  
<div class="modal fade" tabindex="-1" role="dialog" id="confirmDeleteBox">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
       
      </div>
      <div class="modal-body">
        <p>Are you sure, you want to delete this pincode?</p>
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
    <button type="button" class="btn post-btn flat-btn" data-dismiss="modal"> OK </button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div>

		@endsection
