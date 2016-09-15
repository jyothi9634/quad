{{--*/ $routeName = strtolower(substr(class_basename(Route::currentRouteAction()), (strpos(class_basename(Route::currentRouteAction()), '@') + 1))) /*--}}
        <!-- LeftNav Content Starts Here -->
      
@inject('common', 'App\Components\CommonComponent')
@inject('checkout', 'App\Components\CheckoutComponent')

<div class="crum-2">
    @for($i = 0; $i <= count(Request::segments()); $i++)
        @if(Request::segment($i) == MESSAGESINDEX)
            Messages
        @elseif(Request::segment($i) == FTLCREATEPOST || Request::segment($i) == LTLCREATEPOST)
            <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> <a href="/sellerlist">Posts</a> <i class="fa  fa-angle-right"></i> + Post
        @elseif(Request::segment($i) == SELLERPOSTLIST)
            <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> Posts (@if(Session::get('type')==2) Market Leads @else Spot @endif)<i class="fa  fa-angle-right"></i> My Posts
        @elseif(Request::segment($i) == SELLERSEARCH)
            <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> Search
		@elseif(Request::segment($i) == SELLERSEARCHRESULTS || Request::segment($i) == SELLERTERMSEARCHRESULTS)
            <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} (@if(Session::get('session_spot_or_term')==2) Term @else Spot @endif)<i class="fa  fa-angle-right"></i> Search Results 
        @elseif(Request::segment($i) == SELLERPOSTS)
        <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> <a href="/sellerlist">Posts (@if(Session::get('type')==2) Market Leads @else Spot @endif)</a> <i class="fa  fa-angle-right"></i> My Posts
        @elseif(Request::segment($i) == PTLZONE)
        <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> Manage Zone
		@elseif(Request::segment($i) == PTLTIER)
        <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> Manage Tier
		@elseif(Request::segment($i) == PTLTMATRIX)
        <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> Manage Transit Days Matrix
		@elseif(Request::segment($i) == PTLSECTOR)
        <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> Manage Sector
        @elseif(Request::segment($i) == PTLPINCODE)
        <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> Manage Pincode        
        @elseif(Request::segment($i) == SELLERPOSTDETAILS)
            <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> <a href="/sellerlist">Posts (@if(Session::get('type')==2) Market Leads @else Spot @endif)</a> <i class="fa  fa-angle-right"></i> <a href="javascript:void(0)" onclick="history.go(-1); return false;">My Posts</a> <i class="fa  fa-angle-right"></i> Individual Post
        @elseif(Request::segment($i) == SELLERORDERS || Request::segment($i) == SELLERORDERSEARCH)
            <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> Orders (@if(Session::get('orderType_id')==2) Contract @else Spot @endif)
        @elseif(Request::segment($i) == SELLERORDERDETAILS)
            <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> <a href="javascript:void(0)" onclick="history.go(-1); return false;">Orders (@if(Session::get('orderType_id')==2) Contract @else Spot @endif)</a> <i class="fa  fa-angle-right"></i> Order
        @elseif(Request::segment($i) == SELLERCONSIGNMENT)
            <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> <a href="/orders/seller_orders">Orders</a> <i class="fa  fa-angle-right"></i> Order Consignment
        @elseif(Request::segment($i) == FTLCREATEQUOTE || Request::segment($i) == LTLCREATEQUOTE || Request::segment($i) == INTRACREATEQUOTE || Request::segment($i) == RELOCATIONCREATEPOST)
            <i class="fa  fa-angle-right"></i> Buyer <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> <a href="/buyerposts">Posts</a> <i class="fa  fa-angle-right"></i> Post & Get Quote
        @elseif(Request::segment($i) == BUYERPOSTS)
            <i class="fa  fa-angle-right"></i> Buyer <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> Posts(@if(Session::get('post_type')=='term') Term @else Spot @endif)<i class="fa  fa-angle-right"></i> My Posts
		@elseif(Request::segment($i) == BUYERSEARCH)
            <i class="fa  fa-angle-right"></i> Buyer <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> Search
		@elseif(Request::segment($i) == BUYERSEARCHRESULTS)
            <i class="fa  fa-angle-right"></i> Buyer <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> Search Results
        @elseif(Request::segment($i) == BUYERPOSTDETAIL)
            <i class="fa  fa-angle-right"></i> Buyer <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> <a href="javascript:void(0)" onclick="history.go(-1); return false;">Posts(@if(Session::get('post_type')=='term') Term @else Spot @endif)</a> <i class="fa  fa-angle-right"></i> Individual Post
        @elseif(Request::segment($i) == BUYERORDERS || Request::segment($i) == BUYERORDERSEARCH)
            <i class="fa  fa-angle-right"></i> Buyer <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> Orders (@if(Session::get('order_type')==2) Contract @else Spot @endif)
        @elseif(Request::segment($i) == BUYERORDERDETAILS)
            <i class="fa  fa-angle-right"></i> Buyer <i class="fa  fa-angle-right"></i> Home <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} > <a href="javascript:void(0)" onclick="history.go(-1); return false;">Orders (@if(Session::get('order_type')==2) Contract @else Spot @endif)</a> <i class="fa  fa-angle-right"></i> Order
        @elseif(Request::segment($i) == BUYERDETAILS)
            <i class="fa  fa-angle-right"></i> Buyer <i class="fa  fa-angle-right"></i> Home <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} > <a href="javascript:void(0)" onclick="history.go(-1); return false;">Orders (@if(Session::get('order_type')==2) Contract @else Spot @endif)</a> <i class="fa  fa-angle-right"></i> Order
        @elseif(Request::segment($i) == BUYERTERMPOSTDETAIL)
            <i class="fa  fa-angle-right"></i> Buyer <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> {!! $common->getServiceBreadCrumbName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> {!! $common->getServiceName(Session::get('service_id')) !!} <i class="fa  fa-angle-right"></i> <a href="javascript:void(0)" onclick="history.go(-1); return false;" >Posts(@if(Session::get('post_type')=='term') Term @else Spot @endif)</a> <i class="fa  fa-angle-right"></i> Individual Post
        @elseif(Request::segment($i) == MESSAGES)
	         @if( (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
	         <i class="fa  fa-angle-right"></i> Buyer
	         @else
	         <i class="fa  fa-angle-right"></i> Seller
	         @endif
	        <i class="fa  fa-angle-right"></i> Messages            
       	 @elseif(Request::segment($i) == EDITSELLER || Request::segment($i) == PTLSELLERBUSINESS || Request::segment($i) == BUYEREDIT || Request::segment($i) == BUYERBUSINESS)
	        
	        @if( (isset(Auth::user()->lkp_role_id) && Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
             <i class="fa  fa-angle-right"></i> Buyer
             @else
                 @if(isset(Auth::user()->lkp_role_id))
             <i class="fa  fa-angle-right"></i> Seller
                 @endif
             @endif
             @if(isset(Auth::user()->lkp_role_id))
            <i class="fa  fa-angle-right"></i> Profile
            @endif
	        
	    @elseif(Request::segment($i) == VEHICLELIST)
	         @if( (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
	         <i class="fa  fa-angle-right"></i> Buyer
	         @else
	         <i class="fa  fa-angle-right"></i> Seller
	         @endif
	        <i class="fa  fa-angle-right"></i> Manage Vehicles
		@elseif(Request::segment($i) == PTLVEHICLEREGISTER)
	         @if( (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
	         <i class="fa  fa-angle-right"></i> Buyer
	         @else
	         <i class="fa  fa-angle-right"></i> Seller
	         @endif
	        <i class="fa  fa-angle-right"></i> <a href="/vehiclelist">Manage Vehicles</a>  <i class="fa  fa-angle-right"></i> Add Vehicle
		@elseif(Request::segment($i) == PTLVEHICLEEDIT)
	         @if( (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
	         <i class="fa  fa-angle-right"></i> Buyer
	         @else
	         <i class="fa  fa-angle-right"></i> Seller
	         @endif
	        <i class="fa  fa-angle-right"></i> <a href="/vehiclelist">Manage Vehicles</a>  <i class="fa  fa-angle-right"></i> Edit Vehicle
	   @elseif(Request::segment($i) == WAREHOUSELIST)
	         @if( (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
	         <i class="fa  fa-angle-right"></i> Buyer
	         @else
	         <i class="fa  fa-angle-right"></i> Seller
	         @endif
	        <i class="fa  fa-angle-right"></i> Manage Warehouses 
	   @elseif(Request::segment($i) == PTLWAREHOUSEREGISTER)
	         @if( (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
	         <i class="fa  fa-angle-right"></i> Buyer
	         @else
	         <i class="fa  fa-angle-right"></i> Seller
	         @endif
	        <i class="fa  fa-angle-right"></i> <a href="/warehouselist">Manage Warehouses</a> <i class="fa  fa-angle-right"></i> Add Warehouse
	   @elseif(Request::segment($i) == PTLWAREHOUSEEDIT)
	         @if( (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
	         <i class="fa  fa-angle-right"></i> Buyer
	         @else
	         <i class="fa  fa-angle-right"></i> Seller
	         @endif
	        <i class="fa  fa-angle-right"></i> <a href="/warehouselist">Manage Warehouses</a> <i class="fa  fa-angle-right"></i> Edit Warehouse
	    @elseif(Request::segment($i) == EQPLIST)
	         @if( (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
	         <i class="fa  fa-angle-right"></i> Buyer
	         @else
	         <i class="fa  fa-angle-right"></i> Seller
	         @endif
	        <i class="fa  fa-angle-right"></i> Manage Equipment
		@elseif(Request::segment($i) == PTLEQPREGISTER)
	         <i class="fa  fa-angle-right"></i> Seller
	         <i class="fa  fa-angle-right"></i> <a href="/list">Manage Equipment</a> <i class="fa  fa-angle-right"></i> Add Equipment
	    @elseif(Request::segment($i) == PTLEQPEDIT)
	         <i class="fa  fa-angle-right"></i> Seller
	         <i class="fa  fa-angle-right"></i> <a href="/list">Manage Equipment</a> <i class="fa  fa-angle-right"></i> Edit Equipment
	    @elseif(Request::segment($i) == SENTMESSAGES)
	         @if( (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
	         <i class="fa  fa-angle-right"></i> Buyer
	         @else
	         <i class="fa  fa-angle-right"></i> Seller
	         @endif
	        <i class="fa  fa-angle-right"></i> Sent Messages
	   @elseif(Request::segment($i) == GETMESSAGEDETAILS)
	         @if( (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
	         <i class="fa  fa-angle-right"></i> Buyer
	         @else
	         <i class="fa  fa-angle-right"></i> Seller
	         @endif
	   		@if(strpos($_SERVER['HTTP_REFERER'],"sentmessages")=== false)
	        <i class="fa  fa-angle-right"></i> <a href="{{$_SERVER['HTTP_REFERER']}}">Messages</a> <i class="fa  fa-angle-right"></i> Message Details
	        @else
	         <i class="fa  fa-angle-right"></i> <a href="{{$_SERVER['HTTP_REFERER']}}">Sent Messages</a> <i class="fa  fa-angle-right"></i> Message Details
	    	 @endif
	        
        @elseif(Request::segment($i) == CART)
         @if( (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
         <i class="fa  fa-angle-right"></i> Buyer
         @else
         <i class="fa  fa-angle-right"></i> Seller
         @endif
         <i class="fa  fa-angle-right"></i> Cart
        @elseif(Request::segment($i) == CHECKOUT)
	        @if( (Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER) )
	         <i class="fa  fa-angle-right"></i> Buyer
	         @else
	         <i class="fa  fa-angle-right"></i> Seller
	         @endif
             <i class="fa  fa-angle-right"></i> Cart <i class="fa  fa-angle-right"></i> Make Payment
        @endif
    @endfor
</div>
 @if (Auth::guest())		
 <div class="top-menu-strip"></div>
 @else

{{--*/ $pageServiceId = Session::get('service_id')  /*--}}
@if($pageServiceId == 0 || $pageServiceId == '')
    {{--*/ $pageServiceId = '0'  /*--}}
@endif

{{--*/  $homeClass = '';   /*--}}
{{--*/  $messageClass = '';   /*--}}
{{--*/  $postClass = '';   /*--}}
{{--*/  $sellerpostClass = '';   /*--}}


	@if($routeName == 'index' || $routeName == 'networkprofile' || $routeName=='listofrecomendations' || $routeName=='listofpartners' || $routeName=='listofjobs' || $routeName=='listofarticles')

       
      {{--*/  $networkClass = '1'  /*--}}
    @else
      {{--*/   $networkClass = '0'  /*--}}
     @endif

{{--*/  $masterClass = '';  /*--}}
{{--*/  $orderClass = '';  /*--}}
  
        @if($routeName == 'index_1')
       
     {{--*/  $homeClass = '1' /*--}}
    @else
     {{--*/    $homeClass = '0' /*--}}
     @endif
   
   @if($routeName == 'messages' || $routeName == 'getmessagedetails')
   {{--*/    $messageClass = '1' /*--}}
    @else
     {{--*/    $messageClass = '0' /*--}}
     @endif
     
   
 @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
            
            @if($routeName == 'buyerpost' || $routeName == 'buyerpostslist' || $routeName == 'getpostbuyercounteroffer' ||
                $routeName == 'editbuyer' || $routeName == 'createbuyerquote' || $routeName == 'ptlcreatebuyerquote' 
                || $routeName == 'gettermpostbuyercounteroffer'|| $routeName=='biddateeditform'
                || $routeName == 'relocationcreatebuyerpost' || $routeName == 'bideditdraftform' || $routeName == 'editbuyerquoteseller' )
                
       {{--*/ $postClass = '1'; /*--}}
       
 @else {{--*/ $postClass = '0'; /*--}}
  @endif 
  
        @if( $routeName == 'buyerorders' || $routeName == 'buyerordershowdetails')
                
       {{--*/ $orderClass = '1'; /*--}}
       
 @else {{--*/ $orderClass = '0'; /*--}}
  @endif 
  
  
 
  @endif 
 
 @if(Auth::user()->lkp_role_id == SELLER)
           
            @if($routeName == 'sellerpostdetails' || $routeName == 'sellerpostslist' || $routeName== 'sellerlists' || $routeName== 'createseller' || $routeName == 'updateseller' || $routeName == 'ptlcreatesellerpost' || $routeName == 'sellerpostdetails' || $routeName == 'sellerlists' || $routeName == 'relocationcreatesellerpost')
            
            {{--*/     $sellerpostClass = '1'  /*--}}
		    @else
		    {{--*/    $sellerpostClass = '0' /*--}}
		    @endif

    
    
    @if($routeName == 'sellerorders' || $routeName == 'buyerorders' || $routeName == 'showdetails' || $routeName == 'buyerordershowdetails' || $routeName == 'consignmentpickup')
     {{--*/  $orderClass = '1'  /*--}}
    @else
      {{--*/    $orderClass = '0'  /*--}}
    @endif
    
     @if($routeName == 'viewzone')
       
      {{--*/  $masterClass = '1'  /*--}}
    @else
      {{--*/   $masterClass = '0'  /*--}}
     @endif
     
     @if( $routeName == 'index' || $routeName == 'networkfeeds' || $routeName == 'networkprofile' || $routeName=='listofrecomendations' || $routeName=='listofpartners' || $routeName=='listofjobs' || $routeName=='listofarticles')
       
      {{--*/  $networkClass = '1'  /*--}}
    @else
      {{--*/   $networkClass = '0'  /*--}}
     @endif
     
  @endif  

		<div class="top-menu-strip">
            <div class="container">
                <div class="pull-left">
                    <input type="hidden" name="settabpage" id="settabpage" value=""/>
                    <a @if($homeClass == '1') class="active" @endif href="/home">Home</a>
                    <a @if($messageClass == '1') class="active" @endif href="{{ url('/messages') }}">Messages</a>
                    @if((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
                        @if($pageServiceId == 0)
                            <a  data-toggle="modal" data-target="#change-service" onclick="setTabsPage('/buyerposts')">Posts</a>
                        @else
                            <a  @if($postClass == '1') class="active" @endif href="javascript:void(0)" onclick="return checkSession({{$pageServiceId}},'/buyerposts');">Posts</a>
                        @endif
                    @elseif((Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER))
                         @if($pageServiceId == 0)
                            <a  data-toggle="modal" data-target="#change-service" onclick="setTabsPage('/sellerlist')">Posts</a>
                        @else
                            <a @if($sellerpostClass == '1') class="active" @endif href="javascript:void(0)" onclick="return checkSession({{$pageServiceId}},'/sellerlist');">Posts</a>
                        @endif
                    @endif
                    
                   @if((Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER))
                        @if($pageServiceId == 0)
                            <a  data-toggle="modal" data-target="#change-service" onclick="setTabsPage('/orders/seller_orders')">Orders</a>
                        @else
                            <a  @if($orderClass == '1') class="active" @endif href="javascript:void(0)" onclick="return checkSession({{$pageServiceId}},'/orders/seller_orders');">Orders</a>
	                    @endif
                   @elseif((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
                        @if($pageServiceId == 0)
                            <a  data-toggle="modal" data-target="#change-service" onclick="setTabsPage('/orders/buyer_orders')">Orders</a>
                        @else
              		        <a  @if($orderClass == '1') class="active" @endif  href="javascript:void(0)"  onclick="return checkSession({{$pageServiceId}},'/orders/buyer_orders');">Orders</a>
                        @endif
                   @endif
<!--                     <a  @if($networkClass == '1') class="active" @endif href="javascript:void(0)">Network</a> -->
                     {{--*/ $url = "/network" /*--}}
                    @if((Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER))
                    	<a @if($networkClass == '1') class="active" @endif href={{ $url }} >Network</a>
                   @elseif((Auth::user()->lkp_role_id == BUYER && (Session::get('last_login_role_id')== 0)) || (Session::get('last_login_role_id')== BUYER))
						<a  @if($networkClass == '1') class="active" @endif href={{ $url }} >Network</a>
                   @endif
                   
                </div>
                <div class="pull-right">
                
                 @if( Auth::user()->lkp_role_id == BUYER )
                {{--*/    $roleClass = 'B'  /*--}}
                 @elseif( Auth::user()->lkp_role_id == SELLER )
                  {{--*/    $roleClass = 'S'  /*--}}
               
                 @endif
                 @if(Session::get('last_login_role_id') != 0 && Session::get('last_login_role_id')!=null)
                 @if( Auth::user()->lkp_role_id == BUYER )
                 	@if( Session::get('last_login_role_id') == 1 )
                    <a class="active-user-type" href="javascript:void(0);" id="switchRole" data-role="1" data-role="1">Buyer</a>
                    <a class="inactive-user-type" href="javascript:void(0);" id="activate_{{ $roleClass }}seller">Seller</a>
                    @elseif( Session::get('last_login_role_id') == 2 )
                    <a class="inactive-user-type" href="javascript:void(0);" id="switchRole" data-role="1">Buyer</a>
                    <a href="javascript:void(0);" class="active-user-type" id="activate_{{ $roleClass }}seller">Seller</a>
                    @endif
                 
                 @elseif( Auth::user()->lkp_role_id == SELLER )
                 	@if( Session::get('last_login_role_id') == 1 )
                    <a class="active-user-type" href="javascript:void(0);" id="activate_{{ $roleClass }}buyer">Buyer</a>
                    <a class="inactive-user-type" href="javascript:void(0);" id="switchRole" data-role="2">Seller</a>
                    @elseif( Session::get('last_login_role_id') == 2 )
                    <a class="inactive-user-type" href="javascript:void(0);" id="activate_{{ $roleClass }}buyer">Buyer</a>
                    <a href="javascript:void(0);" class="active-user-type" id="switchRole" data-role="2">Seller</a>
                    @endif
                 @endif
                
                    
                
                
                    
                    
               @endif  
                              
                    
					
					 

                </div>
            </div>
        </div>       
@endif
