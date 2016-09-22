$(document).ready(function(){

	$(".add-on").prev().addClass("borderLeftNone");

	$(".form-control").focusin(function(){
		$(this).addClass("selectBorder");
		$(this).parent().find("span").addClass("selectBorder");
	});
	$(".form-control").focusout(function(){
		$(this).removeClass("selectBorder");	
		$(this).parent().find("span").removeClass("selectBorder");
	});				
	$("body").keydown(function(){
    	$(".bootstrap-select .btn.dropdown-toggle.btn-default").focusin(function(){
			$(this).addClass("selectBorder");
			$(this).parent().parent().find("span").addClass("selectBorder");
			$(this).closest(".form-control-fld span").addClass("selectBorder");
		});
		$(".form-control").focusin(function(){
			$(this).addClass("selectBorder");
			$(this).parent().find("span").addClass("selectBorder");
		});
		$(".bootstrap-select .btn.dropdown-toggle.btn-default").focusout(function(){
			$(this).removeClass("selectBorder");	
			$(this).parent().parent().find("span").removeClass("selectBorder");
			$(this).closest(".form-control-fld span").removeClass("selectBorder");
		});
	});



	
	



	$(".log-div-inner ul.log-dropdown > li").hover(function(){
		$(this).addClass("gray-shade");
		$(this).find("div").show();
	}, function(){
		$(this).removeClass("gray-shade");
		$(this).find("div").hide();
	});

	$("select").addClass("selectpicker");
	$("select[multiple='multiple']").removeClass("selectpicker");
	
	limitslider();
	
	//------------------Network Add Meesage to profile JS Starts Here-------------------------
	$("#profilemessage .addmessage").click(function(){
		var message_subject = $('#message_subject').val();
		var message_body = $('#message_body').val();
		var id = $('#profileid').val();
		var data = {
		        'message_subject': message_subject,
		        'message_body': message_body,
		        'userid': id
		    };
                    $('#addmessage').attr('disabled','disabled');
		 $.ajax({
		        type: "POST",
		        url: '/addmessagetoprofile',
		        data: data,
		        success: function(jsonData) {
		            if(jsonData.success == false) {
		            	
		            	if(jsonData.errors.message_subject)
		            		$('#messagesubject_error').html(jsonData.errors.message_subject);
		            	else
		            		$('#messagesubject_error').html('');
		            	if(jsonData.errors.message_body)
		            		$('#messagebody_error').html(jsonData.errors.message_body);
		            	else
		            		$('#messagebody_error').html('');
                                    $('#addmessage').removeAttr('disabled');
		            } else {
		            	$('#messagesubject_error').html('');
		            	$('#messagebody_error').html('');
		            	
		            	$("#erroralertmodal .modal-body").html("Message Sent Successfully");
		                $("#erroralertmodal").modal({
		                    show: true
		                });
		                
		            	location.reload();  
		            }	      
		        },
		        error: function(request, status, error) {	            
		        },
		    });	 
	});
	
	
	//------------------Network Add Recomendation JS Starts Here-------------------------
	$("#addrecommend .recommendation").click(function(){
		var recomendation_body = $('#recomendation_body').val();
		var id = $('#profileid').val();
		var data = {
		        'body': recomendation_body,
		        'userid': id
		    };
		 $.ajax({
		        type: "POST",
		        url: '/addrecomendation',
		        data: data,
		        dataType: 'json',
		        success: function(jsonData) {
		            if(jsonData.success == false) {
		            	
		            	if(jsonData.errors.body)
		            		$('#recomendation_body_error').html(jsonData.errors.body);
		            	else
		            		$('#recomendation_body_error').html('');
		            	
		            } else {
		            	$('#recomendation_body_error').html('');
		            	
		            	location.reload();  
		            }	      
		        },
		        error: function(request, status, error) {	            
		        },
		    });	 
	});
	if($('#share_id').length){
		$('#share_id').tokenize({
	        datas: "/sharelistofusers?profid="+$("#profid").val(),
	        onAddToken:function(value, text, e){
	            var tokens=$("#share_id").val();
	                $("#share_id").val(tokens+","+value);
			},
			onRemoveToken:function(value, text, e){
				var tokens = $("#share_id").val();
				tokens = tokens.replace(','+value,'');
				$("#share_id").val(tokens);
			},
		});
	}
	$('.TokenSearch #user_ids').attr("placeholder", "Username *");
	$("#shareprofile .sharesubmit").click(function(){
		
		var shareids= $("#share_id").val();
		var sharesubject= $("#share_subject").val();
		var sharebody= $("#share_body").val();
		var user_link= $("#user_link").val();
		var data = {
		        'shareids': shareids,
		        'sharesubject':sharesubject,
		        'sharebody':sharebody,
		        'user_link':user_link,
		    };
		 $.ajax({
		        type: "POST",
		        url: '/shareprofile',
		        data: data,
		        dataType: 'json',
		        success: function(jsonData) {
		            if(jsonData.success == false) {
		            	if(jsonData.errors.shareids)
		            		$('#shareid__error').html(jsonData.errors.shareids);
		            	else
		            		$('#shareid__error').html('');
		            	if(jsonData.errors.sharesubject)
		            		$('#subject_error').html(jsonData.errors.sharesubject);
		            	else
		            		$('#subject_error').html('');
		            	if(jsonData.errors.sharebody)
		            		$('#sharebody__error').html(jsonData.errors.sharebody);
		            	else
		            		$('#sharebody__error').html('');
		            } else {
		            	$('#shareid__error').html('');
		            	$('#subject_error').html('');
		            	$('#sharebody__error').html('');
		            	$("#erroralertmodal .modal-body").html("Profile Shared Successfully");
		                $("#erroralertmodal").modal({
		                    show: true
		                });
		            	location.reload();  
		            }		        	   	
	        	        
		        },
		        error: function(request, status, error) {	            
		        },
		    });	
	});
	
	
	
	$("#recomends_links span").click(function(){
		var d = $(this).attr('data-showdiv'); 
		if(d == "recomendation_given") {
			$("#recomendation_given").show();
			$("#recomendation_received").hide();
		}else if(d == "recomendation_received") {
			$("#recomendation_given").hide();
			$("#recomendation_received").show();
		}else{
			$("#recomendation_given").show();
			$("#recomendation_received").hide();
		}

		$("#recomends_links span").each(function(){
			$(this).attr('class','');
		})
		$(this).addClass('red');
	});
	
	function limitslider(){
	
		//alert("hello");
		var item_count = $("#partners #myCarousel1 .carousel-inner > div.item").length;
		var item_img_count = $("#partners #myCarousel1 .carousel-inner > div.item ul li").length;
		if(item_count == 1){
			
			if(item_img_count <=5){
					$("#partners #myCarousel1 .carousel-control").hide();
			}
		}else if(item_count == 0){
			$(".nodata-partners").show();
			//$("#partners .inner-block-bg").hide();
			$("#partners #myCarousel1 .carousel-control").hide();
		} 
		if(item_count == 1){
			$("#partners #myCarousel1").addClass("change_alignment");
		}
			
		$("a[href=#partners]").click(function(){
			var item_count = $("#partners #myCarousel1 .carousel-inner > div.item").length;
			var item_img_count = $("#partners #myCarousel1 .carousel-inner > div.item ul li").length;
			if(item_count == 1){
				
				if(item_img_count <=5){
						$("#partners #myCarousel1 .carousel-control").hide();
				}
			}else if(item_count == 0){
				$(".nodata-partners").show();
				//$("#partners .inner-block-bg").hide();
				$("#partners #myCarousel1 .carousel-control").hide();
			} 
			if(item_count == 1){
				$("#partners #myCarousel1").addClass("change_alignment");
			}
			
		});
		
		$("a[href=#following]").click(function(){
			
			
			var item_count = $("#following #myCarousel2 .carousel-inner > div.item").length;
			var item_img_count = $("#following #myCarousel2 .carousel-inner > div.item ul li").length;
			if(item_count == 1){
				
				if(item_img_count <=5){
						$("#following #myCarousel2 .carousel-control").hide();
				}
			}else if(item_count == 0){
				$(".nodata-followers").show();
				//$("#following .inner-block-bg").hide();
				$("#following #myCarousel2 .carousel-control").hide();
			} 
			if(item_count == 1){
				$("#following #myCarousel2").addClass("change_alignment");
			}
		});
	
		$("a[href=#groups]").click(function(){
			var item_count = $("#groups #myCarousel3 .carousel-inner > div.item").length;
			var item_img_count = $("#groups #myCarousel3 .carousel-inner > div.item ul li").length;
			if(item_count == 1){
				
				if(item_img_count <=5){
						$("#groups #myCarousel3 .carousel-control").hide();
				}
			}else if(item_count == 0){
				$(".nodata-groups").show();
				//$("#groups .inner-block-bg").hide();
				$("#groups #myCarousel3 .carousel-control").hide();
			} 
			if(item_count == 1){
				$("#groups #myCarousel3").addClass("change_alignment");
			}
		});
	}
	
	
	$('input[disabled="disabled"]').prev().addClass("disable-bg");
	
	$('input[text]').each(function(){
	    var isDisabled = $(this).prop('disabled');
	    
	    if (isDisabled)
	    {
	        $(this).prev().addClass("disable-bg");
	    }
	});
	
	
	
	// CALENDAR CLEAR CODE STARTS HERE
	
	
	$(".from-date-control").click(function(){
		setTimeout(function(){
			$('.clear-date-from, .clear-date-to').hide();
			$('.clear-date-from').show();	
		},10);
	});
	$(".to-date-control").click(function(){
		setTimeout(function(){
			$('.clear-date-from, .clear-date-to').hide();
			$('.clear-date-to').show();	
		},10);
	});
	
	$( "body" ).on( "click", ".clear-date-from", function() {
		$(".from-date-control").val("");
	});
	
	$( "body" ).on( "click", ".clear-date-to", function() {
		$(".to-date-control").val("");
	});
	

// CALENDAR CLEAR CODE ENDS HERE	
	
	
	
	
	 $(".table-row div[class*='col-md-'], .data-value").each(function(){
		var value_1 = $(this).html();

		if (/\s/.test(value_1)) {
			$(this).addClass("break-word");
		}else{
			$(this).addClass("break-all");
		}
	});






	$(".search .dropdown-menu li a").click(function(){
	  	$(this).parents(".dropdown").find(".add-btn .change-search-icon > i").removeClass("fa fa-bars");
	  	var add_class = $(this).find("i").attr("class");
	  	$(this).parents(".dropdown").find(".add-btn .change-search-icon > i").attr("class",add_class);
	});


	var isIE = /*@cc_on!@*/false || !!document.documentMode;
	if (isIE==true){  
		$(".documents-terms input[type='file'], .upload-fld input[type='file']").css({
		    'left': '0',
		    'width':'125%'
		});
		$(".upload-browse-btn, .update_txt_test_buyer").hide();
		$("#community_group_logo, #community_group_logo1").css({
			"width":"100%",
			"height":"35px"
		});
	}



	var isOpera = !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
	var isChrome = !!window.chrome && !isOpera;              // Chrome 1+
	if (isChrome==true){  
		$(".documents-terms input[type='file']").addClass("chrome-alignment");
		$("#community_group_logo, #community_group_logo1").css({
			"left":"-86px",
			"top":"7px"
		});
	}



	
	$('.consult').click(function(){
		$(this).parent().parent().find(".dr-consult").slideToggle();
	});
	
	
	$(document).on('mouseover','.descriptionuser',function(){
		$("#partner-div").show();	
		var data = {
		        'id': $(this).attr('id')
		    };
		 $.ajax({
		        type: "GET",
		        url: '/getdescriptionuser',
		        data: data,
		        dataType: 'text',
		        success: function(data) {
		        	if(data == 0){
		        		
		        		$("#desctiption_partner").html("Not Specified");
		        	}else{
			          $("#desctiption_partner").html(data);	           
		        	}
		        },
		        error: function(request, status, error) {	            
		        },
		    });
	});
	
	$(document).on('mouseout','.descriptionuser',function(){
		$("#partner-div").hide();
	});
	$(document).on('mouseover','.descriptionfollows',function(){
		
		$("#follows-div").show();	
		var data = {
		        'id': $(this).attr('id')
		    };
		 $.ajax({
		        type: "GET",
		        url: '/getdescriptionuser',
		        data: data,
		        dataType: 'text',
		        success: function(data) {
		        	if(data == 0){
		        		$("#desctiption_follows").html("Not Specified");
		        	}else{
			          $("#desctiption_follows").html(data);	           
		        	}
		        },
		        error: function(request, status, error) {	            
		        },
		    });
	});
	$(document).on('mouseout','.descriptionfollows',function(){
		$("#follows-div").hide();
	});
		$(document).on('mouseover','.descriptiongroups',function(){
		$("#groups-div").show();	
		var data = {
		        'id': $(this).attr('id')
		    };
		 $.ajax({
		        type: "GET",
		        url: '/getdescriptiongroup',		        
		        data: data,
		        dataType: 'text',
		        success: function(data) {
		        	if(data == 0){
		        		$("#desctiption_groups").html("Not Specified");
		        	}else{
			          $("#desctiption_groups").html(data);	           
		        	}
		        },
		        error: function(request, status, error) {	            
		        },
		    });
	});
	
		
		$(document).on('mouseout','.descriptiongroups',function(){
			$("#groups-div").hide();
		});
		
	$(".show-data-link").click(function(){
        $(this).find(".show-icon").toggle();
        $(this).find(".hide-icon").toggle();
        $(this).parent().parent().parent().find(".show-data-div").slideToggle(500);
    });
	
	$(".submit-data").click(function(){
		$(this).toggleClass("red-btn-click");
		$(this).parent().parent().find(".submit-data-div").slideToggle(500);
	});


	$(".close-icon").click(function(){
		$(".show-icon").show();
		$(".hide-icon").hide();
		$(".show_details").show();
		$(".hide_details").hide();
		$(".detailsslide").removeClass("red-link");
		$(this).parent().parent().parent().slideUp(500);
	});

	
	$('.selectpicker').change(function(){
		$(".add-on-block").slideDown(700);
	});

	$(".transaction-details").click(function(){
		$(this).find(".show-icon").toggle();
		$(this).find(".hide-icon").toggle();
		$(this).parent().parent().parent().parent().find(".show-trans-details-div").slideToggle(500);
	});
	$(".transaction-details-expand").click(function(){
		$(this).find(".show-icon").toggle();
		$(this).find(".hide-icon").toggle();
		$(this).parent().parent().parent().parent().find(".show-trans-details-div-expand").slideToggle(500);
	});
	$(".show-data-cust").click(function(){
		$(this).parent().parent().find(".show-data-div").slideToggle(500);
	});

	$(".submit-data1").click(function(){
		$(this).toggleClass("red-btn-click");
		$(this).parent().parent().find(".show-data-link .show-icon").toggle();
		$(this).parent().parent().find(".show-data-link .hide-icon").toggle();
		$(this).parent().parent().find(".show-data-div").slideToggle(500);
	});

	$(".detail-head").click(function(){
		$(this).toggleClass("white-bg");
		$(this).next().slideToggle(500);
	});
	$("input[disabled='1']").prev().css("background-color","#eee");
	$(".add-on.unit").prev("input[type=text]").css("border-right","0");
	$(".add-on.unit-days").prev("input[type=text]").css("border-right","0");

	$( ".input-prepend").wrap("<div class='error_align_div'></div>");
	$( ".normal-select").wrap("<div class='error_align_div'></div>");

	$(".calendar, .dateRange").focus(function(){
		$("#ui-datepicker-div").addClass("margin-less");
		$('select').addClass("selectpicker");
	});

	$(".new_message").click(function(){
		$('.confirmation-message .TokenSearch #user_ids').attr("placeholder", "To");
	});


	if ($(".compare-fld .comparision_types_div").length==1){
		$(".compare-fld").addClass("compare-fld-alignment");
	}
	if ($(".compare-fld > div.pull-right").length==1){
		$(".compare-fld").addClass("compare-fld-alignment1");
	}

	/*$(".ui-autocomplete-input").click(function(){
	 $(".ui-autocomplete").animate({
	 "left": 160-35
	 });
	 });*/

	/*$(".update_txt").click(function(index) {
	 $(this).on("change", function(){

	 var filename = $(this).val();
	 alert("name==>"+filename);
	 $(this).parent().parent().next(".uploaded_file").val(filename);
	 });
	 });	*/

	$(".add-btn").click(function(){
		$("input[disabled='1']").prev().addClass("disable-bg");
	});



	$(".ftl_spot_transaction_details, .ptlBuyerDetailsSlide, .intrabuyerdetails_list, .detailsslide").click(function(){
		$(this).toggleClass("red-link");
	});


	$(".add-on.unit1").addClass("manage");
	$(".add-on.unit").addClass("manage");
	$(".add-on.unit-days").addClass("manage");
	
	
	//------------------Relocation JS Srats Here-------------------------


	$(".advanced-search-link").click(function(){
		$(".advanced-search-details").toggle();
		$(".more-search, .less-search").toggle();
	});

	$('.select-inventory').on('change', function() {
		if(this.value!=''){
			$(".inventory-block").show();
		}else{
			$(".inventory-block").hide();
		}
	});

	

	$(".crum-2").detach().prependTo(".main-container .main > .container:first");
	if($(".main-left").length==0){
		$(".main-container .main > .container:first").prepend("<div class='top-block'><div class='top-left-block'></div><div class='top-right-block'></div></div>")
		$(".crum-2").detach().prependTo(".main-container .main > .container:first .top-left-block");
		$(".main-container .main > .container:first > span.pull-left").detach().appendTo(".main-container .main > .container:first .top-left-block");
		$(".main-container .main > .container:first > h1.page-title").detach().appendTo(".main-container .main > .container:first .top-left-block");
		$(".main-container .main > .container:first > a.change-service").detach().appendTo(".main-container .main > .container:first .top-left-block");

		
		$(".main-container .main > .container:first > a").detach().appendTo(".main-container .main > .container:first .top-right-block");
		$(".main-container .main > .container:first > span.pull-right").detach().appendTo(".main-container .main > .container:first .top-right-block");
	}
	
	



	//------------------Relocation JS Ends Here-------------------------
	
	$("#btnfollwing").click(function(){
	
		var recomendation_body = $('#follwing_search').val();
		//var id = $('#profileid').val();
		var data = {
		        'searchtext': recomendation_body,
		       // 'userid': id
		    };
		 $.ajax({
		        type: "POST",
		        url: '/searchfollwingusers',
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
		        data: data,
		        dataType: 'json',
		        success: function(result) {	
		        	
		        	//alert(result.html);
		        	$("#myCarousel2").html(""); 
		        	$("#myCarousel2").html(result.html);

					var item_count = $("#following #myCarousel2 .carousel-inner > div.item").length;
					var item_img_count = $("#following #myCarousel2 .carousel-inner > div.item ul li").length;

					if(item_count != 0){

						$("#following #myCarousel2").show();
						$(".nodata-followers").hide();
						if(item_img_count <=5){

								$("#following #myCarousel2 .carousel-control").hide();
								$("#following #myCarousel2").show();
								$(".nodata-followers").hide();
						}
					}else if(item_count == 0){
						$("#follows-div").hide();
						$(".nodata-followers").show();
						$("#following #myCarousel2").hide();
						$(".nodata-followers").show();
						
					} 
		        	
					
		        	
		        },
		        error: function(request, status, error) {	            
		        },
		    });	 
	});
	
	$("#btnpartner").click(function(){
		var recomendation_body = $('#partner_search').val();
		//var id = $('#profileid').val();
		var data = {
		        'searchtext': recomendation_body,
		       // 'userid': id
		    };
		 $.ajax({
		        type: "POST",
		        url: '/searchpartnerusers',
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
		        data: data,
		        dataType: 'json',
		        success: function(result) {	
		        	
		        	//alert(result.html);
		        	$("#myCarousel1").html(""); 
		        	$("#myCarousel1").html(result.html);

					var item_count = $("#partners #myCarousel1 .carousel-inner > div.item").length;
					var item_img_count = $("#partners #myCarousel1 .carousel-inner > div.item ul li").length;
					if(item_count != 0){
						$("#partners #myCarousel1").show();
						$(".nodata-partners").hide();
						if(item_img_count <=5){
								$("#partners #myCarousel1 .carousel-control").hide();
								$("#partners #myCarousel1").show();
								$(".nodata-partners").hide();
						}
					}else if(item_count == 0){
						$("#partner-div").hide();
						$(".nodata-partners").show();
						$("#partners #myCarousel1").hide();
						$(".nodata-partners").show();
					} 
		        	
					
		        	
		        },
		        error: function(request, status, error) {	            
		        },
		    });	 
	});
	
	
	$("#btngroup").click(function(){
		var recomendation_body = $('#group_search').val();
		//var id = $('#profileid').val();
		var data = {
		        'searchtext': recomendation_body,
		       // 'userid': id
		    };
		 $.ajax({
		        type: "POST",
		        url: '/searchgroupusers',
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
		        data: data,
		        dataType: 'json',
		        success: function(result) {	
		        	
		        	//alert(result.html);
		        	$("#myCarousel3").html(""); 
		        	$("#myCarousel3").html(result.html);

					var item_count = $("#groups #myCarousel3 .carousel-inner > div.item").length;
					var item_img_count = $("#groups #myCarousel3 .carousel-inner > div.item ul li").length;
					if(item_count != 0){
						$("#groups #myCarousel3").show();
						$(".nodata-groups").hide();
						if(item_img_count <=5){
								$("#groups #myCarousel3 .carousel-control").hide();
								$("#groups #myCarousel3").show();
								$(".nodata-groups").hide();
						}
					}else if(item_count == 0){
						$("#groups-div").hide();
						$(".nodata-groups").show();
						$("#groups #myCarousel3").hide();
						$(".nodata-groups").show();
					} 
		        	
					
		        	
		        },
		        error: function(request, status, error) {	            
		        },
		    });	 
	});



	$(".crum-2").prev(".crum-2").hide();
	var post_message_text_height = $(".post-message-text").height();
	if(post_message_text_height >= 35){
		$(".post-message-btn").addClass("post-message-btn-styles");
	}

	




});

function setHomeBreadCrumb(bcname,role){
	if(bcname == 'search'){
		if(role == 'buyer'){
			$("#index_breadcrumb").html('Search &amp; Book');
		}else if(role == 'seller'){
			$("#index_breadcrumb").html('Search &amp; Submit Quote');
		}
	}else if(bcname == 'post'){
		if(role == 'buyer'){
			$("#index_breadcrumb").html('Post &amp; Get Quote');
		}else if(role == 'seller'){
			$("#index_breadcrumb").html('Post Rate Card');
		}
	}else if(bcname == 'market'){
		$("#index_breadcrumb").html('Market Feeds');
	}
}
//------------------Network Follow JS Starts Here-------------------------
function followNetworkProfile(id,status){	
	
	var data = {
	        'userid': id,
	        'status': status
	    };
	 $.ajax({
	        type: "POST",
	        url: '/follow',
	        data: data,
	        dataType: 'text',
	        success: function(data) {
	        	if(status==1){
	        	$("#followingid").html('<a href="javascript:void(0)" data-target="#followprofile" data-toggle="modal" onclick="followNetworkProfile('+id+',0)">Following</a><i class="fa fa-check"></i>') ;
	        	}else{
	        		$("#followingid").html('<a href="javascript:void(0)" data-target="#followprofile" data-toggle="modal" onclick="followNetworkProfile('+id+',1)">Follow</a>') ;	
	        	}
	        },
	        error: function(request, status, error) {	            
	        },
	    });	 
}
//------------------Network Partner Request JS Starts Here-------------------------
function partnerRequest(id){	
	
	var data = {
	        'userid': id
	    };
	 $.ajax({
	        type: "POST",
	        url: '/partnerrequest',
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
	        data: data,
	        dataType: 'text',
	        success: function(data) {	   	        	
	        	location.reload();         
	        },
	        error: function(request, status, error) {	            
	        },
	    });	 
}
//------------------Network Partner Acceptence JS Starts Here-------------------------
function acceptpartner(id){	
	
	var data = {
	        'userid': id
	    };
	 $.ajax({
	        type: "POST",
	        url: '/acceptpartner',
	        data: data,
	        dataType: 'text',
	        success: function(data) {	
	        	$("#erroralertmodal .modal-body").html("Partner Request Accepted");
                $("#erroralertmodal").modal({
                    show: true
                });
                
	        	location.reload();         
	        },
	        error: function(request, status, error) {	            
	        },
	    });	 
}
//------------------Network Recommendation approvalJS Starts Here-------------------------
function approverecommend(id,status,type){	
	
	var data = {
	        'userid': id,
	        'status': status,
	        'type': type,
	    };
	 $.ajax({
	        type: "POST",
	        url: '/acceptrecomendation',
	        data: data,
	        dataType: 'text',
	        success: function(data) {	   	        	
	        	location.reload();         
	        },
	        error: function(request, status, error) {	            
	        },
	    });	 
}
function subcriptionuserservice(serviceidcheck,url){
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
        	if(data == 2){
        		location.href="/home";
        	}
        	else if (data == 1) {
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
            location.href="/home";
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
            location.href="/home";
        }
    	});
}
//--------------------- Comment Delete-------------------------//
function cancelcomment(commentid,id){
	var answer = confirm ("Are you sure you want to delete the comment?");
	  if (answer)
	  {
		  datastr = '&commentIds=' + commentid+'&id=' + id;
		  $.ajax({
		  type : 'post', // defining the ajax type
		  url : '/deletepostcomment', // calling the controller with the

		  dataType : 'html', // datatype
		  data : datastr, // passing the data used for operation
		  success : function(jsonData){
			   location.reload();
                  
		   }
       },"json");
	  }	  
 }	  

//-----------------------Coment Edit----------------------------//
function editcomment(feedid,commentid,id){
	
	datastr = '&commentIds=' + commentid+'&id=' + id;
	  $.ajax({
	  type : 'post', // defining the ajax type
	  url : '/deletepostcomment', // calling the controller with the

	  dataType : 'html', // datatype
	  data : datastr, // passing the data used for operation
	  success : function(jsonData){
		  $("#hidecomment"+commentid).hide();
	   }
	  },"json");
	  
	var rowid = "#txtFeedComment"+feedid;

	$( "#txtFeedComment"+feedid).val($("#commentid_"+commentid).html());
	
	
	
	
}

$(document).ready(function(){
	$(".advanced-search-link-officemove").click(function(){
		$(".advanced-search-details-officemove").toggle();
		$(".more-search-officemove, .less-search-officemove").toggle();
	});

	//$( ".main-container .main-left" ).detach().prependTo( ".main .main-inner" );
});			

