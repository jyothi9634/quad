@extends('default')

@section('content')
<!-- LeftNav Content Starts Here -->
{{--*/ $routeName = strtolower(substr(class_basename(Route::currentRouteAction()), (strpos(class_basename(Route::currentRouteAction()), '@') + 1))) /*--}}
@inject('common', 'App\Components\CommonComponent')
{{--*/ $strUrl = '' /*--}}
@for($i = 0; $i <= count(Request::segments()); $i++)
{{--*/ $strUrl .= Request::segment($i)."/" /*--}}
@endfor

@if($routeName == "sellerpostdetails" || $routeName == "sellerpostslist" 
    || $routeName == "updateseller" || $routeName == "ptlupdatesellerpost")
	{{--*/ $strUrl = "/sellerlist" /*--}}
@endif
@if($routeName == "getpostbuyercounteroffer" || $routeName == "editbuyerquote")
	{{--*/ $strUrl = "/buyerposts" /*--}}
@endif
@if($routeName =="showdetails" || $routeName =="consignmentpickup")
	{{--*/ $strUrl = "/orders/seller_orders" /*--}}
@endif
@if($routeName =="buyerordershowdetails")
	{{--*/ $strUrl = "/orders/buyer_orders" /*--}}
@endif
@if($routeName =="sellersearchresults")
	{{--*/ $strUrl = "/sellersearchbuyers" /*--}}
@endif
@if($routeName =="buyersearchresults")
	{{--*/ $strUrl = "/buyersearch" /*--}}
@endif
@if($routeName == "viewzone" || $routeName == "viewtier" 
    || $routeName == "viewtransitmatrix" || $routeName == "viewsector"
    || $routeName == "viewpincode" || $routeName == "viewaddpincode")
    {{--*/ $strUrl = "/sellerlist" /*--}}
@endif

@if($routeName =="index" &&  (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER))
	{{--*/ $strUrl = "/sellersearchbuyers" /*--}}
@endif
@if($routeName =="index" &&  (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
	{{--*/ $strUrl = "/buyersearch" /*--}}
@endif


@include('partials.page_top_navigation')
<div class="clearfix"></div>
<div class="main">
	<div class="container">
	
		@if (Session::has('edit_success_message')  && Session::get('edit_success_message')!='')
	
	<div class="text-success col-sm-12 text-center flash-txt-new alert-success">
	{{ Session::get('edit_success_message') }}
	</div>
	@endif
	@if (Session::has('status')  && Session::get('status')!='')
	<div class="flash">
	
	<div class="text-success col-sm-12 text-center flash-txt alert-success">
	{{ Session::get('status') }}
	</div></div>
	
	@endif
	<div class="clearfix"></div>
				<div class="home-block">
					<div class="tabs">
						<ul class="nav nav-tabs">
						    <li class="active"><a data-toggle="tab" href="#search" onclick="setHomeBreadCrumb('search','buyer')"><i class="fa fa-search"></i> Search &amp; Book</a></li>
						    <li><a data-toggle="tab" href="#post" onclick="setHomeBreadCrumb('post','buyer')"><i class="fa fa-envelope-o"></i> Post &amp; Get Quote</a></li>
						    <li><a data-toggle="tab" href="#market" onclick="setHomeBreadCrumb('market','buyer')"><i class="fa fa-rss"></i> Market Feeds</a></li>
						    
	  					</ul>
						  <div class="tab-content">
						  <p class="crum"><span class="red">Select Service {{$routeName}}</span><i class="fa fa-angle-right"></i><span>Search &amp; Book</span><i class="fa fa-angle-right"></i><span>Manage Orders</span></p>
							    <div id="search" class="tab-pane fade in active">
								    <div class="home-menu">
									    <h3>Transportation</h3>

									    <div class="service-div-block">
											<div class="service-div">Road</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(1,'search')">
										    		<img src="images/log-icons/FTL.png" title="{{FTL_IMAGE_TITLE}}"/>
										    		FTL
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(2,'search')">
										    		<img src="images/log-icons/LTL.png" title="{{LTL_IMAGE_TITLE}}"/>
										    		LTL
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
										    <div class="service-div">Rail</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(6,'search')">
										    		<img src="images/log-icons/rail.png" title="{{RAIL_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
											<div class="service-div">Air</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(7,'search')">
										    		<img src="images/log-icons/air_dom.png" title="{{AIRDOM_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(8,'search')">
										    		<img src="images/log-icons/air_intl.png" title="{{AIRINT_IMAGE_TITLE}}"/>
										    		Intl
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
										    <div class="service-div">Ocean</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(9,'search')">
										    		<img src="images/log-icons/ocean.png" title="{{OCEAN_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
										    <div class="service-div">Intracity</div>
										    <div id="hyper_local" class="service-div service-icon-div checkserviceseller">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}"/>
										    		Hyper Local
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin('','search')">
										    		<img src="images/log-icons/intracity.png" title="{{INTRACITY_IMAGE_TITLE}}"/>
										    		Intracity
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
										    <div class="service-div">Courier</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(21,'search')">
										    		<img src="images/log-icons/courier.png" title="{{COURIER_IMAGE_TITLE}}"/>
										    	</a>
										    </div>
										</div>
								    </div>
								     <div class="home-menu">
									    <h3>Vehicle</h3>

									    <div class="service-div-block">
											<div class="service-div">Vehicle</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(4,'search')">
										    		<img src="images/log-icons/truck_haul.png" title="{{TRUCK_HAUL_IMAGE_TITLE}}" />
										    		Haul
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(5,'search')">
										    		<img src="images/log-icons/truck_lease.png" title="{{TRUCK_LEASE_IMAGE_TITLE}}"/>
										    		Lease
										    	</a>
										    </div>	
										</div>

								    </div>
								     <div class="home-menu">
									    <h3>Relocation</h3>
									    
									    <div class="service-div-block">
											<div class="service-div">Home</div>
										    <div class="service-div service-icon-div">
										    	<a href="#" onclick="return homeServiceLogin(15,'search')">
										    		<img src="images/log-icons/domestic.png" title="{{RELOCATION_DOMESTIC_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a href="javascript:void(0)" onclick="return homeServiceLogin({{RELOCATION_PET_MOVE}},'search')">
										    		<img src="images/log-icons/pet_move.png" title="{{RELOCATION_PETMOVE_IMAGE_TITLE}}"/>
										    		Pet Move
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
											<div class="service-div"><span class="invisible_text">Home</span></div>
										    <div class="service-div service-icon-div">
										    	<a href="javascript:void(0)" onclick="return homeServiceLogin({{RELOCATION_INTERNATIONAL}},'search')">
										    		<img src="images/log-icons/international.png" title="{{RELOCATION_INTERNATIONAL_IMAGE_TITLE}}"/>
										    		International
										    	</a>
										    </div>
										    <div class="service-div service-icon-div ">
										    	<a href="#">
										    		<img src="images/log-icons/global_mobility.png" title="{{RELOCATION_GLOBAL_MOBILITY_IMAGE_TITLE}}"/>
										    		Global Mobility
										    	</a>
										    </div>	
										</div>

	

										<div class="service-div-block">
										    <div class="service-div">Office</div>
										    <div class="service-div service-icon-div ">
										    	<a href="javascript:void(0)" onclick="return homeServiceLogin({{RELOCATION_OFFICE_MOVE}},'search')">
										    		<img src="images/log-icons/office_domestic.png" title="{{RELOCATION_OFFICE_DOMESTIC_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										</div>

								    </div>
 								     <div class="home-menu non_link">
 									    <h3>Up Coming Services</h3>
 									    <p><a >Multi Modal</a></p>
 									    <p><a >Warehouse</a></p>
 										<p><a >Handling Services</a></p>
 										<p><a >Packaging Services</a></li>
 										<p><a >Equipment Lease</a></li>
 										<p><a >3rd Party Logistics</a></p>
 										<p><a >Shipping and Marine</a></p>
 									    <p><a >Speciality Logistics</a></p>
 								    </div>
								     
							    </div>
							    <div id="post" class="tab-pane fade">
								    <div class="home-menu">
									    <h3>Transportation</h3>
									    <div class="service-div-block">
											<div class="service-div">Road</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(1,'post')">
										    		<img src="images/log-icons/FTL.png" title="{{FTL_IMAGE_TITLE}}"/>
										    		FTL
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(2,'post')">
										    		<img src="images/log-icons/LTL.png" title="{{LTL_IMAGE_TITLE}}"/>
										    		LTL
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
										    <div class="service-div">Rail</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(6,'post')">
										    		<img src="images/log-icons/rail.png" title="{{RAIL_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
											<div class="service-div">Air</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(7,'post')">
										    		<img src="images/log-icons/air_dom.png" title="{{AIRDOM_IMAGE_TITLE}}" />
										    		Domestic
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(8,'post')">
										    		<img src="images/log-icons/air_intl.png" title="{{AIRINT_IMAGE_TITLE}}" />
										    		Intl
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
										    <div class="service-div">Ocean</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(9,'post')">
										    		<img src="images/log-icons/ocean.png" title="{{OCEAN_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
										    <div class="service-div">Intracity</div>
										    <div id="hyper_local" class="service-div service-icon-div">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}" />
										    		Hyper Local
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin('','post')">
										    		<img src="images/log-icons/intracity.png" title="{{INTRACITY_IMAGE_TITLE}}"/>
										    		Intracity
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
										    <div class="service-div">Courier</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(21,'post')">
										    		<img src="images/log-icons/courier.png" title="{{COURIER_IMAGE_TITLE}}"/>
										    	</a>
										    </div>
										</div>

								    </div>
								     <div class="home-menu">
									    <h3>Vehicle</h3>
									    <div class="service-div-block">
											<div class="service-div">Vehicle</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(4,'post')">
										    		<img src="images/log-icons/truck_haul.png" title="{{TRUCK_HAUL_IMAGE_TITLE}}"/>
										    		Haul
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(5,'post')">
										    		<img src="images/log-icons/truck_lease.png" title="{{TRUCK_LEASE_IMAGE_TITLE}}" />
										    		Lease
										    	</a>
										    </div>	
										</div>
								    </div>
								     <div class="home-menu">
									    <h3>Relocation</h3>
									    <div class="service-div-block">
											<div class="service-div">Home</div>
										    <div class="service-div service-icon-div">
										    	<a href="#" onclick="return homeServiceLogin(15,'search')">
										    		<img src="images/log-icons/domestic.png" title="{{RELOCATION_DOMESTIC_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a href="javascript:void(0)" onclick="return homeServiceLogin({{RELOCATION_PET_MOVE}},'search')">
										    		<img src="images/log-icons/pet_move.png" title="{{RELOCATION_PETMOVE_IMAGE_TITLE}}"/>
										    		Pet Move
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
											<div class="service-div"><span class="invisible_text">Home</span></div>
										    <div class="service-div service-icon-div">
										    	<a href="javascript:void(0)" onclick="return homeServiceLogin({{RELOCATION_INTERNATIONAL}},'search')">
										    		<img src="images/log-icons/international.png" title="{{RELOCATION_INTERNATIONAL_IMAGE_TITLE}}"/>
										    		International
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a href="#">
										    		<img src="images/log-icons/global_mobility.png" title="{{RELOCATION_GLOBAL_MOBILITY_IMAGE_TITLE}}"/>
										    		Global Mobility
										    	</a>
										    </div>	
										</div>

	

										<div class="service-div-block">
										    <div class="service-div">Office</div>
										    <div class="service-div service-icon-div ">
										    	<a href="javascript:void(0)" onclick="return homeServiceLogin({{RELOCATION_OFFICE_MOVE}},'search')">
										    		<img src="images/log-icons/office_domestic.png" title="{{RELOCATION_OFFICE_DOMESTIC_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										</div>
								    </div>
								     <div class="home-menu non_link">
 									    <h3>Up Coming Services</h3>
 									    <p><a >Multi Modal</a></p>
 									    <p><a >Warehouse</a></p>
 										<p><a >Handling Services</a></p>
 										<p><a >Packaging Services</a></li>
 										<p><a >Equipment Lease</a></li>
 										<p><a >3rd Party Logistics</a></p>
 										<p><a >Shipping and Marine</a></p>
 									    <p><a >Speciality Logistics</a></p>
 								    </div>
							    </div>
							    <div id="market" class="tab-pane fade">
								     <div class="home-menu">
									    <h3>Transportation</h3>
									    <div class="service-div-block">
											<div class="service-div">Road</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(1,'search')">
										    		<img src="images/log-icons/FTL.png" title="{{FTL_IMAGE_TITLE}}"/>
										    		FTL
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(2,'search')">
										    		<img src="images/log-icons/LTL.png" title="{{LTL_IMAGE_TITLE}}"/>
										    		LTL
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
										    <div class="service-div">Rail</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(6,'search')">
										    		<img src="images/log-icons/rail.png" title="{{RAIL_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
											<div class="service-div">Air</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(7,'search')">
										    		<img src="images/log-icons/air_dom.png" title="{{AIRDOM_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(8,'search')">
										    		<img src="images/log-icons/air_intl.png" title="{{AIRINT_IMAGE_TITLE}}" />
										    		Intl
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
										    <div class="service-div">Ocean</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(9,'search')">
										    		<img src="images/log-icons/ocean.png" title="{{OCEAN_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
										    <div class="service-div">Intracity</div>
										    <div id="hyper_local" class="service-div service-icon-div">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}"/>
										    		Hyper Local
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin('','search')">
										    		<img src="images/log-icons/intracity.png" title="{{INTRACITY_IMAGE_TITLE}}"/>
										    		Intracity
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
										    <div class="service-div">Courier</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(21,'search')">
										    		<img src="images/log-icons/courier.png" title="{{COURIER_IMAGE_TITLE}}"/>
										    	</a>
										    </div>
										</div>
								    </div>
								     <div class="home-menu">
									    <h3>Vehicle</h3>
									    <div class="service-div-block">
											<div class="service-div">Vehicle</div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(4,'search')">
										    		<img src="images/log-icons/truck_haul.png" title="{{TRUCK_HAUL_IMAGE_TITLE}}"/>
										    		Haul
										    	</a>
										    </div>
										    <div class="service-div service-icon-div">
										    	<a onclick="homeServiceLogin(5,'search')">
										    		<img src="images/log-icons/truck_lease.png" title="{{TRUCK_LEASE_IMAGE_TITLE}}"/>
										    		Lease
										    	</a>
										    </div>	
										</div>
								    </div>
								     <div class="home-menu">
									    <h3>Relocation</h3>
									    <div class="service-div-block">
											<div class="service-div">Home</div>
										    <div class="service-div service-icon-div">
										    	<a href="#" onclick="return homeServiceLogin(15,'search')">
										    		<img src="images/log-icons/domestic.png" title="{{RELOCATION_DOMESTIC_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										    <div class="service-div service-icon-div ">
										    	<a href="#" onclick="return homeServiceLogin(17,'search')">
										    		<img src="images/log-icons/pet_move.png" title="{{RELOCATION_PETMOVE_IMAGE_TITLE}}"/>
										    		Pet Move
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
											<div class="service-div"><span class="invisible_text">Home</span></div>
										    <div class="service-div service-icon-div ">
										    	<a href="#" onclick="return homeServiceLogin(18,'search')">
										    		<img src="images/log-icons/international.png" title="{{RELOCATION_INTERNATIONAL_IMAGE_TITLE}}"/>
										    		International
										    	</a>
										    </div>
										    <div class="service-div service-icon-div ">
										    	<a href="#">
										    		<img src="images/log-icons/global_mobility.png" title="{{RELOCATION_GLOBAL_MOBILITY_IMAGE_TITLE}}"/>
										    		Global Mobility
										    	</a>
										    </div>	
										</div>

	

										<div class="service-div-block">
										    <div class="service-div">Office</div>
										    <div class="service-div service-icon-div ">
										    	<a href="#" onclick="return homeServiceLogin(20,'search')">
										    		<img src="images/log-icons/office_domestic.png" title="{{RELOCATION_OFFICE_DOMESTIC_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										</div>
								    </div>
 								     <div class="home-menu non_link">
 									    <h3>Up Coming Services</h3>
 									    <p><a >Multi Modal</a></p>
 									    <p><a >Warehouse</a></p>
 										<p><a >Handling Services</a></p>
 										<p><a >Packaging Services</a></li>
 										<p><a >Equipment Lease</a></li>
 										<p><a >3rd Party Logistics</a></p>
 										<p><a >Shipping and Marine</a></p>
 									    <p><a >Speciality Logistics</a></p>
 								    </div>
							    </div>
						  </div>
					</div>
					
				</div>
				
						
					</div>
				</div>
			<div class="clearfix"></div>
 @include('partials.footer')	
  </div>			
@endsection