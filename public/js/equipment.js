
function getCity(val) {
    $.ajax({
        type: "POST",
        url: "/getcity",
        data: 'district_id=' + $('#district_id').val(),
        success: function (data) {
            $("#city_id").html(data);
            $('.selectpicker').selectpicker('refresh');
        }
    });
}
function getLocality(val) {
    $.ajax({
        type: "POST",
        url: "/getlocality",
        data: 'city_id=' + $('#city_id').val(),
        success: function (data) {
            $("#locality_id").html(data);
            $('.selectpicker').selectpicker('refresh');
        }
    });
}
function getDistrict(val) {
    $.ajax({
        type: "POST",
        url: "/getdistrict",
        data: 'state_id=' + $('#state_id').val(),
        success: function (data) {
            $("#district_id").html(data);
            $('.selectpicker').selectpicker('refresh');
        }
    });
}

function uniquenessCheckChasis() {
	var chasis_no = $("#chasis_number").val();
	datastr = '&chasisno=' + chasis_no ;
	// ajax function starts
	$.ajax({
		type : 'post', // defining the ajax type
		url : '/checkuniquechasis', // calling the
		dataType : 'html', // datatype
		data : datastr, // passing the data used for
		// operation
		success : function(html) {
			if(html != "200"){
				$("#error_chasis_number").html(html);
			}else{
				$("#error_chasis_number").html('');
			}
		}
	});
}
function uniquenessCheckEngine() {
	
	var engine_no = $("#engine_number").val();
	datastr =  '&engineno=' + engine_no;
	// ajax function starts
	$.ajax({
		type : 'post', // defining the ajax type
		url : '/checkuniqueengine', // calling the
		dataType : 'html', // datatype
		data : datastr, // passing the data used for
		// operation
		success : function(html) {
			if(html != "200"){
				$("#error_engine_number").html(html);
			}else{
				$("#error_engine_number").html('');
			}
		}
	});
}

$(document).ready(function () {
    $("#chasis_number").blur(function() {
		var currVal = $(this).val();
		if(currVal){
			uniquenessCheckChasis();
		}	
	});
        $("#engine_number").blur(function() {
		var currVal = $(this).val();
		if(currVal){
			uniquenessCheckEngine();
		}	
	});


    $("#equipment_register_form_edit").validate(
            {
                ignore: [],
                rules: {
                    'equipment_type_id': {required: true},
                    'state_id': {required: true},
                    'city_id': {required: true},
                    'district_id': {required: true},
                    'equipment_specs': {required: true},
                    'is_driver': {required: true},
                    'cdbaccept': {required: true},
                    'pincode': {
                        required: true,
                        digits: true,
                        rangelength: [5, 6]
                    }
                },
                errorPlacement: function(error, element) {
                    $(element).parent('div').append(error);
                },
                messages: {
                    'equipment_type_id': {required: "Please select equipment type"},
                    'state_id': {required: "Please select State"},
                    'city_id': {required: "Please select City"},
                    'district_id': {required: "Please select Locality"},
                    'equipment_specs': {required: "Please Enter Equipment Specifications"},
                    'is_driver': {required: "Please select Driver"},
                    'cdbaccept': {required: "Please Check Terms & Conditions"},
                    'pincode': {
                        required: "Please Enter Pincode",
                        rangelength: "Please Enter Pincode between 4-6 characters long"
                    }
                },
                errorClass: 'errorMessage',
                submitHandler: function (form) {
                    form.submit();
                }
            });

    $("#equipment_register_form").validate(
            {
                ignore: [],
                rules: {
                    'equipment_type_id': {required: true},
                    'state_id': {required: true},
                    'city_id': {required: true},
                    'district_id': {required: true},
                    'equipment_specs': {required: true},
                    'equipment_image': {
                        required: true,
                        accept: "jpg|jpeg|gif|png",
                        },
                    'is_driver': {required: true},
                    'cdbaccept': {required: true},
                    'pincode': {
                        required: true,
                        digits: true,
                        rangelength: [5, 6]
                    }

                },
                errorPlacement: function(error, element) {
                    $(element).parent('div').append(error);
                },
                messages: {
                    'equipment_type_id': {required: "Please select equipment type"},
                    'state_id': {required: "Please select State"},
                    'city_id': {required: "Please select City"},
                    'district_id': {required: "Please select Locality"},
                    'equipment_specs': {required: "Please Enter Equipment Specifications"},
                    'equipment_image': {
                                        required: "Please Upload EquipmentImage",
                                        accept:"select valid input file format Ex jpg,jpeg,gif,png."
                                        },
                    'is_driver': {required: "Please select Driver"},
                    'cdbaccept': {required: "Please Check Terms & Conditions"},
                    'pincode': {
                        required: "Please Enter Pincode",
                        rangelength: "Please Enter Pincode between 4-6 characters long"
                    }

                },
                errorClass: 'errorMessage',
                submitHandler: function (form) {
                    form.submit();
                }
            });
    $("#warehouse-master-form").validate(
            {
                ignore: [],
                rules: {
                    'wh_type': "required",
                    'city_id': "required",
                    'district_id': "required",
                    'state_id': "required",
                    'cdbaccept': "required",
                    'from_dt': {
                        required: true,
                        //date: true
                    },
                    'to_dt': {
                        required: true,
                        //date: true
                    },
                    'space_min_ft': {
                        required: true,
                        digits: true
                    },
                    'space_max_ft': {
                        required: true,
                        digits: true
                    },
                    'capacity': "required",
                    'wh_owner_fist_name': {
                        required: true,
                        //lettersonly: true,
                        minlength: 3,
                    },
                    'wh_owner_last_name': {
                        required: true,
                        //lettersonly: true,
                        minlength: 3,
                    },
                    'wh_address': "required",
                    'wh_short_name': "required",
                    'pincode': {
                        required: true,
                        digits: true,
                        rangelength: [5, 6]
                    },
                    'mobile_number': {
                        required: true,
                        digits: true,
                        minlength:10,
                        maxlength: 10,
                        //length: 10
                    },
                    'email': {
                        required: true,
                        email: true,
                    }

                },
                errorPlacement: function(error, element) {
                    $(element).parent('div').append(error);
                },
                messages: {
                    'wh_type': "Please select Warehouse type",
                    'city_id': "Please select Warehouse Location",
                    'district_id': "Please select Warehouse District",
                    'state_id': "Please select Warehouse State",
                    'from_dt': "Please select From Date",
                    'to_dt': "Please select To Date",
                    'cdbaccept': "Please Check Terms & Conditions",
                    'space_min_ft': {
                        required: "Please enter Min Feet",
                    },
                    'space_max_ft': {
                        required: "Please enter Max Feet",
                    },
                    'capacity': "Please enter capacity",
                    'wh_owner_fist_name': "Please select Owner First Name",
                    'wh_owner_last_name': "Please select Owner Last Name",
                    'wh_address': "Please enter Address",
                    'wh_short_name': "Please enter Short Name",
                    'pincode': {
                        required: "Please enter Pincode",
                        rangelength: "Please enter pincode between 4-6 characters long"
                    },
                    'mobile_number': {
                        required: "Please enter Mobile Number",
                        length: "Please enter Mobile Number 10 characters long"
                    },
                    'email': {
                        required: "Please enter Email",
                        email: "Please enter Valid Email",
                    }

                },
                errorClass: 'errorMessage',
                submitHandler: function (form) {
                    form.submit();
                }
            });
    $("#vehicle-master-form").validate(
            {
                ignore: [],
                rules: {
                    'vehicle_owned': {
                        digits: true
                    },
                    'cdbaccept': "required",
                    'vehicle_attatched': {
                        digits: true
                    },
                    'vehicle_gps': {
                        digits: true
                    },
                    'vehicle_number': {
                        required: true,
                        //digits: true
                    },
                    'vehicle_type': {required: true},
                    'vehicle_width': {
                        required: true,
                        number: true
                    },
                    'vehicle_height': {
                        required: true,
                        number: true
                    },
                    'vehicle_length': {
                        required: true,
                        number: true
                    },
                    'vehicle_capacity': {required: true},
                    'reg_owner_fname': {
                        required: true,
                        //lettersonly: true,
                        minlength: 3,
                    },
                    'reg_owner_lname': {
                        required: true,
                        //lettersonly: true,
                        minlength: 3,
                    },
                    'mfg_year': {
                        required: true,
                        digits: true
                    },
                    'chasis_number': {
                        required: true,
                       // digits: true
                    },
                    'engine_number': {
                        //digits: true
                    },
                    'insurance_validity': {
                        //date: true
                    },
                    'fc_validity': {
                        //date: true
                    },
                    'insurance_file_name': {
                       accept: "jpg|jpeg|gif|png|doc|pdf",
                    },
                    'permit_copy_file_name': {
                        accept: "jpg|jpeg|gif|png|doc|pdf",
                    },
                    'fc_file_name': {
                        accept: "jpg|jpeg|gif|png|doc|pdf",
                    },
                    'rc_file_name': {
                        accept: "jpg|jpeg|gif|png|doc|pdf",
                    },
                    'device_number': {
                        required: {
                            depends: function() {
                                return $('input[name=is_gps]:checked').val() == '1';
                            }
                        }                          
                    },
                    'sim_imsi_number': {
                        required: {
                            depends: function() {
                                return $('input[name=is_gps]:checked').val() == '1';
                            }
                        }                          
                    },
                    'mobile_operator': {
                        required: {
                            depends: function() {
                                return $('input[name=is_gps]:checked').val() == '1';
                            }
                        }                          
                    },
                    'mobile_number': {
                        required: {
                            depends: function() {
                                return $('input[name=is_gps]:checked').val() == '1';
                            }
                        }, 
                        digits: true                         
                    },
                    'device_fixed_date': {
                        required: {
                            depends: function() {
                                return $('input[name=is_gps]:checked').val() == '1';
                            }
                        }                          
                    },
                },
                errorPlacement: function(error, element) {
                    $(element).closest('div').append(error);
                },
                messages: {
                    'vehicle_number': {
                        required: "Please select Vehicle number",
                    },
                    'vehicle_type': {required: "Please select Vehicle type"},
                    'vehicle_width': {
                        required: "Please enter Vehicle width",
                    },
                    'vehicle_height': {
                        required: "Please enter Vehicle height",
                    },
                    'vehicle_length': {
                        required: "Please enter Vehicle length",
                    },
                    'vehicle_capacity': {required: "Please Select Vehicle Capacity"},
                    'reg_owner_fname': {required: "Please enter First Name"},
                    'reg_owner_lname': {required: "Please enter Last Name"},
                    'cdbaccept': "Please Check Terms & Conditions",
                    'mfg_year': {
                        required: "Please enter Mfg Year",
                    },
                    'chasis_number': {
                        required: "Please enter Chasis Number",
                    },
                    'insurance_file_name': {
                       accept: "select valid input file format Ex jpg,jpeg,gif,png,doc,pdf.",
                    },
                    'permit_copy_file_name': {
                        accept: "select valid input file format Ex jpg,jpeg,gif,png,doc,pdf.",
                    },
                    'fc_file_name': {
                        accept: "select valid input file format Ex jpg,jpeg,gif,png,doc,pdf.",
                    },
                    'rc_file_name': {
                        accept: "select valid input file format Ex jpg,jpeg,gif,png,doc,pdf.",
                    },
                    'device_number': {
                        required: "Please enter Device Number",
                    },
                    'sim_imsi_number': {
                        required: "Please enter Sim IMSI Number",
                    },
                    'mobile_operator': {
                        required: "Please enter Mobile Operator",
                    },
                    'mobile_number': {
                        required: "Please enter Mobile Number",
                    },
                    'device_fixed_date': {
                        required: "Please select date of device fixed in vehicle ",
                    },
                },
                errorClass: 'errorMessage',
                submitHandler: function (form) {
                    form.submit();
                }
            });
            
    /*$( "#datepicker_from" ).datepicker({
     //defaultDate: "+1w",
     dateFormat: "yy-mm-dd",
     changeMonth: true,
     numberOfMonths: 1,
     minDate: 0,
     onClose: function( selectedDate ) {
     $( "#datepicker_to" ).datepicker( "option", "minDate", selectedDate );
     }
     });*/
    $("#datepicker_from").datepicker({
        dateFormat: "dd/mm/yy",
        minDate: 0,
        onSelect: function (date) {
            var date2 = $('#datepicker_from').datepicker('getDate');
            date2.setDate(date2.getDate() + 1);
            $('#datepicker_to').datepicker('setDate', date2);
            //sets minDate to dt1 date + 1
            $('#datepicker_to').datepicker('option', 'minDate', date2);
        }
    });
    $('#datepicker_to').datepicker({
        dateFormat: "dd/mm/yy",
        onClose: function () {
            var dt1 = $('#datepicker_from').datepicker('getDate');
            var dt2 = $('#datepicker_to').datepicker('getDate');
            //check to prevent a user from entering a date below date of dt1
            if (dt2 <= dt1) {
                var minDate = $('#datepicker_to').datepicker('option', 'minDate');
                $('#datepicker_to').datepicker('setDate', minDate);
            }
        }
    });
    $("#insurance_validity").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        changeYear:true,
        numberOfMonths: 1,
        minDate: 0,

    });
    $("#fc_validity").datepicker({
        dateFormat: "dd/mm/yy",
        changeMonth: true,
        numberOfMonths: 1,
        //minDate: 0,

    });
    $(document).on('change', '.insurance_file_name :file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
    $('#insurance_file_name').html(label);
        //console.log(label);
});
$(document).on('change', '.permit_copy_file_name :file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
    $('#permit_copy_file_name').html(label);
        //console.log(label);
});
$(document).on('change', '.fc_file_name :file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
    $('#fc_file_name').html(label);
        //console.log(label);
});
$(document).on('change', '.rc_file_name :file', function() {
    var input = $(this),
        numFiles = input.get(0).files ? input.get(0).files.length : 1,
        label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
    input.trigger('fileselect', [numFiles, label]);
    $('#rc_file_name').html(label);
        //console.log(label);
});

    
    /*$('.btn-file .file1').on('fileselect', function (event, numFiles, label) {
alert('hi');
        var input = $(this).parents('.input-group').find(':text'),
                log = numFiles > 1 ? numFiles + ' files selected' : label;
alert(log);
        if (input.length) {
            input.val(log);
        } else {
            if (log) {
                if ($(this).closest('div').find('#train_name').length > 0) {
                    $(this).closest('div').find('#train_name').html(log);
                }
            }
        }

    });*/
    
    // GPS Form Fields Toggle
    if($('input[name="is_gps"]').length){
        $(document).on('click','input[name="is_gps"]',function(){
            if($(this).val()==1){
                $('input[name="device_number"]').val('');
                $('input[name="sim_imsi_number"]').val('');
                $('input[name="mobile_operator"]').val('');
                $('input[name="mobile_number"]').val('');
                $('input[name="device_fixed_date"]').val('');
                $('#GPSFields').removeClass('displayNone');
            }else{
                $('#GPSFields').addClass('displayNone');
            }  

        });
        $('input[name="device_fixed_date"]').datepicker({
            dateFormat: "dd/mm/yy",
            changeMonth: true,
            numberOfMonths: 1,
            //minDate: 0,
            maxDate:0,
        });
    }    
});