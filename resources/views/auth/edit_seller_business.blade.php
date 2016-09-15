@extends('app') @section('content') @if (Session::has('message')) @endif
@include('partials.page_top_navigation')
<div class="login-head">
		<h1 class="margin-bottom-none">
			<!--span>LOGISTIKS.COM</span-->
			<p>Edit Profile</p>
		</h1>
	</div>
<div class="main">
<div class="container reg_crumb">
	
	<div class="home-block home-block-login">
		<div class="tabs">

			<div class="tab-content">
				<div id="seller" class="tab-pane fade in active">
					<div class="login-block">
						<div class="login-form login-form-2">
							<div class="center-width">
					@if($is_seller_business_exist == 1)
					{!! Form::open(array('url' =>
								'register/edit_seller_business/'.$seller_id, 'id' =>
								'corporate-seller-form', 'class'=>'form-inline margin-top',
								'enctype' => 'multipart/form-data' )) !!}
					@else
								{!! Form::open(array('url' => 'register/buyer_toggle_seller_business', 'id'
								=> 'corporate-seller-form', 'class'=>'form-inline margin-top',
								'enctype' => 'multipart/form-data' )) !!}
					
					
					@endif

								<div class="col-md-12 padding-none">
									<label for="" class="col-md-12 padding-none">Name of the
										Company</label>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text
											('name',$seller_business->name, array('class'=>'form-control
											form-control1', 'id'=>'txt_company_name','maxlength'=>'120',
											'placeholder'=>'Company Name*' )) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<!--<div class="input-prepend">{!! Form:: text ('established_in',
											$seller_business->established_in, array(
											'class'=>'form-control form-control1',
											'id'=>'txt_established_in', 'maxlength'=>'4',
											'placeholder'=>'Year of establishment*' )) !!}</div>-->
										<div class="normal-select">	
											{!!
												Form::select('established_in',array('' => 'Year of establishment*')
												+ $getYearofEstablished
												,$seller_business->established_in,['class'=>'selectpicker','id'=>'txt_established_in'])
											!!}									
										</div>
									</div>
								</div>

								<div class="col-md-12 padding-none space-top">
									<label for="" class="col-md-12 padding-none">Business Details</label>
									<div class="col-md-12 form-control-fld">
										<div class="input-prepend">{!! Form:: textarea ('address',
											$seller_business->address , array( 'class'=>'form-control
											form-control1',
											'id'=>'txt_company_address','placeholder'=>'Address*',
											'maxlength'=>'350', 'rows'=>'5' )) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="normal-select">{!!
											Form::select('lkp_country_id',array('' => 'Select Country*')
											+ $country , $seller_business->lkp_country_id
											,['class'=>'selectpicker','onChange'=>"getState()",'id'=>'company_country'])
											!!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="normal-select">{!!
											Form::select('lkp_state_id',array('' => 'Select
											State*')+$stateList , $seller_business->lkp_state_id
											,['class'=>'selectpicker','id'=>'company_state']) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="normal-select">{!!
											Form::select('lkp_business_type_id',array('' => 'Select
											Business type*') + $business ,
											$seller_business->lkp_business_type_id
											,['class'=>'selectpicker','id'=>'businessType_id']) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend" id="other-business-txt">{!!
											Form::text ('other_business_type',
											$seller_business->other_business_type,
											array('class'=>'form-control
											form-control1','placeholder'=>'Specify other business type*',
											'id'=>'txt_other_business', 'maxlength'=>'40' )) !!}</div>
									</div>



								</div>
								<div class="col-md-12 padding-none">
								
									<div class="col-md-6 padding-none">
									<label for="" class="col-md-12 padding-none">&nbsp;</label>
									<div class="col-md-12 form-control-fld">
										<div class="input-prepend">
										{!! Form:: text ('pincode',$seller_business->pincode ,array( 'class'=>'form-control numericvalidation form-control1', 'id'=>'txt_company_pincode','placeholder'=>'Pin Code*', 'maxlength'=>'6' )) !!}
										{!! Form:: hidden ('pincode_hidden', $seller_business->principal_place,array( 'class'=>'form-control form-control1','id'=>'hidden_user_pincode' )) !!}
										</div>
									</div>
									</div>
									<div class="col-md-6 padding-none">
									<label for="" class="col-md-12 padding-none">Principal Place of business</label>
									<div class="col-md-12 form-control-fld">
										<div class="input-prepend">
										{!! Form:: text ('principal_place',$seller_business->principal_place , array('class'=>'form-control form-control1','readonly','id'=>'txt_principal_place','placeholder'=>'Ex: Hyderabad *', 'maxlength'=>'30' )) !!}
										</div>
									</div>
									</div>
								</div>
								<div class="col-md-12 padding-none">
									<label for="" class="col-md-12 padding-none">Annual Turn over</label>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text
											('current_turnover', $seller_business->current_turnover
											,array( 'class'=>'form-control numberVal form-control1',
											'id'=>'txt_current_turnover', 'placeholder'=>'Current Year',
											'maxlength'=>'10' )) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text
											('first_year_turnover',
											$seller_business->first_year_turnover,array(
											'class'=>'form-control numberVal form-control1',
											'id'=>'txt_first_yr_turnover', 'placeholder'=>'Year-1',
											'maxlength'=>'10' )) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text
											('second_year_turnover',
											$seller_business->second_year_turnover ,array(
											'class'=>'form-control numberVal form-control1',
											'id'=>'txt_second_yr_turnover', 'placeholder'=>'Year-2',
											'maxlength'=>'10' )) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text
											('third_year_turnover', $seller_business->third_year_turnover
											,array( 'class'=>'form-control numberVal form-control1',
											'id'=>'txt_third_yr_turnover', 'placeholder'=>'Year-3',
											'maxlength'=>'10' )) !!}</div>
									</div>
								</div>

								
								<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Company Information</label>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('employee_strengths',(['' => 'Select Employee Strengths Type*'] + $getEmployeeStrengths), $seller_business->lkp_employee_strength_id, ['class' => 'selectpicker','id' => 'employee_strengths']) !!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('lkp_industry',(['' => 'Select Industry Type*'] + $lkp_industry), $seller_business->lkp_industry_id, ['class' => 'selectpicker','id' => 'lkp_industry']) !!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('lkp_specialities',(['' => 'Select Specialities Type*'] + $getSpecialities), $seller_business->lkp_speciality_id, ['class' => 'selectpicker','id' => 'lkp_specialities']) !!}
											</div>
										</div>
									</div>
								
								<div class="col-md-12 padding-none space-top">
									<label for="" class="col-md-12 padding-none">Official Contact</label>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text
											('contact_firstname', $seller_business->contact_firstname
											,array( 'class'=>'form-control letterValdiation form-control1',
											'id'=>'txt_cp_first_name', 'placeholder'=>'First Name',
											'maxlength'=>'30' )) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text
											('contact_lastname', $seller_business->contact_lastname
											,array('class'=>'form-control letterValdiation form-control1',
											'id'=>'txt_cp_last_name', 'placeholder'=>'Last Name',
											'maxlength'=>'30')) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text
											('contact_designation', $seller_business->contact_designation
											,array('class'=>'form-control letterValdiation form-control1',
											'id'=>'txt_cp_designation',
											'placeholder'=>'Designation','maxlength'=>'30')) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text ('contact_email',
											$seller_business->contact_email ,array('class'=>'form-control
											form-control1','placeholder'=>'*Applicant Email',
											'id'=>'txt_cp_email','maxlength'=>'50')) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text ('contact_mobile',
											$seller_business->contact_mobile
											,array('class'=>'form-control numericvalidation form-control1',
											'placeholder'=>'Mobile Number*',
											'id'=>'txt_cp_mobile','maxlength'=>'10')) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text
											('contact_landline', $seller_business->contact_landline
											,array('class'=>'form-control numericvalidation form-control1',
											'id'=>'txt_cp_landline','maxlength'=>'15',
											'placeholder'=>'Landline Number*') ) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text ('gta',
											$seller_business->gta ,array('class'=>'form-control
											form-control1', 'id'=>'txt_company_gta','maxlength'=>'30',
											'placeholder'=>'GTA Number')) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text
											('service_tax_number', $seller_business->service_tax_number
											,array('class'=>'form-control
											form-control1','id'=>'txt_service_tax_number',
											'placeholder'=>'Service Tax No*', 'maxlength'=>'30')) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text ('tin',
											$seller_business->tin ,array('class'=>'form-control
											form-control1', 'id'=>'txt_company_tin','maxlength'=>'30',
											'placeholder'=>'TIN Number*')) !!}</div>
									</div>

									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text ('pannumber',
											$seller_business->pannumber ,array('class'=>'form-control
											form-control1', 'id'=>'txt_company_pannumber','maxlength'=>'10',
											'placeholder'=>'PAN Number*')) !!}</div>
									</div>
									
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text ('bankname',
											$seller_business->bankname ,array('class'=>'form-control
											form-control1', 'id'=>'txt_company_bank',
											'placeholder'=>'Bank Name', 'maxlength'=>'50')) !!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text
											('branchname',$seller_business->branchname,array('class'=>'form-control
											form-control1','id'=>'txt_company_branch','placeholder'=>'Branch','maxlength'=>'50'))
											!!}</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<!-- div class="input-prepend">

											<span class="btn btn-default btn-file btn-upload"> Logo {!!
												Form:: file ('logo_file', '',array('class'=>'form-control',
												'id'=>'logo_file','placeholder'=>''))!!} </span>

										</div> -->

										<div class="col-xs-12">
											@if($seller_business->logo) <a target="blank"
												href="{{url('uploads/seller/'.$userId.'/logo/'.$seller_business->logo)}}"
												class="form-group pull-left overflow-hide" id="logo_file">{{
												$seller_business->logo }}</a> @else
											<p class="form-group pull-left overflow-hide" id="logo_file"></p>
											@endif
										</div>
									</div>
								</div>
								
								<div class="col-md-12 padding-none space-top">
									<label for="" class="col-md-12 padding-none">Business Description</label>
										<div class="col-md-12 form-control-fld">
											<div class="input-prepend">{!! Form:: textarea ('description_user',
												$seller_business->description, array( 'class'=>'form-control form-control1',
												'id'=>'txt_description','placeholder'=>'Description',
												'maxlength'=>'350', 'rows'=>'5' )) !!}</div>
										</div>
									</div>

									<div class="col-md-12 padding-none space-top">
										<label for="" class="col-md-12 padding-none">User Upload Documents</label>
										<div class="col-md-6">	
											@if($seller_business->user_pic)
												{{--*/ $user_pic_ext = pathinfo($seller_business->user_pic, PATHINFO_EXTENSION) /*--}}
												@if(file_exists(SELLERUPLOADPATH.$seller_business->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$seller_business->user_pic)))	
													<img class="img-responsive" src="{{URL::to(SELLERUPLOADPATH.$seller_business->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$seller_business->user_pic))}}">
												@elseif(file_exists(BUYERUPLOADPATH.$seller_business->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$seller_business->user_pic)))	
													<img class="img-responsive" src="{{URL::to(BUYERUPLOADPATH.$seller_business->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$seller_business->user_pic))}}">
												@endif
											@endif
										</div>
										<div class="col-md-6">	
											@if($seller_business->logo)
												{{--*/ $logo_ext = pathinfo($seller_business->logo, PATHINFO_EXTENSION) /*--}}
												@if(file_exists(SELLERUPLOADPATH.$seller_business->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$seller_business->logo)))
													<img class="img-responsive" src="{{URL::to(SELLERUPLOADPATH.$seller_business->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$seller_business->logo))}}">
												@elseif(file_exists(BUYERUPLOADPATH.$seller_business->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$seller_business->logo)))
													<img class="img-responsive" src="{{URL::to(BUYERUPLOADPATH.$seller_business->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$seller_business->logo))}}">
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
								
								<div class="col-md-12 padding-none space-top">
									<label for="" class="col-md-12 padding-none">Upload Documents</label>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											<span class="btn btn-default btn-file btn-upload"> In
												Corporation{!! Form:: file ('in_corporation_file',
												'',array('class'=>'form-control ',
												'id'=>'txt_in_corporation_file','placeholder'=>'','maxlength'=>'30'))
												!!} </span>
										</div>
										<div class="col-xs-12">
											@if($seller_business->in_corporation_file) <a target="blank"
												href="{{url('uploads/seller/'.$userId.'/'.$seller_business->in_corporation_file)}}"
												class="form-group pull-left overflow-hide"
												id="in_corporation_file">{{
												$seller_business->in_corporation_file }}</a> @else
											<p class="form-group pull-left overflow-hide"
												id="in_corporation_file"></p>
											@endif
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											<span class="btn btn-default btn-file btn-upload"> TIN {!!
												Form:: file ('tin_filepath', '',array('class'=>'form-control
												',
												'id'=>'txt_tin_file','placeholder'=>'','maxlength'=>'30'))
												!!} </span>
										</div>
										<div class="col-xs-12">
											@if($seller_business->tin_filepath) <a target="blank"
												href="{{url('uploads/seller/'.$userId.'/'.$seller_business->tin_filepath)}}"
												class="form-group pull-left overflow-hide" id="tin_filepath">{{
												$seller_business->tin_filepath }}</a> @else
											<p class="form-group pull-left overflow-hide"
												id="tin_filepath"></p>
											@endif
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											<span class="btn btn-default btn-file btn-upload"> Pan Card
												{!! Form:: file ('pancard_filepath', '',
												array('class'=>'form-control ',
												'id'=>'txt_pancard_file','placeholder'=>'','maxlength'=>'30'))
												!!} </span>
										</div>

										<div class="col-xs-12">
											@if($seller_business->pancard_filepath) <a target="blank"
												href="{{url('uploads/seller/'.$userId.'/'.$seller_business->pancard_filepath)}}"
												class="form-group pull-left overflow-hide"
												id="pancard_filepath"> {{ $seller_business->pancard_filepath
												}}</a> @else
											<p class="form-group pull-left overflow-hide"
												id="pancard_filepath"></p>
											@endif
										</div>



									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											<span class="btn btn-default btn-file btn-upload"> GTA {!!
												Form:: file ('gta_filepath',
												'',array('class'=>'form-control',
												'id'=>'txt_gta_file','placeholder'=>'','maxlength'=>'30'))
												!!} </span>
										</div>

										<div class="col-xs-12">
											@if($seller_business->gta_filepath) <a target="blank"
												href="{{url('uploads/seller/'.$userId.'/'.$seller_business->gta_filepath)}}"
												class="form-group pull-left overflow-hide" id="gta_filepath">
												{{ $seller_business->gta_filepath }}</a> @else
											<p class="form-group pull-left overflow-hide"
												id="gta_filepath"></p>
											@endif
										</div>



									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											<span class="btn btn-default btn-file btn-upload"> Sales
												Tax{!! Form:: file ('sales_tax_filepath',
												'',array('class'=>'form-control ',
												'id'=>'txt_sales_tax_file','placeholder'=>'','maxlength'=>'30'))
												!!} </span>
										</div>

										<div class="col-xs-12">
											@if($seller_business->sales_tax_filepath) <a target="blank"
												href="{{url('uploads/seller/'.$userId.'/'.$seller_business->sales_tax_filepath)}}"
												class="form-group pull-left overflow-hide"
												id="sales_tax_filepath">{{
												$seller_business->sales_tax_filepath }}</a> @else
											<p class="form-group pull-left overflow-hide"
												id="sales_tax_filepath"></p>
											@endif
										</div>


									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											<span class="btn btn-default btn-file btn-upload"> Service
												Tax{!! Form:: file ('service_tax_filepath',
												'',array('class'=>'form-control ',
												'id'=>'txt_service_tax_file','placeholder'=>'','maxlength'=>'30'))
												!!} </span>
										</div>

										<div class="col-xs-12">
											@if($seller_business->service_tax_filepath) <a target="blank"
												href="{{url('uploads/seller/'.$userId.'/'.$seller_business->service_tax_filepath)}}"
												class="form-group pull-left overflow-hide"
												id="service_tax_filepath">{{
												$seller_business->service_tax_filepath }} </a> @else
											<p class="form-group pull-left overflow-hide"
												id="service_tax_filepath"></p>
											@endif
										</div>


									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											<span class="btn btn-default btn-file btn-upload"> Central
												Excise {!! Form:: file ('central_excise_filepath',
												'',array('class'=>'form-control ',
												'id'=>'txt_central_excise_file','placeholder'=>'','maxlength'=>'30'))
												!!}</span>
										</div>

										<div class="col-xs-12">
											@if($seller_business->central_excise_filepath) <a
												target="blank"
												href="{{url('uploads/seller/'.$userId.'/'.$seller_business->central_excise_filepath)}}"
												class="form-group pull-left overflow-hide"
												id="central_excise_filepath">
												{{$seller_business->central_excise_filepath}}</a> @else
											<p class="form-group pull-left overflow-hide"
												id="central_excise_filepath"></p>
											@endif
										</div>



									</div>
								</div>


								<div class="col-md-12 padding-none space-top reg_input-check">

									<p class="line-space">
										Services Offered <span class="red">*</span> <span
											class="error" id="error_services"></span>
									
									
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
												<div class="service-div service-icon-div pull-left">
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
														Rail {{$service->service_name}}</a>
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
														Ocean {{$service->service_name}}</a>
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

												<div class="service-div service-icon-div  pull-left hyperlocal-image checkserviceseller">
													<a><img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}"/>
														Hyper Local</a>
													<div class="clearfix"></div>
												</div>
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
											@foreach($allServices as $service) @if($service->group_name
											== 'Road') @if($service->service_crumb_name == 'Vehicle')
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

										<div class="select_service_block border-none">
											<p class="col-md-12 padding-none line-space service-icon-div">Relocation</p>



											<span class="error" id="error_services"></span>
											@foreach($allServices as $service) @if($service->group_name
											== 'Relocation') 
											@if($service->service_crumb_name =='Relocation')
											
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
												{!! Form::select('intracity_locality[]', array('Default' =>
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


								<div class="col-md-4 form-control-fld space-top pull-right">{!!
									Form::submit('Update', array( 'class'=>'btn add-btn
									pull-right', 'id'=>'submitSellercorporate' )) !!}</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
</div>
@include('partials.footer')
</div>
@endsection
<!-- 
<script type="text/javascript">
    $(document).ready(function() {
     
//         $('#states_multiple').multiselect();
//         $('#city_multiple').multiselect();
//         $('#states_multipleSelect').multiselect();
//         $('#locality_multiple').multiselect();

        });
</script>-->
