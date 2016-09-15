<!--  if(isset($update_buyer)){
	$model->attributes=$update_buyer->attributes;

}-->
@extends('app') @section('content')

<div class="clearfix"></div>
<div class="main">
	<div class="container">
		<!--div class="login-head">
			<h1>
				Thank You For Joining <span>LOGISTIKS.COM</span>
				<p>Tell us a little about your business</p>
			</h1>
		</div-->
		<div class="home-block home-block-login">
			<div class="tabs">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#">Seller</a></li>
				</ul>
				<?php //dd($buyer); ?> 
				<div class="tab-content">
					{!! Form::open(array('url' => 'register/switch_seller', 'id' =>
					'individual-seller-form', 'class'=>'individual-seller-form
					form-inline margin-top', 'enctype' => 'multipart/form-data' )) !!}
					<div id="seller" class="tab-pane fade in active">

						<div class="login-block">
							<div class="login-form login-form-2">
								<div class="center-width">
									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Name of the
											Person</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('firstname',$buyer->firstname, array( 
												'placeholder'=>'First Name*','class'=>'form-control
												form-control1', 'id'=>'txt_first_name', 'maxlength'=>'30', 'readonly' => 'readonly' ))
												!!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('lastname',$buyer->lastname,
												array( 'class'=>'form-control form-control1',
												'placeholder'=>'Last Name*',
												'id'=>'txt_last_name','maxlength'=>'30',
												'readonly' => 'readonly' )) !!}</div>
										</div>
									</div>
									<div class="col-md-12 padding-none space-top">
										<label for="" class="col-md-12 padding-none">Personal Details</label>
										<div class="col-md-12 form-control-fld">
											<div class="input-prepend">
											{!! Form:: textarea('address',$buyer->address,
												array( 'class'=>'form-control form-control1',
												'id'=>'txt_address', 'placeholder'=>'Address*',
												'maxlength'=>'240', 'rows'=>'5')
											) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('pincode', 
												$buyer->pincode,
												array( 'class'=>'form-control form-control1',
												'id'=>'txt_pincode', 'placeholder'=>'Pincode*',
												'maxlength'=>'6' )) !!}
												{!! Form:: hidden ('pincode_hidden', $buyer->principal_place,array( 'class'=>'form-control form-control1','id'=>'hidden_user_pincode' )) !!}

											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('landline',
												$buyer->landline,
												array( 'class'=>'form-control form-control1',
												'placeholder'=>'Landline Number', 'id'=>'txt_landline',
												'maxlength'=>'15' )) !!}</div>
										</div>

									</div>
									<p class="line-space">Business Details</p>
									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Nature of
											business</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('nature_of_business', '', array( 'class'=>'form-control
												form-control1','id'=>'txt_business_nature',
												'placeholder'=>'ex. Export &amp; import*', 'maxlength'=>'50'
												)) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<!--<div class="input-prepend">{!! Form:: text ('established_in',
												'', array( 'class'=>'form-control form-control1',
												'placeholder'=>'Year of establishment*',
												'id'=>'txt_established_in','maxlength'=>'4' )) !!}</div>-->
											<div class="normal-select">
												{!!
                                                    Form::select('established_in',array('' => 'Year of establishment*')
                                                    + $getYearofEstablished
                                                    ,null,['class'=>'selectpicker','id'=>'txt_established_in'])
                                                !!}
											</div>
										</div>
									</div>
									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Principal Place
											of business</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('principal_place', $buyer->principal_place, array( 'class'=>'form-control
												form-control1','placeholder'=>'Ex: Hyderabad *',
												'id'=>'txt_business_place', 'maxlength'=>'50' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('principal_place_pincode', '', array(
												'class'=>'form-control form-control1',
												'id'=>'txt_principal_place_pincode',
												'placeholder'=>'Pincode*', 'maxlength'=>'6' )) !!}</div>
										</div>
									</div>
									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Annual Turn over</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('current_turnover', '',array( 'class'=>'form-control numberVal
												form-control1', 'id'=>'txt_current_turnover',
												'placeholder'=>'Current Year', 'maxlength'=>'30' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('first_year_turnover', '',array( 'class'=>'form-control numberVal
												form-control1', 'id'=>'txt_first_yr_turnover',
												'placeholder'=>'Year1', 'maxlength'=>'30' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('second_year_turnover', '',array( 'class'=>'form-control numberVal
												form-control1', 'id'=>'txt_second_yr_turnover',
												'placeholder'=>'Year2', 'maxlength'=>'30' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('third_year_turnover', '',array( 'class'=>'form-control numberVal
												form-control1', 'id'=>'txt_second_yr_turnover',
												'placeholder'=>'Year3', 'maxlength'=>'30' )) !!}</div>
										</div>
									</div>

									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Company Information</label>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('employee_strengths',(['' => 'Select Employee Strengths Type*'] + $getEmployeeStrengths), null, ['class' => 'selectpicker','id' => 'employee_strengths']) !!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('lkp_industry',(['' => 'Select Industry Type*'] + $lkp_industry), null, ['class' => 'selectpicker','id' => 'lkp_industry']) !!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('lkp_specialities',(['' => 'Select Specialities Type*'] + $getSpecialities), null, ['class' => 'selectpicker','id' => 'lkp_specialities']) !!}
											</div>
										</div>
									</div>
									
									
									<div class="col-md-12 padding-none space-top">
										<label for="" class="col-md-12 padding-none">Official Contact</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_firstname', '',array( 'class'=>'form-control letterValdiation
												form-control1', 'id'=>'txt_cp_first_name',
												'placeholder'=>'First Name', 'maxlength'=>'30' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_lastname', '',array( 'class'=>'form-control letterValdiation
												form-control1', 'id'=>'txt_cp_last_name',
												'placeholder'=>'Last Name', 'maxlength'=>'30' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_designation', '',array('class'=>'form-control letterValdiation
												form-control1',
												'id'=>'txt_cp_designation','placeholder'=>'Designation','maxlength'=>'30'))
												!!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_email',$user_email ,array('id'=>'txt_cp_email',
												'placeholder'=>'Applicant Email *(Required)',
												'class'=>'form-control form-control1','maxlength'=>'50', 'readonly' => 'readonly'))
												!!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('contact_mobile',
												$buyer->mobile,array('class'=>'form-control numericvalidation form-control1',
												'placeholder'=>'Contact Mobile*',
												'id'=>'txt_cp_mobile','maxlength'=>'13')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_landline', '',array('class'=>'form-control numericvalidation
												form-control1','id'=>'txt_cp_landline','maxlength'=>'15',
												'placeholder'=>'Land line No. with STD Code*') ) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('gta',
												'',array('class'=>'form-control form-control1',
												'id'=>'txt_company_gta','placeholder'=>'GTA','maxlength'=>'30'))
												!!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('service_tax_number', '',array('class'=>'form-control
												form-control1', 'id'=>'txt_service_tax_number',
												'placeholder'=>'Service Tax Number*','maxlength'=>'30')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('tin',
												'',array('class'=>'form-control form-control1',
												'id'=>'txt_company_tin','placeholder'=>'TIN*','maxlength'=>'30'))
												!!}</div>
										</div>

										<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
										{!! Form:: text ('pannumber',$userRecord->pannumber,array('class'=>'form-control alphanumeric_strVal form-control1','id'=>'txt_company_pannumber','placeholder'=>'PAN Number*','maxlength'=>'10')) !!}
										</div>
										</div>
										
										
										
										
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('bankname',
												'',array('class'=>'form-control form-control1',
												'id'=>'txt_company_bank', 'placeholder'=>'Bank Name',
												'maxlength'=>'30')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('branchname',
												'',array('class'=>'form-control form-control1',
												'id'=>'txt_company_branch','placeholder'=>'Branch Name',
												'maxlength'=>'30')) !!}</div>
										</div>
									</div>
									<div class="col-md-12 padding-none space-top">
										<label for="" class="col-md-12 padding-none">Upload Documents</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file btn-upload"> In
													Corporation {!! Form:: file
													('in_corporation_file','',array('class'=>'fileInput',
													'id'=>'txt_in_corporation_file','placeholder'=>'','maxlength'=>'50'))
													!!} </span>
											</div>
											<p class="form-group pull-left overflow-hide"
												id="in_corporation_file"></p>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file btn-upload"> TIN {!!
													Form:: file ('tin_filepath',
													'',array('class'=>'form-control margin-bottom input-sm
													fileInput',
													'id'=>'txt_tin_file','placeholder'=>'','maxlength'=>'50'))
													!!} </span>
											</div>
											<p class="form-group pull-left overflow-hide"
												id="tin_filepath"></p>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file btn-upload"> Pan Card
													{!! Form:: file ('pancard_filepath',
													'',array('class'=>'form-control margin-bottom input-sm
													fileInput',
													'id'=>'txt_pancard_file','placeholder'=>'','maxlength'=>'50'))
													!!} </span>
											</div>
											<p class="form-group pull-left overflow-hide"
												id="pancard_filepath"></p>

										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file  btn-upload"> GTA {!!
													Form:: file ('gta_filepath',
													'',array('class'=>'form-control margin-bottom input-sm
													fileInput',
													'id'=>'txt_gta_file','placeholder'=>'','maxlength'=>'50'))
													!!} </span>
											</div>
											<p class="form-group pull-left overflow-hide"
												id="gta_filepath"></p>

										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file  btn-upload "> Sales
													Tax{!! Form:: file ('sales_tax_filepath',
													'',array('class'=>'form-control margin-bottom input-sm
													fileInput',
													'id'=>'txt_sales_tax_file','placeholder'=>'','maxlength'=>'50'))
													!!} </span>
											</div>
											<p class="form-group pull-left overflow-hide"
												id="sales_tax_filepath"></p>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file  btn-upload"> Service
													Tax{!! Form:: file ('service_tax_filepath',
													'',array('class'=>'form-control margin-bottom input-sm
													fileInput',
													'id'=>'txt_service_tax_file','placeholder'=>'','maxlength'=>'50'))
													!!} </span>
											</div>
											<p class="form-group pull-left overflow-hide"
												id="service_tax_filepath"></p>

										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file btn-upload"> Central
													Excise {!! Form:: file ('central_excise_filepath',
													'',array('class'=>'form-control margin-bottom input-sm
													fileInput',
													'id'=>'txt_central_excise_file','placeholder'=>'','maxlength'=>'50'))
													!!} </span>
											</div>
											<p class="form-group pull-left overflow-hide"
												id="central_excise_filepath"></p>

										</div>
									</div>
									<div class="col-md-12 padding-none space-top reg_input-check">
										<p class="line-space">
											Services Offered <span class="red">*</span>
										</p>
										<span class="error" id="error_services"></span>
										<div class="clearfix"></div>
										<div class="service_selection">
											<div class="select_service_block">
												<p
													class="col-md-12 padding-none line-space service-icon-div">Transportation</p>
												@foreach ($services as $service) @if($service->group_name ==
												'Road') @if($service->service_crumb_name ==
												'Transportation')

												<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}" >
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach @foreach ($services as $service)


												@if($service->group_name == 'Rail')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-12 padding-left-none  margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach @foreach ($services as $service)


												@if($service->group_name == 'Air')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach @foreach ($services as $service)


												@if($service->group_name == 'Ocean')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-12 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach @foreach ($services as $service)


												@if($service->group_name == 'Intracity')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-12 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>
												@endif @endif @endforeach @foreach ($services as $service)


												@if($service->group_name == 'Courier')
												@if($service->service_crumb_name == 'Transportation')

												<div class="col-md-12 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach
											</div>


											<div class="select_service_block">
												<p
													class="col-md-12 padding-none line-space service-icon-div">Vehicle</p>
												@foreach ($services as $service) @if($service->group_name ==
												'Road') @if($service->service_crumb_name == 'Vehicle')

												<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endif @endforeach

											</div>

											<div class="select_service_block border-none">
												<p
													class="col-md-12 padding-none line-space service-icon-div">Relocation</p>
												@foreach ($services as $service) @if($service->group_name ==
												'Relocation') @if($service->service_crumb_name ==
												'Relocation')

												<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>
												@endif @endif @endforeach
											</div>

											<div class="col-md-4 padding-left-none displayNone">
												<p
													class="col-md-12 padding-none line-space service-icon-div">Upcoming</p>
												@foreach ($services as $service) @if($service->group_name ==
												'Upcoming')

												<div class="col-md-12 padding-left-none  margin-bottom " title="{{strtoupper($service->service_name)}}">
													<input type="checkbox" id="service_{{$service->id}}"
														name="services[]" value="{{$service->id}}"><span
														class="lbl padding-8  pull-left"></span>
													<div class="service-div service-icon-div  pull-left">
														<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
															{{$service->service_name}}</a>
														<div class="clearfix"></div>
													</div>
												</div>

												@endif @endforeach
											</div>


											<div class="col-sm-12 padding-none space-top">


												<div class="col-xs-12 padding-none displayNone space"
													id="intracityArea">
													<label for="" class="col-md-12 padding-none">For Road
														Intracity only</label>
													<div
														class="normal-select margin-bottom col-md-6 padding-left-none">{!!
														Form::select('intracity_city[]',array('' => 'Select City')
														+ $cities,null,['class'=>'drop-scroll selectpicker',
														'onChange'=>"getIntraLocality();",
														'id'=>'states_multipleSelect','multiple'=>true]) !!}</div>
													<div
														class="normal-select margin-bottom col-md-6 padding-left-none">
														{!! Form::select('intracity_locality[]',array('' =>
														'Select Locality'),null,['class'=>'drop-scroll
														selectpicker','id'=>'locality_multiple','multiple'=>true])
														!!}</div>
													<span class="error error form-group margin-top"
														id="error_intracity_area"></span>

												</div>


											</div>
											<div class="col-sm-12 padding-none space-top">

												<div class="col-sm-12 padding-none displayNone" id="pmArea">
													<small>For Relocation only</small><br>
													<div
														class="normal-select margin-bottom col-md-6 padding-left-none">
														{!! Form::select('pm_state[]',array('Default' => 'Select
														State') + $stateList,null,['class'=>'selectpicker',
														'onChange'=>"return getpmCity();",
														'id'=>'states_multiple','multiple'=>true]) !!}</div>
													<div
														class="normal-select margin-bottom col-md-6 padding-left-none">{!!
														Form::select('pm_city[]', array('Default' => 'Select
														City') ,
														null,['class'=>'selectpicker','id'=>'city_multiple','multiple'=>true])
														!!}</div>
													<span class="error error form-group margin-top"
														id="error_pm_area"></span>
												</div>
											</div>



											<div class="col-md-4 form-control-fld space-top pull-right">
												{!! Form::submit('Save &amp; Continue', array( 'class'=>'btn
												add-btn-2', 'id'=>'submitSeller')) !!}</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						{!! Form::close() !!}

					</div>

				</div>
			</div>
			<div class="clearfix"></div>
		</div>

	</div>
</div>
@include('partials.footer')
</div>

@endsection
