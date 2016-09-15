$(function() {
	/*Hide and show spot and term divs 
	 */
	$("#spot_lead_type").click(function(){
        $(".showhide_spot").show();
        $(".showhide_term").hide();
        $(".spot_or_term").val(1);
        $('#ftl_term_insert').trigger("reset");
        $('#term_buyer_quote').trigger("reset");
        $('#term_load_type, #term_vehicle_type').val('');
        $('.selectpicker').selectpicker('refresh');
    });
    $("#term_lead_radio,#term_lead_type").click(function(){
        $(".showhide_term").show();
        $(".showhide_spot").hide();
        $(".spot_or_term").val(2);
        $('#buyer_quotelineitems_form_validation').trigger("reset");    
        $('#buyer_quote_form').trigger("reset");        
    });
    
    //Relcoation Term and Spot Show hide functioanlity
    $("#relocation_spot").click(function(){
    	$(".relocation_spot_show").show();
    	$(".relocation_term_show").hide();
    	document.getElementById("posts-form_buyer_relocation").reset();
    	$('#vehicle_category').selectpicker('referesh');
    	$('#vehicle_category_type').selectpicker('referesh');
        
    });
    $("#relocation_term").click(function(){
    	$(".relocation_term_show").show();
    	$(".relocation_spot_show").hide();
    	
    	document.getElementById("relocation_term_firstform").reset();
    	document.getElementById("term_relocbuyer_quote").reset();
    	//$('#relocation_term_firstform').trigger("reset");
       // $('#term_relocbuyer_quote').trigger("reset");
    	$('#term_vehicle_category').selectpicker('referesh');
    	$('#term_vehicle_category_type').selectpicker('referesh');
    	$("#term_dispatch_date").attr("disabled",false);
    	$("#term_delivery_date").attr("disabled",false);
        $(".term_relocation_hhg_buyer_create").show();
        $(".term_relocation_vehicle_buyer_create").hide();
        $(".vehiclegrid").hide();
        $(".hhggrid").show();
        $(".table-row").remove();
        
        
    });
  
   //Check term view count update
    $(".checkviewcountdiv").click(function(){
    	var termBuyerQuoteid	=	$( this ).attr( "id" );   
    	//var checkBlock=$('.term_quote_details_'+termBuyerQuoteid).css('display');      	
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
    	});
    
    /********Cancel term contract**********/
    $(".cancel_buyer_term").click(function() {
        var message = confirm("Are you sure want to cancel the Contract!");
        if (message == true) {
            var id = $(this).data("id");
            
           
			 $.ajax({
		           type: "GET",
		           url: '/cancelbuyerterm/'+id,
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
		           data: '', // serializes the form's elements.
		           success: function(msg)
		           {
		        	  if(msg==2){
		        		  
		        		  $("#erroralertmodal .modal-body").html("This Contract already accpted by Seller.");
			        	   $("#erroralertmodal").modal({
		                       show: true
		                   }).one('click','.ok-btn',function (e){
		                	   //window.location.assign(location.href)
	                           location.reload();
		                   });
		        		  
		        	  }else{
		        	   $("#erroralertmodal .modal-body").html("Contract cancelled successfully.");
		        	   $("#erroralertmodal").modal({
	                       show: true
	                   }).one('click','.ok-btn',function (e){
	                	   //window.location.assign(location.href)
                           location.reload();
	                   });
		             }
		           }
		         });
            //window.location.href = '/cancelbuyerterm/' + id;
        } else {
            return false;
        }
    });
    //Chnage and calculation totla price 
    $('.indenet_quantity').keyup(function () {
       var indent_qty_id  =   $(this).attr('qty_id');
       var tot = $('#indenet_quantity_'+indent_qty_id).val();
       var vehicleCapacity = $('#vehicle_capacity_'+indent_qty_id).val();
       var vehicleUnit = $('#vehicle_units_'+indent_qty_id).val();
       
       if(vehicleUnit!="KG"){
    	    tot = tot;
       }else{
    	   tot = tot*1000;
        }
       
       var noofLoads =Math.ceil(tot/vehicleCapacity);       
       var seller_price = (tot * $('#contract_price_'+indent_qty_id).val());
       //noofLoads=Math.ceil(noofLoads);
       $('#total_price_'+indent_qty_id).val(seller_price);
       $('#noofloads_'+indent_qty_id).val(noofLoads);
       $('#numofloads_'+indent_qty_id).val(noofLoads);       //alert(noofLoads);
    });
    
    //Buyer orders in term place indenet toggle.
    $(".placeindenet_showhide").click(function() {
        var contractId = $(this).data("placeindenet");
        $("#placeindenet_history_details_" + contractId).hide();
        $("#placeindenet_details_" + contractId).slideToggle("500");        
    });

    $(".placeindenet_history_showhide").click(function() {
        var contractId = $(this).data("placeindenet");
        $("#placeindenet_details_" + contractId).hide();
        $("#placeindenet_history_details_" + contractId).slideToggle("500");
    });

  //Disbale place indent button in contract details as per current date condition
    $('.disbale_place_indent').click(function(event){
    	alert("here");
    });
    
    /*From and to locations
	 */
    $(document).on('focus click keyup keypress blur change', '#term_from_location', function() {
	    $("#term_from_location").autocomplete({	        
	    	source: "/autocomplete?fromlocation="+$('#term_to_location_id').val(),
	        minLength: 1,
	        select: function(event, ui) {
	            $('#term_from_location').val(ui.item.value);
	            $('#term_from_location_id').val(ui.item.id);
                $(this).closest("form").validate().element($('#term_from_location_id'));
                /*Need to add this below class to every autocomplete: Shriram */
                $('#term_from_location').addClass("clsAutoDisable");
	        }
	    });
    });    
    $(document).on('focus click keyup keypress blur change', '#term_to_location', function() {
        $("#term_to_location").autocomplete({            
            source: "/autocomplete?fromlocation="+$('#term_from_location_id').val(),
            minLength: 1,
            select: function(event, ui) {
                $('#term_to_location').val(ui.item.value);
                $('#term_to_location_id').val(ui.item.id);
                $(this).closest("form").validate().element($('#term_to_location_id'));
                /*Need to add this below class to every autocomplete: Shriram */
                $('#term_to_location').addClass("clsAutoDisable");
            }
        });
    });
    
  //Term not setted location in hidden filed-like enter wrong pincode  
    $( "#term_from_location" ).keyup(function(e) {
		if (e.which !== 13) {
			var from_id_hidden_buyer = $('#term_from_location_id').val("");
			if (from_id_hidden_buyer != '') {				
			}
		}
	});
	$( "#term_to_location" ).keyup(function(e) {
		if (e.which !== 13) {
			var to_id_hidden_buyer = $('#term_to_location_id').val("");
			if (to_id_hidden_buyer != '') {				
			}
		}
	});    
    
    /*from and to dates functionality
	 */
    $("#term_dispatch_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        minDate: 0,        
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            $("#term_delivery_date").datepicker(
                "option", "minDate", selectedDate);
            $("#last_bid_date").datepicker(
                    "option", "maxDate", selectedDate);
        }
    });
    $("#term_delivery_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,        
        minDate: 0,
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            $("#term_dispatch_date").datepicker("option",
                "maxDate", selectedDate);            
        }
    });

    //@raman: Start
    $('#last_bid_date').datepicker({
        changeMonth: true,
        numberOfMonths: 1,        
        minDate: 0,
        dateFormat: "dd/mm/yy"
    });
    
    /* Shriram: */
    $(document).on("focus click keyup keypress blur change",'#bid_time_icon_add', function(e){
        $(".hour.disabled, .minute.disabled").addClass("timeDisable");
        //Checking Bid Closure date
        var lastBidDate = $('#last_bid_date').val();
        $("#err_bid_close_time").html('');
        if(lastBidDate == null || lastBidDate == ''){
            $("#err_bid_close_time").html('Select Bid Closure Date first');
            return false;
        }
        $("#bid_time_icon_add").datetimepicker({
            format: 'hh:ii',
            autoclose: true,
            startView: 1,
            maxView: 1,
            minuteStep:2,
            linkField: "bid_close_time",
            linkFormat: "hh:ii"
        }).on("show", function(e){
            var lastBidDate = $('#last_bid_date').val();
            var currDate = moment().format('DD/MM/YYYY');
            if(lastBidDate == currDate){
                var lBd = $('#last_bid_date').val().split('/').reverse();
                var TimeZoned = new Date( lBd[0], (lBd[1]-1), lBd[2], moment().format('H'), moment().format('mm'), moment().format('ss'));
            }else{
            	var lBd = $('#last_bid_date').val().split('/').reverse();
            	var TimeZoned = new Date( lBd[0], (lBd[1]-1), lBd[2]);
            }
            $('#bid_time_icon_add').datetimepicker('setStartDate', TimeZoned);
        }); 
    });
    
    /* Shriram: For edit bid time */
    $(document).on("focus click keyup keypress blur change",'#bid_time_icon', function(e){
    //$('#bid_edit_close_time').on("focusin click", function(e){
        $(".hour.disabled, .minute.disabled").addClass("timeDisable");
        //Checking Bid Closure date
        var lastBidDate = $('#last_edit_bid_date').val();
        $("#err_bid_close_time").html('');
        if(lastBidDate == null || lastBidDate == ''){
            $("#err_bid_close_time").html('Select Bid Closure Date first');
            return false;
        }
        $("#bid_time_icon").datetimepicker({
            format: 'hh:ii',
            autoclose: true,
            startView: 1,
            maxView: 1,
            minuteStep:2,
            linkField: "bid_edit_close_time",
            linkFormat: "hh:ii"
        }).on("show", function(e){
            var lastBidDate = $('#last_edit_bid_date').val();
            var currDate = moment().format('DD/MM/YYYY');
            if(lastBidDate == currDate){
                var lBd = $('#last_edit_bid_date').val().split('/').reverse();
                var TimeZoned = new Date( lBd[0], lBd[1], lBd[2], moment().format('H'), moment().format('mm'), moment().format('ss'));
                $('#bid_time_icon').datetimepicker('setStartDate', TimeZoned);
            }else{
                $('#bid_time_icon').datetimepicker('setStartDate', null);
            }
        }); 
    });
    //@raman: end

    
    $("#last_edit_bid_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,        
        //minDate: $('#term_end_min_close_date_hidden').val(),
        //maxDate:$('#term_end_close_date_hidden').val(),
        minDate: 0, 
        maxDate:$('#term_end_max_close_date_hidden').val(),
        dateFormat: "dd/mm/yy",   
        onClose: function(selectedDate) {
        	if(selectedDate==$('#term_end_min_close_date_hidden').val()){
        	var d = $('#term_end_min_close_time_hidden').val();
        	var res = d.split(":"); 
        	$("#bid_close_time").datetimepicker({
            	format: 'h:ii:ss',  
            	//maxDate:moment({hour: res[0], minute: res[1]}),
            	disabledHours: [
                                res[0],
                            ]
                //maxTime:$('#term_end_min_close_time_hidden').val(),                    
            });
        }
        }
    });
    
    $("#sellers-search-buyers, #term_relocint_air_ocean").validate({
    	ignore: "input[type='text']:hidden",
        rules : {
            "term_from_city_id" : {
                required : true,
            },
            "term_from_location" : {
                required : true,
            },
            "term_to_location" : {
                required : true,
            },
            "term_to_city_id" : {
                required : true,
            },
            "lkp_vehicle_type_id" : {
                required : true,
            },
            "lkp_load_type_id" : {
                required : true,
            }
        },
        errorPlacement: function(error, element) {
        	$(element).parent('div').after(error); 
        },
        messages : {
            "term_from_location" : {
                required : "",
            },
            "term_from_city_id" : {
                required : "This is required field",
            },
            "term_to_location" : {
                required : "",
            },
            "term_to_city_id" : {
                required : "This is required field",
            }
        },
        submitHandler : function(form) {
            form.submit();
        },
    });

    //ptl search validate
    $("#sellers-search-buyers-ptl").validate({
        ignore: [],
        rules : {
            "term_from_location_id" : {
                required : true,
            },
            "term_to_location_id" : {
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
            "term_from_location_id" : {
                required : "From location is required",
            },
            "term_to_location_id" : {
                required : "To location is required",
            },
            "lkp_load_type_id" : {
                required : "Load type is required",
            },
            "lkp_packaging_type_id" : {
                required : "Packaging type is required",
            }
        },
        submitHandler : function(form) {
            form.submit();
        },
    });
    
    
    
 // Buyer quote form validation
    $("#ftl_term_insert, #relocation_term_firstform, #relocationair_term_firstform").validate({ // initialize the
        // plugin
        ignore: "input[type='text']:hidden",
        rules: {
            "term_from_location": {
                required: true,
            },
            "term_from_location_id": {
                required: true,
            },
            "term_to_location": {
                required: true,
            },
            "term_to_location_id": {
                required: true,
            },    
            "term_ptlpurposesType": {
                required: true,
            },
            "term_from_location_pincode": {
                required: true,
            },
            "term_from_location_pincode_id": {
                required: true,
            },
            "term_to_location_pincode": {
                required: true,
            },
            "term_to_location_pincode_id": {
                required: true,
            },   
            "term_iecode" : {
                required : {
	            	depends: function(element) {
	            		if ($('#term_shipment_type').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}

	            	}
                },
                tendigitsvalidations: { 
            		depends: function(element) {
	            		if ($('#term_shipment_type').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}
            		}
            	}
            },
            "from_airport_term": {
                required: true,
               
            },"from_airport_term_id": {
                required: true,
            },
            
            "to_airport_term": {
                required: true,               
            },
            "to_airport_term_id": {
                required: true,
            },
            "term_dispatch_date": {
                required: true,
            },
            "term_delivery_date": {
                required: true,
            },            
            "term_load_type": {
                required: true,
            },
            "term_package_type": {
                required: true,
            },
            "term_shipment_type": {
                required: true,
            },
            "term_sender_identify": {
                required: true,
            },
            "term_quantity": {
                required: true,
                number: true,
                fivebythreevalidations:true,
            },
            "term_vehicle_type": {
                required: true,
            },
            "quote_type": {
                required: true,
            },  
            "term_noof_packages": {
                required: true,
                digits: true,
                //intergervalidation: true,
            },
            "term_volume": {
                required: true,
                fivebythreevalidations : true,
                number: true,
            },
            "relocation_term_noofshipments": {
                required: true,
                number: true,
            },
            "relocation_term_volume": {
                required: true,
                number: true,
            },
            "term_vehicle_category" : {
                required : true,
            },
            "relocation_term_nooftrips" : {
                required : true,
            },
             "term_vehicle_model" : {
                required : true,
                alphaNumeric : true
            },
            
            "term_vehicle_category_type" : {
                required : {
	            	depends: function(element) {
	            		if ($('#term_vehicle_category').val() == 1){
		            		return true;
		            	}else{
		            		return false;
		            	}

	            	}
                }
            },
            "relocation_term_noofmoves" : {
                required : true,
                number: true,
            },
            "relocation_term_kg_move" : {
                required : true,
                number: true,
            },
            "relocation_term_cbm_move" : {
                required : true,
                number: true,
            },
            
            
        },
        errorPlacement: function(error, element) {
        	$(element).parent('div').after(error);
        },
        messages: {
            "term_from_location": {
                required: "",
            },
            "term_from_location_id": {
                required: "From Location is required",
            },
            "term_to_location": {
                required: "",
            },
            "term_to_location_id": {
                required: "To Location is required",
            },
            
            "term_from_location_pincode": {
                required: "",
            },
            "term_from_location_pincode_id": {
                required: function() {
                    if ($("#term_from_location_pincode").val() == "") {
                        return "This field is required.";
                    } else  {
                        return "Please enter valid from pincode";
                    }
                }
            },
            "term_to_location_pincode": {
                required: "",
            },
            "term_to_location_pincode_id": {
                required: function() {
                    if ($("#term_to_location_pincode").val() == "") {
                        return "This field is required.";
                    } else  {
                        return "Please enter valid to pincode";
                    }
                }
            },
            "from_airport_term": {
                required: '',               
            },
            "from_airport_term_id": {
                required: 'From airport is required',              
            },
            "to_airport_term": {
                required: '',               
            },
            "to_airport_term_id": {
                required: 'To airport is required',               
            },
            "term_dispatch_date": {
                required: "Valid from is required",
            },
            "term_delivery_date": {
                required: "Valid to is required",
            },            
            "term_load_type": {
                required: "Load Type is required",
            },
            "term_package_type": {
                required: "Package Type is required",
            },
            "term_shipment_type": {
                required: "Shipment Type is required",
            },
            "term_sender_identify": {
                required: "Sender Identity is required",
            },
            "term_quantity": {
                required: "Quantity is required",
            },
            "term_quantity": {
                required: "Quantity is required",
                number: "Only numbers are allowed",
                accept: "Only numbers are allowed"
            },
            "term_vehicle_type": {
                required: "Vehicle Type is required",
            },
            "quote_type": {
                required: "Price Type is required",
            },    
            "term_noof_packages": {
                required: "No. of packages is required",                
            }, 
            "relocation_term_volume": {
                required: "Volume is required",                
            }, 
            "relocation_term_noofshipments": {
                required: "No of Shipments is required",                
            }, 
            "term_volume": {
                required: "Volume is required",
                number: "Only numbers are allowed",
                accept: "Only numbers are allowed"
            }, 
            "relocation_term_noofmoves": {
                required: "Number of Moves is required",
                number: "Only numbers are allowed",
                accept: "Only numbers are allowed"
            }, 
            "relocation_term_kg_move": {
                required: "KG per Move is required",
                number: "Only numbers are allowed",
                accept: "Only numbers are allowed"
            }, 
            "relocation_term_cbm_move": {
                required: "CBM per Move is required",
                number: "Only numbers are allowed",
                accept: "Only numbers are allowed"
            }, 
           
        },
		
        submitHandler: function(form) { // for demo
		    form.submit();
        }
    });
/**
 * Start : Jagadeesh - 03/05/2016
 */
    jQuery.validator.addMethod("alphaNumeric", function(value, element) {
        return this.optional(element) || /^[a-z0-9\\-]+$/i.test(value);
    }, "Only letters, numbers,"); 
/**
 * End : Jagadeesh - 03/05/2016
 */
 // Add more items elements store in hidden fileds
    var seller_id_list = new Array();
    var uniq_check_add_items_list = new Array();
    $('#term_add_buyer_more')
        .click(
            function() {
                $("#ftl_term_insert").validate().cancelSubmit = false; // Validation
                var num = parseInt($('#next_term_add_buyer_more_id').val()) + 1;
                $('#next_term_add_buyer_more_id').val(num);
                var from_location_value = $('#term_from_location_id').val();
                var to_location_value = $('#term_to_location_id').val();
                var load_type_value = $("#term_load_type option:selected").text();
                var vehicle_type_value = $("#term_vehicle_type option:selected").text();
                var delivery_date = $('#term_delivery_date').val();
                var dispatch_date = $('#term_dispatch_date').val();
                var units_value = $('#term_capacity').val();
                var from_location = $('#term_from_location').val();
                var to_location = $('#term_to_location').val();
                var load_type = $('#term_load_type').val();
                var vehicle_type = $('#term_vehicle_type').val();
                var units = $('#term_capacity').val();
                var quantity = $('#term_quantity').val(); 
                var noofloads = $('#term_loads').val();
                //alert(quantity);
                if($("#update_term_line").val()==1){
                	var remove_val_ftl=$("#update_term_row_unique").val();
                	uniq_check_add_items_list.splice($.inArray(remove_val_ftl, uniq_check_add_items_list),1);
                	$('.request_row_' + $("#update_term_row_count").val()).remove();	
                	$("#update_term_line").val(0);
                }
                if (from_location != "" && to_location != "" && from_location_value != '' && to_location_value != '' && load_type != "" && vehicle_type != "" && units != "" && quantity != "") {
                    $('#error-add-item').text('');   
                   
                   //Check post existas are not in line items
                    var unique = from_location_value+to_location_value+load_type;
                    if ($.inArray(unique,uniq_check_add_items_list)==-1) {
                    	uniq_check_add_items_list.unshift(unique);
                    //Add multiple sellers store in variable	
	            	var seller_location_id = from_location_value;
	                seller_id_list.unshift(seller_location_id);
                     
                    var html = '<div class="table-row inner-block-bg"><div class="request_row_' + num + '"><div class="col-md-2 padding-left-none" id="term_from_'+num+'">' + from_location + '</div><div class="col-md-2 padding-left-none" id="term_to_'+num+'">' + to_location + '</div><div class="col-md-3 padding-left-none">' + load_type_value + '</div><div class="col-md-2 padding-left-none">' + vehicle_type_value + '</div><div class="col-md-2 padding-left-none">' +quantity+' '+ units + '</div><div class="col-md-1 padding-left-none"><a class="term_edit_lineitem edit_this edit" term-data-string="'+unique+'" row_ftlterm_id="' + num + '"><i class="fa fa-edit red" title="Edit"></i></a>&nbsp;<a style ="cursor:pointer;" class="term_remove_lineitem remove_this remove" term-data-string="'+unique+'" row_ftlterm_id="' + num + '"><i class="fa fa-trash red" title="Delete"></i></a></div><input type="hidden" name="from_location[]" class="from_location" id="from_location_'+num+'"  value="' + from_location_value + '"><input type="hidden" name="to_location[]" id="to_location_'+num+'" value="' + to_location_value + '"><input type="hidden" name="delivery_date[]" value="' + delivery_date + '"><input type="hidden" name="dispatch_date[]" value="' + dispatch_date + '"><input type="hidden" name="load_type[]" id="load_type_'+num+'" value="' + load_type + '"><input type="hidden" name="vehicle_type[]" id="vehicle_type_'+num+'" value="' + vehicle_type + '"><input type="hidden" name="capacity[]" id="capacity_'+num+'" value="' + units + '"><input type="hidden" name="quantity[]" id="quantity_'+num+'" value="' + quantity + '"><input type="hidden" name="no_of_loads[]" id="no_of_loads_'+num+'" value="' + noofloads + '"></div>';
                    $('.term_request_rows').append(html);
                    $('#term_from_location').val("");
                    $('#term_to_location').val("");
                    $('#term_from_location_id').val("");
                    $('#term_to_location_id').val("");                    
                    $("#term_dispatch_date").prop('disabled', true);
                    $("#term_delivery_date").prop('disabled', true);
                    $('#term_quantity').val("");
                    $('#term_capacity').val("");
                    $('#term_load_type').val("");
                    $('#term_vehicle_type').val("");
                    $('#dimensions').val("");
                    $('#term_loads').val("");                   
                    $("#ftl_term_insert")
                        .validate().cancelSubmit = true;
                    $('.selectpicker').selectpicker('refresh');
                    $("#term_dispatch_date").datepicker({
                        changeMonth: true,
                        numberOfMonths: 1,
                        minDate: 0,        
                        dateFormat: "dd/mm/yy",
                        onClose: function(selectedDate) {
                            $("#term_delivery_date").datepicker(
                                "option", "minDate", selectedDate);
                        }
                    });
                    $("#term_delivery_date").datepicker({
                        changeMonth: true,
                        numberOfMonths: 1,        
                        minDate: 0,
                        dateFormat: "dd/mm/yy",
                        onClose: function(selectedDate) {
                            $("#term_dispatch_date").datepicker("option",
                                "maxDate", selectedDate);
                        }
                    });
                    
                    } else {
                    	$("#erroralertmodal .modal-body").html("Post already exists for the specified parameters! Please modify parameters.");
                        $("#erroralertmodal").modal({
                            show: true
                        });
                    }
                    
                    return false;
                }
            });
    
    $('#term_update_buyer_more').click(function() {

        var currentrowid = $("#current_row_id").val();
        var rowid = "#single_post_item_"+currentrowid;

        var datepicker_from_value = $('#term_dispatch_date').val();
        var datepicker_to_value = $('#term_delivery_date').val();
        $('#dispatch_date').val(datepicker_from_value);
        $('#delivery_date').val(datepicker_to_value);

        $(rowid+" div:first-child").html($( "#term_from_location" ).val());
        $(rowid+" div:nth-child(2)").html($( "#term_to_location" ).val());
        $(rowid+" div:nth-child(3)").html($( "#term_load_type option:selected" ).text());
        $(rowid+" div:nth-child(4)").html($( "#term_vehicle_type option:selected" ).text());
        $(rowid+" div:nth-child(5)").html($( "#term_quantity" ).val());

        $(rowid+" input[name='from_location[]']").val($( "#term_from_location_id" ).val());
        $(rowid+" input[name='to_location[]']").val($( "#term_to_location_id" ).val());
        $(rowid+" input[name='quantity[]']").val($( "#term_quantity" ).val());
        $(rowid+" input[name='capacity[]']").val($( "#term_capacity" ).val());
        $(rowid+" input[name='load_type[]']").val($( "#term_load_type option:selected" ).val());
        $(rowid+" input[name='vechile_type[]']").val($( "#term_vehicle_type option:selected" ).val());

        $("#term_from_location").val("");$("#term_from_location_id").val("");
        $("#term_to_location").val("");$("#term_to_location_id").val("");
        $("#term_load_type").val("");
        $('#term_capacity').val("");
        $('#term_quantity').val("");
        $('#term_vehicle_type').val("");

        $('.selectpicker').selectpicker('refresh');
        $('.term_buyer_update').hide();



        	/*$.ajax({
		           type: "POST",
		           url: '/termpostitemupdate',
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
		           data: $("#ftl_term_insert").serialize(), // serializes the form's elements.
		           success: function(msg)
		           {
		        	  var rowid="#single_post_item_"+$("#buyer_item_id").val();
		        	  var msg_item=msg.split("|");

		        	 $( rowid +" .from_location_text" ).html(msg_item[0]);
		        	 $( rowid +" .to_location_text" ).html(msg_item[1]);
		        	 $( rowid +" .load_type_text" ).html(msg_item[2]);
		        	 $( rowid +" .vehicle_type_text" ).html(msg_item[3]);
		        	 $( rowid +" .quantity_type_text" ).html(msg_item[4]);


		        	 $("#ftl_term_insert").trigger('reset');
		        	 $('.selectpicker').selectpicker('refresh');
		        	 $('.term_buyer_add').show();
		        	 $('.term_buyer_update').hide();

		           }
		         });*/
        	
        });

    $('#term_update_buyer_more_ptl').click(function() {
        var currentserviceid = $("#current_service_id").val();
        var currentrowid = $("#current_row_id").val();
        var rowid = "#single_post_item_"+currentrowid;

        var datepicker_from_value = $('#term_dispatch_date').val();
        //var datepicker_from_value = convertDateFormatForDatePickerSeller(datepicker_from_value);
        var datepicker_to_value = $('#term_delivery_date').val();
        //var datepicker_to_value = convertDateFormatForDatePickerSeller(datepicker_to_value);
        $('#dispatch_date').val(datepicker_from_value);
        $('#delivery_date').val(datepicker_to_value);
        $('#is_door_pickup').val("0");$('#is_door_delivery').val("0");
        if ($('input#term_door_pickup').is(':checked')) {
            $('#is_door_pickup').val(1);
        }
        if ($('input#term_door_delivery').is(':checked')) {
            $('#is_door_delivery').val(1);
        }
        if(currentserviceid == 9 || currentserviceid == 8){
            $(rowid+" div:first-child").html($( "#from_airport_term" ).val());
            $(rowid+" div:nth-child(2)").html($( "#to_airport_term" ).val());
            $(rowid+" input[name='from_location[]']").val($( "#from_airport_term_id" ).val());
            $(rowid+" input[name='to_location[]']").val($( "#to_airport_term_id" ).val());

            $(rowid+" input[name='ie_code[]']").val($( "#term_iecode" ).val());
            $(rowid+" input[name='product_made[]']").val($( "#term_product_mode" ).val());
            $(rowid+" input[name='lkp_air_ocean_shipment_type_id[]']").val($( "#term_shipment_type option:selected" ).val());
            $(rowid+" input[name='lkp_air_ocean_sender_identity_id[]']").val($( "#term_sender_identify option:selected" ).val());
        }else{
            $(rowid+" div:first-child").html($( "#term_from_location_pincode" ).val());
            $(rowid+" div:nth-child(2)").html($( "#term_to_location_pincode" ).val());
            $(rowid+" input[name='from_location[]']").val($( "#term_from_location_pincode_id" ).val());
            $(rowid+" input[name='to_location[]']").val($( "#term_to_location_pincode_id" ).val());
        }

        if(currentserviceid != 21){
        $(rowid+" div:nth-child(3)").html($( "#term_load_type option:selected" ).text());
        $(rowid+" div:nth-child(4)").html($( "#term_package_type option:selected" ).text());
        
        $(rowid+" div:nth-child(7)").html($( "#term_noof_packages" ).val());
        $(rowid+" div:nth-child(5)").html($( "#term_volume" ).val());
        $(rowid+" div:nth-child(6)").html($( "#term_units" ).val());
        }else{
        	 
             $(rowid+" div:nth-child(3)").html($( "#term_volume" ).val());
             $(rowid+" div:nth-child(4)").html("CFT");
             $(rowid+" div:nth-child(5)").html($( "#term_noof_packages" ).val());
        }

        $(rowid+" input[name='volume[]']").val($( "#term_volume" ).val());
        $(rowid+" input[name='capacity[]']").val($( "#term_units" ).val());
        $(rowid+" input[name='number_packages[]']").val($( "#term_noof_packages" ).val());
        $(rowid+" input[name='load_type[]']").val($( "#term_load_type option:selected" ).val());
        $(rowid+" input[name='package_type[]']").val($( "#term_package_type option:selected" ).val());


        $("#term_from_location_pincode").val("");$("#term_from_location_id").val("");
        $("#term_to_location_pincode").val("");$("#term_to_location_id").val("");
        $("#term_load_type").val("");$('#term_package_type').val("");
        $('#volume').val(""); $('#units').val("");$('#number_packages').val("");

        $('.selectpicker').selectpicker('refresh');
        $('.term_buyer_update').hide();

    });


    $('#term_update_buyer_more_relocation').click(function() {
        var termtype = $(this).attr("term_type");
        var currentrowid = $("#current_row_id").val();
        var rowid = "#single_post_item_"+currentrowid;

        var datepicker_from_value = $('#term_dispatch_date').val();
        var datepicker_to_value = $('#term_delivery_date').val();
        $('#dispatch_date').val(datepicker_from_value);
        $('#delivery_date').val(datepicker_to_value);

        $(rowid+" div:first-child").html($( "#term_from_location" ).val());
        $(rowid+" div:nth-child(2)").html($( "#term_to_location" ).val());
        $(rowid+" input[name='from_location[]']").val($( "#term_from_location_id" ).val());
        $(rowid+" input[name='to_location[]']").val($( "#term_to_location_id" ).val());

        if(termtype =="house"){
            $(rowid+" div:nth-child(3)").html($( "#relocation_term_volume" ).val());
            $(rowid+" div:nth-child(4)").html($( "#relocation_term_noofshipments" ).val());
            $(rowid+" input[name='volume[]']").val($( "#relocation_term_volume" ).val());
            $(rowid+" input[name='number_packages[]']").val($( "#relocation_term_noofshipments" ).val());
        }else{
            $(rowid+" div:nth-child(3)").html($( "#term_vehicle_category option:selected" ).text());
            $(rowid+" div:nth-child(4)").html($( "#term_vehicle_category_type option:selected" ).text());
            $(rowid+" div:nth-child(5)").html($( "#term_vehicle_model" ).val());
            $(rowid+" div:nth-child(6)").html($( "#relocation_term_nooftrips" ).val());
            $(rowid+" input[name='lkp_vehicle_category_id[]']").val($( "#term_vehicle_category option:selected" ).val());
            $(rowid+" input[name='lkp_vehicle_category_type_id[]']").val($( "#term_vehicle_category_type option:selected" ).val());
            $(rowid+" input[name='vehicle_model[]']").val($( "#term_vehicle_model" ).val());
            $(rowid+" input[name='no_of_vehicles[]']").val($( "#relocation_term_nooftrips" ).val());
        }


        $("#term_from_location").val("");$("#term_from_location_id").val("");
        $("#term_to_location").val("");$("#term_to_location_id").val("");
        $('#relocation_term_volume').val("");
        $('#relocation_term_noofshipments').val("");
        $('#term_vehicle_model').val("");$('#relocation_term_nooftrips').val("");
        $('#term_vehicle_category').val("");$('#term_vehicle_category_type').val("");


        $('.selectpicker').selectpicker('refresh');
        $('.term_buyer_update').hide();

    });

    $('#term_update_buyer_more_relocationint').click(function() {
        var currentrowid = $("#current_row_id").val();
        var rowid = "#single_post_item_"+currentrowid;

        var datepicker_from_value = $('#term_dispatch_date').val();
        var datepicker_to_value = $('#term_delivery_date').val();
        $('#dispatch_date').val(datepicker_from_value);
        $('#delivery_date').val(datepicker_to_value);

        $(rowid+" div:first-child").html($( "#term_from_location" ).val());
        $(rowid+" div:nth-child(2)").html($( "#term_to_location" ).val());
        $(rowid+" input[name='from_location[]']").val($( "#term_from_location_id" ).val());
        $(rowid+" input[name='to_location[]']").val($( "#term_to_location_id" ).val());

        $(rowid+" div:nth-child(3)").html($( "#term_number_loads" ).val());
        $(rowid+" div:nth-child(4)").html($( "#relocation_term_avg_kg_per_move" ).val());
        $(rowid+" input[name='number_loads[]']").val($( "#term_number_loads" ).val());
        $(rowid+" input[name='avg_kg_per_move[]']").val($( "#relocation_term_avg_kg_per_move" ).val());

        $("#term_from_location").val("");$("#term_from_location_id").val("");
        $("#term_to_location").val("");$("#term_to_location_id").val("");
        $('#term_number_loads').val("");
        $('#relocation_term_avg_kg_per_move').val("");

        $('.selectpicker').selectpicker('refresh');
        $('.term_buyer_update').hide();

    });


    $('#term_update_buyer_more_relocationglobal').click(function() {
        var currentrowid = $("#current_row_id").val();
        var rowid = "#single_post_item_"+currentrowid;

        var datepicker_from_value = $('#term_dispatch_date').val();
        var datepicker_to_value = $('#term_delivery_date').val();
        $('#dispatch_date').val(datepicker_from_value);
        $('#delivery_date').val(datepicker_to_value);

        $(rowid+" div:first-child").html($( "#term_relgm_service_type option:selected" ).text());
        $(rowid+" div:nth-child(2)").html($( "#term_measurement" ).val() + " " + $( "#term_measurement_unit" ).val() );
        $(rowid+" input[name='service_ids[]']").val($( "#term_relgm_service_type option:selected" ).val());
        $(rowid+" input[name='measurements[]']").val($( "#term_measurement" ).val());
        $(rowid+" input[name='measurement_units[]']").val($( "#term_measurement_unit" ).val());

        $("#term_relgm_service_type").val("");$("#term_measurement").val("");
        $("#term_measurement_unit").val("");

        $('.selectpicker').selectpicker('refresh');
        $('.term_buyer_update').hide();

    });


//    $(document).on('click', '.remove_this', function() {
//        var rowid = $(this).attr("row_id");
//        $('.request_row_' + rowid).remove();
//    });
    //Need to check remove item exists or not
   $(document).on('click', '.term_remove_lineitem', function() {
	  
        var rowid_ftl_term = $(this).attr("row_ftlterm_id");
        remove_val_ftl = $(this).attr("term-data-string");
        var r = confirm("Are you sure, you want you delete?");
        if (r == true) {
        	uniq_check_add_items_list.splice($.inArray(remove_val_ftl, uniq_check_add_items_list),1);
            $('.request_row_' + rowid_ftl_term).remove();
        } 
        
    });
   
   $(document).on('click', '.edit_this', function() {
       
	   var rowid_ftl_term = $(this).attr("row_ftlterm_id");
	   var remove_val_ftl = $(this).attr("term-data-string");
	   $("#update_term_line").val(1);
   	   $("#update_term_row_count").val(rowid_ftl_term);
   	   $("#update_term_row_unique").val(remove_val_ftl);
   	   
	   $('#term_from_location').val($("#term_from_"+rowid_ftl_term).html());
       $('#term_to_location').val($("#term_to_"+rowid_ftl_term).html());
       $('#term_to_location_id').val($("#to_location_"+rowid_ftl_term).val());
       $('#term_from_location_id').val($("#from_location_"+rowid_ftl_term).val());
       $("#term_dispatch_date").prop('disabled', true);
       $("#term_delivery_date").prop('disabled', true);
       $('#term_quantity').val($("#quantity_"+rowid_ftl_term).val());
       $('#term_capacity').val($("#capacity_"+rowid_ftl_term).val());
       $('#term_load_type').selectpicker('val',$("#load_type_"+rowid_ftl_term).val());
       $('#term_vehicle_type').selectpicker('val',$("#vehicle_type_"+rowid_ftl_term).val());
       $('#term_loads').val($("#no_of_loads_"+rowid_ftl_term).val()); 
       
   });
   
   
   $(document).on('click', '.termreloc_remove_lineitem', function() {
		  
       var rowid_ftl_term = $(this).attr("row_ftlterm_id");
       remove_val_ftl = $(this).attr("term-data-string");
       var r = confirm("Are you sure, you want you delete?");
       if (r == true) {
       	uniq_check_add_items_list.splice($.inArray(remove_val_ftl, uniq_check_add_items_list),1);
           $('.request_row_' + rowid_ftl_term).remove();
       } 
       
   });
   
$(document).on('click', '.termreloc_edit_lineitem', function() {
       
	   var rowid_ftl_term = $(this).attr("row_id");
	   //var remove_val_ftl = $(this).attr("term-data-string");
	   
	   $("#update_relocterm_line").val(1);
   	   $("#update_relocterm_row_count").val(rowid_ftl_term);
   	  // $("#update_term_row_unique").val(remove_val_ftl);
       var typeselection = $('input[name=term_post_rate_card_type]:checked').val();
       
       $('#term_from_location').val($("#from_loc_"+rowid_ftl_term).html());
       $('#term_to_location').val($("#to_loc_"+rowid_ftl_term).html());
       $('#term_to_location_id').val($("#to_location_id_"+rowid_ftl_term).val());
       $('#term_from_location_id').val($("#from_location_id_"+rowid_ftl_term).val());
       
       if(typeselection==1){
    	   $('#relocation_term_volume').val($("#relocation_term_volume_"+rowid_ftl_term).val());
           $('#relocation_term_noofshipments').val($("#relocation_term_noofshipments_"+rowid_ftl_term).val());  
    	   
       }
       
       if(typeselection==2){
    	   $('#term_vehicle_category').selectpicker('val',$("#rel_vehicle_type_cat_"+rowid_ftl_term).val());
           $('#term_vehicle_category_type').selectpicker('val',$("#rel_vehicle_type_cat_type_"+rowid_ftl_term).val());	
           $('#term_vehicle_model').val($("#relocation_term_vehicle_model_"+rowid_ftl_term).val());
           $('#relocation_term_nooftrips').val($("#relocation_term_nooftrips_"+rowid_ftl_term).val());
    	   
       }
	   
       $('#term_quantity').val($("#quantity_"+rowid_ftl_term).val());
       $('#term_capacity').val($("#capacity_"+rowid_ftl_term).val());
       $('#term_load_type').selectpicker('val',$("#load_type_"+rowid_ftl_term).val());
       $('#term_vehicle_type').selectpicker('val',$("#vehicle_type_"+rowid_ftl_term).val());
       $('#term_loads').val($("#no_of_loads_"+rowid_ftl_term).val()); 
       
   });

$(document).on('click', '.termrelocair_edit_lineitem', function() {
    
	   var rowid_ftl_term = $(this).attr("row_id");
	   //var remove_val_ftl = $(this).attr("term-data-string");
	   
	   $("#update_relocterm_line").val(1);
	   $("#update_relocterm_row_count").val(rowid_ftl_term);
	  // $("#update_term_row_unique").val(remove_val_ftl);
   
    $('#term_from_location').val($("#from_loc_"+rowid_ftl_term).html());
    $('#term_to_location').val($("#to_loc_"+rowid_ftl_term).html());
    $('#term_to_location_id').val($("#to_location_id_"+rowid_ftl_term).val());
    $('#term_from_location_id').val($("#from_location_id_"+rowid_ftl_term).val());
    $('#relocation_term_noofmoves').val($("#relocation_term_noofmoves_"+rowid_ftl_term).val());
    $('#relocation_term_kg_move').val($("#relocation_term_kgmove_"+rowid_ftl_term).val());
    $('#relocation_term_cbm_move').val($("#relocation_term_kgmove_"+rowid_ftl_term).val());
    
});

   
    
 // checking add items empty or not
    $('#term_add_buyer_quote,#term_add_buyer_quote_draft').click(function(e) {
        var thisid = $(this).attr("id");
        if(thisid == "term_add_buyer_quote"){
            $("#confirm_but").val("Float RFP");
        }

        //validation add new docs
    	$(".dynamic_validations_term").each(function (item) {
            $(this).rules("add", {
            	required : true,
            	accept: "docx|txt|doc|pdf|xls|csv|xlsx",
            });
            $("#term_buyer_quote").validate().element("#"+$(this).attr("id"));
            //$("#term_buyer_quote").validate().element("#box_term2")
            /*if($("#term_buyer_quote").validate().element("#"+$(this).attr("id"))){
            	$(".documents-terms .error").addClass("forceerror");
            }else{
            	$(".documents-terms .error").removeClass("forceerror");
            }*/
            
        });	
    	
        var id = $('.term_request_rows .table-row').children().size();
        
        if (id == 0) {           
            $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        } else {
            $('#term_buyer_quote').submit();
            if($('#term_buyer_quote').valid()){
                $("#term_add_buyer_quote_draft").prop('disabled', true);                    
                $("#term_add_buyer_quote").prop('disabled', true);                    
            }
            return true;
        }
    });



    $("#term_buyer_quote").validate({
    	ignore: [],
        rules: {        	
        	"bid_type": {
                required: true,
            },
            "last_bid_date": {
                required: true,
            },
            "max_weight_accepted_text" : {
            	required : true,
            	fourbythreevalidations: true,
            },
            "incremental_weight_text" : {
            	required : { 
            		depends: function(element) {
            			if ($('#check_max_weight').is(':checked')){
    	            		return true;
    	            	}else{
    	            		return false;
    	            	}
            		}
            	},
            	nabythreevalidations: { 
            		depends: function(element) {
            			if ($('#check_max_weight').is(':checked')){
    	            		return true;
    	            	}else{
    	            		return false;
    	            	}
            		}
            	}
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
    
	$('.documents-terms .documents-add').click(function(){
    	var n = $('.update_txt_test_buyer').length;
        var num = parseInt($('#term_next_terms_count_search').val()) + 1;
        $('#term_next_terms_count_search').val(num);
    
        if( 3 < n ) {
            $("#erroralertmodal .modal-body").html("You can add 5 documents only !");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        }
        var box_html = $('<div class="text-box col-md-12 padding-none"><div class="col-md-4 form-control-fld"><div class="upload-fld"><button class="btn add-btn upload-browse-btn pull-right">Browse...</button><input type="file" class="form-control form-control1 update_txt_test_buyer update_txt dynamic_validations_term" name="terms_condtion_types_term_' + num + '" value="" id="box_term' + num + '" /></div></div> <a href="#" class="remove-box-term margin-top-6" style="line-height: 35px; margin-left: 5px;"><i class="fa fa-trash red" title="Delete"></i></a></div>');
    
        box_html.hide();
        $('.documents-terms div.text-box:last').after(box_html);
        box_html.fadeIn('slow');
    
        $(".dynamic_validations_term").each(function (item) {
            $(this).rules("add", {
            	required : true,
            	accept: "docx|txt|doc|pdf|xls|csv|xlsx",
            });
        });


        
        var isIE1 = /*@cc_on!@*/false || !!document.documentMode;
        if (isIE1==true){  
            $(".update_txt_test_buyer").css({
                'left': '0',
                'width':'125%'
            });
            $(".upload-browse-btn").hide();
        }

       
        var isOpera1 = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
        var isChrome1 = !!window.chrome && !isOpera1;              // Chrome 1+
        if (isChrome1==true){  
            $(".update_txt_test_buyer").addClass("chrome-alignment");
        }

        return false;
    });
    $('.documents-terms').on('click', '.remove-box-term', function(){
        //$(this).parent().css( 'background-color', '#FF6C6C' );
        $(this).parent().fadeOut("fast", function() {
            $(this).remove();
            $('.box-number-delete').each(function(index){
                $(this).text( index + 1 );
            });
        });
        return false;
    });

	$(".lineitem_checkbox").change(function(){
		
		var term_buyerlineitem = $(this).attr('id');
		var termRemoveString = 'term_lineitem_';
	    var termLineItemId = term_buyerlineitem.replace(termRemoveString,'');
	    var termLineItemId_buyer = termLineItemId.trim();
        var ischecked= $(this).is(':checked');
		if(ischecked){			
			var checke_line_item = parseInt($('#buyer_items_count').val()) + 1;
		    $('#buyer_items_count').val(checke_line_item);
		    var buyers_items_defult = $("#buyer_line_item_id").val();
		    $("#buyer_line_item_id").val(buyers_items_defult+","+termLineItemId_buyer);
		}else{
			var checke_line_item = parseInt($('#buyer_items_count').val()) - 1;
		    $('#buyer_items_count').val(checke_line_item);
		    var buyer_line_item_id_remove = $("#buyer_line_item_id").val();
		    buyer_line_item_id_add = buyer_line_item_id_remove.replace(','+termLineItemId_buyer,'');
			$("#buyer_line_item_id").val(buyer_line_item_id_add);
		}
	});


    var rules = new Object();
    var messages = new Object();
    $('.termintialquotesubmit_form input:text').each(function() {
        rules[this.name] = { required: true,decimalvalidation: true };
        messages[this.name] = { required: 'This field is required' };
    });

    var validator = $(".termintialquotesubmit_form").validate({
        ignore: "input[type='text']:hidden,input[type='checkbox']:hidden",
        rules: rules,
        messages: messages,
        errorPlacement: function(error, element) {
            $(element).parent().append(error);
        },

    });
    
    $('.couriertermintialquotesubmit_form input:text').each(function() {
        rules[this.name] = { required: true,decimalvalidation: true };
        messages[this.name] = { required: 'This field is required' };
    });
    $(".couriertermintialquotesubmit_form").each(function (item) {
        var formid = $(this).attr("id");
	    var validator = $("#"+formid).validate({
	        ignore: "input[type='text']:hidden,input[type='checkbox']:hidden",
	        rules: rules,
	        messages: messages,
	        errorPlacement: function(error, element) {
	            $(element).parent().append(error);
	        },
	
	    });
    });
    
    $('.couriertermintialquotesubmit').click(function(e) {
    	
		e.preventDefault();
		var item_id=$("#item_id").val();
		var id=$('#buyer_items_count').val();
		var buyer_quoe_id=e.target.id.split("_");
		var formid = $(this).closest(".couriertermintialquotesubmit_form").attr("id");
		if($('#'+formid).valid()) {
		//if($(this).closest(".couriertermintialquotesubmit_form").valid()) {
            var btnVal = $(this).attr('id');
            var btnName = $(this).attr('name');
            var submitData=$("#intialquotebidding_"+buyer_quoe_id[1]).serialize();
            var btn = '&'+btnName+'='+btnVal;
            submitData += btn;
            $.ajax({
                type: "POST",
                url: '/couriertermintialquoteseller',
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
                    if(btnName=="save"){
                        $("#erroralertmodal .modal-body").html("Quote saved successfully.");
                    }else{
                        $("#erroralertmodal .modal-body").html("Quote submitted successfully.");
                    }
                    $("#erroralertmodal").modal({
                        show: true
                    }).one('click','.ok-btn',function (e){
                        //window.location.assign(location.href)
                        location.reload();
                    });
                }
            });
        }
	
    });
    
    
    $('.termintialquotesubmit').click(function(e) {
    		e.preventDefault();
			var item_id=$("#item_id").val();
			var id=$('#buyer_items_count').val();
			var buyer_quoe_id=e.target.id.split("_");
			//return false;
			if(id==0){
				$("#erroralertmodal .modal-body").html("Please add atleast one line item to the list to submit a quote");
	            $("#erroralertmodal").modal({
	                show: true
	            });
				return false;
			}else{
				if($(this).closest(".termintialquotesubmit_form").valid()) {
                    var btnVal = $(this).attr('id');
                    var btnName = $(this).attr('name');
                    var submitData=$("#intialquotebidding_"+buyer_quoe_id[1]).serialize();
                    var btn = '&'+btnName+'='+btnVal;
                    submitData += btn;
                    $.ajax({
                        type: "POST",
                        url: '/termintialquoteseller',
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
                            if(btnName=="save"){
                                $("#erroralertmodal .modal-body").html("Quote saved successfully.");
                            }else{
                                $("#erroralertmodal .modal-body").html("Quote submitted successfully.");
                            }
                            $("#erroralertmodal").modal({
                                show: true
                            }).one('click','.ok-btn',function (e){
                                //window.location.assign(location.href)
                                location.reload();
                            });
                        }
                    });
                }
		    }
		
	});
    

   
    
/******************************From Location starts AIR International*********************************************/
	$(document).on('focus click keyup keypress blur change', '#from_airport_term', function() {
		$( "#from_airport_term" ).autocomplete({
	            source: "/autocomplete?country=india&fromlocation="+$('#to_airport_term_id').val(),
	            minLength: 1,
	            select: function(event, ui) {
	                $('#from_airport_term').val(ui.item.value);
	                $('#from_airport_term_id').val(ui.item.id);
					$(this).closest("form").validate().element($('#from_airport_term_id'));
                    /*Need to add this below class to every autocomplete: Shriram */
                    $('#from_airport_term').addClass("clsAutoDisable");
	            }
		});
	});
	
	$(document).on('focus click keyup keypress blur change', '#to_airport_term', function() {
            $( "#to_airport_term" ).autocomplete({
                source: "/autocomplete?fromlocation="+$('#from_airport_term_id').val(),
                minLength: 1,
                select: function(event, ui) {
                    $('#to_airport_term').val(ui.item.value);
                    $('#to_airport_term_id').val(ui.item.id);
					$(this).closest("form").validate().element($('#to_airport_term_id'));
                    /*Need to add this below class to every autocomplete: Shriram */
                    $('#to_airport_term').addClass("clsAutoDisable");
                }
            });
	});
/******************************From Location ends AIR International*********************************************/
 
    
 // Post private and Post public conditions
    $('#term_post_private').click(function() {
        var id = $('.term_request_rows').children().size();
        if (id != 0) {
            $("#showhidepost").css("display", "block");
            $.ajax({
                url: '/getTermSellerList',
                type: "post",
                data: {
                    'seller_list': seller_id_list,
                    '_token': $('input[name=_token]').val()
                },
                success: function(data) {
                    if (data != "") {
                        $(".token-input-list").remove();
                        $("#term_seller_list").tokenInput(data);
                    } else {                       
                        $("#erroralertmodal .modal-body").html("No Sellers Available.");
                        $("#erroralertmodal").modal({
                            show: true
                        });
                        $('#term_post_private').prop('checked', false);
                        $("#showhidepost").css("display", "none");
                        return false;
                    }
                },
                error: function(request, status, error) {
                    $('#term_post_private').val(null);
                    alert(error);
                },
            });
        } else {
        	$("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
                show: true
            });
            $('#term_post_private').prop('checked', false);
            return false;
        }
    });
    $('#term_post_public').click(function() {
        var id = $('.term_request_rows').children().size();
        if (id == 0) {
            $("#showhidepost").css("display", "none");            
            $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
                show: true
            });
            $('#term_post_public').prop('checked', false);
            return false;
        } else {
        	$('#term_seller_list').val("");
            $("#showhidepost").css("display", "none");            
        }
    });
    
    
 // Post private and Post public conditions relocation term
    $('#term_relocation_post_private').click(function() {
          //for reloacation globalmobility check serfice some js issue purpose srinu (23rd june,2016)
         if($("#service_global_id").val() == 19) {
               var id=1;
         } else {
               var id = $('.relocation_term_request_rows').children().size();
         }
        
        if (id != 0) {
            $("#showhidepost").css("display", "block");
            $.ajax({
                url: '/getTermSellerList',
                type: "post",
                data: {
                    'seller_list': seller_id_list,
                    '_token': $('input[name=_token]').val()
                },
                success: function(data) {
                    if (data != "") {
                        $(".token-input-list").remove();
                        $("#term_seller_list").tokenInput(data);
                    } else {                       
                        $("#erroralertmodal .modal-body").html("No Sellers Available.");
                        $("#erroralertmodal").modal({
                            show: true
                        });
                        $('#term_relocation_post_private').prop('checked', false);
                        $("#showhidepost").css("display", "none");
                        return false;
                    }
                },
                error: function(request, status, error) {
                    $('#term_relocation_post_private').val(null);
                    alert(error);
                },
            });
        } else {
        	$("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
                show: true
            });
            $('#term_relocation_post_private').prop('checked', false);
            return false;
        }
    });
    $('#term_relocation_post_public').click(function() {
    	
    	//alert("hello");
        var id = $('.relocation_term_request_rows').children().size();
        if (id == 0) {
            $("#showhidepost").css("display", "none");            
            $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
                show: true
            });
            $('#term_relocation_post_public').prop('checked', false);
            return false;
        } else {
        	$('#term_seller_list').val("");
            $("#showhidepost").css("display", "none");            
        }
    });


    
    /*
     * Generate contract form
     */
    $('.termgeneratecontract').click(function(e) {
    	
    	//alert("hello");
		e.preventDefault();
		var allVals = [];
		var str=$(this).val();
		
		var valid=0;
		
		$('input:checkbox.seller_quote_items_'+str).each(function() {
			
			if($(this).is(":checked")==true){
		    	 
		    	 var text_id=$(this).attr('id').split('_');
		    	 
		    	if(($("#contractquote_"+text_id[1]).val() && $("#contractquote_"+text_id[1]).val()=="" || $("#contractquote_"+text_id[1]).val()==0) || ($("#contractquote_"+text_id[1]).val() && isNaN($("#contractquote_"+text_id[1]).val()))){
		    	
		    		 valid=1;
		 			//return false;
		    	 }else{
		    	
		    	 allVals.push($(this).val());
		         }
		    	 
		      }
		     });
		
		if(valid==1){
			$("#erroralertmodal .modal-body").html("Enter only numeric values");
            $("#erroralertmodal").modal({
                show: true
            });
			return false;	
		}
		if(allVals.length==0){
			$("#erroralertmodal .modal-body").html("Please add atleast one line item to the list to Generate Contratct");
            $("#erroralertmodal").modal({
                show: true
            });
			return false;
		}else{			
		
		
		//if($("#posts-form").valid()) {
		var submitData=$("#generate_contract_"+str).serialize();
		 //var btnName = $('#termintialquotesubmit').attr('name');
		 //$("#add_quote_seller_id").prop('disabled', true);
		 //$("#add_quote_seller").prop('disabled', true);
          var btnVal = str;
          var btn = '&seller='+btnVal;
         submitData += btn;
		 $.ajax({
	           type: "POST",
	           url: '/generatecontractbuyer',
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
	        	   $("#erroralertmodal .modal-body").html("Contract "+msg+" generated successfully.");
	        	   $("#erroralertmodal").modal({
                       show: true
                   }).one('click','.ok-btn',function (e){
                	   location.reload();
                   });
	           }
	         });
	}
	
});
    
    
// Validations for update bid close timings
$("#term_bid_date_edit").validate({
    rules: {
        "last_bid_date": {
            required: true,
        },
        "bid_close_time": {
            required: true,
        },
    },
    errorPlacement: function(error, element) {
    	$(element).parent('div').after(error);
    },
    messages: {
        "last_bid_date": {
            required: "Please Select Bid Closure Date",
        },
        "bid_close_time": {
            required: "Please Select Bid Closure Time",
        },
    },
});


//Validations for place indenet form
$("#term_click_booknow").validate({
    rules: {
        "current_indenet_quantity": {
            required: true,
        }        
    },
    messages: {
        "current_indenet_quantity": {
            required: "Please Enter Quantity",
        },       
    },
});


/*******************Term Js starts here***********************/

/*Hide and show spot and term divs 
 */
$("#spot_enquiry_type").click(function(){  
	$("#spot_show_hide_block").show();
    $("#term_show_hide_block").hide(); 
    $('#ftl_term_insert').trigger("reset");
    $('#ptlBuyerQuotelineitemsForm').trigger("reset");
    $('#term_load_type, #term_package_type').val('');
    $('.selectpicker').selectpicker('refresh');
});
$("#term_enquiry_type").click(function(){
    $("#term_show_hide_block").show();
    $("#spot_show_hide_block").hide();   
});

//auto complete For LTL Term searching Pincodes
$(document).on('click', '#term_from_location_pincode', function() {
 $("#term_from_location_pincode").autocomplete({
     source: "/ptlPincodesAutocomplete?ptlFromLocation="+$('#term_to_location_pincode_id').val(),
 	minLength: 1,
     select: function(event, ui) {     	
     	$('#term_from_location_pincode').val(ui.item.value);
         $('#term_from_location_pincode_id').val(ui.item.id);
         $(this).closest("form").validate().element($('#term_from_location_pincode_id'));
        /*Need to add this below class to every autocomplete: Shriram */
        $('#term_from_location_pincode').addClass("clsAutoDisable");
     }     
 });   
});
$(document).on('click', '#term_to_location_pincode', function() {
	var service_courier_buyer = $('#Service_ID').val();
	   if(service_courier_buyer == 21){
		   var service_courier_buyer_url_to = "/ptlPincodesAutocompleteCourier?ptlFromLocation="+$('#term_from_location_pincode_id').val()+"&courier_delivery_type="+ $('#term_post_delivery_type').val()+"&to="+2;
	   }else{
		   var service_courier_buyer_url_to = "/ptlPincodesAutocomplete?ptlFromLocation="+$('#term_from_location_pincode_id').val();
	   }
	
	
 $("#term_to_location_pincode").autocomplete({
    source: service_courier_buyer_url_to,
    minLength: 1,
    select: function(event, ui) {
            $('#term_to_location_pincode').val(ui.item.value);
            $('#term_to_location_pincode_id').val(ui.item.id);
         $(this).closest("form").validate().element($('#term_to_location_pincode_id'));
         /*Need to add this below class to every autocomplete: Shriram */
        $('#term_to_location_pincode').addClass("clsAutoDisable");
       }                  
   });
});

// checking add items empty or not
$('#term_add_buyer_quote_save_ltl').click(function(e) {	

    $("#confirm_but").val("Float RFP");
	//validation add new docs
	$(".dynamic_validations_term").each(function (item) {
        $(this).rules("add", {
        	required : true,
        	accept: "docx|txt|doc|pdf|xls|csv|xlsx",
        });
        $("#term_add_buyer_quote_save_ltl").validate({
        	errorPlacement: function(error, element) {
            	//$(element).parent('div').after(error);
            }
        }).element("#"+$(this).attr("id"));
        //$("#term_buyer_quote").validate().element("#box_term2")
        /*if($("#term_buyer_quote").validate().element("#"+$(this).attr("id"))){
        	$(".documents-terms .error").addClass("forceerror");
        }else{
        	$(".documents-terms .error").removeClass("forceerror");
        }*/
        
    });	
	
    var id = $('.term_request_rows_ltl').children().size();
    if (id == 0) {           
        $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    } else {
        $('#term_buyer_quote_ltl').submit();
        if($('#term_buyer_quote_ltl').valid()){
            $("#term_add_buyer_quote_save_ltl_draft").prop('disabled', true);
            $("#term_add_buyer_quote_save_ltl").prop('disabled', true);         
        }
        return true;
    }
}); 

/**Code Started  28-04-2016 by Srinu
 * added below function for add line item or not in draft
 * check line item empty or not fot LTL and remaing services
 */
$('#term_add_buyer_quote_save_ltl_draft').click(function(e) {	
    var id = $('.term_request_rows_ltl').children().size();
    if (id == 0) {           
        $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    } else {
        $('#term_buyer_quote_ltl').submit();
        return true;
    }
});
/***********************Code Ended 28-04-2016 by Srinu***************************/

$("#term_buyer_quote_ltl").validate({
	ignore: [],
    rules: {    
    	"bid_type": {
            required: true,
        },
        "last_bid_date": {
            required: true,
        },
        "bid_close_time": {
            required: true,
        },
        "max_weight_accepted_text" : {
        	required : true,
        	fourbythreevalidations: true,
        },
        "high_price" : {
            required : true,
			decimalvalidation: true,
        },
        "quoteaccess_id": {
            required: true,
        },
        "incremental_weight_text" : {
        	required : { 
        		depends: function(element) {
        			if ($('#check_max_weight').is(':checked')){
	            		return true;
	            	}else{
	            		return false;
	            	}
        		}
        	},
        	nabythreevalidations: { 
        		depends: function(element) {
        			if ($('#check_max_weight').is(':checked')){
	            		return true;
	            	}else{
	            		return false;
	            	}
        		}
        	}
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
            		if ($(".create-posttype-service-ltl-term:checked").val() == 2){  
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
        "last_bid_date":{
            required:"Bid clouser date is required333."
        }, 
        "agree":{
            required:"Terms & Conditions is required."
        }, 
    }

});

//Add more items elements store in hidden fileds
var seller_id_list_reloca_term = new Array();
$('#term_add_relocation').click(function() {
                       
            var num = parseInt($('#next_term_add_relocation_buyer_more_id').val()) + 1;
            var typeselection = $('input[name=term_post_rate_card_type]:checked').val();
            $('#next_term_add_relocation_buyer_more_id').val(num);            
            var from_location_value = $('#term_from_location_id').val();
            var to_location_value = $('#term_to_location_id').val();
            var delivery_date = $('#term_delivery_date').val();
            var dispatch_date = $('#term_dispatch_date').val();
            var from_location = $('#term_from_location').val();
            var to_location = $('#term_to_location').val();
            var relocation_term_vehicle_cat_type='';
            if(typeselection==1){
            var relocation_term_volume = $('#relocation_term_volume').val();
            var relocation_term_noofshipments = $('#relocation_term_noofshipments').val();
            }else{
            var relocation_term_vehicle_cat = $('#term_vehicle_category option:selected').text();
            if(relocation_term_vehicle_cat==1){
            relocation_term_vehicle_cat_type = $('#term_vehicle_category_type option:selected').text();
            }
            var relocation_term_vehicle_cat_val = $('#term_vehicle_category option:selected').val();
            var relocation_term_vehicle_cat_type_val = $('#term_vehicle_category_type option:selected').val();
            var relocation_term_vehicle_model = $('#term_vehicle_model').val();
            var relocation_term_nooftrips = $('#relocation_term_nooftrips').val();
            }
            if($("#update_relocterm_line").val()==1){
            	//var remove_val_ftl=$("#update_term_row_unique").val();
            	//uniq_check_add_items_list.splice($.inArray(remove_val_ftl, uniq_check_add_items_list),1);
            	$('.request_row_' + $("#update_relocterm_row_count").val()).remove();	
            	$("#update_relocterm_line").val(0);
            }
            
            if (from_location != "" && to_location != "" && from_location_value != '' && to_location_value != '' && dispatch_date != '' && delivery_date != '' && relocation_term_volume != '' && relocation_term_noofshipments != '') {           	
            $('#error-relocation-term-add-item').text('');
            var seller_location_id = from_location_value;
            seller_id_list_reloca_term.unshift(seller_location_id);
           
            if(typeselection==1){
            var html = '<div class="table-row inner-block-bg request_row_' + num + '"><div class="col-md-3 padding-left-none" id="from_loc_'+ num +'">' + from_location + '</div><div class="col-md-3 padding-left-none" id="to_loc_'+ num +'">' + to_location + '</div><div class="col-md-3 padding-left-none" id="rel_vol_'+ num +'">' + relocation_term_volume + '</div><div class="col-md-2 padding-none" id="rel_shno_'+ num +'">' + relocation_term_noofshipments + '</div><div class="class="col-md-1 padding-none"><a class="edit_this edit termreloc_edit_lineitem" row_id="' + num + '"><i class="fa fa-edit red" title="Edit"></i></a>&nbsp;<a class="remove_this remove termreloc_remove_lineitem" row_id="' + num + '"><i class="fa fa-trash red" title="Delete"></i></a></div><input type="hidden" name="from_location[]" class="from_location"  value="' + from_location_value + '" id="from_location_id_'+num+'"><input type="hidden" name="to_location[]" value="' + to_location_value + '" id="to_location_id_'+num+'"><input type="hidden" name="delivery_date[]" value="' + delivery_date + '" id="delivery_date_'+num+'"><input type="hidden" name="dispatch_date[]" value="' + dispatch_date + '" id="dispatch_date_'+num+'"><input type="hidden" name="relocation_term_volume[]" value="' + relocation_term_volume + '" id="relocation_term_volume_'+num+'"><input type="hidden" name="relocation_term_noofshipments[]" value="' + relocation_term_noofshipments + '" id="relocation_term_noofshipments_'+num+'"></div>';
            }else{
            var html = '<div class="table-row inner-block-bg request_row_' + num + '"><div class="col-md-2 padding-left-none" id="from_loc_'+ num +'">' + from_location + '</div><div class="col-md-2 padding-left-none" id="to_loc_'+ num +'">' + to_location + '</div><div class="col-md-2 padding-left-none" id="rel_vehicle_cat_'+ num +'">' + relocation_term_vehicle_cat + '</div><div class="col-md-2 padding-left-none" id="rel_vehicle_type_cat'+ num +'">' + relocation_term_vehicle_cat_type + '</div><div class="col-md-2 padding-left-none" id="rel_vehicle_model_'+ num +'">' + relocation_term_vehicle_model + '</div><div class="col-md-1 padding-none" id="rel_shno_'+ num +'">' + relocation_term_nooftrips + '</div><div class="class="col-md-1 padding-none"><a class="edit_this edit termreloc_edit_lineitem" row_id="' + num + '"><i class="fa fa-edit red" title="Edit"></i></a>&nbsp;<a class="remove_this remove termreloc_remove_lineitem" row_id="' + num + '"><i class="fa fa-trash red" title="Delete"></i></a></div><input type="hidden" name="from_location[]" class="from_location"  value="' + from_location_value + '" id="from_location_id_'+num+'"><input type="hidden" name="to_location[]" value="' + to_location_value + '" id="to_location_id_'+num+'"><input type="hidden" name="delivery_date[]" value="' + delivery_date + '" id="delivery_date_'+num+'"><input type="hidden" name="dispatch_date[]" value="' + dispatch_date + '" id="dispatch_date_'+num+'"><input type="hidden" name="relocation_term_vehicle_cat[]" value="' + relocation_term_vehicle_cat_val + '" id="relocation_term_vehicle_cat_'+num+'"><input type="hidden" name="relocation_term_vehicle_cat_type[]" value="' + relocation_term_vehicle_cat_type_val + '" id="relocation_term_vehicle_cat_type_'+num+'"><input type="hidden" name="relocation_term_vehicle_model[]" value="' + relocation_term_vehicle_model + '" id="relocation_term_vehicle_model_'+num+'"><input type="hidden" name="relocation_term_nooftrips[]" value="' + relocation_term_nooftrips + '" id="relocation_term_nooftrips_'+num+'"></div>';	
            }

            $('.relocation_term_request_rows').append(html);
            $("#term_dispatch_date").prop('disabled', true);
            $("#term_delivery_date").prop('disabled', true);
            $("#term_dispatch_date").prev().addClass("disable-bg");
            $("#term_delivery_date").prev().addClass("disable-bg");
            $('#term_from_location').val("");
            $('#term_to_location').val("");
            $('#term_from_location_id').val("");
            $('#term_to_location_id').val("");  
            $('#relocation_term_noofshipments').val("");
            $('#relocation_term_volume').val(""); 
            $('#term_vehicle_model').val("");
            $('#relocation_term_nooftrips').val("");
            $('.selectpicker').selectpicker('refresh');
            //$('.termratetype_selection_buyer').prop('disabled', true);
            $("#term_dispatch_date").datepicker({
                changeMonth: true,
                numberOfMonths: 1,
                minDate: 0,        
                dateFormat: "dd/mm/yy",
                onClose: function(selectedDate) {
                    $("#term_delivery_date").datepicker(
                        "option", "minDate", selectedDate);
                }
            });
            $("#term_delivery_date").datepicker({
                changeMonth: true,
                numberOfMonths: 1,        
                minDate: 0,
                dateFormat: "dd/mm/yy",
                onClose: function(selectedDate) {
                    $("#term_dispatch_date").datepicker("option",
                        "maxDate", selectedDate);
                }
            });
                return false;
            }
        });
/*
 * Relcoation international term create post
 * Add line item function here
 * @srinu start 20 june,2016 
 */

//Set radio button value for check condition air  or ocean
$(document).ready(function(){
        $("input[class='check_relint_type']:radio").change(function(){
            if($(this).val() == '1')
            {                
              $('#check_post_valid_type').val(1);
              $('#check_air_ocean_sel').html("Average KG/Move");
            }
            else if($(this).val() == '2')
            {                
              $('#check_post_valid_type').val(2);
              $('#check_air_ocean_sel').html("Average CBM/Move");
            } else {
               $('#check_post_valid_type').val(2); 
               $('#check_air_ocean_sel').html("Average CBM/Move");
            }         
        });
    });

//check item add or not and 
$('#term_relocationint_add_buyer_quote,#term_relocationint_add_buyer_quote_draft').click(function(e) {    
	var thisid = $(this).attr("id");
	if(thisid == "term_relocationint_add_buyer_quote"){
		$("#confirm_but").val("Float RFP");
	}
	//validation add new docs
	$(".dynamic_validations_term").each(function (item) {
            $(this).rules("add", {
                    required : true,
                    accept: "docx|txt|doc|pdf|xls|csv|xlsx",
            });
            $("#term_relocationint_add_buyer_quote").validate().element("#"+$(this).attr("id"));
        });

        var id = $('.relocation_term_request_rows .table-row').children().size();
        if (id == 0) {
            $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
            show: true
            });
        return false;
        } else {
        $('#term_relocbuyer_quote').submit();
        if($('#term_relocbuyer_quote').valid()){
            $("#term_relocationint_add_buyer_quote").prop('disabled', true);    
            $("#term_relocationint_add_buyer_quote_draft").prop('disabled', true);      
        }
        return true;
    }
});

$('#term_add_relocationair').click(function() {
    
    var num = parseInt($('#next_term_add_relocation_buyer_more_id').val()) + 1;
    $('#next_term_add_relocation_buyer_more_id').val(num);            
    var from_location_value = $('#term_from_location_id').val();
    var to_location_value = $('#term_to_location_id').val();
    var delivery_date = $('#term_delivery_date').val();
    var dispatch_date = $('#term_dispatch_date').val();
    var from_location = $('#term_from_location').val();
    var to_location = $('#term_to_location').val();
    
    if($("#check_post_valid_type").val()==1){   
    var relocation_term_kg_move = $('#relocation_term_kg_move').val();
    }else{
    var relocation_term_kg_move = $('#relocation_term_cbm_move').val();	
    }
    
    if($("#check_post_valid_type").val()==1){   
    var relocation_term_noofmove = $('#relocation_termair_noofmoves').val();    
    }else{
    var relocation_term_noofmove = $('#relocation_termocean_noofmoves').val();    
    }
    
    
    if($("#update_relocterm_line").val()==1){    	
    	$('.request_row_' + $("#update_relocterm_row_count").val()).remove();	
    	$("#update_relocterm_line").val(0);
    }   
    
    if (from_location != "" && to_location != "" && from_location_value != '' && to_location_value != '' && dispatch_date != '' && delivery_date != '' && relocation_term_noofmove != '' && relocation_term_kg_move != '') {   
    $('#error-relocation-term-add-item').text('');
    var seller_location_id = from_location_value;
    seller_id_list_reloca_term.unshift(seller_location_id);
    
    var html = '<div class="table-row inner-block-bg request_row_' + num + '"><div class="col-md-3 padding-left-none" id="from_loc_'+ num +'">' + from_location + '</div><div class="col-md-3 padding-left-none" id="to_loc_'+ num +'">' + to_location + '</div><div class="col-md-3 padding-left-none" id="rel_vol_'+ num +'">' + relocation_term_noofmove + '</div><div class="col-md-2 padding-none" id="rel_shno_'+ num +'">' + relocation_term_kg_move + '</div><div class="class="col-md-1 padding-none"><a class="edit_this edit termrelocair_edit_lineitem" row_id="' + num + '"><i class="fa fa-edit red" title="Edit"></i></a>&nbsp;<a class="remove_this remove termreloc_remove_lineitem" row_id="' + num + '"><i class="fa fa-trash red" title="Delete"></i></a></div><input type="hidden" name="from_location[]" class="from_location"  value="' + from_location_value + '" id="from_location_id_'+num+'"><input type="hidden" name="to_location[]" value="' + to_location_value + '" id="to_location_id_'+num+'"><input type="hidden" name="delivery_date[]" value="' + delivery_date + '" id="delivery_date_'+num+'"><input type="hidden" name="dispatch_date[]" value="' + dispatch_date + '" id="dispatch_date_'+num+'"><input type="hidden" name="relocation_term_kgmove[]" value="' + relocation_term_kg_move + '" id="relocation_term_kgmove_'+num+'"><input type="hidden" name="relocation_term_noofmoves[]" value="' + relocation_term_noofmove + '" id="relocation_term_noofmoves_'+num+'"></div>';   

    $('.relocation_term_request_rows').append(html);
    $("#term_dispatch_date").prop('disabled', true);
    $("#term_delivery_date").prop('disabled', true);
    $("#term_dispatch_date").prev().addClass("disable-bg");
    $("#term_delivery_date").prev().addClass("disable-bg");
    $('#term_from_location').val("");
    $('#term_to_location').val("");
    $('#term_from_location_id').val("");
    $('#term_to_location_id').val("");  
    $('#relocation_term_noofmoves').val("");
    if($("#check_post_valid_type").val()==1){
    $('#relocation_term_kg_move').val(""); 
    }else{
    $('#relocation_term_cbm_move').val("");
    }
    
    $('.selectpicker').selectpicker('refresh');
    $("#term_dispatch_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,
        minDate: 0,        
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            $("#term_delivery_date").datepicker(
                "option", "minDate", selectedDate);
        }
    });
    $("#term_delivery_date").datepicker({
        changeMonth: true,
        numberOfMonths: 1,        
        minDate: 0,
        dateFormat: "dd/mm/yy",
        onClose: function(selectedDate) {
            $("#term_dispatch_date").datepicker("option",
                "maxDate", selectedDate);
        }
    });
        return false;
    }
});
//Add more items elements store in hidden fileds
    var seller_id_list = new Array();
    var uniq_check_add_items_list = new Array();
    $('#term_add_more_locations').click(
        function() {
            $("#ftl_term_insert").validate().cancelSubmit = false; // Validation
            var num = parseInt($('#next_term_add_buyer_more_id_term').val()) + 1;
            $('#next_term_add_buyer_more_id_term').val(num);
            if($('#Service_ID').val()==8 || $('#Service_ID').val()==9){
                var from_location_value = $('#from_airport_term_id').val();
                var to_location_value = $('#to_airport_term_id').val();
                var from_location = $('#from_airport_term').val();
                var to_location = $('#to_airport_term').val();

                var term_shipment_type = $('#term_shipment_type').val();
                var term_iecode = $('#term_iecode').val();
                var term_sender_identify = $('#term_sender_identify').val();
                var term_product_mode = $('#term_product_mode').val();
            } else {
                var from_location_value = $('#term_from_location_pincode_id').val();
                var to_location_value = $('#term_to_location_pincode_id').val();
                var from_location = $('#term_from_location_pincode').val();
                var to_location = $('#term_to_location_pincode').val();
            }

            if($('#Service_ID').val()==21){
            	var term_courier_types = $('#term_courier_types').val();
                var term_post_delivery_type = $('#term_post_delivery_type').val();
            }
            if($('#Service_ID').val()!=8 && $('#Service_ID').val()!=9){
                if ($('#term_door_pickup').prop('checked')) {
                    var term_door_pickup = 1;
                } else {
                    var term_door_pickup = 0;
                }
                if ($('#term_door_delivery').prop('checked')) {
                    var term_door_delivery = 1;
                } else {
                    var term_door_delivery = 0;
                }
            }

            var load_type_value = $("#term_load_type option:selected")
                .text();
            var term_package_type = $(
                "#term_package_type option:selected").text();
            var delivery_date = $('#term_delivery_date').val();
            var dispatch_date = $('#term_dispatch_date').val();
            var units_value = $('#term_units').val();
            var load_type = $('#term_load_type').val();
            var package_type = $('#term_package_type').val();
            var units = $('#term_units').val();
            var term_volume = $('#term_volume').val();
            var noofloads = $('#term_noof_packages').val();
            if($("#update_ltl_term_line").val()==1){
                var unique_id=$("#update_ltl_term_row_unique").val()
                uniq_check_add_items_list.splice($.inArray(unique_id, uniq_check_add_items_list),1);
                $('.request_row_' + $("#update_ltl_term_row_count").val()).remove();
                $("#update_ltl_term_line").val(0);
            }
            if (from_location != "" && to_location != "" && from_location_value != '' && to_location_value != '' && load_type != "" && package_type != "" && units != "" && term_volume != "" && noofloads != "" && $("#ftl_term_insert").valid()) {
                $('#error-add-item-term').text('');
                //Check post existas are not in line items
                var unique = from_location_value+to_location_value+load_type+package_type;
                if ($.inArray(unique,uniq_check_add_items_list)==-1) {
                    uniq_check_add_items_list.unshift(unique);
                    //Add multiple sellers store in variable
                    var seller_location_id = from_location_value;
                    seller_id_list.unshift(seller_location_id);

                    var html = '<div class="table-row inner-block-bg request_row_' + num + '">';
                    var html = html+'<div class="col-md-2 padding-left-none" id="term_ltl_from_'+num+'">' + from_location + '</div>';
                    var html = html+'<div class="col-md-2 padding-left-none" id="term_ltl_to_'+num+'">' + to_location + '</div>';
                    if($('#Service_ID').val() != 21){
                    var html = html+'<div class="col-md-2 padding-left-none">' + load_type_value + '</div>';
                    var html = html+'<div class="col-md-1 padding-left-none">' + term_package_type + '</div>';
                    }
                    var html = html+'<div class="col-md-1 padding-left-none">' + term_volume + '</div>';
                    var html = html+'<div class="col-md-1 padding-left-none">' + units + '</div>';
                    var html = html+'<div class="col-md-2 padding-left-none">' + noofloads + '</div>';
                    var html = html+'<div class="col-md-1 padding-none text-center"><a class="ltledit_term_items edit" data-term-ltl-string="'+unique+'" row_id_ltl_term="' + num + '"><i class="fa fa-edit red" title="Edit"></i></a>&nbsp;<a style ="cursor:pointer;" class="ltlremove_term_items remove" data-term-ltl-string="'+unique+'" row_id_ltl_term="' + num + '"><i class="fa fa-trash" title="Delete"></i></a></div>';
                    var html = html+'<input type="hidden" name="from_location[]" id="term_ltl_from_location_'+num+'" class="from_location"  value="' + from_location_value + '">';
                    var html = html+'<input type="hidden" name="to_location[]" id="term_ltl_to_location_'+num+'" value="' + to_location_value + '">';
                    var html = html+'<input type="hidden" name="delivery_date[]" id="term_ltl_delivery_'+num+'" value="' + delivery_date + '">';
                    var html = html+'<input type="hidden" name="dispatch_date[]" id="term_ltl_dispatch_'+num+'" value="' + dispatch_date + '">';
                    var html = html+'<input type="hidden" name="load_type[]" id="term_load_type_'+num+'" value="' + load_type + '">';
                    var html = html+'<input type="hidden" name="package_type[]" id="term_ltl_packagetype_'+num+'" value="' + package_type + '">';
                    var html = html+'<input type="hidden" name="units[]" id="term_ltl_units_'+num+'" value="' + units + '">';
                    var html = html+'<input type="hidden" name="term_volume[]" id="term_ltl_volume_'+num+'" value="' + term_volume + '">';
                    var html = html+'<input type="hidden" name="no_of_loads[]" id="term_ltl_no_of_loads_'+num+'" value="' + noofloads + '">';
                    var html = html+'<input type="hidden" name="term_shipment_type[]" id="term_shipment_type_'+num+'" value="' + term_shipment_type + '">';
                    var html = html+'<input type="hidden" name="term_iecode[]" id="term_iecode_'+num+'" value="' + term_iecode + '">';
                    var html = html+'<input type="hidden" name="term_sender_identify[]" id="term_sender_identify_'+num+'"  value="' + term_sender_identify + '">';
                    var html = html+'<input type="hidden" name="term_product_mode[]" id="term_product_mode_'+num+'" value="' + term_product_mode + '">';
                    var html = html+'<input type="hidden" name="term_door_pickup[]" value="' + term_door_pickup + '">';
                    var html = html+'<input type="hidden" name="term_courier_types[]" value="' + term_courier_types + '">';
                    var html = html+'<input type="hidden" name="term_post_delivery_type[]" value="' + term_post_delivery_type + '">';
                    var html = html+'<input type="hidden" name="term_door_delivery[]" value="' + term_door_delivery + '">';
                    $('.term_request_rows_ltl').append(html);
                    $('#term_from_location_pincode').val("");
                    $('#term_to_location_pincode').val("");
                    $('#term_from_location_pincode_id').val("");
                    $('#term_to_location_pincode_id').val("");
                    $("#term_dispatch_date").prop('disabled', true);
                    $("#term_delivery_date").prop('disabled', true);
                    $("#spot_enquiry_type").prop('disabled', true);
                    if($('#Service_ID').val()==21){
                    $("#term_domestic").prop('disabled', true);
                	$("#term_international").prop('disabled', true);
                	$("#term_documents").prop('disabled', true);
                 	$("#term_parcel").prop('disabled', true);
                    }
                    
                    $('#term_quantity').val("");
                    $('#term_capacity').val("");
                    $('#term_load_type').val("");
                    $('#term_package_type').val("");
                    //$('#term_units').val("");
                    $('#term_noof_packages').val("");
                    $('#term_volume').val("");
                    if($('#Service_ID').val()==8 || $('#Service_ID').val()==9){
                        $('#term_shipment_type').selectpicker('referesh');
                        $('#term_iecode').val("");
                        $('#term_sender_identify').selectpicker('referesh');
                        $('#term_product_mode').val("");
                    }
                    if($('#Service_ID').val()!=8 && $('#Service_ID').val()!=9){                        
                        $("#term_door_pickup").prop('checked', false);
                        $("#term_door_delivery").prop('checked', false);
                    }
                    $("#ftl_term_insert")
                        .validate().cancelSubmit = true;
                    $('.selectpicker').selectpicker('refresh');
                    $("#term_dispatch_date").datepicker({
                        changeMonth: true,
                        numberOfMonths: 1,
                        minDate: 0,
                        dateFormat: "dd/mm/yy",
                        onClose: function(selectedDate) {
                            $("#term_delivery_date").datepicker(
                                "option", "minDate", selectedDate);
                        }
                    });
                    $("#term_delivery_date").datepicker({
                        changeMonth: true,
                        numberOfMonths: 1,
                        minDate: 0,
                        dateFormat: "dd/mm/yy",
                        onClose: function(selectedDate) {
                            $("#term_dispatch_date").datepicker("option",
                                "maxDate", selectedDate);
                        }
                    });
                } else {
                    $("#erroralertmodal .modal-body").html("Post already exists for the specified parameters! Please modify parameters.");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                }

                return false;
            }
        });

//Need to check remove item exists or not
$(document).on('click', '.ltlremove_term_items', function() {
     var rowid_ltl_term = $(this).attr("row_id_ltl_term");
     remove_val_ftl = $(this).attr("data-term-ltl-string");
     var r = confirm("Are you sure, you want you delete?");
     if (r == true) {
     	uniq_check_add_items_list.splice($.inArray(remove_val_ftl, uniq_check_add_items_list),1);
         $('.request_row_' + rowid_ltl_term).remove();
     }       
 });

$(document).on('click', '.ltledit_term_items', function() {
    
	 var rowid_ltl_term = $(this).attr("row_id_ltl_term");
	 remove_val_ftl = $(this).attr("data-term-ltl-string");
	   $("#update_ltl_term_line").val(1);
	   $("#update_ltl_term_row_count").val(rowid_ltl_term);
	   $("#update_ltl_term_row_unique").val(remove_val_ftl);
	   
	   $('#term_from_location_pincode').val($("#term_ltl_from_"+rowid_ltl_term).html());
       $('#term_to_location_pincode').val($("#term_ltl_to_"+rowid_ltl_term).html());
       $('#term_from_location_pincode_id').val($("#term_ltl_from_location_"+rowid_ltl_term).val());
       $('#term_to_location_pincode_id').val($("#term_ltl_to_location_"+rowid_ltl_term).val());               
       $("#term_dispatch_date").prop('disabled', true);
       $("#term_delivery_date").prop('disabled', true);
       $('#term_load_type').selectpicker('val',$("#term_load_type_"+rowid_ltl_term).val());
       $('#term_package_type').selectpicker('val',$("#term_ltl_packagetype_"+rowid_ltl_term).val());
       $('#term_noof_packages').val($("#term_ltl_no_of_loads_"+rowid_ltl_term).val());    
       $('#term_volume').val($("#term_ltl_volume_"+rowid_ltl_term).val());    
       
       if($('#Service_ID').val()==8 || $('#Service_ID').val()==9){            	
           $('#from_airport_term_id').val($("#term_ltl_from_location_"+rowid_ltl_term).val());
           $('#to_airport_term_id').val($("#term_ltl_to_location_"+rowid_ltl_term).val());
           $('#from_airport_term').val($("#term_ltl_from_"+rowid_ltl_term).html());
           $('#to_airport_term').val($("#term_ltl_to_"+rowid_ltl_term).html());
           
           $('#term_shipment_type').selectpicker('val',$("#term_shipment_type_"+rowid_ltl_term).val());
           $('#term_iecode').val($("#term_iecode_"+rowid_ltl_term).val());
           $('#term_sender_identify').selectpicker('val',$("#term_sender_identify_"+rowid_ltl_term).val());
           $('#term_product_mode').val($("#term_product_mode_"+rowid_ltl_term).val());
       } 
    
});


//Place indent Validation for contracts in FTL
$(".boonowvalidations").click(function () {    
	var uniqId  = $(this).attr("id");
	//alert(uniqId);
	var currentIndent = $("#indenet_quantity_"+uniqId).val();	
	if (currentIndent == '') {		
		 $("#erroralertmodal .modal-body").html("Please enter current indent quantity.");
         $("#erroralertmodal").modal({
             show: true
         });
		return false;
	} else if(isNaN(currentIndent)) {
		$("#erroralertmodal .modal-body").html("Please enter numbers only.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	} else if(currentIndent<=0) {
		$("#erroralertmodal .modal-body").html("Please enter valid indent quantity.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	} else {
		return true;
	}
        
        
}); 



$(".couriertermbooknow .courier_term_length,.couriertermbooknow .courier_term_width,.couriertermbooknow .courier_term_height," +
  ".couriertermbooknow .courier_CheckUnitWeight,.couriertermbooknow .courier_noofpack,.couriertermbooknow .courier_packvalue").keyup(function() {
	
	
	var buyer_term_contract_id = $(this).attr('id').split("_").reverse()[0];
	
	var increment = $("#price_slab_hidden_value_"+buyer_term_contract_id).val();
	var seller_id = $("#seller_id_"+buyer_term_contract_id).val();
	var buyer_quote_id = $("#buyer_quote_id_"+buyer_term_contract_id).val();
	var term_buyer_quote_sellers_quotes_price_id = $("#term_buyer_quote_sellers_quotes_price_id_"+buyer_term_contract_id).val();
	
	var courier_type = $("#courier_type_"+buyer_term_contract_id).val();
	if(courier_type==2){
		var length = $("#courier_term_length_"+buyer_term_contract_id).val();
		var width = $("#courier_term_width_"+buyer_term_contract_id).val();
		var height = $("#courier_term_height_"+buyer_term_contract_id).val();
		var lengthUnit = $("#courier_term_weighttype_"+buyer_term_contract_id).val();
  	}
  	else{
  		var length = $("#doc_type_"+buyer_term_contract_id).val();
		var width = $("#doc_type_"+buyer_term_contract_id).val();
		var height = $("#doc_type_"+buyer_term_contract_id).val();
		var lengthUnit = $("#doc_type_"+buyer_term_contract_id).val();  
  	}
	var UnitWeight = $("#courier_CheckUnitWeight_"+buyer_term_contract_id).val();
	var WeightUnit = $("#courier_CheckWeightUnit_"+buyer_term_contract_id).val();
	var noOfPackage = $("#courier_term_noofpackages_"+buyer_term_contract_id).val();
    var packageValue = $("#courierterm_package_value_"+buyer_term_contract_id).val();
    var conversionfactor = $("#conversion_factor_"+buyer_term_contract_id).val();
    var transit_days = $("#transit_days_"+buyer_term_contract_id).val();
    var fuel_charges = $("#fuel_charges_"+buyer_term_contract_id).val();
    var cod_charges = $("#cod_charges_"+buyer_term_contract_id).val();
    var freight_charges = $("#freight_charges_"+buyer_term_contract_id).val();
    var arc_charges = $("#arc_charges_"+buyer_term_contract_id).val();
    var max_value = $("#max_value_"+buyer_term_contract_id).val();
    
    var incremental_weight = $("#incremental_weight_"+buyer_term_contract_id).val();
    var rate_per_increment = $("#rate_per_increment_"+buyer_term_contract_id).val();
    var remaining_incremental_weight = $("#remaining_incremental_weight_"+buyer_term_contract_id).val();
    
    
      
    var regexPattern= /^\d{1,4}(\.\d{1,4})?$/;
    var regexpackagevalue = /^\d{1,4}(\.\d{1,2})?$/;
	var regexNumericPattern= /^\d{1,4}?$/;
	
	
	if((length).trim() && (width).trim() && (height).trim()  && (noOfPackage).trim() && (packageValue).trim()){
		
        $.ajax({
            type: "POST",
            url: "/getcouriertermfreightdetails",
            data: {
                 'length': length,
                 'width': width,
                 'height': height,
                 'lengthUnit': lengthUnit,
                 'UnitWeight': UnitWeight,
                 'WeightUnit': WeightUnit,
                 'noOfPackage': noOfPackage,
                 'packageValue':packageValue,
                 'sellerid': seller_id,
                 'buyer_quote_id': buyer_quote_id,
                 'term_buyer_quote_sellers_quotes_price_id':term_buyer_quote_sellers_quotes_price_id,
                 'conversionfactor':conversionfactor,
                 'transit_days':transit_days,
                 'fuel_charges':fuel_charges,
                 'cod_charges':cod_charges,
                 'freight_charges':freight_charges,
                 'arc_charges':arc_charges,
                 'max_value':max_value,
                 'courier_type':courier_type,
                 'incremental_weight':incremental_weight,
                 'rate_per_increment':rate_per_increment,
                 'remaining_incremental_weight':remaining_incremental_weight,
            },
            success: function(jsonData) {
                if(jsonData.success && jsonData.freightDetails) {
                    $('#totalamnt_'+buyer_term_contract_id).html(jsonData.freightDetails.totalAmount);
                    $('#total_hidden_amnt_'+buyer_term_contract_id).val(jsonData.freightDetails.totalAmount);
                    $('#totalfrieght_'+buyer_term_contract_id).html(jsonData.freightDetails.totalFrieght);
                }
            }
        }, "json");
    }
	
});
$(".couriertermbooknow .courier_CheckWeightUnit,.couriertermbooknow .courier_displayvolumeweight").change(function() {
			
			
			var buyer_term_contract_id = $(this).attr('id').split("_").reverse()[0];
			
			var increment = $("#price_slab_hidden_value_"+buyer_term_contract_id).val();
			var seller_id = $("#seller_id_"+buyer_term_contract_id).val();
			var buyer_quote_id = $("#buyer_quote_id_"+buyer_term_contract_id).val();
			var term_buyer_quote_sellers_quotes_price_id = $("#term_buyer_quote_sellers_quotes_price_id_"+buyer_term_contract_id).val();
			
			var courier_type = $("#courier_type_"+buyer_term_contract_id).val();
			if(courier_type==2){
				var length = $("#courier_term_length_"+buyer_term_contract_id).val();
				var width = $("#courier_term_width_"+buyer_term_contract_id).val();
				var height = $("#courier_term_height_"+buyer_term_contract_id).val();
				var lengthUnit = $("#courier_term_weighttype_"+buyer_term_contract_id).val();
		  	}
		  	else{
		  		var length = $("#doc_type_"+buyer_term_contract_id).val();
				var width = $("#doc_type_"+buyer_term_contract_id).val();
				var height = $("#doc_type_"+buyer_term_contract_id).val();
				var lengthUnit = $("#doc_type_"+buyer_term_contract_id).val();  
		  	}
			var UnitWeight = $("#courier_CheckUnitWeight_"+buyer_term_contract_id).val();
			var WeightUnit = $("#courier_CheckWeightUnit_"+buyer_term_contract_id).val();
			var noOfPackage = $("#courier_term_noofpackages_"+buyer_term_contract_id).val();
		    var packageValue = $("#courierterm_package_value_"+buyer_term_contract_id).val();
		    var conversionfactor = $("#conversion_factor_"+buyer_term_contract_id).val();
		    var transit_days = $("#transit_days_"+buyer_term_contract_id).val();
		    var fuel_charges = $("#fuel_charges_"+buyer_term_contract_id).val();
		    var cod_charges = $("#cod_charges_"+buyer_term_contract_id).val();
		    var freight_charges = $("#freight_charges_"+buyer_term_contract_id).val();
		    var arc_charges = $("#arc_charges_"+buyer_term_contract_id).val();
		    var max_value = $("#max_value_"+buyer_term_contract_id).val();
		    
		    var incremental_weight = $("#incremental_weight_"+buyer_term_contract_id).val();
		    var rate_per_increment = $("#rate_per_increment_"+buyer_term_contract_id).val();
		    var remaining_incremental_weight = $("#remaining_incremental_weight_"+buyer_term_contract_id).val();
		    
		    
		      
		    var regexPattern= /^\d{1,4}(\.\d{1,4})?$/;
		    var regexpackagevalue = /^\d{1,4}(\.\d{1,2})?$/;
			var regexNumericPattern= /^\d{1,4}?$/;
			
			
			if((length).trim() && (width).trim() && (height).trim()  && (noOfPackage).trim() && (packageValue).trim()){
				
		        $.ajax({
		            type: "POST",
		            url: "/getcouriertermfreightdetails",
		            data: {
		                 'length': length,
		                 'width': width,
		                 'height': height,
		                 'lengthUnit': lengthUnit,
		                 'UnitWeight': UnitWeight,
		                 'WeightUnit': WeightUnit,
		                 'noOfPackage': noOfPackage,
		                 'packageValue':packageValue,
		                 'sellerid': seller_id,
		                 'buyer_quote_id': buyer_quote_id,
		                 'term_buyer_quote_sellers_quotes_price_id':term_buyer_quote_sellers_quotes_price_id,
		                 'conversionfactor':conversionfactor,
		                 'transit_days':transit_days,
		                 'fuel_charges':fuel_charges,
		                 'cod_charges':cod_charges,
		                 'freight_charges':freight_charges,
		                 'arc_charges':arc_charges,
		                 'max_value':max_value,
		                 'courier_type':courier_type,
		                 'incremental_weight':incremental_weight,
		                 'rate_per_increment':rate_per_increment,
		                 'remaining_incremental_weight':remaining_incremental_weight,
		            },
		            success: function(jsonData) {
		                if(jsonData.success && jsonData.freightDetails) {
		                    $('#totalamnt_'+buyer_term_contract_id).html(jsonData.freightDetails.totalAmount);
		                    $('#total_hidden_amnt_'+buyer_term_contract_id).val(jsonData.freightDetails.totalAmount);
		                    $('#totalfrieght_'+buyer_term_contract_id).html(jsonData.freightDetails.totalFrieght);
		                }
		            }
		        }, "json");
		    }
			
		});



//Place indent Validation for contracts in FTL
$(".boonowvalidationsterm").click(function () {
	var uniqId  = $(this).attr("id");	
	var length = $("#term_length_"+uniqId).val();
	var width = $("#term_width_"+uniqId).val();
	var height = $("#term_height_"+uniqId).val();
	var lengthUnit = $("#term_weighttype_"+uniqId).val();
	var noOfPackage = $("#term_noofpackages_"+uniqId).val();
        var unitweight = $("#ptlUnitsWeight_"+uniqId).val();
        var unitweighttype = $("#ptlCheckUnitWeight_"+uniqId).val();
	if (length == '') {		
		 $("#erroralertmodal .modal-body").html("Please enter length.");
         $("#erroralertmodal").modal({
             show: true
         });
		return false;
	} else if(width == '') {
		$("#erroralertmodal .modal-body").html("Please enter width.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	} else if(height == '') {
		$("#erroralertmodal .modal-body").html("Please enter height.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	}  else if(lengthUnit == '') {
		$("#erroralertmodal .modal-body").html("Please enter length unit.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	}	
	else if(noOfPackage == '') {
		$("#erroralertmodal .modal-body").html("Please enter no of packages.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	}else if(unitweight == '') {
		$("#erroralertmodal .modal-body").html("Please enter unit weight.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	}else if(unitweighttype == '') {
		$("#erroralertmodal .modal-body").html("Please enter unit weight type.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	} else if(isNaN(length || width || height || noOfPackage || unitweight)) {
		$("#erroralertmodal .modal-body").html("Please enter numbers only.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	}
	
}); 


$(".booknowcouriervalidationsterm").click(function () {
	var uniqId  = $(this).attr("id");	
	var courier_type = $("#courier_type_"+uniqId).val();
	if(courier_type==2){
		var length = $("#courier_term_length_"+uniqId).val();
		var width = $("#courier_term_width_"+uniqId).val();
		var height = $("#courier_term_height_"+uniqId).val();
		var lengthUnit = $("#courier_term_weighttype_"+uniqId).val();
	}else{
		var length = $("#doc_type_"+uniqId).val();
		var width = $("#doc_type_"+uniqId).val();
		var height = $("#doc_type_"+uniqId).val();
		var lengthUnit = $("#doc_type_"+uniqId).val();
	}
	
	var noOfPackage = $("#courier_term_noofpackages_"+uniqId).val();
    var unitweight = $("#courier_CheckUnitWeight_"+uniqId).val();
    var unitweighttype = $("#courier_CheckWeightUnit_"+uniqId).val();
	var packagevalue = $("#courierterm_package_value_"+uniqId).val();
	
	
	if (length == '') {		
		 $("#erroralertmodal .modal-body").html("Please enter length.");
         $("#erroralertmodal").modal({
             show: true
         });
		return false;
	} else if(width == '') {
		$("#erroralertmodal .modal-body").html("Please enter width.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	} else if(height == '') {
		$("#erroralertmodal .modal-body").html("Please enter height.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	}  else if(lengthUnit == '') {
		$("#erroralertmodal .modal-body").html("Please select length unit.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	}else if(unitweight == '') {
		$("#erroralertmodal .modal-body").html("Please enter unit weight.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	}else if(unitweighttype == '') {
		$("#erroralertmodal .modal-body").html("Please select unit weight type.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	}	
	else if(noOfPackage == '') {
		$("#erroralertmodal .modal-body").html("Please enter no of packages.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	}	
	else if(packagevalue == '') {
		$("#erroralertmodal .modal-body").html("Please enter Package Value.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	} else if(isNaN(length || width || height || noOfPackage || unitweight || packagevalue)) {
		$("#erroralertmodal .modal-body").html("Please enter numbers only.");
        $("#erroralertmodal").modal({
            show: true
        });
		return false;
	}
	
}); 



$('.noofpack').keyup(function(event) {
    if(/\d/.test(String.fromCharCode(event.which) )===false){
        $('#error-package').html('enter only numeric value');
    $('.noofpack').val("");
    }else{
        $('#error-package').html('');
    }
});

//Calculations for volume and amounts for palce indent
$('.displayvolumeweight, .ptlCheckUnitWeight, .numtermpack').keyup(function() {	
	if($(this).closest('.noofpack').val()!="" && $(this).closest('.noofpack').val()!=0){
	var uniqId  = $(this).attr('termpack_id');
	//alert(uniqId);
	var ptlLength  ='';
        var ptlWidth = '';
        var ptlHeight ='';
        var contract_kgper_cft = $('#contract_kgper_cft_'+uniqId).val();
        var contract_rateper_kg = $('#contract_rateperkg_'+uniqId).val();
        var data = {
                'ptlweightType' : $('#term_weighttype_'+uniqId).val(),
                'ptlLength' : $('#term_length_'+uniqId).val(),
                'ptlWidth' : $('#term_width_'+uniqId).val(),
                'ptlHeight' : $('#term_height_'+uniqId).val()
        };
if($('#term_weighttype_'+uniqId).val()!=""){
    $.ajax({
			type : "GET",
			url : '/getVolumeWeight',
			data : data,
			dataType : 'text',
			success : function(data) {
				console.log(data);
				if (data !="") {
                    var res = data.split(" ");
					$('#term_hidden_volume_'+uniqId).val(res[0]);
                    
                    if($('#ptlCheckUnitWeight_'+uniqId).val() == 2){
                        var units = $('#ptlUnitsWeight_'+uniqId).val() * 0.001;

                    }else if($('#ptlCheckUnitWeight_'+uniqId).val() == 3){
                        var units = $('#ptlUnitsWeight_'+uniqId).val() * 1000;

                    }else{
                        var units = $('#ptlUnitsWeight_'+uniqId).val();
                    }   
					var volume = $("#term_hidden_volume_"+uniqId).val(); 
                    var computed_max_volume =Math.max(volume, units);                                      
					var noOfPackage = $("#term_noofpackages_"+uniqId).val();
					var contract_kgper_cft = $('#contract_kgper_cft_'+uniqId).val();
					$('#hidden_volumetric_weight_'+uniqId).val(volume);
					//var totalAmount = parseInt(noOfPackage * volume);
					//alert(volume);
                    var totalAmount = parseInt(noOfPackage * computed_max_volume);
					//var volumetricWeight = parseInt(totalAmount * contract_kgper_cft);
					//var baseFright = parseInt(volumetricWeight * contract_rateper_kg);
                    var baseFright = parseInt(totalAmount * contract_rateper_kg);
					$('#displaybaseFright_'+uniqId).html(baseFright);	
					$('#displayVolumetricWeight_'+uniqId).html(volume);	
					$('#totalamnt_'+uniqId).html(baseFright);
					$('#total_hidden_amnt_'+uniqId).val(baseFright);
					$('#displayVolumeW_'+uniqId).html(totalAmount);	
                                        $('#term_hidden_volume_ltl_'+uniqId).val(totalAmount);	
					$('#displayVolumenone').hide();
			        $('#displayVolumeW').show();
				} else {					 
					 $("#erroralertmodal .modal-body").html("Please select package sizes.");
			         $("#erroralertmodal").modal({
			             show: true
			         });
					$('#ptlCheckVolWeight').val(null);
					$('#ptlCheckVolWeight').selectpicker('refresh');				
				}
			},
			error : function(request, status, error) {				
				    //alert(request.responseText);
			},
		});  
 }    
	}else{
		 $("#erroralertmodal .modal-body").html("Please select package sizes.");
         $("#erroralertmodal").modal({
             show: true
         });
	}
});

$('.modifydisplayvolumeweight').on('change', function(){
    if($(this).closest('.noofpack').val()!="" && $(this).closest('.noofpack').val()!=0){
    var uniqId  = $(this).attr('termpack_id');
    var ptlLength  ='';
    var ptlWidth = '';
    var ptlHeight ='';
    var contract_kgper_cft = $('#contract_kgper_cft').val();
    var contract_rateper_kg = $('#contract_rateperkg_'+uniqId).val();
var data = {
    'ptlweightType' : $('#term_weighttype_'+uniqId).val(),
    'ptlLength' : $('#term_length_'+uniqId).val(),
    'ptlWidth' : $('#term_width_'+uniqId).val(),
    'ptlHeight' : $('#term_height_'+uniqId).val()
};
if($('#term_weighttype_'+uniqId).val()!=""){
    $.ajax({
            type : "GET",
            url : '/getVolumeWeight',
            data : data,
            dataType : 'text',
            success : function(data) {
                //console.log(data);
                if (data !="") {
                        var res = data.split(" ");
                    $('#term_hidden_volume_'+uniqId).val(res[0]);
                                        
                                        if($('#ptlCheckUnitWeight_'+uniqId).val() == 2){
                                            var units = $('#ptlUnitsWeight_'+uniqId).val() * 0.001;

                                        }else if($('#ptlCheckUnitWeight_'+uniqId).val() == 3){
                                            var units = $('#ptlUnitsWeight_'+uniqId).val() * 1000;

                                        }else{
                                            var units = $('#ptlUnitsWeight_'+uniqId).val();
                                        }   
                    var volume = $("#term_hidden_volume_"+uniqId).val(); 
                                        var computed_max_volume =Math.max(volume, units);
                                        
                    //alert(computed_max_volume);
                    var noOfPackage = $("#term_noofpackages_"+uniqId).val();
                    var contract_kgper_cft = $('#contract_kgper_cft_'+uniqId).val();
                    //var totalAmount = parseInt(noOfPackage * volume);
                                        var totalAmount = parseInt(noOfPackage * computed_max_volume);
                                        
                    //var volumetricWeight = parseInt(totalAmount * contract_kgper_cft);
                    //var baseFright = parseInt(volumetricWeight * contract_rateper_kg);
                    var baseFright = parseInt(totalAmount * contract_rateper_kg);
                    $('#displaybaseFright_'+uniqId).html(baseFright);   
                    $('#displayVolumetricWeight_'+uniqId).html(volume); 
                    $('#totalamnt_'+uniqId).html(baseFright);
                    $('#total_hidden_amnt_'+uniqId).val(baseFright);
                    $('#displayVolumeW_'+uniqId).html(totalAmount);             
                    $('#displayVolumenone').hide();
                    $('#displayVolumeW').show();
                } else {                     
                     $("#erroralertmodal .modal-body").html("Please select package sizes.");
                     $("#erroralertmodal").modal({
                         show: true
                     });
                    $('#ptlCheckVolWeight').val(null);
                    $('#ptlCheckVolWeight').selectpicker('refresh');                
                }
            },
            error : function(request, status, error) {              
                    //alert(request.responseText);
            },
        });  
   }
    }else{
         $("#erroralertmodal .modal-body").html("Please select package sizes.");
         $("#erroralertmodal").modal({
             show: true
         });
    }
});

$('.term_height, .term_length, .term_width').keyup(function() {
//$('.modifydisplayvolumeweight, .modifyptlCheckUnitWeight').on('change', function(){
	if($(this).closest('.noofpack').val()!="" && $(this).closest('.noofpack').val()!=0){
	var uniqId  = $(this).attr('termpack_id');
	var ptlLength  ='';
    var ptlWidth = '';
    var ptlHeight ='';
    var contract_kgper_cft = $('#contract_kgper_cft').val();
    var contract_rateper_kg = $('#contract_rateperkg_'+uniqId).val();
var data = {
	'ptlweightType' : $('#term_weighttype_'+uniqId).val(),
	'ptlLength' : $('#term_length_'+uniqId).val(),
	'ptlWidth' : $('#term_width_'+uniqId).val(),
	'ptlHeight' : $('#term_height_'+uniqId).val()
};
if($('#term_weighttype_'+uniqId).val()!=""){
    $.ajax({
			type : "GET",
			url : '/getVolumeWeight',
			data : data,
			dataType : 'text',
			success : function(data) {
				//console.log(data);
				if (data !="") {
                        var res = data.split(" ");
					$('#term_hidden_volume_'+uniqId).val(res[0]);
                                        
                                        if($('#ptlCheckUnitWeight_'+uniqId).val() == 2){
                                            var units = $('#ptlUnitsWeight_'+uniqId).val() * 0.001;

                                        }else if($('#ptlCheckUnitWeight_'+uniqId).val() == 3){
                                            var units = $('#ptlUnitsWeight_'+uniqId).val() * 1000;

                                        }else{
                                            var units = $('#ptlUnitsWeight_'+uniqId).val();
                                        }   
					var volume = $("#term_hidden_volume_"+uniqId).val(); 
                                        var computed_max_volume =Math.max(volume, units);
                                        
					//alert(computed_max_volume);
					var noOfPackage = $("#term_noofpackages_"+uniqId).val();
					var contract_kgper_cft = $('#contract_kgper_cft_'+uniqId).val();
					//var totalAmount = parseInt(noOfPackage * volume);
                                        var totalAmount = parseInt(noOfPackage * computed_max_volume);
                                        
					//var volumetricWeight = parseInt(totalAmount * contract_kgper_cft);
					//var baseFright = parseInt(volumetricWeight * contract_rateper_kg);
                    var baseFright = parseInt(totalAmount * contract_rateper_kg);
                    $('#displaybaseFright_'+uniqId).html(baseFright);	
                    $('#displayVolumetricWeight_'+uniqId).html(volume);	
                    $('#totalamnt_'+uniqId).html(baseFright);
                    $('#total_hidden_amnt_'+uniqId).val(baseFright);
                    $('#displayVolumeW_'+uniqId).html(totalAmount);				
					$('#displayVolumenone').hide();
			        $('#displayVolumeW').show();
				} else {					 
					 $("#erroralertmodal .modal-body").html("Please select package sizes.");
			         $("#erroralertmodal").modal({
			             show: true
			         });
					$('#ptlCheckVolWeight').val(null);
					$('#ptlCheckVolWeight').selectpicker('refresh');				
				}
			},
			error : function(request, status, error) {				
				    //alert(request.responseText);
			},
		});  
   }
	}else{
		 $("#erroralertmodal .modal-body").html("Please select package sizes.");
         $("#erroralertmodal").modal({
             show: true
         });
	}
});


//$('.courier_term_height, .courier_term_length, .courier_term_width').keyup(function() {
//	
//	if($(this).closest('.noofpack').val()!="" && $(this).closest('.noofpack').val()!=0){
//	var uniqId  = $(this).attr('termpack_id');
//	var ptlLength  ='';
//    var ptlWidth = '';
//    var ptlHeight ='';
//    var max_weight =  $("#max_weight_"+uniqId).val();
//    var contract_kgper_cft = $('#contract_kgper_cft').val();
//    var contract_rateper_kg = $('#contract_rateperkg_'+uniqId).val();
//var data = {
//	'ptlweightType' : $('#courier_term_weighttype_'+uniqId).val(),
//	'ptlLength' : $('#courier_term_length_'+uniqId).val(),
//	'ptlWidth' : $('#courier_term_width_'+uniqId).val(),
//	'ptlHeight' : $('#courier_term_height_'+uniqId).val()
//};
//if($('#term_weighttype_'+uniqId).val()!=""){
//    $.ajax({
//			type : "GET",
//			url : '/getVolumeWeight',
//			data : data,
//			dataType : 'text',
//			success : function(data) {
//				//console.log(data);
//				if (data !="") {
//                    var res = data.split(" ");
//					$('#term_hidden_volume_'+uniqId).val(res[0]);
//                                        
//                    if($('#ptlCheckUnitWeight_'+uniqId).val() == 2){
//                        var units = $('#ptlUnitsWeight_'+uniqId).val() * 0.001;
//
//                    }else if($('#ptlCheckUnitWeight_'+uniqId).val() == 3){
//                        var units = $('#ptlUnitsWeight_'+uniqId).val() * 1000;
//
//                    }else{
//                        var units = $('#ptlUnitsWeight_'+uniqId).val();
//                    }   
//					var volume = $("#term_hidden_volume_"+uniqId).val(); 
//                    var computed_max_volume =Math.max(volume, units);
//                    var noOfPackage = $("#term_noofpackages_"+uniqId).val();
//					var contract_kgper_cft = $('#contract_kgper_cft_'+uniqId).val();
//					var totalAmount = parseInt(noOfPackage * computed_max_volume);
//                    var baseFright = parseInt(totalAmount * contract_rateper_kg);
//                    $('#displaybaseFright_'+uniqId).html(baseFright);	
//                    $('#displayVolumetricWeight_'+uniqId).html(volume);	
//                    $('#totalamnt_'+uniqId).html(baseFright);
//                    $('#total_hidden_amnt_'+uniqId).val(baseFright);
//                    $('#displayVolumeW_'+uniqId).html(totalAmount);				
//					$('#displayVolumenone').hide();
//			        $('#displayVolumeW').show();
//				} else {					 
//					 $("#erroralertmodal .modal-body").html("Please select package sizes.");
//			         $("#erroralertmodal").modal({
//			             show: true
//			         });
//					$('#ptlCheckVolWeight').val(null);
//					$('#ptlCheckVolWeight').selectpicker('refresh');				
//				}
//			},
//			error : function(request, status, error) {				
//				    //alert(request.responseText);
//			},
//		});  
//   }
//	}else{
//		 $("#erroralertmodal .modal-body").html("Please select package sizes.");
//         $("#erroralertmodal").modal({
//             show: true
//         });
//	}
//});







$(".noofpack").keyup(function () {
    var uniqId  = $(this).attr('termpack_id');
        if($('#ptlCheckUnitWeight_'+uniqId).val() == 2){
            var units = $('#ptlUnitsWeight_'+uniqId).val() * 0.001;

        }else if($('#ptlCheckUnitWeight_'+uniqId).val() == 3){
            var units = $('#ptlUnitsWeight_'+uniqId).val() * 1000;

        }else{
            var units = $('#ptlUnitsWeight_'+uniqId).val();
        }
	
	var noOfPackage = $("#term_noofpackages_"+uniqId).val();
	var volume = $("#term_hidden_volume_"+uniqId).val();
        
        var computed_max_volume =Math.max(volume, units);
        //alert(computed_max_volume);
	var contract_kgper_cft = $('#contract_kgper_cft_'+uniqId).val();
	var contract_rateper_kg = $('#contract_rateperkg_'+uniqId).val();
	var totalAmount = parseInt(noOfPackage * computed_max_volume);
	//var volumetricWeight = parseInt(totalAmount * contract_kgper_cft);
	//var baseFright = parseInt(volumetricWeight * contract_rateper_kg);
        var baseFright = parseInt(totalAmount * contract_rateper_kg);
        $('#displaybaseFright_'+uniqId).html(baseFright);	
        $('#displayVolumetricWeight_'+uniqId).html(volume);	
        $('#totalamnt_'+uniqId).html(baseFright);
        $('#total_hidden_amnt_'+uniqId).val(baseFright);
        $('#displayVolumeW_'+uniqId).html(totalAmount);		
}); 

//End calculations for placeindent functionality

//Post private and Post public conditions
$('#term_quote_access_private').click(function() {
    var id = $('.term_request_rows_ltl').children().size();
    if (id != 0) {
        $("#showhidepost").css("display", "block");
        $.ajax({
            url: '/getTermSellerList',
            type: "post",
            data: {
                'seller_list': seller_id_list,
                '_token': $('input[name=_token]').val()
            },
            success: function(data) {
                if (data != "") {
                    $(".token-input-list").remove();
                    $("#term_seller_list").tokenInput(data);
                } else {                       
                    $("#erroralertmodal .modal-body").html("No Sellers Available.");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                    $('#term_quote_access_private').prop('checked', false);
                    $("#showhidepost").css("display", "none");
                    return false;
                }
            },
            error: function(request, status, error) {
                $('#term_quote_access_private').val(null);
                alert(error);
            },
        });
    } else {
    	$("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
        $("#erroralertmodal").modal({
            show: true
        });
        $('#term_quote_access_private').prop('checked', false);
        return false;
    }
});
$('#term_quote_access_public').click(function() {
    var id = $('.term_request_rows_ltl').children().size();
    if (id == 0) {
        $("#showhidepost").css("display", "none");            
        $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
        $("#erroralertmodal").modal({
            show: true
        });
        $('#term_quote_access_public').prop('checked', false);
        return false;
    } else {
    	$('#term_seller_list').val("");
        $("#showhidepost").css("display", "none");            
    }
});



//Relocation Term Add New Item
//Add more items elements store in hidden fileds
var seller_id_list = new Array();
var uniq_check_add_items_list = new Array();
$('#term_add_relocation').click(function() {
	 var num = parseInt($('#next_term_add_relocation_buyer_more_id').val()) + 1;
	 $('#next_term_add_relocation_buyer_more_id').val(num);
	 var from_location_value = $('#term_from_location_id').val();
     var to_location_value = $('#term_to_location_id').val();
     var delivery_date = $('#term_delivery_date').val();
     var dispatch_date = $('#term_dispatch_date').val();
     var from_location = $('#term_from_location').val();
     var to_location = $('#term_to_location').val();
     
     if (from_location != "" && to_location != "" && from_location_value != '' && to_location_value != '') {
         $('#"error-relocation-term-add-item"').text('');   
        //Check post existas are not in line items
         var unique = from_location_value+to_location_value;
         if ($.inArray(unique,uniq_check_add_items_list)==-1) {
        	 alert();
        	 return false;
         	uniq_check_add_items_list.unshift(unique);
         //Add multiple sellers store in variable	
     	var seller_location_id = from_location_value;
         seller_id_list.unshift(seller_location_id);
          
         var html = '<div class="table-row inner-block-bg"><div class="request_row_' + num + '"><div class="col-md-2 padding-left-none" id="term_from_'+num+'">' + from_location + '</div><div class="col-md-2 padding-left-none" id="term_to_'+num+'">' + to_location + '</div><div class="col-md-1 padding-left-none"><a class="term_edit_lineitem edit_this edit" term-data-string="'+unique+'" row_ftlterm_id="' + num + '"><i class="fa fa-edit red" title="Edit"></i></a>&nbsp;<a style ="cursor:pointer;" class="term_remove_lineitem remove_this remove" term-data-string="'+unique+'" row_ftlterm_id="' + num + '"><i class="fa fa-trash red" title="Delete"></i></a></div><input type="hidden" name="from_location[]" class="from_location" id="from_location_'+num+'"  value="' + from_location_value + '"><input type="hidden" name="to_location[]" id="to_location_'+num+'" value="' + to_location_value + '"><input type="hidden" name="delivery_date[]" value="' + delivery_date + '"><input type="hidden" name="dispatch_date[]" value="' + dispatch_date + '"></div>';
         $('.relocation_term_request_rows').append(html);
         $('#term_from_location').val("");
         $('#term_to_location').val("");
         $('#term_from_location_id').val("");
         $('#term_to_location_id').val("");                    
         $("#term_dispatch_date").prop('disabled', true);
         $("#term_delivery_date").prop('disabled', true);                     
         $("#relocation_term_firstform")
             .validate().cancelSubmit = true;
         $('.selectpicker').selectpicker('refresh');
         $("#term_dispatch_date").datepicker({
             changeMonth: true,
             numberOfMonths: 1,
             minDate: 0,        
             dateFormat: "dd/mm/yy",
             onClose: function(selectedDate) {
                 $("#term_delivery_date").datepicker(
                     "option", "minDate", selectedDate);
             }
         });
         $("#term_delivery_date").datepicker({
             changeMonth: true,
             numberOfMonths: 1,        
             minDate: 0,
             dateFormat: "dd/mm/yy",
             onClose: function(selectedDate) {
                 $("#term_dispatch_date").datepicker("option",
                     "maxDate", selectedDate);
             }
         });
         
         } else {
         	$("#erroralertmodal .modal-body").html("Post already exists for the specified parameters! Please modify parameters.");
             $("#erroralertmodal").modal({
                 show: true
             });
         }
         
         return false;
     }
});


//checking add items empty or not
$('#term_relocation_add_buyer_quote,#term_relocation_add_buyer_quote_draft').click(function(e) {    
	var thisid = $(this).attr("id");
	if(thisid == "term_relocation_add_buyer_quote"){
		$("#confirm_but").val("Float RFP");
	}
	//validation add new docs
	$(".dynamic_validations_term").each(function (item) {
			$(this).rules("add", {
				required : true,
				accept: "docx|txt|doc|pdf|xls|csv|xlsx",
			});
			$("#term_relocation_add_buyer_quote").validate().element("#"+$(this).attr("id"));
		});
		 
		var id = $('.relocation_term_request_rows .table-row').children().size();

		if (id == 0) {
            $("#erroralertmodal .modal-body").html("Please add atleast one item to the list.");
            $("#erroralertmodal").modal({
            show: true
            });
            		return false;
        } else {
        $('#term_relocation_add_buyer_quote').submit();
        /*if($('#term_relocation_add_buyer_quote').valid()){
            $("#term_relocation_add_buyer_quote").prop('disabled', true);    
            $("#term_relocation_add_buyer_quote_draft").prop('disabled', true);      
        }*/
        return true;
    }
});





$("#term_relocbuyer_quote").validate({
	ignore: [],
    rules: {        	
    	
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



$(".relocationbooknow").click(function () {
	
	var ratecard  = $(this).attr("rel");
	
	var uniqId  = $(this).attr("id");	
	
	
	if(ratecard==1 || ratecard==0){
	
		if ($("#property_type_"+uniqId).val()=="") {		
			 $("#erroralertmodal .modal-body").html("Please select PropertyType.");
	         $("#erroralertmodal").modal({
	             show: true
	         });
			return false;
		 }	
		
		if ($("#load_type_"+uniqId).val()=="") {		
			 $("#erroralertmodal .modal-body").html("Please select Load Type.");
	         $("#erroralertmodal").modal({
	             show: true
	         });
			return false;
		 }	
		
	
	}
	else{
		
		if ($("#term_numberofveh_"+uniqId).val()=="") {		
			 $("#erroralertmodal .modal-body").html("Please enter number of vehicles.");
	         $("#erroralertmodal").modal({
	             show: true
	         });
			return false;
		 }	
		
		
	}
	
	
}); 


$(".termratetype_selection_buyer").click(function(){
	
	
	var typeselection = $('input[name=term_post_rate_card_type]:checked').val()
	
	if(typeselection == 1){
		$(".term_relocation_hhg_buyer_create").show();
		$(".term_relocation_vehicle_buyer_create").hide();
		$('.vehiclegrid').hide();
		$('.hhggrid').show();
		$("#term_post_rate_card_type").val(1);
	}else if(typeselection == 2){
		$(".term_relocation_vehicle_buyer_create").show();
		$(".term_relocation_hhg_buyer_create").hide();
		$('.vehiclegrid').show();
		$('.hhggrid').hide();
		$("#term_post_rate_card_type").val(2);
	}
	
	
	 
	
	
});

$('.term_veh').keyup(function() {
	
	
	var uniqId  = $(this).attr("id");	
	console.log(uniqId);
	var noveh = $(this).val();
	uniqId=uniqId.split('_');
	
	var contPrice=$("#contractprice_"+uniqId[2]).val();
	
	var TotalPrice=contPrice*noveh;
	
	$("#displaybaseFright_veh_"+uniqId[2]).html(TotalPrice);
	$("#displaytotalamnt_veh_"+uniqId[2]).html(TotalPrice)
	$("#total_hidden_amount_veh_"+uniqId[2]).html(TotalPrice);
	$("#total_hidden_amnt_"+uniqId[2]).val(TotalPrice);
   
});

$(".placeindent").click(function(){
	
	 $.ajax({
         url: '/forgetinventorysession',
         type: "post",
         data: '',
         success: function(data) {
            
         },
         error: function(request, status, error) {
             $('#term_quote_access_private').val(null);
             alert(error);
         },
     });
	
});

}); //Totla Braces ends here.
/* get Capacity from load type
 */
function getTermCapacity() {
    var data = {
        'load_type': $('#term_load_type').val()
    };
    $.ajax({
        type: "GET",
        url: '/getCapacity',
        data: data,
        dataType: 'text',
        success: function(data) {
            $('#term_capacity').val(data);
        },
        error: function(request, status, error) {
            $('#term_capacity').val('');
        },
    });
}
/*check term no of loads
 */
function getTermLoads(vehicletype) {
    var data = {
        'vehicle_type': vehicletype,
        'quantity': $('#term_quantity').val()
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
                $('#term_loads').val(myarr[1]);
            } else {
                $("#erroralertmodal .modal-body").html("Please enter quantity.");
                $("#erroralertmodal").modal({
                    show: true
                });
                $('#term_vehicle_type').val(null);
                $('#term_vehicle_type').selectpicker('refresh');
            }
        },
        error: function(request, status, error) {
            $('#term_vehicle_type').val(null);
            $('#term_vehicle_type').selectpicker('refresh');
            $('#term_loads').val(null);
        },
    });
}

/*Change Post type for lisitng 
post buyer side */

function changePostType(str){	
	//console.log(str);
	if(str=="spot"){
	//window.location="/buyerposts";
	window.location="/buyerposts";
    }else{
    	//alert("hello");
    window.location="/buyertermposts";
    //window.location="/buyertermposts";	
    }	
}

function termChangePostType(str){	
	//console.log(str);
	if(str=="1"){
	//window.location="/buyerposts";
	window.location="/sellerlist";
    }else{
    	//alert("hello");
    window.location="/sellertermposts";
    //window.location="/buyertermposts";	
    }	
}

function checkSellerPostitem(str){	
	//alert("hello");
	var quote_item_id=str.split("_");$(this).is(":checked")==true
	console.log(quote_item_id[2]);
	console.log(quote_item_id[3]);
	console.log($("#"+str).is(":checked"));
	if($("#"+str).is(":checked")==true){
		if($("#contractquote_"+quote_item_id[1])){	
			$("#contractquote_"+quote_item_id[1]).removeAttr("disabled");
		}
		if($("#intialquote_"+quote_item_id[2])){
		$("#intialquote_"+quote_item_id[2]).removeAttr("disabled");	
		}
		if($("#initial_rate_per_kg_"+quote_item_id[2])){
			$("#initial_rate_per_kg_"+quote_item_id[2]).removeAttr("disabled");
			$("#initial_kg_per_cft_"+quote_item_id[2]).removeAttr("disabled");
		}
		if($("#rateper_kg_"+quote_item_id[2])){
			
			$("#rateper_kg_"+quote_item_id[2]).removeAttr("disabled");
			$("#transit_days_"+quote_item_id[2]).removeAttr("disabled");
			$("#crating_charges_"+quote_item_id[3]).removeAttr("disabled");
			$("#storate_charges_"+quote_item_id[3]).removeAttr("disabled");
			$("#escort_charges_"+quote_item_id[3]).removeAttr("disabled");
			$("#handyman_charges_"+quote_item_id[3]).removeAttr("disabled");
			$("#property_search_"+quote_item_id[3]).removeAttr("disabled");
			$("#brokerage_"+quote_item_id[3]).removeAttr("disabled");
		}
		if($("#transport_charges_"+quote_item_id[2])){
			
			$("#transport_charges_"+quote_item_id[2]).removeAttr("disabled");
			$("#od_charges_"+quote_item_id[2]).removeAttr("disabled");
			
		}
		if($("odlcl_charges_"+quote_item_id[2])){
			
			$("#odlcl_charges_"+quote_item_id[2]).removeAttr("disabled");
			$("#odtwentyft_charges_"+quote_item_id[2]).removeAttr("disabled");
			$("#odfortyft_charges_"+quote_item_id[2]).removeAttr("disabled");
			$("#frieghtlcl_charges_"+quote_item_id[2]).removeAttr("disabled");
			$("#frieghttwenty_charges_"+quote_item_id[2]).removeAttr("disabled");
			$("#frieghtforty_charges_"+quote_item_id[2]).removeAttr("disabled");
			$("#transit_days_"+quote_item_id[2]).removeAttr("disabled");
		}
		if($("frieghthundred_charges_"+quote_item_id[2])){
			
			$("#frieghthundred_charges_"+quote_item_id[2]).removeAttr("disabled");
			$("#frieghtthreehundred_charges_"+quote_item_id[2]).removeAttr("disabled");
			$("#frieghtfivehundred_charges_"+quote_item_id[2]).removeAttr("disabled");
			
		}
		
	}else{
		if($("#contractquote_"+quote_item_id[1])){	
			$("#contractquote_"+quote_item_id[1]).attr("disabled","disabled");
		}
		if($("#intialquote_"+quote_item_id[2])){
			$("#intialquote_"+quote_item_id[2]).attr("disabled","disabled");	
		 }
		if($("#initial_rate_per_kg_"+quote_item_id[2])){
			$("#initial_rate_per_kg_"+quote_item_id[2]).attr("disabled","disabled");
			$("#initial_kg_per_cft_"+quote_item_id[2]).attr("disabled","disabled");
		
		}
		if($("#rateper_kg_"+quote_item_id[2])){
			
			$("#rateper_kg_"+quote_item_id[2]).attr("disabled","disabled");
			$("#transit_days_"+quote_item_id[2]).attr("disabled","disabled");
			$("#crating_charges_"+quote_item_id[3]).attr("disabled","disabled");
			$("#storate_charges_"+quote_item_id[3]).attr("disabled","disabled");
			$("#escort_charges_"+quote_item_id[3]).attr("disabled","disabled");
			$("#handyman_charges_"+quote_item_id[3]).attr("disabled","disabled");
			$("#property_search_"+quote_item_id[3]).attr("disabled","disabled");
			$("#brokerage_"+quote_item_id[3]).attr("disabled","disabled");
		}
		if($("#transport_charges_"+quote_item_id[2])){
			
			$("#transport_charges_"+quote_item_id[2]).attr("disabled","disabled");
			$("#od_charges_"+quote_item_id[2]).attr("disabled","disabled");
			
		}
		if($("odlcl_charges_"+quote_item_id[2])){
			
		$("#odlcl_charges_"+quote_item_id[2]).attr("disabled","disabled");
		$("#odtwentyft_charges_"+quote_item_id[2]).attr("disabled","disabled");
		$("#odfortyft_charges_"+quote_item_id[2]).attr("disabled","disabled");
		$("#frieghtlcl_charges_"+quote_item_id[2]).attr("disabled","disabled");
		$("#frieghttwenty_charges_"+quote_item_id[2]).attr("disabled","disabled");
		$("#frieghtforty_charges_"+quote_item_id[2]).attr("disabled","disabled");
		$("#transit_days_"+quote_item_id[2]).attr("disabled","disabled");
		}
		
		if($("frieghthundred_charges_"+quote_item_id[2])){
			
			$("#frieghthundred_charges_"+quote_item_id[2]).attr("disabled","disabled");
			$("#frieghtthreehundred_charges_"+quote_item_id[2]).attr("disabled","disabled");
			$("#frieghtfivehundred_charges_"+quote_item_id[2]).attr("disabled","disabled");
			
		}
	}
}

function checkSetTermBooknow() {
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
                $("#search-dialog").dialog("close");
            }
        }
    }, "json");

}


function updatetermpostlineitem(postid){
	var rowid = "#single_post_item_"+postid;

    $( "#ftl_term_insert input[name='term_from_location']" ).val($( rowid +" .from_location_text" ).html());
	$( "#ftl_term_insert input[name='term_from_location_id']" ).val($( rowid +" input[name='from_location[]']" ).val());
	$( "#ftl_term_insert input[name='term_to_location']" ).val($( rowid +" .to_location_text" ).html());
	$( "#ftl_term_insert input[name='term_to_location_id']" ).val($( rowid +" input[name='to_location[]']" ).val());
	$( "#ftl_term_insert input[name='term_quantity']" ).val($( rowid +" .quantity_type_text" ).html());
	
	$("#term_load_type option[value='"+$( rowid +" input[name='load_type[]']" ).val()+"']").prop('selected', true);
	$("#term_vehicle_type option[value='"+$( rowid +" input[name='vechile_type[]']" ).val()+"']").prop('selected', true);
	$("#current_row_id").val(postid);
	$('.selectpicker').selectpicker('refresh');
	$('.term_buyer_add').hide();
	$('.term_buyer_update').show();
    console.log("showed");
	$("#buyer_item_id").val(postid);
	//$('#term_add_buyer_more').val("Update");
	//$('#term_vehicle_type').trigger("change");
}
function updateptltermpostlineitem(postid){
    var rowid = "#single_post_item_"+postid;
    var currentserviceid = $("#current_service_id").val();
    if(currentserviceid == 9 || currentserviceid == 8){
        $( "#ftl_term_insert input[name='from_airport_term']" ).val($( rowid +" .from_location_text" ).html());
        $( "#ftl_term_insert input[name='from_airport_term_id']" ).val($( rowid +" input[name='from_location[]']" ).val());
        $( "#ftl_term_insert input[name='to_airport_term']" ).val($( rowid +" .to_location_text" ).html());
        $( "#ftl_term_insert input[name='to_airport_term_id']" ).val($( rowid +" input[name='to_location[]']" ).val());


        $("#term_shipment_type option[value='"+$( rowid +" input[name='lkp_air_ocean_shipment_type_id[]']" ).val()+"']").prop('selected', true);
        $("#term_sender_identify option[value='"+$( rowid +" input[name='lkp_air_ocean_sender_identity_id[]']" ).val()+"']").prop('selected', true);
        $( "#ftl_term_insert input[name='term_iecode']" ).val($( rowid +" input[name='ie_code[]']" ).val());
        $( "#ftl_term_insert input[name='term_product_mode']" ).val($( rowid +" input[name='product_made[]']" ).val());
    }else{
        $( "#ftl_term_insert input[name='term_from_location_pincode']" ).val($( rowid +" .from_location_text" ).html());
        $( "#ftl_term_insert input[name='term_from_location_pincode_id']" ).val($( rowid +" input[name='from_location[]']" ).val());
        $( "#ftl_term_insert input[name='term_to_location_pincode']" ).val($( rowid +" .to_location_text" ).html());
        $( "#ftl_term_insert input[name='term_to_location_pincode_id']" ).val($( rowid +" input[name='to_location[]']" ).val());
    }

    $( "#ftl_term_insert input[name='term_noof_packages']" ).val($( rowid +" input[name='number_packages[]']" ).val());
    $( "#ftl_term_insert input[name='term_volume']" ).val($( rowid +" input[name='volume[]']" ).val());
    $( "#ftl_term_insert input[name='term_units']" ).val($( rowid +" input[name='capacity[]']" ).val());

    $("#term_load_type option[value='"+$( rowid +" input[name='load_type[]']" ).val()+"']").prop('selected', true);
    $("#term_package_type option[value='"+$( rowid +" input[name='package_type[]']" ).val()+"']").prop('selected', true);
    $("#current_row_id").val(postid);
    $('.selectpicker').selectpicker('refresh');
    $('.term_buyer_add').hide();
    $('.term_buyer_update').show();
    console.log("showed");
    $("#buyer_item_id").val(postid);
    //$('#term_add_buyer_more').val("Update");
    //$('#term_vehicle_type').trigger("change");
}

function updaterelcoationtermpostlineitem(postid,type){
    var rowid = "#single_post_item_"+postid;
    $( ".vehicle_type_car_term").hide();
    $( "#ftl_term_insert input[name='term_from_location']" ).val($( rowid +" .from_location_text" ).html());
    $( "#ftl_term_insert input[name='term_from_location_id']" ).val($( rowid +" input[name='from_location[]']" ).val());
    $( "#ftl_term_insert input[name='term_to_location']" ).val($( rowid +" .to_location_text" ).html());
    $( "#ftl_term_insert input[name='term_to_location_id']" ).val($( rowid +" input[name='to_location[]']" ).val());
    if(type == "house"){
        $( "#ftl_term_insert input[name='relocation_term_volume']" ).val($( rowid +" input[name='volume[]" ).val());
        $( "#ftl_term_insert input[name='relocation_term_noofshipments']" ).val($( rowid +" input[name='number_packages[]" ).val());
    }else{
        $("#term_vehicle_category option[value='"+$( rowid +" input[name='lkp_vehicle_category_id[]']" ).val()+"']").prop('selected', true);
        $("#term_vehicle_category_type option[value='"+$( rowid +" input[name='lkp_vehicle_category_type_id[]']" ).val()+"']").prop('selected', true);
        $( "#ftl_term_insert input[name='term_vehicle_model']" ).val($( rowid +" input[name='vehicle_model[]" ).val());
        $( "#ftl_term_insert input[name='relocation_term_nooftrips']" ).val($( rowid +" input[name='no_of_vehicles[]" ).val());
        console.log($( rowid +" input[name='lkp_vehicle_category_type_id[]']" ).val());
        if($( rowid +" input[name='lkp_vehicle_category_type_id[]']" ).val() != "" && $( rowid +" input[name='lkp_vehicle_category_type_id[]']" ).val() != 0){
            $( ".vehicle_type_car_term").show();
        }
    }


    $("#current_row_id").val(postid);
    $('.selectpicker').selectpicker('refresh');
    $('.term_buyer_add').hide();
    $('.term_buyer_update').show();
    console.log("showed");
    $("#buyer_item_id").val(postid);
}

function updaterelcoationInttermpostlineitem(postid,type){
    var rowid = "#single_post_item_"+postid;
    $( ".vehicle_type_car_term").hide();
    $( "#ftl_term_insert input[name='term_from_location']" ).val($( rowid +" .from_location_text" ).html());
    $( "#ftl_term_insert input[name='term_from_location_id']" ).val($( rowid +" input[name='from_location[]']" ).val());
    $( "#ftl_term_insert input[name='term_to_location']" ).val($( rowid +" .to_location_text" ).html());
    $( "#ftl_term_insert input[name='term_to_location_id']" ).val($( rowid +" input[name='to_location[]']" ).val());
    $( "#ftl_term_insert input[name='term_number_loads']" ).val($( rowid +" input[name='number_loads[]" ).val());
    $( "#ftl_term_insert input[name='relocation_term_avg_kg_per_move']" ).val($( rowid +" input[name='avg_kg_per_move[]" ).val());

    $("#current_row_id").val(postid);
    $('.selectpicker').selectpicker('refresh');
    $('.term_buyer_add').hide();
    $('.term_buyer_update').show();
    console.log("showed");
    $("#buyer_item_id").val(postid);
}

function updaterelcoationGlobaltermpostlineitem(postid){

    var rowid = "#single_post_item_"+postid;
    $( "#term_relgm_service_type" ).val($( rowid +" input[name='service_ids[]']" ).val());
    $( "#term_measurement").val($( rowid +" input[name='measurements[]']" ).val());
    $( "#term_measurement_unit").val($( rowid +" input[name='measurement_units[]']" ).val());

    $("#current_row_id").val(postid);
    $('.selectpicker').selectpicker('refresh');
    $('.term_buyer_add').hide();
    $('.term_buyer_update').show();
    $("#buyer_item_id").val(postid);
}


function deleteTermFile(file_id){
    var r = confirm("Are you sure, you want you delete?");
    if (r == true) {
        $.ajax({
            type: "POST",
            url: '/termpostitemdelete',
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
            data: "fileid=" + file_id, // serializes the form's elements.
            success: function () {
                $("#file_" + file_id).remove();
                $(".documents-terms").show();


            }
        });
    }
}
function volumeWeightCourierTerm(weightType,serviceId,cid)
{	
	var buyer_term_contract_id = cid.getAttribute("termpack_id");
	
		
	var ptlLength = $("#courier_term_length_"+buyer_term_contract_id).val();
    var ptlWidth  = $("#courier_term_width_"+buyer_term_contract_id).val();
    var ptlHeight = $("#courier_term_height_"+buyer_term_contract_id).val();
    var max_weight=$("#max_weight_"+buyer_term_contract_id).val();

    var data = {
			'ptlweightType' : weightType,
			'ptlLength' :ptlLength,
			'ptlWidth' :ptlWidth,
			'ptlHeight' : ptlHeight
		};
				
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
//					var calval= res[0];
//					
//					if(calval>max_weight){
//						 $("#erroralertmodal .modal-body").html("Max weight is exceeded");
//				         $("#erroralertmodal").modal({
//				             show: true
//				         });
//						$("#courier_term_length_"+buyer_term_contract_id).val("");
//						$("#courier_term_width_"+buyer_term_contract_id).val("");
//						$("#courier_term_height_"+buyer_term_contract_id).val("");
//					}
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
					$('#ptlCheckVolWeight').val(null);
					$('#ptlCheckVolWeight').selectpicker('refresh');				
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
		$('#ptlCheckVolWeight').val(null);
		$('#ptlCheckVolWeight').selectpicker('refresh');
	    }
}
function bidTypeChange(bidType){
	
	var bidTypes='';
	
	if($(".bidcheckopen").is(":checked")==true){
	bidTypes=1;
	}
	
	if($(".bidcheckclose").is(":checked")==true){
	if(bidTypes!=''){
	bidTypes=bidTypes+','+2;
	}else{
	bidTypes=2;	
	}
	}
	//alert(bidTypes);
	
	$("#bid_type_value").val(bidTypes);
	$("#seller_term_posts_buyers_search_filter").submit();	
	
}