@extends('app') @section('content')
@include('partials.page_top_navigation')
<div class="clearfix"></div>
<div class="main">
	<div class="container">
		<div class="login-head">
			<h1>
				Thank You For Joining  <span>LOGISTIKS.COM</span>
				<p>Tell us a little about your business</p>
			</h1>
		</div>

	    @if (Session::has('success_message'))
		<div class="flash ">
			<p class="text-success col-sm-12 text-center flash-txt alert-success">{{ Session::get('success_message') }}</p>
		</div>
		@endif
		@if (Session::has('error_message'))
		<div class="flash ">
			<p class="text-alert col-sm-12 text-center flash-txt alert-danger">{{ Session::get('error_message') }}</p>
		</div>
		@endif

		<div class="home-block home-block-login">
			<div class="tabs">
				<ul class="nav nav-tabs">
				    <li class="active"><a href="#buyer">Buyer</a></li>
				    <li><a class="roleSelector" data-value = "2" data-selection="seller">Seller</a></li>
				    <li><a class="roleSelector" data-value = "2" data-selection="both">Both</a></li>
				    
					</ul>

				  <div class="tab-content">
					<div id="buyer" class="tab-pane fade in active">
						<div class="login-block">
							<div class="login-form login-form-2">
								<div class="center-width">
								{!! Form::open(array('url' => 'buyerregister', 'id'=>'buyer-details-form', 'class'=>'form-inline margin-top','enctype' => 'multipart/form-data' )) !!}

										
						<div class="col-md-12 padding-none">
							<label for="" class="col-md-12 padding-none">Name of the Person</label>
							<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
									{!! Form:: text ('firstname', '', array( 'class'=>'form-control letterValdiation form-control1','id'=>'txt_user_first_name','placeholder'=>'First Name*', 'maxlength'=>'30' )) !!}
								</div>
							</div>
							<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
									{!!Form:: text ('lastname', '', array( 'class'=>'form-control letterValdiation form-control1', 'id'=>'txt_user_last_name', 'placeholder'=>'Last Name*', 'maxlength'=>'30' )) !!}
								</div>
							</div>
						</div>
						<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
								{!! Form:: text ('pincode', '',array( 'class'=>'form-control form-control1 numericvalidation','id'=>'txt_user_pincode', 'placeholder'=>'Pincode*','maxlength'=>'6' )) !!}
								{!! Form:: hidden ('pincode_hidden', '',array( 'class'=>'form-control form-control1','id'=>'hidden_user_pincode' )) !!}
								</div>
							</div>
						<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
									{!!Form:: text ('location', '', array( 'class'=>'form-control form-control1', 'id'=>'location', 'placeholder'=>'Location' )) !!}
								</div>
							</div>
							
						<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
									{!!Form:: text ('city', '', array( 'class'=>'form-control form-control1', 'id'=>'city', 'placeholder'=>'City' )) !!}
								</div>
							</div>
						<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
									{!!Form:: text ('district', '', array( 'class'=>'form-control form-control1', 'id'=>'district', 'placeholder'=>'District' )) !!}
								</div>
							</div>
							<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
									{!!Form:: text ('state', '', array( 'class'=>'form-control form-control1', 'id'=>'state', 'placeholder'=>'State' )) !!}
								</div>
							</div>
						<div class="col-md-12 padding-none">
						<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
									{!!Form:: text ('address1', '', array( 'class'=>'form-control form-control1', 'id'=>'address1', 'placeholder'=>'Address Line 1*', 'maxlength'=>'100' )) !!}
								</div>
							</div>
						<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
									{!!Form:: text ('address2', '', array( 'class'=>'form-control form-control1', 'id'=>'address2', 'placeholder'=>'Address Line 2*', 'maxlength'=>'100' )) !!}
								</div>
							</div>	
						</div>
						<div class="col-md-12 padding-none space-top">
							<label for="" class="col-md-12 padding-none">Personal Details</label>
							<div class="col-md-12 form-control-fld">
								<div class="input-prepend">
									{!! Form::textarea('address','',array( 'class'=>'form-control form-control1 clsAddress', 'id'=>'txt_user_address','placeholder'=>'Address*', 'cols'=>'48', 'rows'=>'3','maxlength'=>'350' )) !!}
		
								</div>
							</div>
							<!--<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
								{!! Form:: text ('pincode', '',array( 'class'=>'form-control form-control1 numericvalidation','id'=>'txt_user_pincodeOld', 'placeholder'=>'Pincode*','maxlength'=>'6' )) !!}
								{!! Form:: hidden ('pincode_hidden', '',array( 'class'=>'form-control form-control1','id'=>'hidden_user_pincode' )) !!}
								</div>
							</div>-->
                                                                                                
                            <div class="col-md-6 form-control-fld">
								<div class="input-prepend">
								{!! Form:: text('principal_place', '', array( 'class'=>'form-control form-control1','readonly','id'=>'principal_place','placeholder'=>'Principal Place of business' )) !!}
								</div>
							</div>                                                                                                
							
							<div class="clearfix"></div>
							<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
								{!! Form:: text ('mobile',$user_phone, array( 'class'=>'form-control  form-control1 clsMobileno','id'=>'txt_user_mobile', 'placeholder'=>'Mobile Number*', 'maxlength'=>'10' )) !!}
								</div>
							</div>
							<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
									{!!Form:: text ('alternate_mobile_number', '', array( 'class'=>'form-control form-control1', 'id'=>'alternate_mobile_number', 'placeholder'=>'Alternate Mobile number', 'maxlength'=>'10' )) !!}
								</div>
							</div>	
                                                                            
                          	<div class="col-md-6 form-control-fld">
								<div class="input-prepend">
								{!! Form:: text ('landline','', array( 'class'=>'form-control numericvalidation form-control1','id'=>'txt_user_landline', 'placeholder'=>'Landline Number','maxlength'=>'15' )) !!}
									
								</div>
							</div>
												
												<div class="clearfix"></div>
												
                                                                                                <div class="col-md-6 form-control-fld">
													<div class="input-prepend">
													{!! Form:: text('contact_email', $user_email, array( 'class'=>'form-control form-control1 clsEmailAddr','id'=>'txt_user_email_id','placeholder'=>'E-Mail Id*' )) !!}
													</div>
												</div>
												
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
														{!!Form:: text ('alternate_email', '', array( 'class'=>'form-control form-control1', 'id'=>'alternate_email', 'placeholder'=>'Alternate E Mail ID' )) !!}
													</div>
												</div>
                                                                                                
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
													{!! Form:: text ('company_name','', array( 'class'=>'form-control form-control1','id'=>'company_name', 'placeholder'=>'Company Name','maxlength'=>'50' )) !!}
													</div>
												</div>
												<div class="clearfix"></div>
												<div class="col-md-6 form-control-fld">
													 <div class="normal-select">
													{!! Form::select('lkp_industry',(['' => 'Select Industry Type*'] + $lkp_industry), null, ['class' => 'selectpicker','id' => 'lkp_industry']) !!}
													</div>
												</div>
												<div class="col-md-6 form-control-fld">
													 <div class="normal-select">
														<select name="id_proof" id="id_proof">
														<option value="">ID Proof*</option>
														<option value="1">Adhar card</option>
														<option value="2">Driving Lic</option>
														<option value="3">Passport</option>
														<option value="4">Pancard</option>
														<option value="5">Voter Id</option>
													  </select>
											  </div>
											  </div>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
													{!! Form:: text ('pannumber','',array('class'=>'form-control alphanumeric_strVal form-control1','id'=>'txt_company_pannumber','placeholder'=>'PAN Number*','maxlength'=>'10')) !!}
													</div>
												</div>
												<div class="col-md-12 padding-none space-top">
												<label for="" class="col-md-12 padding-none">Business Description</label>
													<div class="col-md-12 form-control-fld">
														<div class="input-prepend">{!! Form:: textarea ('description_user',
															'', array( 'class'=>'form-control form-control1',
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
												
											</div>
											<div class="col-md-4 form-control-fld space-top pull-right">
											{!! Form::submit('Submit', array( 'class'=>'btn add-btn-2','onclick'=>'return buyerRegistration();')) !!}
											</div>
											
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
