{{--*/ $routeName = strtolower(substr(class_basename(Route::currentRouteAction()), (strpos(class_basename(Route::currentRouteAction()), '@') + 1))) /*--}}
<!-- Header Content Starts Here-->
@inject('common', 'App\Components\CheckoutComponent')
@inject('common', 'App\Components\CommonComponent')
<style>
    .cart-head{ font-size: 30px; margin: 3px 0px;}
</style>
{{--*/ $pageServiceId = Session::get('service_id')  /*--}}
@if($pageServiceId == 0 || $pageServiceId == '')
{{--*/ $pageServiceId = '0'  /*--}}
@endif

<!-- <div class="col-md-12 col-sm-12 col-xs-12 main-header">	 -->
<!--     <div class="row"> -->
    
    <div class="main-outer">
    
    <header>
    			<div class="container">
				<div class="logo-div">
				 
					<a href="/home"><img src="{{ asset('/images/logo.png') }}" alt="Logistiks" class="logo" /></a>
				</div>
				<div class="nav-block">
					<nav>
						<div class="container-fluid padding-none">
							<div class="navbar-responsive">
								<button class="nav-icon" type="button">
						        	<span class="icon-bar"></span>
						        	<span class="icon-bar"></span>
									<span class="icon-bar"></span>
								</button>
							</div>
							<div class="main-navigation">
								<ul class="navigation-list pull-left">
									@if (Auth::guest())
									<li class="active"><a href="/">Market Place</a></li>
									@else
									<li class="active"><a href="/home">Market Place</a></li>
									@endif
									<li><a href="#">Community</a></li>
									<li><a href="#">Dealers</a></li>
									<li><a href="#">Tools</a></li>								
								</ul>
							</div>
						</div>
					</nav>

					
					
				</div>

				<div class="search-area">
					<div class="search">

						


						<div class="dropdown">
						    <button class="btn add-btn dropdown-toggle" type="button" data-toggle="dropdown">
						    	<span class="change-search-icon"><i class="fa fa-bars"></i></span>
						    	<span class="caret"></span>
						    </button>
						    <ul class="dropdown-menu">
						        <li><a><i class="fa fa-area-chart"></i> Market Place</a></li>
						        <li><a><i class="fa fa-bank"></i> Community</a></li>
						        <li><a><i class="fa fa-apple"></i> Dealers</a></li>
						        <li><a><i class="fa fa-beer"></i> Tools</a></li>
						    </ul>
						</div>

						<input type="text" class="form-control input-sm" maxlength="64" placeholder="Search" />
						<button type="submit" class="btn add-btn-search btn-sm"><i class="fa fa-search"></i></button>
					</div>
				</div>	
                            @if (Auth::guest())	
				@else
                            <a class="mobile-hide login_name" title="{{ Auth::user()->username }}" style="display:none;">
                    @if(isset(Auth::user()->username) && !empty(Auth::user()->username))
                        {{ ucfirst(Auth::user()->username) }}
                    @endif
                </a>@endif
				<div class="log-div-inner">
					<ul class="nav pull-right log-dropdown">
						
						   @if (Auth::guest())					

						   	
						   	<div class="log-div">
					<button class="login" type="button" data-toggle="modal" data-target="#login-modal">Login</button>
					<a href="/register"><button class="signup">Sign Up</button></a>
				</div>
               
              

            @else
            
            <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown">
                        <span>					
                            <span class="count msg_count"> 
				@if(Session::get('service_id') == 0)
                                {{ $common->getBuyerSellerMessageCountDefualt() }}
                                @else
                                {{ $common->getBuyerSellerMessageCount() }}
                                @endif
                            </span>
			</span>
                            <span>
                                    <i class="fa fa-envelope-o log-icon"></i>
                                    
                                    <b class="caret"></b>
                            </span>
                            <span class="log-name">Messages</span>

                    </a>
                    <ul class="dropdown-menu">
<li><a href="{{ url('/messages') }}">Inbox</a></li>
<li><a href="{{ url('/sentmessages') }}">Sent Messages</a></li>
                    </ul>
            </li>


						<li class="plain-menu">
							@if(Auth::user()->lkp_role_id == BUYER)
								<a href="#"  data-toggle="modal" data-target="#change-service" onclick="setTabsPage('/buyerposts')">
							@elseif(Auth::user()->lkp_role_id == SELLER)
								<a href="#"  data-toggle="modal" data-target="#change-service" onclick="setTabsPage('/sellerlist')">
							@endif
								<span>
									<i class="fa fa-clipboard log-icon"></i>
									<span class="count">
									 @if (Auth::guest())	
									 @else
									 @if(Auth::user()->lkp_role_id == BUYER)
							            @if(Session::get('service_id') == 0)
							                   {{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ROAD)}}
							               @else
							                   {{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, Session::get('service_id'))}}
							               @endif
							                @elseif(Auth::user()->lkp_role_id == SELLER)
							                @if(Session::get('service_id') == 0)
							                    {{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ROAD)}}
							                @else
							                    {{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, Session::get('service_id'))}}
							                @endif @endif
							                @endif
           </span>
								</span>
								<span class="log-name">Posts</span>
							</a>
						</li>
						<li class="plain-menu">
							@if(Auth::user()->lkp_role_id == BUYER)
								<a href="#"  data-toggle="modal" data-target="#change-service" onclick="setTabsPage('/orders/buyer_orders')">
							@elseif(Auth::user()->lkp_role_id == SELLER)
								<a href="#"  data-toggle="modal" data-target="#change-service" onclick="setTabsPage('/orders/seller_orders')">
							@endif
								<span>
									<i class="fa fa-rupee log-icon"></i>
									<span class="count"> 
									 @if (Auth::guest())	
									 @else
				@if(Auth::user()->lkp_role_id == BUYER)
          		@if(Session::get('service_id') == 0)
                    {{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ORDERSCOUNT)}}
                @else
                    {{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ORDERSINVUDUAL)}}
                @endif
                
                 @elseif(Auth::user()->lkp_role_id == SELLER)
                @if(Session::get('service_id') == 0)
                    {{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ORDERSCOUNT)}}
                @else
                    {{$common->getBuyerSellerCount(Auth::user()->id, Auth::user()->lkp_role_id, ORDERSINVUDUAL)}}
                @endif @endif
                @endif</span>
								</span>
								<span class="log-name">Orders</span>
							</a>
						</li>
						<li class="plain-menu">
							<a href="#">
								<span>
									<i class="fa fa-users log-icon"></i>
									<span class="count">0</span>
								</span>
								<span class="log-name">Network</span>
							</a>
						</li>
						<li class="dropdown">
							<a class="dropdown-toggle" data-toggle="dropdown">
								<span>
									<i class="fa fa-user log-icon"></i>
									<b class="caret"></b>
								</span>
								<span class="log-name">Profile</span>
								
							</a>
							<ul class="dropdown-menu">
							<li><span class="login-name" title="{{ Auth::user()->username }}">
                    @if(isset(Auth::user()->username) && !empty(Auth::user()->username))
                        {{ ucfirst(trans(Auth::user()->username)) }}
                    @endif
                </span></li>
                @if($routeName != 'seller' && $routeName != 'buyerbusiness' && $routeName != 'sellerbusiness' 
                            && $routeName != 'buyer' && $routeName != 'selectuser')
                            <li><a href="{{ url('/editmyprofile') }}">My Profile</a></li>
                            @endif
                            @if(((Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER)) && ($routeName != 'seller' 
                            && $routeName != 'buyerbusiness' && $routeName != 'sellerbusiness' 
                            && $routeName != 'buyer' && $routeName != 'selectuser'))
                                <li><a href="{{ url('/list') }}">Manage Equipments</a></li>
                                <li><a href="{{ url('/warehouselist') }}">Manage Warehouses</a></li>
                                <li><a href="{{ url('/vehiclelist') }}">Manage Vehicles</a></li>
                            @endif
                            <li><a href="{{ url('/changepassword') }}">Change Password</a></li>
							<li><a href="#">Search History</a></li>
                            <li><a href="{{ url('/auth/logout') }}">Logout</a></li>
                
								
							
							</ul>
						</li>
						@endif
           
					</ul>
				</div>
			</div>
			
		</header>
		
    


		<div class="clearfix"></div>
 <!-- login popup -->
			<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    	  <div class="modal-dialog">

				<div class="loginmodal-container">
				 <button type="button" class="close" data-dismiss="modal">&times;</button>
				 
					<h1>Login to Your Account</h1><br>
				 <form class="form-horizontal" role="form" method="POST" action="{{ url('/auth/login') }}">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="user_clicked_service" id="user_clicked_service" value="">  
                    <input type="hidden" name="user_clicked_page" id="user_clicked_page" value="">          
                    <input type="text" name="email" placeholder="Username *"  value="{{ old('email') }}" class="form-control form-control1 margin-bottom">

                    <input type="password" name="password" placeholder="Password *"  class="form-control form-control1 margin-bottom">
                    <input type="hidden" name="is_active" id="is_active" value="1"/>

                    <input type="submit" value="Login" class="login loginmodal-submit">
                </form>
                
					
				  <div class="login-help">
					  <a href="{{ url('/register') }}">Register</a> - <a href="{{ url('/password/email') }}">Forgot Password?</a>
				  </div>
				</div>
			</div>
		  </div>
		<!-- Inner Menu Starts Here -->
    
    
<!-- </div> 
    
    
@if((isset(Auth::user()->id) && Auth::user()->id != '' && $routeName != 'vieweditsellerbusiness' && $routeName != 'vieweditbuyerbusiness'))
	<div class="responsive_sidebars">
	        <div class="container">
	            <div class="left-bar"><i class="fa fa-list-ul"></i></div>
	            <div class="right-bar"><i class="fa fa-angle-down"></i> <span class="right-bar-title">Quick Search</span></div>
	        </div>
	</div>
@endif-->

<!-- Header Content Ends Here-->
