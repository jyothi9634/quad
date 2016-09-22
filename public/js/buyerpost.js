$(function() {
    
    $(document).on('focus click keyup keypress blur change', '#vehicle', function() {
	    $("#vehicle").autocomplete({
	        //source: "/autocomplete",
	    	source: "/autocompletevehicles",
	        minLength: 1,
	        select: function(event, ui) {
	            $('#vehicle').val(ui.item.value);
                $(this).closest("form").validate().element($('#vehicle'));
                /*Need to add this below class to every autocomplete: Shriram */
                $('#vehicle').addClass("clsAutoDisable");
	        }
	    });
    });
    
    $('input[name="is_check_commercial"]').click(function() {
        $('#is_commercial').val(this.value);
    	
    });
    
    //th and tl booknow timepicker js added by swathi 04-05-2016
    $(document).on("focus click keyup keypress blur change",'#booknw_reporting_time', function(e){
    	$(".hour.disabled, .minute.disabled").addClass("timeDisable");
        var reportingDate = $('.buyer_counter_offer_reporting_date').val();
        $("#err_bk_reporting_time").html('');
        if(reportingDate == null || reportingDate == ''){
            $(".error_calendar").html('Select Reporting Date first');
            return false;
        }
        $("#booknw_reporting_time").datetimepicker({
            format: 'hh:ii',
            autoclose: true,
            startView: 1,
            maxView: 1,
            minuteStep:2,
            linkField: $(".timepicker_from").attr('name'),
            linkFormat: "hh:ii"
        }).on("show", function(e){
            var currDate = moment().format('DD/MM/YYYY');
            if(reportingDate == currDate){
                var lBd = $('.buyer_counter_offer_reporting_date').val().split('/').reverse();
                var TimeZoned = new Date( lBd[0], (lBd[1]-1), lBd[2], moment().format('H'), moment().format('mm'), moment().format('ss'));
            }else{
            	var lBd = $('.buyer_counter_offer_reporting_date').val().split('/').reverse();
            	var TimeZoned = new Date( lBd[0], (lBd[1]-1), lBd[2]);
            } 
            $('#booknw_reporting_time').datetimepicker('setStartDate', TimeZoned);
        }).on('changeDate', function(e){
            /*added by shriram on jun 22*/
            $("#booknw_reporting_to_time .timepicker_to").val('');
        });            
    });
    
    $(document).on("focus click keyup keypress blur change",'#booknw_reporting_to_time', function(e){
        $(".datetimepicker.datetimepicker-dropdown-bottom-right").addClass("alignDatepicker");
        $(".hour.disabled, .minute.disabled").addClass("timeDisable");
        //Checking reporting date
        var reportingDate = $('.buyer_counter_offer_reporting_date').val();
        $(".error_calendar").html('');
        if(reportingDate == null || reportingDate == ''){
            $(".error_calendar").html('Select Report Date first');
            return false;
        }
        var reportingtime = $('.timepicker_from').val();
        $(".error_time").html('');
        if(reportingtime == null || reportingtime == ''){
            $(".error_time").html('Select Report From Time first');
            return false;
        }
        $("#booknw_reporting_to_time").datetimepicker({
            format: 'hh:ii',
            autoclose: true,
            startView: 1,
            maxView: 1,
            minuteStep:2,
            linkField: $(".timepicker_to").attr('name'),
            linkFormat: "hh:ii"
        }).on("show", function(e){
            var reportingDate = $('.buyer_counter_offer_reporting_date').val();
            var currDate = moment().format('DD/MM/YYYY');
            
            var lBd = $('.buyer_counter_offer_reporting_date').val().split('/').reverse();
            var lBdt = $('.timepicker_from').val().split(':');
            //var TimeZoned = new Date( lBd[0], lBd[1], lBd[2], moment().format('H'), moment().format('mm'), moment().format('ss'));
            var TimeZoned = new Date( lBd[0], lBd[1], lBd[2], lBdt[0], lBdt[1], moment().format('ss'));
            $('#booknw_reporting_to_time').datetimepicker('setStartDate', TimeZoned);
            
        }); 
    });
    //end th and tl booknow timepicker js
     
    
    //new js for TH
// Check out page payment method option validation
$(document).on("click",".paymentMode",function() {
    payment_method = $(this).attr('data-value');
    $('input[name="gatewayName"]').attr('checked', false);
    $('#bank_'+payment_method+'_1').trigger('click');
    $('#payment_method').val(payment_method);
    if(payment_method=='NB'){
        $('.neft_hide').addClass('displayNone');
    }else{
        $('.neft_hide').removeClass('displayNone');
    }
});
//th quotes booknow
 $(document).on("click","#addbuyerpostcounteroffer_th .add_buyer_addtocart_details,#addbuyerpostcounteroffer_th .add_buyer_checkout_details", function() {
       
        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var isCheckout;
        if($(this).text() == 'Checkout') {
            isCheckout = '1';
        } else {
            isCheckout = '0';
        }
        var sourceLocationType = $('#buyer_counter_offer_source_location_type_' + rowNo).val();
        //other fields for ftl quotes
        var sourceLocationTypeOther = $('#buyer_counter_offer_source_location_type_text').val();
        
        var consignmentPickupDate = $('#buyer_counter_offer_reporting_date_' + rowNo).val();
        var consignmentPickupFromTime = $('#buyer_counter_offer_reporting_fromtime_' + rowNo).val();
        var consignmentPickupToTime = $('#buyer_counter_offer_reporting_totime_' + rowNo).val();
        
        var consignorName = $('#buyer_counter_offer_consignor_name_' + rowNo).val();
        var consignorNumber = $('#buyer_counter_offer_consignor_number_' + rowNo).val();
        var consignorEmail = $('#buyer_counter_offer_consignor_email_' + rowNo).val();
        var consignorAddress = $('#buyer_counter_offer_consignor_address_' + rowNo).val();
        var consignorPin = $('#buyer_counter_offer_consignor_pincode_' + rowNo).val();
        var buyerId = $('#buyer_post_buyer_id_' + rowNo).val();
        var sellerId = $('#buyer_post_seller_id_' + rowNo).val();
        var quoteItemId = $('#buyer_quote_item_id_' + rowNo).val();
        var postItemId = $('#seller_post_item_id_' + rowNo).val();
        var price = $('#buyer_post_price_' + rowNo).data('price');
        var additionalDetails = $('#buyer_counter_offer_additional_details_' + rowNo).val();

        var sellerPostedFromDate = $('#buyer_counter_offer_seller_post_from_date_' + rowNo).val();
        var sellerPostedToDate = $('#buyer_counter_offer_seller_post_to_date_' + rowNo).val();
        checkAndSetBooknow_th(sourceLocationTypeOther,sourceLocationType, 
             buyerId, sellerId, consignmentPickupDate, consignmentPickupFromTime,consignmentPickupToTime,
            consignorName, consignorNumber, consignorEmail, consignorAddress,
            consignorPin,  additionalDetails,
            rowNo,  quoteItemId, postItemId, price,isCheckout, null,
            sellerPostedFromDate, sellerPostedToDate);
    });
    
    //For search book now in th
    $(document).on("click","#TH-buyer-search-booknow .booknow_buyer,#TH-buyer-search-booknow .add_buyer_checkout_details", function() {
    
        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var isCheckout;
        if($(this).text() == 'Checkout') {
            isCheckout = '1';
        } else {
            isCheckout = '0';
        }
        var sourceLocationType = $('#buyersearch_booknow_offer_source_location_type_' + rowNo).val();
        //other fields for ftl search
        var sourceLocationTypeOther = $('#buyersearch_booknow_offer_source_location_type_text').val();
        
        var consignmentPickupDate = $('#buyersearch_booknow_offer_reporting_date_' + rowNo).val();
        var consignmentPickupFromTime = $('#buyersearch_booknow_offer_reporting_fromtime_' + rowNo).val();
        var consignmentPickupToTime = $('#buyersearch_booknow_offer_reporting_totime_' + rowNo).val();
        var consignorName = $('#buyersearch_booknow_offer_consignor_name_' + rowNo).val();
        var consignorNumber = $('#buyersearch_booknow_offer_consignor_number_' + rowNo).val();
        var consignorEmail = $('#buyersearch_booknow_offer_consignor_email_' + rowNo).val();
        var consignorAddress = $('#buyersearch_booknow_offer_consignor_address_' + rowNo).val();
        var consignorPin = $('#buyersearch_booknow_offer_consignor_pincode_' + rowNo).val();
        var additionalDetails = $('#buyersearch_booknow_offer_additional_details_' + rowNo).val();
        var buyerId = $('#buyersearch_booknow_buyer_id_' + rowNo).val();
        var sellerId = $('#buyersearch_booknow_seller_id_' + rowNo).val();
        var price = $('#buyersearch_booknow_seller_price_' + rowNo).val();
        var quoteItemId = '';
        var postItemId = rowNo;
        var sellerPostedFromDate = $('#buyersearch_booknow_dispatch_date_' + rowNo).val();
        var sellerPostedToDate = $('#buyersearch_booknow_delivery_date_' + rowNo).val();
        
        //post creation for book now
        allData = {'postItemId': postItemId};
        $.ajax({
                type: "POST",
                url: "/setbuyerpost",
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
                    if (data!=0&& data!='') {
                        quoteItemId=data;
                        checkAndSetBooknow_th(sourceLocationTypeOther,sourceLocationType, 
             buyerId, sellerId, consignmentPickupDate, consignmentPickupFromTime,consignmentPickupToTime,
            consignorName, consignorNumber, consignorEmail, consignorAddress,
            consignorPin,  additionalDetails,
            rowNo,  quoteItemId, postItemId, price,isCheckout, null,
            sellerPostedFromDate, sellerPostedToDate);
                    } 
                }
            }, "json");
        
    });
    //th leads booknow
    $(document).on("click","#TH-buyer-leads-booknow .booknow_buyer,#TH-buyer-leads-booknow .add_buyer_checkout_details", function() {
        //alert('here');
        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var isCheckout;
        if($(this).text() == 'Checkout') {
            isCheckout = '1';
        } else {
            isCheckout = '0';
        }
        var sourceLocationType = $('#buyersearch_booknow_offer_source_location_type_' + rowNo).val();
        //other fields for ftl leads
        var sourceLocationTypeOther = $('#buyersearch_booknow_offer_source_location_type_text').val();
        
        var consignmentPickupDate = $('#buyersearch_booknow_offer_reporting_date_' + rowNo).val();
        var consignmentPickupFromTime = $('#buyersearch_booknow_offer_reporting_fromtime_' + rowNo).val();
        var consignmentPickupToTime = $('#buyersearch_booknow_offer_reporting_totime_' + rowNo).val();
        var consignorName = $('#buyersearch_booknow_offer_consignor_name_' + rowNo).val();
        var consignorNumber = $('#buyersearch_booknow_offer_consignor_number_' + rowNo).val();
        var consignorEmail = $('#buyersearch_booknow_offer_consignor_email_' + rowNo).val();
        var consignorAddress = $('#buyersearch_booknow_offer_consignor_address_' + rowNo).val();
        var consignorPin = $('#buyersearch_booknow_offer_consignor_pincode_' + rowNo).val();
        var additionalDetails = $('#buyersearch_booknow_offer_additional_details_' + rowNo).val();
        var sellerId = $('#buyer_leads_post_seller_id_' + rowNo).val();
        var quoteItemId = $('#cancel_buyer_counter_offer_enquiry').data('id');
        var postItemId = $('#leads_seller_post_item_id_' + rowNo).val();

        var price = $('#buyer_leads_price' + rowNo).val();
        var buyerId = $('#buyer_leads_post_buyer_id_' + rowNo).val();

        var sellerPostedFromDate = $('#buyer_counter_offer_seller_post_from_date_' + rowNo).val();
        var sellerPostedToDate = $('#buyer_counter_offer_seller_post_to_date_' + rowNo).val();
        checkAndSetBooknow_th(sourceLocationTypeOther,sourceLocationType, 
             buyerId, sellerId, consignmentPickupDate, consignmentPickupFromTime,consignmentPickupToTime,
            consignorName, consignorNumber, consignorEmail, consignorAddress,
            consignorPin,  additionalDetails,
            rowNo,  quoteItemId, postItemId, price,isCheckout, null,
            sellerPostedFromDate, sellerPostedToDate);
        
    });
    
    //TH quotes book now reporting date validations
    $(document).on("change paste","#addbuyerpostcounteroffer_th .buyer_counter_offer_reporting_date",function() {
    
        var itemId = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var consignmentPickupDate = $('#buyer_counter_offer_reporting_date_' + itemId).val();
               
        if (consignmentPickupDate) {
            var formattedConsignmentPickupDate = convertDateFormatForDatePicker(consignmentPickupDate);
            //var buyerDispatchDate = $('#buyer_counter_offer_seller_post_from_date_' + itemId).val();
            var buyerDispatchDate = $('#fdispatch-date_' + itemId).val();
            var formattedSellerPostedFromDate = convertDateFormat(buyerDispatchDate);
            var sellerPostedToDate = $('#buyer_counter_offer_seller_to_date_' + itemId).val();
            //var deliveryDate = $('#buyer_counter_offer_seller_post_to_date_' + itemId).val();
            //alert(formattedConsignmentPickupDate);alert(formattedSellerPostedFromDate);
            if(!sellerPostedToDate) {
                if(formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                    $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                    $(this).val("");
                    return false;
                }
            }
            var formattedSellerPostedToDate = convertDateFormat(sellerPostedToDate);
            if (formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
            if (formattedConsignmentPickupDate > formattedSellerPostedToDate) {
                $("#erroralertmodal .modal-body").html("Seller post not valid for pickup date selected.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
        }
    });
    
    //TH leads book now pickup date validations
    $(document).on("change paste","#TH-buyer-leads-booknow .buyer_counter_offer_reporting_date",function() {
   
        var itemId = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var consignmentPickupDate = $(this).val();
        if (consignmentPickupDate) {
            var formattedConsignmentPickupDate = convertDateFormatForDatePicker(consignmentPickupDate);
            var buyerDispatchDate = $('#fdispatch-date_' + itemId).val();
            var formattedSellerPostedFromDate = convertDateFormat(buyerDispatchDate);
            var sellerPostedToDate = $('#buyer_leads_counter_offer_seller_to_date_' + itemId).val();
            if(!sellerPostedToDate) {
                if(formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                    $("#erroralertmodal .modal-body").html("Reporting date cannot be before dispatch date.");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                    $(this).val("");
                    return false;
                }
            }
            var formattedSellerPostedToDate = convertDateFormat(sellerPostedToDate);
            if (formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                $("#erroralertmodal .modal-body").html("Reporting date cannot be before dispatch date.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
            if (formattedConsignmentPickupDate > formattedSellerPostedToDate) {
                $("#erroralertmodal .modal-body").html("Seller post not valid for Reporting date selected.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
        }
    });
    
    //TH search book now pickup date validations
    $(document).on("change paste","#TH-buyer-search-booknow .buyer_counter_offer_reporting_date",function() {
    
        var itemId = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        //var consignmentPickupDate = $('#buyer_counter_offer_consignment_pickup_date_' + itemId).val();
        var consignmentPickupDate = $(this).val();       
        if (consignmentPickupDate) {
            var formattedConsignmentPickupDate = convertDateFormatForDatePicker(consignmentPickupDate);
            var sellerPostedFromDate = $('#buyersearch_booknow_from_date_' + itemId).val();
            var buyerDispatchDate = $('#fdispatch-date_' + itemId).val();
            var formattedSellerPostedFromDate = convertDateFormat(buyerDispatchDate);
            var sellerPostedToDate = $('#buyersearch_booknow_to_date_' + itemId).val();
            if(!sellerPostedToDate) {
                if(formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                    $("#erroralertmodal .modal-body").html("Reporting date cannot be before dispatch date.");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                    $(this).val("");
                    return false;
                }
            }
            var formattedSellerPostedToDate = convertDateFormat(sellerPostedToDate);
            if (formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                $("#erroralertmodal .modal-body").html("Reporting date cannot be before dispatch date.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
            if (formattedConsignmentPickupDate > formattedSellerPostedToDate) {
                $("#erroralertmodal .modal-body").html("Seller post not valid for Reporting date selected.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
        }
    });
    
    
    //added for others fileds in booknow
    $(document).on('change', '.buyer_counter_offer_source_location_type_ , .buyersearch_booknow_offer_source_location_type_', function() {
        if($(this).val()==11){
            $('#buyer_counter_offer_source_location_type_text').parent().parent().css('display','block');
            $('#buyersearch_booknow_offer_source_location_type_text').parent().parent().css('display','block');
        }else{
            $('#buyer_counter_offer_source_location_type_text').parent().parent().css('display','none');
            $('#buyersearch_booknow_offer_source_location_type_text').parent().parent().css('display','none');
        }
    });
    $(document).on('change', '.buyersearch_booknow_offer_destination_location_type_ , .buyer_counter_offer_destination_location_type_', function() {
        if($(this).val()==11){
            $('#buyer_counter_offer_destination_location_type_text').parent().parent().css('display','block');
            $('#buyersearch_booknow_offer_destination_location_type_text').parent().parent().css('display','block');
        }else{
            $('#buyer_counter_offer_destination_location_type_text').parent().parent().css('display','none');
            $('#buyersearch_booknow_offer_destination_location_type_text').parent().parent().css('display','none');
        }
    });
    $(document).on('change', '.buyer_counter_offer_packaging_type_ , .buyersearch_booknow_offer_packaging_type_', function() {
        if($(this).val()==13){
            $('#buyer_counter_offer_packaging_type_text').parent().parent().css('display','block');
            $('#buyersearch_booknow_offer_packaging_type_text').parent().parent().css('display','block');
        }else{
            $('#buyer_counter_offer_packaging_type_text').parent().parent().css('display','none');
            $('#buyersearch_booknow_offer_packaging_type_text').parent().parent().css('display','none');
        }
    });//end
	if($('#term_contract_to_dateformated').val()!=""){
		var d = new Date();
		var curr_date = d.getDate();
		var curr_month = d.getMonth();
		var curr_year = d.getFullYear();
		
		var date1 = convertDateFormatToDisplay(convertDateFormat($("#term_contract_from_date").val()));
		var date2 = curr_date + "/" + curr_month + "/" + curr_year;
		var date1 = new Date(date1);
		var date2 = new Date(date2);
		//console.log(date1);	
		//console.log(date2);	
		var diffDays=date2.getDate() - date1.getDate();
		//console.log(diffDays);
		if(diffDays>=0){
		var minDate=0;	
		}else{
			//alert($("#term_contract_from_date").val());
			var minDate=convertDateFormatToDisplay(convertDateFormat($("#term_contract_from_date").val()));
			
		}
            $('.buyer_counter_offer_consignment_pickup_date').datepicker({ 
                dateFormat: "dd/mm/yy",
                minDate: minDate, 
                maxDate: $('#term_contract_to_dateformated').val()
            });
            
        
        }
	$(".buyer_counter_offer_consignment_pickup_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        //minDate:$('#cpick').val(),
        minDate:convertDateFormatToDisplay(convertDateFormat($(".flexyDispatch").val())),
        dateFormat: "dd/mm/yy",
    });
    
    var pick_len=$("#pick_vehicles").children( ".table-row" ).length;//alert(len);
    if(pick_len>0){
        $('.truckhaul').show();
    }else{
        $('.truckhaul').hide();
    }
    
    $("#cdelivery_date").datepicker({
            changeMonth: true,
            numberOfMonths: 1,
            //minDate:$('#cpick').val(),
            minDate:$('#cpick').val(),
            maxDate:0,
            dateFormat: "dd/mm/yy",
            onSelect: function(dateText, inst) {
                $('#delivery_time').val("");
            }
        });
    
    $("#reporting_date").datepicker({
        changeMonth: true,       
        minDate: 0,        
        //maxDate:$('#cdelivery').val(),
        dateFormat: "dd/mm/yy",        
    });
    $("#pick_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        minDate: $('#cpick').val(),
        maxDate:0,
        //maxDate:$('#cdelivery').val(),
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            if($("#pick_date").val()!=""){
            $("#lr_date").datepicker(
                "option", "minDate", selectedDate);
            }
        }
    });
    
    if($("#pick_date").val()!=""){
    $("#lr_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        //minDate: 0,
        maxDate:0,
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            //$("#pick_date").datepicker("option","minDate", selectedDate);
        }
    });
    }else{
        $("#lr_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        minDate:$('#cpick').val(),
        maxDate:0,
        dateFormat: "dd/mm/yy",
        
    });
    }
    /*$("#cdelivery_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        //minDate:$('#cpick').val(),
        minDate:$("#track_locations .loc_date:last").text(),
        maxDate:0,
        dateFormat: "dd/mm/yy",
    });*/
    var len=$("#track_locations").children( ".table-row" ).length;//alert(len);
    if(len>0){
       $('#track_confirm').show();
        $("#date").datepicker({
            changeMonth: true,
            numberOfMonths: 1,
            minDate:$("#track_locations .loc_date:last").text(),
            maxDate:0,
            dateFormat: "dd/mm/yy",
            onClose: function(selectedDate) {
                //$("#date").datepicker("option", "minDate", selectedDate);
            /*if($("#date").val()!=""){
                if( Date($("#track_locations .loc_date:last").text())>Date(selectedDate))
                    $("#date").datepicker("option", "minDate", $("#track_locations .loc_date:last").text());
                else
                    $("#date").datepicker("option", "minDate", selectedDate);
            }*/
            }
        });
        $("#cdelivery_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        //minDate:$('#cpick').val(),
        minDate:$("#track_locations .loc_date:last").text(),
        maxDate:0,
        dateFormat: "dd/mm/yy",
        onSelect: function(dateText, inst) {
            $('#delivery_time').val("");
        }
    });
    }else{
        $('#track_confirm').hide();
        $("#date").datepicker({
            changeMonth: true,
            numberOfMonths: 1,
            minDate:$('#cpick').val(),
            maxDate:0,
            dateFormat: "dd/mm/yy",
            onClose: function(selectedDate) {
                if(selectedDate)
                {
                   
                    /*if( Date($("#track_locations .loc_date:last").text())>Date(selectedDate))
                        $("#date").datepicker("option", "minDate", $("#track_locations .loc_date:last").text());
                    else
                        $("#date").datepicker("option", "minDate", selectedDate);*/
                }
            }
        });
        $("#cdelivery_date").datepicker({
            changeMonth: true,
            numberOfMonths: 1,
            //minDate:$('#cpick').val(),
            minDate:$('#cpick').val(),
            maxDate:0,
            dateFormat: "dd/mm/yy",
            onSelect: function(dateText, inst) {
                $('#delivery_time').val("");
            }
        });
    }
    
	$( ".search-items" ).each(function( index ) {
	var id	=	$( this ).attr( "id" );	
	$('#basic_'+id).text( $('#base_freight_price_'+id).text() + '/-' );
	if($('#doordelivery_'+id).text() == '') {
		var doordelivery = 0;
	} else {
		var doordelivery = $('#doordelivery_'+id).text();
	}
	if($('#doorpickup_'+id).text() == '') {
		var doorpickup = 0;
	} else {
		var doorpickup= $('#doorpickup_'+id).text();
	}
    var totalAmount = parseFloat($('#tot_price_new_'+id).text())+parseFloat(doordelivery)+parseFloat(doorpickup);
    //var totalAmount = parseInt($('#basic_'+id).text());
	$('#total_'+id).text( commaSeparateNo( totalAmount.toFixed(2), false )  ).append(' /-');
	$('#total_search_booknow_price_'+id).val(totalAmount.toFixed(2));
	$('#buyer_post_price_'+id).text( commaSeparateNo( totalAmount.toFixed(2), true) );
	});
	
	//Check price calculation adding in js buyer leads price
	$( ".totalprice_calc_leads" ).each(function( index ) {
		var leadsId  =   $(this).attr('leads_id');
		var amnt=  $('#total_leads_price_'+leadsId).val();
		$('#buyer_leads_post_price_'+leadsId).text(amnt);		
	});
	
	//Check price calculations for LTL Leads in buyer section.
	$( ".chargeable_checkamnt_class" ).each(function( index ) {
		var leadsId  =   $(this).attr('leads_id');		
		var totalbase=  $('.total_base_oda_'+leadsId).last().text();
		var total=  $('.total_all_'+leadsId).last().text();
		//alert(total);
		$('#ltl_leads_basefright_'+leadsId).text(totalbase).append(' /-');
		$('#total_hidden_price_'+leadsId).text(total);
		$('#ptl_buyer_leads_post_price_'+leadsId).text(total).append(' /-');
	});
	
	$( ".courier_total_price_calc" ).each(function( index ) {
		var leadsId  =   $(this).attr('leads_id');		
		var tot_price_cal=$('.courier_tot_price_calc_'+leadsId).text();
		var tot_charge_amnt=$('.courier_tot_chargeable_calc_'+leadsId).text();
		$('#ltl_leads_basefright_'+leadsId).text(tot_charge_amnt).append(' /-');
		$('#total_hidden_price_'+leadsId).text(tot_price_cal);
		$('#ptl_buyer_leads_post_price_'+leadsId).text(tot_price_cal).append(' /-');		
	});
	
	
	//Check condition for caclulation.
	/*$( ".volume_calc" ).each(function( index ) {
		var id	=	$( this ).attr( "id" );	
		if($('#ratecardtype_'+id).text() == 1) {
			var totalAmount = (parseInt($('#volumecft_'+id).text())*parseInt($('#odacharges_'+id).text()))+parseInt($('#transportcharges_'+id).text())
			  ;
		} else {
			var totalAmount = parseInt($('#odacharges_'+id).text())+parseInt($('#transportcharges_'+id).text())
			 ;	
		}
	    
    //alert(parseInt($('#odacharges_'+id).text()));
		$('#totalestimatecharges_'+id).text(totalAmount);
        $('#buyersearch_booknow_seller_price_'+id).val(totalAmount);
		});	*/
	
    $("#ptl_select_all_name").click(function() {
        $( ".ptl_select_name" ).trigger("click");
    });
    $("#ftl_select_all_name").click(function() {
        $( ".ftl_select_name" ).trigger("click");
    });

    //Buyer search result details page toggle script
    $(".accept_contract").click(function() {
        var buyerId = $(this).data("orderid");
       // console.log($(this).html());
        //var accept_show_button_expand = $(this).parent().parent().find("span.show-icon").css("display");
        //if(accept_show_button_expand =="none"){
        if($(this).attr('rel')=="Accept Contract"){
        	
           $(".accept_hide_button_"+buyerId).attr('style','display: block;');	
        }else{
        	//alert("hello");
        	$(".accept_hide_button_"+buyerId).attr('style','display: none;');
        	
        	console.log($("#minus-icon_"+buyerId).attr("style"));
        	 if($("#minus-icon_"+buyerId).attr("style")=="display: none;"){
        		
             	$("#minus-icon_"+buyerId).attr("style","display:inline;");	
             	$("#plus-icon_"+buyerId).attr("style","display: none;");	
             }else{
            	
             	$("#plus-icon_"+buyerId).attr("style","display:inline;");
             	$("#minus-icon_"+buyerId).attr("style","display: none;");
             }
        }
        //console.log($("#minus-icon").attr("style"));
       
        //console.log($("#plus-icon").attr("style"));
      //  $(".accept_hide_button_"+buyerId).show();
        $(".accept_"+buyerId).slideToggle("500");
        //}
    });
    $(".buyerdetails_list").click(function() {
        var buyerId = $(this).closest('span').data("sellerlistid");
        $(".buyer_listdetails_" + buyerId).slideToggle("500");
        $(".buyerbooknow_listdetails_" + buyerId).hide("500");
    });
    
    //Buyer Market ledas for seller data toggle
    $(".buyerdetails_list").click(function() {
        var buyerId = $(this).closest('span').data("sellerlistid");
        $(".buyer_listdetails_" + buyerId).slideToggle("500");
        $(".buyerbooknow_listdetails_" + buyerId).hide("500");
    });
    
    
    //Added code for toggle for details in term count offer page - srinu (21-3-2016) and bug no :
    $(".buyertermdetails_list").click(function() {
        var buyerId = $(this).closest('span').data("sellerlistid");        
        $(".buyer_listdetails_" + buyerId).slideToggle("500");
        $(".buyerbooknow_listdetails_" + buyerId).hide("500");
    });
    
    
    $(".tabs-showdiv").click(function() {
        $(".tabs-showdiv").removeClass( "active" );
        $(this).addClass( "active" );
        var divId = $(this).data("showdiv");
//        console.log(divId);
        $(".tabs-group").hide();
        $("#"+divId).show();
    });

    $(".ptl_buyer_submit_counter_offer").click(function() {
        var buyerId = $(this).data("buyerpostofferid");
        $("#ptl_counter_offer_details_" + buyerId).slideToggle("500");
    });
    // Location wise selection auto complete for from and to
    $(document).on('focus click keyup keypress blur change', '#from_location', function() {
	    $("#from_location").autocomplete({
	        //source: "/autocomplete",
	    	source: "/autocomplete?fromlocation="+$('#to_location_id').val(),
	        minLength: 1,
	        select: function(event, ui) {
	            $('#from_location').val(ui.item.value);
	            $('#from_location_id').val(ui.item.id);
                $(this).closest("form").validate().element($('#from_location_id'));
                /*Need to add this below class to every autocomplete: Shriram */
                $('#from_location').addClass("clsAutoDisable");
	        }
	    });
    });
    
    $(document).on('focus click keyup keypress blur change', '#to_location', function() {
        $("#to_location").autocomplete({
            //source: "/autocomplete?fromlocation=" + from_location,
            source: "/autocomplete?fromlocation="+$('#from_location_id').val(),
            minLength: 1,
            select: function(event, ui) {
                $('#to_location').val(ui.item.value);
                $('#to_location_id').val(ui.item.id);
                $(this).closest("form").validate().element($('#to_location_id'));
                /*Need to add this below class to every autocomplete: Shriram */
                $('#to_location').addClass("clsAutoDisable");
            }
        });
    });
    // Add at least one item to the list in add more items section
    // checking add items empty or not
    $('#add_buyer_quote').click(function(e) {
        var id = $('.request_rows .table-row').children().size();
        if (id == 0) {
           // alert("Please add atleast one item to the list");
            $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        } else {
            $('#buyer_quote_form').submit();
            if($('#buyer_quote_form').valid()){
                $("#add_buyer_quote").prop('disabled', true);                    
            }
            return true;
        }
    });
    $('#draft_quote').click(function(e) {
        var id = $('.request_rows').children().size();
        if (id == 0) {
            //alert("Please add atleast one item to the list");
            $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
                show: true
            });
        } else {
            $('#buyer_quote_form').submit();
            return true;
        }
    });
    // Dispatch and Delivery date pickers for PTL and FTl
    $("#dispatch_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        minDate: 0,
        show_flexible: 1,
        flex_identifier: "is_dispatch_flexible",
        flex_text: "Flexible dates",
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            $("#delivery_date").datepicker(
                "option", "minDate", selectedDate);
        }
    });
    $("#delivery_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        show_flexible: 1,
        flex_identifier: "is_delivery_flexible",
        flex_text: "Flexible dates",
        minDate: 0,
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            $("#dispatch_date").datepicker("option",
                "maxDate", selectedDate);
        }
    });
    // Buyer search form date picker without condtions min date and remaing
    $("#dispatch_date1").datepicker({
        changeMonth: true,
        minDate: 0,
        dateFormat: "dd/mm/yy",
        show_flexible: 1,
        flex_identifier: "dispatch_flexible",
        flex_text: "Flexible dates",
        onClose: function(selectedDate) {
            $("#delivery_date1").datepicker("option", "minDate", selectedDate);
        }
    });
    $("#delivery_date1").datepicker({
        changeMonth: true,
        minDate: 0,
        show_flexible: 1,
        flex_identifier: "delivery_flexible",
        flex_text: "Flexible dates",
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            $("#dispatch_date1").datepicker("option", "maxDate", selectedDate);
        }
    });
    // Buyer search for sellers results filter datepicker
    $('.filter_calendar_dates').datepicker({
        dateFormat: "dd/mm/yy",
        onSelect: function(dateText, inst) {
            $(this).closest("form").submit();
        }
    });
    // Hide Toatal Ftl buyer quote form in and show conditions
    $('#lead_type').change(function(e) {
        var lead_type = $('#lead_type').val();
        if (lead_type == 2) {
            $("#totla_tab_hide").css("display", "none");
            $("#totla_tab_hide2").css("display", "none");
            $("#totla_tab_hide3").css("display", "none");
            return false;
        } else {
            $("#totla_tab_hide").css("display", "block");
            $("#totla_tab_hide2").css("display", "block");
            $("#totla_tab_hide3").css("display", "block");
        }
    });
    // Add more items elements store in hidden fileds
    var seller_id_list = new Array();
    var sel_list = new Array();
    $('#add_buyer_more')
        .click(
            function() {
                $("#buyer_quotelineitems_form_validation").validate().cancelSubmit = false; // Validation
                // for
                // add
                // more
                // in
                // after
                // first
                // time
                var num = parseInt($('#next_add_buyer_more_id').val()) + 1;
                $('#next_add_buyer_more_id').val(num);
                var from_location_value = $('#from_location_id').val();
                var to_location_value = $('#to_location_id').val();
                var load_type_value = $("#load_type option:selected").text();
                var vehicle_type_value = $("#vehicle_type option:selected").text();
                var delivery_date = $('#delivery_date').val();
                var dispatch_date = $('#dispatch_date').val();
                var units_value = $('#capacity').val();
                var from_location = $('#from_location').val();
                var to_location = $('#to_location').val();
                var load_type = $('#load_type').val();
                var vehicle_type = $('#vehicle_type').val();
                var units = $('#capacity').val();
                var quantity = $('#quantity').val();
                if($("#quote_id").val()==2){
                var price = $('#price').val();
                }else{
                var price = "";	
                }
                var Quotes_quote_type = $('#quote_id').val();
                var noofloads = $('#no_of_loads').val();
                var is_dispatch_flexible = $('#is_dispatch_flexible_hidden').val();
                var is_delivery_flexible = $('#is_delivery_flexible_hidden').val();
                //alert(from_location); return false;
                if (Quotes_quote_type == '1') {
                    var price_no = "0";
                } else {
                    if (isNaN(price)) {
                        alert("Please Enter Valid Price");
                        document.getElementById("price").value = '';
                        document.getElementById("price").focus();
                        return false;
                    }
                    var price_no = $('#price').val();
                }
                if (from_location != "" && to_location != "" && from_location_value != '' && to_location_value != '' && load_type != "" && vehicle_type != "" && units != "" && quantity != "" && Quotes_quote_type != "" && price_no != "" && dispatch_date != "") {
                	var unique = dispatch_date+delivery_date+from_location_value+to_location_value+load_type+vehicle_type+quantity+units+Quotes_quote_type+price_no;
                	
                	if ($.inArray(unique,sel_list)==-1) {
                	//sel_list.unshift(unique);
                	if($("#update_line").val()==1){
                	$('.request_row_' + $("#update_row_count").val()).remove();	
                	$("#update_line").val(0);
                	}
                    $('#error-add-item').text('');
                    var seller_location_id = from_location_value;
                    seller_id_list.unshift(seller_location_id);
                    var html = '<div class="table-row inner-block-bg request_row_' + num + '"><div class="col-md-2 padding-left-none" id="from_loc_'+ num +'">' + from_location + '</div><div class="col-md-2 padding-left-none" id="to_loc_'+ num +'">' + to_location + '</div><div class="col-md-3 padding-left-none" id="load_type_text_'+ num +'">' + load_type_value + '</div><div class="col-md-2 padding-left-none" id="vehicle_type_text_'+ num +'">' + vehicle_type_value + '</div><div class="col-md-2 padding-none" id="price_'+ num +'">' + price + '</div><div class="class="col-md-2 padding-none"><a class="edit_this edit" row_id="' + num + '"><i class="fa fa-edit red" title="Edit"></i></a>&nbsp;<a class="remove_this remove" row_id="' + num + '"><i class="fa fa-trash red" title="Delete"></i></a></div><input type="hidden" name="from_location[]" class="from_location"  value="' + from_location_value + '" id="from_location_id_'+num+'"><input type="hidden" name="to_location[]" value="' + to_location_value + '" id="to_location_id_'+num+'"><input type="hidden" name="delivery_date[]" value="' + delivery_date + '" id="delivery_date_'+num+'"><input type="hidden" name="dispatch_date[]" value="' + dispatch_date + '" id="dispatch_date_'+num+'"><input type="hidden" name="load_type[]" value="' + load_type + '" id="load_type_'+num+'"><input type="hidden" name="vehicle_type[]" value="' + vehicle_type + '" id="vehicle_type_'+num+'"><input type="hidden" name="capacity[]" value="' + units + '" id="unit_cap_'+num+'"><input type="hidden" name="quantity[]" value="' + quantity + '" id="quant_'+num+'"><input type="hidden" name="quote_id[]" value="' + Quotes_quote_type + '" id="quote_'+num+'"><input type="hidden" name="no_of_loads[]" value="' + noofloads + '" id="no_loads_'+num+'"><input type="hidden" name="price[]" value="' + price + '"><input type="hidden" name="is_dispatch_flexible[]" value="' + is_dispatch_flexible + '" id="dispach_flexible'+num+'"><input type="hidden" name="is_delivery_flexible[]" value="' + is_delivery_flexible + '" id="delivery_flexible'+num+'"></div>';
                    $('.request_rows').append(html);
                    $('#from_location').val("");
                    $('#to_location').val("");
                    $('#from_location_id').val("");
                    $('#to_location_id').val("");
                    $('#delivery_date').val("");
                    $('#dispatch_date').val("");
                    $("#delivery_date").datepicker("destroy");
                    $("#dispatch_date").datepicker("destroy");
                    $('#quantity').val("");
                    $('#capacity').val("");
                    $('#load_type').val("");
                    $('#vehicle_type').val("");
                    $('#dimensions').val("");
                    $('#no_of_loads').val("");
                    $('#quote_id').val("");
                    $('#price').val("");
                    $("#is_dispatch_flexible").prop('checked', false);
                    $("#is_delivery_flexible").prop('checked', false);
                    $("#buyer_quotelineitems_form_validation")
                        .validate().cancelSubmit = true;
                    $('.selectpicker').selectpicker('refresh');
                    $("#dispatch_date")
                        .datepicker({
                            changeMonth: true,
                            numberOfMonths: 1,
                            minDate: 0,
                            show_flexible: 1,
                            flex_identifier: "is_dispatch_flexible",
                            flex_text: "Flexible dates",
                            dateFormat: "dd/mm/yy",
                            onClose: function(selectedDate) {
                                $("#delivery_date")
                                    .datepicker(
                                        "option",
                                        "minDate",
                                        selectedDate);
                            }
                        });
                    $("#delivery_date")
                        .datepicker({
                            changeMonth: true,
                            numberOfMonths: 1,
                            show_flexible: 1,
                            flex_identifier: "is_delivery_flexible",
                            flex_text: "Flexible dates",
                            minDate: 0,
                            dateFormat: "dd/mm/yy",
                            onClose: function(selectedDate) {
                                $("#dispatch_date")
                                    .datepicker(
                                        "option",
                                        "maxDate",
                                        selectedDate);
                            }
                        });
                    return false;
                	} else {
                        $("#erroralertmodal .modal-body").html("Post already exists for the specified parameters! Please modify parameters.");
                        $("#erroralertmodal").modal({
                            show: true
                        });
                        return false;
                    }
                }
                
            });
    $(document).on('click', '.remove_this', function() {
        var rowid = $(this).attr("row_id");
        $('.request_row_' + rowid).remove();
    });
   
    $(document).on('click', '.edit_this', function() {
    	
    	
    	
    	 var rowid = $(this).attr("row_id");
    	$("#update_line").val(1);
    	$("#update_row_count").val(rowid);
    	// alert($("#load_type_"+rowid).val());
    	$('#from_location').val($("#from_loc_"+rowid).html());
        $('#to_location').val($("#to_loc_"+rowid).html());
        $('#from_location_id').val($("#from_location_id_"+rowid).val());
        $('#to_location_id').val($("#to_location_id_"+rowid).val());
        if($('#reporting_date').length){
            $('#reporting_date').val($("#reporting_date"+rowid).val());
        }else{
            $('#delivery_date').val($("#delivery_date_"+rowid).val());
            $('#dispatch_date').val($("#dispatch_date_"+rowid).val());
        }    
        $('#quantity').val($("#quant_"+rowid).val());
        $('#capacity').val($("#unit_cap_"+rowid).val());
        $('#load_type').selectpicker('val', $("#load_type_"+rowid).val());
        
        var serviceid = $('#service_id_ftl').val();        
        //if(serviceid == 1){
            if($('#vehicle_type').length){
                $('#vehicle_type').selectpicker('val',$("#vehicle_type_"+rowid).val());
            }
       // } //else {
            //if($('#vehicle_types').length){
               // $('#vehicle_types').selectpicker('val',$("#vehicle_type_"+rowid).val());
            //}
        //}
        
        
        $('#no_of_loads').val($("#no_loads_"+rowid).val());
        $('#quote_id').selectpicker('val',$("#quote_"+rowid).val());
        $('#price').val($("#price_"+rowid).html());
       
        
       });
    
    $("#buyer_quote_form").validate({
    	ignore: [],
        rules: {            
            "agree": {
                required: true,
            },
            "quoteaccess_id": {
                required: true,
            },
            "seller_list" : {
                required : {
	            	depends: function(element) {
	            		if ($(".create-posttype-service-ftl:checked").val() == 2){           			
		            		return true;
		            	}else{
		            		return false;
		            	}

	            	}
                }
            }
        },
        errorPlacement: function(error, element) {        	
            $(element).parent().parent().append(error);
        },
        messages : {
        	"agree": {
                required: "Terms & Conditions is required",
            },
            
    },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });

    $("#changepasswordform")
            .validate(
                    {                   
                    ignore: "input[type='text']:hidden",
                    // Specify the validation rules
                        rules : {
                            'old_password' : "required",
                            //'password' : "required",
                            'password_confirmation' :"required",
                            'password' : {
                                required : true,
                                //accept : "/^(?=.*[A-Za-z])(?=.*\d)(?=.*[$@$!%*#?&])[A-Za-z\d$@$!%*#?&]{8,}$/",
                                minlength : 8
                            },
                        },
                        errorPlacement: function(error, element) {
                            $(element).parent().after(error);
                        },
                        // Specify the validation error messages
                        messages : {
                            'old_password' : "Please enter old password",
                            'password' : {
                                required:"Plese enter new password",
                                minlength:"Please enter 8 characters long alphanumeric password having atleast one special character",
                            },
                            'password_confirmation' : "Please enter confirm password",  
                        },
                        submitHandler : function(form) {
                            
                            form.submit();
                        }
                    });
  
   
    $("#buyer_quote_updateform").validate({
    	ignore: [],
        rules: {
            "seller_list": {
                required: true,
            },            
        }
    });
    
    
 // Psot private and Post public functionality in Ftl buyerquote creation   
    $('#ftlPostPublic').change(function() {
    	var id = $('.request_rows').children().size();
    	var postingId = $('#ftlPostPublic').val();
    	if (!id) {        		 
           // alert("Please add atleast one item to the list");  
            $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
                show: true
            });
            $('#"ftlPostPublic"').val('');
            $('.selectpicker').selectpicker('refresh');
            $("#hideseller").css("display", "none");
            return false;
    	}
    	if (postingId == 1) {
    		$("#hideseller").css("display", "none");
    	} else if (postingId == 2) {
   		 $("#hideseller").css("display", "block");
         $.ajax({
             url: '/getSellerslist',
             type: "post",
             data: {
                 'seller_list': seller_id_list,
                 '_token': $('input[name=_token]').val()
             },
             success: function(data) {
            	 if (data!=null & data!='') {
                     $(".token-input-list").remove();
                     $("#demo-input-local").tokenInput(data);
                 } else {
                     alert("No Sellers Available");
                     $('#"ftlPostPublic"').val('');
                     $('.selectpicker').selectpicker('refresh');
                     $("#hideseller").css("display", "none");
                     return false;
                 }
             },
             error: function(request, status, error) {
            	 $('#"ftlPostPublic"').val('');
                 $('.selectpicker').selectpicker('refresh');
                 $("#hideseller").css("display", "none");
                 alert(error);
             },
         });	 
	 }
    	
    
    });

    $('.create-posttype').click(function() {
        var id = $('.request_rows').children().size();
        var postingId = $(".create-posttype:checked").val();
        if (!id) { 
            $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
                show: true
            });           
            $('.selectpicker').selectpicker('refresh');
            $("#hideseller").css("display", "none");
            return false;
        }
        if (postingId == 1) {
            $("#hideseller").css("display", "none");
        } else if (postingId == 2) {
         $("#hideseller").css("display", "block");
         $.ajax({
             url: '/getSellerslist',
             type: "post",
             data: {
                 'seller_list': seller_id_list,
                 '_token': $('input[name=_token]').val()
             },
             success: function(data) {
                 if (data!=null & data!='') {
                     $(".token-input-list").remove();
                     $("#demo-input-local").tokenInput(data);
                 } else {
                     alert("No Sellers Available"); 
                     $('#"post-private"').val('');
                     $('.selectpicker').selectpicker('refresh');
                     $("#hideseller").css("display", "none");
                     return false;
                 }
             },
             error: function(request, status, error) {                
                 $('.selectpicker').selectpicker('refresh');
                 $("#hideseller").css("display", "none");
                 alert(error);
             },
         });     
     }
        
    
    });
    
    
    
    // Check checkbox select checkbox value
    function changeCheckboxValue($chekboxDetails) {
        if ($chekboxDetails.is(':checked')) {
            $chekboxDetails.val(1);
        } else {
            $chekboxDetails.val(0);
        }
    }
    $(".buyer_counter_offer_insurance").change(function() {
        changeCheckboxValue($(this));
    });
    
    //LTL and Other service add checkbox checking commercial and non-commercial        
    $('.is_commercial').on('change', function() {
       // alert($('input[name="is_commercial"]:checked', '#ptlBuyerQuotelineitemsForm').val()); 
       var setCheckvalue = $('input[name="is_commercial"]:checked', '#ptlBuyerQuotelineitemsForm').val();       
       $('.is_commercial_check_ptl').val(setCheckvalue);
    });

    
    
    //LTL and Other service add checkbox checking commercial and non-commercial        
    $('.is_commercial_modify').on('change', function() {
       // alert($('input[name="is_commercial"]:checked', '#ptlBuyerQuotelineitemsForm').val()); 
       var setCheckvalue = $('input[name="is_commercial"]:checked', '#ptlBuyerQuotelineitemsForm').val();       
       $('.is_commercial_check_ptl').val(setCheckvalue);
    });

    $("#booknow_buyer_form, #addptlbuyerpostcounteroffer, #ptl_buyer_results_form, #ftl-buyer-leads, #ltl-buyer-leads").on("click", ".buyer_search_insurance", function() {
        changeCheckboxValue($(this));
    });
//ltl quotes book now pickup date validations
    $("#addptlbuyerpostcounteroffer").on("change paste", ".buyer_counter_offer_consignment_pickup_date", function() {
        var itemId = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var consignmentPickupDate = $('#buyersearch_booknow_offer_consignment_pickup_date_' + itemId).val();
        if (consignmentPickupDate) {
            var formattedConsignmentPickupDate = convertDateFormatForDatePicker(consignmentPickupDate);
            //var sellerPostedFromDate = $('#ptl_buyer_counter_offer_seller_post_from_date_' + itemId).val();
            var sellerPostedFromDate = $('#fdispatch-date_' + itemId).val();
            
            var formattedSellerPostedFromDate = convertDateFormat(sellerPostedFromDate);
            //var sellerPostedToDate = $('#ptl_buyer_counter_offer_seller_post_to_date_' + itemId).val();
            var sellerPostedToDate = $('#ptl_buyer_counter_offer_seller_to_date_' + itemId).val();
            var deliveryDate = $('#ptl_buyer_counter_offer_seller_post_to_date_' + itemId).val();
            if(!sellerPostedToDate) {
                if(formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                    $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                    $(this).val("");
                    return false;
                }
            }
            var formattedSellerPostedToDate = convertDateFormat(sellerPostedToDate);
            if (formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
           // if (formattedConsignmentPickupDate < formattedSellerPostedFromDate || formattedConsignmentPickupDate > formattedSellerPostedToDate) {
            if (formattedConsignmentPickupDate > formattedSellerPostedToDate) {
                $("#erroralertmodal .modal-body").html("Seller post not valid for pickup date selected.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
        }
    });
//ftl quotes book now pickup date validations
    $("#addbuyerpostcounteroffer").on("change paste", ".buyer_counter_offer_consignment_pickup_date", function() {
        var itemId = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var consignmentPickupDate = $('#buyer_counter_offer_consignment_pickup_date_' + itemId).val();
               
        if (consignmentPickupDate) {
            var formattedConsignmentPickupDate = convertDateFormatForDatePicker(consignmentPickupDate);
            //var buyerDispatchDate = $('#buyer_counter_offer_seller_post_from_date_' + itemId).val();
            var buyerDispatchDate = $('#fdispatch-date_' + itemId).val();
            var formattedSellerPostedFromDate = convertDateFormat(buyerDispatchDate);
            var sellerPostedToDate = $('#buyer_counter_offer_seller_to_date_' + itemId).val();
            var deliveryDate = $('#buyer_counter_offer_seller_post_to_date_' + itemId).val();
            //alert(formattedConsignmentPickupDate);alert(formattedSellerPostedFromDate);
            if(!sellerPostedToDate) {
                if(formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                    $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                    $(this).val("");
                    return false;
                }
            }
            //alert($('#fdispatch-date_' + itemId).val());alert(convertDateFormat(buyerDispatchDate));
            var formattedSellerPostedToDate = convertDateFormat(sellerPostedToDate);
            if (formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
            if (formattedConsignmentPickupDate > formattedSellerPostedToDate) {
                $("#erroralertmodal .modal-body").html("Seller post not valid for pickup date selected.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
        }
    });
    //ftl leads book now pickup date validations
    $("#ftl-buyer-leads-booknow").on("change paste", ".buyer_counter_offer_consignment_pickup_date", function() {
        var itemId = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var consignmentPickupDate = $(this).val();
        if (consignmentPickupDate) {
            var formattedConsignmentPickupDate = convertDateFormatForDatePicker(consignmentPickupDate);
            //var buyerDispatchDate = $('#buyer_leads_counter_offer_seller_post_from_date_' + itemId).val();
            var buyerDispatchDate = $('#fdispatch-date_' + itemId).val();
            var formattedSellerPostedFromDate = convertDateFormat(buyerDispatchDate);
            var sellerPostedToDate = $('#buyer_leads_counter_offer_seller_to_date_' + itemId).val();
            //var deliveryDate = $('#buyer_leads_counter_offer_seller_post_to_date_' + itemId).val();
            if(!sellerPostedToDate) {
                if(formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                    $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                    $(this).val("");
                    return false;
                }
            }
            var formattedSellerPostedToDate = convertDateFormat(sellerPostedToDate);
            if (formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
            if (formattedConsignmentPickupDate > formattedSellerPostedToDate) {
                $("#erroralertmodal .modal-body").html("Seller post not valid for pickup date selected.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
        }
    });
    //ltl leads book now pickup date validations
    $("#ltl-buyer-leads-booknow").on("change paste", ".buyer_counter_offer_consignment_pickup_date", function() {
        var itemId = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var consignmentPickupDate = $(this).val();
        if (consignmentPickupDate) {
            var formattedConsignmentPickupDate = convertDateFormatForDatePicker(consignmentPickupDate);
            //var buyerDispatchDate = $('#ptl_buyer_leads_counter_offer_seller_post_from_date_' + itemId).val();
            var buyerDispatchDate = $('#fdispatch-date_' + itemId).val();
            var formattedSellerPostedFromDate = convertDateFormat(buyerDispatchDate);
            var sellerPostedToDate = $('#ptl_buyer_leads_counter_offer_seller_to_date_' + itemId).val();
            //var deliveryDate = $('#buyer_leads_counter_offer_seller_post_to_date_' + itemId).val();
            if(!sellerPostedToDate) {
                if(formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                    $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                    $(this).val("");
                    return false;
                }
            }
            var formattedSellerPostedToDate = convertDateFormat(sellerPostedToDate);
            if (formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
            if (formattedConsignmentPickupDate > formattedSellerPostedToDate) {
                $("#erroralertmodal .modal-body").html("Seller post not valid for pickup date selected.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
        }
    });
    //ftl search book now pickup date validations
    $("#ftl-buyer-search-booknow").on("change paste", ".buyer_counter_offer_consignment_pickup_date", function() {
        var itemId = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        //var consignmentPickupDate = $('#buyer_counter_offer_consignment_pickup_date_' + itemId).val();
        var consignmentPickupDate = $(this).val();       
        if (consignmentPickupDate) {
            var formattedConsignmentPickupDate = convertDateFormatForDatePicker(consignmentPickupDate);
            var sellerPostedFromDate = $('#buyersearch_booknow_from_date_' + itemId).val();
            //var buyerDispatchDate = $('#buyersearch_booknow_dispatch_date_' + itemId).val();
            var buyerDispatchDate = $('#fdispatch-date_' + itemId).val();
            var formattedSellerPostedFromDate = convertDateFormat(buyerDispatchDate);
            var sellerPostedToDate = $('#buyersearch_booknow_to_date_' + itemId).val();
            if(!sellerPostedToDate) {
                if(formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                    $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                    $(this).val("");
                    return false;
                }
            }
            var formattedSellerPostedToDate = convertDateFormat(sellerPostedToDate);
            if (formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
            if (formattedConsignmentPickupDate > formattedSellerPostedToDate) {
                $("#erroralertmodal .modal-body").html("Seller post not valid for pickup date selected.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
        }
    });
    //ltl search book now pickup date validations
    $("#ltl-buyer-search-booknow").on("change paste", ".buyer_counter_offer_consignment_pickup_date", function() {
        var itemId = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var consignmentPickupDate = $(this).val();
        if (consignmentPickupDate) {
            var formattedConsignmentPickupDate = convertDateFormatForDatePicker(consignmentPickupDate);
            //var buyerDispatchDate = $('#ptl_buyer_dispatchs_' + itemId).val();
            var buyerDispatchDate = $('#fdispatch-date_' + itemId).val();
            var formattedSellerPostedFromDate = convertDateFormat(buyerDispatchDate);
            var sellerPostedToDate = $('#ptl_buyer_search_seller_to_date_' + itemId).val();
            if(!sellerPostedToDate) {
                if(formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                    $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                    $(this).val("");
                    return false;
                }
            }
            var formattedSellerPostedToDate = convertDateFormat(sellerPostedToDate);
            if (formattedConsignmentPickupDate < formattedSellerPostedFromDate) {
                $("#erroralertmodal .modal-body").html("Consignment pickup date cannot be before dispatch date.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
            if (formattedConsignmentPickupDate > formattedSellerPostedToDate) {
                $("#erroralertmodal .modal-body").html("Seller post not valid for pickup date selected.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $(this).val("");
                return false;
            }
        }
    });

    function convertDateFormatForDatePicker(consignmentPickupDate) {
        if (consignmentPickupDate) {
            var arrayFormattedDate = consignmentPickupDate.split("/");
            ///*** mm/dd/yy format 
            var formattedDate = new Date(arrayFormattedDate["2"], parseInt(arrayFormattedDate["1"] - 1), arrayFormattedDate["0"]);
            return formattedDate;
        }
    }

    function convertDateFormat(dbFormattedDate) {
        if (dbFormattedDate) {
            var arrayFormattedDate = dbFormattedDate.split("-");
            var formattedDate = new Date(arrayFormattedDate["0"], parseInt(arrayFormattedDate["1"] - 1), arrayFormattedDate["2"]);
            return formattedDate;
        }
    }
    
    function convertDateFormatToDisplay(formattedDate) {
        if(formattedDate) {
            var newFormattedDate = formattedDate.getDate() + '/' + (formattedDate.getMonth() + 1) + '/' +  formattedDate.getFullYear();
            return newFormattedDate;
        }
    }

    $("#addbuyerpostcounteroffer .add_buyer_addtocart_details,#addbuyerpostcounteroffer .add_buyer_checkout_details").click(function() {
        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var isCheckout;
        if($(this).text() == 'Checkout') {
            isCheckout = '1';
        } else {
            isCheckout = '0';
        }
        var sourceLocationType = $('#buyer_counter_offer_source_location_type_' + rowNo).val();
        var destinationLocationType = $('#buyer_counter_offer_destination_location_type_' + rowNo).val();
        var packagingType = $('#buyer_counter_offer_packaging_type_' + rowNo).val();
        //other fields for ftl quotes
        var sourceLocationTypeOther = $('#buyer_counter_offer_source_location_type_text').val();
        var destinationLocationTypeOther = $('#buyer_counter_offer_destination_location_type_text').val();
        var packagingTypeOther = $('#buyer_counter_offer_packaging_type_text').val();
        
        var consignmentPickupDate = $('#buyer_counter_offer_consignment_pickup_date_' + rowNo).val();
        var consignmentNeedInsurance = $('#buyer_counter_offer_need_insurance_' + rowNo).val();
        var consignmentValue = $('#buyer_counter_offer_consignment_value_' + rowNo).val();
        var consignorName = $('#buyer_counter_offer_consignor_name_' + rowNo).val();
        var consignorNumber = $('#buyer_counter_offer_consignor_number_' + rowNo).val();
        var consignorEmail = $('#buyer_counter_offer_consignor_email_' + rowNo).val();
        var consignorAddress = $('#buyer_counter_offer_consignor_address_' + rowNo).val();
        var consignorPin = $('#buyer_counter_offer_consignor_pincode_' + rowNo).val();
        var consigneeName = $('#buyer_counter_offer_consignee_name_' + rowNo).val();
        var consigneeNumber = $('#buyer_counter_offer_consignee_number_' + rowNo).val();
        var consigneeEmail = $('#buyer_counter_offer_consignee_email_' + rowNo).val();
        var consigneePin = $('#buyer_counter_offer_consignee_pin_' + rowNo).val();
        var consigneeAddress = $('#buyer_counter_offer_consignee_address_' + rowNo).val();
        var buyerId = $('#buyer_post_buyer_id_' + rowNo).val();
        var sellerId = $('#buyer_post_seller_id_' + rowNo).val();
        var quoteItemId = $('#buyer_quote_item_id_' + rowNo).val();
        var postItemId = $('#seller_post_item_id_' + rowNo).val();
        var price = $('#buyer_post_price_' + rowNo).data('price');
        var additionalDetails = $('#buyer_counter_offer_additional_details_' + rowNo).val();

        var sellerPostedFromDate = $('#buyer_counter_offer_seller_post_from_date_' + rowNo).val();
        var sellerPostedToDate = $('#buyer_counter_offer_seller_post_to_date_' + rowNo).val();
        
        var termContractDispatchDate='';
       if($('#service_id').val()==19){
            checkAndSetGMBooknow(sourceLocationTypeOther,sourceLocationType, buyerId,
            sellerId,   consignorName, consignorNumber,
            consignorEmail, consignorAddress, consignorPin,  additionalDetails,
             rowNo,quoteItemId, postItemId, price, isCheckout,null,sellerPostedFromDate,sellerPostedToDate, null,termContractDispatchDate);
        }else{
        checkAndSetBooknow(sourceLocationTypeOther,destinationLocationTypeOther,packagingTypeOther,sourceLocationType, destinationLocationType,
            packagingType, buyerId, sellerId, consignmentPickupDate, consignmentValue,
            consignorName, consignorNumber, consignorEmail, consignorAddress,
            consignorPin, consigneeName, additionalDetails,
            consigneeNumber, consigneeEmail, consigneePin, consigneeAddress,
            rowNo, consignmentNeedInsurance, quoteItemId, postItemId, price,isCheckout, null,
            sellerPostedFromDate, sellerPostedToDate, null);
        }
        
    });
    $("#ftl-buyer-leads-booknow").on("click", ".booknow_buyer,.add_buyer_checkout_details", function() {
        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var isCheckout;
        if($(this).text() == 'Checkout') {
            isCheckout = '1';
        } else {
            isCheckout = '0';
        }
        var sourceLocationType = $('#buyersearch_booknow_offer_source_location_type_' + rowNo).val();
        var destinationLocationType = $('#buyersearch_booknow_offer_destination_location_type_' + rowNo).val();
        var packagingType = $('#buyersearch_booknow_offer_packaging_type_' + rowNo).val();
        //other fields for ftl leads
        var sourceLocationTypeOther = $('#buyersearch_booknow_offer_source_location_type_text').val();
        var destinationLocationTypeOther = $('#buyersearch_booknow_offer_destination_location_type_text').val();
        var packagingTypeOther = $('#buyersearch_booknow_offer_packaging_type_text').val();
        
        var consignmentPickupDate = $('#buyersearch_booknow_offer_consignment_pickup_date_' + rowNo).val();
        var consignmentNeedInsurance = $('#buyersearch_booknow_offer_need_insurance_' + rowNo).val();
        var consignmentValue = $('#buyersearch_booknow_offer_consignment_value_' + rowNo).val();
        var consignorName = $('#buyersearch_booknow_offer_consignor_name_' + rowNo).val();
        var consignorNumber = $('#buyersearch_booknow_offer_consignor_number_' + rowNo).val();
        var consignorEmail = $('#buyersearch_booknow_offer_consignor_email_' + rowNo).val();
        var consignorAddress = $('#buyersearch_booknow_offer_consignor_address_' + rowNo).val();
        var consignorPin = $('#buyersearch_booknow_offer_consignor_pincode_' + rowNo).val();
        var consigneeName = $('#buyersearch_booknow_offer_consignee_name_' + rowNo).val();
        var consigneeNumber = $('#buyersearch_booknow_offer_consignee_number_' + rowNo).val();
        var consigneeEmail = $('#buyersearch_booknow_offer_consignee_email_' + rowNo).val();
        var consigneePin = $('#buyersearch_booknow_offer_consignee_pin_' + rowNo).val();
        var consigneeAddress = $('#buyersearch_booknow_offer_consignee_address_' + rowNo).val();
        var additionalDetails = $('#buyersearch_booknow_offer_additional_details_' + rowNo).val();
        var sellerId = $('#buyer_leads_post_seller_id_' + rowNo).val();
        var quoteItemId = $('#cancel_buyer_counter_offer_enquiry').data('id');
        var postItemId = $('#leads_seller_post_item_id_' + rowNo).val();

        var price = $('#buyer_leads_price' + rowNo).val();
        var buyerId = $('#buyer_leads_post_buyer_id_' + rowNo).val();

        var sellerPostedFromDate = $('#buyer_counter_offer_seller_post_from_date_' + rowNo).val();
        var sellerPostedToDate = $('#buyer_counter_offer_seller_post_to_date_' + rowNo).val();
        checkAndSetBooknow(sourceLocationTypeOther,destinationLocationTypeOther,packagingTypeOther,sourceLocationType, destinationLocationType,
            packagingType, buyerId, sellerId, consignmentPickupDate, consignmentValue,
            consignorName, consignorNumber, consignorEmail, consignorAddress,
            consignorPin, consigneeName, additionalDetails,
            consigneeNumber, consigneeEmail, consigneePin, consigneeAddress,
            rowNo, consignmentNeedInsurance, quoteItemId, postItemId, price,isCheckout, null,
            sellerPostedFromDate, sellerPostedToDate, null);
        
    });

    $("#ltl-buyer-leads-booknow").on("click", ".booknow_buyer,.add_buyer_checkout_details", function() {
        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var isCheckout;
        if($(this).text() == 'Checkout') {
            isCheckout = '1';
        } else {
            isCheckout = '0';
        }

        var serviceid = $('#service_id').val();
        var consignmentNeedFragile = null;
        if(serviceid == 7 || serviceid == 8){
            consignmentNeedFragile = $('#buyersearch_booknow_offer_is_fragile_' + rowNo).val();
        }

        var sourceLocationType = null;
        var destinationLocationType = null;
        var packagingType = null;
        var sourceLocationTypeOther = null;
        var destinationLocationTypeOther = null;
        var packagingTypeOther = null;
        var consignmentPickupDate = $('#buyersearch_booknow_offer_consignment_pickup_date_' + rowNo).val();
        var consignmentNeedInsurance = $('#buyersearch_booknow_offer_need_insurance_' + rowNo).val();
        var consignmentValue = $('#buyersearch_booknow_offer_consignment_value_' + rowNo).val();
        var consignorName = $('#buyersearch_booknow_offer_consignor_name_' + rowNo).val();
        var consignorNumber = $('#buyersearch_booknow_offer_consignor_number_' + rowNo).val();
        var consignorEmail = $('#buyersearch_booknow_offer_consignor_email_' + rowNo).val();
        var consignorAddress = $('#buyersearch_booknow_offer_consignor_address_' + rowNo).val();
        var consignorPin = $('#buyersearch_booknow_offer_consignor_pincode_' + rowNo).val();
        var consigneeName = $('#buyersearch_booknow_offer_consignee_name_' + rowNo).val();
        var consigneeNumber = $('#buyersearch_booknow_offer_consignee_number_' + rowNo).val();
        var consigneeEmail = $('#buyersearch_booknow_offer_consignee_email_' + rowNo).val();
        var consigneePin = $('#buyersearch_booknow_offer_consignee_pin_' + rowNo).val();
        var consigneeAddress = $('#buyersearch_booknow_offer_consignee_address_' + rowNo).val();
        var additionalDetails = $('#buyersearch_booknow_offer_additional_details_' + rowNo).val();

        var buyerId = $('#ptl_buyer_leads_post_buyer_id_' + rowNo).val();
        var sellerId = $('#ptl_buyer_leads_post_seller_id_' + rowNo).val();
        var price = $('#ptl_buyer_leads_post_price_' + rowNo).data('price');
        var quoteId = $('#ptl_cancel_buyer_counter_offer_enquiry').data('id');
        var quoteItemId = null;
        var postItemId = $('#ptl_buyer_leads_seller_post_item_id_' + rowNo).val();

        var sellerPostedFromDate = $('#ptl_buyer_leads_counter_offer_seller_post_from_date_' + rowNo).val();
        var sellerPostedToDate = $('#ptl_buyer_leads_counter_offer_seller_post_to_date_' + rowNo).val();
        
       
        checkAndSetBooknow(sourceLocationTypeOther,destinationLocationTypeOther,packagingTypeOther,sourceLocationType, destinationLocationType, packagingType, buyerId,
            sellerId, consignmentPickupDate, consignmentValue, consignorName, consignorNumber,
            consignorEmail, consignorAddress, consignorPin, consigneeName, additionalDetails,
            consigneeNumber, consigneeEmail, consigneePin, consigneeAddress, rowNo,
            consignmentNeedInsurance, quoteItemId, postItemId, price, isCheckout, quoteId, 
            sellerPostedFromDate, sellerPostedToDate, consignmentNeedFragile);
    });

    function checkAndSetBooknow(sourceLocationTypeOther,destinationLocationTypeOther,packagingTypeOther,sourceLocationType, destinationLocationType, packagingType,
        buyerId, sellerId, consignmentPickupDate, consignmentValue, consignorName,
        consignorNumber, consignorEmail, consignorAddress, consignorPin, consigneeName,
        additionalDetails, consigneeNumber, consigneeEmail, consigneePin, consigneeAddress,
        rowNo, consignmentNeedInsurance, quoteItemId, postItemId, price, isCheckout, quoteId,
        sellerPostedFromDate, sellerPostedToDate, consignmentNeedFragile, contractId, enquiryType, contractFromDate, contractToDate) {
        if (validateBooknowFields(sourceLocationType, destinationLocationType,
          packagingType, consignmentPickupDate, consignmentValue,
            consignorName, consignorNumber, consignorEmail, consignorAddress,
            consignorPin, consigneeName, consigneeNumber, consigneeEmail,
            consigneePin, consigneeAddress, rowNo)) {
        	
        	
        	quoteId = (!quoteId) ? '' : quoteId;
            sellerPostedFromDate = (!sellerPostedFromDate) ? '' : sellerPostedFromDate;
            sellerPostedToDate = (!sellerPostedToDate) ? '' : sellerPostedToDate;
            consignmentNeedFragile = (!consignmentNeedFragile) ? '' : consignmentNeedFragile;
            contractId = (!contractId) ? '' : contractId;
            enquiryType = (!enquiryType) ? '' : enquiryType;
            contractFromDate = (!contractFromDate) ? '' : contractFromDate;
            contractToDate = (!contractToDate) ? '' : contractToDate;
            var ajaxUrl = (contractId) ? "/settermbuyerbooknow" : "/setbuyerbooknow";
            	
            allData = {
                'sourceLocationType': sourceLocationType,
                'destinationLocationType': destinationLocationType,
                'packagingType': packagingType,
                'sourceLocationTypeOther': sourceLocationTypeOther,
                'destinationLocationTypeOther': destinationLocationTypeOther,
                'packagingTypeOther': packagingTypeOther,
                'buyerId': buyerId,
                'sellerId': sellerId,
                'consignmentPickupDate': consignmentPickupDate,
                'consignmentValue': consignmentValue,
                'consignorName': consignorName,
                'consignorNumber': consignorNumber,
                'consignorEmail': consignorEmail,
                'consignorAddress': consignorAddress,
                'consignorPin': consignorPin,
                'consigneeName': consigneeName,
                'additionalDetails': additionalDetails,
                'consigneeNumber': consigneeNumber,
                'consigneeEmail': consigneeEmail,
                'consigneePin': consigneePin,
                'consigneeAddress': consigneeAddress,
                'buyerCounterOfferId': rowNo,
                'consignmentNeedInsurance': consignmentNeedInsurance,
                'consignmentNeedFragile': consignmentNeedFragile,
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
             }else{
             $("#source_location").html($('#buyersearch_booknow_offer_source_location_type_' + rowNo +' option:selected').text());
             }
             
             if($('#buyersearch_booknow_offer_destination_location_type_' + rowNo +' option:selected').text()=='Others – Specify'){
             $("#destination_location").html($('#buyersearch_booknow_offer_destination_location_type_text').val());
             }else{
             $("#destination_location").html($('#buyersearch_booknow_offer_destination_location_type_' + rowNo +' option:selected').text());
             }
             
            } else {
            	
            	if($('#buyer_counter_offer_source_location_type_' + rowNo +' option:selected').text()=='Others – Specify'){
            	$("#source_location").html($('#buyer_counter_offer_source_location_type_text').val());
            	}else{
            	$("#source_location").html($('#buyer_counter_offer_source_location_type_' + rowNo +' option:selected').text());
            	}		

            	if($('#buyer_counter_offer_destination_location_type_' + rowNo +' option:selected').text()=='Others – Specify'){
            	$("#destination_location").html($('#buyer_counter_offer_destination_location_type_text').val());
            	}else{
            	$("#destination_location").html($('#buyer_counter_offer_destination_location_type_' + rowNo +' option:selected').text());
            	}	
                                
            }        	
        	
        	$("#consignor").html(consignorName);
        	$("#consignor_mobile").html(consignorNumber);
        	$("#consignor_adddress").html(consignorAddress);
        	$("#consignee_name").html(consigneeName);
        	$("#consignee_mobile").html(consigneeNumber);
        	$("#consignee_address").html(consigneeAddress);
        	$("#buyer_user").html($("#buyer_name").val());
        	if(consignmentPickupDate){
        	$("#pickup_con_date").html(consignmentPickupDate);
        	}
                
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
    
    $("#acceptterms").on("click", function() {
    	
    	var allData=JSON.parse( $("#alldata").val() );
    	//console.log(allData);
    	var ajaxUrl=$("#ajaxurl").val();
    	var isCheckout=$("#ischeckout").val();
    	var service_id=$("#service_id").val();
    	var postType="";
    	console.log(ajaxUrl);
    	if(service_id==2 || service_id==6 || service_id==7 || service_id==8 || service_id==9){
    	postType="GET";	
    	}else{
    	postType="POST";		
    	}
    	$.ajax({
            type: postType,
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
    	
    	
    });
    
    function checkAndSetBooknowLtl(sourceLocationTypeOther,destinationLocationTypeOther,packagingTypeOther,sourceLocationType, destinationLocationType, packagingType,
        buyerId, sellerId, consignmentPickupDate, consignmentValue, consignorName,
        consignorNumber, consignorEmail, consignorAddress, consignorPin, consigneeName,
        additionalDetails, consigneeNumber, consigneeEmail, consigneePin, consigneeAddress,
        rowNo, consignmentNeedInsurance, quoteItemId, postItemId, price, isCheckout, quoteId,
        sellerPostedFromDate, sellerPostedToDate, consignmentNeedFragile, contractId, enquiryType, contractFromDate, contractToDate) {
        if (validateBooknowFields(sourceLocationType, destinationLocationType,
                packagingType, consignmentPickupDate, consignmentValue,
                consignorName, consignorNumber, consignorEmail, consignorAddress,
                consignorPin, consigneeName, consigneeNumber, consigneeEmail,
                consigneePin, consigneeAddress, rowNo)) {
            quoteId = (!quoteId) ? '' : quoteId;
            sellerPostedFromDate = (!sellerPostedFromDate) ? '' : sellerPostedFromDate;
            sellerPostedToDate = (!sellerPostedToDate) ? '' : sellerPostedToDate;
            consignmentNeedFragile = (!consignmentNeedFragile) ? '' : consignmentNeedFragile;
            contractId = (!contractId) ? '' : contractId;
            enquiryType = (!enquiryType) ? '' : enquiryType;
            contractFromDate = (!contractFromDate) ? '' : contractFromDate;
            contractToDate = (!contractToDate) ? '' : contractToDate;
            var ajaxUrl = (contractId) ? "/settermbuyerbooknow" : "/setbuyerbooknow";
                
            allData = {
                'sourceLocationType': sourceLocationType,
                'destinationLocationType': destinationLocationType,
                'packagingType': packagingType,
                'sourceLocationTypeOther': sourceLocationTypeOther,
                'destinationLocationTypeOther': destinationLocationTypeOther,
                'packagingTypeOther': packagingTypeOther,
                'buyerId': buyerId,
                'sellerId': sellerId,
                'consignmentPickupDate': consignmentPickupDate,
                'consignmentValue': consignmentValue,
                'consignorName': consignorName,
                'consignorNumber': consignorNumber,
                'consignorEmail': consignorEmail,
                'consignorAddress': consignorAddress,
                'consignorPin': consignorPin,
                'consigneeName': consigneeName,
                'additionalDetails': additionalDetails,
                'consigneeNumber': consigneeNumber,
                'consigneeEmail': consigneeEmail,
                'consigneePin': consigneePin,
                'consigneeAddress': consigneeAddress,
                'buyerCounterOfferId': rowNo,
                'consignmentNeedInsurance': consignmentNeedInsurance,
                'consignmentNeedFragile': consignmentNeedFragile,
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
            };
            
            var commercial=$("#commerical_type").val();
        	
        	if(commercial==1){
           $('#booknow-popup').modal({
                show: 'false'
              });
            
            $("#alldata").val(JSON.stringify( allData ));
            $("#ajaxurl").val(ajaxUrl);
            $("#ischeckout").val(isCheckout);
            if($('#buyer_counter_offer_source_location_type_' + rowNo +' option:selected').text()){
            $("#source_location").html($('#buyer_counter_offer_source_location_type_' + rowNo +' option:selected').text());
            $("#destination_location").html($('#buyer_counter_offer_destination_location_type_' + rowNo +' option:selected').text());
            }
            $("#consignor").html(consignorName);
            $("#consignor_mobile").html(consignorNumber);
            $("#consignor_adddress").html(consignorAddress);
            $("#consignee_name").html(consigneeName);
            $("#consignee_mobile").html(consigneeNumber);
            $("#consignee_address").html(consigneeAddress);
            $("#buyer_user").html($("#buyer_name").val());
            if(consignmentPickupDate){
            	$("#pickup_con_date").html(consignmentPickupDate);
            	}
            
            return false;
        	}
        	else{
            $.ajax({
                type: "GET",
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

    function checknSetIntraBooknow(buyerId, sellerId, consignmentPickupDate, consignmentPickupTime,  rowNo, quoteItemId, postItemId, price) {

            allData = {
                'buyerId': buyerId,
                'sellerId': sellerId,
                'consignmentPickupDate': consignmentPickupDate,
                'consignmentPickupTime': consignmentPickupTime,
                'buyerCounterOfferId': rowNo,
                'quoteItemId': quoteItemId,
                'postItemId': postItemId,
                'price': price
            };
            $.ajax({
                type: "POST",
                url: "/setbuyerbooknow",
                data: allData,
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
                success: function(data) {
                    
                    if (data.success) {
                        alert(data.message);
                        //$(".intrabuyerbooknow_details").click();
                        $(".search-dialog").dialog("close");
                    }
                }
            }, "json");
        
    }


    function validateBooknowFields(sourceLocationType, destinationLocationType,
        packagingType, consignmentPickupDate, consignmentValue,
        consignorName, consignorNumber, consignorEmail, consignorAddress,
        consignorPin, consigneeName, consigneeNumber, consigneeEmail,
        consigneePin, consigneeAddress, rowNo) {
        var sourceLocationTypeErrorMessage, destinationLocationTypeErrorMessage, packagingTypeErrorMessage,
            consignmentPickupDateErrorMessage, consignmentValueErrorMessage, consignorNameErrorMessage, consignorNumberErrorMessage,
            consignorEmailErrorMessage, consignorAddressErrorMessage, consignorPinErrorMessage, consigneeNameErrorMessage,
            consigneeEmailErrorMessage, consigneeNumberErrorMessage, consigneeAddressErrorMessage, consigneePinErrorMessage;
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
        //destination other text
        if (destinationLocationType == '11') {
            if ($('#buyer_counter_offer_destination_location_type_text').val() == '' || $('#buyersearch_booknow_offer_destination_location_type_text').val() == '') {
                destinationLocationTypeErrorMessage = "Please enter other Destination Location Type!";
                isValidErrorNumber++;
            }else {
                destinationLocationTypeErrorMessage = '';
            }
        } 
        $('#buyer_counter_offer_destination_location_type_text_error_' + rowNo).html(destinationLocationTypeErrorMessage);
        $('#buyersearch_booknow_offer_destination_location_type_text_error_' + rowNo).html(destinationLocationTypeErrorMessage);
        if (destinationLocationType == '0') {
            destinationLocationTypeErrorMessage = "Please select Destination Location Type!";
            isValidErrorNumber++;
        } else {
            destinationLocationTypeErrorMessage = '';
        }
        $('#buyer_counter_offer_destination_location_type_error_' + rowNo).html(destinationLocationTypeErrorMessage);
        //packaging other text
        if (packagingType == '13') {
            if ($('#buyer_counter_offer_packaging_type_text').val() == '' || $('#buyersearch_booknow_offer_packaging_type_text').val() == '') {
                    packagingTypeErrorMessage = "Please enter other Packaging Type!";
                isValidErrorNumber++;
            }else {
                packagingTypeErrorMessage = '';
            }
        } 
        $('#buyer_counter_offer_packaging_type_text_error_' + rowNo).html(packagingTypeErrorMessage);
        $('#buyersearch_booknow_offer_packaging_type_text_error_' + rowNo).html(packagingTypeErrorMessage);
        
        if (packagingType == '0') {
            packagingTypeErrorMessage = "Please select Packaging Type!";
            isValidErrorNumber++;
        } else {
            packagingTypeErrorMessage = '';
        }
        $('#buyer_counter_offer_packaging_type_error_' + rowNo).html(packagingTypeErrorMessage);
        if (!consignmentPickupDate) {
            consignmentPickupDateErrorMessage = "Please enter consignment pickup date!";
            isValidErrorNumber++;
        } else {
            consignmentPickupDateErrorMessage = '';
        }
        $('#buyer_counter_offer_consignment_pickup_date_error_' + rowNo).html(consignmentPickupDateErrorMessage);
        if (!consignmentValue) {
            consignmentValueErrorMessage = "Please enter consignment value!";
            isValidErrorNumber++;
        } else if (consignmentValue && !isNumber(consignmentValue)) {
            consignmentValueErrorMessage = "Please enter proper consignment value!";
            isValidErrorNumber++;
        } else {
            consignmentValueErrorMessage = '';
        }
        $('#buyer_counter_offer_consignment_value_error_' + rowNo).html(consignmentValueErrorMessage);
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
        if (!consigneeName ) {
            consigneeNameErrorMessage = "Please enter consignee name!";
            isValidErrorNumber++;
        } else if (consigneeName && !validateName(consigneeName)) {
            consigneeNameErrorMessage = "Please enter proper consignee name with 50 characters long!";
            isValidErrorNumber++;
        } else {
            consigneeNameErrorMessage = '';
        }
        $('#buyer_counter_offer_consignee_name_error_' + rowNo).html(consigneeNameErrorMessage);
        if (consigneeEmail && !validateEmail(consigneeEmail)) {
            consigneeEmailErrorMessage = "Please enter proper consignee email!";
            isValidErrorNumber++;
        } else {
            consigneeEmailErrorMessage = '';
        }
        $('#buyer_counter_offer_consignee_email_error_' + rowNo).html(consigneeEmailErrorMessage);
        if (!consigneeNumber) {
            consigneeNumberErrorMessage = "Please enter consignee mobile!";
            isValidErrorNumber++;
        } else if (consigneeNumber && !validatePhone(consigneeNumber)) {
            consigneeNumberErrorMessage = "Please enter mobile number 10 characters long!";
            isValidErrorNumber++;
        } else {
            consigneeNumberErrorMessage = '';
        }
        $('#buyer_counter_offer_consignee_number_error_' + rowNo).html(consigneeNumberErrorMessage);
        if (!consigneeAddress) {
            consigneeAddressErrorMessage = "Please enter consignee address!";
            isValidErrorNumber++;
        } else {
            consigneeAddressErrorMessage = '';
        }
        $('#buyer_counter_offer_consignee_address_error_' + rowNo).html(consigneeAddressErrorMessage);
        if (!consigneePin) {
            consigneePinErrorMessage = "Please enter consignee pin code!";
            isValidErrorNumber++;
        } /*else if (consigneePin && !validateIndianZipCode(consigneePin)) {
            consigneePinErrorMessage = "Please enter pincode 6 characters long!";
            isValidErrorNumber++;
        }*/ else {
            consigneePinErrorMessage = '';
        }
        if ($("#ptlTocheckLocationId").val()=='') {
            consigneePinErrorMessage = "Please enter consignee pin code!";
            isValidErrorNumber++;
        }
        $('#buyer_counter_offer_consignee_pin_error_' + rowNo).html(consigneePinErrorMessage);
        if (isValidErrorNumber > 0) {
            return false;
        } else {
            return true;
        }
    }
    
    function isNumber(value) {
        var decimalNumber = /^\-?([0-9]+(\.[0-9]+)?|Infinity)$/;
        return decimalNumber.test(value);
    }

    function validateEmail($email) {
       // var emailReg = /^[-a-z0-9~!$%^&*_=+}{\'?]+(\.[-a-z0-9~!$%^&*_=+}{\'?]+)*@([a-z0-9_][-a-z0-9_]*(\.[-a-z0-9_]+)*\.(aero|arpa|biz|com|coop|edu|gov|info|int|mil|museum|name|net|org|pro|travel|mobi|[a-z][a-z])|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(:[0-9]{1,5})?$/;
    	 var emailReg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,3}))$/;
        return emailReg.test($email);
        
    }

    function validatePhone(phoneText) {
        //var filter = /^[0-9-+]+$/;
        var filter = /(^[0-9]{10,12}$)/
        return filter.test(phoneText);
    }

    function validateIndianZipCode(MyZipCode) {
        //var checkZipCode = /(^\d{6}$)/;
        var checkZipCode = /(^[0-9]{5,6}$)/;
        return checkZipCode.test(MyZipCode);
    }
    
    function validateName($name) {
        var nameReg = /^[a-zA-Z ]{2,50}$/;
        return nameReg.test($name);
    }

    /******************Below script for book now in buyersearch results*****************/

    $("#ltl-buyer-search-booknow").on("click", ".booknow_buyer,.add_buyer_checkout_details", function() { 
        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var isCheckout;
        if($(this).text() == 'Checkout') {
            isCheckout = '1';
        } else {
            isCheckout = '0';
        }
        var sourceLocationType = null;
        var destinationLocationType = null;
        var packagingType = null;
        //other fileds in ltl
        var sourceLocationTypeOther = null;
        var destinationLocationTypeOther = null;
        var packagingTypeOther = null;
        
        var consignmentPickupDate = $('#buyersearch_booknow_offer_consignment_pickup_date_' + rowNo).val();
        var consignmentNeedInsurance = $('#buyersearch_booknow_offer_need_insurance_' + rowNo).val();

        var serviceid = $('#service_id').val();
        if(serviceid == 7 || serviceid == 8){
        var consignmentNeedFragile = $('#buyersearch_booknow_offer_is_fragile_' + rowNo).val();
        }else{
        var consignmentNeedFragile = '';   
        }

        var consignmentValue = $('#buyersearch_booknow_offer_consignment_value_' + rowNo).val();
        var consignorName = $('#buyersearch_booknow_offer_consignor_name_' + rowNo).val();
        var consignorNumber = $('#buyersearch_booknow_offer_consignor_number_' + rowNo).val();
        var consignorEmail = $('#buyersearch_booknow_offer_consignor_email_' + rowNo).val();
        var consignorAddress = $('#buyersearch_booknow_offer_consignor_address_' + rowNo).val();
        var consignorPin = $('#buyersearch_booknow_offer_consignor_pincode_' + rowNo).val();
        var consigneeName = $('#buyersearch_booknow_offer_consignee_name_' + rowNo).val();
        var consigneeNumber = $('#buyersearch_booknow_offer_consignee_number_' + rowNo).val();
        var consigneeEmail = $('#buyersearch_booknow_offer_consignee_email_' + rowNo).val();
        var consigneePin = $('#buyersearch_booknow_offer_consignee_pin_' + rowNo).val();
        var consigneeAddress = $('#buyersearch_booknow_offer_consignee_address_' + rowNo).val();
        var additionalDetails = $('#buyersearch_booknow_offer_additional_details_' + rowNo).val();
        var buyerId = $('#ptl_buyer_post_buyer_id_' + rowNo).val();
        var sellerId = $('#ptl_buyer_post_seller_id_' + rowNo).val();
        var price = $('#ptl_buyer_search_booknow_seller_price_' + rowNo).val();
        var quoteItemId = '';
        var postItemId = rowNo;
        //post values to create
        var from = $('#ptl_buyer_from_id_' + rowNo).val();
        var to = $('#ptl_buyer_to_id_' + rowNo).val();
        var dispatch = $('#ptl_buyer_dispatch_' + rowNo).val();
        var delivery = $('#ptl_buyer_delivery_' + rowNo).val();
        var load = $('#ptl_buyer_load_id_' + rowNo).val();
        var pack = $('#ptl_buyer_pack_id_' + rowNo).val();
        var volume = $('#ptl_buyer_volume_' + rowNo).val();
        var fdispatch = $('#ptl_buyer_fdispatch_' + rowNo).val();
        var door_pick = $('#ptl_buyer_doorpick_' + rowNo).val();
        var fdelivery = $('#ptl_buyer_fdelivery_' + rowNo).val();
        var door_del = $('#ptl_buyer_doordelivery_' + rowNo).val();
        var weight_type = $('#ptl_buyer_weight_type_' + rowNo).val();
        var no_pack = $('#ptl_buyer_no_pack_' + rowNo).val();
        var unit_weight = $('#ptl_buyer_unit_weight_' + rowNo).val();
        var vol_type = $('#ptl_buyer_vol_type_' + rowNo).val();
        var length = $('#ptl_buyer_length_' + rowNo).val();
        var width = $('#ptl_buyer_width_' + rowNo).val();
        var height = $('#ptl_buyer_height_' + rowNo).val();
        var dispatch_selected = $('#ptl_buyer_dispatchs_' + rowNo).val();
        var delivery_selected = $('#ptl_buyer_deliverys_' + rowNo).val();
        
        var shipment_type = $('#ptl_buyer_shipment_type_' + rowNo).val();
        var iecode = $('#ptl_buyer_iecode_' + rowNo).val();
        var sender_identity = $('#ptl_buyer_sender_identity_' + rowNo).val();
        var product_made = $('#ptl_buyer_product_made_' + rowNo).val();
        
        //post creation for book now
        allData = {'from': from,'to': to,'dispatch': dispatch,
        'delivery': delivery,'load': load,'pack': pack,
        'volume': volume,'fdispatch': fdispatch,'door_pick': door_pick,
        'fdelivery': fdelivery,'door_del': door_del,'weight_type': weight_type,
        'no_pack': no_pack,'unit_weight': unit_weight,'vol_type': vol_type,
        'length': length,'width': width,'height': height,'postItemId': postItemId,
        'shipment_type': shipment_type,'iecode': iecode,'sender_identity': sender_identity,
        'product_made': product_made};
        $.ajax({
                type: "GET",
                url: "/setbuyerpost",
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
                    if (data!=0&& data!='') {
                        var quoteId=data;
                        checkAndSetBooknowLtl(sourceLocationTypeOther,destinationLocationTypeOther,packagingTypeOther,sourceLocationType, destinationLocationType, packagingType, buyerId,
            sellerId, consignmentPickupDate, consignmentValue, consignorName, consignorNumber,
            consignorEmail, consignorAddress, consignorPin, consigneeName, additionalDetails,
            consigneeNumber, consigneeEmail, consigneePin, consigneeAddress, rowNo,
            consignmentNeedInsurance, null, postItemId, price, isCheckout, quoteId,
            dispatch_selected, delivery_selected, consignmentNeedFragile);
                    } 
                }
            }, "json");
        
    });
    /////For search book now in ftl
    $("#ftl-buyer-search-booknow").on("click", ".booknow_buyer,.add_buyer_checkout_details", function() {
    	
    	// return false;
        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var isCheckout;
        if($(this).text() == 'Checkout') {
            isCheckout = '1';
        } else {
            isCheckout = '0';
        }
        var sourceLocationType = $('#buyersearch_booknow_offer_source_location_type_' + rowNo).val();
        var destinationLocationType = $('#buyersearch_booknow_offer_destination_location_type_' + rowNo).val();
        var packagingType = $('#buyersearch_booknow_offer_packaging_type_' + rowNo).val();
        //other fields for ftl search
        var sourceLocationTypeOther = $('#buyersearch_booknow_offer_source_location_type_text').val();
        var destinationLocationTypeOther = $('#buyersearch_booknow_offer_destination_location_type_text').val();
        var packagingTypeOther = $('#buyersearch_booknow_offer_packaging_type_text').val();
        
        var consignmentPickupDate = $('#buyersearch_booknow_offer_consignment_pickup_date_' + rowNo).val();
        var consignmentNeedInsurance = $('#buyersearch_booknow_offer_need_insurance_' + rowNo).val();
        var consignmentValue = $('#buyersearch_booknow_offer_consignment_value_' + rowNo).val();
        var consignorName = $('#buyersearch_booknow_offer_consignor_name_' + rowNo).val();
        var consignorNumber = $('#buyersearch_booknow_offer_consignor_number_' + rowNo).val();
        var consignorEmail = $('#buyersearch_booknow_offer_consignor_email_' + rowNo).val();
        var consignorAddress = $('#buyersearch_booknow_offer_consignor_address_' + rowNo).val();
        var consignorPin = $('#buyersearch_booknow_offer_consignor_pincode_' + rowNo).val();
        var consigneeName = $('#buyersearch_booknow_offer_consignee_name_' + rowNo).val();
        var consigneeNumber = $('#buyersearch_booknow_offer_consignee_number_' + rowNo).val();
        var consigneeEmail = $('#buyersearch_booknow_offer_consignee_email_' + rowNo).val();
        var consigneePin = $('#buyersearch_booknow_offer_consignee_pin_' + rowNo).val();
        var consigneeAddress = $('#buyersearch_booknow_offer_consignee_address_' + rowNo).val();
        var additionalDetails = $('#buyersearch_booknow_offer_additional_details_' + rowNo).val();
        var buyerId = $('#buyersearch_booknow_buyer_id_' + rowNo).val();
        var sellerId = $('#buyersearch_booknow_seller_id_' + rowNo).val();
        var price = $('#buyersearch_booknow_seller_price_' + rowNo).val();
        var quoteItemId = '';
        var postItemId = rowNo;
        var sellerPostedFromDate = $('#buyersearch_booknow_dispatch_date_' + rowNo).val();
        var sellerPostedToDate = $('#buyersearch_booknow_delivery_date_' + rowNo).val();

        var ptlTocheckLocationId = $('#ptlTocheckLocationId').val();
        
        

        var termContractDispatchDate='';
        //alert($('#service_id').val());

        //post creation for book now
        allData = {'postItemId': postItemId};
        $.ajax({
                type: "POST",
                url: "/setbuyerpost",
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
                    if (data!=0&& data!='') {
                        quoteItemId=data;
                        if($('#service_id').val()==19){
                            checkAndSetGMBooknow(sourceLocationTypeOther,sourceLocationType, buyerId,
                            sellerId,   consignorName, consignorNumber,
                            consignorEmail, consignorAddress, consignorPin,  additionalDetails,
                             rowNo,quoteItemId, postItemId, price, isCheckout,null,sellerPostedFromDate,sellerPostedToDate, null,termContractDispatchDate);
                        }else{
                        checkAndSetBooknow(sourceLocationTypeOther,destinationLocationTypeOther,packagingTypeOther,sourceLocationType, destinationLocationType, packagingType, buyerId,
                            sellerId, consignmentPickupDate, consignmentValue, consignorName, consignorNumber,
                            consignorEmail, consignorAddress, consignorPin, consigneeName, additionalDetails,
                            consigneeNumber, consigneeEmail, consigneePin, consigneeAddress, rowNo,
                            consignmentNeedInsurance, quoteItemId, postItemId, price, isCheckout,null,sellerPostedFromDate,sellerPostedToDate, null);
                        }
                    } 
                }
            }, "json");
    	
        
    });
    $("#addptlbuyerpostcounteroffer").on("click", ".add_buyer_addtocart_details, .add_buyer_checkout_details", function() {
        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var isCheckout;
        if($(this).text() == 'Checkout') {
            isCheckout = '1';
        } else {
            isCheckout = '0';
        }
        var sourceLocationType = null;
        var destinationLocationType = null;
        var packagingType = null;
        //other fields for ftl search
        var sourceLocationTypeOther = null;
        var destinationLocationTypeOther = null;
        var packagingTypeOther = null;
        
        var consignmentPickupDate = $('#buyersearch_booknow_offer_consignment_pickup_date_' + rowNo).val();
        var consignmentNeedInsurance = $('#buyersearch_booknow_offer_need_insurance_' + rowNo).val();

        var serviceid = $('#service_id').val();
        if(serviceid == 7 || serviceid == 8){
        var consignmentNeedFragile = $('#buyersearch_booknow_offer_is_fragile_' + rowNo).val();
        }else{
        var consignmentNeedFragile = '';   
        }    

        var consignmentValue = $('#buyersearch_booknow_offer_consignment_value_' + rowNo).val();
        var consignorName = $('#buyersearch_booknow_offer_consignor_name_' + rowNo).val();
        var consignorNumber = $('#buyersearch_booknow_offer_consignor_number_' + rowNo).val();
        var consignorEmail = $('#buyersearch_booknow_offer_consignor_email_' + rowNo).val();
        var consignorAddress = $('#buyersearch_booknow_offer_consignor_address_' + rowNo).val();
        var consignorPin = $('#buyersearch_booknow_offer_consignor_pincode_' + rowNo).val();
        var consigneeName = $('#buyersearch_booknow_offer_consignee_name_' + rowNo).val();
        var consigneeNumber = $('#buyersearch_booknow_offer_consignee_number_' + rowNo).val();
        var consigneeEmail = $('#buyersearch_booknow_offer_consignee_email_' + rowNo).val();
        var consigneePin = $('#buyersearch_booknow_offer_consignee_pin_' + rowNo).val();
        var consigneeAddress = $('#buyersearch_booknow_offer_consignee_address_' + rowNo).val();
        var additionalDetails = $('#buyersearch_booknow_offer_additional_details_' + rowNo).val();
        var buyerId = $('#ptl_buyer_post_buyer_id_' + rowNo).val();
        var sellerId = $('#ptl_buyer_post_seller_id_' + rowNo).val();
        var price = $('#buyer_post_price_' + rowNo).data('price');
        var quoteItemId = $('#ptl_buyer_quote_item_id_' + rowNo).val();
        var quoteId = $('#ptl_buyer_quote_id_' + rowNo).val();
        var postItemId = $('#ptl_seller_post_item_id_' + rowNo).val();

        var sellerPostedFromDate = $('#ptl_buyer_counter_offer_seller_post_from_date_' + rowNo).val();
        var sellerPostedToDate = $('#ptl_buyer_counter_offer_seller_post_to_date_' + rowNo).val();

        checkAndSetBooknow(sourceLocationTypeOther,destinationLocationTypeOther,packagingTypeOther,sourceLocationType, destinationLocationType, packagingType, buyerId,
            sellerId, consignmentPickupDate, consignmentValue, consignorName, consignorNumber,
            consignorEmail, consignorAddress, consignorPin, consigneeName, additionalDetails,
            consigneeNumber, consigneeEmail, consigneePin, consigneeAddress, rowNo,
            consignmentNeedInsurance, quoteItemId, postItemId, price, isCheckout, quoteId, 
            sellerPostedFromDate, sellerPostedToDate, consignmentNeedFragile);
    });
    $(".intra_booknow_buyer_form").on("click", ".booknow_buyer", function(e) {
        e.preventDefault();
        var id=$(this).data('intrabooknow_list');
        var opt = {
        autoOpen: true,
        modal: true,
        width: 550,
        title: 'Details'
    };
        $("#search-dialog_"+id).dialog(opt);
    $("#search-dialog_"+id).dialog("open"); 
        $("#search-dialog_"+id).removeClass("displayNone"); 
    });
    $(".search-dialog").dialog({
        autoOpen: false,
        modal: true,
        buttons : {
             "Confirm" : function() {
                 //$("#search-dialog").dialog(opt);
                 $(this).dialog("close");  
                 $(this).parents().find(".ui-dialog.ui-widget").hide();
                 $(".ui-widget-overlay").hide();
                 //var rowNo = $('#intra_booknow_buyer_form .booknow_buyer').attr('data-intrabooknow_list');
                 var rowNo=$(this).data('bqid');
             var consignmentPickupDate = $('#buyersearch_booknow_offer_consignment_pickup_date_' + rowNo).val();
             var consignmentPickupTime = $('#buyersearch_booknow_offer_consignment_pickup_time_' + rowNo).val();
             var buyerId = $('#buyersearch_booknow_buyer_id_' + rowNo).val();
             var sellerId = $('#buyersearch_booknow_seller_id_' + rowNo).val();
             var price = $('#buyersearch_booknow_seller_price_' + rowNo).val();
             var quoteItemId = '';
             var postItemId = rowNo;
             //alert(consignmentNeedInsurance);
             allData = {'postItemId': postItemId};
             $.ajax({
                type: "POST",
                url: "/setbuyerpost",
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
                    if (data!=0&& data!='') {
                        quoteItemId=data;
                        checknSetIntraBooknow( buyerId,sellerId, consignmentPickupDate, consignmentPickupTime, 
                  rowNo, quoteItemId, postItemId, price);
                    } 
                }
            }, "json");
             
             },
             "modify" : function() {
               //$(this).dialog("close");
               window.location.href = 'buyersearch';
             }
           }
    });
    /************************End Script*************************/

    $(".cart_delete_yes").on("click",function(){               
        var cart_item_id = $("#delete_cart_item_id").val();
        window.location.href = '/cart/deleteitem/' + cart_item_id;    
    });
    /*$(".delete_cart_item").click(function() {
        var message = confirm("Are you sure want to delete cart item!");
        if (message == true) {
            var id = $(this).data("id");
            window.location.href = '/cart/deleteitem/' + id;
        } else {
            return false;
        }
    });*/
    $("#cancel_buyer_counter_offer_enquiry").click(function() {
        var message = confirm("Are you sure want to delete!");
        if (message == true) {
            var id = $(this).data("id");
            window.location.href = '/cancelenquiry/' + id;
        } else {
            return false;
        }
    });
    $("#ptl_cancel_buyer_counter_offer_enquiry").click(function() {
        var message = confirm("Are you sure want to delete!");
        if (message == true) {
            var id = $(this).data("id");
            window.location.href = '/cancelenquiry/' + id;
        } else {
            return false;
        }
    });
    $("#buyer_post_counter_offer_comparision_types").change(function() {
        var buyerQuoteId = $(this).parent(".comparision_types_div").data("buyerquoteid");
        var comparisonType = $(this).val();
        if(comparisonType==2){
        var priceval = $("#priceval").val();
        }else{
        var priceval = $("#transitval").val();
        }
        //alert(priceval);
        //$().redirect('/getbuyercounteroffer'+buyerQuoteId, {'comparisonType' : comparisonType});
        var allVals = [];
		
	     $('input:checkbox.quotecheck').each(function() {
	    	 
	      if($(this).is(":checked")==true){
	    	
	    	 allVals.push($(this).val());
	      }
	     });
        window.location = '/getbuyercounteroffer/' + buyerQuoteId + '/' + comparisonType + '/' + priceval + '/' + allVals ;
    });
    
    $("#buyer_postrelocation_counter_offer_comparision_types").change(function() {
    	
    	var sellerids=[];
    	var compareid=$(this).val();
    	   $('.checksellres').each(function () {
    			
    		   if($(this).is(":checked")==true){
    		    	
    			   sellerids.push($(this).val());
    			 }
    	        //selectedValue.push($(this).val());
    	    });
    	   
    	    
    	    var buyerpostid=$("#buyer_details_id").val();	
    	   if(compareid!=0){
    		   if(sellerids.length!=0){
    			  
    		   window.location.href = '/getbuyercounteroffer/'+buyerpostid+'/'+compareid+'/'+sellerids;   
    		   }else{
    		   window.location.href = '/getbuyercounteroffer/'+buyerpostid+'/'+compareid;
    		   }
    	   }else{
    		 window.location.href = '/getbuyercounteroffer/'+buyerpostid;   
    	   }
    	   
    });
    
    $("#addbuyerpostcounteroffer .add_buyer_counter_offer_details, #addbuyerpostcounteroffer_th .add_buyer_counter_offer_details").click(function() {
        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var $counterOfferFieldId = $('#buyer_post_counter_offer_' + rowNo);
        var counterOfferValue = $counterOfferFieldId.val();
        
        var $counterTransitFieldId = $('#buyer_post_transit_days_' + rowNo);
        var counterTransitValue = $counterTransitFieldId.val();
        
        var regexPattern = /^\d{0,8}(\.\d{1,2})?$/;
        if (!counterOfferValue.trim()) {
            alert('Please enter counter offer!');
            $counterOfferFieldId.focus();
            return false;
        } else if (!regexPattern.test(counterOfferValue)) {
            alert(' Counter Offer should be a number!');
            $counterOfferFieldId.focus();
            return false;
        } else if (counterOfferValue>200000){
            alert(' Counter Offer should be Less than 2 Lakhs');
            $counterOfferFieldId.focus();
            return false;
        } else if (!counterTransitValue.trim()) {
            alert('Please enter Transit Days!');
            $counterTransitFieldId.focus();
            return false;
        } else if (!regexPattern.test(counterTransitValue)) {
            alert(' transit Days should be a number!');
            $counterTransitFieldId.focus();
            return false;
        }else {
            $.ajax({
                type: "POST",
                url: "/setbuyercounteroffer",
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
                    'counterOfferValue': counterOfferValue,
                    'countertransitValue': counterTransitValue,
                    'buyerCounterOfferId': rowNo
                    
                },
                success: function(jsonData) {
                    $("#erroralertmodal .modal-body").html('Counter offer added successfully.');
                    $("#erroralertmodal").modal({
                        show: true
                    }).one('click','.ok-btn',function (e){
                        location.reload();
                    });
//                    alert('Counter offer added successfully.');
//                    location.reload();
                }
            }, "json");
        }
    });
    $("#addptlbuyerpostcounteroffer .ptl_counter_rate_per_kg, #addptlbuyerpostcounteroffer .ptl_conversion_kg_cft").focusout(function() {
        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var sellerPostItemId = $('#counter_offer_submit_button_' + rowNo).data("ptl_seller_post_item_id");
        var $counterRateForKgFieldId = $('#ptl_counter_rate_per_kg_' + rowNo);
        var $conversionKgCftFieldId = $('#ptl_conversion_kg_cft_' + rowNo);
        var counterRateForKgValue = $counterRateForKgFieldId.val();
        var conversionKgCftValue = $conversionKgCftFieldId.val();
        var regexPattern_1 = /^\d{1,4}(\.\d{1,4})?$/;
        var regexPattern_2 = /^\d{1,4}(\.\d{1,2})?$/;
        if (!counterRateForKgValue || !counterRateForKgValue.trim()) {
            return false;
        } else if (counterRateForKgValue == 0) {
        	 alert('Rate Per Kg should be greater than 0');
            return false;
        }else if (!conversionKgCftValue || !conversionKgCftValue.trim()) {
            return false;
        }else if (conversionKgCftValue == 0) {
       	 alert('Conversion Factor should be greater than 0');
         return false;
        } else if (!regexPattern_2.test(counterRateForKgValue)) {
            alert('Rate Per Kg should be less than 10000 with max of 2 decimals!');
            return false;
        } else if (!regexPattern_1.test(conversionKgCftValue)) {
            alert('Kg Per Ccm should be less than 10000 with max of 4 decimals!');
            return false;
        } else {
        	e
            $.ajax({
                type: "POST",
                url: "/getfreightdetails",
                data: {
                    'counterRateForKgValue': counterRateForKgValue,
                    'conversionKgCftValue': conversionKgCftValue,
                    'buyerCounterOfferId': rowNo,
                    'sellerPostItemId': sellerPostItemId
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
    $("#addptlbuyerpostcounteroffer .ptl_add_buyer_counter_offer_details").click(function() {
        var rowNo = $(this).data("ptl_booknow_buyer_quoteid");
        var sellerPostItemId = $(this).data("ptl_seller_post_item_id");
        var $counterRateForKgFieldId = $('#ptl_counter_rate_per_kg_' + rowNo);
        var $conversionKgCftFieldId = $('#ptl_conversion_kg_cft_' + rowNo);
        var counterRateForKgValue = $counterRateForKgFieldId.val();
        var conversionKgCftValue = $conversionKgCftFieldId.val();
        var regexPattern_1 = /^\d{0,8}(\.\d{1,2})?$/;
        var regexPattern_2 = /^\d{0,8}(\.\d{1,4})?$/;
        var id=$('#service_id').val();
        if (!counterRateForKgValue.trim()) {
            alert('Please enter counter rate for KG!');
            $counterOfferFieldId.focus();
            return false;
        } else if (!conversionKgCftValue.trim()) {
            if(id==2 || id==6)
            alert('Please enter conversion KG Cft value!');
            else if(id==7 || id==8)
            alert('Please enter conversion KG Ccm value!');
            else if(id==9)
            alert('Please enter conversion KG Cm value!');
            
            $counterOfferFieldId.focus();
            return false;
        } else if (!regexPattern_1.test(counterRateForKgValue) || !regexPattern_2.test(conversionKgCftValue)) {
            alert('Value should be a number!');
            $counterOfferFieldId.focus();
            return false;
        }else if (counterRateForKgValue<=0 && conversionKgCftValue<=0) {
            alert('Value should be a greater than 0');
            $counterOfferFieldId.focus();
            return false;
        } else {
            $.ajax({
                type: "POST",
                url: "/setbuyercounteroffer",
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
                    'counterRateForKgValue': counterRateForKgValue,
                    'conversionKgCftValue': conversionKgCftValue,
                    'buyerCounterOfferId': rowNo,
                    'sellerPostItemId': sellerPostItemId
                },
                success: function(jsonData) {
                    var id=$('#service_id').val();
                    if(id==2 || id==6)
                    alert('Counter rate for KG and Conversion KG Cft value added successfully.');
                    else if(id==7 || id==8)
                    alert('Counter rate for KG and Conversion KG Ccm value added successfully.');
                    else if(id==9)
                    alert('Counter rate for KG and Conversion KG Cm value added successfully.');
                    location.reload();
                }
            }, "json");
        }
    });
    // hide and show of sumitquery details
    $(".buyer_submit_counter_offer").click(function() {
        //$('input[type="submit"]').removeAttr('disabled');
        var strClass = $(this).data("buyerpostofferid");
        // $(".table-slide-1").slideToggle("500");
        $(".counter_offer_details_" + strClass).slideToggle("500");
        return false;
    });
    // $(".detailsslide-4").click(function(){ buyer_book_now_details_
    // //hide and show of sumitquery details
    // $(".detailsslide-1").click(function(){
    // $('input[type="submit"]').removeAttr('disabled');
    // $(".table-slide-1").slideToggle("500");
    // });
    //	   
    // $(".detailsslide-4").click(function(){
    // $(".table-slide-4").slideToggle("500");
    // });
    $(" .buyer_leads_book_now").click(function() {
    	if($('#service_id').val()!=1){    
    		$(this).closest('form').append('<input type="hidden" name="price" id="lead_price" value="" />');
    	var id = $(this).data('id');
    	$('#lead_price').val($('#total_hidden_price_'+id).text());
    	$(this).closest('form').submit(); 
    }else{
    	var url = $(this).data('url');
        window.location = url;
    }
    });
    $(".buyer_book_now").click(function() {
        var url = $(this).data('url');
        window.location = url;
//        var strBookNowClass = $(this).closest('span').data("buyerpostofferid");
//        $(".buyer_book_now_details_" + strBookNowClass).slideToggle("500");
//        return false;
    });
    /*
     * $('.calendar.flexible_dispatch_date').click(function() { if
     * ($('#ui-datepicker-div .checkbox_div').length) { } else {
     * $("#ui-datepicker-div").prepend("<div class='checkbox_div'><input
     * type='checkbox' id = 'flexible_dispatch_date' class='checkbox' /> <span
     * class='checkbox_value'>Flexible Dispatch Date</span></div>"); } });
     * $('.calendar.flexible_delivery_date').click(function() { if
     * ($('#ui-datepicker-div .checkbox_div').length) { } else {
     * $("#ui-datepicker-div").prepend("<div class='checkbox_div'><input
     * type='checkbox' id = 'flexible_delivery_date' class='checkbox' /> <span
     * class='checkbox_value'>Flexible Delivery Date</span></div>"); } });
     */

    // Validations for insert form and edit forms in buyer get quote

    $('#add_buyer_quote').on('click', function() {
        $("#buyer_quote_form").valid();
    });
    
    
    // Check select payment method validation
    $("#checkout-form-lines").validate({
        rules: {
            "payment_method": {
                required: true,
            },
            "agree_payment": {
                required: true,
            },
        },
        messages: {
            "payment_method": {
                required: "Please Select Payment Method",
            },
            "agree_payment": {
                required: "Please Check Terms & Conditions",
            },
        },
        errorPlacement: function(error, element) {
        	$(element).parent('div').after(error);
        },
    });
    jQuery.validator.addMethod("floatvalidation", function(value, element) {
        if(parseFloat(value)>0){
            return this.optional(element) || /^\d+(\.\d{1,3})?$/i.test(value);
         }else{
             //alert("Please enter only digits greater than 0");
         }
   }, function(params, element) {

        element.value  = Math.floor(element.value * 1000) / 1000;

        if(parseFloat(element.value)>0){
            return "Quantity is truncated to 3 decimals"
        }else{
            return "Please enter value greater than 0"
        }

    } );
   
    // Buyer quote form validation
    $("#buyer_quotelineitems_form_validation").validate({ // initialize the
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
            "dispatch_date": {
                required: true,
            },

            "load_type": {
                required: true,
            },
            "c": {
                required: true,
                number: true,
                lessThanEqualthousand:true,
            },
            "vehicle_type": {
                required: true,
            },
            "quote_type": {
                required: true,
            },
            "price": {
                required: true,
                number: true,
                pricevalidation:true,
            },
            "no_of_loads": {
                //required: true,
                number: true,
                noloadsalidation:true,
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
                required: "From Location is required",
            },
            "to_location": {
                required: "",
            },
            "to_location_id": {
                required: "To Location is required",
            },
            "dispatch_date": {
                required: "Dispatch date is required",
            },

            "load_type": {
                required: "Load type is required",
            },
            "c": {
                required: "Quantity is required",
            },
            "c": {
                required: "Quantity is required",
                number: "Please enter only numbers",
                accept: "Please enter only numbers"
            },
            "vehicle_type": {
                required: "Vehicle Type is required",
            },
            "quote_type": {
                required: "Price Type is required",
            },
            "price": {
                required: "Price is required",
            },
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });


    /*** Start : Buyer truck haul quote form validation ***/
    $("#buyer_truckhaul_quotelineitems_form_validation").validate({ // initialize the
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
            "reporting_date": {
                required: true,
            },

            "load_type": {
                required: true,
            },
            "c": {
                required: true,
                number: true,
                //floatvalidation:true,
                fivedecimalvalidation:true,
            },
            "vehicle_type": {
                required: true,
            },
            "quote_type": {
                required: true,
            },
            "price": {
                required: true,
                number: true,
                pricevalidation:true,
            },
            "no_of_loads": {
                //required: true,
                number: true,
                noloadsalidation:true,
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
                required: "From Location is required",
            },
            "to_location": {
                required: "",
            },
            "to_location_id": {
                required: "To Location is required",
            },
            "reporting_date": {
                required: "Reporting date is required",
            },

            "load_type": {
                required: "Load type is required",
            },
            "c": {
                required: "Quantity is required",
            },
            "c": {
                required: "Quantity is required",
                number: "Please enter only numbers",
                accept: "Please enter only numbers"
            },
            "vehicle_type": {
                required: "Vehicle Type is required",
            },
            "quote_type": {
                required: "Price Type is required",
            },
            "price": {
                required: "Price is required",
            },
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });

    // Add more items elements store in hidden fileds
    var seller_id_list = new Array();
    $('#add_th_buyer_more')
        .click(
            function() {
                $("#buyer_truckhaul_quotelineitems_form_validation").validate().cancelSubmit = false; // Validation
                // for
                // add
                // more
                // in
                // after
                // first
                // time
                var num = parseInt($('#next_add_buyer_more_id').val()) + 1;
                $('#next_add_buyer_more_id').val(num);
                var from_location_value = $('#from_location_id').val();
                var to_location_value = $('#to_location_id').val();
                var load_type_value = $("#load_type option:selected").text();
                var vehicle_type_value = $("#vehicle_type option:selected").text();
                var reporting_date = $('#reporting_date').val();
                var units_value = $('#capacity').val();
                var from_location = $('#from_location').val();
                var to_location = $('#to_location').val();
                var load_type = $('#load_type').val();
                var vehicle_type = $('#vehicle_type').val();
                var units = $('#capacity').val();
                var quantity = $('#quantity').val();
                if($("#quote_id").val()==2){
                var price = $('#price').val();
                }else{
                var price = ""; 
                }
                var Quotes_quote_type = $('#quote_id').val();
                var noofloads = $('#no_of_loads').val();
                var is_dispatch_flexible = $('#is_dispatch_flexible_hidden').val();
                var is_delivery_flexible = $('#is_delivery_flexible_hidden').val();
                //alert(from_location); return false;
                if (Quotes_quote_type == '1') {
                    var price_no = "0";
                } else {
                    if (isNaN(price)) {
                        alert("Please Enter Valid Price");
                        document.getElementById("price").value = '';
                        document.getElementById("price").focus();
                        return false;
                    }
                    var price_no = $('#price').val();
                }
                
                if (from_location != "" && to_location != "" && from_location_value != '' && to_location_value != '' && load_type != "" && vehicle_type != "" && units != "" && quantity != "" && Quotes_quote_type != "" && price_no != "") {
                    if($("#update_line").val()==1){
                    $('.request_row_' + $("#update_row_count").val()).remove(); 
                    $("#update_line").val(0);
                    }
                    $('#error-add-item').text('');
                    var seller_location_id = from_location_value;
                    seller_id_list.unshift(seller_location_id);
                    var html = '<div class="table-row inner-block-bg request_row_' + num + '"><div class="col-md-2 padding-left-none" id="from_loc_'+ num +'">' + from_location + '</div><div class="col-md-2 padding-left-none" id="to_loc_'+ num +'">' + to_location + '</div><div class="col-md-3 padding-left-none" id="load_type_text_'+ num +'">' + load_type_value + '</div><div class="col-md-2 padding-left-none" id="vehicle_type_text_'+ num +'">' + vehicle_type_value + '</div><div class="col-md-2 padding-none" id="price_'+ num +'">' + price + '</div><div class="class="col-md-2 padding-none"><a class="edit_this edit" row_id="' + num + '"><i class="fa fa-edit red" title="Edit"></i></a>&nbsp;<a class="remove_this remove" row_id="' + num + '"><i class="fa fa-trash red" title="Delete"></i></a></div><input type="hidden" name="from_location[]" class="from_location"  value="' + from_location_value + '" id="from_location_id_'+num+'"><input type="hidden" name="to_location[]" value="' + to_location_value + '" id="to_location_id_'+num+'"><input type="hidden" name="reporting_date[]" value="' + reporting_date + '" id="reporting_date'+num+'"><input type="hidden" name="load_type[]" value="' + load_type + '" id="load_type_'+num+'"><input type="hidden" name="vehicle_type[]" value="' + vehicle_type + '" id="vehicle_type_'+num+'"><input type="hidden" name="capacity[]" value="' + units + '" id="unit_cap_'+num+'"><input type="hidden" name="quantity[]" value="' + quantity + '" id="quant_'+num+'"><input type="hidden" name="quote_id[]" value="' + Quotes_quote_type + '" id="quote_'+num+'"><input type="hidden" name="no_of_loads[]" value="' + noofloads + '" id="no_loads_'+num+'"><input type="hidden" name="price[]" value="' + price + '"><input type="hidden" name="is_dispatch_flexible[]" value="' + is_dispatch_flexible + '" id="dispach_flexible'+num+'"><input type="hidden" name="is_delivery_flexible[]" value="' + is_delivery_flexible + '" id="delivery_flexible'+num+'"></div>';
                    $('.request_rows').append(html);
                    $('#from_location').val("");
                    $('#to_location').val("");
                    $('#from_location_id').val("");
                    $('#to_location_id').val("");
                    $('#reporting_date').val("");
                    $("#reporting_date").datepicker("destroy");
                    $('#quantity').val("");
                    $('#capacity').val("");
                    $('#load_type').val("");
                    $('#vehicle_type').val("");
                    $('#dimensions').val("");
                    $('#no_of_loads').val("");
                    $('#quote_id').val("");
                    $('#price').val("");
                    $("#is_dispatch_flexible").prop('checked', false);
                    $("#is_delivery_flexible").prop('checked', false);
                    $("#buyer_truckhaul_quotelineitems_form_validation")
                        .validate().cancelSubmit = true;
                    $('.selectpicker').selectpicker('refresh');
                    $("#reporting_date")
                        .datepicker({
                            changeMonth: true,
                            numberOfMonths: 1,
                            minDate: 0,                            
                            dateFormat: "dd/mm/yy",                            
                        });
                    return false;
                }
            });
    /*** END : Buyer truck haul quote form validation ***/

    jQuery.validator.addMethod("pricevalidation", function(value, element) {
            if(parseFloat(value)>0){
                //return this.optional(element) || /^\d+(\.\d{1,2})?$/i.test(value);
            	return this.optional(element) || /^\d{1,6}(\.\d{1,2})?$/i.test(parseFloat(element.value));
            }else{
            }
        }, function(params, element) {
        	if(element.value != ''){
            element.value  = Math.floor(element.value * 100) / 100;
        	}
            var count_value = /^\d{1,6}(\.\d{1,2})?$/i.test(parseFloat(element.value));
            if(count_value == false){
             	return "Price should be less than 1000000"
            }	
            else if(parseFloat(element.value)>0){
                return "Price is truncated to 2 decimals"
            }else{
                return "Please enter value greater than 0"
            }

        });
    
    
    jQuery.validator.addMethod("rateperkg", function(value, element) {
        if(parseFloat(value)>0){
            //return this.optional(element) || /^\d+(\.\d{1,2})?$/i.test(value);
        	return this.optional(element) || /^\d{1,5}(\.\d{1,2})?$/i.test(parseFloat(element.value));
        }else{
        }
    }, function(params, element) {
    	if(element.value != ''){
        element.value  = Math.floor(element.value * 100) / 100;
    	}
        var count_value = /^\d{1,5}(\.\d{1,2})?$/i.test(parseFloat(element.value));
        if(count_value == false){
         	return "Price should be less than 10000"
        }	
        else if(parseFloat(element.value)>0){
            return "Price is truncated to 2 decimals"
        }else{
            return "Please enter value greater than 0"
        }

    });
    
    jQuery.validator.addMethod("fivedecimalvalidation", function(value, element) {
        if(parseFloat(value)>0){
            //return this.optional(element) || /^\d+(\.\d{1,2})?$/i.test(value);
        	return this.optional(element) || /^\d{1,4}(\.\d{1,3})?$/i.test(parseFloat(element.value));
        }else{
        }
    }, function(params, element) {

        element.value  = Math.floor(element.value * 1000) / 1000;
        var count_value = /^\d{1,4}(\.\d{1,3})?$/i.test(parseFloat(element.value));
        if(count_value == false){
        	return "Quantity should be less than 10000"
        }else if(parseFloat(element.value)>0){
            return "Quantity is truncated to 3 decimals"
        }else{
            return "Please enter value greater than 0"
        }

    });
    /*
    jQuery.validator.addMethod("pricevalidation", function(value, element) {
        if(parseFloat(value)>0){
            return this.optional(element) || /^\d+(\.\d{1,2})?$/i.test(value);
        }else{
            //alert("Please enter only digits greater than 0");
        }
    }, "Please enter value greater than 0, price will be truncated to 2 decimals");
    */
    // buyer search form validation
    $("#buyer_search_form , #buyer_results_form").validate({ // initialize the plugin
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
            "from_date": {
                required: true,
            },
            "lkp_load_type_id": {
                required: true,
            },
            "quantity": {
                required: true,
                number: true,
                //floatvalidation:true,
                lessThanEqualthousand:true,
            },
            "lkp_vehicle_type_id": {
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
                required: "This is required field",
            },
            "to_location": {
                required: "",
            },
            "to_location_id": {
                required: "This is required field",
            },
            "from_date": {
                required: "Enter Dispatch Date",
            },
            "lkp_load_type_id": {
                required: "Select Load Type",
            },
            "quantity": {
                required: "Enter Quantity",
                number: "Only numbers are allowed",
                accept: "Only numbers are allowed"
            },
            "lkp_vehicle_type_id": {
                required: "Select Vehicle Type",
            },
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });
    
    $("#buyer_search_form_modify").validate({ // initialize the plugin
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
            "from_date": {
                required: true,
            },
            "lkp_load_type_id": {
                required: true,
            },
            "quantity": {
                required: true,
                number: true,
              // floatvalidation:true,
                fivedecimalvalidation:true,
            },
            "lkp_vehicle_type_id": {
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
                required: "This is required field",
            },
            "to_location": {
                required: "",
            },
            "to_location_id": {
                required: "This is required field",
            },
            "from_date": {
                required: "Enter Dispatch Date",
            },
            "lkp_load_type_id": {
                required: "Select Load Type",
            },
            "quantity": {
                required: "Enter Quantity",
                number: "Only numbers are allowed",
                accept: "Only numbers are allowed"
            },
            "lkp_vehicle_type_id": {
                required: "Select Vehicle Type",
            },
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });

    // buyer search form validation
    $("#buyer_search_form_tl").validate({ // initialize the plugin
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
            "from_date": {
                required: true,
            },
            "to_date": {
                required: true,
            },
            "lkp_trucklease_lease_term_id": {
                required: true,
            },
            "lkp_vehicle_type_id": {
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
                required: "This is required field",
            },
            "to_location": {
                required: "",
            },
            "to_location_id": {
                required: "This is required field",
            },
            "from_date": {
                required: "Enter From Date",
            },
            "to_date": {
                required: "Enter To Date",
            },
            "lkp_trucklease_lease_term_id": {
                required: "Select Lease Term",
            },
            "lkp_vehicle_type_id": {
                required: "Select Vehicle Type",
            },
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });
    
    
    
    /*
     * |----------------------------------------------------------------------------------------------------------------- | | |
     * |       INTRACITY STARTED
     * |-----------------------------------------------------------------------------------------------------------------
     * |Here we have all kinds of javascript validations and ajax for intracity
     */
    /**
     * ***********************Below script for buyer search validations****************************************
     */

    $("#intracity_buyer_search_form").validate({ // initialize the plugin
        ignore: "input[type='text']:hidden",
        rules: {
            "lkp_city_id": {
                required: true,
            },
            "from_location": {
                required: true,
            },
            "from_location_id" : {
                required : true,
            },
            "to_location": {
                required: true,
            },
            "to_location_id" : {
                required : true,
            },
            "pickup_date": {
                required: true,
            },
            "pickup_time": {
                required: true,
            },
            "lkp_load_type_id": {
                required: true,
            },
            "weight": {
                required: true,
                number: true,
                floatvalidation:true,
            },
            "lkp_vehicle_type_id": {
                required: true,
            },
            "rate_type": {
                required: true,
            },
        },
        errorPlacement: function(error, element) {
            $(element).parent().parent().append(error);
        },
        messages: {
            "from_location": {
                required: "",
            },
            "from_location_id": {
                required: "This is required field",
            },
            "to_location": {
                required: "",
            },
            "to_location_id": {
                required: "This is required field",
            },
            "pickup_date": {
                required: "Enter pickup Date",
            },
            "lkp_load_type_id": {
                required: "Select Load Type",
            },
            "quantity": {
                required: "Enter Quantity",
            },
            "lkp_vehicle_type_id": {
                required: "Select Vehicle Type",
            },
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });
    /**
     * 
     * BUYER INTRACITY POST FORM
     * 
     * 
     */
    
    
    
    $("#intracity-buyer-post").validate({ // initialize the plugin
        ignore: "input[type='text']:hidden",
        rules: {
            "lkp_city_id": {
                required: true

            },
            "lkp_rate_type": {
                required: true
            },
            "lkp_vehicle_id": {
                required: true

            },
            "load_type": {
                required: true

            },
            "pickup_date": {
                required: true
            },
            "pickup_time": {
                required: true
            },
            "units": {
                required: true,
                number: true,
                floatvalidation:true,
            },
            "lkp_ict_weight_parameter_id": {
                required: true,
            },
            "from_location": {
                required: true
            },
            "to_location": {
                required: true
            },
        },
        errorPlacement: function(error, element) {
            $(element).parent('div').parent('div').append(error);
        },
        messages: {
            "lkp_city_id": {
                required: 'City is required'

            },
            "lkp_rate_type": {
                required: 'Rate type is required'
            },
            "lkp_vehicle_id": {
                required: 'Vehicle type is required'

            },
            "load_type": {
                required: 'Load type is required'

            },
            "pickup_date": {
                required: 'Pickup date is required'
            },
            "pickup_time": {
                required: 'Pickup time is required'
            },
            "units": {
                required: 'Units is required',
                number:'Please enter only integers'
            },
            "lkp_ict_weight_parameter_id": {
                required: 'Parameter of weight is required'
               
            },
            "from_location": {
                required: 'From Location is required'

            },
            "to_location": {
                required: 'To Location is required'

            },
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });
    /**
     * *************************INTRACITY AUTOCOMPLETE FOR LOCATIONS*****************************************
     */
    // alert("fdsfds");
    $(".intrabuyerdetails_list").click(function() {
    var id = $(this).attr('data-sellerlistid');
    $(".intrabuyer_listdetails_" + id).slideToggle("500");
});
    $("#lkp_city_id").change(function() {
        $("#to_intra_location").val('');
        $("#from_intra_location").val('');
    });
    // intracitybuyercontroller have the query
    $("#from_intra_location").click(function() {
        var city_id = $("#lkp_city_id").val();
        $("#from_intra_location").autocomplete({
            source: "/intracityautocomplete?city_id=" + city_id,
            minLength: 1,
            select: function(event, ui) {
                $('#from_intra_location').val(ui.item.value);
                $('#from_location_id').val(ui.item.id);
                $(this).closest("form").validate().element($('#from_location_id'));
                /*Need to add this below class to every autocomplete: Shriram */
                $('#from_intra_location').addClass("clsAutoDisable");
            }
        });
    });
    // intracitybuyercontroller have the query
    $("#from_intra_location").on('blur keyup', function() {
        var city_id = $("#lkp_city_id").val();
        var from_location = $(this).val();
        $("#to_intra_location").autocomplete({
            source: "/intracityautocompleteto?fromlocation=" + from_location + "&city_id=" + city_id,
            minLength: 1,
            select: function(event, ui) {
                $('#to_intra_location').val(ui.item.value);
                $('#to_location_id').val(ui.item.id);
                $(this).closest("form").validate().element($('#to_location_id'));
                /*Need to add this below class to every autocomplete: Shriram */
                $('#to_intra_location').addClass("clsAutoDisable");
            }
        });
    });

    $('.pickup_date').datepicker({
        dateFormat: "dd/mm/yy",
        minDate: 0
    });

    $('#from_intra_location').click(function() {
        if ($('#lkp_city_id').val() == '') {
            alert("Please select the city");
            $('#from_intra_location').blur();
        }

    });
    $('#to_intra_location').click(function() {
        if ($('#lkp_city_id').val() == '') {
            alert("Please select the city");
            $('#to_intra_location').blur();
        }

    });
    /*******************************************CANCEL INTRACITY BUYER POST**************************************************/
	//seller post cancel process

	$("#cancelBuyerIntraPost").click(function() {
		 var answer = confirm ("Are you sure you want to delete the post?");
		    if (answer)
		    {
		var postId = $('#intraSeller-post-id').val();
		datastr = '&postId=' + postId;
		// ajax function starts
		$.ajax({
			type : 'post', // defining the ajax type
			url : '/intracitybuyer/cancelpost', // calling the controller with the
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
    /** *********************** MSP PAGES VALIDATION **************************** */
    // ADD SELLER VEHICLE TO INTRACITY ORDERS
    $("#confirm-intracity-order").validate({ // initialize the plugin
        ignore: "input[type='text']:hidden",
        rules: {
            "order_id": "required",
            "lkp_ict_vehicle_id": "required"
        },
        errorPlacement: function(error, element) {
            $(element).parent('div').append(error);
        },
        // Specify the validation error messages
        messages: {
            "order_id": {
                required: 'Please select order'

            },
            "lkp_ict_vehicle_id": {
                required: 'Please select vehicle'
            },
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });
    // CREATE SELLER QUOTES FOR BUYER INTRACITY POST

    $("#create-seller-intracity-quote").validate({ // initialize the plugin
        ignore: "input[type='text']:hidden",
        rules: {
            "buyer_quote_id": "required",
            "lkp_ict_vehicle_id": "required",
            "initial_quote_price": {
                required: true,
                number: true
            }

        },
        errorPlacement: function(error, element) {
            $(element).parent('div').append(error);
        },
        // Specify the validation error messages
        messages: {
            "buyer_quote_id": {
                required: "Please select buyer post"

            },
            "lkp_ict_vehicle_id": {
                required: "Please select vehicle"
            },
            "initial_quote_price": {
                required: "Please enter initial quote price",
                number: "Please enter only numbers"
            }
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });
    
    $("#relocation_seller_list").click(function() {
    	
    	//alert("hello");
        $( ".checksellres" ).trigger("click");
    });

    
    // UPDATE THE STATUS OF THE BUYER INTRACITY ORDER
    
    $('#pickupBtn').click(function(){
    	if($('#intra_order_id').val() !=''){
    		updateOrder('1');
    	}else{
    		$('#intra_order_error').html('Please select order#');
    	}
    	
    });
    $('#deliverBtn').click(function(){
    	if($('#intra_order_id').val() !=''){
    		updateOrder('2');
        	}else{
        		$('#intra_order_error').html('Please select order#');
        	}
    });
    
   function updateOrder(id){
    	
    	if(id == '1'){var orderStatus = '4';}
    	else if(id == '2'){var orderStatus = '6';}
    	{
    		orderId = $('#intra_order_id').val();
			datastr = '&status=' + orderStatus + '&order_id=' + orderId;
			
			
			// ajax function starts
			$.ajax({
				type : 'post', // defining the ajax type
				url : '/intracity/updateorder', // calling the controller with
				/*beforeSend : function() {
					$.blockUI({
						overlayCSS : {
							backgroundColor : '#000'
						}
					});
				},
				complete : function() {
					$.unblockUI();
				},*/ // the
				// action involved
				dataType : 'html', // datatype
				data : datastr,
				success : function(html) {
					 
					 
					$('#updateOrderError').html(html);
					 
					//window.location = '/msp/update_orders';
				}
			});
		}
    }
    
    
    
    
    /** *********************End******************** */
    /***************************************************************************
     * |------------------------------------------------------------------- |
     * PTL | | Here starts PTL Js Functions (srinu added this
     * functions-16-11-2015) | |
     * |-------------------------------------------------------------------- /
     **************************************************************************/
    $(".letterValdiation").keypress(function(event){
        var inputValue = event.charCode;
        /*if((inputValue > 47 && inputValue < 58) && (inputValue != 32)){
            event.preventDefault();
        }*/
        if(!(inputValue >= 65 && inputValue <= 122) && (inputValue != 32 && inputValue != 0)) { 
            event.preventDefault(); 
        }
    });   
   jQuery.validator.addMethod("intergervalidation", function(value, element) {
	  
           if(parseFloat(value)>0){
           	return this.optional(element) || /^\d{1,4}?$/i.test(value);
           }else{
           }
       }, function(params, element) {
           if(parseFloat(element.value)>0){
               return "No of packages possible value is 9999 only"
           }else{
               return "Please enter value greater than 0"
           }
       });
   //***********************Only for Transit days and credit period validation *****************************//
   jQuery.validator.addMethod("transitvalidation", function(value, element) {
		  
       if(parseFloat(value)>0){
       	return this.optional(element) || /^\d{1,3}?$/i.test(value);
       }else{
       }
   }, function(params, element) {
       if(parseFloat(element.value)>0){
           return "Transit days possible value is 999 only"
       }else{
           return "Please enter value greater than 0"
       }
   });
   jQuery.validator.addMethod("creditvalidation", function(value, element) {
		  
       if(parseFloat(value)>0){
       	return this.optional(element) || /^\d{1,3}?$/i.test(value);
       }else{
       }
   }, function(params, element) {
       if(parseFloat(element.value)>0){
           return "Credit Period possible value is 999 only"
       }else{
           return "Please enter value greater than 0"
       }
   });
   jQuery.validator.addMethod("creditvalidation", function(value, element) {
		  
       if(parseFloat(value)>0){
       	return this.optional(element) || /^\d{1,3}?$/i.test(value);
       }else{
       }
   }, function(params, element) {
       if(parseFloat(element.value)>0){
           return "Credit Period possible value is 999 only"
       }else{
           return "Please enter value greater than 0"
       }
   });
   //***********************Only for Transit days and credit period validation*****************************//
   
   //***********************Only for no of loads validation *****************************//
   jQuery.validator.addMethod("noloadsalidation", function(value, element) {
		  
       if(parseFloat(value)>0){
       	return this.optional(element) || /^\d{1,4}?$/i.test(value);
       }else{
       }
   }, function(params, element) {
       if(parseFloat(element.value)>0){
           return "No of loads possible value is 9999 only"
       }else if(isNaN(parseFloat(element.value))){
    	   return ""
       }else{
           return "Please enter value greater than 0"
       }
   });
   //***********************Only for no of loads validation *****************************//
   
    // Below script is validations for PTL Buyer get quote form validation
   
   jQuery.validator.addMethod("ptlbuyerdecimalvalidation", function(value, element) {
       return this.optional(element) || /^[1-9]\d*(\.\d+)?$/i.test(value);
   }, "You must include three decimal places");
   
   //air & ocean js
    if($('#Service_ID').val()==8 || $('#Service_ID').val()==9){
    $("#ptlBuyerQuotelineitemsForm").validate({
        ignore: [],
        rules: {
            "from_airport": {
                required: true,
               
            },
            "from_airport_id": {
                required: true,
            },
            
            "to_airport": {
                required: true,
               
            },
            "to_airport_id": {
                required: true,
            },
            
            "ptlDispatchDate": {
                required: true
            },
            "ptlShipmentType": {
                required: true
            },
            "ptlSenderIdentity": {
                required: true
            },
            
            "ptlLoadType" : {
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
            
            "ptlIECode" : {
                required : {
	            	depends: function(element) {
	            		if ($('#ptlShipmentType').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}

	            	}
                },
                tendigitsvalidations: { 
            		depends: function(element) {
	            		if ($('#ptlShipmentType').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	}
            },
            "ptlProductMade" : {
                required : {
	            	depends: function(element) {
	            		if ($('#ptlShipmentType').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}

	            	}
                }
            },
            
            "ptlPackageType": {
                required: true
            },
            "ptlLength": {
                required: true,
                number: true,
                fourbythreevalidations:true,
            },
            "ptlWidth": {
                required: true,  
                number: true,
                fourbythreevalidations:true,
            },
            "ptlHeight": {
                required: true,
                number: true,
                fourbythreevalidations:true,
            },
            "ptlCheckVolWeight": {
                required: true
            },
            "ptlUnitsWeight": {
                required: true,
                number: true
            },
            "ptlCheckUnitWeight": {
                required: true
            },
            "ptlNopackages": {
                required: true,
                digits: true,
                //intergervalidation: true,
            },
        },
        errorPlacement: function(error, element) {
        	//$(element).parent('div').after(error);
            $(element).parent().parent().append(error);
        },
        messages: {
            "from_airport": {
                required: '',
               
            },
            "from_airport_id": {
                required: 'This field is required',
              
            },
            "to_airport": {
                required: '',
               
            },
            "to_airport_id": {
                required: 'This field is required',
               
            },
            "ptlDispatchDate": {
                required: 'Dispatch date is required'
            },
            "ptlShipmentType": {
                required: 'Shipment type is required'
            },
            "ptlSenderIdentity": {
                required: 'Sender identity is required'
            },
            "ptlLoadType": {
                required: 'Select Load Type'
            },
            "ptlIECode": {
                required: 'IE code is required'
            },
            "ptlProductMade": {
                required: 'Product Made is required'
            },
            "ptlPackageType": {
                required: 'Package type is required'  
            },
            "ptlLength": {
                required: 'Length is required',
               
                accept: "Only numbers are allowed"
            },
            "ptlWidth": {
                required: 'Width is required',
               
                accept: "Only numbers are allowed"
            },
            "ptlHeight": {
                required: 'Height is required',
              
                accept: "Only numbers are allowed"
            },
            "ptlCheckVolWeight": {
                required: 'Select Length Unit'
            },
            "ptlUnitsWeight": {
                required: 'Units Weight is required',
               
                accept: "Only numbers are allowed"
            },
            "ptlCheckUnitWeight": {
                required: 'Weight Unit is required'
            },
            "ptlNopackages": {
                required: 'No of Packages is required'
            },
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });
    
}else if($('#Service_ID').val()==21){
    $("#ptlBuyerQuotelineitemsForm").validate({
        ignore: [],
        rules: {
            "ptlFromLocation": {
                required: true,
            },
            "ptlFromLocationId": {
                required: true,
            },
            
            "ptlToLocation": {
                required: true,
            },
            "post_delivery_type": {
                required: true,
            },
            "ptlpurposesType": {
            	required : {
	            	depends: function(element) {
	            		if ($('#courier_types').val() == 2 || $('#courier_types').val() == ''){
		            		return true;
		            	}else{
		            		return false;
		            	}

	            	}
                },
            },
            "packeagevalue": {
                number: true,
                required: true,
            },
            "courier_types": {
                required: true,
            },
            "ptlToLocationId": {
                required: true,
            },
            
            "ptlDispatchDate": {
                required: true
            },
            "ptlLengthCourier": {
                number: true,
            	required : {
	            	depends: function(element) {
	            		if ($('#courier_types').val() == 2 || $('#courier_types').val() == ''){
		            		return true;
		            	}else{
		            		return false;
		            	}

	            	}
                }
            },
            "ptlWidthCourier": {
                number: true,
            	required : {
	            	depends: function(element) {
	            		if ($('#courier_types').val() == 2 || $('#courier_types').val() == ''){
		            		return true;
		            	}else{
		            		return false;
		            	}

	            	}
                }
            },
            "ptlHeightCourier": {
                number: true,
            	required : {
	            	depends: function(element) {
	            		if ($('#courier_types').val() == 2 || $('#courier_types').val() == ''){
		            		return true;
		            	}else{
		            		return false;
		            	}

	            	}
                }
            },
            "ptlCheckVolWeightCourier": {
            	required : {
	            	depends: function(element) {
	            		if ($('#courier_types').val() == 2 || $('#courier_types').val() == ''){
		            		return true;
		            	}else{
		            		return false;
		            	}

	            	}
                },
            },
            "ptlUnitsWeight": {
                required: true,
                number: true,
                floatvalidation: true,
            },
            "ptlCheckUnitWeight": {
                required: true
            },
            "ptlNopackages": {
                required: true,
                digits: true,
                //intergervalidation: true,
            },
        },
        errorPlacement: function(error, element) {
            $(element).parent().parent().append(error);
        },
        messages: {
            "ptlFromLocation": {
                required: '',
            },
            "ptlFromLocationId": {
                required: 'From location is required',
            },
            "ptlToLocation": {
                required: '',
            },
            "ptlToLocationId": {
                required: 'To location is required',
            },
            "ptlDispatchDate": {
                required: 'Ddispatch date is required'
            },
            "ptlLoadType": {
                required: 'Load type is required'
            },
            "ptlPackageType": {
                required: 'Package type is required'
            },
            "ptlLength": {
                required: 'Length is required',
                accept: "Only numbers are allowed"
            },
            "ptlWidth": {
                required: 'Width is required',
                accept: "Only numbers are allowed"
            },
            "ptlHeight": {
                required: 'Height is required',
                accept: "Only numbers are allowed"
            },
            "ptlCheckVolWeight": {
                required: 'Length Unit is required'
            },
            "ptlUnitsWeight": {
                required: 'Units weight is required',
                accept: "Only numbers are allowed"
            },
            "ptlCheckUnitWeight": {
                required: 'Weight Unit is required'
            },
            "ptlNopackages": {
                required: 'No of packages is required'
            },
            "ptlLengthCourier": {
                required: 'Length is required'
            },
            "ptlWidthCourier": {
                required: 'Width is required'
            },
            "ptlHeightCourier": {
                required: 'Height is required'
            }, 
            "ptlCheckVolWeightCourier": {
                required: "Length's Unit is required"
            },
            "packeagevalue": {
                required: 'Package value is required'
            }, 
            "courier_types": {
                required: 'Courier type is required'
            },
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });
    }
    else{
    $("#ptlBuyerQuotelineitemsForm").validate({
        ignore: [],
        rules: {
            "ptlFromLocation": {
                required: true,
               // number: true
            },
            "ptlFromLocationId": {
                required: true,
            },
            
            "ptlToLocation": {
                required: true,
               // number: true
            },
            "post_delivery_type": {
                required: true,
               // number: true
            },
            "packeagevalue": {
                required: true,
               // number: true
            },
            "courier_types": {
                required: true,
               // number: true
            },
            "ptlToLocationId": {
                required: true,
            },
            
            "ptlDispatchDate": {
                required: true
            },
            //"ptlDeliveryhDate": {
             //   required: true
           // },           
            
            "ptlLoadType" : {
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
            
            "ptlPackageType": {
                required: true
            },
            "ptlLength": {
                required: true,
                number: true,
                fourbythreevalidations:true,
            },
            "ptlWidth": {
                required: true,  
                number: true,
                fourbythreevalidations:true,
            },
            "ptlHeight": {
                required: true,
                number: true,
                fourbythreevalidations:true,
            },
            "ptlCheckVolWeight": {
                required: true
            },
            "ptlUnitsWeight": {
                required: true,
                number: true
            },
            "ptlCheckUnitWeight": {
                required: true
            },
            "ptlNopackages": {
                required: true,
                digits: true,
                //intergervalidation: true,
            },
        },
        errorPlacement: function(error, element) {
            $(element).parent().parent().append(error);
        },
        messages: {
            "ptlFromLocation": {
                required: '',
            },
            "ptlFromLocationId": {
                required: 'From pincode is required',
            },
            "ptlToLocation": {
                required: '',
            },
            "ptlToLocationId": {
                required: 'To pincode is required',
            },
            "ptlDispatchDate": {
                required: 'Dispatch date is required'
            },
            "ptlLoadType": {
                required: 'Load type is required'
            },
            "ptlPackageType": {
                required: 'Package type is required'
            },
            "ptlLength": {
                required: 'Length is required',
                accept: "Only numbers are allowed"
            },
            "ptlWidth": {
                required: 'Width is required',
                accept: "Only numbers are allowed"
            },
            "ptlHeight": {
                required: 'Height is required',
                accept: "Only numbers are allowed"
            },
            "ptlCheckVolWeight": {
                required: 'Length Unit is required'
            },
            "ptlUnitsWeight": {
                required: 'Units weight is required',
                accept: "Only numbers are allowed"
            },
            "ptlCheckUnitWeight": {
                required: 'Weight Unit is required'
            },
            "ptlNopackages": {
                required: 'No of packages is required'
            },
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });
    }

// End Get quote validations script
    
    // Psot private and Post public functionality in PTL buyerquote creation      

    $('.ptlcreate-posttype').click(function() {
    	var id = $('.ptlRequestRows').children().size();
    	var postingId = $(".ptlcreate-posttype:checked").val();
    	if (!id) {
    		$("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
                show: true
            });            
            $('#post-public').attr('checked', true);
            $("#hideseller").css("display", "none");
            return false;
    	}
    	if (postingId == 1) {
    		$("#hideseller").css("display", "none");
    	} else if (postingId == 2) {
   		 $("#hideseller").css("display", "block");
         $.ajax({
             url: '/getPtlSellerList',
             type: "post",
             data: {
                 'seller_list': seller_id_list,
                 '_token': $('input[name=_token]').val()
             },
             success: function(data) {
                 if (data!=null & data!='') {
                     $(".token-input-list").remove();
                     $("#demo-input-local").tokenInput(data);
                 } else {
                	 $("#hideseller").css("display", "none");                     
                     $("#erroralertmodal .modal-body").html("No sellers available.");
    		         $("#erroralertmodal").modal({
    		             show: true
    		         });                    
                     $('#post-public').attr('checked', true);
                     $('#post-private').attr('checked', false);
                     return false;
                 }
             },
             error: function(request, status, error) {
            	 $('#post-public').attr('checked', true);
                 $("#hideseller").css("display", "none");
                 alert(error);
             },
         });	 
	 }
    	
    
    });
    
    $('.crete-relocation').click(function() {
    	
    	var postingId = $(".crete-relocation:checked").val();
    	
    	if (postingId == 1) {
    		$("#hideseller").css("display", "none");
    	} else if (postingId == 2) {
   		 $("#hideseller").css("display", "block");
         $.ajax({
             url: '/getSellerslist',
             type: "post",
             data: {
                 //'seller_list': seller_id_list,
                 '_token': $('input[name=_token]').val()
             },
             success: function(data) {
                 if (data!=null & data!='') {
                     $(".token-input-list").remove();
                     $("#demo-input-local").tokenInput(data);
                 } else {
                	 $("#hideseller").css("display", "none");                     
                     $("#erroralertmodal .modal-body").html("No sellers available.");
    		         $("#erroralertmodal").modal({
    		             show: true
    		         });                    
                     $('#post-public').attr('checked', true);
                     $('#post-private').attr('checked', false);
                     return false;
                 }
             },
             error: function(request, status, error) {
            	 $('#post-public').attr('checked', true);
                 $("#hideseller").css("display", "none");
                 alert(error);
             },
         });	 
	 }
    	
    
    });

/*** Code Started  28042016  by Jagadeesh 
Change the code to fix search Getting issue ***/

var ptlDispatchDate_a = new Array();
var ptlDeliveryhDate_a = new Array();
var ptlFromLocation_a = new Array();
var ptlToLocation_a = new Array();
var fromlocationName_a = new Array();
var tolocationName_a = new Array();
var ptlLoadType_a = new Array();
var ptlPackageType_a = new Array();
var ptlLength_a = new Array();
var ptlWidth_a = new Array();
var ptlHeight_a = new Array();
var ptlCheckVolWeight_a = new Array();
var ptlDisplayVolumeWeight_a = new Array();
var ptlUnitsWeight_a = new Array();
var ptlFlexiableDispatch_a = new Array();
var ptlDoorpickup_a = new Array();
var ptlFlexiableDelivery_a = new Array();
var ptlDoorDelivery_a = new Array();
var ptlCheckUnitWeight_a = new Array();
var ptlNopackages_a = new Array();
var ptlLoadTypeName_a = new Array();
var ptlShipmentType_a = new Array();
var ptlIECode_a = new Array();
var ptlSenderIdentity_a = new Array();
var ptlProductMade_a = new Array();
var new_row_a = new Array();
var ptlLengthCourier_a = new Array();
var ptlWidthCourier_a = new Array();
var ptlHeightCourier_a = new Array();
var ptlCheckVolWeightCourier_a = new Array();
var ptlPurposesType_a = new Array();
var ptlPackageType_a = new Array();
var ptlLoadType_a = new Array();
var packeagevalue_a = new Array();
var post_delivery_types_a = new Array();
var courier_types_a = new Array();
function searchCheckPassingValues(request_array,request_value){
   // if($.inArray(request_value, request_array)==-1){
        request_array.push(request_value);
        //request_array [] = request_value;
        //console.log(request_array);
   // }
} 

function arrayTostringValue(request_array){
    return request_array.join('|');
    //console.log(request_array.join('|'));
}
/*** Code Ended   28042016  by Jagadeesh ***/

    // Add more items script for in ptl buyer quote
    var seller_id_list = new Array();
    $('#ptlAddMoreItems').click(function() {
    	//alert("cvbv");return false;
                $("#ptlBuyerQuotelineitemsForm").validate().cancelSubmit = false; 
                // Validation for add more in after first time
                var num = parseInt($('#ptlBuyerAddMoreItems').val()) + 1;
                $('#ptlBuyerAddMoreItems').val(num);
                if($('#Service_ID').val()==8 || $('#Service_ID').val()==9){
                    var ptlFromLocation = $('#from_airport_id').val();
                    var ptlToLocation = $('#to_airport_id').val();
                    var ptlFromLocationDisplay = $('#from_airport').val();
                    var ptlToLocationdisplay = $('#to_airport').val();
                    
                    var ptlShipmentType = $('#ptlShipmentType').val();
                    var ptlIECode = $('#ptlIECode').val();
                    var ptlSenderIdentity = $('#ptlSenderIdentity').val();
                    var ptlProductMade = $('#ptlProductMade').val();
                }else if($('#Service_ID').val()==21){
                	var post_delivery_type_value = $('#post_delivery_type').val();
                	var courier_types_value = $('#courier_types').val();
                	var packeagevalue = $('#packeagevalue').val();
                	var ptlFromLocation = $('#ptlFromLocationId').val();
                    var ptlToLocation = $('#ptlToLocationId').val();
                    var ptlFromLocationDisplay = $('#ptlFromLocation').val();
                    var ptlToLocationdisplay = $('#ptlToLocation').val();
                    var ptlLengthCourier = $('#ptlLengthCourier').val();
                    var ptlWidthCourier = $('#ptlWidthCourier').val();
                    var ptlHeightCourier = $('#ptlHeightCourier').val();
                    var ptlCheckVolWeightCourier = $('#ptlCheckVolWeightCourier').val();
                    var ptlPurposesType = $('#ptlPurposesType').val();
                }else{
                    var ptlFromLocation = $('#ptlFromLocationId').val();
                    var ptlToLocation = $('#ptlToLocationId').val();
                    var ptlFromLocationDisplay = $('#ptlFromLocation').val();
                    var ptlToLocationdisplay = $('#ptlToLocation').val();
                }
                var ptlDispatchDate = $('#ptlDispatchDate').val();
                var ptlDeliveryhDate = $('#ptlDeliveryhDate').val();
                var ptlLoadTypeName = $("#ptlLoadType option:selected").text();
                var ptlLoadType = $('#ptlLoadType').val();
                var ptlPackageTypeName = $("#ptlPackageType option:selected").text();
                var ptlPackageType = $('#ptlPackageType').val();
                var ptlDisplayVolumeWeight = $('#ptlDisplayVolumeWeight').val();
                var ptlCheckUnitWeightdisplay = $("#ptlCheckUnitWeight option:selected").text();
                var ptlUnitsWeight = $('#ptlUnitsWeight').val();
//                if ($('#ptlFlexiableDispatch_hidden').prop('checked')) {
//                    var ptlFlexiableDispatch = 1;
//                } else {
//                    var ptlFlexiableDispatch = 0;
//                }
                if($('#Service_ID').val()!=8 && $('#Service_ID').val()!=9){
                if ($('#ptlDoorpickup').prop('checked')) {
                    var ptlDoorpickup = 1;
                } else {
                    var ptlDoorpickup = 0;
                }
                if ($('#ptlDoorDelivery').prop('checked')) {
                    var ptlDoorDelivery = 1;
                } else {
                    var ptlDoorDelivery = 0;
                }
            }
//                if ($('#ptlFlexiableDelivery_hidden').prop('checked')) {
//                    var ptlFlexiableDelivery = 1;
//                } else {
//                    var ptlFlexiableDelivery = 0;
//                }
                var ptlFlexiableDispatch = $('#ptlFlexiableDispatch_hidden').val();
                var ptlFlexiableDelivery = $('#ptlFlexiableDelivery_hidden').val();
                var ptlLength = $('#ptlLength').val();
                var ptlWidth = $('#ptlWidth').val();
                var ptlHeight = $('#ptlHeight').val();
                var ptlCheckVolWeight = $('#ptlCheckVolWeight').val();
                var ptlCheckUnitWeight = $('#ptlCheckUnitWeight').val();
                var ptlNopackages = $('#ptlNopackages').val();
                var ptlshipmenttype = $('#ptlShipmentType').val();
                var ptlsenderidentity = $('#ptlSenderIdentity').val();
                var update=0;
                
                if($("#update_ptl_line").val()==1){
            		
            		$('.request_row_' + $("#update_row_count_ptl").val()).remove();
            		$("#update_ptl_line").val("");
            		update=$("#update_location_divcount").val();
            		
                	}
                
                if ($('#Service_ID').val()==21) {
                	var courier_selection = ptlDispatchDate != ''  && courier_types_value != '' && packeagevalue != '' && ptlFromLocation != '' && ptlToLocation != '' && ptlLoadType != '' && ptlPackageType != '' && ptlLength != '' && ptlWidth != '' && ptlHeight != '' && ptlUnitsWeight !='' && ptlCheckVolWeight != '' && ptlCheckUnitWeight != '' && ptlNopackages != ''&& ptlshipmenttype != '' && ptlsenderidentity != '';
                }else{
                	var courier_selection = ptlDispatchDate != ''  && ptlFromLocation != '' && ptlToLocation != '' && ptlLoadType != '' && ptlPackageType != '' && ptlLength != '' && ptlWidth != '' && ptlHeight != '' && ptlUnitsWeight !='' && ptlCheckVolWeight != '' && ptlCheckUnitWeight != '' && ptlNopackages != ''&& ptlshipmenttype != '' && ptlsenderidentity != '';
                }
                if (courier_selection && $("#ptlBuyerQuotelineitemsForm").valid()) {
                    $('#error-ptl-add-item').text('');
                    var seller_location_id = ptlFromLocation;
                    seller_id_list.unshift(seller_location_id);
                    var checkSearchValue = $('#ptlBuyerSearchCompareId').val(); //This condition cheked for search or insert                    

                    if(checkSearchValue == 2) {
                    	

                    	//alert("search here");
                    	var html = '<div class="table-row inner-block-bg request_row_' + num + '">';

                        if($('#Service_ID').val()==21){
                            if(courier_types_value == 2){
                            if(ptlPurposesType == 1){
                                var ptlPurposesType_value = "Personal";
                            }else{
                                var ptlPurposesType_value = "Commercial";
                            }
                            }else{
                                var ptlPurposesType_value = "NA";
                            }
                        var html = html+'<div class="col-md-2 padding-none">' + ptlPurposesType_value + '</div>';
                        }
                    	if($('#Service_ID').val()!=21){                    	
                    	var html = html+'<div class="col-md-2 padding-left-none">' + ptlPackageTypeName + '</div>';
                    	var html = html+'<div class="col-md-2 padding-left-none">' + ptlLoadTypeName + '</div>';
                    	}
                    	if($('#Service_ID').val()==21){
                    		if(courier_types_value == 2){
                    			var html = html+'<div class="col-md-2 padding-left-none">' + ptlDisplayVolumeWeight + '</div>';
                    		}else{
                    			var ptlDisplayVolumeWeight = 'NA';
                    			var html = html+'<div class="col-md-2 padding-left-none">' + ptlDisplayVolumeWeight + '</div>';
                    		}
                    	}else{
                            var html = html+'<div class="col-md-2 padding-left-none">' + ptlDisplayVolumeWeight + '</div>';
                        }
                    	var html = html+'<div class="col-md-2 padding-left-none">' + ptlUnitsWeight + ptlCheckUnitWeightdisplay + '</div>';
                    	var html = html+'<div class="col-md-2 padding-none">' + ptlNopackages + '</div>';
                    	if($('#Service_ID').val()==21){
                    		if(ptlPurposesType == 1){
                    			var ptlPurposesType_value = "Personal";
                    		}else{
                    			var ptlPurposesType_value = "Commercial";
                    		}
                        var html = html+'<div class="col-md-2 padding-none">' + packeagevalue + '</div>';
                        }
/*                      var html = html+'<input type="hidden" name="ptlDispatchDate[]"  value="' + ptlDispatchDate + '">';
                        var html = html+'<input type="hidden" name="ptlDeliveryhDate[]" value="' + ptlDeliveryhDate + '">';
                        var html = html+'<input type="hidden" name="ptlFromLocation[]"  value="' + ptlFromLocation + '"><input type="hidden" name="ptlToLocation[]"  value="' + ptlToLocation + '">';
                        var html = html+'<input type="hidden" name="fromlocationName[]"  value="' + ptlFromLocationDisplay + '"><input type="hidden" name="tolocationName[]"  value="' + ptlToLocationdisplay + '">';
*/
                        /*** Code Started  28042016  by Jagadeesh 
                        Change the code to fix search Getting issue ***/

                        searchCheckPassingValues(ptlDispatchDate_a,ptlDispatchDate);
                        searchCheckPassingValues(ptlDeliveryhDate_a,ptlDeliveryhDate);
                        searchCheckPassingValues(ptlFromLocation_a,ptlFromLocation);
                        searchCheckPassingValues(ptlToLocation_a,ptlToLocation);
                        searchCheckPassingValues(fromlocationName_a,ptlFromLocationDisplay);
                        searchCheckPassingValues(tolocationName_a,ptlToLocationdisplay);

                        /*** Code Ended   28042016  by Jagadeesh ***/
                        if($('#Service_ID').val()!=21){
                        //var html = html+'<input type="hidden" name="ptlLoadType[]" value="' + ptlLoadType + '"><input type="hidden" name="ptlPackageType[]" value="' + ptlPackageType + '">';
                            /*** Code Started  28042016  by Jagadeesh 
                            Change the code to fix search Getting issue ***/
                            searchCheckPassingValues(ptlLoadType_a,ptlLoadType);
                            searchCheckPassingValues(ptlPackageType_a,ptlPackageType);
                            /*** Code Ended   28042016  by Jagadeesh ***/
                        }
                        if($('#Service_ID').val()==21){
/*                        var html = html+'<input type="hidden" name="ptlLengthCourier[]" value="' + ptlLengthCourier + '">';
                        var html = html+'<input type="hidden" name="ptlWidthCourier[]" value="' + ptlWidthCourier + '">';
                        var html = html+'<input type="hidden" name="ptlHeightCourier[]" value="' + ptlHeightCourier + '">';
                        var html = html+'<input type="hidden" name="ptlCheckVolWeightCourier[]" value="' + ptlCheckVolWeightCourier + '">';
                        var html = html+'<input type="hidden" name="ptlPurposesType[]" id="ptlPurposesType'+num+'" value="' + ptlPurposesType + '">';
*/
                            /*** Code Started  28042016  by Jagadeesh 
                            Change the code to fix search Getting issue ***/
                            searchCheckPassingValues(ptlLengthCourier_a,ptlLengthCourier);
                            searchCheckPassingValues(ptlWidthCourier_a,ptlWidthCourier);
                            searchCheckPassingValues(ptlHeightCourier_a,ptlHeightCourier);
                            searchCheckPassingValues(ptlCheckVolWeightCourier_a,ptlCheckVolWeightCourier);
                            searchCheckPassingValues(ptlPurposesType_a,ptlPurposesType);
                            /*** Code Ended   28042016  by Jagadeesh ***/
                        }else{
/*                            var html = html+'<input type="hidden" name="ptlLength[]" value="' + ptlLength + '">';
                            var html = html+'<input type="hidden" name="ptlWidth[]" value="' + ptlWidth + '">';
                            var html = html+'<input type="hidden" name="ptlHeight[]" value="' + ptlHeight + '">';
                            var html = html+'<input type="hidden" name="ptlCheckVolWeight[]" value="' + ptlCheckVolWeight + '">';
*/
                            /*** Code Started  28042016  by Jagadeesh 
                            Change the code to fix search Getting issue ***/
                            searchCheckPassingValues(ptlLength_a,ptlLength);
                            searchCheckPassingValues(ptlWidth_a,ptlWidth);
                            searchCheckPassingValues(ptlHeight_a,ptlHeight);
                            searchCheckPassingValues(ptlCheckVolWeight_a,ptlCheckVolWeight);
                            /*** Code Ended   28042016  by Jagadeesh ***/
                        }
                        if($('#Service_ID').val() == 21){
/*                          var html = html+'<input type="hidden" name="packeagevalue[]" value="' + packeagevalue + '">';
                            var html = html+'<input type="hidden" name="post_delivery_types[]" value="' + post_delivery_type_value + '">';
                            var html = html+'<input type="hidden" name="courier_types[]" value="' + courier_types_value + '">';
*/
                            /*** Code Started  28042016  by Jagadeesh 
                            Change the code to fix search Getting issue ***/
                            searchCheckPassingValues(packeagevalue_a,packeagevalue);
                            searchCheckPassingValues(post_delivery_types_a,post_delivery_type_value);
                            searchCheckPassingValues(courier_types_a,courier_types_value);
                            /*** Code Ended   28042016  by Jagadeesh ***/
                        }
/*                      var html = html+'<input type="hidden" name="ptlDisplayVolumeWeight[]" id="ptlDisplayVolumeWeight_' + num + '"  value="' + ptlDisplayVolumeWeight + '"><input type="hidden" name="ptlUnitsWeight[]" value="' + ptlUnitsWeight + '"><input type="hidden" name="ptlFlexiableDispatch[]" value="' + ptlFlexiableDispatch + '"><input type="hidden" name="ptlDoorpickup[]" value="' + ptlDoorpickup + '"><input type="hidden" name="ptlFlexiableDelivery[]" value="' + ptlFlexiableDelivery + '"><input type="hidden" name="ptlDoorDelivery[]" value="' + ptlDoorDelivery + '">';
                        
                        var html = html+'<input type="hidden" name="ptlCheckUnitWeight[]" value="' + ptlCheckUnitWeight + '">';
                        var html = html+'<input type="hidden" name="ptlNopackages[]" value="' + ptlNopackages + '"><input type="hidden" name="ptlLoadTypeName[]" value="' + ptlLoadTypeName + '"><input type="hidden" name="ptlShipmentType[]"  value="' + ptlShipmentType + '"><input type="hidden" name="ptlIECode[]"  value="' + ptlIECode + '"><input type="hidden" name="ptlSenderIdentity[]"  value="' + ptlSenderIdentity + '"><input type="hidden" name="ptlProductMade[]"  value="' + ptlProductMade + '"></div>';
*/
                            /*** Code Started  28042016  by Jagadeesh 
                            Change the code to fix search Getting issue ***/
                            searchCheckPassingValues(ptlDisplayVolumeWeight_a,ptlDisplayVolumeWeight);
                            searchCheckPassingValues(ptlCheckUnitWeight_a,ptlCheckUnitWeight);
                            searchCheckPassingValues(ptlNopackages_a,ptlNopackages);
                            searchCheckPassingValues(ptlUnitsWeight_a,ptlUnitsWeight);
                            searchCheckPassingValues(ptlFlexiableDispatch_a,ptlFlexiableDispatch);
                            searchCheckPassingValues(ptlDoorpickup_a,ptlDoorpickup);
                            searchCheckPassingValues(ptlFlexiableDelivery_a,ptlFlexiableDelivery);
                            searchCheckPassingValues(ptlDoorDelivery_a,ptlDoorDelivery);
                            searchCheckPassingValues(ptlLoadTypeName_a,ptlLoadTypeName);
                            searchCheckPassingValues(ptlShipmentType_a,ptlShipmentType);
                            searchCheckPassingValues(ptlIECode_a,ptlIECode);
                            searchCheckPassingValues(ptlSenderIdentity_a,ptlSenderIdentity);
                            searchCheckPassingValues(ptlProductMade_a,ptlProductMade);

                            /*** Code Ended   28042016  by Jagadeesh ***/


                        if($('input[name=sea_ptlDispatchDate]').length==0){
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlDispatchDate_a)+'" name="sea_ptlDispatchDate">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlDeliveryhDate_a)+'" name="sea_ptlDeliveryhDate">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlFromLocation_a)+'" name="sea_ptlFromLocation">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlToLocation_a)+'" name="sea_ptlToLocation">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(fromlocationName_a)+'" name="sea_fromlocationName">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(tolocationName_a)+'" name="sea_tolocationName">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlLoadType_a)+'" name="sea_ptlLoadType">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlPackageType_a)+'" name="sea_ptlPackageType">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlLength_a)+'" name="sea_ptlLength">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlWidth_a)+'" name="sea_ptlWidth">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlHeight_a)+'" name="sea_ptlHeight">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlCheckVolWeight_a)+'" name="sea_ptlCheckVolWeight">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlDisplayVolumeWeight_a)+'" id="ptlDisplayVolumeWeight_2" name="sea_ptlDisplayVolumeWeight">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlUnitsWeight_a)+'" name="sea_ptlUnitsWeight">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlFlexiableDispatch_a)+'" name="sea_ptlFlexiableDispatch">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlDoorpickup_a)+'" name="sea_ptlDoorpickup">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlFlexiableDelivery_a)+'" name="sea_ptlFlexiableDelivery">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlDoorDelivery_a)+'" name="sea_ptlDoorDelivery">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlCheckUnitWeight_a)+'" name="sea_ptlCheckUnitWeight">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlNopackages_a)+'" name="sea_ptlNopackages">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlLoadTypeName_a)+'" name="sea_ptlLoadTypeName">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlShipmentType_a)+'" name="sea_ptlShipmentType">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlIECode_a)+'" name="sea_ptlIECode">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlSenderIdentity_a)+'" name="sea_ptlSenderIdentity">';
                            var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlProductMade_a)+'" name="sea_ptlProductMade">';
                            if($('#Service_ID').val() == 21){
                                var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlLengthCourier_a)+'" name="sea_ptlLengthCourier">';
                                var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlWidthCourier_a)+'" name="sea_ptlWidthCourier">';
                                var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlHeightCourier_a)+'" name="sea_ptlHeightCourier">';
                                var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlCheckVolWeightCourier_a)+'" name="sea_ptlCheckVolWeightCourier">';
                                var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlPurposesType_a)+'" name="sea_ptlPurposesType">';
                                var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlPackageType_a)+'" name="sea_ptlPackageType">';
                                var html = html+'<input type="hidden" value="'+arrayTostringValue(ptlLoadType_a)+'" name="sea_ptlLoadType">';
                                var html = html+'<input type="hidden" value="'+arrayTostringValue(packeagevalue_a)+'" name="sea_packeagevalue">';
                                var html = html+'<input type="hidden" value="'+arrayTostringValue(post_delivery_types_a)+'" name="sea_post_delivery_types">';
                                var html = html+'<input type="hidden" value="'+arrayTostringValue(courier_types_a)+'" name="sea_courier_types">';
                            }
                        }else{
                            $('input[name=sea_ptlDispatchDate]').val(arrayTostringValue(ptlDispatchDate_a));
                            $('input[name=sea_ptlDeliveryhDate]').val(arrayTostringValue(ptlDeliveryhDate_a));
                            $('input[name=sea_ptlFromLocation]').val(arrayTostringValue(ptlFromLocation_a));
                            $('input[name=sea_ptlToLocation]').val(arrayTostringValue(ptlToLocation_a));
                            $('input[name=sea_fromlocationName]').val(arrayTostringValue(fromlocationName_a));
                            $('input[name=sea_tolocationName]').val(arrayTostringValue(tolocationName_a));
                            $('input[name=sea_ptlLoadType]').val(arrayTostringValue(ptlLoadType_a));
                            $('input[name=sea_ptlPackageType]').val(arrayTostringValue(ptlPackageType_a));
                            $('input[name=sea_ptlLength]').val(arrayTostringValue(ptlLength_a));
                            $('input[name=sea_ptlWidth]').val(arrayTostringValue(ptlWidth_a));
                            $('input[name=sea_ptlHeight]').val(arrayTostringValue(ptlHeight_a));
                            $('input[name=sea_ptlCheckVolWeight]').val(arrayTostringValue(ptlCheckVolWeight_a));
                            $('input[name=sea_ptlDisplayVolumeWeight]').val(arrayTostringValue(ptlDisplayVolumeWeight_a));
                            $('input[name=sea_ptlUnitsWeight]').val(arrayTostringValue(ptlUnitsWeight_a));
                            $('input[name=sea_ptlFlexiableDispatch]').val(arrayTostringValue(ptlFlexiableDispatch_a));
                            $('input[name=sea_ptlDoorpickup]').val(arrayTostringValue(ptlDoorpickup_a));
                            $('input[name=sea_ptlFlexiableDelivery]').val(arrayTostringValue(ptlFlexiableDelivery_a));
                            $('input[name=sea_ptlDoorDelivery]').val(arrayTostringValue(ptlDoorDelivery_a));
                            $('input[name=sea_ptlCheckUnitWeight]').val(arrayTostringValue(ptlCheckUnitWeight_a));
                            $('input[name=sea_ptlNopackages]').val(arrayTostringValue(ptlNopackages_a));
                            $('input[name=sea_ptlLoadTypeName]').val(arrayTostringValue(ptlLoadTypeName_a));
                            $('input[name=sea_ptlShipmentType]').val(arrayTostringValue(ptlShipmentType_a));
                            $('input[name=sea_ptlIECode]').val(arrayTostringValue(ptlIECode_a));
                            $('input[name=sea_ptlSenderIdentity]').val(arrayTostringValue(ptlSenderIdentity_a));
                            $('input[name=sea_ptlProductMade]').val(arrayTostringValue(ptlProductMade_a));
                            if($('#Service_ID').val() == 21){
	                            $('input[name=sea_ptlLengthCourier]').val(arrayTostringValue(ptlLengthCourier_a));
	                            $('input[name=sea_ptlWidthCourier]').val(arrayTostringValue(ptlWidthCourier_a));
	                            $('input[name=sea_ptlHeightCourier]').val(arrayTostringValue(ptlHeightCourier_a));
	                            $('input[name=sea_ptlCheckVolWeightCourier]').val(arrayTostringValue(ptlCheckVolWeightCourier_a));
	                            $('input[name=sea_ptlPurposesType]').val(arrayTostringValue(ptlPurposesType_a));
	                            $('input[name=sea_ptlPackageType]').val(arrayTostringValue(ptlPackageType_a));
	                            $('input[name=sea_ptlLoadType]').val(arrayTostringValue(ptlLoadType_a));
	                            $('input[name=sea_packeagevalue]').val(arrayTostringValue(packeagevalue_a));
	                            $('input[name=sea_post_delivery_types]').val(arrayTostringValue(post_delivery_types_a));
	                            $('input[name=sea_courier_types]').val(arrayTostringValue(courier_types_a));
                            }
                        }
                    } else {
                    	//alert("inserrt here");
                    	var html = '<div class="table-row inner-block-bg request_row_' + num + '">';
                    	if($('#Service_ID').val()!=21){
                    	var html = html+'<div class="col-md-2 padding-left-none line-height">' + ptlLoadTypeName + '</div>';
                    	var html = html+'<div class="col-md-2 padding-left-none line-height">' + ptlPackageTypeName + '</div>';
                    	}
                    	if($('#Service_ID').val()==21){
                    		if(courier_types_value == 2){
                    		if(ptlPurposesType == 1){
                    			var ptlPurposesType_value = "Personal";
                    		}else{
                    			var ptlPurposesType_value = "Commercial";
                    		}
                    		}else{
                    			var ptlPurposesType_value = "NA";
                    		}
                        var html = html+'<div class="col-md-2 padding-none">' + ptlPurposesType_value + '</div>';
                        }
                    	if($('#Service_ID').val()==21){
                    		if(courier_types_value == 2){
                    			var html = html+'<div class="col-md-2 padding-left-none">' + ptlDisplayVolumeWeight + '</div>';
                    		}else{
                    			var ptlDisplayVolumeWeight = 'NA';
                    			var html = html+'<div class="col-md-2 padding-left-none">' + ptlDisplayVolumeWeight + '</div>';
                    		}
                    	}else{
                    		var html = html+'<div class="col-md-2 padding-left-none">' + ptlDisplayVolumeWeight + '</div>';	
                    	}
                    	var html = html+'<div class="col-md-2 padding-left-none">' + ptlUnitsWeight + ptlCheckUnitWeightdisplay + '</div>';
                    	var html = html+'<div class="col-md-2 padding-none">' + ptlNopackages + '</div>';
                    	if($('#Service_ID').val()==21){
                    	var html = html+'<div class="col-md-2 padding-none">' + packeagevalue + '</div>';
                    	}
                    	var html = html+'<div class="col-md-2 padding-left-none text-center"><a class="edit_ptl_this edit" row_id="' + num + '"><i class="fa fa-edit red" title="Edit"></i></a>&nbsp;<a class="remove_this remove" row_id="' + num + '"><i class="fa fa-trash red" title="Delete"></i></a></div><input type="hidden" name="ptlDispatchDate[]"  value="' + ptlDispatchDate + '"><input type="hidden" name="ptlDeliveryhDate[]" value="' + ptlDeliveryhDate + '"><input type="hidden" name="ptlFromLocation[]" id="ptlFromLocation_'+num+'"  value="' + ptlFromLocation + '"><input type="hidden" name="ptlToLocation[]" id="ptlToLocation_'+num+'"  value="' + ptlToLocation + '">';
                    	//if($('#Service_ID').val()!=21){
                    	var html = html+'<input type="hidden" name="ptlLoadType[]" id="ptlLoadType_'+num+'" value="' + ptlLoadType + '"><input type="hidden" name="ptlPackageType[]" id="ptlPackageType_'+num+'" value="' + ptlPackageType + '">';
                    	//}
                    	var html = html+'<input type="hidden" name="ptlDisplayVolumeWeight[]" id="ptlDisplayVolumeWeight_'+num+'" value="' + ptlDisplayVolumeWeight + '">';
                    	if($('#Service_ID').val() == 21){
                    		var html = html+'<input type="hidden" name="packeagevalue[]" id="packeagevalue_'+num+'" value="' + packeagevalue + '">';
                    		var html = html+'<input type="hidden" name="post_delivery_types[]" id="post_delivery_types_'+num+'" value="' + post_delivery_type_value + '">';
                    		var html = html+'<input type="hidden" name="courier_types[]" id="courier_types_'+num+'" value="' + courier_types_value + '">';
                    	}
                    	var html = html+'<input type="hidden" name="ptlUnitsWeight[]" id="ptlUnitsWeight_'+num+'" value="' + ptlUnitsWeight + '">';
                    	var html = html+'<input type="hidden" name="ptlFlexiableDispatch[]" id="ptlFlexiableDispatch_'+num+'" value="' + ptlFlexiableDispatch + '">';
                    	var html = html+'<input type="hidden" name="ptlDoorpickup[]" id="ptlDoorpickup'+num+'" value="' + ptlDoorpickup + '">';
                    	var html = html+'<input type="hidden" name="ptlFlexiableDelivery[]" id="ptlFlexiableDelivery'+num+'" value="' + ptlFlexiableDelivery + '">';
                    	var html = html+'<input type="hidden" name="ptlDoorDelivery[]" id="ptlDoorDelivery'+num+'" value="' + ptlDoorDelivery + '">';
                    	if($('#Service_ID').val()==21){
                    	var html = html+'<input type="hidden" name="ptlLengthCourier[]" id="ptlLengthCourier'+num+'" value="' + ptlLengthCourier + '">';
                    	var html = html+'<input type="hidden" name="ptlPurposesType[]" id="ptlPurposesType'+num+'" value="' + ptlPurposesType + '">';
                    	var html = html+'<input type="hidden" name="ptlWidthCourier[]" id="ptlWidthCourier'+num+'" value="' + ptlWidthCourier + '">';
                    	var html = html+'<input type="hidden" name="ptlHeightCourier[]" id="ptlHeightCourier'+num+'" value="' + ptlHeightCourier + '">';
                    	var html = html+'<input type="hidden" name="ptlCheckVolWeightCourier[]" id="ptlCheckVolWeightCourier'+num+'" value="' + ptlCheckVolWeightCourier + '">';
                    	}else{
                    		var html = html+'<input type="hidden" name="ptlLength[]" id="ptlLength_'+num+'" value="' + ptlLength + '">';
                        	var html = html+'<input type="hidden" name="ptlWidth[]" id="ptlBreadth_'+num+'" value="' + ptlWidth + '">';
                        	var html = html+'<input type="hidden" name="ptlHeight[]" id="ptlHeight_'+num+'" value="' + ptlHeight + '">';
                        	var html = html+'<input type="hidden" name="ptlCheckVolWeight[]" id="ptlCheckVolWeight_'+num+'" value="' + ptlCheckVolWeight + '">';
                    	}
                    	var html = html+'<input type="hidden" name="ptlCheckUnitWeight[]" id="ptlCheckUnitWeight_'+num+'" value="' + ptlCheckUnitWeight + '">';
                    	var html = html+'<input type="hidden" name="ptlNopackages[]" id="ptlNopackages'+num+'" value="' + ptlNopackages + '">';
                    	var html = html+'<input type="hidden" name="ptlShipmentType[]" id="ptlShipmentType'+num+'"  value="' + ptlShipmentType + '">';
                    	var html = html+'<input type="hidden" name="ptlIECode[]" id="ptlIECode'+num+'"  value="' + ptlIECode + '">';
                    	var html = html+'<input type="hidden" name="ptlSenderIdentity[]" id="ptlSenderIdentity'+num+'"  value="' + ptlSenderIdentity + '">';
                    	var html = html+'<input type="hidden" name="ptlProductMade[]" id="ptlProductMade'+num+'"  value="' + ptlProductMade + '">';
                    	var html = html+'<input type="hidden" id="fromlocationName_'+num+'" name="fromlocationName[]"  value="' + ptlFromLocationDisplay + '"><input type="hidden" id="tolocationName_'+num+'" name="tolocationName[]"  value="' + ptlToLocationdisplay + '">';
                    	var html = html+'</div>';
                    }
                    if(update>0){
                    	//alert(update);
                    $('#ptl_add_locations_'+update+' .ptlRequestRows').last().append(html);
                    }else{
                    $('.ptlRequestRows').last().append(html);
                    }
                    // Get location names from pincodes for showing                   
                    var data = {
                        'fromPincode': ptlFromLocation,
                        'toPincode': ptlToLocation,
                    };
                    
                    $.ajax({
                        type: "GET",
                        url: '/getPinlocationInItems',
                        data: data,
                        dataType: 'text',
                        success: function(data) {
                            var myarr = data.split("~!~");
                            if(update>0){
                            	$("#ptl_add_locations_"+update+" .fromPin").html(myarr[0]);
                            	$("ptl_add_locations_"+update+" .toPin").html(myarr[1]);
                            }else{
                            	$(".fromPin").last().html(myarr[0]);
                            	$(".toPin").last().html(myarr[1]);
                            }
                        },
                        error: function(request, status, error) {
                            // alert(request.responseText);
                        },
                    });
                    var ptlLineItemsSize = $('.ptlRequestRows').children().size();
                    if (ptlLineItemsSize == 0) {} else {
                        $("#ptlDispatchDate").prop('disabled', true);
                        $("#ptlDeliveryhDate").prop('disabled', true);
                        if($('#Service_ID').val()==8 || $('#Service_ID').val()==9){
                            $("#from_airport").prop('disabled', true);
                            $("#to_airport").prop('disabled', true);
                            $("#ptlShipmentType").prop('disabled', true);
                            $("#ptlIECode").prop('disabled', true);
                            $("#ptlSenderIdentity").prop('disabled', true);
                            $("#ptlProductMade").prop('disabled', true);
                        }else if($('#Service_ID').val()==21){
                            if(update){
                                $("#ptlFromLocation").prop('disabled', false);
                                $("#ptlToLocation").prop('disabled', false); 
                                $('#ptlFromLocation').val("");
                                $('#ptlToLocation').val(""); 
                                $('#ptlFromLocationId').val("");
                                $('#ptlToLocationId').val(""); 
                                $('#domestic').prop('disabled', false);
                                $('#international').prop('disabled', false);
                                $('#documents').prop('disabled', false);
                                $('#parcel').prop('disabled', false);
                            }else{
                                $("#ptlFromLocation").prop('disabled', true);
                                $("#ptlToLocation").prop('disabled', true);  
                                $('#domestic').prop('disabled', true);
                                $('#international').prop('disabled', true);
                                $('#documents').prop('disabled', true);
                                $('#parcel').prop('disabled', true);
                            }                            
                        }else{
                        $("#ptlFromLocation").prop('disabled', true);
                        $("#ptlToLocation").prop('disabled', true);  
                        $('#domestic').prop('disabled', true);
                        $('#international').prop('disabled', true);
                    }
                        $('#ptlFlexiableDispatch_hidden').prop('disabled', true);
                        
                        $('#ptlFlexiableDelivery_hidden').prop('disabled', true);
                        $('.add-on').addClass('disable-bg');
                        
                        $('.is_commercial').prop('disabled', true);
                    }
                    // emty values set here
                    $('#ptlLength').val("");
                    $('#ptlWidth').val("");
                    $('#ptlHeight').val("");
                    if($('#Service_ID').val()==21){
                    $('#ptlLengthCourier').val("");
                    $('#ptlWidthCourier').val("");
                    $('#ptlHeightCourier').val("");
                    }
                    $('#ptlCheckVolWeight').val("");
                    $('#ptlUnitsWeight').val("");
                    $('#ptlNopackages').val("");
                    $('#ptlDisplayVolumeWeight').val("");
                    $('#ptlLoadType').val("");
                    $('#packeagevalue').val("");
                    $('#ptlPackageType').val("");
                    $('#ptlCheckUnitWeight').val("");
                    $('#displayVolumeW').html("");
                    $('.selectpicker').selectpicker('refresh');
                    return false;
                }
            });
    // Remove button for add multiple line items in ptl get quote
    $(document).on('click', '.remove_this', function() {
            var ptlRowid = $(this).attr("row_id");
            $('.request_row_' + ptlRowid).remove();
            var afterRemoveRequest = $('.ptlRequestRows').children().size();
            if (afterRemoveRequest == 0) {
                $("#ptlDispatchDate").prop('disabled', false);
                $("#ptlDeliveryhDate").prop('disabled', false);
                if($('#Service_ID').val()==8 || $('#Service_ID').val()==9){
                        $("#from_airport").prop('disabled', false);
                        $("#to_airport").prop('disabled', false);
                        $("#ptlShipmentType").prop('disabled', false);
                        $("#ptlIECode").prop('disabled', false);
                        $("#ptlSenderIdentity").prop('disabled', false);
                        $("#ptlProductMade").prop('disabled', false);
                        $('.selectpicker').selectpicker('refresh');
                }
                else if($('#Service_ID').val()==21){
                	$("#ptlFromLocation").prop('disabled', false);
                    $("#ptlToLocation").prop('disabled', false);
                    $('#post_delivery_type').val(""); 
                    $('#domestic').attr("checked", false);
                    $('#international').attr("checked", false);
                    $('#domestic').prop('disabled', false);
                    $('#international').prop('disabled', false);
                    $('#documents').prop('disabled', false);
                    $('#parcel').prop('disabled', false);
                }else{
                    $("#ptlFromLocation").prop('disabled', false);
                    $("#ptlToLocation").prop('disabled', false);
                    $('#ptlDoorpickup').prop('disabled', false);
                    $('#ptlDoorDelivery').prop('disabled', false);
                }
                $('#ptlFlexiableDispatch_hidden').prop('disabled', false);
                
                $('#ptlFlexiableDelivery_hidden').prop('disabled', false);
                
            }
        })
        
        
   $(document).on('click', '.edit_ptl_this', function() {
	   
	   var parent_id = $(this).parents('.ptl_add_locations').attr('id');
	   parent_id=parent_id.split('_');
	   var ptlRowid = $(this).attr("row_id");
	   $("#update_ptl_line").val(1);
       $("#update_row_count_ptl").val(ptlRowid);
       $("#update_location_divcount").val(parent_id[3]);
	   //$("#ptlDispatchDate").prop('disabled', false);
      // $("#ptlDeliveryhDate").prop('disabled', false);
       if($('#Service_ID').val()==8 || $('#Service_ID').val()==9){
               $("#from_airport").prop('disabled', false);
               $("#to_airport").prop('disabled', false);
               
               $('#from_airport').val($("#fromlocationName_"+ptlRowid).val());
               $('#to_airport').val($("#tolocationName_"+ptlRowid).val());
               
               $('#from_airport_id').val($("#ptlFromLocation_"+ptlRowid).val());
               $('#to_airport_id').val($("#ptlToLocation_"+ptlRowid).val());
               
               $("#ptlShipmentType").prop('disabled', false);
               $("#ptlIECode").prop('disabled', false);
               $("#ptlSenderIdentity").prop('disabled', false);
               $("#ptlProductMade").prop('disabled', false);
               $('.selectpicker').selectpicker('refresh');
       }
       else if($('#Service_ID').val()==21){
       	   $("#ptlFromLocation").prop('disabled', false);
           $("#ptlToLocation").prop('disabled', false);
           
           $('#ptlFromLocation').val($("#fromlocationName_"+ptlRowid).val());
           $('#ptlToLocation').val($("#tolocationName_"+ptlRowid).val());
           
           $('#ptlFromLocationId').val($("#ptlFromLocation_"+ptlRowid).val());
           $('#ptlToLocationId').val($("#ptlToLocation_"+ptlRowid).val());
           //commented by swathi 29-04-2016
           //$('#post_delivery_type').val(""); 
           //end comment by swathi 29-04-2016
           $('#domestic').prop('disabled', true);
           $('#international').prop('disabled', true);
       }else{
           $("#ptlFromLocation").prop('disabled', false);
           $("#ptlToLocation").prop('disabled', false);
           $('#ptlDoorpickup').prop('disabled', false);
           $('#ptlDoorDelivery').prop('disabled', false);

           $('#ptlFromLocation').val($("#fromlocationName_"+ptlRowid).val());
           $('#ptlToLocation').val($("#tolocationName_"+ptlRowid).val());
           
           $('#ptlFromLocationId').val($("#ptlFromLocation_"+ptlRowid).val());
           $('#ptlToLocationId').val($("#ptlToLocation_"+ptlRowid).val());
       }
       $('#ptlFlexiableDispatch_hidden').prop('disabled', false);
       $('#ptlFlexiableDelivery_hidden').prop('disabled', false);
       
         $('#ptlLength').val($("#ptlLength_"+ptlRowid).val());
         $('#ptlWidth').val($("#ptlBreadth_"+ptlRowid).val());
         $('#ptlHeight').val($("#ptlHeight_"+ptlRowid).val());
         $('#ptlCheckVolWeight').selectpicker('val',$("#ptlCheckVolWeight_"+ptlRowid).val());
         $('#ptlUnitsWeight').val($("#ptlUnitsWeight_"+ptlRowid).val());
         $('#ptlNopackages').val($("#ptlNopackages"+ptlRowid).val());
         $('#ptlLoadType').selectpicker('val', $("#ptlLoadType_"+ptlRowid).val());
         $('#ptlPackageType').selectpicker('val', $("#ptlPackageType_"+ptlRowid).val());
         $('#ptlCheckUnitWeight').selectpicker('val', $("#ptlCheckUnitWeight_"+ptlRowid).val());
         $('#ptlDisplayVolumeWeight').val($("#ptlDisplayVolumeWeight_"+ptlRowid).val());
         
         if($('#Service_ID').val()==8 || $('#Service_ID').val()==9){
             
             $("#ptlShipmentType").selectpicker('val', $("#ptlShipmentType"+ptlRowid).val());
             $("#ptlIECode").val($("#ptlIECode"+ptlRowid).val());
             $("#ptlSenderIdentity").selectpicker('val', $("#ptlSenderIdentity"+ptlRowid).val());
             $("#ptlProductMade").val($("#ptlProductMade"+ptlRowid).val());
          }
        if($('#Service_ID').val()==21){
        	 $('#ptlLengthCourier').val($("#ptlLengthCourier"+ptlRowid).val());
             $('#ptlWidthCourier').val($("#ptlWidthCourier"+ptlRowid).val());
             $('#ptlHeightCourier').val($("#ptlHeightCourier"+ptlRowid).val());	
             $("#packeagevalue").val($("#packeagevalue_"+ptlRowid).val());
        
        	
        } 
        
      });
        // Add New location set empty values
    $('#addNewLocations').click(
        function() {
            var ptlLineItemsSize = $('.ptlRequestRows').last().children('div').size();
            if (ptlLineItemsSize == 0) {
               // alert("Please add atleast one item to the list");
                $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
                $("#erroralertmodal").modal({
                    show: true
                });
                return false;
            } else {
                var body_locations = $('#ptl_addmore_locations div').first().html();
                $('#ptlPackageType').val("");
                $("#ptlLength").val("");
                $("#ptlWidth").val("");
                $("#ptlHeight").val("");
                $('#ptlCheckVolWeight').val("");
                $('#ptlUnitsWeight').val("");
                $('#ptlNopackages').val("");
                $('#ptlDisplayVolumeWeight').val(""); 
                $('#displayVolumeW').html("");
                var checkSearchValue = $('#ptlBuyerSearchCompareId').val();
                if($('#Service_ID').val()==8 || $('#Service_ID').val()==9){
                    $('#from_airport').val("");
                    $('#to_airport').val(""); 
                }else  if($('#Service_ID').val()==21){
                    $('#ptlFromLocation').val("");
                    $('#ptlToLocation').val(""); 
                    $('#ptlFromLocationId').val("");
                    $('#ptlToLocationId').val(""); 
                    //$('#post_delivery_type').val(""); 
                    //$('#domestic').attr("checked", false);
                    //$('#international').attr("checked", false);
                    $('#domestic').prop('disabled', false);
                    $('#international').prop('disabled', false);
                    $('#documents').prop('disabled', false);
                    $('#parcel').prop('disabled', false);
                }else{
                	$('#ptlFromLocation').val("");
                    $('#ptlToLocation').val(""); 
                }
                
                if(checkSearchValue == 2) {
                    $("#ptlDispatchDate").prop('disabled', true);
                    $("#ptlDeliveryhDate").prop('disabled', true);
                    $('#ptlFlexiableDispatch_hidden').prop('disabled', true);
                    
                    $('#ptlFlexiableDelivery_hidden').prop('disabled', true);
                    if($('#Service_ID').val()!=8 && $('#Service_ID').val()!=9){
                      //  $('#ptlDoorpickup').prop('disabled', true);
                      //  $('#ptlDoorDelivery').prop('disabled', true);
                    }
                } else {
                	//$('#ptlDispatchDate').val("");
                    //$('#ptlDeliveryhDate').val("");
                	//$('#ptlFlexiableDispatch_hidden').val("");
                    //$('#ptlFlexiableDelivery_hidden').val("");
                	
                    
                    if($('#Service_ID').val()!=8 && $('#Service_ID').val()!=9){
                        $('#ptlDoorpickup').val("");
                        $('#ptlDoorDelivery').val("");
                        $('#ptlDoorpickup').prop('disabled', false);
                        $('#ptlDoorDelivery').prop('disabled', false);
                    }
                	$("#ptlDispatchDate").prop('disabled', true);
                    $("#ptlDeliveryhDate").prop('disabled', true);
                    $('#ptlFlexiableDispatch_hidden').prop('disabled', true);
                    $('#ptlFlexiableDelivery_hidden').prop('disabled', true);
                    
                }      
                if($('#Service_ID').val()!=8 && $('#Service_ID').val()!=9){
                $("#ptlFromLocation").prop('disabled', false);
                $("#ptlToLocation").prop('disabled', false);
                $('#ptlFromLocation').val("");
                $('#ptlToLocation').val("");
                }else{
                    $("#from_airport").prop('disabled', false);
                    $("#to_airport").prop('disabled', false);
                    $('#from_airport').val("");
                    $('#to_airport').val("");
                    $("#ptlShipmentType").val("");
                    $("#ptlIECode").val("");
                    $("#ptlSenderIdentity").val("");
                    $("#ptlProductMade").val("");
                    $("#ptlShipmentType").prop('disabled', false);
                    $("#ptlIECode").prop('disabled', false);
                    $("#ptlSenderIdentity").prop('disabled', false);
                    $("#ptlProductMade").prop('disabled', false);
                }
                
                $('.selectpicker').selectpicker('refresh');
                // alert($('#ptl_addmore_locations').html());
                //alert($("#locations_count").val());
                var location_id = parseInt($("#locations_count").val())+1;
                $('#ptl_addmore_locations').append('<div class="ptl_add_locations" id="ptl_add_locations_'+location_id+'">'+body_locations+"</div>");
                $("#locations_count").val(parseInt($("#locations_count").val())+1);
                
                
                $('.ptlRequestRows').last().html('');
              //swathi
               // var inc=$('.ptl_add_locations #ptlBuyerAddMoreItems').val();
                var inc=$('#ptlBuyerAddMoreItems').val();
                $('.ptlRequestRows').last().append('<input type="hidden" value="'+inc+'" name="new_row[]" >');
                $('.fromPin').last().html('');
                $('.toPin').last().html('');
                
                $("#ptlDispatchDate").datepicker("destroy");
                $("#ptlDeliveryhDate").datepicker("destroy");
                
                $("#ptlDispatchDate").datepicker({
                    changeMonth: true,
                    numberOfMonths: 1,
                    minDate: 0,    
                    show_flexible: 1,
                    flex_identifier: "ptlFlexiableDispatch",
                    flex_text: "Flexible dates", 
                    dateFormat: "dd/mm/yy",
                    onClose: function(selectedDate) {
                        $("#ptlDeliveryhDate").datepicker(
                            "option", "minDate", selectedDate);
                    }
                });
                $("#ptlDeliveryhDate ").datepicker({
                    changeMonth: true,
                    numberOfMonths: 1,       
                    minDate: 0,
                    show_flexible: 1,
                    flex_identifier: "ptlFlexiableDelivery",
                    flex_text: "Flexible dates", 
                    dateFormat: "dd/mm/yy",
                    onClose: function(selectedDate) {
                        $("#ptlDispatchDate").datepicker("option",
                            "maxDate", selectedDate);
                    }
                });
                $('.add-on').removeClass('disable-bg');
                $("html, body").animate({ scrollTop: 0 }, "slow", function() {
                	document.getElementById("ptlFromLocation").focus();
                });
               
            }

        });
    
    
  //PTL not setted location in hidden filed-like enter wrong pincode  
    $( "#ptlFromLocation" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden_buyer = $('#ptlFromLocationId').val("");
			if (from_id_hidden_buyer != '') {
				//$(".from_location_class label.error").html("");
			}
		}
	});

	
	$( "#ptlToLocation" ).keyup(function(e) {
		if (e.which !== 13) {
			var to_id_hidden_buyer = $('#ptlToLocationId').val("");
			if (to_id_hidden_buyer != '') {
				//$(".to_location_class label.error").html("");
			}
		}
	});
	
	
	$( ".ptlTocheckbooknowLocation" ).keyup(function(e) {
		if (e.which !== 13) {
			var to_id_hidden_buyer = $('#ptlTocheckLocationId').val("");
			if (to_id_hidden_buyer != '') {
				$("label.booknowtopincodecheck").html("");
			}
		}
	});
	
	//check total price calculation in FTL buyer marlet leads 
	 $( ".checktotal_price_marketleadsftl" ).keyup(function(e) {
		 var id	=	$( this ).attr( "data-id" );
		 var total_price_calc=$('#noofloads_'+id).val()*$('#sellerprice_'+id).val();
		 $('#buyersearch_booknow_seller_price_'+id).val(total_price_calc);
		 $('.display_marketledaspriceftl_'+id).text(total_price_calc);		
	});
    
    $('#ptlAddBuyerQuote').click(function(e) {
        var id = $('.ptlRequestRows').children().size();
        if (id == 0) {
           // alert("Please add atleast one item to the list");
            $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        } else {
            $('#ptlBuyerQuoteInsert').submit();
            if($('#ptlBuyerQuoteInsert').valid()){
                    $("#ptlAddBuyerQuote").prop('disabled', true);                    
                }
            return true;
        }
    });
    // auto complete For ptl searching Pincodes
    
   $(document).on('focus click keyup keypress blur change', '#ptlFromLocation', function() {
	   
	   //console.log($('#ptlFromLocation').val());
	   //$("#ptlFromLocation").removeClass('numericvalidation_autopop');

       var service_courier_buyer = $('#Service_ID').val();
	   
	   if(service_courier_buyer == 21){
		   var service_courier_buyer_url = "/ptlPincodesAutocompleteCourier?ptlFromLocation="+$('#ptlToLocationId').val()+"&courier_delivery_type="+ $('#post_delivery_type').val()+"&to="+1;
	   }else{
		   var service_courier_buyer_url = "/ptlPincodesAutocomplete?ptlFromLocation="+$('#ptlToLocationId').val();
	   }
	   
	   $("#ptlFromLocation").autocomplete({
        source: service_courier_buyer_url,
    	minLength: 1,
        select: function(event, ui) {        	
        	$('#ptlFromLocation').val(ui.item.value);
            $('#ptlFromLocationId').val(ui.item.id);
            $(this).closest("form").validate().element($('#ptlFromLocationId'));
            /*Need to add this below class to every autocomplete: Shriram */
            $('#ptlFromLocation').addClass("clsAutoDisable");
            //$('#ptlFromLocation').removeClass("maxlimitsix_lmtVal");
            //$("#ptlFromLocation").addClass('numericvalidation_autopop');
        }
        
    }); 
    
     
});


$(document).on('focus click keyup keypress blur change', '#pincodeLocation', function() {
	   
		   var service_courier_buyer_url_to = "/pincodesAutocomplete?pincode="+$('#pincodeLocation').val();
	   
	   
                $("#pincodeLocation").autocomplete({
                   source: service_courier_buyer_url_to,
                   minLength: 1,
                   select: function(event, ui) {
                       $('#pincodeLocation').val(ui.item.value);
                       $('#pincodeLocationId').val(ui.item.id);
					   $("#lkp_state_id").val(ui.item.state_id);
						$("#lkp_location_id").val(ui.item.id);
						$("#lkp_district_id").val(ui.item.lkp_district_id);
						$("#pincode").val(ui.item.pincode);
						$("#region").val(ui.item.region);
						
						$("#business_city").val(ui.item.city);
						$("#business_state").val(ui.item.statename);
						$("#business_location").val(ui.item.postoffice_name);
						$("#business_district").val(ui.item.districtname);
						
						$("#city").val(ui.item.city);
						$("#state").val(ui.item.statename);
						$("#location").val(ui.item.postoffice_name);
						$("#district").val(ui.item.districtname);
						
                       //$(this).closest("form").validate().element($('#ptlToLocationId'));
                       /*Need to add this below class to every autocomplete: Shriram */
                       $('#pincodeLocation').addClass("clsAutoDisable");
                    }
                       
                });
    
                
            })




   $(document).on('focus click keyup keypress blur change', '#ptlToLocation', function() {
	   var service_courier_buyer = $('#Service_ID').val();
	   if(service_courier_buyer == 21){
		   var service_courier_buyer_url_to = "/ptlPincodesAutocompleteCourier?ptlFromLocation="+$('#ptlFromLocationId').val()+"&courier_delivery_type="+ $('#post_delivery_type').val()+"&to="+2;
	   }else{
		   var service_courier_buyer_url_to = "/ptlPincodesAutocomplete?ptlFromLocation="+$('#ptlFromLocationId').val();
	   }
	   
                $("#ptlToLocation").autocomplete({
                   source: service_courier_buyer_url_to,
                   minLength: 1,
                   select: function(event, ui) {
                       $('#ptlToLocation').val(ui.item.value);
                       $('#ptlToLocationId').val(ui.item.id);
                       $(this).closest("form").validate().element($('#ptlToLocationId'));
                       /*Need to add this below class to every autocomplete: Shriram */
                       $('#ptlToLocation').addClass("clsAutoDisable");
                    }
                       
                });
    
                
            });
   
   
   $(document).on('focus click keyup keypress blur change', '.ptlTocheckbooknowLocation', function() {
	   var service_courier_buyer = $('#Service_ID').val();
	   var buyer_to_districtid = $('#districtid').val();
		
	  
	   var service_courier_buyer_url_to = "/ptlToPincodesCheckout?ptlTocheckLocation="+$('#ptlTocheckLocationId').val()+"&buyer_to_districtid="+buyer_to_districtid;
	  
		   	
       $(".ptlTocheckbooknowLocation").autocomplete({
	       source: service_courier_buyer_url_to,
	       minLength: 1,
	       
	       
	       response: function(event, ui) { 
	    	   
	    	   var pinlength= $('.ptlTocheckbooknowLocation').val();
	    	   if(pinlength.length==6){
	    	    if (ui.content.length === 0) {
	    	    	var answer = confirm ("Pincode not in To Location district. Click Cancel to reenter pincode or Ok to skip this validation.");
	    	  	  if (answer)
	    	  	    {
	    	  		 $('.ptlTocheckbooknowLocation').hide();  
	    	    	 $('#ptlTocheckLocationId').val($('.ptlTocheckbooknowLocation').val());
	    	    	 $('#ptlTocheckLocationId').show();
	    	  	    }
	    	    } 
	    	   }
	    	     
	       },
	       select: function(event, ui) {
	    	   $('#ptlTocheckLocationId').val(ui.item.value);
	           $('.ptlTocheckbooknowLocation').val(ui.item.value);
	           $(this).closest("form").validate().element($('#ptlTocheckLocationId'));
	           /*Need to add this below class to every autocomplete: Shriram */
	           $('.ptlTocheckbooknowLocation').addClass("clsAutoDisable");
	           $("label.booknowtopincodecheck").html("");
	       }
           
       });	        
    });

// Dispatch and Delivery date pickers for PTL and FTl
    $("#ptlDispatchDate").datepicker({
        changeMonth: true,
        minDate: 0,
        show_flexible: 1,
        flex_identifier: "ptlFlexiableDispatch",
        flex_text: "Flexible dates",        
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            $("#ptlDeliveryhDate").datepicker(
                "option", "minDate", selectedDate);
        }
    });
    $("#ptlDeliveryhDate").datepicker({
        changeMonth: true,
        minDate: 0,
        show_flexible: 1,
        flex_identifier: "ptlFlexiableDelivery",
        flex_text: "Flexible dates",    
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            $("#ptlDispatchDate").datepicker("option",
                "maxDate", selectedDate);
        }
    });


    $('#ptlAddBuyerSearch').click(function(e) {
        var id = $('.ptlRequestRows').children().size();
        if (id == 0) {
            //alert("Please add atleast one item to the list");
            $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        } else {
            $('#ptlBuyerSearchSendValues').submit();
            return true;
        }
    });
    
  //Buyer search result details page toggle script in PTL 
    $(".ptlBuyerDetailsSlide").click(function() {
        var $sellerPostId = $(this).attr("data-ptlsellerlistid");
        $(".ptlBuyerDetailsList_" + $sellerPostId).slideToggle("500");
        datastr = '&postId=' + $(this).data('ptlsellerlistid');
        $.ajax({
            type : 'post', // defining the ajax type
            url : '/updatesellerpostview', // calling the controller with the
            // action involved
            dataType : 'html', // datatype
            data : datastr, // passing the data used for operation
            success : function(html) {                  
                    }
        });
       // $(".buyerbooknow_listdetails_" + buyerId).hide("500");
    });
    
    
 // Buyer search form date picker without condtions min date and remaing
    $("#ptlDispatchDate").datepicker({
        changeMonth: true,
        minDate: 0,
        dateFormat: "dd/mm/yy",        
        onClose: function(selectedDate) {
            $("#ptlDeliveryhDate").datepicker("option", "minDate", selectedDate);
        }
    });
    $("#ptlDeliveryhDate").datepicker({
        changeMonth: true,
        minDate: 0,      
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            $("#ptlDispatchDate").datepicker("option", "maxDate", selectedDate);
        }
    });
    
    
    /**
     * create new function for update seller view count
     * start
     * @return count
     * @srinu and 2-05-2016
     * 
     */    
     $(".viewcount_show-data-link").click(function(){        
        $(this).find(".show-icon").toggle();
        $(this).find(".hide-icon").toggle();       
        $(this).parent().parent().parent().find(".show-data-div").slideToggle(500);
        var checkStatus = $(this).children('span').attr('style');
        if(checkStatus=="display: none;") {            
            var sellerPostId=$(this).attr('data-quoteId');	            
           // var table=$(this).attr('data-table');	        
            var data = {
                    'sellerPostId': $(this).attr('data-quoteId'),                    
                };
             $.ajax({
                    type: "GET",
                    url: '/sellerViewCountUpdate',
                    data: data,
                    dataType: 'text',
                    success: function(data) {	    	                      
                    },
                    error: function(request, status, error) {    	            
                    },
                }); 
        }
    });    
        
        /**
         * End
         * @srinu and 2-05-2016
         */
    
 //In ftl buyer posts check all cancel functionality
    
    
$(".gridbuyercheckbox").click(function(e){    	
    	//e.preventDefault();
    	e.stopImmediatePropagation();    	
}); 
    
    $('#globalbuyerpostlistcheck').click(function(){
        if($(this).prop("checked")) {
            $(".checkBoxClass").prop("checked", true);
        } else {
            $(".checkBoxClass").prop("checked", false);
        }                
    });
    
    //ptl buyer post cancel
    
    $('#ptlbuyerpostcheck').click(function(){
        if($(this).prop("checked")) {
            $(".checkBoxClass").prop("checked", true);
        } else {
            $(".checkBoxClass").prop("checked", false);
        }                
    });
   
    $('#ptlAddBuyerQuote').on('click', function() {
        $("#ptlBuyerQuoteInsert").valid();
    });
    
    $("#ptlBuyerQuoteInsert").validate({
    	ignore: [],
        rules: {            
            "agree": {
                required: true,
            },
            "prohibited": {
                required: true,
            },
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
            }
        },
        errorPlacement: function(error, element) {        	
            $(element).parent().parent().append(error);
        },

        messages: {
            "agree": {
                required: "Terms & Conditions is required",
            },
            
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });
    
    //Check and atuo update no of loads
    $("#quantity").keyup(function() {    	
    	var quantity = $('#quantity').val();
    	var vehicletype = $('#vehicle_type').val();
    	CheckLoads(vehicletype);
    	//alert(quantity);
    });
    
    $(".cancel_bpost_yes").on("click",function(){
  
              
    var postid = $("#buyercancellationpostid").val(); 
    
   
    datastr = '&postIds=' + postid +'&str=' + postid;
        
        // ajax function starts
        $.ajax({
            type : 'post', // defining the ajax type
            url : '/buyerpostcancel', // calling the controller with the
            // action involved
            dataType : 'html', // datatype
            data : datastr, // passing the data used for operation
            beforeSend: function () {
                $("#cancelbuyerpostmodal").modal('hide');
                $.blockUI({
                    overlayCSS: {
                        backgroundColor: '#000'
                    }
                });
            },
            complete: function () {
                //$.unblockUI();
            },
            success : function(msg) {
                    $("#erroralertmodal .modal-body").html(msg);
                    $("#erroralertmodal").modal({
                        show: true
                    }).one('click','.ok-btn',function (e){
                        $.unblockUI();
                        location.reload();
                    });                    
                    
                    }
        });
    });

    $("#ptlLength, #ptlWidth, #ptlHeight").keyup(function() {
        var ptlLength = $('#ptlLength').val();
        var ptlWidth = $('#ptlWidth').val();
        var ptlHeight = $('#ptlHeight').val();

        var weightType = $('#ptlCheckVolWeight').val();
        if (!weightType) {
            $('#displayVolumeW').hide();
            $('#displayVolumenone').show();
            $('#ptlCheckVolWeight').val(null);
            $('#ptlCheckVolWeight').selectpicker('refresh');
            return false;
        }
        if (!isValidUnit(ptlLength)) {
            $('#ptlLength').val('');
        } else if (!isValidUnit(ptlWidth)) {
            $('#ptlWidth').val('');
        } else if (!isValidUnit(ptlHeight)) {
            $('#ptlHeight').val('');
        } else {
            weightType = $('#ptlCheckVolWeight').val();
            volumeWeight(weightType);
        }
    });
    $("#ptlLengthCourier, #ptlWidthCourier, #ptlHeightCourier").keyup(function() {
    	
    	
        var ptlLength = $('#ptlLengthCourier').val();
        var ptlWidth = $('#ptlWidthCourier').val();
        var ptlHeight = $('#ptlHeightCourier').val();
        
        //console.log(ptlLength); 

        var weightType = $('#ptlCheckVolWeightCourier').val();
        if (!weightType) {
            $('#displayVolumeW').hide();
            $('#displayVolumenone').show();
            $('#ptlCheckVolWeight').val(null);
            $('#ptlCheckVolWeight').selectpicker('refresh');
            return false;
        }
        if (!isValidUnit(ptlLength)) {
            $('#ptlLengthCourier').val('');
        } else if (!isValidUnit(ptlWidth)) {
            $('#ptlWidthCourier').val('');
        } else if (!isValidUnit(ptlHeight)) {
            $('#ptlHeightCourier').val('');
        } else {
            weightType = $('#ptlCheckVolWeightCourier').val();
            volumeWeight(weightType,21);
        }
    });
    $('.message_details_page').click(function() {
        var url = $(this).data('url');
        window.location = url;
    });
    $("#sendmessage").validate({
        ignore: [],
            // Specify the validation rules
            rules : {
                    'message_to' : "required",
                    'message_subject' : "required",
                    'message_body' :"required",
                    
            },
            // Specify the validation error messages
            messages : {
                    'message_to' : "Please select atleast one user",
                    'message_subject' : "Plese select the subject",
                    'message_body' : "Please specify message body",
                    
            },
            submitHandler : function(form) {
                    form.submit();
            }
    });
    $('.new_message').click(function(e) {
        // alert("Please add atleast one item to the list");
//        $("#new_message_modal .modal-body").html("Please add atleast one item to the list.");
        $('#from_name').attr('placeholder',"From: "+$('.login_name').text().trim());
        if($(this).attr('data-userid')) {
            var str =$(this).data('userid');
            datastr = '&userid=' + str ;
            $.ajax({
                        type: "POST",
                        url: '/getusername',
                        data : datastr,
                        success: function(data) {
                           if(data!=""){
                               $("#message_to").val(str);
                               $('#user_ids').attr('placeholder',"To: "+data);
                               $('#user_ids').attr('readonly','');
                           }
                        },
                        error: function(request, status, error) {
                            // alert(request.responseText);
                        },
                    });
        }else{
            $('#user_ids').attr('placeholder',"To *");
        }
        
        var str1 =   $('h1.page-title').text();
        var str_title =   str1.split(" - ");
        if($(this).attr('data-buyerquoteitemid')) {
            var transaction_no=$(this).data('transaction_no');
            //var str_transaction =   transaction_no.split(" - ");
            $('#buyer_quote_item').val($(this).data('buyerquoteitemid'));
            $('#buyer_quote').val($(this).data('id'));
            $('#message_subject').val("Ref:- POST:"+transaction_no);
            
        }
        if($(this).attr('data-buyerleadsitemid')) {
            var transaction_no=$(this).data('transaction');
            //var str_transaction =   transaction_no.split(" - ");
            $('#buyer_quote_item_leads').val($(this).data('buyerleadsitemid'));
            $('#buyer_quote').val($(this).data('id'));
            $('#message_subject').val("Ref:- POST:"+transaction_no);
            
        }
        if($(this).attr('data-orderid')) {
            $('#order_id_for_model').val($(this).data('orderid'));
            datastr = '&orderid=' + $(this).data('orderid') ;
            $.ajax({
                        type: "POST",
                        url: '/getorderno',
                        data : datastr,
                        success: function(data) {
                           if(data!=""){
                               $('#message_subject').val("Ref:- ORDER:"+data);
                           }
                        },
                        error: function(request, status, error) {
                            // alert(request.responseText);
                        },
                    });
            //$('#message_subject').val("Ref:- ORDER:"+$(this).parent(4).closest('.order_no').text());
        }
        if($(this).attr('data-contractid')) {
            $('#contract_id_for_model').val($(this).data('contractid'));
            datastr = '&contractid=' + $(this).data('contractid') ;
            $.ajax({
                        type: "POST",
                        url: '/getcontractno',
                        data : datastr,
                        success: function(data) {
                           if(data!=""){
                               $('#message_subject').val("Ref:- CONTRACT:"+data);
                           }
                        },
                        error: function(request, status, error) {
                            // alert(request.responseText);
                        },
                    });
            //$('#message_subject').val("Ref:- CONTRACT:"+str_title[1]);
        }
        if($(this).attr('data-buyerquoteitemidforseller')) {
            var transaction_no=$(this).data('buyer-transaction');
            $('#buyer_quote_item_seller').val($(this).data('buyerquoteitemidforseller'));
            $('#seller_post').val($(this).data('id'));
            $('#message_subject').val("Ref:- POST:"+transaction_no);
            
        }
        if($(this).attr('data-buyerquoteitemidforsellerleads')) {
            var transaction_no=$(this).data('buyer-transaction-leads');
            $('#buyer_quote_item_seller').val($(this).data('buyerquoteitemidforsellerleads'));
            $('#seller_post').val($(this).data('id'));
            $('#message_subject').val("Ref:- POST:"+transaction_no);
            
        }

        if($(this).attr('data-subject')) {
            $('#message_subject').val($(this).data('subject'));
        }
        if($(this).attr('data-msgid')) {
            $('#message_id').val($(this).data('msgid'));
        }
        if($('#message_subject').val() == ""){
            $('#message_subject').removeAttr('readonly');
        }
        /*if($(this).attr('data-searchbuyerquoteitemidseller')) {
            $('#buyer_quote_item_for_search_seller').val($(this).data('searchbuyerquoteitemidseller'));
        }
        if($(this).attr('data-orderidseller')) {
            $('#order_id_for_model_seller').val($(this).data('orderidseller'));
        }
        if($(this).attr('data-searchbuyerquoteitemid')) {
            $('#buyer_quote_item_for_search').val($(this).data('searchbuyerquoteitemid'));
        }*/
        if($(this).attr('data-term')) {
            $('#is_term').val($(this).data('term'));
        }
//        if($(this).attr('data-searchbuyerquoteitemid')) {
//            $('#buyer_quote_item_for_search').val($(this).data('searchbuyerquoteitemid'));
//        }
//        if($(this).attr('data-orderid')) {
//            $('#order_id_for_model').val($(this).data('orderid'));
//        }
        $("#new_message_modal").modal({
            show: true
        });
        return false;
    });
    var myBackup = $('#new_message_modal').clone();
    $(document).on('click', '#send_message_from_search', function() {
    
        $("#sendmessage").validate().cancelSubmit = false; 
        
        if($('#message_body').val()==''){
            alert('Please enter message body');
            return false;
        }
        $('#sendmessage').attr('method','get');
        
               var formObj = $('#sendmessage');
                //var formData = new FormData(formObj[0]);
                var formData = formObj.serialize();
                    $.ajax({
                        type: "GET",
                        url: '/setmessagedetails',
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
                        data : formData,
                        processData: false,
                        contentType: false,
                        success: function(data) {
                            $('#sendmessage').removeAttr('method');
                           if(data==1){
                               //alert('Message sent successfully');
                               $("#erroralertmodal .modal-body").html('Message sent successfully.');
                                $("#erroralertmodal").modal({
                                    show: true
                                });
                               $('#message_body').text("");
                               //$(".close").click();
                               $('#new_message_modal').modal('hide').remove();
                                var myClone = myBackup.clone();
                                $('body').append(myClone);
                                $('#message_to').tokenize({
                                    datas: "/getnamelist",
                                    onAddToken:function(value, text, e){
                                        var tokens=$("#message_to").val();
                                        $("#message_to").val(tokens+","+value);
                                                },
                                                onRemoveToken:function(value, text, e){
                                                        var tokens = $("#message_to").val();
                                                        tokens = tokens.replace(','+value,'');
                                                        $("#message_to").val(tokens);
                                                },
                                });
                               
                           }
                        },
                        error: function(request, status, error) {
                            // alert(request.responseText);
                        },
                    });
                });

     // Psot private and Post public functionality in PTL buyerquote creation   
    $('#message_to').tokenize({
            datas: "/getnamelist",
            onAddToken:function(value, text, e){
                var tokens=$("#message_to").val();
                $("#message_to").val(tokens+","+value);
			},
			onRemoveToken:function(value, text, e){
				var tokens = $("#message_to").val();
				tokens = tokens.replace(','+value,'');
				$("#message_to").val(tokens);
			},
	});
    
//    $("#new_message_modal .message_save_button").click(function() {
//        var message_to = $('message_to').value();
//        var message_subject = $("#message_subject").val();
//        var message_body = $("#message_body").val();
//        var is_draft = 1;
////        $.ajax({
////            type: "POST",
////            url: "/setmessagedetails",
////            beforeSend: function() {
////                $.blockUI({
////                    overlayCSS: {
////                        backgroundColor: '#000'
////                    }
////                });
////            },
////            complete: function() {
////                $.unblockUI();
////            },
////            data: {
////                'recipient': recipient,
////                'subject': subject,
////                'message': message
////            },
////            success: function(jsonData) {
////                $("#erroralertmodal .modal-body").html('Counter offer added successfully.');
////                $("#erroralertmodal").modal({
////                    show: true
////                }).one('click','.ok-btn',function (e){
////                    location.reload();
////                });
//////                    alert('Counter offer added successfully.');
//////                    location.reload();
////            }
////        }, "json");

    $("#ftl_term_booknow .add_buyer_addtocart_details,#ftl_term_booknow .add_buyer_checkout_details").click(function() {
        
    	
    	var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var isCheckout;
        if($(this).text() == 'Checkout') {
            isCheckout = '1';
        } else {
            isCheckout = '0';
        }
        var sourceLocationType = $('#buyersearch_booknow_offer_source_location_type_' + rowNo).val();
        var destinationLocationType = $('#buyersearch_booknow_offer_destination_location_type_' + rowNo).val();
        var packagingType = $('#buyersearch_booknow_offer_packaging_type_' + rowNo).val();
        //other fields for ftl leads
        var sourceLocationTypeOther = $('#buyersearch_booknow_offer_source_location_type_text').val();
        var destinationLocationTypeOther = $('#buyersearch_booknow_offer_destination_location_type_text').val();
        var packagingTypeOther = $('#buyersearch_booknow_offer_packaging_type_text').val();
        
        var consignmentPickupDate = $('#buyersearch_booknow_offer_consignment_pickup_date_' + rowNo).val();
        var consignmentNeedInsurance = $('#buyersearch_booknow_offer_need_insurance_' + rowNo).val();
        var consignmentValue = $('#buyersearch_booknow_offer_consignment_value_' + rowNo).val();
        var consignorName = $('#buyersearch_booknow_offer_consignor_name_' + rowNo).val();
        var consignorNumber = $('#buyersearch_booknow_offer_consignor_number_' + rowNo).val();
        var consignorEmail = $('#buyersearch_booknow_offer_consignor_email_' + rowNo).val();
        var consignorAddress = $('#buyersearch_booknow_offer_consignor_address_' + rowNo).val();
        var consignorPin = $('#buyersearch_booknow_offer_consignor_pincode_' + rowNo).val();
        var consigneeName = $('#buyersearch_booknow_offer_consignee_name_' + rowNo).val();
        var consigneeNumber = $('#buyersearch_booknow_offer_consignee_number_' + rowNo).val();
        var consigneeEmail = $('#buyersearch_booknow_offer_consignee_email_' + rowNo).val();
        var consigneePin = $('#buyersearch_booknow_offer_consignee_pin_' + rowNo).val();
        var consigneeAddress = $('#buyersearch_booknow_offer_consignee_address_' + rowNo).val();
        var additionalDetails = $('#buyersearch_booknow_offer_additional_details_' + rowNo).val();
        var buyerId = $('#term_booknow_buyer_id').val();
        var quoteId = $('#term_booknow_quote_id').val();
        var sellerId = $('#term_seller_id').val();
        var quoteItemId = rowNo;
        var postItemId = null;
        var price = $('#term_total_price').val();
        var contractId = $('#term_booknow_contract_id').val();
        var contractFromDate = $('#term_contract_from_date').val();
        var contractToDate = $('#term_contract_to_date').val();
        if($('#service_id').val()==19){
        var termContractDispatchDate = $('#term_contract_dispatch_date').val();
        }

        var sellerPostedFromDate = null;
        var sellerPostedToDate = null;
        var enquiryType = $('#enquiry_type').val();
        
        if($('#service_id').val()==19){
        	
        	checkAndSetGMBooknow(sourceLocationTypeOther,sourceLocationType, buyerId,
                    sellerId,   consignorName, consignorNumber,
                    consignorEmail, consignorAddress, consignorPin,additionalDetails,
                    rowNo,quoteItemId, postItemId, price, isCheckout,null,sellerPostedFromDate,sellerPostedToDate,contractId, enquiryType,contractFromDate,contractToDate,termContractDispatchDate);
            
        }else{
        checkAndSetBooknow(sourceLocationTypeOther,destinationLocationTypeOther,packagingTypeOther,sourceLocationType, destinationLocationType,
                    packagingType, buyerId, sellerId, consignmentPickupDate, consignmentValue,
                    consignorName, consignorNumber, consignorEmail, consignorAddress,
                    consignorPin, consigneeName, additionalDetails,
                    consigneeNumber, consigneeEmail, consigneePin, consigneeAddress,
                    rowNo, consignmentNeedInsurance, quoteItemId, postItemId, price,isCheckout, quoteId,
                    sellerPostedFromDate, sellerPostedToDate, null, contractId, enquiryType,contractFromDate,contractToDate);
        }
        
    });
    $("#leadcompare").on("click",function() {
    	$(".compare-fld").hide();
    	
    });
    $("#quotecompare").on("click",function() {
    	$(".compare-fld").show();
    	
    });
//    $("#ftl-buyer-leads").on("click", ".booknow_buyer,.add_buyer_checkout_details", function() {
//        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
//        var isCheckout;
//        if($(this).text() == 'Checkout') {
//            isCheckout = '1';
//        } else {
//            isCheckout = '0';
//        }
//        var sourceLocationType = $('#buyersearch_booknow_offer_source_location_type_' + rowNo).val();
//        var destinationLocationType = $('#buyersearch_booknow_offer_destination_location_type_' + rowNo).val();
//        var packagingType = $('#buyersearch_booknow_offer_packaging_type_' + rowNo).val();
//        var consignmentPickupDate = $('#buyersearch_booknow_offer_consignment_pickup_date_' + rowNo).val();
//        var consignmentNeedInsurance = $('#buyersearch_booknow_offer_need_insurance_' + rowNo).val();
//        var consignmentValue = $('#buyersearch_booknow_offer_consignment_value_' + rowNo).val();
//        var consignorName = $('#buyersearch_booknow_offer_consignor_name_' + rowNo).val();
//        var consignorNumber = $('#buyersearch_booknow_offer_consignor_number_' + rowNo).val();
//        var consignorEmail = $('#buyersearch_booknow_offer_consignor_email_' + rowNo).val();
//        var consignorAddress = $('#buyersearch_booknow_offer_consignor_address_' + rowNo).val();
//        var consignorPin = $('#buyersearch_booknow_offer_consignor_pincode_' + rowNo).val();
//        var consigneeName = $('#buyersearch_booknow_offer_consignee_name_' + rowNo).val();
//        var consigneeNumber = $('#buyersearch_booknow_offer_consignee_number_' + rowNo).val();
//        var consigneeEmail = $('#buyersearch_booknow_offer_consignee_email_' + rowNo).val();
//        var consigneePin = $('#buyersearch_booknow_offer_consignee_pin_' + rowNo).val();
//        var consigneeAddress = $('#buyersearch_booknow_offer_consignee_address_' + rowNo).val();
//        var additionalDetails = $('#buyersearch_booknow_offer_additional_details_' + rowNo).val();
//        var sellerId = $('#buyer_leads_post_seller_id_' + rowNo).val();
//        var quoteItemId = $('#cancel_buyer_counter_offer_enquiry').data('id');
//        var postItemId = $('#leads_seller_post_item_id_' + rowNo).val();
//
//        var price = $('#buyer_leads_post_price_' + rowNo).data('price');
//        var buyerId = $('#buyer_leads_post_buyer_id_' + rowNo).val();
//
//        var sellerPostedFromDate = $('#buyer_counter_offer_seller_post_from_date_' + rowNo).val();
//        var sellerPostedToDate = $('#buyer_counter_offer_seller_post_to_date_' + rowNo).val();
//        checkAndSetBooknow(sourceLocationType, destinationLocationType,
//            packagingType, buyerId, sellerId, consignmentPickupDate, consignmentValue,
//            consignorName, consignorNumber, consignorEmail, consignorAddress,
//            consignorPin, consigneeName, additionalDetails,
//            consigneeNumber, consigneeEmail, consigneePin, consigneeAddress,
//            rowNo, consignmentNeedInsurance, quoteItemId, postItemId, price,isCheckout, null,
//            sellerPostedFromDate, sellerPostedToDate, null);
//        

//    });
    
    $('.property_type_select').change(function(e) {
    	
    	var indentTotal=$(this).attr('id');
    	indentTotal=indentTotal.split('_');
    	indentId=indentTotal[2];
    	var contractprice=$("#contractprice_"+indentId).val(); 
    	//alert(indentId);
    	var data = {
    	        'prop_id': $('#property_type_'+indentId).val()
    	    };
    	 $.ajax({
    	        type: "GET",
    	        url: '/getpropertycft',
    	        data: data,
    	        dataType: 'text',
    	        success: function(data) {
    	        	
    	        	//alert(data);
    	             $("#volume_"+indentId).val(data);
    	             
    	             $("#displayVolumeW_"+indentId).html(data);
    	        	 $("#displaybaseFright_"+indentId).html(data*contractprice);
    	        	 $("#displaytotalamnt_"+indentId).html(data*contractprice);
    	        	 
    	        	 $("#total_hidden_volume_"+indentId).val(data);
    	        	 $("#total_hidden_frieght_"+indentId).val(data*contractprice);
    	        	 $("#total_hidden_amnt_"+indentId).val(data*contractprice);
    	             
    	           
    	        },
    	        error: function(request, status, error) {
    	            
    	        },
    	    });
    	
    });
    
    $('.indent-inventory').change(function(e) {
    	
    	var indentTotal=$(this).attr('id');
    	indentTotal=indentTotal.split('_');
    	indentId=indentTotal[2];
    	
    	var data = {
    	        'room_id': $('#room_type_'+indentId).val()
    	    };
    	 $.ajax({
    	        type: "GET",
    	        url: '/getpropertyparticulars',
    	        data: data,
    	        dataType: 'json',
    	        success: function(data) {
    	        	
    	        	//alert(data.html);
    	             $("#inventory_data_"+indentId).html(data.html);
    	           
    	        },
    	        error: function(request, status, error) {
    	            
    	        },
    	    });
    	
    });
    
    //Internation Air calculation
    $(".cartons").blur(function() {
        var currElement = $(this);
    	var totalcartons=0;
    	var frieghtcharges=0;
    	var odcahrges=$("#intodcharges").val();
    	var conversionfactor=0;
    	var totalodcharges=0;
    	var cont_id=$(this).attr('id').split('_');
    	$(".cartons").each(function() {
    		
    		if($(this).val()!=''){
    			//console.log($(this).val());
    		totalcartons=totalcartons+parseInt($(this).val())*parseInt($(this).attr('rel'));
    		}
    	})
    	
    	//console.log(totalcartons);
    	if(totalcartons<=100){
    		frieghtcharges=parseFloat($("#frieghtone").val());	
    	}
    	else if(totalcartons<=300){
    		frieghtcharges=parseFloat($("#frieghtthree").val());	
    	}
    	else if(totalcartons<=500){
    		frieghtcharges=parseFloat($("#frieghtfive").val());	
    	}
    	
    	if(totalcartons<500){
    	conversionfactor=parseFloat((totalcartons*3000)/1728);
    	
		//console.log(frieghtcharges);
    	totalodcharges=parseFloat(conversionfactor*odcahrges);
    	totalfrieghtcharges=parseFloat(totalcartons*frieghtcharges);
    	totalcharges=totalodcharges+totalfrieghtcharges;
    	
    	totalodcharges=parseFloat(totalodcharges.toFixed(2));
    	totalfrieghtcharges=parseFloat(totalfrieghtcharges.toFixed(2));
		totalcharges=parseFloat(totalcharges.toFixed(2));
    	
    	$("#total-weight_"+cont_id[2]).html(totalcartons);
    	$("#total-frieght_"+cont_id[2]).html(commaSeparateNo(totalfrieghtcharges,true));
    	$("#total-od_"+cont_id[2]).html(commaSeparateNo(totalodcharges,true));
    	$("#totalairamount_"+cont_id[2]).html(commaSeparateNo(totalcharges,true));
    	$("#total_hidden_amnt_"+cont_id[2]).val(totalcharges);
    	$("#total_hidden_kgs_"+cont_id[2]).val(totalcartons);
    	}else{
    		
            $("#erroralertmodal .modal-body").html("Total cartons weight must be less than or equal to 500.");
	        $("#erroralertmodal").modal({
	             show: true
	        });
            currElement.val('');
	        return false; 
    		
    	}
    	
    });
    
    $(".number_days").blur(function() {
    	
    	var cont_id=$(this).attr('id').split('_');
    	var totalPrice=0;
    	var days=$(this).val();
    	
    	totalPrice=days*$("#contract_mobilityprice").val();
    	
    	$("#total_mobility_"+cont_id[2]).html(totalPrice);
    	$("#total_hidden_amnt_"+cont_id[2]).val(totalPrice);
    	$("#total_hidden_days_"+cont_id[2]).val(days);
    	$("#global_pickup_date_"+cont_id[2]).val($("#consignment_pickup_date_"+cont_id[2]).val());
    	
    });
    
    $(".relocationmobility").on("click",function() {
    	
    	var contid=$(this).attr('id');
        var from_date = $("#term_contract_from_date").val();
        var check_date = $("#consignment_pickup_date_"+contid).val();       

        var frmDD = from_date.split('/').reverse();
        var consDD = check_date.split('/').reverse();

        var chkFromDate = moment(frmDD[0]+'-'+frmDD[1]+'-'+frmDD[2]);
        var chkConsDate = moment(consDD[0]+'-'+consDD[1]+'-'+consDD[2]);        
        

          if($("#consignment_pickup_date_"+contid).val()==""){
        	 
        	$("#erroralertmodal .modal-body").html("Please enter pickup date.");
    	         $("#erroralertmodal").modal({
    	             show: true
    	         });
    	         return false;   
          }else if($("#number_days_"+contid).val()==""){
        	  
        	$("#erroralertmodal .modal-body").html("Please enter number of days.");
    	         $("#erroralertmodal").modal({
    	             show: true
    	         });  
    	         return false;   
          }else if(chkConsDate < chkFromDate) {
            $("#erroralertmodal .modal-body").html("Pickup date should be in contract validity period.");
                 $("#erroralertmodal").modal({
                     show: true
                 });
                 return false;  
          }else{
        	  return true;
          }
    
    }); 
    
    $(".relocationinternatonalairbooknow").on("click",function(){
    	
    	var contid=$(this).attr('id');
    	var totcartoncount=0;
    	$(".cartons").each(function() {
    		
    		if($(this).val()!=''){
    		totcartoncount=1;	
    		}
    	});
    	
    	if(totcartoncount==0){
    		
    	$("#erroralertmodal .modal-body").html("Please enter number of cartons.");
	         $("#erroralertmodal").modal({
	             show: true
	         });  
	         return false;  
    		
    	}
    });
    
$(".relocationinternatonaloceanbooknow").on("click",function(){
    	
    	var contid=$(this).attr('id');
    	var totcartoncount=0;
    	
    	if($("#property_type_"+contid).val()==0){
    		
    	$("#erroralertmodal .modal-body").html("Please select property type.");
	         $("#erroralertmodal").modal({
	             show: true
	         });  
	         return false;  
    		
    	}
    	if($("#room_type_"+contid).val()==0){
    		
    		$("#erroralertmodal .modal-body").html("Please select room type.");
	         $("#erroralertmodal").modal({
	             show: true
	         });  
	         return false; 
    		
    	}
    	//alert($("#inventory_count_div_"+contid).html());
    	if($("#inventory_count_div_"+contid).html()==""){
    		
    		$("#erroralertmodal .modal-body").html("Please fill inventory.");
	         $("#erroralertmodal").modal({
	             show: true
	         });  
	         return false; 
		}
    }); 
}); // Total script main end braces

function getSelectedTokenizedVale(id){
    var selectedValues = [];
    $('#'+id).each(function () {
        selectedValue.push($(this).val());
    });
    return selectedValues;
}

//cancel function in ptl
function ptlbuyerpostcancel(str) {
	/*if ($("input[type='checkbox'][name='buyerpostptlcheck']:checked").length==0) {    		
		//alert("check atleast one post to delete");
		$("#erroralertmodal .modal-body").html("Check atleast one post to delete.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;         
    } else {  */  	
	var answer = confirm ("Are you sure you want to delete the post?");
	  if (answer)
	    {
		  /*var allVals = [];    		
		     $('input:checkbox.checkBoxClass').each(function() {
		    	 if($(this).is(":checked")==true){		    	   	
		    	   	allVals.push($(this).val());
		    	     }		       
		     });*/
		datastr = '&postIds=' + str +'&str=' + str;
		// ajax function starts
		$.ajax({
			type : 'post', // defining the ajax type
			url : '/buyerpostcancel', // calling the controller with the    			
			dataType : 'html', // datatype
			data : datastr, // passing the data used for operation
			success : function(html) {
					alert(html);
					location.reload();
					/*$('.checkBoxClass').each(function(){
						this.checked = false;
						});
					$('input:checkbox#ptlbuyerpostcheck').removeAttr('checked');  */  					
					}
				});
	    	}
    	//}	  
	}



//Canel fucntion in ftl
function buyerpostcancel(str) { 

	/*if ($("input[type='checkbox'][name='buyerpostcheck']:checked").length==0) {    		
		//alert("Check atleast one post to cancel");
		 $("#erroralertmodal .modal-body").html("Check atleast one post to delete.");
         $("#erroralertmodal").modal({
             show: true
         });
		return false;         
    } else {  */  	
	var answer = confirm ("Are you sure you want to delete the post?");
	  if (answer)
	    {
		  /*var allVals = [];    		
		     $('input:checkbox.checkBoxClass').each(function() {
		    	 if($(this).is(":checked")==true){		    	   	
		    	   	allVals.push($(this).val());
		    	     }		       
		     });*/
		datastr = '&postIds=' + str +'&str=' + str;
		// ajax function starts
		$.ajax({
			type : 'post', // defining the ajax type
			url : '/buyerpostcancel', // calling the controller with the    			
			dataType : 'html', // datatype
			data : datastr, // passing the data used for operation
			success : function(html) {
					alert("Post deleted successfully");
					location.reload();
					/*$('.checkBoxClass').each(function(){
						this.checked = false;
						});
					$('input:checkbox#globalbuyerpostlistcheck').removeAttr('checked');   */ 					
					}
				});
	    	}
    	//}	
	}

//Calculate volume Weight type calculation	
function volumeWeight(weightType,serviceId)
{	
				if(serviceId == 21){
					   var ptlLength   =$('#ptlLengthCourier').val();
		                var ptlWidth = $('#ptlWidthCourier').val();
		                var ptlHeight = $('#ptlHeightCourier').val();
		                var data = {
		        				'ptlweightType' : weightType,
		        				'ptlLength' : $('#ptlLengthCourier').val(),
		        				'ptlWidth' : $('#ptlWidthCourier').val(),
		        				'ptlHeight' : $('#ptlHeightCourier').val()
		        			};
				}else{
					 var ptlLength   =$('#ptlLength').val();
		             var ptlWidth = $('#ptlWidth').val();
		             var ptlHeight = $('#ptlHeight').val();
		             var data = {
		     				'ptlweightType' : weightType,
		     				'ptlLength' : $('#ptlLength').val(),
		     				'ptlWidth' : $('#ptlWidth').val(),
		     				'ptlHeight' : $('#ptlHeight').val()
		     			};
				}
	//retun true then condititon conme in this function
             
		
			if((ptlLength!=0 && ptlWidth!=0 && ptlHeight!=0) && (ptlLength!='' && ptlWidth!='' && ptlHeight!='')){
    				$.ajax({
    				type : "GET",
    				url : '/getVolumeWeight',
    				data : data,
    				dataType : 'text',
    				success : function(data) {
    					if (data !="") {
                            var res = data.split(" ");
    						$('#ptlDisplayVolumeWeight').val(res[0]);
    						$('#displayVolumeW').html(data);
    						//$("#displayVolumenone").css("display", "none");		
    						$('#displayVolumenone').hide();
    				        $('#displayVolumeW').show();
    					} else {
    						 //alert("Please Select Package Sizes");
    						 $("#erroralertmodal .modal-body").html("Please select package sizes.");
    				         $("#erroralertmodal").modal({
    				             show: true
    				         });
    						/*@Shriram, jun 30 Below line commented becoz after response it is coming to original state */
                            /*$('#ptlCheckVolWeight').val(null);
                            $('#ptlCheckVolWeight').selectpicker('refresh');*/
    					}
    				},
    				error : function(request, status, error) {				
    					    //alert(request.responseText);
    				},
    			});
            }else{
                $("#erroralertmodal .modal-body").html("Please select package sizes.");
                $("#erroralertmodal").modal({
	             show: true
	            });
			    /*@Shriram, jun 30 Below line commented becoz after response it is coming to original state */
                /*$('#ptlCheckVolWeight').val(null);
                $('#ptlCheckVolWeight').selectpicker('refresh');*/
            }
}



function isValidUnit(value) {
    //var regexPattern = /^\d{0,8}(\.\d{1,3})?$/;
    //var regexPattern = /^(0|0?[1-9]\d*)\.\d\d\d$/;
    //if(!value || !value.trim() || !regexPattern.test(value)){
    if(!value || !value.trim()){
        $('#displayVolumenone').show();
        $('#displayVolumeW').hide();
        /*@Shriram, jun 30 Below line commented becoz after response it is coming to original state */
        /*$('#ptlCheckVolWeight').val(null);
        $('#ptlCheckVolWeight').selectpicker('refresh');*/
        return false;
    } else {
        $('#displayVolumenone').hide();
        $('#displayVolumeW').show();
        return true;
    }
}


/** ******** Below script ajax onchange capacity in FTl************ */
function GetCapacity() {
    var data = {
        'load_type': $('#load_type').val()
    };
    $.ajax({
        type: "GET",
        url: '/getCapacity',
        data: data,
        dataType: 'text',
        success: function(data) {
            $('#capacity').val(data);
        },
        error: function(request, status, error) {
            $('#capacity').val('');
        },
    });
}

/** ******** Below script ajax onchange capacity in FTl************ */
function GetWeightType() {
	
    /*if($('#units_max_weight').val() == 1){
		$('#max_weight_accepted').addClass("clsCOURMaxWeight");
		$('#max_weight_accepted').removeClass("clsCOURMaxWeight");
		$('#max_weight_accepted').val("");
	}else{
		$('#max_weight_accepted').removeClass("clsCOURMaxWeight");
		$('#max_weight_accepted').addClass("clsCOURMaxWeight");
		$('#max_weight_accepted').val("");
	}*/
}


//Hide and Show price textbox select price type in FTl post creation
function HidePrice(price) {
    if (price == "1") {
        document.getElementById('hide_price').style.display = 'none';
        $('label[for="price"]').hide();
    } else {
        document.getElementById('hide_price').style.display = 'table';
        
        //$("#hide_price").addClass("table-div");
    }
}
// Count no of loads depends on vehicle type and quanntity in FTl post creation
function CheckLoads(vehicletype) {
    var data = {
        'vehicle_type': vehicletype,
        'quantity': $('#quantity').val()
    };
    $.ajax({
        type: "GET",
        url: '/getNoofLoads',
        data: data,
        dataType: 'text',
        success: function(data) {
            var myarr = data.split("-");
            $("#dimensions").html(myarr[0]);
            if (myarr[1] > 0) {
                $('#no_of_loads').val(myarr[1]);
            } else {
            	$("#erroralertmodal .modal-body").html("Please enter quantity.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $('#vehicle_type').val(null);
                $('#vehicle_type').selectpicker('refresh');
            }
        },
        error: function(request, status, error) {
            $('#vehicle_type').val(null);
            $('#vehicle_type').selectpicker('refresh');
            $('#no_of_loads').val(null);
        },
    });
}

//Relocation Start

function getPropertyCft(){
	
	//$('#property_id').selectpicker('refresh');
	var data = {
	        'prop_id': $('#property_type').val()
	    };
	 $.ajax({
	        type: "GET",
	        url: '/getpropertycft',
	        data: data,
	        dataType: 'text',
	        success: function(data) {
	        	
	        	//alert(data);
	             $("#volume").val(data);
	           
	        },
	        error: function(request, status, error) {
	            
	        },
	    });
	 
}

function getPropertyCftTerm(indentId){
	//alert(indentId);
	//$('#property_id').selectpicker('refresh');
	var indentId=$("#term_click_booknow").attr('rel');
	var contractprice=$("#contractprice_"+indentId).val(); 
	alert(indentId);
	var data = {
	        'prop_id': $('#property_type_'+indentId).val()
	    };
	 $.ajax({
	        type: "GET",
	        url: '/getpropertycft',
	        data: data,
	        dataType: 'text',
	        success: function(data) {
	        	
	        	alert(data);
	             $("#volume_"+indentId).val(data);
	             
	             $("#displayVolumeW_"+indentId).html(data);
	        	 $("#displaybaseFright_"+indentId).html(data*contractprice);
	        	 $("#displaytotalamnt_"+indentId).html(data*contractprice);
	        	 
	        	 $("#total_hidden_volume_"+indentId).val(data);
	        	 $("#total_hidden_frieght_"+indentId).val(data*contractprice);
	        	 $("#total_hidden_amnt_"+indentId).val(data*contractprice);
	             
	           
	        },
	        error: function(request, status, error) {
	            
	        },
	    });
	 
}



function getRoomParticulars(){
	
	
	var data = {
	        'room_id': $('#room_type').val()
	    };
	 $.ajax({
	        type: "GET",
	        url: '/getpropertyparticulars',
	        data: data,
	        dataType: 'json',
	        success: function(data) {
	        	
	        	//alert(data.html);
	             $("#inventory_data").html(data.html);
	           
	        },
	        error: function(request, status, error) {
	            
	        },
	    });
	
}


$(function() {

$('.save-continue').click(function() {
	
	//$(this.form).attr('rel');
	var form=$(this.form).attr('id');
	
	var fd = $("#"+form).serialize(); 
	var roomitemscount=0;
	var contractprice='';
	var formid=form.split('_');
	
	var indentId='';
	if($(this.form).attr('id')=='term_click_booknow_'+formid[3]){
	var indentId=$(this.form).attr('rel');  
	
	console.log($("#contractprice_"+indentId).val());
	contractprice=$("#contractprice_"+indentId).val();  
	
    }
	$('.roomitems').each(function () {
		
		if($(this).val()!="")
		{
		 roomitemscount=roomitemscount+1;
		}	
        //selectedValue.push($(this).val());
    });
	if(roomitemscount==0){
	 $("#erroralertmodal .modal-body").html("Please enter any value to save.");
     $("#erroralertmodal").modal({
      show: true
     });
     
     return false;
     }
	$.ajax({
        type: "POST",
        url: '/saveinventorydetails?contractprice=' + contractprice,
        data: fd,
        dataType: 'json',
        success: function(data) {
        	
        	//alert(data.html);
        	if(indentId!=''){
        		
        	 $("#displayVolumeW_"+indentId).html(data.TotalIndentVolume);
        	 $("#displaybaseFright_"+indentId).html(data.TotalIndentPrice);
        	 $("#displaytotalamnt_"+indentId).html(data.TotalIndentPrice);
        	 
        	 $("#total_hidden_volume_"+indentId).val(data.TotalIndentVolume);
        	 $("#total_hidden_frieght_"+indentId).val(data.TotalIndentPrice);
        	 $("#total_hidden_amnt_"+indentId).val(data.TotalIndentPrice);
        	 
        	 $("#inventory_count_div_"+indentId).html(data.html);
        	 
        	 }else{
        	
             $("#inventory_count_div").html(data.html);
        	 }
           
        },
        error: function(request, status, error) {
            
        },
    });
	
});

    $('.save-continue-search').click(function() {

        //$(this.form).attr('rel');
        var form=$(this.form).attr('id');
        var fd = $("#"+form).serialize();
        var roomitemscount=0;
        var contractprice='';

        var indentId='';
        if($(this.form).attr('id')=='term_click_booknow'){
            var indentId=$(this.form).attr('rel');
            //alert();
            console.log($("#contractprice_"+indentId).val());
            contractprice=$("#contractprice_"+indentId).val();

        }
        $('.roomitems').each(function () {

            if($(this).val()!="")
            {
                roomitemscount=roomitemscount+1;
            }
            //selectedValue.push($(this).val());
        });
        if(roomitemscount==0){
            $("#erroralertmodal .modal-body").html("Please enter any value to save.");
            $("#erroralertmodal").modal({
                show: true
            });

            return false;
        }
        $.ajax({
            type: "POST",
            url: '/savesearchinventorydetails?contractprice=' + contractprice,
            data: fd,
            dataType: 'json',
            success: function(data) {

                //alert(data.html);


                   //alert(data.TotalIndentVolume);
                    $("#total_hidden_volume").val(data.TotalIndentVolume);
                    $("#crating_items").val(data.TotalCrtaing);		


                $("#inventory_count_div").html(data.html);

            },
            error: function(request, status, error) {

            },
        });

    });
    
    $('.save-continue-international').click(function() {
    	
    	
    	//alert("hello");
    	//alert($(this.form).attr('rel'));
    	var form=$(this.form).attr('id');
    	
    	var fd = $("#"+form).serialize(); 
    	var roomitemscount=0;
    	var contractprice='';
    	var formid=form.split('_');
    	
    	var indentId='';
    	if($(this.form).attr('id')=='term_click_booknow_'+formid[3]){
    	var indentId=$(this.form).attr('rel');  
    	
    	//console.log($("#contractprice_"+indentId).val());
    	contractprice=1;  
    	
        }
    	$('.roomitems').each(function () {
    		
    		if($(this).val()!="")
    		{
    		 roomitemscount=roomitemscount+1;
    		}	
            //selectedValue.push($(this).val());
        });
    	if(roomitemscount==0){
    	 $("#erroralertmodal .modal-body").html("Please enter any value to save.");
         $("#erroralertmodal").modal({
          show: true
         });
         
         return false;
         }
    	$.ajax({
            type: "POST",
            url: '/saveinventorydetails?contractprice=' + contractprice,
            data: fd,
            dataType: 'json',
            success: function(data) {
            	
            	//alert(data.html);
            	if(indentId!=''){
            		
            	 var volumecbm=0;
            	 var shipmenttype='';
            	 var frieghtocean=0;
            	 var odocean=0;
            	 var totodprice=0;
            	 var totfrieghtprice=0;
            	 var TotalOcaenPrice=0;
            	 volumecbm=parseFloat(data.TotalIndentVolume/35.5);
            	 volumecbm=volumecbm.toFixed(2);
            	 
            	
            	 if(volumecbm<=10){
            		
            		shipmenttype='LCL';	
            		frieghtocean=parseFloat($("#frieghtlcl").val());
            		odocean=parseFloat($("#odlcl").val());
            	 }
            	 else if(volumecbm<=30){
            		
             		shipmenttype='FCL 20';
             		frieghtocean=parseFloat($("#frieghttwenty").val());
             		odocean=parseFloat($("#odtwenty").val());
             	 }
            	 else if(volumecbm<=60){
            		 
              		shipmenttype='FCL 40';	
              		frieghtocean=parseFloat($("#frieghtforty").val());
              		odocean=parseFloat($("#odforty").val());
              	 }else{
              		$("#erroralertmodal .modal-body").html("Total volume must be less than or equal to 60 CBM.");
	       	         $("#erroralertmodal").modal({
	       	             show: true
	       	         });
	       	         return false;
              	 }
            	 
            	 
            	
            	 totodprice=parseFloat(volumecbm*odocean);
            	 totfrieghtprice=parseFloat(volumecbm*frieghtocean);
            	 TotalOcaenPrice=totodprice+totfrieghtprice;
            	 
            	 totodprice=totodprice.toFixed(2);
            	 totfrieghtprice=totfrieghtprice.toFixed(2);
            	 TotalOcaenPrice=TotalOcaenPrice.toFixed(2);
            		 
            	 $("#shipment_type_"+indentId).html(shipmenttype);
            	 $("#volume_cbm_"+indentId).html(volumecbm);
            	 $("#frieght_ocean_"+indentId).html(commaSeparateNo(totfrieghtprice,true));
            	 $("#od_ocean_"+indentId).html(commaSeparateNo(totodprice,true));
            	 $("#total_ocean_"+indentId).html(commaSeparateNo(TotalOcaenPrice,true));
            	 
            	 $("#total_hidden_kgs_"+indentId).val(volumecbm);
            	 $("#total_hidden_amnt_"+indentId).val(TotalOcaenPrice);
            	 
            	 $("#inventory_count_div_"+indentId).html(data.html);
            	 
            	 }else{
            	
                 $("#inventory_count_div").html(data.html);
            	 }
               
            },
            error: function(request, status, error) {
                
            },
        });
    	
    });    

$(".ratetype_selection_buyer").click(function(){
	
	
	var typeselection = $('input[name=post_rate_card_type]:checked').val()
	
	if(typeselection == 1){
		$(".relocation_house_hold_buyer_create").show();
		$(".relocation_vehicle_buyer_create").hide();
		$("#household_items").val(1);
	}else if(typeselection == 2){
		$(".relocation_vehicle_buyer_create").show();
		$(".relocation_house_hold_buyer_create").hide();
		$("#household_items").val(2);
	}
	
	
	 
	
	
});

$(".ratetype_selection_buyer_term").click(function(){
	
	var indentId=$(this.form).attr('rel');
	var typeselection = $('input[name=post_rate_card_type_'+indentId+']:checked').val()
	
	if(typeselection == 1){
		$(".relocation_house_hold_buyer_create").show();
		$(".relocation_vehicle_buyer_create").hide();
		$("#household_items").val(1);
		
	}else if(typeselection == 2){
		$(".relocation_vehicle_buyer_create").show();
		$(".relocation_house_hold_buyer_create").hide();
		$("#household_items").val(2);
	}
	
	var indentId=$(this.form).attr('rel');  
	
	 $("#displayVolumeW_"+indentId).html('0.00');
	 $("#displaybaseFright_"+indentId).html('0.00');
	 $("#displaytotalamnt_"+indentId).html('0.00');
	 
	 $("#total_hidden_volume_"+indentId).val('');
	 $("#total_hidden_frieght_"+indentId).val('');
	 $("#total_hidden_amnt_"+indentId).val('');
	 
	 $("#volume_"+indentId).val('');
	 $('.selectpicker').selectpicker('val','');
	 $(".advanced-search-details").hide();
	
});

$(".cancel_order_yes").on("click",function(){
    $.ajax({
                    type: "GET",
                    url: "/orders/cancel/"+$("#cancellationorderid").val(),
                    beforeSend: function () {
                        $("#cancelordermodal").modal('hide');
                        $.blockUI({
                            overlayCSS: {
                                backgroundColor: '#000'
                            }
                        });
                    },
                    complete: function () {
                        $.unblockUI();
                    },

                    success: function (jsonData) {                        
                            location.reload();                     

                    }
                }, "json");
    });

//Buyer post validation

$("#posts-form_buyer_relocation").validate({
	ignore: "input[type='text']:hidden",
	rules : {
		"post_rate_card_type" : {required : true},
		"valid_from" : {required : true},
		"from_date" : {required : true},
		//"valid_to" : {required : true},
		//"to_date" : {required : true},		
		"from_location" : {required : true},
		"from_location_id" : {required : true},
		"to_location" : {required : true},
		"to_location_id" : {required : true},
		"agree" : {required : true},
        "vehicle_model" : {
            alphaNumeric : true
        },
		"property_type" : {
			required : {
				depends: function(element) {
					if ($('#household_items').val() == 1){
						return true;
					}else{
						return false;
					}
				}
			},
		},		
		"load_type" : {
			required : {
				depends: function(element) {
					if ($('#household_items').val() == 1){
						return true;
					}else{
						return false;
					}
				}
			},
		},
		"vehicle_category" : {
			required : {
				depends: function(element) {
					if ($('#household_items').val() == 2){
						return true;
					}else{
						return false;
					}
				}
			},
		},
		"vehicle_category_type" : {
			required : {
				depends: function(element) {
					if ($('#vehicle_category').val() == 1){
						return true;
					}else{
						return false;
					}
				}
			},
		},
		
//		"vehicle_model" : {
//			required : {
//				depends: function(element) {
//					if ($('#household_items').val() == 2){
//						return true;
//					}else{
//						return false;
//					}
//				}
//			},
//			
//		},
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
				required : "Enter Dispatch Date",
			},
			"from_date" : {
				required : "Enter Dispatch Date",
			},
//			"valid_to" : {
//				required : "Enter Delivery Date",
//			},
//			"to_date" : {
//				required : "Enter Delivery Date",
//			},
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
			//return false;
                        if($('#posts-form_buyer_relocation').valid()){
                            $("#getquote").prop('disabled', true);                    
                        }
			form.submit();
		}
	});

$(document).on('keyup', '#quantity', function() {
        getVehiclesList();
    });
$(document).on('keyup', '#qty', function() {
        getVehiclesLists();
    }); 
$(document).on('keyup', '#term_quantity', function() {
        getVehiclesListTerm();
    }); 
    
    
    /* pet move booknow  */
    $("#addbuyerpostcounteroffer").on("click", ".booknow_buyer,.add_buyer_checkout_details", function() {
        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var isCheckout;
        if($(this).text() == 'Checkout') {
            isCheckout = '1';
        } else {
            isCheckout = '0';
        }
        var sourceLocationType = $('#buyer_counter_offer_source_location_type_' + rowNo).val();
        var destinationLocationType = $('#buyer_counter_offer_destination_location_type_' + rowNo).val();
        //other fields for ftl quotes
        var sourceLocationTypeOther = $('#buyer_counter_offer_source_location_type_text').val();
        var destinationLocationTypeOther = $('#buyer_counter_offer_destination_location_type_text').val();
        
        var consignmentPickupDate = $('#buyer_counter_offer_consignment_pickup_date_' + rowNo).val();
        var consignmentNeedInsurance = $('#buyer_counter_offer_need_insurance_' + rowNo).val();
        var consignmentValue = $('#buyer_counter_offer_consignment_value_' + rowNo).val();
        var consignorName = $('#buyer_counter_offer_consignor_name_' + rowNo).val();
        var consignorNumber = $('#buyer_counter_offer_consignor_number_' + rowNo).val();
        var consignorEmail = $('#buyer_counter_offer_consignor_email_' + rowNo).val();
        var consignorAddress = $('#buyer_counter_offer_consignor_address_' + rowNo).val();
        var consignorPin = $('#buyer_counter_offer_consignor_pincode_' + rowNo).val();
        var consigneeName = $('#buyer_counter_offer_consignee_name_' + rowNo).val();
        var consigneeNumber = $('#buyer_counter_offer_consignee_number_' + rowNo).val();
        var consigneeEmail = $('#buyer_counter_offer_consignee_email_' + rowNo).val();
        var consigneePin = $('#buyer_counter_offer_consignee_pin_' + rowNo).val();
        var consigneeAddress = $('#buyer_counter_offer_consignee_address_' + rowNo).val();
        var buyerId = $('#buyer_post_buyer_id_' + rowNo).val();
        var sellerId = $('#buyer_post_seller_id_' + rowNo).val();
        var quoteItemId = $('#buyer_quote_item_id_' + rowNo).val();
        var postItemId = $('#seller_post_item_id_' + rowNo).val();
        var price = $('#buyer_post_price_' + rowNo).data('price');
        var additionalDetails = $('#buyer_counter_offer_additional_details_' + rowNo).val();

        var sellerPostedFromDate = $('#buyer_counter_offer_seller_post_from_date_' + rowNo).val();
        var sellerPostedToDate = $('#buyer_counter_offer_seller_post_to_date_' + rowNo).val();
        checkAndSetBooknow(sourceLocationTypeOther,destinationLocationTypeOther,null,sourceLocationType, destinationLocationType,
            null, buyerId, sellerId, consignmentPickupDate, consignmentValue,
            consignorName, consignorNumber, consignorEmail, consignorAddress,
            consignorPin, consigneeName, additionalDetails,
            consigneeNumber, consigneeEmail, consigneePin, consigneeAddress,
            rowNo, consignmentNeedInsurance, quoteItemId, postItemId, price,isCheckout, null,
            sellerPostedFromDate, sellerPostedToDate, null);
        
    });
    
    


});
function getVehiclesList() {
    if($('#quantity').attr('search'))
    {
         var val=$('#quantity').attr('search');
    }else {
        var val=0;
    }
    var data = {
        'weight': $('#quantity').val(),
        'weight_type': 3,
        'search':val
    };
    $.ajax({
        type: "GET",
        url: '/getVehicles',
        data: data,
        dataType: 'text',
        success: function(data) {
            $("#vechile_type").html(data);
            $("#vehicle_type").html(data);
            $('.selectpicker').selectpicker('refresh');
        },
        error: function(request, status, error) {
            $('#vechile_type').val('');
        },
    });
}
function getVehiclesLists() {
    if($('#qty').attr('search'))
    {
         var val=$('#qty').attr('search');
    }else {
        var val=0;
    }
    var data = {
        'weight': $('#qty').val(),
        'weight_type': 3,
        'search':val
    };
    $.ajax({
        type: "GET",
        url: '/getVehicles',
        data: data,
        dataType: 'text',
        success: function(data) {
            $("#vechile_type").html(data);
            $('.selectpicker').selectpicker('refresh');
        },
        error: function(request, status, error) {
            $('#vechile_type').val('');
        },
    });
}

function getVehiclesListTerm() {
    var data = {
        'weight': $('#term_quantity').val(),
        'weight_type': 3
    };
    $.ajax({
        type: "GET",
        url: '/getVehicles',
        data: data,
        dataType: 'text',
        success: function(data) {
            $("#term_vehicle_type").html(data);
            $('.selectpicker').selectpicker('refresh');
        },
        error: function(request, status, error) {
            $('#vechile_type').val('');
        },
    });
}

function getVehicleTypes(){
	
	//alert($("#vehicle_category").val());
	
	if($("#vehicle_category").val()==2){
		$(".vehicle_type_car").hide();
		
		
	}else{
		$(".vehicle_type_car").show();	
	}
}

function getVehicleTypesTerm(){
	
	if($("#term_vehicle_category").val()==2){
		$(".vehicle_type_car_term").hide();
		
	}else{
		$(".vehicle_type_car_term").show();	
	}
}

/*function getVehicleTypesTerm(){
	
	//alert($("#vehicle_category").val());
	var indentId=$("#term_click_booknow").attr('rel');
	var contractprice=$("#contractprice_"+indentId).val(); 
	
	if($("#vehicle_category_"+indentId).val()==2){
		$(".vehicle_type_car").hide();
		
	}else{
		$(".vehicle_type_car").show();	
	}
}*/



//Relocation End

function setorderid(orderid){
    $("#cancellationorderid").val(orderid);
}

function setcancelbuyerpostid(postid){
    $("#buyercancellationpostid").val(postid);
}




    
function checkAndSetBooknow_th(sourceLocationTypeOther,sourceLocationType, 
        buyerId, sellerId, consignmentPickupDate, consignmentPickupFromTime,consignmentPickupToTime,
        consignorName,consignorNumber, consignorEmail, consignorAddress, consignorPin,
        additionalDetails,rowNo,  quoteItemId, postItemId, price, isCheckout, quoteId,
        sellerPostedFromDate, sellerPostedToDate  ) {
	
	   // alert("hello");
        if (validateBooknowFields_th(sourceLocationType,  consignmentPickupDate, 
                consignmentPickupFromTime,consignmentPickupToTime,
                consignorName, consignorNumber, consignorEmail, consignorAddress,
                consignorPin, rowNo)) {
            quoteId = (!quoteId) ? '' : quoteId;
            sellerPostedFromDate = (!sellerPostedFromDate) ? '' : sellerPostedFromDate;
            sellerPostedToDate = (!sellerPostedToDate) ? '' : sellerPostedToDate;
            
            consignmentPickupFromTime = (!consignmentPickupFromTime) ? '' : consignmentPickupFromTime;
            consignmentPickupToTime = (!consignmentPickupToTime) ? '' : consignmentPickupToTime;
            var ajaxUrl =  "/setbuyerbooknow";
            	
            allData = {
                'sourceLocationType': sourceLocationType,
                'sourceLocationTypeOther': sourceLocationTypeOther,
                'buyerId': buyerId,
                'sellerId': sellerId,
                'consignmentPickupDate': consignmentPickupDate,
                'consignmentPickupFromTime': consignmentPickupFromTime,
                'consignmentPickupToTime': consignmentPickupToTime,
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
                'sellerPostedToDate': sellerPostedToDate
                
            };
            
            var commercial=$("#commerical_type").val();
        	
        	if(commercial==1){
            $('#booknow-popup').modal({
	            show: 'false'
			  });
        	
        	$("#alldata").val(JSON.stringify( allData ));
        	$("#ajaxurl").val(ajaxUrl);
        	$("#ischeckout").val(isCheckout);
        	
        	if($('#buyer_counter_offer_source_location_type_' + rowNo +' option:selected').text()){
        	 if($('#buyer_counter_offer_source_location_type_' + rowNo +' option:selected').text()=='Others – Specify')
        	  {	
        	   $("#source_location").html($('#buyer_counter_offer_source_location_type_text').val());
        	  }else{
        	   $("#source_location").html($('#buyer_counter_offer_source_location_type_' + rowNo +' option:selected').text());  
        	  }
        	}else{
        		
        	  if($('#buyersearch_booknow_offer_source_location_type_' + rowNo +' option:selected').text()=='Others – Specify')
          	  {	
          	   $("#source_location").html($('#buyersearch_booknow_offer_source_location_type_text').val());
          	  }else{
          	   $("#source_location").html($('#buyersearch_booknow_offer_source_location_type_' + rowNo +' option:selected').text());  
          	  }	
        	
        	}
        	//$("#destination_location").html($('#buyer_counter_offer_destination_location_type_' + rowNo +' option:selected').text());
        	$("#consignor").html(consignorName);
        	$("#consignor_mobile").html(consignorNumber);
        	$("#consignor_adddress").html(consignorAddress);
        	       	
            $("#buyer_user").html($("#buyer_name").val());

            if(consignmentPickupDate)
            {
            $("#pickup_con_date").html(consignmentPickupDate);
            }
        	return false;
        	}
        	else{
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
    
    
    
   function validateBooknowFields_th(sourceLocationType, 
         consignmentPickupDate,consignmentPickupFromTime,consignmentPickupToTime,
        consignorName, consignorNumber, consignorEmail, consignorAddress,
        consignorPin,  rowNo) {
        var sourceLocationTypeErrorMessage,  
            consignmentPickupDateErrorMessage, consignmentPickupFromTimeErrorMessage,consignmentPickupToTimeErrorMessage,
            consignorNameErrorMessage, consignorNumberErrorMessage,
            consignorEmailErrorMessage, consignorAddressErrorMessage, consignorPinErrorMessage ;
        var isValidErrorNumber = 0;
        //source other text
        if (sourceLocationType == '11') {
            if ($('#buyer_counter_offer_source_location_type_text').val() == '' || $('#buyersearch_booknow_offer_source_location_type_text').val() == '') {
                sourceLocationTypeErrorMessage = "Please enter other source location type!";
                isValidErrorNumber++;
            }else {
                sourceLocationTypeErrorMessage = '';
            }
        } 
        $('#buyer_counter_offer_source_location_type_text_error_' + rowNo).html(sourceLocationTypeErrorMessage);
        $('#buyersearch_booknow_offer_source_location_type_text_error_' + rowNo).html(sourceLocationTypeErrorMessage);
        if (sourceLocationType == '0') {
            sourceLocationTypeErrorMessage = 'Please enter Reporting location type!';
            isValidErrorNumber++;
        } else {
            sourceLocationTypeErrorMessage = '';
        }
        $('#buyer_counter_offer_source_location_type_error_' + rowNo).html(sourceLocationTypeErrorMessage);
        //date
        if (!consignmentPickupDate) {
            consignmentPickupDateErrorMessage = "Please enter Reporting date!";
            isValidErrorNumber++;
        } else {
            consignmentPickupDateErrorMessage = '';
        }
        $('#buyer_counter_offer_reporting_date_error_' + rowNo).html(consignmentPickupDateErrorMessage);
        //from time
        if (!consignmentPickupFromTime) {
            consignmentPickupFromTimeErrorMessage = "Please enter Reporting From Time!";
            isValidErrorNumber++;
        } else {
            consignmentPickupFromTimeErrorMessage = '';
        }
        $('#buyer_counter_offer_reporting_fromtime_error_' + rowNo).html(consignmentPickupFromTimeErrorMessage);
        //to time
        if (!consignmentPickupToTime) {
            consignmentPickupToTimeErrorMessage = "Please enter Reporting To Time!";
            isValidErrorNumber++;
        } else {
            consignmentPickupToTimeErrorMessage = '';
        }
        $('#buyer_counter_offer_reporting_totime_error_' + rowNo).html(consignmentPickupToTimeErrorMessage);
        
        //name
        if (!consignorName) {
            consignorNameErrorMessage = "Please enter Reporting name!";
            isValidErrorNumber++;
        } else if (consignorName && !validateName(consignorName)) {
            consignorNameErrorMessage = "Please enter proper Reporting name with 50 characters long!";
            isValidErrorNumber++;
        } else {
            consignorNameErrorMessage = '';
        }
        $('#buyer_counter_offer_consignor_name_error_' + rowNo).html(consignorNameErrorMessage);
        if (!consignorNumber) {
            consignorNumberErrorMessage = "Please enter Reporting mobile!";
            isValidErrorNumber++;
        } else if (consignorNumber && !validatePhone(consignorNumber)) {
            consignorNumberErrorMessage = "Please enter mobile number 10 characters long!";
            isValidErrorNumber++;
        } else {
            consignorNumberErrorMessage = '';
        }
        $('#buyer_counter_offer_consignor_number_error_' + rowNo).html(consignorNumberErrorMessage);
        if (consignorEmail && !validateEmail(consignorEmail)) {
            consignorEmailErrorMessage = "Please enter proper Reporting email!";
            isValidErrorNumber++;
        } else {
            consignorEmailErrorMessage = '';
        }
        $('#buyer_counter_offer_consignor_email_error_' + rowNo).html(consignorEmailErrorMessage);
        if (!consignorAddress) {
            consignorAddressErrorMessage = "Please enter Reporting address!";
            isValidErrorNumber++;
        } else {
            consignorAddressErrorMessage = '';
        }
        $('#buyer_counter_offer_consignor_address_error_' + rowNo).html(consignorAddressErrorMessage);
        if (!consignorPin) {
            consignorPinErrorMessage = "Please enter Reporting pin code!";
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
    function validateIndianZipCode(MyZipCode) {
        //var checkZipCode = /(^\d{6}$)/;
        var checkZipCode = /(^[a-zA-Z0-9]{5,6}$)/;
        return checkZipCode.test(MyZipCode);
    }
    function validateName($name) {
        var nameReg = /^[a-zA-Z ]{2,50}$/;
        return nameReg.test($name);
    }

/**
 * Start : Jagadeesh - 03/05/2016
 */
    jQuery.validator.addMethod("alphaNumeric", function(value, element) {
        return this.optional(element) || /^[a-z0-9\\-]+$/i.test(value);
    }, "Only letters, numbers,"); 
/**
 * End : Jagadeesh - 03/05/2016
 */


/**
 * Check and change class validtions for truck lease term depends on price class
 */

function changeRateClass(value){   
    
    switch(value){
        case "1": document.getElementById("price").className = "form-control numberVal fourdigitstwodecimals_deciVal"; break;       
        case "2": document.getElementById("price").className = "form-control numberVal fivedigitstwodecimals_deciVal"; break;
        case "3": document.getElementById("price").className = "form-control numberVal sixdigitstwodecimals_deciVal"; break;
        case "4": document.getElementById("price").className = "form-control numberVal sevendigitstwodecimals_deciVal"; break;
    }
    document.getElementById("price").value = "";
}

/**
 * Check and change class validtions for courier depends on weight unit
 */

function changeRateClass(value){   
    
    switch(value){
        case "1": document.getElementById("price").className = "form-control numberVal fourdigitstwodecimals_deciVal"; break;       
        case "2": document.getElementById("price").className = "form-control numberVal fivedigitstwodecimals_deciVal"; break;
        case "3": document.getElementById("price").className = "form-control numberVal sixdigitstwodecimals_deciVal"; break;
        case "4": document.getElementById("price").className = "form-control numberVal sevendigitstwodecimals_deciVal"; break;
    }
    document.getElementById("price").value = "";
}

