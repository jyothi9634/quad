@extends('app') @section('content')
@include('partials.page_top_navigation') @if (Session::has('message') &&
Session::get('message')!='')
<div class="flash">
	<p class="text-success col-sm-12 text-center flash-txt alert-info">{{ Session::get('message') }}</p>
</div>
@endif


<div class="main-container">
<div class="login-head heading-margin-top">
			<h1 class="margin-top margin-bottom-none">
				<span>LOGISTIKS.COM</span>
				<p>Update Vehicle Details</p>
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
									'vehicle_update/'.$vehicles->id,'id'=>'vehicle-master-form','enctype'=>'multipart/form-data','class'=>'form-inline
									margin-top']) !!}


									<div class="col-md-12 form-control-fld">

										<div class=" col-md-12 form-control-fld">{!!
											Form::label('vehicle_owned', 'I Transporter / Vehicle Owner',
											array('class' => '')) !!}</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('vehicle_owned',$vehicles->first_year_turnover,['class'=>'form-control
											form-control1 numericvalidation', 'placeholder'=>'No. Vehicles Owned']) !!}</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('vehicle_attatched',$vehicles->second_year_turnover,['class'=>'form-control
											form-control1 numericvalidation', 'placeholder'=>'No. Vehicles Attached']) !!}</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('vehicle_gps',$vehicles->third_year_turnover,['class'=>'form-control
											form-control1 numericvalidation', 'placeholder'=>'No. Vehicles with GPS']) !!}</div>
									</div>
									<div class="clearform"></div>
									<h4>Vehicle form</h4>

									<div class="col-sm-6 form-control-fld">
						{!! Form::text('vehicle_number', $vehicles->vehicle_number
										,['class'=>'form-control form-control1 alphanumeric_strVal','maxlength'=>20,
										'placeholder'=>'Vehicle Number *','maxlength'=>11]) !!}
										 @if($errors->has('vehicle_number'))
										<p style="color: red;">{!! $errors->first('vehicle_number') !!}</p>
										@endif
									</div>
									<div class="col-sm-6 form-control-fld">
										<div class="normal-select">{!! Form::select('vehicle_type',array('' => 'Select Vehicle
										Type *') + $vehicle, $vehicles->lkp_vehicle_type_id
										,['class'=>'form-control form-control1']) !!}
										 @if($errors->has('vehicle_type'))
										<p style="color: red;">{!!$errors->first('vehicle_type')!!}</p>
										@endif
											</div>
									</div>
									<div class="col-md-12 form-control-fld">
										{{--*/ $dimensions = explode('*',
										$vehicles->vehicle_dimension) /*--}}
										<div class=" col-md-12 form-control-fld">{!!
											Form::label('vehicle_dimension', 'Dimension', array('class'
											=> '')) !!}</div>
											
											<div class="col-md-4 form-control-fld">
											{!!
											Form::text('vehicle_length',$dimensions[2],['class'=>'form-control
											form-control1 threedigitstwodecimals_deciVal','placeholder'=>' L *']) !!}
											 @if($errors->has('vehicle_length'))
											<p style="color: red;">{!!$errors->first('vehicle_length')!!}</p>
											@endif
										</div>
										<div class="col-md-4 form-control-fld">
											{!!
											Form::text('vehicle_width',$dimensions[0],['class'=>'form-control
											form-control1 threedigitstwodecimals_deciVal', 'placeholder'=>' W *']) !!} 
											@if($errors->has('vehicle_width'))
											<p style="color: red;">{!!$errors->first('vehicle_width')!!}</p>
											@endif
										</div>
										<div class="col-md-4 form-control-fld">
											{!!
											Form::text('vehicle_height',$dimensions[1],['class'=>'form-control
											form-control1 threedigitstwodecimals_deciVal ','placeholder'=>' H *']) !!}
											 @if($errors->has('vehicle_height'))
											<p style="color: red;">{!!$errors->first('vehicle_height')!!}</p>
											@endif
										</div>
										
									</div>


									<div class="col-md-12 form-control-fld">
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('vehicle_capacity',array('' => 'Select
											Vehicle Capacity *') + $vehiclecapacities,
											$vehicles->vehicle_capacity ,['class'=>'form-control
											form-control1']) !!}
											 @if($errors->has('vehicle_capacity'))
											<p style="color: red;">{!!$errors->first('vehicle_capacity')!!}</p>
											@endif
											</div>
										</div>
										<div class="col-md-6 form-control-fld"><div class="normal-select">{!!
											Form::select('load_type',array('' => 'Select Load Type') +
											$load_type, $vehicles->lkp_load_type_id
											,['class'=>'form-control form-control1']) !!}</div></div>
									</div>

									<div class="col-md-12 form-control-fld">
										<div class=" col-md-12 form-control-fld">{!!
											Form::label('reg_owner_fname', 'Registered Owner',
											array('class' => '')) !!}</div>
										<div class="col-md-6 form-control-fld">
											{!! Form::text('reg_owner_fname',
											$vehicles->reg_owner_firstname ,['class'=>'form-control
											form-control1 alphaonly_strVal','placeholder'=>'First Name *','maxlength'=>50]) !!} 
											@if($errors->has('reg_owner_fname'))
											<p style="color: red;">{!!$errors->first('reg_owner_fname')!!}</p>
											@endif
										</div>
										<div class="col-md-6 form-control-fld">
											{!! Form::text('reg_owner_lname',
											$vehicles->reg_owner_lastname ,['class'=>'form-control
											form-control1 alphaonly_strVal','placeholder'=>'Last Name *','maxlength'=>50]) !!}
											 @if($errors->has('reg_owner_lname'))
											<p style="color: red;">{!!$errors->first('reg_owner_lname')!!}</p>
											@endif
										</div>
										<div class="col-md-6 form-control-fld">
											{!! Form::text('mfg_year', $vehicles->mfg_year ,
											['class'=>'form-control form-control1 numericvalidation',
											'placeholder'=>'Manufactured On *','maxlength'=>4]) !!}
											 @if($errors->has('mfg_year'))
											<p style="color: red;">{!!$errors->first('mfg_year')!!}</p>
											@endif

										</div>
										<div class="col-md-6 form-control-fld">
											{!! Form::text('chasis_number', $vehicles->chasis_number
											,['class'=>'form-control form-control1 alphanumeric_strVal',
											'placeholder'=>'Chassis Number *','maxlength'=>50]) !!}
											 @if($errors->has('chasis_number'))
											<p style="color: red;">{!!$errors->first('chasis_number')!!}</p>
											@endif
										</div>
										<div class="col-md-6 form-control-fld">{!!
											Form::text('engine_number', $vehicles->engine_number
											,['class'=>'form-control form-control1 clsEngineNumber',
											'placeholder'=>'Engine Number']) !!}</div>
									</div>

									<div class="col-md-6 form-control-fld">
										<div class="">
										<span class="pull-left" 
										{!!
											Form::label('is_gps', 'GPS Available', array('class' => ''))
											!!}</span>
										<div class="pull-left">
											<!--label> 
                                <?php //echo Form::radio('is_gps', '1', ($vehicles->is_gps == 1) ? true : false); ?> <span class="lbl padding-8"></span> Yes
                            </label> 
                            <label> 
                                <?php //echo Form::radio('is_gps', '0', ($vehicles->is_gps == 0) ? true : false); ?><span class="lbl padding-8"></span>  No
                            </label-->
											<div class="radio-block">
												<div class="radio_inline">
													<input type="radio" name="is_gps" id="is_gps_yes" value="1"
														{!! ($vehicles->is_gps == 1) ? "checked" : "" !!} /> <label
														for="is_gps_yes"><span></span>Yes</label>
												</div>
												<div class="radio_inline">
													<input type="radio" name="is_gps" id="is_gps_no" value="0"
														{!! ($vehicles->is_gps == 0) ? "checked" : "" !!} /> <label
														for="is_gps_no"><span></span>No</label>
												</div>
											</div>
										</div>

										</div>
									</div>
									<div class="col-md-12 form-control-fld margin-none padding-none @if($vehicles->is_gps==0) displayNone @endif" id="GPSFields">
										<div class="col-md-6 form-control-fld">	
											{!!Form::text('device_number',$vehicles->device_number,['class'=>'form-control
											form-control1 numericvalidation', 'placeholder'=>'Device Number *']) !!}											
										</div>
										<div class="col-md-6 form-control-fld">	
											{!!Form::text('sim_imsi_number',$vehicles->sim_imsi_number,['class'=>'form-control
											form-control1 alphanumeric_strVal', 'placeholder'=>'Sim IMSI Number *']) !!}											
										</div>
										<div class="col-md-6 form-control-fld">	
											{!!Form::text('mobile_operator',$vehicles->mobile_operator,['class'=>'form-control
											form-control1 alphaonly_strVal', 'placeholder'=>'Mobile Operator *']) !!}											
										</div>
										<div class="col-md-6 form-control-fld">	
											{!!Form::text('mobile_number',$vehicles->mobile_number,['class'=>'form-control
											form-control1 numericvalidation', 'placeholder'=>'Mobile Number *','maxlength'=>10]) !!}											
										</div>
										<div class="col-md-6 form-control-fld">
												<div class="input-prepend">
													<span class="add-on"><i class="fa fa-calendar-o"></i></span>
													{!!
													Form::text('device_fixed_date',date("d/m/Y",
												strtotime($vehicles->device_fixed_date)),['class'=>'form-control','placeholder'
													=> 'Date of device fixed in vehicle *']) !!}
												</div>
										</div>
									</div>

									<div class="col-md-6 form-control-fld">
										<div class=" col-md-12 form-control-fld">
										<span class="pull-left padding-top-20">{!!
											Form::label('is_insured', 'Insured', array('class' => '
											col-md-6 form-control-fld')) !!}
										</span>
										<div class="pull-left padding-top-20">
											<!--label> 
                               <?php //echo Form::radio('is_insured', '1', ($vehicles->is_insured == 1) ? true : false); ?><span class="lbl padding-8"></span>  Yes
                            </label> 
                            <label> 
                                <?php //echo Form::radio('is_insured', '0', ($vehicles->is_insured == 0) ? true : false); ?><span class="lbl padding-8"></span>  No
                            </label-->
											<div class="radio-block">
												<div class="radio_inline">
													<input type="radio" name="is_insured" id="is_insured_yes"
														value="1" {!! ($vehicles->is_insured == 1) ? "checked" :
													"" !!} /> <label for="is_insured_yes"><span></span>Yes</label>
												</div>
												<div class="radio_inline">
													<input type="radio" name="is_insured" id="is_insured_no"
														value="0" {!! ($vehicles->is_insured == 0) ? "checked" :
													"" !!} /> <label for="is_insured_no"><span></span>No</label>
												</div>
											</div>
										</div>
									  

										<!--div class=" col-md-12 form-control-fld">
                            {!! Form::label('insurance_validity', 'Insurance Validity', array('class' => '')) !!}
                        </div-->                                                <div class="clearfix"></div>
										
											@if ($vehicles->insurance_validity != '' &&	$vehicles->insurance_validity != '1970-01-01')
											<div class="col-md-12 form-control-fld padding-none">
                                                                                            <div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!!
												Form::text('insurance_validity',date("d/m/Y",
												strtotime($vehicles->insurance_validity)),['class'=>'form-control','placeholder'
												=> 'Insurance Validity']) !!}
											</div>
                                                                                        </div>
											@else
											<div class="col-md-12 form-control-fld padding-none">
                                                                                            <div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!!
												Form::text('insurance_validity','',['class'=>'form-control',
												'placeholder' => 'Insurance Validity']) !!}
											</div>
                                                                                        </div>
											@endif
										
										</div>
									</div>

									<div class="clearfix"></div>

									<div class="col-md-6 form-control-fld">
										<!--div class=" col-md-12 form-control-fld">
                            {!! Form::label('permit_type', 'Permit Type', array('class' => '')) !!}
                        </div-->
										<div class="col-md-12 form-control-fld">
											<div class="normal-select">
                                                                                {!! Form::select('permit_type',array('' => 'Select permit Type ') + $permittypes,$vehicles->permit_type, ['class'=>'selectpicker']) !!}
                                                                        </div>
                                                                                </div>
									</div>

									<div class="col-md-6 form-control-fld">
										<!--div class=" col-md-12 form-control-fld">
                            {!! Form::label('fc_validity', 'FC Validity', array('class' => '')) !!}
                        </div-->
										<div class="col-md-12 form-control-fld">
											@if ($vehicles->fc_validity != '' && $vehicles->fc_validity
											!= '1970-01-01')
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!! Form::text('fc_validity',date("d/m/Y",
												strtotime($vehicles->fc_validity)),['class'=>'form-control',
												'placeholder' => 'FC Validity']) !!}
											</div>
											@else
											<div class="input-prepend">
												<span class="add-on"><i class="fa fa-calendar-o"></i></span>
												{!! Form::text('fc_validity','',['class'=>'form-control',
												'placeholder' => 'FC Validity']) !!}
											</div>
											@endif
										</div>

									</div>


									<div class="col-md-12 form-control-fld">
										<label class=" col-md-12 form-control-fld">Upload Self
											Attested Documents</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span
													class="btn btn-default btn-file btn-upload insurance_file_name">
													Insurance<input type="hidden" name="in_file"
													value="2000000000"> {!!
													Form::file('insurance_file_name',null,['class'=>'','id'=>"insurance_file_name"])
													!!}


												</span>
											</div>
											<div class="col-xs-12">
												@if($vehicles->insurance_file_name) <a target="blank"
													href="{{url('/uploads/users/'.$vehicles->insurance_file_name)}}"
													class="form-group pull-left overflow-hide"
													id="insurance_file_name">{{substr($vehicles->insurance_file_name,0,14)}}...</a>
												@else
												<p class="form-group pull-left overflow-hide"
													id="insurance_file_name"></p>
												@endif
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
												@if($vehicles->permit_copy_file_name) <a target="blank"
													href="{{url('/uploads/users/'.$vehicles->permit_copy_file_name)}}"
													class="form-group pull-left overflow-hide"
													id="permit_copy_file_name">{{substr($vehicles->permit_copy_file_name,0,14)}}...</a>
												@else
												<p class="form-group pull-left overflow-hide"
													id="permit_copy_file_name"></p>
												@endif
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
												@if($vehicles->fc_file_name) <a target="blank"
													href="{{url('/uploads/users/'.$vehicles->fc_file_name)}}"
													class="form-group pull-left overflow-hide"
													id="fc_file_name">{{substr($vehicles->fc_file_name,0,14)}}...</a>
												@else
												<p class="form-group pull-left overflow-hide"
													id="fc_file_name"></p>
												@endif
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
												@if($vehicles->rc_file_name) <a target="blank"
													href="{{url('/uploads/users/'.$vehicles->rc_file_name)}}"
													class="form-group pull-left overflow-hide"
													id="rc_file_name">{{substr($vehicles->rc_file_name,0,14)}}...</a>
												@else
												<p class="form-group pull-left overflow-hide"
													id="rc_file_name"></p>
												@endif
											</div>
										</div>
									</div>
									<div class="col-md-12 form-control-fld">

										<div class="col-sm-3 padding-none">
											<div id="insurance_file_name" class="text-break">

												@if ($errors->has('insurance_file_name'))
												<p style="color: red;">{!!$errors->first('insurance_file_name')!!}</p>
												@endif
											</div>
										</div>
										<div class="col-sm-3 padding-right-none">
											<div id="permit_copy_file_name" class="text-break">

												@if ($errors->has('permit_copy_file_name'))
												<p style="color: red;">{!!$errors->first('permit_copy_file_name')!!}</p>
												@endif
											</div>
										</div>
										<div class="col-sm-3 padding-right-none">
											<div id="fc_file_name" class="text-break">

												@if ($errors->has('fc_file_name'))
												<p style="color: red;">{!!$errors->first('fc_file_name')!!}</p>
												@endif
											</div>
										</div>
										<div class="col-sm-3 padding-none">
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
											Form::text('transport_reg_id', $vehicles->transport_reg_id, ['class'=>'form-control form-control1']) !!}</div>
									</div>
									<div class="col-md-12 form-control-fld">
										<div class="col-sm-8 padding-none">
											<input type="checkbox" id="cdbaccept" checked="checked"
												name="cdbaccept"> <span class="lbl padding-8"></span> Accept Term &amp; Conditions (Digital Contract) &nbsp; &nbsp;
										</div>
										<div class="clearfix"></div>
										<div class="col-sm-4 padding-none">{!! Form::submit('Confirm &
											Register', ['class' => 'btn register_submit post-btn
											margin-top']) !!}</div>


									</div>
									{!! Form::close() !!}

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
