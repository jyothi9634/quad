@extends('app')

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

@if($routeName =="index" && (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER))
	{{--*/ $strUrl = "/sellersearchbuyers" /*--}}
@endif
@if($routeName =="index" && (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
	{{--*/ $strUrl = "/buyersearch" /*--}}
@endif
@inject('commonComponent', 'App\Components\CommonComponent')
{{--*/ $SellerServiceExits=$commonComponent->getSellerServiceExits() /*--}}
<?php
	/*echo "<pre>";
	print_r($SellerServiceExits);
	exit;*/
?>

@include('partials.page_top_navigation')
<div class="clearfix"></div>
<div class="main">
	<div class="container">
	
	<div id="flash-txt" class="text-success col-sm-12 text-center displayNone margin-bottom">
	
	</div>
	<div class="clearfix"></div>
	
@if (Session::has('edit_success_message')  && Session::get('edit_success_message')!='')
	<div class="flash">
	<p class="text-success col-sm-12 text-center flash-txt alert-success">
	{{ Session::get('edit_success_message') }}
	</p>
</div>
@endif
<!-- FOR PASSWORD RESET NOTIFICATION -->


	@if (Session::has('status')  && Session::get('status')!='')
	<div class="flash">
	
	<div class="text-success col-sm-12 text-center flash-txt alert-success">
	{{ Session::get('status') }}
	</div></div>
	
	
	@endif
<div class="home-block">
				
					<div class="tabs">
						<ul class="nav nav-tabs">
							@if((Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER))
							<li class="active"><a data-toggle="tab" href="#search" onclick="setHomeBreadCrumb('search','seller')"><i class="fa fa-search"></i> Search &amp; Submit Quote</a></li>
						    <li><a data-toggle="tab" href="#post" onclick="setHomeBreadCrumb('post','seller')"><i class="fa fa-envelope-o"></i> Post Rate Card</a></li>
						    <li><a data-toggle="tab" href="#market" onclick="setHomeBreadCrumb('market','seller')" onclick="setHomeBreadCrumb('search')"><i class="fa fa-rss"></i> Market Feeds</a></li>
							@elseif((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
							<li class="active"><a data-toggle="tab" href="#search" onclick="setHomeBreadCrumb('search','buyer')"><i class="fa fa-search"></i> Search &amp; Book</a></li>
						    <li><a data-toggle="tab" href="#post" onclick="setHomeBreadCrumb('post','buyer')"><i class="fa fa-envelope-o"></i> Post &amp; Get Quote</a></li>
						    <li><a data-toggle="tab" href="#market" onclick="setHomeBreadCrumb('market','buyer')"><i class="fa fa-rss"></i> Market Feeds</a></li>
						    @endif
						    
						    
	  					</ul>
						  <div class="tab-content">
						  @if((Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER))
						  <p class="crum"><span class="red">Select Service</span><i class="fa fa-angle-right"></i><span>Search &amp; Submit Quote</span><i class="fa fa-angle-right"></i><span>Manage Orders</span></p>
						  @elseif((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
						  <p class="crum"><span class="red">Select Service</span><i class="fa fa-angle-right"></i><span>Search &amp; Book</span><i class="fa fa-angle-right"></i><span>Manage Orders</span></p>

						  @endif
							    <div id="search" class="tab-pane fade in active">
								    <div class="home-menu">
									    <h3>Transportation</h3>
									    @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))

									    	<div class="service-div-block">
												<div class="service-div">Road</div>
											    <div id="ftl" class="service-div service-icon-div">
											    	<a @if(Session::get('service_id') == ROAD_FTL) class="active-inner" @endif onclick="return buyersetservice(1,'{{$strUrl}}')">
                                                                <img src="images/log-icons/FTL.png" title="{{FTL_IMAGE_TITLE}}"/>
											    		FTL
											    	</a>
											    </div>
											    <div id="ltl" class="service-div service-icon-div">
											    	<a @if(Session::get('service_id') == ROAD_PTL) class="active-inner" @endif  onclick="return buyersetservice(2,'{{$strUrl}}')">
											    		<img src="images/log-icons/LTL.png" title="{{LTL_IMAGE_TITLE}}"/>
											    		LTL
											    	</a>
											    </div>	
											</div>
											<div class="service-div-block">
										    <div class="service-div">Rail</div>
										    <div id="rail" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == RAIL) class="active-inner" @endif onclick="return buyersetservice(6,'{{$strUrl}}')">
										    		<img src="images/log-icons/rail.png" title="{{RAIL_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
											<div class="service-div">Air</div>
										    <div id="airdomestic" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == AIR_DOMESTIC) class="active-inner" @endif onclick="return buyersetservice(7,'{{$strUrl}}')">
										    		<img src="images/log-icons/air_dom.png" title="{{AIRDOM_IMAGE_TITLE}}" />
										    		Domestic
										    	</a>
										    </div>
										    <div id="airinternational" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == AIR_INTERNATIONAL) class="active-inner" @endif onclick="return buyersetservice(8,'{{$strUrl}}')">
										    		<img src="images/log-icons/air_intl.png" title="{{AIRINT_IMAGE_TITLE}}"/>
										    		Intl
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
										    <div class="service-div">Ocean</div>
										    <div id="ocean" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == OCEAN) class="active-inner" @endif onclick="return buyersetservice(9,'{{$strUrl}}')">
										    		<img src="images/log-icons/ocean.png" title="{{OCEAN_IMAGE_TITLE}}"/>
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
											    	<a @if(Session::get('service_id') == ROAD_FTL) class="active-inner" @endif onclick="return subcriptionuserservice(1,'{{$strUrl}}')">
											    		<img src="images/log-icons/FTL.png" title="{{FTL_IMAGE_TITLE}}"/>
											    		FTL
											    	</a>
											    </div>
											    
											    @if (in_array(ROAD_PTL, $SellerServiceExits))
														<div id="ltl" class="service-div service-icon-div">
      													@else
														<div id="ltl" class="service-div service-icon-div checkserviceseller">
 														@endif
											    	<a @if(Session::get('service_id') == ROAD_PTL) class="active-inner" @endif onclick="return subcriptionuserservice(2,'{{$strUrl}}')">
											    		<img src="images/log-icons/LTL.png" title="{{LTL_IMAGE_TITLE}}"/>
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
										    	<a @if(Session::get('service_id') == RAIL) class="active-inner" @endif onclick="return subcriptionuserservice(6,'{{$strUrl}}')">
										    		<img src="images/log-icons/rail.png" title="{{RAIL_IMAGE_TITLE}}"/>
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
										    	<a @if(Session::get('service_id') == AIR_DOMESTIC) class="active-inner" @endif onclick="return subcriptionuserservice(7,'{{$strUrl}}')">
										    		<img src="images/log-icons/air_dom.png" title="{{AIRDOM_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										     			@if (in_array(AIR_INTERNATIONAL, $SellerServiceExits))
														<div id="airinternational" class="service-div service-icon-div">
      													@else
														<div id="airinternational" class="service-div service-icon-div checkserviceseller">
 														@endif
										    	<a @if(Session::get('service_id') == AIR_INTERNATIONAL) class="active-inner" @endif onclick="return subcriptionuserservice(8,'{{$strUrl}}')">
										    		<img src="images/log-icons/air_intl.png" title="{{AIRINT_IMAGE_TITLE}}" />
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
										    	<a @if(Session::get('service_id') == OCEAN) class="active-inner" @endif onclick="return subcriptionuserservice(9,'{{$strUrl}}')">
										    		<img src="images/log-icons/ocean.png" title="{{OCEAN_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>
												
										@endif

										@if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
										<div class="service-div-block">
										    <div class="service-div">Intracity</div>
										    <div id="hyper_local" class="service-div service-icon-div checkserviceseller">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}" />
										    		Hyper Local
										    	</a>
										    </div>
										    <div id="intracity" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == ROAD_INTRACITY) class="active-inner" @endif onclick="return buyersetservice(3,'{{$strUrl}}')">
										    		<img src="images/log-icons/intracity.png" title="{{INTRACITY_IMAGE_TITLE}}"/>
										    		Intracity
										    	</a>
										    </div>
										</div>
										
										@elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )

										<div class="service-div-block">
										    <div class="service-div">Intracity</div>
										    <div id="hyper_local" class="service-div service-icon-div checkserviceseller">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}"/>
										    		Hyper Local
										    	</a>
										    </div>
										    <div class="service-div service-icon-div checkserviceseller">
										    	<a href="javascript:void(0);">
										    		<img src="images/log-icons/intracity.png" title="{{INTRACITY_IMAGE_TITLE}}"/>
										    		Intracity
										    	</a>
										    </div>
										</div>



										@endif	
										@if( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )
										<div class="service-div-block">
										    <div class="service-div">Courier</div>
										    			@if (in_array(COURIER, $SellerServiceExits))
														<div id="courier" class="service-div service-icon-div">
      													@else
														<div id="courier" class="service-div service-icon-div checkserviceseller">
 														@endif
										    	<a @if(Session::get('service_id') == COURIER) class="active-inner" @endif onclick="return subcriptionuserservice(21,'{{$strUrl}}')">
										    		<img src="images/log-icons/courier.png" title="{{COURIER_IMAGE_TITLE}}"/>
										    	</a>										    	
										    </div>
										</div>
										@else
										<div class="service-div-block">
										    <div class="service-div">Courier</div>
										    <div id="courier" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == COURIER) class="active-inner" @endif onclick="return buyersetservice(21,'{{$strUrl}}')">
										    		<img src="images/log-icons/courier.png" title="{{COURIER_IMAGE_TITLE}}"/>
										    	</a>										    	
										    </div>
										</div>
										@endif
								    </div>
								     <div class="home-menu">
									    <h3>Vehicle</h3>

									    <div class="service-div-block">
											<div class="service-div">Vehicle</div>
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

											        <a @if(Session::get('service_id') == ROAD_TRUCK_HAUL) class="active-inner" @endif onclick="return buyersetservice(4,'{{$strUrl}}')">
											    		<img src="images/log-icons/truck_haul.png" title="{{TRUCK_HAUL_IMAGE_TITLE}}"/>
											    		Haul
											    	</a>
											    @elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )
													<a @if(Session::get('service_id') == ROAD_TRUCK_HAUL) class="active-inner" @endif onclick="return subcriptionuserservice(4,'{{$strUrl}}')">
											    		<img src="images/log-icons/truck_haul.png" title="{{TRUCK_HAUL_IMAGE_TITLE}}"/>
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

													<a @if(Session::get('service_id') == ROAD_TRUCK_LEASE) class="active-inner" @endif onclick="return buyersetservice(5,'{{$strUrl}}')">
											    		<img src="images/log-icons/truck_lease.png" title="{{TRUCK_LEASE_IMAGE_TITLE}}"/>
											    		Lease
											    	</a>
											    @elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )
													<a @if(Session::get('service_id') == ROAD_TRUCK_LEASE) class="active-inner" @endif onclick="return subcriptionuserservice(5,'{{$strUrl}}')">
											    		<img src="images/log-icons/truck_lease.png" title="{{TRUCK_LEASE_IMAGE_TITLE}}"/>
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
										    <a href="javascript:void(0)" onclick="return buyersetservice(15,'{{$strUrl}}')">
										    @else
										    			@if (in_array(RELOCATION_DOMESTIC, $SellerServiceExits))
														<div class="service-div service-icon-div">
      													@else
														<div class="service-div service-icon-div checkserviceseller">
 														@endif
										    <a  onclick="return subcriptionuserservice(15,'{{$strUrl}}')">
										    @endif	
										    		<img src="images/log-icons/domestic.png" title="{{RELOCATION_DOMESTIC_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										    @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
										    <div class="service-div service-icon-div ">
										    <a href="javascript:void(0)" onclick="return buyersetservice({{RELOCATION_PET_MOVE}},'{{$strUrl}}')">
										    @else
										    			@if (in_array(RELOCATION_PET_MOVE, $SellerServiceExits))
														<div class="service-div service-icon-div ">
      													@else
														<div class="service-div service-icon-div checkserviceseller">
 														@endif
										    <a  onclick="return subcriptionuserservice({{RELOCATION_PET_MOVE}},'{{$strUrl}}')">
										    @endif	
										    		<img src="images/log-icons/pet_move.png" title="{{RELOCATION_PETMOVE_IMAGE_TITLE}}"/>
										    		Pet Move
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
											<div class="service-div"><span class="invisible_text">Home</span></div>
										    @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
										    <div class="service-div service-icon-div ">
										    <a href="javascript:void(0)" onclick="return buyersetservice({{RELOCATION_INTERNATIONAL}},'{{$strUrl}}')">
										    @else
										    			@if (in_array(RELOCATION_INTERNATIONAL, $SellerServiceExits))
														<div class="service-div service-icon-div ">
      													@else
														<div class="service-div service-icon-div checkserviceseller">
 														@endif
										    <a  onclick="return subcriptionuserservice({{RELOCATION_INTERNATIONAL}},'{{$strUrl}}')">
										    @endif	
										    		<img src="images/log-icons/international.png" title="{{RELOCATION_INTERNATIONAL_IMAGE_TITLE}}"/>
										    		International
										    	</a>
										    </div>
										@if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
										    <div class="service-div service-icon-div ">
										    	<a href="javascript:void(0)" onclick="return buyersetservice({{RELOCATION_GLOBAL_MOBILITY}},'{{$strUrl}}')">
										    	@else
										    			@if (in_array(RELOCATION_GLOBAL_MOBILITY, $SellerServiceExits))
														<div class="service-div service-icon-div ">
      													@else
														<div class="service-div service-icon-div checkserviceseller">
 														@endif
										    <a  onclick="return subcriptionuserservice({{RELOCATION_GLOBAL_MOBILITY}},'{{$strUrl}}')">
										    @endif	
												<img src="images/log-icons/global_mobility.png" title="{{RELOCATION_GLOBAL_MOBILITY_IMAGE_TITLE}}"/>
										    		Global Mobility
										    	</a>
										    </div>	
										</div>
										<div class="service-div-block">
										    <div class="service-div">Office</div>
										    @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
										    <div class="service-div service-icon-div ">
										    <a href="javascript:void(0)" onclick="return buyersetservice({{RELOCATION_OFFICE_MOVE}},'{{$strUrl}}')">
										    @else
										    			@if (in_array(RELOCATION_OFFICE_MOVE, $SellerServiceExits))
														<div class="service-div service-icon-div ">
      													@else
														<div class="service-div service-icon-div checkserviceseller">
 														@endif
										    <a  onclick="return subcriptionuserservice({{RELOCATION_OFFICE_MOVE}},'{{$strUrl}}')">
										    @endif	
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
									    @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))

									    	<div class="service-div-block">
												<div class="service-div">Road</div>
											    <div id="ftl" class="service-div service-icon-div">
											    	<a @if(Session::get('service_id') == ROAD_FTL) class="active-inner" @endif onclick="return buyersetservice(1,'/createbuyerquote')">
											    		<img src="images/log-icons/FTL.png" title="{{FTL_IMAGE_TITLE}}"/>
											    		FTL
											    	</a>
											    </div>
											    <div id="ltl" class="service-div service-icon-div">
											    	<a @if(Session::get('service_id') == ROAD_PTL) class="active-inner" @endif  onclick="return buyersetservice(2,'/ptl/createbuyerquote')">
											    		<img src="images/log-icons/LTL.png" title="{{LTL_IMAGE_TITLE}}"/>
											    		LTL
											    	</a>
											    </div>	
											</div>


										<div class="service-div-block">
										    <div class="service-div">Rail</div>
										    <div id="rail" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == RAIL) class="active-inner" @endif onclick="return buyersetservice(6,'/ptl/createbuyerquote')">
										    		<img src="images/log-icons/rail.png" title="{{RAIL_IMAGE_TITLE}}" />
										    		Parcel
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
											<div class="service-div">Air</div>
										    <div id="airdomestic" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == AIR_DOMESTIC) class="active-inner" @endif onclick="return buyersetservice(7,'/ptl/createbuyerquote')">
										    		<img src="images/log-icons/air_dom.png" title="{{AIRDOM_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										    <div id="airinternational" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == AIR_INTERNATIONAL) class="active-inner" @endif onclick="return buyersetservice(8,'/ptl/createbuyerquote')">
										    		<img src="images/log-icons/air_intl.png" title="{{AIRINT_IMAGE_TITLE}}"/>
										    		Intl
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
										    <div class="service-div">Ocean</div>
										    <div id="ocean" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == OCEAN) class="active-inner" @endif onclick="return buyersetservice(9,'/ptl/createbuyerquote')">
										    		<img src="images/log-icons/ocean.png" title="{{OCEAN_IMAGE_TITLE}}"/>
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
											    	<a @if(Session::get('service_id') == ROAD_FTL) class="active-inner" @endif onclick="return subcriptionuserservice(1,'/createseller')">
											    		<img src="images/log-icons/FTL.png" title="{{FTL_IMAGE_TITLE}}"/>
											    		FTL
											    	</a>
											    </div>
											    @if (in_array(ROAD_PTL, $SellerServiceExits))
														<div id="ltl" class="service-div service-icon-div">
      													@else
														<div id="ltl" class="service-div service-icon-div checkserviceseller">
 														@endif
											    	<a @if(Session::get('service_id') == ROAD_PTL) class="active-inner" @endif onclick="return subcriptionuserservice(2,'/ptl/createsellerpost')">
											    		<img src="images/log-icons/LTL.png" title="{{LTL_IMAGE_TITLE}}"/>
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
										    	<a @if(Session::get('service_id') == RAIL) class="active-inner" @endif onclick="return subcriptionuserservice(6,'/ptl/createsellerpost')">
										    		<img src="images/log-icons/rail.png" title="{{RAIL_IMAGE_TITLE}}"/>
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
										    	<a @if(Session::get('service_id') == AIR_DOMESTIC) class="active-inner" @endif onclick="return subcriptionuserservice(7,'/ptl/createsellerpost')">
										    		<img src="images/log-icons/air_dom.png" title="{{AIRDOM_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										    @if (in_array(AIR_INTERNATIONAL, $SellerServiceExits))
														<div id="airinternational" class="service-div service-icon-div">
      													@else
														<div id="airinternational" class="service-div service-icon-div checkserviceseller">
 														@endif
										    	<a @if(Session::get('service_id') == AIR_INTERNATIONAL) class="active-inner" @endif onclick="return subcriptionuserservice(8,'/ptl/createsellerpost')">
										    		<img src="images/log-icons/air_intl.png" title="{{AIRINT_IMAGE_TITLE}}"/>
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
										    	<a @if(Session::get('service_id') == OCEAN) class="active-inner" @endif onclick="return subcriptionuserservice(9,'/ptl/createsellerpost')">
										    		<img src="images/log-icons/ocean.png" title="{{OCEAN_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>

												
										@endif

										
										@if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))

										<div class="service-div-block">
										    <div class="service-div">Intracity</div>
										    <div id="hyper_local" class="service-div service-icon-div checkserviceseller">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}" />
										    		Hyper Local
										    	</a>
										    </div>
										    <div id="intracity" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == ROAD_INTRACITY) class="active-inner" @endif onclick="return buyersetservice(3,'/intracity/buyer_post')">
										    		<img src="images/log-icons/intracity.png" title="{{INTRACITY_IMAGE_TITLE}}"/>
										    		Intracity
										    	</a>
										    </div>
										</div>


										@elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )

										<div class="service-div-block">
										    <div class="service-div">Intracity</div>
										    <div id="hyper_local" class="service-div service-icon-div checkserviceseller">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}"/>
										    		Hyper Local
										    	</a>
										    </div>
										    <div class="service-div service-icon-div checkserviceseller">
										    	<a href="javascript:void(0);">
										    		<img src="images/log-icons/intracity.png" title="{{INTRACITY_IMAGE_TITLE}}"/>
										    		Intracity
										    	</a>
										    </div>
										</div>



										@endif	
										@if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
									    <div class="service-div-block">
										    <div class="service-div">Courier</div>
										    <div id="courier" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == COURIER) class="active-inner" @endif onclick="return buyersetservice(21,'/ptl/createbuyerquote')">
										    		<img src="images/log-icons/courier.png" title="{{COURIER_IMAGE_TITLE}}"/>
										    	</a>
										    </div>
										</div>
										@elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )
										 <div class="service-div-block">
										    <div class="service-div">Courier</div>
										    @if (in_array(COURIER, $SellerServiceExits))
														<div id="courier" class="service-div service-icon-div">
      													@else
														<div id="courier" class="service-div service-icon-div checkserviceseller">
 														@endif
										    	<a @if(Session::get('service_id') == COURIER) class="active-inner" @endif onclick="return subcriptionuserservice(21,'/ptl/createsellerpost')">
										    		<img src="images/log-icons/courier.png" title="{{COURIER_IMAGE_TITLE}}"/>
										    	</a>
										    </div>
										</div>
										@endif	

								    </div>
								     <div class="home-menu">
									    <h3>Vehicle</h3>
										<div class="service-div-block">
											<div class="service-div">Vehicle</div>
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

											        <a @if(Session::get('service_id') == ROAD_TRUCK_HAUL) class="active-inner" @endif onclick="return buyersetservice(4,'/truckhaul/createbuyerquote')">
											    		<img src="images/log-icons/truck_haul.png" title="{{TRUCK_HAUL_IMAGE_TITLE}}"/>
											    		Haul
											    	</a>
											    @elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )
													<a @if(Session::get('service_id') == ROAD_TRUCK_HAUL) class="active-inner" @endif onclick="return subcriptionuserservice(4,'/truckhaul/createsellerpost')">
											    		<img src="images/log-icons/truck_haul.png" title="{{TRUCK_HAUL_IMAGE_TITLE}}"/>
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

													<a @if(Session::get('service_id') == ROAD_TRUCK_LEASE) class="active-inner" @endif onclick="return buyersetservice(5,'/trucklease/createbuyerquote')">
											    		<img src="images/log-icons/truck_lease.png" title="{{TRUCK_LEASE_IMAGE_TITLE}}"/>
											    		Lease
											    	</a>
											    @elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )
													<a @if(Session::get('service_id') == ROAD_TRUCK_LEASE) class="active-inner" @endif onclick="return subcriptionuserservice(5,'/trucklease/createsellerpost')">
											    		<img src="images/log-icons/truck_lease.png" title="{{TRUCK_LEASE_IMAGE_TITLE}}"/>
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
										    		<a onclick="return buyersetservice(15,'/relocation/creatbuyerrpost')">
										    	@else
										    		@if (in_array(RELOCATION_DOMESTIC, $SellerServiceExits))
														<div class="service-div service-icon-div">
      													@else
														<div class="service-div service-icon-div checkserviceseller">
 														@endif
										    		<a onclick="return subcriptionuserservice(15,'/relocation/createsellerpost')">
										    	@endif		
										    		<img src="images/log-icons/domestic.png" title="{{RELOCATION_DOMESTIC_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
									    	@if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
									    		<div class="service-div service-icon-div">
									    		<a onclick="return buyersetservice({{RELOCATION_PET_MOVE}},'/relocation/creatbuyerrpost')">
									    	@else
									    		@if (in_array(RELOCATION_PET_MOVE, $SellerServiceExits))
													<div class="service-div service-icon-div">
  													@else
													<div class="service-div service-icon-div checkserviceseller">
														@endif
									    		<a onclick="return subcriptionuserservice({{RELOCATION_PET_MOVE}},'/relocation/createsellerpost')">
									    	@endif		
										    		<img src="images/log-icons/pet_move.png" title="{{RELOCATION_PETMOVE_IMAGE_TITLE}}"/>
										    		Pet Move
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
											<div class="service-div"><span class="invisible_text">Home</span></div>
									    	@if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
									    		<div class="service-div service-icon-div">
									    		<a onclick="return buyersetservice({{RELOCATION_INTERNATIONAL}},'/relocation/creatbuyerrpost')">
									    	@else
									    		@if (in_array(RELOCATION_INTERNATIONAL, $SellerServiceExits))
													<div class="service-div service-icon-div">
  													@else
													<div class="service-div service-icon-div checkserviceseller">
														@endif
									    		<a onclick="return subcriptionuserservice({{RELOCATION_INTERNATIONAL}},'/relocation/createsellerpost')">
									    	@endif		
										    		<img src="images/log-icons/international.png" title="{{RELOCATION_INTERNATIONAL_IMAGE_TITLE}}"/>
										    		International
										    	</a>
										    </div>
										    @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
										    <div class="service-div service-icon-div ">
										      <a onclick="return buyersetservice({{RELOCATION_GLOBAL_MOBILITY}},'/relocation/creatbuyerrpost')">
										      @else
									    		@if (in_array(RELOCATION_GLOBAL_MOBILITY, $SellerServiceExits))
													<div class="service-div service-icon-div">
  													@else
													<div class="service-div service-icon-div checkserviceseller">
														@endif
									    		<a onclick="return subcriptionuserservice({{RELOCATION_GLOBAL_MOBILITY}},'/relocation/createsellerpost')">
									    	@endif		
										    		<img src="images/log-icons/global_mobility.png" title="{{RELOCATION_GLOBAL_MOBILITY_IMAGE_TITLE}}"/>
										    		Global Mobility
										    	</a>
										    </div>	
										</div>
										<div class="service-div-block">
										    <div class="service-div">Office</div>
										    	@if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
										    		<div class="service-div service-icon-div">
										    		<a onclick="return buyersetservice({{RELOCATION_OFFICE_MOVE}},'/relocation/creatbuyerrpost')">
										    	@else
										    		@if (in_array(RELOCATION_OFFICE_MOVE, $SellerServiceExits))
														<div class="service-div service-icon-div">
      													@else
														<div class="service-div service-icon-div checkserviceseller">
 														@endif
										    		<a onclick="return subcriptionuserservice({{RELOCATION_OFFICE_MOVE}},'/relocation/createsellerpost')">
										    	@endif		
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
									    @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))


									    <div class="service-div-block">
												<div class="service-div">Road</div>
											    <div id="ftl" class="service-div service-icon-div">
											    	<a @if(Session::get('service_id') == ROAD_FTL) class="active-inner" @endif onclick="return buyersetservice(1,'{{$strUrl}}')">
											    		<img src="images/log-icons/FTL.png" title="{{FTL_IMAGE_TITLE}}"/>
											    		FTL
											    	</a>
											    </div>
											    <div id="ltl" class="service-div service-icon-div">
											    	<a @if(Session::get('service_id') == ROAD_PTL) class="active-inner" @endif  onclick="return buyersetservice(2,'{{$strUrl}}')">
											    		<img src="images/log-icons/LTL.png" title="{{LTL_IMAGE_TITLE}}"/>
											    		LTL
											    	</a>
											    </div>	
											</div>
											<div class="service-div-block">
										    <div class="service-div">Rail</div>
										    <div id="rail" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == RAIL) class="active-inner" @endif onclick="return buyersetservice(6,'{{$strUrl}}')">
										    		<img src="images/log-icons/rail.png" title="{{RAIL_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>

										<div class="service-div-block">
											<div class="service-div">Air</div>
										    <div id="airdomestic" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == AIR_DOMESTIC) class="active-inner" @endif onclick="return buyersetservice(7,'{{$strUrl}}')">
										    		<img src="images/log-icons/air_dom.png" title="{{AIRDOM_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										    <div id="airinternational" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == AIR_INTERNATIONAL) class="active-inner" @endif onclick="return buyersetservice(8,'{{$strUrl}}')">
										    		<img src="images/log-icons/air_intl.png" title="{{AIRINT_IMAGE_TITLE}}"/>
										    		Intl
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
										    <div class="service-div">Ocean</div>
										    <div id="ocean" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == OCEAN) class="active-inner" @endif onclick="return buyersetservice(9,'{{$strUrl}}')">
										    		<img src="images/log-icons/ocean.png" title="{{OCEAN_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>
											
										@elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER))

										<div class="service-div-block">
												<div class="service-div">Road</div>
											    @if (in_array(ROAD_FTL, $SellerServiceExits))
											<div id="ftl" class="service-div service-icon-div">
      										@else
											<div id="ftl" class="service-div service-icon-div checkserviceseller">
 											@endif
											    	<a @if(Session::get('service_id') == ROAD_FTL) class="active-inner" @endif onclick="return subcriptionuserservice(1,'{{$strUrl}}')">
											    		<img src="images/log-icons/FTL.png" title="{{FTL_IMAGE_TITLE}}"/>
											    		FTL
											    	</a>
											    </div>
											     @if (in_array(ROAD_PTL, $SellerServiceExits))
														<div id="ltl" class="service-div service-icon-div">
      													@else
														<div id="ltl" class="service-div service-icon-div checkserviceseller">
 														@endif
											    	<a @if(Session::get('service_id') == ROAD_PTL) class="active-inner" @endif onclick="return subcriptionuserservice(2,'{{$strUrl}}')">
											    		<img src="images/log-icons/LTL.png" title="{{LTL_IMAGE_TITLE}}" />
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
										    	<a @if(Session::get('service_id') == RAIL) class="active-inner" @endif onclick="return subcriptionuserservice(6,'{{$strUrl}}')">
										    		<img src="images/log-icons/rail.png" title="{{RAIL_IMAGE_TITLE}}"/>
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
										    	<a @if(Session::get('service_id') == AIR_DOMESTIC) class="active-inner" @endif onclick="return subcriptionuserservice(7,'{{$strUrl}}')">
										    		<img src="images/log-icons/air_dom.png" title="{{AIRDOM_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
										    @if (in_array(AIR_INTERNATIONAL, $SellerServiceExits))
														<div id="airinternational" class="service-div service-icon-div">
      													@else
														<div id="airinternational" class="service-div service-icon-div checkserviceseller">
 														@endif
										    	<a @if(Session::get('service_id') == AIR_INTERNATIONAL) class="active-inner" @endif onclick="return subcriptionuserservice(8,'{{$strUrl}}')">
										    		<img src="images/log-icons/air_intl.png" title="{{AIRINT_IMAGE_TITLE}}"/>
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
										    	<a @if(Session::get('service_id') == OCEAN) class="active-inner" @endif onclick="return subcriptionuserservice(9,'{{$strUrl}}')">
										    		<img src="images/log-icons/ocean.png" title="{{OCEAN_IMAGE_TITLE}}"/>
										    		Parcel
										    	</a>
										    </div>
										</div>

										@endif


										

										@if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))

										<div class="service-div-block">
										    <div class="service-div">Intracity</div>
										    <div id="hyper_local" class="service-div service-icon-div checkserviceseller">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}"/>
										    		Hyper Local
										    	</a>
										    </div>
										    <div id="intracity" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == ROAD_INTRACITY) class="active-inner" @endif onclick="return buyersetservice(3,'{{$strUrl}}')">
										    		<img src="images/log-icons/intracity.png" title="{{INTRACITY_IMAGE_TITLE}}"/>
										    		Intracity
										    	</a>
										    </div>
										</div>

										@elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )

										<div class="service-div-block">
										    <div class="service-div">Intracity</div>
										    <div id="hyper_local" class="service-div service-icon-div checkserviceseller">
										    	<a href="#">
										    		<img src="{{url('images/log-icons/hyper_local.png')}}" title="{{INTRACITY_HYPERLOCAL_IMAGE_TITLE}}"/>
										    		Hyper Local
										    	</a>
										    </div>	
										    <div class="service-div service-icon-div checkserviceseller">
										    	<a href="javascript:void(0);">
										    		<img src="images/log-icons/intracity.png" title="{{INTRACITY_IMAGE_TITLE}}"/>
										    		Intracity
										    	</a>
										    </div>
										</div>

										@endif	
										@if( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )
									    <div class="service-div-block">
										    <div class="service-div">Courier</div>
										    @if (in_array(COURIER, $SellerServiceExits))
														<div id="courier" class="service-div service-icon-div">
      													@else
														<div id="courier" class="service-div service-icon-div checkserviceseller">
 														@endif
										    	<a @if(Session::get('service_id') == COURIER) class="active-inner" @endif onclick="return subcriptionuserservice(21,'{{$strUrl}}')">
										    		<img src="images/log-icons/courier.png" title="{{COURIER_IMAGE_TITLE}}"/>
										    	</a>
										    </div>
										</div>
										@else
										 <div class="service-div-block">
										    <div class="service-div">Courier</div>
										    <div id="courier" class="service-div service-icon-div">
										    	<a @if(Session::get('service_id') == COURIER) class="active-inner" @endif onclick="return buyersetservice(21,'{{$strUrl}}')">
										    		<img src="images/log-icons/courier.png" title="{{COURIER_IMAGE_TITLE}}"/>
										    	</a>
										    </div>
										</div>
										@endif

								    </div>
								     <div class="home-menu">
									    <h3>Vehicle</h3>

									    <div class="service-div-block">
											<div class="service-div">Vehicle</div>
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

											        <a @if(Session::get('service_id') == ROAD_TRUCK_HAUL) class="active-inner" @endif onclick="return buyersetservice(4,'{{$strUrl}}')">
											    		<img src="images/log-icons/truck_haul.png" title="{{TRUCK_HAUL_IMAGE_TITLE}}"/>
											    		Haul
											    	</a>
											    @elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )
													<a @if(Session::get('service_id') == ROAD_TRUCK_HAUL) class="active-inner" @endif onclick="return subcriptionuserservice(4,'{{$strUrl}}')">
											    		<img src="images/log-icons/truck_haul.png" title="{{TRUCK_HAUL_IMAGE_TITLE}}"/>
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

													<a @if(Session::get('service_id') == ROAD_TRUCK_LEASE) class="active-inner" @endif onclick="return buyersetservice(5,'{{$strUrl}}')">
											    		<img src="images/log-icons/truck_lease.png" title="{{TRUCK_LEASE_IMAGE_TITLE}}"/>
											    		Lease
											    	</a>
											    @elseif( (Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER) )
													<a @if(Session::get('service_id') == ROAD_TRUCK_LEASE) class="active-inner" @endif onclick="return subcriptionuserservice(5,'{{$strUrl}}')">
											    		<img src="images/log-icons/truck_lease.png" title="{{TRUCK_LEASE_IMAGE_TITLE}}"/>
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
										    		<a onclick="return buyersetservice(15,'/buyersearch')">
										    	@else
										    		@if (in_array(RELOCATION_DOMESTIC, $SellerServiceExits))
														<div class="service-div service-icon-div">
      													@else
														<div class="service-div service-icon-div checkserviceseller">
 														@endif
										    		<a onclick="return subcriptionuserservice(20,'/sellersearchbuyers')">
										    	@endif		
										    		<img src="images/log-icons/domestic.png" title="{{RELOCATION_DOMESTIC_IMAGE_TITLE}}"/>
										    		Domestic
										    	</a>
										    </div>
									    	@if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
									    		<div class="service-div service-icon-div ">
									    		<a onclick="return buyersetservice({{RELOCATION_PET_MOVE}},'/buyersearch')">
									    	@else
									    		@if (in_array(RELOCATION_PET_MOVE, $SellerServiceExits))
													<div class="service-div service-icon-div ">
  													@else
													<div class="service-div service-icon-div checkserviceseller">
														@endif
									    		<a onclick="return subcriptionuserservice({{RELOCATION_PET_MOVE}},'/sellersearchbuyers')">
									    	@endif		
										    		<img src="images/log-icons/pet_move.png" title="{{RELOCATION_PETMOVE_IMAGE_TITLE}}"/>
										    		Pet Move
										    	</a>
										    </div>	
										</div>

										<div class="service-div-block">
											<div class="service-div"><span class="invisible_text">Home</span></div>
									    	@if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
									    		<div class="service-div service-icon-div ">
									    		<a onclick="return buyersetservice({{RELOCATION_INTERNATIONAL}},'/buyersearch')">
									    	@else
									    		@if (in_array(RELOCATION_INTERNATIONAL, $SellerServiceExits))
													<div class="service-div service-icon-div ">
  													@else
													<div class="service-div service-icon-div checkserviceseller">
														@endif
									    		<a onclick="return subcriptionuserservice({{RELOCATION_INTERNATIONAL}},'/sellersearchbuyers')">
									    	@endif		
										    		<img src="images/log-icons/international.png" title="{{RELOCATION_INTERNATIONAL_IMAGE_TITLE}}"/>
										    		International
										    	</a>
										    </div>
										    @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
										    <div class="service-div service-icon-div ">
										    	<a onclick="return buyersetservice({{RELOCATION_GLOBAL_MOBILITY}},'/buyersearch')">
										    	@else
									    		@if (in_array(RELOCATION_GLOBAL_MOBILITY, $SellerServiceExits))
													<div class="service-div service-icon-div ">
  													@else
													<div class="service-div service-icon-div checkserviceseller">
														@endif
									    		<a onclick="return subcriptionuserservice({{RELOCATION_GLOBAL_MOBILITY}},'/sellersearchbuyers')">
									    	@endif	
										    		<img src="images/log-icons/global_mobility.png" title="{{RELOCATION_GLOBAL_MOBILITY_IMAGE_TITLE}}"/>
										    		Global Mobility
										    	</a>
										    </div>	
										</div>
										<div class="service-div-block">
										    <div class="service-div">Office</div>
										    @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
										    		<div class="service-div service-icon-div ">
										    		<a onclick="return buyersetservice({{RELOCATION_OFFICE_MOVE}},'/relocation/creatbuyerrpost')">
										    	@else
										    		@if (in_array(RELOCATION_OFFICE_MOVE, $SellerServiceExits))
														<div class="service-div service-icon-div ">
      													@else
														<div class="service-div service-icon-div checkserviceseller">
 														@endif
										    		<a onclick="return subcriptionuserservice(20,'/sellersearchbuyers')">
										    	@endif		
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
					<div class="clearfix"></div>
				</div>

				<div class="notifications">
						<div class="notifications_inner">
							<div class="notif-left"><img src="../images/home-img.jpg"/></div>
							<div class="notif-right">
								<p>
									<span class="pull-left"><span class="red">John Deer</span> Updated Consignment Status</span>
									<span class="pull-right">1hr ago</span>

								</p>
							</div>
						</div>
						<div class="notifications_inner">
							<div class="notif-left"><img src="../images/home-img.jpg"/></div>
							<div class="notif-right">
								<p>
									<span class="pull-left"><span class="red">John Deer</span> Updated Consignment Status</span>
									<span class="pull-right">1hr ago</span>

								</p>
							</div>
						</div>
					</div>
				</div>

	
	
<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
	<div class="modal-dialog">

		<!-- Modal content-->
		<div
			class="modal-content registeration pull-left col-md-12 ">
			<button data-dismiss="modal" class="close" type="button">×</button>
			<div class="modal-body pull-left col-md-12 padding-none">
				<div class="col-md-12 col-sm-12 col-xs-12 pull-left padding-none">
					<p class="pull-left user">

					
					@if( (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
							<span class="red">Welcome {{ ucfirst(trans($common->getUserNameFromRole(BUYER,Auth::user()->id))) }}
							@elseif((Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER))
								<span class="red">Welcome {{ ucfirst(trans($common->getUserNameFromRole(SELLER,Auth::user()->id))) }}
							@endif
					
						</span><br />We recommend <br /> Click here to upload Your <a
							class="red" href="#" id="mylabel">Business Logo</a> <input
							id="myinput" type="file"
							onchange='return uploadImage({{$userId}});'
							class="from-control btn btn-warning displayNone"> <br><label class="success pull-left" id="logoSuccess"></label>
					</p>
					<p class="pull-right">
						<span class="round-circle"><i class="fa fa-headphones"></i> </span><span
							class="pull-right">24X7 Customer Support<br>040 39412345
						</span>
					</p>
				</div>

				<div class="col-md-12 col-sm-12 col-xs-12 pull-left padding-none">
					<p class="text-left">
						Please click on relevant icons <br /> for complete Registeration <span
							class="text-right col-md-10 padding-none full-image"><img
							src="{{url('images/registration-image.jpg')}}"
							usemap="#Map" /> <map name="Map">
								<area shape="rect" coords="74,51,181,80"
									href="/vehicleregister">
								<area shape="rect" coords="196,2,249,53"
									href="/warehouseregister">
								<area shape="rect" coords="271,54,365,97"
									href="/equipmentregister">
								<area shape="rect" coords="273,126,357,183" href="#">
							</map> </span>
					</p>
				<div class="clearfix"></div>
					<p class="pull-right ">
						<a href="#" data-dismiss="modal" class="bg-red" id="askmeLater">Ask me later</a>
					</p>
				</div>
			</div>

		</div>

	</div>
</div>
<button type="button"  id="mybut"
	class="btn btn-primary displayNone" data-toggle="modal" data-target="#myModal">
	Launch demo modal</button>
	
	
	<!-- activate popup for first login -->
@if($activePopup)
<script type="text/javascript">
	
    $(document).ready(function() {
      //  $('#myModal').modal('show'); 
        $('#mybut').trigger('click');
        $('#mylabel').on('click',function() {
            $('#myinput').trigger('click');
        });
    });
    
   
</script>
	@endif	

			
	<!-- Page Center Content Ends Here -->
	<!-- Right Starts Here -->
	<!-- Right Ends Here -->
</div>	        </div> @include('partials.footer')	 </div>			
@endsection