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

<div class="col-lg-2 col-md-3 col-sm-3 col-xs-12 main-left">
				
				@if( Auth::user()->lkp_role_id == BUYER )
						<span class="left-dropdown-text">
						<a href='/editmyprofile'>
						{{$common->getBuyerName(Auth::user()->id, Auth::user()->lkp_role_id)}}
						</a>
						</span>
					@elseif( Auth::user()->lkp_role_id == SELLER )
					{{--*/ $img=$common->getSellerNameImage(Auth::user()->id, Auth::user()->lkp_role_id) /*--}}
					@if( Auth::user()->logo)
					{{--*/ $logoImg = Auth::user()->logo /*--}}
						<span class="left-dropdown-text">
						<img width='100%' src="{{url($logoImg)}}">
						</span>
					@else
					<span class="left-dropdown-text"><a href='/editmyprofile'>{{ strstr($img, ',', true) }}</a></span>
					@endif
				@endif
				
				<div class="block">
					<p class="block-title">
						@if( Auth::user()->lkp_role_id == BUYER )
						<span>Seller</span> | <a href="/editmyprofile">Buyer</a>
						@elseif( Auth::user()->lkp_role_id == SELLER )
						<a href="/editmyprofile">Seller</a> | <span>Buyer</span>
						@endif
					</p>
				</div>
				<div class="profile-block dropdown_data">
					<h3 class="block-head"><a class="main-active">Transport</a></h3>
					<ul>
						<li><a href="#">Multi Modal<span class="menu-count">0</span></a></li>
						<li class="inner-dropdown @if($common->getServiceName(Session::get('service_id')) == ROADTFTL || $common->getServiceName(Session::get('service_id')) == ROADTLTL || $common->getServiceName(Session::get('service_id')) == ROADTINTRA) active @endif">Road <span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ROAD)}}</span>
							<ol style="@if($common->getServiceName(Session::get('service_id')) == ROADTFTL || $common->getServiceName(Session::get('service_id')) == ROADTLTL || $common->getServiceName(Session::get('service_id')) == ROADTINTRA) display:block @endif">

						@if(Auth::user()->lkp_role_id == BUYER)
								<li id="ftl"><a @if(Session::get('service_id') == ROAD_FTL) class="active-inner" @endif onclick="return buyersetservice(1,'{{$strUrl}}')">FTL <span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ROAD_FTL)}}</span></a></li>
								<li id="ltl"><a @if(Session::get('service_id') == ROAD_PTL) class="active-inner" @endif  onclick="return buyersetservice(2,'{{$strUrl}}')">LTL <span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ROAD_PTL)}}</span></a></li>
								<li id="intracity"><a @if(Session::get('service_id') == ROAD_INTRACITY) class="active-inner" @endif onclick="return buyersetservice(3,'{{$strUrl}}')">Intracity <span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ROAD_INTRACITY)}}</span></a></li>
						@elseif( Auth::user()->lkp_role_id == SELLER )
								<li id="ftl"><a @if(Session::get('service_id') == ROAD_FTL) class="active-inner" @endif onclick="return subcriptionuserservice(1,'{{$strUrl}}')">
								FTL<span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ROAD_FTL)}}</span></a></li>
								<li id="ltl"><a @if(Session::get('service_id') == ROAD_PTL) class="active-inner" @endif onclick="return subcriptionuserservice(2,'{{$strUrl}}')">LTL <span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ROAD_PTL)}}</span></a></li>
								<li><a href="javascript:void(0);">Intracity <span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ROAD_INTRACITY)}}</span></a></li>
						@endif
								<li><a @if(Session::get('service_id') == ROAD_TRUCK_HAUL) class="active-inner" @endif href="#">Truck Haul <span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ROAD_TRUCK_HAUL)}}</span></a></li>
								<li><a @if(Session::get('service_id') == ROAD_TRUCK_LEASE) class="active-inner" @endif href="#">Truck Lease <span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ROAD_TRUCK_LEASE)}}</span></a></li>
							</ol>
						</li>
						<li id="rail"><a @if(Session::get('service_id') == RAIL) class="active-inner" @endif onclick="return subcriptionuserservice(6,'{{$strUrl}}')">Rail 
							<span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, RAIL)}}</span></a>
						</li>
						<li class="inner-dropdown @if(Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL) active @endif">Air <span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, AIR)}}</span>
							<ol style="@if(Session::get('service_id') == AIR_DOMESTIC || Session::get('service_id') == AIR_INTERNATIONAL) display:block @endif">
								<li id="airdomestic"><a @if(Session::get('service_id') == AIR_DOMESTIC) class="active-inner" @endif onclick="return subcriptionuserservice(7,'{{$strUrl}}')">Domestic 
								<span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, AIR_DOMESTIC)}}</span></a></li>
								<li id="airinternational"><a @if(Session::get('service_id') == AIR_INTERNATIONAL) class="active-inner" @endif onclick="return subcriptionuserservice(8,'{{$strUrl}}')">International 
								<span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, AIR_INTERNATIONAL)}}</span></a></li>
							</ol>
						</li>
						<li id="ocean"><a @if(Session::get('service_id') == OCEAN) class="active-inner" @endif onclick="return subcriptionuserservice(9,'{{$strUrl}}')">Ocean 
						<span class="menu-count">{{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, OCEAN)}}</span></a></li></li>
					</ul>
				</div>
				<div class="profile-block">
					<h3 class="block-head"><a href="#">Warehouse</a></h3>
				</div>
				<div class="profile-block dropdown_data">
					<h3 class="block-head"><a>Handling &amp; Packaging</a></h3>
					<ul style="display: none;">
						<li><a href="#">Handling Services<span class="menu-count">0</span></a></li>
						<li><a href="#">Packaging Services<span class="menu-count">0</span></a></li>
						<li><a href="#">Equipment Lease<span class="menu-count">0</span></a></li>
					</ul>
				</div>
				<div class="profile-block">
					<h3 class="block-head"><a href="#">3rd Party Logistics</a></h3>
				</div>
				<div class="profile-block dropdown_data">
					<h3 class="block-head"><a>Relocation</a></h3>
					<ul>
						<li><a href="#">Domestic <span class="menu-count">0</span></a></li>
						<li><a href="#">International <span class="menu-count">0</span></a></li>
						<li><a href="#">Global mobility <span class="menu-count">0</span></a></li>
						<li><a href="#">Office move <span class="menu-count">0</span></a></li>
						<li><a href="#">Pet move <span class="menu-count">0</span></a></li>
					</ul>
				</div>
				<div class="profile-block">
					<h3 class="block-head"><a href="#">Shipping</a></h3>
				</div>
				<div class="profile-block">
					<h3 class="block-head"><a href="#">Courier</a></h3>
				</div>
					<?php
						//$layeredResults = Session::get('layered_filter');
						//echo "<pre>"; print_R($layeredResults); echo "</pre>";
					?>
		<div class="clearfix"></div>
		<?php
			$selectedSellers = isset($_REQUEST['selected_users']) ? explode(",",$_REQUEST['selected_users']) : array();
		?>

		@if (Session::has('show_layered_filter'))
			@if (Session::has('layered_filter'))
				<h6><b>List <?php echo (Auth::user()->lkp_role_id == 1) ? "Sellers" : "Buyers"; ?></b></h6>
				<div class="layered_nav  margin-top col-xs-12 padding-none">
					@if(Session::has('layered_filter') && is_array(Session::get('layered_filter')))
						@foreach (Session::get('layered_filter') as $userId => $userName)
							<?php $selected = in_array($userId, $selectedSellers) ? 'checked="checked"' : ""; ?>
							<div class="col-xs-12 padding-none"><input type="checkbox" class="checkbox pull-left filtercheckbox" value="{{$userId}}" {{$selected}}} name="search_by_user"> {{ $userName }}</div>
						@endforeach
					@endif
				</div>
			@endif
		@endif
</div>
<?php
	Session::forget('show_layered_filter');
?>


<!-- LeftNav Content Ends Here -->