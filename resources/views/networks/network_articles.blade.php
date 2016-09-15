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
                  <a href="/network">Network</a><i class="fa  fa-angle-right"></i> <a href="/network/profile/{{$id}}">Profile<i class="fa  fa-angle-right"></i> <a href="javascript:void(0)">Articles</a></div>
                  <span class="pull-left">
                     <h1 class="page-title">Articles</h1>
                  </span>
                  <div class="gray-bg network padding-20">
                     <div class="news-feed articles" id="feedDispBlk">

                        <!-- Feed Block -->
                        @foreach($getarticles as $articleslist)
                           <?php 
                           if( $articleslist->feed_type == 'share' && $articleslist->share_feed_type !='article')
                              continue;
                           ?>

                  		 <div class="clsFeedSingleBlk">
                        <div class="col-md-12 feed-block padding-none margin-none">
                           <div class="col-md-12 padding-none margin-none">
                              <div class="col-md-12 feed-info">
                                 <div class="pull-right">{{date("d/m/Y",strtotime( $articleslist->created_at))}}</div>
                                 <span class="user-name">
                                 {{-- ********************* Shared feed ********************* --}}
                                    @if($articleslist->feed_type == 'share')
                                    <strong>Shared by <a href="{{ URL::to('network/profile',$articleslist->user_id)}}">{{ucwords($articleslist->username)}}</a></strong> : {{$articleslist->feed_description}}<br>
                                    @else
                                       <strong>{{$articleslist->feed_title}}</strong>
                                    @endif
                                 </span>

                                 @if($articleslist->feed_type == 'share')
                                    <p><strong>{{$articleslist->share_feed_title}}</strong><br />
                                    <?php
					                $sanitized = htmlspecialchars($articleslist->share_feed_description, ENT_QUOTES);
								    echo str_replace(array("\r\n", "\n"), array("<br />", "<br />"), $sanitized);
					                 ?>
                                    </p>
                                 @else
                                    <p>
                                    <?php
					                $sanitized = htmlspecialchars($articleslist->feed_description, ENT_QUOTES);
								    echo str_replace(array("\r\n", "\n"), array("<br />", "<br />"), $sanitized);
					                 ?>
                                    </p>
                                 @endif

                                 <div class="feed-links" id="feedLinksBlk{{$articleslist->id}}" data-id="{{$articleslist->id}}" >
					                  {{--*/ $feedComCnt = $commoncomponent::feedComents($articleslist->id, 'count') /*--}}
					                  {{--*/ $fdLikes = $commoncomponent::feedLikes($articleslist->id, 'count') /*--}}
					                  {{--*/ $fdLikeCheck = $commoncomponent::feedLikes($articleslist->id, 'check') /*--}}
					                  <span class="clsLike" data-url="{{ URL::to('network/ajxfeedlike') }}">{{ $fdLikeCheck? 'Unlike':'Like' }}</span> 
					                  <span class="clsShare" data-url="{{ URL::to('network/ajxsharefeed') }}" data-toggle="modal" data-target="#popupfeedShare">Share</span> 
					                  <span class="clsComments">Comment</span>
					                  <span class="likes" data-likecnt="{{$fdLikes}}">
					                     <i class="fa fa-thumbs-o-up"></i> <span>{{$fdLikes}}</span>
					                  </span>
					                  <span class="comments" data-comcnt="{{$feedComCnt}}">
					                     <i class="fa fa-comment-o"></i> <span>{{$feedComCnt}}</span>
					                  </span>
							      </div>
                                
                                
                              </div>
                              <div class="col-md-12 feed-info comments-block margin-none" id="feedComBlk{{$articleslist->id}}">
               								<div id="ajxLoadFeedCom{{$articleslist->id}}">
                                 {{--*/ $jobcommnets = $commoncomponent::getJobCommnets($articleslist->id) /*--}}
                                 	@if(count($jobcommnets)>0)
                                       	@foreach($jobcommnets as $comments)
		                                 <div class="col-md-12 padding-none form-control-fld" id="hidecomment{{$comments->id}}">
                                             <div class="user-pic pull-left"><i class="fa fa-user"></i></div>
                                             <span class="user-name pull-left"><strong>{{$comments->username}} </strong>
                                             <span id="commentid_{{$comments->id}}">{{$comments->comments}}</span>
                                             </span>
                                             <div class="pull-right">
                                             
                                             	@if($articleslist->created_by == Auth::User()->id && $comments->created_by == Auth::User()->id )
	                                             <a href="javascript:void(0)" data-target="#editpostcomment" data-toggle="modal" onclick="editcomment({{$articleslist->id}},{{$comments->id}},{{$comments->created_by}})" ><i class="fa fa-edit" title="Edit"></i></a>&nbsp;&nbsp;
	                                             <a href="javascript:void(0)" data-target="#cancelpostcomment" data-toggle="modal" onclick="cancelcomment({{$comments->id}},{{$comments->created_by}})" ><i class="fa fa-trash" title="Delete"></i></a>&nbsp;&nbsp;
	                                             @elseif($articleslist->created_by == Auth::User()->id && $comments->created_by != Auth::User()->id )
	                                             <a href="javascript:void(0)" data-target="#cancelpostcomment" data-toggle="modal" onclick="cancelcomment({{$comments->id}},{{$comments->created_by}})" ><i class="fa fa-trash" title="Delete"></i></a>&nbsp;&nbsp;
	                                             @elseif($articleslist->created_by != Auth::User()->id && $comments->created_by == Auth::User()->id )
	                                             <a href="javascript:void(0)" data-target="#editpostcomment" data-toggle="modal" onclick="editcomment({{$articleslist->id}},{{$comments->id}},{{$comments->created_by}})" ><i class="fa fa-edit" title="Edit"></i></a>&nbsp;&nbsp;
	                                             <a href="javascript:void(0)" data-target="#cancelpostcomment" data-toggle="modal" onclick="cancelcomment({{$comments->id}},{{$comments->created_by}})" ><i class="fa fa-trash" title="Delete"></i></a>&nbsp;&nbsp;
	                                             @endif
	                                             {{ date('d/m/Y',strtotime($comments->created_at))}}</div>
                                          </div>
		                                @endforeach
                                     @endif
                                     </div>
                                 	<div class="col-md-12 padding-none" >
							                 <input type="text" placeholder="Add a comment" class="clsFeedComment form-control form-control1" name="txtFeedComment" id="txtFeedComment{{$articleslist->id}}"  data-feedid="{{$articleslist->id}}" data-curuser="{{Auth::User()->username}}" data-url="{{ URL::to('network/ajxpostcomment') }}">
							               </div>
                              </div>
                           </div>
                        </div>
                        </div>
                        @endforeach
                       
                        <!-- Feed Block -->
                        
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
