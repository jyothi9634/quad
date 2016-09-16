@extends('app') @section('content')
@include('partials.page_top_navigation')
<div class="clearfix"></div>
<div class="login-head">
			<h1>
				Welcome to  <span>LOGISTIKS.COM</span>
				<p>Please fill the form below to register for Membership</p>
			</h1>
		</div>
<div class="main">
	<div class="container reg_crumb">
		
		<div class="home-block home-block-login">
			<div class="tabs">

				<div class="tab-content">
					{!! Form::open(array('url' => '', 'name' => 'individual-form', 'id' => 'individual-form', 'class'=>'form-inline margin-top' )) !!}
						<div class="login-block">
							<div class="login-form login-form-2">
								
								
								<div class="center-width">
									<div class="col-md-12 padding-none">
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												{!! Form:: text ('firstname', '', array( 'class'=>'form-control letterValdiation form-control1','id'=>'firstname','placeholder'=>'First Name*', 'maxlength'=>'30' )) !!}
											</div>
										</div>
										<div class="col-md-6 form-control-fld">
											<div class="input-prepend">
												{!!Form:: text ('lastname', '', array( 'class'=>'form-control letterValdiation form-control1', 'id'=>'lastname', 'placeholder'=>'Last Name*', 'maxlength'=>'30' )) !!}
											</div>
										</div>
									</div>
								</div>
								
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('pincode', '', array( 'class'=>'form-control form-control1','id'=>'pincode','placeholder'=>'Pincode*', 'maxlength'=>'6' )) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!!Form:: text ('location', '', array( 'class'=>'form-control form-control1', 'id'=>'location', 'placeholder'=>'Location*', 'maxlength'=>'30' )) !!}
										</div>
									</div>
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('city', '', array( 'class'=>'form-control form-control1','id'=>'city','placeholder'=>'City*', 'maxlength'=>'30' )) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!!Form:: text ('state', '', array( 'class'=>'form-control form-control1', 'id'=>'state', 'placeholder'=>'State*', 'maxlength'=>'30' )) !!}
										</div>
									</div>
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('address1', '', array( 'class'=>'form-control form-control1','id'=>'address1','placeholder'=>'Address Line 1*', 'maxlength'=>'150' )) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('address2', '', array( 'class'=>'form-control form-control1','id'=>'address2','placeholder'=>'Address Line 2*', 'maxlength'=>'150' )) !!}
										</div>
									</div>
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('landline', '', array( 'class'=>'form-control form-control1','id'=>'landline','placeholder'=>'Landline number*', 'maxlength'=>'12' )) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('phone', '', array( 'class'=>'form-control form-control1','id'=>'phone','placeholder'=>'Mobile number*', 'maxlength'=>'10','readonly' )) !!}
										</div>
									</div>
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('alternative_mobile', '', array( 'class'=>'form-control form-control1','id'=>'alternative_mobile','placeholder'=>'Alternative Mobile Number', 'maxlength'=>'10' )) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('email', '', array( 'class'=>'form-control form-control1','id'=>'email','placeholder'=>'Email ID*', 'maxlength'=>'55','readonly' )) !!}
										</div>
									</div>
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('alternative_email', '', array( 'class'=>'form-control form-control1','id'=>'alternative_email','placeholder'=>'Alternate E Mail ID', 'maxlength'=>'55' )) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="normal-select">
											<select name="id_proof" id="id_proof" class="form-control form-control1">
												<option value="">ID Proof*</option>
												<option value="1">Adhar card</option>
												<option value="2">Driving Lic</option>
												<option value="3">Passport</option>
												<option value="4">Pancard</option>
												<option value="5">Voter Id</option>
											</select>
										</div>
									</div>
								</div>	
							
								<div class="col-md-6 form-control-fld">
									<div class="input-prepend">
										{!! Form:: text ('id_proof_value', '', array( 'class'=>'form-control form-control1','id'=>'id_proof_value','placeholder'=>'ID Proof Value*', 'maxlength'=>'55' )) !!}
									</div>
								</div>
								<div class="col-md-12 form-control-fld">
								<br>
									<div class="col-md-8 form-control-fld">
										<input type="checkbox" id="cdbaccept" name="cdbaccept"><span class="lbl padding-8"></span> Logistiks.com Terms and conditions
									</div>
									<div class="col-md-4 form-control-fld space-top pull-right">
										{!! Form::submit('Update', array( 'class'=>'btn add-btn pull-right', 'id'=>'submitSeller')) !!}
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
