$(function() {
    
    /* Shriram: Intracity Buyer Post Creation */
    $(document).on("focus click keyup keypress blur change",'#pickup_time_datep', function(e){
        $(".hour.disabled, .minute.disabled").addClass("timeDisable");
        var lastPickupDate = $('#pickup_date').val();
        $("#err_pickup_time_datep").html('');
        if(lastPickupDate == null || lastPickupDate == ''){
            $("#err_pickup_time_datep").html('Select Pickup date first');
            return false;
        }
        $("#pickup_time_datep").datetimepicker({
            format: 'hh:ii',
            autoclose: true,
            startView: 1,
            maxView: 1,
            minuteStep:2,
            linkField: "pickup_time",
            linkFormat: "hh:ii"
        }).on("show", function(e){
            var currDate = moment().format('DD/MM/YYYY');
            $(".hour.disabled, .minute.disabled").addClass("timeDisable");
            if(lastPickupDate == currDate){
                var lBd = $('#pickup_date').val().split('/').reverse();
                var TimeZoned = new Date( lBd[0], (lBd[1]-1), lBd[2], moment().format('H'), moment().format('mm'), moment().format('ss'));
            }else{
            	var lBd = $('#pickup_date').val().split('/').reverse();
            	var TimeZoned = new Date( lBd[0], (lBd[1]-1), lBd[2]);
            }
            $('#pickup_time_datep').datetimepicker('setStartDate', TimeZoned);
            
            
            
        }); 
    });


    /* Shriram: Intracity Buyer Post Search */
    $(document).on("focus click keyup keypress blur change",'#pickup_time_search', function(e){
        $(".hour.disabled, .minute.disabled").addClass("timeDisable");
        var lastPickupDate = $('#dispatch_date1').val();
        $("#err_pickup_time_datep").html('');
        if(lastPickupDate == null || lastPickupDate == ''){
            $("#err_pickup_time_datep").html('Select Pickup date first');
            return false;
        }
        $("#pickup_time_search").datetimepicker({
            format: 'hh:ii',
            autoclose: true,
            startView: 1,
            maxView: 1,
            minuteStep:2,
            linkField: "pickup_time",
            linkFormat: "hh:ii"
        }).on("show", function(e){
            var currDate = moment().format('DD/MM/YYYY');
            $(".hour.disabled, .minute.disabled").addClass("timeDisable");

            
            if(lastPickupDate == currDate){
                var lBd = $('#dispatch_date1').val().split('/').reverse();
                var TimeZoned = new Date( lBd[0], (lBd[1]-1), lBd[2], moment().format('H'), moment().format('mm'), moment().format('ss'));
            }else{
            	var lBd = $('#dispatch_date1').val().split('/').reverse();
            	var TimeZoned = new Date( lBd[0], (lBd[1]-1), lBd[2]);
            }
            $('#pickup_time_search').datetimepicker('setStartDate', TimeZoned);
            
            
            
            
        }); 
    });

    $('.timepicker').datetimepicker({
		format: 'h:ii',
        autoclose: true,
        showMeridian: false,
        startView: 1,
        maxView: 1,
        pickDate: false
    }).on("show", function(){
        $(".table-condensed th").text("");
    });

$("#intraaddbuyerpostcounteroffer .intra_add_buyer_addtocart_details").click(function(e) {
        e.preventDefault();
    var id=$(this).attr('id');
    
    var opt = {
        autoOpen: true,
        modal: true,
        width: 550,
        title: 'Details'
    };
        $("#dialog_"+id).dialog(opt);
    $("#dialog_"+id).dialog("open");     
    $("#dialog_"+id).removeClass("displayNone"); 
        
    });
    $(".dialog").dialog({
   autoOpen: false,
   modal: true,
   buttons : {
        "Confirm" : function() {
            //alert("You have confirmed!");  
            //var rowNo = $("#intraaddbuyerpostcounteroffer .intra_add_buyer_addtocart_details").attr('id');
            var rowNo=$(this).data('bqid');
            var buyerId = $('#buyer_post_buyer_id_' + rowNo).val();
            var vehicleId = $('#buyer_post_vehicle_id_' + rowNo).val();
            var quoteItemId = $('#buyer_quote_item_id_' + rowNo).val();
            var price = $('#buyer_post_price_' + rowNo).val();
            var consignmentPickupDate = $('#pickup_date').val();
            var consignmentPickupTime = $('#pickup_time').val();
            
            checknSetBooknow(buyerId, vehicleId,rowNo,quoteItemId,  price,consignmentPickupDate,consignmentPickupTime);
        },
        "Cancel" : function() {
          $(this).dialog("close");
        }
      }
    });
    function checknSetBooknow(buyerId, vehicleId,rowNo,quoteItemId,  price,consignmentPickupDate,consignmentPickupTime) {
	        allData = { 'buyerId' : buyerId, 'vehicleId' : vehicleId,
	                    'buyerCounterOfferId' : rowNo,'quoteItemId' : quoteItemId,
	                    'price' : price,'consignmentPickupDate': consignmentPickupDate,
                            'consignmentPickupTime':consignmentPickupTime
	                  };
	        $.ajax({
	              type: "POST",
	              url : "/setbuyerbooknow",
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
	              data : allData,
	              success : function(data){
	                alert(data.message);
	                if(data.success){
	                    location.reload();
	                }else{
                            $("#dialog").dialog("close");
                        }
	              }
	        },"json");
    	
    }
    $(document).on('blur', '#weight', function() {
    
        getVehicles();
    });
    $(document).on('change', '#weight_type', function() {
    
        getVehicles();
    });
    function getVehicles() {
    var data = {
        'weight': $('#weight').val(),
        'weight_type': $('#weight_type').val()
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
    
    
});