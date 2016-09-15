{{--*/ $routeName = strtolower(substr(class_basename(Route::currentRouteAction()), (strpos(class_basename(Route::currentRouteAction()), '@') + 1))) /*--}}
@inject('common', 'App\Components\CommonComponent')

{{--*/ $pageServiceId = Session::get('service_id')  /*--}}
@if($pageServiceId == 0 || $pageServiceId == '')
{{--*/ $pageServiceId = '0'  /*--}}
@endif

 @if($routeName == 'buyersearch' || $routeName == 'buyersearchresults' ||
     $routeName == 'sellersearchbuyers' || $routeName == 'sellersearchresults')
       {{--*/ $classname = "main-active"  /*--}}
@else
       {{--*/ $classname = ""  /*--}}
@endif

<div class="col-lg-3 col-md-3 col-sm-4 col-xs-12 main-right">
				<span class="left-top-text text-center">Help Desk</span>
				<div class="block text-center">
					<h3 class="block-head">
					@if(Auth::user()->lkp_role_id == SELLER)				
 
					<a href="javascript:void(0);" class="{{$classname}}" onclick="return checkSession({{$pageServiceId}},'/sellersearchbuyers');" >Services</a>
					@elseif(Auth::user()->lkp_role_id == BUYER)
					<a href="javascript:void(0);" class="{{$classname}}" onclick="return checkSession({{$pageServiceId}},'/buyersearch');">Services</a>
				    @endif
					</h3>
					<h3 class="block-head"><a href="#">Community</a></h3>
					<h3 class="block-head"><a href="#">Effortless Transportation - {!! $common->getServiceName(Session::get('service_id')) !!}</a></h3>
					<div class="clearfix"></div>
				</div>
				<div class="block">
					<ul class="right-menu">
						<li><a href="#"><img src="{{url('images/right-icon1.png')}}" /> Manage Ship Cycle</a></li>
					<li><a href="#"><img src="{{url('images/right-icon2.png')}}" /> Minimize Shipping Cost </a></li>
					<li><a href="#"><img src="{{url('images/right-icon3.png')}}" /> Competitive Pricing </a></li>
					<li><a href="#"><img src="{{url('images/right-icon4.png')}}" /> Carrier Selection</a></li>
					<li><a href="#"><img src="{{url('images/right-icon5.png')}}" /> Door Pickup & Door Delivery </a></li>
					<li><a href="#"><img src="{{url('images/right-icon3.png')}}" /> Get Quote & Negotiate Online</a></li>
					<li><a href="#"><img src="{{url('images/right-icon4.png')}}" /> Track & Trace Consignment</a></li>
					<li><a href="#"><img src="{{url('images/right-icon6.png')}}" /> 24x7 Customer Support</a></li>
					</ul>
					<div class="clearfix"></div>
				</div>
			</div>
<!-- Right Content Ends Here -->
