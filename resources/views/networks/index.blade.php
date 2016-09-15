@extends('network_app')

@section('content')
@inject('commoncomponent', 'App\Components\CommonComponent')
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
         <a href="/network">Network</a></div>
         <span class="pull-left">
            <h1 class="page-title">Network</h1>
         </span>
         
         <div class="gray-bg network">
            
            {{-- Network Header file --}}
            @include('networks/header')            

            <div class="col-md-12">
               <ul class="nav-tabs">
                  <li class="active"><a data-toggle="tab" href="#news-feed">News Feed</a></li>
                  <li><a data-toggle="tab" href="#connections">Connections</a></li>
                  <li><a data-toggle="tab" href="#services">Services</a></li>
               </ul>
               
               {{-- Profile and Potential Partners --}}
               @include('networks/profile-potential-count')
               
               <div class="tab-content">
                  
                  {{-- Network news feed section --}}
                  @include('networks/news-feed')
                  
                  <div id="connections" class="tab-pane fade">
                              <div class="clearfix"></div>
                              <div class="col-md-3 padding-none">
                                 <ul class="nav-tabs connections-tabs">
                                    <li class="active"><a data-toggle="tab" href="#partners">Partners</a></li>
                                    <li><a data-toggle="tab" href="#following">Following</a></li>
                                    <li><a data-toggle="tab" href="#groups">Groups (Memberships)</a></li>
                                 </ul>
                              </div>
                              <div class="col-md-9 padding-none">
                                 <div class="clearfix"></div>
                                 <div class="tab-content connections-content-tabs">
                                    <div id="partners" class="tab-pane fade in active">
                                       <div class="inner-block-bg">
                                          <div class="col-md-6 form-control-fld padding-none">
                                             <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-search"></i></span>
                                                <input type="text" placeholder="Search" name="partner_search" id="partner_search"  class="form-control">
                                             </div>
                                          </div>
                                          <div class="col-md-1 form-control-fld">
                                             <button class="btn add-btn" id="btnpartner" type="submit">Go</button>
                                          </div>
                                          <div class="clearfix"></div>
                                          <div id="myCarousel1" class="carousel slide" data-ride="carousel1">
                                             <!-- Wrapper for slides -->
                                             <div class="carousel-inner" role="listbox">
                                                {{--*/ $g =1; $ga=1; /*--}}
                                             @foreach($getLogoPartners as $key=>$getLogoPartner)
                                             
                                             @if($getLogoPartner->user_id == Auth::user()->id)
                                             {{--*/ $getLogoPartner->partner_user_id = $getLogoPartner->partner_user_id /*--}}
                                             @else
                                             {{--*/ $getLogoPartner->partner_user_id = $getLogoPartner->user_id /*--}} 
                                             @endif
                                             
                                          		{{--*/ $getLogo = $getLogoPartner->logo; $active=''; /*--}}
                                          		
                                          		{{--*/ $partner_id_profile = "network/profile/$getLogoPartner->partner_user_id" /*--}}
                                          		
                                          		@if($getLogoPartner->lkp_role_id == 2)
                                          		{{--*/ $url = "uploads/seller/$getLogoPartner->partner_user_id/" /*--}}
                                          		@else
                                          		{{--*/ $url = "uploads/buyer/$getLogoPartner->partner_user_id/" /*--}}
                                          		@endif
                                          		{{--*/ $getlogopartner = $url.$getLogo /*--}}
                                          		
                                          		
                                          		
                                          		
                                          		@if($ga==1)
                                          			{{--*/ $active = 'active' /*--}}
                                          		@endif
                                          		
                                          		
                                          		 @if ($g%5 == 1)
											      
											     <div class="item {{$active}}">
                                                <ul>   
											    @endif
											    
											    @if($getLogo != '')
												    @if(\File::exists($getlogopartner))
												    
												    {{--*/ $getlogopartner =  $commoncomponent::str_replace_last( '.' , '_124_73.' , $getlogopartner ) /*--}}
												    <a href='{{url($partner_id_profile)}}'>
												    <li class ='descriptionuser' id ='{{ $getLogoPartner->partner_user_id }}'>
												    <img id ='{{ $getLogoPartner->partner_user_id }}' src="{{url($getlogopartner)}}"></li>
												    </a>
												    @else
												    <a href='{{url($partner_id_profile)}}'>
												    <li class ='descriptionuser' id ='{{ $getLogoPartner->partner_user_id }}'>
												    <img id ='{{ $getLogoPartner->partner_user_id }}' src="../images/profile-pic.png">
												    </li>
												    </a>
												    @endif
												@else
											    <a href='{{url($partner_id_profile)}}'>
											    <li class ='descriptionuser' id ='{{ $getLogoPartner->partner_user_id }}'>
											    <img id ='{{ $getLogoPartner->partner_user_id }}' src="../images/profile-pic.png">
											    </li>
											    </a>
											    @endif
											   @if ($g%5 == 0)
											    
											    </ul>
											    </div>
											    @endif
											    {{--*/ $g++; $ga++/*--}}
											  @endforeach
											
											@if ($g%5 != 1)
												</ul>
                                                </div>
                                             @endif
                                             </div>
                                             <!-- Left and right controls -->
                                             <a class="left carousel-control" href="#myCarousel1" role="button" data-slide="prev">
                                             <i class="fa fa-chevron-left"></i>
                                             </a>
                                             <a class="right carousel-control" href="#myCarousel1" role="button" data-slide="next">
                                             <i class="fa fa-chevron-right"></i>
                                             </a>
                                          </div>
                                          <div class="clearfix"></div>
                                          <div id='partner-div'>
                                          <h3>Description</h3>
                                          <p id='desctiption_partner'></p>
                                          </div>
                                          <div class ='nodata-partners'>No Partners are Available</div>
                                       </div>
                                       
                                    </div>
                                    <div id="following" class="tab-pane fade">
                                    {{--*/ $countFollowers = count($getLogoFollowers) /*--}}
                                       <div class="inner-block-bg">
                                          <div class="col-md-6 form-control-fld padding-none">
                                             <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-search"></i></span>
                                                <input type="text" placeholder="Search" name="follwing_search" id="follwing_search" class="form-control">
                                              </div>
                                          </div>
                                          <div class="col-md-2 form-control-fld">
                                             <button class="btn add-btn" id="btnfollwing" type="submit">Go</button>
                                          </div>
                                          <div class="clearfix"></div>
                                          <div id="myCarousel2" class="carousel slide" data-ride="carousel2">
                                             <!-- Wrapper for slides -->
                                             <div class="carousel-inner" role="listbox">
                                                 {{--*/ $f = 1; $fa=1; /*--}}
                                             @foreach($getLogoFollowers as $key=>$getLogoFollower)
                                          	 {{--*/ $getLogo = $getLogoFollower->logo; $active=''; /*--}}
                                          	 
                                          	 {{--*/ $follower_user_id_profile = "network/profile/$getLogoFollower->follower_user_id" /*--}}
                                          	 
                                          	 @if($getLogoFollower->lkp_role_id == 2)
                                          		{{--*/ $url = "uploads/seller/$getLogoFollower->follower_user_id/" /*--}}
                                          		@else
                                          		{{--*/ $url = "uploads/buyer/$getLogoFollower->follower_user_id/" /*--}}
                                          		@endif
                                          		{{--*/ $getlogofollower = $url.$getLogo /*--}}
                                          	 
                                          	 
                                          	 
                                          	 
                                          		@if($fa==1)
                                          			{{--*/ $active = 'active' /*--}}
                                          		@endif
                                          		
                                          		
                                          		 @if ($f%5 == 1)
											      
											     <div class="item {{$active}}">
                                                <ul>   
											    @endif
											    
											    
											     @if($getLogo != '')
												     @if(\File::exists($getlogofollower))
												     
												     {{--*/ $getlogofollower =  $commoncomponent::str_replace_last( '.' , '_124_73.' , $getlogofollower ) /*--}}
												     <a href='{{url($follower_user_id_profile)}}'>
												    <li class ='descriptionfollows' id ='{{ $getLogoFollower->follower_user_id }}'>
												    <img id ='{{ $getLogoFollower->follower_user_id }}' src="{{url($getlogofollower)}}"></li>
												    </a>
												     @else
												     
												    <a href='{{url($follower_user_id_profile)}}'>
												    <li class ='descriptionfollows' id ='{{ $getLogoFollower->follower_user_id }}'>
												    <img id ='{{ $getLogoFollower->follower_user_id }}' src="../images/profile-pic.png">
												    </li>
												    </a>
												    @endif
											    
											     @else
											     
											    <a href='{{url($follower_user_id_profile)}}'>
											    <li class ='descriptionfollows' id ='{{ $getLogoFollower->follower_user_id }}'>
											    <img id ='{{ $getLogoFollower->follower_user_id }}' src="../images/profile-pic.png">
											    </li>
											    </a>
											    @endif
											   @if ($f%5 == 0)
											    
											    </ul>
											    </div>
											    @endif
											    {{--*/ $f++; $fa++/*--}}
											  @endforeach
											
											@if ($f%5 != 1)
												</ul>
                                                </div>
                                             @endif
                                             </div>
                                             <!-- Left and right controls -->
                                             <a class="left carousel-control" href="#myCarousel2" role="button" data-slide="prev">
                                             <i class="fa fa-chevron-left"></i>
                                             </a>
                                             <a class="right carousel-control" href="#myCarousel2" role="button" data-slide="next">
                                             <i class="fa fa-chevron-right"></i>
                                             </a>
                                          </div>
                                          <div class="clearfix"></div>
                                          <div id='follows-div'>
                                          <h3>Description</h3>
                                          <p id='desctiption_follows'></p>
                                       </div>
                                       <div class ='nodata-followers'>No Followers are Available</div>
                                       </div>
                                       
                                       
                                    </div>
                                    
                                   
                                    
                                    <div id="groups" class="tab-pane fade">
                                    
                                    {{--*/ $count_groups = count($getLogoCommunitygroups) /*--}}
                                       <div class="inner-block-bg">
                                          <div class="col-md-6 form-control-fld padding-none">
                                             <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-search"></i></span>
                                                <input type="text" placeholder="Search" name="group_search" id="group_search" class="form-control">
                                             </div>
                                          </div>
                                          <div class="col-md-2 form-control-fld">
                                             <button class="btn add-btn" id="btngroup" type="submit">Go</button>
                                          </div>
                                          <div class="clearfix"></div>
                                          <div id="myCarousel3" class="carousel slide" data-ride="carousel3">
                                             <!-- Wrapper for slides -->
                                             <div class="carousel-inner" role="listbox">
                                             {{--*/ $id = Auth::User()->id /*--}}
                                             {{--*/ $p = 1; $pa=1; $url = "uploads/community/groups/$id/" /*--}}
                                             @foreach($getLogoCommunitygroups as $key=>$getLogoCommunitygroup)
                                          	 	{{--*/ $getLogogroup = $getLogoCommunitygroup->logo_file_name; $active=''; /*--}}
                                          	 	{{--*/ $getLogogroup = $url.$getLogogroup /*--}}
                                          	 	
                                          	 	{{--*/ $group_user_id_profile = "community/groupdetails/$getLogoCommunitygroup->id" /*--}}
                                          	 	
                                          	 	
                                          		@if($pa==1)
                                          			{{--*/ $active = 'active' /*--}}
                                          		@endif
                                          		@if ($p%5 == 1)
											         <div class="item {{$active}}">
                                                	 	<ul>   
											    @endif
											    @if($getLogogroup != '')
												    @if(\File::exists($getLogogroup))
												    
												    <a href='{{url($group_user_id_profile)}}'>
												    <li class ='descriptiongroups' id ='{{ $getLogoCommunitygroup->id }}'>
												    <img id ='{{ $getLogoCommunitygroup->id }}' src="{{url($getLogogroup)}}"></li>
												    </a>
												     @else
												     <a href='{{url($group_user_id_profile)}}'>
												    <li class ='descriptiongroups' id ='{{ $getLogoCommunitygroup->id }}'>
												    <img id ='{{ $getLogoCommunitygroup->id }}' src="../images/profile-pic.png">
												    </li>
												    </a>
												    @endif
											    
											     @else
											     <a href='{{url($group_user_id_profile)}}'>
											    <li class ='descriptiongroups' id ='{{ $getLogoCommunitygroup->id }}'>
											    <img id ='{{ $getLogoCommunitygroup->id }}' src="../images/profile-pic.png">
											    </li>
											    </a>
											    @endif
											    @if ($p%5 == 0)
											    </ul>
                                                </div>
											    @endif
											    
											    {{--*/ $p++; $pa++/*--}}
											@endforeach
											
											@if ($p%5 != 1)
													</ul>
                                                </div>
                                             @endif
                                             </div>
                                             <!-- Left and right controls -->
                                             <a class="left carousel-control" href="#myCarousel3" role="button" data-slide="prev">
                                             <i class="fa fa-chevron-left"></i>
                                             </a>
                                             <a class="right carousel-control" href="#myCarousel3" role="button" data-slide="next">
                                             <i class="fa fa-chevron-right"></i>
                                             </a>
                                          </div>
                                          <div class="clearfix"></div>
                                          <div id='groups-div'>
                                          <h3 id='desctiption_groups_head'>Description</h3>
                                          <p  id='desctiption_groups'></p>
                                       </div>
                                       <div class ='nodata-groups'>No Groups are Available</div>
                                       </div>
                                       
                                    </div>
                                 </div>
                              </div>
                           </div>

                  <div id="services" class="tab-pane fade">
                     <ul class="nav-tabs inner-tabs">
                        <li class="active"><a data-toggle="tab" href="#offered">Services Offered</a></li>
                        <li><a data-toggle="tab" href="#required">Services Required</a></li>
                     </ul>
                     <div class="clearfix"></div>
                     <div class="tab-content feed-tab-content">
                        <div id="offered" class="tab-pane fade in active">
                           <div class="inner-block-bg padding-10">
                              {{--*/ $count_services_offered = count($servicesOfferlists) /*--}}
                              
                              @if($count_services_offered>0)
                              <div>
								<ul>
                              @foreach($servicesOfferlists as $key=>$servicesOfferlist)
											            
							<li><a onclick="return subcriptionuserservice({{ $key }},'/sellerlist')">{{ $commoncomponent::getGroupName($key) }} {{ $servicesOfferlist }}</a></li>
											    

							@endforeach
								</ul>
							</div>
							@else
							
							<div>This User No Services Offered</div>
							
							@endif
                           </div>
                        </div>
                        <div id="required" class="tab-pane fade">
                           <div class="inner-block-bg padding-10">                              
                              {{--*/ $count_services_required = count($servicesRequriedlists) /*--}}
                              
                              @if($count_services_required>0)
                              <div>
								<ul>
                              @foreach($servicesRequriedlists as $key=>$servicesRequriedlist)
											            
							<li><a onclick="return buyersetservice({{ $key }},'/buyerposts')">{{ $commoncomponent::getGroupName($key) }} {{ $servicesRequriedlist }}</a></li>
											    

							@endforeach
								</ul>
							</div>
							@else
							
							<div>This User No Services Required</div>
							
							@endif
                              
                              
                           </div>
                        </div>
                     </div>
                  </div>

               </div>
            </div>
         </div>
      </div>
   </div>
   <div class="clearfix"></div>
		
</div>
@include('partials.footer')
@endsection