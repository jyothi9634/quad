@extends('app') @section('content')
@include('partials.page_top_navigation')
<div class="clearfix"></div>
<div class="login-head">
			<h1>
				Welcome to  <span>LOGISTIKS.COM</span>
				<p>Please fill the form below to register for Membership</p>
			</h1>
		</div>
@if (Session::has('message'))
		<div class="flash ">
			<p class="text-success col-sm-12 text-center flash-txt alert-success">{{ Session::get('message') }}</p>
		</div>
		@endif
		@if (Session::has('error_message'))
		<div class="flash ">
			<p class="text-alert col-sm-12 text-center flash-txt alert-danger">{{ Session::get('error_message') }}</p>
		</div>
		@endif	
<div class="main">
	<div class="container reg_crumb">
		
		<div class="home-block home-block-login">
			<div class="tabs">

				<div class="tab-content">
					{!! Form::open(array('url' => '/saveIndividual', 'name' => 'individual_form', 'id' => 'individual_form', 'class'=>'form-inline margin-top' )) !!}
						<div class="login-block">
							<div class="login-form login-form-2">
								
								<input type="hidden" name="user_id" value="{{$user->id}}"/>
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
											{!!Form:: text ('location', '', array( 'class'=>'form-control form-control1', 'id'=>'location', 'placeholder'=>'Location*', 'maxlength'=>'30','readonly')) !!}
											<input type="hidden" name="lkp_location_id" id="lkp_location_id" value="" />
										</div>
									</div>
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('city', '', array( 'class'=>'form-control form-control1','id'=>'city','placeholder'=>'City*', 'maxlength'=>'30' ,'readonly')) !!}
											<input type="hidden" name="lkp_city_id" id="lkp_city_id" value=""/>
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!!Form:: text ('district', '', array( 'class'=>'form-control form-control1', 'id'=>'district', 'placeholder'=>'District*', 'maxlength'=>'30' ,'readonly')) !!}
											<input type="hidden" name="lkp_district_id" id="lkp_district_id"  value=""/>
										</div>
									</div>
									
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!!Form:: text ('state', '', array( 'class'=>'form-control form-control1', 'id'=>'state', 'placeholder'=>'State*', 'maxlength'=>'30' ,'readonly')) !!}
											<input type="hidden" name="lkp_state_id" id="lkp_state_id"  value=""/>
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('address1', '', array( 'class'=>'form-control form-control1','id'=>'address1','placeholder'=>'Address Line 1*', 'maxlength'=>'150' )) !!}
										</div>
									</div>
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('address2', '', array( 'class'=>'form-control form-control1','id'=>'address2','placeholder'=>'Address Line 2*', 'maxlength'=>'150' )) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('address3', '', array( 'class'=>'form-control form-control1','id'=>'address3','placeholder'=>'Address Line 3', 'maxlength'=>'150' )) !!}
										</div>
									</div>
								</div>
								<div class="col-md-12 padding-none">
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('landline', '', array( 'class'=>'form-control form-control1','id'=>'landline','placeholder'=>'Landline number', 'maxlength'=>'11' )) !!}
										</div>
									</div>
									<div class="col-md-6 form-control-fld">
										<div class="input-prepend">
											{!! Form:: text ('mobile', $user->phone, array( 'class'=>'form-control form-control1','id'=>'mobile','placeholder'=>'Mobile number*', 'maxlength'=>'10','readonly' )) !!}
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
											{!! Form:: text ('email', $user->email, array( 'class'=>'form-control form-control1','id'=>'email','placeholder'=>'Email ID*', 'maxlength'=>'55','readonly' )) !!}
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
											<p class="error" style="display:none" id="idProofError">Required</p>
										</div>
									</div>
									
								</div>
								<div class="col-md-6 form-control-fld idproofValue" style="display:none;">
										<div class="input-prepend">
											{!! Form:: text ('id_proof_value', '', array( 'class'=>'form-control form-control1','id'=>'id_proof_value','placeholder'=>'ID Proof Value*', 'maxlength'=>'22' )) !!}
										</div>
									</div>
								<div class="col-md-12 form-control-fld">
								<br>
									<div class="col-md-8 form-control-fld">
										<input type="checkbox" id="cdbaccept" name="cdbaccept"><span class="lbl padding-8"></span> Logistiks.com Terms and conditions
									</div>
									<div class="col-md-4 form-control-fld space-top pull-right">
										{!! Form::submit('Update', array( 'class'=>'btn add-btn pull-right', 'id'=>'updateIndividual')) !!}
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
<script src="/js/additional-methods.js"></script>
<script>

$("#pincode,#landline,#alternative_mobile").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
               return false;
        }
   });
   
$(function() {
	

		
	$("#individual_form").validate({
		errorClass: "error-1",
		rules : {
			firstname: {
				required: true,
			},
			lastname: {
				required: true,
			},
			address1: {
				required: true,
			},
			address2: {
				required: true,
			},
			
			alternative_email: {
                email:true,
				notEqulToEmail: true
            },
			alternative_mobile : {
				notEqulToMobileNumber:true
			},
			
			id_proof_value: {
				required: true,
				idProofFormate: true
			},
			pincode: {
                required: true,
				pincode: true,
            },
			landline: {
				integer:true
			},
			cdbaccept: {
				required:true
			}
		},	
		errorPlacement: function(error, element) {
			console.log(element);
			$(element).parent('div').append(error);
		},
	});
	

	
	$("#updateIndividual").on("click",function() {
		if($("#id_proof").val() == "") {
			$("#idProofError").css("display","block");
			//return false;
		} else {
			$("#idProofError").css("display","none");
		}
	});
	
	$("#id_proof").on("change",function(){
		if($("#id_proof").val() == "") {
			$("#idProofError").css("display","block");
			//return false;
		} else {
			$("#idProofError").css("display","none");
		}
	});
	
	$( "form" ).submit(function( event ) {
		if($("#id_proof").val() == "") {
			//$("#idProofError").css("display","block");
			return false;
		}
		
	});	
	
	$("#id_proof").on('change',function() {
		if($(this).val() == "") {
				$(".idproofValue").css("display","none");
		} else {
			$(".idproofValue").css("display","block");
		}
	});

});   
   
</script>
	@endsection
