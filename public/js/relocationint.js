$(function() {
	
	
	$('#oceanpayment_options').change(function(){
		
		var payment_options_value = $(this).val();
		
		if (payment_options_value == 4) {
			$("#oceanshow_credit_period").css("display", "block");
		}else{
			$("#oceanshow_credit_period").css("display", "none");
		}
		if (payment_options_value == 1) {
			$("#oceanshow_advanced_period").css("display", "block");
		}else{
			$("#oceanshow_advanced_period").css("display", "none");
		}
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
    
    $("#relocationint_spot").click(function(){
    	$(".relocation_spot_show").show();
    	$(".relocation_term_show").hide();
    	document.getElementById("posts-form_buyer_relocationair").reset();
    	$('.relocation_air_show').show();
    	$('.relocation_ocean_show').hide();        
    });
    
    $("#relocationint_term").click(function(){
    	$(".relocation_term_show").show();
    	$(".relocation_spot_show").hide();
    });
    
    
    
    $("#relocation_air").click(function(){
    	if($(".relocation_term_show").attr('style')=='display: block;' || $(".relocation_term_show").attr('style')==''){
    	$("#relocationair_term_firstform").trigger('reset');
        document.getElementById("term_relocbuyer_quote").reset();
        $('.relocation_ocean_field').hide();        
        $('.relocation_air_field').show();	
        $("#post_type_term").val("1");
        console.log($("#post_type_term").val());
        }else{	
    	$('.relocation_air_show').show();
    	$('.relocation_ocean_show').hide(); 
        $("#from_location_id").val("");
        $("#to_location_id").val("");
        }
    });
    
    $("#relocation_ocean").click(function(){
    	
    	if($(".relocation_term_show").attr('style')=='display: block;'  || $(".relocation_term_show").attr('style')==''){
    	$("#relocationair_term_firstform").trigger('reset');
        document.getElementById("term_relocbuyer_quote").reset();
        $('.relocation_ocean_field').show();        
        $('.relocation_air_field').hide();	
        console.log($("#post_type").val());
        $("#post_type_term").val("2");
        console.log($("#post_type_term").val());
    	}else{
        $("#posts-form_buyer_relocationair").trigger('reset');
        document.getElementById("posts-form_buyer_relocationair").reset();
        $('.relocation_ocean_show').show();        
    	$('.relocation_air_show').hide();
    	}
    });
    

    $("#int_air_spot").click(function(){
    	
        $('.relocation_int_air').show();
    	$('.relocation_int_ocean').hide();
    	$('#int_ocean_spot').attr('checked', false);

        document.getElementById("posts-form-lines_oceanint").reset();
        //$("#posts-form-lines_int").validate().resetForm();

        $("#posts-form-lines_oceanint #from_location_id").val('');
        $("#posts-form-lines_oceanint #to_location_id").val('');

    });
    $("#int_ocean_spot").click(function(){
    	
        $('.relocation_int_ocean').show();
    	$('.relocation_int_air').hide();
    	$('#int_air_spot').attr('checked', false);

        document.getElementById("posts-form-lines_int").reset();
        //$("#posts-form-lines_int").validate().resetForm();

        $("#posts-form-lines_int #from_location_id_intre").val('');
        $("#posts-form-lines_int #to_location_id_intre").val('');
    });
jQuery.validator.addMethod("threevalidationswithzero", function(value, element) {
    if(parseFloat(value)>0){
    	return this.optional(element) || /^\d{1,3}?$/i.test(parseFloat(element.value));
    }else{
    	return true;
    }
}, function(params, element) {
	if(element.value != ''){
    element.value  = Math.floor(element.value * 100) / 100;
	}
    var count_value = /^\d{1,3}?$/i.test(parseFloat(element.value));
    if(count_value == false){
     	return "Charges should be less than 1000"
    }	
    

});

    //Buyer post validation

$("#posts-form_buyer_relocationair").validate({
	ignore: "input[type='text']:hidden",
	rules : {
		"post_type" : {required : true},
		"valid_from" : {required : true},
		//"from_date" : {required : true},
				
		//"from_location" : {required : true},
		"from_location_id" : {required : true},
		//"to_location" : {required : true},
		"to_location_id" : {required : true},
		"agree" : {required : true},
                "cartons_1" : {
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
            },
		
	   },
		errorPlacement: function(error, element) {
//			if($(element).attr('type') == "checkbox" || $(element).attr('type') == "radio" ){
//				$(element).parent('div').parent('div').after(error);
//			}else{
				$(element).parent('div').after(error);
			//}
		},
		messages : {
			"valid_from" : {
				required : "Enter Dispatch Date",
			},
			"from_location_id" : {
				required: function() {
                                    if ($("#from_location").val() == "") {
                                        return "This field is required.";
                                    } else  {
                                        return "Please enter valid from location";
                                    }
                                }
			},
			"to_location_id" : {
				required: function() {
                                    if ($("#to_location").val() == "") {
                                        return "This field is required.";
                                    } else  {
                                        return "Please enter valid to location";
                                    }
                                }
			},
			
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
			}//alert($(".cartons").length);
                        var flag=0;
                        $( ".cartons" ).each(function( index ) {
                            if($(this).val()!=""){
                                flag=1;
                            }
                        });
                        if(flag==0){
                            $("#erroralertmodal .modal-body").html("Please add one carton type atleast.");
                                 $("#erroralertmodal").modal({
                                  show: true
                                 })
                            return false;
			}
			//return false;
			form.submit();
		}
	});

/*
 * Add code for relocation international save and continue inventory details for getquote
 * @ Srininivas , Date :May 23th,2016
 */
$('.save-continue-rel-intocean').click(function() {
	
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
        url: '/saveinventorydetailsrelocean?contractprice=' + contractprice,
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

    $('.save-continue-rel-intocean-search').click(function() {

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



$( "#from_location_intre" ).keyup(function(e) {
	if (e.which !== 13) {
		var from_id_hidden = $('#from_location_id_intre').val("");
		if (from_id_hidden != '') {
		}
	}
});

$( "#to_location_intre" ).keyup(function(e) {
	if (e.which !== 13) {
		var to_id_hidden = $('#to_location_id_intre').val("");
		if (to_id_hidden != '') {
		}
	}
}); 


/******************************From Location starts FTL*********************************************/
$(document).on('focus click keyup keypress blur change', '#from_location_intre', function() {
	$( "#from_location_intre" ).autocomplete({
            source: "/autocomplete?fromlocation="+$('#to_location_id_intre').val(),
            minLength: 1,
            select: function(event, ui) {
				$('#from_location_intre').val(ui.item.value);
                $('#from_location_id_intre').val(ui.item.id);
                $('#seller_district_id_intre').val(ui.item.dist_id);
				$(this).closest("form").validate().element($('#from_location_id_intre'));
            }
	});
});

$(document).on('focus click keyup keypress blur change', '#to_location_intre', function() {
        $( "#to_location_intre" ).autocomplete({
            source: "/autocomplete?fromlocation="+$('#from_location_id_intre').val(),
            minLength: 1,
            select: function(event, ui) {
                $('#to_location_intre').val(ui.item.value);
                $('#to_location_id_intre').val(ui.item.id);
				$(this).closest("form").validate().element($('#to_location_id_intre'));
            },
        });
});
/******************************From Location ends FTL*********************************************/
//Multi Line item for Int-ocean
$('#add_more_int_ocean').on('click', function() {
    $("#posts-form-lines-int-ocean").valid();
});


/*** Int Ocean **/

$("#posts-form-lines-int-ocean").validate({
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
        "shipment_types" : {
            required : true,
        },
        "volumetype" : {
            required : true,
        },
        "Odcharges" : {
            required : true,
            number: true,
            fourbytwovalidations:true,
        },
        "freightcharge" : {
            required : true,
            number: true,
            //fourbytwovalidations:true,
        },
        "oceantransitdays" : {
            required : true,
            digits: true,
            transitvalidation:true,
            rangelength: [0,3]
        }
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
            required: function() {
                if ($("#from_location").val() == "") {
                    return "This field is required.";
                } else  {
                    return "Please enter valid from location";
                }
            }
        },
        "to_location" : {
            required : "",
        },
        "to_location_id" : {
            required: function() {
                if ($("#to_location").val() == "") {
                    return "This field is required.";
                } else  {
                    return "Please enter valid to location";
                }
            }
        },
        "shipment_types" : {
        	required : "Shipment Type is required",
        },
        "volumetype" : {
        	required : "Volume Type is required",
        },
        "Odcharges" : {
        	required : "O & D charges is required",
        },
        "freightcharge" : {
        	required : "Freight is required",
        },
        "oceantransitdays" : {
            required : "Transit Days is required",
        }
    },
    submitHandler : function(form) {
        form.submit();
    }
});

/******************************Multi line add items*********************************************/
var sel_list = new Array();
$('#add_more_int_ocean').click(function() {
	var num = parseInt($('#next_add_more_id').val()) + 1;
    $('#next_add_more_id').val(num);
    var from_location = $('#from_location').val();
    var to_location = $('#to_location').val();
    var int_air_coean = $('#int_air_coean').val();
    
    var from_location_identifier = $('#from_location_id').val();
    var to_location_identifier = $('#to_location_id').val();
    
    var datepicker_from_value = $('#datepicker').val();
    var datepicker_from_value = convertDateFormatForDatePickerSeller(datepicker_from_value);
    var datepicker_to_value = $('#datepicker_to_location').val();
    var datepicker_to_value = convertDateFormatForDatePickerSeller(datepicker_to_value);
    
    
    var seller_district = $('#seller_district_id').val();
    
    var ts_from = Date.parse(datepicker_from_value);
    var ts_to = Date.parse(datepicker_to_value);
    
    var shipment_types = $('#shipment_types').val();
    var shipment_types_text = $('#shipment_types option:selected' ).text();
    var volumetype = $('#volumetype').val();
    var volumetypetext = $('#volumetype option:selected' ).text();
    var Odcharges = $('#Odcharges').val();
    var freightcharge = $('#freightcharge').val();
    if(shipment_types == 1){
    	var freight_units =" (Rs per CBM)";
    }else{
    	var freight_units =" (Rs Flat)";
    }
    
    var transit = $('#oceantransitdays').val();
    var units = $('#oceantransitdays_units').val();
    var transit_numric = /^[0-9]{1,3}$/.test(transit);
    var vehicle_type_value = $( "#vechile_type option:selected" ).text();
    var subscription_start_date_start_val = $('#ocen_subscription_start_date_start').val();
    var subscription_end_date_end_val = $('#ocen_subscription_end_date_end').val();
    var current_date_seller = $('#ocen_current_date_seller').val();
    
    if((datepicker_from_value > subscription_end_date_end_val) || (current_date_seller < subscription_start_date_start_val) || (datepicker_from_value < subscription_start_date_start_val)){
    	var end_date_subscription = "from date";
    }else{
    	var end_date_subscription = "to date";
    }
   
    if (to_location_identifier != '' && from_location_identifier != '' && datepicker_from_value != null && datepicker_to_value != null && from_location != '' && to_location != '' && shipment_types!='' && volumetype!='' && Odcharges!='' && freightcharge!='' && transit != '' && transit != 0 && transit_numric == true) {
    if((current_date_seller >= subscription_start_date_start_val) && (current_date_seller <= subscription_end_date_end_val) && (datepicker_from_value >= subscription_start_date_start_val) && (datepicker_from_value <= subscription_end_date_end_val) && (datepicker_to_value >= subscription_start_date_start_val) && (datepicker_to_value <= subscription_end_date_end_val)){
    if (to_location_identifier != '' && from_location_identifier != '' && datepicker_from_value != '' && datepicker_to_value != '' && from_location != '' && to_location != ''  && shipment_types!='' && volumetype!='' && Odcharges!='' && freightcharge!='' && transit != '' && transit != 0 && transit_numric == true) {
        var unique = from_location_identifier+to_location_identifier+shipment_types+volumetype;


        if ($.inArray(unique,sel_list)==-1) {
            sel_list.unshift(unique);
        $.ajax({
            type : 'post',
            url : '/lineitemscheck',
            data : {
                'from_location' : from_location_identifier,
                'to_location' : to_location_identifier,
                'int_air_coean' : int_air_coean,
                'from_date_seller' : datepicker_from_value,
                'to_date_seller' : datepicker_to_value,
                'transit_days' : 1
            },
            dataType : "html",
            type : 'POST',
            success : function(data) {
            	
                if (data != '1') {
                	
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
                        + '"><div class="col-md-3 padding-left-none from_location_text" id="shipping_type_text_'+num+'">'
                        + shipment_types_text
                        + '</div><div class="col-md-2 padding-left-none " id="volume_type_text_'+num+'">'
                        + volumetypetext
                        + '</div><div class="col-md-2 padding-left-none " id="odcharge_'+num+'">'
                        + Odcharges + '  /- (Rs per CBM)'
                        + '</div><div class="col-md-2 padding-left-none " id="freightcharge_'+num+'">'
                        + freightcharge +' /-'+ freight_units
                        + '</div><div class="col-md-2 padding-left-none " id="transit_'+num+'">'
                        + transit +' '+ units
                        + '</div><div class="col-md-1 padding-none"><a href="javascript:void(0)" style ="cursor:pointer; margin-right:10px;" onclick="updatecreatepostlineitem('+num+')" class="edit_this_line_int_ocean edit" data-string="'+unique+'" row_id="'
                        + num
                        + '"><i class="fa fa-edit red" title="Edit"></i></a><a style ="cursor:pointer;" class="remove_this_line_item remove" data-string="'+unique+'" row_id="'
                        + num
                        + '"><i class="fa fa-trash red" title="Delete"></i></a></div><input type="hidden" name="from_location[]" id="from_location_id_'+num+'" value="'
                        + from_location_identifier
                        + '"><input type="hidden" name="to_location[]" id="to_location_id_'+num+'" value="'
                        + to_location_identifier
                        + '"><input type="hidden" name="shipment_types[]" id="shipment_types_'+num+'" value="'
                        + shipment_types
                        + '"><input type="hidden" name="volume_types[]" id="volume_types_'+num+'" value="'
                        + volumetype
                        + '"><input type="hidden" name="od_charge[]" id="od_charge_'+num+'" value="'
                        + Odcharges
                        + '"><input type="hidden" name="freight_charge[]" id="freight_charge_'+num+'" value="'
                        + freightcharge 
                        + '"><input type="hidden" name="transitdays[]" id="transitdays_'+num+'" value="'
                        + transit
                        + '"><input type="hidden" name="units[]" id="units_'+num+'" value="'
                        + units
                        + '"><input type="hidden" name="sellerdistrict[]" id="sellerdistricts_'+num+'" value="'
                        + seller_district
                        + '"><div class="clearfix"></div></div>';

                    $("#multi-line-itemes").show();
                    $('.request_rows').append(html);
                    var id_line_itemes = $('.request_rows').children().size();
                    if (id_line_itemes == 0){
                    }else{
                    	$("#datepicker").prop('disabled', true);
                    	$("#datepicker_to_location").prop('disabled', true);
                    }
                    $("#ocean_valid_from_val").val(datepicker_from_value);
                    $("#ocean_valid_to_val").val(datepicker_to_value);
                    $('#from_location').val(from_location);
                    $('#from_location_id').val(from_location_identifier);
                    $('#to_location').val(to_location);
                    $('#to_location_id').val(to_location_identifier);
                    $('#shipment_types').val("");
                    $('#oceantransitdays_units').val("");
                    $('#volumetype').val("");
                    $('#Odcharges').val("");
                    $('#freightcharge').val("");
                    $('#oceantransitdays').val("");
                    $('#from_location').prop('disabled', true);
                    $('#to_location').prop('disabled', true);
                    $('#add_more_update_inter').show();
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



$(document).on('click', '#add_more_update_inter', function() {
	
	///alert("hello");
	var sel_list = new Array();
    if($("#posts-form-lines-int-ocean").valid()) {
    	
    	
        var current_row_id = $("#current_row_id").val();
        var from_location = $('#from_location').val();
        var to_location = $('#to_location').val();
        var int_air_coean = $('#int_air_coean').val();
        var from_location_identifier = $('#from_location_id').val();
        var to_location_identifier = $('#to_location_id').val();
        
        var datepicker_from_value = $('#datepicker').val();
        var datepicker_from_value = convertDateFormatForDatePickerSeller(datepicker_from_value);
        var datepicker_to_value = $('#datepicker_to_location').val();
        var datepicker_to_value = convertDateFormatForDatePickerSeller(datepicker_to_value);
        
        
        var seller_district = $('#seller_district_id').val();
        
        var ts_from = Date.parse(datepicker_from_value);
        var ts_to = Date.parse(datepicker_to_value);
        
        var shipment_types = $('#shipment_types').val();
        var shipment_types_text = $('#shipment_types option:selected' ).text();
        var volumetype = $('#volumetype').val();
        var volumetypetext = $('#volumetype option:selected' ).text();
        var Odcharges = $('#Odcharges').val();
        var freightcharge = $('#freightcharge').val();
        if(shipment_types == 1){
        	var freight_units =" (Rs per CBM)";
        }else{
        	var freight_units =" (Rs Flat)";
        }
        
        var transit = $('#oceantransitdays').val();
        var units = $('#oceantransitdays_units').val();
        
        var transit_text = $('#oceantransitdays').val()+''+$('#oceantransitdays_units').val();
        
        var transit_numric = /^[0-9]{1,3}$/.test(transit);
        var vehicle_type_value = $( "#vechile_type option:selected" ).text();
        var subscription_start_date_start_val = $('#ocen_subscription_start_date_start').val();
        var subscription_end_date_end_val = $('#ocen_subscription_end_date_end').val();
        var current_date_seller = $('#ocen_current_date_seller').val();
        
        if((datepicker_from_value > subscription_end_date_end_val) || (current_date_seller < subscription_start_date_start_val) || (datepicker_from_value < subscription_start_date_start_val)){
        	var end_date_subscription = "from date";
        }else{
        	var end_date_subscription = "to date";
        }
       
        if (to_location_identifier != '' && from_location_identifier != '' && datepicker_from_value != null && datepicker_to_value != null && from_location != '' && to_location != '' && shipment_types!='' && volumetype!='' && Odcharges!='' && freightcharge!='' && transit != '' && transit != 0 && transit_numric == true) {
        if((current_date_seller >= subscription_start_date_start_val) && (current_date_seller <= subscription_end_date_end_val) && (datepicker_from_value >= subscription_start_date_start_val) && (datepicker_from_value <= subscription_end_date_end_val) && (datepicker_to_value >= subscription_start_date_start_val) && (datepicker_to_value <= subscription_end_date_end_val)){
        if (to_location_identifier != '' && from_location_identifier != '' && datepicker_from_value != '' && datepicker_to_value != '' && from_location != '' && to_location != ''  && shipment_types!='' && volumetype!='' && Odcharges!='' && freightcharge!='' && transit != '' && transit != 0 && transit_numric == true) {
            var unique = shipment_types+volumetype;


            if ($.inArray(unique,sel_list)==-1) {
                sel_list.unshift(unique);
            $.ajax({
                type : 'post',
                url : '/lineitemscheck',
                data : {
                    'from_location' : from_location_identifier,
                    'to_location' : to_location_identifier,
                    'int_air_coean' : int_air_coean,
                    'from_date_seller' : datepicker_from_value,
                    'post_item_id': current_row_id,
                    'to_date_seller' : datepicker_to_value,
                    'transit_days' : 1
                },
                dataType : "html",
                type : 'POST',
                success : function(data) {
                                if (data == '0') {
                                    //row updates
                                    var rowid = "#single_post_item_" + current_row_id;
                                    $(rowid + " .shipment_text").html(shipment_types_text);
                                    $(rowid + " .volume_text").html(volumetypetext);
                                    $(rowid + " .od_charges_text").html(Odcharges);
                                    $(rowid + " .freight_charges_text").html(freightcharge);
                                    $(rowid + " .transitdays_text").html(transit_text);
                                    $(rowid + " input[name='shipment_type[]']").val(shipment_types);
                                    $(rowid + " input[name='shipment_volume[][]']").val(volumetype);
                                    $(rowid + " input[name='od_charges[][]']").val(Odcharges);
                                    $(rowid + " input[name='transitdays[]']").val(transit);
                                    $(rowid + " input[name='freight_charges[]']").val(freightcharge);
                                    $(rowid + " input[name='units[]']").val(units);
                                    
                                    var id_line_itemes = $('.request_rows').children().size();
                                    if (id_line_itemes == 0) {
                                    } else {
                                        $("#datepicker").prop('disabled', true);
                                    }
                                    $("#valid_from_val").val(datepicker_from_value);
                                    $("#valid_to_val").val(datepicker_to_value);
                                    $('#from_location').val(from_location);
                                    $('#from_location_id').val(from_location_identifier);
                                    $('#to_location').val(to_location);
                                    $('#to_location_id').val(to_location_identifier);
                                    $('#shipment_types').val("");
                                    $('#oceantransitdays_units').val("");
                                    $('#volumetype').val("");
                                    $('#Odcharges').val("");
                                    $('#freightcharge').val("");
                                    $('#oceantransitdays').val("");
                                    $('#from_location').prop('disabled', true);
                                    $('#to_location').prop('disabled', true);
                                    $('#add_more_update_inter').hide();
                                    $('.selectpicker').selectpicker('refresh');

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


    $(document).on('click', '#add_quote_seller_id_confirm_int', function() {
        var todatevalidation = $("#posts-form-lines-int-ocean").validate().element($('#datepicker_to_location'));
        if(todatevalidation){
            return true;
        }
        return false;
    });


/******************************Multi line Remove items*********************************************/
$(document).on('click', '.edit_this_line_int_ocean', function() {
	
    var rowid = $(this).attr("row_id");
	
    remove_val = $(this).attr("data-string");
    sel_list.splice($.inArray(remove_val, sel_list),1);
	 $("#update_ftl_seller_line").val(1);
 	 $("#update_ftl_seller_row_count").val(rowid);
 

 	$('#shipment_types').selectpicker('val',$("#shipment_types_"+rowid).val());
 	CheckVolume($("#shipment_types_"+rowid).val());
 	setTimeout(function(){
 		$('#volumetype').selectpicker('val',$("#volume_types_"+rowid).val());
 		}, 1000);
    
    $('#Odcharges').val($("#od_charge_"+rowid).val());
    $('#freightcharge').val($("#freight_charge_"+rowid).val());
    $('#oceantransitdays').val($("#transitdays_"+rowid).val());
    $('#oceantransitdays_units').selectpicker('val',$("#units_"+rowid).val());
    
	
});




/* @end
 * @Srinivas , Date :May 23th,2016
 * @end relocation ocean inventory details set
 */
	
    /******************************Save as draft functionality*********************************************/
	$('#add_quote_seller_int').click(function(e) {
		e.preventDefault();
		$('#posts-form-lines_int').submit();
		if($('#posts-form-lines_int').valid()){
			$("#add_quote_seller_id_int").prop('disabled', true);
			$("#add_quote_seller_int").prop('disabled', true);
		}
		
	});
	
/******************************Save as draft functionality*********************************************/
/******************************confirm functionality*********************************************/
	$('#add_quote_seller_id_int').click(function(e) {
			e.preventDefault();
			if($("#posts-form-lines_int").valid()) {
			var submitData=$("#posts-form-lines_int").serialize();
			var btnName = $('#add_quote_seller_id_int').attr('name');
			$("#add_quote_seller_id_int").prop('disabled', true);
    		$("#add_quote_seller_int").prop('disabled', true);
            var btnVal = $('#add_quote_seller_id_int').val();
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
	
	
	 $('#add_quote_seller_oceanint').click(function(e) {
         e.preventDefault();
         var id=$('.request_rows').children().size();
         if(id==0){
             $("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
             $("#erroralertmodal").modal({
                 show: true
             });
             return false;
         }else{
             $('#posts-form-lines_oceanint').submit();
             if($('#posts-form-lines_oceanint').valid()){
                 $("#add_quote_seller_id_oceanint").prop('disabled', true);
                 $("#add_quote_seller_oceanint").prop('disabled', true);
             }
         }
     });
	
	
	
	$('#add_quote_seller_id_oceanint').click(function(e) {
		e.preventDefault();
		 var id=$('.request_rows').children().size();
         if(id==0){
             $("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
             $("#erroralertmodal").modal({
                 show: true
             });
             return false;
         }else{
		
		if($("#posts-form-lines_oceanint").valid()) {
			
		var submitData=$("#posts-form-lines_oceanint").serialize();
		var btnName = $('#add_quote_seller_id_oceanint').attr('name');
		$("#add_quote_seller_id_oceanint").prop('disabled', true);
		$("#add_quote_seller_oceanint").prop('disabled', true);
        var btnVal = $('#add_quote_seller_id_oceanint').val();
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
      }
});
	
	
	var sel_list_terms = new Array();
	$('.my-form-ocen .add-box-ocen').click(function(){
	var n = $('.update_txt_test_ocen').length;
    var num = parseInt($('#next_terms_count_search_ocen').val()) + 1;
    $('#next_terms_count_search_ocen').val(num);
    if( 2 < n ) {
            $("#erroralertmodal .modal-body").html("You can add 5 charges only !");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        }
        var box_html = $('<div class="text-box form-control-fld terms-and-conditions-block"><div class="col-md-3 col-sm-4 col-xs-5 padding-none tc-block-fld padding-left-none labelcharges"><div class="input-prepend"><input type="text" name="labeltext_' + num + '"  placeholder= "Other Charges" class="form-control form-control1 labelcharges dynamic_labelcharges" value=""  /></div></div>   <div class="col-md-3"><div class="input-prepend"><input type="text" class="form-control form-control1 pricebox update_txt_test_ocen update_txt dynamic_validations numberVal" placeholder ="0.00" name="terms_condtion_types_' + num + '" value="" id="box_' + num + '" /><span class="add-on unit">Rs</span></div></div> <a href="#" class="remove-box" data-string="'+num+'"><i class="fa fa-trash red" title="Delete"></i></a></div>');
        box_html.hide();
        $('.my-form-ocen div.text-box:last').after(box_html);
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
	
	 $('.my-form-ocen').on('click', '.remove-box', function(){
	        $(this).parent().fadeOut("fast", function() {
	        	remove_val_terms = $(this).attr("data-string");
	            $(this).remove();
	            $('.box-number').each(function(index){
	                $(this).text( index + 1 );
	            });
	        });
	        return false;
	    });
	/******************************buyer List For Seller Starts*********************************************/
	$('.create-posttype-service-ocen').click(function(){
		 posttype_val = $(".create-posttype-service-ocen:checked").val();
		if (posttype_val == 2){
			$(".demo-input_buyers_ocen").css("display", "block");
		}else{
			$(".demo-input_buyers_ocen").css("display", "none");
		}
	});


/******************************buyer List For Seller ends*********************************************/
	
/******************************confirm functionality*********************************************/
	 $("#posts-form-lines_int").validate({
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
	            "from_location_intre" : {
	                required : true,
	            },
	            "to_location_intre" : {
	                required : true,
	            },
	            "to_location_id" : {
	                required : true,
	            },
	            "freightcharge_1" : {
					number: true,
					fourbytwovalidations: {
		            	depends: function(element) {
		            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }

				},
	            "freightcharge_2" : {
					number: true,
					fourbytwovalidations: {
		            	depends: function(element) {
		            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }

				},
	            "freightcharge_3" : {
					number: true,
					fourbytwovalidations: {
		            	depends: function(element) {
		            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }

				},
	            "freightcharge_4" : {
					number: true,
					fourbytwovalidations: {
		            	depends: function(element) {
		            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }

				},
	            "freightcharge_5" : {
					number: true,
					fourbytwovalidations: {
		            	depends: function(element) {
		            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }

				},
	            "freightcharge_6" : {
					number: true,
					fourbytwovalidations: {
		            	depends: function(element) {
		            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }

				},
	            "odcharges_1" : {
					number: true,
					fourbytwovalidations: {
		            	depends: function(element) {
		            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }

				},
	            "odcharges_2" : {
					number: true,
					fourbytwovalidations: {
		            	depends: function(element) {
		            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }

				},
	            "odcharges_3" : {
					number: true,
					fourbytwovalidations: {
		            	depends: function(element) {
		            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }

				},
	            "odcharges_4" : {
					number: true,
					fourbytwovalidations: {
		            	depends: function(element) {
		            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }

				},
	            "odcharges_5" : {
					number: true,
					fourbytwovalidations: {
		            	depends: function(element) {
		            		if ($('#sellerpoststatus').val() == 1 || $('#sellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }

				},
	            "odcharges_6" : {
					number: true,
					fourbytwovalidations: {
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
				"storate_charges" : {
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
	            "terms_condtion_types_1" : {
	                number: true,
	                fourbytwovalidationswithzero: {
		            	depends: function(element) {
		            		if ($('#oceansellerpoststatus').val() == 1 || $('#oceansellerpoststatus').val() == 0){
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
		            		if ($('#oceansellerpoststatus').val() == 1 || $('#oceansellerpoststatus').val() == 0){
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
		            		if ($('#oceansellerpoststatus').val() == 1 || $('#oceansellerpoststatus').val() == 0){
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
	            "transitdays" : {
	                required : true,
	                digits: true,
	                transitvalidation:true,
	                rangelength: [0,3]
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
	        	//$(element).parent('div').after(error);
	        	 $(element).parent().parent().append(error);
	        },
	        messages : {
	            "valid_from" : {
	                required : "Valid From date is required",
	            },
	            "valid_to" : {
	                required : "Valid To date is required",
	            },
	            "from_location_intre" : {
	                required : "",
	            },
	            "from_location_id" : {
	                required: function() {
	                    if ($("#from_location_intre").val() == "") {
	                        return "This field is required.";
	                    } else  {
	                        return "Please enter valid from location";
	                    }
	                }
	            },
	            "to_location_intre" : {
	                required : "",
	            },
	            "to_location_id" : {
	                required: function() {
	                    if ($("#to_location_intre").val() == "") {
	                        return "This field is required.";
	                    } else  {
	                        return "Please enter valid to location";
	                    }
	                }
	            },
	            "transitdays" : {
	                required : "Transit Days is required",
	            }
	        },
	        submitHandler : function(form) {
	            form.submit();
	        }
	    });


	 
	 
	 $("#posts-form-lines_oceanint").validate({
		 ignore: [],
	        rules : {
	            "crating_charges" : {
	                required : true,
	                number: true,
	                fourbytwovalidations:true,
	            },
	            "ocen_tracking" : {
	                required : {
		            	depends: function(element) {
		            		if ($('#oceansellerpoststatus').val() == 1){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }
	            },
	            "accept_payment_ocen[]" : {
	                required : {
		            	depends: function(element) {
		            		if ($('#oceansellerpoststatus').val() == 1){
			            	if ($('#oceanpayment_options').val() == 1){
			            		return true;
			            	}else{
			            		return false;
			            	}
		            	}
		            	}
	                }
	            },
			    "buyer_list_for_sellers_ocen" : {
			        required : {
			        	depends: function(element) {
			        		if ($('#oceansellerpoststatus').val() == 1){
			        			if ($(".create-posttype-service-ocen:checked").val() == 2){
			            		return true;
			            	}else{
			            		return false;
			            	}
			        	}
			        	}
			        }
			    },
	            "credit_period_ocen" : {
	            	required : {
		            	depends: function(element) {
		            		if ($('#oceansellerpoststatus').val() == 1){
			            	if ($('#oceanpayment_options').val() == 4){
			            		return true;
			            	}else{
			            		return false;
			            	}
		            	}
		            	}
	                },
	                creditvalidation:{
		            	depends: function(element) {
		            		if ($('#oceansellerpoststatus').val() == 1){
			            	if ($('#oceanpayment_options').val() == 4){
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
					fourbytwovalidationswithzero: {
		            	depends: function(element) {
		            		if ($('#oceansellerpoststatus').val() == 1 || $('#oceansellerpoststatus').val() == 0){
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
		            		if ($('#oceansellerpoststatus').val() == 1 || $('#oceansellerpoststatus').val() == 0){
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
		            		if ($('#oceansellerpoststatus').val() == 1 || $('#oceansellerpoststatus').val() == 0){
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
		            		if ($('#oceansellerpoststatus').val() == 1 || $('#oceansellerpoststatus').val() == 0){
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
		            		if ($('#oceansellerpoststatus').val() == 1 || $('#oceansellerpoststatus').val() == 0){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }
	            	},
	            "accept_credit_netbanking_ocen[]" : {
	                required : {
		            	depends: function(element) {
		            		if ($('#oceansellerpoststatus').val() == 1){
			            	if ($('#oceanpayment_options').val() == 4){
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
		            		if ($('#oceansellerpoststatus').val() == 1){
			            		return true;
			            	}else{
			            		return false;
			            	}

		            	}
	                }
	            }

	        },
	        errorPlacement: function(error, element) {
	        	//$(element).parent('div').after(error);
	        	$(element).parent().parent().append(error);
	        },
	        messages : {
	        },
	        submitHandler : function(form) {
	        	form.submit();
	        }
	    });
	 
	 
    /********* Start : Sellersearchfor buyer Spot Air / Ocean Validation *****/  
    
    $("#spot_service_air").click(function(){
        $("#show_spot_air").show();
        $("#show_spot_ocean").hide();
    });
    
    $("#spot_service_ocean").click(function(){
        $("#show_spot_ocean").show();
        $("#show_spot_air").hide();
    });
     

    //Sellersearchfor buyer relocation internationam spot Air / ocean  form validation
    $("#relocation_international_sellersearch_buyers_spot").validate({ // initialize the
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
//          "valid_to": {
//              required: true,
//          },  
            
        },
        errorPlacement: function(error, element) {
            $(element).parent('div').after(error); 
        },
        messages: {
            "from_location": {
                required: "",
            },
            "from_location_id": {
                required: "From Location should be required",
            },
            "to_location": {
                required: "",
            },
            "to_location_id": {
                required: "To Location should be required",
            },
            "valid_from": {
                required: "Select Dispatch Date",
            },   
//          "valid_to": {
//              required: "Select Delivery Date",
//          },  
            
        },
        submitHandler: function(form) { // for demo
            $(element).parent('div').after(error);
        }
    });

    /********* END : Search Spot Air / Ocean Validation *****/  
    
 /*
 * Add code for relocation international save and continue inventory details for getquote
 * @ Srininivas , Date :May 24th,2016.
 */    
    $("#relocationint_ocean_getquote").validate({       
        ignore: [],
        rules: {
            "from_location_intre": {
                required: true,
            },
            "from_location_id_intre": {
                required: true,
            },
            "to_location_intre": {
                required: true,
            },
            "to_location_id_intre": {
                required: true,
            },
            "valid_from": {
                required: true,
            }, 
            "property_type": {
                required: true,
            },
            "ptlQuoteaccessId": {
                required: true,
            },
             "agree": {
                required: true,
            },
            "seller_list" : {
                required : {
                    depends: function(element) {
                        if ($(".create-relocationint-ocean:checked").val() == 2){           			
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
            "from_location_intre": {
                required: "",
            },
            "from_location_id_intre": {
                required: function() {
                    if ($("#from_location_intre").val() == "") {
                        return "This field is required.";
                    } else  {
                        return "Please enter valid to location";
                    }
                }
            },
            "to_location_intre": {
                required: "",
            },
            "to_location_id_intre": {
                required: function() {
                    if ($("#to_location_intre").val() == "") {
                        return "This field is required.";
                    } else  {
                        return "Please enter valid to location";
                    }
                }
            },
            "valid_from": {
                required: "Select Dispatch Date",
            }, 
            "property_type": {
                required: "Select Property Type",
            }, 
        },
        submitHandler: function(form) {
            form.submit();
        }
    });
    
 /*
 * End relocation international save and continue inventory details for getquote
 * @ Srininivas , Date :May 24th,2016.
 */  
   
    
    
    $('.crete-relocationair').click(function() {
    	
    	var postingId = $(".crete-relocationair:checked").val();
    	
    	if (postingId == 1) {
    		$("#hideseller").css("display", "none");
    	} else if (postingId == 2) {
   		 $("#hideseller").css("display", "block");
         $.ajax({
             url: '/getSellerslist',
             type: "post",
             data: {
                 'post_type': $('#post_type').val(),
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
    
    
     $('.create-relocationint-ocean').click(function() {
    	var post_type=2;        
    	var postingId = $(".create-relocationint-ocean:checked").val();    	
    	if (postingId == 1) {
    		$("#hideseller_relocean").css("display", "none");
    	} else if (postingId == 2) {
   		 $("#hideseller_relocean").css("display", "block");
         $.ajax({
             url: '/getSellerslist',
             type: "post",
             data: {
                 'post_type': post_type,
                 '_token': $('input[name=_token]').val()
             },
             success: function(data) {
                 if (data!=null & data!='') {
                     $(".token-input-list").remove();
                     $("#demo-input-local-relocean").tokenInput(data);
                 } else {
                	 $("#hideseller_relocean").css("display", "none");                     
                         $("#erroralertmodal .modal-body").html("No sellers available.");
    		         $("#erroralertmodal").modal({
    		             show: true
    		         });                    
                     $('#post-public-relocean').attr('checked', true);
                     $('#post-private-relocean').attr('checked', false);
                     return false;
                 }
             },
             error: function(request, status, error) {
            	 $('#post-public-relocean').attr('checked', true);
                 $("#hideseller_relocean").css("display", "none");
                 alert(error);
             },
         });	 
      }    	
    
  });
    
	
    $("#shipment_types").change(function() {   
    	
    	var shipment = $('#shipment_types').val();
    	
    	CheckVolume(shipment);
    	//alert(quantity);
    });
    
    
});

function oceanupdatepoststatus(status){
	$("#oceansellerpoststatus").val(status);
}

function getRelIntOceanRoomParticulars(){	
	
	var data = {
	        'room_id': $('#room_type').val()
	    };
	 $.ajax({
	        type: "GET",
	        url: '/getpropertyparticulars',
	        data: data,
	        dataType: 'json',
	        success: function(data) {
	             $("#inventory_data").html(data.html);	           
	        },
	        error: function(request, status, error) {	            
	        },
	    });	
}


//Shiupment and volume selection
function CheckVolume(shipment) {
    var data = {
       
        'shipment': shipment
    };
   
    $.ajax({
        type: "GET",
        url: '/getvolumetype',
        data: data,
        dataType: 'text',
        success: function(data) {
        	
        	 var myarr = data.split("-");
        	 $("#volumetype").html(data);
             $('.selectpicker').selectpicker('refresh');
             if(shipment==1)
         		$("#freightcharge").attr("placeholder", "Freight (Rs per CBM)*");
         	else
         		$("#freightcharge").attr("placeholder", "Freight (Rs Flat)*");
        },
        error: function(request, status, error) {
            $('#volumetype').val(null);
            $('#volumetype').selectpicker('refresh');
        },
    });
}
