<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="initial-scale=1.0,minimum-scale=1.0,maximum-scale=1.0,width=device-width,height=device-height,target-densitydpi=device-dpi,user-scalable=yes" />
	<title>Logistiks</title>
	<script src="{{ asset('/js/jquery.min.js') }}"></script>
	
	<title>Logistiks</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
	<link rel="stylesheet" type="text/css" href="{{ asset('/css/bootstrap.css') }}" media="screen">
	<link rel="stylesheet" type="text/css" href="{{ asset('/css/bootstrap-select.min.css') }}">
	<link rel="stylesheet" type="text/css"  href="{{ asset('/css/sass/stylesheets/developer.css') }}"> 	
	<link rel="stylesheet" type="text/css" href="{{ asset('/css/style.css') }}" media="screen">
	<link rel="stylesheet" type="text/css" href="{{ asset('/css/responsive.css') }}" media="screen">
	<link rel="stylesheet" type="text/css" href="{{ asset('/css/font-awesome.min.css') }}" media="screen">
	<link rel="stylesheet" type="text/css" href="{{ asset('/css/jquery.range.css') }}">



	<!-- Fonts -->
	<link href='//fonts.googleapis.com/css?family=Roboto:400,300' rel='stylesheet' type='text/css'>

	
	

	<script src="{{ asset('/js/jquery.validate.min.js') }}"></script>
	<script src="{{ asset('/js/buyerpost.js') }}"></script>
	<script src="{{ asset('/js/bootstrap.min.js') }}"></script>
<!--<script src="{{ asset('/js/bootstrap-multiselect.js') }}"></script>-->
	<script src="{{ asset('/js/bootstrap-select.js') }}"></script>
    <script src="{{ asset('/js/jquery-ui.js') }}"></script>
      <script src="{{ asset('/js/jquery.blockUI.js') }}"></script>
<script src="{{ asset('/js/login.js') }}"></script>

	<script src="{{ asset('/js/jquery.slimscroll.js') }}"></script>
    	<script src="{{ asset('/js/equipment.js') }}"></script>
    	<script src="{{ asset('/js/usersales.js') }}"></script>
    	<script src="{{ asset('/js/sellerpost.js') }}"></script>	
	
	<!-- -Below scripts for seller auto complete-- -->
	<script  src="{{ asset('/js/jquery.tokeninput.js') }}"></script>
	<script  src="{{ asset('/js/jquery.tokenize.js') }}"></script>
	<script  src="{{ asset('/js/order.js') }}"></script>
	<script
	src="{{ asset('/js/bootstrap-datetimepicker.min.js')}}"></script>
<script
	src="{{ asset('/js/bootstrap-datetimepicker.js')}}"></script>
<script
	src="{{ asset('/js/bootstrap-datetimepicker.fr.js')}}"></script>
<script
	src="{{ asset('/js/seller-intracity.js')}}"></script>	
	<script
	src="{{ asset('/js/regular-validation.js')}}"></script>	
	<script
	src="{{ asset('/js/jquery.plugin.js')}}"></script>
	<script
	src="{{ asset('/js/jquery.calculator.js')}}"></script>	
	<script src="{{ asset('/js/custom.js') }}"></script>

	<script src="{{ asset('/js/jquery.alphanum.js')}}"></script>
	<script src="{{ asset('/js/commonvalidation.js')}}"></script>

	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<script type="text/javascript">
		$(document).ready(function(){
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
		$('.calendar').datepicker({ dateFormat: "dd/mm/yy"});
                
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

        /*$('.dateRange').datetimepicker({
            format: 'yyyy-mm-dd',
            weekStart: 1,
            todayBtn:  1,
            autoclose: 1,
            //todayHighlight: 1,
            startView: 2,
            forceParse: 0,
            minView:2,
            maxView: 1,
            showMeridian: 1
          });*/
        $('.dateRange').datepicker({ dateFormat: "yy-mm-dd"});
	    //using datepicker as timepicker
	    
		$('.timepicker').datetimepicker({
		
			 format: 'h:ii:ss',
             autoclose: true,
             showMeridian: false,
             startView: 1,
             maxView: 1
		       
			});
		
		$('.selectpicker').change(function(){
			$(this).selectpicker('refresh');
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
			$(".searchSubmit").click(function(){
				$(this).closest("form").submit();
			});

			/*$(".left-bar").click(function(){
				$(".main-right").animate({right: "-100%"}, 500);
				if($(".left-bar").hasClass("animateclass")){	
					$(".main-left").animate({left: "-100%"}, 500);
					$(".left-bar").removeClass("animateclass");
				}else{
					$(".main-left").animate({left: "0"}, 500).css("display", "block");
					$(".left-bar").addClass("animateclass");
				}
				
			});
			
			$(".right-bar").click(function(){
				$(".main-left").animate({left: "-100%"}, 500);
				if($(".right-bar").hasClass("animateclass1")){	
					$(".main-right").animate({right: "-100%"}, 500);
					$(".right-bar").removeClass("animateclass1");
				}else{
					$(".main-right").animate({right: "0"}, 500).css("display", "block");
					$(".right-bar").addClass("animateclass1");
				}
			});*/

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
				max: 3500,
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

		
		
	});


		function outputUpdate(vol) {
			document.querySelector('#volume').value = vol;
		}
	</script>	
        
        @if(Session::get ( 'service_id' )==ROAD_INTRACITY)
        <script  src="{{ asset('/js/intracity_buyer.js') }}"></script>
        @endif
	<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
	<!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
	<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
		<script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
	<![endif]-->
	
	
	
	
	<script type="text/javascript">
	$(document).ready(function(){
	$(".dropdown_data ul li.inner-dropdown").click(function(){
	    	$(this).toggleClass("active");
		    $(this).find(".fa-caret-down").hide();
		    $(this).find(".fa-caret-right").show();
		    $(this).find("ol").slideToggle();
	    });
	    });
	    </script>	

</head>
<body>
@include('partials.header')
<div class="clearfix"></div>

<div class="main-container">	

@yield('content')
</div>
	
	

	
<script src="{{ asset('/js/jquery.validate.min.js') }}"></script>

	<meta name="csrf-token" content="{{ csrf_token() }}" />
	<script type="text/javascript">
		$(document).ready(function(){
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$(".login-js").click(function(){

				$(".login-slide").fadeToggle(500);
			});
		});
	</script>
	
	
</body>
</html>