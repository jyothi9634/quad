/**
 * 
 */


 
$(document).ready(function() {

	// submit every form by hiting "ENTER"
	$('#login-formq input').keydown(function(e) {
		var key = e.which;
		
		if (key == 13) {
			// As ASCII code for ENTER key is "13"
			// Submit that particular form only
			//$(this).closest('input[type="submit"]').trigger('click');
			$returnValue = userRegistration();
			if($returnValue != false){
				$(this).closest('#login-form').submit();
			}else {
				return false;
			}
		}
	});
	
	
	$("#regenrateOtp").on("click",function(event){
		event.preventDefault();
		sendOneTimePassword();
		$(".resend_otp").css('display','block');
		setTimeout(function() { $(".resend_otp").css('display','none'); }, 5000);
	});
	
	
	function sendOtp(email_or_mobile){
		$.ajax({
			type: "POST",
			url: "/register/otp",
			beforeSend: function() {
				$.blockUI({
					overlayCSS: {
						backgroundColor: '#000'
					}
				});
			},
			complete: function() {
				$.unblockUI();
			},
			data: {
				'phone': email_or_mobile,
			},
			success: function(jsonData) {
				//alert(jsonData);
				$('#otp-field').modal('show');
			}
		}, "json");
	}

	$('#resend_otp').click(function(event){
		event.preventDefault();
		var email_or_mobile = document.getElementById('phone').value;
		sendOtp(email_or_mobile);
		$(".resend_otp").html("OTP Sent successfully").show();
		setTimeout(function() { $(".resend_otp").hide(); }, 5000);
		return false;
	});
	$('#registerSubmitq').click(function(){
		$returnValue = userRegistration();
		if($returnValue != false){
			var email_or_mobile = document.getElementById('phone').value;
			email_true = isemail(email_or_mobile);
			if(email_true < 0){
				sendOneTimePassword();
				return false;
			}

			//$(this).closest('#login-form').submit();
		} else {
			return false;
		}
	});
	
	$('#registerSignup').click(function(event){
		
		event.preventDefault();
		var otp = $("#otp").val();
		
		$.ajax({
			type: "POST",
			url: "/register/validateotp",
			data: {
				'otp': otp,
			},
			
			success: function(jsonData) {
				console.log(jsonData.status);
				if(jsonData.status == 'failed'){
					document.getElementById("error_otp").innerHTML = "Please enter valid OTP";	
					document.getElementById("otp").style.borderColor = "red";
					document.getElementById("otp").style.borderColor = "red";
					return false;
				} else {
					document.getElementById("error_otp").innerHTML = "";	
					document.getElementById("otp").style.borderColor = "";
					document.getElementById("otp").style.borderColor = "";
					$( "#login-form" ).submit();
				}
				$returnValue = userRegistration();
				if($returnValue != false){
					$( "#login-form" ).submit();
					
				}
				
			}
		});
		
		
		
	});

	$(document).on('click', '#confirm_otp', function() {
		var otp = document.getElementById('otp').value;
		if(otp.length == 0){
			$('.otp_error').html("Please enter OTP");
			return false;
		}else if(otp.length < 6){
			$('.otp_error').html("Please enter Valid OTP");
			return false;
		}
		$.ajax({
			type: "POST",
			url: "/register/validateotp",
			beforeSend: function() {
				$('.otp_error').html("");
				$.blockUI({
					overlayCSS: {
						backgroundColor: '#000'
					}
				});
			},
			complete: function() {
				$.unblockUI();
			},
			data: {
				'otp':  document.getElementById('otp').value,
			},
			success: function(jsonData) {
				console.log(jsonData.status);
				if(jsonData.status != "success"){
					$('.otp_error').html("Invalid OTP, Please Re-Enter");
				}else{
					$('#otp-field').modal('hide');
					$('#login-form').submit();
				}
			}
		}, "json");
	});

	//$(document).on('focus click keyup keypress blur change', '#pincode', function() {
	$(document).on("keyup","#pincode",function(){
		var pincodeVal = 0;
		pincodeVal = $('#pincode').val();
		if(pincodeVal.length > 5){
			var data = {
					'prop_pinid': $('#pincode').val()
				};
			 $.ajax({
					type: "GET",
					url: '/getPincodeDetails',
					data: data,
					dataType: 'json',
					
					success: function(data) {	   	        	
						$("#city").val(data.divisionname);
						$("#state").val(data.statename);
						$("#location").val(data.postoffice_name);
						$("#district").val(data.districtname);
						
						$("#lkp_city_id").val(data.lkp_city_id);
						$("#lkp_state_id").val(data.state_id);
						$("#lkp_location_id").val(data.id);
						$("#lkp_district_id").val(data.lkp_district_id);
				   
					},
					error: function(request, status, error) {	            
					},
				});
		} else {
			$("#city").val("");
			$("#state").val("");
			$("#location").val("");
			$("#district").val("");
		}	
	});
	
	$( "#txt_user_pincode" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#hidden_user_pincode').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
	
	$(document).on('blur', '#txt_pincode', function() {
		var data = {
		        'prop_pinid': $('#txt_pincode').val()
		    };
		 $.ajax({
		        type: "GET",
		        url: '/getprincipalplace',
		        data: data,
		        dataType: 'text',
		        success: function(data) {	   	        	
		        	$("#txt_business_place").val(data);
		        	$("#hidden_user_pincode").val(data);
		        },
		        error: function(request, status, error) {	            
		        },
		    });
	});
	
	$( "#txt_pincode" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#hidden_user_pincode').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
	
	$(document).on('blur', '#txt_company_pincode', function() {
		var data = {
		        'prop_pinid': $('#txt_company_pincode').val()
		    };
		 $.ajax({
		        type: "GET",
		        url: '/getprincipalplace',
		        data: data,
		        dataType: 'text',
		        success: function(data) {	   	        	
		        	$("#txt_principal_place").val(data);
		        	$("#hidden_user_pincode").val(data);
		        },
		        error: function(request, status, error) {	            
		        },
		    });
	});
	
	$( "#txt_company_pincode" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#hidden_user_pincode').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
	
	$(document).on('blur', '#txt_principal_place_pincode', function() {
		var data = {
		        'prop_pinid': $('#txt_principal_place_pincode').val()
		    };
		 $.ajax({
		        type: "GET",
		        url: '/getprincipalplace',
		        data: data,
		        dataType: 'text',
		        success: function(data) {	   	        	
		        	$("#txt_business_place").val(data);
		        	$("#hidden_user_pincode").val(data);
		        },
		        error: function(request, status, error) {	            
		        },
		    });
	});
	
	$( "#txt_principal_place_pincode" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#hidden_user_pincode').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
	
	$(".toggle_window_yes").on("click",function(){  
        switchToRole =  $("#toggle_to_role_id").val();
    	switchingRoles(switchToRole);       
       
    });

    $(".toggle_seller_yes").on("click",function(){  

    	//@Raman
    	$("#togglesellerrolemodal").modal('hide');

    	switchToRoleSeller =  $("#toggle_seller_to_role_id").val();

        // ajax function starts
		$.ajax({
			type : 'post', // defining the ajax type
			url : '/check_toggle_role', // calling the controller with the
			// action involved
			dataType : 'html', // datatype
			//data : datastr, // passing the data used for operation
			success : function(html) {
				 
				if(html =='pop_up')
				{
					$('#confirmUseDetailsBox').modal('show');
					$('#confirmUseDetailsBox').find('#allowBuyerDetails').on('click', function(){
						
					 	$.ajax({
							type : 'post', // defining the ajax type
							url : '/toggle_role', // calling the controller with the
							beforeSend : function() {
								$.blockUI({
									overlayCSS : {
										backgroundColor : '#000'
									}
								});
							},
							complete : function() {
								$.unblockUI();
							},
							// action involved
							dataType : 'json', // datatype
							//data : datastr, // passing the data used for operation
							success : function(html) {
								
								if(html.success=='1'){
									
									$('#updateSuccessBox').modal('show'); 
									$('#displayMessage').html('Buyer details submitted successfully'); 
									//redirect according to their individual / business nature
									$("#updateSuccessBox").on('hidden.bs.modal', function () {
										if(html.business == '1'){
											window.location.href = "/";
										}else{
											window.location.href = "/";
										}
								 	});

								}else{
									$('#updateSuccessBox').modal('show'); 
									$('#displayMessage').html('Error occured while updating your details. Try after some time.'); 
								}
							}
						});

					});

					$('#confirmUseDetailsBox').find('#fillBuyerDetails').on('click', function(){
						//Seller manually filling buyer details
						$.ajax({
							type : 'post', // defining the ajax type
							url : '/fill_seller_details', // calling the controller with the
							// action involved
							dataType : 'json', // datatype
							//data : datastr, // passing the data used for operation
							success : function(html) {
								//$("#togglebuyerrolemodal").modal('hide');
								window.location = html.redirect;
							}
						});
					});
		
				}
				else if(html == 'editBuyer'){
				 	var switchingTo = '1';
					datastr = '&switchTo=' + switchingTo ;
					$.ajax({
						type : 'post', // defining the ajax type
						url : '/switch_roles', // calling the controller with the action involved
						dataType : 'html', // datatype
						data : datastr, // passing the data used for operation
						success : function(html) {
							if(html=='1'){
								$("#togglebuyerrolemodal").modal('hide');									
								window.location.href = "/home";
							}
						}
			 		});
				}
				else{
				 	window.location.href = "/";
				}
			}
		});
       
    });

	$(".toggle_buyer_yes").on("click",function(){  
        switchToRoleBuyer =  $("#toggle_buyer_to_role_id").val();
        $.ajax({
					type : 'post', // defining the ajax type
					url : '/toggle_role', // calling the controller with the
					// action involved
					dataType : 'json', // datatype
					//data : datastr, // passing the data used for operation
					success : function(html) {
							window.location = html.redirect;
						
					}
			 });
    });

	// display city and locality dropdown on check of intracity and packers &
	// movers checkbox
	if ($("#service_3").is(':checked')) {
		$("#intracityArea").addClass("displayBlock");
	}

	if ($("#service_15").is(':checked')) {
		//$("#pmArea").addClass("displayBlock");
	}
	
	//Seller Buyer Toggle Feature
	
	$("#activate_Sbuyer").click(toggleSellerRole);
	$("#activate_Sseller").click(toggleSellerRole);
		
	$("#activate_Bbuyer").click(toggleBuyerRole);
	$("#activate_Bseller").click(toggleBuyerRole);
	$("#switchRole").click(function(){
		var switchTo = $(this).data("role");
		$("#toggle_to_role_id").val(switchTo);
		if(switchTo == '1'){
			$("#toggle_role_name").html("Buyer");
		}else{
			$("#toggle_role_name").html("Seller");
		}
		$("#toggleuserrolemodal").modal('show');
		//switchingRoles(switchTo);
	});
	function switchingRoles(switchingTo){
		datastr = '&switchTo=' + switchingTo ;
	$.ajax({
		type : 'post', // defining the ajax type
		url : '/switch_roles', // calling the controller with the action involved
		dataType : 'html', // datatype
		data : datastr, // passing the data used for operation
		success : function(html) {
		
				if(html=='1'){
					$("#toggleuserrolemodal").modal('hide');				

					window.location.href = "/home";

				}
		}
 });}
		
	function toggleSellerRole(e){	
		$("#togglesellerrolemodal").modal('show');
	}
	
	
	//function called if buyer tries to toggle roles
	function toggleBuyerRole(e){
	 	$("#togglebuyerrolemodal").modal('show');
	}

	

});

// user registration form validation
// When the browser is ready...
$(function() {
	$(".is_business").click(function() {
		var is_business = $(this).val();
		$('#is_business').val(is_business);
		$('#submitRegister').click();
	});
	$(".checkServices").change(function() {
		existCheckservices();
	});

	// display city and locality dropdown on check of intracity and packers &
	// movers checkbox

	$("#service_3").change(function() {
		if (this.checked) {

			$("#intracityArea").addClass("displayBlock");

		} else {
			$("#intracityArea").removeClass("displayBlock");

		}

	});

	$("#service_15").change(function() {
		//alert("hgfh");
		if (this.checked) {

			//$("#pmArea").addClass("displayBlock");

		} else {
			//$("#pmArea").removeClass("displayBlock");
		}

	});

	$("#otp_form").validate({ // initialize the plugin
		rules : {
			"otp" : {
				required : true
			},
		}
	});

	// Setup form validation on the corporate buyer form
	
	$("#corporate-buyer-form").validate({					
					ignore: "input[type='text']:hidden",
					// Specify the validation rules
						rules : {
							'name' : "required",
							'lkp_business_type_id' : "required",
							'other_business_type' :"required",
							'lkp_country_id' : "required",
							'lkp_state_id' : "required",
							'established_in' :{
								required : true,
								number : true
							},
							'pincode' : {
								required : true,
								number : true,
								rangelength : [ 4, 6 ]
							},
							'pincode_hidden' : {
								required : true,
							},
							'pannumber' : {
								required : true,
							},
							'employee_strengths' : {
								required : true,
							},
							'lkp_industry' : {
								required : true,
							},
							'lkp_specialities' : {
								required : true,
							},
							'address' : {
								required : true,	
							},
							'contact_firstname':{
								required : true,	
							},
							'contact_mobile' : {
								required : true,
								accept : "[0-9]+",
								minlength : 10
							},

							'contact_email' : {
								required : true,
								email : true
							},
							'contact_landline' : {
								required : true,
								accept : "[0-9]+",
								rangelength : [ 10, 15 ]
							},
							'current_turnover' :{
								number :true,
								turnovervalidations : true
							},
							'first_year_turnover' :{
								number :true,
								turnovervalidations : true
							},
							'second_year_turnover' :{
								number :true,
								turnovervalidations : true
							},
							'third_year_turnover' :{
								number :true,
								turnovervalidations : true
							},
							'service_tax_number' : "required",

							'tin' : "required",
						// "termsCondition" : "required",

						},
						errorPlacement: function(error, element) {
				        	$(element).parent().after(error);
				        },
						// Specify the validation error messages
						messages : {
							'name' : "Please enter your company name",
							'lkp_business_type_id' : "Plese select the business type",
							'other_business_type' : "Please specify your business type",
							'lkp_country_id' : "Plese select the country",
							'lkp_state_id' : "Plese select the state",

							'pincode' : {
								required : "",

							},
							'pincode_hidden' : {
								required : "Please enter your pincode / zipcode",
								number : "Please enter only numbers in pincode field",
								maxlength : "Please enter pincode between 4 to 6 character long"

							},
							'principal_place' : {
								required : "Please enter your principal place of business",
								number : "Please enter less than 50 characters"
							},

							'address' : "Please enter company address",
							'contact_firstname': "Please enter First name",
							'contact_mobile' : {
								required : "Please enter your mobile number",
								number : "Phone number must contain digits only",
								minlength : "Please enter 10 digits mobile number"
							},
							'contact_email' : {
								required : "Please enter an email address",
								email : "Please enter a valid email address"
							},
							'contact_landline' : {
								required : "Please enter company's landline number",
								number : "Please enter only numbers in landline field",
								rangelength : "Please enter number between 10 to 15 character long"

							},

							'gta' : "Please enter your GTA number",

							'service_tax_number' : "Please enter your Service Tax",

							'tin' : "Please enter your TIN No.",
						// "termsCondition" : "Please check Terms & Condition",

						},
						submitHandler : function(form) {
							
							form.submit();
						}
					});

	// individual buyer form validation
	$("#buyer-details-form").validate({
		ignore: "input[type='text']:hidden",
		rules : {
			"firstname" : {
				required : true,
				//accept : "[a-zA-Z]+"
				lettersonly: true
			},
			"lastname" : {
				required : true,
				//accept : "[a-zA-Z]"
				lettersonly: true
			},
			/*"company_name" : {  // Comminted as discussed with krishna
				required : true,
				//accept : "[a-zA-Z]+"
				//lettersonly: true
			},*/
			"lkp_industry" : {
				required : true,
			},
			"pannumber" : {
				required : true,
			},
			"address" : {
				required : true
			},
			"pincode" : {
				required : true,
			},
			"pincode_hidden" : {
				required : true,
			},

			"mobile" : {
				required : true,
				number : true,
				accept : "[0-9]+",
				minlength: 10

			},

			"contact_email" : {
				required : true,
				email : true,
                                maxlength:70
			},
			'landline' : {
				accept : "[0-9]+",
				rangelength : [ 10, 15 ]
			},
			'location' : {
				required : true
			},
			
		},
		errorPlacement: function(error, element) {
        	$(element).parent().parent().after(error);
        },

		messages : {
			"firstname" : {
				required : "Please enter first name",
				//accept : "Please enter only alphabets"
				lettersonly : "Please enter only alphabets"
			},

			"lastname" : {
				required : "Please enter last name ",
				//accept : "Please enter only alphabets"
				lettersonly : "Please enter only alphabets"
			},

			"address" : {
				required : "Please enter address"
			},
			"pincode" : {
				required : ""
			},
			"pincode_hidden" : {
				required : "Please enter valid pincode",
			},

			"mobile" : {
				required : "Please enter mobile number",
				number : "Please enter only numbers",
				accept : "Please enter only numbers",
				minlength: "Please enter 10 digits mobile number"

			},

			"contact_email" : {
				required : "Please enter email",
				email : "Please enter valid email",
                                maxlength : "Please enter less than 70 characters"
			},
			
			"landline" : {
				number : "Please enter only numbers in landline field",
				rangelength : "Please enter number between 10 to 15 character long"

			}
		},
		submitHandler : function(form) { // for demo
			form.submit();
		}
	});

jQuery.validator.addMethod("lettersonly", function(value, element) {
    return this.optional(element) || /^[a-z\s]+$/i.test(value);
}, "Only alphabetical characters"); 

	// Setup form validation on the #register-form element
	$("#corporate-seller-form").validate({
						ignore: "input[type='text']:hidden",
						// Specify the validation rules
						rules : {
							'name' : "required",
							'established_in' :{
								required : true,
								number : true
							},
							'lkp_business_type_id' : "required",
							'lkp_country_id' : "required",
							'lkp_state_id' : "required",

							'pincode' : {
								required : true,
								number : true,
								rangelength : [ 4, 6 ]
							},
							"pincode_hidden" : {
								required : true,
							},
							"pannumber" : {
								required : true,
							},
							'employee_strengths' : {
								required : true,
							},
							'lkp_industry' : {
								required : true,
							},
							'lkp_specialities' : {
								required : true,
							},
							'other_business_type' : {
								required:true
							},
							'address' : {
								required:true
							},

							'contact_firstname' : {
								required : true
							},

							'contact_mobile' : {
								required : true,
								number : true,
								minlength : 10
							},

							'contact_email' : {
								required : true,
								email : true
							},
							'contact_landline' : {
								required : true,
								number : true,
								rangelength : [ 10, 15 ]
							},
							'current_turnover' :{
								number :true,
								turnovervalidations : true
							},
							'first_year_turnover' :{
								number :true,
								turnovervalidations : true
							},
							'second_year_turnover' :{
								number :true,
								turnovervalidations : true
							},
							'third_year_turnover' :{
								number :true,
								turnovervalidations : true
							},
							'service_tax_number' : "required",

							'tin' : "required",
						// "termsCondition" : "required",

						},
						errorPlacement: function(error, element) {
				        	$(element).parent().after(error);
				        },

						// Specify the validation error messages
						messages : {
							'name' : "Please enter your company name",
							'established_in' :{
								required : "Please enter the year of establishment",
								number : "Please enter year only in numbers"
							},
							'lkp_business_type_id' : "Plese select the business type",
							'lkp_country_id' : "Plese select the country",
							'lkp_state_id' : "Plese select the state",

							'pincode' : {
								required : "",

							},
							"pincode_hidden" : {
								required : "Please enter your pincode / zipcode",
							},
							'other_business_type' : "Please specify your business type",

							'principal_place' : {
								required : "Please enter your principal place of business",
								maxlength : "Please enter less than 50 characters"
							},

							'address' : "Please enter company address",
							'contact_firstname': "Please enter First name",
							'contact_mobile' : {
								required : "Please enter your mobile number",
								number : "Phone number must contain digits only",
								minlength : "Please enter 10 digits mobile number"
							},
							'contact_email' : {
								required : "Please enter an email address",
								email : "Please enter a valid email address"
							},
							'contact_landline' : {
								required : "Please enter company's landline number",
								number : "Please enter only numbers in landline field",
								rangelength : "Please enter number between 10 to 15 character long"

							},

							'gta' : "Please enter your GTA number",

							'service_tax_number' : "Please enter your Service Tax",

							'tin' : "Please enter your TIN No.",
						// "termsCondition" : "Please check Terms & Condition",

						},
						submitHandler : function(form) {
							if (checkArea() === true) {
								form.submit();
							} else {
								return false;
							}
						}
					});

	// Setup form validation on the #seller_individual registeration form
	// element
	$("#individual-seller-form").validate({
		ignore: "input[type='text']:hidden",
						// Specify the validation rules
						rules : {
							'firstname' : "required",
							'lastname' : "required",
							'address' : "required",
							'nature_of_business' : "required",
							'established_in' : {
								required : true,
								number : true
							},

							'pincode' : {
								required : true,
								/*number : true,
								rangelength : [ 4, 6 ]*/
							},
							'pincode_hidden' : {
								required : true,
							},
							'pannumber' : {
								required : true,
							},
							'employee_strengths' : {
								required : true,
							},
							'lkp_industry' : {
								required : true,
							},
							'lkp_specialities' : {
								required : true,
							},
							'contact_mobile' : {
								required : true,
								number : true,
								minlength : 10,
								maxlength : 10
							},

							'contact_email' : {
								required : true,
								email : true
							},
							'contact_landline' : {
								required : true,
								number : true,
								rangelength : [ 10, 15 ]
							},
							'current_turnover' :{
								number :true,
								turnovervalidations : true
							},	
							'first_year_turnover' :{
								number :true,
								turnovervalidations : true
							},	
							'second_year_turnover' :{
								number :true,
								turnovervalidations : true
							},	
							'third_year_turnover' :{
								number :true,
								turnovervalidations : true
							},	
							'service_tax_number' : "required",

							'tin' : "required",

						},
						errorPlacement: function(error, element) {
				        	$(element).parent().after(error);
				        },
						// Specify the validation error messages
						messages : {

							'firstname' : "Please enter your firstname",
							'lastname' : "Please enter your lastname",
							'address' : "Please enter your address",
							'nature_of_business' : "Please enter nautre of your business",
							'established_in' : {
								required : "Please enter year of establishment for the user",
								number : "Please enter only numbers"
							},

							'pincode' : {
								required : "",

							},
							'pincode_hidden' : {
								required : "Please enter your pincode / zipcode",
								number : "Please enter only numbers in pincode field",
								maxlength : "Please enter pincode between 4 to 6 character long"

							},
							'principal_place_pincode' : {
								required : "Please enter your pincode / zipcode",

							},

							'principal_place' : {
								required : "Please enter your principal place of business",
								number : "Please enter less than 50 characters"
							},

							'address' : "Please enter company address",

							'contact_mobile' : {
								required : "Please enter your mobile number",
								number : "Phone number must contain digits only",
								minlength : "Please enter 10 digits mobile number",
								maxlength : "Please enter 10 digits mobile number"
							},
							'contact_email' : {
								required : "Please enter an email address",
								email : "Please enter a valid email address"
							},
							'contact_landline' : {
								required : "Please enter company's landline number",
								number : "Please enter only numbers in landline field",
								rangelength : "Please enter number between 10 to 15 character long"

							},

							'gta' : "Please enter your GTA number",

							'service_tax_number' : "Please enter your Service Tax",

							'tin' : "Please enter your TIN No.",

						},
						submitHandler : function(form) {
							// form.submit();
							if (checkArea() === true) {
								form.submit();
							} else {
								return false;
							}
						}
					});
/**** Start:@jagadeesh 29042016 ****/
	jQuery.validator.addMethod("turnovervalidations", function(value, element) {
	    if(parseFloat(value)>0){
	    	return this.optional(element) || /^\d{1,12}(\.\d{1,2})?$/i.test(parseFloat(element.value));
	    }else{
			return true;
	    }
	}, function(params, element) {
		if(element.value != ''){
	    element.value  = Math.floor(element.value * 100) / 100;
		}
	    var count_value = /^\d{1,12}(\.\d{1,2})?$/i.test(parseFloat(element.value));
	    if(count_value == false){
	     	return "Turnover should be less than 10000000000";
	    }	
	    else if(parseFloat(element.value)>0){
	        return "Turnover is truncated to 2 decimals";
	    }else{
	        return "Turnover enter value greater than 0";
	    }

	});

/**** End:@jagadeesh 29042016 ****/

	// accepting user selection buyer / seller


	$(".user_registration_roles li").click(function() {
		$.blockUI({
			overlayCSS : {
				backgroundColor : '#000'
			}
		});
		$(".user_registration_roles li").removeClass("active");
		$(this).addClass("active");
		setTimeout(function(){ $.unblockUI(); }, 1000);

	});
	$(".roleSelector").click(function() {

		//var buyerId = $(this).parent('span').data("sellerlistid");
		
		var userRole = $(this).data("value");
		var selection = $(this).data("selection");

		// passing the data to the ajax function
		// that is further passed to the controller

		datastr = '&user_role=' + userRole + '&selection='+selection;

		// ajax function starts
		$.ajax({
			type : 'post', // defining the ajax type
			url : '/temp_role', // calling the controller with the
			beforeSend : function() {
				$.blockUI({
					overlayCSS : {
						backgroundColor : '#000'
					}
				});
			},
			complete : function() {
				$.unblockUI();
			},
			// action involved
			dataType : 'json', // datatype
			data : datastr, // passing the data used for operation
			success : function(html) {
				 //alert(html.redirect);
				window.location = html.redirect;

			}
		});

	});

	// ask me later popup
	$("#askmeLater").click( function() {

		var time = '30';
		datastr = '&time=' + time;
		// ajax function starts
		$.ajax({
		type : 'post', // defining the ajax type
		url : '/home/askmeLater', // calling the
		// controller with the action involved
		dataType : 'html', // datatype
		data : datastr, // passing the data used for operation
		success : function(response) {
			if(response =='1'){
				//dataMessage = "<span class='flash flash-txt alert-success'>Thankyou for your feedback</span>";
				//$('#flash-txt').removeC('displayNone');
				//$("#flash-txt").html(dataMessage).fadeIn("slow").delay(5000).fadeOut('slow');
				}}
				});
	});

	$("#user_email1").blur(function() {
		var currVal = $(this).val();
		//if(verifyEmail(currVal)){
			uniquenessCheck();
		//}
	});

	

	// seller confirmation mail
	// accepting user selection buyer / seller
	$("#pay_success").click(function() {
		
		
		 if($('input[name=selectPeriod]').is(':checked')) {
	 var subscriptionTime =$('input[name=selectPeriod]:checked', '#subscriptionForm').val(); 
			
		
		
		if (document.getElementById('termCheckbox').checked) {
			
			datastr = '&time=' + subscriptionTime;
			
			
			// ajax function starts
			$.ajax({
				type : 'post', // defining the ajax type
				url : '/thankyou/sellerConfirm', // calling the controller with
				beforeSend : function() {
					$.blockUI({
						overlayCSS : {
							backgroundColor : '#000'
						}
					});
				},
				complete : function() {
					$.unblockUI();
				}, // the
				// action involved
				dataType : 'html', // datatype
				data : datastr,
				success : function(html) {
					// alert(html);
					// alert("PLEASE LOGIN AGAIN TO CONTINUE");
					window.location = '/register';
				}
			});
		}
		else{
			 $('#time-selection-error').html(''); 
			$('#term-error').html('Please accept terms & conditions');
			
		}
		 }else{
			 $('#term-error').html('');
			 $('#time-selection-error').html('Please select one of the time period'); 
		 }
		
		
	});

	$('.flash').slideDown(function() {
		setTimeout(function() {
			$('.flash').fadeIn(1000).delay(100).fadeOut(3000);
		}, 2000);
	});
	
	/**
	 * FOR BUSINESS BUYER/ SELLER
	 */
	//by default hide the text box and show on selection of others option
	if($('#businessType_id').val() == 8){
			$('#other-business-txt').show();
		}
	else{
			$('#other-business-txt').hide();
		}

	$('#businessType_id').change(function(){
		$businessType = $(this).val();
		if ($businessType == 8){
			
			$('#other-business-txt').show();
			
		}else{
			$('#other-business-txt').hide();
			
		}
		
	});

	$('.copy_email_to_phone').change(function () {
		$('#login_phone').val($(this).val());
	});
	$('.copy_email_to_phone_popup').change(function () {
		$('#login_phone_popup').val($(this).val());
	});
	
	

});
function checkArea() {

	var Checkservices = existCheckservices();
	var validIntracity = checkIntracity();
	//var validPm = checkPm();

	if (validIntracity == false || Checkservices == false) {
		return false;
	} else if (validIntracity == true && Checkservices == true) {
		return true;
	} else {
		return false;
	}
}
// checkboxlist validation for seller registration
function existCheckservices() {
	var checkboxLength = $('input[type="checkbox"]:checked').length;	
	if (checkboxLength < 1) {

		$("#error_services")
				.html("Please select atleast one services offering");
		return false;

	} else {

		$("#error_services").html("");
		return true;

	}
}
function checkIntracity() {
	if (document.getElementById('service_3').checked) {
	
		
		if ($("#states_multipleSelect").val() == '' || $("#locality_multiple").val() == '') {
			document.getElementById('error_intracity_area').innerHTML = "Please select City & Locality for Road-Intracity services";
			return false;
		}
		else if($("#states_multipleSelect").val() == null || $("#locality_multiple").val() == null){
			document.getElementById('error_intracity_area').innerHTML = "Please select City & Locality for Road-Intracity services";

			// stop submit
			return false;
			} else {
			document.getElementById('error_intracity_area').innerHTML = "";
			return true;
		}

	} else {
		return true;
	}

}

function checkPm() {
	// if packers and movers is selected
	/*if (document.getElementById('service_15').checked) {

		if ($("#states_multiple").val() == '' || $("#city_multiple").val() == '') {
			document.getElementById('error_pm_area').innerHTML = "Please select State & City for Packers & Movers service";

			// stop submit
			return false;
		}
		else if ($("#states_multiple").val() == null || $("#city_multiple").val() == null) {
			document.getElementById('error_pm_area').innerHTML = "Please select State & City for Packers & Movers service";

			// stop submit
			return false;
		}
		
		else {
			document.getElementById('error_pm_area').innerHTML = "";

			return true;
		}

	} else {
		return true;
	}*/
	return true;
}

function isemail(email_or_mobile){
	var email_true = email_or_mobile.indexOf("@");
	if(email_true < 0 && isNaN(email_or_mobile)){
		email_true = 1;
	}
	return email_true;
}

function uniquenessCheck() {
	var option_value;
	var user_email_id = document.getElementById("user_email").value;
	if (document.getElementById("option1").checked) {
		 option_value = 1;
	} else {
		 option_value = 0;
	}
	/*var email_or_mobile = document.getElementById('user_email').value;
	email_true = isemail(email_or_mobile);

	if(email_true >= 0){
		if (!validateEmail(email_or_mobile)) {
			document.getElementById('error_user_email').innerHTML = "Please enter valid email";
			document.getElementById("user_email").style.borderColor = "red";
			document.getElementById('user_email').focus();
			return false;
		}
	}else{
		if (email_or_mobile && !validateMobilePhone(email_or_mobile)) {
			document.getElementById('error_user_email').innerHTML = "Please enter valid mobile number";
			document.getElementById("user_email").style.borderColor = "red";
			document.getElementById('user_email').focus();
			return false;
		}
	}*/
	// passing the data to the ajax function
	// that is further passed to the controller

	datastr = '&user_email=' + user_email_id + '&optionValue=' + option_value;

	// ajax function starts
	$.ajax({
		type : 'post', // defining the ajax type
		url : '/checkunique', // calling the
		// controller with the action involved
		dataType : 'html', // datatype
		data : datastr, // passing the data used for
		// operation
		success : function(html) {
			if(html != "200"){
				$("#error_user_email").html(html);
				$('#registerSubmit').prop('disabled', true);
			}else{
				$("#error_user_email").html('');
				$('#registerSubmit').prop('disabled', false);
			}
		}
	});
}

function validateMobilePhone(phoneText) {
	//var filter = /^[0-9-+]+$/;
	var filter = /(^[0-9]{10,12}$)/
	return filter.test(phoneText);
}

// check remember me cookie as a localstorage
// $(function() {
//
// if (localStorage.chkbx && localStorage.chkbx != '') {
// $('#remember_me').attr('checked', 'checked');
// $('#login_email').val(localStorage.usrname);
// $('#login_pass').val(localStorage.pass);
// } else {
// $('#remember_me').removeAttr('checked');
// $('#login_email').val('');
// $('#login_pass').val('');
// }
//
// });

// //remember me functionality
// function remember_me() {
//
// if ($('#remember_me').is(':checked')) {
// // save username and password as cookie in local storage
// localStorage.usrname = $('#login_email').val();
// localStorage.pass = $('#login_pass').val();
// localStorage.chkbx = $('#remember_me').val();
// } else {
// localStorage.usrname = '';
// localStorage.pass = '';
// localStorage.chkbx = '';
// }
// }

// client side validation for registration and login pagefunction
function validateLogin() {

	var str = document.getElementById('login_email').value;
	// check if input has '@' in between
	var exist = str.indexOf("@");
	if (exist > -1) {
		// validate email address
		if (!validateEmail(document.getElementById('login_email').value)) {
			document.getElementById('error_login_email').innerHTML = "Invalid email";
			document.getElementById('login_email').style.borderColor = "red";
			return false;
		} else {
			document.getElementById("error_login_email").innerHTML = "";
			document.getElementById('login_email').style.borderColor = "";
			// remember me
			remember_me();
		}

	}
	// check if input has all numbers
	else if (/^\d+$/.test(str) == true) {

		// check for valid indian mobile no.
		var mob = /^[7-9]{1}[0-9]{9}$/;
		if (mob.test(str) == false) {
			document.getElementById("error_login_email").innerHTML = "Please enter valid Mobile";
			document.getElementById('login_email').style.borderColor = "red";
			return false;
		} else {
			document.getElementById("error_login_email").innerHTML = "";
			document.getElementById('login_email').style.borderColor = "";
			remember_me();
		}
	}
	// If neither email nor phone format
	else {
		document.getElementById('error_login_email').innerHTML = "Please enter Email / Mobile";
		document.getElementById('login_email').style.borderColor = "red";
		return false;
	}

	if (document.getElementById("login_pass").value == '') {
		document.getElementById("error_login_pass").innerHTML = "Please enter Password";
		document.getElementById('login_pass').style.borderColor = "red";

		return false;
	} // check if password is more than 4 characters or not
	else if (document.getElementById("login_pass").value.length < 5) {

		document.getElementById("error_login_email").innerHTML = "Please enter password more than 4 characters";
		document.getElementById('login_pass').style.borderColor = "red";
		return false;
	}

	else {
		document.getElementById("error_login_email").innerHTML = "";
		document.getElementById('login_pass').style.borderColor = "";
		remember_me();
	}

}

function userRegistration() {
	if (document.getElementById('option1').checked) {

		if (document.getElementById("user_email").value == '') {
			document.getElementById("error_user_email").innerHTML = "Please enter email ";
			document.getElementById("user_email").style.borderColor = "red";
			document.getElementById("user_email").focus();
			return false;
		} else {

			var str = document.getElementById("user_email").value;
			// check whether input is Mail
			var exist = str.indexOf("@");
			if (exist > -1) {
				if (!validateEmail(document.getElementById('user_email').value)) {
					document.getElementById('error_user_email').innerHTML = "Please enter valid email";
					document.getElementById("user_email").style.borderColor = "red";
					document.getElementById('user_email').focus();
					return false;
				} else {
					document.getElementById("error_user_email").innerHTML = "";
					document.getElementById("user_email").style.borderColor = "";
				}
				if (document.getElementById("conf_email").value == '') {
					document.getElementById("error_conf_email").innerHTML = "Please enter confirm email";
					document.getElementById("conf_email").style.borderColor = "red";
					document.getElementById("conf_email").focus();
					return false;
				} else {
					document.getElementById("error_conf_email").innerHTML = "";
					document.getElementById("conf_email").style.borderColor = "";
				}
				if (document.getElementById("conf_email").value != document
						.getElementById("user_email").value) {
					document.getElementById("error_conf_email").innerHTML = "Email and confirm email should match";
					document.getElementById("conf_email").style.borderColor = "red";
					document.getElementById("user_email").style.borderColor = "red";
					return false;
				} else {
					document.getElementById("error_conf_email").innerHTML = "";
					document.getElementById("conf_email").style.borderColor = "";
					document.getElementById("user_email").style.borderColor = "";
				}
			}

			// check if input has all numbers
			else if (/^\d+$/.test(str) == true) {

				// check for valid indian mobile no.
				var mob = /^[7-9]{1}[0-9]{9}$/;
				if (mob.test(str) == false) {

					document.getElementById("error_user_email").innerHTML = "Please enter valid mobile number";
					document.getElementById("user_email").style.borderColor = "red";
					document.getElementById("user_email").focus();
					return false;
				} else {
					document.getElementById("error_user_email").innerHTML = "";
					document.getElementById("user_email").style.borderColor = "";
				}
				if (document.getElementById("conf_email").value != document
						.getElementById("user_email").value) {
					document.getElementById("error_conf_email").innerHTML = "Mobile and confirm Mobile should match";
					document.getElementById("conf_email").style.borderColor = "red";
					document.getElementById("user_email").style.borderColor = "red";
					return false;
				} else {
					document.getElementById("error_conf_email").innerHTML = "";
					document.getElementById("conf_email").style.borderColor = "";
					document.getElementById("user_email").style.borderColor = "";
				}

			}

			else {

				document.getElementById("error_user_email").innerHTML = "Please enter valid email ";
				document.getElementById("user_email").focus();
				document.getElementById("user_email").style.borderColor = "red";
				return false;
			}
		}
	} else if (document.getElementById('option2').checked) {

		if (document.getElementById("user_email").value == '') {
			document.getElementById("error_user_email").innerHTML = "Please enter email ";
			document.getElementById("user_email").style.borderColor = "red";
			document.getElementById("user_email").focus();
			return false;
		} else {
			document.getElementById("error_user_email").innerHTML = "";
			document.getElementById("user_email").style.borderColor = "";
		}

		if (!validateEmail(document.getElementById('user_email').value)) {
			document.getElementById('error_user_email').innerHTML = "Please enter valid email";
			document.getElementById("user_email").style.borderColor = "red";
			document.getElementById('user_email').focus();
			return false;
		} else {
			document.getElementById("error_user_email").innerHTML = "";
			document.getElementById("user_email").style.borderColor = "";
		}
		if (document.getElementById("conf_email").value == '') {
			document.getElementById("error_conf_email").innerHTML = "Please enter confirm email";
			document.getElementById("conf_email").style.borderColor = "red";
			document.getElementById("conf_email").focus();
			return false;
		} else {
			document.getElementById("error_conf_email").innerHTML = "";
			document.getElementById("conf_email").style.borderColor = "";
		}
		if (document.getElementById("conf_email").value != document
				.getElementById("user_email").value) {
			document.getElementById("error_conf_email").innerHTML = "Email and confirm email should match";
			document.getElementById("conf_email").style.borderColor = "red";
			document.getElementById("user_email").style.borderColor = "red";
			return false;
		} else {
			document.getElementById("error_conf_email").innerHTML = "";
			document.getElementById("conf_email").style.borderColor = "";
			document.getElementById("user_email").style.borderColor = "";
		}

	}
	if (document.getElementById("password").value == '') {
		document.getElementById("error_password").innerHTML = "Please enter Password";
		document.getElementById("password").style.borderColor = "red";
		document.getElementById("password").focus();
		return false;
	} else if (document.getElementById("password").value.match(/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/)) {
		document.getElementById("error_password").innerHTML = "";
		document.getElementById("password").style.borderColor = "";

	} else {
		document.getElementById("error_password").innerHTML = "Please enter 8 characters long alphanumeric password having atleast one special character";
		document.getElementById("password").style.borderColor = "red";
		document.getElementById("password").focus();
		return false;
	}

	if (document.getElementById("password").value != document
			.getElementById("conf_password").value) {
		document.getElementById("error_conf_password").innerHTML = "Password and confirm password should match";
		document.getElementById("conf_password").style.borderColor = "red";
		document.getElementById("password").style.borderColor = "red";
		return false;
	} else {
		document.getElementById("error_conf_password").innerHTML = "";
		document.getElementById("conf_password").style.borderColor = "";
		document.getElementById("password").style.borderColor = "";
	}
	
	if (document.getElementById("phone").value == '') {
		document.getElementById("error_phone").innerHTML = "Please enter valid mobile number";
		document.getElementById("phone").style.borderColor = "red";
		document.getElementById("phone").style.borderColor = "red";
		return false;
	} else {
		document.getElementById("error_phone").innerHTML = "";
		document.getElementById("phone").style.borderColor = "";
		document.getElementById("phone").style.borderColor = "";
	}
	
	

}
function uploadImage($uid) {
	var data = new FormData();
	data.append('file', $('#myinput')[0].files[0]);
	$.ajax({
		url : '/home/uploadLogo',
		type : 'POST',
		processData : false,
		contentType : false,
		data : data,
		success : function(html) {
			// alert(html);
			$("#logoSuccess").html(html);
		}

	});
}

function getState() {
	var country_id = $("#company_country").val();

	// passing the data to the ajax function
	// that is further passed to the controller

	datastr = '&country_id=' + country_id;

	// ajax function starts
	$.ajax({
		type : 'post', // defining the ajax type
		url : '/register/getState', // calling the
		// controller with the action involved
		dataType : 'html', // datatype
		data : datastr, // passing the data used for
		// operation
		success : function(result) {
			// alert(result);
			$("#company_state").html(result);
                        $('.selectpicker').selectpicker('refresh');

		}
	});
}

function getIntraLocality() {
	if ($("#states_multipleSelect").val() != '') {
		var city_array = $("#states_multipleSelect").val();

		// passing the data to the ajax function
		// that is further passed to the controller

		datastr = '&cities=' + city_array;

		// ajax function starts
		$.ajax({
			type : 'post', // defining the ajax type
			url : '/register/getIntraLocality', // calling the
			// controller with the action involved
			dataType : 'html', // datatype
			data : datastr, // passing the data used for
			// operation
			success : function(result) {
				// alert(result);
				$("#locality_multiple").html(result);
				 $('#locality_multiple').selectpicker('refresh');

			}
		});
	}
}
function getpmCity() {
	if ($("#states_multiple").val() != '') {
		var state_array = $("#states_multiple").val();

		// passing the data to the ajax function
		// that is further passed to the controller

		datastr = '&stateList=' + state_array;

		// ajax function starts
		$.ajax({
			type : 'post', // defining the ajax type
			url : '/register/getpmCity', // calling the
			// controller with the action involved
			dataType : 'html', // datatype
			data : datastr, // passing the data used for
			// operation
			success : function(result) {
				// alert(result);
				$("#city_multiple").html(result);
				 $('#city_multiple').selectpicker('refresh');

			}
		});
	}
}



/**********************Validate email & phone************************** */

/** VALIDATION RULES BY REGEX **/
function validateName(name) {
	var namePattern = /^([a-zA-Z ])*$/;
	if (namePattern.test(name))
		nameresult = true;
	else
		nameresult = false;
	return nameresult;
}
function validatePhone(inputvalue) {
	var pattern = /^(?:\+?\d{2}[ -]?[\d -][\d -]+)$/;
	if (pattern.test(inputvalue)) {
		return true;
	} else {
		return false;
	}
}
function validateEmail(email) {
	var reg = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9-]+\.[a-zA-Z]{2,4}$/;
	if (reg.test(email))
		testresults = true;
	else
		testresults = false;
	return (testresults);
}
function validateTerritory(name) {
	var namePattern = /^([a-zA-Z ])*$/;
	if (namePattern.test(name))
		nameresult = true;
	else
		nameresult = false;
	return nameresult;
}

/** ********************Validate email & phone************************** */
function homeServiceLogin(serviceClicked,page){
	$("#user_clicked_service").val(serviceClicked);
	$("#user_clicked_page").val(page);

	$('#login-modal').modal('show');
}

function verifyEmail(eVal){
    var status = false;     
    if(eVal!=''){
    	var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
	    if (eVal.search(emailRegEx) == -1) {
	      $(".validEmailCheck").html('<p id="error_user_email">Please enter valid email</p>');
	      status = false;
	    }else {
	      status = true;
	    }
    }
    return status;
}


function sendOneTimePassword() {
		var phone = $("#phone").val();
		$.ajax({
			type: "POST",
			url: "/register/otp",
			data: {
				'phone': phone,
			},
			beforeSend: function() {
				$('#registerSubmit').text('Processing...');
				$('#registerSubmit').prop('disabled', true);
			},
			success: function(jsonData) {
				$('#enableOtp').css('display','block');
				$('#registerSubmit').text('Submit');
				$('#registerSubmit').prop('disabled', false);
			}
		}, "json");
}

$(document).ready(function(){
	$("#phone").keypress(function (e) {
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
               return false;
        }
   });

   jQuery.validator.addMethod("pwscheck", function(value, element) {
	return this.optional(element) || /^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/i.test(value);
}, "Password contain atleast one digit one special char and one alphabet");
   
  $("#registerSubmit").on("click",function() {
	 if($("#login-form").valid()) {
		if($("#phone") .valid()) {
			sendOneTimePassword();
		}
	 }
	 
  });
  
  $('#login-form').validate({
	  errorClass: "error-1",
		rules:{
			user_email:{
				required:true,
				email: true,
				remote: {
					url : '/checkExistence',
					type: "post"
				 }
			},
			conf_email:{
				required:true,
				email: true,
				equalTo: '#user_email'
			},
			password:{
				required:true,
				minlength: 8,
                maxlength: 30,
                pwscheck: true
			},
			conf_password:{
				required:true,
				equalTo: '#password'
			},
			phone: {
				required:true,
				remote:{
					url: '/checkExistence',
					type: "post"
				}
			},
			
		},
		messages: {
			user_email:{
				remote: "Email already in use!"
			},
			phone:{
				remote: "Phone already in use!"
			}
		}
		
		
	 });
   
});

