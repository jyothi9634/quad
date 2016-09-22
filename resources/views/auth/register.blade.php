{{--*/ $status = Request::input('status') /*--}} 
@extends('default')
@section('content')
@include('partials.page_top_navigation')
<div class="reg_page">
<div class="container">
				<div class="login-head">
					<h1>
						Welcome to <span>LOGISTIKS.COM</span>
						<p>Please fill the form below to register for Membership</p>
					</h1>
				</div>
	@if (Session::has('message') && Session::get('message')!='')
	<div class="flash">
		<p class="text-error col-sm-12 text-center flash-txt alert-success">{{
			Session::get('message') }}</p>
	</div>
	@endif @if($status == 'success')
	<div class="flash">
		<p class="text-error col-sm-12 text-center flash-txt alert-success">
			Your account has been confirmed successfully. Now you can avail the
			privileges of a Seller as well as Buyer.</p>
	</div>

	@endif
	
	{!! Form::open(array('url' => 'userregister', 'name'=>'registerForm',
		'id' =>'login-form', 'class'=>'' )) !!}
	
				<div class="login-block">
				<input type="hidden"  value="0" name="is_business" id="option1" class="search-text" >
					<!--<div class="radio-inline col-md-8 col-md-offset-2">
						<label class="radio-inline text-big">Select Category </label>
						<label class="radio-inline">
<input type="radio"  checked="checked" value="0" name="is_business" id="option1" class="search-text" ><label for="option1"><span></span>Individual</label>
</label>
	<!-- <input type="radio" checked="checked" value="0" name="is_business" id="option1" class="search-text" ><span class="lbl padding-8">Individual</span> </label> -->
						
					<!--<label class="radio-inline"> 
					<input type="radio" class="search-text" value="1" name="is_business" id="option2" ><label for="option2"><span></span>Business</label>
					</label>
<!-- <input type="radio" class="search-text" value="1" name="is_business" id="option2"><span class="lbl padding-8">Business</span> </label> -->
					<!--</div>-->
					<!--<div class="social-login">
							<span class="bg"><a href="{{ url('/facebook?key=login') }}"  alt="Facebook" title="Facebook" ><img src="../images/facebook.jpg" alt=""></a></span>
                                                        <span class="bg"><a href="{{ url('/google?key=login') }}" alt="Linked In" title="Linked In"  ><img src="../images/google.jpg" alt=""></a></span>
							<span class="bg"><a href="{{ url('/linkedin?key=login') }}"  alt="Google Plus" title="Google Plus"><img src="../images/linkedin.jpg" alt=""></a></span>
					</div>-->
					<!--<div class="text-center size"> -------------------------- Or -------------------------- </div>-->
					<div class="login-form">
						<div class="center-width">
							<div class="col-md-12 form-control-fld margin-bottom-none">
								<div class="input-prepend">
{!! Form:: text ('user_email', '', array ( 'class' => 'form-control form-control1 clsEmailAddr', 'id' => 'user_email', 'placeholder' =>'Email Id*', 'maxlength' => '50' )) !!}
								</div>
								<span class="error validEmailCheck">
			<p id="error_user_email"></p> </span> 
							</div>
							<div class="col-md-12 form-control-fld margin-bottom-none">
								<div class="input-prepend">
{!! Form::text ('conf_email', '',array ( 'class' => 'form-control form-control1 clsEmailAddr', 'autocomplete'=> 'off', 'id' => 'conf_email', 'onpaste' => "return false", 'maxlength' => '50', 'placeholder' =>'Re-enter Email Id*')) !!}
		
								</div>
								<span class="error"><p id="error_conf_email"></p> </span>
							</div>
							<div class="col-md-12 form-control-fld margin-bottom-none">
								<div class="input-prepend">
									{!! Form::password ('password', array ( 'class' => 'form-control form-control1 clsPasswordVal', 'autocomplete' => 'off', 'id' => 'password', 'maxlength' => '25', 'onpaste' => "return false", 'placeholder' =>'Password *' )) !!} <span class="error"><p id="error_password"></p> </span>
								</div>
							</div>
							
							<div class="col-md-12 form-control-fld margin-bottom-none">
								<div class="input-prepend">
									{!! Form::password ('conf_password', array ( 'class' => 'form-control form-control1 clsPasswordVal', 'autocomplete' => 'off', 'id' =>'conf_password', 'onpaste' => "return false", 'maxlength' => '25', 'placeholder' => 'Re-enter Password *' )) !!}
								</div>
								<span class="error"><p id="error_conf_password"></p></span>
							</div>
							
							<div class="col-md-12 form-control-fld margin-bottom-none">
								<div class="input-prepend">
									{!! Form::text('phone', '', $attributes = array('class' => 'form-control form-control1 phone_number', 'autocomplete' => 'off', 'id' =>'phone', 'maxlength' => '10', 'placeholder' => 'Mobile Number *' )) !!}
								</div>
								<span class="error"><p id="error_phone"></p></span>
							</div>
							

							<div class="col-md-12 form-control-fld margin-bottom-none">
							{!! Form::button('Submit', array( 'name'=>'registerSubmit','id'=>'registerSubmit','class'=>'btn add-btn-2'))!!}
							</div>
							
							<div class="col-md-12 form-control-fld margin-bottom-none">
							<p class="reg_text_align">
									By clicking "<a href="#" class="link-red">Sign up</a>" or "<a href="#" class="link-red">Sign In</a>" <br>
							I acknowledge and agree to the <a href="memberRegistration/termsOfuse" class="normal-link-line" target="_blank"><i>Terms of use.</i></a> ,<br>
							 <a href="{{url('privacypolicy')}}" class="normal-link-line" target="_blank"><i>Privacy Policy.</i></a>, 
							 <a href="memberRegistration/cancellationPolicy" class="normal-link-line" target="_blank"><i>Cancellation Policy.</i></a> and
							 <a href="memberRegistration/aboutUs" class="normal-link-line" target="_blank"><i>About Us.</i></a>
							 </p>
							</div>
							
							<!-- <div class="col-md-12 form-control-fld margin-bottom-none">
							<p class="reg_text_align">
									By clicking "<a href="#" class="link-red">Sign up</a>" or "<a href="#" class="link-red">Sign In</a>" <br>
							I acknowledge and agree to the Terms of Use <br>
							and <a href="#" class="normal-link-line"><i>Privacy Policy.</i></a>
							</p>
							</div> -->
							
							<div id="enableOtp" style="display:none;"> 
							<div class="col-md-12 form-control-fld margin-bottom-none">
								<div class="input-prepend">
									{!! Form::text('otp', '', $attributes = array('class' => 'form-control form-control1 validateOtp', 'autocomplete' => 'off', 'id' =>'otp', 'maxlength' => '4', 'placeholder' => 'Enter OTP *' )) !!}
								</div>
								<span class="error"><p id="error_otp"></p></span>
								<span class="resend_otp" style="display:none;">OTP Sent successfully</span>
								<a href="#" id="regenrateOtp" class="link-red resendOtp">Re-Generate OTP</a><br>
							</div>
							
							
							{!! Form::submit('Sign up', array( 'name'=>'submitRegister','id'=>'registerSignup','class'=>'btn add-btn-2'))!!}
							</div>
							<div class="registration-individual-row2">
								Need Help ? Helpdesk  | Call  (040) 394 12345 
							</div>
						</div>
					</div>
				</div>
				
				{!! Form::close() !!}
				
				<div class="clearfix"></div>
		<div class="col-md-12 form-control-fld margin-bottom-none">
								<div class="input-prepend">
		<p class="line-height">
			By Clicking "Sign Up" or "Sign In with Facebook / Linked In/ Google
			+" I acknowledge and agree to the <span class="bottom_link">Terms of
				Use</span> and <span class="bottom_link">Privacy Policy</span>.
		</p>

		@if($otp) {!! Form:: open(array('url' =>
		'/register/validateotp','name'=>'otp_form', 'id'=>'otp_form')) !!} <br>

		{!! Form::text ('otp',$otp, array ( 'class' => 'col-md-12 input_txt
		margin-bottom', 'autocomplete' => 'off', 'id' => 'otp1', 'placeholder'
		=> 'Enter OTP *' )) !!}
</div></div>



		<div class="top-borer">{!! Form::submit('Sign Up', array('class'=>'btn
			col-sm-12')) !!}</div>
		{!! Form:: close() !!} @endif
		<div class="clearfix"></div>

		<p>Need Help ? Helpdesk | Call +91 99999 99679</p>
				
			</div>	</div>	</div> </div>
			
			@include('partials.footer')

</div>

<div class="modal fade" id="otp-field" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div class="modal-content">
			<button type="button" class="close" data-dismiss="modal">&times;</button>
			<div class="modal-body">
				<div class="col-md-12 padding-none">
					<div class="col-md-6 form-control-fld">
						{!! Form::hidden('hidden_otp', '') !!}
						{!! Form::text ('otp', '',array ( 'class' => 'form-control form-control1 clsMobileno numberVal', 'autocomplete'=> 'off', 'id' => 'otp', 'onpaste' => "return false", 'maxlength' => '4', 'placeholder' =>'Please Enter OTP *')) !!}
					</div>
					<div class="col-md-6 form-control-fld">
						<input type="button" value="Submit" id="confirm_otp" class="login loginmodal-submit"/>
						<input type="button" value="Resend OTP" id="resend_otp" class="login loginmodal-submit"/>
					</div>
					<div class="col-md-12 form-control-fld">
						<span class="otp_error error"></span>
						<span class="resend_otp"></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
   // var otp=<?//php //echo $otp;?>;
//     if(otp!=0){
//         window.alert(otp);
//     }
   
    $(document).ready(function(){ 
    	  $('#option2').click(function()
    	  {
    		  if (document.getElementById("option2").checked == true) {
    	    $("#user_email").attr("placeholder", "Email Id or Mobile Number *");
    	    $("#conf_email").attr("placeholder", "Re-enter Email Id or Mobile Number *");
    	}
    	  });

    	  $('#option1').click(function()
    	    	  {
    		  if (document.getElementById("option1").checked == true) {
    	    	    $("#user_email").attr("placeholder", "Email Id or Mobile Number *");
    	    	    $("#conf_email").attr("placeholder", "Re-enter Email Id or Mobile Number *");
    	    	}
    	    	  });
    	});
	   
    	    
    
</script>
@endsection