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
	  					</ul>
						  <div class="tab-content">
							<div id="buyer" class="tab-pane fade in active">
								<div class="login-block">
									<div class="login-form login-form-2">
										<div class="center-width">
										{!! Form::open(array('url' => 'switch_buyer', 'id'	=>'buyer-details-form', 'class'=>'form-inline margin-top' )) !!}

										
											<div class="col-md-12 padding-none">
												<label for="" class="col-md-12 padding-none">Name of the Person</label>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
													{!! Form:: text ('firstname', '', array( 'class'=>'form-control form-control1','id'=>'txt_user_first_name','placeholder'=>'First Name*', 'maxlength'=>'30' )) !!}

													</div>
												</div>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
														{!! Form:: text ('lastname', '', array( 'class'=>'form-control form-control1', 'id'=>'txt_user_last_name', 'placeholder'=>'Last Name*', 'maxlength'=>'30' )) !!}
													</div>
												</div>
											</div>
											<div class="col-md-12 padding-none space-top">
												<label for="" class="col-md-12 padding-none">Personal Details</label>
												<div class="col-md-12 form-control-fld">
													<div class="input-prepend">
															{!! Form::textarea('address','',array( 'class'=>'form-control form-control1', 'id'=>'txt_user_address','placeholder'=>'Address*', 'cols'=>'48', 'rows'=>'3','maxlength'=>'350' )) !!}
							
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
														{!! Form:: text ('landline','', array( 'class'=>'form-control form-control1','id'=>'txt_user_landline', 'placeholder'=>'Landline Number','maxlength'=>'15' )) !!}
														
													</div>
												</div>
												<div class="clearfix"></div>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
														{!! Form:: text ('mobile','', array( 'class'=>'form-control form-control1','id'=>'txt_user_mobile', 'placeholder'=>'Mobile Number*','maxlength'=>'10' )) !!}
													</div>
												</div>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
													{!! Form:: text('contact_email','', array( 'class'=>'form-control form-control1','id'=>'txt_user_email_id','placeholder'=>'E-Mail Id*' )) !!}
													</div>
												</div>
												<div class="clearfix"></div>
												<div class="col-md-6 form-control-fld">
													<div class="input-prepend">
													{!! Form:: text('principal_place', '', array( 'class'=>'form-control form-control1','readonly','id'=>'principal_place','placeholder'=>'Principal Place of business' )) !!}
													</div>
												</div>
											</div>
											<div class="col-md-4 form-control-fld space-top pull-right">
											{!! Form::submit('Submit', array( 'class'=>'btn add-btn-2',
					'onclick'=>'return buyerRegistration();')) !!}
												
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
