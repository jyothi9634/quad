$(document).ready(function() {
    
    /* Shriram: jun 10 */	
    $('.clsFromDate').datepicker({ dateFormat: "dd/mm/yy"})
    .on('change', function() {
        var fromDate = $(this).val();
        var toDate = $(".clsToDate").val();
        loadLeaseterms(fromDate,toDate);
    });
    $('.clsToDate').datepicker({ dateFormat: "dd/mm/yy"})
    .on('change', function() {
        var fromDate = $(".clsFromDate").val();
        var toDate = $(this).val();
        loadLeaseterms(fromDate,toDate);
    });


	$('#lease_type').change(function(){
        var leasetype = $("#lease_type").val();
       if(leasetype == 1){
    	   
       }else if(leasetype == 2){
    	   
       }else if(leasetype == 3){
    	   
       }else{
    	   
       }
        
    });
	
	$('#add_more_tl').on('click', function() {
        $("#trucklease-posts-form-lines").valid();
    });
	
	 $("#trucklease-posts-form").validate({
	    	ignore: [],
	        rules : {
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
	            "accept_payment[]" : {
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
	            "terms_condtion_types1" : {
	                number: true,
	                fivebytwovalidationswithzero:{
		            	depends: function(element) {
		            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                },

	            },
	            "terms_condtion_types2" : {
	                number: true,
	                fourbytwovalidationswithzero:{
		            	depends: function(element) {
		            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                },
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
	            "credit_period" : {
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
	                creditvalidation:{
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
	        		 $(element).parent().parent().append(error);
	        },
	        messages : {
	            "terms_condtion_types1" : {
	                required : "Cancellation Charge is required",
	            },
	            "terms_condtion_types2" : {
	                required : "Other Charges is required",
	            },
	            "terms_condtion_types_1" : {
	                required : "Other Charges is required",
	            },
	            "terms_condtion_types_2" : {
	                required : "Other Charges is required",
	            },
	            "terms_condtion_types_3" : {
	                required : "Other Charges is required",
	            },
	            "labeltext_1" : {
	                required : "Other Charges is required",
	            },
	            "labeltext_2" : {
	                required : "Other Charges is required",
	            },
	            "labeltext_3" : {
	                required : "Other Charges is required",
	            },
	            "tracking" : {
	                required : "Tracking is required",
	            },
	            "accept_payment[]" : {
	                required : "Payment method is required",
	            },
	            "agree" : {
	                required : "Terms & Conditions is required",
	            },
	            
	        },
	    });
	
	
	$("#trucklease-posts-form-lines").validate({
    	ignore: "input[type='text']:hidden",
        rules : {
            "valid_from" : {
                required : true,
            },
            "valid_to" : {
                required : true,
            },
            "from_location_id" : {
                required : true,
            },
            "from_location" : {
                required : true,
            },
            "LeaseTerms" : {
                required : true,
            },
            "MinimumLeasePeriod" : {
                required : true,
                number: true,
                rangelength : [ 1, 3 ]
                
            },
            "LoadTypeMasters" : {
                required : true,
            },
            "driver_cost" : {
            	required : {
                    depends: function(element) {
                        if ($('#need_driver').val() == 1){
                            return true;
                        }else{
                            return false;
                        }

                    }
                },
                number: true,
            },
            "VehicleTypeMasters" : {
                required : true,
            },
            "VehicleNumber" : {
                required : true,
            },
            "SelectStatesPermit" : {
                required : true,
            },
            "price" : {
                required : true,
                number: true
                //pricevalidation:true,
            },
        },
        errorPlacement: function(error, element) {
        	$(element).parent('div').after(error);
        },
        messages : {
            "valid_from" : {
                required : "Valid From date is required",
            },
            "MinimumLeasePeriod" : {
            	rangelength : "Please enter a value between 1 and 3 digits long.",
                max : "Minimum Lease Period is greater than Validity Period.",
            },
            "valid_to" : {
                required : "Valid To date is required",
            },
            "from_location" : {
                required : "",
            },
            "from_location_id" : {
                required : "From Location is required",
            },
            "LeaseTerms" : {
                required : "Lease Type is required",
            },
            "VehicleTypeMasters" : {
                required : "Vehicle Type is required",
            },
            "LoadTypeMasters" : {
                required : "Preferred Goods is required",
            },
            "VehicleNumber" : {
                required : "Vehicle Model is required",
            },
            "SelectStatesPermit" : {
                required : "Permit is required",
            },
           
            "price" : {
                required : "Price is required",
            }
        },
        submitHandler : function(form) {
            form.submit();
        }
    });
	
	
	/******************************Multi line add items*********************************************/
    var sel_list = new Array();
    $('#add_more_tl').click(function() {
        if($("#trucklease-posts-form-lines").valid()){  //before adding a line item, validation of form should happen first
    	var num = parseInt($('#next_add_more_id').val()) + 1;
        $('#next_add_more_id').val(num);
        var from_location = $('#from_location').val();
        var datepicker_from_value = $('#datepicker').val();
        var datepicker_from_value = convertDateFormatForDatePickerSeller(datepicker_from_value);
        var datepicker_to_value = $('#datepicker_to_location').val();
        var datepicker_to_value = convertDateFormatForDatePickerSeller(datepicker_to_value);
        var seller_district = $('#seller_district_id').val();
        var states = $('#permitstates').val();
        var ts_from = Date.parse(datepicker_from_value);
        var ts_to = Date.parse(datepicker_to_value);
        var from_location_identifier = $('#from_location_id').val();
        var load_type = $('#load_type').val();
        var lease_type = $('#lease_type').val();
        var minimum_lease_period = $('#minimum_lease_period').val();
        var vehiclenumber = $('#vehiclenumber').val();
        var lease_type_value = $( "#lease_type option:selected" ).text();
        var vehicle_type = $('#vechile_type').val();
        var fuel_need = $('#fuel_need').val();
        //var check_driver_availablity = $('#check_driver_availablity').val();
        var driver_cost = $('#driver_cost').val();
        var price = $('#price').val();
        var price_numric = /^\d+(\.\d{2})?$/.test(price);
        var load_type_value = $( "#load_type option:selected" ).text();
        var vehicle_type_value = $( "#vechile_type option:selected" ).text();
        if (load_type_value ==  "Load Type (All)"){
            load_type_value = "All";
        }
        if (vehicle_type_value ==  "Vehicle Type (All)"){
        	vehicle_type_value = "All";
        }
        
        if($('#check_driver_availablity').is(':checked')) {
        	var check_driver_availablity = 1;
        }else{
        	var check_driver_availablity = 0;
        }
        
        var vechile_type_value = $( "#vechile_type option:selected" ).text();
        var subscription_start_date_start_val = $('#subscription_start_date_start').val();
        var subscription_end_date_end_val = $('#subscription_end_date_end').val();
        var current_date_seller = $('#current_date_seller').val();
        
        if((datepicker_from_value > subscription_end_date_end_val) || (current_date_seller < subscription_start_date_start_val) || (datepicker_from_value < subscription_start_date_start_val)){
        	var end_date_subscription = "from date";
        }else{
        	var end_date_subscription = "to date";
        }
        
        if (from_location_identifier != '' && lease_type != '' && states != '' && vehiclenumber != '' && datepicker_from_value != null && datepicker_to_value != null && from_location != '' && price != '' && price != 0 &&  load_type != null && vehicle_type != '' && price_numric == true ) {
        if((current_date_seller >= subscription_start_date_start_val) && (current_date_seller <= subscription_end_date_end_val) && (datepicker_from_value >= subscription_start_date_start_val) && (datepicker_from_value <= subscription_end_date_end_val) && (datepicker_to_value >= subscription_start_date_start_val) && (datepicker_to_value <= subscription_end_date_end_val)){
        if (from_location_identifier != '' && datepicker_from_value != '' && datepicker_to_value != '' && from_location != '' &&   price != '' && load_type != '' && vehicle_type != '' && price_numric == true) {
            var unique = from_location_identifier+vehicle_type+ts_from+ts_to;
            
            if($("#update_lease_seller_line").val()==1)
        	{
            	rowid=$("#update_lease_seller_row_count").val();
				var remove_unique=$("#update_lease_seller_row_unique").val();
				sel_list.splice($.inArray(remove_unique, sel_list),1);
				 $('.request_row_' + rowid).remove();	
            	$("#update_lease_seller_line").val(0);
            } 

            if ($.inArray(unique,sel_list)==-1) {
                sel_list.unshift(unique);
            $.ajax({
                type : 'post',
                url : '/truckleaselineitemscheck',
                data : {
                    'from_location' : from_location_identifier,
                    'from_date_seller' : datepicker_from_value,
                    'to_date_seller' : datepicker_to_value,
                    'vehicle_type' : vehicle_type,
                    'load_type' : load_type
                },
                dataType : "html",
                type : 'POST',
                success : function(data) {
                    if (data == '0') {
                    	
                    	if($("#update_lease_seller_line").val()==1)
                    	{
                        	$('.request_row_' + $("#update_ftl_seller_row_count").val()).remove();	
                        	$("#update_lease_seller_line").val(0);
                        }
                    	
                    	var rowid = "#single_post_item_" + num;
                        var html = '<div class="table-row inner-block-bg request_row_'
                            + num
                            + '" id="single_post_item_'
                            + num
                            + '"><div class="col-md-2 padding-left-none from_location_text" id="from_loc_text_'+num+'">'
                            + from_location
                            + '</div><div class="col-md-2 padding-left-none">'
                            + lease_type_value
                            + '</div><div class="col-md-2 padding-left-none">'
                            + minimum_lease_period
                            + '</div><div class="col-md-2 padding-left-none">'
                            + vehicle_type_value
                            + '</div><div class="col-md-2 padding-left-none">'
                            + load_type_value
                            + '</div><div class="col-md-1 padding-left-none">'
                            + price
                            + '</div><div class="col-md-1 padding-none"><a href="javascript:void(0)" style ="cursor:pointer; margin-right:10px;" class="edit_this_line_lease editlease" data-string="'+unique+'" row_id="'
                            + num
                            + '"><i class="fa fa-edit red" title="Edit"></i></a><a style ="cursor:pointer;" class="remove_this_line_item remove" data-string="'+unique+'" row_id="'
                            + num
                            + '"><i class="fa fa-trash red" title="Delete"></i></a></div><input type="hidden" name="from_location[]" id="from_location_id_'+num+'" value="'
                            + from_location_identifier
                            + '"><input type="hidden" name="load_type[]" id="load_type_'+num+'" value="'
                            + load_type
                            + '"><input type="hidden" name="lease_type[]" id="lease_type_'+num+'" value="'
                            + lease_type
                            + '"><input type="hidden" name="minimum_lease_period[]" id="minimum_lease_period_'+num+'" value="'
                            + minimum_lease_period
                            + '"><input type="hidden" name="vechile_type[]" id="vechile_type_'+num+'" value="'
                            + vehicle_type
                            + '"><input type="hidden" name="vehiclenumber[]" id="vehiclenumber_'+num+'" value="'
                            + vehiclenumber
                            + '"><input type="hidden" name="sellerdistrict[]" id="sellerdistricts_'+num+'" value="'
                            + seller_district
                            + '"><input type="hidden" name="price[]" id="price_'+num+'" value="'
                            + price
                            + '"><input type="hidden" name="states[]" id="states_'+num+'" value="'
                            + states
                            + '"><input type="hidden" name="fuel_need[]" id="fuel_need_'+num+'" value="'
                            + fuel_need
                            + '"><input type="hidden" name="check_driver_availablity[]" id="check_driver_availablity_'+num+'" value="'
                            + check_driver_availablity
                            + '"><input type="hidden" name="driver_cost[]" id="driver_cost_'+num+'" value="'
                            + driver_cost
                            + '"><div class="clearfix"></div></div>';

                        $("#multi-line-itemes").show();
                        $('.request_rows').append(html);
                        var id_line_itemes = $('.request_rows').children().size();
                        if (id_line_itemes == 0){
                        }else{
                        	$("#datepicker").prop('disabled', true);
                        	$("#datepicker_to_location").prop('disabled', true);
                        }
                        
                       // document.getElementById('trucklease-posts-form-lines').reset();
                        $("#valid_from_val").val(datepicker_from_value);
                        $("#valid_to_val").val(datepicker_to_value);
                        $('#from_location').val("");
                        $('#from_location_id').val("");
                        $('#to_location').val("");
                        $('#to_location_id').val("");
                        $('#vechile_type').val("");
                        $('#load_type').val("");
                        $('#price').val("");
                        $("#minimum_lease_period").val("");
                        $("#minunit").html("");
                        $("#vehiclenumber").val("");
                        $("#check_driver_availablity").prop("checked",false);
                        $("#driver_cost").val("");
                        $("#driver_cost").attr("disabled",true);
                        //$('.selectpicker').selectpicker('refresh');
                        $('select[name=LeaseTerms]').selectpicker('val', '');
                        $('select[name=VehicleTypeMasters]').selectpicker('val', '');
                        $('select[id=fuel_need]').selectpicker('val', '');
                        //$('#load_type').multiselect('refresh');
                        //$('#permitstates').multiselect('refresh');
                        
                        
                        function multiselect_deselectAll($el) {
                            $('option', $el).each(function(element) {
                                $el.multiselect('deselect', $(this).val());
                            });
                        }

                        $('.m_select').each(function() {
                            var select = $(this);
                            multiselect_deselectAll(select);
                        });
                        
                        
                        //$(".national-permit .multiselect-container li").removeClass("active");
                        //$(".national-permit .multiselect-container li input[ype='checkbox']").attr("checked",false);

                    } else {
                    	$("#erroralertmodal .modal-body").html("Post already exists for the specified parameters! Please modify parameters.");
                        $("#erroralertmodal").modal({
                            show: true
                        });
                    }
                },
                error : function() {
                    alert("error");
                }
            });
            } else {
                $("#erroralertmodal .modal-body").html("Post already exists for the specified parameters! Please modify parameters.");
                $("#erroralertmodal").modal({
                    show: true
                });
            }
        }
        }else{
        	$("#erroralertmodal .modal-body").html("Your post valid "+end_date_subscription+" is beyond your subscription date, please select valid "+end_date_subscription+" within your subscription validity date");
                $("#erroralertmodal").modal({
                    show: true
                });
        	}
        }
      }  
    });
/******************************Multi line add items*********************************************/
	
    $(document).on('click', '.edit_this_line_lease', function() {
    	
    	var rowid = $(this).attr("row_id");
    	var period='';
    	 $("#update_lease_seller_line").val(1);
     	 $("#update_lease_seller_row_count").val(rowid);
     	var remove_val = $(this).attr("data-string");
     	$("#update_lease_seller_row_unique").val();
     	sel_list.splice($.inArray(remove_val, sel_list),1);
     	$( "#from_location").val($("#from_loc_text_"+rowid).html());
     	$('#from_location_id').val($("#from_location_id_"+rowid).val());
     	$("#vechile_type").selectpicker('val',$("#vechile_type_"+rowid).val());
		$("#lease_type").selectpicker('val',$("#lease_type_"+rowid).val());
		$('#minimum_lease_period').val($("#minimum_lease_period_"+rowid).val());
		$('#vehiclenumber').val($("#vehiclenumber_"+rowid).val());
		if($("#lease_type_"+rowid).val()==1){
		period='Days';	
		}
		if($("#lease_type_"+rowid).val()==2){
		period='Weeks';	
		}
		if($("#lease_type_"+rowid).val()==3){
		period='Months';	
		}
		if($("#lease_type_"+rowid).val()==4){
		period='Years';	
		}
		$('#minunit').val(period);
		
		var data = $("#load_type_"+rowid).val();
        var valArr = data.split(",");
        var size = valArr.length;
        for (var i=0; i < size; i++) {
            $('#load_type').multiselect('select', valArr[i]);
         } 
        
       // console.log($("#states_"+rowid).val());
        var datastate = $("#states_"+rowid).val();
        var valStateArr = datastate.split(",");
        var size = valStateArr.length;
        for (var i=0; i < size; i++) {
            $('#permitstates').multiselect('select', valStateArr[i]);
         } 
       
        $("#fuel_need").selectpicker('val',$("#fuel_need_"+rowid).val());
        $("#price").val($("#price_"+rowid).val());
        
        if($("#check_driver_availablity_"+rowid).val()==1){
        $("#check_driver_availablity").prop("checked",true);
        $("#driver_cost").val($("#driver_cost_"+rowid).val());
        }
       
        
		
    	
    });
	
	/******************************confirm functionality*********************************************/
	$('#add_quote_seller_id_tl').click(function(e) {
		e.preventDefault();
		var id=$('.request_rows').children().size();
		if(id==0){
			$("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
            $("#erroralertmodal").modal({
                show: true
            });
			return false;
		}else{
			if($("#trucklease-posts-form").valid()) {
			var submitData=$("#trucklease-posts-form").serialize();
			 var btnName = $('#add_quote_seller_id_tl').attr('name');
			 $("#add_quote_seller_id_tl").prop('disabled', true);
    		 $("#add_quote_seller_tl").prop('disabled', true);
             var btnVal = $('#add_quote_seller_id_tl').val();
             var btn = '&'+btnName+'='+btnVal;
             submitData += btn;
			 $.ajax({
		           type: "POST",
		           url: '/truckleaseaddseller',
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
/******************************confirm functionality*********************************************/
	$('.leaseterm').change(function(e) {
		
		var leaseterm=$("#lease_type").val();
        $("#minimum_lease_period").removeAttr('max');
		days = parseInt($("#daysDiffCnt").val());
        if(days==0)
            days=1;
		//alert(leaseterm);
		if(leaseterm==1){
            $("#minimum_lease_period").attr('max',days);            
			$(".minunit").html("Days");
		}
		if(leaseterm==2){
            weeks = Math.floor(days/7);
            $("#minimum_lease_period").attr('max',weeks);            
			$(".minunit").html("Weeks");	
		}
		if(leaseterm==3){
            //alert(days);
            months = Math.floor(days/30);
            $("#minimum_lease_period").attr('max',months);            
			$(".minunit").html("Months");	
		}
		if(leaseterm==4){
            //alert(days);
            years = Math.floor(days/365);
            $("#minimum_lease_period").attr('max',years);            
			$(".minunit").html("Years");	
		}
		
	});
	
	
	/******************************Save as draft functionality*********************************************/
	$('#add_quote_seller_tl').click(function(e) {
		e.preventDefault();
		var id=$('.request_rows').children().size();
		if(id==0){
			$("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
            $("#erroralertmodal").modal({
                show: true
            });
			return false;
		}else{
			
			
			
			$('#trucklease-posts-form').submit();
			if($('#trucklease-posts-form').valid()){
				$("#add_quote_seller_id_tl").prop('disabled', true);
				$("#add_quote_seller_tl").prop('disabled', true);
			}
		}
	});
/******************************Save as draft functionality*********************************************/

	//initial quote validation
	$("#addroadsellerpostquoteoffer .initial_quote_submit").click(function(){
		var seller_post_item_id = $('#seller_post_item_id').val();

	    var buttonId = $(this).attr('id');
	    var removeString = 'initail_quote_submit_';
	    var rowNo = buttonId.replace(removeString, '');
	    var $sellerQuoteOfferFieldId = $('#initial_quote_' + rowNo);
	    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
	    
   		var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
		if (!($sellerQuoteOfferFieldId.val()).trim()) {
			
			$("#erroralertmodal .modal-body").html("Please enter Initial Quote");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerQuoteOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerOfferValue)) {
			$("#erroralertmodal .modal-body").html("Initial Quote should be a number");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			$sellerQuoteOfferFieldId.focus();
			 return false;
		}else if (sellerOfferValue==0) {
			$("#erroralertmodal .modal-body").html("Initial Quote should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			$sellerQuoteOfferFieldId.focus();
			 return false;
		} else {
			 $.ajax({
	            type: "POST",
	            url : "/sellersubmitquote",
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
	            data : { 'initial_quote': sellerOfferValue, 'buyer_buyerquote_id' : rowNo,'seller_post_item_id' : seller_post_item_id },
			   success : function(jsonData){
				   $("#erroralertmodal .modal-body").html('Initial Quote given successfully.');
	               $("#erroralertmodal").modal({
	                   show: true
	               }).one('click','.ok-btn',function (e){
	                   location.reload();
	               });
				   
			   }
	       },"json");
		}
	});
	
	//Counter Offer Acceptence
	$("#addroadsellerpostquoteoffer .counter_quote_submit").click(function(e){
		var seller_post_item_id = $('#seller_post_item_id').val()
		var buttonId = $(this).attr('id');
	    var removeString = 'counter_quote_submit_';
	    var rowNo = buttonId.replace(removeString, '');
	    var $sellerQuoteOfferFieldId =$('#counter_quote_' + rowNo);
	    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
		
			 $.ajax({
	            type: "POST",
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
	            url : "/sellercounterquotesubmit",
	            data : { 'counter_quote': sellerOfferValue, 'buyer_buyerquote_id' : rowNo,
	            		  'seller_post_item_id' :seller_post_item_id},
				    success : function(jsonData){
					
				    	$("#erroralertmodal .modal-body").html('Counter Offer accepted successfully.');
	                $("#erroralertmodal").modal({
	                    show: true
	                }).one('click','.ok-btn',function (e){
	                    location.reload();
	                });
					   
				   }
	       },"json");
		
	});
	
	
	//Final Quote
	$("#addroadsellerpostquoteoffer .final_quote_submit").click(function(e){
		var buttonId = $(this).attr('id');
	    var removeString = 'final_quote_submit_';
	    var rowNo = buttonId.replace(removeString, '');
	    var $sellerQuoteOfferFieldId = $('#final_quote_' + rowNo);
	    var sellerOfferValue = $sellerQuoteOfferFieldId.val();

	    
		var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
		if (!($sellerQuoteOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Final Quote");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerQuoteOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerOfferValue)) {
			$("#erroralertmodal .modal-body").html("Final Quote should be a number");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerQuoteOfferFieldId.focus();
			 return false;
		} else {
			 $.ajax({
	            type: "POST",
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
	            url : "/sellerfinalquotesubmit",
	            data : { 'final_quote': sellerOfferValue, 'buyer_buyerquote_id' : rowNo },
				    success : function(jsonData){
				    	$("#erroralertmodal .modal-body").html('Final Quote given successfully.');
	                $("#erroralertmodal").modal({
	                    show: true
	                }).one('click','.ok-btn',function (e){
	                    location.reload();
	                });
				    	
				   }
	       },"json");
		}
	});
	
	
	//accept Firm Price
	$("#addroadsellerpostquoteoffer .accept_quote_submit").click(function(e){
		var buttonId = $(this).attr('id');
	    var removeString = 'acccept_quote_submit_';
	   
	    var rowNo = buttonId.replace(removeString, '');
	    
	    var $sellerQuoteOfferFieldId = $('#accept_quote_' + rowNo);
	    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
	    
	    var seller_post_item_id = $('#seller_post_item_id').val();
	    var transactionid = $('#transactionid').val();
	    $.ajax({
	        type: "POST",
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
	        url : "/sellerfirmacceptance",
	        data : { 'accept_quote': sellerOfferValue, 'buyer_buyerquote_id' : rowNo,
	        		  'seller_post_item_id' : seller_post_item_id,'transactionid' :transactionid },
		   success : function(jsonData){
			   $("#erroralertmodal .modal-body").html('Firm Offer from Buyer accepted.');
	           $("#erroralertmodal").modal({
	               show: true
	           }).one('click','.ok-btn',function (e){
	               location.reload();
	           });
			   
		   }
		},"json");
	
		
	});
	
	$("#leadsellerpostquoteoffertl .initial_lead_quote_submit").click(function(){
		var seller_post_item_id = $('#seller_post_item_id').val();

	    var buttonId = $(this).attr('id');
	    var removeString = 'initail_lead_quote_submit_';
	    var rowNo = buttonId.replace(removeString, '');
	    
	    var $sellerQuoteOfferFieldId = $('#initial_lead_quote_' + rowNo);
	    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
	    
	   	var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
		if (!($sellerQuoteOfferFieldId.val()).trim()) {
			
			$("#erroralertmodal .modal-body").html("Please enter Initial Quote");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerQuoteOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerOfferValue)) {
			$("#erroralertmodal .modal-body").html("Initial Quote should be a number");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			$sellerQuoteOfferFieldId.focus();
			 return false;
		}
		else {
			 $.ajax({
	            type: "POST",
	            url : "/sellersubmitquote",
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
	            data : { 'initial_quote': sellerOfferValue,'buyer_buyerquote_id' : rowNo,'seller_post_item_id' : seller_post_item_id },
			   success : function(jsonData){
				   $("#erroralertmodal .modal-body").html('Initial Quote given successfully.');
	               $("#erroralertmodal").modal({
	                   show: true
	               }).one('click','.ok-btn',function (e){
	                   location.reload();
	               });
				   
			   }
	       },"json");
		}
	});
	
	
	//Lead counter
	$("#leadsellerpostquoteoffertl .counter_lead_quote_submit").click(function(e){
		var seller_post_item_id = $('#seller_post_item_id').val()
		var buttonId = $(this).attr('id');
	    var removeString = 'counter_lead_quote_submit_';
	    var rowNo = buttonId.replace(removeString, '');
	    var $sellerQuoteOfferFieldId =$('#counter_lead_quote_' + rowNo);
	    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
		
			 $.ajax({
	            type: "POST",
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
	            url : "/sellercounterquotesubmit",
	            data : { 'counter_quote': sellerOfferValue, 'buyer_buyerquote_id' : rowNo,
	            		  'seller_post_item_id' :seller_post_item_id},
				    success : function(jsonData){
					
				    	$("#erroralertmodal .modal-body").html('Counter Offer accepted successfully.');
	                $("#erroralertmodal").modal({
	                    show: true
	                }).one('click','.ok-btn',function (e){
	                    location.reload();
	                });
					   
				   }
	       },"json");
		
	});

	//Lead Final quote SUbmit
	$("#leadsellerpostquoteoffertl .final_lead_quote_submit").click(function(e){
		var buttonId = $(this).attr('id');
	    var removeString = 'final_lead_quote_submit_';
	    var rowNo = buttonId.replace(removeString, '');
	    var $sellerQuoteOfferFieldId = $('#final_lead_quote_' + rowNo);
	    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
	  
	    var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
		if (!($sellerQuoteOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Final Quote");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerQuoteOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerOfferValue)) {
			$("#erroralertmodal .modal-body").html("Final Quote should be a number");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerQuoteOfferFieldId.focus();
			 return false;
		} else {
			 $.ajax({
	            type: "POST",
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
	            url : "/sellerfinalquotesubmit",
	            data : { 'final_quote': sellerOfferValue, 'buyer_buyerquote_id' : rowNo },
				    success : function(jsonData){
				    	$("#erroralertmodal .modal-body").html('Final Quote given successfully.');
	                $("#erroralertmodal").modal({
	                    show: true
	                }).one('click','.ok-btn',function (e){
	                    location.reload();
	                });
				    	
				   }
	       },"json");
		}
	});
	
	//Intial quote for seacrh buyer quotes
	$("#addsellersearchpostquoteofferTL .initial_quote_submit").click(function(){

	    var buttonId = $(this).attr('id');
	    var removeString = 'initail_quote_submit_';
	    var rowNo = buttonId.replace(removeString, '');
	    var $sellerQuoteOfferFieldId = $('#initial_quote_' + rowNo);
	    var sellerOfferValue = $sellerQuoteOfferFieldId.val();

	    var fromcity = $('#from_city').val();
	    var fromdate = $('#from_date').val();
	    var todate = $('#to_date').val();
	    var tracking = 1;
	    var paymentoptions = $('.payment_options_' + rowNo).val();
	    var credit_peroid = $('.credit_period_ptl_' + rowNo).val();
	    var credit_period_units = $('.credit_period_units_' + rowNo).val();

		
		var regexPattern= /^\d{1,6}(\.\d{1,2})?$/;
		var regexNumericPattern= /^\d{1,3}?$/;
		
		if (!($sellerQuoteOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Initial Quote");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerQuoteOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerOfferValue)) {
			$("#erroralertmodal .modal-body").html("Initial Quote field should be less than 1000000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			 $sellerQuoteOfferFieldId.focus();
			 return false;
		}else if (sellerOfferValue==0) {
			$("#erroralertmodal .modal-body").html("Initial Quote field should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			 $sellerQuoteOfferFieldId.focus();
			 return false;
		}
		
		if (paymentoptions == 1) {
			if($('input[type=checkbox]:checked').length == 0)
			{
				$("#erroralertmodal .modal-body").html("Please check atleast one payment Type");
		        $("#erroralertmodal").modal({
		            show: true
		        });   
		        credit_peroid = 0;
		        credit_period_units = 0;
				 return false;
			}
		}else if(paymentoptions == 4) {
			if($('input[type=checkbox]:checked').length == 0)
			{
				
				$("#erroralertmodal .modal-body").html("Please check atleast one payment Type");
		        $("#erroralertmodal").modal({
		            show: true
		        });   
		        return false;
			}
			else if ($('input[type=checkbox]:checked').length >=1 && $('input[type=checkbox]:checked').length <= 2){
				checkvalue = $( "input:checked" ).val();
				if(checkvalue == 2 || $('input[type=checkbox]:checked').length==2){
					if (!(credit_peroid).trim()) {
						$("#erroralertmodal .modal-body").html("Please enter Credit Period");
				        $("#erroralertmodal").modal({
				            show: true
				        });
						return false;
					} else if (!regexNumericPattern.test(credit_peroid)) {
						$("#erroralertmodal .modal-body").html("Credit Period Field should be numeric and less than 4 digits");
				        $("#erroralertmodal").modal({
				            show: true
				        }); 
				        return false;
					}
					credit_peroid = $('#credit_period_ptl').val();
					credit_period_units = $('#credit_period_units').val();
				}
			}
			
		}else{
			credit_peroid =0;
			credit_period_units =0;
		}
		
		if((($sellerQuoteOfferFieldId.val()).trim())){
			$.ajax({
	            type: "POST",
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
	            url : "/sellersearchsubmitquote",
	            data : { 'initial_quote': sellerOfferValue, 'buyer_buyerquote_id' : rowNo,
	            		 'from_city_loc':fromcity,
	            		 'tracking' : tracking,'paymentoptions' : paymentoptions,
	            		 'credit_peroid':credit_peroid,'credit_period_units':credit_period_units,
	            		 'from_date_dispatch':fromdate,'to_date_delivery':todate},
			   success : function(jsonData){
				  
				   $("#erroralertmodal .modal-body").html('Initial Quote given successfully.');
	               $("#erroralertmodal").modal({
	                   show: true
	               }).one('click','.ok-btn',function (e){
	                   location.reload();
	               });
			   }
	       },"json");
		}
	});
	
	
	
	//Accept quotes
	$("#addsellersearchpostquoteofferTL .accept_quote_submit").click(function(){

	    var buttonId = $(this).attr('id');
	    var removeString = 'accept_quote_submit_';
	    var rowNo = buttonId.replace(removeString, '');
	    var $sellerQuoteOfferFieldId = $('#accept_quote_' + rowNo);
	    var sellerOfferValue = $sellerQuoteOfferFieldId.val();

	    var regexPattern= /^\d{1,6}(\.\d{1,2})?$/;
		var regexNumericPattern= /^\d{1,3}?$/;
	    
	    var fromcity = $('#from_city').val();
	    var tocity = $('#to_city').val();
	    var fromdate = $('#from_date').val();
	    var todate = $('#to_date').val();
	    var tracking = 1;
	    var paymentoptions = $('.payment_options_' + rowNo).val();
	    var credit_peroid = $('.credit_period_ptl_' + rowNo).val();
	    var credit_period_units = $('.credit_period_units_' + rowNo).val();
	    var search = $('#search').val();
	    
	    
		if (paymentoptions == 1) {
			if($('input[type=checkbox]:checked').length == 0)
			{
				$("#erroralertmodal .modal-body").html("Please check atleast one payment Type");
		        $("#erroralertmodal").modal({
		            show: true
		        });   
		        credit_peroid = 0;
		        credit_period_units = 0;
				 return false;
			}
		}else if(paymentoptions == 4) {
			if($('input[type=checkbox]:checked').length == 0)
			{
				
				$("#erroralertmodal .modal-body").html("Please check atleast one payment Type");
		        $("#erroralertmodal").modal({
		            show: true
		        });   
		        return false;
			}
			else if ($('input[type=checkbox]:checked').length >=1 && $('input[type=checkbox]:checked').length <= 2){
				checkvalue = $( "input:checked" ).val();
				if(checkvalue == 2 || $('input[type=checkbox]:checked').length==2){
					if (!(credit_peroid).trim()) {
						$("#erroralertmodal .modal-body").html("Please enter Credit Period");
				        $("#erroralertmodal").modal({
				            show: true
				        });
						return false;
					} else if (!regexNumericPattern.test(credit_peroid)) {
						$("#erroralertmodal .modal-body").html("Credit Period Field should be numeric and less than 4 digits");
				        $("#erroralertmodal").modal({
				            show: true
				        }); 
				        return false;
					}
					credit_peroid = $('#credit_period_ptl').val();
					credit_period_units = $('#credit_period_units').val();
				}
			}
			
		}else{
			credit_peroid =0;
			credit_period_units =0;
		}
		
		if((($sellerQuoteOfferFieldId.val()).trim())){
			 $.ajax({
		        type: "POST",
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
		        url : "/sellerpublicaccept",
		        data : { 'accept_quote': sellerOfferValue, 'buyer_buyerquote_id' : rowNo,
		        		 'from_city_loc':fromcity,'to_city_loc':tocity,'search':search,
	            		 'tracking' : tracking,'paymentoptions' : paymentoptions,
	            		 'credit_peroid':credit_peroid,'credit_period_units':credit_period_units,
		        		 'from_date_dispatch':fromdate,'to_date_delivery':todate},
			   success : function(jsonData){
				   $("#erroralertmodal .modal-body").html('Firm Offer from Buyer accepted.');
		           $("#erroralertmodal").modal({
		               show: true
		           }).one('click','.ok-btn',function (e){
		               location.reload();
		           });
				   
			   }
		   },"json");
		}

	});
	
	
	//Accept Counter quotes
	$("#addsellersearchpostquoteofferTL .counter_quote_submit").click(function(){

	    var buttonId = $(this).attr('id');
	    var removeString = 'counter_quote_submit_';
	    var rowNo = buttonId.replace(removeString, '');
	    var $sellerQuoteOfferFieldId = $('#counter_quote_' + rowNo);
	    var sellerOfferValue = $sellerQuoteOfferFieldId.val();

	    var fromcity = $('#from_city').val();
	    var fromdate = $('#from_date').val();
	    var todate = $('#to_date').val();
	    var search = $('#search').val();

		 $.ajax({
	        type: "POST",
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
	        url : "/selleraccept",
	        data : { 'accept_quote': sellerOfferValue, 'buyer_buyerquote_id' : rowNo,
	        		 'from_city_loc':fromcity,'search':search,
	        		 'from_date_dispatch':fromdate,'to_date_delivery':todate},
		   success : function(jsonData){
			  
			   $("#erroralertmodal .modal-body").html('Firm Offer from Buyer accepted.');
	           $("#erroralertmodal").modal({
	               show: true
	           }).one('click','.ok-btn',function (e){
	               location.reload();
	           });
			   
		   }
	   },"json");

	});
	//Final quote for seacrh buyer quotes
	$("#addsellersearchpostquoteofferTL .final_quote_submit").click(function(e){
		var buttonId = $(this).attr('id');
	    var removeString = 'final_quote_submit_';
	    var rowNo = buttonId.replace(removeString, '');
	    var $sellerQuoteOfferFieldId = $('#final_quote_' + rowNo);
	    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
	    
	    
		var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
		if (!($sellerQuoteOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Final Quote");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerQuoteOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerOfferValue)) {
			$("#erroralertmodal .modal-body").html("Final Quote should be a number");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerQuoteOfferFieldId.focus();
			 return false;
		}else if (sellerOfferValue==0) {
			$("#erroralertmodal .modal-body").html("Final Quote should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerQuoteOfferFieldId.focus();
			 return false;
		}else {
			 $.ajax({
	            type: "POST",
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
	            url : "/sellerfinalquotesubmit",
	            data : { 'final_quote': sellerOfferValue,'buyer_buyerquote_id' : rowNo },
				   success : function(jsonData){
					 
					  $("#erroralertmodal .modal-body").html('Final Quote given successfully.');
		               $("#erroralertmodal").modal({
		                   show: true
		               }).one('click','.ok-btn',function (e){
		                   location.reload();
		               });
					   
				   }
	       },"json");
		}
	});

	
});



