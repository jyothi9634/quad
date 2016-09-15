@extends('app') @section('content')
@include('partials.page_top_navigation') @if (Session::has('message') &&
Session::get('message')!='')
<div class="flash">
	<p class="text-success col-sm-12 text-center flash-txt alert-success">{{
		Session::get('message') }}</p>
</div>
@endif

<div class="main-container">
	<div class="container">
		<div class="login-head">
			<h1>
				Thank You For Joining <span>LOGISTIKS.COM</span>
				<p>Tell us a little about your business</p>
			</h1>
		</div>
		<div class="home-block home-block-login">
			<div class="tabs">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#buyer">Buyer</a></li>
					<li><a class="roleSelector" data-value="2">Seller</a></li>
					<li><a class="roleSelector" data-value="2">Both</a></li>

				</ul>
				<div class="tab-content">
					<div id="buyer" class="tab-pane fade in active">
						<div class="login-block">
							<div class="login-form login-form-2">
								<div class="center-width">
									{!! Form::open(array('url' => 'register/buyer_business', 'id'
									=> 'corporate-buyer-form', 'class'=>'form-inline margin-top',
									'enctype' => 'multipart/form-data' )) !!}


									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Name of the
											company </label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('name', '',
												array( 'id'=>'txt_company_name', 'class'=>'form-control
												form-control1', 'placeholder'=>'Name of the Company*',
												'maxlength'=>'120' )) !!}</div>
										</div>
									
				
									<div class="col-md-6 form-control-fld">
										<!--<div class="input-prepend">
											<!--{!! Form:: text ('established_in',
											'', array( 'class'=>'form-control form-control1',
											'placeholder'=>'Year of establishment*',
											'id'=>'txt_established_in', 'maxlength'=>'4' )) !!}-->
										<div class="normal-select">	
											{!!
												Form::select('established_in',array('' => 'Year of establishment*')
												+ $getYearofEstablished
												,null,['class'=>'selectpicker','id'=>'txt_established_in'])
											!!}									
										</div>
									</div>
									</div>
									<div class="col-md-12 padding-none space-top">
										<label for="" class="col-md-12 padding-none">Business Details</label>
										<div class="col-md-12 form-control-fld">
											<div class="input-prepend">{!! Form:: textarea ('address',
												'', array( 'class'=>'form-control form-control1 clsBusiDescription',
												'id'=>'txt_company_address','placeholder'=>'Address*',
												'maxlength'=>'350', 'rows'=>'5' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('lkp_country_id',array('' => 'Select Country*')
												+ $country
												,null,['class'=>'selectpicker','onChange'=>"getState()",'id'=>'company_country'])
												!!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('lkp_state_id',array('' => 'Select State*')
												,null,['class'=>'selectpicker','id'=>'company_state']) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="normal-select">{!!
												Form::select('lkp_business_type_id',array('' => 'Select
												Business type*') + $business
												,null,['class'=>'selectpicker','id'=>'businessType_id']) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend" id="other-business-txt">{!!
												Form::text ('other_business_type', '',
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
											{!! Form:: text ('pincode','',array( 'class'=>'form-control form-control1 clsPinCode','id'=>'txt_company_pincode', 'placeholder'=>'Pin Code*','maxlength'=>'6' )) !!}
											{!! Form:: hidden ('pincode_hidden', '',array( 'class'=>'form-control form-control1','id'=>'hidden_user_pincode' )) !!}
												
												</div>
										</div>
										</div>
										<div class="col-md-6 padding-none">
										<label for="" class="col-md-12 padding-none">Principal Place of business</label>
										<div class="col-md-12 form-control-fld">
											<div class="input-prepend">
											{!! Form:: text('principal_place', '', array( 'class'=>'form-control form-control1','readonly','id'=>'txt_principal_place','placeholder'=>'Ex: Hyderabad *', 'maxlength'=>'30' )) !!}
											</div>
										</div>
										</div>
									</div>
									<div class="col-md-12 padding-none">
										<label for="" class="col-md-12 padding-none">Annual Turn over</label>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('current_turnover', '',array( 'class'=>'form-control numberVal
												form-control1', 'id'=>'txt_current_turnover',
												'placeholder'=>'Current Year', 'maxlength'=>'10' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('first_year_turnover', '',array( 'class'=>'form-control numberVal
												form-control1', 'id'=>'txt_first_yr_turnover',
												'placeholder'=>'Year-1', 'maxlength'=>'10' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('second_year_turnover', '',array( 'class'=>'form-control numberVal
												form-control1', 'id'=>'txt_second_yr_turnover',
												'placeholder'=>'Year-2', 'maxlength'=>'10' )) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('third_year_turnover', '',array( 'class'=>'form-control numberVal
												form-control1', 'id'=>'txt_third_yr_turnover',
												'placeholder'=>'Year-3', 'maxlength'=>'10' )) !!}</div>
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
												('contact_lastname', '',array('class'=>'form-control letterValdiation
												form-control1', 'id'=>'txt_cp_last_name',
												'placeholder'=>'Last Name', 'maxlength'=>'30')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_designation', '',array('class'=>'form-control letterValdiation
												form-control1', 'id'=>'txt_cp_designation',
												'placeholder'=>'Designation','maxlength'=>'30')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_email',$user_email,array('class'=>'form-control
												form-control1 clsEmailAddr','placeholder'=>'*Applicant Email',
												'id'=>'txt_cp_email','maxlength'=>'50')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_mobile',$user_phone,array('class'=>'form-control 
												form-control1 clsMobileno', 'id'=>'txt_cp_mobile',
												'placeholder'=>'Mobile Number*','maxlength'=>'10')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('contact_landline','',array('class'=>'form-control clsLandline
												form-control1', 'id'=>'txt_cp_landline',
												'placeholder'=>'Landline Number*','maxlength'=>'15') ) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('gta',
												'',array('class'=>'form-control form-control1',
												'id'=>'txt_company_gta','maxlength'=>'30',
												'placeholder'=>'GTA Number')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text
												('service_tax_number','',array('class'=>'form-control
												form-control1', 'id'=>'txt_service_tax_number',
												'placeholder'=>'Service Tax No*', 'maxlength'=>'30')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('tin',
												'',array('class'=>'form-control form-control1',
												'id'=>'txt_company_tin','placeholder'=>'TIN Number*',
												'maxlength'=>'30')) !!}</div>
										</div>

										<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
										{!! Form:: text ('pannumber','',array('class'=>'form-control alphanumeric_strVal form-control1','id'=>'txt_company_pannumber','placeholder'=>'PAN Number*','maxlength'=>'10')) !!}
										</div>
										</div>
										
										
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text ('bankname', '',array('class'=>'form-control form-control1 clsBankName',
												'id'=>'txt_company_bank', 'placeholder'=>'Bank Name', 'maxlength'=>'50')) !!}</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">{!! Form:: text('branchname','',array('class'=>'form-control form-control1 clsBranchName','id'=>'txt_company_branch','placeholder'=>'Branch','maxlength'=>'50'))
												!!}</div>
										</div>
									</div>
									
									<div class="col-md-12 padding-none space-top">
									<label for="" class="col-md-12 padding-none">Business Description</label>
										<div class="col-md-12 form-control-fld">
											<div class="input-prepend">{!! Form:: textarea ('description_user',
												'', array( 'class'=>'form-control form-control1 clsBusiDescription',
												'id'=>'txt_description','placeholder'=>'Description',
												'maxlength'=>'350', 'rows'=>'5' )) !!}</div>
										</div>
									</div>
									<div class="col-md-12 padding-none space-top">
										<label for="" class="col-md-12 padding-none">User Upload Documents</label>
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
													Corporation {!! Form:: file
													('in_corporation_file','',array('class'=>'fileInput',
													'id'=>'txt_in_corporation_file','placeholder'=>'','maxlength'=>'50'))
													!!} </span>
											</div>
											<div class="col-xs-12">
												<p class="form-group pull-left overflow-hide"
													id="in_corporation_file">&nbsp;</p>
											</div>
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
											<div class="col-xs-12">
												<p class="form-group pull-left overflow-hide"
													id="tin_filepath">&nbsp;</p>
											</div>
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

											<div class="col-xs-12">
												<p class="form-group pull-left overflow-hide"
													id="pancard_filepath">&nbsp;</p>
											</div>



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

											<div class="col-xs-12">
												<p class="form-group pull-left overflow-hide"
													id="gta_filepath">&nbsp;</p>
											</div>



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

											<div class="col-xs-12">
												<p class="form-group pull-left overflow-hide"
													id="sales_tax_filepath">&nbsp;</p>
											</div>


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

											<div class="col-xs-12">
												<p class="form-group pull-left overflow-hide"
													id="service_tax_filepath">&nbsp;</p>
											</div>


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

											<div class="col-xs-12">
												<p class="form-group pull-left overflow-hide"
													id="central_excise_filepath">&nbsp;</p>
											</div>



										</div>
									</div>
									<div class="col-md-4 form-control-fld space-top pull-right">
										{!! Form::submit('Submit', array( 'class'=>'btn
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
@include('partials.footer')
</div>


@endsection
