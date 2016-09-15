$(function() {
    
    $('#add_quote_seller_id_relocationgm').click(function(e) {
        
		e.preventDefault();
        $('#posts-form_relocationgm').valid();
        var flag=0;
        $( ".gm_service_rates" ).each(function( index ) {
            if($( this ).val()!=''){
                flag=1;
            }
        });
        //var id=$('.request_rows').children().size();
        if(flag==0){
            $("#erroralertmodal .modal-body").html("Please add atleast one rate from the list");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        }

		if($("#posts-form_relocationgm").valid()) {
			var submitData=$("#posts-form_relocationgm").serialize();
			var btnName = $('#add_quote_seller_id_relocationgm').attr('name');
			$("#add_quote_seller_id_relocationgm").prop('disabled', true);
			$("#add_quote_seller_relocation").prop('disabled', true);
			var btnVal = $('#add_quote_seller_id_relocationgm').val();
			var btn = '&'+btnName+'='+btnVal;
			submitData += btn;
			$.ajax({
				type: "POST",
				url: '/relocationsellerpostcreation',
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
				data: submitData, // serializes the form's elements.
				success: function(msg)
				{

					$("#erroralertmodal .modal-body").html("Your request for post has been successfully posted to the relevant buyers. Your transacton id is "+msg+". You would be getting the enquiries from the buyers online.");
					$("#erroralertmodal").modal({
						show: true
					}).one('click','.ok-btn',function (e){
						window.location="/sellerlist";

					});
				}
			});
		}

	});
    
        $(document).on('focus click keyup keypress blur change', '#to_location', function() {
            $( "#to_location" ).autocomplete({
                source: "/autocomplete?fromlocation=",
                minLength: 1,
                select: function(event, ui) {
                    $('#to_location').val(ui.item.value);
                    $('#to_location_id').val(ui.item.id);
                    $(this).closest("form").validate().element($('#to_location_id'));
                    $('#seller_district_id').val(ui.item.dist_id);
                    /*Need to add this below class to every autocomplete: Shriram */
                    $("#to_location").addClass("clsAutoDisable");
                },
                
            });
	});
    
jQuery.validator.addMethod("threebytwovalidations", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,3}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    }else{return true;
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
    	return this.optional(element) || /^\d{1,5}?$/i.test(parseFloat(element.value));
    }else{
        return true
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 1000) / 1000;
	}
    var count_value = /^\d{1,5}?$/i.test(parseFloat(element.value));console.log(count_value);
    if(count_value == false){
     	return "The value should be less than 99999"
    }	
    else{
        return "Please enter value greater than 0"
    }

});
jQuery.validator.addMethod("fivebytwovalidations", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,5}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    }else{return true;
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 1000) / 1000;
	}
    var count_value = /^\d{1,5}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "Please enter less than 100000 only"
    }	
    else if(parseFloat(element.value)>0){
        return "The value truncated to 2 decimals"
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
                                fivebytwovalidations: true,
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
        
        $('#add_quote_seller_id_relocation_update,#add_quote_seller_relocationgm').click(function(e) {
            e.preventDefault();
            $('#posts-form_relocationgm').valid();
        //var id=$('.request_rows').children().size();
        var flag=0;
        $( ".gm_service_rates" ).each(function( index ) {
            if($( this ).val()!=''){
                flag=1;
            }
        });
        if(flag==0){
            $("#erroralertmodal .modal-body").html("Please add atleast one rate type from the list");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        }else{
            if($('#posts-form_relocationgm').valid()) {
                $('#posts-form_relocationgm').submit();
                $("#add_quote_seller_relocationgm").prop('disabled', true);
                $("#add_quote_seller_id_relocationgm").prop('disabled', true);
            }
        }
    });

	//******** Multyline Submit quote *******//

	$(".relocationgm_quote_submit").click(function(){
        var formid = $(this).parent().parent(".relocationgm_quote_submit").attr("id");
		var serviceid = $('#serviceid').val();
		var seller_post_item_id = $('#seller_post_item_id').val();
		var className = "#relocgm_" + $(this).attr('name');
		var formvalues = $(className).serialize();
		var buttonId = $(this).attr('id');
		var removeString = 'submitform_quote_';
		var rowNo = buttonId.replace(removeString, '');
		var buyerquoteid = $('#buyerquoteid_' + rowNo).val();

        var byerquote_id = $(this).attr('quote-data');
		var selected_services = $("#quote_ids_"+byerquote_id).val();
		var flag=0;
		/*$(className).validate({
			ignore: "input[type='text']:hidden",
		});*/

		if(selected_services!="" && selected_services!=",") {
			check_quote_item_ids = $("#quote_ids_"+byerquote_id).val().split(',');
			check_quote_item_ids.sort();
			jQuery.each(check_quote_item_ids, function( i, val ) {
				if(val!='' && $('#relgm_quote_'+val).val()==''){
		            $("#erroralertmodal .modal-body").html("Please enter quote");
		            $("#erroralertmodal").modal({
		                show: true
		            });
		            flag++;
		           //return false;
		            //$('#relgm_quote_'+val).focus();
				}
			});
			if(flag==0){
				datavaluses = {};
				if($("#from_search_page").length){
					datavaluses.from_location_id = $('#from_location_id_' + rowNo).val();
					datavaluses.to_location_id = $('#to_location_id_' + rowNo).val();
					datavaluses.valid_from = $('#valid_from_' + rowNo).val();
					datavaluses.valid_to = $('#valid_to_' + rowNo).val();
					datavaluses.post_rate_card_type = $('#post_rate_card_type_' + rowNo).val();
					datavaluses.enquiry_volume = $('#enquiry_volume_' + rowNo).val();
					datavaluses.paymentoptions = $('#payment_options').val();
					datavaluses.tracking = $('#tracking_' + rowNo).val();

				}else{
					datavaluses.from_location_id = $('#from_loc_' + rowNo).val();
					datavaluses.to_location_id = $('#to_loc_' + rowNo).val();
					datavaluses.post_rate_card_type = $('#post_rate_' + rowNo).val();
				}

				datavaluses.formvalues = formvalues;
				datavaluses.buyerquote_id = rowNo;
				datavaluses.seller_post_item_id = seller_post_item_id;
				datavaluses.ratepercft = $('#od_charges_' + rowNo).val();
				datavaluses.transport_charges = $('#transport_charges_' + rowNo).val();
				datavaluses.transport_days = $('#transport_days_' + rowNo).val();
				datavaluses.transport_units = $('#transport_units_' + rowNo).val();
				datavaluses.creating_charges = $('#creating_charges_' + rowNo).val();
				datavaluses.storage_charges = $('#storage_charges_' + rowNo).val();
				datavaluses.other_charges = $('#other_charges_' + rowNo).val();
				datavaluses.international_type = $('#international_type_' + rowNo).val();
				
				
				datavaluses.property_type = $('#property_type_' + rowNo).val();
				datavaluses.load_category = $('#load_category_' + rowNo).val();
				datavaluses.vehicle_type = $('#vehicle_type_' + rowNo).val();
				datavaluses.car_size = $('#car_size_' + rowNo).val();
				datavaluses.escort_charges = $('#escort_charges_' + rowNo).val();
				datavaluses.handyman_charges = $('#handyman_charges_' + rowNo).val();
				datavaluses.property_search = $('#property_search_' + rowNo).val();
				datavaluses.enquiry_volume = $('#enquiry_volume_' + rowNo).val();
				datavaluses.brokerage_charges = $('#brokerage_charges_' + rowNo).val();
				datavaluses.buyerid = $('#buyer_id_' + rowNo).val();
				datavaluses.total_price = $('#total_price_' + rowNo).val();
				//console.log(datavaluses);return false;
				if (serviceid == 15) {
					var con = $('#od_charges_' + rowNo).val().trim();
				}
				if (con != "") {
					$.ajax({
						type: "POST",
						url: "/sellersubmitquote",
						beforeSend: function () {
							$.blockUI({
								overlayCSS: {
									backgroundColor: '#000'
								}
							});
						},
						complete: function () {
							$.unblockUI();
						},

						data: datavaluses,
						success: function (jsonData) {

							$("#erroralertmodal .modal-body").html('Quote submitted successfully.');
							$("#erroralertmodal").modal({
								show: true
							}).one('click', '.ok-btn', function (e) {
								location.reload();
							});

						}
					}, "json");
				}
			}else{
				return false;
			}

		}else{
            $("#erroralertmodal .modal-body").html("Please select atleast on service");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
		}
	});

});

function checkRLGMPostitem(str)
{
	quote_id = $("#"+str).attr('quote-data');
	quote_item_ids = $("#quote_ids_"+quote_id).val();
	quote_item_id_val = $("#"+str).val();
	if(quote_item_ids){
		quote_items = quote_item_ids.split(',');	
		//console.log(quote_items);
		if(jQuery.inArray( quote_item_id_val, quote_items )==-1){
			$('#relgm_quote_'+quote_item_id_val).attr('disabled',false);
			update_quote_item_ids=quote_item_ids+','+quote_item_id_val;
		}else{
			$('#relgm_quote_'+quote_item_id_val).val('');
			$('#relgm_quote_'+quote_item_id_val).attr('disabled',true);
			update_quote_item_ids = jQuery.grep(quote_items, function(value) {
			  return value != quote_item_id_val;
			});		
			update_quote_item_ids.sort();
			update_quote_item_ids.toString();	
		}
	}else{
		$('#relgm_quote_'+quote_item_id_val).attr('disabled',false);
		update_quote_item_ids=quote_item_id_val;
	}
	$("#quote_ids_"+quote_id).val(update_quote_item_ids);
}