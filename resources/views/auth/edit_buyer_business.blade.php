@extends('app') @section('content')
@include('partials.page_top_navigation') @if (Session::has('message') &&
Session::get('message')!='')
<div class="flash">
	<p class="text-success col-sm-12 text-center flash-txt alert-success">{{
		Session::get('message') }}</p>
</div>
@endif
<div class="main-container">
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
					<div id="buyer" class="tab-pane fade in active">
						<div class="login-block">
							<div class="login-form login-form-2">
								<div class="center-width">
									{!! Form::open(array('url' =>
									'register/edit_buyer_business/'.$buyer_id, 'id'
									=>'corporate-buyer-form', 'class'=>'form-inline', 'enctype' =>
									'multipart/form-data' )) !!}


									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Name of the
											company </label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('name',
												$buyer_business->name , array( 'id'=>'txt_company_name',
												'class'=>'form-control form-control1', 'maxlength'=>'120',
												'placeholder'=>'Name of the Company*' )) !!}</div>
										</div>
										
										<div class="col-md-6 form-control-fld">
										<!--<div class="input-prepend">{!! Form:: text ('established_in',
											$buyer_business->established_in, array(
											'class'=>'form-control form-control1',
											'id'=>'txt_established_in', 'maxlength'=>'4',
												'placeholder'=>'Year of establishment*' )) !!}</div>-->
											<div class="normal-select">	
												{!!
													Form::select('established_in',array('' => 'Year of establishment*')
													+ $getYearofEstablished
													,$buyer_business->established_in,['class'=>'selectpicker','id'=>'txt_established_in'])
												!!}									
											</div>
									</div>
										
									</div>

									<div class="col-md-12 padding-none space-top">
										<label for="" class="col-md-12 padding-none">Business Details</label>
										<div class="col-md-12 form-control-fld">
											<div class="input-prepend">{!! Form:: textarea ('address',
												$buyer_business->address , array( 'class'=>'form-control form-control1 clsAddress',
												'id'=>'txt_company_address','placeholder'=>'Address*',
												'rows'=>'5' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('lkp_country_id',array('' => 'Select Country*')
												+ $country ,$buyer_business->lkp_country_id
												,['class'=>'selectpicker','onChange'=>"getState()",'id'=>'company_country'])
												!!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!! Form::select('lkp_state_id',
												array('' => 'Select State*') + $state,
												$buyer_business->lkp_state_id,['class'=>'selectpicker','id'=>'company_state'])
												!!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('lkp_business_type_id',array('' => 'Select
												Business type*') + $business
												,$buyer_business->lkp_business_type_id,['class'=>'selectpicker','id'=>'businessType_id'])
												!!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend" id="other-business-txt">{!!
												Form::text ('other_business_type',
												$buyer_business->other_business_type ,
												array('class'=>'form-control
												form-control1','placeholder'=>'Specify other business
												type*', 'id'=>'txt_other_business', 'maxlength'=>'40' )) !!}</div>
										</div>



									</div>
									<div class="col-md-12 padding-none">
									
									<div class="col-md-6 padding-none">
									<label for="" class="col-md-12 padding-none">&nbsp;</label>
										<div class="col-md-12 form-control-fld">
											<div class="input-prepend">
											{!! Form:: text ('pincode',$buyer_business->pincode ,array( 'class'=>'form-control form-control1 clsPinCode', 'id'=>'txt_company_pincode','placeholder'=>'Pin Code*' )) !!}
											{!! Form:: hidden ('pincode_hidden', $buyer_business->principal_place,array( 'class'=>'form-control form-control1','id'=>'hidden_user_pincode' )) !!}
											</div>
										</div>
										</div>
										
									<div class="col-md-6 padding-none">
										<label for="" class="col-md-12 padding-none">Principal Place of business</label>
										<div class="col-md-12 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('principal_place', $buyer_business->principal_place ,
												array( 'class'=>'form-control
												form-control1','id'=>'txt_principal_place',
												'placeholder'=>'Ex: Hyderabad *','readonly', 'maxlength'=>'30' )) !!}</div>
										</div>
										
										</div>
										
									</div>
									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Annual Turn over</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('current_turnover',
												$buyer_business->current_turnover,array(
												'class'=>'form-control numberVal form-control1',
												'id'=>'txt_current_turnover', 'placeholder'=>'Current Year',
												'maxlength'=>'10' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('first_year_turnover', $buyer_business->first_year_turnover
												,array( 'class'=>'form-control numberVal form-control1',
												'id'=>'txt_first_yr_turnover', 'placeholder'=>'Year-1',
												'maxlength'=>'10' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('second_year_turnover',$buyer_business->second_year_turnover
												,array( 'class'=>'form-control numberVal form-control1',
												'id'=>'txt_second_yr_turnover', 'placeholder'=>'Year-2',
												'maxlength'=>'10' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('third_year_turnover', $buyer_business->third_year_turnover
												,array( 'class'=>'form-control numberVal form-control1',
												'id'=>'txt_third_yr_turnover', 'placeholder'=>'Year-3',
												'maxlength'=>'10' )) !!}</div>
										</div>
									</div>

									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Company Information</label>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('employee_strengths',(['' => 'Select Employee Strengths Type*'] + $getEmployeeStrengths), $buyer_business->lkp_employee_strength_id, ['class' => 'selectpicker','id' => 'employee_strengths']) !!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('lkp_industry',(['' => 'Select Industry Type*'] + $lkp_industry), $buyer_business->lkp_industry_id, ['class' => 'selectpicker','id' => 'lkp_industry']) !!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">
											{!! Form::select('lkp_specialities',(['' => 'Select Specialities Type*'] + $getSpecialities), $buyer_business->lkp_speciality_id, ['class' => 'selectpicker','id' => 'lkp_specialities']) !!}
											</div>
										</div>
									</div>
									
									
									<div class="col-md-12 padding-none space-top">
										<label for="" class="col-md-12 padding-none">Official Contact</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_firstname', $buyer_business->contact_firstname
												,array( 'class'=>'form-control letterValdiation form-control1',
												'id'=>'txt_cp_first_name', 'placeholder'=>'First Name',
												'maxlength'=>'30' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_lastname', $buyer_business->contact_lastname
												,array('class'=>'form-control letterValdiation form-control1',
												'id'=>'txt_cp_last_name', 'placeholder'=>'Last Name',
												'maxlength'=>'30')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_designation', $buyer_business->contact_designation
												,array('class'=>'form-control letterValdiation form-control1',
												'id'=>'txt_cp_designation',
												'placeholder'=>'Designation','maxlength'=>'30')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_email',$buyer_business->contact_email,array('class'=>'form-control
												form-control1','placeholder'=>'Applicant Email*',
												'id'=>'txt_cp_email','maxlength'=>'50')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_mobile',$buyer_business->contact_mobile,array('class'=>'form-control numericvalidation
												form-control1', 'id'=>'txt_cp_mobile',
												'placeholder'=>'Mobile Number*','maxlength'=>'10')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_landline', $buyer_business->contact_landline
												,array('class'=>'form-control numericvalidation form-control1',
												'id'=>'txt_cp_landline','maxlength'=>'15',
												'placeholder'=>'Landline Number*') ) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('gta',
												$buyer_business->gta ,array('class'=>'form-control
												form-control1', 'id'=>'txt_company_gta','maxlength'=>'30',
												'placeholder'=>'GTA Number')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('service_tax_number', $buyer_business->service_tax_number
												,array('class'=>'form-control form-control1 clsServiceTaxno',
												 'id'=>'txt_service_tax_number',
												'placeholder'=>'Service Tax No*')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('tin',
												$buyer_business->tin ,array('class'=>'form-control
												form-control1 clsTinNumber',
												'id'=>'txt_company_tin','placeholder'=>'TIN Number*')) !!}</div>
										</div>

										
										<div class="col-md-6 form-control-fld">
										<div class="input-prepend">{!! Form:: text ('pannumber',
											$buyer_business->pannumber ,array('class'=>'form-control
											form-control1 clsPancardNo', 'id'=>'txt_company_pannumber',
											'placeholder'=>'PAN Number*')) !!}</div>
									</div>
										
										
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('bankname',
												$buyer_business->bankname ,array('class'=>'form-control form-control1 clsBankName', 'id'=>'txt_company_bank', 'placeholder'=>'Bank Name')) !!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												{!! Form:: text ('branchname',
												$buyer_business->branchname ,array('class'=>'form-control form-control1 clsBranchName','id'=>'txt_company_branch','placeholder'=>'Branch','maxlength'=>'50')) !!}
											</div>
										</div>
									</div>
									
									<div class="col-md-12 padding-none space-top">
									<label for="" class="col-md-12 padding-none">Business Description</label>
										<div class="col-md-12 form-control-fld">
											<div class="input-prepend">{!! Form:: textarea ('description_user',
												$buyer_business->description, array( 'class'=>'form-control form-control1 clsBusiDescription',
												'id'=>'txt_description','placeholder'=>'Description', 'rows'=>'5' )) !!}</div>
										</div>
									</div>
									
									<div class="col-md-12 padding-none space-top">
										<label for="" class="col-md-12 padding-none">User Upload Documents</label>
										<div class="col-md-6">	
											@if($buyer_business->user_pic)
												{{--*/ $user_pic_ext = pathinfo($buyer_business->user_pic, PATHINFO_EXTENSION) /*--}}
												@if(file_exists(BUYERUPLOADPATH.$buyer_business->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$buyer_business->user_pic)))
													<img class="img-responsive" src="{{URL::to(BUYERUPLOADPATH.$buyer_business->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$buyer_business->user_pic))}}">
												@elseif(file_exists(SELLERUPLOADPATH.$buyer_business->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$buyer_business->user_pic)))
													<img class="img-responsive" src="{{URL::to(SELLERUPLOADPATH.$buyer_business->user_id.'/'.str_replace('.'.$user_pic_ext,'_124_73.'.$user_pic_ext,$buyer_business->user_pic))}}">
												@endif	
											@endif
										</div>
										<div class="col-md-6">	
											@if($buyer_business->logo)
												{{--*/ $logo_ext = pathinfo($buyer_business->logo, PATHINFO_EXTENSION) /*--}}
												@if((BUYERUPLOADPATH.$buyer_business->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$buyer_business->logo)))
													<img class="img-responsive" src="{{URL::to(BUYERUPLOADPATH.$buyer_business->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$buyer_business->logo))}}">
												@elseif((SELLERUPLOADPATH.$buyer_business->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$buyer_business->logo)))
													<img class="img-responsive" src="{{URL::to(SELLERUPLOADPATH.$buyer_business->user_id.'/'.str_replace('.'.$logo_ext,'_124_73.'.$logo_ext,$buyer_business->logo))}}">
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
													'',array('class'=>'form-control margin-bottom ',
													'id'=>'txt_in_corporation_file','placeholder'=>'','maxlength'=>'30'))
													!!} </span>
												<div class="clearfix"></div>
											</div>

											<div class="col-xs-12">
												@if($buyer_business->in_corporation_file) <a target="blank"
													href="{{url('/uploads/buyer/'.$userId.'/'.$buyer_business->in_corporation_file)}}"
													class="form-group pull-left overflow-hide"
													id="in_corporation_file">{{substr($buyer_business->in_corporation_file,0,14)}}...</a>
												@else
												<p class="form-group pull-left overflow-hide"
													id="in_corporation_file"></p>
												@endif
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file btn-upload"> TIN {!!
													Form:: file ('tin_filepath',
													'',array('class'=>'form-control',
													'id'=>'txt_tin_file','placeholder'=>'','maxlength'=>'30'))
													!!} </span>
											</div>
											<div class="col-xs-12">
												@if($buyer_business->tin_filepath) <a target="blank"
													href="{{url('/uploads/buyer/'.$userId.'/'.$buyer_business->tin_filepath)}}"
													class="form-group pull-left overflow-hide"
													id="tin_filepath">
													{{substr($buyer_business->tin_filepath,0,14)}}...</a> @else
												<p class="form-group pull-left overflow-hide"
													id="tin_filepath"></p>
												@endif
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file btn-upload"> Pan Card
													{!! Form:: file ('pancard_filepath',
													'',array('class'=>'form-control margin-bottom ',
													'id'=>'txt_pancard_file','placeholder'=>'','maxlength'=>'30'))
													!!} </span>
											</div>

											<div class="col-xs-12">
												@if($buyer_business->pancard_filepath) <a target="blank"
													href="{{url('/uploads/buyer/'.$userId.'/'.$buyer_business->pancard_filepath)}}"
													class="form-group pull-left overflow-hide"
													id="pancard_filepath">{{substr($buyer_business->pancard_filepath,0,14)}}....</a>
												@else
												<p class="form-group pull-left overflow-hide"
													id="pancard_filepath"></p>
												@endif
											</div>



										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file btn-upload"> GTA {!!
													Form:: file ('gta_filepath',
													'',array('class'=>'form-control margin-bottom ',
													'id'=>'txt_gta_file','placeholder'=>'','maxlength'=>'30'))
													!!} </span>
											</div>

											<div class="col-xs-12">
												@if($buyer_business->gta_filepath) <a target="blank"
													href="{{url('/uploads/buyer/'.$userId.'/'.$buyer_business->gta_filepath)}}"
													class="form-group pull-left overflow-hide"
													id="gta_filepath">{{substr($buyer_business->gta_filepath,0,14)}}...</a>
												@else
												<p class="form-group pull-left overflow-hide"
													id="gta_filepath"></p>
												@endif
											</div>



										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file btn-upload"> Sales
													Tax{!! Form:: file ('sales_tax_filepath',
													'',array('class'=>'form-control margin-bottom ',
													'id'=>'txt_sales_tax_file','placeholder'=>'','maxlength'=>'30'))
													!!} </span>
											</div>

											<div class="col-xs-12">
												@if($buyer_business->sales_tax_filepath) <a target="blank"
													href="{{url('/uploads/buyer/'.$userId.'/'.$buyer_business->sales_tax_filepath)}}"
													class="form-group pull-left overflow-hide"
													id="sales_tax_filepath">{{substr($buyer_business->sales_tax_filepath,0,14)}}...</a>
												@else
												<p class="form-group pull-left overflow-hide"
													id="sales_tax_filepath"></p>
												@endif
											</div>


										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file btn-upload"> Service
													Tax{!! Form:: file ('service_tax_filepath',
													'',array('class'=>'form-control margin-bottom ',
													'id'=>'txt_service_tax_file','placeholder'=>'','maxlength'=>'30'))
													!!} </span>
											</div>

											<div class="col-xs-12">
												@if($buyer_business->service_tax_filepath) <a target="blank"
													href="{{url('/uploads/buyer/'.$userId.'/'.$buyer_business->service_tax_filepath)}}"
													class="form-group pull-left overflow-hide"
													id="service_tax_filepath">
													{{substr($buyer_business->service_tax_filepath,0,14)}}...</a>
												@else
												<p class="form-group pull-left overflow-hide"
													id="service_tax_filepath"></p>
												@endif
											</div>


										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												<span class="btn btn-default btn-file btn-upload"> Central
													Excise {!! Form:: file ('central_excise_filepath',
													'',array('class'=>'form-control margin-bottom ',
													'id'=>'txt_central_excise_file','placeholder'=>'','maxlength'=>'30'))
													!!}</span>
											</div>

											<div class="col-xs-12">
												@if($buyer_business->central_excise_filepath) <a
													target="blank"
													href="{{url('/uploads/buyer/'.$userId.'/'.$buyer_business->central_excise_filepath)}}"
													class="form-group pull-left overflow-hide"
													id="central_excise_filepath">{{substr($buyer_business->central_excise_filepath,0,14)}}...</a>
												@else
												<p class="form-group pull-left overflow-hide"
													id="central_excise_filepath"></p>
												@endif
											</div>



										</div>
									</div>
									<div class="col-md-4 form-control-fld space-top pull-right">
										{!! Form::submit('Update', array( 'class'=>'btn
										add-btn-2','id'=>'submitBuyercorporate' )) !!}</div>


									{!! Form::close() !!}

								</div>
							</div>
						</div>
					</div>


				</div>
			</div>
			<div class="clearfix"></div>
		</div>

	</div>
	</div>
</div>
@include('partials.footer')
</div>


@endsection
