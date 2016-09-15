$(function() {
jQuery.validator.addMethod("threebytwovalidations", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,3}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    }else{
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 100) / 100;
	}else{
		return "This field is required."
	}
    var count_value = /^\d{1,3}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "Charges should be less than 1000"
    }	
    else if(parseFloat(element.value)>0){
        return "Charges is truncated to 2 decimals"
    }else{
        return "Charges enter value greater than 0"
    }

});
jQuery.validator.addMethod("fivedigitsvalidation", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,5}$/i.test(parseFloat(element.value));
    }else{
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 1000) / 1000;
	}
    var count_value = /^\d{1,5}$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "The value should be less than 99999"
    }	
    else if(parseFloat(element.value)>0){
        return "The value is truncated to 3 decimals"
    }else{
        return "Please enter value greater than 0"
    }

});
//validation of seller post creation
	$("#posts-form_relocationgm").validate({
		ignore: "input[type='text']:hidden",
		rules : {
			
			"valid_from" : {required : true},
			"valid_to" : {required : true},
//			"from_location" : {required : true},
//			"from_location_id" : {required : true},
			"to_location" : {required : true},
			"to_location_id" : {required : true},
			
			
			"city_orientation" : {
				number : true,
                                fivedigitsvalidation: true,
			},
			
			"home_view" : {
				number: true,
                                fivedigitsvalidation: true,
			},

			
			"home_search" : {
				number: true,
                                threebytwovalidations: true,
			},
                        "frro" : {
				number: true,
                                fivedigitsvalidation: true,
			},
                        "visa_extension" : {
				number: true,
                                fivedigitsvalidation: true,
			},
			"settling_services" : {
				number: true,
                                fivedigitsvalidation: true,
			},
                        "cross_cultural_training" : {
				number: true,
                                fivedigitsvalidation: true,
			},
			
			
			"cancellation_charge_price" : {
				number: true,
				fourbytwovalidationswithzero: {
                                            depends: function(element) {
                                                    if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
                                                            return true;
                                                    }else{
                                                            return false;
                                                    }

                                            }
                                }
                            },
			"docket_charge_price" : {
				number: true,
				fourbytwovalidationswithzero: {
                                    depends: function(element) {
                                            if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
                                                    return true;
                                            }else{
                                                    return false;
                                            }

                                    }
                                }
			},
                        

			"terms_condtion_types1" : {
				number: true,
				fourbytwovalidationswithzero: {
                                    depends: function(element) {
                                            if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
                                                    return true;
                                            }else{
                                                    return false;
                                            }

                                    }
                                }
			},
			"terms_condtion_types2" : {
				number: true,
				fourbytwovalidationswithzero: {
                                    depends: function(element) {
                                            if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
                                                    return true;
                                            }else{
                                                    return false;
                                            }

                                    }
                                }
			},
			"terms_condtion_types_1" : {
				number: true,
				fourbytwovalidationswithzero: {
                                    depends: function(element) {
                                            if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
                                                    return true;
                                            }else{
                                                    return false;
                                            }

                                    }
                                }
			},
			"terms_condtion_types_2" : {
				number: true,
				fourbytwovalidationswithzero: {
                                    depends: function(element) {
                                            if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
                                                    return true;
                                            }else{
                                                    return false;
                                            }

                                    }
                                }
			},
			"terms_condtion_types_3" : {
				number: true,
				fourbytwovalidationswithzero: {
                                    depends: function(element) {
                                            if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
                                                    return true;
                                            }else{
                                                    return false;
                                            }

                                    }
                                }
			},
			"buyer_list_for_sellers" : {
				required : {
					depends: function(element) {
						if ($('#sellerpoststatus').val() == 1){
							if ($(".create-posttype-service:checked").val() == 2){
								return true;
							}else{
								return false;
							}
						}
					}
				}
			},
			"accept_payment_ptl[]" : {
				required : {
					depends: function(element) {
						if ($('#sellerpoststatus').val() == 1){
							if ($('#payment_options').val() == 1){
								return true;
							}else{
								return false;
							}
						}
					}
				}
			},
			"credit_period_ptl" : {
				required : {
					depends: function(element) {
						if ($('#sellerpoststatus').val() == 1){
							if ($('#payment_options').val() == 4){
								return true;
							}else{
								return false;
							}
						}
					}
				},
				creditvalidation: {
					depends: function(element) {
						if ($('#sellerpoststatus').val() == 1){
							if ($('#payment_options').val() == 4){
								return true;
							}else{
								return false;
							}
						}
					}
				}
			},
			"accept_credit_netbanking[]" : {
				required : {
					depends: function(element) {
						if ($('#sellerpoststatus').val() == 1){
							if ($('#payment_options').val() == 4){
								return true;
							}else{
								return false;
							}
						}
					}
				}
			},
			"agree" : {
				required : {
					depends: function(element) {
						if ($('#sellerpoststatus').val() == 1){
							return true;
						}else{
							return false;
						}

					}
				}
			},
		},
		errorPlacement: function(error, element) {
			if($(element).attr('type') == "checkbox" || $(element).attr('type') == "radio" ){
				$(element).parent('div').parent('div').after(error);
			}else{
				$(element).parent('div').after(error);
			}
		},
		messages : {
			"valid_from" : {
				required : "Valid From should be valid",
			},
			"valid_to" : {
				required : "Valid To should be valid",
			},
			
			"to_location" : {
				required : "",
			},
			"to_location_id" : {
				required : "To Location should be valid",
			},
			
			
			"city_orientation" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
			"home_view" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
			"home_search" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
			"frro" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
			"visa_extension" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
			"settling_services" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
                        "cross_cultural_training" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
			"cancellation_charge_price" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
			"docket_charge_price" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
		},
		submitHandler : function(form) {
			form.submit();
		}
	});
        
        $('#add_quote_seller_relocationgm').click(function(e) {
            e.preventDefault();
            if($('#posts-form_relocationgm').valid()) {
                $('#posts-form_relocationgm').submit();
                $("#add_quote_seller_relocationgm").prop('disabled', true);
                $("#add_quote_seller_id_relocationgm").prop('disabled', true);
            }
        
    });
        
});