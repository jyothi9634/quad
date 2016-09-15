$(document).ready(function() {
    
    $("#ptlLoadType").change(function() {    	
    	var ptlLoadType = $('#ptlLoadType').val();
    	//var ptlPackageType = $('#ptlPackageType').val();
    	GetPackages(ptlLoadType);
    	//alert(quantity);
    });
    $("#load_type").change(function() {    	
    	var ptlLoadType = $('#load_type').val();
    	GetSearchPackages(ptlLoadType);
    	
    });
    $("#spot_load_type").change(function() {    	
    	var ptlLoadType = $('#spot_load_type').val();
    	GetSpotTermPackages(ptlLoadType);
    	
    });
    $("#term_load_type").change(function() {    	
    	var ptlLoadType = $('#term_load_type').val();
    	GetTermPackages(ptlLoadType);
    	
    });
    $('#posts_type').change(function(){
        if($(this).val()==2){
        $('#post_type_div').show();
    }else{
        $('#post_type_div').hide();
    }
    });
    $(".detailsslide-1").click(function(){
        
        $(this).children(".show_details").toggle();
        $(this).children(".hide_details").toggle();
    });
$('.spot_transaction_details_view_list').hide();
$(".spot_transaction_details_list").click(function(){
	
	var id =$(this).attr('id');
    $("#spot_transaction_details_view_"+id).slideToggle(500);
    $("spot_transaction_details_list").closest(".show_details").toggle();
    $("spot_transaction_details_list"+id).closest(".hide_details").toggle();
});

//$('.numberVal').onkeypress(function (event) {
//    var keycode = event.keyCode || event.which;
//    if (!(event.shiftKey == false && (keycode == 9 || keycode == 46 || keycode == 8 || keycode == 37 || keycode == 39 || (keycode >= 48 && keycode <= 57)))) {
//        event.preventDefault();
//    }
//});
$(document).on('keypress','.numberVal',function(event){
    var keycode = event.keyCode || event.which;
    if (!(event.shiftKey == false && (keycode == 9 || keycode == 46 || keycode == 8 || keycode == 37 || keycode == 39 || (keycode >= 48 && keycode <= 57)))) {
        event.preventDefault();
    }
});

jQuery.validator.addMethod("sixdigitsvalidation", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,6}(\.\d{1,6})?$/i.test(parseFloat(element.value));
    }else{
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 1000) / 1000;
	}
    var count_value = /^\d{1,6}(\.\d{1,6})?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "The value should be less than 999999"
    }	
    else if(parseFloat(element.value)>0){
        return "The value is truncated to 3 decimals"
    }else{
        return "Please enter value greater than 0"
    }

});

jQuery.validator.addMethod("twodecimalvalidation", function(value, element) {
    if(parseFloat(value)>0){
        return this.optional(element) || /^\d+(\.\d{1,2})?$/i.test(value);
     }else{
    	 return true;
     }
}, function(params, element) {

    element.value  = Math.floor(element.value * 100) / 100;

    if(parseFloat(element.value)>0){
        return "Charges are truncated to 2 decimals"
    }else{
    	return "Charges are truncated to 2 decimals"
    }

} );

jQuery.validator.addMethod("nabytwovalidations", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,9}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    }else{
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 100) / 100;
	}
    var count_value = /^\d{1,9}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "Price should be less than 1000000"
    }	
    else if(parseFloat(element.value)>0){
        return "Price is truncated to 2 decimals"
    }else{
        return "Please enter value greater than 0"
    }

});


jQuery.validator.addMethod("threebythreevalidations", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,3}(\.\d{1,3})?$/i.test(parseFloat(element.value));
    }else{
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 1000) / 1000;
	}
    var count_value = /^\d{1,3}(\.\d{1,3})?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "The value should be less than 1000"
    }	
    else if(parseFloat(element.value)>0){
        return "The value is truncated to 3 decimals"
    }else{
        return "Please enter value greater than 0"
    }

});

jQuery.validator.addMethod("rateperinc", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,9}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    }else{
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 100) / 100;
	}
    var count_value = /^\d{1,9}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "The value should be less than 1000000"
    }	
    else if(parseFloat(element.value)>0){
        return "The value is truncated to 2 decimals"
    }else{
        return "Please enter value greater than 0"
    }

});

jQuery.validator.addMethod("nabythreevalidations", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,9}(\.\d{1,3})?$/i.test(parseFloat(element.value));
    }else{
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 1000) / 1000;
	}
    var count_value = /^\d{1,9}(\.\d{1,3})?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "The value should be less than 1000000"
    }	
    else if(parseFloat(element.value)>0){
        return "The value is truncated to 3 decimals"
    }else{
        return "Please enter value greater than 0"
    }

});
jQuery.validator.addMethod("tendigitsvalidations", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,10}(\.\d{1,3})?$/i.test(parseFloat(element.value));
    }else{
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 1000) / 1000;
	}
    var count_value = /^\d{1,10}(\.\d{1,3})?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "The value should be less than 10000000"
    }	
    else if(parseFloat(element.value)>0){
        return "The value is truncated to 3 decimals"
    }else{
        return "Please enter value greater than 0"
    }

});
jQuery.validator.addMethod("arcvalidations", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,9}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    }else{
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 100) / 100;
	}
    var count_value = /^\d{1,9}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "Please enter value should be less than 1000000"
    }	
    else if(parseFloat(element.value)>0){
        return "The value is truncated to 2 decimals"
    }else{
        return "Please enter value greater than 0"
    }

});

jQuery.validator.addMethod("fourbytwovalidations", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,4}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    }else{
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 100) / 100;
	}else{
		return "This field is required."
	}
    var count_value = /^\d{1,4}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "Charges should be less than 10000"
    }	
    else if(parseFloat(element.value)>0){
        return "Charges is truncated to 2 decimals"
    }else{
        return "Charges enter value greater than 0"
    }

});

jQuery.validator.addMethod("sixbytwovalidations", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,6}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    }else{
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 100) / 100;
	}else{
		return "This field is required."
	}
    var count_value = /^\d{1,6}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "Charges should be less than 1000000"
    }	
    else if(parseFloat(element.value)>0){
        return "Charges is truncated to 2 decimals"
    }else{
        return "Charges enter value greater than 0"
    }

});

jQuery.validator.addMethod("fourbytwovalidationswithzero", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,4}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    }else{
    	return true;
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 100) / 100;
	}
    var count_value = /^\d{1,4}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "Charges should be less than 10000"
    }	
    else if(parseFloat(element.value)>0){
        return "Charges is truncated to 2 decimals"
    }

});

jQuery.validator.addMethod("fourbythreevalidations", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,4}(\.\d{1,3})?$/i.test(parseFloat(element.value));
    }else{
    }
}, function(params, element) {
	if(parseFloat(element.value)>0){
        return "The value truncated to 3 decimals";
    }else{
        return "Please enter value greater than 0";
    }
});

jQuery.validator.addMethod("fourbyfourvalidations", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,4}(\.\d{1,4})?$/i.test(parseFloat(element.value));
    }else{
        return false;
    }
}, function(params, element) {
	if(parseFloat(element.value)>0){
        return "The value truncated to 4 decimals";
    }else{
        return "Please enter value greater than 0";
    }
});

$.validator.addMethod('lessThanEqualthousand', function(value, element, param) {
    var x = 1000;
    var i = parseFloat(value);
    var j = parseFloat(x);
    return i <= j;
}, "Quantity must be less than 1000");


jQuery.validator.addMethod("fivebytwovalidationswithzero", function(value, element) {
    if(parseFloat(value)>0){
        return this.optional(element) || /^\d{1,5}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    }else{
        return true;
    }
}, function(params, element) {
    if(element.value != ''){
    element.value  = Math.floor(element.value * 100) / 100;
    }
    var count_value = /^\d{1,5}(\.\d{1,2})?$/i.test(parseFloat(element.value));
    if(count_value == false){
        return "Charges should be less than 10000"
    }   
    else if(parseFloat(element.value)>0){
        return "Charges is truncated to 2 decimals"
    }

});
jQuery.validator.addMethod("fivebythreevalidations", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,5}(\.\d{1,3})?$/i.test(parseFloat(element.value));
    }else{
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 1000) / 1000;
	}
    var count_value = /^\d{1,4}(\.\d{1,3})?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "Please enter less than 100000 only"
    }	
    else if(parseFloat(element.value)>0){
        return "The value truncated to 3 decimals"
    }else{
        return "Please enter value greater than 0"
    }

});

 //initial quote validation
$("#addsellerpostquoteoffer .initial_quote_submit").click(function(){
	var seller_post_item_id = $('#seller_post_item_id').val();

    var buttonId = $(this).attr('id');
    var removeString = 'initail_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    var $sellerQuoteOfferFieldId = $('#initial_quote_' + rowNo);
    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
    

    var $sellerTransitFieldId = $('#initial_transit_days_' + rowNo);
    var sellertransitValue = $sellerTransitFieldId.val();

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
	} else if (sellerOfferValue>200000) {
		$("#erroralertmodal .modal-body").html("Initial Quote should be less than or equal to 2 Lakhs");
        $("#erroralertmodal").modal({
            show: true
        });
		$sellerQuoteOfferFieldId.focus();
		 return false;
	}else if (!($sellerTransitFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
        $sellerTransitFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellertransitValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
        $sellerTransitFieldId.focus();
		 return false;
	}else if (sellertransitValue==0) {
		$("#erroralertmodal .modal-body").html("Transit Days should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        $sellerTransitFieldId.focus();
		 return false;
	}else {
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
            data : { 'initial_quote': sellerOfferValue,'initial_transit': sellertransitValue, 'buyer_buyerquote_id' : rowNo,'seller_post_item_id' : seller_post_item_id },
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

//lead FTL
$("#leadsellerpostquoteoffer .initial_lead_quote_submit").click(function(){
	var seller_post_item_id = $('#seller_post_item_id').val();

    var buttonId = $(this).attr('id');
    var removeString = 'initail_lead_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var $sellerQuoteOfferFieldId = $('#initial_lead_quote_' + rowNo);
    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
    
    var $sellerTransitFieldId = $('#initial_transit_days_' + rowNo);
    var sellertransitValue = $sellerTransitFieldId.val();

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
	}else if (sellerOfferValue>200000) {
		$("#erroralertmodal .modal-body").html("Initial Quote should be less than or equal to 2 Lakhs");
        $("#erroralertmodal").modal({
            show: true
        });
		$sellerQuoteOfferFieldId.focus();
		 return false;
	} else if (!($sellerTransitFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
        $sellerTransitFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellertransitValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
        $sellerTransitFieldId.focus();
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
            data : { 'initial_quote': sellerOfferValue,'initial_transit': sellertransitValue, 'buyer_buyerquote_id' : rowNo,'seller_post_item_id' : seller_post_item_id },
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


$("#addptlsellerpostquoteoffer .ptl_initial_rate_per_kg,#addptlsellerpostquoteoffer .ptl_initial_conversion,#addptlsellerpostquoteoffer .ptl_initial_pickup ,#addptlsellerpostquoteoffer .ptl_initial_delivery,#addptlsellerpostquoteoffer .ptl_initial_oda").blur(function() {
	var serviceid = $('#serviceid').val();
	var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];
	
	var buyerId = $(this).attr('id').split("_").reverse()[1];
	var buyerquoteId = $(this).attr('id').split("_").reverse()[0]
    var $rateForKgFieldId = $('#initial_quote_rateperkg_' + rowNo);
    var $kgCftFieldId = $('#initial_quote_kgperdft_' + rowNo);
    
    if(serviceid == 8 || serviceid == 9){
    	var $pickup = 0;
        var $delivery = 0;
        var $oda = 0;
        var pickupvalue = 0;
        var deliveryvalue = 0;
        var odachargevalue = 0;
    
    }else{
	    var $pickup = $('#initial_quote_pickup_' + rowNo);
	    var $delivery = $('#initial_quote_delivery_' + rowNo);
	    var $oda = $('#initial_quote_oda_' + rowNo);
	    var pickupvalue = $pickup.val();
	    var deliveryvalue = $delivery.val();
	    var odachargevalue = $oda.val();
	   
    }

    var $incrementcount = $('#incrementcount_' + rowNo);
    var incrementcount = $incrementcount.val();
    
    var counterRateForKgValue = $rateForKgFieldId.val();
    var conversionKgCftValue = $kgCftFieldId.val();
   
    
    
    
    var regexrateperkgPattern = /^\d{1,4}(\.\d{1,2})?$/;
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
    
	
	 if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
	        return false;
	    } else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
	        return false;
	    }  else if(counterRateForKgValue == 0){
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexrateperkgPattern.test(counterRateForKgValue) ) {
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be less than 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    } else if(conversionKgCftValue == 0){
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexconversionfactorPattern.test(conversionKgCftValue) ) {
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be less than 10000 with max of 3 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }   else {
	
	
    	
        $.ajax({
            type: "POST",
            url: "/getSellerfreightdetails",
            data: {
                'counterRateForKgValue': counterRateForKgValue,
                'conversionKgCftValue': conversionKgCftValue,
                'pickupvalue': pickupvalue,
                'deliveryvalue': deliveryvalue,
                'odachargevalue': odachargevalue,
                'buyerId': buyerId,
                'buyerquoteId': buyerquoteId,
                'incrementcount': incrementcount,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#final_rate_per_kg_' + rowNo).data('rateperkg', jsonData.freightDetails.counterRatePerKg);
                    $('#pick_up_rate_' + rowNo).data('pickuprateperkg', jsonData.freightDetails.pickUpPrice);
                    $('#delivery_charges_' + rowNo).data('deliverycharges', jsonData.freightDetails.deliveryPrice);
                    $('#oda_charges_' + rowNo).data('odacharges', jsonData.freightDetails.oda);
                    $('#freight_charges_' + rowNo).data('freightcharges', jsonData.freightDetails.totalFreightAmount);
                    $('#total_charges_' + rowNo).data('totalcharges', jsonData.freightDetails.totalAmount);
                    $('#final_rate_per_kg_' + rowNo).text(jsonData.freightDetails.formattedCounterRatePerKg);
                    $('#pick_up_rate_' + rowNo).text(jsonData.freightDetails.formattedPickUpPrice);
                    $('#delivery_charges_' + rowNo).text(jsonData.freightDetails.formattedDeliveryPrice);
                    $('#oda_charges_' + rowNo).text(jsonData.freightDetails.formattedOda);
                    $('#freight_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalFreightAmount);
                    $('#total_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalAmount);
                }
            }
        }, "json");
    }
	
});


//search onfocusout calculation
$("#addptlsellersearchpostquoteoffer .ptl_initial_rate_per_kg,#addptlsellersearchpostquoteoffer .ptl_initial_conversion,#addptlsellersearchpostquoteoffer .ptl_initial_pickup ,#addptlsellersearchpostquoteoffer .ptl_initial_delivery,#addptlsellersearchpostquoteoffer .ptl_initial_oda").blur(function() {
	var serviceid = $('#serviceid').val();
	//var buyerquoteid = $('#buyerquoteid').val();
	var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];
	
	var buyerId = $(this).attr('id').split("_").reverse()[1];
	var buyerquoteId = $(this).attr('id').split("_").reverse()[0]
    var $rateForKgFieldId = $('#initial_quote_rateperkg_' + rowNo);
    var $kgCftFieldId = $('#initial_quote_kgperdft_' + rowNo);

	var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    if(serviceid == 8 || serviceid == 9){
    	var $pickup = 0;
        var $delivery = 0;
        var $oda = 0;
        var pickupvalue = 0;
        var deliveryvalue = 0;
        var odachargevalue = 0;
    
    }else{
	    var $pickup = $('#initial_quote_pickup_' + rowNo);
	    var $delivery = $('#initial_quote_delivery_' + rowNo);
	    var $oda = $('#initial_quote_oda_' + rowNo);
	    var pickupvalue = $pickup.val();
	    var deliveryvalue = $delivery.val();
	    var odachargevalue = $oda.val();
	   
    }

    
     
    var $incrementcount = $('#incrementcount_' + rowNo);
    var incrementcount = $incrementcount.val();
    
    var counterRateForKgValue = $rateForKgFieldId.val();
    var conversionKgCftValue = $kgCftFieldId.val();
   
    var regexrateperkgPattern = /^\d{1,4}(\.\d{1,2})?$/;
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
    
	 if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
	        return false;
	    } else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
	        return false;
	    }  else if(counterRateForKgValue == 0){
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexrateperkgPattern.test(counterRateForKgValue) ) {
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be less than 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    } else if(conversionKgCftValue == 0){
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexconversionfactorPattern.test(conversionKgCftValue) ) {
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be less than 10000 with max of 3 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }   else {
    	
        $.ajax({
            type: "POST",
            url: "/getSellerfreightdetails",
            data: {
                'counterRateForKgValue': counterRateForKgValue,
                'conversionKgCftValue': conversionKgCftValue,
                'pickupvalue': pickupvalue,
                'deliveryvalue': deliveryvalue,
                'odachargevalue': odachargevalue,
                'buyerId': buyerId,
                'buyerquoteId': buyerquoteid,
                'incrementcount': incrementcount,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#final_rate_per_kg_' + rowNo).data('rateperkg', jsonData.freightDetails.counterRatePerKg);
                    $('#pick_up_rate_' + rowNo).data('pickuprateperkg', jsonData.freightDetails.pickUpPrice);
                    $('#delivery_charges_' + rowNo).data('deliverycharges', jsonData.freightDetails.deliveryPrice);
                    $('#oda_charges_' + rowNo).data('odacharges', jsonData.freightDetails.oda);
                    $('#freight_charges_' + rowNo).data('freightcharges', jsonData.freightDetails.totalFreightAmount);
                    $('#total_charges_' + rowNo).data('totalcharges', jsonData.freightDetails.totalAmount);
                    $('#final_rate_per_kg_' + rowNo).text(jsonData.freightDetails.formattedCounterRatePerKg);
                    $('#pick_up_rate_' + rowNo).text(jsonData.freightDetails.formattedPickUpPrice);
                    $('#delivery_charges_' + rowNo).text(jsonData.freightDetails.formattedDeliveryPrice);
                    $('#oda_charges_' + rowNo).text(jsonData.freightDetails.formattedOda);
                    $('#freight_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalFreightAmount);
                    $('#total_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalAmount);
                }
            }
        }, "json");
    }
	
});



$("#addptlsellersearchpostquoteoffer .ptl_final_rate_per_kg,#addptlsellersearchpostquoteoffer .ptl_final_conversion,#addptlsellersearchpostquoteoffer .ptl_final_pickup ,#addptlsellersearchpostquoteoffer .ptl_final_delivery,#addptlsellersearchpostquoteoffer .ptl_final_oda").blur(function() {
	var serviceid = $('#serviceid').val();
	var buyerquoteid = $('#buyerquoteid').val();
	var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];
	
	var buyerId = $(this).attr('id').split("_").reverse()[1];
	var buyerquoteId = $(this).attr('id').split("_").reverse()[0]
    var $rateForKgFieldId = $('#final_quote_rateperkg_' + rowNo);
    var $kgCftFieldId = $('#final_quote_kgperdft_' + rowNo);
    
    if(serviceid == 8 || serviceid == 9){
    	var $pickup = 0;
        var $delivery = 0;
        var $oda = 0;
        var pickupvalue = 0;
        var deliveryvalue = 0;
        var odachargevalue = 0;
    
    }else{
	    var $pickup = $('#final_quote_pickup_' + rowNo);
	    var $delivery = $('#final_quote_delivery_' + rowNo);
	    var $oda = $('#final_quote_oda_' + rowNo);
	    var pickupvalue = $pickup.val();
	    var deliveryvalue = $delivery.val();
	    var odachargevalue = $oda.val();
	   
    }

    
     
    var $incrementcount = $('#incrementcount_' + rowNo);
    var incrementcount = $incrementcount.val();
    
    var counterRateForKgValue = $rateForKgFieldId.val();
    var conversionKgCftValue = $kgCftFieldId.val();
   
    var regexrateperkgPattern = /^\d{1,4}(\.\d{1,2})?$/;
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/; 
	
	 if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
	        return false;
	    } else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
	        return false;
	    }  else if(counterRateForKgValue == 0){
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexrateperkgPattern.test(counterRateForKgValue) ) {
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be less than 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    } else if(conversionKgCftValue == 0){
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexconversionfactorPattern.test(conversionKgCftValue) ) {
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be less than 10000 with max of 3 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }   else {
    	
        $.ajax({
            type: "POST",
            url: "/getSellerfreightdetails",
            data: {
                'counterRateForKgValue': counterRateForKgValue,
                'conversionKgCftValue': conversionKgCftValue,
                'pickupvalue': pickupvalue,
                'deliveryvalue': deliveryvalue,
                'odachargevalue': odachargevalue,
                'buyerId': buyerId,
                'buyerquoteId': buyerquoteid,
                'incrementcount': incrementcount,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#final_rate_per_kg_' + rowNo).data('rateperkg', jsonData.freightDetails.counterRatePerKg);
                    $('#pick_up_rate_' + rowNo).data('pickuprateperkg', jsonData.freightDetails.pickUpPrice);
                    $('#delivery_charges_' + rowNo).data('deliverycharges', jsonData.freightDetails.deliveryPrice);
                    $('#oda_charges_' + rowNo).data('odacharges', jsonData.freightDetails.oda);
                    $('#freight_charges_' + rowNo).data('freightcharges', jsonData.freightDetails.totalFreightAmount);
                    $('#total_charges_' + rowNo).data('totalcharges', jsonData.freightDetails.totalAmount);
                    $('#final_rate_per_kg_' + rowNo).text(jsonData.freightDetails.formattedCounterRatePerKg);
                    $('#pick_up_rate_' + rowNo).text(jsonData.freightDetails.formattedPickUpPrice);
                    $('#delivery_charges_' + rowNo).text(jsonData.freightDetails.formattedDeliveryPrice);
                    $('#oda_charges_' + rowNo).text(jsonData.freightDetails.formattedOda);
                    $('#freight_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalFreightAmount);
                    $('#total_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalAmount);
                }
            }
        }, "json");
    }
	
});



//Courier Search
$("#couriersearchpostquoteoffer  .ptl_initial_rate_per_kg,#couriersearchpostquoteoffer .ptl_initial_conversion,#couriersearchpostquoteoffer .ptl_initial_fuel ,#couriersearchpostquoteoffer .ptl_initial_cod,#couriersearchpostquoteoffer .ptl_initial_freight,#couriersearchpostquoteoffer .ptl_initial_arc").blur(function() {
	var serviceid = $('#serviceid').val();
	
	var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];
	
	var buyerId = $(this).attr('id').split("_").reverse()[1];
	var buyerquoteId = $(this).attr('id').split("_").reverse()[0]
    var $rateForKgFieldId = $('#initial_quote_rateperkg_' + rowNo);
    var $kgCftFieldId = $('#initial_quote_kgperdft_' + rowNo);
    
    
    var $pickup = $('#initial_fuel_surcharge_rupees_' + rowNo);
	var $delivery = $('#initial_cod_rupees_' + rowNo);
	var $oda = $('#initial_freight_collect_rupees_' + rowNo);
	var $arc = $('#initial_arc_rupees_' + rowNo);
	var buyerquoteid= $('#buyerquoteid_' + rowNo).val();
	var pickupvalue = $pickup.val();
	var deliveryvalue = $delivery.val();
    var odachargevalue = $oda.val();
    var arcvalue = $arc.val();
    var $payment = $('#payment_options_' + rowNo);
	var paymentvalue = $payment.val();
	if(paymentvalue==2){
		var paymentval = 1;
	}else{
		var paymentval = 0;
	}

    
     
    var $incrementcount = $('#incrementcount_' + rowNo);
    var incrementcount = $incrementcount.val();
    
    var counterRateForKgValue = $rateForKgFieldId.val();
    var conversionKgCftValue = $kgCftFieldId.val();
   
    var regexrateperkgPattern = /^\d{1,4}(\.\d{1,2})?$/;
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
    
    if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
        return false;
    } else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
        return false;
    }  else if(counterRateForKgValue == 0){
    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }else if (!regexrateperkgPattern.test(counterRateForKgValue) ) {
    	$("#erroralertmodal .modal-body").html("Rate per Kg should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    } else if(conversionKgCftValue == 0){
    	$("#erroralertmodal .modal-body").html("Conversion Factor should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }else if (!regexconversionfactorPattern.test(conversionKgCftValue) ) {
    	$("#erroralertmodal .modal-body").html("Conversion Factor should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }   else {
    	
        $.ajax({
            type: "POST",
            url: "/getSellerfreightdetails",
            data: {
                'counterRateForKgValue': counterRateForKgValue,
                'conversionKgCftValue': conversionKgCftValue,
                'pickupvalue': pickupvalue,
                'deliveryvalue': deliveryvalue,
                'odachargevalue': odachargevalue,
                'arcvalue': arcvalue,
                'buyerId': buyerId,
                'buyerquoteId': buyerquoteid,
                'incrementcount': incrementcount,
                'seller_post_item_id':serviceid,
                'paymentval':paymentval,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#final_rate_per_kg_' + rowNo).data('rateperkg', jsonData.freightDetails.counterRatePerKg);
                    $('#pick_up_rate_' + rowNo).data('pickuprateperkg', jsonData.freightDetails.pickUpPrice);
                    $('#delivery_charges_' + rowNo).data('deliverycharges', jsonData.freightDetails.deliveryPrice);
                    $('#oda_charges_' + rowNo).data('odacharges', jsonData.freightDetails.oda);
                    $('#freight_charges_' + rowNo).data('freightcharges', jsonData.freightDetails.totalFreightAmount);
                    $('#total_charges_' + rowNo).data('totalcharges', jsonData.freightDetails.totalAmount);
                    $('#final_rate_per_kg_' + rowNo).text(jsonData.freightDetails.formattedCounterRatePerKg);
                    $('#pick_up_rate_' + rowNo).text(jsonData.freightDetails.formattedPickUpPrice);
                    $('#delivery_charges_' + rowNo).text(jsonData.freightDetails.formattedDeliveryPrice);
                    $('#oda_charges_' + rowNo).text(jsonData.freightDetails.formattedOda);
                    $('#freight_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalFreightAmount);
                    $('#total_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalAmount);
                }
            }
        }, "json");
    }
	
});

$("#couriersearchpostquoteoffer  .ptl_payment").change(function() {
	
	var serviceid = $('#serviceid').val();
	
	var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];
	
	var buyerId = $(this).attr('id').split("_").reverse()[1];
	var buyerquoteId = $(this).attr('id').split("_").reverse()[0]
    var $rateForKgFieldId = $('#initial_quote_rateperkg_' + rowNo);
    var $kgCftFieldId = $('#initial_quote_kgperdft_' + rowNo);
    
    
    var $pickup = $('#initial_fuel_surcharge_rupees_' + rowNo);
	var $delivery = $('#initial_cod_rupees_' + rowNo);
	var $oda = $('#initial_freight_collect_rupees_' + rowNo);
	var $arc = $('#initial_arc_rupees_' + rowNo);
	var buyerquoteid= $('#buyerquoteid_' + rowNo).val();
	var pickupvalue = $pickup.val();
	var deliveryvalue = $delivery.val();
    var odachargevalue = $oda.val();
    var arcvalue = $arc.val();
    var $payment = $('#payment_options_' + rowNo);
	var paymentvalue = $payment.val();
	if(paymentvalue==2){
		var paymentval = 1;
	}else{
		var paymentval = 0;
	}
    
     
    var $incrementcount = $('#incrementcount_' + rowNo);
    var incrementcount = $incrementcount.val();
    
    var counterRateForKgValue = $rateForKgFieldId.val();
    var conversionKgCftValue = $kgCftFieldId.val();
   
    var regexrateperkgPattern = /^\d{1,4}(\.\d{1,2})?$/;
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
    
    if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
        return false;
    } else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
        return false;
    }  else if(counterRateForKgValue == 0){
    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }else if (!regexrateperkgPattern.test(counterRateForKgValue) ) {
    	$("#erroralertmodal .modal-body").html("Rate per Kg should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    } else if(conversionKgCftValue == 0){
    	$("#erroralertmodal .modal-body").html("Conversion Factor should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }else if (!regexconversionfactorPattern.test(conversionKgCftValue) ) {
    	$("#erroralertmodal .modal-body").html("Conversion Factor should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }   else {
    	
        $.ajax({
            type: "POST",
            url: "/getSellerfreightdetails",
            data: {
                'counterRateForKgValue': counterRateForKgValue,
                'conversionKgCftValue': conversionKgCftValue,
                'pickupvalue': pickupvalue,
                'deliveryvalue': deliveryvalue,
                'odachargevalue': odachargevalue,
                'arcvalue': arcvalue,
                'buyerId': buyerId,
                'buyerquoteId': buyerquoteid,
                'incrementcount': incrementcount,
                'seller_post_item_id':serviceid,
                'paymentval':paymentval,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#final_rate_per_kg_' + rowNo).data('rateperkg', jsonData.freightDetails.counterRatePerKg);
                    $('#pick_up_rate_' + rowNo).data('pickuprateperkg', jsonData.freightDetails.pickUpPrice);
                    $('#delivery_charges_' + rowNo).data('deliverycharges', jsonData.freightDetails.deliveryPrice);
                    $('#oda_charges_' + rowNo).data('odacharges', jsonData.freightDetails.oda);
                    $('#freight_charges_' + rowNo).data('freightcharges', jsonData.freightDetails.totalFreightAmount);
                    $('#total_charges_' + rowNo).data('totalcharges', jsonData.freightDetails.totalAmount);
                    $('#final_rate_per_kg_' + rowNo).text(jsonData.freightDetails.formattedCounterRatePerKg);
                    $('#pick_up_rate_' + rowNo).text(jsonData.freightDetails.formattedPickUpPrice);
                    $('#delivery_charges_' + rowNo).text(jsonData.freightDetails.formattedDeliveryPrice);
                    $('#oda_charges_' + rowNo).text(jsonData.freightDetails.formattedOda);
                    $('#freight_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalFreightAmount);
                    $('#total_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalAmount);
                }
            }
        }, "json");
    }
		
});

$("#couriersearchpostquoteoffer  .ptl_final_rate_per_kg,#couriersearchpostquoteoffer .ptl_final_conversion,#couriersearchpostquoteoffer .ptl_final_fuel ,#couriersearchpostquoteoffer .ptl_final_cod,#couriersearchpostquoteoffer .ptl_final_freight,#couriersearchpostquoteoffer .ptl_final_arc").blur(function() {
	var serviceid = $('#serviceid').val();
	
	var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];
	
	var buyerId = $(this).attr('id').split("_").reverse()[1];
	var buyerquoteId = $(this).attr('id').split("_").reverse()[0]
    var $rateForKgFieldId = $('#final_quote_rateperkg_' + rowNo);
    var $kgCftFieldId = $('#final_quote_kgperdft_' + rowNo);
    
    
    var $pickup = $('#final_fuel_surcharge_rupees_' + rowNo);
	var $delivery = $('#final_cod_rupees_' + rowNo);
	var $oda = $('#final_freight_collect_rupees_' + rowNo);
	var $arc = $('#final_arc_rupees_' + rowNo);
	var buyerquoteid= $('#buyerquoteid_' + rowNo).val();
	var pickupvalue = $pickup.val();
	var deliveryvalue = $delivery.val();
    var odachargevalue = $oda.val();
    var arcvalue = $arc.val();
    var $payment = $('#final_payment_options_' + rowNo);
	var paymentvalue = $payment.val();
	
	if(paymentvalue=='Cash on delivery'){
		var paymentval = 1;
	}else{
		var paymentval = 0;
	}  
    var $incrementcount = $('#incrementcount_' + rowNo);
    var incrementcount = $incrementcount.val();
    
    var counterRateForKgValue = $rateForKgFieldId.val();
    var conversionKgCftValue = $kgCftFieldId.val();
   
    var regexrateperkgPattern = /^\d{1,4}(\.\d{1,2})?$/;
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
    
	if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
        return false;
    } else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
        return false;
    } else if(counterRateForKgValue == 0){
    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }else if (!regexrateperkgPattern.test(counterRateForKgValue) ) {
    	$("#erroralertmodal .modal-body").html("Rate per Kg should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    } else if(conversionKgCftValue == 0){
    	$("#erroralertmodal .modal-body").html("Conversion Factor should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }else if (!regexconversionfactorPattern.test(conversionKgCftValue) ) {
    	$("#erroralertmodal .modal-body").html("Conversion Factor should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }   else {
    	
        $.ajax({
            type: "POST",
            url: "/getSellerfreightdetails",
            data: {
                'counterRateForKgValue': counterRateForKgValue,
                'conversionKgCftValue': conversionKgCftValue,
                'pickupvalue': pickupvalue,
                'deliveryvalue': deliveryvalue,
                'odachargevalue': odachargevalue,
                'arcvalue': arcvalue,
                'buyerId': buyerId,
                'buyerquoteId': buyerquoteid,
                'incrementcount': incrementcount,
                'seller_post_item_id':serviceid,
                'paymentval':paymentval,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#final_rate_per_kg_' + rowNo).data('rateperkg', jsonData.freightDetails.counterRatePerKg);
                    $('#pick_up_rate_' + rowNo).data('pickuprateperkg', jsonData.freightDetails.pickUpPrice);
                    $('#delivery_charges_' + rowNo).data('deliverycharges', jsonData.freightDetails.deliveryPrice);
                    $('#oda_charges_' + rowNo).data('odacharges', jsonData.freightDetails.oda);
                    $('#freight_charges_' + rowNo).data('freightcharges', jsonData.freightDetails.totalFreightAmount);
                    $('#total_charges_' + rowNo).data('totalcharges', jsonData.freightDetails.totalAmount);
                    $('#final_rate_per_kg_' + rowNo).text(jsonData.freightDetails.formattedCounterRatePerKg);
                    $('#pick_up_rate_' + rowNo).text(jsonData.freightDetails.formattedPickUpPrice);
                    $('#delivery_charges_' + rowNo).text(jsonData.freightDetails.formattedDeliveryPrice);
                    $('#oda_charges_' + rowNo).text(jsonData.freightDetails.formattedOda);
                    $('#freight_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalFreightAmount);
                    $('#total_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalAmount);
                }
            }
        }, "json");
    }
	
});

$("#addcouriersellerpostquoteoffer .ptl_initial_rate_per_kg,#addcouriersellerpostquoteoffer .ptl_initial_conversion,#addcouriersellerpostquoteoffer .ptl_initial_fuel ,#addcouriersellerpostquoteoffer .ptl_initial_cod,#addcouriersellerpostquoteoffer .ptl_initial_freight,#addcouriersellerpostquoteoffer .ptl_initial_arc").blur(function() {
	
	var serviceid = $('#serviceid').val();
	var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];

	var seller_post_item_id = $('#seller_post_item_id').val();
	var buyerId = $(this).attr('id').split("_").reverse()[1];
	var buyerquoteId = $(this).attr('id').split("_").reverse()[0]
    var $rateForKgFieldId = $('#initial_quote_rateperkg_' + rowNo);
    var $kgCftFieldId = $('#initial_quote_kgperdft_' + rowNo);
    
	var $pickup = $('#initial_fuel_surcharge_rupees_' + rowNo);
	var $delivery = $('#initial_cod_rupees_' + rowNo);
	var $oda = $('#initial_freight_collect_rupees_' + rowNo);
	var $arc = $('#initial_arc_rupees_' + rowNo);
	var pickupvalue = $pickup.val();
	var deliveryvalue = $delivery.val();
    var odachargevalue = $oda.val();
    var arcvalue = $arc.val();
	   
    

    var $incrementcount = $('#incrementcount_' + rowNo);
    var incrementcount = $incrementcount.val();
    
    var counterRateForKgValue = $rateForKgFieldId.val();
    var conversionKgCftValue = $kgCftFieldId.val();
    
    var regexrateperkgPattern = /^\d{1,4}(\.\d{1,2})?$/;
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
    
    
	 if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
	        return false;
	    } else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
	        return false;
	    }  else if(counterRateForKgValue == 0){
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexrateperkgPattern.test(counterRateForKgValue) ) {
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be less than 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    } else if(conversionKgCftValue == 0){
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexconversionfactorPattern.test(conversionKgCftValue) ) {
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be less than 10000 with max of 3 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }   else {
    	
        $.ajax({
            type: "POST",
            url: "/getSellerfreightdetails",
            data: {
                'counterRateForKgValue': counterRateForKgValue,
                'conversionKgCftValue': conversionKgCftValue,
                'pickupvalue': pickupvalue,
                'deliveryvalue': deliveryvalue,
                'odachargevalue': odachargevalue,
                'arcvalue': arcvalue,
                'buyerId': buyerId,
                'buyerquoteId': buyerquoteId,
                'seller_post_item_id':seller_post_item_id,
                'incrementcount': incrementcount,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#final_rate_per_kg_' + rowNo).data('rateperkg', jsonData.freightDetails.counterRatePerKg);
                    $('#pick_up_rate_' + rowNo).data('pickuprateperkg', jsonData.freightDetails.pickUpPrice);
                    $('#delivery_charges_' + rowNo).data('deliverycharges', jsonData.freightDetails.deliveryPrice);
                    $('#oda_charges_' + rowNo).data('odacharges', jsonData.freightDetails.oda);
                    $('#freight_charges_' + rowNo).data('freightcharges', jsonData.freightDetails.totalFreightAmount);
                    $('#total_charges_' + rowNo).data('totalcharges', jsonData.freightDetails.totalAmount);
                    $('#final_rate_per_kg_' + rowNo).text(jsonData.freightDetails.formattedCounterRatePerKg);
                    $('#pick_up_rate_' + rowNo).text(jsonData.freightDetails.formattedPickUpPrice);
                    $('#delivery_charges_' + rowNo).text(jsonData.freightDetails.formattedDeliveryPrice);
                    $('#oda_charges_' + rowNo).text(jsonData.freightDetails.formattedOda);
                    $('#freight_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalFreightAmount);
                    $('#total_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalAmount);
                }
            }
        }, "json");
    }
	
});


$("#leadptlsellercounterquoteoffer .ptl_initial_rate_per_kg,#leadptlsellercounterquoteoffer .ptl_initial_conversion,#leadptlsellercounterquoteoffer .ptl_initial_fuel ,#leadptlsellercounterquoteoffer .ptl_initial_cod,#leadptlsellercounterquoteoffer .ptl_initial_freight,#leadptlsellercounterquoteoffer .ptl_initial_arc").blur(function() {
	
	var serviceid = $('#serviceid').val();
	var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];

	var seller_post_item_id = $('#seller_post_item_id').val();
	var buyerId = $(this).attr('id').split("_").reverse()[1];
	var buyerquoteId = $(this).attr('id').split("_").reverse()[0]
    var $rateForKgFieldId = $('#initial_lead_quote_rateperkg_' + rowNo);
    var $kgCftFieldId = $('#initial_lead_quote_kgperdft_' + rowNo);
    
	var $pickup = $('#initial_fuel_surcharge_rupees_' + rowNo);
	var $delivery = $('#initial_cod_rupees_' + rowNo);
	var $oda = $('#initial_freight_collect_rupees_' + rowNo);
	var $arc = $('#initial_arc_rupees_' + rowNo);
	var pickupvalue = $pickup.val();
	var deliveryvalue = $delivery.val();
    var odachargevalue = $oda.val();
    var arcvalue = $arc.val();
	   
    

    var $incrementcount = $('#incrementcount_' + rowNo);
    var incrementcount = $incrementcount.val();
    
    var counterRateForKgValue = $rateForKgFieldId.val();
    var conversionKgCftValue = $kgCftFieldId.val();
   
    
    
    var regexrateperkgPattern = /^\d{1,4}(\.\d{1,2})?$/;
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
    
   
    
    if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
 	        return false;
 	    } else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
 	        return false;
 	    }  else if(counterRateForKgValue == 0){
 	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
 	        $("#erroralertmodal").modal({
 	            show: true
 	        });
 	        return false;
    }else if (!regexrateperkgPattern.test(counterRateForKgValue) ) {
    	$("#erroralertmodal .modal-body").html("Rate per Kg should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    } else if(conversionKgCftValue == 0){
    	$("#erroralertmodal .modal-body").html("Conversion Factor should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }else if (!regexconversionfactorPattern.test(conversionKgCftValue) ) {
    	$("#erroralertmodal .modal-body").html("Conversion Factor should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }   else {
	
    	
        $.ajax({
            type: "POST",
            url: "/getSellerfreightdetails",
            data: {
                'counterRateForKgValue': counterRateForKgValue,
                'conversionKgCftValue': conversionKgCftValue,
                'pickupvalue': pickupvalue,
                'deliveryvalue': deliveryvalue,
                'odachargevalue': odachargevalue,
                'arcvalue': arcvalue,
                'buyerId': buyerId,
                'buyerquoteId': buyerquoteId,
                'seller_post_item_id':seller_post_item_id,
                'incrementcount': incrementcount,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#final_rate_per_kg_' + rowNo).data('rateperkg', jsonData.freightDetails.counterRatePerKg);
                    $('#pick_up_rate_' + rowNo).data('pickuprateperkg', jsonData.freightDetails.pickUpPrice);
                    $('#delivery_charges_' + rowNo).data('deliverycharges', jsonData.freightDetails.deliveryPrice);
                    $('#oda_charges_' + rowNo).data('odacharges', jsonData.freightDetails.oda);
                    $('#freight_charges_' + rowNo).data('freightcharges', jsonData.freightDetails.totalFreightAmount);
                    $('#total_charges_' + rowNo).data('totalcharges', jsonData.freightDetails.totalAmount);
                    $('#final_rate_per_kg_' + rowNo).text(jsonData.freightDetails.formattedCounterRatePerKg);
                    $('#pick_up_rate_' + rowNo).text(jsonData.freightDetails.formattedPickUpPrice);
                    $('#delivery_charges_' + rowNo).text(jsonData.freightDetails.formattedDeliveryPrice);
                    $('#oda_charges_' + rowNo).text(jsonData.freightDetails.formattedOda);
                    $('#freight_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalFreightAmount);
                    $('#total_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalAmount);
                }
            }
        }, "json");
    }
	
});


$("#leadptlsellercounterquoteoffer .ptl_final_rate_per_kg,#leadptlsellercounterquoteoffer .ptl_final_conversion,#leadptlsellercounterquoteoffer .ptl_final_fuel ,#leadptlsellercounterquoteoffer .ptl_final_cod,#leadptlsellercounterquoteoffer .ptl_final_freight,#leadptlsellercounterquoteoffer .ptl_final_arc").blur(function() {
	
	var serviceid = $('#serviceid').val();
	var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];

	var seller_post_item_id = $('#seller_post_item_id').val();
	var buyerId = $(this).attr('id').split("_").reverse()[1];
	var buyerquoteId = $(this).attr('id').split("_").reverse()[0]
    var $rateForKgFieldId = $('#final_lead_quote_rateperkg_' + rowNo);
    var $kgCftFieldId = $('#final_lead_quote_kgperdft_' + rowNo);
    
	var $pickup = $('#final_fuel_surcharge_rupees_' + rowNo);
	var $delivery = $('#final_cod_rupees_' + rowNo);
	var $oda = $('#final_freight_collect_rupees_' + rowNo);
	var $arc = $('#final_arc_rupees_' + rowNo);
	var pickupvalue = $pickup.val();
	var deliveryvalue = $delivery.val();
    var odachargevalue = $oda.val();
    var arcvalue = $arc.val();
	   
    

    var $incrementcount = $('#incrementcount_' + rowNo);
    var incrementcount = $incrementcount.val();
    
    var counterRateForKgValue = $rateForKgFieldId.val();
    var conversionKgCftValue = $kgCftFieldId.val();
   
    var regexrateperkgPattern = /^\d{1,4}(\.\d{1,2})?$/;
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
    
    
	 if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
	        return false;
	    } else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
	        return false;
	    }  else if(counterRateForKgValue == 0){
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	 }else if (!regexrateperkgPattern.test(counterRateForKgValue) ) {
	 	$("#erroralertmodal .modal-body").html("Rate per Kg should be less than 10000 with max of 2 decimals");
	     $("#erroralertmodal").modal({
	         show: true
	     });
	     return false;
	 } else if(conversionKgCftValue == 0){
	 	$("#erroralertmodal .modal-body").html("Conversion Factor should be greater than 0");
	     $("#erroralertmodal").modal({
	         show: true
	     });
	     return false;
	 }else if (!regexconversionfactorPattern.test(conversionKgCftValue) ) {
	 	$("#erroralertmodal .modal-body").html("Conversion Factor should be less than 10000 with max of 3 decimals");
	     $("#erroralertmodal").modal({
	         show: true
	     });
	     return false;
	 }   else {
	
    	
        $.ajax({
            type: "POST",
            url: "/getSellerfreightdetails",
            data: {
                'counterRateForKgValue': counterRateForKgValue,
                'conversionKgCftValue': conversionKgCftValue,
                'pickupvalue': pickupvalue,
                'deliveryvalue': deliveryvalue,
                'odachargevalue': odachargevalue,
                'arcvalue': arcvalue,
                'buyerId': buyerId,
                'buyerquoteId': buyerquoteId,
                'seller_post_item_id':seller_post_item_id,
                'incrementcount': incrementcount,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#final_rate_per_kg_' + rowNo).data('rateperkg', jsonData.freightDetails.counterRatePerKg);
                    $('#pick_up_rate_' + rowNo).data('pickuprateperkg', jsonData.freightDetails.pickUpPrice);
                    $('#delivery_charges_' + rowNo).data('deliverycharges', jsonData.freightDetails.deliveryPrice);
                    $('#oda_charges_' + rowNo).data('odacharges', jsonData.freightDetails.oda);
                    $('#freight_charges_' + rowNo).data('freightcharges', jsonData.freightDetails.totalFreightAmount);
                    $('#total_charges_' + rowNo).data('totalcharges', jsonData.freightDetails.totalAmount);
                    $('#final_rate_per_kg_' + rowNo).text(jsonData.freightDetails.formattedCounterRatePerKg);
                    $('#pick_up_rate_' + rowNo).text(jsonData.freightDetails.formattedPickUpPrice);
                    $('#delivery_charges_' + rowNo).text(jsonData.freightDetails.formattedDeliveryPrice);
                    $('#oda_charges_' + rowNo).text(jsonData.freightDetails.formattedOda);
                    $('#freight_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalFreightAmount);
                    $('#total_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalAmount);
                }
            }
        }, "json");
    }
	
});


$("#leadptlsellerpostquoteoffer .ptl_initial_rate_per_kg,#leadptlsellerpostquoteoffer .ptl_initial_conversion,#leadptlsellerpostquoteoffer .ptl_initial_pickup ,#leadptlsellerpostquoteoffer .ptl_initial_delivery,#leadptlsellerpostquoteoffer .ptl_initial_oda").blur(function() {
	
	var serviceid = $('#serviceid').val();
	var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];
	
	var buyerId = $(this).attr('id').split("_").reverse()[1];
	var buyerquoteId = $(this).attr('id').split("_").reverse()[0]
    var $rateForKgFieldId = $('#initial_lead_quote_rateperkg_' + rowNo);
    var $kgCftFieldId = $('#initial_lead_quote_kgperdft_' + rowNo);
    
    if(serviceid == 8 || serviceid == 9){
    	var $pickup = 0;
        var $delivery = 0;
        var $oda = 0;
        var pickupvalue = 0;
        var deliveryvalue = 0;
        var odachargevalue = 0;
    
    }else{
	    var $pickup = $('#initial_lead_quote_pickup_' + rowNo);
	    var $delivery = $('#initial_lead_quote_delivery_' + rowNo);
	    var $oda = $('#initial_lead_quote_oda_' + rowNo);
	    var pickupvalue = $pickup.val();
	    var deliveryvalue = $delivery.val();
	    var odachargevalue = $oda.val();
	   
    }

    var $incrementcount = $('#incrementcount_' + rowNo);
    var incrementcount = $incrementcount.val();
    
    var counterRateForKgValue = $rateForKgFieldId.val();
    var conversionKgCftValue = $kgCftFieldId.val();
   
    
    var regexrateperkgPattern = /^\d{1,4}(\.\d{1,2})?$/;
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
	        return false;
	    } else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
	        return false;
	    }  else if(counterRateForKgValue == 0){
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexrateperkgPattern.test(counterRateForKgValue) ) {
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be less than 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    } else if(conversionKgCftValue == 0){
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexconversionfactorPattern.test(conversionKgCftValue) ) {
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be less than 10000 with max of 3 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }   else {
	
    
    	
        $.ajax({
            type: "POST",
            url: "/getSellerfreightdetails",
            data: {
                'counterRateForKgValue': counterRateForKgValue,
                'conversionKgCftValue': conversionKgCftValue,
                'pickupvalue': pickupvalue,
                'deliveryvalue': deliveryvalue,
                'odachargevalue': odachargevalue,
                'buyerId': buyerId,
                'buyerquoteId': buyerquoteId,
                'incrementcount': incrementcount,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#final_rate_per_kg_' + rowNo).data('rateperkg', jsonData.freightDetails.counterRatePerKg);
                    $('#pick_up_rate_' + rowNo).data('pickuprateperkg', jsonData.freightDetails.pickUpPrice);
                    $('#delivery_charges_' + rowNo).data('deliverycharges', jsonData.freightDetails.deliveryPrice);
                    $('#oda_charges_' + rowNo).data('odacharges', jsonData.freightDetails.oda);
                    $('#freight_charges_' + rowNo).data('freightcharges', jsonData.freightDetails.totalFreightAmount);
                    $('#total_charges_' + rowNo).data('totalcharges', jsonData.freightDetails.totalAmount);
                    $('#final_rate_per_kg_' + rowNo).text(jsonData.freightDetails.formattedCounterRatePerKg);
                    $('#pick_up_rate_' + rowNo).text(jsonData.freightDetails.formattedPickUpPrice);
                    $('#delivery_charges_' + rowNo).text(jsonData.freightDetails.formattedDeliveryPrice);
                    $('#oda_charges_' + rowNo).text(jsonData.freightDetails.formattedOda);
                    $('#freight_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalFreightAmount);
                    $('#total_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalAmount);
                }
            }
        }, "json");
    }
	
});


$("#addcouriersellerpostquoteoffer .ptl_final_rate_per_kg,#addcouriersellerpostquoteoffer .ptl_final_conversion,#addcouriersellerpostquoteoffer .ptl_final_fuel ,#addcouriersellerpostquoteoffer .ptl_final_cod,#addcouriersellerpostquoteoffer .ptl_final_freight,#addcouriersellerpostquoteoffer .ptl_final_arc").blur(function() {
	
	var serviceid = $('#serviceid').val();
	var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];

	var seller_post_item_id = $('#seller_post_item_id').val();
	var buyerId = $(this).attr('id').split("_").reverse()[1];
	var buyerquoteId = $(this).attr('id').split("_").reverse()[0]
    var $rateForKgFieldId = $('#final_quote_rateperkg_' + rowNo);
    var $kgCftFieldId = $('#final_quote_kgperdft_' + rowNo);
    
	var $pickup = $('#final_fuel_surcharge_rupees_' + rowNo);
	var $delivery = $('#final_cod_rupees_' + rowNo);
	var $oda = $('#final_freight_collect_rupees_' + rowNo);
	var $arc = $('#final_arc_rupees_' + rowNo);
	var pickupvalue = $pickup.val();
	var deliveryvalue = $delivery.val();
    var odachargevalue = $oda.val();
    var arcvalue = $arc.val();
	   
    

    var $incrementcount = $('#incrementcount_' + rowNo);
    var incrementcount = $incrementcount.val();
    
    var counterRateForKgValue = $rateForKgFieldId.val();
    var conversionKgCftValue = $kgCftFieldId.val();
   
    
    var regexrateperkgPattern = /^\d{1,4}(\.\d{1,2})?$/;
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
    
    
	 if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
	        return false;
	    } else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
	        return false;
	    }  else if(counterRateForKgValue == 0){
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexrateperkgPattern.test(counterRateForKgValue) ) {
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be less than 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    } else if(conversionKgCftValue == 0){
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexconversionfactorPattern.test(conversionKgCftValue) ) {
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be less than 10000 with max of 3 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }   else {
    	
        $.ajax({
            type: "POST",
            url: "/getSellerfreightdetails",
            data: {
                'counterRateForKgValue': counterRateForKgValue,
                'conversionKgCftValue': conversionKgCftValue,
                'pickupvalue': pickupvalue,
                'deliveryvalue': deliveryvalue,
                'odachargevalue': odachargevalue,
                'arcvalue': arcvalue,
                'buyerId': buyerId,
                'buyerquoteId': buyerquoteId,
                'seller_post_item_id':seller_post_item_id,
                'incrementcount': incrementcount,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#final_rate_per_kg_' + rowNo).data('rateperkg', jsonData.freightDetails.counterRatePerKg);
                    $('#pick_up_rate_' + rowNo).data('pickuprateperkg', jsonData.freightDetails.pickUpPrice);
                    $('#delivery_charges_' + rowNo).data('deliverycharges', jsonData.freightDetails.deliveryPrice);
                    $('#oda_charges_' + rowNo).data('odacharges', jsonData.freightDetails.oda);
                    $('#freight_charges_' + rowNo).data('freightcharges', jsonData.freightDetails.totalFreightAmount);
                    $('#total_charges_' + rowNo).data('totalcharges', jsonData.freightDetails.totalAmount);
                    $('#final_rate_per_kg_' + rowNo).text(jsonData.freightDetails.formattedCounterRatePerKg);
                    $('#pick_up_rate_' + rowNo).text(jsonData.freightDetails.formattedPickUpPrice);
                    $('#delivery_charges_' + rowNo).text(jsonData.freightDetails.formattedDeliveryPrice);
                    $('#oda_charges_' + rowNo).text(jsonData.freightDetails.formattedOda);
                    $('#freight_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalFreightAmount);
                    $('#total_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalAmount);
                }
            }
        }, "json");
    }
	
});



$("#addptlsellerpostquoteoffer .ptl_final_rate_per_kg,#addptlsellerpostquoteoffer .ptl_final_conversion,#addptlsellerpostquoteoffer .ptl_final_pickup ,#addptlsellerpostquoteoffer .ptl_final_delivery,#addptlsellerpostquoteoffer .ptl_final_oda").blur(function() {
	var serviceid = $('#serviceid').val();
	var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];
	
	var buyerId = $(this).attr('id').split("_").reverse()[1];
	var buyerquoteId = $(this).attr('id').split("_").reverse()[0]
    var $rateForKgFieldId = $('#final_quote_rateperkg_' + rowNo);
    var $kgCftFieldId = $('#final_quote_kgperdft_' + rowNo);
   
    if(serviceid == 8 || serviceid == 9){
    	var $pickup = 0;
	    var $delivery = 0;
	    var $oda = 0;
	    var pickupvalue = 0;
	    var deliveryvalue = 0;
	    var odachargevalue = 0;
    }else{
	    var $pickup = $('#final_quote_pickup_' + rowNo);
	    var $delivery = $('#final_quote_delivery_' + rowNo);
	    var $oda = $('#final_quote_oda_' + rowNo);
	    var pickupvalue = $pickup.val();
	    var deliveryvalue = $delivery.val();
	    var odachargevalue = $oda.val();
    }
    var $incrementcount = $('#incrementcount_' + rowNo);
    
    
    var counterRateForKgValue = $rateForKgFieldId.val();
    var conversionKgCftValue = $kgCftFieldId.val();
    
    var incrementcount = $incrementcount.val();
    
    var regexrateperkgPattern = /^\d{1,4}(\.\d{1,2})?$/;
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	
	if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
        return false;
    } else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
        return false;
    }  else if(counterRateForKgValue == 0){
    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }else if (!regexrateperkgPattern.test(counterRateForKgValue) ) {
    	$("#erroralertmodal .modal-body").html("Rate per Kg should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    } else if(conversionKgCftValue == 0){
    	$("#erroralertmodal .modal-body").html("Conversion Factor should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }else if (!regexconversionfactorPattern.test(conversionKgCftValue) ) {
    	$("#erroralertmodal .modal-body").html("Conversion Factor should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }   else {
    	
        $.ajax({
            type: "POST",
            url: "/getSellerfreightdetails",
            data: {
                'counterRateForKgValue': counterRateForKgValue,
                'conversionKgCftValue': conversionKgCftValue,
                'pickupvalue': pickupvalue,
                'deliveryvalue': deliveryvalue,
                'odachargevalue': odachargevalue,
                'buyerId': buyerId,
                'buyerquoteId': buyerquoteId,
                'incrementcount': incrementcount,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#final_rate_per_kg_' + rowNo).data('rateperkg', jsonData.freightDetails.counterRatePerKg);
                    $('#pick_up_rate_' + rowNo).data('pickuprateperkg', jsonData.freightDetails.pickUpPrice);
                    $('#delivery_charges_' + rowNo).data('deliverycharges', jsonData.freightDetails.deliveryPrice);
                    $('#oda_charges_' + rowNo).data('odacharges', jsonData.freightDetails.oda);
                    $('#freight_charges_' + rowNo).data('freightcharges', jsonData.freightDetails.totalFreightAmount);
                    $('#total_charges_' + rowNo).data('totalcharges', jsonData.freightDetails.totalAmount);
                    $('#final_rate_per_kg_' + rowNo).text(jsonData.freightDetails.formattedCounterRatePerKg);
                    $('#pick_up_rate_' + rowNo).text(jsonData.freightDetails.formattedPickUpPrice);
                    $('#delivery_charges_' + rowNo).text(jsonData.freightDetails.formattedDeliveryPrice);
                    $('#oda_charges_' + rowNo).text(jsonData.freightDetails.formattedOda);
                    $('#freight_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalFreightAmount);
                    $('#total_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalAmount);
                }
            }
        }, "json");
    }
	
});

$("#leadptlsellerpostquoteoffer .ptl_final_rate_per_kg,#leadptlsellerpostquoteoffer .ptl_final_conversion,#leadptlsellerpostquoteoffer .ptl_final_pickup ,#leadptlsellerpostquoteoffer .ptl_final_delivery,#leadptlsellerpostquoteoffer .ptl_final_oda").blur(function() {
	
	var serviceid = $('#serviceid').val();
	var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];
	
	var buyerId = $(this).attr('id').split("_").reverse()[1];
	var buyerquoteId = $(this).attr('id').split("_").reverse()[0]
    var $rateForKgFieldId = $('#final_lead_quote_rateperkg_' + rowNo);
    var $kgCftFieldId = $('#final_lead_quote_kgperdft_' + rowNo);
    
    if(serviceid == 8 || serviceid == 9){
    	var $pickup = 0;
        var $delivery = 0;
        var $oda = 0;
        var pickupvalue = 0;
        var deliveryvalue = 0;
        var odachargevalue = 0;
    
    }else{
	    var $pickup = $('#final_lead_quote_pickup_' + rowNo);
	    var $delivery = $('#final_lead_quote_delivery_' + rowNo);
	    var $oda = $('#final_lead_quote_oda_' + rowNo);
	    var pickupvalue = $pickup.val();
	    var deliveryvalue = $delivery.val();
	    var odachargevalue = $oda.val();
	   
    }

    var $incrementcount = $('#incrementcount_' + rowNo);
    var incrementcount = $incrementcount.val();
    
    var counterRateForKgValue = $rateForKgFieldId.val();
    var conversionKgCftValue = $kgCftFieldId.val();
   
    
    var regexrateperkgPattern = /^\d{1,4}(\.\d{1,2})?$/;
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
	        return false;
	    } else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
	        return false;
	    }  else if(counterRateForKgValue == 0){
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexrateperkgPattern.test(counterRateForKgValue) ) {
	    	$("#erroralertmodal .modal-body").html("Rate per Kg should be less than 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    } else if(conversionKgCftValue == 0){
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be greater than 0");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }else if (!regexconversionfactorPattern.test(conversionKgCftValue) ) {
	    	$("#erroralertmodal .modal-body").html("Conversion Factor should be less than 10000 with max of 3 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        return false;
	    }   else {
	
    	
        $.ajax({
            type: "POST",
            url: "/getSellerfreightdetails",
            data: {
                'counterRateForKgValue': counterRateForKgValue,
                'conversionKgCftValue': conversionKgCftValue,
                'pickupvalue': pickupvalue,
                'deliveryvalue': deliveryvalue,
                'odachargevalue': odachargevalue,
                'buyerId': buyerId,
                'buyerquoteId': buyerquoteId,
                'incrementcount': incrementcount,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#final_rate_per_kg_' + rowNo).data('rateperkg', jsonData.freightDetails.counterRatePerKg);
                    $('#pick_up_rate_' + rowNo).data('pickuprateperkg', jsonData.freightDetails.pickUpPrice);
                    $('#delivery_charges_' + rowNo).data('deliverycharges', jsonData.freightDetails.deliveryPrice);
                    $('#oda_charges_' + rowNo).data('odacharges', jsonData.freightDetails.oda);
                    $('#freight_charges_' + rowNo).data('freightcharges', jsonData.freightDetails.totalFreightAmount);
                    $('#total_charges_' + rowNo).data('totalcharges', jsonData.freightDetails.totalAmount);
                    $('#final_rate_per_kg_' + rowNo).text(jsonData.freightDetails.formattedCounterRatePerKg);
                    $('#pick_up_rate_' + rowNo).text(jsonData.freightDetails.formattedPickUpPrice);
                    $('#delivery_charges_' + rowNo).text(jsonData.freightDetails.formattedDeliveryPrice);
                    $('#oda_charges_' + rowNo).text(jsonData.freightDetails.formattedOda);
                    $('#freight_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalFreightAmount);
                    $('#total_charges_' + rowNo).text(jsonData.freightDetails.formattedTotalAmount);
                }
            }
        }, "json");
    }
	
});

//Enquiries Initial Quote PTL
$("#addptlsellerpostquoteoffer .ptl_initial_quote_submit").click(function(){

	
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();
		
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_initial_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#initial_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#initial_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
    
    if(serviceid == 8 || serviceid == 9){
    	var sellerPickupOfferValue = 0;
  	    var sellerDeliveryOfferValue = 0;
  	    var sellerODAOfferValue = 0;
	    
    }
    else{
    	var $sellerPickupFieldId = $('#initial_quote_pickup_' + rowNo);
	    var sellerPickupOfferValue = $sellerPickupFieldId.val();
	    
	    var $sellerDeliveryOfferFieldId = $('#initial_quote_delivery_' + rowNo);
	    var sellerDeliveryOfferValue = $sellerDeliveryOfferFieldId.val();
	    
	    var $sellerODAOfferFieldId = $('#initial_quote_oda_' + rowNo);
	    var sellerODAOfferValue = $sellerODAOfferFieldId.val();
    }
    	
    var $sellertransitOfferFieldId = $('#initial_quote_transit_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
	
	
	var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (sellerRateKgOfferValue==0) {
		
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Ccm");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cbm");
		else
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cft");
        $("#erroralertmodal").modal({
            show: true
        }); 
		
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (!regexconversionfactorPattern.test(sellerKgCftOfferValue)) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be less than 10000 with max of 3 decimals");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be less than 10000 with max of 3 decimals");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        });  
		
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (sellerKgCftOfferValue==0) {
		
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be greater than 0");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be greater than 0");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });  
		 return false;
	} 
	if(serviceid == 8 || serviceid == 9){
		
	}else{
		if (!($sellerPickupFieldId.val()).trim()) {
			 
			$("#erroralertmodal .modal-body").html("Please enter Pickup Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerPickupFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerPickupOfferValue)) {
			
			$("#erroralertmodal .modal-body").html("Pickup Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerPickupFieldId.focus();
			 return false;
		} 
		if (!($sellerDeliveryOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Delivery Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerDeliveryOfferValue)) {
			$("#erroralertmodal .modal-body").html("Delivery Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} 
		if (!($sellerODAOfferFieldId.val()).trim()) {
			
			$("#erroralertmodal .modal-body").html("Please enter ODA Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerODAOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerODAOfferValue)) {
			$("#erroralertmodal .modal-body").html("ODA Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			
			 $sellerODAOfferFieldId.focus();
			 return false;
		} 
	}
	if (!($sellertransitOfferFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	}else if (sellertransitOfferValue==0) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	}
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.pickupvalue = sellerPickupOfferValue;
	datavaluses.deliveryvalue = sellerDeliveryOfferValue;
	datavaluses.odavalue = sellerODAOfferValue;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount = increment;
	datavaluses.cbuyerquoteid = buyerquoteid;
	if(serviceid == 8 || serviceid == 9){
		var $con = $sellerRateKgFieldId.val().trim();
	}else{
		$con = (($sellerPickupFieldId.val()).trim() && ($sellerDeliveryOfferFieldId.val()).trim() && ($sellerODAOfferFieldId.val()).trim());
	}
	if($con){
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
            
			data : datavaluses,
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


//Courier Initial
$("#addcouriersellerpostquoteoffer .ptl_initial_quote_submit").click(function(){

	
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();
		
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_initial_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var packagevalue = $('#packagevalue_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    var daysselect = $('#dayspicker_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#initial_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#initial_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
    
   
	var $sellerFuelFieldId = $('#initial_fuel_surcharge_rupees_' + rowNo);
    var sellerFuelOfferValue = $sellerFuelFieldId.val();
    
    var $sellerCODrFieldId = $('#initial_cod_rupees_' + rowNo);
    var sellerCODValue = $sellerCODrFieldId.val();
    
    var $sellerFreightFieldId = $('#initial_freight_collect_rupees_' + rowNo);
	var sellerFreightValue = $sellerFreightFieldId.val();
	
	var $sellerArcFieldId = $('#initial_arc_rupees_' + rowNo);
	var sellerArcValue = $sellerArcFieldId.val();
	
    	
    var $sellertransitOfferFieldId = $('#initial_transit_days_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
	
	
	var regexPattern= /^\d{1,5}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (sellerRateKgOfferValue == 0){
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	}else if (!regexPattern.test(sellerRateKgOfferValue)) {
		
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Conversion Factor");
        $("#erroralertmodal").modal({
            show: true
        }); 
		
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (sellerKgCftOfferValue == 0){
		$("#erroralertmodal .modal-body").html("Conversion Factor Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });  
		
		 $sellerKgCftFieldId.focus();
		 return false;
		
	}else if (!regexconversionfactorPattern.test(sellerKgCftOfferValue)) {
		$("#erroralertmodal .modal-body").html("Conversion Factor Field should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        });  
		
		 $sellerKgCftFieldId.focus();
		 return false;
	} 
	
		if (!($sellerFuelFieldId.val()).trim()) {
			 
			$("#erroralertmodal .modal-body").html("Please enter Fuel Surcharge");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        $sellerFuelFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerFuelOfferValue)) {
			
			$("#erroralertmodal .modal-body").html("Fuel Surcharges Field should be a number");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        $sellerFuelFieldId.focus();
			 return false;
		} 
		if (!($sellerCODrFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter COD");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        $sellerCODrFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerCODValue)) {
			$("#erroralertmodal .modal-body").html("COD Field should be a number");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        $sellerCODrFieldId.focus();
			 return false;
		} 
		if (!($sellerFreightFieldId.val()).trim()) {
			
			$("#erroralertmodal .modal-body").html("Please enter Freight Collect");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerFreightFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerFreightValue)) {
			$("#erroralertmodal .modal-body").html("Freight Collect Field should be a number");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			
			 $sellerFreightFieldId.focus();
			 return false;
		} 
		if (!($sellerArcFieldId.val()).trim()) {
			
			$("#erroralertmodal .modal-body").html("Please enter ARC");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        $sellerArcFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerArcValue)) {
			$("#erroralertmodal .modal-body").html("ARC Field should be a number");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			
	        $sellerArcFieldId.focus();
			 return false;
		} 
	
	if (!($sellertransitOfferFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	}
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.fuelvalue = sellerFuelOfferValue;
	datavaluses.codvalue = sellerCODValue;
	datavaluses.freightvalue = sellerFreightValue;
	datavaluses.arcvalue = sellerArcValue;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount = increment;
	datavaluses.daysselect = daysselect;
	datavaluses.cbuyerquoteid = buyerquoteid;
	
	var $con = (($sellerFuelFieldId.val()).trim() && ($sellerCODrFieldId.val()).trim() && ($sellerFreightFieldId.val()).trim() && ($sellerArcFieldId.val()).trim());
	
	if($con){
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
            
			data : datavaluses,
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

//Courier Initial
$("#addcouriersellerpostquoteoffer .ptl_final_quote_submit").click(function(){

	
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();
		
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_final_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var packagevalue = $('#packagevalue_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    var daysselect = $('#dayspicker_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#final_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#final_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
    
   
	var $sellerFuelFieldId = $('#final_fuel_surcharge_rupees_' + rowNo);
    var sellerFuelOfferValue = $sellerFuelFieldId.val();
    
    var $sellerCODrFieldId = $('#final_cod_rupees_' + rowNo);
    var sellerCODValue = $sellerCODrFieldId.val();
    
    var $sellerFreightFieldId = $('#final_freight_collect_rupees_' + rowNo);
	var sellerFreightValue = $sellerFreightFieldId.val();
	
	var $sellerArcFieldId = $('#final_arc_rupees_' + rowNo);
	var sellerArcValue = $sellerArcFieldId.val();
	
    	
    var $sellertransitOfferFieldId = $('#final_transit_days_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
	
    var regexPattern= /^\d{1,5}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	}else if(sellerRateKgOfferValue == 0){
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Conversion Factor");
        $("#erroralertmodal").modal({
            show: true
        }); 
		
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if(sellerKgCftOfferValue == 0){
		$("#erroralertmodal .modal-body").html("Conversion Factor Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });  
		
		 $sellerKgCftFieldId.focus();
		 return false;
	}else if (!regexconversionfactorPattern.test(sellerKgCftOfferValue)) {
		$("#erroralertmodal .modal-body").html("Conversion Factor Field should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        });  
		
		 $sellerKgCftFieldId.focus();
		 return false;
	} 
	
		if (!($sellerFuelFieldId.val()).trim()) {
			 
			$("#erroralertmodal .modal-body").html("Please enter Fuel Surcharge");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        $sellerFuelFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerFuelOfferValue)) {
			
			$("#erroralertmodal .modal-body").html("Fuel Surcharges Field should be a number");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        $sellerFuelFieldId.focus();
			 return false;
		} 
		if (!($sellerCODrFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter COD");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        $sellerCODrFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerCODValue)) {
			$("#erroralertmodal .modal-body").html("COD Field should be a number");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        $sellerCODrFieldId.focus();
			 return false;
		} 
		if (!($sellerFreightFieldId.val()).trim()) {
			
			$("#erroralertmodal .modal-body").html("Please enter Freight Collect");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerFreightFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerFreightValue)) {
			$("#erroralertmodal .modal-body").html("Freight Collect Field should be a number");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			
			 $sellerFreightFieldId.focus();
			 return false;
		} 
		if (!($sellerArcFieldId.val()).trim()) {
			
			$("#erroralertmodal .modal-body").html("Please enter ARC");
	        $("#erroralertmodal").modal({
	            show: true
	        });
	        $sellerArcFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerArcValue)) {
			$("#erroralertmodal .modal-body").html("ARC Field should be a number");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			
	        $sellerArcFieldId.focus();
			 return false;
		} 
	
	if (!($sellertransitOfferFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	}
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.fuelvalue = sellerFuelOfferValue;
	datavaluses.codvalue = sellerCODValue;
	datavaluses.freightvalue = sellerFreightValue;
	datavaluses.arcvalue = sellerArcValue;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount = increment;
	datavaluses.daysselect = daysselect;
	datavaluses.cbuyerquoteid = buyerquoteid;
	
	var $con = (($sellerFuelFieldId.val()).trim() && ($sellerCODrFieldId.val()).trim() && ($sellerFreightFieldId.val()).trim() && ($sellerArcFieldId.val()).trim());
	
	if($con){
		 $.ajax({
            type: "POST",
            url : "/sellerfinalquotesubmit",
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
            
			data : datavaluses,
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


//Enquires counter
$("#addcouriersellerpostquoteoffer .ptl_counter_quote_submit").click(function(){
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();
		
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_counter_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var packagevalue = $('#packagevalue_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    var daysselect = $('#dayspicker_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#counter_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    var $sellerKgCftFieldId = $('#counter_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
	
    var $sellerFuelFieldId = $('#initial_fuel_surcharge_rupees_' + rowNo);
    var sellerFuelOfferValue = $sellerFuelFieldId.val();
    
    var $sellerCODrFieldId = $('#initial_cod_rupees_' + rowNo);
    var sellerCODValue = $sellerCODrFieldId.val();
    
    var $sellerFreightFieldId = $('#initial_freight_collect_rupees_' + rowNo);
	var sellerFreightValue = $sellerFreightFieldId.val();
	
	var $sellerArcFieldId = $('#initial_arc_rupees_' + rowNo);
	var sellerArcValue = $sellerArcFieldId.val();
	
    	
    var $sellertransitOfferFieldId = $('#initial_transit_days_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
    
	var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
	var regexNumericPattern= /[1-9]\d*/;
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        }); 
		
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Ccm");
		else if(ser$sellerArcFieldIdviceid == 9)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cbm");
		else
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cft");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerKgCftOfferValue)) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be a number");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be a number");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	}
	

		
	
	
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.fuelvalue = sellerFuelOfferValue;
	datavaluses.codvalue = sellerCODValue;
	datavaluses.freightvalue = sellerFreightValue;
	datavaluses.arcvalue = sellerArcValue;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount = increment;
	datavaluses.daysselect = daysselect;
	datavaluses.cbuyerquoteid = buyerquoteid;
	
		$con = (($sellerFuelFieldId.val()).trim() && ($sellerCODrFieldId.val()).trim() && ($sellerFreightFieldId.val()).trim());
	
	if($con){
		 $.ajax({
            type: "POST",
            url : "/selleraccept",
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
			data : datavaluses,
		   success : function(jsonData){
			   $("#erroralertmodal .modal-body").html('Counter Offer from Buyer accepted.');
               $("#erroralertmodal").modal({
                   show: true
               }).one('click','.ok-btn',function (e){
                   location.reload();
               });
			   
		   }
       },"json");
	}
});




//Lead Initial Quote PTL
$("#leadptlsellerpostquoteoffer .ptl_lead_initial_quote_submit").click(function(){

	
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();
		
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_lead_initial_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#initial_lead_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#initial_lead_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
    
    if(serviceid == 8 || serviceid == 9){
    	var sellerPickupOfferValue = 0;
  	    var sellerDeliveryOfferValue = 0;
  	    var sellerODAOfferValue = 0;
	    
    }
    else{
    	var $sellerPickupFieldId = $('#initial_lead_quote_pickup_' + rowNo);
	    var sellerPickupOfferValue = $sellerPickupFieldId.val();
	    
	    var $sellerDeliveryOfferFieldId = $('#initial_lead_quote_delivery_' + rowNo);
	    var sellerDeliveryOfferValue = $sellerDeliveryOfferFieldId.val();
	    
	    var $sellerODAOfferFieldId = $('#initial_lead_quote_oda_' + rowNo);
	    var sellerODAOfferValue = $sellerODAOfferFieldId.val();
    }
    	
    var $sellertransitOfferFieldId = $('#initial_lead_quote_transit_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
    var regexPattern= /^\d{1,5}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	}else if(sellerRateKgOfferValue == 0){
    	$("#erroralertmodal .modal-body").html("Rate per Kg should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    } 
	if (!($sellerKgCftFieldId.val()).trim()) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Ccm");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cbm");
		else
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cft");
        $("#erroralertmodal").modal({
            show: true
        }); 
		
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (!regexconversionfactorPattern.test(sellerKgCftOfferValue)) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be less than 10000 with max of 3 decimals");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be less than 10000 with max of 3 decimals");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        });  
		
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if(sellerKgCftOfferValue == 0){
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be greater than 0");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be greater than 0");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });  
        return false;
    } 
	if(serviceid == 8 || serviceid == 9){
		
	}else{
		if (!($sellerPickupFieldId.val()).trim()) {
			 
			$("#erroralertmodal .modal-body").html("Please enter Pickup Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerPickupFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerPickupOfferValue)) {
			
			$("#erroralertmodal .modal-body").html("Pickup Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerPickupFieldId.focus();
			 return false;
		}
		if (!($sellerDeliveryOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Delivery Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerDeliveryOfferValue)) {
			$("#erroralertmodal .modal-body").html("Delivery Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} 
		if (!($sellerODAOfferFieldId.val()).trim()) {
			
			$("#erroralertmodal .modal-body").html("Please enter ODA Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerODAOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerODAOfferValue)) {
			$("#erroralertmodal .modal-body").html("ODA Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			
			 $sellerODAOfferFieldId.focus();
			 return false;
		} 
	}
	if (!($sellertransitOfferFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if(sellertransitOfferValue == 0){
    	$("#erroralertmodal .modal-body").html("Transit Days should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    } 
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.pickupvalue = sellerPickupOfferValue;
	datavaluses.deliveryvalue = sellerDeliveryOfferValue;
	datavaluses.odavalue = sellerODAOfferValue;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount = increment;
	datavaluses.cbuyerquoteid = buyerquoteid;
	if(serviceid == 8 || serviceid == 9){
		var $con = $sellerRateKgFieldId.val().trim();
	}else{
		$con = (($sellerPickupFieldId.val()).trim() && ($sellerDeliveryOfferFieldId.val()).trim() && ($sellerODAOfferFieldId.val()).trim());
	}
	if($con){
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
            
			data : datavaluses,
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


//Courier Initial
$("#leadptlsellercounterquoteoffer .ptl_lead_initial_quote_submit").click(function(){

	
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();
		
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_lead_initial_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    var daysselect = $('#dayspicker_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#initial_lead_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#initial_lead_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
    
  
	var $sellerFuelFieldId = $('#initial_fuel_surcharge_rupees_' + rowNo);
    var sellerFuelOfferValue = $sellerFuelFieldId.val();
    
    var $sellerCODOfferFieldId = $('#initial_cod_rupees_' + rowNo);
    var sellerCODOfferValue = $sellerCODOfferFieldId.val();
    
    var $sellerfreightOfferFieldId = $('#initial_freight_collect_rupees_' + rowNo);
    var sellerFrieghtOfferValue = $sellerfreightOfferFieldId.val();
    
    var $sellerArcOfferFieldId = $('#initial_arc_rupees_' + rowNo);
    var sellerArcOfferValue = $sellerArcOfferFieldId.val();
    	
    var $sellertransitOfferFieldId = $('#initial_transit_days_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
	
	var regexPattern= /^\d{1,5}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (sellerRateKgOfferValue == 0){
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	}else if (!regexPattern.test(sellerRateKgOfferValue)) {
		
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Conversion Factor");
        $("#erroralertmodal").modal({
            show: true
        }); 
		
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (sellerKgCftOfferValue == 0){
		$("#erroralertmodal .modal-body").html("Conversion Factor Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });  
		
		 $sellerKgCftFieldId.focus();
		 return false;
	}else if (!regexconversionfactorPattern.test(sellerKgCftOfferValue)) {
		$("#erroralertmodal .modal-body").html("Conversion Factor Field should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        });  
		
		 $sellerKgCftFieldId.focus();
		 return false;
	} 
	
	if (!($sellerFuelFieldId.val()).trim()) {
		 
		$("#erroralertmodal .modal-body").html("Please enter Fuel Surcharges");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerFuelFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerFuelOfferValue)) {
		
		$("#erroralertmodal .modal-body").html("Fuel Surcharges Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerFuelFieldId.focus();
		 return false;
	} 
	if (!($sellerCODOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter COD");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerCODOfferFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerCODOfferValue)) {
		$("#erroralertmodal .modal-body").html("COD Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerCODOfferFieldId.focus();
		 return false;
	} 
	if (!($sellerfreightOfferFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter Freight Rupees");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerfreightOfferFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerFrieghtOfferValue)) {
		$("#erroralertmodal .modal-body").html("Freight	 Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		
		 $sellerfreightOfferFieldId.focus();
		 return false;
	} 
	
	if (!($sellerArcOfferFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter ARC");
        $("#erroralertmodal").modal({
            show: true
        });
        $sellerArcOfferFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerArcOfferValue)) {
		$("#erroralertmodal .modal-body").html("ARc Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		
        $sellerArcOfferFieldId.focus();
		 return false;
	} 
	
	if (!($sellertransitOfferFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	}
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.fuelvalue = sellerFuelOfferValue;
	datavaluses.codvalue = sellerCODOfferValue;
	datavaluses.freightvalue = sellerFrieghtOfferValue;
	datavaluses.arcvalue = sellerArcOfferValue;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount = increment;
	datavaluses.daysselect = daysselect;
	datavaluses.cbuyerquoteid = buyerquoteid;
	
	$con = (($sellerFuelFieldId.val()).trim() && ($sellerCODOfferFieldId.val()).trim() && ($sellerfreightOfferFieldId.val()).trim());
	
	if($con){
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
            
			data : datavaluses,
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

$("#leadptlsellercounterquoteoffer .ptl_lead_final_quote_submit").click(function(){

	
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();
		
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_lead_final_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    var daysselect = $('#dayspicker_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#final_lead_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#final_lead_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
    
  
	var $sellerFuelFieldId = $('#final_fuel_surcharge_rupees_' + rowNo);
    var sellerFuelOfferValue = $sellerFuelFieldId.val();
    
    var $sellerCODOfferFieldId = $('#final_cod_rupees_' + rowNo);
    var sellerCODOfferValue = $sellerCODOfferFieldId.val();
    
    var $sellerfreightOfferFieldId = $('#final_freight_collect_rupees_' + rowNo);
    var sellerFrieghtOfferValue = $sellerfreightOfferFieldId.val();
    
    var $sellerArcOfferFieldId = $('#final_arc_rupees_' + rowNo);
    var sellerArcOfferValue = $sellerArcOfferFieldId.val();
    	
    var $sellertransitOfferFieldId = $('#final_transit_days_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    

	var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (sellerRateKgOfferValue == 0){
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	}else if (!regexPattern.test(sellerRateKgOfferValue)) {
		
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Conversion Factor");
        $("#erroralertmodal").modal({
            show: true
        }); 
		
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (sellerKgCftOfferValue == 0){
		$("#erroralertmodal .modal-body").html("Conversion Factor Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });  
		
		 $sellerKgCftFieldId.focus();
		 return false;
	}else if (!regexconversionfactorPattern.test(sellerKgCftOfferValue)) {
		$("#erroralertmodal .modal-body").html("Conversion Factor Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });  
		
		 $sellerKgCftFieldId.focus();
		 return false;
	} 
	
	if (!($sellerFuelFieldId.val()).trim()) {
		 
		$("#erroralertmodal .modal-body").html("Please enter Fuel Surcharges");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerFuelFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerFuelOfferValue)) {
		
		$("#erroralertmodal .modal-body").html("Fuel Surcharges Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerFuelFieldId.focus();
		 return false;
	} 
	if (!($sellerCODOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter COD");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerCODOfferFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerCODOfferValue)) {
		$("#erroralertmodal .modal-body").html("COD Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerCODOfferFieldId.focus();
		 return false;
	} 
	if (!($sellerfreightOfferFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter Freight Rupees");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerfreightOfferFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerFrieghtOfferValue)) {
		$("#erroralertmodal .modal-body").html("Freight	 Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		
		 $sellerfreightOfferFieldId.focus();
		 return false;
	} 
	
	if (!($sellerArcOfferFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter ARC");
        $("#erroralertmodal").modal({
            show: true
        });
        $sellerArcOfferFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerArcOfferValue)) {
		$("#erroralertmodal .modal-body").html("ARc Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		
        $sellerArcOfferFieldId.focus();
		 return false;
	} 
	
	if (!($sellertransitOfferFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	}
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.fuelvalue = sellerFuelOfferValue;
	datavaluses.codvalue = sellerCODOfferValue;
	datavaluses.freightvalue = sellerFrieghtOfferValue;
	datavaluses.arcvalue = sellerArcOfferValue;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount = increment;
	datavaluses.daysselect = daysselect;
	datavaluses.cbuyerquoteid = buyerquoteid;
	
	$con = (($sellerFuelFieldId.val()).trim() && ($sellerCODOfferFieldId.val()).trim() && ($sellerfreightOfferFieldId.val()).trim());
	
	if($con){
		 $.ajax({
            type: "POST",
            url : "/sellerfinalquotesubmit",
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
            
			data : datavaluses,
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


//Enquires counter
$("#leadptlsellercounterquoteoffer .ptl_lead_counter_quote_submit").click(function(){
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();
		
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_lead_counter_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var packagevalue = $('#packagevalue_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    var daysselect = $('#dayspicker_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#counter_lead_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    var $sellerKgCftFieldId = $('#counter_lead_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
	
    var $sellerFuelFieldId = $('#initial_fuel_surcharge_rupees_' + rowNo);
    var sellerFuelOfferValue = $sellerFuelFieldId.val();
    
    var $sellerCODrFieldId = $('#initial_cod_rupees_' + rowNo);
    var sellerCODValue = $sellerCODrFieldId.val();
    
    var $sellerFreightFieldId = $('#initial_freight_collect_rupees_' + rowNo);
	var sellerFreightValue = $sellerFreightFieldId.val();
	
	var $sellerArcFieldId = $('#initial_arc_rupees_' + rowNo);
	var sellerArcValue = $sellerArcFieldId.val();
	
    	
    var $sellertransitOfferFieldId = $('#initial_transit_days_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
    
	var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
	var regexNumericPattern= /[1-9]\d*/;
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        }); 
		
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Conversion Factor");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerKgCftOfferValue)) {
		$("#erroralertmodal .modal-body").html("Conversion Factor Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	}
	

		
	
	
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.fuelvalue = sellerFuelOfferValue;
	datavaluses.codvalue = sellerCODValue;
	datavaluses.freightvalue = sellerFreightValue;
	datavaluses.arcvalue = sellerArcValue;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount = increment;
	datavaluses.daysselect = daysselect;
	datavaluses.cbuyerquoteid = buyerquoteid;
	
		$con = (($sellerFuelFieldId.val()).trim() && ($sellerCODrFieldId.val()).trim() && ($sellerFreightFieldId.val()).trim());
	
	if($con){
		 $.ajax({
            type: "POST",
            url : "/selleraccept",
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
			data : datavaluses,
		   success : function(jsonData){
			   $("#erroralertmodal .modal-body").html('Counter Offer from Buyer accepted.');
               $("#erroralertmodal").modal({
                   show: true
               }).one('click','.ok-btn',function (e){
                   location.reload();
               });
			   
		   }
       },"json");
	}
});

//Enquires counter
$("#addptlsellerpostquoteoffer .ptl_counter_quote_submit").click(function(){
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();
	//alert(formvalues);
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_counter_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val(); 
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#counter_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#counter_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
	
    
    if(serviceid == 8 || serviceid == 9){
    	var sellerPickupOfferValue = 0;
  	    var sellerDeliveryOfferValue = 0;
  	    var sellerODAOfferValue = 0;
	    
    }else{
	    var $sellerPickupFieldId = $('#initial_quote_pickup_' + rowNo);
	    var sellerPickupOfferValue = $sellerPickupFieldId.val();
	    
	    var $sellerDeliveryOfferFieldId = $('#initial_quote_delivery_' + rowNo);
	    var sellerDeliveryOfferValue = $sellerDeliveryOfferFieldId.val();
	    
	    var $sellerODAOfferFieldId = $('#initial_quote_oda_' + rowNo);
	    var sellerODAOfferValue = $sellerODAOfferFieldId.val();
	}
    
    var $sellertransitOfferFieldId = $('#initial_quote_transit_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
	var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
	var regexNumericPattern= /[1-9]\d*/;
	
	/*if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        }); 
		
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Ccm");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cm");
		else
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cft");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerKgCftOfferValue)) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be a number");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cm Field should be a number");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	}
	if(serviceid == 8 || serviceid == 9){
		
	}else{
		if (!($sellerPickupFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Pickup Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerPickupFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerPickupOfferValue)) {
			$("#erroralertmodal .modal-body").html("Pickup Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerPickupFieldId.focus();
			 return false;
		} 
		if (!($sellerDeliveryOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Delivery Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} else 	if (!regexPattern.test(sellerDeliveryOfferValue)) {
			$("#erroralertmodal .modal-body").html("Delivery Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} 
		if (!($sellerODAOfferFieldId.val()).trim()) {
			
			$("#erroralertmodal .modal-body").html("Please enter ODA Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			 $sellerODAOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerODAOfferValue)) {
			$("#erroralertmodal .modal-body").html("ODA Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			
			 $sellerODAOfferFieldId.focus();
			 return false;
		}
	}
	if (!($sellertransitOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} */
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.pickupvalue = sellerPickupOfferValue;
	datavaluses.deliveryvalue = sellerDeliveryOfferValue;
	datavaluses.odavalue = sellerODAOfferValue;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount = increment;
	datavaluses.cbuyerquoteid = buyerquoteid;
	if(serviceid == 8 || serviceid == 9){
		var $con = $sellerRateKgFieldId.val().trim();
	}else{
		$con = (($sellerPickupFieldId.val()).trim() && ($sellerDeliveryOfferFieldId.val()).trim() && ($sellerODAOfferFieldId.val()).trim());
	}
	if($con){
		 $.ajax({
            type: "POST",
            url : "/selleraccept",
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
			data : datavaluses,
		   success : function(jsonData){
			   $("#erroralertmodal .modal-body").html('Counter Offer from Buyer accepted.');
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
$("#leadptlsellerpostquoteoffer .ptl_lead_counter_quote_submit").click(function(){
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();
	//alert(formvalues);
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_lead_counter_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val(); 
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#counter_lead_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#counter_lead_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
	
    
    if(serviceid == 8 || serviceid == 9){
    	var sellerPickupOfferValue = 0;
  	    var sellerDeliveryOfferValue = 0;
  	    var sellerODAOfferValue = 0;
	    
    }else{
	    var $sellerPickupFieldId = $('#initial_lead_quote_pickup_' + rowNo);
	    var sellerPickupOfferValue = $sellerPickupFieldId.val();
	    
	    var $sellerDeliveryOfferFieldId = $('#initial_lead_quote_delivery_' + rowNo);
	    var sellerDeliveryOfferValue = $sellerDeliveryOfferFieldId.val();
	    
	    var $sellerODAOfferFieldId = $('#initial_lead_quote_oda_' + rowNo);
	    var sellerODAOfferValue = $sellerODAOfferFieldId.val();
	}
    
    var $sellertransitOfferFieldId = $('#initial_lead_quote_transit_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
	var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
	var regexNumericPattern= /[1-9]\d*/;
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        }); 
		
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Ccm");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cbm");
		else
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cft");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerKgCftOfferValue)) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be a number");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be a number");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	}
	if(serviceid == 8 || serviceid == 9){
		
	}else{
		if (!($sellerPickupFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Pickup Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerPickupFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerPickupOfferValue)) {
			$("#erroralertmodal .modal-body").html("Pickup Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerPickupFieldId.focus();
			 return false;
		} 
		if (!($sellerDeliveryOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Delivery Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} else 	if (!regexPattern.test(sellerDeliveryOfferValue)) {
			$("#erroralertmodal .modal-body").html("Delivery Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} 
		if (!($sellerODAOfferFieldId.val()).trim()) {
			
			$("#erroralertmodal .modal-body").html("Please enter ODA Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			 $sellerODAOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerODAOfferValue)) {
			$("#erroralertmodal .modal-body").html("ODA Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			
			 $sellerODAOfferFieldId.focus();
			 return false;
		}
	}
	if (!($sellertransitOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} 
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.pickupvalue = sellerPickupOfferValue;
	datavaluses.deliveryvalue = sellerDeliveryOfferValue;
	datavaluses.odavalue = sellerODAOfferValue;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount = increment;
	datavaluses.cbuyerquoteid = buyerquoteid;
	if(serviceid == 8 || serviceid == 9){
		var $con = $sellerRateKgFieldId.val().trim();
	}else{
		$con = (($sellerPickupFieldId.val()).trim() && ($sellerDeliveryOfferFieldId.val()).trim() && ($sellerODAOfferFieldId.val()).trim());
	}
	if($con){
		 $.ajax({
            type: "POST",
            url : "/selleraccept",
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
			data : datavaluses,
		   success : function(jsonData){
			   $("#erroralertmodal .modal-body").html('Counter Offer from Buyer accepted.');
               $("#erroralertmodal").modal({
                   show: true
               }).one('click','.ok-btn',function (e){
                   location.reload();
               });
			   
		   }
       },"json");
	}
});



//Enquiries Final Quote PTL
$("#addptlsellerpostquoteoffer .ptl_final_quote_submit").click(function(){
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val()
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();
	//alert(formvalues);
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_final_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val(); 
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#final_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#final_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
    
    if(serviceid == 8 || serviceid == 9){
    	var sellerPickupOfferValue = 0;
  	    var sellerDeliveryOfferValue = 0;
  	    var sellerODAOfferValue = 0;
	    
    }else{
    
	    var $sellerPickupFieldId = $('#final_quote_pickup_' + rowNo);
	    var sellerPickupOfferValue = $sellerPickupFieldId.val();
	    
	    var $sellerDeliveryOfferFieldId = $('#final_quote_delivery_' + rowNo);
	    var sellerDeliveryOfferValue = $sellerDeliveryOfferFieldId.val();
	    
	    var $sellerODAOfferFieldId = $('#final_quote_oda_' + rowNo);
	    var sellerODAOfferValue = $sellerODAOfferFieldId.val();
	
	}
    
    var $sellertransitOfferFieldId = $('#final_quote_transit_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	}  else if (sellerRateKgOfferValue==0) {
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Ccm");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cbm");
		else
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cft");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (!regexconversionfactorPattern.test(sellerKgCftOfferValue)) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be less than 10000 with max of 3 decimals");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be less than 10000 with max of 3 decimals");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (sellerKgCftOfferValue==0) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be greater than 0");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be greater than 0");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerKgCftFieldId.focus();
		 return false;
	} 
	if(serviceid == 8 || serviceid == 9){
		
	}else{
		if (!($sellerPickupFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Pickup Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			 $sellerPickupFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerPickupOfferValue)) {
			
			$("#erroralertmodal .modal-body").html("Pickup Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			 $sellerPickupFieldId.focus();
			 return false;
		} 
		if (!($sellerDeliveryOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Delivery Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} else 	if (!regexPattern.test(sellerDeliveryOfferValue)) {
			$("#erroralertmodal .modal-body").html("Delivery Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });   
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} 
		if (!($sellerODAOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter ODA Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerODAOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerODAOfferValue)) {
			$("#erroralertmodal .modal-body").html("ODA Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerODAOfferFieldId.focus();
			 return false;
		} 
	}
	if (!($sellertransitOfferFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		
		$("#erroralertmodal .modal-body").html("Transit Days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (sellertransitOfferValue==0) {
		
		$("#erroralertmodal .modal-body").html("Transit Days Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} 
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.pickupvalue = sellerPickupOfferValue;
	datavaluses.deliveryvalue = sellerDeliveryOfferValue;
	datavaluses.odavalue = sellerODAOfferValue;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount = increment;
	datavaluses.cbuyerquoteid = buyerquoteid;
	if(serviceid == 8 || serviceid == 9){
		var $con = $sellerRateKgFieldId.val().trim();
	}else{
		$con = (($sellerPickupFieldId.val()).trim() && ($sellerDeliveryOfferFieldId.val()).trim() && ($sellerODAOfferFieldId.val()).trim());
	}
	if($con){
		 $.ajax({
            type: "POST",
            url : "/sellerfinalquotesubmit",
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
			data : datavaluses,
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

//Leads Final Quote PTL
$("#leadptlsellerpostquoteoffer .ptl_lead_final_quote_submit").click(function(){
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val()
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();
	//alert(formvalues);
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_lead_final_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val(); 
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#final_lead_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#final_lead_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
    
    if(serviceid == 8 || serviceid == 9){
    	var sellerPickupOfferValue = 0;
  	    var sellerDeliveryOfferValue = 0;
  	    var sellerODAOfferValue = 0;
	    
    }else{
    
	    var $sellerPickupFieldId = $('#final_lead_quote_pickup_' + rowNo);
	    var sellerPickupOfferValue = $sellerPickupFieldId.val();
	    
	    var $sellerDeliveryOfferFieldId = $('#final_lead_quote_delivery_' + rowNo);
	    var sellerDeliveryOfferValue = $sellerDeliveryOfferFieldId.val();
	    
	    var $sellerODAOfferFieldId = $('#final_lead_quote_oda_' + rowNo);
	    var sellerODAOfferValue = $sellerODAOfferFieldId.val();
	
	}
    
    var $sellertransitOfferFieldId = $('#final_lead_quote_transit_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	}  else if(sellerRateKgOfferValue == 0){
    	$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }
	if (!($sellerKgCftFieldId.val()).trim()) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Ccm");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cm");
		else
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cft");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (!regexconversionfactorPattern.test(sellerKgCftOfferValue)) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be less than 10000 with max of 3 decimals");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be less than 10000 with max of 3 decimals");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if(sellerKgCftOfferValue == 0){
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be greater than 0");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be greater than 0");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
        return false;
    }
	if(serviceid == 8 || serviceid == 9){
		
	}else{
		if (!($sellerPickupFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Pickup Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			 $sellerPickupFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerPickupOfferValue)) {
			
			$("#erroralertmodal .modal-body").html("Pickup Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			 $sellerPickupFieldId.focus();
			 return false;
		} 
		if (!($sellerDeliveryOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Delivery Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });  
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} else 	if (!regexPattern.test(sellerDeliveryOfferValue)) {
			$("#erroralertmodal .modal-body").html("Delivery Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });   
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} 
		if (!($sellerODAOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter ODA Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerODAOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerODAOfferValue)) {
			$("#erroralertmodal .modal-body").html("ODA Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerODAOfferFieldId.focus();
			 return false;
		} 
	}
	if (!($sellertransitOfferFieldId.val()).trim()) {
		
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		
		$("#erroralertmodal .modal-body").html("Transit Days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if(sellertransitOfferValue == 0){
    	$("#erroralertmodal .modal-body").html("Transit Days should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    } 
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.pickupvalue = sellerPickupOfferValue;
	datavaluses.deliveryvalue = sellerDeliveryOfferValue;
	datavaluses.odavalue = sellerODAOfferValue;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount = increment;
	datavaluses.cbuyerquoteid = buyerquoteid;
	if(serviceid == 8 || serviceid == 9){
		var $con = $sellerRateKgFieldId.val().trim();
	}else{
		$con = (($sellerPickupFieldId.val()).trim() && ($sellerDeliveryOfferFieldId.val()).trim() && ($sellerODAOfferFieldId.val()).trim());
	}
	if($con){
		 $.ajax({
            type: "POST",
            url : "/sellerfinalquotesubmit",
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
			data : datavaluses,
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
$("#addsellersearchpostquoteoffer .initial_quote_submit").click(function(){

    var buttonId = $(this).attr('id');
    var removeString = 'initail_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    var $sellerQuoteOfferFieldId = $('#initial_quote_' + rowNo);
    var sellerOfferValue = $sellerQuoteOfferFieldId.val();

    var $sellerTransitFieldId = $('#initial_transit_days_' + rowNo);
    var sellertransitValue = $sellerTransitFieldId.val();
    
    var fromcity = $('#from_city').val();
    var tocity = $('#to_city').val();
    var fromdate = $('#from_date').val();
    var todate = $('#to_date').val();
    var tracking = $('#tracking_' + rowNo).val();
    var paymentoptions = $('.payment_options_' + rowNo).val();
    var credit_peroid = $('.credit_period_ptl_' + rowNo).val();
    var credit_period_units = $('.credit_period_units_' + rowNo).val();

	
	var regexPattern= /^\d{1,5}(\.\d{1,2})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	if (!($sellerQuoteOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Initial Quote");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerQuoteOfferFieldId.focus();
		 return false;
	} else if (sellerOfferValue>200000) {
		$("#erroralertmodal .modal-body").html("Initial Quote should be less than or equal to 2 Lakhs");
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
	}  else if (!($sellerTransitFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
		 return false;
	}else if(sellertransitValue<=0){
		$("#erroralertmodal .modal-body").html("Transit Days Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });  
        $sellerTransitFieldId.focus();
		 return false;
	
	} else if (!regexNumericPattern.test(sellertransitValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        });  
        $sellerTransitFieldId.focus();
		 return false;
	} 
	if (!(tracking).trim()) {
		$("#erroralertmodal .modal-body").html("Please Select Tracking");
        $("#erroralertmodal").modal({
            show: true
        });   
        tracking.focus();
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
	
	if((($sellerQuoteOfferFieldId.val()).trim() && ($sellerTransitFieldId.val()).trim())){
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
            data : { 'initial_quote': sellerOfferValue,'initial_transit': sellertransitValue, 'buyer_buyerquote_id' : rowNo,
            		 'from_city_loc':fromcity,'to_city_loc':tocity,
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
$("#addsellersearchpostquoteoffer .accept_quote_submit").click(function(){

    var buttonId = $(this).attr('id');
    var removeString = 'accept_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    var $sellerQuoteOfferFieldId = $('#accept_quote_' + rowNo);
    var sellerOfferValue = $sellerQuoteOfferFieldId.val();

    var $sellerTransitFieldId = $('#accept_transit_days_' + rowNo);
    var sellertransitValue = $sellerTransitFieldId.val();
    
    var regexPattern= /^\d{1,6}(\.\d{1,2})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
    
    var fromcity = $('#from_city').val();
    var tocity = $('#to_city').val();
    var fromdate = $('#from_date').val();
    var todate = $('#to_date').val();
    var tracking = $('#tracking_' + rowNo).val();
    var paymentoptions = $('.payment_options_' + rowNo).val();
    var credit_peroid = $('.credit_period_ptl_' + rowNo).val();
    var credit_period_units = $('.credit_period_units_' + rowNo).val();
    var search = $('#search').val();
    if (!($sellerTransitFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        });  
        $sellerTransitFieldId.focus();
		 return false;
	}else if (sellertransitValue==0) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });  
        $sellerTransitFieldId.focus();
		 return false;
	} 
    if (!(tracking).trim()) {
		$("#erroralertmodal .modal-body").html("Please Select Tracking");
        $("#erroralertmodal").modal({
            show: true
        });   
        tracking.focus();
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
	
	if((($sellerQuoteOfferFieldId.val()).trim() && ($sellerTransitFieldId.val()).trim())){
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
	        data : { 'accept_quote': sellerOfferValue, 'buyer_buyerquote_id' : rowNo,'accept_transit': sellertransitValue,
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
$("#addsellersearchpostquoteoffer .counter_quote_submit").click(function(){

    var buttonId = $(this).attr('id');
    var removeString = 'counter_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    var $sellerQuoteOfferFieldId = $('#counter_quote_' + rowNo);
    var sellerOfferValue = $sellerQuoteOfferFieldId.val();

    var fromcity = $('#from_city').val();
    var tocity = $('#to_city').val();
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
        		 'from_city_loc':fromcity,'to_city_loc':tocity,'search':search,
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




$("#addptlsellersearchpostquoteoffer .ptl_initial_quote_submit").click(function(){
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	var zone_or_location = $('#zone_or_location').val();
	
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();

	
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_initail_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var from_city_loc = $('#from_city_loc_' + rowNo).val();
    var to_city_loc = $('#to_city_loc_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();
    
    var tracking = $('#tracking_' + rowNo).val();
    var paymentoptions = $('.payment_options_' + rowNo).val();
    var credit_peroid = $('.credit_period_ptl_' + rowNo).val();
    var credit_period_units = $('.credit_period_units_' + rowNo).val();
    
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#initial_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#initial_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
    if(serviceid == 8 || serviceid == 9){
    	var sellerPickupOfferValue = 0;
  	    var sellerDeliveryOfferValue = 0;
  	    var sellerODAOfferValue = 0;
	    
    }
    else{
	    var $sellerPickupFieldId = $('#initial_quote_pickup_' + rowNo);
	    var sellerPickupOfferValue = $sellerPickupFieldId.val();
	    
	    var $sellerDeliveryOfferFieldId = $('#initial_quote_delivery_' + rowNo);
	    var sellerDeliveryOfferValue = $sellerDeliveryOfferFieldId.val();
	    
	    var $sellerODAOfferFieldId = $('#initial_quote_oda_' + rowNo);
	    var sellerODAOfferValue = $sellerODAOfferFieldId.val();
    }
    
    var $sellertransitOfferFieldId = $('#initial_quote_transit_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        });   
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (sellerRateKgOfferValue==0) {
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Ccm");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cbm");
		else
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cft");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (!regexconversionfactorPattern.test(sellerKgCftOfferValue)) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be less than 10000 with max of 3 decimals");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be less than 10000 with max of 3 decimals");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerKgCftFieldId.focus();
		 return false;
	}  else if (sellerKgCftOfferValue==0) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be greater than 0");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be greater than 0");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerKgCftFieldId.focus();
		 return false;
	} 
	if(serviceid == 8 || serviceid == 9){
		
	}else{
		if (!($sellerPickupFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Pickup Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerPickupFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerPickupOfferValue)) {
			$("#erroralertmodal .modal-body").html("Pickup Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerPickupFieldId.focus();
			 return false;
		} 
		if (!($sellerDeliveryOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Delivery Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerDeliveryOfferValue)) {
			$("#erroralertmodal .modal-body").html("Delivery Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} 
		if (!($sellerODAOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter ODA Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerODAOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerODAOfferValue)) {
			$("#erroralertmodal .modal-body").html("ODA Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerODAOfferFieldId.focus();
			 return false;
		} 
	}
	if (!($sellertransitOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} 
	
	if (!(tracking).trim()) {
		$("#erroralertmodal .modal-body").html("Please Select Tracking");
        $("#erroralertmodal").modal({
            show: true
        });   
        tracking.focus();
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
				if (!($('#credit_period_ptl').val()).trim()) {
					$("#erroralertmodal .modal-body").html("Please enter Credit Period");
			        $("#erroralertmodal").modal({
			            show: true
			        });
					return false;
				} else if (!regexPattern.test($('#credit_period_ptl').val())) {
					$("#erroralertmodal .modal-body").html("Credit Period Field should be a number");
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
	
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.pickupvalue = sellerPickupOfferValue;
	datavaluses.deliveryvalue = sellerDeliveryOfferValue;
	datavaluses.odavalue = sellerODAOfferValue;
	datavaluses.from_city_loc = from_city_loc;
	datavaluses.to_city_loc = to_city_loc;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount =increment;
	datavaluses.tracking = tracking;
	datavaluses.paymentoptions = paymentoptions;
	datavaluses.credit_peroid = credit_peroid;
	datavaluses.credit_period_units = credit_period_units;
	datavaluses.cbuyerquoteid = buyerquoteid;
	datavaluses.zone_or_location = zone_or_location;
	if(serviceid == 8 || serviceid == 9){
		var $con = $sellerRateKgFieldId.val().trim();
	}else{
		$con = (($sellerPickupFieldId.val()).trim() && ($sellerDeliveryOfferFieldId.val()).trim() && ($sellerODAOfferFieldId.val()).trim());
	}
	if($con){
		 $.ajax({
            type: "POST",
            url : "/sellersearchsubmitquote",
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
			data : datavaluses,
			
		
   		
   		   success : function(jsonData){
			  
			   $("#erroralertmodal .modal-body").html('Initial Quote given successfully.');
               $("#erroralertmodal").modal({
                   show: true
               }).one('click','.ok-btn',function (e){            	   
                   location.reload();
                   //window.location = window.location.pathname;
            	   //window.location = window.location;
            	   //window.location.href = window.location.href;
            	   //$(".data-refresh").load(location.href + " .data-refresh>*", "");
            	   //window.location.reload(true);
            	   //$("#block-refresh").load(location.href + " #block-refresh>*", "");
            	   //window.location.assign(location.href) 
                   //document.location.reload(true);
            	   //location = location
            	   //location = location.href
            	   //location = window.location
            	   //location = self.location
            	   //location = window.location.href
            	   //location = self.location.href
            	   //location = location['href']
            	   //location = window['location']
            	   //location = window['location'].href
            	   //location.assign(location)
            	   //self['location']['reload']()
            	   //self['location']['replace'](self.location['href'])
               });
		   }
       },"json");
	}
});



$("#couriersearchpostquoteoffer .ptl_initial_quote_submit").click(function(){
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	var zone_or_location = $('#zone_or_location').val();
	
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();

	
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_initail_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var couriertype = $('#courier_type').val();
    var courierdeliverytype = $('#courier_delivery_type').val();
    var units = $('#units_' + rowNo).val();
    var from_city_loc = $('#from_city_loc_' + rowNo).val();
    var to_city_loc = $('#to_city_loc_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();
    var daysselect = $('#dayspicker_' + rowNo).val();

    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    
    var tracking = $('#tracking_' + rowNo).val();
    var paymentoptions = $('.payment_options_' + rowNo).val();
    var credit_peroid = $('.credit_period_ptl_' + rowNo).val();
    var credit_period_units = $('.credit_period_units_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#initial_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#initial_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
   
    var $sellerPickupFieldId = $('#initial_fuel_surcharge_rupees_' + rowNo);
    var sellerPickupOfferValue = $sellerPickupFieldId.val();
    
    var $sellerDeliveryOfferFieldId = $('#initial_cod_rupees_' + rowNo);
    var sellerDeliveryOfferValue = $sellerDeliveryOfferFieldId.val();
    
    var $sellerODAOfferFieldId = $('#initial_freight_collect_rupees_' + rowNo);
    var sellerODAOfferValue = $sellerODAOfferFieldId.val();
    
    var $sellerARCOfferFieldId = $('#initial_arc_rupees_' + rowNo);
    var sellerARCOfferValue = $sellerARCOfferFieldId.val();
   
    
    var $sellertransitOfferFieldId = $('#initial_quote_transit_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
	var regexPattern= /^\d{1,5}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        });   
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		$("#erroralertmodal .modal-body").html("Rate per Kg Field should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	}  else if (sellerRateKgOfferValue==0) {
		$("#erroralertmodal .modal-body").html("Rate per Kg Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Conversion Factor");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (!regexconversionfactorPattern.test(sellerKgCftOfferValue)) {
		$("#erroralertmodal .modal-body").html("Conversion Factor Field should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (sellerKgCftOfferValue==0) {
		$("#erroralertmodal .modal-body").html("Conversion Factor Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerKgCftFieldId.focus();
		 return false;
	} 
	
	if (!($sellerPickupFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Fuel Surcharges");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerPickupFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerPickupOfferValue)) {
		$("#erroralertmodal .modal-body").html("Fuel Surcharges Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerPickupFieldId.focus();
		 return false;
	} 
	if (!($sellerDeliveryOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter COD");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerDeliveryOfferFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerDeliveryOfferValue)) {
		$("#erroralertmodal .modal-body").html("COD Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerDeliveryOfferFieldId.focus();
		 return false;
	} 
	if (!($sellerODAOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Freight Rupees");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerODAOfferFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerODAOfferValue)) {
		$("#erroralertmodal .modal-body").html("Freight Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerODAOfferFieldId.focus();
		 return false;
	} 
	if (!($sellerARCOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter ARC Field");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerARCOfferFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerARCOfferValue)) {
		$("#erroralertmodal .modal-body").html("ARC Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerARCOfferFieldId.focus();
		 return false;
	}
	if (!($sellertransitOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} 
	
	
	if (!(tracking).trim()) {
		$("#erroralertmodal .modal-body").html("Please Select Tracking");
        $("#erroralertmodal").modal({
            show: true
        });   
        tracking.focus();
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
				if (!($('#credit_period_ptl').val()).trim()) {
					$("#erroralertmodal .modal-body").html("Please enter Credit Period");
			        $("#erroralertmodal").modal({
			            show: true
			        });
					return false;
				} else if (!regexPattern.test($('#credit_period_ptl').val())) {
					$("#erroralertmodal .modal-body").html("Credit Period Field should be a number");
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
	
	
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.couriertype = couriertype;
	datavaluses.courierdeliverytype = courierdeliverytype;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.fuelvalue = sellerPickupOfferValue;
	datavaluses.codvalue = sellerDeliveryOfferValue;
	datavaluses.freightvalue = sellerODAOfferValue;
	datavaluses.arcvalue = sellerARCOfferValue;
	datavaluses.from_city_loc = from_city_loc;
	datavaluses.to_city_loc = to_city_loc;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount =increment;
	datavaluses.daysselect = daysselect;
	datavaluses.tracking = tracking;
	datavaluses.paymentoptions = paymentoptions;
	datavaluses.credit_peroid = credit_peroid;
	datavaluses.credit_period_units = credit_period_units;
	datavaluses.cbuyerquoteid = buyerquoteid;
	datavaluses.zone_or_location = zone_or_location;
	if(serviceid == 8 || serviceid == 9){
		var $con = $sellerRateKgFieldId.val().trim();
	}else{
		$con = (($sellerPickupFieldId.val()).trim() && ($sellerDeliveryOfferFieldId.val()).trim() && ($sellerODAOfferFieldId.val()).trim());
	}
	if($con){
		 $.ajax({
            type: "POST",
            url : "/sellersearchsubmitquote",
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
			data : datavaluses,
			
		
   		
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

//courier search final
$("#couriersearchpostquoteoffer .ptl_final_quote_submit").click(function(){
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	var zone_or_location = $('#zone_or_location').val();
	
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();

	
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_final_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var packagevalue = $('#packagevalue_' + rowNo).val();
    var couriertype = $('#courier_type').val();
    var courierdeliverytype = $('#courier_delivery_type').val();
    var units = $('#units_' + rowNo).val();
    var from_city_loc = $('#from_city_loc_' + rowNo).val();
    var to_city_loc = $('#to_city_loc_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();
    var daysselect = $('#dayspicker_' + rowNo).val();

    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#final_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#final_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
   
    var $sellerPickupFieldId = $('#final_fuel_surcharge_rupees_' + rowNo);
    var sellerPickupOfferValue = $sellerPickupFieldId.val();
    
    var $sellerDeliveryOfferFieldId = $('#final_cod_rupees_' + rowNo);
    var sellerDeliveryOfferValue = $sellerDeliveryOfferFieldId.val();
    
    var $sellerODAOfferFieldId = $('#final_freight_collect_rupees_' + rowNo);
    var sellerODAOfferValue = $sellerODAOfferFieldId.val();
    
    var $sellerARCOfferFieldId = $('#final_arc_rupees_' + rowNo);
    var sellerARCOfferValue = $sellerARCOfferFieldId.val();
   
    
    var $sellertransitOfferFieldId = $('#final_quote_transit_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
    var regexPattern= /^\d{1,5}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        });   
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		$("#erroralertmodal .modal-body").html("Rate per Kg Field should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (sellerRateKgOfferValue==0) {
		$("#erroralertmodal .modal-body").html("Rate per Kg Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Conversion Factor");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (!regexconversionfactorPattern.test(sellerKgCftOfferValue)) {
		$("#erroralertmodal .modal-body").html("Conversion Factor Field should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (sellerKgCftOfferValue==0) {
		$("#erroralertmodal .modal-body").html("Conversion Factor Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerKgCftFieldId.focus();
		 return false;
	} 
	
	if (!($sellerPickupFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Fuel Surcharges");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerPickupFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerPickupOfferValue)) {
		$("#erroralertmodal .modal-body").html("Fuel Surcharges Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerPickupFieldId.focus();
		 return false;
	} 
	if (!($sellerDeliveryOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter COD");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerDeliveryOfferFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerDeliveryOfferValue)) {
		$("#erroralertmodal .modal-body").html("COD Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerDeliveryOfferFieldId.focus();
		 return false;
	} 
	if (!($sellerODAOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Freight Rupees");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerODAOfferFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerODAOfferValue)) {
		$("#erroralertmodal .modal-body").html("Freight Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerODAOfferFieldId.focus();
		 return false;
	} 
	if (!($sellerARCOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter ARC Field");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerARCOfferFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerARCOfferValue)) {
		$("#erroralertmodal .modal-body").html("ARC Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerARCOfferFieldId.focus();
		 return false;
	}
	if (!($sellertransitOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} 
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.packagevalue = packagevalue;
	datavaluses.couriertype = couriertype;
	datavaluses.courierdeliverytype = courierdeliverytype;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.fuelvalue = sellerPickupOfferValue;
	datavaluses.codvalue = sellerDeliveryOfferValue;
	datavaluses.freightvalue = sellerODAOfferValue;
	datavaluses.arcvalue = sellerARCOfferValue;
	datavaluses.from_city_loc = from_city_loc;
	datavaluses.to_city_loc = to_city_loc;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount =increment;
	datavaluses.daysselect = daysselect;
	datavaluses.cbuyerquoteid = buyerquoteid;
	datavaluses.zone_or_location = zone_or_location;
	if(serviceid == 8 || serviceid == 9){
		var $con = $sellerRateKgFieldId.val().trim();
	}else{
		$con = (($sellerPickupFieldId.val()).trim() && ($sellerDeliveryOfferFieldId.val()).trim() && ($sellerODAOfferFieldId.val()).trim());
	}
	if($con){
		 $.ajax({
            type: "POST",
            url : "/sellerfinalquotesubmit",
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
			data : datavaluses,
			
		
   		
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




//courier search Counter
$("#couriersearchpostquoteoffer .ptl_counter_quote_submit").click(function(){
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val();
	var zone_or_location = $('#zone_or_location').val();
	
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();

	
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_counter_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var packagevalue = $('#packagevalue_' + rowNo).val();
    var couriertype = $('#courier_type').val();
    var courierdeliverytype = $('#courier_delivery_type').val();
    var units = $('#units_' + rowNo).val();
    var from_city_loc = $('#from_city_loc_' + rowNo).val();
    var to_city_loc = $('#to_city_loc_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();
    var daysselect = $('#dayspicker_' + rowNo).val();

    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#initial_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#initialfinal_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
   
    var $sellerPickupFieldId = $('#initial_fuel_surcharge_rupees_' + rowNo);
    var sellerPickupOfferValue = $sellerPickupFieldId.val();
    
    var $sellerDeliveryOfferFieldId = $('#initial_cod_rupees_' + rowNo);
    var sellerDeliveryOfferValue = $sellerDeliveryOfferFieldId.val();
    
    var $sellerODAOfferFieldId = $('#initial_freight_collect_rupees_' + rowNo);
    var sellerODAOfferValue = $sellerODAOfferFieldId.val();
    
    var $sellerARCOfferFieldId = $('#initial_arc_rupees_' + rowNo);
    var sellerARCOfferValue = $sellerARCOfferFieldId.val();
   
    
    var $sellertransitOfferFieldId = $('#initial_quote_transit_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
	var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
	var regexNumericPattern= /[1-9]\d*/;
	
	
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.packagevalue = packagevalue;
	datavaluses.couriertype = couriertype;
	datavaluses.courierdeliverytype = courierdeliverytype;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.fuelvalue = sellerPickupOfferValue;
	datavaluses.codvalue = sellerDeliveryOfferValue;
	datavaluses.freightvalue = sellerODAOfferValue;
	datavaluses.arcvalue = sellerARCOfferValue;
	datavaluses.from_city_loc = from_city_loc;
	datavaluses.to_city_loc = to_city_loc;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount =increment;
	datavaluses.daysselect = daysselect;
	datavaluses.cbuyerquoteid = buyerquoteid;
	datavaluses.zone_or_location = zone_or_location;
	if(serviceid == 8 || serviceid == 9){
		var $con = $sellerRateKgFieldId.val().trim();
	}else{
		$con = (($sellerPickupFieldId.val()).trim() && ($sellerDeliveryOfferFieldId.val()).trim() && ($sellerODAOfferFieldId.val()).trim());
	}
	if($con){
		 $.ajax({
            type: "POST",
            url : "/selleraccept",
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
			data : datavaluses,
			
		
   		
   		   success : function(jsonData){
			  
			   $("#erroralertmodal .modal-body").html('Counter Offer from Buyer accepted.');
               $("#erroralertmodal").modal({
                   show: true
               }).one('click','.ok-btn',function (e){            	   
                   location.reload();
                  
               });
		   }
       },"json");
	}
});


//Counter for PTL
$("#addptlsellersearchpostquoteoffer .ptl_counter_quote_submit").click(function(){
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val()
	
	var zone_or_location = $('#zone_or_location').val();
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();

	
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_counter_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var from_city_loc = $('#from_city_loc_' + rowNo).val();
    var to_city_loc = $('#to_city_loc_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();

    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#counter_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
   
    var $sellerKgCftFieldId = $('#counter_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
    if(serviceid == 8 || serviceid == 9){
    	var sellerPickupOfferValue = 0;
  	    var sellerDeliveryOfferValue = 0;
  	    var sellerODAOfferValue = 0;
	    
    }
    else{
	    var $sellerPickupFieldId = $('#initial_quote_pickup_' + rowNo);
	    var sellerPickupOfferValue = $sellerPickupFieldId.val();
	    
	    var $sellerDeliveryOfferFieldId = $('#initial_quote_delivery_' + rowNo);
	    var sellerDeliveryOfferValue = $sellerDeliveryOfferFieldId.val();
	    
	    var $sellerODAOfferFieldId = $('#initial_quote_oda_' + rowNo);
	    var sellerODAOfferValue = $sellerODAOfferFieldId.val();
    }   
    var $sellertransitOfferFieldId = $('#initial_quote_transit_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
	var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
	var regexNumericPattern= /[1-9]\d*/;
	
	/*if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Ccm");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cm");
		else
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cft");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerKgCftOfferValue)) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be a number");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cm Field should be a number");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerKgCftFieldId.focus();
		 return false;
	} 
	if(serviceid == 8 || serviceid == 9){
		
	}else{
		if (!($sellerPickupFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Pickup Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerPickupFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerPickupOfferValue)) {
			$("#erroralertmodal .modal-body").html("Pickup Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        }); 
			 $sellerPickupFieldId.focus();
			 return false;
		} 
		if (!($sellerDeliveryOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Delivery Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			$sellerDeliveryOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerDeliveryOfferValue)) {
			$("#erroralertmodal .modal-body").html("Delivery Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} 
		if (!($sellerODAOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter ODA Rupees");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerODAOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerODAOfferValue)) {
			$("#erroralertmodal .modal-body").html("ODA Field should be less then 10000 with max of 2 decimals");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			 $sellerODAOfferFieldId.focus();
			 return false;
		} 
	}
	if (!($sellertransitOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days Field should be a number");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellertransitOfferFieldId.focus();
		 return false;
	} */
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.pickupvalue = sellerPickupOfferValue;
	datavaluses.deliveryvalue = sellerDeliveryOfferValue;
	datavaluses.odavalue = sellerODAOfferValue;
	datavaluses.from_city_loc = from_city_loc;
	datavaluses.to_city_loc = to_city_loc;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount =increment;
	datavaluses.cbuyerquoteid = buyerquoteid;
	datavaluses.zone_or_location = zone_or_location;
	if(serviceid == 8 || serviceid == 9){
		var $con = $sellerRateKgFieldId.val().trim();
	}else{
		$con = (($sellerPickupFieldId.val()).trim() && ($sellerDeliveryOfferFieldId.val()).trim() && ($sellerODAOfferFieldId.val()).trim());
	}
	if($con){
		 $.ajax({
            type: "POST",
            url : "/selleraccept",
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
			data : datavaluses,
			
		
   		
   		   success : function(jsonData){
			  
			   $("#erroralertmodal .modal-body").html('Counter Offer from Buyer accepted.');
               $("#erroralertmodal").modal({
                   show: true
               }).one('click','.ok-btn',function (e){
                   location.reload();
               });
		   }
       },"json");
	}
});

$("#addptlsellersearchpostquoteoffer .ptl_final_quote_submit").click(function(){
	var serviceid = $('#serviceid').val();
	var seller_post_item_id = $('#seller_post_item_id').val()
	var zone_or_location = $('#zone_or_location').val();
	
	dataObj = {};
	var className = ".formquoteid_"+$(this).attr('name');
	
	var formvalues = $(className).serialize();

	
    var buttonId = $(this).attr('id');
    var removeString = 'ptl_final_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
  
    var volumetric = $('#volumetric_' + rowNo).val();
    var packageno = $('#packagenos_' + rowNo).val();
    var units = $('#units_' + rowNo).val();
    var from_city_loc = $('#from_city_loc_' + rowNo).val();
    var to_city_loc = $('#to_city_loc_' + rowNo).val();
    var sellerkgpercft = $('#sellerkgpercft_' + rowNo).val();
    var buyerquoteid = $('#buyerquoteid_' + rowNo).val();
    var increment = $('#incrementcount_' + rowNo).val();
    
    var $sellerRateKgFieldId = $('#final_quote_rateperkg_' + rowNo);
    var sellerRateKgOfferValue = $sellerRateKgFieldId.val();
    
    var $sellerKgCftFieldId = $('#final_quote_kgperdft_' + rowNo);
    var sellerKgCftOfferValue = $sellerKgCftFieldId.val();
    if(serviceid == 8 || serviceid == 9){
    	var sellerPickupOfferValue = 0;
  	    var sellerDeliveryOfferValue = 0;
  	    var sellerODAOfferValue = 0;
	    
    }
    else{
	    var $sellerPickupFieldId = $('#final_quote_pickup_' + rowNo);
	    var sellerPickupOfferValue = $sellerPickupFieldId.val();
	    
	    var $sellerDeliveryOfferFieldId = $('#final_quote_delivery_' + rowNo);
	    var sellerDeliveryOfferValue = $sellerDeliveryOfferFieldId.val();
	    
	    var $sellerODAOfferFieldId = $('#final_quote_oda_' + rowNo);
	    var sellerODAOfferValue = $sellerODAOfferFieldId.val();
    }
    var $sellertransitOfferFieldId = $('#final_quote_transit_' + rowNo);
    var sellertransitOfferValue = $sellertransitOfferFieldId.val();
    
    var regexPattern= /^\d{1,4}(\.\d{1,2})?$/;
    var regexconversionfactorPattern = /^\d{1,4}(\.\d{1,4})?$/;
	var regexNumericPattern= /^\d{1,3}?$/;
	
	if (!($sellerRateKgFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Rate per Kg");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerRateKgOfferValue)) {
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be less than 10000 with max of 2 decimals");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} else if (sellerRateKgOfferValue==0) {
		$("#erroralertmodal .modal-body").html("Rate Per Kg Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerRateKgFieldId.focus();
		 return false;
	} 
	if (!($sellerKgCftFieldId.val()).trim()) {
		 
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Ccm");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cbm");
		else
			$("#erroralertmodal .modal-body").html("Please enter Kg Per Cft");
        $("#erroralertmodal").modal({
            show: true
        });
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (!regexconversionfactorPattern.test(sellerKgCftOfferValue)) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be less than 10000 with max of 3 decimals");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be less than 10000 with max of 3 decimals");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be less than 10000 with max of 3 decimals");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerKgCftFieldId.focus();
		 return false;
	} else if (sellerKgCftOfferValue==0) {
		if(serviceid == 7 || serviceid == 8)
			$("#erroralertmodal .modal-body").html("Kg Per Ccm Field should be greater than 0");
		else if(serviceid == 9)
			$("#erroralertmodal .modal-body").html("Kg Per Cbm Field should be greater than 0");
		else
			$("#erroralertmodal .modal-body").html("Kg Per Cft Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerKgCftFieldId.focus();
		 return false;
	} 
	if(serviceid == 8 || serviceid == 9){
		
	}else{
		if (!($sellerPickupFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Pickup Rupees");
		    $("#erroralertmodal").modal({
		        show: true
		    }); 
			 $sellerPickupFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerPickupOfferValue)) {
			$("#erroralertmodal .modal-body").html("Pickup Field should be less then 10000 with max of 2 decimals");
		    $("#erroralertmodal").modal({
		        show: true
		    }); 
			 $sellerPickupFieldId.focus();
			 return false;
		} 
		if (!($sellerDeliveryOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter Delivery Rupees");
		    $("#erroralertmodal").modal({
		        show: true
		    }); 
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerDeliveryOfferValue)) {
			$("#erroralertmodal .modal-body").html("Delivery Field should be less then 10000 with max of 2 decimals");
		    $("#erroralertmodal").modal({
		        show: true
		    }); 
			 $sellerDeliveryOfferFieldId.focus();
			 return false;
		} 
		if (!($sellerODAOfferFieldId.val()).trim()) {
			$("#erroralertmodal .modal-body").html("Please enter ODA Rupees");
		    $("#erroralertmodal").modal({
		        show: true
		    }); 
			 $sellerODAOfferFieldId.focus();
			 return false;
		} else if (!regexPattern.test(sellerODAOfferValue)) {
			$("#erroralertmodal .modal-body").html("ODA Field should be less then 10000 with max of 2 decimals");
		    $("#erroralertmodal").modal({
		        show: true
		    }); 
			 $sellerODAOfferFieldId.focus();
			 return false;
		}
	}
	if (!($sellertransitOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit days");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (!regexNumericPattern.test(sellertransitOfferValue)) {
		$("#erroralertmodal .modal-body").html("Transit days Field should be numeric and less than 4 digits");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellertransitOfferFieldId.focus();
		 return false;
	} else if (sellertransitOfferValue==0) {
		$("#erroralertmodal .modal-body").html("Transit days Field should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellertransitOfferFieldId.focus();
		 return false;
	} 
	datavaluses = {};
	datavaluses.formvalues = formvalues;
	datavaluses.buyer_buyerquote_id = rowNo;
	datavaluses.seller_post_item_id = seller_post_item_id;
	datavaluses.rateperkgValue = sellerRateKgOfferValue;
	datavaluses.kgpercftValue = sellerKgCftOfferValue;
	datavaluses.pickupvalue = sellerPickupOfferValue;
	datavaluses.deliveryvalue = sellerDeliveryOfferValue;
	datavaluses.odavalue = sellerODAOfferValue;
	datavaluses.transitrValue = sellertransitOfferValue;
	datavaluses.incrementcount = increment;
	datavaluses.cbuyerquoteid = buyerquoteid;
	datavaluses.zone_or_location = zone_or_location;
	if(serviceid == 8 || serviceid == 9){
		var $con = $sellerRateKgFieldId.val().trim();
	}else{
		$con = (($sellerPickupFieldId.val()).trim() && ($sellerDeliveryOfferFieldId.val()).trim() && ($sellerODAOfferFieldId.val()).trim());
	}
	if($con){
		 $.ajax({
            type: "POST",
            url : "/sellerfinalquotesubmit",
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
			data : datavaluses,
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


//public initial quote
$("#addsellerpostquoteoffer .initial_quote_public_submit").click(function(){
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
            url : "/sellersubmitquote",
            data : { 'initial_quote': sellerOfferValue, 'buyer_buyerquote_id' : rowNo },
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


//Counter quote SUbmit
$("#addsellerpostquoteoffer .counter_quote_submit").click(function(e){
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

//Lead counter
$("#leadsellerpostquoteoffer .counter_lead_quote_submit").click(function(e){
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


//Final quote SUbmit
$("#addsellerpostquoteoffer .final_quote_submit").click(function(e){
	var buttonId = $(this).attr('id');
    var removeString = 'final_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    var $sellerQuoteOfferFieldId = $('#final_quote_' + rowNo);
    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
    

    var $sellerTransitFieldId = $('#final_transit_days_' + rowNo);
    var sellerTransitValue = $sellerTransitFieldId.val();
    
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
	}else if (sellerOfferValue>200000) {
		$("#erroralertmodal .modal-body").html("Final Quote should be less than or equal to 2 Lakhs");
        $("#erroralertmodal").modal({
            show: true
        });
		$sellerQuoteOfferFieldId.focus();
		 return false;
	}else if (!($sellerTransitFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerTransitValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
		 return false;
	}else if (sellerTransitValue==0) {
		$("#erroralertmodal .modal-body").html("Transit Days should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
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
            data : { 'final_quote': sellerOfferValue, 'final_transit': sellerTransitValue, 'buyer_buyerquote_id' : rowNo },
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


//Lead Final quote SUbmit
$("#leadsellerpostquoteoffer .final_lead_quote_submit").click(function(e){
	var buttonId = $(this).attr('id');
    var removeString = 'final_lead_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    var $sellerQuoteOfferFieldId = $('#final_lead_quote_' + rowNo);
    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
    
    var $sellerTransitFieldId = $('#final_transit_days_' + rowNo);
    var sellertransitValue = $sellerTransitFieldId.val();
    
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
	}else if (sellerOfferValue>200000) {
		$("#erroralertmodal .modal-body").html("Final Quote should be less than or equal to 2 Lakhs");
        $("#erroralertmodal").modal({
            show: true
        });
		$sellerQuoteOfferFieldId.focus();
		 return false;
	} else if (!($sellerTransitFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellertransitValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
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
            data : { 'final_quote': sellerOfferValue,'final_transit': sellertransitValue, 'buyer_buyerquote_id' : rowNo },
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
$("#addsellerpostquoteoffer .accept_quote_submit").click(function(e){
	var buttonId = $(this).attr('id');
    var removeString = 'acccept_quote_submit_';
   
    var rowNo = buttonId.replace(removeString, '');
    
    var $sellerQuoteOfferFieldId = $('#accept_quote_' + rowNo);
    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
    
    var $sellerTransitFieldId = $('#accept_transit_days_' + rowNo);
    var sellerTransitValue = $sellerTransitFieldId.val();
    
    var seller_post_item_id = $('#seller_post_item_id').val();
    var transactionid = $('#transactionid').val();
    var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
    if (!($sellerTransitFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerTransitValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
		 return false;
	} else if (sellerTransitValue==0) {
		$("#erroralertmodal .modal-body").html("Transit Days should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
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
	        url : "/sellerfirmacceptance",
	        data : { 'accept_quote': sellerOfferValue, 'buyer_buyerquote_id' : rowNo,'accept_transit':sellerTransitValue,
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
	}
	
});

//lead accept Firm Price
$("#leadsellerpostquoteoffer .accept_lead_quote_submit").click(function(e){
	var buttonId = $(this).attr('id');
    var removeString = 'accept_lead_quote_submit_';
   
    var rowNo = buttonId.replace(removeString, '');
    var $sellerQuoteOfferFieldId = $('#accept_lead_quote_' + rowNo);
    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
    

    var $sellerTransitFieldId = $('#initial_transit_days_' + rowNo);
    var sellerTransitValue = $sellerTransitFieldId.val();
    
    var seller_post_item_id = $('#seller_post_item_id').val();
    var transactionid = $('#transactionid').val();
    
    var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
    if (!($sellerTransitFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellerTransitValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
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
	        url : "/sellerfirmacceptance",
	        data : { 'accept_quote': sellerOfferValue, 'buyer_buyerquote_id' : rowNo,'accept_transit': sellerTransitValue,
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
	}
	
});

//Final quote for seacrh buyer quotes
$("#addsellersearchpostquoteoffer .final_quote_submit").click(function(e){
	var buttonId = $(this).attr('id');
    var removeString = 'final_quote_submit_';
    var rowNo = buttonId.replace(removeString, '');
    var $sellerQuoteOfferFieldId = $('#final_quote_' + rowNo);
    var sellerOfferValue = $sellerQuoteOfferFieldId.val();
    

    var $sellerTransitFieldId = $('#final_transit_days_' + rowNo);
    var sellertransitValue = $sellerTransitFieldId.val();
    
    
	var regexPattern= /^\d{0,8}(\.\d{1,2})?$/;
	if (!($sellerQuoteOfferFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Final Quote");
        $("#erroralertmodal").modal({
            show: true
        }); 
		 $sellerQuoteOfferFieldId.focus();
		 return false;
	} else if (sellerOfferValue>200000) {
		$("#erroralertmodal .modal-body").html("Final Quote  should be less than or equal to 2 Lakhs");
        $("#erroralertmodal").modal({
            show: true
        });  
		 $sellerQuoteOfferFieldId.focus();
		 return false;
	} else if (!($sellerTransitFieldId.val()).trim()) {
		$("#erroralertmodal .modal-body").html("Please enter Transit Days");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
		 return false;
	} else if (!regexPattern.test(sellertransitValue)) {
		$("#erroralertmodal .modal-body").html("Transit Days should be a number");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
		 return false;
	}else if (sellertransitValue==0) {
		$("#erroralertmodal .modal-body").html("Transit Days should be greater than 0");
        $("#erroralertmodal").modal({
            show: true
        }); 
        $sellerTransitFieldId.focus();
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
            data : { 'final_quote': sellerOfferValue,'final_transit': sellertransitValue, 'buyer_buyerquote_id' : rowNo },
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
/*
$("#check_max_weight").change(function() {
    if(this.checked) {
    	$("#incremental_weight").removeAttr('readonly');
    	$("#rate_per_increment").removeAttr('readonly');
    }else{
    	$("#incremental_weight").prop('readonly', true);
        $("#rate_per_increment").prop('readonly', true);
        $("#incremental_weight").val('');
        $("#rate_per_increment").val('');
    }
});*/


$("#check_driver_availablity").change(function() {
    if(this.checked) {
    	$("#need_driver").val(1);
    	
    	$("#driver_cost").prop('disabled', false);
    }else{
        $("#need_driver").val(0);
        $("#driver_cost").val('');
        $("#driver_cost").prop('disabled', true);
        
    }
});

//Multi line items validation for Ftl
    $('#add_more').on('click', function() {
        $("#posts-form-lines").valid();
    });
    


    $("#posts-form").validate({
    	ignore: [],
        rules : {
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
            }
		    ,
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
    $("#posts-form-lines").validate({
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
            "to_location" : {
                required : true,
            },
            "to_location_id" : {
                required : true,
            },
            "VehicleTypeMasters" : {
                required : true,
            },
            "LoadTypeMasters" : {
                required : true,
            },
            "transitdays" : {
                required : true,
                digits: true,
                transitvalidation:true,
                rangelength: [0,3]
            },
            "price" : {
                required : true,
                number: true,
                pricevalidation:true,
            },
        },
        errorPlacement: function(error, element) {
        	$(element).parent('div').after(error);
        },
        messages : {
            "valid_from" : {
                required : "Valid From date is required",
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
            "to_location" : {
                required : "",
            },
            "to_location_id" : {
                required : "To Location is required",
            },
            "VehicleTypeMasters" : {
                required : "Vehicle Type is required",
            },
            "LoadTypeMasters" : {
                required : "Load Type is required",
            },
            "transitdays" : {
                required : "Transit Days is required",
            },
            "price" : {
                required : "Price is required",
            }
        },
        submitHandler : function(form) {
            form.submit();
        }
    });
    
    
    
    
    
    
/**** Start : Truck Haul Post Validation  *****/
    $('#add_more_th').on('click', function() {
        $("#truckhaul-posts-form-lines").valid();
    });

    $("#truckhaul-posts-form").validate({
        ignore: [],
        rules : {
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
            }
            ,
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
    $("#truckhaul-posts-form-lines").validate({
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
            "to_location" : {
                required : true,
            },
            "to_location_id" : {
                required : true,
            },
            "VehicleTypeMasters" : {
                required : true,
            },
            "vehicle_number" : {
                required : true,
            },
//            "LoadTypeMasters" : {
//                required : true,
//            },
            "transitdays" : {
                required : true,
                digits: true,
                transitvalidation:true,
                rangelength: [0,3]
            },
            "price" : {
                required : true,
                number: true,
                pricevalidation:true,
            },
        },
        errorPlacement: function(error, element) {
            $(element).parent('div').after(error);
        },
        messages : {
            "valid_from" : {
                required : "Valid From date is required",
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
            "to_location" : {
                required : "",
            },
            "to_location_id" : {
                required : "To Location is required",
            },
            "VehicleTypeMasters" : {
                required : "Vehicle Type is required",
            },
            "vehicle_number" : {
                required : "Vehicle Number is required",
            },
//            "LoadTypeMasters" : {
//                required : "Load Type is required",
//            },
            "transitdays" : {
                required : "Transit Days is required",
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
    var sel_list_th = new Array();
    $('#add_more_th').click(function() {
        var num = parseInt($('#next_add_more_id').val()) + 1;
        $('#next_add_more_id').val(num);
        var from_location = $('#from_location').val();
        var datepicker_from_value = $('#datepicker').val();
        var datepicker_from_value = convertDateFormatForDatePickerSeller(datepicker_from_value);
        var datepicker_to_value = $('#datepicker_to_location').val();
        var datepicker_to_value = convertDateFormatForDatePickerSeller(datepicker_to_value);
        var seller_district = $('#seller_district_id').val();
        var ts_from = Date.parse(datepicker_from_value);
        var ts_to = Date.parse(datepicker_to_value);
        var from_location_identifier = $('#from_location_id').val();
        var to_location_identifier = $('#to_location_id').val();
        var load_type = $('#load_type').val();
        var vehicle_type = $('#vechile_type').val();
        var to_location = $('#to_location').val();
        var price = $('#price').val();
        var units = $('#transitdays_units').val();
        var transit = $('#transitdays').val();
        var price_numric = /^\d+(\.\d{2})?$/.test(price);
        var transit_numric = /^[0-9]{1,3}$/.test(transit);
        var load_type_value = $( "#load_type option:selected" ).text();
        var vehicle_type_value = $( "#vechile_type option:selected" ).text();
        var vehicle_number = $('#vehicle_number').val();
        if (load_type_value ==  "Select Load Type"){
            load_type_value = "NA";
        } else {
            load_type_value = load_type_value;
        }
        if (vehicle_type_value ==  "Vehicle Type (All)"){
            vehicle_type_value = "All";
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
        
        if (to_location_identifier != '' && from_location_identifier != '' && datepicker_from_value != null && datepicker_to_value != null && from_location != '' && to_location != ''&& price != '' && price != 0 && transit != '' && transit != 0 &&  vehicle_type != '' && price_numric == true && transit_numric == true) {
        if((current_date_seller >= subscription_start_date_start_val) && (current_date_seller <= subscription_end_date_end_val) && (datepicker_from_value >= subscription_start_date_start_val) && (datepicker_from_value <= subscription_end_date_end_val) && (datepicker_to_value >= subscription_start_date_start_val) && (datepicker_to_value <= subscription_end_date_end_val)){
        if (to_location_identifier != '' && from_location_identifier != '' && datepicker_from_value != '' && datepicker_to_value != '' && from_location != '' && to_location != ''&& price != '' && transit != ''  && vehicle_type != '' && price_numric == true && transit_numric == true) {
            var unique = from_location_identifier+to_location_identifier+vehicle_type+ts_from+ts_to+transit;


            if ($.inArray(unique,sel_list_th)==-1) {
                sel_list_th.unshift(unique);
            $.ajax({
                type : 'post',
                url : '/truckhaul/lineitemscheck',
                data : {
                    'from_location' : from_location_identifier,
                    'to_location' : to_location_identifier,
                    'from_date_seller' : datepicker_from_value,
                    'to_date_seller' : datepicker_to_value,
                    'vehicle_type' : vehicle_type,
                    'load_type' : load_type,
                    'transit_days' : transit
                },
                dataType : "html",
                type : 'POST',
                success : function(data) {
                    if (data == '0') {
                        
                        if($("#update_ftl_seller_line").val()==1)
                        {
                            $('.request_row_' + $("#update_ftl_seller_row_count").val()).remove();  
                            $("#update_ftl_seller_line").val(0);
                        }
                        
                        var rowid = "#single_post_item_" + num;
                        var html = '<div class="table-row inner-block-bg request_row_'
                            + num
                            + '" id="single_post_item_'
                            + num
                            + '"><div class="col-md-2 padding-left-none from_location_text" id="from_loc_text_'+num+'">'
                            + from_location
                            + '</div><div class="col-md-2 padding-left-none to_location_text" id="to_loc_text_'+num+'">'
                            + to_location
                            + '</div><div class="col-md-2 padding-left-none">'
                            + load_type_value
                            + '</div><div class="col-md-2 padding-left-none">'
                            + vehicle_type_value
                            + '</div><div class="col-md-2 padding-none">'
                            + vehicle_number
                            + '</div><div class="col-md-1 padding-none">'
                            + price
                            + '/-</div><div class="col-md-1 padding-none"><a href="javascript:void(0)" style ="cursor:pointer; margin-right:10px;" onclick="updatecreatepostlineitem('+num+')" class="edit_this_line_th edit" data-string="'+unique+'" row_id="'
                            + num
                            + '"><i class="fa fa-edit red" title="Edit"></i></a><a style ="cursor:pointer;" class="remove_this_line_item remove" data-string="'+unique+'" row_id="'
                            + num
                            + '"><i class="fa fa-trash red" title="Delete"></i></a></div><input type="hidden" name="from_location[]" id="from_location_id_'+num+'" value="'
                            + from_location_identifier
                            + '"><input type="hidden" name="to_location[]" id="to_location_id_'+num+'" value="'
                            + to_location_identifier
                            + '"><input type="hidden" name="load_type[]" id="load_type_'+num+'" value="'
                            + load_type
                            + '"><input type="hidden" name="vechile_type[]" id="vechile_type_'+num+'" value="'
                            + vehicle_type
                            + '"><input type="hidden" name="transitdays[]" id="transitdays_'+num+'" value="'
                            + transit
                            + '"><input type="hidden" name="units[]" id="units_'+num+'" value="'
                            + units
                            + '"><input type="hidden" name="sellerdistrict[]" id="sellerdistricts_'+num+'" value="'
                            + seller_district
                            + '"><input type="hidden" name="price[]" id="price_'+num+'" value="'
                            + price
                            + '"><input type="hidden" name="vehicle_number[]" id="vehicle_number_'+num+'" value="'
                            + vehicle_number
                            + '"><div class="clearfix"></div></div>';

                        $("#multi-line-itemes").show();
                        $('.request_rows').append(html);
                        var id_line_itemes = $('.request_rows').children().size();
                        if (id_line_itemes == 0){
                        }else{
                            $("#datepicker").prop('disabled', true);
                            $("#datepicker_to_location").prop('disabled', true);
                        }
                        $("#valid_from_val").val(datepicker_from_value);
                        $("#valid_to_val").val(datepicker_to_value);
                        $('#from_location').val("");
                        $('#from_location_id').val("");
                        $('#to_location').val("");
                        $('#to_location_id').val("");
                        $('#vechile_type').val("");
                        $('#vehicle_number').val("");
                        $('#load_type').val("");
                        $('#transitdays').val("");
                        $('#price').val("");
                        $('.selectpicker').selectpicker('refresh');

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

    });

    /******************************Multi line Remove items*********************************************/
        $(document).on('click', '.edit_this_line_th', function() {
            var rowid = $(this).attr("row_id");
            remove_val = $(this).attr("data-string");
            sel_list_th.splice($.inArray(remove_val, sel_list_th),1);
             $("#update_ftl_seller_line").val(1);
             $("#update_ftl_seller_row_count").val(rowid);
               
            $('#from_location').val($("#from_loc_text_"+rowid).html());
            $('#from_location_id').val($("#from_location_id_"+rowid).val());
            $('#to_location').val($("#to_loc_text_"+rowid).html());
            $('#to_location_id').val($("#to_location_id_"+rowid).val());
            $('#vechile_type').selectpicker('val',$("#vechile_type_"+rowid).val());
            $('#load_type').selectpicker('val',$("#load_type_"+rowid).val());
            $('#transitdays').val($("#transitdays_"+rowid).val());
            $('#price').val($("#price_"+rowid).val());
            $('#vehicle_number').val($("#vehicle_number_"+rowid).val());
            
        });

/******************************Multi line add items*********************************************/

    /******************************Save as draft functionality*********************************************/
        $('#add_quote_seller_th').click(function(e) {
            e.preventDefault();
            var id=$('.request_rows').children().size();
            if(id==0){
                $("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
                $("#erroralertmodal").modal({
                    show: true
                });
                return false;
            }else{
                
                
                
                $('#truckhaul-posts-form').submit();
                if($('#truckhaul-posts-form').valid()){
                    $("#add_quote_seller_id_th").prop('disabled', true);
                    $("#add_quote_seller_th").prop('disabled', true);
                }
            }
        });
    /******************************Save as draft functionality*********************************************/

    /******************************confirm functionality*********************************************/
        $('#add_quote_seller_id_th').click(function(e) {
            e.preventDefault();
            var id=$('.request_rows').children().size();
            if(id==0){
                $("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
                $("#erroralertmodal").modal({
                    show: true
                });
                return false;
            }else{
                if($("#truckhaul-posts-form").valid()) {
                var submitData=$("#truckhaul-posts-form").serialize();
                 var btnName = $('#add_quote_seller_id_th').attr('name');
                 $("#add_quote_seller_id_th").prop('disabled', true);
                 $("#add_quote_seller_th").prop('disabled', true);
                 var btnVal = $('#add_quote_seller_id_th').val();
                 var btn = '&'+btnName+'='+btnVal;
                 submitData += btn;
                 $.ajax({
                       type: "POST",
                       url: '/truckhaul/addsellerpost',
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
                                if($("#ftl_flag_set").val() =='1' && $("#ftl_order_id").val() != ''){
                                    window.location = "/consignment_pickup/"+$("#ftl_order_id").val();  
                                }else{
                                    window.location="/sellerlist";
                                }
                             
                           });
                       }
                     });
            }
            }
        });
    /******************************confirm functionality*********************************************/


    //update seller post FTL
    var sel_list_th = new Array();
    $(document).on('click', '#add_more_update_th', function() {
            if($("#truckhaul-posts-form-lines").valid()) {
                var current_row_id = $("#current_row_id").val();
                var from_location = $('#from_location').val();
                var datepicker_from_value = $('#datepicker').val();
                var datepicker_from_value = convertDateFormatForDatePickerSeller(datepicker_from_value);
                var datepicker_to_value = $('#datepicker_to_location').val();
                var datepicker_to_value = convertDateFormatForDatePickerSeller(datepicker_to_value);
                var seller_district = $('#seller_district_id').val();
                var ts_from = Date.parse(datepicker_from_value);
                var ts_to = Date.parse(datepicker_from_value);
                var from_location_identifier = $('#from_location_id').val();
                var to_location_identifier = $('#to_location_id').val();
                var load_type = $('#load_type').val();
                var vehicle_type = $('#vechile_type').val();
                var to_location = $('#to_location').val();
                var price = $('#price').val();
                var units = $('#transitdays_units').val();
                var transit = $('#transitdays').val();
                var price_numric = /^\d+(\.\d{2})?$/.test(price);
                var transit_numric =  /^[0-9]{1,3}$/.test(transit);
                var load_type_value = $("#load_type option:selected").text();
                var vehicle_type_value = $("#vechile_type option:selected").text();
                var vehicle_number = $('#vehicle_number').val();
                if (load_type_value == "Load Type (All)") {
                    load_type_value = "All";
                }
                if (vehicle_type_value == "Vehicle Type (All)") {
                    vehicle_type_value = "All";
                }
                var vechile_type_value = $("#vechile_type option:selected").text();
                var subscription_start_date_start_val = $('#subscription_start_date_start').val();
                var subscription_end_date_end_val = $('#subscription_end_date_end').val();
                var current_date_seller = $('#current_date_seller').val();
                if (to_location_identifier != '' && from_location_identifier != '' && datepicker_from_value != '' && datepicker_to_value != '' && price != '' && transit != '' && price_numric == true && transit_numric == true) {
                    if ((current_date_seller >= subscription_start_date_start_val) && (current_date_seller <= subscription_end_date_end_val) && (datepicker_from_value >= subscription_start_date_start_val) && (datepicker_from_value <= subscription_end_date_end_val) && (datepicker_to_value >= subscription_start_date_start_val) && (datepicker_to_value <= subscription_end_date_end_val)) {
                        if (to_location_identifier != '' && from_location_identifier != '' && datepicker_from_value != '' && datepicker_to_value != '' && price != '' && transit != '' && vehicle_type != '' && price_numric == true && transit_numric == true) {
                            var unique = from_location_identifier + to_location_identifier + load_type + vehicle_type+transit;

                            if ($.inArray(unique, sel_list) == -1) {
                                //sel_list.unshift(unique);
                                $.ajax({
                                    type: 'post',
                                    url: '/truckhaul/lineitemscheck',
                                    data: {
                                        'from_location': from_location_identifier,
                                        'to_location': to_location_identifier,
                                        'from_date_seller': datepicker_from_value,
                                        'to_date_seller': datepicker_to_value,
                                        'vehicle_type': vehicle_type,
                                        'load_type' : load_type,
                                        'post_item_id': current_row_id,
                                        'transit_days': transit
                                    },
                                    dataType: "html",
                                    type: 'POST',
                                    success: function (data) {
                                        if (data == '0') {
                                            //row updates
                                            var rowid = "#single_post_item_" + current_row_id;
                                            $(rowid + " .from_location_text").html(from_location);
                                            $(rowid + " .to_location_text").html(to_location);
                                            $(rowid + " .load_type_text").html(load_type_value);
                                            $(rowid + " .vehicle_type_text").html(vehicle_type_value);
                                            $(rowid + " .vehicle_number").html(vehicle_number);
                                            $(rowid + " .price_text").html(price);
                                            $(rowid + " input[name='from_location[]']").val(from_location_identifier);
                                            $(rowid + " input[name='to_location[]']").val(to_location_identifier);
                                            $(rowid + " input[name='load_type[]']").val(load_type);
                                            $(rowid + " input[name='vechile_type[]']").val(vehicle_type);
                                            $(rowid + " input[name='transitdays[]']").val(transit);
                                            $(rowid + " input[name='units[]']").val(units);
                                            $(rowid + " input[name='sellerdistrict[]']").val(seller_district);
                                            $(rowid + " input[name='price[]']").val(price);
                                            $(rowid + " input[name='vehicle_number[]']").val(vehicle_number);
                                            var id_line_itemes = $('.request_rows').children().size();
                                            if (id_line_itemes == 0) {
                                            } else {
                                                $("#datepicker").prop('disabled', true);
                                            }
                                            $("#valid_from_val").val(datepicker_from_value);
                                            $("#valid_to_val").val(datepicker_to_value);
                                            $('#from_location').val("");
                                            $('#from_location_id').val("");
                                            $('#to_location').val("");
                                            $('#to_location_id').val("");
                                            $('#vechile_type').val("");
                                            $('#load_type').val("");
                                            $('#transitdays').val("");
                                            $('#price').val("");
                                            $('.selectpicker').selectpicker('refresh');
                                            $('#add_more_update').hide();
                                            $('#vehicle_number').val("")
                                            //$('#vechile_type').trigger("change");
                                            $('#dimension').hide();


                                        } else {
                                            $("#erroralertmodal .modal-body").html("Post already exists for the specified parameters! Please modify parameters.");
                                            $("#erroralertmodal").modal({
                                                show: true
                                            });
                                        }
                                    },
                                    error: function () {
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
                    } else {
                        $("#erroralertmodal .modal-body").html("Your post valid from date is beyond your subscription date, please select valid from date within your subscription validity date");
                        $("#erroralertmodal").modal({
                            show: true
                        });

                    }
                }
            }
    });
    
    
    $(document).on('click', '#add_more_update_tl', function() {
    	
    	///alert("hello");
    	var sel_list = new Array();
        if($("#trucklease-posts-form-lines").valid()) {
        	
        	
            var current_row_id = $("#current_row_id").val();
            var from_location = $('#from_location').val();
            var datepicker_from_value = $('#datepicker_tl').val();
            var datepicker_from_value = convertDateFormatForDatePickerSeller(datepicker_from_value);
            var datepicker_to_value = $('#datepicker_to_location_tl').val();
            
            var datepicker_to_value = convertDateFormatForDatePickerSeller(datepicker_to_value);
            
            var seller_district = $('#seller_district_id').val();
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
            var check_driver_availablity = $('#check_driver_availablity').val();
            var driver_cost = $('#driver_cost').val();
            var price = $('#price').val();
            var states = $('#permitstates').val();
            var price_numric = /^\d+(\.\d{2})?$/.test(price);
            var load_type_value = $( "#load_type option:selected" ).text();
           // var load_type_ids = $( "#load_type option:selected" ).val();
            var vehicle_type_value = $( "#vechile_type option:selected" ).text();
            if (load_type_value ==  "Load Type (All)"){
                load_type_value = "All";
            }
            if (vehicle_type_value ==  "Vehicle Type (All)"){
            	vehicle_type_value = "All";
            }
            
            
            if($('#check_driver_availablity').is(':checked')) {
            	var need_diver_avil = 1;
            }else{
            	var need_diver_avil = 0;
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
            if (from_location_identifier != '' && datepicker_from_value != null && datepicker_to_value != null && from_location != '' && price != '' && price != 0 &&  load_type != '' && vehicle_type != '' && price_numric == true ) {
            	
            if((current_date_seller >= subscription_start_date_start_val) && (current_date_seller <= subscription_end_date_end_val) && (datepicker_from_value >= subscription_start_date_start_val) && (datepicker_from_value <= subscription_end_date_end_val) && (datepicker_to_value >= subscription_start_date_start_val) && (datepicker_to_value <= subscription_end_date_end_val)){
            if (from_location_identifier != '' && datepicker_from_value != '' && datepicker_to_value != '' && from_location != '' &&   price != '' && load_type != '' && vehicle_type != '' && price_numric == true) {
                var unique = from_location_identifier+vehicle_type+ts_from+ts_to;
                
              // alert($.inArray(unique,sel_list));
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
                        'post_item_id': current_row_id,
                        'load_type' : load_type
                    },
                    dataType : "html",
                    type : 'POST',
                    success : function(data) {
                                    if (data == '0') {
                                        //row updates
                                        var rowid = "#single_post_item_" + current_row_id;
                                        $(rowid + " .from_location_text").html(from_location);
                                        $(rowid + " .lease_period_text").html(minimum_lease_period);
                                        $(rowid + " .lease_term_text").html(lease_type_value);
                                        $(rowid + " .vehicle_type_text").html(vehicle_type_value);
                                        $(rowid + " .price_text").html(price);
                                        $(rowid + " input[name='from_location[]']").val(from_location_identifier);
                                        $(rowid + " input[name='vechile_type[]']").val(vehicle_type);
                                        $(rowid + " input[name='sellerdistrict[]']").val(seller_district);
                                        $(rowid + " input[name='price[]']").val(price);
                                        $(rowid + " input[name='permit_item[]']").val(states);
                                        $(rowid + " input[name='minimum_lease_period[]']").val(minimum_lease_period);
                                        $(rowid + " input[name='lease_term[]']").val(lease_type);
                                        $(rowid + " input[name='fuel_included[]']").val(fuel_need);
                                        $(rowid + " input[name='vehicle_make_model_year[]']").val(vehiclenumber);
                                        $(rowid + " input[name='driver_availability[]']").val(need_diver_avil);
                                        $(rowid + " input[name='driver_charges[]']").val(driver_cost);
                                        $(rowid + " input[name='prefered_goods[]']").val(load_type);
                                        $(rowid + " input[name='prermit_states[]']").val(states);
                                        $(rowid + " .goods_text").html(load_type_value);
                                        
                                        var id_line_itemes = $('.request_rows').children().size();
                                        if (id_line_itemes == 0) {
                                        } else {
                                            $("#datepicker").prop('disabled', true);
                                        }
                                        $("#valid_from_val").val(datepicker_from_value);
                                        $("#valid_to_val").val(datepicker_to_value);
                                        $('#from_location').val("");
                                        $('#from_location_id').val("");
                                        $('#vechile_type').val("");
                                        $('#vehiclenumber').val("");
                                        $('#load_type').val("");
                                        $('#price').val("");
                                        $('#driver_cost').val("");
                                        $('#check_driver_availablity').attr('checked', false);
                                        //$('.selectpicker').selectpicker('refresh');
                                        $('#add_more_update_tl').hide();
                                        $('select[name=LeaseTerms]').selectpicker('val', '');
                                        $('select[name=VehicleTypeMasters]').selectpicker('val', '');
                                        $('select[id=fuel_need]').selectpicker('val', '');
                                        $('#load_type').multiselect('refresh');
                                        $('#permitstates').multiselect('refresh');

                                    } else {
                                        $("#erroralertmodal .modal-body").html("Post already exists for the specified parameters! Please modify parameters.");
                                        $("#erroralertmodal").modal({
                                            show: true
                                        });
                                    }
                                },
                                error: function () {
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
                } else {
                    $("#erroralertmodal .modal-body").html("Your post valid from date is beyond your subscription date, please select valid from date within your subscription validity date");
                    $("#erroralertmodal").modal({
                        show: true
                    });

                }
            }
        }
});


/**** End : Truck Haul Post Validation *****/
    $('#add_more_ptl').on('click', function() {
        $("#posts-form-lines_ptl").valid();
    });
     jQuery.validator.addMethod("decimalvalidation", function(value, element) {
    	 return this.optional(element) || /^\d+(\.\d{1,4})?$/i.test(value);
   }, "Please enter only digits, with max 4 decimals");
   	
     jQuery.validator.addMethod("creditperod", function(value, element) {
    	 return this.optional(element) || /^0*[0-9]{1,3}$/i.test(value);
   }, "Credit Period possible value is 999");
     
    $("#posts-form-lines_ptl ").validate({
    	ignore: "input[type='text']:hidden",
    	
        rules : {
            "valid_from" : {
                required : true,
            },
            "option_wise_ptl" : {
                required : true,
            },
            "post_delivery_type" : {
                required : true,
            },
            "courier_types" : {
                required : true,
            },
            "valid_to" : {
                required : true,
            },
            "from_location" : {
                required : true,
            },
            "from_location_id" : {
                required : true,
            },
            "to_location" : {
                required : true,
            },
            "to_location_id" : {
                required : true,
            },
            "transitdays" : {
                required : true,
                digits: true,
                transitvalidation:true,
                rangelength: [0,3]
            },
            "price" : {
                required : true,
                number: true,
                rateperkg:true,
            },
        },
        errorPlacement: function(error, element) {
        	$(element).parent('div').parent('div').after(error);
        },
        messages : {
            "valid_from" : {
                required : "Valid From date is required",
            },
            "valid_to" : {
                required : "Valid To date is required",
            },
            "from_location" : {
                required : "",
            },
            "from_location_id" : {
                required : 
	                function() {
	                    var ZLValue = $("#zone_or_location").val();
	                    var serviceValue = $("#serviceid").val();
	                    if(serviceValue == 8){
	                    	return "From Airport is required";
	                    }
	                    else if	(serviceValue == 9){
	                    	return "From Ocean is required";
	                    }
	                    else{
		                    if (ZLValue == 1) {
		                        return "From Zone is required";
		                    } else {
		                        return "From Pincode is required";
		                    }
	                    }
	                }
            },
            "to_location" : {
                required : "",
            },
            "to_location_id" : {
                required : 
	                function() {
	                    var ZLValue = $("#zone_or_location").val();
	                    var serviceValue = $("#serviceid").val();
	                    var postDelivery = $("#post_delivery_type").val();
	                   
	                    if(serviceValue == 8){
	                    	return "To Airport is required";
	                    }
	                    else if	(serviceValue == 9){
	                    	return "To Ocean is required";
	                    }
	                    else{
		                    if (ZLValue == 1) {
		                    	if(postDelivery == 1)
		                    		return "To Zone is required";
		                    	else
		                    		return "To Country is required";
		                    } else {
		                    	if(postDelivery == 1)
		                    		return "To Pincode is required";
		                    	else
		                    		return "To Country is required";
		                    }
	                    }
	                }
            },
            "transitdays" : {
                required : "Transit days is required",
            },
            "price" : {
                required : "Price is required",
            }
        },
        submitHandler : function(form) {
            form.submit();
        }
    });
    $("#posts-form_ptl").validate({
    	ignore: [],
        rules : {
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
            "kgpercft" : {
            	required : { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	},
            	number: true,
            	fourbyfourvalidations: { 
            		depends: function(element) {
            			if($('#serviceid').val() == 7 || $('#serviceid').val() == 8){
            				if ($('#sellerpoststatus').val() == 1){
    		            		return true;
    		            	}else{
    		            		return false;
    		            	}
            			}
	            		
            		}
            	},
            	fourbythreevalidations: { 
            		depends: function(element) {
            			if($('#serviceid').val() == 7 || $('#serviceid').val() == 8){
            			}else{
            				if ($('#sellerpoststatus').val() == 1){
    		            		return true;
    		            	}else{
    		            		return false;
    		            	}
            			}
	            		
            		}
            	},
            },
            "fuel_surcharge_text" : {
            	number :true,
            	required : { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	},
            	fourbytwovalidations: { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	}
            },
            "conversion_factor_text" : {
            	number :true,
            	required : { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	},
            	fourbythreevalidations: { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	},
            },
            "max_weight_accepted_text" : {
            	required : { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	},
            	fourbythreevalidations: { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	}
            },
            "incremental_weight_text" : {
            	required : { 
            		depends: function(element) {
            			if ($('#sellerpoststatus').val() == 1 && $('#check_max_weight').is(':checked')){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	},
            	nabythreevalidations: { 
            		depends: function(element) {
            			if ($('#sellerpoststatus').val() == 1 && $('#check_max_weight').is(':checked')){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	}
            },
            "rate_per_increment_text" : {
            	number :true,
            	required : { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1 && $('#check_max_weight').is(':checked')){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	},
            	rateperinc: { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1 && $('#check_max_weight').is(':checked')){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	}
            },
            "check_on_delivery_text" : {
            	number :true,
            	required : { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	},
            	rateperinc: { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	}
            },
            "freight_collect_text" : {
            	number :true,
            	required : { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	},
            	pricevalidation:{ 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	},
            },
            "arc_text" : {
            	number :true,
            	required : { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	},
            	arcvalidations: { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	}
            },
            "maximum_value_text" : {
            	number :true,
            	required : { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	},
            	rateperinc: { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	}
            },
            "pickup" : {
                required : {
                        depends: function(element) {
                            if ($('#sellerpoststatus').val() == 1){
                                return true;
                            }else{
                                return false;
                            }

	            	}
                },
                number: true,
                fourbythreevalidations: { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	}
            },
            "delivery" : {
                required : {
                        depends: function(element) {
                            if ($('#sellerpoststatus').val() == 1){
                                return true;
                            }else{
                                return false;
                            }

	            	}
                },
                number: true,
                fourbythreevalidations: { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	}
            },
            "oda" : {
                required : {
                        depends: function(element) {
                            if ($('#sellerpoststatus').val() == 1){
                                return true;
                            }else{
                                return false;
                            }

	            	}
                },
                number: true,
                fourbythreevalidations: { 
            		depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	}
            },
            "low_price" : {
                required : {
                        depends: function(element) {
                            if ($('#sellerpoststatus').val() == 1){
                                return true;
                            }else{
                                return false;
                            }

	            	}
                },
				decimalvalidation: true
            },
            "high_price" : {
                required : {
                        depends: function(element) {
                            if ($('#sellerpoststatus').val() == 1){
                                return true;
                            }else{
                                return false;
                            }

	            	}
                },
				decimalvalidation: true
            },
            "actual_price" : {
            	 number: true,
                required : {
                        depends: function(element) {
                            if ($('#sellerpoststatus').val() == 1){
                                return true;
                            }else{
                                return false;
                            }

	            	}
                },
                pricevalidation: {
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
        		 //$(element).closest('div').append(error);
        		 //$(element).parent().parent().append(error);
        		 $(element).parent().parent().append(error);
        },
        messages : {
            "agree" : {
                required : "Terms & Conditions is required",
            },
            "kgpercft": {
                required: function() {
                    if($('#serviceid').val() == 7 || $('#serviceid').val() == 8){
                            return "Kg per CCM is required"
                    } else if($('#serviceid').val() == 9){
                         return "Kg per CBM is required"
                    }   else{
                            return "Kg per CFT is required";
                    }
                }
            },
            "accept_payment_ptl" : {
                required : "payment mode is required",
            },
            "tracking" : {
                required : "Tracking is required",
            },
            "terms_condtion_types1" : {
                required : "Cancellation charges is required",
            },
            "delivery" : {
                required : "Delivery is required",
            },
            "terms_condtion_types2" : {
                required : "Other charges is required",
            },
            "oda" : {
                required : "ODA is required",
            },
            "pickup" : {
                required : "Pickup is required",
            },
            "fuel_surcharge_text" : {
                required : "Fuel surcharge is required",
            },
            "check_on_delivery_text" : {
                required : "Delivery charges is required",
            },
            "freight_collect_text" : {
                required : "Freight charges is required",
            },
            "arc_text" : {
                required : "ARC is required",
            },
            "maximum_value_text" : {
                required : "Maximum value is required",
            },
            "tracking" : {
                required : "Tracking mode is required",
            },
            "accept_payment_ptl[]" : {
                required : "Payment mode is required",
            },
            
            
        },
        submitHandler : function(form) {
        	if($('#serviceid').val() == 21){
        	var index_value = $('#price_slap_hidden_value').val();
       		var max_weight_accepted = $('#max_weight_accepted').val();
       		if(index_value == 0){
	       		var high_salb = parseFloat($('#high_price').val());
	       		var high_slab_focus = '#high_price';
       		}else{
	       		var high_salb = parseFloat($('#high_weight_slab_'+index_value).val());	
	       		var high_slab_focus = '#high_weight_slab_'+index_value;
       		}
       		if(high_salb > max_weight_accepted){
       			$("#erroralertmodal .modal-body").html("High slab value should not be grtear than max weight.");
    	        $("#erroralertmodal").modal({
    	            show: true
    	        }).one('click','.ok-btn',function (e){
	            	 $(high_slab_focus).focus();
	               });
    			return false;
       		}else{
       			form.submit();
       			}
       		}else{
       			form.submit();
       		}
        },
    });
    $("#posts-form-update").validate({
    	ignore: [],
        rules : {
            "valid_from" : {
                required : true,
            },
            "valid_to" : {
                required : true,
            },
            "from_location" : {
                required : true,
            },
            "to_location" : {
                required : true,
            },
            "LoadTypeMasters" : {
                required : true,
            },
            "VehicleTypeMasters" : {
                required : true,
            },
            "transitdays" : {
                required : true,
                digits: true
            },
            "price" : {
                required : true,
                digits: true
            },

        },
        errorPlacement: function(error, element) {
        	$(element).parent('div').after(error);
        },
        submitHandler : function(form) {
            form.submit();
        },
    });

    $("#sellers-posts-buyers").validate({
    	ignore: "input[type='text']:hidden",
        rules : {
            "dispatch_date" : {
                required : true,
            },
            "from_city_id" : {
                required : true,
            },
            "from_location" : {
                required : true,
            },
            "to_location" : {
                required : true,
            },
            "to_city_id" : {
                required : true,
            },
            "qty" : {
               number: true,
               lessThanEqualthousand: {
	            	depends: function(element) {
	            		if ($('#qty').val() != ''){
		            		return true;
		            	}else{
		            		return false;
		            	}

	            	}
                },
            },
            "lkp_vehicle_type_id" : {
                required : true,
            },
            "lkp_load_type_id" : {
                required : true,
            }
        },
        errorPlacement: function(error, element) {
        	$(element).parent().parent().after(error);
        },
        messages : {
            "dispatch_date" : {
                required : "Dispatch Date is required.",
            },
            "from_location" : {
                required : "",
            },
            "from_city_id" : {
                required : "From Location is required.",
            },
            "to_location" : {
                required : "",
            },
            /*"to_city_id" : {
                required : "This is required field",
            }*/
            "to_city_id": {
                required: function() {
                    if ($("#to_location").val() == "") {
                        return "To Location is required.";
                    } else  {
                        return "Please enter valid location";
                    }
                }
            }
        },
        submitHandler : function(form) {
            form.submit();
        },
    });

    //truck lease validations
    $("#sellers-posts-buyers-tl").validate({
        ignore: "input[type='text']:hidden",
        rules : {
            "dispatch_date" : {
                required : true,
            },
            "delivery_date" : {
                required : true,
            },
            "from_city_id" : {
                required : true,
            },
            "from_location" : {
                required : true,
            },
            "to_location" : {
                required : true,
            },
            "to_city_id" : {
                required : true,
            },
            //"qty" : {
            //  required : true,
            // number: true,
            // floatvalidation: true,
            //},
            "lkp_vehicle_type_id" : {
                required : true,
            },
            "lkp_trucklease_lease_term_id" : {
                required : true,
            }
        },
        errorPlacement: function(error, element) {
            $(element).parent().parent().after(error);
        },
        messages : {
            "dispatch_date" : {
                required : "This field is required.",
            },
            "from_location" : {
                required : "",
            },
            "from_city_id" : {
                required : "This field is required.",
            },
            "to_location" : {
                required : "",
            },
            /*"to_city_id" : {
             required : "This is required field",
             }*/
            "to_city_id": {
                required: function() {
                    if ($("#to_location").val() == "") {
                        return "This field is required.";
                    } else  {
                        return "Please enter valid location";
                    }
                }
            }
        },
        submitHandler : function(form) {
            form.submit();
        },
    });
    
    $('#testDiv4').slimScroll({
        alwaysVisible: true
    });
    $('#demo-input').tokenize({
            datas: "/buyerlist",
            onAddToken:function(value, text, e){
                var tokens=$("#demo-input").val();
                    $("#demo-input").val(tokens+","+value);
			},
			onRemoveToken:function(value, text, e){
				var tokens = $("#demo-input").val();
				tokens = tokens.replace(','+value,'');
				$("#demo-input").val(tokens);
			},
	});
    $('#demo-input-ocen').tokenize({
        datas: "/buyerlist",
        onAddToken:function(value, text, e){
            var tokens=$("#demo-input-ocen").val();
                $("#demo-input-ocen").val(tokens+","+value);
		},
		onRemoveToken:function(value, text, e){
			var tokens = $("#demo-input-ocen").val();
			tokens = tokens.replace(','+value,'');
			$("#demo-input-ocen").val(tokens);
		},
});
    $('#demo_input_select').tokenize({
        datas: "/buyerlist",
        onAddToken:function(value, text, e){
            var tokens=$("#demo_input_select_hidden").val();
                $("#demo_input_select_hidden").val(tokens+","+value);
		},
		onRemoveToken:function(value, text, e){
			var tokens = $("#demo_input_select_hidden").val();
			tokens = tokens.replace(','+value,'');
			$("#demo_input_select_hidden").val(tokens);
		},
    });
    
	$( "#from_location" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#from_location_id').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
	
	$( "#from_airport" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#from_airport_id').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
	
	$( "#to_airport" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#to_airport_id').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
	
	
	$( "#from_occean" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#from_occean_id').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
	
	$( "#to_occean" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#to_occean_id').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
	
	$( "#term_from_location_pincode" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#term_from_location_pincode_id').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
	
	$( "#intra_from_location" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#lkp_city_id').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
	$( "#term_to_location_pincode" ).keyup(function(e) {
		if (e.which !== 13) {
			var to_id_hidden = $('#term_to_location_pincode_id').val("");
			if (to_id_hidden != '') {
				//$(".to_location_class label.error").html("");
			}
		}
	});
        
        $( "#from_intra_location" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#from_location_id').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
        
          $( "#to_intra_location" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#to_location_id').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
	
	$( "#to_location" ).keyup(function(e) {
		if (e.which !== 13) {
			var to_id_hidden = $('#to_location_id').val("");
			if (to_id_hidden != '') {
				//$(".to_location_class label.error").html("");
			}
		}
	});
	
	$( "#from_location_ptl" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#from_location_id').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});

	$( "#to_location_ptl" ).keyup(function(e) {
		if (e.which !== 13) {
			var to_id_hidden = $('#to_location_id').val("");
			if (to_id_hidden != '') {
				//$(".to_location_class label.error").html("");
			}
		}
	});
	
	$( "#from_location_ptl_search" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#from_location_id').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});

	$( "#to_location_ptl_search" ).keyup(function(e) {
		if (e.which !== 13) {
			var to_id_hidden = $('#to_location_id').val("");
			if (to_id_hidden != '') {
				//$(".to_location_class label.error").html("");
			}
		}
	});
	
	$( "#term_from_location_ptl_search" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden = $('#term_from_location_id').val("");
			var from_id_hidden = $('#term_from_location1_id').val("");
			if (from_id_hidden != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});
	

	$( "#term_to_location_ptl_search" ).keyup(function(e) {
		if (e.which !== 13) {
			var to_id_hidden = $('#term_to_location_id').val("");
			if (to_id_hidden != '') {
				//$(".to_location_class label.error").html("");
			}
		}
	});

	
	$(document).on('click', '#documents', function() {
		var courier_types_val = 1;
		 $('#courier_types').val(courier_types_val);
		 $('#documents_display').hide();
		 $('#documents_display_courier').hide();
		 $('#parcel_hide').hide();
	});
	$(document).on('click', '#parcel', function() {
		var courier_types_val = 2;
		 $('#courier_types').val(courier_types_val);
		 $('#documents_display').show();
		 $('#documents_display_courier').show();
		 $('#parcel_hide').show();
	});
	
	$(document).on('click', '#term_documents', function() {
		var courier_types_val = 1;
		 $('#term_courier_types').val(courier_types_val);
	});
	$(document).on('click', '#term_parcel', function() {
		var courier_types_val = 2;
		 $('#term_courier_types').val(courier_types_val);
	});

	$(document).on('click', '#domestic', function() {
		var domesticor_or_international = 1;
		 $('#post_delivery_type').val(domesticor_or_international);
		 
		 var zone_or_location_val_courier = $('#zone_or_location').val();
			if(zone_or_location_val_courier == 1){
				$('#to_location_ptl_search').attr("placeholder", "To Zone*");
		        $('#to_location_ptl').attr("placeholder", "To Zone*");
			}else if(zone_or_location_val_courier == 2){
				$('#to_location_ptl_search').attr("placeholder", "To pin code*");
		        $('#to_location_ptl').attr("placeholder", "To pin code*");
			}else{
				$('#to_location_ptl_search').attr("placeholder", "To pin code*");
		        $('#ptlToLocation').attr("placeholder", "To pin code*");
                $('#ptlToLocation').val('');
                
			}
			$('#to_location_ptl_search').addClass("numericvalidation_autopop maxlimitsix_lmtVal");
			$('#ptlToLocation').addClass("numericvalidation_autopop maxlimitsix_lmtVal");
		 
	});
	$(document).on('click', '#international', function() {
        var domesticor_or_international = 2;
		 $('#post_delivery_type').val(domesticor_or_international);
		 var zone_or_location_val_courier = $('#zone_or_location').val();
			if(zone_or_location_val_courier == 1){
				$('#to_location_ptl_search').attr("placeholder", "To Country*");
		        $('#to_location_ptl').attr("placeholder", "To Country*");
			}else if(zone_or_location_val_courier == 2){
				$('#to_location_ptl_search').attr("placeholder", "To Country*");
		        $('#to_location_ptl').attr("placeholder", "To Country*");
			}else{
				$('#to_location_ptl_search').attr("placeholder", "To Country*");
		        $('#ptlToLocation').attr("placeholder", "To Country*");
                $('#ptlToLocation').val('');
                $('#ptlToLocation').removeClass("numericvalidation_autopop maxlimitsix_lmtVal");
                $('#to_location_ptl_search').removeClass("numericvalidation_autopop maxlimitsix_lmtVal");
                
			}
			//$('#ptlToLocation').removeClass("numericvalidation_autopop maxlimitsix_lmtVal");
            $('#to_location_ptl_search').removeClass("numericvalidation_autopop maxlimitsix_lmtVal");
			if(domesticor_or_international == 2){
				$('#to_location_ptl').removeClass("numericvalidation_autopop");
			}
			
	});
	
	
	//Term search validations
	$(document).on('click', '#term_domestic', function() {
		var domesticor_or_international = 1;
		 $('#term_post_delivery_type').val(domesticor_or_international);
		 
		 var zone_or_location_val_courier = $('#term_zone_or_location').val();
		 	if(zone_or_location_val_courier == 1){
				$('#term_to_location_ptl_search').attr("placeholder", "To Zone*");
		        $('#to_location_ptl').attr("placeholder", "To Zone*");
		        $('#term_to_location_ptl_search').val('');
		        $('#to_location_ptl').val('');
		        $('#term_to_location_id').val('');
			}else if(zone_or_location_val_courier == 2){
				$('#term_to_location_ptl_search').attr("placeholder", "To pin code*");
		        $('#to_location_ptl').attr("placeholder", "To pin code*");
		        $('#term_to_location_ptl_search').val('');
		        $('#to_location_ptl').val('');
		        $('#term_to_location_id').val('');
			}else{
				$('#term_to_location_ptl_search').attr("placeholder", "To pin code*");
		        $('#ptlToLocation').attr("placeholder", "To pin code*");
		        $('#term_to_location_ptl_search').val('');
		        $('#ptlToLocation').val('');
		        $('#term_to_location_id').val('');
			}
		 
	});
	$(document).on('click', '#term_international', function() {
		var domesticor_or_international = 2;
		 $('#term_post_delivery_type').val(domesticor_or_international);
		 var zone_or_location_val_courier = $('#zone_or_location').val();
			if(zone_or_location_val_courier == 1){
				$('#term_to_location_ptl_search').attr("placeholder", "To Country*");
		        $('#to_location_ptl').attr("placeholder", "To Country*");
		        $('#term_to_location_ptl_search').val('');
		        $('#to_location_ptl').val('');
		        $('#term_to_location_id').val('');
			}else if(zone_or_location_val_courier == 2){
				$('#term_to_location_ptl_search').attr("placeholder", "To Country*");
		        $('#to_location_ptl').attr("placeholder", "To Country*");
		        $('#term_to_location_ptl_search').val('');
		        $('#to_location_ptl').val('');
		        $('#term_to_location_id').val('');
			}else{
				$('#term_to_location_ptl_search').attr("placeholder", "To Country*");
		        $('#ptlToLocation').attr("placeholder", "To Country*");
		        $('#term_to_location_ptl_search').val('');
		        $('#ptlToLocation').val('');
		        $('#term_to_location_id').val('');
			}
	});
	

	$(document).on('click', '#term_domestic', function() {
		var domesticor_or_international = 1;
		$('#term_post_delivery_type').val(domesticor_or_international);
		$('#term_to_location_pincode').attr("placeholder", "To Pincode *");
        $('#term_to_location_pincode').val('');
        $('#term_to_location_pincode').addClass("numericvalidation_autopop maxlimitsix_lmtVal");
        $('#term_to_location_ptl_search').addClass("numericvalidation_autopop maxlimitsix_lmtVal");

	});
	
	$(document).on('click', '#term_international', function() {
		var domesticor_or_international = 2;
		$('#term_post_delivery_type').val(domesticor_or_international);
		$('#term_to_location_pincode').attr("placeholder", "To Country*");
        $('#term_to_location_pincode').val('');
        $('#term_to_location_pincode').removeClass("numericvalidation_autopop maxlimitsix_lmtVal");
        $('#term_to_location_ptl_search').removeClass("numericvalidation_autopop maxlimitsix_lmtVal");
	});
	
	/******************************From Location starts FTL*********************************************/
	$(document).on('focus click keyup keypress blur change', '#from_location', function() {
		$( "#from_location" ).autocomplete({
	            source: "/autocomplete?fromlocation="+$('#to_location_id').val(),
	            minLength: 1,
	            select: function(event, ui) {
					$('#from_location').val(ui.item.value);
	                $('#from_location_id').val(ui.item.id);
	                $('#seller_district_id').val(ui.item.dist_id);
					$(this).closest("form").validate().element($('#from_location_id'));
                    /*Need to add this below class to every autocomplete: Shriram */
                    $('#from_location').addClass("clsAutoDisable");
	            }
		});
	});
	
	$(document).on('focus click keyup keypress blur change', '#to_location', function() {
            $( "#to_location" ).autocomplete({
                source: "/autocomplete?fromlocation="+$('#from_location_id').val(),
                minLength: 1,
                select: function(event, ui) {
                    $('#to_location').val(ui.item.value);
                    $('#to_location_id').val(ui.item.id);
					$(this).closest("form").validate().element($('#to_location_id'));
                    /*Need to add this below class to every autocomplete: Shriram */
                    $("#to_location").addClass("clsAutoDisable");
                },
                /*response: function(event,ui) {
                    if (ui.content.length == 1) {
                        ui.item = ui.content[0];
                        $(this).data('ui-autocomplete')._trigger('select', 'autocompleteselect', ui);
                        $(this).autocomplete('close');
                    }
                }*/
            });
	});
	/******************************From Location ends FTL*********************************************/
	
	/******************************From Location starts intracity*********************************************/
	$(document).on('focus click keyup keypress blur change', '#intra_from_location', function() {
		$( "#intra_from_location" ).autocomplete({
	            source: "/autocomplete?fromlocation="+$('#to_location_id').val(),
	            minLength: 1,
	            select: function(event, ui) {
					$('#intra_from_location').val(ui.item.value);
	                $('#lkp_city_id').val(ui.item.id);
	                $('#seller_district_id').val(ui.item.dist_id);
					$(this).closest("form").validate().element($('#lkp_city_id'));
                    /*Need to add this below class to every autocomplete: Shriram */
                    $("#intra_from_location").addClass("clsAutoDisable");
	            }
		});
	});
	/******************************From Location ends intracity*********************************************/
	
	/******************************From Location starts AIR International*********************************************/
	$(document).on('focus click keyup keypress blur change', '#from_airport', function() {
		$( "#from_airport" ).autocomplete({
	            source: "/autocomplete?country=india&fromlocation="+$('#to_airport_id').val(),
	            minLength: 1,
	            select: function(event, ui) {
	                $('#from_airport').val(ui.item.value);
	                $('#from_airport_id').val(ui.item.id);
					$(this).closest("form").validate().element($('#from_airport_id'));
                    /*Need to add this below class to every autocomplete: Shriram */
                    $("#from_airport").addClass("clsAutoDisable");
	            }
		});
	});
	
	$(document).on('focus click keyup keypress blur change', '#to_airport', function() {
            $( "#to_airport" ).autocomplete({
                source: "/autocomplete?fromlocation="+$('#from_airport_id').val(),
                minLength: 1,
                select: function(event, ui) {
                    $('#to_airport').val(ui.item.value);
                    $('#to_airport_id').val(ui.item.id);
					$(this).closest("form").validate().element($('#to_airport_id'));
                    /*Need to add this below class to every autocomplete: Shriram */
                    $("#to_airport").addClass("clsAutoDisable");
                }
            });
	});
	/******************************From Location ends AIR International*********************************************/
	
	/******************************From Location starts AIR International*********************************************/
	$(document).on('focus click keyup keypress blur change', '#from_occean', function() {
		$( "#from_occean" ).autocomplete({
	            source: "/autocomplete?fromlocation="+$('#to_occean_id').val(),
	            minLength: 1,
	            select: function(event, ui) {
	                $('#from_occean').val(ui.item.value);
	                $('#from_occean_id').val(ui.item.id);
					$(this).closest("form").validate().element($('#from_occean_id'));
                    /*Need to add this below class to every autocomplete: Shriram */
                    $("#from_occean").addClass("clsAutoDisable");
	            }
		});
	});
	
	$(document).on('focus click keyup keypress blur change', '#to_occean', function() {
            $( "#to_occean" ).autocomplete({
                source: "/autocomplete?fromlocation="+$('#from_occean_id').val(),
                minLength: 1,
                select: function(event, ui) {
                    $('#to_occean').val(ui.item.value);
                    $('#to_occean_id').val(ui.item.id);
					$(this).closest("form").validate().element($('#to_occean_id'));
                    /*Need to add this below class to every autocomplete: Shriram */
                    $("#to_occean").addClass("clsAutoDisable");
                }
            });
	});
	/******************************From Location ends Ocean*********************************************/
	
	/******************************From Location starts for ptl*********************************************/
	//if(location.pathname == "/ptl/createsellerpost"){
		//zonelocation();
	//}
	if($('#zone_or_location').val() == 1) {
	$("#post-private").prop('disabled', true);
	}
	
	function zonelocation(){

	if ($("#zone_wise_ptl").is(":checked")) {
			$('#from_location_ptl').val("");
			$('#to_location_ptl').val("");
			$('#from_location_id').val("");
			$('#to_location_id').val("");
			$('#to_location_ptl_search').val("");
			$('#from_location_ptl_search').val("");
			$("#post-private").prop('disabled', true);
			$('.Tokenize').hide();
			$("#post-public").prop( "checked", true );
			$("#post-private").prop( "checked", false );
	        var zone_location_id = 1;
	        $('#zone_or_location').val(zone_location_id);
	        $('#from_location_ptl').attr("placeholder", "From Zone*");
	        $('#from_location_ptl_search').attr("placeholder", "From Zone*");

	        // add alphanumeric validation
	        $('#from_location_ptl').addClass('alphanumeric_withSpace');
	        $('#from_location_ptl_search').addClass('alphanumeric_withSpace');
	        $('#from_location_ptl').attr('maxlength','10');
	        $('#from_location_ptl_search').attr('maxlength','10');
	        
	        // remove maxlimit and numeric auto popup validation
	        $('#from_location_ptl').removeClass('numericvalidation_autopop maxlimitsix_lmtVal');
	        $('#from_location_ptl_search').removeClass('numericvalidation_autopop maxlimitsix_lmtVal');

	        
	        var service_courier_del = $('#serviceid').val();
			if(service_courier_del == 21){
			var post_delivery_type_val = $('#post_delivery_type').val();
			if(post_delivery_type_val == 1){
				$('#to_location_ptl_search').attr("placeholder", "To Zone*");
		        $('#to_location_ptl').attr("placeholder", "To Zone*");
                $('#to_location_ptl').attr('maxlength',10);
                $('#to_location_ptl_search').attr('maxlength',10);
			}else{
				$('#to_location_ptl_search').attr("placeholder", "To Country*");
		        $('#to_location_ptl').attr("placeholder", "To Country*");
			}
	        
			}else{
				$('#to_location_ptl_search').attr("placeholder", "To Zone*");
		        $('#to_location_ptl').attr("placeholder", "To Zone*");

                $('#to_location_ptl').attr('maxlength',10);
                $('#to_location_ptl_search').attr('maxlength',10);
		        
			}
			 // add alphanumeric validation
	        $('#to_location_ptl').addClass('alphanumeric_withSpace');
	        $('#to_location_ptl_search').addClass('alphanumeric_withSpace');
	        
	        // remove maxlimit and numeric auto popup validation
	        $('#to_location_ptl').removeClass('numericvalidation_autopop maxlimitsix_lmtVal');
	        $('#to_location_ptl_search').removeClass('numericvalidation_autopop maxlimitsix_lmtVal');
	        
	        }
	        else{
	        $('#from_location_ptl').val("");
			$('#to_location_ptl').val("");
			$('#from_location_id').val("");
			$('#to_location_id').val("");
			$('#to_location_ptl_search').val("");
			$('#from_location_ptl_search').val("");
			$("#post-private").prop('disabled', false);
	        var zone_location_id = 2;
	        $('#zone_or_location').val(zone_location_id);
	        $('#from_location_ptl').attr("placeholder", "From pin code*");
	        $('#from_location_ptl_search').attr("placeholder", "From pin code*");

	        // remove alphanumeric validation
	        $('#from_location_ptl').removeClass('alphanumeric_withSpace');
	        $('#from_location_ptl_search').removeClass('alphanumeric_withSpace');
	        $('#from_location_ptl').removeAttr('maxlength');
	        $('#from_location_ptl_search').removeAttr('maxlength');
	        
	        // Add maxlimit and numeric auto popup validation
	        $('#from_location_ptl').addClass('numericvalidation_autopop maxlimitsix_lmtVal');
	        $('#from_location_ptl_search').addClass('numericvalidation_autopop maxlimitsix_lmtVal');
	        
	        var service_courier_del = $('#serviceid').val();
			if(service_courier_del == 21){
			var post_delivery_type_val = $('#post_delivery_type').val();
			if(post_delivery_type_val == 1){
				$('#to_location_ptl_search').attr("placeholder", "To pin code*");
		        $('#to_location_ptl').attr("placeholder", "To pin code*");
                $('#to_location_ptl_search').addClass('numericvalidation_autopop maxlimitsix_lmtVal');
			}else{
				$('#to_location_ptl_search').attr("placeholder", "To Country*");
		        $('#to_location_ptl').attr("placeholder", "To Country*");
			}
	        
			}else{
			$('#to_location_ptl_search').attr("placeholder", "To pin code*");
		    $('#to_location_ptl').attr("placeholder", "To pin code*");	
			}
	        // remove alphanumeric validation
	        $('#to_location_ptl').removeClass('alphanumeric_withSpace');
	        $('#to_location_ptl_search').removeClass('alphanumeric_withSpace');
	        $('#to_location_ptl').removeAttr('maxlength');
	        $('#to_location_ptl_search').removeAttr('maxlength');
	        
	        // Add maxlimit and numeric auto popup validation
	        $('#to_location_ptl').addClass('numericvalidation_autopop maxlimitsix_lmtVal');
	        //$('#to_location_ptl_search').addClass('numericvalidation_autopop maxlimitsix_lmtVal');

	        }
	return 1;
	}
	
	$(document).on('click', '#zone_wise_ptl', function() {
		var line_itemes__ptl_check = $('.request_rows_ptl').children().size();
		var zone_or_location = $('#zone_or_location').val();
		
		if (zone_or_location == 2) {
			if(line_itemes__ptl_check !=0){
				if (confirm("All the data entered will be lost. Click Ok to continue, Cancel to cancel operation") == true) {
					$("#datepicker").removeAttr('disabled').val('');
					$("#datepicker_to_location").removeAttr('disabled').val('');
					var service_courier_del = $('#serviceid').val();
					if(service_courier_del == 21){
					$("#domestic").removeAttr('disabled').val('');
					$("#international").removeAttr('disabled').val('');
					}
					window.location.assign(location.href)
				}else{
					return false;
				}
			}
		}
	});
	$(document).on('click', '#location_wise_ptl', function() {
		var line_itemes__ptl_check = $('.request_rows_ptl').children().size();
		var zone_or_location = $('#zone_or_location').val();
		
		if (zone_or_location == 1) {
			if(line_itemes__ptl_check !=0){
				if (confirm("All the data entered will be lost. Click Ok to continue, Cancel to cancel operation") == true) {
					$("#datepicker").removeAttr('disabled').val('');
					$("#datepicker_to_location").removeAttr('disabled').val('');
					var service_courier_del = $('#serviceid').val();
					if(service_courier_del == 21){
					$("#domestic").removeAttr('disabled').val('');
					$("#international").removeAttr('disabled').val('');
					}
					window.location.assign(location.href)
					}else{
					return false;
				}
			}
		}
	});
	
	$("#zone_wise_ptl, #location_wise_ptl").click(function(){
		var line_itemes__ptl_check = $('.request_rows_ptl').children().size();
		if(line_itemes__ptl_check ==0){
	    	zonelocation();
		}
	});
	
    $(document).on('focus click keyup keypress blur change', '#from_location_ptl', function() {
    	if ($("#zone_wise_ptl").is(":checked")) {
            var zone_location_id = 1;
            }
            else{
            var zone_location_id = 2;
            }
    	var service_courier = $('#serviceid').val();
    	if (service_courier == 21){
    		var service_courier_url = "/ptlZoneAutocompleteCourier?ptlFromLocation="+$('#to_location_id').val()+"&zone_location_id_value="+ $('#zone_or_location').val()+"&courier_delivery_type="+ $('#post_delivery_type').val()+"&from="+1;
    	}else{
    		var service_courier_url = "/ptlZoneAutocomplete?ptlFromLocation="+$('#to_location_id').val()+"&zone_location_id_value="+ $('#zone_or_location').val();
    	}
    	
    	
        $( "#from_location_ptl" ).autocomplete({
            source: service_courier_url,
            minLength: 1,
            select: function(event, ui) {
                $('#from_location_ptl').val(ui.item.value);
                $('#from_location_id').val(ui.item.id);
				$(this).closest("form").validate().element($('#from_location_id'));
				if(zone_location_id == 2){
					updatetransit();
				}
                /*Need to add this below class to every autocomplete: Shriram */
                $("#from_location_ptl").addClass("clsAutoDisable");
            }
        });
    });
    $(document).on('focus click keyup keypress blur change', '#to_location_ptl', function() {
		if ($("#zone_wise_ptl").is(":checked")) {
			var zone_location_id = 1;
		}
		else{
			var zone_location_id = 2;
		}
		var service_courier = $('#serviceid').val();
    	if (service_courier == 21){
    		var service_courier_url_to = "/ptlZoneAutocompleteCourier?ptlFromLocation="+$('#from_location_id').val()+"&zone_location_id_value="+ $('#zone_or_location').val()+"&courier_delivery_type="+ $('#post_delivery_type').val()+"&from="+2;
    	}else{
    		var service_courier_url_to = "/ptlZoneAutocomplete?ptlFromLocation="+$('#from_location_id').val()+"&zone_location_id_value="+ $('#zone_or_location').val();
    	}
        $( "#to_location_ptl" ).autocomplete({
            source: service_courier_url_to,
            minLength: 1,
            select: function(event, ui) {
                $('#to_location_ptl').val(ui.item.value);
                $('#to_location_id').val(ui.item.id);
				$(this).closest("form").validate().element($('#to_location_id'));
				if(zone_location_id == 2){
					updatetransit();
				}
                /*Need to add this below class to every autocomplete: Shriram */
                $("#to_location_ptl").addClass("clsAutoDisable");
            }
        });
    });

	//$(document).on('blur', '#to_location_ptl,#from_location_ptl', function() {
	function updatetransit(){
		var fromlocation = $('#from_location_id').val();
		var to_location = $('#to_location_id').val();
		if(fromlocation != "" && to_location != ""){
			$.ajax({
				url: '/ptlTransitAutofill',
				type: "get",
				data: {'fromlocation': fromlocation, 'to_location': to_location},
				success: function(data){
					$("#transitdays_ptl").val(data);
				},
				error : function(request, status, error) {
					$("#transitdays_ptl").val('');
				},
			});
		}
	}
	//);
    
    
    $(document).on('focus click keyup keypress blur change', '#from_location_ptl_search', function() {
    	if ($("#zone_wise_ptl").is(":checked")) {
            var zone_location_id = 1;
        }
        else{
        	var zone_location_id = 2;
        }
    	
    	var service_courier = $('#serviceid').val();
    	if (service_courier == 21){
    		var service_courier_url_search = "/ptlZoneAutocompleteCourierSearch?ptlFromLocation="+$('#to_location_id').val()+"&zone_location_id_value="+zone_location_id+"&courier_delivery_type="+ $('#post_delivery_type').val()+"&search="+"1";
    	}else{
    		var service_courier_url_search = "/ptlZoneAutocompletesearch?country=india&ptlFromLocation="+$('#to_location_id').val()+"&zone_location_id_value="+$('#zone_or_location').val();
    	}
        $( "#from_location_ptl_search" ).autocomplete({
            source: service_courier_url_search,
            minLength: 1,
            select: function(event, ui) {
                $('#from_location_ptl_search').val(ui.item.value);
                $('#from_location_id').val(ui.item.id);
                /*Need to add this below class to every autocomplete: Shriram */
                $("#from_location_ptl_search").addClass("clsAutoDisable");
            }
        });
    });
    $(document).on('focus click keyup keypress blur change', '#to_location_ptl_search', function() {
    	if ($("#zone_wise_ptl").is(":checked")) {
            var zone_location_id = 1;
        }
        else{
        	var zone_location_id = 2;
        }
    	var service_courier = $('#serviceid').val();
    	if (service_courier == 21){
    		var service_courier_url_to_search = "/ptlZoneAutocompleteCourierSearch?ptlFromLocation="+$('#from_location_id').val()+"&zone_location_id_value="+zone_location_id+"&courier_delivery_type="+ $('#post_delivery_type').val()+"&search="+"2";
    	}else{
    		var service_courier_url_to_search = "/ptlZoneAutocompletesearch?ptlFromLocation="+$('#from_location_id').val()+"&zone_location_id_value="+$('#zone_or_location').val();
    	}
        $( "#to_location_ptl_search" ).autocomplete({
            source: service_courier_url_to_search,
            minLength: 1,
            select: function(event, ui) {
                $('#to_location_ptl_search').val(ui.item.value);
                $('#to_location_id').val(ui.item.id);
                /*Need to add this below class to every autocomplete: Shriram */
                $("#to_location_ptl_search").addClass("clsAutoDisable");
            }
        });
    });

	
	//auto suggest for courier term written by ravi @09-05-2016 start
	$(document).on('focus click keyup keypress blur change', '#term_from_location_ptl_search', function() {
    	if ($("#term_zone_wise_ptl").is(":checked")) {
            var zone_location_id = 1;
        }
        else{
        	var zone_location_id = 2;
        }
    	
    	var service_courier = $('#serviceid').val();
    	if (service_courier == 21){
    		var service_courier_url_search = "/ptlZoneAutocompleteCourierSearch?ptlFromLocation="+$('#term_to_location_id').val()+"&zone_location_id_value="+zone_location_id+"&courier_delivery_type="+ $('#term_post_delivery_type').val()+"&search="+"1";
    	}else{
    		var service_courier_url_search = "/ptlZoneAutocompletesearch?country=india&ptlFromLocation="+$('#term_to_location_id').val()+"&zone_location_id_value="+zone_location_id;
    	}
    	
        $( "#term_from_location_ptl_search" ).autocomplete({
            source: service_courier_url_search,
            minLength: 1,
            select: function(event, ui) {
                $('#term_from_location_ptl_search').val(ui.item.value);
                $('#term_from_location_id').val(ui.item.id);
                $('#term_from_location1_id').val(ui.item.id);
                /*Need to add this below class to every autocomplete: Shriram */
                $("#term_from_location_ptl_search").addClass("clsAutoDisable");
            }
        });
    });
    $(document).on('focus click keyup keypress blur change', '#term_to_location_ptl_search', function() {
    	if ($("#term_zone_wise_ptl").is(":checked")) {
            var zone_location_id = 1;
        }
        else{
        	var zone_location_id = 2;
        }
    	var service_courier = $('#serviceid').val();
    	if (service_courier == 21){
    		var service_courier_url_to_search = "/ptlZoneAutocompleteCourierSearch?ptlFromLocation="+$('#term_from_location_id').val()+"&zone_location_id_value="+zone_location_id+"&courier_delivery_type="+ $('#term_post_delivery_type').val()+"&search="+"2";
    	}else{
    		var service_courier_url_to_search = "/ptlZoneAutocompletesearch?ptlFromLocation="+$('#term_from_location_id').val()+"&zone_location_id_value="+zone_location_id;
    	}
        $( "#term_to_location_ptl_search" ).autocomplete({
            source: service_courier_url_to_search,
            minLength: 1,
            select: function(event, ui) {
                $('#term_to_location_ptl_search').val(ui.item.value);
                $('#term_to_location_id').val(ui.item.id);
                /*Need to add this below class to every autocomplete: Shriram */
                $("#term_to_location_ptl_search").addClass("clsAutoDisable");
            }
        });
    });
	
  //auto suggest for courier term written by ravi @09-05-2016 end
	
	$("#term_zone_wise_ptl, #term_location_wise_ptl").click(function(){
		var line_itemes__ptl_check = $('.request_rows_ptl').children().size();
			term_zonelocation();

	});

	function term_zonelocation(){

		if ($("#term_zone_wise_ptl").is(":checked")) {

			$('#term_from_location_ptl').val("");
			$('#term_to_location_ptl').val("");
			$('#term_from_location_id').val("");
			$('#term_to_location_id').val("");
			$('#term_from_location_ptl_search').val("");
			$('#term_to_location_ptl_search').val("");
			$('#term_from_location1_id').val("");

			var zone_location_id = 1;
			$('#term_zone_or_location').val(zone_location_id);
			$('#term_from_location_ptl').attr("placeholder", "From Zone*");
			$('#term_from_location_ptl_search').attr("placeholder", "From Zone*");
			$('#term_to_location_ptl_search').attr("placeholder", "To Zone*");
			$('#term_to_location_ptl').attr("placeholder", "To Zone*");
			

            // add alphanumeric validation
            $('#term_from_location_ptl').addClass('alphanumericspace_strVal');
            $('#term_from_location_ptl_search').addClass('alphanumericspace_strVal');
            $('#term_from_location_ptl').attr('maxlength','10');
            $('#term_from_location_ptl_search').attr('maxlength','10');
            
            // remove maxlimit and numeric auto popup validation
            $('#term_from_location_ptl').removeClass('numericvalidation_autopop maxlimitsix_lmtVal');
            $('#term_from_location_ptl_search').removeClass('numericvalidation_autopop maxlimitsix_lmtVal');

                // add alphanumeric validation
                $('#term_to_location_ptl').addClass('alphanumericspace_strVal');
                $('#term_to_location_ptl_search').addClass('alphanumericspace_strVal');
                $('#term_to_location_ptl').attr('maxlength',10);
                $('#term_to_location_ptl_search').attr('maxlength',10);
                
                // remove maxlimit and numeric auto popup validation
                $('#term_to_location_ptl').removeClass('numericvalidation_autopop maxlimitsix_lmtVal');
                $('#term_to_location_ptl_search').removeClass('numericvalidation_autopop maxlimitsix_lmtVal');
			
			
			var service_courier_del = $('#serviceid').val();
			if(service_courier_del == 21){
			var post_delivery_type_val = $('#term_post_delivery_type').val();
			if(post_delivery_type_val == 1){
				$('#term_to_location_ptl_search').attr("placeholder", "To Zone*");
		        $('#term_to_location_ptl').attr("placeholder", "To Zone*");
			}else{
				$('#term_to_location_ptl_search').attr("placeholder", "To Country*");
		        $('#term_to_location_ptl').attr("placeholder", "To Country*");
			}
	        
			}else{
				$('#term_to_location_ptl_search').attr("placeholder", "To Zone*");
		        $('#term_to_location_ptl').attr("placeholder", "To Zone*");
			}
		}
		else{
			$('#term_from_location_ptl').val("");
			$('#term_to_location_ptl').val("");
			$('#term_from_location_id').val("");
			$('#term_to_location_id').val("");
			$('#term_from_location_ptl_search').val("");
			$('#term_to_location_ptl_search').val("");
			$('#term_from_location1_id').val("");
			var zone_location_id = 2;
			$('#term_zone_or_location').val(zone_location_id);
			$('#term_from_location_ptl').attr("placeholder", "From pin code*");
			$('#term_to_location_ptl').attr("placeholder", "To pin code*");
			$('#term_from_location_ptl_search').attr("placeholder", "From pin code*");
			$('#term_to_location_ptl_search').attr("placeholder", "To pin code*");
			
            // remove alphanumeric validation
            $('#term_from_location_ptl').removeClass('alphanumericspace_strVal');
            $('#term_from_location_ptl_search').removeClass('alphanumericspace_strVal');
            $('#term_from_location_ptl').removeAttr('maxlength');
            $('#term_from_location_ptl_search').removeAttr('maxlength');
            
            // Add maxlimit and numeric auto popup validation
            $('#term_from_location_ptl').addClass('numericvalidation_autopop maxlimitsix_lmtVal');
            $('#term_from_location_ptl_search').addClass('numericvalidation_autopop maxlimitsix_lmtVal');

            // remove alphanumeric validation
            $('#term_to_location_ptl').removeClass('alphanumericspace_strVal');
            $('#term_to_location_ptl_search').removeClass('alphanumericspace_strVal');
            $('#term_to_location_ptl').removeAttr('maxlength');
            $('#term_to_location_ptl_search').removeAttr('maxlength');
            
            // Add maxlimit and numeric auto popup validation
            $('#term_to_location_ptl').addClass('numericvalidation_autopop maxlimitsix_lmtVal');
            $('#term_to_location_ptl_search').addClass('numericvalidation_autopop maxlimitsix_lmtVal');

			var service_courier_del = $('#serviceid').val();
			if(service_courier_del == 21){
			var post_delivery_type_val = $('#term_post_delivery_type').val();
			if(post_delivery_type_val == 1){
				$('#term_to_location_ptl_search').attr("placeholder", "To pin code*");
		        $('#term_to_location_ptl').attr("placeholder", "To pin code*");
			}else{
				$('#term_to_location_ptl_search').attr("placeholder", "To Country*");
		        $('#term_to_location_ptl').attr("placeholder", "To Country*");
			}
	        
			}else{
			$('#term_to_location_ptl_search').attr("placeholder", "To pin code*");
		    $('#term_to_location_ptl').attr("placeholder", "To pin code*");	
			}
			
			
		}
		return 1;
	}


	/******************************From Location ends for ptl*********************************************/
	/******************************Vechile Type location starts*********************************************/
	$('#vechile_type').change(function(){
		var value = $(this).val();
		if(value != ""){
			$.ajax({
				url: '/getvehicletype',
				type: "post",
				data: {'id': $(this).val(), '_token': $('input[name=_token]').val()},
				success: function(data){
					var vechile_types = data.split("-");
					$("#dimension").html(vechile_types[0]);
					$("#capacity").html(vechile_types[1]);
					$("#units").html(vechile_types[2]);
				},
				error : function(request, status, error) {
					$("#dimension").html('');
					$("#capacity").html('');
					$("#units").html('');
				},
			});
		}
	});

    $('#vechile_types').change(function(){
        var value = $(this).val();
        if(value != ""){
            $.ajax({
                url: '/getvehicletype',
                type: "post",
                data: {'id': $(this).val(), '_token': $('input[name=_token]').val()},
                success: function(data){
                    var vechile_types = data.split("-");
                    $("#dimension").html(vechile_types[0]);
                    $("#capacity").html(vechile_types[1]);
                    $("#units").html(vechile_types[2]);
                },
                error : function(request, status, error) {
                    $("#dimension").html('');
                    $("#capacity").html('');
                    $("#units").html('');
                },
            });
        }
    });

    //@Raman, Seller/Home/Transportation/FTL/Search 
    $('#vechile_type_1').change(function(){
        var value = $(this).val();
        if(value != ""){
            $.ajax({
                url: '/getvehicletype',
                type: "post",
                data: {'id': $(this).val(), '_token': $('input[name=_token]').val()},
                success: function(data){
                    var vechile_types = data.split("-");
                    $("#dimension_1").html(vechile_types[0]);
                    //$("#capacity").html(vechile_types[1]);
                    //$("#units").html(vechile_types[2]);
                },
                error : function(request, status, error) {
                    $("#dimension_1").html('');
                    //$("#capacity").html('');
                    //$("#units").html('');
                },
            });
        }
    });

	$('.selectpicker').change(function(){
		$(this).selectpicker('refresh');
	});
    $(document).on('click', '.dispatch_dates_cust', function() {
    	
    	
        var id = $(this).attr("id");
        //alert(id);
        var hiddenflexid = "#"+id+"_hidden";
        
        if ($(this).is(':checked')) {
        	
            $(hiddenflexid).val(1);
            $(".dispatch_flexi").val(1);
        }else{
        	
            $(hiddenflexid).val(0);
            $(".delivery_flexi").val(1);
        }

    });
	/******************************Vechile Type location ends*********************************************/

	/******************************Date Pickers*********************************************/
	   $( "#datepicker" ).datepicker({
		   dateFormat: "dd/mm/yy",
	       changeMonth: true,
	       numberOfMonths: 1,
           //show_flexible: 1,
           //flex_identifier: "dispatch_flexible",
           //flex_text:"Flexible dates",
	       minDate: 0,
	       onClose: function( selectedDate ) {
	         $( "#datepicker_to_location" ).datepicker( "option", "minDate", selectedDate );
	       }
	     });
	     $( "#datepicker_to_location" ).datepicker({
	       dateFormat: "dd/mm/yy",
	       changeMonth: true,
	       numberOfMonths: 1,
           //show_flexible: 1,
           //flex_identifier: "delivery_flexible",
           //flex_text:"Flexible dates",
	       minDate: 0,
	       onClose: function( selectedDate ) {
	         $( "#datepicker" ).datepicker( "option", "maxDate", selectedDate );
             /*Added Valid date issue: shriram*/
             $( "#valid_to_val").val(convertDateFormatForDatePickerSeller(selectedDate));
	       }
	     });
	     /******************************Date Pickers for search*********************************************/
	     
	     /******************************Date Pickers*********************************************/
		   $( "#datepicker_air_re" ).datepicker({
			   dateFormat: "dd/mm/yy",
		       changeMonth: true,
		       numberOfMonths: 1,
	           //show_flexible: 1,
	           //flex_identifier: "dispatch_flexible",
	           //flex_text:"Flexible dates",
		       minDate: 0,
		       onClose: function( selectedDate ) {
		         $( "#datepicker_to_location_air_re" ).datepicker( "option", "minDate", selectedDate );
		       }
		     });
		     $( "#datepicker_to_location_air_re" ).datepicker({
		       dateFormat: "dd/mm/yy",
		       changeMonth: true,
		       numberOfMonths: 1,
	           //show_flexible: 1,
	           //flex_identifier: "delivery_flexible",
	           //flex_text:"Flexible dates",
		       minDate: 0,
		       onClose: function( selectedDate ) {
		         $( "#datepicker_air_re" ).datepicker( "option", "maxDate", selectedDate );
		       }
		     });
		     /******************************Date Pickers for search*********************************************/
	     
	     /******************************Date Pickers*********************************************/
		   $( "#datepicker_tl" ).datepicker({
			   dateFormat: "dd/mm/yy",
		       changeMonth: true,
		       numberOfMonths: 1,
	           //show_flexible: 1,
	           //flex_identifier: "dispatch_flexible",
	           //flex_text:"Flexible dates",
		       minDate: 0,
		       onClose: function( selectedDate ) {
		         $( "#datepicker_to_location" ).datepicker( "option", "minDate", selectedDate );
		       }
		     });
		     $( "#datepicker_to_location_tl" ).datepicker({
		       dateFormat: "dd/mm/yy",
		       changeMonth: true,
		       numberOfMonths: 1,
	           //show_flexible: 1,
	           //flex_identifier: "delivery_flexible",
	           //flex_text:"Flexible dates",
		       minDate: 0,
		       onClose: function( selectedDate ) {
		          $( "#valid_from_val" ).datepicker( "option", "maxDate", selectedDate );
                  /*Added Valid date issue: shriram*/
                  $( "#valid_to_val").val(convertDateFormatForDatePickerSeller(selectedDate));
		       }
		     });
		     /******************************Date Pickers for search*********************************************/
	     
	     
	     $( "#datepicker_search" ).datepicker({
			   dateFormat: "dd/mm/yy",
		       changeMonth: true,
		       numberOfMonths: 1,
	           show_flexible: 1,
	           flex_identifier: "dispatch_flexible",
	           flex_text:"Flexible dates",
	           minDate:0,
		       onClose: function( selectedDate ) {
		         $( "#datepicker_to_location_search" ).datepicker( "option", "minDate", selectedDate );
		       }
		     });
		     $( "#datepicker_to_location_search" ).datepicker({
		       dateFormat: "dd/mm/yy",
		       changeMonth: true,
		       numberOfMonths: 1,
	           show_flexible: 1,
	           flex_identifier: "delivery_flexible",
	           flex_text:"Flexible dates",
		       minDate:0,
		       onClose: function( selectedDate ) {
		         $( "#datepicker_search" ).datepicker( "option", "maxDate", selectedDate );
		       }
		     });
		     
		     
		     $( "#start_dispatch_date" ).datepicker({
			   dateFormat: "dd/mm/yy",
		       changeMonth: true,
		       numberOfMonths: 1,
		       maxDate:$( "#end_dispatch_date" ).val(),
	           onClose: function( selectedDate ) {
		         $( "#end_dispatch_date" ).datepicker( "option", "minDate", selectedDate );
		       }
		     });
		     $( "#end_dispatch_date" ).datepicker({
		       dateFormat: "dd/mm/yy",
		       changeMonth: true,
		       numberOfMonths: 1,
		       minDate: $( "#start_dispatch_date" ).val(),
		       onClose: function( selectedDate ) {
		         $( "#start_dispatch_date" ).datepicker( "option", "maxDate", selectedDate );
		       }
		     });
		     
	     /******************************Payment Options starts*********************************************/
			$('#payment_options').change(function(){
				var payment_options_value = $(this).val();
				if (payment_options_value == 4) {
					$("#show_credit_period").css("display", "block");
				}else{
					$("#show_credit_period").css("display", "none");
				}
				if (payment_options_value == 1) {
					$("#show_advanced_period").css("display", "block");
				}else{
					$("#show_advanced_period").css("display", "none");
				}
			});
			/******************************Payment Options ends*********************************************/
			/******************************Payment PTL Options starts*********************************************/
			 $('.ptl_payment').change(function(){
			 var rowNo = $(this).attr('id').split("_").reverse()[1]+"_"+$(this).attr('id').split("_").reverse()[0];
			
			 var buttonId = rowNo;
			 var removeString = 'options';
			 var rowNo = buttonId.replace(removeString, '');
			 var payment_options_value = $('#payment_options_' + rowNo).val();
			 if (payment_options_value == 4) {
			 $("#show_credit_period_" + rowNo).css("display", "block");
			 }else{
			 $("#show_credit_period_" + rowNo).css("display", "none");
			 }
			 if (payment_options_value == 1) {
			 $("#show_advanced_period_" + rowNo).css("display", "block");
			 }else{
			 $("#show_advanced_period_" + rowNo).css("display", "none");
			 }
			 });
			 /******************************Payment PTL Options ends*********************************************/			
			
	
/******************************Multi line add items*********************************************/
    var sel_list = new Array();
    $('#add_more').click(function() {
    	var num = parseInt($('#next_add_more_id').val()) + 1;
        $('#next_add_more_id').val(num);
        var from_location = $('#from_location').val();
        var datepicker_from_value = $('#datepicker').val();
        var datepicker_from_value = convertDateFormatForDatePickerSeller(datepicker_from_value);
        var datepicker_to_value = $('#datepicker_to_location').val();
        var datepicker_to_value = convertDateFormatForDatePickerSeller(datepicker_to_value);
        var seller_district = $('#seller_district_id').val();
        var ts_from = Date.parse(datepicker_from_value);
        var ts_to = Date.parse(datepicker_to_value);
        var from_location_identifier = $('#from_location_id').val();
        var to_location_identifier = $('#to_location_id').val();
        var load_type = $('#load_type').val();
        var vehicle_type = $('#vechile_type').val();
        var to_location = $('#to_location').val();
        var price = $('#price').val();
        var units = $('#transitdays_units').val();
        var transit = $('#transitdays').val();
        var price_numric = /^\d+(\.\d{2})?$/.test(price);
        var transit_numric = /^[0-9]{1,3}$/.test(transit);
        var load_type_value = $( "#load_type option:selected" ).text();
        var vehicle_type_value = $( "#vechile_type option:selected" ).text();
        if (load_type_value ==  "Load Type (All)"){
            load_type_value = "All";
        }
        if (vehicle_type_value ==  "Vehicle Type (All)"){
        	vehicle_type_value = "All";
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
        
        if (to_location_identifier != '' && from_location_identifier != '' && datepicker_from_value != null && datepicker_to_value != null && from_location != '' && to_location != ''&& price != '' && price != 0 && transit != '' && transit != 0 && load_type != '' && vehicle_type != '' && price_numric == true && transit_numric == true) {
        if((current_date_seller >= subscription_start_date_start_val) && (current_date_seller <= subscription_end_date_end_val) && (datepicker_from_value >= subscription_start_date_start_val) && (datepicker_from_value <= subscription_end_date_end_val) && (datepicker_to_value >= subscription_start_date_start_val) && (datepicker_to_value <= subscription_end_date_end_val)){
        if (to_location_identifier != '' && from_location_identifier != '' && datepicker_from_value != '' && datepicker_to_value != '' && from_location != '' && to_location != ''&& price != '' && transit != '' && load_type != '' && vehicle_type != '' && price_numric == true && transit_numric == true) {
            var unique = from_location_identifier+to_location_identifier+load_type+vehicle_type+ts_from+ts_to+transit;


            if ($.inArray(unique,sel_list)==-1) {
                sel_list.unshift(unique);
            $.ajax({
                type : 'post',
                url : '/lineitemscheck',
                data : {
                    'from_location' : from_location_identifier,
                    'to_location' : to_location_identifier,
                    'from_date_seller' : datepicker_from_value,
                    'to_date_seller' : datepicker_to_value,
                    'vehicle_type' : vehicle_type,
                    'load_type' : load_type,
                    'transit_days' : transit
                },
                dataType : "html",
                type : 'POST',
                success : function(data) {
                    if (data == '0') {
                    	
                    	if($("#update_ftl_seller_line").val()==1)
                    	{
                        	$('.request_row_' + $("#update_ftl_seller_row_count").val()).remove();	
                        	$("#update_ftl_seller_line").val(0);
                        }
                    	
                    	var rowid = "#single_post_item_" + num;
                        var html = '<div class="table-row inner-block-bg request_row_'
                            + num
                            + '" id="single_post_item_'
                            + num
                            + '"><div class="col-md-2 padding-left-none from_location_text" id="from_loc_text_'+num+'">'
                            + from_location
                            + '</div><div class="col-md-2 padding-left-none to_location_text" id="to_loc_text_'+num+'">'
                            + to_location
                            + '</div><div class="col-md-3 padding-left-none">'
                            + load_type_value
                            + '</div><div class="col-md-2 padding-left-none">'
                            + vehicle_type_value
                            + '</div><div class="col-md-2 padding-none">'
                            + price
                            + '/-</div><div class="col-md-1 padding-none"><a href="javascript:void(0)" style ="cursor:pointer; margin-right:10px;" onclick="updatecreatepostlineitem('+num+')" class="edit_this_line edit" data-string="'+unique+'" row_id="'
                            + num
                            + '"><i class="fa fa-edit red" title="Edit"></i></a><a style ="cursor:pointer;" class="remove_this_line_item remove" data-string="'+unique+'" row_id="'
                            + num
                            + '"><i class="fa fa-trash red" title="Delete"></i></a></div><input type="hidden" name="from_location[]" id="from_location_id_'+num+'" value="'
                            + from_location_identifier
                            + '"><input type="hidden" name="to_location[]" id="to_location_id_'+num+'" value="'
                            + to_location_identifier
                            + '"><input type="hidden" name="load_type[]" id="load_type_'+num+'" value="'
                            + load_type
                            + '"><input type="hidden" name="vechile_type[]" id="vechile_type_'+num+'" value="'
                            + vehicle_type
                            + '"><input type="hidden" name="transitdays[]" id="transitdays_'+num+'" value="'
                            + transit
                            + '"><input type="hidden" name="units[]" id="units_'+num+'" value="'
                            + units
                            + '"><input type="hidden" name="sellerdistrict[]" id="sellerdistricts_'+num+'" value="'
                            + seller_district
                            + '"><input type="hidden" name="price[]" id="price_'+num+'" value="'
                            + price
                            + '"><div class="clearfix"></div></div>';

                        $("#multi-line-itemes").show();
                        $('.request_rows').append(html);
                        var id_line_itemes = $('.request_rows').children().size();
                        if (id_line_itemes == 0){
                        }else{
                        	$("#datepicker").prop('disabled', true);
                        	$("#datepicker_to_location").prop('disabled', true);
                        }
                        $("#valid_from_val").val(datepicker_from_value);
                        $("#valid_to_val").val(datepicker_to_value);
                        $('#from_location').val("");
                        $('#from_location_id').val("");
                        $('#to_location').val("");
                        $('#to_location_id').val("");
                        $('#vechile_type').val("");
                        $('#load_type').val("");
                        $('#transitdays').val("");
                        $('#price').val("");
                        $('.selectpicker').selectpicker('refresh');

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

    });
/******************************Multi line add items*********************************************/

    
    
    
    
    
    
    
    
    
    
    /******************************Multi line Remove items*********************************************/
        $(document).on('click', '.remove_this_line', function() {
            var rowid = $(this).attr("row_id");
            remove_val = $(this).attr("data-string");
            var r = confirm("Are you sure, you want you delete?");
            if (r == true) {
            	sel_list.splice($.inArray(remove_val, sel_list),1);
                $('.request_row_' + rowid).remove();
            }
            var id_line_itemes_empty = $('.request_rows').children().size();
            if (id_line_itemes_empty == 0){
            	$("#datepicker").prop('disabled', false);
            	$("#datepicker_to_location").prop('disabled', false);
            }
        });
    /******************************Multi line Remove items*********************************************/
        $(document).on('click', '.edit_this_line', function() {
            var rowid = $(this).attr("row_id");
            remove_val = $(this).attr("data-string");
            sel_list.splice($.inArray(remove_val, sel_list),1);
        	 $("#update_ftl_seller_line").val(1);
         	 $("#update_ftl_seller_row_count").val(rowid);
         	   
            $('#from_location').val($("#from_loc_text_"+rowid).html());
            $('#from_location_id').val($("#from_location_id_"+rowid).val());
            $('#to_location').val($("#to_loc_text_"+rowid).html());
            $('#to_location_id').val($("#to_location_id_"+rowid).val());
            $('#vechile_type').selectpicker('val',$("#vechile_type_"+rowid).val());
            $('#load_type').selectpicker('val',$("#load_type_"+rowid).val());
            $('#transitdays').val($("#transitdays_"+rowid).val());
            $('#price').val($("#price_"+rowid).val());
            
        	
        });
        
    /******************************Multi line Remove Line items*********************************************/
        $(document).on('click', '.remove_this_line_item', function() {
            var rowid = $(this).attr("row_id");
            remove_val = $(this).attr("data-string");
            var r = true;
            if (r == true) {
            	sel_list.splice($.inArray(remove_val, sel_list),1);
                $('.request_row_' + rowid).remove();
            }
            var id_line_itemes_empty = $('.request_rows').children().size();
            if (id_line_itemes_empty == 0){
            	$("#datepicker").prop('disabled', false);
            	$("#datepicker_to_location").prop('disabled', false);
            }
        });
    /******************************Multi line Remove Line items*********************************************/
        
    /******************************Multi line Edit items*********************************************/
        $(document).on('click', '.updatepostlineitem', function() {
            var rowid = $(this).attr("row_id");
            remove_val = $(this).attr("data-string");
            var r = confirm("Are you sure, you want you delete?");
            if (r == true) {
            	sel_list.splice($.inArray(remove_val, sel_list),1);
                $('.request_row_' + rowid).disable();
            }
            var id_line_itemes_empty = $('.request_rows').children().size();
            if (id_line_itemes_empty == 0){
            	$("#datepicker").prop('disabled', false);
            	$("#datepicker_to_location").prop('disabled', false);
            }
        });
    /******************************Multi line Edit items*********************************************/
    
/******************************Multi line add items for Ptl*********************************************/
        var sel_list_ptl = new Array();    
    $('#add_more_ptl').click(function() {
    	var num = parseInt($('#next_add_more_id_ptl').val()) + 1;
        $('#next_add_more_id_ptl').val(num);
        var serviceid = $('#serviceid').val();
        var datepicker_from_value = $('#datepicker').val();
        var datepicker_from_value = convertDateFormatForDatePickerSeller(datepicker_from_value);
        var datepicker_to_value = $('#datepicker_to_location').val();
        var datepicker_to_value = convertDateFormatForDatePickerSeller(datepicker_to_value);
        if(serviceid == 21){
        var price_ptl_value = 1;
        }else{
        var price_ptl_value = $('#price_ptl').val();
        }
        var transitdays_units_ptl_value = $('#transitdays_units_ptl').val();
        var transitdays_ptl_value = $('#transitdays_ptl').val();
        var transit_value_days = transitdays_ptl_value+" "+transitdays_units_ptl_value;
        var subscription_start_date_start_val = $('#subscription_start_date_start').val();
        var subscription_end_date_end_val = $('#subscription_end_date_end').val();
        var current_date_seller = $('#current_date_seller').val();
        
        if(serviceid == 8){
        	var from_location_ptl = $('#from_airport').val();
            var to_location_ptl = $('#to_airport').val();
        }else if(serviceid == 9){
        	var from_location_ptl = $('#from_occean').val();
            var to_location_ptl = $('#to_occean').val();
        }else{
        	var from_location_ptl = $('#from_location_ptl').val();
        	var to_location_ptl = $('#to_location_ptl').val();
        }
        
        var from_location_pin = from_location_ptl.split("-"); 
        var to_location_pin = to_location_ptl.split("-"); 
         
        if(serviceid == 8){
        	 var from_location_identifier = $('#from_airport_id').val();
             var to_location_identifier = $('#to_airport_id').val();
        }else if(serviceid == 9){
        	 var from_location_identifier = $('#from_occean_id').val();
             var to_location_identifier = $('#to_occean_id').val();
        }else{
        var from_location_identifier = $('#from_location_id').val();
        var to_location_identifier = $('#to_location_id').val();
        }
        var ts_from = Date.parse(datepicker_from_value);
        var ts_to = Date.parse(datepicker_to_value);
        
        var price_numric = /^\d{1,5}(\.\d{1,2})?$/i.test(price_ptl_value);
        var transit_numric = /^[0-9]{1,3}$/.test(transitdays_ptl_value);
        
        
        if ($("#zone_wise_ptl").is(":checked")) {
        var zone_location_id = 1;
        }else{
        var zone_location_id = 2;
        }
        
        if((datepicker_from_value > subscription_end_date_end_val) || (current_date_seller < subscription_start_date_start_val) || (datepicker_from_value < subscription_start_date_start_val)){
        	var end_date_subscription = "from date";
        }else{
        	var end_date_subscription = "to date";
        }
        
        if($("#update_ptl_seller_line").val()==1)
     	{
         	$('.request_row_ptl_' + $("#update_ptl_seller_row_count").val()).remove();
         	sel_list_ptl.splice($.inArray($("#update_ptl_remove_count").val(), sel_list_ptl),1);
         	$("#update_ptl_seller_line").val(0);
         	
         }
        if (from_location_identifier != '' && to_location_identifier != '' && datepicker_from_value != null && datepicker_to_value != null && from_location_ptl != '' && to_location_ptl != ''&& price_ptl_value != '' && price_ptl_value != 0 && transitdays_ptl_value != 0 && transitdays_ptl_value != '' ) {

        if((current_date_seller >= subscription_start_date_start_val) && (current_date_seller <= subscription_end_date_end_val) && (datepicker_from_value >= subscription_start_date_start_val) && (datepicker_from_value <= subscription_end_date_end_val) && (datepicker_to_value >= subscription_start_date_start_val) && (datepicker_to_value <= subscription_end_date_end_val)){

         if (datepicker_from_value != '' && datepicker_to_value != '' && from_location_ptl != '' && to_location_ptl != ''&& price_ptl_value != '' && transitdays_ptl_value != '' && price_numric == true && transit_numric == true ) {
        	 var unique = from_location_identifier+to_location_identifier+zone_location_id+ts_from+ts_to+transitdays_ptl_value;
        	 
        if ($.inArray(unique,sel_list_ptl)==-1) {
                 sel_list_ptl.unshift(unique);
                 $.ajax({
                     type : 'post',
                     url : '/lineitemscheckptl',
                     data : {
                         'from_location' : from_location_identifier,
                         'to_location' : to_location_identifier,
                         'from_date_seller' : datepicker_from_value,
                         'to_date_seller' : datepicker_to_value,
                         'zone_location_id_value' : zone_location_id,
                         'transit_days'   : transitdays_ptl_value
                     },
                     dataType : "html",
                     type : 'POST',
                     success : function(data) {
                         if (data == '0') {
                        
        var html = '<div class="table-row inner-block-bg request_row_ptl_'
                            + num
                            + '"><div class="col-md-3 padding-left-none" id="ptl_from_text_'+num+'">'
                            +
                            from_location_pin[0]
                            + '</div><div class="col-md-3 padding-left-none" id="ptl_to_text_'+num+'">'
                            + to_location_pin[0]
                            + '</div>';
        if(serviceid != 21){
        var html = html+'<div class="col-md-3 padding-left-none">'
                            + price_ptl_value
                            + '</div>';
        }
        var html = html+'<div class="col-md-2 padding-left-none">'
                            + transit_value_days
                            + '</div><div class="col-md-1 padding-left-none"><a style ="cursor:pointer;" class="ptledit_this_line_ptl edit" data-string="'+unique+'" row_id_ptl="'
                            + num
                            + '"><i class="fa fa-edit red" title="Edit"></i></a>&nbsp;<a style ="cursor:pointer;" class="ptlremove_this_line_ptl remove" data-string="'+unique+'" row_id_ptl="'
                            + num
                            + '"><i class="fa fa-trash red" title="Delete"></i></a></div><input type="hidden" name="from_location[]" id="ptl_from_locaion_id_'+num+'" value="'
                            + from_location_identifier
                            + '"><input type="hidden" name="to_location[]" id="ptl_to_locaion_id_'+num+'" value="'
                            + to_location_identifier
                            + '"><input type="hidden" name="transitdays[]" id="ptl_transitdays_'+num+'" value="'
                            + transitdays_ptl_value
                            + '"><input type="hidden" name="units[]" id="ptl_units_'+num+'" value="'
                            + transitdays_units_ptl_value
                            + '">';
        if(serviceid != 21){
        var html = html+'<input type="hidden" name="price[]" id="ptl_price_'+num+'" value="'
                            + price_ptl_value
                            + '">';
        }
        var html = html+'<div class="clearfix"></div></div>';
                        $("#multi-line-itemes").show();
                        $('.request_rows_ptl').append(html);
                        var id_line_itemes_ptl = $('.request_rows_ptl').children().size();
                        if (id_line_itemes_ptl == 0){
                        }else{
                        	$("#datepicker").prop('disabled', true);
                        	$("#datepicker_to_location").prop('disabled', true);
                        	$("#domestic").prop('disabled', true);
                        	$("#international").prop('disabled', true);
                        }
                        $("#valid_from_val").val(datepicker_from_value);
                        $("#valid_to_val").val(datepicker_to_value);
                        $("#post_type_id").val(zone_location_id);
                        if(serviceid == 21){
                        	
                        	$("#post_or_delivery_type_id").val($('#post_delivery_type').val());
                        	$("#courier_or_types_id").val($('#courier_types').val());
                        }
                        $('#from_location_ptl').val("");
                        $('#to_location_ptl').val("");
                        $('#to_location_id').val("");
                        $('#from_location_id').val("");
                        $('#from_airport').val("");
                        $('#to_airport').val("");
                        $('#from_airport_id').val("");
                        $('#to_airport_id').val("");
                        $('#from_occean').val("");
                        $('#to_occean').val("");
						$('#from_occean_id').val("");
						$('#to_occean_id').val("");
                        $('#transitdays_ptl').val("");
                        $('#price_ptl').val("");
                         $('.selectpicker').selectpicker('refresh');
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
    });
    
    $(document).on('click', '.ptlremove_this_line_ptl', function() {
        var rowid_ptl = $(this).attr("row_id_ptl");
        remove_val_ptl = $(this).attr("data-string");
        var r = confirm("Are you sure, you want you delete?");
        if (r == true) {
        	sel_list_ptl.splice($.inArray(remove_val_ptl, sel_list_ptl),1);
            $('.request_row_ptl_' + rowid_ptl).remove();
        }
        var id_line_itemes_empty_ptl = $('.request_rows_ptl').children().size();
        if (id_line_itemes_empty_ptl == 0){
        	$("#datepicker").prop('disabled', false);
        	$("#datepicker_to_location").prop('disabled', false);
        	$("#zone_wise_ptl").prop('disabled', false);
        	$("#location_wise_ptl").prop('disabled', false);
        	$("#domestic").prop('disabled', false);
        	//$("#domestic").prop('disabled', false);
        	$("#international").prop('disabled', false);
        }
    });
    $(document).on('click', '.ptledit_this_line_ptl', function() {
        var rowid_ptl = $(this).attr("row_id_ptl");
        var serviceid = $('#serviceid').val();
        remove_ptl_val = $(this).attr("data-string");
        $("#update_ptl_seller_line").val(1);
    	$("#update_ptl_seller_row_count").val(rowid_ptl);
    	$("#update_ptl_remove_count").val(remove_ptl_val);
        
    	
        $('#from_location_ptl').val($("#ptl_from_text_"+rowid_ptl).html());
        $('#from_location_id').val($("#ptl_from_locaion_id_"+rowid_ptl).val());
        $('#to_location_ptl').val($("#ptl_to_text_"+rowid_ptl).html());
        $('#to_location_id').val($("#ptl_to_locaion_id_"+rowid_ptl).val());
        
        
        if(serviceid==8){
        	$('#from_airport').val($("#ptl_from_text_"+rowid_ptl).html());
            $('#to_airport').val($("#ptl_to_text_"+rowid_ptl).html());
            $('#from_airport_id').val($("#ptl_from_locaion_id_"+rowid_ptl).val());
            $('#to_airport_id').val($("#ptl_to_locaion_id_"+rowid_ptl).val());
        }
        if(serviceid==9){
        	$('#from_occean').val($("#ptl_from_text_"+rowid_ptl).html());
        	$('#to_occean').val($("#ptl_to_text_"+rowid_ptl).html());
        	$('#from_occean_id').val($("#ptl_from_locaion_id_"+rowid_ptl).val());
    		$('#to_occean_id').val($("#ptl_to_locaion_id_"+rowid_ptl).val());
        }
        
        $('#transitdays_ptl').val($("#ptl_transitdays_"+rowid_ptl).val());
        $('#price_ptl').val($("#ptl_price_"+rowid_ptl).val());
        
    });    
    
/******************************Multi line add items for ptl*********************************************/
/******************************buyer List For Seller Starts*********************************************/
	$('.create-posttype-service').click(function(){
		
		 posttype_val = $(".create-posttype-service:checked").val();
		if (posttype_val == 2){
			$(".demo-input_buyers").css("display", "block");
		}else{
			
			$(".demo-input_buyers").css("display", "none");
		}
	});


/******************************buyer List For Seller ends*********************************************/

/******************************Save as draft functionality*********************************************/
	$('#add_quote_seller').click(function(e) {
		e.preventDefault();
		var id=$('.request_rows').children().size();
		if(id==0){
			$("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
            $("#erroralertmodal").modal({
                show: true
            });
			return false;
		}else{
			
			
			
			$('#posts-form').submit();
			if($('#posts-form').valid()){
				$("#add_quote_seller_id").prop('disabled', true);
				$("#add_quote_seller").prop('disabled', true);
			}
		}
	});
/******************************Save as draft functionality*********************************************/
/******************************Save as draft functionality for ptl*********************************************/
	$('#add_quote_seller_ptl').click(function(e) {
		e.preventDefault();
		var id_ptl=$('.request_rows_ptl').children().size();
		if(id_ptl==0){
			$("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
            $("#erroralertmodal").modal({
                show: true
            });
			return false;
		}else{
			$('#posts-form_ptl').submit();
			if($('#posts-form_ptl').valid()) {
				$("#add_quote_seller_ptl").prop('disabled', true);
				$("#add_quote_seller_id_ptl").prop('disabled', true);
			}
		}
	});
/******************************Save as draft functionality for ptl*********************************************/
/******************************confirm functionality*********************************************/
	$('#add_quote_seller_id').click(function(e) {
		e.preventDefault();
		var id=$('.request_rows').children().size();
		if(id==0){
			$("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
            $("#erroralertmodal").modal({
                show: true
            });
			return false;
		}else{
			if($("#posts-form").valid()) {
			var submitData=$("#posts-form").serialize();
			 var btnName = $('#add_quote_seller_id').attr('name');
			 $("#add_quote_seller_id").prop('disabled', true);
    		 $("#add_quote_seller").prop('disabled', true);
             var btnVal = $('#add_quote_seller_id').val();
             var btn = '&'+btnName+'='+btnVal;
             submitData += btn;
			 $.ajax({
		           type: "POST",
		           url: '/addseller',
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

/******************************confirm functionality for ptl*********************************************/
	$('#add_quote_seller_id_ptl').click(function(e) {
		e.preventDefault();
		var id=$('.request_rows_ptl').children().size();
		if(id==0){
			$("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			return false;
		}else{
			if($('#serviceid').val() == 21){
				var index_value = $('#price_slap_hidden_value').val();
	       		var max_weight_accepted = $('#max_weight_accepted').val();
	       		if(index_value == 0){
		       		var high_salb = parseFloat($('#high_price').val());
		       		var high_slab_focus = '#high_price';
	       		}else{
		       		var high_salb = parseFloat($('#high_weight_slab_'+index_value).val());	
		       		var high_slab_focus = '#high_weight_slab_'+index_value;
	       		}
	       		if(high_salb > max_weight_accepted){
	       			$("#erroralertmodal .modal-body").html("High slab value should not be grtear than max weight.");
	    	        $("#erroralertmodal").modal({
	    	            show: true
	    	        }).one('click','.ok-btn',function (e){
   	            	 $(high_slab_focus).focus();
 	               });
	    			return false;
	       			
	       		}else{
	       			if($("#posts-form_ptl").valid()) {
	       			 var submitData=$("#posts-form_ptl").serialize();
	       			 var btnName = $('#add_quote_seller_id_ptl').attr('name');
	       			 $("#add_quote_seller_ptl").prop('disabled', true);
	            		 $("#add_quote_seller_id_ptl").prop('disabled', true);
	                    var btnVal = $('#add_quote_seller_id_ptl').val();
	                    var btn = '&'+btnName+'='+btnVal;
	                    submitData += btn;
	       			 $.ajax({
	       		           type: "POST",
	       		           url: '/sellerpostcreation',
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
				
			}else{
				if($("#posts-form_ptl").valid()) {
					 var submitData=$("#posts-form_ptl").serialize();
					 var btnName = $('#add_quote_seller_id_ptl').attr('name');
					 $("#add_quote_seller_ptl").prop('disabled', true);
		     		 $("#add_quote_seller_id_ptl").prop('disabled', true);
		             var btnVal = $('#add_quote_seller_id_ptl').val();
		             var btn = '&'+btnName+'='+btnVal;
		             submitData += btn;
					 $.ajax({
				           type: "POST",
				           url: '/sellerpostcreation',
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
			
		}
	});

	/******************************confirm functionality for ptl*********************************************/

	//hide and show of sumitquery details
    $(".quotedetailsslide-1.seller_submit_quote,#click-link").click(function(){
        $('input[type="submit"]').removeAttr('disabled');
        var strClass = $(this).data("buyernbuyerquoteid");
        $(".quote_details_2_"+strClass).slideToggle("500");
        if ($(this).hasClass('seller_counter')){
        	$('.hide-final').hide();
        	$('.hide-submit').hide();
        	$('.show-submit').show();
        } else {
        	$('.hide-final').show();
        	$('.hide-submit').show();
        	$('.show-submit').hide();
        }
    });

    $(".quotedetailsslide-4.seller_submit_quote,.quotedetailsslide-4.seller_counter").click(function(){
        var strClass = $(this).closest('span').data("buyernbuyerquoteid");
        $(".table-slide-4.quote_details_"+strClass).slideToggle("500");
    });

   
    
    //---------------accordian style-----------------------
    
    $(".dropdown_data h3").click(function(){
    	$(this).next("ul").slideToggle();
		$(this).find("a").toggleClass("main-active");
		
    	$(this).parent().find("ul > li .menu-count").addClass("menu-count-hide");
    	
    	setTimeout(function(){	
    		$(".dropdown_data ul > li .menu-count").removeClass("menu-count-hide");
    	}, 500);
    	
    });
    


    $(".dropdown_data ul").first().slideToggle();


    $(".hide_details").hide();
    
    $(".spot_transaction_top_details").click(function(){
        $(".spot_transaction_details_view").slideToggle(500);
        $(".show_top_details").toggle();
        $(".hide_top_details").toggle();
    });
    $(".spot_transaction_details").click(function(){
        $(".spot_transaction_details_view").slideToggle(500);
        $(this).find(".show_details").toggle();
        $(this).find(".hide_details").toggle();
    });

    $(".ftl_spot_transaction_details").click(function(){
    	var buyerId = $(this).parent('span').data("sellerlistid");
    	$(".ftl_spot_transaction_details_view_"+buyerId).slideToggle(500);
        $(this).find(".show_details").toggle();
        $(this).find(".hide_details").toggle();
        datastr = '&postId=' + $(this).parent('span').attr('id');

        $.ajax({
			type : 'post', // defining the ajax type
			url : '/updatesellerpostview', // calling the controller with the
			// action involved
			dataType : 'html', // datatype
			data : datastr, // passing the data used for operation
			success : function(html) {					
					}
		});
    });
    
    $(".tab_details_hide").hide();
    $(".tab_details_view").click(function(){
        $(".tab_details").slideToggle(500);
        $(".tab_details_show").toggle();
        $(".tab_details_hide").toggle();
    });

    $(document).on('click', '.sellesearchdetails_list', function() {
    
		var buyerIdforseller = $(this).closest('span').data("buyersearchlistid");
                //alert($(".seller_listdetails_"+buyerIdforseller).attr('style'));
                if($(".seller_listdetails_"+buyerIdforseller).attr('style')=='display: none;'){
                    var data = {
	    	        'term_quote_id': $( this ).attr( "id" )    	        
	    	    };
	    	 $.ajax({
	    	        type: "GET",
	    	        url: '/termviewcountupdate',
	    	        data: data,
	    	        dataType: 'text',
	    	        success: function(data) {	    	                      
	    	        },
	    	        error: function(request, status, error) {    	            
	    	        },
	    	    }); 
                }
		$(".seller_listdetails_"+buyerIdforseller).slideToggle("500");

	});


    $(".sellesearchdetails_1").click(function(){
		var buyerIdforseller = $(this).data("buyersearchlistid");
		$(".sellesearchdetails_2").removeClass('customclass1');
		if($(this).hasClass('customclass')) {
			$(".seller_quotedetails_"+buyerIdforseller).hide();
			$(this).removeClass('customclass');
			$('.hide-final_'+buyerIdforseller).hide();
    		$('.hide-submit_'+buyerIdforseller).hide();
		} else { 
			$(".seller_quotedetails_"+buyerIdforseller).show();
			$('.hide-final_'+buyerIdforseller).show();
    		$('.hide-submit_'+buyerIdforseller).show();
			$(this).addClass('customclass');

		}
    	$('.show-submit_'+buyerIdforseller).hide();
	});

    $(".sellesearchdetails_2").click(function(){
    	$(".sellesearchdetails_1").removeClass('customclass');
		var buyerIdforseller = $(this).data("buyersearchlistid");
		if($(this).hasClass('customclass1')) {
			$(".seller_quotedetails_"+buyerIdforseller).hide();
			$(this).removeClass('customclass1');
			$('.show-submit_'+buyerIdforseller).hide();
		} else { 
			$(".seller_quotedetails_"+buyerIdforseller).show();
			$(this).addClass('customclass1');
			$('.show-submit_'+buyerIdforseller).show();

		}
    	$('.hide-final_'+buyerIdforseller).hide();
    	$('.hide-submit_'+buyerIdforseller).hide();
	});

    
    $(".ltlsellesearchdetails_1").click(function(){
		var buyerIdforseller = $(this).closest('span').data("buyersearchlistid");
		$(".seller_quotedetails_"+buyerIdforseller).slideToggle("500");
		$('.hide-final').show();
    	$('.hide-submit').show();
    	$('.show-submit').hide();
	});
    $(".ltlsellesearchdetails_2").click(function(){
		var buyerIdforseller = $(this).closest('span').data("buyersearchlistid");
		$(".seller_quotedetails_"+buyerIdforseller).slideToggle("500");
    	$('.hide-final').hide();
    	$('.hide-submit').hide();
    	$('.show-submit').show();
	});
    
    
        $(document).on('click', '.detailsslide', function() {
		var buyerIdforseller = $(this).closest('span').data("buyersearchlistid");
		//alert(buyerIdforseller);
                //added by swathi 02/05/2016

                if($(this).children('span').attr('style')=='display: none;'){
                    var buyer=buyerIdforseller.split('_');
                    $.ajax({
                            url: '/getbuyerviewcount',
                            type: "post",
                            data: {
                                'buyer_item_id': buyer[1]
                            },
                            success: function(data) {
                                
                            },
                            error: function(request, status, error) {
                                alert(error);
                            },
                        });	
                }
                //end comment
		$(".quote_details_1_"+buyerIdforseller).slideToggle("500");
		$(this).find(".show_details").toggle();
		$(this).find(".hide_details").toggle();
	});
        $(document).on('click', '.detailsslide-term', function() {
	
		var termBuyerIdforseller =$(this).attr('id');
                
           if($(".term_quote_details_"+termBuyerIdforseller).attr('style')=='display: none;'){
	    	var data = {
	    	        'term_quote_id': $( this ).attr( "id" )    	        
	    	    };
	    	 $.ajax({
	    	        type: "GET",
	    	        url: '/termviewcountupdate',
	    	        data: data,
	    	        dataType: 'text',
	    	        success: function(data) {	    	                      
	    	        },
	    	        error: function(request, status, error) {    	            
	    	        },
	    	    });  
                }    
		$(".term_quote_details_"+termBuyerIdforseller).slideToggle("1500");
		$(this).find(".show_details").toggle();
		$(this).find(".hide_details").toggle();
		
	});
        
     $(document).on('click', '.detailsslide-office', function() {
    		var buyerIdforseller = $(this).attr('rel');
    		
    		var buyer=buyerIdforseller.split('_');
    		//console.log($('.term_quote_details_'+buyer[1]).attr('style'));
    		if($('.term_quote_details_'+buyer[1]).attr('style')=='display: none;'){
                     
                        $.ajax({
                                url: '/getbuyerviewcount',
                                type: "post",
                                data: {
                                    'buyer_item_id': buyer[1]
                                },
                                success: function(data) {
                                    
                                },
                                error: function(request, status, error) {
                                    alert(error);
                                },
                            });	
    		 }
    		$(".quote_details_1_"+buyerIdforseller).slideToggle("500");
    		$(this).find(".show_details").toggle();
    		$(this).find(".hide_details").toggle();
    	});
	$(".detailsslide-3").click(function(){
		var buyerIdforseller = $(this).closest('span').data("buyersearchlistid");
		$(".quote_details_1_"+buyerIdforseller).slideToggle("500");
		$(this).find(".show_details").toggle();
		$(this).find(".hide_details").toggle();
	});
	
	$(".seller_counter").click(function(){
		var buyerIdforseller = $(this).closest('span').data("buyernbuyerquoteid");
		$(".counter_details_2_"+buyerIdforseller).slideToggle("500");
	});
	
	$(".services_link").click(function(){
		$(".hover-menu").toggle();
	});

	$(".hover-menu a").click(function(){
		$(".hover-inner-menu").toggle();
	});


/*******************************************CANCEL POST**************************************************/
	//seller post cancel process

	$("#cancel-seller-post").click(function() {
		 var answer = confirm ("Are you sure you want to delete the post?");
		  if (answer)
		    {
		var postId = $('#seller-post-id').val();
		datastr = '&postId=' + postId;
		// ajax function starts
		$.ajax({
			type : 'post', // defining the ajax type
			url : '/sellerposts/cancelsellerpost', // calling the controller with the
			// action involved
			dataType : 'html', // datatype
			data : datastr, // passing the data used for operation
			success : function(html) {
					alert(html);
					location.reload();
					}
		});

		    }
	});
	var sel_list_terms = new Array();
	$('.my-form .add-box').click(function(){
	var n = $('.update_txt_test').length;
    var num = parseInt($('#next_terms_count_search').val()) + 1;
    $('#next_terms_count_search').val(num);
    if( 2 < n ) {
            $("#erroralertmodal .modal-body").html("You can add 5 charges only !");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        }
        var box_html = $('<div class="text-box form-control-fld terms-and-conditions-block"><div class="col-md-3 col-sm-4 col-xs-5 padding-none tc-block-fld padding-left-none labelcharges"><div class="input-prepend"><input type="text" name="labeltext_' + num + '"  placeholder= "Other Charges" class="form-control form-control1 clsOtherText labelcharges dynamic_labelcharges " value=""  /></div></div>   <div class="col-md-3"><div class="input-prepend"><input type="text" class="form-control form-control1 pricebox update_txt_test update_txt dynamic_validations clsGMSOtherCharges" placeholder ="0.00" name="terms_condtion_types_' + num + '" value="" id="box_' + num + '" /><span class="add-on unit">Rs</span></div></div> <a href="#" class="remove-box" data-string="'+num+'"><i class="fa fa-trash red" title="Delete"></i></a></div>');
        box_html.hide();
        
        $('.my-form div.text-box:last').after(box_html);
        box_html.fadeIn('slow');

        $('.numberVal').keypress(function (event) {
            var keycode = event.keyCode || event.which;
            if (!(event.shiftKey == false && (keycode == 9 || keycode == 46 || keycode == 8 || keycode == 37 || keycode == 39 || (keycode >= 48 && keycode <= 57)))) {
                event.preventDefault();
            }
        });
        
        $(".dynamic_validations").each(function (item) {
            $(this).rules("add", {
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
            });
        });
        return false;
    });
    $('.my-form').on('click', '.remove-box', function(){
        $(this).parent().fadeOut("fast", function() {
        	remove_val_terms = $(this).attr("data-string");
            $(this).remove();
            $('.box-number').each(function(index){
                $(this).text( index + 1 );
            });
        });
        return false;
    });
    $('#max_weight_accepted').blur(function(){
    	var max_weight_accepted = parseFloat($('#max_weight_accepted').val());
    	var max_weight_accepted_value = /^\d+(\.\d{1,4})?$/i.test(max_weight_accepted);
    	var index_value = $('#price_slap_hidden_value').val();
    	if (max_weight_accepted_value && index_value == 0){
    	$('#high_price').val(max_weight_accepted);
    	}
    });
    $('#high_price').blur(function(){
    	var check_max_weight_accepted = parseFloat($('#max_weight_accepted').val());
    	var check_high_price_accepted = parseFloat($('#high_price').val());
    	var check_max_weight_accepted_value = /^\d+(\.\d{1,4})?$/i.test(check_max_weight_accepted);
    	var check_high_price_first_value = /^\d+(\.\d{1,4})?$/i.test($('#high_price').val());
    	if (check_max_weight_accepted_value == true && check_high_price_first_value == true){    		
    		if (check_max_weight_accepted > check_high_price_accepted ){
    			removeReadonlyProperty();
        	}else{
    			readonlyProperty();
        	}
    	}
    });
///////////////////////////Price slab start here//////////////////////////////////////
    
    $('.add-price-slap .add-box').click(function(){
    		var index_value = $('#price_slap_hidden_value').val();
       		var max_weight_accepted = $('#max_weight_accepted').val();
       		var max_weight_accepted_value = /^\d+(\.\d{1,4})?$/i.test(max_weight_accepted);
       		var max_weight_accepted_count = max_weight_accepted.length;
       		var high_price_first_value = /^\d+(\.\d{1,4})?$/i.test($('#high_price').val());
       		if (max_weight_accepted_value){
       			var max_weight_accepted = max_weight_accepted;
       		}else{
       			var max_weight_accepted = '';
       		}
    		if (index_value == 0){
    			if(high_price_first_value == true && $('#high_price').val() != ''){
            		var high_price_value = parseFloat($('#high_price').val());
    			}else{
    				var high_price_value = '';
    			}
    			if((max_weight_accepted_count == 0)){
    				$("#erroralertmodal .modal-body").html("Maximum weight accepted should not be empty.");
    	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
    	            	 $('#max_weight_accepted').focus();
    	               });	
    	           
    			}else if(max_weight_accepted_value == false){
    				$("#erroralertmodal .modal-body").html("Maximum weight accepted should be digits, with max 4 decimals.");
    	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
   	            	 $('#max_weight_accepted').focus();
 	               });
    			}else if(high_price_first_value == false){
    				$("#erroralertmodal .modal-body").html("High slab shold not be empty.");
    	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
   	            	 $('#high_price').focus();
 	               });
    			}else if (max_weight_accepted <= high_price_value) {
    				$("#erroralertmodal .modal-body").html("Max price slab value should not be greater than or equal to  maximum weight accepted value");
    	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
    	            	$('#high_price').focus();
 	               });
    			}else{
    			var num = parseInt($('#price_slap_hidden_value').val()) + 1;
            	$('#price_slap_hidden_value').val(num);
    			$("#remove_item_" + (num-1)).find('a').remove();
    			
            	var box_html = $('<div id="remove_item_' + num + '" class="add-price-slap table-row inner-block-bg"><div class="col-md-3 padding-left-none"><input type="text" readonly id="low_weight_salb_' + num + '" name="low_weight_salb_' + num + '"  placeholder= "0.00" class="form-control form-control1 dynamic_low_weight" value="'+high_price_value+'"  /></div><div class="col-md-3 padding-left-none"><div><input type="text" class="form-control form-control1 update_txt dynamic_high_weight numberVal" placeholder ="0.00" name="high_weight_slab_' + num + '" value="'+max_weight_accepted+'" id="high_weight_slab_' + num + '" onblur="javascript:checkPriceForInerment(this.value,this.id)"></div></div><div class="col-md-3 padding-left-none"><div><input type="text" class="form-control form-control1 update_txt fivedigitstwodecimals_deciVal numberVal dynamic_prices" placeholder ="0.00" name="price_slab_' + num + '" value="" id="price_slab_' + num + '" /></div></div><div class="col-md-1 form-control-fld padding-left-none padding-top-7"><a href="#" class="remove-box-prices"><i class="fa fa-trash red" title="Delete"></i></a></div></div>');
                box_html.hide();
                $('.slabtable div.add-price-slap:last').after(box_html);
                box_html.fadeIn('slow');
                readonlyProperty();
                $('.numberVal').keypress(function (event) {
                    var keycode = event.keyCode || event.which;
                    if (!(event.shiftKey == false && (keycode == 9 || keycode == 46 || keycode == 8 || keycode == 37 || keycode == 39 || (keycode >= 48 && keycode <= 57)))) {
                        event.preventDefault();
                    }
                });
                if (index_value == 0){
        			$("#high_price").prop('readonly', true);
        		}else{
        			$('#high_weight_slab_'+index_value).prop('readonly', true);
        		}
    			}
    		}else{
    			var high_weight_slab_first_value = /^\d+(\.\d{1,4})?$/i.test($('#high_weight_slab_'+index_value).val());
    			var low_salb = parseFloat($('#low_weight_salb_'+index_value).val());
    			var high_salb = parseFloat($('#high_weight_slab_'+index_value).val());
    			if(high_weight_slab_first_value == true && $('#high_weight_slab_'+index_value).val() != ''){
        			var high_price_value = parseFloat($('#high_weight_slab_'+index_value).val());
    			}else{
    				var high_price_value = '';
    			}
    			if((max_weight_accepted_count == 0)){
    				$("#erroralertmodal .modal-body").html("Maximum weight accepted should not be empty.");
    	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
    	            	$('#max_weight_accepted').focus();
  	               });
    			}else if(high_weight_slab_first_value == false){
    				$("#erroralertmodal .modal-body").html("Maximum weight accepted should be digits, with max 4 decimals.");
    	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
    	            	$('#high_weight_slab_'+index_value).focus();
   	               });
    			}else if (max_weight_accepted <= high_price_value) {
    				$("#erroralertmodal .modal-body").html("Max price slab value should not be greater than or equal to maximum weight accepted value");
    	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
    	            	$('#high_weight_slab_'+index_value).focus();
   	               });
    			}else if (low_salb >= high_price_value) {
    			}else{
	    			var num = parseInt($('#price_slap_hidden_value').val()) + 1;
	            	$('#price_slap_hidden_value').val(num);
	    			$("#remove_item_" + (num-1)).find('a').remove();
	            	var box_html = $('<div id="remove_item_' + num + '" class="add-price-slap table-row inner-block-bg"><div class="col-md-3 padding-left-none"><input type="text" readonly id="low_weight_salb_' + num + '" name="low_weight_salb_' + num + '"  placeholder= "0.00" class="form-control form-control1 dynamic_low_weight" value="'+high_price_value+'"  /></div><div class="col-md-3 padding-left-none"><div><input type="text" class="form-control form-control1 update_txt dynamic_high_weight numberVal" placeholder ="0.00" name="high_weight_slab_' + num + '" value="'+max_weight_accepted+'" id="high_weight_slab_' + num + '" onblur="javascript:checkPriceForInerment(this.value,this.id)"></div></div><div class="col-md-3 padding-left-none"><div><input type="text" class="form-control form-control1 fivedigitstwodecimals_deciVal update_txt numberVal dynamic_prices" placeholder ="0.00" name="price_slab_' + num + '" value="" id="price_slab_' + num + '" /></div></div><div class="col-md-1 form-control-fld padding-left-none  padding-top-7"><a href="#" class="remove-box-prices"><i class="fa fa-trash red" title="Delete"></i></a></div></div>');
	                box_html.hide();
	                $('.slabtable div.add-price-slap:last').after(box_html);
	                box_html.fadeIn('slow');
	                readonlyProperty();
	                $('.numberVal').keypress(function (event) {
	                    var keycode = event.keyCode || event.which;
	                    if (!(event.shiftKey == false && (keycode == 9 || keycode == 46 || keycode == 8 || keycode == 37 || keycode == 39 || (keycode >= 48 && keycode <= 57)))) {
	                        event.preventDefault();
	                    }
	                });
	                if (index_value == 1){
	                	$('#high_weight_slab_'+index_value).prop('readonly', true);
	        		}else{
	        			$('#high_weight_slab_'+index_value).prop('readonly', true);
	        		}
    			}
    		}
            $(".dynamic_low_weight").each(function (item) {
                $(this).rules("add", {
                	required : {
    	            	depends: function(element) {
    	            		if ($('#sellerpoststatus').val() == 1){
    		            		return true;
    		            	}else{
    		            		return false;
    		            	}

    	            	}
                    },
                    number: true
                });
            });
            $(".dynamic_high_weight").each(function (item) {
                $(this).rules("add", {
                	required : {
    	            	depends: function(element) {
    	            		if ($('#sellerpoststatus').val() == 1){
    		            		return true;
    		            	}else{
    		            		return false;
    		            	}

    	            	}
                    },
                    decimalvalidation: true
                });
            });
            $(".dynamic_prices").each(function (item) {
                $(this).rules("add", {
                	required : {
    	            	depends: function(element) {
    	            		if ($('#sellerpoststatus').val() == 1){
    		            		return true;
    		            	}else{
    		            		return false;
    		            	}

    	            	}
                    },
                    number: true
                });
            });
            return false;
        });
    
    
///////////////////////////Price slab for term create quote buyer start here//////////////////////////////////////
    $('.add-price-slap .add-box-buyer').click(function(){
		var index_value = $('#price_slap_hidden_value').val();
   		var max_weight_accepted = $('#max_weight_accepted').val();
   		var max_weight_accepted_value = /^\d+(\.\d{1,4})?$/i.test(max_weight_accepted);
   		var max_weight_accepted_count = max_weight_accepted.length;
   		var high_price_first_value = /^\d+(\.\d{1,4})?$/i.test($('#high_price').val());
   		if (max_weight_accepted_value){
   			var max_weight_accepted = max_weight_accepted;
   		}else{
   			var max_weight_accepted = '';
   		}
		if (index_value == 0){
			if(high_price_first_value == true && $('#high_price').val() != ''){
        		var high_price_value = parseFloat($('#high_price').val());
			}else{
				var high_price_value = '';
			}
			if((max_weight_accepted_count == 0)){
				$("#erroralertmodal .modal-body").html("Maximum weight accepted should not be empty.");
	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
	            	 $('#max_weight_accepted').focus();
	               });	
	           
			}else if(max_weight_accepted_value == false){
				$("#erroralertmodal .modal-body").html("Maximum weight accepted should be digits, with max 4 decimals.");
	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
	            	 $('#max_weight_accepted').focus();
	               });
			}else if(high_price_first_value == false){
				$("#erroralertmodal .modal-body").html("High slab shold not be empty.");
	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
	            	 $('#high_price').focus();
	               });
			}else if (max_weight_accepted <= high_price_value) {
				$("#erroralertmodal .modal-body").html("Max price slab value should not be greater than or equal to  maximum weight accepted value");
	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
	            	$('#high_price').focus();
	               });
			}else{
			var num = parseInt($('#price_slap_hidden_value').val()) + 1;
        	$('#price_slap_hidden_value').val(num);
			$("#remove_item_" + (num-1)).find('a').remove();
        	var box_html = $('<div id="remove_item_' + num + '" class="add-price-slap table-row inner-block-bg"><div class="col-md-3 padding-left-none"><input type="text" readonly id="low_weight_salb_' + num + '" name="low_weight_salb_' + num + '"  placeholder= "0.00" class="form-control form-control1 dynamic_low_weight" value="'+high_price_value+'"  /></div><div class="col-md-3 padding-left-none"><div><input type="text" class="form-control form-control1 update_txt dynamic_high_weight numberVal" placeholder ="0.00" name="high_weight_slab_' + num + '" value="'+max_weight_accepted+'" id="high_weight_slab_' + num + '" onblur="javascript:checkPriceForInerment(this.value,this.id)"></div></div><div class="col-md-1 form-control-fld padding-left-none padding-top-7"><a href="#" class="remove-box-prices"><i class="fa fa-trash red" title="Delete"></i></a></div></div>');
            box_html.hide();
            $('.slabtable div.add-price-slap:last').after(box_html);
            box_html.fadeIn('slow');
            readonlyProperty();
            $('.numberVal').keypress(function (event) {
                var keycode = event.keyCode || event.which;
                if (!(event.shiftKey == false && (keycode == 9 || keycode == 46 || keycode == 8 || keycode == 37 || keycode == 39 || (keycode >= 48 && keycode <= 57)))) {
                    event.preventDefault();
                }
            });
            if (index_value == 0){
    			$("#high_price").prop('readonly', true);
    		}else{
    			$('#high_weight_slab_'+index_value).prop('readonly', true);
    		}
			}
		}else{
			var high_weight_slab_first_value = /^\d+(\.\d{1,4})?$/i.test($('#high_weight_slab_'+index_value).val());
			var low_salb = parseFloat($('#low_weight_salb_'+index_value).val());
			var high_salb = parseFloat($('#high_weight_slab_'+index_value).val());
			if(high_weight_slab_first_value == true && $('#high_weight_slab_'+index_value).val() != ''){
    			var high_price_value = parseFloat($('#high_weight_slab_'+index_value).val());
			}else{
				var high_price_value = '';
			}
			if((max_weight_accepted_count == 0)){
				$("#erroralertmodal .modal-body").html("Maximum weight accepted should not be empty.");
	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
	            	$('#max_weight_accepted').focus();
	               });
			}else if(high_weight_slab_first_value == false){
				$("#erroralertmodal .modal-body").html("Maximum weight accepted should be digits, with max 4 decimals.");
	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
	            	$('#high_weight_slab_'+index_value).focus();
	               });
			}else if (max_weight_accepted <= high_price_value) {
				$("#erroralertmodal .modal-body").html("Max price slab value should not be greater than or equal to maximum weight accepted value");
	            $("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
	            	$('#high_weight_slab_'+index_value).focus();
	               });
			}else if (low_salb >= high_price_value) {
			}else{
    			var num = parseInt($('#price_slap_hidden_value').val()) + 1;
            	$('#price_slap_hidden_value').val(num);
    			$("#remove_item_" + (num-1)).find('a').remove();
            	var box_html = $('<div id="remove_item_' + num + '" class="add-price-slap table-row inner-block-bg"><div class="col-md-3 padding-left-none"><input type="text" readonly id="low_weight_salb_' + num + '" name="low_weight_salb_' + num + '"  placeholder= "0.00" class="form-control form-control1 dynamic_low_weight" value="'+high_price_value+'"  /></div><div class="col-md-3 padding-left-none"><div><input type="text" class="form-control form-control1 update_txt dynamic_high_weight numberVal" placeholder ="0.00" name="high_weight_slab_' + num + '" value="'+max_weight_accepted+'" id="high_weight_slab_' + num + '" onblur="javascript:checkPriceForInerment(this.value,this.id)"></div></div><div class="col-md-1 form-control-fld padding-left-none  padding-top-7"><a href="#" class="remove-box-prices"><i class="fa fa-trash red" title="Delete"></i></a></div></div>');
                box_html.hide();
                $('.slabtable div.add-price-slap:last').after(box_html);
                box_html.fadeIn('slow');
                readonlyProperty();
                $('.numberVal').keypress(function (event) {
                    var keycode = event.keyCode || event.which;
                    if (!(event.shiftKey == false && (keycode == 9 || keycode == 46 || keycode == 8 || keycode == 37 || keycode == 39 || (keycode >= 48 && keycode <= 57)))) {
                        event.preventDefault();
                    }
                });
                if (index_value == 1){
                	$('#high_weight_slab_'+index_value).prop('readonly', true);
        		}else{
        			$('#high_weight_slab_'+index_value).prop('readonly', true);
        		}
			}
		}
        $(".dynamic_high_weight").each(function (item) {
            $(this).rules("add", {
            	required : true,
                decimalvalidation: true
            });
        });
        return false;
    });
    
    
    
        $('.slabtable').on('click', '.remove-box-prices', function(){
        	var remove_item_id = $('#price_slap_hidden_value').val();
        	remove_id = "#remove_item_"+remove_item_id;
            if($(remove_id).remove()){
            	//if($(".price-slap-add > div[id*=remove_item_]").length==1){
            		//var append_delete = '<a href="#" class="remove-box-prices"><i class="fa fa-trash red" title="Delete"></i></a>';
            		//$("#remove_item_"+(remove_item_id-1)+" div.col-md-1").append(append_delete);
            	//}
            	var delete_value = $('#price_slap_hidden_value').val()-1;
            	$('#price_slap_hidden_value').val(delete_value);
        		var index_value = $('#price_slap_hidden_value').val();
        		var max_weight_accepted = parseFloat($('#max_weight_accepted').val());
        		var high_price_accepted = parseFloat($('#high_weight_slab_'+index_value).val());
        		 if (index_value == 0){
        			 var high_price_defualt = $("#high_price").val();
        			 $("#high_price").prop('readonly', false);
        			 if (max_weight_accepted > high_price_defualt ){
          				removeReadonlyProperty();
          			}else{
          				readonlyProperty();
          	    	}
         		}else{
         			$('#high_weight_slab_'+index_value).prop('readonly', false);
         			if (max_weight_accepted > high_price_accepted ){
         				removeReadonlyProperty();
         			}else{
         				readonlyProperty();
         	    	}
         			
         		}      		
            }
            if($(".price-slap-update > div[id*=remove_item_]").length!=1){
            	$(".price-slap-update > div[id*=remove_item_]:last-child").append('<div class="col-md-3 form-control-fld padding-left-none padding-top-7"><a class="remove-box-prices" href="#"><i class="fa fa-trash red" title="Delete"></i></a></div>');
            }
            if($(".price-slap-add > div[id*=remove_item_]").length!=0){
            	$(".price-slap-add > div[id*=remove_item_]:last-child").append('<div class="col-md-3 form-control-fld padding-left-none padding-top-7"><a class="remove-box-prices" href="#"><i class="fa fa-trash red" title="Delete"></i></a></div>');
            }
            return false;
            
        });
    
///////////////////////////Price slab ends here//////////////////////////////////////
    
    
	//update seller post FTL
	var sel_list = new Array();
	$(document).on('click', '#add_more_update', function() {
			if($("#posts-form-lines").valid()) {
				var current_row_id = $("#current_row_id").val();
				var from_location = $('#from_location').val();
				var datepicker_from_value = $('#datepicker').val();
                var datepicker_from_value = convertDateFormatForDatePickerSeller(datepicker_from_value);
				var datepicker_to_value = $('#datepicker_to_location').val();
                var datepicker_to_value = convertDateFormatForDatePickerSeller(datepicker_to_value);
				var seller_district = $('#seller_district_id').val();
				var ts_from = Date.parse(datepicker_from_value);
				var ts_to = Date.parse(datepicker_from_value);
				var from_location_identifier = $('#from_location_id').val();
				var to_location_identifier = $('#to_location_id').val();
				var load_type = $('#load_type').val();
				var vehicle_type = $('#vechile_type').val();
				var to_location = $('#to_location').val();
				var price = $('#price').val();
				var units = $('#transitdays_units').val();
				var transit = $('#transitdays').val();
				var price_numric = /^\d+(\.\d{2})?$/.test(price);
				var transit_numric =  /^[0-9]{1,3}$/.test(transit);
				var load_type_value = $("#load_type option:selected").text();
				var vehicle_type_value = $("#vechile_type option:selected").text();
				if (load_type_value == "Load Type (All)") {
					load_type_value = "All";
				}
				if (vehicle_type_value == "Vehicle Type (All)") {
					vehicle_type_value = "All";
				}
				var vechile_type_value = $("#vechile_type option:selected").text();
				var subscription_start_date_start_val = $('#subscription_start_date_start').val();
				var subscription_end_date_end_val = $('#subscription_end_date_end').val();
				var current_date_seller = $('#current_date_seller').val();
				if (to_location_identifier != '' && from_location_identifier != '' && datepicker_from_value != '' && datepicker_to_value != '' && price != '' && transit != '' && price_numric == true && transit_numric == true) {
					if ((current_date_seller >= subscription_start_date_start_val) && (current_date_seller <= subscription_end_date_end_val) && (datepicker_from_value >= subscription_start_date_start_val) && (datepicker_from_value <= subscription_end_date_end_val) && (datepicker_to_value >= subscription_start_date_start_val) && (datepicker_to_value <= subscription_end_date_end_val)) {
						if (to_location_identifier != '' && from_location_identifier != '' && datepicker_from_value != '' && datepicker_to_value != '' && price != '' && transit != '' && vehicle_type != '' && price_numric == true && transit_numric == true) {
							var unique = from_location_identifier + to_location_identifier + load_type + vehicle_type+transit;

							if ($.inArray(unique, sel_list) == -1) {
								//sel_list.unshift(unique);
								$.ajax({
									type: 'post',
									url: '/lineitemscheck',
									data: {
										'from_location': from_location_identifier,
										'to_location': to_location_identifier,
										'from_date_seller': datepicker_from_value,
										'to_date_seller': datepicker_to_value,
										'vehicle_type': vehicle_type,
										'load_type' : load_type,
										'post_item_id': current_row_id,
                                        'transit_days': transit
									},
									dataType: "html",
									type: 'POST',
									success: function (data) {
										if (data == '0') {
											//row updates
											var rowid = "#single_post_item_" + current_row_id;
											$(rowid + " .from_location_text").html(from_location);
											$(rowid + " .to_location_text").html(to_location);
											$(rowid + " .load_type_text").html(load_type_value);
											$(rowid + " .vehicle_type_text").html(vehicle_type_value);
											$(rowid + " .price_text").html(price);
											$(rowid + " input[name='from_location[]']").val(from_location_identifier);
											$(rowid + " input[name='to_location[]']").val(to_location_identifier);
											$(rowid + " input[name='load_type[]']").val(load_type);
											$(rowid + " input[name='vechile_type[]']").val(vehicle_type);
											$(rowid + " input[name='transitdays[]']").val(transit);
											$(rowid + " input[name='units[]']").val(units);
											$(rowid + " input[name='sellerdistrict[]']").val(seller_district);
											$(rowid + " input[name='price[]']").val(price);
											var id_line_itemes = $('.request_rows').children().size();
											if (id_line_itemes == 0) {
											} else {
												$("#datepicker").prop('disabled', true);
											}
											$("#valid_from_val").val(datepicker_from_value);
											$("#valid_to_val").val(datepicker_to_value);
											$('#from_location').val("");
											$('#from_location_id').val("");
											$('#to_location').val("");
											$('#to_location_id').val("");
											$('#vechile_type').val("");
											$('#load_type').val("");
											$('#transitdays').val("");
											$('#price').val("");
											$('.selectpicker').selectpicker('refresh');
											$('#add_more_update').hide();
											//$('#vechile_type').trigger("change");
											$('#dimension').hide();


										} else {
											$("#erroralertmodal .modal-body").html("Post already exists for the specified parameters! Please modify parameters.");
					                        $("#erroralertmodal").modal({
					                            show: true
					                        });
										}
									},
									error: function () {
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
					} else {
						$("#erroralertmodal .modal-body").html("Your post valid from date is beyond your subscription date, please select valid from date within your subscription validity date");
		                $("#erroralertmodal").modal({
		                    show: true
		                });

					}
				}
			}
	});

    //update seller post PTL
    $('#add_more_update_ptl').click(function() {
        if($("#posts-form-lines_ptl").valid()) {
            var current_row_id = $("#current_row_id").val();

            var datepicker_from_value = $('#datepicker').val();
            var datepicker_from_value = convertDateFormatForDatePickerSeller(datepicker_from_value);
            var datepicker_to_value = $('#datepicker_to_location').val();
            var datepicker_to_value = convertDateFormatForDatePickerSeller(datepicker_to_value);
            var price_ptl_value = $('#price_ptl').val();
            var transitdays_units_ptl_value = $('#transitdays_units_ptl').val();
            var transitdays_ptl_value = $('#transitdays_ptl').val();
            var transit_value_days = transitdays_ptl_value+" "+transitdays_units_ptl_value;
            var subscription_start_date_start_val = $('#subscription_start_date_start').val();
            var subscription_end_date_end_val = $('#subscription_end_date_end').val();
            var current_date_seller = $('#current_date_seller').val();
            var from_location_ptl = $('#from_location_ptl').val();
            var to_location_ptl = $('#to_location_ptl').val();


            if ($("#zone_wise_ptl").is(":checked")) {
                var zone_location_id = 1;
            }else{
                var zone_location_id = 2;
            }


            if((current_date_seller >= subscription_start_date_start_val) && (current_date_seller <= subscription_end_date_end_val) && (datepicker_from_value >= subscription_start_date_start_val) && (datepicker_from_value <= subscription_end_date_end_val) && (datepicker_to_value >= subscription_start_date_start_val) && (datepicker_to_value <= subscription_end_date_end_val)){

                if (datepicker_from_value != '' && datepicker_to_value != '' && from_location_ptl != '' && to_location_ptl != ''&& price_ptl_value != '' && transitdays_ptl_value != '' ) {

                    var rowid = "#single_post_item_" + current_row_id;
                    $(rowid + " .price_text").html(price_ptl_value);
                    $(rowid + " .transitdays_text").html(transit_value_days);

                    $(rowid + " input[name='units[]']").val(transitdays_units_ptl_value);
                    $(rowid + " input[name='price[]']").val(price_ptl_value);
                    $(rowid + " input[name='transitdays[]']").val(transitdays_ptl_value);
                    $("#valid_to_val").val(datepicker_to_value);
                    $('#add_more_update_ptl').hide();

                    var id_line_itemes_ptl = $('.request_rows_ptl').children().size();
                    if (id_line_itemes_ptl == 0){
                    }else{
                        $("#datepicker").prop('disabled', true);
                        $("#datepicker_to_location").prop('disabled', true);
                        $("#zone_wise_ptl").prop('disabled', true);
                        $("#location_wise_ptl").prop('disabled', true);
                    }
                    $("#valid_from_val").val(datepicker_from_value);
                    $("#valid_to_val").val(datepicker_to_value);
                    $("#post_type_id").val(zone_location_id);
                    $('#from_location_ptl').val("");
                    $('#to_location_ptl').val("");
                    $('#transitdays_ptl').val("");
                    $('#price_ptl').val("");
                    $('.selectpicker').selectpicker('refresh');
                }
            }else{
                $("#erroralertmodal .modal-body").html("Your post valid from date is beyond your subscription date, please select valid from date within your subscription validity date");
                $("#erroralertmodal").modal({
                    show: true
                });

            }
        }
    });
    //ptl search validate
    $("#sellers-posts-buyers-ptl").validate({
        ignore: [],
        rules : {
            "dispatch_date" : {
                required : true,
            },
            "from_location_id" : {
                required : true,
            },
            "to_location_id" : {
                required : true,
            },
            "lkp_load_type_id" : {
                required : true,
            },
            "lkp_packaging_type_id" : {
                required : true,
            },
            "lkp_air_ocean_shipment_type_id" : {
                required : true,
            },
            "lkp_air_ocean_sender_identity_id" : {
                required : true,
            }
        },
        errorPlacement: function(error, element) {
            $(element).parent().parent().append(error);
        },
        messages : {
            "dispatch_date" : {
                required : "Dispatch date is required",
            },
            "from_location_id" : {
            	required : 
	                function() {
	                    var ZLValue = $("#zone_or_location").val();
	                    var serviceValue = $("#serviceid").val();
	                    var postDelivery = $("#post_delivery_type").val();
	                   
	                    if(serviceValue == 8){
	                    	return "From Airport is required";
	                    }
	                    else if	(serviceValue == 9){
	                    	return "From Ocean is required";
	                    }
	                    else{
		                    if (ZLValue == 1) {
		                    	return "From Zone is required";
		                    } else {
		                    	return "From Pincode is required";
		                    }
	                    }
	                }
            },
            "to_location_id" : {
            	required : 
	                function() {
	                    var ZLValue = $("#zone_or_location").val();
	                    var serviceValue = $("#serviceid").val();
	                    var postDelivery = $("#post_delivery_type").val();
	                   
	                    if(serviceValue == 8){
	                    	return "To Airport is required";
	                    }
	                    else if	(serviceValue == 9){
	                    	return "To Ocean is required";
	                    }
	                    else{
		                    if (ZLValue == 1) {
		                    	if(postDelivery == 2)
		                    		return "To Country is required";
		                    	else
		                    		return "To Zone is required";
		                    } else {
		                    	if(postDelivery == 2)
		                    		return "To Country is required";
		                    	else
		                    		return "To Pincode is required";
		                    }
	                    }
	                }
            },
            "lkp_load_type_id" : {
                required : "load type is required",
            },
            "lkp_packaging_type_id" : {
                required : "packaging type is required",
            },
            "lkp_air_ocean_shipment_type_id" : {
                required : "Shipment type is required",
            },
            "lkp_air_ocean_sender_identity_id" : {
                required : "Sender is required",
            }
        },
        submitHandler : function(form) {
            form.submit();
        },
    });

    $('#globalpostlistcheck').click(function(){
        if($(this).prop("checked")) {
            $(".checkBoxClass").prop("checked", true);
        } else {
            $(".checkBoxClass").prop("checked", false);
        }                
    });
     

    $(".gridcheckbox").click(function(e){
    	e.stopImmediatePropagation();
    }); 
    
   $(".gridcheckboxitems").click(function(e){
    	e.stopImmediatePropagation();
    });
    
    $.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Please Enter Alpha numberic."
    ); 
      
   //ptl add tier master validate
   $("#ptl-add-tier").validate({
       ignore: [],
       rules : {
           "tier_name" : {
               required : true, regex: "^[a-zA-Z0-9 ']{2,50}$",
           },
           "tier_code" : {
               required : true, regex: "^[a-zA-Z0-9']{1,50}$",
           }
           
       },
       errorPlacement: function(error, element) {
           $(element).parent('div').append(error);
       },
       messages : { 
       	
       	"tier_name" : {
           required : "Please enter tier name",
       },
       "tier_code" : {
           required : "Please enter tier code",
       }
       },
       submitHandler : function(form) {
           form.submit();
       },
   });
   
 //ptl zone master validate
   $("#ptl-add-zone").validate({
       ignore: [],
       rules : {
           "zone_name" : {
               required : true,regex: "^[a-zA-Z0-9 ']{2,50}$",
           },
           "zone_code" : {
               required : true,regex: "^[a-zA-Z0-9']{1,50}$",
           }
           
       },
       errorPlacement: function(error, element) {
           $(element).parent('div').append(error);
       },
       messages : { 
       	
       	"zone_name" : {
           required : "Please enter zone name",
       },
       "zone_code" : {
           required : "Please enter zone code",
       }
       },
       submitHandler : function(form) {
           form.submit();
       },
   });
   
   //ptl sector master validate
   $("#ptl-add-sector").validate({
       ignore: [],
       rules : {
           "sector_name" : {
               required : true,regex: "^[a-zA-Z0-9 ']{2,50}$",
           },
           "sector_code" : {
               required : true,regex: "^[a-zA-Z0-9']{1,50}$",
           },
           "zone_id" : {
               required : true,
           },
           "tier_id" : {
               required : true,
           }
           
       },
       errorPlacement: function(error, element) {
           $(element).parent('div').append(error);
       },
       messages : {
           "sector_name" : {
               required : "Please enter sector name",
           },
           "sector_code" : {
               required : "Please enter sector code",
           },
           "zone_id" : {
               required : "Please select zone",
           },
           "tier_id" : {
               required : "Please select tier",
           }
           
       },
       submitHandler : function(form) {
           form.submit();
       },
   });  
   
   //ptl add-pincode master validate
   $("#ptl-add-pincode").validate({
       ignore: [],
       rules : {
           "ptl_pincode_id" : {
               required : true,
               number:true,
               rangelength : [ 5, 6 ]
           },
           "ptl_sector_id" : {
               required : true
           },
           "oda_pincode" : {
               required : true
           }
       },
       errorPlacement: function(error, element) {
           $(element).parent('div').append(error);
       },
       messages : {
           "ptl_pincode_id" : {
        	    required : "Please enter your pincode / zipcode",
				number : "Please enter only numbers in pincode field",
				maxlength : "Please enter pincode between 4 to 6 character long"
           },
           "ptl_sector_id" : {
               required : "Please select sector"
           },
           "oda_pincode" : {
               required : "Please select ODA"
           }
       },
       submitHandler : function(form) {
           form.submit();
       },
   });  
   
   //TRANSIT MATRIX INLINE EDIT
   $('#tblcontentEdit').hide();
   $("table.editable td").click( function(e){
       if($(this).find('input').length){
            return ;   
       }      
       
   	//apend id to textbox
       var td_id = $(this).attr('id'); 
      
         
       var input = $("<input type='text'  class='form-control form-control1 editableColumn clsTransitDays'  id="+td_id+">").val( $(this).text() );
       $(this).empty().append(input);
       $(this).find('input')
       .focus(function(e){
           if($(this).val()=='0' || $(this).val()==''){$(this).val('');}
       }).keydown(function(event){
            
            if ( event.keyCode == 46 || event.keyCode == 8 || event.keyCode == 9 || event.keyCode == 27 || event.keyCode == 190  || event.keyCode == 13 || 
                 // Allow: Ctrl+A
                (event.keyCode == 65 && event.ctrlKey === true) || 
                // Allow: home, end, left, right
                (event.keyCode >= 35 && event.keyCode <= 39)) {
                // let it happen, don't do anything
                return;
            }
            else {
               // Ensure that it is a number and stop the keypress
               if (event.shiftKey || (event.keyCode < 48 || event.keyCode > 57) && (event.keyCode < 96 || event.keyCode > 105 )) {
                   event.preventDefault(); 
               }   
           }

       }).blur( function( e ){
                 	if($(this).val()!="" && ($(this).val() > 0) ){
   					if (!isNaN(parseFloat($(this).val()))) {
   						var val1=parseFloat($(this).val()).toFixed();
   						$(this).val(val1);
   						$(this).parent('td').text( 
   							  $(this).val()
   						);
   					  }
   					}
   					else{
   						$(this).parent('td').text("");
   					}
               });  
       $('.editableColumn').blur(function(){

       	
       	var transitId = $(this).attr('id');
   		var transitValue = $(this).val();
   		// passing the data to the ajax function
   		// that is further passed to the controller

   		datastr = '&transitId=' + transitId+'&transitValue=' + transitValue;

        /* Max Between 1 - 999 */
        if(transitValue <= 0 || parseInt(transitValue) > 999){
        	$("#erroralertmodal .modal-body").html("Transit Days Should be greater than 0 and Less than 999");
	        $("#erroralertmodal").modal({
	            show: true
	        });
			return false;
        }else{
	   		// ajax function starts
	   		$.ajax({
	   			type : 'post', // defining the ajax type
	   			url : '/ptlmasters/editTransits', // calling the controller with the
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
	   			dataType : 'html', // datatype
	   			data : datastr, // passing the data used for operation
	   			success : function(html) {
	   				$('#tblcontentEdit').show();
	   				setTimeout(function () { FadeDiv(); }, 4000);
	   				 function FadeDiv()
	   				  {
	   				    $('#tblcontentEdit').fadeOut();
	   				  }
	   			}
	   		});
        }
       });
     
   });

   
   // PINCODE MASTER AUTOCOMPLETE PTL MASTER 
   $("#ptl_pincode_id").click(function() {
     
       $("#ptl_pincode_id").autocomplete({
           source: "/zipautocomplete",
           minLength: 1,
           select: function( event, ui ) {
        	   $('#ptl_pincode_id').val(ui.item.pincode);
               $('#ptlPincodeId').val(ui.item.id);
               $('#pincode_location').val(ui.item.postoffice_name + ', ' + ui.item.taluk);
               $('#postal_division').val(ui.item.divisionname);
               $('#pincode_city').val(ui.item.regionname);
               $('#pincode_district').val(ui.item.districtname);
               $('#pincode_state').val(ui.item.statename);
               
               $('#auto_division_name').html(ui.item.postoffice_name);
               $('#auto_district_name').html(ui.item.districtname);
               $('#auto_state_name').html(ui.item.statename);
               /*Need to add this below class to every autocomplete: Shriram */
                $("#ptl_pincode_id").addClass("clsAutoDisable");
               return false;
           },
        change: function(event, ui) {
           if (!ui.item) {
        	   $('#ptl_pincode_id').val('');
               $('#ptlPincodeId').val('');
           }
       }
          
       });
   });

// PTL MASTER AUTOCOMPLETE ALL FIELDS AFTER SELECTING PINCODE
   $("#ptl_sector_id").change(function(){
       var sectorId = $(this).val();
      
  		// passing the data to the ajax function
  		// that is further passed to the controller

  		datastr = '&sectorId=' + sectorId;

  		// ajax function starts
  		$.ajax({
  			type : 'post', // defining the ajax type
  			url : '/ptlmasters/fillform', // calling the controller with the
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
  				$tierName = html.tier_name;
  				$zoneName = html.zone_name;
  				$('#lkp_zone_id').val($zoneName);
  				$('#lkp_tier_id').val($tierName);
  				
  				}
  		});
       
   });
   
   $.calculator.setDefaults({showOn: 'both', buttonImageOnly: true, buttonImage: '../../../images/calc.png'});
   $('#calculatoropen').calculator();
   $('#calculatoropen1').calculator();
   $('#calculatoropen2').calculator();
   $('#calculatoropen3').calculator();
   $(".cla_class").next().css("margin-top", "-5px");
   $("#lkp_load_type_id, #lkp_vehicle_type_id").on('change',function(){
    selectedresponse = "";
       $("input:checkbox[name=search_by_user]:checked").each(function(){
           selectedresponse = selectedresponse+","+$(this).val();
       });
       $("#selected_users").val(selectedresponse);
       selectedprice = "";
       $("input:checkbox[name=search_by_price]:checked").each(function(){
           selectedprice = selectedprice+","+$(this).val();
       });
       $("#selected_prices").val(selectedprice);
       selectedfrom = "";
       $("input:checkbox[name=search_by_from]:checked").each(function(){
           selectedfrom = selectedfrom+","+$(this).val();
       });
       $("#selected_from_date").val(selectedfrom);
       selectedto = "";
       $("input:checkbox[name=search_by_to]:checked").each(function(){
           selectedto = selectedto+","+$(this).val();
       });
       $("#selected_to_date").val(selectedto);
        selectedPayments = "";
        $("input:checkbox[name=search_by_payment]:checked").each(function(){
           selectedPayments = selectedPayments+","+$(this).val();
       });
       
       $("#selected_payments").val(selectedPayments);
      // alert(selectedPayments);
       selectedFlexibleDispatch = "";
       $("input:radio[name=date_flexiable]:checked").each(function(){
           selectedFlexibleDispatch = $(this).val();
       });
       $("#selected_flexible_dispatch").val(selectedFlexibleDispatch);

       selectedFlexibleDelivery = "";
       $("input:radio[name=del_date_flexiable]:checked").each(function(){
           selectedFlexibleDelivery = $(this).val();
       });
       $("#selected_flexible_delivery").val(selectedFlexibleDelivery);

       selectedLoadType = "";
       selectedLoadType = $("#lkp_load_type_id").val();
       $("#selected_load_type_id").val(selectedLoadType);

       selectedVehicleType = "";
       selectedVehicleType = $("#lkp_vehicle_type_id").val();
       $("#selected_vehicle_type_id").val(selectedVehicleType);
       
       $(".filter_form").submit();
   });
   $(".filtercheckbox").click(function(){
	   selectedresponse = "";
	   $("input:checkbox[name=search_by_user]:checked").each(function(){
		   selectedresponse = selectedresponse+","+$(this).val();
	   });
	   $("#selected_users").val(selectedresponse);
	   selectedprice = "";
	   $("input:checkbox[name=search_by_price]:checked").each(function(){
		   selectedprice = selectedprice+","+$(this).val();
	   });
	   $("#selected_prices").val(selectedprice);
	   selectedfrom = "";
	   $("input:checkbox[name=search_by_from]:checked").each(function(){
		   selectedfrom = selectedfrom+","+$(this).val();
	   });
	   $("#selected_from_date").val(selectedfrom);
	   selectedto = "";
	   $("input:checkbox[name=search_by_to]:checked").each(function(){
		   selectedto = selectedto+","+$(this).val();
	   });
	   $("#selected_to_date").val(selectedto);
	   	selectedPayments = "";
	    $("input:checkbox[name=search_by_payment]:checked").each(function(){
		   selectedPayments = selectedPayments+","+$(this).val();
	   });
	   
	   $("#selected_payments").val(selectedPayments);
	  // alert(selectedPayments);
       selectedFlexibleDispatch = "";
       $("input:radio[name=date_flexiable]:checked").each(function(){
           selectedFlexibleDispatch = $(this).val();
       });
       $("#selected_flexible_dispatch").val(selectedFlexibleDispatch);

       selectedFlexibleDelivery = "";
       $("input:radio[name=del_date_flexiable]:checked").each(function(){
           selectedFlexibleDelivery = $(this).val();
       });
       $("#selected_flexible_delivery").val(selectedFlexibleDelivery);

       selectedLoadType = "";
       selectedLoadType = $("#lkp_load_type_id").val();
       $("#selected_load_type_id").val(selectedLoadType);

       selectedVehicleType = "";
       selectedVehicleType = $("#lkp_vehicle_type_id").val();
       $("#selected_vehicle_type_id").val(selectedVehicleType);
	   
	   $(".filter_form").submit();

   });

    $(".cancel_spost_yes").on("click",function(){
  
    var allVals = [];
				
	var str = $("#cancellationstrposts").val(); 
	var postid = $("#cancellationpostid").val();  
	if(str == 'items'){
		var allVals = postid.split(",");
		//var allVals = postid;
		//alert(allVals); //return false;
	} else { 
		allVals.push(postid);
	}
	datastr = '&postIds=' + allVals +'&str=' + str;
		
		// ajax function starts
		$.ajax({
			type : 'post', // defining the ajax type
			url : '/sellerpostcancel', // calling the controller with the
			// action involved
			dataType : 'html', // datatype
			data : datastr, // passing the data used for operation
			beforeSend: function () {
                        $("#cancelsellerpostmodal").modal('hide');
                        $.blockUI({
                            overlayCSS: {
                                backgroundColor: '#000'
                            }
                        });
                    },
                    complete: function () {
                        $.unblockUI();
                    },
			success : function(html) {
					$("#erroralertmodal .modal-body").html(html);
					$("#erroralertmodal").modal({
                        show: true
                    }).one('click','.ok-btn',function (e){
                        location.reload();
                    });

					$('.checkBoxClass').each(function(){
						this.checked = false;
						});
					$('input:checkbox#globalpostlistcheck').removeAttr('checked');
					
					}
		});
	});
});

//seller post status update
function updatepoststatus(status){
	$("#sellerpoststatus").val(status);
}
//seller post status update

function updatepostlineitem(postid){

	var rowid = "#single_post_item_"+postid;
    if($('#posts-form-lines').length){	
    	$( "#posts-form-lines input[name='from_location']" ).val($( rowid +" .from_location_text" ).html());
    	$( "#posts-form-lines input[name='from_location_id']" ).val($( rowid +" input[name='from_location[]']" ).val());
    	$( "#posts-form-lines input[name='to_location']" ).val($( rowid +" .to_location_text" ).html());
    	$( "#posts-form-lines input[name='to_location_id']" ).val($( rowid +" input[name='to_location[]']" ).val());
    	$( "#posts-form-lines input[name='transitdays']" ).val($( rowid +" input[name='transitdays[]']" ).val());
    	//$( "#posts-form-lines input[name='units']" ).val($( rowid +" input[name='units[]']" ).val());
    	$("#transitdays_units option[value='"+$( rowid +" input[name='units[]']" ).val()+"']").prop('selected', true);
    	$( "#posts-form-lines input[name='price']" ).val($( rowid +" input[name='price[]']" ).val()	);
    	$("#load_type option[value='"+$( rowid +" input[name='load_type[]']" ).val()+"']").prop('selected', true);
    	$("#vechile_type option[value='"+$( rowid +" input[name='vechile_type[]']" ).val()+"']").prop('selected', true);
    	$("#current_row_id").val(postid);
    	$('.selectpicker').selectpicker('refresh');
    	$('#add_more_update').show();
    	$('#dimension').show();
    	$('#vechile_type').trigger("change");
    }else if($('#truckhaul-posts-form-lines').length){    
        $( "#truckhaul-posts-form-lines input[name='from_location']" ).val($( rowid +" .from_location_text" ).html());
        $( "#truckhaul-posts-form-lines input[name='from_location_id']" ).val($( rowid +" input[name='from_location[]']" ).val());
        $( "#truckhaul-posts-form-lines input[name='to_location']" ).val($( rowid +" .to_location_text" ).html());
        $( "#truckhaul-posts-form-lines input[name='to_location_id']" ).val($( rowid +" input[name='to_location[]']" ).val());
        $( "#truckhaul-posts-form-lines input[name='transitdays']" ).val($( rowid +" input[name='transitdays[]']" ).val());
        $( "#truckhaul-posts-form-lines input[name='vehicle_number']" ).val($( rowid +" input[name='vehicle_number[]']" ).val());
        $( "#truckhaul-posts-form-lines input[name='price']" ).val($( rowid +" input[name='price[]']" ).val());
        //$( "#truckhaul-posts-form-lines input[name='units']" ).val($( rowid +" input[name='units[]']" ).val());
        $("#transitdays_units option[value='"+$( rowid +" input[name='units[]']" ).val()+"']").prop('selected', true);
        $( "#posts-form-lines input[name='price']" ).val($( rowid +" input[name='price[]']" ).val() );
        $("#load_type option[value='"+$( rowid +" input[name='load_type[]']" ).val()+"']").prop('selected', true);
        $("#vechile_type option[value='"+$( rowid +" input[name='vechile_type[]']" ).val()+"']").prop('selected', true);
        $("#vechile_type option[value='"+$( rowid +" input[name='vechile_type[]']" ).val()+"']").prop('selected', true);
        $("#current_row_id").val(postid);
        $('.selectpicker').selectpicker('refresh');
        $('#add_more_update_th').show();
        $('#dimension').show();
        $('#vechile_type').trigger("change");
	}else if($('#posts-form-lines_oceanint').length){    
		
		$('#shipment_types').selectpicker('val',$( rowid +" input[name='shipment_type[]']" ).val());
	 	CheckVolume($( rowid +" input[name='shipment_type[]']" ).val());
	 	setTimeout(function(){
	 		$('#volumetype').selectpicker('val',$( rowid +" input[name='shipment_volume[]']" ).val());
	 		}, 1000);
	    $('#oceantransitdays').val($( rowid +" input[name='transitdays[]']" ).val());
	    $('#Odcharges').val($( rowid +" input[name='od_charges[]']" ).val());
	    $('#freightcharge').val($( rowid +" input[name='freight_charges[]']" ).val());
	    $('#oceantransitdays_units').selectpicker('val',$( rowid +" input[name='units[]']" ).val());
	    
	    $('#from_location').val($('#from_location_id_inter_text').val());
	    $('#to_location').val($('#to_location_id_inter_text').val());
	    $('#from_location_id').val($('#from_location_id_inter').val());
	    $('#to_location_id').val($('#to_location_id_inter').val());
        $( "#truckhaul-posts-form-lines input[name='from_location_id']" ).val($( rowid +" input[name='from_location[]']" ).val());
	    
	    $("#current_row_id").val(postid);
	    $('.selectpicker').selectpicker('refresh');
	    $('#add_more_update_inter').show();
    }else if($('#trucklease-posts-form-lines').length){   
        $( "#trucklease-posts-form-lines input[name='from_location']" ).val($( rowid +" .from_location_text" ).html());
        $( "#trucklease-posts-form-lines input[name='from_location_id']" ).val($( rowid +" input[name='from_location[]']" ).val());
        $( "#trucklease-posts-form-lines input[name='price']" ).val($( rowid +" input[name='price[]']" ).val());
        $( "#trucklease-posts-form-lines input[name='minimum_lease_period']" ).val($( rowid +" input[name='minimum_lease_period[]']" ).val());
        $( "#trucklease-posts-form-lines input[name='VehicleNumber']" ).val($( rowid +" input[name='vehicle_make_model_year[]']" ).val());
        $("#load_type option[value='"+$( rowid +" input[name='load_type[]']" ).val()+"']").prop('selected', true);
        $("#lease_type option[value='"+$( rowid +" input[name='lease_term[]']" ).val()+"']").prop('selected', true);
        $("#vechile_type option[value='"+$( rowid +" input[name='vechile_type[]']" ).val()+"']").prop('selected', true);
        $("#fuel_need option[value='"+$( rowid +" input[name='fuel_included[]']" ).val()+"']").prop('selected', true);
        $("#states option[value='"+$( rowid +" input[name='permit_item[]']" ).val()+"']").prop('selected', true);
        if($( rowid +" input[name='minimum_lease_period[]']" ).val()!=0){
        $("#driver_cost").attr('disabled',false);
        }
        if($( rowid +" input[name='driver_availability[]']" ).val()!=0){
        $("#check_driver_availablity").prop('checked',true);
        }
        $( "#trucklease-posts-form-lines input[name='driver_cost']" ).val($( rowid +" input[name='driver_charges[]']" ).val());
        var data = $( rowid +" input[name='prefered_goods[]']" ).val();
        var datastates = $( rowid +" input[name='prermit_states[]']" ).val();
        var valArr = data.split(",");
        var i = 0, size = valArr.length;
        for (i; i < size; i++) {
            $('#load_type').multiselect('select', valArr[i]);
         }
        var valArrState = datastates.split(",");
        var i = 0, size = valArrState.length;
        for (i; i < size; i++) {
            $('#permitstates').multiselect('select', valArrState[i]);
        }
        $("#current_row_id").val(postid);
        $('.selectpicker').selectpicker('refresh');
        $('#add_more_update_tl').show();
        $('#dimension').show();
        $('#vechile_type').trigger("change");
        
        
    }
}

function updatecreatepostlineitem(postid){
	
	var rowid = "#single_post_item_"+postid;
	
	$( "#posts-form-lines input[name='from_location']" ).val($( rowid +" .from_location_text" ).html());
	$( "#posts-form-lines input[name='from_location_id']" ).val($( rowid +" input[name='from_location[]']" ).val());
	$( "#posts-form-lines input[name='to_location']" ).val($( rowid +" .to_location_text" ).html());
	$( "#posts-form-lines input[name='to_location_id']" ).val($( rowid +" input[name='to_location[]']" ).val());
	$( "#posts-form-lines input[name='transitdays']" ).val($( rowid +" input[name='transitdays[]']" ).val());
	//$( "#posts-form-lines input[name='units']" ).val($( rowid +" input[name='units[]']" ).val());
	$("#transitdays_units option[value='"+$( rowid +" input[name='units[]']" ).val()+"']").prop('selected', true);
	$( "#posts-form-lines input[name='price']" ).val($( rowid +" input[name='price[]']" ).val()	);
	$("#load_type option[value='"+$( rowid +" input[name='load_type[]']" ).val()+"']").prop('selected', true);
	$("#vechile_type option[value='"+$( rowid +" input[name='vechile_type[]']" ).val()+"']").prop('selected', true);
	$("#current_row_id").val(postid);
	$('.selectpicker').selectpicker('refresh');
	$('#add_more_update').show();
	$('#dimension').show();
	$('#vechile_type').trigger("change");
	
	
	
}

function updatePtlpostlineitem(postid){

    var rowid = "#single_post_item_"+postid;

    $( "#posts-form-lines_ptl input[name='from_location']" ).val($( rowid +" .from_location_text" ).html());
    $( "#posts-form-lines_ptl input[name='from_location_id']" ).val($( rowid +" input[name='from_location[]']" ).val());
    $( "#posts-form-lines_ptl input[name='to_location']" ).val($( rowid +" .to_location_text" ).html());
    $( "#posts-form-lines_ptl input[name='to_location_id']" ).val($( rowid +" input[name='to_location[]']" ).val());
    $( "#posts-form-lines_ptl input[name='transitdays']" ).val($( rowid +" input[name='transitdays[]']" ).val());
    $( "#posts-form-lines_ptl input[name='units']" ).val($( rowid +" input[name='units[]']" ).val());
	$("#posts-form-lines_ptl option[value='"+$( rowid +" input[name='units[]']" ).val()+"']").prop('selected', true);
    $( "#posts-form-lines_ptl input[name='price']" ).val($( rowid +" input[name='price[]']" ).val()	);
    $("#datepicker_to_location").removeAttr('disabled');
    $("#datepicker_to_location").removeAttr('readonly');
    $("#current_row_id").val(postid);
    $('.selectpicker').selectpicker('refresh');
    $('#add_more_update_ptl').show();

}

function sellerpostcancel(str,postid){
	//if ($("input[type='checkbox'][name='sellerpostcheck']:checked").length==0){
		/*$("#erroralertmodal .modal-body").html("Check atleast one post to delete");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;*/
   // }else{
	
	var answer = confirm ("Are you sure you want to delete the post?");
	  if (answer)
	    {
		  var allVals = [];
				
		     /*$('input:checkbox.checkBoxClass').each(function() {
		    	 
		      
		     if($(this).is(":checked")==true){
		    	
		    	 allVals.push($(this).val());
		      }
		     });*/
			if(str == 'items'){
				var allVals = postid.split(",");
				//var allVals = postid;
				//alert(allVals); //return false;
			} else { 
				allVals.push(postid);
			}
		datastr = '&postIds=' + allVals +'&str=' + str;
		 //return false;
		// ajax function starts
		$.ajax({
			type : 'post', // defining the ajax type
			url : '/sellerpostcancel', // calling the controller with the
			// action involved
			dataType : 'html', // datatype
			data : datastr, // passing the data used for operation
			success : function(html) {
					alert(html);
					location.reload();
					$('.checkBoxClass').each(function(){
						this.checked = false;
						});
					$('input:checkbox#globalpostlistcheck').removeAttr('checked');
					
					}
		});
   }
  //}	  
 }	  

/*function subcriptionuserservice(serviceidcheck,url){
	if($("#settabpage").val() != ""){
		url = $("#settabpage").val();
	}
    $.ajax({
        type : 'post',
        url : '/checksubcriptionuser',
        data : {
            'serviceidcheck_id' : serviceidcheck,
        },
        dataType : "html",
        type : 'POST',       
        success : function(data) {
        	if (data == 1) {
        		$.ajax({
				type : 'post', // defining the ajax type
				url : '/set_session_service', // calling the controller with the
				dataType : 'html', // datatype
				data : {
		            'service' : serviceidcheck,
		        },
				success : function(html) {
						  window.location= url;						
						}
				});        		
				
			}else{
				
				if (confirm("You have not subscribed to the service. Do you want to subscribe now?") == true) {
					window.location="/editmyprofile";
				}else{
					return false;
				}
			}
        },
        error : function() {
            alert("error");
            
        }
    	});
}
function buyersetservice(serviceidcheck,url){
	if($("#settabpage").val() != ""){
		url = $("#settabpage").val();
	}
    $.ajax({
        type : 'post',
        url : '/set_session_service',
        data : {
            'service' : serviceidcheck,
        },
        dataType : "html",
        type : 'POST',
        success : function(data) {
        	 window.location= url;	
        },
        error : function() {
            alert("error");
            
        }
    	});
}

function setTabsPage(pageToRedirect){
	$("#settabpage").val(pageToRedirect);
}

function checkSession(serviceidcheck,url){
	$("#settabpage").val();
	 $.get( "/processing-status", function( data ) {
                 var sessionVal = data;
                 if(sessionVal == 0){
                 	$("#erroralertmodal .modal-body").html("Please select any service to proceed");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                 	return false;
                 }else{ 
                 	return subcriptionuserservice(serviceidcheck,url);
                 }
            });
}*/
function selleritemdetial(str,itemid){
	
	var answer = confirm ("Are you sure you want to delete the post?");
	  if (answer)
	    {
		  
		datastr = '&postIds=' + itemid +'&str=' + str;
		// ajax function starts
		$.ajax({
			type : 'post', // defining the ajax type
			url : '/sellerpostcancel', // calling the controller with the
			// action involved
			dataType : 'html', // datatype
			data : datastr, // passing the data used for operation
			success : function(html) {
					alert(html);
					location.reload();
					$('.checkBoxClass').each(function(){
						this.checked = false;
						});
					$('input:checkbox#globalpostlistcheck').removeAttr('checked');
					
					}
		});
   }
 }	


function relocationsellerpostcancel(postId){
	
	var answer = confirm ("Are you sure you want to delete the post?");
	  if (answer)
	    {			  
		$.ajax({
			type : 'post',
			url : '/relocationsellerpostcancel/'+postId +' ',			
			dataType : 'html',
			//data : postId, // passing the data used for operation
			success : function(html) {
					alert(html);
					location.reload();					
					}
		});
   }
 }	


$(document).ready(function(){
    $(".left-bar").click(function(){
        $(".right-bar").removeClass("animateclass1");
        $(".main-right").animate({right: "-100%"}, 300,function(){
        	$(".main-right").hide();
            if($(".left-bar").hasClass("animateclass")){
                $(".main-left").animate({left: "-100%"}, 300);
                $(".left-bar").removeClass("animateclass");
            } else {
                $(".main-left").animate({left: "0"}, 300).css("display", "block");
                $(".left-bar").addClass("animateclass");
            }
        });
    });
    $(".right-bar").click(function(){
        $(".left-bar").removeClass("animateclass");
        $(".main-left").animate({left: "-100%"}, 300, function(){
            if($(".right-bar").hasClass("animateclass1")){
                $(".main-right").animate({right: "-100%"}, 300,function(){
                	$(".main-right").hide();
                });
                $(".right-bar").removeClass("animateclass1");
            } else {
                $(".main-right").show("",function(){
                	$(".main-right").animate({right: "0"}, 300);
                });
                $(".right-bar").addClass("animateclass1");
            }
        });
    });
    

$("#seller_post_info_links a").click(function(){
var d = $(this).attr('data-showdiv'); 

if(d == "ftl-seller-enquiry") {
	$("#ftl-seller-enquiry").show(); 
	$("#ftl-seller-leads").hide(); 
	$("#ftl-seller-messages").hide();
	$("#ftl-seller-marketanalytics").hide();
	$("#ftl-seller-documentation").hide();


}
else if(d == "ftl-seller-leads") {
$("#ftl-seller-enquiry").hide(); 
$("#ftl-seller-leads").show(); 
$("#ftl-seller-messages").hide();
$("#ftl-seller-marketanalytics").hide();
$("#ftl-seller-documentation").hide();

} else if(d == "ftl-seller-messages") {

$("#ftl-seller-enquiry").hide(); 
$("#ftl-seller-leads").hide(); 
$("#ftl-seller-messages").show();
$("#ftl-seller-marketanalytics").hide();
$("#ftl-seller-documentation").hide();
} else if(d == "ftl-seller-marketanalytics") {
$("#ftl-seller-enquiry").hide(); 
$("#ftl-seller-leads").hide(); 
$("#ftl-seller-messages").hide();
$("#ftl-seller-marketanalytics").show();
$("#ftl-seller-documentation").hide();

}else if(d == "ftl-seller-documentation") {
$("#ftl-seller-enquiry").hide(); 
$("#ftl-seller-leads").hide(); 
$("#ftl-seller-messages").hide();
$("#ftl-seller-marketanalytics").hide();
$("#ftl-seller-documentation").show();

} 

$("#seller_post_info_links a").each(function(){
	$(this).attr('class','');
})
$(this).addClass('red');
});

//Delete Zones from mAster
$('.deleteZone').click(function(){
	
	var zoneId = $(this).data('idzone');
	 $('#confirmDeleteBox').modal('show');
	  $('#confirmDeleteBox').find('#trueDelete').on('click', function(){
	
	datastr = '&zoneId=' + zoneId;
	// ajax function starts
	$.ajax({
	type : 'post', // defining the ajax type
	url: '/ptlmasters/deletePtlZone', // calling the controller with the action involved
	dataType : 'html', // datatype
	data : datastr, // passing the data used for operation
	success : function(response) { 
		if (response == "ok" ){
			$('#successDeleteBox').modal('show');
			  $('#displayMessage').html('Zone has been deleted successfully');

			 $("#successDeleteBox").on('hidden.bs.modal', function () {
			
				 location.reload();
			 });
	}else if(response == "zone"){
		$('#successDeleteBox').modal('show');
		  $('#displayMessage').html('This zone is associated with sectors. Please delete those sectors before deleting this Zone.');
		}else{
			$('#successDeleteBox').modal('show');
			  $('#displayMessage').html('Eror occured while deleting Zone, Please try after sometime.');
		}
		
	}
			});
	  });

});
//autofill update popup zones
$('.editZone').click(function(){
	var zoneId = $(this).data('idzone');
	
	$('#zoneName').val($('#zoneName_'+zoneId).html().trim());
	$('#zoneCode').val($('#zoneCode_'+zoneId).html().trim());
	$('#hiddenZoneId').val(zoneId);
	
	  
});
//update zone master
$('#updateZoneButton').click(function(){
	var zoneId = $('#hiddenZoneId').val();
	var zoneName = $('#zoneName').val();
	var zoneCode = $('#zoneCode').val();
	if(zoneName == ''){
		$("#zoneName_error").html("Please enter zone name");
		$("#zoneName").css("border-color", "#f95f5f");
		$("#zoneName").focus();
		return false;}
	else{
	$("#zoneName_error").html("");
	$("#zoneName").css("border-color", "");
	}
	if(zoneCode == ''){
		$("#zoneCode_error").html("Please enter zone code");
		$("#zoneCode").css("border-color", "#f95f5f");
		$("#zoneCode").focus();
		return false;}
	else{
		$("#zoneCode_error").html("");
		$("#zoneCode").css("border-color", "");
		
	}
	
	datastr = '&zoneId='+zoneId+'&zoneName='+zoneName+'&zoneCode='+zoneCode;
	
	// ajax function starts
	$.ajax({
	type : 'post', // defining the ajax type
	url: '/ptlmasters/editPtlZone', // calling the controller with the action involved
	dataType : 'html', // datatype
	data : datastr, // passing the data used for operation
	success : function(response) { 
		if (response == "ok" ){
			$('#successDeleteBox').modal('show');
			  $('#displayMessage').html('Zone updated successfully.');
			  $("#successDeleteBox").on('hidden.bs.modal', function () {
					
					 location.reload();
				 });
	}else if(response == "error"){
		$('#successDeleteBox').modal('show');
		  $('#displayMessage').html('Error while updating zone. Please try after some time');
		}else{
			$('#successDeleteBox').modal('show');
			  $('#displayMessage').html(response);
			  
		}
		
	}
			});
	  
});



//autofill update popup TIERS
$('.editTier').click(function(){
	var tierId = $(this).data('idtier');
	
	$('#tierName').val($('#tierName_'+tierId).html().trim());
	$('#tierCode').val($('#tierCode_'+tierId).html().trim());
	$('#hiddenTierId').val(tierId);
	
	  
});

//update tier master
$('#updateTierButton').click(function(){
	var tierId = $('#hiddenTierId').val();
	var tierName = $('#tierName').val();
	var tierCode = $('#tierCode').val();
	if(tierName == ''){
		$("#tierName_error").html("Please enter tier name");
		$("#tierName").css("border-color", "#f95f5f");
		$("#tierName").focus();
		return false;}
	else{
	$("#tierName_error").html("");
	$("#tierName").css("border-color", "");
	}
	if(tierCode == ''){
		$("#tierCode_error").html("Please enter tier code");
		$("#tierCode").css("border-color", "#f95f5f");
		$("#tierCode").focus();
		return false;}
	else{
		$("#tierCode_error").html("");
		$("#tierCode").css("border-color", "");
		
	}
	
	datastr = '&tierId='+tierId+'&tierName='+tierName+'&tierCode='+tierCode;
	// ajax function starts
	$.ajax({
	type : 'post', // defining the ajax type
	url: '/ptlmasters/editPtlTier', // calling the controller with the action involved
	dataType : 'html', // datatype
	data : datastr, // passing the data used for operation
	success : function(response) { 
		if (response == "ok" ){
			$('#successDeleteBox').modal('show');
			  $('#displayMessage').html('Tier updated successfully.');
			  $("#successDeleteBox").on('hidden.bs.modal', function () {
					
					 location.reload();
				 });
	}else if(response == "error"){
		$('#successDeleteBox').modal('show');
		  $('#displayMessage').html('Error while updating tier. Please try after some time');
		}else{	$('#successDeleteBox').modal('show');
		  $('#displayMessage').html(response)
		  }
		
	}
			});
	  
});
//delete tier

$('.deleteTier').click(function(){
	  
	var tierId = $(this).data('idtier');
	 $('#confirmDeleteBox').modal('show');
	  $('#confirmDeleteBox').find('#trueDelete').on('click', function(){
	
	datastr = '&tierId=' + tierId;
	// ajax function starts
	$.ajax({
	type : 'post', // defining the ajax type
	url: '/ptlmasters/deletePtlTier', // calling the controller with the action involved
	dataType : 'html', // datatype
	data : datastr, // passing the data used for operation
	success : function(response) { 
		
			
			 
			if (response == "ok" ){
				$('#successDeleteBox').modal('show');
				  $('#displayMessage').html('Tier has been deleted successfully');
				 $("#successDeleteBox").on('hidden.bs.modal', function () {
				
					 location.reload();
				 });
			}else if(response == "sector"){
				$('#successDeleteBox').modal('show');
				  $('#displayMessage').html('Sector(s) are allocated to this Tier. Please delete those Sectors before deleting this Tier.');
			}else{
				$('#successDeleteBox').modal('show');
				  $('#displayMessage').html('Eror occured while deleting tier, Please try after sometime.');
			}
			
	} 
			});
	 
	  });
});


//DELETE SECTOR

$('.deleteSector').click(function(){
	var sectorId = $(this).data('idsector');
	 $('#confirmDeleteBox').modal('show');
	  $('#confirmDeleteBox').find('#trueDelete').on('click', function(){
	
	datastr = '&sectorId=' + sectorId;
	// ajax function starts
	$.ajax({
	type : 'post', // defining the ajax type
	url: '/ptlmasters/deletePtlSector', // calling the controller with the action involved
	dataType : 'html', // datatype
	data : datastr, // passing the data used for operation
	success : function(response) { 
		if (response == "ok" ){
			$('#successDeleteBox').modal('show');
			  $('#displayMessage').html('Sector has been deleted successfully');
			 $("#successDeleteBox").on('hidden.bs.modal', function () {
			
				 location.reload();
			 });
	}
		else if(response == "sector"){
			$('#successDeleteBox').modal('show');
			  $('#displayMessage').html('This sector is associated with pincodes. Please delete those pincodes before deleting this Sector.');
		}else{
			$('#successDeleteBox').modal('show');
			  $('#displayMessage').html('Eror occured while deleting Sector, Please try after sometime.');
			
		}
		
	} 
			});
	 
	  });
});

//autofill update popup SECTOR
$('.editSector').click(function(){
	var tierId = $(this).data('idsector');
	var zone    =   $.trim($('#zoneName_'+tierId).attr('zone_id'));
        var tier    =   $.trim($('#tierName_'+tierId).attr('tier_id'));
	$('#sectorName').val($.trim($('#sectorName_'+tierId).html()));
	$('#sectorCode').val($.trim($('#sectorCode_'+tierId).html()));
	
        $('#zoneName').selectpicker('val', zone);
        $('#tierName').selectpicker('val', tier);
	$('#hiddenSectorId').val(tierId);
	
	  
});

//update sector master
$('#updateSectorButton').click(function(){
	var sectorId = $('#hiddenSectorId').val();
	var sectorName = $('#sectorName').val();
	var sectorCode = $('#sectorCode').val();
        var zone = $('#zoneName').val();
	var tier = $('#tierName').val();
	if(sectorName == ''){
		$("#sectorName_error").html("Please enter sector name");
		$("#sectorName").css("border-color", "#f95f5f");
		$("#sectorName").focus();
		return false;}
	else{
	$("#sectorName_error").html("");
	$("#sectorName").css("border-color", "");
	}
	if(sectorCode == ''){
		$("#sectorCode_error").html("Please enter sector code");
		$("#sectorCode").css("border-color", "#f95f5f");
		$("#sectorCode").focus();
		return false;}
	else{
		$("#sectorCode_error").html("");
		$("#sectorCode").css("border-color", "");
		
	}
	
	datastr = '&sectorId='+sectorId+'&sectorName='+sectorName+'&sectorCode='+sectorCode+'&zone='+zone+'&tier='+tier;
	// ajax function starts
	$.ajax({
	type : 'post', // defining the ajax type
	url: '/ptlmasters/editPtlSector', // calling the controller with the action involved
	dataType : 'html', // datatype
	data : datastr, // passing the data used for operation
	success : function(response) { 
		if (response == "ok" ){
			 $('#successDeleteBox').modal('show');
			 $('#displayMessage').html('Sector updated successfully.');
			  $("#successDeleteBox").on('hidden.bs.modal', function () {
					
					 location.reload();
				 });
			 
	}else if(response == "error"){
		 $('#successDeleteBox').modal('show');
		  $('#displayMessage').html('Error while updating Sector. Please try after some time');
			
		}else{
			$('#successDeleteBox').modal('show');
			  $('#displayMessage').html(response);
		  
		}
		
	}
			});
	  
});


//DELETE Pincode from PINCODE MASTERS

$('.deletePincode').click(function(){
	var pincodeId = $(this).data('idpincode');

	datastr = '&pincodeId=' + pincodeId;
	// ajax function starts
	$.ajax({
	type : 'post', // defining the ajax type
	url: '/ptlmasters/checkPtlPincode', // calling the controller with the action involved
	dataType : 'html', // datatype
	data : datastr, // passing the data used for operation
	success : function(response) { 
		if (response == "ok" ){
			  $('#successDeleteBox').modal('show');
			  $('#displayMessage').html('Please delete the posts associated with this pincode.');
			
		}else{
			
			  $('#confirmDeleteBox').modal('show');
			  $('#confirmDeleteBox').find('#trueDelete').on('click', function(){
				
					
				 	dataId = '&pincodeId=' + pincodeId;
					// ajax function starts
					$.ajax({
					type : 'post', // defining the ajax type
					url: '/ptlmasters/deletePtlPincode', // calling the controller with the action involved
					dataType : 'html', // datatype
					data : dataId, // passing the data used for operation
					success : function(response) { 
						
						if (response == "ok" ){
							$('#successDeleteBox').modal('show');
							 $('#displayMessage').html('Pincode has been deleted successfully');
							 $("#successDeleteBox").on('hidden.bs.modal', function () {
							
								 location.reload();
							 });
							// location.reload();
						}
					
	else{
		  $('#confirmDeleteBox').modal('show');
		  $('#displayMessage').html('Eror occured while deleting Pincode, Please try after sometime');
		
		}
		
	} 
			});
	 
	  });
		}
	}
	
	
	});
	
	  

});

$('#ptl_pincode_id').on("blur",function(){
	

	var pinCode = $('#ptlPincodeId').val();
	
	datastr = '&pinCode=' + pinCode;
	// ajax function starts
	$.ajax({
	type : 'post', // defining the ajax type
	url: '/ptlmasters/fillEditPtlSector', // calling the controller with the action involved
	dataType : 'html', // datatype
	data : datastr, // passing the data used for operation
	success : function(response) {
		
		obj = JSON.parse(response);
		if(obj[0]){
		//$('#ptl_pincode_sector_id').val(obj[0].sector);
                $('#ptl_pincode_sector_id').selectpicker('val', obj[0].sector);
                $('#oda_pincode').selectpicker('val', obj[0].oda);
		//$('#oda_pincode').val(obj[0].oda);
        //$("#oda_pincodeoption[value='"+obj[0].oda+"']").prop('selected', true);
		}
		else{
			$('#ptl_pincode_sector_id').val('');
			$('#oda_pincode').val('');
			}
	} 
			});

    //$('.selectpicker').selectpicker('refresh');

	
});


});

function changeLocType(){
	
	$("#from_location_id").val('');
	$("#to_location_id").val('');
	$('.form-inline').submit();
	
}
/**Relocation start */

$(document).ready(function() {
	//switch ratecard types selection
	$(".ratetype_selection").click(function(){
		$(".relocation_house_hold_create").hide();
		$(".relocation_vehicle_create").hide();
		$("#household_items_mandatory").val(0);
		$("#vehicle_items_mandatory").val(0);
		var typeselection = $('input[name=post_rate_card_type]:checked', '#posts-form_relocation').val();
		if(typeselection == 1){
			$(".relocation_house_hold_create").show();
			$("#household_items_mandatory").val(1);
            $('.vehicle_not_display').show();
            $('.terms-and-conditions-block').show();
            $('span.storate_charges').text('PER CFT/Day');
		}else if(typeselection == 2){
			$(".relocation_vehicle_create").show();
			$("#vehicle_items_mandatory").val(1);
            $('.vehicle_not_display').hide();
            $('.terms-and-conditions-block').hide();
            $('span.storate_charges').text('Per Day');

		}else if(typeselection == 3){
			$(".relocation_house_hold_create").show();
			$(".relocation_vehicle_create").show();
			$("#household_items_mandatory").val(1);
			$("#vehicle_items_mandatory").val(1);
            $('.vehicle_not_display').show();
            $('.terms-and-conditions-block').show();
            $('span.storate_charges').text('PER CFT/Day');
		}
	});
	//add more relocation item
	var sel_list_house = new Array();
	$('#add_more_relocation').on('click', function() {
        $('#household_items_mandatory').val(1);
		var num = parseInt($('#next_add_more_id_reloc').val()) + 1;
		$('#next_add_more_id_reloc').val(num);
		//alert(num);
		var from_location = $(this).closest("form").validate().element($('#from_location_id'));
		var to_location = $(this).closest("form").validate().element($('#to_location_id'));
		var datepicker = $(this).closest("form").validate().element($('#datepicker'));
		var datepicker_to_location = $(this).closest("form").validate().element($('#datepicker_to_location'));
		if(from_location == true && to_location == true && datepicker == true && datepicker_to_location == true) {
			var subscription_start_date_start_val = $('#subscription_start_date_start').val();
			var subscription_end_date_end_val = $('#subscription_end_date_end').val();
			var current_date_seller = $('#current_date_seller').val();
			var datepicker_from_value = $('#datepicker').val();
			var datepicker_from_value = convertDateFormatForDatePickerSeller(datepicker_from_value);
			var datepicker_to_value = $('#datepicker_to_location').val();
			var datepicker_to_value = convertDateFormatForDatePickerSeller(datepicker_to_value);

			if ((datepicker_from_value > subscription_end_date_end_val) || (current_date_seller < subscription_start_date_start_val) || (datepicker_from_value < subscription_start_date_start_val)) {
				var end_date_subscription = "from date";
			} else {
				var end_date_subscription = "to date";
			}


			if ((current_date_seller >= subscription_start_date_start_val) && (current_date_seller <= subscription_end_date_end_val) && (datepicker_from_value >= subscription_start_date_start_val) && (datepicker_from_value <= subscription_end_date_end_val) && (datepicker_to_value >= subscription_start_date_start_val) && (datepicker_to_value <= subscription_end_date_end_val)) {
				var line_itemes = $('#household_row_items').children().size();
				//$("#posts-form_relocation").valid();
				//$("#posts-form_relocation").valid();
                var validator = $( "#posts-form_relocation" ).validate();

				var propertytypes = validator.element($('#propertytypes'));
				var volume = $(this).closest("form").validate().element($('#volume'));
				var load_types = $(this).closest("form").validate().element($('#load_types'));
				var rate_per_cft = $(this).closest("form").validate().element($('#rate_per_cft'));
				var transit_days = validator.element($('#transit_days'));
				var transport_charges = $(this).closest("form").validate().element($('#transport_charges'));
				var unique = $("#propertytypes option:selected").val() + $("#load_types option:selected").val() + $("#transit_days").val() + $("#transitdays_units_relocation option:selected").val();

				if ($.inArray(unique, sel_list_house) == -1) {

					if (propertytypes == true && volume == true && load_types == true && rate_per_cft == true && transit_days == true && transport_charges == true) {
						$.ajax({
							type: 'post',
							url: '/lineitemscheckrelocation',
							data: {
								'propertytypes': $("#propertytypes option:selected").val(),
								'load_types': $("#load_types option:selected").val(),
								'transit_days': $("#transit_days").val(),
								'transit_days_units': $("#transitdays_units_relocation option:selected").val(),
								'from_date_seller': datepicker_from_value,
								'to_date_seller': datepicker_to_value,
								'rate_card_type': 1,
								'from_location_id': $("#from_location_id").val(),
								'to_location_id': $("#to_location_id").val(),
							},
							dataType: "html",
							type: 'POST',
							success: function (data) {
								
								
								if (data == '0') {
                                    sel_list_house.unshift(unique);
                                    if($("#update_reloc_seller_line").val()==1)
                                    {
                                        var remove_unique=$("#update_reloc_seller_row_unique").val();
                                        sel_list_house.splice($.inArray(remove_unique, sel_list_house),1);
                                        $('.request_row_' + $("#update_reloc_seller_row_count").val()).remove();
                                        $("#update_reloc_seller_line").val(0);
                                    }
									
									
									var html = '<div class="table-row inner-block-bg request_row_'+ num +'" data-string="' + num + '">' +
										'<div class="col-md-2 padding-left-none">' + $("#propertytypes option:selected").text() + '</div>' +
										'<div class="col-md-1 padding-left-none">' + $("#volume").val() + ' CFT</div>' +
										'<div class="col-md-2 padding-left-none">' + $("#rate_per_cft").val() + '/-</div>' +
										'<div class="col-md-2 padding-left-none">' + $("#transit_days").val() + ' ' + $("#transitdays_units_relocation option:selected").text() + '</div>' +
										'<div class="col-md-2 padding-left-none">' + $("#load_types option:selected").text() + '</div>' +
										'<div class="col-md-2 padding-left-none">' + $("#transport_charges").val() + ' /-</div>' +
										'<div class="col-md-1 padding-none text-center"><a href="javascript:void(0)" style ="cursor:pointer; margin-right:10px;" class="edit_this_reloc edit" data-string="'+unique+'" row_id="'
			                            + num + '"><i class="fa fa-edit red" title="Edit"></i></a><a style ="cursor:pointer;" class="remove_this_line_reloc remove" data-string="'+unique+'" row_id="'
			                            + num + '"><i class="fa fa-trash red" title="Delete"></i></a></div>' +
										'<input type="hidden" value="' + $("#propertytypes option:selected").val() + '" name="propertytypes_hidden[]" id="propertytypes_'+num+'">' +
										'<input type="hidden" value="' + $("#volume").val() + '" name="volume_hidden[]" id="volume_'+num+'">' +
										'<input type="hidden" value="' + $("#rate_per_cft").val() + '" name="rate_per_cft_hidden[]" id="rate_per_cft_'+num+'">' +
										'<input type="hidden" value="' + $("#transit_days").val() + '" name="transit_days_hidden[]" id="transit_days_'+num+'">' +
										'<input type="hidden" value="' + $("#transitdays_units_relocation option:selected").val() + '" name="transitdays_units_relocation_hidden[]" id="transitdays_units_'+num+'">' +
										'<input type="hidden" value="' + $("#load_types option:selected").val() + '" name="load_types_hidden[]" id="load_types_'+num+'">' +
										'<input type="hidden" value="' + $("#transport_charges").val() + '" name="transport_charges_hidden[]" id="transport_charges_'+num+'">' +

										'</div>';

									$("#valid_from_val").val(datepicker_from_value);
									$("#valid_to_val").val(datepicker_to_value);
									$("#propertytypes").val("");
									$("#volume").val("");
									$('#load_types').val("");
									$('#rate_per_cft').val("");
									$('#transit_days').val("");
									$('#transport_charges').val("");
									$('#transitdays_units_relocation').val("");
									$('.selectpicker').selectpicker('refresh');
                                    $("#household_row_items").append(html);
									disablerelcationcreatepost();
                                    var line_itemes = $('#household_row_items').children().size();
                                    $("#household_items").val(line_itemes);
								} else {
									$("#erroralertmodal .modal-body").html("Post already exists for the specified parameters! Please modify parameters.");
									$("#erroralertmodal").modal({
										show: true
									});
								}
							},
							error: function () {
								alert("error");
							}
						});

					}
				} else {
					$("#erroralertmodal .modal-body").html("Post already exists for the specified parameters! Please modify parameters.");
					$("#erroralertmodal").modal({
						show: true
					});
				}
			} else {
				$("#erroralertmodal .modal-body").html("Your post valid " + end_date_subscription + " is beyond your subscription date, please select valid " + end_date_subscription + " within your subscription validity date");
				$("#erroralertmodal").modal({
					show: true
				});
			}
		}
        /*$('#to_location').prev().addClass("disable-bg");
        $('#from_location').prev().addClass("disable-bg");
        $('#to_location').addClass("disable-bg");
        $('#from_location').addClass("disable-bg");
        $('.calendar.hasDatepicker').prev().addClass("disable-bg");*/
	});
	
	 $(document).on('click', '.remove_this_line_reloc', function() {
         var rowid = $(this).attr("row_id");
         remove_val = $(this).attr("data-string");
         var r = confirm("Are you sure, you want you delete?");
         if (r == true) {
        	 sel_list_house.splice($.inArray(remove_val, sel_list_house),1);
             $('.request_row_' + rowid).remove();
         }
         var id_line_itemes_empty = $('.request_rows').children().size();
         if (id_line_itemes_empty == 0){
         	$("#datepicker").prop('disabled', false);
         	$("#datepicker_to_location").prop('disabled', false);
         }
     });
	 
	 $(document).on('click', '.edit_this_reloc', function() {
         
		 var rowid = $(this).attr("row_id");
		 var remove_val = $(this).attr("data-string");
		 sel_list_house.splice($.inArray(remove_val, sel_list_house),1);
	    	$("#update_reloc_seller_line").val(1);
	     	$("#update_reloc_seller_row_count").val(rowid);
	     	$("#update_reloc_seller_row_unique").val(remove_val);
     	 
		    $("#propertytypes").selectpicker('val',$("#propertytypes_"+rowid).val());
			$("#volume").val($("#volume_"+rowid).val());
			$('#load_types').selectpicker('val',$("#load_types_"+rowid).val());
			$('#rate_per_cft').val($("#rate_per_cft_"+rowid).val());
			$('#transit_days').val($("#transit_days_"+rowid).val());
			$('#transport_charges').val($("#transport_charges_"+rowid).val());
			$('#transitdays_units_relocation').selectpicker('val',$("#transitdays_units_"+rowid).val());
			 
		 
     });
	 
	//add more vehicle
	var sel_list_vehicle = new Array();
	$('#add_more_relocation_vehicle').on('click', function() {
        $('#vehicle_items_mandatory').val(1);
		var num_veh = parseInt($('#next_add_more_veh_id_reloc').val()) + 1;
		$('#next_add_more_veh_id_reloc').val(num_veh);
		var from_location = $(this).closest("form").validate().element($('#from_location_id'));
		var to_location = $(this).closest("form").validate().element($('#to_location_id'));
		var datepicker = $(this).closest("form").validate().element($('#datepicker'));
		var datepicker_to_location = $(this).closest("form").validate().element($('#datepicker_to_location'));
		if(from_location == true && to_location == true && datepicker == true && datepicker_to_location == true) {

			var subscription_start_date_start_val = $('#subscription_start_date_start').val();
			var subscription_end_date_end_val = $('#subscription_end_date_end').val();
			var current_date_seller = $('#current_date_seller').val();
			//$("#posts-form_relocation").valid();
			var datepicker_from_value = $('#datepicker').val();
			var datepicker_from_value = convertDateFormatForDatePickerSeller(datepicker_from_value);
			var datepicker_to_value = $('#datepicker_to_location').val();
			var datepicker_to_value = convertDateFormatForDatePickerSeller(datepicker_to_value);
			if ((datepicker_from_value > subscription_end_date_end_val) || (current_date_seller < subscription_start_date_start_val) || (datepicker_from_value < subscription_start_date_start_val)) {
				var end_date_subscription = "from date";
			} else {
				var end_date_subscription = "to date";
			}

			if ((current_date_seller >= subscription_start_date_start_val) && (current_date_seller <= subscription_end_date_end_val) && (datepicker_from_value >= subscription_start_date_start_val) && (datepicker_from_value <= subscription_end_date_end_val) && (datepicker_to_value >= subscription_start_date_start_val) && (datepicker_to_value <= subscription_end_date_end_val)) {

				var vehicle_types = $(this).closest("form").validate().element($('#vehicle_types'));
				var vehicle_type_category = $(this).closest("form").validate().element($('#vehicle_type_category'));
				var cost = $(this).closest("form").validate().element($('#cost'));
				var transit_days_vehicle = $(this).closest("form").validate().element($('#transit_days_vehicle'));
				var transport_charges_vehicle = $(this).closest("form").validate().element($("#transport_charges_vehicle"));
				var unique = $("#vehicle_types option:selected").val() + $("#vehicle_type_category option:selected").val() + $("#transit_days_vehicle").val() + $("#transitdays_units_relocation_vehicle option:selected").val();
				//alert(sel_list_vehicle);
				if($("#update_reloc_veh_seller_line").val()==1)
            	{
					var remov_unique=$("#update_reloc_veh_seller_row_unique").val();
					sel_list_vehicle.splice($.inArray(remov_unique, sel_list_vehicle),1);
					$('.request_row_veh_' + $("#update_reloc_veh_seller_row_count").val()).remove();	
                	$("#update_reloc_veh_seller_line").val(0);
                }
				if ($.inArray(unique, sel_list_vehicle) == -1) {
					//sel_list_vehicle.unshift(unique);
					if (vehicle_types == true && vehicle_type_category == true && cost == true && transit_days_vehicle == true && transport_charges_vehicle == true) {
						$.ajax({
							type: 'post',
							url: '/lineitemscheckrelocation',
							data: {
								'vehicle_types': $("#vehicle_types option:selected").val(),
								'vehicle_type_category': $("#vehicle_type_category option:selected").val(),
								'transit_days': $("#transit_days_vehicle").val(),
								'transit_days_units': $("#transitdays_units_relocation_vehicle option:selected").val(),
								'from_date_seller': datepicker_from_value,
								'to_date_seller': datepicker_to_value,
								'rate_card_type': 2,
								'from_location_id': $("#from_location_id").val(),
								'to_location_id': $("#to_location_id").val()
							},
							dataType: "html",
							type: 'POST',
							success: function (data) {
								if (data == '0') {
									var vehicletypetext = $("#vehicle_type_category option:selected").text();
                                    vehicletypetext = (vehicletypetext == "Select Car Size*") ? "N/A" : vehicletypetext;
									var html = '<div class="table-row inner-block-bg request_row_veh_'+ num_veh +'" data-string="' + num_veh + '">' +
										'<div class="col-md-3 padding-left-none">' + $("#vehicle_types option:selected").text() + '</div>' +
										'<div class="col-md-2 padding-left-none">' + vehicletypetext + '</div>' +
										'<div class="col-md-1 padding-left-none">' + $("#cost").val() + '/-</div>' +
										'<div class="col-md-2 padding-left-none">' + $("#transit_days_vehicle").val() + ' ' + $("#transitdays_units_relocation_vehicle option:selected").text() + '</div>' +
										'<div class="col-md-2 padding-none">' + $("#transport_charges_vehicle").val() + ' /-</div>' +
										'<div class="col-md-2 padding-none"><a href="javascript:void(0)" style ="cursor:pointer; margin-right:10px;" class="edit_this_reloc_veh edit" data-string="'+num_veh+'" row_id="'
			                            + num_veh + '"><i class="fa fa-edit red" title="Edit"></i></a><a style ="cursor:pointer;" class="remove_this_line_reloc_veh remove" data-string="'+num_veh+'" row_id="'
			                            + num_veh + '"><i class="fa fa-trash red" title="Delete"></i></a></div>' +
										'<input type="hidden" value="' + $("#vehicle_types option:selected").val() + '" name="vehicle_types_hidden[]" id="vehicle_types_'+num_veh+'">' +
										'<input type="hidden" value="' + $("#vehicle_type_category option:selected").val() + '" name="vehicle_type_category_hidden[]" id="vehicle_type_category_'+num_veh+'">' +
										'<input type="hidden" value="' + $("#cost").val() + '" name="cost_hidden[]" id="cost_'+num_veh+'">' +
										'<input type="hidden" value="' + $("#transit_days_vehicle").val() + '" name="transit_days_vehicle_hidden[]" id="transit_days_vehicle_'+num_veh+'">' +
										'<input type="hidden" value="' + $("#transitdays_units_relocation_vehicle").val() + '" name="transitdays_units_relocation_vehicle_hidden[]" id="transitdays_units_relocation_vehicle_'+num_veh+'">' +
										'<input type="hidden" value="' + $("#transport_charges_vehicle").val() + '" name="transport_charges_vehicle_hidden[]" id="transport_charges_vehicle_'+num_veh+'">' +
										'</div>';


									$("#valid_from_val").val(datepicker_from_value);
									$("#valid_to_val").val(datepicker_to_value);
									$("#vehicle_types").val("");
									$("#vehicle_type_category").val("");
									$('#cost').val("");
									$('#transit_days_vehicle').val("");
									$('#transitdays_units_relocation_vehicle').val("");
									$('#transport_charges_vehicle').val("");
									$('.selectpicker').selectpicker('refresh');
                                    $("#vehicle_row_items").append(html);
									disablerelcationcreatepost();
                                    var line_itemes = $('#vehicle_row_items').children().size();
                                    $("#vehicle_items").val(line_itemes);
								} else {
									$("#erroralertmodal .modal-body").html("Post already exists for the specified parameters! Please modify parameters.");
									$("#erroralertmodal").modal({
										show: true
									});
								}
							},
							error: function () {
								alert("error");
							}
						});
					}
				} else {
					$("#erroralertmodal .modal-body").html("Post already exists for the specified parameters! Please modify parameters.");
					$("#erroralertmodal").modal({
						show: true
					});
				}
			} else {
				$("#erroralertmodal .modal-body").html("Your post valid " + end_date_subscription + " is beyond your subscription date, please select valid " + end_date_subscription + " within your subscription validity date");
				$("#erroralertmodal").modal({
					show: true
				});
			}
		}

	});
	
	$(document).on('click', '.remove_this_line_reloc_veh', function() {
        var rowid = $(this).attr("row_id");
        remove_val = $(this).attr("data-string");
        var r = confirm("Are you sure, you want you delete?");
        if (r == true) {
        	sel_list_vehicle.splice($.inArray(remove_val, sel_list_vehicle),1);
            $('.request_row_veh_' + rowid).remove();
        }
        var id_line_itemes_empty = $('.request_rows_veh').children().size();
        if (id_line_itemes_empty == 0){
        	$("#datepicker").prop('disabled', false);
        	$("#datepicker_to_location").prop('disabled', false);
        }
    });
	 
	 $(document).on('click', '.edit_this_reloc_veh', function() {
        
		 var rowid = $(this).attr("row_id");
		 var remove_val = $(this).attr("data-string");
	    	$("#update_reloc_veh_seller_line").val(1);
	     	$("#update_reloc_veh_seller_row_count").val(rowid);
	     	$("#update_reloc_veh_seller_row_unique").val(remove_val); 
	     	$("#vehicle_types").selectpicker('val',$("#vehicle_types_"+rowid).val());
			$("#vehicle_type_category").selectpicker('val',$("#vehicle_type_category_"+rowid).val());
			$('#cost').val($("#cost_"+rowid).val());
			$('#transit_days_vehicle').val($("#transit_days_vehicle_"+rowid).val());
			$('#transitdays_units_relocation_vehicle').selectpicker('val',$("#transitdays_units_relocation_vehicle_"+rowid).val());
			$('#transport_charges_vehicle').val($("#transport_charges_vehicle_"+rowid).val());

             if($("#vehicle_types_"+rowid).val() == 2){
                 $(".vehicle_type_car").hide();
                 $("#vehicle_type_category option[value='']").prop('selected', true);
             }else{
                 $(".vehicle_type_car").show();
             }


	 });
    $('#add_quote_seller_relocation').click(function(e) {
        $('#household_items_mandatory').val(0);
        $('#vehicle_items_mandatory').val(0);
        e.preventDefault();
        $('#posts-form_relocation').valid();
        var id=$('.request_rows').children().size();
        if(id==0){
            $("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        }else{
            if($('#posts-form_relocation').valid()) {
                $('#posts-form_relocation').submit();
                $("#add_quote_seller_relocation").prop('disabled', true);
                $("#add_quote_seller_id_relocation").prop('disabled', true);
            }
        }
    });

	$('#add_quote_seller_id_relocation').click(function(e) {
        $('#household_items_mandatory').val(0);
        $('#vehicle_items_mandatory').val(0);
		e.preventDefault();
        $('#posts-form_relocation').valid();
		var id=$('.request_rows').children().size();
        if(id==0){
            $("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        }

		if($("#posts-form_relocation").valid()) {
			var submitData=$("#posts-form_relocation").serialize();
			var btnName = $('#add_quote_seller_id_relocation').attr('name');
			$("#add_quote_seller_id_relocation").prop('disabled', true);
			$("#add_quote_seller_relocation").prop('disabled', true);
			var btnVal = $('#add_quote_seller_id_relocation').val();
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

	//validation of seller post creation
	$("#posts-form_relocation").validate({
		ignore: "input[type='text']:hidden",
		rules : {
			"post_rate_card_type" : {required : true},
			"valid_from" : {required : true},
			"valid_to" : {required : true},
			"from_location" : {required : true},
			"from_location_id" : {required : true},
			"to_location" : {required : true},
			"to_location_id" : {required : true},
			"propertytypes" : {
				required : {
					depends: function(element) {
						if ($('#household_items').val() == 0 && $('input[name=post_rate_card_type]:checked', '#posts-form_relocation').val() == 1) {
                            return true;
                        }else if ($("#household_items_mandatory").val() == 1){
							return true;
						}else{
							return false;
						}
					}
				},
			},
			"volume" : {
				required : {
					depends: function(element) {
                        if ($('#household_items').val() == 0) {
                            return true;
                        }else if ($("#household_items_mandatory").val() == 1){
                            return true;
                        }else{
							return false;
						}
					}
				},
				digits: true,
				//rangelength: [0,3]
			},
			"loadtypes" : {
				required : {
					depends: function(element) {
                        if ($('#household_items').val() == 0 && $('input[name=post_rate_card_type]:checked', '#posts-form_relocation').val() == 1) {
                            return true;
                        }else if ($("#household_items_mandatory").val() == 1){
                            return true;
                        }else{
							return false;
						}
					}
				},
			},
			"rate_per_cft" : {
				required : {
					depends: function(element) {
                        if ($('#household_items').val() == 0) {
                            return true;
                        }else if ($("#household_items_mandatory").val() == 1){
                            return true;
                        }else{
							return false;
						}
					}
				},
                fourbytwovalidations: {
                    depends: function(element) {
                        if ($('#household_items').val() == 0) {
                            return true;
                        }else if ($("#household_items_mandatory").val() == 1){
                            return true;
                        }else{
                            return false;
                        }
                    }
                }
			},
			"transit_days" : {
				required : {
					depends: function(element) {
                        if ($('#household_items').val() == 0) {
                            return true;
                        }else if ($("#household_items_mandatory").val() == 1){
                            return true;
                        }else{
							return false;
						}
					}
				},
				digits: true,
				transitvalidation: {
					depends: function(element) {
                        if ($('#household_items').val() == 0) {
                            return true;
                        }else if ($("#household_items_mandatory").val() == 1){
                            return true;
                        }else{
							return false;
						}
					}
				},
			},
			"transport_charges" : {
				number: true,
				required : {
					depends: function(element) {
                        if ($('#household_items').val() == 0) {
                            return true;
                        }else if ($("#household_items_mandatory").val() == 1){
                            return true;
                        }else{
							return false;
						}
					}
				},
                sixdigitsvalidation: {
                    depends: function(element) {
                        if ($('#household_items').val() == 0) {
                            return true;
                        }else if ($("#household_items_mandatory").val() == 1){
                            return true;
                        }else{
                            return false;
                        }
                    }
                },
			},

			"vehicle_types" : {
				required : {
					depends: function(element) {
                        if ($('#vehicle_items').val() == 0 && $('input[name=post_rate_card_type]:checked', '#posts-form_relocation').val() == 2) {
                            return true;
                        }else if ($("#vehicle_items_mandatory").val() == 1){
                            return true;
                        }else{
							return false;
						}
					}
				},
			},
			"vehicle_type_category" : {
				required : {
					depends: function(element) {
						if (($('#vehicle_items').val() == 0 || $("#vehicle_items_mandatory").val() == 1) && $("#vehicle_types").val() == 1){
							return true;
						}else{
							return false;
						}
					}
				},
			},
			"cost" : {
				required : {
					depends: function(element) {
                        if ($('#vehicle_items').val() == 0) {
                            return true;
                        }else if ($("#vehicle_items_mandatory").val() == 1){
                            return true;
                        }else{
							return false;
						}
					}
				},
                sixbytwovalidations: {
                    depends: function(element) {
                        if ($('#vehicle_items').val() == 0) {
                            return true;
                        }else if ($("#vehicle_items_mandatory").val() == 1){
                            return true;
                        }else{
                            return false;
                        }
                    }
                }
			},
			"transit_days_vehicle" : {
				required : {
					depends: function(element) {
                        if ($('#vehicle_items').val() == 0) {
                            return true;
                        }else if ($("#vehicle_items_mandatory").val() == 1){
                            return true;
                        }else{
							return false;
						}
					}
				},
				digits: true,
				transitvalidation: {
					depends: function(element) {
                        if ($('#vehicle_items').val() == 0) {
                            return true;
                        }else if ($("#vehicle_items_mandatory").val() == 1){
                            return true;
                        }else{
							return false;
						}
					}
				},
			},
			"transport_charges_vehicle" : {
				number: true,
				required : {
					depends: function(element) {
                        if ($('#vehicle_items').val() == 0) {
                            return true;
                        }else if ($("#vehicle_items_mandatory").val() == 1){
                            return true;
                        }else{
							return false;
						}
					}
				},
				sixdigitsvalidation: {
                    depends: function(element) {
                        if ($('#vehicle_items').val() == 0) {
                            return true;
                        }else if ($("#vehicle_items_mandatory").val() == 1){
                            return true;
                        }else{
                            return false;
                        }
                    }
                }
			},

			"crating_charges" : {number: true,fourbytwovalidationswithzero: true},
			"storate_charges" : {number: true,fourbytwovalidationswithzero: true},
			"escort_charges" : {number: true,fourbytwovalidationswithzero: true},
			"handyman_charges" : {number: true,fourbytwovalidationswithzero: true},
			"property_search" : {number: true},
			"brokerage" : {number: true},
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
				/*fourbytwovalidationswithzero: {
	            	depends: function(element) {
	            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
		            		return true;
		            	}else{
		            		return false;
		            	}

	            	}
                }*/
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
			"to_location" : {
				required : "",
			},
			"to_location_id" : {
				required : "To Location should be valid",
			},
			"transitdays" : {
				required : "Transit days should be valid",
			},
			"price" : {
				required : "Price should be valid",
			},
			"handyman_charges" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
			"crating_charges" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
			"storate_charges" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
			"escort_charges" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
			"property_search" : {
				//required : "Price should be valid",
				number: "Only numbers are allowed",
			},
			"brokerage" : {
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


	$('#update_more_relocation_property').on('click', function() {
		var line_itemes = $('#household_row_items').children().size();
		//$("#posts-form_relocation").valid();
		var propertytypes = $(this).closest("form").validate().element($('#propertytypes'));
		var volume = $(this).closest("form").validate().element($('#volume'));
		var load_types = $(this).closest("form").validate().element($('#load_types'));
		var rate_per_cft = $(this).closest("form").validate().element($('#rate_per_cft'));
		var transit_days = $(this).closest("form").validate().element($('#transit_days'));
		var transport_charges = $(this).closest("form").validate().element($('#transport_charges'));
		if(propertytypes == true && volume == true && load_types == true && rate_per_cft == true && transit_days == true && transport_charges == true){
			var currentrowid = $("#current_household_row_id").val();
			var rowid = "#single_property_post_item_"+currentrowid;

			$(rowid+" div:first-child").html($( "#propertytypes option:selected" ).text());
			$(rowid+" div:nth-child(3)").html($( "#load_types option:selected" ).text());
			$(rowid+" div:nth-child(2)").html($( "#volume" ).val()+" CFT");
			$(rowid+" div:nth-child(4)").html($( "#rate_per_cft" ).val()+" /-");
			$(rowid+" div:nth-child(5)").html($( "#transport_charges" ).val()+" /-");
			$(rowid+" div:nth-child(6)").html($( "#transit_days" ).val()+" "+$( "#transitdays_units_relocation option:selected" ).text());

			$(rowid+" input[name='propertytypes_hidden[]']").val($( "#propertytypes option:selected" ).val());
			$(rowid+" input[name='volume_hidden[]']").val($( "#volume" ).val());
			$(rowid+" input[name='rate_per_cft_hidden[]']").val($( "#rate_per_cft" ).val());
			$(rowid+" input[name='transit_days_hidden[]']").val($( "#transit_days" ).val());
			$(rowid+" input[name='transitdays_units_relocation_hidden[]']").val($( "#transitdays_units_relocation option:selected" ).val());
			$(rowid+" input[name='load_types_hidden[]']").val($( "#load_types option:selected" ).val());
			$(rowid+" input[name='transport_charges_hidden[]']").val($( "#transport_charges" ).val());


			$('#update_more_relocation_property').hide();
			var line_itemes = $('#household_row_items').children(".table-row").size();
			$("#household_items").val(line_itemes);
			$("#propertytypes").val("");
			$("#volume").val("");
			$('#load_types').val("");
			$('#rate_per_cft').val("");
			$('#transit_days').val("");
			$('#transport_charges').val("");
			$('#transitdays_units_relocation').val("");
			$('.selectpicker').selectpicker('refresh');
			$('#update_more_relocation_property').hide();
            $("#household_items_mandatory").val(0);

		}
	});
	//add more vehicle
	$('#update_more_relocation_vehicle').on('click', function() {
		//$("#posts-form_relocation").valid();

		var vehicle_types = $(this).closest("form").validate().element($('#vehicle_types'));
		var vehicle_type_category = $(this).closest("form").validate().element($('#vehicle_type_category'));
		var cost = $(this).closest("form").validate().element($('#cost'));
		var transit_days_vehicle = $(this).closest("form").validate().element($('#transit_days_vehicle'));
		var transport_charges_vehicle = $(this).closest("form").validate().element($("#transport_charges_vehicle"));
		if(vehicle_types == true && vehicle_type_category == true && cost == true && transit_days_vehicle == true && transport_charges_vehicle == true){
			var currentrowid = $("#current_vehicle_row_id").val();
			var rowid = "#single_vehicle_post_item_"+currentrowid;

			$(rowid+" div:first-child").html($( "#vehicle_types option:selected" ).text());
			$(rowid+" div:nth-child(2)").html($( "#vehicle_type_category option:selected" ).text());
			$(rowid+" div:nth-child(3)").html($( "#cost" ).val()+" /-");
			$(rowid+" div:nth-child(4)").html($( "#transport_charges_vehicle" ).val()+" /-");
			$(rowid+" div:nth-child(5)").html($( "#transit_days_vehicle" ).val()+" "+$( "#transitdays_units_relocation_vehicle option:selected" ).text());


			$(rowid+" input[name='vehicle_types_hidden[]']").val($( "#vehicle_types option:selected" ).val());
			$(rowid+" input[name='vehicle_type_category_hidden[]']").val($( "#vehicle_type_category option:selected" ).val());
			$(rowid+" input[name='cost_hidden[]']").val($( "#cost" ).val());
			$(rowid+" input[name='transit_days_vehicle_hidden[]']").val($( "#transit_days_vehicle" ).val());
			$(rowid+" input[name='transitdays_units_relocation_vehicle_hidden[]']").val($( "#transitdays_units_relocation_vehicle option:selected" ).val());
			$(rowid+" input[name='transport_charges_vehicle_hidden[]']").val($( "#transport_charges_vehicle" ).val());

			var line_itemes = $('#vehicle_row_items').children(".table-row").size();
			$("#vehicle_items").val(line_itemes);

			$("#vehicle_types").val("");
			$("#vehicle_type_category").val("");
			$('#cost').val("");
			$('#transit_days_vehicle').val("");
			$('#transitdays_units_relocation_vehicle').val("");
			$('#transport_charges_vehicle').val("");

			$('.selectpicker').selectpicker('refresh');
			$('#update_more_relocation_vehicle').hide();

            $("#vehicle_items_mandatory").val(0);
		}
	});




	
	//Sellersearchfor buyer relocation domestic form validation
	$("#relocation_domestic_sellersearch_buyers").validate({ // initialize the
	    // plugin
	    ignore: "input[type='text']:hidden",
	    rules: {
	        "from_location": {
	            required: true,
	        },
	        "from_location_id": {
	            required: true,
	        },
	        "to_location": {
	            required: true,
	        },
	        "to_location_id": {
	            required: true,
	        },
	        "valid_from": {
	            required: true,
	        },	 
//	        "valid_to": {
//	            required: true,
//	        },	
	        "post_type": {
	            required: true,
	        },
	        
	    },
	    errorPlacement: function(error, element) {
	    	$(element).parent('div').after(error); 
	    },
	    messages: {
	        "from_location": {
	            required: "",
	        },
	        "from_location_id": {
	            required: "From Location should be valid",
	        },
	        "to_location": {
	            required: "",
	        },
	        "to_location_id": {
	            required: "To Location should be valid",
	        },
	        "valid_from": {
	            required: "Enter Dispatch Date",
	        },	 
//	        "valid_to": {
//	            required: "Enter Delivery Date",
//	        },	
	        "post_type": {
	            required: "Enter Post Type",
	        },	
	        
	    },
	    submitHandler: function(form) { // for demo
			$(element).parent('div').after(error);
	    }
	});



	var rules = new Object();
	var messages = new Object();
	$('.relocation_submit_quote input:text').each(function() {
		/*rules[this.name] = { required: true,decimalvalidation: true,fourbytwovalidations:true };*/
        rules[this.name] = { required: true}
		messages[this.name] = { required: 'This field is required' };
	});
	$('.relocation_submit_quote input:checkbox').each(function() {
		rules[this.name] = { required: true};
		messages[this.name] = { required: 'This field is required' };
	});
	$('.relocation_submit_quote select').each(function() {
		rules[this.name] = { required: true };
		messages[this.name] = { required: 'This field is required' };
	});


    $(".relocation_submit_quote").each(function (item) {
        var formid = $(this).attr("id");
        var validator = $("#"+formid).validate({
            ignore: "input[type='text']:hidden,input[type='checkbox']:hidden,input.novalidation",
            rules: rules,
            messages: messages,
            errorPlacement: function(error, element) {
            $(element).parent().append(error);
            },
        });
	});

	$('.relocation_submit_quote input').blur(function(){

		var totalprice = 0;
		var name = $(this).attr("name");
		id = name.substring(name.lastIndexOf('_'));
		var ratecardtype = $("#post_rate_card_type"+id).val();
		var serviceid = $("#serviceid").val();
		 
		if(ratecardtype == 1){
			var volume = $("#total_cft"+id).val();
			var crating_volume = $("#crating_cft"+id).val();
			var od_charges = $("#od_charges"+id).val();
			var transport_charges = $("#transport_charges"+id).val();
			var creating_charges = $("#creating_charges"+id).val();


			if(!isNaN(od_charges)){
				totalprice = parseFloat(volume) * parseFloat(od_charges);
			}
			if(!isNaN(transport_charges) && transport_charges != ""){
				totalprice = totalprice + parseFloat(transport_charges);
			}
			if(!isNaN(creating_charges) && creating_charges != ""){
				totalprice += parseFloat(crating_volume) * parseFloat(creating_charges);
			}
		}else{
			//totalprice = parseInt($("#vehicle_cost_"+$("#vehicle_type"+id).val()).val());
			totalprice = parseFloat($("#od_charges"+id).val());
			var transport_charges = parseFloat($("#transport_charges"+id).val());
			if(serviceid==17)
			{
				
				var cageweight = $("#cageweight"+id).val();
				if(!isNaN(transport_charges) && transport_charges != ""){
					totalprice = parseFloat(totalprice) + (parseFloat(transport_charges)*cageweight);
				}
			}else{
				if(!isNaN(transport_charges) && transport_charges != ""){
					totalprice = parseFloat(totalprice) + parseFloat(transport_charges);
				}
			}
		}
		$('#total_price_display'+id).html(totalprice);
		$('#total_price'+id).val(totalprice);

	})



	//Enquiries Initial Quote PTL
	$(".relocation_quote_submit").click(function(){
       // alert("test");
        var formid = $(this).parent().parent(".relocation_submit_quote").attr("id");
		if($('#'+formid).valid()) {
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
		}
	});
	return false;

});
function updaterelocationpropertypostlineitem(postid){

	var rowid = "#single_property_post_item_"+postid;
	$( ".relocation_house_hold_create input[name='volume']" ).val($( rowid +" .volume" ).val());
	$("#propertytypes option[value='"+$( rowid +" input[name='propertytypes_hidden[]']" ).val()+"']").prop('selected', true);
	$("#load_types option[value='"+$( rowid +" input[name='load_types_hidden[]']" ).val()+"']").prop('selected', true);
	$("#transitdays_units_relocation option[value='"+$( rowid +" input[name='transitdays_units_relocation_hidden[]']" ).val()+"']").prop('selected', true);
	$( ".relocation_house_hold_create input[name='rate_per_cft']" ).val($( rowid +" .rate_per_cft" ).val());
	$( ".relocation_house_hold_create input[name='transport_charges']" ).val($( rowid +" .transport_charges" ).val());
	$( ".relocation_house_hold_create input[name='transit_days']" ).val($( rowid +" .transit_days" ).val());
	$("#current_household_row_id").val(postid);
	$('.selectpicker').selectpicker('refresh');
	$('#update_more_relocation_property').show();
    $("#household_items_mandatory").val(1);

}

function updaterelocationvehiclepostlineitem(postid){

	var rowid = "#single_vehicle_post_item_"+postid;
	$( ".relocation_house_hold_create input[name='volume']" ).val($( rowid +" .volume" ).val());
	$("#vehicle_types option[value='"+$( rowid +" input[name='vehicle_types_hidden[]']" ).val()+"']").prop('selected', true);
	$("#vehicle_type_category option[value='"+$( rowid +" input[name='vehicle_type_category_hidden[]']" ).val()+"']").prop('selected', true);
	$("#transitdays_units_relocation_vehicle option[value='"+$( rowid +" input[name='transitdays_units_relocation_vehicle_hidden[]']" ).val()+"']").prop('selected', true);
	$( ".relocation_vehicle_create input[name='cost']" ).val($( rowid +" .cost" ).val());
	$( ".relocation_vehicle_create input[name='transport_charges_vehicle']" ).val($( rowid +" .transport_charges_vehicle" ).val());
	$( ".relocation_vehicle_create input[name='transit_days_vehicle']" ).val($( rowid +" .transit_days_vehicle" ).val());
	$("#current_vehicle_row_id").val(postid);
	$('.selectpicker').selectpicker('refresh');
	$('#update_more_relocation_vehicle').show();
    if($( rowid +" input[name='vehicle_types_hidden[]']" ).val() == 2){
        $(".vehicle_type_car").hide();
        $("#vehicle_type_category option[value='']").prop('selected', true);
    }else{
        $(".vehicle_type_car").show();
    }
    $("#vehicle_items_mandatory").val(1);

}

function disablerelcationcreatepost(){
	$( "input[name='post_rate_card_type']" ).prop('readonly', true);
	$( "#from_location" ).prop('readonly', true);
	$( "#to_location" ).prop('readonly', true);
	$( "#datepicker" ).prop('disabled', true);
	$( "#datepicker_to_location" ).prop('disabled', true);
}
function getSellerPropertyCft(){	
	//$('#property_id').selectpicker('refresh');
	var data = {
	        'prop_id': $('#propertytypes').val()
	    };
	 $.ajax({
	        type: "GET",
	        url: '/getpropertycft',
	        data: data,
	        dataType: 'text',
	        success: function(data) {	   	        	
	             $("#volume").val(data);	           
	        },
	        error: function(request, status, error) {	            
	        },
	    });	 
}

/**Relocation end */
/******************************Db Formate for all datepickers starts*********************************************/
function convertDateFormatForDatePickerSeller(consignmentPickupDate){
	if(consignmentPickupDate) {
		var dbFormat = consignmentPickupDate.split("/");
		var formattedDate = dbFormat[2]+"-"+dbFormat[1]+"-"+dbFormat[0];
		return formattedDate;
	}
}
/******************************Db Formate for all datepickers ends*********************************************/
/******************************This is seller post creation and edit price slabs starts*********************************************/
function readonlyProperty(){
	$("#check_max_weight").prop( "checked", false );
	$("#check_max_weight_assign").val(0);
	$("#incremental_weight").prop('readonly', true);
    $("#rate_per_increment").prop('readonly', true);
    $("#incremental_weight").val('');
    $("#rate_per_increment").val('');
}
function removeReadonlyProperty(){
	$("#check_max_weight").prop( "checked", true );
	$("#check_max_weight_assign").val(1);
	$("#incremental_weight").removeAttr('readonly');
	$("#rate_per_increment").removeAttr('readonly');
}

function checkPriceForInerment(price,row_id){
	var removeString = 'high_weight_slab_';
    var rowNo = row_id.replace(removeString, '');
    var low_weight_accepted = parseFloat($('#low_weight_salb_'+rowNo).val());
	var max_weight_accepted = parseFloat($('#max_weight_accepted').val());
	var high_price_accepted = price;
	var max_weight_accepted_value = /^\d+(\.\d{1,4})?$/i.test(max_weight_accepted);
	var high_price_first_value = /^\d+(\.\d{1,4})?$/i.test(price);
	if (max_weight_accepted_value == true && high_price_first_value == true){
		if(low_weight_accepted == high_price_accepted){
			$("#erroralertmodal .modal-body").html("Low slab and high slab should not be equal");
			$("#erroralertmodal").modal({show: true});
			$('#high_weight_slab_'+rowNo).val('');
    	}else if(low_weight_accepted > high_price_accepted){
			$("#erroralertmodal .modal-body").html("High slab should be greater than low slab.");
			$("#erroralertmodal").modal({show: true}).one('click','.ok-btn',function (e){
				$('#high_weight_slab_'+rowNo).val('');
				setTimeout(function() { $('#high_weight_slab_'+rowNo).focus().select();}, 4);
				return false;
            });
    	}
		if (max_weight_accepted > high_price_accepted ){
			removeReadonlyProperty();
		}else{
			readonlyProperty();
    	}
	}
}
/******************************This is seller post creation and edit price slabs Ends*********************************************/

function setcancelpostid(str,postid){
	$("#cancellationstrposts").val(str);
	$("#cancellationpostid").val(postid);
}

function setcartitem(itemid){
    $("#delete_cart_item_id").val(itemid);
}

function getSellerVehicleTypes(){

    //alert($("#vehicle_category").val());

    if($("#vehicle_types").val()==2){
        $(".vehicle_type_car").hide();


    }else{
        $(".vehicle_type_car").show();
    }
}

function GetPackages(ptlLoadType) {
    var data = {
        'ptlLoadType': ptlLoadType
    };
    $.ajax({
        type: "POST",
        url: '/getpackages',
        data: data,
        dataType: 'text',
        success: function(data) {
            if (data !="") {
                $('#ptlPackageType').html(data);
                $('#ptlPackageType').selectpicker('refresh');
            }
        },
        error: function(request, status, error) {
            $('#ptlPackageType').html('');
            $('#ptlPackageType').selectpicker('refresh');
        },
    });
}
function GetSearchPackages(ptlLoadType) {
    var data = {
        'ptlLoadType': ptlLoadType
    };
    $.ajax({
        type: "POST",
        url: '/getpackages',
        data: data,
        dataType: 'text',
        success: function(data) {
            if (data !="") {
                $('#lkp_packaging_type_id').html(data);
                $('#lkp_packaging_type_id').selectpicker('refresh');
            }
        },
        error: function(request, status, error) {
            $('#lkp_packaging_type_id').html('');
            $('#lkp_packaging_type_id').selectpicker('refresh');
        },
    });
}
function GetTermPackages(ptlLoadType) {
    var data = {
        'ptlLoadType': ptlLoadType
    };
    $.ajax({
        type: "POST",
        url: '/getpackages',
        data: data,
        dataType: 'text',
        success: function(data) {
            if (data !="") {
                $('#term_package_type').html(data);
                $('#term_package_type').selectpicker('refresh');
            }
        },
        error: function(request, status, error) {
            $('#term_package_type').html('');
            $('#term_package_type').selectpicker('refresh');
        },
    });
}
function GetSpotTermPackages(ptlLoadType) {
    var data = {
        'ptlLoadType': ptlLoadType
    };
    $.ajax({
        type: "POST",
        url: '/getpackages',
        data: data,
        dataType: 'text',
        success: function(data) {
            if (data !="") {
                $('#spot_package_type').html(data);
                $('#spot_package_type').selectpicker('refresh');
            }
        },
        error: function(request, status, error) {
            $('#spot_package_type').html('');
            $('#spot_package_type').selectpicker('refresh');
        },
    });
}
