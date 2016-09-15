function validateBooknowFieldsGM(sourceLocationType, 
        consignorName, consignorNumber, consignorEmail, consignorAddress,
        consignorPin, rowNo) {
        var sourceLocationTypeErrorMessage,  
              consignorNameErrorMessage, consignorNumberErrorMessage,
            consignorEmailErrorMessage, consignorAddressErrorMessage, consignorPinErrorMessage, consigneeNameErrorMessage;
        var isValidErrorNumber = 0;
        //source other text
        if (sourceLocationType == '11') {
            if ($('#buyer_counter_offer_source_location_type_text').val() == '' || $('#buyersearch_booknow_offer_source_location_type_text').val() == '') {
                sourceLocationTypeErrorMessage = "Please enter other Source Location Type!";
                isValidErrorNumber++;
            }else {
                sourceLocationTypeErrorMessage = '';
            }
        } 
        $('#buyer_counter_offer_source_location_type_text_error_' + rowNo).html(sourceLocationTypeErrorMessage);
        $('#buyersearch_booknow_offer_source_location_type_text_error_' + rowNo).html(sourceLocationTypeErrorMessage);
        if (sourceLocationType == '0') {
            sourceLocationTypeErrorMessage = 'Please select Source Location Type!';
            isValidErrorNumber++;
        } else {
            sourceLocationTypeErrorMessage = '';
        }
        $('#buyer_counter_offer_source_location_type_error_' + rowNo).html(sourceLocationTypeErrorMessage);
         
        if (!consignorName ) {
            consignorNameErrorMessage = "Please enter consignor name!";
            isValidErrorNumber++;
        } else if (consignorName && !validateName(consignorName)) {
            consignorNameErrorMessage = "Please enter proper consignor name with 50 characters long!";
            isValidErrorNumber++;
        } else {
            consignorNameErrorMessage = '';
        }
        $('#buyer_counter_offer_consignor_name_error_' + rowNo).html(consignorNameErrorMessage);
        
        if (!consignorNumber) {
            consignorNumberErrorMessage = "Please enter consignor mobile!";
            isValidErrorNumber++;
        } else if (consignorNumber && !validatePhone(consignorNumber)) {
            consignorNumberErrorMessage = "Please enter mobile number 10 characters long!";
            isValidErrorNumber++;
        } else {
            consignorNumberErrorMessage = '';
        }
        $('#buyer_counter_offer_consignor_number_error_' + rowNo).html(consignorNumberErrorMessage);
        
        if (consignorEmail && !validateEmail(consignorEmail)) {
            consignorEmailErrorMessage = "Please enter proper consignor email!";
            isValidErrorNumber++;
        } else {
            consignorEmailErrorMessage = '';
        }
        $('#buyer_counter_offer_consignor_email_error_' + rowNo).html(consignorEmailErrorMessage);
        
        if (!consignorAddress) {
            consignorAddressErrorMessage = "Please enter consignor address!";
            isValidErrorNumber++;
        } else {
            consignorAddressErrorMessage = '';
        }
        $('#buyer_counter_offer_consignor_address_error_' + rowNo).html(consignorAddressErrorMessage);
        
        if (!consignorPin) {
            consignorPinErrorMessage = "Please enter consignor pin code!";
            isValidErrorNumber++;
        } else if (consignorPin && !validateIndianZipCode(consignorPin)) {
            consignorPinErrorMessage = "Please enter pincode 6 characters long!";
            isValidErrorNumber++;
        } else {
            consignorPinErrorMessage = '';
        }
        $('#buyer_counter_offer_consignor_pincode_error_' + rowNo).html(consignorPinErrorMessage);
        
        if (isValidErrorNumber > 0) {
            return false;
        } else {
            return true;
        }
    }

function checkAndSetGMBooknow(sourceLocationTypeOther,sourceLocationType,  
        buyerId, sellerId,   consignorName,
        consignorNumber, consignorEmail, consignorAddress, consignorPin, 
        additionalDetails, 
        rowNo,  quoteItemId, postItemId, price, isCheckout, quoteId,
        sellerPostedFromDate, sellerPostedToDate,contractId, enquiryType, contractFromDate, contractToDate,termContractDispatchDate) {
        
		//alert(contractId);
		if (validateBooknowFieldsGM(sourceLocationType, 
            consignorName, consignorNumber, consignorEmail, consignorAddress,
            consignorPin,  rowNo)) {
        	
        	
        	quoteId = (!quoteId) ? '' : quoteId;
            sellerPostedFromDate = (!sellerPostedFromDate) ? '' : sellerPostedFromDate;
            sellerPostedToDate = (!sellerPostedToDate) ? '' : sellerPostedToDate;
            
            contractId = (!contractId) ? '' : contractId;
            enquiryType = (!enquiryType) ? '' : enquiryType;
            contractFromDate = (!contractFromDate) ? '' : contractFromDate;
            contractToDate = (!contractToDate) ? '' : contractToDate;
            var ajaxUrl = (contractId) ? "/settermbuyerbooknow" : "/setbuyerbooknow";
            	
            allData = {
                'sourceLocationType': sourceLocationType,
                
                'sourceLocationTypeOther': sourceLocationTypeOther,
               
                'buyerId': buyerId,
                'sellerId': sellerId,
                'consignorName': consignorName,
                'consignorNumber': consignorNumber,
                'consignorEmail': consignorEmail,
                'consignorAddress': consignorAddress,
                'consignorPin': consignorPin,
                
                'additionalDetails': additionalDetails,
                'buyerCounterOfferId': rowNo,
                'quoteItemId': quoteItemId,
                'postItemId': postItemId,
                'quoteId': quoteId,
                'price': price,
                'sellerPostedFromDate': sellerPostedFromDate, 
                'sellerPostedToDate': sellerPostedToDate,
                'contractId': contractId,
                'enquiryType': enquiryType,
                'contractFromDate': contractFromDate,
                'contractToDate': contractToDate,
                'termContractDispatchDate': termContractDispatchDate,
            };
            
        	var commercial=$("#commerical_type").val();
        	
        	if(commercial==1){
        	$('#booknow-popup').modal({
	            show: 'false'
			  });
        	
        	$("#alldata").val(JSON.stringify( allData ));
        	$("#ajaxurl").val(ajaxUrl);
        	$("#ischeckout").val(isCheckout);
            if($('#buyersearch_booknow_offer_source_location_type_' + rowNo +' option:selected').text() != '') {
             if($('#buyersearch_booknow_offer_source_location_type_' + rowNo +' option:selected').text()=='Others – Specify'){
             $("#source_location").html($('#buyersearch_booknow_offer_source_location_type_text').val());
             //$("#destination_location").html($('#buyersearch_booknow_offer_destination_location_type_text').val());
             }else{
             $("#source_location").html($('#buyersearch_booknow_offer_source_location_type_' + rowNo +' option:selected').text());
             //$("#destination_location").html($('#buyersearch_booknow_offer_destination_location_type_' + rowNo +' option:selected').text());
             }
            } else {
            	
            if($('#buyer_counter_offer_source_location_type_' + rowNo +' option:selected').text()=='Others – Specify'){
            $("#source_location").html($('#buyer_counter_offer_source_location_type_text').val());
            //$("#destination_location").html($('#buyer_counter_offer_destination_location_type_text').val());
            }else{
            $("#source_location").html($('#buyer_counter_offer_source_location_type_' + rowNo +' option:selected').text());
            //$("#destination_location").html($('#buyer_counter_offer_destination_location_type_' + rowNo +' option:selected').text());
             }		
                                
            }        	
        	
        	$("#consignor").html(consignorName);
        	$("#consignor_mobile").html(consignorNumber);
        	$("#consignor_adddress").html(consignorAddress);
        	$("#buyer_user").html($("#buyer_name").val());
                
        	return false;
        	}else{
        	
        		$.ajax({
                    type: "POST",
                    url: ajaxUrl,
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
                    data: allData,
                    success: function(data) {
                        if (data.success) {
                            //$('.buyer_book_now_button_div, .buyer_book_now_content').hide();
                            if(isCheckout != "0"){
                                $( ".fa.fa-shopping-cart" ).trigger("click");
                            } else {
                                $("#erroralertmodal .modal-body").html(data.message);
                                $("#erroralertmodal").modal({
                                    show: true
                                }).one('click','.ok-btn',function (e){
                                    var cart_cnt = parseInt($('div.cart_icon span.superscript').text());
                                    cart_cnt = cart_cnt + 1;
                                    $('div.cart_icon span.superscript').text(cart_cnt);
                                   // if(!contractId) {
                                        var url = $('.buyer_post_details_url').data('url');
                                        window.location = url;
                                        //$(".buyer_book_now_content").slideToggle("500");
                                    //}
                                });
                            }
                        } else {
                            alert(data.message);
                        }
                    }
                }, "json");
        		
        		
        	}
			
            
        }
    }

$(document).ready(function() {

	// Start  : Validation for Buyer Post & Get Quote Create / Update 
		
		//Toggle Spot or Term
		$("#relocationgm_spot").click(function(){
	    	$(".relocation_global_spot_show").show();
	    	$(".relocation_global_term_show").hide();
	    });
	    
	    $("#relocationgm_term").click(function(){
	    	$(".relocation_global_term_show").show();
	    	$(".relocation_global_spot_show").hide();
	    });

	    /***** Start : Spot Post & Getquote *****/
	    	    //Buyer post validation

$("#posts-form_buyer_relocation_gm").validate({
	ignore: "input[type='text']:hidden",
	rules : {
		"to_location" : {required : true},
		"dispatch_date" : {required : true},		
		"agree" : {required : true},
        /*"cartons_1" : {
                    digits: true,
	            threevalidationswithzero: true,
                },
		"cartons_2" : {
                    digits: true,
	            threevalidationswithzero: true,
                },
         "cartons_3" : {
                    digits: true,
	            threevalidationswithzero: true,
                },*/
		"ptlQuoteaccessId": {
                    required: true,
                },
		"seller_list" : {
                    required : {
                    depends: function(element) {
                           if ($(".create-posttype-service:checked").val() == 2){           			
                                   return true;
                           }else{
                                   return false;
                           }
                   }
               }
            },
		
	   },
		errorPlacement: function(error, element) {
				$(element).parent('div').after(error);
		},
		messages : {
			"dispatch_date" : {
				required : "Enter Dispatch Date",
			},
			"to_location" : {
				required: function() {
                                    if ($("#to_location").val() == "") {
                                        return "This field is required.";
                                    } else  {
                                        return "Please enter valid City";
                                    }
                                }
			}
		},
		submitHandler : function(form) {
			if($(".create-posttype-service:checked").val()==2){
                if($(".token-input-list li").length==1){

                    $("#erroralertmodal .modal-body").html("Please add one seller atleast.");
                         $("#erroralertmodal").modal({
                          show: true
                         })
                    return false;	
                }
			}
            var flag=0;
            $( ".service_ids " ).each(function( index ) {
                if($(this).val()!=""){
                    flag=1;
                }
            });
            if(flag==0){
                $("#erroralertmodal .modal-body").html("Please add one service type atleast.");
                     $("#erroralertmodal").modal({
                      show: true
                     })
                return false;
			}
			//return true;
			form.submit();
		}
	});


	$('.service-box').click(function(e) {
        var is_term ='term_';
        if($("#relocationgm_spot").is(':checked')){
           is_term = '';
        }    
		$('#'+is_term+'relgm_service_type').prop('disabled',false);
        var servicetype = $('#'+is_term+'relgm_service_type').val();
        if(servicetype != 7 && $("#"+is_term+"measurement").val() == ""){
			if($("#relocationgm_spot").is(':checked')){
				$("#err_measurement").html('This field is required.');
				$("#err_measurement").css('display','block');
			}
			else{
				$("#err_term_measurement").html('This field is required.');
				$("#err_term_measurement").css('display','block');
			}
		}else{
			if($("#relocationgm_spot").is(':checked'))
				$("#err_measurement").css('display','none');
			else
				$("#err_term_measurement").css('display','none');
			serviceboxValidation(true);
		}		
		
	});


	$('.servicedata,.term_servicedata').on('click', '.remove-service', function(){
            var is_term ='term_';
            if($("#"+is_term+"relocationgm_spot").is(':checked')){
               is_term = '';
           }
            var remove_item_id = $('#'+is_term+'service_slab_hidden_value').val();
            if($(this).parent().parent().remove()){
                    var delete_value = $('#'+is_term+'service_slab_hidden_value').val()-1;
                    $('#'+is_term+'service_slab_hidden_value').val(delete_value);
            }
            return false;
    });

	$('.servicedata,.term_servicedata').on('click', '.edit-service', function(){

            var is_term ='term_';
            if($("#relocationgm_spot").is(':checked')){
               is_term = '';
           }

            var rowid = $(this).attr("row_id");
            //var remove_val = $(this).attr("data-string");
	//sel_list_house.splice($.inArray(remove_val, sel_list_house),1);
            update_data = $(this).attr('data-pop').split('|');
            $("#"+is_term+"relgm_service_type").val(update_data[0]);
            $("#"+is_term+"measurement").val(update_data[1]);
            $("#"+is_term+"measurement_unit").val(update_data[2]);
            $("#"+is_term+"relgm_service_type").selectpicker('refresh');
            $("#"+is_term+"relgm_service_type").prop('disabled', true);
            $("#"+is_term+"update_reloc_seller_line").val(1);
            $("#"+is_term+"update_reloc_seller_row_count").val(rowid);

    });

    /***** End : Spot Post & Getquote *****/


	    /***** Start : Term Post & Getquote *****/
		$("#term_relocgmbuyer_quote").validate({
			ignore: [],
		    rules: {        	
				"term_dispatch_date": {
		            required: true,
		        },
				"term_delivery_date": {
		            required: true,
		        },
				"from_location_id": {
		            required: true,
		        },
		        "last_bid_date": {
		            required: true,
		        },
		        "bid_close_time": {
		            required: true,
		        },
		        "quoteaccess_id": {
		            required: true,
		        },
		        "agree": {
		            required: true,
		        },
		        "terms_condtion_types_term_defualt": {
		            
		            accept: "docx|txt|doc|pdf|xls|csv|xlsx",
		        },
		        "terms_condtion_types_term_1": {
		           
		            accept: "docx|txt|doc|pdf|xls|csv|xlsx",
		        },
		        "terms_condtion_types_term_2": {
		            
		            accept: "docx|txt|doc|pdf|xls|csv|xlsx",
		        },
		        "terms_condtion_types_term_3": {
		            
		            accept: "docx|txt|doc|pdf|xls|csv|xlsx",
		        },
		        "terms_condtion_types_term_4": {
		           
		            accept: "docx|txt|doc|pdf|xls|csv|xlsx",
		        },
		        "term_seller_list" : {
		            required : {
		            	depends: function(element) {
		            		if ($(".create-posttype-service-ftl-term:checked").val() == 2){      
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
		            }
		        }
		    },
		    errorPlacement: function(error, element) {
		    	$(element).parent('div').after(error);
		    },
			messages: {  
				term_dispatch_date:{
		            required:"Valid From is required.",                  
		        },
				term_delivery_date:{
		            required:"Valid To is required.",                  
		        },
				from_location_id:{
		            required:"Location is required.",                  
		        },
				terms_condtion_types_term_defualt:{
		            required:"This field is required.",                  
		            accept:"select valid input file format Ex pdf,txt,doc,docx,xlsx,csv."
		        },
		        terms_condtion_types_term_1:{
		            required:"This field is required.",                  
		            accept:"select valid input file format Ex pdf,txt,doc,docx,xlsx,csv."
		        },
		        terms_condtion_types_term_2:{
		            required:"This field is required.",                  
		            accept:"select valid input file format Ex pdf,txt,doc,docx,xlsx,csv."
		        },
		        terms_condtion_types_term_3:{
		            required:"This field is required.",                  
		            accept:"select valid input file format Ex pdf,txt,doc,docx,xlsx,csv."
		        },
		        terms_condtion_types_term_4:{
		            required:"This field is required.",                  
		            accept:"select valid input file format Ex pdf,txt,doc,docx,xlsx,csv."
		        },        
		    }

		});

		//checking add items empty or not
		$('#term_relocationgm_add_buyer_quote,#term_relocationgm_add_buyer_quote_draft').click(function(e) {    
			var thisid = $(this).attr("id");
			if(thisid == "term_relocationgm_add_buyer_quote"){
				$("#confirm_but").val("Float RFP");
			}
			//validation add new docs
			$(".dynamic_validations_term").each(function (item) {
					$(this).rules("add", {
						required : true,
						accept: "docx|txt|doc|pdf|xls|csv|xlsx",
					});
					//$("#term_relocationgm_add_buyer_quote").validate().element("#"+$(this).attr("id"));
				});
				 
	        $('#term_relocgmbuyer_quote').submit();
	        if($('#term_relocgmbuyer_quote').valid()){
	            $("#term_relocationgm_add_buyer_quote").prop('disabled', true);    
	            $("#term_relocationgm_add_buyer_quote_draft").prop('disabled', true);      
	        }
	        return true;
		});


	    /***** End : Term Post & Getquote *****/

	// End  : Validation for Buyer Post & Get Quote Create / Update

    /**** Start :  Search Form Validation ****/
    $('#search-form_buyer_relocationgm').validate({
            ignore: "input[type='text']:hidden",
            rules : {
                "from_date" : {required : true},
                "to_location" : {required : true},
                "measurement" : {
                    required : {
                        depends: function(element) {
                            if ($('#relgm_service_type').val() != 7){
                                return true;
                            }else{
                                return false;
                            }
                        }
                    }
                },
                "relgm_service_type" : {required : true},
            },
            errorPlacement: function(error, element) {
                if($(element).attr('type') == "checkbox" || $(element).attr('type') == "radio" ){
                    $(element).parent('div').parent('div').after(error);
                }else{
                    $(element).parent('div').after(error);
                }
            },
            submitHandler : function(form) {
                form.submit();
            }
        });


    /**** End : Search Form ****/ 

});

    function serviceboxValidation(add){
        var is_term ='term_';
        if($("#relocationgm_spot").is(':checked')){
            is_term = '';
        }
        
        var num = parseInt($('#'+is_term+'service_slab_hidden_value').val()) + 1;
        var flag = 0;
        /**
         * Dynamic values Valdiation
         */
        var measurement_unit = 'Day(s)';
        var sel_servicetype_id = $("#"+is_term+"relgm_service_type option:selected").val()
        var sel_servicetype_name = $("#"+is_term+"relgm_service_type option:selected").text();
        var measurement = $("#"+is_term+"measurement").val();
        var measurement_unit = $("#"+is_term+"measurement_unit").val();
    
        $('input[name^="'+is_term+'service_ids"]').each(function() {
            //alert("exist "+$(this).val() );
            if(sel_servicetype_id == $(this).val())
                    flag++;
        });
        
        if(flag == 0){      
            $('#'+is_term+'service_slab_hidden_value').val(num);
            //$("#remove_item_" + (num-1)).find('a').remove();  
            var edit_icon = '&nbsp;';
            if($("#"+is_term+"relgm_service_type").val() != 7)
                edit_icon = '<a href="javascript:void(0)" class="edit-service" row_id="'+num+ '" data-pop="'+sel_servicetype_id+'|'+measurement+'|'+measurement_unit+'"><i class="fa fa-edit red" ></i></a>';

            var add_item = '<div id="'+is_term+'remove_item_' + num + '" data-string="' + num + '" class="table-row inner-block-bg relocation_term_request_rows"><div class="col-md-5 padding-left-none">'+sel_servicetype_name+'</div><div class="col-md-5 padding-left-none gm_measurement">'+measurement+' '+measurement_unit+'</div><div class="col-md-2 form-control-fld padding-left-none  padding-top-7"><input type="hidden" name="'+is_term+'service_ids[]" class="'+is_term+'service_ids" value="'+sel_servicetype_id+'"><input type="hidden" name="'+is_term+'measurements[]" value="'+measurement+'"><input type="hidden" name="'+is_term+'measurement_units[]" value="'+measurement_unit+'">'+edit_icon+'<a href="javascript:void(0)" class="remove-service" row_id="'+num+ '"><i class="fa fa-trash red" title="Delete"></i></a></div></div>'
            var box_html = $(add_item);
            $('.'+is_term+'servicedata').append(box_html);
            
            $("#"+is_term+"measurement").val('');
            $("#"+is_term+"measurement_unit").val('Day(s)');
            $("#"+is_term+"relgm_service_type").val($("#"+is_term+"relgm_service_type option:first").val());

            $("#"+is_term+"measurement").removeClass('clsGMSNoOfPerson');
            $("#"+is_term+"measurement").removeClass('clsGMSRatepRent');              
            $("#"+is_term+"measurement_unit").addClass('clsGMSNoOfDays');


            $('.selectpicker').selectpicker('refresh');
         }else{
                    if($("#"+is_term+"update_reloc_seller_line").val()==1)
                    {
                        var measurement = $("#"+is_term+"measurement").val();
                        var measurement_unit = $("#"+is_term+"measurement_unit").val();
                        $('#'+is_term+'remove_item_' + $("#"+is_term+"update_reloc_seller_row_count").val()+' div.gm_measurement').html(measurement+' '+measurement_unit);
                        $('#'+is_term+'remove_item_' + $("#"+is_term+"update_reloc_seller_row_count").val()+' input[name="'+is_term+'measurements[]"]').val(measurement);
                        var v=$('#'+is_term+'remove_item_' + $("#"+is_term+"update_reloc_seller_row_count").val()+' input[name="'+is_term+'service_ids[]"]').val();
                        var v1=$('#'+is_term+'remove_item_' + $("#"+is_term+"update_reloc_seller_row_count").val()+' input[name="'+is_term+'measurements[]"]').val();
                        var v2=$('#'+is_term+'remove_item_' + $("#"+is_term+"update_reloc_seller_row_count").val()+' input[name="'+is_term+'measurement_units[]"]').val();
                        $('#'+is_term+'remove_item_' + $("#"+is_term+"update_reloc_seller_row_count").val()+' a.edit-service').removeAttr('data-pop');
                        $('#'+is_term+'remove_item_' + $("#"+is_term+"update_reloc_seller_row_count").val()+' a.edit-service').attr('data-pop',v+'|'+v1+'|'+v2);
                        $("#"+is_term+"update_reloc_seller_line").val(0);
                        
                        
                        $("#"+is_term+"measurement").val('');
                        $("#"+is_term+"measurement_unit").val('Day(s)');
                        $("#"+is_term+"relgm_service_type").val($("#"+is_term+"relgm_service_type option:first").val());

                        $('.selectpicker').selectpicker('refresh');
                    }else{
                        //alert('Service Type added already');
                        $("#erroralertmodal .modal-body").html("Service Type added already.");
                        $("#erroralertmodal").modal({
                                show: true
                        });
                        $("#"+is_term+"relgm_service_type").val($("#"+is_term+"relgm_service_type option:first").val());
                        $("#"+is_term+"measurement_unit").val('Day(s)');
                        
                        
                        $('.selectpicker').selectpicker('refresh');
                        $("#"+is_term+"measurement").val('');
                        return false;
                    }
         }
    }
/********** Get Measurement Unit onchange of Service Type ************ */
function getServiceTypeMeasurementUnit() {
    //alert("onchage func called");
	var is_term ='term_';
    var clsPrefix = 'Term';
    if($("#relocationgm_spot").is(':checked')){
       is_term = '';
       clsPrefix = '';
    }
    $("#"+is_term+"measurement").val('');
    var service_type_id = $('#'+is_term+'relgm_service_type').val();

    if(service_type_id == 1 || service_type_id == 2 || service_type_id == 6){
        $("#"+is_term+"measures_div").show();
		$("#"+is_term+"measurement").removeClass('clsGMSNoOfPerson');
		//$("#"+is_term+"measurement").removeClass('clsGMSRateFlat');
		$("#"+is_term+"measurement").removeClass('clsGMSRatepRent');    	
    	$("#"+is_term+"measurement").addClass('clsGMSNoOfDays'+clsPrefix);
    }else if(service_type_id == 4 || service_type_id == 5){
        $("#"+is_term+"measures_div").show();
		$("#"+is_term+"measurement").removeClass('clsGMSNoOfDays'+clsPrefix);
		//$("#"+is_term+"measurement").removeClass('clsGMSRateFlat');
		$("#"+is_term+"measurement").removeClass('clsGMSRatepRent');    	
    	$("#"+is_term+"measurement").addClass('clsGMSNoOfPerson');
    }else if(service_type_id == 7){
        $("#"+is_term+"measures_div").hide();
		$("#"+is_term+"measurement").removeClass('clsGMSNoOfDays'+clsPrefix);
		$("#"+is_term+"measurement").removeClass('clsGMSNoOfPerson');
		$("#"+is_term+"measurement").removeClass('clsGMSRatepRent'); 
        if(is_term == ""){
            $("#err_measurement").html(''); 
            $("#err_measurement").hide('');   	
        }else{
            $("#err_term_measurement").html(''); 
            $("#err_term_measurement").hide('');                 
        }

    	//$("#"+is_term+"measurement").addClass('clsGMSRateFlat');
    }else if(service_type_id == 3){
        $("#"+is_term+"measures_div").show();
		$("#"+is_term+"measurement").removeClass('clsGMSNoOfDays');
		$("#"+is_term+"measurement").removeClass('clsGMSNoOfPerson');
		//$("#"+is_term+"measurement").removeClass('clsGMSRateFlat');    	
    	$("#"+is_term+"measurement").addClass('clsGMSRatepRent');
    }

    var data = {
        'relgm_service_type': service_type_id
    };
    $.ajax({
        type: "GET",
        url: '/getServiceTypeMeasurementUnit',
        data: data,
        dataType: 'text',
        success: function(data) {
            $('#'+is_term+'measurement_unit').val(data);
        },
        error: function(request, status, error) {
            $('#'+is_term+'measurement_unit').val('');
        },
    });
}

function addMeasurementValidation(service_type_id,txtID){
	$("#"+txtID).val('');
	if(service_type_id == 1 || service_type_id == 2 || service_type_id == 6){
        $("#measures_div").show();
		$("#"+txtID).removeClass('clsGMSNoOfPerson');
		//$("#"+txtID).removeClass('clsGMSRateFlat');
		$("#"+txtID).removeClass('clsGMSRatepRent');    	
    	$("#"+txtID).addClass('clsGMSNoOfDays');

        $("#measurement").attr('placeholder','No of Days *');
    }else if(service_type_id == 4 || service_type_id == 5){
        $("#measures_div").show();
		$("#"+txtID).removeClass('clsGMSNoOfDays');
		//$("#"+txtID).removeClass('clsGMSRateFlat');
		$("#"+txtID).removeClass('clsGMSRatepRent');    	
    	$("#"+txtID).addClass('clsGMSNoOfPerson');
        $("#measurement").attr('placeholder','No of Persons *');
    }else if(service_type_id == 7){
         $("#measures_div").hide();
		$("#"+txtID).removeClass('clsGMSNoOfDays');
		$("#"+txtID).removeClass('clsGMSNoOfPerson');
		$("#"+txtID).removeClass('clsGMSRatepRent');    	
    	//$("#"+txtID).addClass('clsGMSRateFlat');
        $("#measurement").attr('placeholder','');        
    }else if(service_type_id == 3){
        $("#measures_div").show();
		$("#"+txtID).removeClass('clsGMSNoOfDays');
		$("#"+txtID).removeClass('clsGMSNoOfPerson');
		//$("#"+txtID).removeClass('clsGMSRateFlat');    	
    	$("#"+txtID).addClass('clsGMSRatepRent');
        $("#measurement").attr('placeholder','Rent *');        
    }

    var data = {
        'relgm_service_type': service_type_id
    };
    $.ajax({
        type: "GET",
        url: '/getServiceTypeMeasurementUnit',
        data: data,
        dataType: 'text',
        success: function(data) {
            $('#measurement_unit').val(data);
        },
        error: function(request, status, error) {
            $('#measurement_unit').val('');
        },
    });    
}
/*function addMeasurementValidation(service_type_id,txtID){
	$("#"+txtID).val('');
	if(service_type_id == 1 || service_type_id == 2 || service_type_id == 6){
		$("#"+txtID).removeClass('clsGMSNoOfPerson');
		//$("#"+txtID).removeClass('clsGMSRateFlat');
		$("#"+txtID).removeClass('clsGMSRatepRent');    	
    	$("#"+txtID).addClass('clsGMSNoOfDays');
    }else if(service_type_id == 4 || service_type_id == 5){
		$("#"+txtID).removeClass('clsGMSNoOfDays');
		//$("#"+txtID).removeClass('clsGMSRateFlat');
		$("#"+txtID).removeClass('clsGMSRatepRent');    	
    	$("#"+txtID).addClass('clsGMSNoOfPerson');
    }else if(service_type_id == 7){
		$("#"+txtID).removeClass('clsGMSNoOfDays');
		$("#"+txtID).removeClass('clsGMSNoOfPerson');
		$("#"+txtID).removeClass('clsGMSRatepRent');    	
    	//$("#"+txtID).addClass('clsGMSRateFlat');
    }else if(service_type_id == 3){
		$("#"+txtID).removeClass('clsGMSNoOfDays');
		$("#"+txtID).removeClass('clsGMSNoOfPerson');
		//$("#"+txtID).removeClass('clsGMSRateFlat');    	
    	$("#"+txtID).addClass('clsGMSRatepRent');
    }
}*/



