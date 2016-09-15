@extends('network_app')
@inject('commoncomponent', 'App\Components\CommonComponent')

@section('content')
<div class="container-inner">
	
	<!-- Page Center Content Starts Here -->
	<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
		<div class="block">
			<div class="tab-nav underline">
				@include('partials.network_page_top_navigation')
			</div>
		</div>
	</div>
		
		
	  <div class="main">
               <div class="container">
                  <div class="crum-2"><i class="fa  fa-angle-right"></i>
                  <a href="/network">Network</a><i class="fa  fa-angle-right"></i> <a href="/network/profile/{{$id}}">Profile<i class="fa  fa-angle-right"></i> <a href="javascript:void(0)">Partners</a></div>
                  <span class="pull-left">
                     <h1 class="page-title">Partners</h1>
                  </span>
                  <div class="gray-bg network">
                     <div class="col-md-12 network-info">
                        <div class="col-md-12 padding-right-none">
                           <div class="pull-right list-info">
                           <span class="black">Partners ({{count($partnersrequestlist) + count($personalpartnersrequestlist)}}) </span>
                           
                           </div>
                           
                        </div>
                     </div>
                     <div class="col-md-12 news-feed partners-list padding-left-none">
                     @if(count($partnersrequestlist)>0)
                     @foreach($partnersrequestlist as $partnerslist)
                        <div class="col-md-4 padding-right-none">
                           <div class="partner">
                              <div class="pull-left">
                             
                                 <div class="profile-pic">
                                 
                                 @if($partnerslist->lkp_role_id == 2)
									{{--*/ $url = "uploads/seller/$partnerslist->user_id/" /*--}}
						        @else
						        	{{--*/ $url = "uploads/buyer/$partnerslist->user_id/" /*--}}
						        @endif
						        {{--*/ $getlogo = $url.$partnerslist->user_pic /*--}}
						        {{--*/ $logo =  $commoncomponent::str_replace_last( '.' , '_94_92.' , $getlogo ) /*--}}
						       
				        		 @if(file_exists($logo))
				        			<img src="{{url($logo)}}"/ >
						        @else
                                	<i class="fa fa-user"></i>
                                @endif 
                                 
                                 
                                 </div>
                              </div>
                              <div class="col-md-8 padding-right-none">
                                 <div class="col-md-12 padding-left-none">
                                    <span class="user-name"><a href="/network/profile/{{$partnerslist->user_id }}"><strong>{{$partnerslist->username}}</strong></a><br>
                                    <!--<span class="sub-link">Cource Director at DCC</span>-->
                                 </div>
                                  
                              </div>
                           </div>
                        </div>
                       @endforeach
                      @endif
                       @if(count($personalpartnersrequestlist)>0)
                     @foreach($personalpartnersrequestlist as $personalpartnersrequestlist)
                        <div class="col-md-4 padding-right-none">
                           <div class="partner">
                              <div class="pull-left">
                                 <div class="profile-pic">
                                 
                                 @if($personalpartnersrequestlist->lkp_role_id == 2)
									{{--*/ $url = "uploads/seller/$personalpartnersrequestlist->user_id/" /*--}}
						        @else
						        	{{--*/ $url = "uploads/buyer/$personalpartnersrequestlist->user_id/" /*--}}
						        @endif
						        {{--*/ $getlogo = $url.$personalpartnersrequestlist->user_pic /*--}}
						        {{--*/ $logo =  $commoncomponent::str_replace_last( '.' , '_94_92.' , $getlogo ) /*--}}
						       
				        		 @if(file_exists($logo))
						        	<img src="{{url($logo)}}"/ >
						        @else
                                <i class="fa fa-user"></i>
                                @endif 
                                 
                                 </div>
                              </div>
                              <div class="col-md-8 padding-right-none">
                                 <div class="col-md-12 padding-left-none">
                                    <span class="user-name"><a href="/network/profile/{{$personalpartnersrequestlist->user_id }}"><strong class="red">{{$personalpartnersrequestlist->username}}</strong><br>
                                    <!--<span class="sub-link">Cource Director at DCC</span>-->
                                 </div>
                                  	
                              </div>
                           </div>
                        </div>
                       @endforeach
                      @endif
                      
                     </div>
                  </div>
               </div>
            </div>
         <div class="clearfix"></div>
          
             
	
</div>
@include('partials.footer')
@endsection

