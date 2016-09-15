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
				<p>Update Equipment Details</p>
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
									<div class="col-md-12 form-control-fld">
										{!! Form::open(['url' =>
										'equip_update/'.$equipments->id,'id'=>'equipment_register_form_edit','enctype'=>'multipart/form-data','class'=>'form-inline
										margin-top']) !!} <label><b>Equipment ID: #{{$equipments->id}}</b></label>
									</div>
									<div class="col-md-12 form-control-fld">

										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
												{!! Form::select('equipment_type_id',array('' => 'Equipment
												Type *') + $equipment, $equipments->lkp_equipment_type_id
												,['class'=>'form-control
												form-control1','id'=>'equipment_type_id',
												'style'=>'width:100%']) !!}
												<p style="color: red;">{!!$errors->first('equipment_type_id')!!}</p>
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											{!! Form::text('equipment_specs',
											$equipments->equipment_specifications ,['rows'=>6,
											'cols'=>50, 'class'=>'form-control form-control
											form-control1', 'placeholder'=>'Equipment Specifications *',
											'id'=>'equipment_specs']) !!}

											<p style="color: red;">{!!$errors->first('equipment_specs')!!}</p>


										</div>
									</div>

									<div class="col-md-12 form-control-fld">

										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('state_id',array('' => 'Select State *') +
											$state,$equipments->lkp_state_id,['class'=>'form-control
											form-control1','onChange'=>"getDistrict()",'id'=>'state_id'])
											!!}

											<p style="color: red;">{!! $errors->first('state_id') !!}</p>
											</div>

										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('district_id',array('' => 'Select District
											*')+
											$district,$equipments->lkp_district_id,['class'=>'form-control
											form-control1','id'=>'district_id','onChange'=>"getCity()"])
											!!}

											<p style="color: red;">{!! $errors->first('district_id') !!}</p>
											</div>

										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('city_id',array('' => 'Select City *')+
											$city,$equipments->lkp_city_id,['class'=>'form-control
											form-control1','id'=>'city_id']) !!}

											<p style="color: red;">{!! $errors->first('city_id') !!}</p>
											</div>


										</div>
										<div class="col-md-6 form-control-fld">
											{!! Form::text('pincode', $equipments->pincode
											,['class'=>'form-control
											form-control1','placeholder'=>'Pincode *']) !!}
											<p style="color: red;">{!!$errors->first('pincode')!!}</p>


										</div>
									</div>

									<div class="clearfix"></div>
									<div class="col-md-12 form-control-fld">
										<label class="col-md-12 form-control-fld"> Driver/Operator <span
											class="red">*</span></label>
										<!--label class="col-md-3  form-control-fld"> 

                            <?php //echo Form::radio('is_driver', '1', ($equipments->is_driver == 1) ? true : false);?> <span class="lbl padding-8"></span> 
                            Available
                        </label> <label class=" col-md-3 form-control-fld"> 
                            <?php //echo Form::radio('is_driver', '0', ($equipments->is_driver == 0) ? true : false);?><span class="lbl padding-8"></span> 
                            Not-Available
                        </label-->
										<div class="margin-bottom radio-block">
											<div class="radio_inline">
												<input type="radio" name="is_driver" id="spot_lead_type"
													value="1" {!! ($equipments->is_driver == 1) ? "checked" :
												"" !!} /> <label for="spot_lead_type"><span></span>Available</label>
											</div>
											<div class="radio_inline">
												<input type="radio" name="is_driver" id="term_lead_radio"
													value="0" {!! ($equipments->is_driver == 0) ? "checked" :
												"" !!} /> <label for="term_lead_radio"><span></span>Not-Available</label>
											</div>
										</div>
										@if ($errors->has('is_driver'))
										<p style="color: red;">{!!$errors->first('is_driver')!!}</p>
										@endif
									</div>




									<div class="col-md-12 form-control-fld">
										<div class="col-md-6 form-control-fld">
											<label>Additional Information</label>
											{!!
											Form::text('equipment_info', $equipments->equipment_info
											,['class'=>'margin-bottom form-control form-control1',
											'placeholder'=>'Additional Info']) !!}
										</div>
									</div>
									<div class="col-md-12 form-control-fld">
										<div class="col-md-12 form-control-fld">
											<label>Upload Equipment Photo <span class="red">*</span></label>
											{!! Form::file('equipment_image',null,['class'=>'filestyle
											btn btn-file file-upload','id'=>"equipmentImgUpload"]) !!}
											@if ($errors->has('equipment_image'))
											<p style="color: red;">{!!$errors->first('equipment_image')!!}</p>
											@endif @if($equipments->equipment_image) <a target="blank"
												href="{{url('/uploads/users/'.$equipments->equipment_image)}}"
												class="margin-bottom pull-left overflow-hide" id="tin_filepath">
												{{$equipments->equipment_image}}</a> @else
											<p class="form-group pull-left overflow-hide"
												id="tin_filepath"></p>
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
										<div class="col-md-6 padding-none ">{!!
											Form::text('transport_reg_id', $equipments->transport_reg_id
											,['class'=>'margin-bottom form-control form-control1']) !!}</div>
									</div>
									<div class="col-md-12 form-control-fld">
										<div class="col-md-8 form-control-fld">
											<input checked="checked" type="checkbox" id="cdbaccept"
												name="cdbaccept"><span class="lbl padding-8"></span> Accept
											Term &amp; Conditions (Digital Contract) &nbsp; &nbsp;
										</div>
										<div class="clearfix"></div>
										<div class="col-md-6 padding-none">{!! Form::submit('Confirm
											&amp; Register', ['class' => 'btn register_submit post-btn'])
											!!}</div>


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
<!-- Page Center Content Ends Here -->
<!-- Right Starts Here -->
@include('partials.footer')
<!-- Right Ends Here -->

</div>
@endsection


