<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport"
	content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,width=device-width,height=device-height,target-densitydpi=device-dpi,user-scalable=yes" />
<title>Logistiks</title>
<!--<script src="{{ asset('/js/jquery-1.11.2.min.js') }}"></script> -->
<script src="{{ asset('/js/jquery.min.js') }}"></script>



<link rel="stylesheet" type="text/css"  href="{{ asset('/css/bootstrap.css') }}">

<link rel="stylesheet" type="text/css"  href="{{ asset('/css/sass/stylesheets/developer.css') }}">
<link rel="stylesheet" type="text/css"	href="{{ asset('/css/bootstrap-select.min.css') }}">
<link rel="stylesheet" type="text/css"	href="{{ asset('/css/style.css') }}">
<!--  <link rel="stylesheet" href="{{ asset('/css/sass/stylesheets/custom.css') }}"> -->
 <link rel="stylesheet" href="{{ asset('/css/sass/stylesheets/jquery-ui.css') }}">
<link rel="stylesheet" type="text/css"	href="{{ asset('/css/responsive.css') }}">

<link rel="stylesheet" type="text/css"	href="{{ asset('/css/jquery.range.css') }}">
<link rel="stylesheet" href="{{ asset('/css/sass/stylesheets/jquery-ui.css') }}">


<link rel="stylesheet" href="{{ asset('/css/sass/stylesheets/token-input.css') }}">	
<link rel="stylesheet" href="{{ asset('/css/sass/stylesheets/jquery.tokenize.css') }}">	
<link href="{{asset('/css/sass/stylesheets/bootstrap-datetimepicker.min.css')}}" rel="stylesheet" media="screen">
<link href="{{asset('/css/sass/stylesheets/jquery.calculator.css')}}" rel="stylesheet" media="screen">



<!-- New JS -->

<script type="text/javascript" src="{{ asset('/js/bootstrap.js') }}"></script>
<script src="{{ asset('/js/bootstrap-select.min.js') }}"></script>
<script src="{{ asset('/js/jquery.range.js') }}"></script>
<script src="{{ asset('/js/custom.js') }}"></script>
<!-- Network Js -->
<script src="{{ asset('/js/network/network.js') }}"></script>

<!-- Below script my custom js and autocomplete js(srinu) -->
<script src="{{ asset('/js/jquery.validate.min.js') }}"></script>
<!-- 	<script src="{{ asset('/js/bootstrap.min.js') }}"></script> -->
<!--script src="{{ asset('/js/bootstrap-select.js') }}"></script-->
<script src="{{ asset('/js/jquery-ui.js') }}"></script>
<script src="{{ asset('/js/jquery.blockUI.js') }}"></script>
<script src="{{ asset('/js/login.js') }}"></script>

<script src="{{ asset('/js/jquery.slimscroll.js') }}"></script>

<!-- -Below scripts for seller auto complete-- -->
<script src="{{ asset('/js/jquery.tokeninput.js') }}"></script>
<script src="{{ asset('/js/jquery.tokenize.js') }}"></script>
<script src="{{ asset('/js/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{ asset('/js/bootstrap-datetimepicker.js')}}"></script>
<script src="{{ asset('/js/bootstrap-datetimepicker.fr.js')}}"></script>
<script src="{{ asset('/js/regular-validation.js')}}"></script>
<script src="{{ asset('/js/jquery.plugin.js')}}"></script>
<script src="{{ asset('/js/load_more.js')}}"></script>

<meta name="csrf-token" content="{{ csrf_token() }}" />
<script type="text/javascript">

		$(document).ready(function(){
                    $('#pincode_form').change(function() {
                        $('#pincode_form').submit();
                    });
			$('input:file').change(function(e){
				  var fileName = e.target.files[0].name;
				 var elementId = $(this).attr('name');
				 $("#"+elementId).html(fileName);
				});

		$(".circle1").hover(function(){
			$(".free-hover").show()

		}, function(){
			$(".free-hover").hide()
		})
		$(".circle2").hover(function(){
			$(".free-hover2").show()

		}, function(){
			$(".free-hover2").hide()
		})
		$(".circle3").hover(function(){
			$(".free-hover3").show()

		}, function(){
			$(".free-hover3").hide()
		});
        $('.buyer_counter_offer_consignment_pickup_date').datepicker({ dateFormat: "dd/mm/yy",minDate: 0});

        $( ".fromcalendar" ).datepicker({
 	       //defaultDate: "+1w",
 		   dateFormat: "dd/mm/yy",
 	       changeMonth: true,
 	       numberOfMonths: 1,
           //minDate: -31,
 	       onClose: function( selectedDate ) {
 	         $( ".tocalendar" ).datepicker( "option", "minDate", selectedDate );
 	       }
 	     });
 	     $( ".tocalendar" ).datepicker({
 	      // defaultDate: "+1w",
 	       dateFormat: "dd/mm/yy",
 	       changeMonth: true,
 	       numberOfMonths: 1,
           minDate: 0,
 	       onClose: function( selectedDate ) {
 	         $( ".fromcalendar" ).datepicker( "option", "maxDate", selectedDate );
 	       }
 	     });
		$('.calendar').datepicker({ dateFormat: "dd/mm/yy"})
		.on('change', function() {
       		 $(this).valid();  // triggers the validation test
    	});
                
		$('.hasDatepicker').datepicker({ dateFormat: "dd/mm/yy"});

        $('.datetimepicker').datetimepicker({
            format: 'yyyy-mm-dd hh:ii:ss',
              weekStart: 1,
              todayBtn:  1,
              autoclose: 1,
              todayHighlight: 1,
              startView: 2,
              forceParse: 0,
              minView: 0,
              maxView: 1,
              showMeridian: 1
          });

       
	    
	     $( ".dateRangeFrom" ).datepicker({
	       //defaultDate: "+1w",
		   dateFormat: "dd/mm/yy",
	       changeMonth: true,
	       numberOfMonths: 1,
           minDate: -31,
	       onClose: function( selectedDate ) {
		       console.log(selectedDate);
	         $( ".dateRangeTo" ).datepicker( "option", "maxDate", selectedDate );
	       }
	     });
	     $( ".dateRangeTo" ).datepicker({
	      // defaultDate: "+1w",
	       dateFormat: "dd/mm/yy",
	       changeMonth: true,
	       numberOfMonths: 1,
           minDate: 0,
	       onClose: function( selectedDate ) {
	    	   console.log(selectedDate);
	         $( ".dateRangeFrom" ).datepicker( "option", "maxDate", selectedDate );
	       }
	     });

	     $('.dateRange').datepicker({ dateFormat: "dd-mm-yy"});
	     $('.dateRangeFormat').datepicker({ dateFormat: "dd/mm/yy"});

	    
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
		
		$('.selectpicker').change(function(){
			$(this).selectpicker('refresh');
			$(this).valid();  // triggers the validation test
		});	
		
			
			$('.filter_calendar').datepicker({
				onSelect: function (dateText, inst) {
					$(this).closest("form").submit();
				}
			});
			$(".detailsslide").click(function(){
			$(".table-slide").slideToggle("500");
			});
                $(".detailsslide-document").click(function(){
				$(".table-slide-document").slideToggle("500");
			});
                $(".detailsslide-pricetrial").click(function(){
				$(".table-slide-pricetrial").slideToggle("500");
			});
			$(".dateRange").change(function(){
				$(this).closest("form").submit();
			});
			$(".dateRangeFormat").change(function(){
				$(this).closest("form").submit();
			});
			$(".searchSubmit").click(function(){
				$(this).closest("form").submit();
			});

			

			$(".search-icon").click(function(){
	        	$(".search-fld").toggle();
	        });
	        $(".mobile-nav").click(function(){
	        	$(".main-nav ul").toggle();
	        });

	        
	        
		//Progrees bar in buyer search result page designer code 

			$( "#slider-range" ).slider({
				range: true,
				min: 0,
				max: parseInt($("#price_to").val())+10000,
				values: [ $("#price_from").val(), $("#price_to").val() ],
				slide: function( event, ui ) {
					$( "#amount" ).val(  ui.values[ 0 ] + "/-" + "     "+  ui.values[ 1 ] + "/-" );
				},
				change: function( event, ui ) {
					$(this).closest("form").submit();
				},
			});
			$( "#amount" ).val( $( "#slider-range" ).slider( "values", 0 ) + "/-" + "     "
				+ $( "#slider-range" ).slider( "values", 1 ) + "/-" );

			$(".html_link").click(function(){
				location.href = $(this).attr("data_link");
				console.log($(this).attr("data_link"));
			});

			
			
		});
	</script>
<script type="text/javascript">
	$(document).ready(function(){
		$.ajaxSetup({
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
		});

		$(".login-js").hide();
		
	});


		function outputUpdate(vol) {
			document.querySelector('#volume').value = vol;
		}
	</script>

@if(Session::get ( 'service_id' )==ROAD_INTRACITY)
<script src="{{ asset('/js/intracity_buyer.js') }}"></script>
@endif
<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->




<script type="text/javascript">
$(window).load(function() {
	$(".loaderGif").fadeOut("slow");

});


	$(document).ready(function(){
		
		//$(".dropdown_data ul ol > li .menu-count").addClass("menu-count-hide");
		$(".dropdown_data ul li.inner-dropdown").click(function(){
	    	$(this).toggleClass("active");
		    $(this).find(".fa-caret-down").hide();
		    $(this).find(".fa-caret-right").show();
		    $(this).find("ol").slideToggle();
		    
		    $(".dropdown_data ul ol > li .menu-count").addClass("menu-count-hide");
    	
    		setTimeout(function(){	
    			$(".dropdown_data ul ol > li .menu-count").removeClass("menu-count-hide");
    		}, 500);
	    });
	    });
	
	</script>
</head>
<body>
<div class="loaderGif"></div>
	<!-- Header Starts Here-->
	@include('partials.header')
	<!-- Header Ends Here-->
	<!-- Main Container Starts Here-->
	
	<div class="main-container">
	
@yield('content')</div>
	<!-- Main Container Ends Here-->

	<!-- Modal -->
	<div id="truckhaul" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content registeration">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Truck Haul</h4>
				</div>
				<div class="modal-body">
					<p>Do you want to create a post for return truck haul.</p>
				</div>
				<div class="modal-footer">
					<button type="button" class="truck_haul_ok" class="btn btn-default"
						data-dismiss="modal">Yes</button>
					<button type="button" class="truck_haul_ok" class="btn btn-default"
						data-dismiss="modal">No</button>
				</div>
			</div>

		</div>
	</div>

	<!-- Modal -->
	<div id="payment" class="modal fade" role="dialog">
		<div class="modal-dialog">

			<!-- Modal content-->
			<div class="modal-content registeration">
				<div class="modal-header">
					<button type="button" class="close payment_success"
						data-dismiss="modal">&times;</button>
					<h4 class="modal-title">Payment Success</h4>
				</div>
				<div class="modal-body">
					<p>Your payment is success and receipt is generated.</p>
				</div>
				<div class="modal-footer">
					<button type="button" id="payment_success"
						class="btn btn-default payment_success" data-dismiss="modal">Ok</button>
				</div>
			</div>

		</div>
	</div>
	<!-- Modal for confirmation message -->
	<div class="modal fade" id="erroralertmodal" style="display: none">
		<div class="modal-dialog confirmation-message">
			<div class="modal-content">
	            <div class="modal-body">
	            	<div class="col-sm-3"><img src="{{url('images/right-icon.png')}}"></div>
	            	<div class="col-sm-9 confirmation-right"><b></b></div>
	            	<div class="col-sm-12 margin-top text-right">
	            		
	                </div>

			        <div class="clearfix"></div>
			    </div>
		        <div class="modal-footer">
	                <button type="button" class="btn btn-default ok-btn error-ok-btn" data-dismiss="modal">Ok</button> 
		        </div>
	            <div class="clearfix"></div>
	        </div>
    	</div>
	</div>
    <!-- Modal for New message -->
    <div class="modal fade" id="new_message_modal" role="dialog" style="display: none">
        {!! Form::open(array('url' => 'setmessagedetails/', 'id' => 'sendmessage', 'name' => 'sendmessage','enctype'=>'multipart/form-data')) !!}
        <div class="modal-dialog confirmation-message">
            <div class="modal-content">     
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <div class="modal-body">
                	<h4 class="sub-head red margin-left-none margin-bottom">New Message</h4>
                    <div >
                        {!! Form::text('', '', array('id' => 'from_name', 'class'=>'form-control form-control1', 'placeholder'=>'From *','readonly')) !!}
                        </div>
                    <div>
                        {!! Form::hidden('message_to', '', array('id' => 'message_to', 'class'=>'form-control form-control1', 'placeholder'=>'To *')) !!}
                    </div>
                    <div>
                        {!! Form::text('message_subject', '', array('id' => 'message_subject', 'class'=>'form-control form-control1 margin-bottom-4', 'placeholder'=>'Subject *' ,'readonly')) !!}
                    </div>
                    <div>
                        {!! Form::hidden('is_term', '', array('id' => 'is_term', 'class'=>'form-control form-control1')) !!}
                        {!! Form::hidden('buyer_quote', '', array('id' => 'buyer_quote', 'class'=>'form-control form-control1')) !!}
                        {!! Form::hidden('seller_post', '', array('id' => 'seller_post', 'class'=>'form-control form-control1')) !!}
                        {!! Form::hidden('buyer_quote_item', '', array('id' => 'buyer_quote_item', 'class'=>'form-control')) !!}
                        {!! Form::hidden('buyer_quote_item_leads', '', array('id' => 'buyer_quote_item_leads', 'class'=>'form-control')) !!}
                        {!! Form::hidden('buyer_quote_item_seller', '', array('id' => 'buyer_quote_item_seller', 'class'=>'form-control')) !!}
                        {!! Form::hidden('buyer_quote_item_seller_leads', '', array('id' => 'buyer_quote_item_seller_leads', 'class'=>'form-control')) !!}
                        {!! Form::hidden('order_id_for_model', '', array('id' => 'order_id_for_model', 'class'=>'form-control')) !!}
                        {!! Form::hidden('contract_id_for_model', '', array('id' => 'contract_id_for_model', 'class'=>'form-control')) !!}
                        {!! Form::hidden('buyer_quote_item_for_search', '', array('id' => 'buyer_quote_item_for_search', 'class'=>'form-control')) !!}
                        {!! Form::hidden('buyer_quote_item_for_search_seller', '', array('id' => 'buyer_quote_item_for_search_seller', 'class'=>'form-control')) !!}
                        {!! Form::hidden('order_id_for_model_seller', '', array('id' => 'order_id_for_model_seller', 'class'=>'form-control')) !!}
                        {!! Form::hidden('message_id', '', array('id' => 'message_id', 'class'=>'form-control')) !!}
                    </div>
		<div>{!! Form::textarea('message_body', '', array('id' => 'message_body', 'class'=>'form-control form-control1 message-body', 'placeholder'=>'Body *')) !!}</div>
                    
                    <div class=""><i class="fa fa-paperclip"></i>

                        {!! Form::label('message_attachment', 'Attachment ', array('class' => ''));   !!}
                        <div>
                            {!! Form::file('message_attachment',array('id' =>'message_attachment','class' => 'filestyle btn-file file-upload')) !!}
                            
                            <div id="message_attachment_display"></div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="modal-footer">
<!--                    
                    <input type="submit" name="save_as_draft" class="btn add-btn flat-btn ok-btn message_save_button" data-toggle="modal" data-target="#new_message_modal" value="Save as Draft">-->
                    @if(strpos($_SERVER['REQUEST_URI'],"byersearchresults")=== false && strpos($_SERVER['REQUEST_URI'],'buyersearchresults')=== false && strpos($_SERVER['REQUEST_URI'],'buyerordersearch')=== false && strpos($_SERVER['REQUEST_URI'],'termsellersearchresults')=== false)
                    <input type="submit" name="send_message" class="btn red-btn flat-btn ok-btn message_send_button" value="Send" > 
                    @else
                    <input type="button" name="send_message" id="send_message_from_search" class="btn btn-default ok-btn message_send_button" value="Send" > 
                    @endif
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
	@if(isset(Auth::user()->id) && Auth::user()->id != '')
    <!-- Modal -->
	  <div class="modal fade" id="change-service" role="dialog">
	    <div class="modal-dialog">
	    
	      <!-- Modal content-->
	      <div class="modal-content">
	        <button type="button" class="close" data-dismiss="modal">&times;</button>
	        <div class="modal-body">

	        {{--*/ $routeName = strtolower(substr(class_basename(Route::currentRouteAction()), (strpos(class_basename(Route::currentRouteAction()), '@') + 1))) /*--}}
				@inject('common', 'App\Components\CommonComponent')
				{{--*/ $SellerServiceExits=$common->getSellerServiceExits() /*--}}
				{{--*/ $strUrlService = '' /*--}}
				@for($i = 0; $i <= count(Request::segments()); $i++)
				{{--*/ $strUrlService .= Request::segment($i)."/" /*--}}
				@endfor
				@if($routeName == "sellerpostdetails" || $routeName == "sellerpostslist"
				    || $routeName == "updateseller" || $routeName == "ptlupdatesellerpost" || $routeName == "relocationupdatesellerpost")
					{{--*/ $strUrlService = "/sellerlist" /*--}}
				@endif
				@if($routeName == "getpostbuyercounteroffer" || $routeName == "editbuyerquote")
					{{--*/ $strUrlService = "/buyerposts" /*--}}
				@endif
				@if($routeName =="showdetails" || $routeName =="consignmentpickup")
					{{--*/ $strUrlService = "/orders/seller_orders" /*--}}
				@endif
				@if($routeName =="buyerordershowdetails")
					{{--*/ $strUrlService = "/orders/buyer_orders" /*--}}
				@endif
				@if($routeName =="sellersearchresults" ||  $routeName =="termsellersearchresults")
					{{--*/ $strUrlService = "/sellersearchbuyers" /*--}}
				@endif
				@if($routeName =="buyersearchresults")
					{{--*/ $strUrlService = "/buyersearch" /*--}}
				@endif
				@if($routeName == "viewzone" || $routeName == "viewtier" 
				    || $routeName == "viewtransitmatrix" || $routeName == "viewsector"
				    || $routeName == "viewpincode" || $routeName == "viewaddpincode")
				    {{--*/ $strUrlService = "/sellerlist" /*--}}
				@endif

				@if($routeName =="index" &&  (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER))
					{{--*/ $strUrlService = "/sellersearchbuyers" /*--}}
				@endif
				@if($routeName =="index" &&  (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
					{{--*/ $strUrlService = "/buyersearch" /*--}}
				@endif
	          	<div class="col-md-12 home-block">
								    <div class="home-menu">
									    <h3>Transportation</h3>
									    
									    @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))

										<div class="service-div-block">
											<div class="service-div">Road</div>
										    <div id="ftl" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == ROAD_FTL) class="active-inner" @endif onclick="return buyersetservice(1,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/FTL.png')}}" title="{{FTL_IMAGE_TITLE}}"/>
										    		FTL
										    	</a>
										    </div>
										    <div id="ltl" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == ROAD_PTL) class="active-inner" @endif  onclick="return buyersetservice(2,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/LTL.png')}}" title="{{LTL_IMAGE_TITLE}}" />
										    		LTL
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
										    <div class="service-div">Rail</div>
										    <div id="rail" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == RAIL) class="active-inner" @endif onclick="return buyersetservice(6,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/rail.png')}}" title="{{RAIL_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
											<div class="service-div">Air</div>
										    <div id="airdomestic" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == AIR_DOMESTIC) class="active-inner" @endif onclick="return buyersetservice(7,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/air_dom.png')}}" title="{{AIRDOM_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										    <div id="airinternational" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == AIR_INTERNATIONAL) class="active-inner" @endif onclick="return buyersetservice(8,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/air_intl.png')}}" title="{{AIRINT_IMAGE_TITLE}}"/>
										    		Intl
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
										    <div class="service-div">Ocean</div>
										    <div id="ocean" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == OCEAN) class="active-inner" @endif onclick="return buyersetservice(9,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/ocean.png')}}" title="{{OCEAN_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>



										@elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )

											<div class="service-div-block">
											<div class="service-div">Road</div>
										    @if (in_array(ROAD_FTL, $SellerServiceExits))
											<div id="ftl" class="service-div service-icon-div">
      										@else
											<div id="ftl" class="service-div service-icon-div checkserviceseller">
 											@endif
										    	<a @if(Session::get('service_id') == ROAD_FTL) class="active-inner" @endif onclick="return subcriptionuserservice(1,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/FTL.png')}}" title="{{FTL_IMAGE_TITLE}}"/>
										    		FTL
										    	</a>
										    </div>
										    @if (in_array(ROAD_PTL, $SellerServiceExits))
														<div id="ltl" class="service-div service-icon-div">
      													@else
														<div id="ltl" class="service-div service-icon-div checkserviceseller">
 														@endif
										    	<a @if(Session::get('service_id') == ROAD_PTL) class="active-inner" @endif onclick="return subcriptionuserservice(2,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/LTL.png')}}" title="{{LTL_IMAGE_TITLE}}"/>
										    		LTL
										    	</a>
										    </div>	
										</div>




										<div class="service-div-block">
										    <div class="service-div">Rail</div>
										    @if (in_array(RAIL, $SellerServiceExits))
														<div id="rail" class="service-div service-icon-div">
      													@else
														<div id="rail" class="service-div service-icon-div checkserviceseller">
 														@endif
										    	<a @if(Session::get('service_id') == RAIL) class="active-inner" @endif onclick="return subcriptionuserservice(6,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/rail.png')}}" title="{{RAIL_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
											<div class="service-div">Air</div>
										    @if (in_array(AIR_DOMESTIC, $SellerServiceExits))
														<div id="airdomestic" class="service-div service-icon-div">
      													@else
														<div id="airdomestic" class="service-div service-icon-div checkserviceseller">
 														@endif
										    	<a @if(Session::get('service_id') == AIR_DOMESTIC) class="active-inner" @endif onclick="return subcriptionuserservice(7,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/air_dom.png')}}" title="{{AIRDOM_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										    @if (in_array(AIR_INTERNATIONAL, $SellerServiceExits))
														<div id="airinternational" class="service-div service-icon-div">
      													@else
														<div id="airinternational" class="service-div service-icon-div checkserviceseller">
 														@endif
										    	<a @if(Session::get('service_id') == AIR_INTERNATIONAL) class="active-inner" @endif onclick="return subcriptionuserservice(8,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/air_intl.png')}}" title="{{AIRINT_IMAGE_TITLE}}"/>
										    		Intl
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
										    <div class="service-div">Ocean</div>
										    @if (in_array(OCEAN, $SellerServiceExits))
														<div id="ocean" class="service-div service-icon-div">
      													@else
														<div id="ocean" class="service-div service-icon-div checkserviceseller">
 														@endif
										    	<a @if(Session::get('service_id') == OCEAN) class="active-inner" @endif onclick="return subcriptionuserservice(9,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/ocean.png')}}" title="{{OCEAN_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>
												
										@endif										

										@if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))

										<div class="service-div-block">
										    <div class="service-div">Intracity</div>
										    <div id="hyper_local" class="service-div service-icon-div">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}"/>
										    		Hyper Local
										    	</a>
										    </div>
										    <div id="intracity" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == ROAD_INTRACITY) class="active-inner" @endif onclick="return buyersetservice(3,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/intracity.png')}}" title="{{INTRACITY_IMAGE_TITLE}}"/>
										    		Intracity
										    	</a>
										    </div>
										</div>
										
										<div class="service-div-block">
										    <div class="service-div">Courier</div>
										    <div id="courier" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == COURIER) class="active-inner" @endif onclick="return buyersetservice(21,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/courier.png')}}" title="{{COURIER_IMAGE_TITLE}}"/>
										    	</a>
										    </div>
										</div>

										@elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id') == 0) || (Session::get('last_login_role_id')== SELLER) )
										<div class="service-div-block">
										    <div class="service-div">Intracity</div>
										    <div id="hyper_local" class="service-div service-icon-div">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}" />
										    		Hyper Local
										    	</a>
										    </div>
										    <div class="service-div service-icon-div checkserviceseller">
										    	<a href="javascript:void(0);">
										    		<img src="{{url('images/log-icons/intracity.png')}}" title="{{INTRACITY_IMAGE_TITLE}}"/>
										    		Intracity
										    	</a>
										    </div>
										</div>
										
										<div class="service-div-block">
										    <div class="service-div">Courier</div>
										    @if (in_array(COURIER, $SellerServiceExits))
														<div id="courier" class="service-div service-icon-div">
      													@else
														<div id="courier" class="service-div service-icon-div checkserviceseller">
 														@endif
										    	<a @if(Session::get('service_id') == COURIER) class="active-inner" @endif onclick="return subcriptionuserservice(21,'{{$strUrlService}}')">
										    		<img src="{{url('images/log-icons/courier.png')}}" title="{{COURIER_IMAGE_TITLE}}"/>
										    	</a>
										    </div>
										</div>
										@endif	
										
								    </div>
								     <div class="home-menu">
									    <h3>Vehicle</h3>
									    <div class="service-div-block">
											<div class="service-div">Vehicle</div>
										    <!--  div class="service-div service-icon-div checkserviceseller">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/truck_haul.png')}}" title="{{TRUCK_HAUL_IMAGE_TITLE}}"/>
										    		Haul
										    	</a>
										    </div>
										    <div class="service-div service-icon-div checkserviceseller">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/truck_lease.png')}}" title="{{TRUCK_LEASE_IMAGE_TITLE}}"/>
										    		Lease
										    	</a>
										    </div-->	
										    @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
												<div  class="service-div service-icon-div">
											@elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )
												@if (in_array(ROAD_TRUCK_HAUL, $SellerServiceExits))
												<div class="service-div service-icon-div">
												@else
												<div class="service-div service-icon-div checkserviceseller">
												@endif
											@endif
										    	@if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))

											        <a @if(Session::get('service_id') == ROAD_TRUCK_HAUL) class="active-inner" @endif onclick="return buyersetservice(4,'{{$strUrlService}}')">
											    		<img src="{{url('images/log-icons/truck_haul.png')}}" title="{{TRUCK_HAUL_IMAGE_TITLE}}"/>
											    		Haul
											    	</a>
											    @elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )
													<a @if(Session::get('service_id') == ROAD_TRUCK_HAUL) class="active-inner" @endif onclick="return subcriptionuserservice(4,'{{$strUrlService}}')">
											    		<img src="{{url('images/log-icons/truck_haul.png')}}" title="{{TRUCK_HAUL_IMAGE_TITLE}}"/>
											    		Haul
											    	</a>
												@endif
										    </div>

											@if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
												<div id="trucklease" class="service-div service-icon-div">
											@elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )
												@if (in_array(ROAD_TRUCK_LEASE, $SellerServiceExits))
												<div id="trucklease" class="service-div service-icon-div">
												@else
												<div id="trucklease" class="service-div service-icon-div checkserviceseller">
												@endif
											@endif
										        @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))

													<a @if(Session::get('service_id') == ROAD_TRUCK_LEASE) class="active-inner" @endif onclick="return buyersetservice(5,'{{$strUrlService}}')">
											    		<img src="{{url('images/log-icons/truck_lease.png')}}" title="{{TRUCK_LEASE_IMAGE_TITLE}}"/>
											    		Lease
											    	</a>
											    @elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )
													<a @if(Session::get('service_id') == ROAD_TRUCK_LEASE) class="active-inner" @endif onclick="return subcriptionuserservice(5,'{{$strUrlService}}')">
											    		<img src="{{url('images/log-icons/truck_lease.png')}}" title="{{TRUCK_LEASE_IMAGE_TITLE}}"/>
											    		Lease
											    	</a>
												@endif

										    </div>	
										</div>
								    </div>
								     <div class="home-menu">
									    <h3>Relocation</h3>
									    <div class="service-div-block">
											<div class="service-div">Home</div>
										    @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
										    <div class="service-div service-icon-div">
										    <a href="javascript:void(0)" onclick="return buyersetservice(15,'{{$strUrlService}}')">
										    @else
										    			@if(in_array(RELOCATION_DOMESTIC, $SellerServiceExits))
														<div class="service-div service-icon-div">
      													@else
														<div class="service-div service-icon-div checkserviceseller">
 														@endif
										    <a onclick="return subcriptionuserservice(15,'{{$strUrlService}}')">
										    @endif		<img src="{{url('images/log-icons/domestic.png')}}" title="{{RELOCATION_DOMESTIC_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										    <div class="service-div service-icon-div checkserviceseller">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/pet_move.png')}}" title="{{RELOCATION_PETMOVE_IMAGE_TITLE}}"/>
										    		Pet Move
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
											<div class="service-div"><span class="invisible_text">Home</span></div>
										    <div class="service-div service-icon-div checkserviceseller">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/international.png')}}" title="{{RELOCATION_INTERNATIONAL_IMAGE_TITLE}}"/>
										    		International
										    	</a>
										    </div>
										    <div class="service-div service-icon-div checkserviceseller">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/global_mobility.png')}}" title="{{RELOCATION_GLOBAL_MOBILITY_IMAGE_TITLE}}"/>
										    		General Mobility
										    	</a>
										    </div>	
										</div>
										<div class="service-div-block">
										    <div class="service-div">Office</div>
										    <div class="service-div service-icon-div checkserviceseller">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/office_domestic.png')}}" title="{{RELOCATION_OFFICE_DOMESTIC_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										</div>
								    </div>
								     
							    </div>
				<div class="clearfix"></div>
			</div>
	        </div>
	      </div>
	      
	    </div>
	    @endif
</body>
</html>
