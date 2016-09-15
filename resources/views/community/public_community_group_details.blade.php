@extends('community_app')
@section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('communityComponent', 'App\Components\community\CommunityComponent')
{{--*/ $members =   $commonComponent->getMembers($displayGroupDetails->id) /*--}}
{{--*/ $href="/community/groupdetails/".$displayGroupDetails->id /*--}}
{{--*/ $membersCheck =   $commonComponent->getMemberCheck($displayGroupDetails->id) /*--}}
<!-- Page Center Content Starts Here -->
	<div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">
		<div class="block">
			<div class="tab-nav underline">
				@include('partials.community_page_top_navigation')
			</div>
		</div>
	</div>	
         <!-- Inner Menu Ends Here -->
         
            <div class="main">
            
            @if(Session::has('gcmsg')) 
	        <div class="flash">
			<p class="text-success col-sm-12 text-center flash-txt alert-success">
			{{ Session::get('gcmsg') }}
			</p>
			</div>
			@endif
			
               <div class="container">
                  <div class="crum-2">
               <i class="fa  fa-angle-right"></i> Seller <i class="fa  fa-angle-right"></i> <a href="/home">Home</a> <i class="fa  fa-angle-right"></i> Community <i class="fa  fa-angle-right"></i> Group View</div>
                  <span class="pull-left">
                     <h1 class="page-title">Group View</h1>
                  </span>
                  <div class="gray-bg network community">
                     <div class="col-md-12 network-info">
                        <div class="col-md-1 padding-none">
                            {{--*/ $userId = Auth::id() /*--}}  
                        @if($displayGroupDetails->logo_file_name!='' && file_exists("uploads/community/groups/".$displayGroupDetails->created_by."/".$displayGroupDetails->logo_file_name))
                           
                           <div class="profile-pic">                                               	 
                          	<img class="img-responsive" src="{{ asset('uploads/community/groups/'.$displayGroupDetails->created_by.'/'.$displayGroupDetails->logo_file_name) }}">
                           </div>
                           @else
                            <div class="profile-pic">                           	  
                                <img class="img-responsive" src="{{URL::asset('images/org-logo.png')}}">                          	  
                            </div>
                           @endif                           
                        </div>
                        <div class="col-md-11 padding-right-none">
                           <div class="col-md-5 title padding-none">
                              <span> {!! $displayGroupDetails->group_name !!}</span><br>
                              <span class="sub-link">{{count($members)}} members</i>
                              </span>
                           </div>
                            
                           @if(!empty($membersCheck) && $membersCheck->is_invited==1)
                           <div class="pull-right">
                               <button class="btn red-btn pull-right request_sent" id="{{$displayGroupDetails->id}}" href="{{$href}}">Request Pending</button>
                           </div>
                           @else
                           <div class="pull-right">
                               <button class="btn red-btn pull-right member_button" id="{{$displayGroupDetails->id}}" href="{{$href}}">Become a Member</button>
                           </div>
                            @endif
                            

                        </div>
                     </div>
                     <div class="clearfix"></div>
                     <div class="col-md-12 about-group">
                        <h3>About This Group</h3>
                        <div class="accordion-group about-text group_desc">
                           <div class="accordion-heading">
                             {{--*/   $desc = substr($displayGroupDetails->description,0,250) /*--}}
                             {!! $desc !!}   
                           </div>
                            <div id="collapseOne_hide" class="displayNone" >
                              {!! $desc !!}
                           </div>
                           <div id="collapseOne_show" class="displayNone">
                              <br>{!! $displayGroupDetails->description !!}
                           </div>
                            @if(strlen($displayGroupDetails->description)>250)
                           <span class="more-text about_group SeeMore">Show More</span>
                           @endif
                        </div>
                        <div class="col-md-12 padding-none">
                           <h3 class="pull-left">Members</h3>
                           <div class="pull-right">{{count($members)}} members</div>

                           <div class="clearfix"></div>
                           <div class="members-list">
                              <ul>
                                  @foreach($grpmemberpartners as $partner)
                                 <li>
                                    <div class="user-pic">
                                        <a href="/network/profile/{{$partner->id}}">
                                        {{--*/  $profiledetails = $commonComponent->getUserDetails($partner->id) /*--}}
                                            
                                        @if($profiledetails->lkp_role_id == 2)
                                        {{--*/ $url = "uploads/seller/$profiledetails->id/" /*--}}
                                        @else
                                        {{--*/ $url = "uploads/buyer/$profiledetails->id/" /*--}}
                                        @endif

                                        {{--*/ $getlogo = $url.$profiledetails->user_pic /*--}}
                                        {{--*/ $logo =  $commonComponent->str_replace_last( '.' , '_40_40.' , $getlogo ) /*--}}
                                                  
                                        @if(isset($partner->logo) && $partner->logo!='' && file_exists($logo))
                                        <img src="{{ asset($partner->logo) }} ">
                                        @else
                                        <i class="fa fa-user"></i>
                                        @endif
                                    </a>
                                    </div>
                                 </li>
                                 @endforeach
                              </ul>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
          
@include('partials.footer')
@endsection