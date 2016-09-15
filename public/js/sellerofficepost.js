$(document).ready(function() {
	
	/**
	 * Sumanth 
	 * Code written for add row for slabs adding 
	 * for save as draft button
	 **/
	$('.slab-box').click(function(e) {
		slabboxValidation(true);
	});

	function slabboxValidation(add){
		
		var num = parseInt($('#price_slap_hidden_value').val()) + 1;
	    /**
	     * Jagadeesh
	     * Dynamic values Valdiation
	     */
	    if(num==1){
	    	var high_price_value = $("#max_distance_slab").val();
			var max_weight_accepted="";
			var index = "";

	    }else {
	    	var high_price_value = $("#max_distance_slab_"+(num-1)).val();
			var max_weight_accepted="";
			var index="_"+(num-1);
	    }

		if(!$("#min_distance_slab"+index).val() && index!=""){
			$("#erroralertmodal .modal-body").html("Min Distance Required");
	        $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
	        	 $("#min_distance_slab"+index).focus();
	           });	
	       return false;
		}else if(!$("#max_distance_slab"+index).val()){
			$("#erroralertmodal .modal-body").html("Max Distance Required");
	        $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
	        	 $("#max_distance_slab"+index).focus();
	           });	
	       return false;
		}else if(parseInt($("#max_distance_slab"+index).val())<=parseInt($("#min_distance_slab"+index).val())){
			$("#erroralertmodal .modal-body").html("Max Distance Not Less Than Or Equal to Min Distance");
	        $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
	        	 $("#max_distance_slab"+index).focus();
	           });	
	       return false
		}else if(!$("#transport_charges_slab"+index).val()){
			$("#erroralertmodal .modal-body").html("Transport Charges Required");
	        $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
	        	 $("#transport_charges_slab"+index).focus();
	           });	
	        return false;
		}else{

	    	if(add==true)	{
		    	$('#price_slap_hidden_value').val(num);
			    $("#remove_item_" + (num-1)).find('a').remove();
		    	var box_html = $('<div id="remove_item_' + num + '" class="add-price-slap table-row inner-block-bg"><div class="col-md-3 form-control-fld"><div class="input-prepend"><input type="text" readonly id="min_distance_slab_' + num + '" name="min_distance_slab_' + num + '"  placeholder= "Min Distance" class="form-control form-control1 dynamic_low_weight clsROMMinKm" value="'+high_price_value+'" required /></div></div><div class="col-md-3 form-control-fld"><div class="input-prepend"><input type="text" class="form-control form-control1 update_txt dynamic_high_weight clsROMMaxKm" placeholder ="Max Distance" name="max_distance_slab_' + num + '" value="'+max_weight_accepted+'" id="max_distance_slab_' + num + '" onblur="javascript:checkPriceForInerment(this.value,this.id)" required></div></div><div class="col-md-3 form-control-fld"><div class="input-prepend"><input type="text" class="form-control form-control1 update_txt dynamic_prices clsROMTransportChargespKm" placeholder ="Transport Charges" name="transport_charges_slab_' + num + '" value="" id="transport_charges_slab_' + num + '" required /></div></div><div class="col-md-1 form-control-fld padding-left-none  padding-top-7"><a href="javascript:void(0)" class="remove-box-prices-office"><i class="fa fa-trash red" title="Delete"></i></a></div></div>');
		        box_html.hide();
		        $('.slabtable div.add-price-slap:last').after(box_html);
		        box_html.fadeIn('slow');

	            if (num == 1){
	            	$('#max_distance_slab'+index).prop('readonly', true);
	    		}else{
	    			$('#max_distance_slab'+index).prop('readonly', true);
	    		}
	    	}else{
	    		return true;
	    	}	
	        //readonlyProperty();
		}		
	}

		$('.slabtable').on('click', '.remove-box-prices-office', function(){
        	var remove_item_id = $('#price_slap_hidden_value').val();
        	remove_id = "#remove_item_"+remove_item_id;
            if($(remove_id).remove()){
            	var delete_value = $('#price_slap_hidden_value').val()-1;
            	$('#price_slap_hidden_value').val(delete_value);
        		var index_value = $('#price_slap_hidden_value').val();

        		var max_weight_accepted = '';
        		var high_price_accepted = parseFloat($('#high_weight_slab_'+index_value).val());
        		 if (index_value == 0){
        			 var high_price_defualt = $("#max_distance_slab").val();
        			 $("#max_distance_slab").prop('readonly', false);
         		}else{
         			$('#max_distance_slab_'+index_value).prop('readonly', false);
         		}      		
            }

            if($(".price-slap-update > div[id*=remove_item_]").length!=1){
            	$(".price-slap-update > div[id*=remove_item_"+index_value+"]:last-child").append('<div class="col-md-3 form-control-fld padding-left-none padding-top-7"><a class="remove-box-prices-office" href="javascript:void(0)"><i class="fa fa-trash red" title="Delete"></i></a></div>');
            }

            if($(".price-slap-add > div[id*=remove_item_]").length!=0){
            	$(".price-slap-add > div[id*=remove_item_"+index_value+"]").append('<div class="col-md-3 form-control-fld padding-left-none padding-top-7"><a class="remove-box-prices-office" href="javascript:void(0)"><i class="fa fa-trash red" title="Delete"></i></a></div>');
            }

            return false;
            
        });
	
	/**
	 * Sumanth 
	 * Code written for validation 
	 * for save as draft button
	 **/
	$('#add_quote_seller_id_relocation_office').click(function(e) {
		
        e.preventDefault();
		if(slabboxValidation(false))
		{
	        $('#posts-form_relocation_office').valid();
			

			if($("#posts-form_relocation_office").valid()) {
				var submitData=$("#posts-form_relocation_office").serialize();
				var btnName = $('#add_quote_seller_id_relocation_office').attr('name');
				$("#add_quote_seller_id_relocation_office").prop('disabled', true);
				$("#add_quote_seller_relocation_office").prop('disabled', true);
				var btnVal = $('#add_quote_seller_id_relocation_office').val();
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
						if(msg==0){
							$("#erroralertmodal .modal-body").html("Post already exist with this details");
							$("#erroralertmodal").modal({
								show: true
							});
							$("#add_quote_seller_id_relocation_office").removeAttr("disabled");
							$("#add_quote_seller_relocation_office").removeAttr("disabled");
							
							
						}else{
							$("#erroralertmodal .modal-body").html("Your request for post has been successfully posted to the relevant buyers. Your transacton id is "+msg+". You would be getting the enquiries from the buyers online.");
							$("#erroralertmodal").modal({
								show: true
							}).one('click','.ok-btn',function (e){
								window.location="/sellerlist";

							});
						
					  }
					}	
				});
			}
		}	
	});


	/**
	 * Sumanth 
	 * Code written for validation 
	 * for save as draft button
	 **/
	$('#update_quote_seller_id_relocation_office').click(function(e) {

        e.preventDefault();
		if(slabboxValidation(false))
		{        
	        $('#posts-form_relocation_office').valid();
			sid = $('#update_id').val();
		
			if($("#posts-form_relocation_office").valid()) {
				var submitData=$("#posts-form_relocation_office").serialize();
				var btnName = $('#add_quote_seller_id_relocation_office').attr('name');
				$("#add_quote_seller_id_relocation_office").prop('disabled', true);
				$("#add_quote_seller_relocation_office").prop('disabled', true);
				var btnVal = $('#add_quote_seller_id_relocation_office').val();
				var btn = '&'+btnName+'='+btnVal;
				submitData += btn;
				$.ajax({
					type: "POST",
					url: '/relocation/updatesellerpost/'+sid,
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
		}

	});
	
	/**
	 * Sumanth 
	 * Code written for validation 
	 * for Confirm button
	 **/
	
	 $('#add_quote_seller_relocation_office').click(function(e) {
	        e.preventDefault();
	    if(slabboxValidation(false)){
	        $('#posts-form_relocation_office').valid();
	        
	        if($('#posts-form_relocation_office').valid()) {
	        	
	        	data="city="+$("#from_location_id").val()+"&from_date="+$("#datepicker").val()+"&to_date="+$("#datepicker_to_location").val();
				 $.ajax({
			            type: "POST",
			            url : "/chekcksellerofficepost",
			            data : data,
					   success : function(dataCount){
						   if(dataCount==0){
							    $('#posts-form_relocation_office').submit();
				                $("#add_quote_seller_relocation_office").prop('disabled', true);
				                $("#add_quote_seller_id_relocation_office").prop('disabled', true);
						   }else{
							$("#erroralertmodal .modal-body").html("Post already exist with this city and dates");
						        $("#erroralertmodal").modal({
						            show: true
						        });   
						   }
						  }
			       },"json");
	                
	            }
	    }
	});

	 /**
		 * Sumanth 
		 * Code written form validation 
		 * for Confirm button
		 **/

	$("#posts-form_relocation_office").validate({
		ignore: "input[type='text']:hidden",
		rules : {
			"post_rate_card_type" : {required : true},
			"valid_from" : {required : true},
			"valid_to" : {required : true},
			"from_location" : {required : true},
			"from_location_id" : {required : true},
			"rate_per_cft" : {required : true},
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

            "tracking" : {
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
			"from_location" : {
				required : "",
			},
			"from_location_id" : {
				required : "From Location should be valid",
			},
			"cancellation_charge_price" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
			
		},
		submitHandler : function(form) {
			form.submit();
		}
	});
	
	/*For submitting quote form market leads validation
	 * Sumanth
	 */
	var rules = new Object();
	var messages = new Object();
	$('.relocationoffice_submit_quote input:text').each(function() {
		rules[this.name] = { required: true,decimalvalidation: true };
		messages[this.name] = { required: 'This field is required' };
	});
	$('.relocationoffice_submit_quote input:checkbox').each(function() {
		rules[this.name] = { required: true};
		messages[this.name] = { required: 'This field is required' };
	});
	$('.relocationoffice_submit_quote select').each(function() {
		rules[this.name] = { required: true };
		messages[this.name] = { required: 'This field is required' };
	});
	

	var validator = $(".relocationoffice_submit_quote").validate({
		ignore: "input[type='text']:hidden,input[type='checkbox']:hidden",
		rules: rules,
		messages: messages,
		errorPlacement: function(error, element) {
	   	$(element).parent().append(error);
	    },

	});

	$('.relocationoffice_submit_quote input').blur(function(){

		//alert("hello");
		var totalprice = 0;
		var name = $(this).attr("name");
		id = name.substring(name.lastIndexOf('_'));
		
		  //totalprice = parseInt($("#vehicle_cost_"+$("#vehicle_type"+id).val()).val());
		if($("#doortodoor_charges"+id).val() != "")
			totalprice = parseFloat($("#doortodoor_charges"+id).val())+' /-';
			
		
		$('#total_price_display'+id).html(totalprice);
		$('#total_price'+id).val(totalprice);

	});
	
	
	$(".relocationoffice_quote_submit").click(function(){
		if($('.relocationoffice_submit_quote').valid()) {
			var serviceid = $('#serviceid').val();
			var seller_post_item_id = $('#seller_post_item_id').val();
			dataObj = {};
			var className = "#reloc_" + $(this).attr('name');
			var formvalues = $(className).serialize();
			var buttonId = $(this).attr('id');
			var removeString = 'submitform_quote_';
			var rowNo = buttonId.replace(removeString, '');
			var buyerquoteid = $('#buyerquoteid_' + rowNo).val();

			
			
			
			datavaluses = {};
			if($("#from_search_page").length){
				datavaluses.from_location_id = $('#from_location_id_' + rowNo).val();
				datavaluses.valid_from = $('#valid_from_' + rowNo).val();
				datavaluses.valid_to = $('#valid_to_' + rowNo).val();
				datavaluses.enquiry_volume = $('#enquiry_volume_' + rowNo).val();
				datavaluses.paymentoptions = $('#payment_options').val();
				datavaluses.tracking = $('#tracking_' + rowNo).val();

			}else{
				datavaluses.from_location_id = $('#from_loc_' + rowNo).val();
				
			}

			datavaluses.formvalues = formvalues;
			datavaluses.buyerquote_id = rowNo;
			datavaluses.seller_post_item_id = seller_post_item_id;
			datavaluses.doortodoor_charges = $('#doortodoor_charges_' + rowNo).val();
			datavaluses.cancellation_charges = $('#cancellation_charges_' + rowNo).val();
			datavaluses.buyerid = $('#buyer_id_' + rowNo).val();
			datavaluses.total_price = $('#total_price_' + rowNo).val();
			//console.log(datavaluses);return false;
			//console.log(con);
			console.log(rowNo);
			var con = $('#doortodoor_charges_' + rowNo).val().trim();
			
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
		}
	});
	
	/*For submitting quote form market leads validation
	 * Sumanth
	 */

	//Sellersearchfor buyer relocation office domestic form validation
	$("#relocation_domestic_office_sellersearch_buyers").validate({ // initialize the
	    // plugin
	    ignore: "input[type='text']:hidden",
	    rules: {
	        "from_location": {
	            required: true,
	        },
	        "from_location_id": {
	            required: true,
	        },
	        "valid_from": {
	            required: true,
	        },	 
//	        "valid_to": {
//	            required: true,
//	        },	
	        
	    },
	    errorPlacement: function(error, element) {
	    	$(element).parent('div').after(error); 
	    },
	    messages: {
	        "from_location": {
	            required: "",
	        },
	        "from_location_id": {
	            required: "City should be valid",
	        },
	        "valid_from": {
	            required: "Select Dispatch Date",
	        },	 
//	        "valid_to": {
//	            required: "Enter Delivery Date",
//	        },	
	        
	    },
	    submitHandler: function(form) { // for demo
			$(element).parent('div').after(error);
	    }
	});



});
