@extends('app') @section('content')
@include('partials.page_top_navigation') @if (Session::has('message') &&
Session::get('message')!='')
<div class="flash ">
	<p class="text-success col-sm-12 text-center flash-txt alert-info">{{
		Session::get('message') }}</p>
</div>
@endif


<div class="main-container">
<div class="login-head heading-margin-top">
			<h1 class="margin-top margin-bottom-none">
				<span>LOGISTIKS.COM</span>
				<p>Equipment Registration</p>
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
									'equipmentregister','id'=>'equipment_register_form','enctype'=>'multipart/form-data','class'=>'form-inline
									margin-top']) !!}

									<div class="col-md-12 form-control-fld">

										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('equipment_type_id',array('' => 'Equipment Type
												*') + $equipment, null,
												['class'=>'selectpicker','id'=>'equipment_type_id',
												'style'=>'width:100%']) !!}</div>
											<p style="color: red;">{!!$errors->first('equipment_type_id')!!}</p>


										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!!
												Form::text('equipment_specs',null,['rows'=>6, 'cols'=>50,
												'class'=>'form-control form-control1',
												'placeholder'=>'Equipment Specifications *',
												'id'=>'equipment_specs']) !!}</div>

											<p style="color: red;">{!!$errors->first('equipment_specs')!!}</p>


										</div>
									</div>

									<div class="col-md-12 form-control-fld">

										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('state_id',array('' => 'Select State *') +
												$state,null,['class'=>'selectpicker','onChange'=>"getDistrict()",'id'=>'state_id'])
												!!}</div>
											<p style="color: red;">{!! $errors->first('state_id') !!}</p>

										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('district_id',array('' => 'Select District
												*'),null,['class'=>'selectpicker','id'=>'district_id','onChange'=>"getCity()"])
												!!}</div>
											<p style="color: red;">{!! $errors->first('district_id') !!}</p>

										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('city_id',array('' => 'Select City *')
												,null,['class'=>'selectpicker','id'=>'city_id']) !!}</div>
											<p style="color: red;">{!! $errors->first('city_id') !!}</p>


										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!!
												Form::text('pincode',null,['class'=>'form-control
												form-control1','placeholder'=>'Pincode *']) !!}</div>
											<p style="color: red;">{!!$errors->first('pincode')!!}</p>


										</div>
									</div>

									<div class="clearfix"></div>
									<div class="col-md-12 form-control-fld">
										<label class="col-md-12 form-control-fld"> Driver/Operator <span
											class="red">*</span></label>
										<!--label class="col-md-3  form-control-fld"> 

                            <?php //echo Form::radio('is_driver', '1'); ?> <span class="lbl padding-8"></span> 
                            Available
                        </label> 
                        <label class=" col-md-3 form-control-fld"> 
                            <?php //echo Form::radio('is_driver', '0'); ?><span class="lbl padding-8"></span> 
                            Not-Available
                        </label-->
										<div class="radio-block">
											<div class="radio_inline">
												<input type="radio" name="is_driver" id="spot_lead_type"
													value="1" checked /> <label for="spot_lead_type"><span></span>Available</label>
											</div>
											<div class="radio_inline">
												<input type="radio" name="is_driver" id="term_lead_radio"
													value="0" /> <label for="term_lead_radio"><span></span>Not-Available</label>
											</div>
										</div>
										@if ($errors->has('is_driver'))
										<p style="color: red;">{!!$errors->first('is_driver')!!}</p>
										@endif
									</div>




									<div class="col-md-12 form-control-fld">
										<div class="col-md-12 form-control-fld">
											<label>Additional Information</label>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!!
												Form::text('equipment_info',null,['class'=>'form-control
												form-control1','placeholder'=>'Additional Info']) !!}</div>
										</div>
									</div>
									<div class="col-md-12 form-control-fld">
										<div class="col-md-12 form-control-fld">
											<label>Upload Equipment Photo <span class="red">*</span></label>
										</div>
										<div class="col-md-12 form-control-fld">
											{!! Form::file('equipment_image',null,['class'=>'filestyle
											btn btn-file file-upload','id'=>"equipmentImgUpload"]) !!}
											@if ($errors->has('equipment_image'))
											<p style="color: red;">{!!$errors->first('equipment_image')!!}</p>
											@endif
										</div>
										<div class="col-sm-4 padding-none">

											<div class="col-sm-6 padding-none imgBox">
												<img alt="" src="" id="imgDiv" class="imgBox">
											</div>

										</div>

									</div>
									<div class="col-md-12 form-control-fld">
										{!! Form::label('transport_reg_id', 'LSP / Transport Reg. ID',
										array('class' => 'col-md-12 form-control-fld')) !!}
										<div class="col-md-6 padding-none ">
											<div class="input-prepend">{!!
												Form::text('transport_reg_id',null,['class'=>'form-control
												form-control1']) !!}</div>
										</div>
									</div>
									<div class="col-md-12 form-control-fld">
										<div class="col-md-8 form-control-fld">
											<input type="checkbox" id="cdbaccept" name="cdbaccept"><span
												class="lbl padding-8"></span> Accept Term &amp; Conditions
											(Digital Contract) &nbsp; &nbsp;
										</div>
										<div class="clearfix"></div>
										<div class="col-md-6 padding-none">{!! Form::submit('Confirm
											&amp; Register', ['class' => 'btn register_submit post-btn'])
											!!}</div>


									</div>
									{!! Form::close() !!}
								</div>
                                                            
                                                            <div class="clearfix"></div>
                                                            
								<div class="center-width upload-divider">

									<h4>Bulk Upload</h4>
									{!! Form::open(['url' =>
									'equipmentupload','id'=>'equipment_form','enctype'=>'multipart/form-data','class'=>'form-inline
									margin-top']) !!}


									<div class="col-md-12 form-control-fld">
										<div class="col-md-6 form-control-fld">
											{!!
											Form::file('equipment_upload',null,['class'=>'filestyle','id'=>"equipmentUpload"])
											!!} @if ($errors->has('equipment_upload'))
											<p style="color: red;">{!!$errors->first('equipment_upload')!!}</p>
											@endif
										</div>
										<div class="col-md-6 form-control-fld">
											<a target="_blank"
												href="{{ url('/downloads/equipment.csv') }}"> <img
												src="{{ asset('/images/download.png') }}" alt="" width="20" />
												Download CSV
											</a>
										</div>
									</div>
									{!! Form::submit('Upload', ['class' => 'btn post-btn']) !!} {!!
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

<!-- Page Center Content Ends Here -->
<!-- Right Starts Here -->
@include('partials.footer')
<!-- Right Ends Here -->

</div>
@endsection


