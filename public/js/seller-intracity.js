$(document).ready(function() {

	$('#add-seller-post').click(function() {

		// Setup form validation on the corporate buyer form
		$("#seller-intracity-create-post").validate({
			ignore : ':not(select:hidden,input:text,select)',
			// Specify the validation rules
			rules : {
				'lkp_city_id' : "required",
				'from_location_id' : "required",
				'to_location_id' : "required",
				'from_date' : "required",
				'to_date' : "required",
				'lkp_load_type_id' : "required",
				'actual_weight' : {
					required : true,
					number : true
				},
				'lkp_vehicle_type_id' : "required",
				'transit_time' : {
					required : true,
					number : true

				},
				'minimum_hours':{
					required : true,
					number : true
				},
				'minimum_charges':{
					required : true,
					number : true
				},
				'minimum_kms':{
					required : true,
					number : true
				},
				'basic_charges' : {
					required : true,
					number : true
				},
				'hourly_waiting_charges' : {
					required : true,
					number : true
				},
				'over_dimension_charges' : {
					required : true,
					number : true
				},
				'labour_charges' : {
					required : true,
					number : true
				},

			},

			// Specify the validation error messages
			messages : {
				'lkp_city_id' : "Please select city",
				'from_location_id' : "Please select from location",
				'to_location_id' : "Please select to location",
				'from_date' : "Please select from date",
				'to_date' : "Please select to date",
				'lkp_load_type_id' : "Please select load type",
				'actual_weight' : {
					required : "Please enter the actual weight of product",
					number : "Please enter only numbers"
				},
				'lkp_vehicle_type_id' : "Please select vehicle type",
				'transit_time' : {
					required : "Please enter transit days / hours",
					number : "Please enter only numbers"

				},
				'minimum_hours':{
					required : "Please enter minimum required hours",
					number : "Please enter only numbers"
				},
				'minimum_charges':{
					required : "Please enter minimum charges",
					number : "Please enter only numbers"
				},
				'minimum_kms':{
					required : "Please enter minimum Kms",
					number : "Please enter only numbers"
				},
				'basic_charges' : {
					required : "Please enter basic charges",
					number : "Please enter only numbers"
				},
				'hourly_waiting_charges' : {
					required : "Please enter waiting charges",
					number : "Please enter only numbers"
				},
				'over_dimension_charges' : {
					required : "Please enter over dimension",
					number : "Please enter only numbers"
				},
				'labour_charges' : {
					required : "Please enter labour charges",
					number : "Please enter only numbers"
				},

			},
			errorPlacement : function(error, element) {
				if ($(element).is('select')) {
					element.next().after(error); // special placement for
													// select elements
				} else {
					error.insertAfter(element); // default placement for
												// everything else
				}
			},

			submitHandler : function(form) {
				return false;
			}
		});

	});

	/*
	 * Change the rate type in intracity seller create post to see the required
	 * rates field
	 * 
	 */
	requiredCharges();
	$('#rate_type_id').change(function() {

		requiredCharges();

	});

});
function populateLocality() {
	var city_id = $("#intracity_city_list").val();
	// passing the data to the ajax function
	// that is further passed to the controller

	datastr = '&cities=' + city_id;

	// ajax function starts
	$.ajax({
		type : 'post', // defining the ajax type
		url : '/sellerintracity/loadlocality', // calling the
		// controller with the action involved
		dataType : 'html', // datatype
		data : datastr, // passing the data used for
		// operation
		success : function(result) {
			//alert(result);
			$("#from_locality_list").html(result);
			$("#from_locality_list").prepend(
					"<option value =''>From Location</option>");
			$("#to_locality_list").html(result);
			$("#to_locality_list").prepend(
					"<option  value =''>To Location</option>");
			$('.selectpicker').selectpicker('refresh');

			// $("#locality_list").html(result);

		}
	});

}

function requiredCharges() {
	var rateValue = $('#rate_type_id').val();
	
	if (rateValue == 1) {
		
		$('#minHours').hide();
		$('#minKilo').hide();
		$('#minCharges').hide();

	} else if (rateValue == 2) {
		$('#minKilo').hide();
		$('#minHours').show();
		$('#minCharges').show();
	} else if (rateValue == 3) {
		$('#minHours').hide();
		$('#minKilo').show();
		$('#minCharges').show();
	}

}
