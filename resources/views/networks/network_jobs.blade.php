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
                  <a href="/network">Network</a><i class="fa  fa-angle-right"></i> <a href="/network/profile/{{$id}}">Profile<i class="fa  fa-angle-right"></i> <a href="javascript:void(0)">Jobs</a></div>
                  <span class="pull-left">
                     <h1 class="page-title">Jobs</h1>
                  </span>
                  <div class="gray-bg network padding-20">
                  <div class="news-feed articles" id="feedDispBlk">
                  	<!-- Feed Block -->
                  	@if(count($getjobs)>0)
                  		@foreach($getjobs as $jobslist)
                  			<?php 
                  			if($jobslist->feed_type == 'share' && $jobslist->share_feed_type!='job') 
                  				continue;
                  			?>
                  		 <div class="clsFeedSingleBlk">
                  				<div class="col-md-12 feed-block padding-none margin-none">
                                    <div class="col-md-12 padding-none margin-none">
                                       <div class="col-md-12 feed-info">
                                          <div class="pull-right">{{ date('d/m/Y',strtotime($jobslist->created_at))}}</div>

                                          <span class="user-name">
		                                 {{-- *************** Shared feed ************ --}}
		                                    @if($jobslist->feed_type == 'share')
		                                    <strong>Shared by <a href="{{ URL::to('network/profile',$jobslist->user_id)}}">{{ucwords($jobslist->username)}}</a></strong> : {{$jobslist->feed_description}}<br>
		                                    @else
		                                       <strong>{{$jobslist->feed_title}}</strong>
		                                    @endif
                                 		</span>

                                          <p>
                                          <?php
					                     $sanitized = htmlspecialchars($jobslist->feed_description, ENT_QUOTES);
										 echo str_replace(array("\r\n", "\n"), array("<br />", "<br />"), $sanitized);
					                     
					                     ?>
                                          </p>
                                          
                                       	  <div class="feed-links" id="feedLinksBlk{{$jobslist->id}}" data-id="{{$jobslist->id}}" >
							                  {{--*/ $feedComCnt = $commoncomponent::feedComents($jobslist->id, 'count') /*--}}
							                  {{--*/ $fdLikes = $commoncomponent::feedLikes($jobslist->id, 'count') /*--}}
							                  {{--*/ $fdLikeCheck = $commoncomponent::feedLikes($jobslist->id, 'check') /*--}}
							                  <span class="clsLike" data-url="{{ URL::to('network/ajxfeedlike') }}">{{ $fdLikeCheck? 'Unlike':'Like' }}</span> 
							                  <span class="clsShare" data-url="{{ URL::to('network/ajxsharefeed') }}" data-toggle="modal" data-target="#popupfeedShare">Share</span> 
							                  <span class="clsComments">Comment</span>
							                  <span class="likes" data-likecnt="{{$fdLikes}}" >
							                     <i class="fa fa-thumbs-o-up"></i> <span>{{$fdLikes}}</span>
							                  </span>
							                  <span class="comments" data-comcnt="{{$feedComCnt}}">
							                     <i class="fa fa-comment-o"></i> <span>{{$feedComCnt}}</span>
							                  </span>
							               </div>
                                       </div>
                                       <div class="col-md-12 feed-info comments-block margin-none" id="feedComBlk{{$jobslist->id}}">
               								
                                       		<?php /* $jobcommnets = $commoncomponent::getJobCommnets($jobslist->id) */ ?>
                                       			
                                       		{{--*/ $jobcommnets = $commoncomponent::feedComents($jobslist->id) /*--}}
	                                       	{{--*/ $feedCommentsCnt = $commoncomponent::feedComents($jobslist->id, 'count') /*--}}

                                       		@if($feedCommentsCnt > 5)   
						                    	<a data-url="{{ URL::to('network/ajxloadcomm')}}" data-pageno="1" data-feedid="{{$jobslist->id}}" href="javascript:void(0)" id="loadMoreComments{{$jobslist->id}}" class="load_more_comments">View previous comments</a>
						                  	@endif  

               								<div id="ajxLoadFeedCom{{$jobslist->id}}">
	                                       
	                                       @if(count($jobcommnets)>0)
	                                       	<?php 
					                           $jobcommnets = json_decode(json_encode($jobcommnets),TRUE);
					                           $jobcommnets = array_reverse($jobcommnets, true);
					                        ?>
                                           	@foreach($jobcommnets as $comments)
                                           		{{--*/ $comments = (object)$comments /*--}}
	                                       	  <div class="col-md-12 padding-none form-control-fld" id="hidecomment{{$comments->id}}">
	                                       	  	 @if($comments->lkp_role_id == 2)
													{{--*/ $url = "uploads/seller/$comments->userid/" /*--}}
										        @else
										        	{{--*/ $url = "uploads/buyer/$comments->userid/" /*--}}
										        @endif
										        {{--*/ $getprofile = $url.$comments->user_pic /*--}}
										        {{--*/ $userpic =  $commoncomponent::str_replace_last( '.' , '_40_40.' , $getprofile ) /*--}}
				        
	                                             <div class="user-pic pull-left">
	                                             @if(file_exists($userpic))
				        							<img src="{{url($userpic)}}"/>
	                                             @else
	                                             <i class="fa fa-user"></i>
	                                             @endif
	                                             </div><span class="user-name pull-left"><strong>{{$comments->username}} : </strong>
	                                             <span id="commentid_{{$comments->id}}">{{$comments->comments}}</span></span>
	                                             <div class="pull-right">
	                                             
	                                             @if($jobslist->created_by == Auth::User()->id && $comments->created_by == Auth::User()->id )
	                                             <a href="javascript:void(0)" data-target="#editpostcomment" data-toggle="modal" onclick="editcomment({{$jobslist->id}},{{$comments->id}},{{$comments->created_by}})" ><i class="fa fa-edit" title="Edit"></i></a>&nbsp;&nbsp;
	                                             <a href="javascript:void(0)" data-target="#cancelpostcomment" data-toggle="modal" onclick="cancelcomment({{$comments->id}},{{$comments->created_by}})" ><i class="fa fa-trash" title="Delete"></i></a>&nbsp;&nbsp;
	                                             @elseif($jobslist->created_by == Auth::User()->id && $comments->created_by != Auth::User()->id )
	                                             <a href="javascript:void(0)" data-target="#cancelpostcomment" data-toggle="modal" onclick="cancelcomment({{$comments->id}},{{$comments->created_by}})" ><i class="fa fa-trash" title="Delete"></i></a>&nbsp;&nbsp;
	                                             @elseif($jobslist->created_by != Auth::User()->id && $comments->created_by == Auth::User()->id )
	                                             <a href="javascript:void(0)" data-target="#editpostcomment" data-toggle="modal" onclick="editcomment({{$jobslist->id}},{{$comments->id}},{{$comments->created_by}})" ><i class="fa fa-edit" title="Edit"></i></a>&nbsp;&nbsp;
	                                             <a href="javascript:void(0)" data-target="#cancelpostcomment" data-toggle="modal" onclick="cancelcomment({{$comments->id}},{{$comments->created_by}})" ><i class="fa fa-trash" title="Delete"></i></a>&nbsp;&nbsp;
	                                             @endif
	                                             {{ date('d/m/Y',strtotime($comments->created_at))}}</div>
	                                          </div>
	                                       	@endforeach
	                                       @endif
	                                       </div>
                                           <div class="col-md-12 padding-none" >
							                  <input type="text" placeholder="Add a comment" class="clsFeedComment form-control form-control1" name="txtFeedComment" id="txtFeedComment{{$jobslist->id}}"  data-feedid="{{$jobslist->id}}" data-curuser="{{Auth::User()->username}}" data-url="{{ URL::to('network/ajxpostcomment') }}">
							               </div>
                                       </div>
                                    </div>
                                 </div>
                                 </div>
                   		@endforeach
                   	@else
                   	No Jobs 
                   	@endif
                  	<div class="clearfix"></div>
                   </div>
                  </div>
               </div>
            </div>
         <div class="clearfix"></div>
          
             
	
</div>
@include('partials.footer')
@endsection

<div class="modal fade" id="popupfeedShare" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content"></div>
   </div>
</div>