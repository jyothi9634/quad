$(document).ready(function() {
	// Buyer quote form validation
    $("#buyer_quote_items_truck_lease").validate({ // initialize the
        ignore: "input[type='text']:hidden",
        rules: {
            "from_location": {
                required: true,
            },
            "from_location_id": {
                required: true,
            },
            "dispatch_date": {
                required: true,
            },
            "delivery_date": {
                required: true,
            },
            "vehicle_type": {
                required: true,
            },
            "lease_terms": {
                required: true,
            },
            "year_make_model": {
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
            "price": {
                required: "Price is required",
            },
        },
        submitHandler: function(form) { // for demo
            form.submit();
        }
    });
    
    
    
    $("#buyer_quote_truck_lease").validate({
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
    
    
    
 // Add more items elements store in hidden fileds
    var seller_id_list = new Array();
    $('#lease_add_buyer_more').click(function() {
                $("#buyer_quote_items_truck_lease").validate().cancelSubmit = false; 
                var num = parseInt($('#next_add_buyer_more_id').val()) + 1;
                $('#next_add_buyer_more_id').val(num);
                var from_location_value = $('#from_location_id').val();
                var lease_term_value = $("#lease_type option:selected").text();
                var vehicle_type_value = $("#vehicle_type option:selected").text();
                var driver_text = $("#driver option:selected").text();
                var fuel_inc = $('#driver').val();
                if($('#need_diver').is(':checked')) {
                	var need_diver = 1;
                	driver_text = 'With Driver';
                }else{
                	var need_diver = 0;
                	driver_text = 'Without Driver';
                }
                var delivery_date = $('#datepicker_to_location').val();
                var year_make_model = $('#year_make_model').val();
                var dispatch_date = $('#datepicker').val();
                var from_location = $('#from_location').val();
                var lease_term = $('#lease_type').val();
                var vehicle_type = $('#vehicle_type').val();
                if($("#quote_id").val()==2){
                var price = $('#price').val();
                }else{
                var price = "--";	
                }
                var Quotes_quote_type = $('#quote_id').val();
                var Quotes_quote_type_text = $("#quote_id option:selected").text();
                //alert(from_location); return false;
                if (Quotes_quote_type == '1') {
                    var price_no = "0";
                } else {
                    var price_no = $('#price').val();
                }
                if (from_location != "" && from_location_value != '' && lease_term != "" && year_make_model != "" && vehicle_type != "" && Quotes_quote_type != "" && price_no != "") {
                	if($("#update_line").val()==1){
                	$('.request_row_' + $("#update_row_count").val()).remove();	
                	$("#update_line").val(0);
                	}
                    $('#error-add-item').text('');
                    var seller_location_id = from_location_value;
                    seller_id_list.unshift(seller_location_id);
                    var html = '<div class="table-row inner-block-bg request_row_' + num + '">';
                    var html = html+'<div class="col-md-2 padding-left-none" id="from_loc_'+ num +'">' + from_location + '</div>';
                    var html = html+'<div class="col-md-2 padding-left-none" id="vehicle_type_text_'+ num +'">' + vehicle_type_value + '</div>';
                    var html = html+'<div class="col-md-2 padding-left-none" id="lease_term_'+ num +'">' + lease_term_value + '</div>';
                    var html = html+'<div class="col-md-2 padding-left-none" id="driver_text_'+ num +'">' + driver_text + '</div>';
                    var html = html+'<div class="col-md-2 padding-left-none" id="quotes_quote_type_text_'+ num +'">' + Quotes_quote_type_text + '</div>';
                    var html = html+'<div class="col-md-1 padding-none" id="price_'+ num +'">' + price + '</div>';
                    var html = html+'<div class="class="col-md-1 padding-none"><a class="edit_this_tl edit" row_id="' + num + '"><i class="fa fa-edit red" title="Edit"></i></a>&nbsp;<a class="remove_this remove" row_id="' + num + '"><i class="fa fa-trash red" title="Delete"></i></a></div>';
                    var html = html+'<input type="hidden" name="from_location[]" class="from_location"  value="' + from_location_value + '" id="from_location_id_'+num+'">';
                    var html = html+'<input type="hidden" name="delivery_date[]" value="' + delivery_date + '" id="delivery_date_'+num+'">';
                    var html = html+'<input type="hidden" name="dispatch_date[]" value="' + dispatch_date + '" id="dispatch_date_'+num+'">';
                    var html = html+'<input type="hidden" name="lease_term[]" value="' + lease_term + '" id="lease_term_hiiden_'+num+'">';
                    var html = html+'<input type="hidden" name="vehicle_type[]" value="' + vehicle_type + '" id="vehicle_type_'+num+'">';
                    var html = html+'<input type="hidden" name="driver_id[]" value="' + need_diver + '" id="driver_id_'+num+'">';
                    var html = html+'<input type="hidden" name="fuel_inc_id[]" value="' + fuel_inc + '" id="fuel_inc_id_'+num+'">';
                    var html = html+'<input type="hidden" name="year_make_model[]" value="' + year_make_model + '" id="year_make_model_'+num+'">';
                    var html = html+'<input type="hidden" name="quote_id[]" value="' + Quotes_quote_type + '" id="quote_'+num+'">';
                    var html = html+'<input type="hidden" name="price[]" value="' + price + '">';
                    var html = html+'</div>';
                    $('.request_rows').append(html);
                    $('#from_location').val("");
                    $('#from_location_id').val("");
                    $('#year_make_model').val("");
                    $('#datepicker_to_location').val("");
                    $('#datepicker').val("");
                    $("#datepicker_to_location").datepicker("destroy");
                    $("#datepicker").datepicker("destroy");
                    $('#vehicle_type').val("");
                    $('#lease_type').val("");
                    $('#driver').val("");
                    $('#need_diver').attr('checked', false);
                    $('#quote_id').val("");
                    $('#price').val("");
                    $("#buyer_quote_items_truck_lease").validate().cancelSubmit = true;
                    $('.selectpicker').selectpicker('refresh');
                    $("#datepicker").datepicker({
                            changeMonth: true,
                            numberOfMonths: 1,
                            minDate: 0,
                            //show_flexible: 1,
                           // flex_identifier: "is_dispatch_flexible",
                            //flex_text: "Flexible dates",
                            dateFormat: "dd/mm/yy",
                            onClose: function(selectedDate) {
                                $("#delivery_date")
                                    .datepicker(
                                        "option",
                                        "minDate",
                                        selectedDate);
                            }
                        });
                    $("#datepicker_to_location").datepicker({
                            changeMonth: true,
                            numberOfMonths: 1,
                            //show_flexible: 1,
                            //flex_identifier: "is_delivery_flexible",
                            //flex_text: "Flexible dates",
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
                }
            });
    
    
    $('#add_buyer_quote_lease').click(function(e) {
        var id = $('.request_rows .table-row').children().size();
        if (id == 0) {
           // alert("Please add atleast one item to the list");
            $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        } else {
            $('#buyer_quote_truck_lease').submit();
            if($('#buyer_quote_truck_lease').valid()){
                $("#add_buyer_quote_lease").prop('disabled', true);                    
            }
            return true;
        }
    });
    
    $('#add_buyer_quote_lease').on('click', function() {
        $("#buyer_quote_truck_lease").valid();
    });
    
    $(document).on('click', '.edit_this_tl', function() {
   	 var rowid = $(this).attr("row_id");
	   	$("#update_line").val(1);
	   	$("#update_row_count").val(rowid);
	   	$('#from_location').val($("#from_loc_"+rowid).html());
	    $('#from_location_id').val($("#from_location_id_"+rowid).val());
	    if($("#driver_id_"+rowid).val() == 1) {
	    	$('#need_diver').prop('checked', true);
        }else{
        	$('#need_diver').prop('checked', false);
        }
	    $('#year_make_model').val($("#year_make_model_"+rowid).val());
	    $('#datepicker_to_location').val($("#delivery_date_"+rowid).val());
	    $('#datepicker').val($("#dispatch_date_"+rowid).val()); 
	    $('#vehicle_type').selectpicker('val',$("#vehicle_type_"+rowid).val());
	    $('#quote_id').selectpicker('val',$("#quote_"+rowid).val());
	    $('#lease_type').selectpicker('val',$("#lease_term_hiiden_"+rowid).val());
	    $('#driver').selectpicker('val',$("#fuel_inc_id_"+rowid).val());
	    $('#price').val($("#price_"+rowid).html());
    });
    
    
    
    $('.lease-create-posttype').click(function() {
        var id = $('.request_rows').children().size();
        var postingId = $(".lease-create-posttype:checked").val();
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
                     $("#post-private").prop("checked", false)
                     $("#post-public").prop("checked", true)
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
    
    $("#addbuyertlpostcounteroffer .add_buyer_counter_offer_details").click(function() {
        var rowNo = $(this).attr('id').substring($(this).attr('id').lastIndexOf("_") + 1);
        var $counterOfferFieldId = $('#buyer_post_counter_offer_' + rowNo);
        var counterOfferValue = $counterOfferFieldId.val();
        
        var regexPattern = /^\d{0,8}(\.\d{1,2})?$/;
        if (!counterOfferValue.trim()) {
            alert('Please enter counter offer!');
            $counterOfferFieldId.focus();
            return false;
        } else if (!regexPattern.test(counterOfferValue)) {
            alert(' Counter Offer should be a number!');
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
                    'counterOfferValue': counterOfferValue,
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
	
});
