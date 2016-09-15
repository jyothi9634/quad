@extends('app') @section('content')
@include('partials.page_top_navigation')
<div class="clearfix"></div>
<div class="login-head">
			<h1 class="margin-bottom-none">
				<span>LOGISTIKS.COM</span>
				<p>Edit Profile</p>
			</h1>
		</div>
<div class="main">
	<div class="container reg_crumb">
		
		<div class="home-block home-block-login">
			<div class="tabs">

				<div class="tab-content">
					{!! Form::open(array('url' => 'register/edit_seller/'.$seller_id,
					'id' => 'individual-seller-form', 'class'=>'form-inline
					margin-top', 'enctype' => 'multipart/form-data' )) !!}
					<div id="seller" class="tab-pane fade in active">

						<div class="login-block">
							<div class="login-form login-form-2">
								<div class="center-width">
									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Name of the
											Person</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('firstname',
												$sellerRecord->firstname, array('class'=>'form-control
												form-control1', 'id'=>'txt_first_name', 'maxlength'=>'30',
												'placeholder'=>'First Name*' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('lastname',
												$sellerRecord->lastname, array( 'class'=>'form-control
												form-control1', 'id'=>'txt_last_name' ,'maxlength'=>'30',
												'placeholder'=>'Last Name*' )) !!}</div>
										</div>
									</div>
									<div class="col-md-12 padding-none space-top">
										<label for="" class="col-md-12 padding-none">Personal Details</label>
										<div class="col-md-12 form-control-fld">
											<div class="input-prepend">{!! Form:: textarea ('address',
												$sellerRecord->address, array( 'class'=>'form-control
												form-control1', 'id'=>'txt_address',
												'placeholder'=>'Address*', 'maxlength'=>'240', 'rows'=>'5'))
												!!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
											{!! Form:: text('pincode',$sellerRecord->pincode, array('class'=>'form-control numericvalidation form-control1','id'=>'txt_pincode','placeholder'=>'Pincode*', 'maxlength'=>'6' )) !!}
											{!! Form:: hidden ('pincode_hidden', $sellerRecord->principal_place,array( 'class'=>'form-control form-control1','id'=>'hidden_user_pincode' )) !!}	
											
												
												</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('landline',$sellerRecord->landline, array(
												'class'=>'form-control form-control1',
												'placeholder'=>'Landline Number*', 'id'=>'txt_landline',
												'maxlength'=>'15' )) !!}</div>
										</div>
										
										<div class="col-md-6 padding-none">
										<label for="" class="col-md-12 padding-none">Principal Place of business</label>
										<div class="col-md-12 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('principal_place', $sellerRecord->principal_place, array(
												'class'=>'form-control
												form-control1','id'=>'txt_business_place',
												'placeholder'=>'Ex: Hyderabad *' ,'readonly', 'maxlength'=>'50' )) !!}</div>
										</div>
									</div>

									</div>
									<div class="col-md-12 padding-none">
										<p class="line-space">Business Details</p>
										<label for="" class="col-md-12 padding-none">Nature of
											business</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('nature_of_business', $sellerRecord->nature_of_business,
												array( 'class'=>'form-control form-control1',
												'placeholder'=>'Ex: Export &amp; import*',
												'id'=>'txt_business_nature', 'maxlength'=>'50' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<!--<div class="input-prepend">{!! Form:: text ('established_in',
												$sellerRecord->established_in, array( 'class'=>'form-control
												form-control1', 'id'=>'txt_established_in',
												'placeholder'=>'Year of establishment*', 'maxlength'=>'4' ))
												!!}</div>-->
											<div class="normal-select">	
												{!!
													Form::select('established_in',array('' => 'Year of establishment*')
													+ $getYearofEstablished
													,$sellerRecord->established_in,['class'=>'selectpicker','id'=>'txt_established_in'])
												!!}									
											</div>
										</div>
									</div>


									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Annual Turn over</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('current_turnover', $sellerRecord->current_turnover ,array(
												'class'=>'form-control numberVal form-control1',
												'id'=>'txt_current_turnover', 'placeholder'=>'Current Year',
												'maxlength'=>'10' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('first_year_turnover', $sellerRecord->first_year_turnover
												,array( 'class'=>'form-control numberVal form-control1',
												'id'=>'txt_first_yr_turnover', 'placeholder'=>'Year1',
												'maxlength'=>'10' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('second_year_turnover', $sellerRecord->second_year_turnover
												,array( 'class'=>'form-control numberVal form-control1',
												'id'=>'txt_second_yr_turnover', 'placeholder'=>'Year2',
												'maxlength'=>'10' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('third_year_turnover',$sellerRecord->third_year_turnover
												,array( 'class'=>'form-control numberVal form-control1',
												'id'=>'txt_second_yr_turnover', 'placeholder'=>'Year3',
												'maxlength'=>'10' )) !!}</div>
										</div>
									</div>

									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Company Information</label>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('employee_strengths',(['' => 'Select Employee Strengths Type*'] + $getEmployeeStrengths), $sellerRecord->lkp_employee_strength_id, ['class' => 'selectpicker','id' => 'employee_strengths']) !!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('lkp_industry',(['' => 'Select Industry Type*'] + $lkp_industry), $sellerRecord->lkp_industry_id, ['class' => 'selectpicker','id' => 'lkp_industry']) !!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('lkp_specialities',(['' => 'Select Specialities Type*'] + $getSpecialities), $sellerRecord->lkp_speciality_id, ['class' => 'selectpicker','id' => 'lkp_specialities']) !!}
											</div>
										</div>
									</div>

									<div class="col-md-12 padding-none space-top">
										<label for="" class="col-md-12 padding-none">Official Contact</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_firstname', $sellerRecord->contact_firstname
												,array( 'class'=>'form-control letterValdiation form-control1',
												'id'=>'txt_cp_first_name', 'placeholder'=>'First Name',
												'maxlength'=>'30' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_lastname',$sellerRecord->contact_lastname ,array(
												'class'=>'form-control letterValdiation form-control1',
												'id'=>'txt_cp_last_name', 'placeholder'=>'Last Name',
												'maxlength'=>'30' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_designation', $sellerRecord->contact_designation
												,array('class'=>'form-control letterValdiation form-control1',
												'id'=>'txt_cp_designation','placeholder'=>'Designation','maxlength'=>'30'))
												!!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('contact_email',
												$sellerRecord->contact_email ,array('id'=>'txt_cp_email',
												'placeholder'=>'Applicant Email *(Required)',
												'class'=>'form-control form-control1','maxlength'=>'50'))
												!!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('contact_mobile',
												$sellerRecord->contact_mobile ,array('class'=>'form-control numericvalidation
												form-control1', 'id'=>'txt_cp_mobile','maxlength'=>'10',
												'placeholder'=>'Contact Mobile*')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_landline', $sellerRecord->contact_landline
												,array('class'=>'form-control numericvalidation
												form-control1','id'=>'txt_cp_landline','maxlength'=>'15',
												'placeholder'=>'Land line No. with STD Code*', ) ) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('gta',
												$sellerRecord->gta ,array('class'=>'form-control
												form-control1', 'id'=>'txt_company_gta',
												'placeholder'=>'GTA', 'maxlength'=>'30')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('service_tax_number', $sellerRecord->service_tax_number
												,array('class'=>'form-control form-control1',
												'id'=>'txt_service_tax_number','maxlength'=>'30',
												'placeholder'=>'Service Tax Number*')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('tin',
												$sellerRecord->tin ,array('class'=>'form-control
												form-control1','id'=>'txt_company_tin',
												'placeholder'=>'TIN*', 'maxlength'=>'30')) !!}</div>
										</div>

										
										<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text ('pannumber',
											$sellerRecord->pannumber ,array('class'=>'form-control
											form-control1', 'id'=>'txt_company_pannumber','maxlength'=>'10',
											'placeholder'=>'PAN Number*')) !!}</div>
									</div>
									
										
										
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('bankname',
												$sellerRecord->bankname ,array('class'=>'form-control
												form-control1', 'id'=>'txt_company_bank',
												'placeholder'=>'Bank Name','maxlength'=>'50')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('branchname',
												$sellerRecord->branchname ,array('class'=>'form-control
												form-control1', 'id'=>'txt_company_branch',
												'placeholder'=>'Branch Name','maxlength'=>'50')) !!}</div>
										</div>
									</div>
									
									<div class="col-md-12 padding-none space-top">
									<label for="" class="col-md-12 padding-none">Business Description</label>
										<div class="col-md-12 form-control-fld">
											<div class="input-prepend">{!! Form:: textarea ('description_user',
												$sellerRecord->description, array( 'class'=>'form-control form-control1',
												'id'=>'txt_description','placeholder'=>'Description',
												'maxlength'=>'350', 'rows'=>'5' )) !!}</div>
										</div>
									</div>

									<div class="col-md-12 padding-none space-top">
										<label for="" class="col-md-12 padding-none">User Upload Documents</label>
										<div class="col-md-6">	
											@if($sellerRecord->user_pic)
												{{--*/ $user_pic_ext = pathinfo($sellerRecord->user_pic, PATHINFO_EXTENSION) /*--}}
												@if(file_exists(SELLERUPLOADPATH.$sellerRecord->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$sellerRecord->user_pic)))	
													<img class="img-responsive" src="{{URL::to(SELLERUPLOADPATH.$sellerRecord->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$sellerRecord->user_pic))}}">
												@elseif(file_exists(BUYERUPLOADPATH.$sellerRecord->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$sellerRecord->user_pic)))	
													<img class="img-responsive" src="{{URL::to(BUYERUPLOADPATH.$sellerRecord->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$sellerRecord->user_pic))}}">
												@endif
											@endif
										</div>
										<div class="col-md-6">	
											@if($sellerRecord->logo)
												{{--*/ $logo_ext = pathinfo($sellerRecord->logo, PATHINFO_EXTENSION) /*--}}
												@if(file_exists(SELLERUPLOADPATH.$sellerRecord->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$sellerRecord->logo)))
													<img class="img-responsive" src="{{URL::to(SELLERUPLOADPATH.$sellerRecord->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$sellerRecord->logo))}}">
												@elseif(file_exists(BUYERUPLOADPATH.$sellerRecord->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$sellerRecord->logo)))
													<img class="img-responsive" src="{{URL::to(BUYERUPLOADPATH.$sellerRecord->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$sellerRecord->logo))}}">
												@endif	
											@endif
										</div>
										<div class="clearfix"></div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file btn-upload">
												 Profile Picture {!! Form:: file('profile_picture',array('class'=>'fileInput','id'=>'txt_profile_picture','placeholder'=>'','accept'=>'jpg|jpeg|png|PNG|JPEG|JPG')) !!} 
												 </span>
											</div>
											<p class="form-group pull-left overflow-hide"
												id="profile_picture"></p>
										</div>

										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file btn-upload">
												 Logo {!! Form:: file ('logo_user',array('class'=>'form-control margin-bottom input-sm fileInput','id'=>'txt_logo_user','placeholder'=>'','accept'=>'jpg|jpeg|png|PNG|JPEG|JPG')) !!} 
												</span>
											</div>
											<p class="form-group pull-left overflow-hide"
												id="logo_user"></p>

										</div>
									</div>
									
									<div class="col-md-12 padding-none space-top reg_input-check">
										
									<p class="line-space">Services Offered <span class="red">*</span>
										
										<span class="error" id="error_services"></span>

										<div class="clearfix"></div>
										<div class="service_selection">
										<div class="select_service_block">
											<p class="col-md-12 padding-none line-space service-icon-div">Transportation</p>
											@foreach($allServices as $service)
											 @if($service->group_name == 'Road')
											  @if($service->service_crumb_name =='Transportation')
											<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
												<input type="checkbox" id="service_{{$service->id}}"
													@if (in_array($service->id, $transport)) checked="checked"
												@endif name="services[]" value="{{$service->id}}"> <span
													class="lbl padding-8 pull-left"></span>
												<div class="service-div service-icon-div  pull-left">
													<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
														{{$service->service_name}}</a>
													<div class="clearfix"></div>
												</div>
											</div>
											@endif @endif @endforeach @foreach($allServices as $service)
											@if($service->group_name == 'Rail')
											@if($service->service_crumb_name == 'Transportation')
											<div class="col-md-12 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
												<input type="checkbox" id="service_{{$service->id}}"
													@if (in_array($service->id, $transport)) checked="checked"
												@endif name="services[]" value="{{$service->id}}"> <span
													class="lbl padding-8 pull-left"></span>
												<div class="service-div service-icon-div  pull-left">
													<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
														{{$service->service_name}}</a>
													<div class="clearfix"></div>
												</div>
											</div>
											@endif @endif @endforeach @foreach($allServices as $service)
											@if($service->group_name == 'Air')
											@if($service->service_crumb_name == 'Transportation')
											<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
												<input type="checkbox" id="service_{{$service->id}}"
													@if (in_array($service->id, $transport)) checked="checked"
												@endif name="services[]" value="{{$service->id}}"> <span
													class="lbl padding-8 pull-left"></span>
												<div class="service-div service-icon-div  pull-left">
													<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
														{{$service->service_name}}</a>
													<div class="clearfix"></div>
												</div>
											</div>
											@endif @endif @endforeach @foreach($allServices as $service)
											@if($service->group_name == 'Ocean')
											@if($service->service_crumb_name == 'Transportation')
											<div class="col-md-12 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
												<input type="checkbox" id="service_{{$service->id}}"
													@if (in_array($service->id, $transport)) checked="checked"
												@endif name="services[]" value="{{$service->id}}"> <span
													class="lbl padding-8 pull-left"></span>
												<div class="service-div service-icon-div  pull-left">
													<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
														{{$service->service_name}}</a>
													<div class="clearfix"></div>
												</div>
											</div>
											@endif @endif @endforeach @foreach($allServices as $service)
											@if($service->group_name == 'Intracity')
											@if($service->service_crumb_name == 'Transportation')
											<div class="col-md-12 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
												<input type="checkbox" id="service_{{$service->id}}"
													@if (in_array($service->id, $transport)) checked="checked"
												@endif name="services[]" value="{{$service->id}}"> <span
													class="lbl padding-8 pull-left"></span>
												<div class="service-div service-icon-div  pull-left">
													<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
														{{$service->service_name}}</a>
													<div class="clearfix"></div>
												</div>
											</div>
											@endif @endif @endforeach @foreach($allServices as $service)
											@if($service->group_name == 'Courier')
											@if($service->service_crumb_name == 'Transportation')
											<div class="col-md-12 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
												<input type="checkbox" id="service_{{$service->id}}"
													@if (in_array($service->id, $transport)) checked="checked"
												@endif name="services[]" value="{{$service->id}}"> <span
													class="lbl padding-8 pull-left"></span>
												<div class="service-div service-icon-div  pull-left">
													<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
														{{$service->service_name}}</a>
													<div class="clearfix"></div>
												</div>
											</div>
											@endif @endif @endforeach

										</div>


										<div class="select_service_block">
											<p class="col-md-12 padding-none line-space service-icon-div">Vehicle</p>
											@foreach($allServices as $service)
											 @if($service->group_name == 'Road')
											 @if($service->service_crumb_name == 'Vehicle')
											<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
												<input type="checkbox" id="service_{{$service->id}}"
													@if (in_array($service->id, $transport)) checked="checked"
												@endif name="services[]" value="{{$service->id}}"> <span
													class="lbl padding-8 pull-left"></span>
												<div class="service-div service-icon-div  pull-left">
													<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
														{{$service->service_name}}</a>
													<div class="clearfix"></div>
												</div>
											</div>
											@endif @endif @endforeach
										</div>

										<div class="select_service_block">
											<p class="col-md-12 padding-none line-space service-icon-div">Relocation</p>



											<span class="error" id="error_services"></span>
											@foreach($allServices as $service)
											 @if($service->group_name == 'Relocation')
											  @if($service->service_crumb_name == 'Relocation')
											 
											<div class="col-md-6 padding-none pull-left margin-bottom" title="{{strtoupper($service->service_name)}}">
												<input type="checkbox" id="service_{{$service->id}}" 
													@if (in_array($service->id, $transport)) checked="checked"
												@endif name="services[]" value="{{$service->id}}" > <span
													class="lbl padding-8 pull-left"></span>
												<div class="service-div service-icon-div  pull-left">
													<a><img src="{{url($service->service_image_path)}}" title="{{strtoupper($service->service_name)}}">
														{{$service->service_name}}</a>
													<div class="clearfix"></div>
												</div>
											</div>
											@endif @endif @endforeach
										</div>
											<div class="col-sm-12 padding-none space-top">
											<div class="col-xs-12 padding-none displayNone space"
												id="intracityArea">
												<label for="" class="col-md-12 padding-none">For Road
													Intracity only</label>
												<div class="col-md-6 padding-left-none normal-select">{!!
													Form::select('intracity_city[]',array('Default' => 'Select
													City') + $intracity_cities,$intra_city,['class'=>'drop-scroll
													selectpicker', 'onChange'=>"return getIntraLocality();",
													'id'=>'states_multipleSelect','multiple'=>true]) !!}</div>
												<div class="col-md-6 padding-left-none normal-select">
													{!! Form::select('intracity_locality[]',array('Default' =>
													'Select
													Locality')+$locality,$intra_locality,['class'=>'drop-scroll
													selectpicker','id'=>'locality_multiple','multiple'=>true])
													!!} <span class="error error form-group margin-top"
														id="error_intracity_area"></span>
												</div>


											</div>


										</div>
										<div class="col-sm-12 padding-none space-top">

											<div class="col-xs-12 padding-none displayNone space"
											id="pmArea">
											<label for="" class="col-md-12 padding-none">For Relocation
												only</label>
											<div class="col-md-6 padding-left-none normal-select">{!!
												Form::select('pm_state[]',array('Default' => 'Select State')
												+ $stateList,$pmState,['class'=>'selectpicker form-control
												form-control1', 'onChange'=>"return getpmCity();",
												'id'=>'states_multiple','multiple'=>true]) !!}</div>
											<div class="col-md-6 padding-left-none normal-select">
												{!! Form::select('pm_city[]',array('Default' => 'Select
												City')+$packMovCities,$pmCity,['class'=>'selectpicker','id'=>'city_multiple','multiple'=>true])
												!!} <span class="error error form-group margin-top"
													id="error_pm_area"></span>
											</div>

										</div>
										</div>

</div>
									</div>

									<div class="col-md-4 form-control-fld space-top pull-right">
										{!! Form::submit('Save &amp; Continue', array( 'class'=>'btn
										add-btn pull-right', 'id'=>'submitSeller')) !!}</div>



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
