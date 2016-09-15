$(function() {
//Buyer post validation
 $.validator.addMethod('GrtrZero', function(value) {
                return parseFloat(value) > 0;
            }, 'Distance shoule be greater than 0');



$("#posts-form_buyer_relocation_officemove").validate({
	ignore: "input[type='text']:hidden",
	rules : {		
		"valid_from" : {required : true},
		"valid_to" : {required : true},
		"from_location" : {required : true},
		"from_location_id" : {required : true},
		"distance" : {
			required : true,
			number: true,
			GrtrZero: true,	
			officemovedistance: true,
           /* decimalvalidation: {
	            	depends: function(element) {
	            		if ($('#distance').val() > 0){
		            		return true;
		            	}else{
		            		return false;
		            	}
	            	}
                }*/
		},
		"agree" : {required : true},        			
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
			if($(element).attr('type') == "checkbox" || $(element).attr('type') == "radio" ){
				$(element).parent('div').parent('div').after(error);
			}else{
				$(element).parent('div').after(error);
			}
		},
		messages : {
			"valid_from" : {
				required : "Enter Pickup Date",
			},
			"valid_to" : {
				required : "Enter Delivery Date",
			},
			"from_location" : {
				required : "",
			},
			"from_location_id" : {
				required : "City should be valid",
			},			
			"distance" : {
				required : "Enter Distance",
				number : "Enter Numeric Only"
			}
		},
		submitHandler : function(form) { 
			roomitemscount = 0;
			$('.roomitems').each(function () {				
				if($(this).val()!="")
				{
				 	roomitemscount=roomitemscount+1;
				}
		        //selectedValue.push($(this).val());
		    });

			if(roomitemscount==0){
			 $("#erroralertmodal .modal-body").html("Please enter any value from inventory details.");
		     $("#erroralertmodal").modal({
		      show: true
		     });
		     return false;
		 	}

			if($(".create-posttype-service:checked").val()==2){
				if($(".token-input-list li").length==1){				
				$("#erroralertmodal .modal-body").html("Please add one seller atleast.");
				     $("#erroralertmodal").modal({
				      show: true
				     })
				return false;					
				}			
			}
			form.submit();
			//return false;
			/*data="city="+$("#from_location_id").val()+"&from_date="+$("#valid_from").val()+"&to_date="+$("#valid_date").val();
			 $.ajax({
		            type: "POST",
		            url : "/chekckbuyerpost",
		            data : data,
				   success : function(dataCount){
					   if(dataCount==0){
					     form.submit();
					   }else{
						$("#erroralertmodal .modal-body").html("Post already exist with this city and dates");
					        $("#erroralertmodal").modal({
					            show: true
					        });   
					   }
					  }
		       },"json");*/
			
		}
	});



    $("#office_buyer_quote_updateform").validate({
    	ignore: [],
        rules: {
            "seller_list": {
                required: true,
            },            
        }
    });


	jQuery.validator.addMethod("officemovedistance", function(value, element) {
        if(parseFloat(value)>0){
            //return this.optional(element) || /^\d+(\.\d{1,2})?$/i.test(value);
            return this.optional(element) || /^\d{1,3}(\.\d{1,3})?$/i.test(parseFloat(element.value));
        }else{
        	return true;
        }
    }, function(params, element) {

        element.value  = Math.floor(element.value * 1000) / 1000;
        var count_value = /^\d{1,3}(\.\d{1,3})?$/i.test(parseFloat(element.value));
        if(count_value == false){
            return "Distance should be less than 1000"
        }else if(parseFloat(element.value)>0){
            return "Distance is truncated to 3 decimals"
        }else{
            return "Please enter value greater than 0"
        }

    });

 // Buyer Serch sellers - Jagadeesh : 16052016

 $("#relocation_domestic_office_buyersearch_sellers").validate({
	ignore: "input[type='text']:hidden",
	rules : {		
		"from_date" : {required : true},
		//"to_date" : {required : true},
		"from_location" : {required : true},
		"from_location_id" : {required : true},
		"distance" : {
			required : true,
			number: true,
			GrtrZero: true,	
			officemovedistance: true,
           /* decimalvalidation: {
	            	depends: function(element) {
	            		if ($('#distance').val() > 0){
		            		return true;
		            	}else{
		            		return false;
		            	}
	            	}
                }*/
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
			"from_date" : {
				required : "Enter Pickup Date",
			},
			"to_date" : {
				required : "Enter Delivery Date",
			},
			"from_location" : {
				required : "",
			},
			"from_location_id" : {
				required : "City should be valid",
			},			
			"distance" : {
				required : "Enter Distance",
				number : "Enter Numeric Only"
			}
		},
		submitHandler : function(form) { 
			roomitemscount = 0;
			$('.roomitems').each(function () {				
				if($(this).val()!="")
				{
				 	roomitemscount=roomitemscount+1;
				}
		        //selectedValue.push($(this).val());
		    });

			if(roomitemscount==0){
			 $("#erroralertmodal .modal-body").html("Please enter any value from inventory details.");
		     $("#erroralertmodal").modal({
		      show: true
		     });
		     return false;
		 	}

			if($(".create-posttype-service:checked").val()==2){
				if($(".token-input-list li").length==1){				
				$("#erroralertmodal .modal-body").html("Please add one seller atleast.");
				     $("#erroralertmodal").modal({
				      show: true
				     })
				return false;					
				}			
			}
			//return false;
			form.submit();
		}
	});
});