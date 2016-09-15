$(document).ready(function() {
    
    $(".relocationpet_quote_submit").click(function(){
    	
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
				//datavaluses.enquiry_volume = $('#enquiry_volume_' + rowNo).val();
				datavaluses.paymentoptions = $('#payment_options').val();
				datavaluses.tracking = $('#tracking_' + rowNo).val();

			}else{
				datavaluses.from_location_id = $('#from_loc_' + rowNo).val();
				datavaluses.to_location_id = $('#to_loc_' + rowNo).val();
			}

			datavaluses.formvalues = formvalues;
			datavaluses.buyerquote_id = rowNo;
			datavaluses.seller_post_item_id = seller_post_item_id;
			datavaluses.od_charges = $('#od_charges_' + rowNo).val();
			datavaluses.freight = $('#transport_charges_' + rowNo).val();
			datavaluses.transport_days = $('#transport_days_' + rowNo).val();
						
			datavaluses.pet_type = $('#pet_type_' + rowNo).val();
			datavaluses.cage_type = $('#cage_type_' + rowNo).val();
			datavaluses.buyerid = $('#buyer_id_' + rowNo).val();
			datavaluses.total_price = $('#total_price_' + rowNo).val();
			//console.log(datavaluses);return false;
			if (serviceid == 17) {
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
    
    $(document).on('click', '.edit_this_relocpet', function() {
         
		 var rowid = $(this).attr("row_id");
		 var remove_val = $(this).attr("data-string");
	    	$("#update_reloc_seller_line").val(1);
	     	$("#update_reloc_seller_row_count").val(rowid);
	     	$("#update_reloc_seller_row_unique").val(remove_val);
     	 
		    $("#pettypes").selectpicker('val',$("#pettypes_"+rowid).val());
			$("#cagetypes").selectpicker('val',$("#cagetypes_"+rowid).val());
			//$('#load_types').selectpicker('val',$("#load_types_"+rowid).val());
			$('#freight').val($("#freight_"+rowid).val());
                        $('.cage-weight').text($("#cageweight_"+rowid).val());
			$('#transit_days').val($("#transit_days_"+rowid).val());
			$('#od_charges').val($("#od_charges_"+rowid).val());
			$('#transitdays_units_relocation').selectpicker('val',$("#transitdays_units_"+rowid).val());
			 
		 
     });
    
    $('#add_quote_seller_relocationpet').click(function(e) {
        $('#pet_items_mandatory').val(0);
        e.preventDefault();
        $('#posts-form_relocationpet').valid();
        var id=$('.request_rows').children().size();
        if(id==0){
            $("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        }else{
            if($('#posts-form_relocationpet').valid()) {
                $('#posts-form_relocationpet').submit();
                $("#add_quote_seller_relocationpet").prop('disabled', true);
                $("#add_quote_seller_id_relocationpet").prop('disabled', true);
            }
        }
    });
    $('#add_quote_seller_id_relocationpet').click(function(e) {
        $('#pet_items_mandatory').val(0);
        e.preventDefault();
        $('#posts-form_relocationpet').valid();
		var id=$('.request_rows').children().size();
        if(id==0){
            $("#erroralertmodal .modal-body").html("Please add atleast one line item to the list");
            $("#erroralertmodal").modal({
                show: true
            });
            return false;
        }
		if($("#posts-form_relocationpet").valid()) {
			var submitData=$("#posts-form_relocationpet").serialize();
			var btnName = $('#add_quote_seller_id_relocationpet').attr('name');
			$("#add_quote_seller_id_relocationpet").prop('disabled', true);
			$("#add_quote_seller_relocationpet").prop('disabled', true);
			var btnVal = $('#add_quote_seller_id_relocationpet').val();
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
	$("#posts-form_relocationpet").validate({
		ignore: "input[type='text']:hidden",
		rules : {
			
			"valid_from" : {required : true},
			"valid_to" : {required : true},
			"from_location" : {required : true},
			"from_location_id" : {required : true},
			"to_location" : {required : true},
			"to_location_id" : {required : true},
			"pettype" : {//required : true,
                            required : {
					depends: function(element) {
						if ($('#pet_items').val() == 0 ) {
                                                return true;
                                            }else if ($("#pet_items_mandatory").val() == 1){
							return true;
						}else{
							return false;
						}
					}
				},
                        },
			"cagetype" : {//required : true
                            required : {
					depends: function(element) {
						if ($('#pet_items').val() == 0 ) {
                                                return true;
                                            }else if ($("#pet_items_mandatory").val() == 1){
							return true;
						}else{
							return false;
						}
					}
				},
                            },
			
			"od_charges" : {
				required : {
					depends: function(element) {
						if ($('#pet_items').val() == 0 ) {
                                                return true;
                                            }else if ($("#pet_items_mandatory").val() == 1){
							return true;
						}else{
							return false;
						}
					}
				},
                            fourbytwovalidations: {
                                depends: function(element) {
                                            if ($('#pet_items').val() == 0 ) {
                                                return true;
                                            }else if ($("#pet_items_mandatory").val() == 1){
							return true;
						}else{
							return false;
						}
                                        },
                            },
                            
			},
			"transit_days" : {
				required : {
					depends: function(element) {
						if ($('#pet_items').val() == 0 ) {
                                                return true;
                                            }else if ($("#pet_items_mandatory").val() == 1){
							return true;
						}else{
							return false;
						}
					}
				},
				digits: true,
				transitvalidation: {
                                    depends: function(element) {
                                            if ($('#pet_items').val() == 0 ) {
                                                return true;
                                            }else if ($("#pet_items_mandatory").val() == 1){
							return true;
						}else{
							return false;
						}
                                        },
                                },
			},
			"freight" : {
				required : {
					depends: function(element) {
						if ($('#pet_items').val() == 0 ) {
                                                return true;
                                            }else if ($("#pet_items_mandatory").val() == 1){
							return true;
						}else{
							return false;
						}
					}
				},
                                //sixdigitsvalidation: true,
                                fourbytwovalidations: {
					depends: function(element) {
                                            if ($('#pet_items').val() == 0 ) {
                                                return true;
                                            }else if ($("#pet_items_mandatory").val() == 1){
							return true;
						}else{
							return false;
						}
                                        },
			},
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
//add more relocation item
var sel_list_house = new Array();
$('#add_more_relocationpet').on('click', function() {

var num = parseInt($('#next_add_more_id_reloc').val()) + 1;
    $('#next_add_more_id_reloc').val(num);
    //alert(num);
    var from_location = $(this).closest("form").validate().element($('#from_location_id'));
    var to_location = $(this).closest("form").validate().element($('#to_location_id'));
    var datepicker = $(this).closest("form").validate().element($('#datepicker'));
    var datepicker_to_location = $(this).closest("form").validate().element($('#datepicker_to_location'));
    var from = $(this).closest("form").validate().element($('#from_location'));
    var to = $(this).closest("form").validate().element($('#to_location'));
    var pettypes = $(this).closest("form").validate().element($('#pettypes'));
    var cagetypes = $(this).closest("form").validate().element($('#cagetypes'));
    var od_charges = $(this).closest("form").validate().element($('#od_charges'));
    var freight = $(this).closest("form").validate().element($('#freight'));
    var transit_days = $(this).closest("form").validate().element($('#transit_days'));
    if(from_location == true && to_location == true && datepicker == true && datepicker_to_location == true && pettypes==true && 
            cagetypes==true && od_charges==true && freight==true && transit_days==true) {
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
                    var line_itemes = $('#relocationpet_row_items').children().size();
                    
                var validator = $( "#posts-form_relocationpet" ).validate();

                    var pettypes = validator.element($('#pettypes'));
                    var cagetypes = $(this).closest("form").validate().element($('#cagetypes'));
                    var od_charges = $(this).closest("form").validate().element($('#od_charges'));
                    var freight = $(this).closest("form").validate().element($('#freight'));
                    var transit_days = validator.element($('#transit_days'));
                    //var transport_charges = $(this).closest("form").validate().element($('#transport_charges'));
                   
                    var unique =$("#od_charges").val() + $("#freight").val() +$("#pettypes option:selected").val() + $("#cagetypes option:selected").val() + $("#transit_days").val() + $("#transitdays_units_relocation option:selected").val();

                    if ($.inArray(unique, sel_list_house) == -1) {
                            
                                    $.ajax({
                                            type: 'post',
                                            url: '/lineitemscheckrelocation',
                                            data: {
                                                    'pettypes': $("#pettypes option:selected").val(),
                                                    'cagetypes': $("#cagetypes option:selected").val(),
                                                    'transit_days': $("#transit_days").val(),
                                                    'transit_days_units': $("#transitdays_units_relocation option:selected").val(),
                                                    'from_date_seller': datepicker_from_value,
                                                    'to_date_seller': datepicker_to_value,
                                                    'from_location_id': $("#from_location_id").val(),
                                                    'to_location_id': $("#to_location_id").val(),
                                            },
                                            dataType: "html",
                                            type: 'POST',
                                            success: function (data) {


                                                if (data == '0') {
                                                    //sel_list_house.unshift(unique);
                                                    if($("#update_reloc_seller_line").val()==1)
                                                    {
                                                        var remove_unique=$("#update_reloc_seller_row_unique").val();
                                                        sel_list_house.splice($.inArray(remove_unique, sel_list_house),1);
                                                        $('.request_row_' + $("#update_reloc_seller_row_count").val()).remove();
                                                        $("#update_reloc_seller_line").val(0);
                                                    }


                                                    var html = '<div class="table-row inner-block-bg request_row_'+ num +'" data-string="' + num + '">' +
                                                        '<div class="col-md-2 padding-left-none">' + $("#from_location").val() + '</div>' +
                                                        '<div class="col-md-1 padding-left-none">' + $("#to_location").val()+ '</div>' +
                                                        '<div class="col-md-1 padding-left-none">' + $("#pettypes option:selected").text() + '</div>' +
                                                        '<div class="col-md-1 padding-left-none">' + $("#cagetypes option:selected").text() + '</div>' +
                                                        '<div class="col-md-2 padding-left-none">' + $("#od_charges").val() + '/-</div>' +
                                                        '<div class="col-md-1 padding-left-none">' + $("#freight").val()+ ' /-</div>' +
                                                        
                                                        '<div class="col-md-2 padding-left-none">' + $("#transit_days").val()  + ' ' + $("#transitdays_units_relocation option:selected").text() + '</div>' +
                                                        '<div class="col-md-1 padding-none text-center"><a href="javascript:void(0)" style ="cursor:pointer; margin-right:10px;" class="edit_this_relocpet edit" data-string="'+unique+'" row_id="'
							                            + num + '"><i class="fa fa-edit red" title="Edit"></i></a><a style ="cursor:pointer;" class="remove_this_line_reloc remove" data-string="'+unique+'" row_id="'
							                            + num + '"><i class="fa fa-trash red" title="Delete"></i></a></div>' +
                                                        '<input type="hidden" value="' + $("#from_location_id").val() + '" name="from_location_id_hidden[]" id="from_location_id_'+num+'">' +
                                                        '<input type="hidden" value="' + $("#to_location_id").val() + '" name="to_location_id_hidden[]" id="to_location_id_'+num+'">' +
                                                        '<input type="hidden" value="' + $("#pettypes option:selected").val() + '" name="pettypes_hidden[]" id="pettypes_'+num+'">' +
                                                        '<input type="hidden" value="' + $("#cagetypes option:selected").val() + '" name="cagetypes_hidden[]" id="cagetypes_'+num+'">' +
                                                        '<input type="hidden" value="' + $("#od_charges").val() + '" name="od_charges_hidden[]" id="od_charges_'+num+'">' +
                                                        '<input type="hidden" value="' + $("#freight").val() + '" name="freight_hidden[]" id="freight_'+num+'">' +
                                                        '<input type="hidden" value="' + $(".cage-weight").text() + '" name="cageweight_hidden[]" id="cageweight_'+num+'">' +
                                                        '<input type="hidden" value="' + $("#transit_days").val() + '" name="transit_days_hidden[]" id="transit_days_'+num+'">' +
                                                        '<input type="hidden" value="' + $("#transitdays_units_relocation option:selected").val() + '" name="transitdays_units_relocation_hidden[]" id="transitdays_units_'+num+'">' +

                                                        '</div>';

                                                $("#valid_from_val").val(datepicker_from_value);
                                                $("#valid_to_val").val(datepicker_to_value);
                                                $("#pettypes").val("");
                                                $("#cagetypes").val("");
                                                $('#od_charges').val("");
                                                $('.cage-weight').text("50 KGs");
                                                $('#freight').val("");
                                                $('#transit_days').val("");
                                                $('#transitdays_units_relocation').val("");
                                                $('.selectpicker').selectpicker('refresh');
                                                $("#relocationpet_row_items").append(html);
                                                disablerelcationpetcreatepost();
                                                var line_itemes = $('#relocationpet_row_items').children().size();
                                                $("#pet_items").val(line_itemes);
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
            } else {
                    $("#erroralertmodal .modal-body").html("Your post valid " + end_date_subscription + " is beyond your subscription date, please select valid " + end_date_subscription + " within your subscription validity date");
                    $("#erroralertmodal").modal({
                            show: true
                    });
            }
    }

});


	/* Load Breed types based on pet type */
	$("#selPettype").change(function(){
	    var data = {'pettypeid': $(this).val()}
	    $("#selBreedtype").empty().append('<option value="0">Breed</option>').selectpicker('refresh');
	    $.ajax({
	        type: "POST",
	        url: $(this).attr("data-purl"),
	        data: data,
	        dataType: 'json',
	        success: function(resData) {
	            if (resData.success == true) {
	            	var htmlText = '';
	            	$.each(resData.optHtml, function(k, v) {
	            		htmlText += '<option value="'+k+'">'+v+'</option>';
	            	});
	            	$("#selBreedtype").append(htmlText);
	                $('#selBreedtype').selectpicker('refresh');
	            }
	        },
	        error: function(request, status, error) {},
	    });
	});
    
    $('#update_more_relocationpet_property').on('click', function() {
		var line_itemes = $('#household_row_items').children().size();
		//$("#posts-form_relocation").valid();
		var pettypes = $(this).closest("form").validate().element($('#pettypes'));
		var cagetypes = $(this).closest("form").validate().element($('#cagetypes'));
		//var load_types = $(this).closest("form").validate().element($('#load_types'));
		var rate_per_cft = $(this).closest("form").validate().element($('#freight'));
		var transit_days = $(this).closest("form").validate().element($('#transit_days'));
		var transport_charges = $(this).closest("form").validate().element($('#od_charges'));
		if(pettypes == true && cagetypes == true &&  rate_per_cft == true && transit_days == true && transport_charges == true){
			var currentrowid = $("#current_household_row_id").val();
			var rowid = "#single_property_post_item_"+currentrowid;

			//$(rowid+" div:first-child").html($( "#pettypes option:selected" ).text());
			//$(rowid+" div:nth-child(3)").html($( "#load_types option:selected" ).text());
			$(rowid+" div:nth-child(3)").html($( "#pettypes option:selected" ).text());
                        $(rowid+" div:nth-child(4)").html($( "#cagetypes option:selected" ).text());
			$(rowid+" div:nth-child(5)").html($( "#od_charges" ).val()+" /-");
			$(rowid+" div:nth-child(6)").html($( "#freight" ).val()+" /-");
			$(rowid+" div:nth-child(7)").html($( "#transit_days" ).val()+" "+$( "#transitdays_units_relocation option:selected" ).text());

			$(rowid+" input[name='pettypes_hidden[]']").val($( "#pettypes option:selected" ).val());
			$(rowid+" input[name='cagetypes_hidden[]']").val($( "#cagetypes" ).val());
			$(rowid+" input[name='freight_hidden[]']").val($( "#freight" ).val());
			$(rowid+" input[name='transit_days_hidden[]']").val($( "#transit_days" ).val());
			$(rowid+" input[name='transitdays_units_relocation_hidden[]']").val($( "#transitdays_units_relocation option:selected" ).val());
			//$(rowid+" input[name='load_types_hidden[]']").val($( "#load_types option:selected" ).val());
			$(rowid+" input[name='od_charges_hidden[]']").val($( "#od_charges" ).val());

			$('#update_more_relocationpet_property').hide();
			var line_itemes = $('#household_row_items').children(".table-row").size();
			$("#household_items").val(line_itemes);
			$("#pettypes").val("");
			$("#cagetypes").val("");
			$('#freight').val("");
			$('#transit_days').val("");
			$('#od_charges').val("");
			$('#transitdays_units_relocation').val("");
			$('.selectpicker').selectpicker('refresh');
			$('#update_more_relocationpet_property').hide();

		}
	});
        
   /** Relocation Pet create form and search form validations
    * Srinivas and date: 12th May,2016.
    */ 
    $("#posts-form_buyer_relocationpet").validate({	
        ignore: [],
	rules : {
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
                   "valid_from": {
                        required: true,
                   },
//                   "valid_to": {
//                       required: true,
//                   },
                   "selPettype": {
                       required: true,
                   },   
                   "selCageType": {
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
                                    if ($(".create-posttype-service-petmove:checked").val() == 2){           			
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
               messages : {
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
                    "valid_from": {
                        required: "Enter Dispatch Date",
                    },
//                    "valid_to": {
//                        required: "Enter Delivery Date",
//                    },
                    "selPettype": {
                        required: "Select Pet Type",
                    },
                    "selCageType": {
                        required: "Select Cage Type",
                    },
                    'agree':{
                    	required: "Terms & Conditions is required"
                    }
                   
               },
            submitHandler: function(form) { // for demo
            form.submit();
            }
            
        });
        
        
        $("#posts_form_sellersearch_relocationpet").validate({
	ignore: "input[type='text']:hidden",
	rules : {
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
                   "pet_type": {
                       required: true,
                   },                  
               },
               errorPlacement: function(error, element) {
                    $(element).parent('div').after(error);
               },
               messages : {
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
                    
                    "valid_from": {
                        required: "Enter Dispatch Date",
                    },                    
                    "pet_type": {
                        required: "Select Pet Type",
                    },
                    
                   
               },
            submitHandler: function(form) { // for demo
            form.submit();
            }
            
        });

});

function disablerelcationpetcreatepost(){
	$( "#from_location" ).prop('readonly', true);
	$( "#to_location" ).prop('readonly', true);
	$( "#datepicker" ).prop('disabled', true);
	$( "#datepicker_to_location" ).prop('disabled', true);
}

function updaterelocationpetpropertypostlineitem(postid){

	var rowid = "#single_property_post_item_"+postid;
	$( ".relocation_house_hold_create input[name='volume']" ).val($( rowid +" .volume" ).val());
	$("#pettypes option[value='"+$( rowid +" input[name='pettypes_hidden[]']" ).val()+"']").prop('selected', true);
	$("#cagetypes option[value='"+$( rowid +" input[name='cagetypes_hidden[]']" ).val()+"']").prop('selected', true);
	$("#transitdays_units_relocation option[value='"+$( rowid +" input[name='transitdays_units_relocation_hidden[]']" ).val()+"']").prop('selected', true);
	$( ".relocation_house_hold_create input[name='freight']" ).val($( rowid +" .freight" ).val());
	$( ".relocation_house_hold_create input[name='od_charges']" ).val($( rowid +" .od_charges" ).val());
	$( ".relocation_house_hold_create input[name='transit_days']" ).val($( rowid +" .transit_days" ).val());
	$("#current_household_row_id").val(postid);
	$('.selectpicker').selectpicker('refresh');
	$('#update_more_relocationpet_property').show();

}

function getCageWeight(){
    var data = {
	        'cage_id': $('#cagetypes').val()
	    };
	 $.ajax({
	        type: "GET",
	        url: '/getcageweight',
	        data: data,
	        dataType: 'text',
	        success: function(data) {	   	        	
	             $(".cage-weight").text(data+' KGs');	           
	        },
	        error: function(request, status, error) {	            
	        },
	    });	 
}