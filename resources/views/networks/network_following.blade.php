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
                  <a href="/network">Network</a><i class="fa  fa-angle-right"></i> <a href="/network/profile/{{$id}}">Profile<i class="fa  fa-angle-right"></i> <a href="javascript:void(0)">Following</a></div>
                  <span class="pull-left">
                     <h1 class="page-title">Following</h1>
                  </span>
                  <div class="gray-bg network">
                     <div class="col-md-12 network-info">
                        <div class="col-md-12 padding-right-none">
                           <div class="pull-right list-info"><span class="black">Following ({{count($getfollow)}}) </span></div>
                        </div>
                     </div>
                     <div class="col-md-12 news-feed partners-list padding-left-none">
                     @if(count($getfollow)>0)
                     @foreach($getfollow as $getfollow)
                        <div class="col-md-4">
                           <div class="partner">
                              <div class="pull-left">
                                 <div class="profile-pic">
                                
                                 @if($getfollow->lkp_role_id == 2)
									{{--*/ $url = "uploads/seller/$getfollow->userid/" /*--}}
						        @else
						        	{{--*/ $url = "uploads/buyer/$getfollow->userid/" /*--}}
						        @endif
						        {{--*/ $getlogo = $url.$getfollow->user_pic /*--}}
						        {{--*/ $logo =  $commoncomponent::str_replace_last( '.' , '_94_92.' , $getlogo ) /*--}}
						       
				        		 @if(file_exists($getlogo))
						        	<img src="{{url($logo)}}"/ >
						        @else
                               		<i class="fa fa-user"></i>
                                @endif 
                                
                                 </div>
                              </div>
                              <div class="col-md-8 padding-right-none">
                                 <div class="col-md-12 padding-left-none">
                                    <span class="user-name"><a href="/network/profile/{{$getfollow->userid }}"><strong title="{{$getfollow->username}}">{{$getfollow->username}}</a></strong><br>
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

