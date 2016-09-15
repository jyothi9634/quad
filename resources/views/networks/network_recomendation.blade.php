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
                  <a href="/network">Network</a><i class="fa  fa-angle-right"></i> <a href="/network/profile/{{$id}}">Profile<i class="fa  fa-angle-right"></i> <a href="javascript:void(0)">Recommendations</a></div>
                  <span class="pull-left">
                     <h1 class="page-title">Recommendations</h1>
                  </span>

                  <div class="gray-bg network padding-20">
                     <div class="col-md-12 network-info padding-none">
                        <div class="col-md-1 padding-none">
                           <div class="profile-pic"><i class="fa fa-user"></i></div>
                        </div>
                        <div class="col-md-11 padding-right-none" id="recomends_links">
                           <div class="col-md-5 title padding-none">
                              <span>Recommendations for {!! $commoncomponent::getUsername($id) !!}	</span><br>
                              
                           </div>
                           <div class="pull-right list-info">
                           <span data-showdiv="recomendation_received" class="red">Received ({{count($recomendationlist)}}) </span> 
                           <span data-showdiv="recomendation_given">Given ({{count($recomendationgiven)}})</span>
                           
                        </div>
                     </div>
					<div class="col-md-12 padding-none" id="recomendation_received">
					@if(count($recomendationlist)>0)
					@foreach($recomendationlist as $recomlist)
                     
                     <div class="col-md-12 news-feed recommendations-list">
                        <div class="col-md-1 padding-none">
                           <div class="profile-pic"><i class="fa fa-user"></i></div>
                        </div>

                        <div class="col-md-11 padding-right-none">
                                       <div class="col-md-12 padding-left-none padding-right-none">
                                          <span class="user-name"><strong> {!! $commoncomponent::getUsername($recomlist->user_id) !!}</strong>
                                          <p>{{$recomlist->recommendation_description}}</p>
                                          @if($recomlist->is_approved == 1)
                                         
                                          	<a href="javascript:void(0)" data-target="#partnerRequest" data-toggle="modal"  class="btn post-btn pull-right"><i class="fa fa-check-square-o"></i>Approved</a>
                                         
                                          @else
                                          @if($id == Auth::user()->id)
                                          <a href="javascript:void(0)" data-target="#partnerRequest" data-toggle="modal" onclick="approverecommend({{ $recomlist->user_id }},1,1)" class="btn red-btn pull-right">Approve</a>
                                          @else
                                          <a href="javascript:void(0)" data-target="#partnerRequest" data-toggle="modal" class="btn red-btn pull-right">Approve</a>
                                          @endif
                                          
                                          @endif
                                       </div>
                                    </div>
                        
                     </div>
                    
                     @endforeach
                     @endif
                     </div>
                     <div class="col-md-12 padding-none" id="recomendation_given" style="display:none;">
                     @if(count($recomendationgiven)>0)
					 @foreach($recomendationgiven as $recomendationgiven)
                     
                     <div class="col-md-12 news-feed recommendations-list">
                        <div class="col-md-1 padding-none">
                           <div class="profile-pic"><i class="fa fa-user"></i></div>
                        </div>

                        <div class="col-md-11 padding-right-none">
                        	<div class="col-md-12 padding-left-none padding-right-none">
                            	<span class="user-name"><strong> {!! $commoncomponent::getUsername($recomendationgiven->user_id) !!}</strong>
                                <p>{{$recomendationgiven->recommendation_description}}</p>
                                @if($recomendationgiven->is_approved == 1)
                                	
                                	<a href="javascript:void(0)" data-target="#partnerRequest" data-toggle="modal"  class="btn post-btn pull-right"><i class="fa fa-check-square-o"></i>Approved</a>
                                	
                                @else
                                 @if($id == Auth::user()->id)
                                	<a href="javascript:void(0)" data-target="#partnerRequest" data-toggle="modal" onclick="approverecommend({{ $recomendationgiven->recommended_to }},1,2)" class="btn red-btn pull-right">Request Sent</a>
                                 @else
                                 <a href="javascript:void(0)" data-target="#partnerRequest" data-toggle="modal" class="btn red-btn pull-right">Request Sent</a>
                                 @endif      
                                @endif
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

