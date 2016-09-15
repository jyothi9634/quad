@extends('app') @section('content')
@include('partials.page_top_navigation') @if (Session::has('message') &&
Session::get('message')!='')
<div class="flash">
	<p class="text-success col-sm-12 text-center flash-txt alert-info">{{
		Session::get('message') }}</p>
</div>
@endif


<div class="main-container">
<div class="login-head heading-margin-top">
			<h1 class="margin-top margin-bottom-none">
				<span>LOGISTIKS.COM</span>
				<p>Vehicle Registration</p>
			</h1>
		</div>
<div class="main">
	<div class="container reg_crumb">

		
		<div class="home-block home-block-login">
			<div class="tabs">

				<div class="tab-content">
					<div id="buyer" class="tab-pane fade in active">
						<div class="login-block">
							<div class="login-form login-form-2">
								<div class="center-width">


									{!! Form::open(['url' =>
									'vehicleregister','id'=>'vehicle-master-form','enctype'=>'multipart/form-data','class'=>'form-inline
									margin-top']) !!}


									<div class="col-md-12 form-control-fld">

										<div class=" col-md-12 form-control-fld">{!!
											Form::label('vehicle_owned', 'I Transporter / Vehicle Owner',
											array('class' => '')) !!}</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('vehicle_owned',null,['class'=>'form-control
											form-control1 numericvalidation', 'placeholder'=>'No. Vehicles Owned']) !!}</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('vehicle_attatched',null,['class'=>'form-control
											form-control1 numericvalidation','placeholder'=>'No. Vehicles Attached']) !!}</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('vehicle_gps',null,['class'=>'form-control
											form-control1 numericvalidation', 'placeholder'=>'No. Vehicles with GPS']) !!}</div>
									</div>
									<div class="clearform"></div>
									<h4>Vehicle form</h4>

									<div class="col-sm-6 form-control-fld">
										{!!	Form::text('vehicle_number',null, ['class'=>'form-control form-control1 clsVehicleno', 'placeholder'=>'Vehicle Number *']) !!}
									@if ($errors->has('vehicle_number'))
										<p style="color: red;">{!! $errors->first('vehicle_number') !!}</p>
										@endif
									</div>
									<div class="col-sm-6 form-control-fld">
										<div class="normal-select">
										{!! Form::select('vehicle_type',array('' => 'Select Vehicle Type*') + $vehicle,null, ['class'=>'selectpicker']) !!}</div>
										@if ($errors->has('vehicle_type'))
										<p style="color: red;">{!! $errors->first('vehicle_type') !!}</p>
										@endif
									</div>
									<div class="col-md-12 form-control-fld">
										<div class=" col-md-12 form-control-fld">
										{!! Form::label('vehicle_dimension', 'Dimension', array('class' => '')) !!}</div>
										
										<div class="col-md-4 form-control-fld">
											{!! Form::text('vehicle_length',null,['class'=>'form-control
											form-control1 clsLTL4LengthCM','placeholder'=>' L *']) !!} 
											@if($errors->has('vehicle_length'))
											<p style="color: red;">{!! $errors->first('vehicle_length') !!}</p>
											@endif
										</div>
										
										<div class="col-md-4 form-control-fld">
											{!! Form::text('vehicle_width',null,['class'=>'form-control
											form-control1 clsLTL4LengthCM','placeholder'=>' W *']) !!} 
											@if ($errors->has('vehicle_width'))
											<p style="color: red;">{!! $errors->first('vehicle_width') !!}</p>
											@endif
										</div>
										
										<div class="col-md-4 form-control-fld">
											{!! Form::text('vehicle_height',null,['class'=>'form-control
											form-control1 clsLTL4LengthCM','placeholder'=>' H *']) !!} 
											@if($errors->has('vehicle_height'))
											<p style="color: red;">{!! $errors->first('vehicle_height') !!}</p>
											@endif
										</div>
									</div>


									<div class="col-md-12 form-control-fld">
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('vehicle_capacity',array('' => 'Select Vehicle
												Capacity *') + $vehiclecapacities,null,['class'=>'selectpicker']) !!}</div>
											@if ($errors->has('vehicle_capacity'))
											<p style="color: red;">{!! $errors->first('vehicle_capacity') !!}</p>
											@endif

										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('load_type',array('' => 'Select Load Type') +
												$load_type,null,['class'=>'selectpicker']) !!}</div>
										</div>
									</div>

									<div class="col-md-12 form-control-fld">
										<div class=" col-md-12 form-control-fld">{!!
											Form::label('reg_owner_fname', 'Registered Owner',
											array('class' => '')) !!}</div>
										<div class="col-md-6 form-control-fld">
											{!! Form::text('reg_owner_fname',null,['class'=>'form-control
											form-control1 clsAlphaSpace','placeholder'=>'First Name *']) !!} 
											@if($errors->has('reg_owner_fname'))
											<p style="color: red;">{!! $errors->first('reg_owner_fname') !!}</p>
											@endif
										</div>
										<div class="col-md-6 form-control-fld">
											{!! Form::text('reg_owner_lname',null,['class'=>'form-control
											form-control1 clsAlphaSpace','placeholder'=>'Last Name *']) !!}
											 @if($errors->has('reg_owner_lname'))
											<p style="color: red;">{!! $errors->first('reg_owner_lname') !!}</p>
											@endif
										</div>
										<div class="col-md-6 form-control-fld">
											{!! Form::text('mfg_year',null,['class'=>'form-control
											form-control1 numericvalidation','placeholder'=>'Year of Manufacture *','maxlength'=>4]) !!}
											@if($errors->has('mfg_year'))
											<p style="color: red;">{!! $errors->first('mfg_year') !!}</p>
											@endif

										</div>
										<div class="col-md-6 form-control-fld">
											{!! Form::text('chasis_number',null,['class'=>'form-control
											form-control1 clsChassisNumber', 'placeholder'=>'Chassis Number *','id'=>'chasis_number']) !!}
											@if($errors->has('chasis_number'))
											<p style="color: red;" >{!! $errors->first('chasis_number') !!}</p>
											@endif
                                                                                        <p style="color: red;" id="error_chasis_number"></p>
										</div>
                                                                            <div class='clearfix'></div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('engine_number',null,['class'=>'form-control
											form-control1 clsEngineNumber', 'placeholder'=>'Engine Number','id'=>'engine_number']) !!}
                                                                                        <p style="color: red;" id="error_engine_number"></p>
                                        </div>
                                        <div class="col-md-6 form-control-fld">{!!
											Form::text('company_name',null,['class'=>'form-control
											form-control1 clsCompanyName', 'placeholder'=>'Company Name','id'=>'company_name']) !!}
                                        <p style="color: red;" id="error_engine_number"></p>
                                		</div>
                                		<div class="col-md-6 form-control-fld">{!!
											Form::text('street_name',null,['class'=>'form-control
											form-control1 clsStreetName', 'placeholder'=>'Street/Door','id'=>'street_name']) !!}
                                        <p style="color: red;" id="error_engine_number"></p>
                                		</div>
                                		<div class="col-md-6 form-control-fld">{!!
											Form::text('city_name',null,['class'=>'form-control
											form-control1 clsCityName', 'placeholder'=>'City','id'=>'city_name']) !!}
                                        <p style="color: red;" id="error_engine_number"></p>
                                		</div>
                                		<div class="col-md-6 form-control-fld">{!!
											Form::text('pincode',null,['class'=>'form-control
											form-control1 clsPincode', 'placeholder'=>'Pincode','id'=>'pincode']) !!}
                                        <p style="color: red;" id="error_engine_number"></p>
                                		</div>
                                		<div class="col-md-6 form-control-fld">{!!
											Form::text('district_name',null,['class'=>'form-control
											form-control1 clsDistrict', 'placeholder'=>'District','id'=>'district_name']) !!}
                                        <p style="color: red;" id="error_engine_number"></p>
                                		</div>
                                		<div class="col-md-6 form-control-fld">{!!
											Form::text('state_name',null,['class'=>'form-control
											form-control1 clsState', 'placeholder'=>'State','id'=>'state_name']) !!}
                                        <p style="color: red;" id="error_engine_number"></p>
                                		</div>
                                		<div class="col-md-6 form-control-fld">{!!
											Form::text('email_id',null,['class'=>'form-control
											form-control1 clsEMailID', 'placeholder'=>'EMail ID','id'=>'E Mail ID']) !!}
                                        <p style="color: red;" id="error_engine_number"></p>
                                		</div>
                                		<div class="col-md-6 form-control-fld">{!!
											Form::text('mobile_number',null,['class'=>'form-control
											form-control1 clsMobileNumber', 'placeholder'=>'Mobile number','id'=>'mobile_number']) !!}
                                        <p style="color: red;" id="error_engine_number"></p>
                                		</div>
                                		<div class="col-md-6 form-control-fld">{!!
											Form::text('landline_number',null,['class'=>'form-control
											form-control1 clsLandlineNumber', 'placeholder'=>'Landline','id'=>'landline_number']) !!}
                                        <p style="color: red;" id="error_engine_number"></p>
                                		</div>
									</div>

									<div class="col-md-12 form-control-fld">
										<div class="padding-top-8">
										<span class="pull-left margin-right-20">
										{!!
											Form::label('is_gps', 'GPS Available', array('class' => ''))
											!!}
										</span>

											<div class="pull-left">
												<div class="radio_inline">
													<input type="radio" name="is_gps" id="spot_lead_type"
														value="1" /> <label for="spot_lead_type"><span></span>Yes</label>
												</div>
												<div class="radio_inline">
													<input type="radio" name="is_gps" id="term_lead_radio"
														value="0" checked /> <label for="term_lead_radio"><span></span>No</label>
												</div>
											</div>
										</div>

									</div>

									<div class="col-md-12 form-control-fld margin-none padding-none displayNone" id="GPSFields">
										<div class="col-md-6 form-control-fld">	
											{!!Form::text('device_number',null,['class'=>'form-control
											form-control1 numericvalidation', 'placeholder'=>'Device Number *']) !!}											
										</div>
										<div class="col-md-6 form-control-fld">	
											{!!Form::text('sim_imsi_number',null,['class'=>'form-control
											form-control1 alphanumeric_strVal', 'placeholder'=>'Sim IMSI Number *']) !!}											
										</div>
										<div class="col-md-6 form-control-fld">	
											{!!Form::text('mobile_operator',null,['class'=>'form-control
											form-control1 alphaonly_strVal', 'placeholder'=>'Mobile Operator *']) !!}											
										</div>
										<div class="col-md-6 form-control-fld">	
											{!!Form::text('mobile_number',null,['class'=>'form-control
											form-control1 clsMobile', 'placeholder'=>'Mobile Number *']) !!}											
										</div>
										<div class="col-md-6 form-control-fld">
												<div class="input-prepend">
													<span class="add-on"><i class="fa fa-calendar-o"></i></span>
													{!!
													Form::text('device_fixed_date',null,['class'=>'form-control','placeholder'
													=> 'Date of device fixed in vehicle *']) !!}
												</div>
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="">
										<span class="pull-left margin-right-20 padding-top-8">
										{!!
											Form::label('is_insured', 'Insured', array('class' => '
											col-md-6 form-control-fld')) !!}
											<!--label> 
                                <?php echo Form::radio('is_insured', '1'); ?><span class="lbl padding-8"></span>  Yes
                            </label> 
                            <label> 
                                <?php echo Form::radio('is_insured', '0'); ?><span class="lbl padding-8"></span>  No
                            </label-->
											</span>

											<div class="pull-left padding-top-8">
												<div class="radio_inline">
													<input type="radio" name="is_insured" id="is_insured_yes"
														value="1" checked /> <label for="is_insured_yes"><span></span>Yes</label>
												</div>
												<div class="radio_inline">
													<input type="radio" name="is_insured" id="is_insured_no"
														value="0" /> <label for="is_insured_no"><span></span>No</label>
												</div>
											</div>
										
										<!--div class=" col-md-12 form-control-fld">
                            {!! Form::label('insurance_validity', 'Insurance Validity', array('class' => '')) !!}
                        </div-->
                                                                                <div class="clearfix"></div>
											<div class="col-md-12 form-control-fld padding-none">
                                                                                            <div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!!
												Form::text('insurance_validity',null,['class'=>'form-control','placeholder'
												=> 'Insurance Validity']) !!}
											</div>
                                                                                        </div>    

										</div>
									</div>

									<div class="clearfix"></div>

									<div class="col-md-6 form-control-fld">
										<!--div class=" col-md-12 form-control-fld">
                            {!! Form::label('permit_type', 'Permit Type', array('class' => '')) !!}
                        </div-->
										
                                                                        <div class="normal-select">                                                                        
                                                                        
                                                                        {!! Form::select('permit_type',array('' => 'Select permit Type ') + $permittypes,null, ['class'=>'selectpicker']) !!}
                                                                        
                                                                        </div>

										
									</div>

									<div class="col-md-6 form-control-fld">
										<!--div class=" col-md-12 form-control-fld">
                            {!! Form::label('fc_validity', 'Fc Validity', array('class' => '')) !!}
                        </div-->
										<div class="col-md-12 form-control-fld">
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!!
												Form::text('fc_validity',null,['class'=>'form-control','placeholder'
												=> 'FC Validity']) !!}
											</div>
										</div>

									</div>


									<div class="col-md-12 form-control-fld">
										<label class=" col-md-12 form-control-fld">Upload Self
											Attested Documents</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span
													class="btn add-default btn-file btn-upload insurance_file_name">
													Insurance<input type="hidden" name="in_file"
													value="2000000000"> {!!
													Form::file('insurance_file_name',null,['class'=>'','id'=>"insurance_file_name"])
													!!}


												</span> @if ($errors->has('insurance_file_name'))
												<p style="color: red;">{!!$errors->first('insurance_file_name')!!}</p>
												@endif
											</div>
											<div class="col-xs-12">
												<p class="form-group pull-left overflow-hide"
													id="insurance_file_name"></p>
											</div>
										</div>


										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span
													class="btn btn-default btn-file btn-upload permit_copy_file_name">
													Permit Copy<input type="hidden" name="pc_file"
													value="2000000000"> {!!
													Form::file('permit_copy_file_name',null,['class'=>'','id'=>"permit_copy_file_name"])
													!!}

												</span>
											</div>
											<div class="col-xs-12">
												<p class="form-group pull-left overflow-hide"
													id="permit_copy_file_name"></p>
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span
													class="btn btn-default btn-file btn-upload fc_file_name">
													FC<input type="hidden" name="fc_file" value="2000000000">

													{!!
													Form::file('fc_file_name',null,['class'=>'','id'=>"fc_file_name"])
													!!}

												</span>
											</div>
											<div class="col-xs-12">
												<p class="form-group pull-left overflow-hide"
													id="fc_file_name"></p>
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span
													class="btn btn-default btn-file btn-upload rc_file_name">
													RC<input type="hidden" name="rc_file" value="2000000000">

													{!!
													Form::file('rc_file_name',null,['class'=>'','id'=>"rc_file_name"])
													!!}

												</span>
											</div>
											<div class="col-xs-12">
												<p class="form-group pull-left overflow-hide"
													id="rc_file_name"></p>
											</div>
										</div>
									</div>
									<div class="col-md-12 form-control-fld">
										<div class="col-md-6 form-control-fld">
											<div id="insurance_file_name" class="text-break">

												@if ($errors->has('insurance_file_name'))
												<p style="color: red;">{!!$errors->first('insurance_file_name')!!}</p>
												@endif
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div id="permit_copy_file_name" class="text-break">

												@if ($errors->has('permit_copy_file_name'))
												<p style="color: red;">{!!$errors->first('permit_copy_file_name')!!}</p>
												@endif
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div id="fc_file_name" class="text-break">

												@if ($errors->has('fc_file_name'))
												<p style="color: red;">{!!$errors->first('fc_file_name')!!}</p>
												@endif
											</div>
										</div>
										<div class=" col-md-6 form-control-fld">
											<div id="rc_file_name" class="text-break">

												@if ($errors->has('rc_file_name'))
												<p style="color: red;">{!!$errors->first('rc_file_name')!!}</p>
												@endif
											</div>
										</div>
									</div>




									<div class="col-md-12 form-control-fld">
										<div class=" col-md-12 form-control-fld">{!!
											Form::label('transport_reg_id', 'LSP/Transport Reg. ID',
											array('class' => '')) !!}</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('transport_reg_id',null,['class'=>'form-control
											form-control1']) !!}</div>
									</div>
									<div class="col-md-12 form-control-fld">
										<div class="col-sm-8 padding-none">
											<input type="checkbox" id="cdbaccept" name="cdbaccept"> <span
												class="lbl padding-8"></span> Accept Term &amp; Conditions
											(Digital Contract) &nbsp; &nbsp;
										</div>
										<div class="clearfix"></div>
										<div class="col-sm-4 padding-none">{!! Form::submit('Confirm &
											Register', ['class' => 'btn register_submit post-btn
											margin-top']) !!}</div>


									</div>

									{!! Form::close() !!}
								</div>
								<div class="clearfix"></div>
								<div class="center-width upload-divider">

									<h4>Bulk Upload</h4>
									{!! Form::open(['url' =>
									'vehicleupload','id'=>'vehicle_form','enctype'=>'multipart/form-data','class'=>'form-inline'])
									!!}

									<div class="col-md-12 form-control-fld">

										<div class="col-md-6 form-control-fld">
											{!!
											Form::file('vehicle_upload',null,['class'=>'filestyle','id'=>"vehicleUpload"])
											!!} @if ($errors->has('vehicle_upload'))
											<p style="color: red;">{!!$errors->first('vehicle_upload')!!}</p>
											@endif
										</div>
										<div class=" col-md-6 form-control-fld">
											<a target="_blank" href="{{ url('/downloads/vehicle.csv') }}"><img
												src="{{ asset('/images/download.png') }}" alt="" width="20" />Download
												CSV</a>
										</div>

									</div>
									{!! Form::submit('Upload', ['class' => 'btn add-btn']) !!} {!!
									Form::close() !!}
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
</div>
<!-- Page Center Content Ends Here -->
<!-- Right Starts Here -->
@include('partials.footer')
<!-- Right Ends Here -->

</div>
@endsection


