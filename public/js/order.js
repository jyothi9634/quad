function addVehicle() {
	if(!$("#truckhaul_confirm").length){
        addVehicleTruck();
	} 
    var truckhaul_confirm = $("#truckhaul_confirm").val();
    if(truckhaul_confirm == '1'){
        $("#truckhaul").modal('show');
    }else  if(truckhaul_confirm == '0' || truckhaul_confirm == ''){
        $.ajax({
            type: "POST",
            url: "/addvehicle",
            data: {'truckhaul_order_no':$('#truckhaul_order_no').val() ,'truckhaul_confirm':$('#truckhaul_confirm').val() ,
                  'truckhaul_valid_from':$('#truckhaul_valid_from').val() ,'truckhaul_valid_to':$('#truckhaul_valid_to').val() ,
                  'truckhaul_from_location_id':$('#truckhaul_from_location_id').val() ,
                  'truckhaul_to_location_id':$('#truckhaul_to_location_id').val() ,
                  'truckhaul_district_id':$('#truckhaul_district_id').val() ,                   
                  'truckhaul_load_type_id':$('#truckhaul_load_type_id').val() ,
                  'truckhaul_vehicle_type_id':$('#truckhaul_vehicle_type_id').val() ,
                  'truckhaul_price':$('#truckhaul_price').val() ,
                  'truckhaul_transit_days':$('#truckhaul_transit_days').val() ,    
                  'truckhaul_vehicle_no':$('#truckhaul_vehicle_no').val() ,'vehicle':  $('#vehicle').val()  ,
                  'driver':  $('#driver').val() ,'mobile':  $('#mobile').val(),'order_id': + $('#order_id').val()},
            success: function (data) {
                if(data!=0){
                    //var str =   '<div class="table table-row"  width="100%"><div class="col-md-12 padding-none"><div class="col-md-4 col-sm-4 col-xs-4 padding-none">'+$('#vehicle').val()+'</div><div class="col-md-4 col-sm-4 col-xs-4 padding-none">'+$('#driver').val()+'</div><div class="col-md-4 col-sm-4 col-xs-4 padding-none">'+$('#mobile').val()+'</div></div></div>';
                    //$("#pick_vehicles").append(str);
                    $('#vehicle').val('');
                    $('#driver').val('');
                    $('#mobile').val('');
                    $('.truckhaul').show();
                    location.reload();
                }else{
                    $('#vehicle').val('');
                    $('#driver').val('');
                    $('#mobile').val('');
                    $("#erroralertmodal .modal-body").html("This vehicle already assigned to this order. Please specify a different vehicle.");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                }
            }
        });
    }
}
function addVehicleTruck() {
    
        $.ajax({
            type: "POST",
            url: "/addvehicle",
            data: {'engine':$('#engine').val() ,'chasis':$('#chasis').val() ,
                  'present_reading':$('#present_reading').val() ,'vehicle_insurance':$('#vehicle_insurance').val() ,
                  'insurance_date':$('#insurance_date').val() ,
                  'vehicle':  $('#vehicle').val(),
                  'driver':  $('#driver').val() ,'mobile':  $('#mobile').val(),'order_id': + $('#order_id').val()},
            success: function (data) {
                if(data!=0){
                    
                    location.reload();
                }else{
                   
                    $("#erroralertmodal .modal-body").html("This vehicle already assigned to this order. Please specify a different vehicle.");
                    $("#erroralertmodal").modal({
                        show: true
                    });
                }
            }
        });
    
}

function addTruckVehicle(){     
            $.ajax({
                type: "POST",
                url: "/addvehicle",
                data: {'add_truck_flag':$('#add_truck_flag').val() ,
                'truckhaul_order_no':$('#truckhaul_order_no').val() ,'truckhaul_confirm':$('#truckhaul_confirm').val() ,
                  'truckhaul_valid_from':$('#truckhaul_valid_from').val() ,'truckhaul_valid_to':$('#truckhaul_valid_to').val() ,
                  'truckhaul_from_location_id':$('#truckhaul_from_location_id').val() ,
                  'truckhaul_to_location_id':$('#truckhaul_to_location_id').val() ,
                  'truckhaul_district_id':$('#truckhaul_district_id').val() ,                   
                  'truckhaul_load_type_id':$('#truckhaul_load_type_id').val() ,
                  'truckhaul_vehicle_type_id':$('#truckhaul_vehicle_type_id').val() ,
                  'truckhaul_price':$('#truckhaul_price').val() ,
                  'truckhaul_transit_days':$('#truckhaul_transit_days').val() ,    
                  'truckhaul_vehicle_no':$('#truckhaul_vehicle_no').val() ,'vehicle':  $('#vehicle').val()  ,
                  'driver':  $('#driver').val() ,'mobile':  $('#mobile').val(),'order_id': + $('#order_id').val()},
              success: function (data) {
                    if(data!=0){
                        //var str =   '<div class="table table-row"  width="100%"><div class="col-md-12 padding-none"><div class="col-md-4 col-sm-4 col-xs-4 padding-none">'+$('#vehicle').val()+'</div><div class="col-md-4 col-sm-4 col-xs-4 padding-none">'+$('#driver').val()+'</div><div class="col-md-4 col-sm-4 col-xs-4 padding-none">'+$('#mobile').val()+'</div></div></div>';
                        //$("#pick_vehicles").append(str);
                        $('#vehicle').val('');
                        $('#driver').val('');
                        $('#mobile').val('');
                        //$('.truckhaul').show();
                        //alert($("#add_truck_flag").val());
                        if($("#add_truck_flag").val() == '1'){
                            location.href="/truckhaul/createsellerpost";
                        }else if($("#add_truck_flag").val() == '0'){
                            location.reload();
                        }
                        
                    }else{
                        $('#vehicle').val('');
                        $('#driver').val('');
                        $('#mobile').val('');
                        $("#erroralertmodal .modal-body").html("This vehicle already assigned to this order. Please specify a different vehicle.");
                        $("#erroralertmodal").modal({
                            show: true
                        });
                    }
                }
            }); 
}
function addLocation() {
    $.ajax({
        type: "POST",
        url: "/addlocation",
        data: {'location':  $('#location').val(),'date':  $('#date').val(),'order_id': + $('#order_id').val()},
        success: function () {
            var str =   '<div class="table table-row"  width="100%"><div class="col-md-12 padding-none"><div class="col-md-4 col-sm-4 col-xs-4 padding-none">'+$('#location').val()+'</div><div class="col-md-4 col-sm-4 col-xs-4 padding-none loc_date">'+$('#date').val()+'</div></div>';
            //$("#track_locations").append(str);
            $('#location').val('');
            $('#date').val('');
            $('#track_confirm').show();
            location.reload();
            //alert($("#track_locations .loc_date:last").text());
            
        }
    });
}
function addGsaTerms() {
    
        $.ajax({
            type: "POST",
            url: "/addgsaterms",
            data: {'order_id': + $('#order_id').val()},
            success: function (data) {
                if(data!=0){
                    location.reload();
                }
            }
        });
    
}
$(document).ready(function () {
    $(document).on('click', '.gsa_accept', function() {//alert('hi');
        $('#consignment-popup').modal({
                                    show: true
                                          }).one('click','#gsa_consign_acceptterms',function (e){
                            addGsaTerms();
                        });
    });
    $("#insurance_date").datepicker({
            changeMonth: true,
            numberOfMonths: 1,
            //minDate:$('#cpick').val(),
            minDate:0,
            dateFormat: "dd/mm/yy",
        });
        
    
    var serviceid = $('#serviceId').val();
    $('#truckhaul').css('display','none');
    $('#payment').css('display','none');
    $('.vehicle').toggle();
    $(".consign_vehicle").click(function(){
        $('.vehicle').slideToggle(500);
    });
    $('.pickup').toggle();
    $(".consign_pickup").click(function(){
        $('.pickup').slideToggle(500);
    });
    $('.track').toggle();
    $(".consign_track").click(function(){
        $('.track').slideToggle(500);
    });
    $('.delivery').toggle();
    $(".consign_delivery").click(function(){
        $('.delivery').slideToggle(500);
    });
    
    $(".truck_haul_ok").click(function(){
        //$('#posts-form-vehicle').submit();
        $("#add_truck_flag").val('1');
        //alert("yes");
        addTruckVehicle();                 
    });
     $(".truck_haul_cancel").click(function(){
        //$('#posts-form-vehicle').submit();
        $("#add_truck_flag").val('0');
        //alert("no");
        addTruckVehicle();                 
    });
    $("#payment_success").click(function(){
        $('#posts-form-receipt').submit();
    });
    
    $(".detailsslide-1").click(function(){
            $(".table-slide-1").slideToggle("500");
    });
    $(".detailsslide-2").click(function(){
            $(".table-slide-2").slideToggle("500");
    });
    $(".detailsslide-3").click(function(){
            $(".table-slide-3").slideToggle("500");
    });
    $(".detailsslide-4").click(function(){
            $(".table-slide-4").slideToggle("500");
    });
    
    $.validator.addMethod(
        "regex",
        function(value, element, regexp) {
            var re = new RegExp(regexp);
            return this.optional(element) || re.test(value);
        },
        "Invalid Input"
    );  

    if(serviceid != 5){

    $("#posts-form-sellerpickup").validate(
    {
        rules: {
            'vehicle': {
                required: true, 
                rangelength: [2, 50]
                //regex: "^[a-zA-Z0-9 ']{4,15}$"
            },
            'driver': {
                required: true, 
                //regex : "^[a-zA-Z ']{2,50}$"
            },
            'mobile': {
                required: true,
                digits: true,
                rangelength: [10, 10]
            },
        },
        messages: {
            'vehicle': {required: "Please Enter Vehicle Number"},
            'driver': {required: "Please Enter Driver Name",
                        accept : "Please enter only alphabets"},
            'mobile': {
                required: "Please Enter Mobile Number",
                digits: "Please Enter Digits Only",
                rangelength: "Please Enter Mobile Number between 10 characters long"
            }
        },
        errorPlacement: function(error, element) {
                 $(element).parent().after(error);
        },        
        errorClass: 'errorMessage',
        submitHandler: function (form) {
        	
        	//alert("jhfj");
            addVehicle();
            //form.submit();
        }
    });
    }
    
    $("#posts-form-pickup").validate(
    {
        rules: {
            'pick_date': {required: true},
            'lr_no': {
                required: true,
                rangelength: [2, 50],
                //regex: "^[a-zA-Z0-9 ']{2,50}$"
                //digits: true,
            },
            'lr_date': {required: true},
            'bill_no': {
                required: true,
                rangelength: [2, 50],
                //regex: "^[a-zA-Z0-9 ']{2,50}$"
                //digits: true,
                },
        },
        messages: {
            'pick_date': {required: "Please Enter Pickup Date"},
            'lr_no': {
                required :
                    function() {
                        var serviceValue = $("#current_service_id").val();


                            if (serviceValue == 17 || serviceValue == 18) {
                                return "Please Enter AWB Number";
                            } else {
                                return "Please Enter LR Number";
                            }

                    },
                //digits: "Please Enter Digits Only",
            },
            'lr_date': {
                required:function() {
                    var serviceValue = $("#current_service_id").val();
                    if (serviceValue == 17 || serviceValue == 18) {
                        return "Please Enter AWB Date";
                    } else {
                        return "Please Enter LR Date";
                    }
                },
             },
            'bill_no': {
                required: "Please Enter Bill Number",
                //digits: "Please Enter Digits Only",
            }
        },
        errorPlacement: function(error, element) {
                 $(element).parent().after(error);
        },        
        errorClass: 'errorMessage',
        submitHandler: function (form) {
            form.submit();
        }
    });
    
    $("#posts-form-tracklocation").validate(
    {
        rules: {
            'location': {
                required: true,
                rangelength: [2, 50],
                    //regex: "^[a-zA-Z ']{2,50}$"
                },
                    'date': {required: true},
        },
        messages: {
            'location': {required: "Please Enter Location"},
            'date': {required: "Please Enter Date"},
        },
        errorPlacement: function(error, element) {
                 $(element).parent().after(error);
        },
        errorClass: 'errorMessage',
        submitHandler: function (form) {
            //addLocation();
            form.submit();
        }
    });
    
   
        $("#posts-form-delivery").validate(
        {
            rules: {
                'delivery_date': {required: true},
                'delivery_driver': {required: true,
                    //regex: "^[a-zA-Z ']{2,50}$"
                    rangelength: [2, 50],
                },
                'delivery_mobile': {
                                    required: true,
                                    digits: true,
                                    rangelength: [10, 10]
                                },
               /* 'freight_amt':{fivebytwovalidationswithzero:true,},  */              

            },
            messages: {
                'delivery_date': {required: "Please Enter Delivery Date"},
                'delivery_driver': {required: "Please Enter Driver Name"},
                'delivery_mobile': {required: "Please Enter Mobile Number",
                                    digits: "Please Enter Digits Only",
                                    rangelength: "Please Enter Mobile Number between 10 characters long"
                                },

            },
            errorPlacement: function(error, element) {
                     $(element).parent().after(error);
            },        
            errorClass: 'errorMessage',
            submitHandler: function (form) {
                form.submit();
            }
        });
    
     if(serviceid == 5){
        $("#posts-form-sellerpickup").validate(
        {
            rules: {
                'vehicle': {
                    required: true, 
                    rangelength: [2, 50]
                    // regex: "^[a-zA-Z0-9 ']{4,15}$"
                },
                'driver': {
                    required: true,
                    rangelength: [2, 50]
                   // regex: "^[a-zA-Z ']{2,50}$"
                },
                'mobile': {
                            required: true,
                            digits: true,
                            rangelength: [10, 10]
                },
                'engine': {
                    required: true,
                    rangelength: [2, 50]
                }, 
                'chasis': {
                    required: true,
                    rangelength: [2, 50]
                }, 
                'present_reading': {
                    required: true,
                    //digits: true
                }, 
                'vehicle_insurance': {
                    required: true,
                    rangelength: [2, 50]
                }, 
                'insurance_date': {required: true}, 
            },
            messages: {
                'vehicle': {required: "Please Enter Delivery Date"},
                'driver': {required: "Please Enter Driver Name"},
                'mobile': {required: "Please Enter Mobile Number",
                            digits: "Please Enter Digits Only",
                            rangelength: "Please Enter Mobile Number between 10 characters long"
                        },
                'engine': {required: "Please Enter Engine Number"},
                'chasis': {required: "Please Enter Chassis Number"},
                'present_reading': {required: "Please Enter Present KM Reading",
                digits: "Please Enter Digits Only"},
                'vehicle_insurance': {required: "Please Enter Insurance Number"},
                'insurance_date': {required: "Please Enter Insurance Valid To Date"},
            },
            errorPlacement: function(error, element) {
                     $(element).parent().after(error);
            },        
            errorClass: 'errorMessage',
            submitHandler: function (form) {
                //form.submit();
                addVehicleTruck();
            }
        });
        
    }
    
    if(serviceid == 4){
        $("#reporting-form-delivery").validate(
        {
            rules: {
                'delivery_date': {required: true},
                'delivery_time': {required: true},
                'delivery_driver': {
                    required: true,
                    rangelength: [2, 50]
                    //regex: "^[a-zA-Z ']{2,50}$"
                },
                'delivery_address': {required: true,
                    rangelength: [2, 50]
                },

            },
            messages: {
                'delivery_date': {required: "Please Enter Reporting Date"},
                'delivery_time': {required: "Please Enter Reporting Time"},
                'delivery_driver': {required: "Please Enter Reporting To"},
                'delivery_address': {required: "Please Enter Reporting Address"},

            },
            errorPlacement: function(error, element) {
                     $(element).parent().after(error);
            },        
            errorClass: 'errorMessage',
            submitHandler: function (form) {
                form.submit();
            }
        });
    }else if(serviceid == 5){
        $("#reporting-form-delivery").validate(
        {
            rules: {
                'delivery_date': {required: true},
                'delivery_time': {required: true},
                'delivery_driver': {
                    required: true,
                    rangelength: [2, 50]
                    //regex: "^[a-zA-Z ']{2,50}$"
                },
                'delivery_address': {required: true},
                'open_reading': {required: true,
                    digits: true
                },
            },
            messages: {
                'delivery_date': {required: "Please Enter Reporting Date"},
                'delivery_time': {required: "Please Enter Reporting Time"},
                'delivery_driver': {required: "Please Enter Reporting To"},
                'delivery_address': {required: "Please Enter Reporting Address"},
                'open_reading': {required: "Please Enter Open KM Reading *",
                digits: "Please Enter Digits Only"},
            },
            errorPlacement: function(error, element) {
                     $(element).parent().after(error);
            },        
            errorClass: 'errorMessage',
            submitHandler: function (form) {
                form.submit();
            }
        });
    }
    

    /* Shriram: Add for Consignment pickup timepicker issue */
    $(document).on("focus click keyup keypress blur change",'#reporting_time_icon', function(e){
        $(".hour.disabled, .minute.disabled").addClass("timeDisable");
        //Checking Bid Closure date
        var lastReportingDate = $('#cdelivery_date').val();
        $("#err_reporting_time").html('');
        if(lastReportingDate == null || lastReportingDate == ''){
            $("#err_reporting_time").html('Select Reporting Date first');
            return false;
        }
        $("#reporting_time_icon").datetimepicker({
            format: 'hh:ii',
            autoclose: true,
            startView: 1,
            maxView: 1,
            minuteStep:2,
            linkField: "delivery_time",
            linkFormat: "hh:ii"
        }).on("show", function(e){
            var lastReportingDate = $('#cdelivery_date').val();
            var currDate = moment().format('DD/MM/YYYY');
            if(lastReportingDate == currDate){
                var lBd = $('#cdelivery_date').val().split('/').reverse();
                var TimeZoned = new Date( lBd[0], lBd[1], lBd[2], moment().format('H'), moment().format('mm'), moment().format('ss'));
                $('#reporting_time_icon').datetimepicker('setStartDate', TimeZoned);
            }else{
                $('#reporting_time_icon').datetimepicker('setStartDate', null);
            }
        }); 
    });
    
    $(document).on('change','#vehicle',function(e){
        if(serviceid == 5){
            var vehicle=$(this).val();
            allData = {'vehicle': vehicle};
            $.ajax({
                    type: "POST",
                    url: "/getvehicledetails",
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
                        
                           $('#engine').val(data.engine_number);
                           $('#chasis').val(data.chasis_number); 
                        
                    }
                }, "json");
            
        }
        
    });


});  
