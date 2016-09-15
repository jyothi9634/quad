@inject('commoncomponent', 'App\Components\CommonComponent')
<div id="news-feed" class="tab-pane fade in active">
    
   @include('networks/newsfeed/posting')
   
   <div class="clearfix"></div>
   <div class="news-feed">
      
      @include('networks/newsfeed/filter-form')
      
      <!-- Feed Block -->
      <div class="col-md-12 feed-block padding-right-none" id="feedDispBlk">
      @if(count($networkFeeds))
         <div id="article_posts">
         {{-- Check and display the feeds of all three types : Start --}}
         @foreach ($networkFeeds as $feed)

          {{-- Single Feed Block --}}
          <div class="clsFeedSingleBlk">
            <div class="col-md-1 padding-none">
               <div class="profile-pic">
               @if($feed->lkp_role_id == 2)
   				{{--*/ $url = "uploads/seller/$feed->userid/" /*--}}
   	        @else
   	        	{{--*/ $url = "uploads/buyer/$feed->userid/" /*--}}
   	        @endif
   	         {{--*/ $getuserpic = $url.$feed->user_pic /*--}}
               	{{--*/ $userpic =  $commoncomponent::str_replace_last( '.' , '_94_92.' , $getuserpic ) /*--}}
               	@if(file_exists($getuserpic))
               	<img src="{{url($userpic)}}"/>
               	@else
               	<i class="fa fa-user"></i>
               	@endif
               </div>
            </div>
            <div class="col-md-11 padding-right-none">
               <div class="col-md-12 feed-info">
                  <span class="user-name">
                  {{-- ********************* Shared feed ********************* --}}
                     @if($feed->feed_type == 'share')
                        <strong>Shared by <a href="{{ URL::to('network/profile',$feed->user_id)}}">{{ucwords($feed->username)}}</a></strong> : {{$feed->feed_description}}<br>
                     @elseif($feed->feed_type == 'recomend')
                        <strong>{!! $feed->feed_title !!}</strong><br>
                     @endif
                     
                     @if($feed->feed_type != 'recomend')
                        <strong><a href="{{ URL::to('network/profile',$feed->user_id) }}">{{ucwords($feed->username)}}</a></strong><br>
                     @endif   
                  </span>

                  @if($feed->feed_type == 'share')
                     <p>{{ucfirst($feed->share_feed_title)}} <br />
                     <?php
                     $sanitized = htmlspecialchars($feed->share_feed_description, ENT_QUOTES);
					      echo str_replace(array("\r\n", "\n"), array("<br />", "<br />"), $sanitized);
                     ?></p>
                  @else
                     <p><strong>{{ucfirst($feed->feed_title)}}</strong></p>
                     <p><?php
                     $sanitized = htmlspecialchars($feed->feed_description, ENT_QUOTES);
					      echo str_replace(array("\r\n", "\n"), array("<br />", "<br />"), $sanitized);
                     ?></p>
                  @endif
                  <div class="feed-links" id="feedLinksBlk{{$feed->id}}" data-id="{{ ($feed->feed_type == 'share')? $feed->share_feed_id:$feed->id }}">
                     {{--*/ $feedComCnt = $CComponent::feedComents($feed->id, 'count') /*--}}
                     {{--*/ $fdLikes = $CComponent::feedLikes($feed->id, 'count') /*--}}
                     {{--*/ $fdLikeCheck = $CComponent::feedLikes($feed->id, 'check') /*--}}
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
               
               {{-- Comments Section: Start --}}
               <div class="col-md-12 feed-info comments-block" id="feedComBlk{{$feed->id}}">
                  {{--*/ $feedComments = $CComponent::feedComents($feed->id) /*--}}
                  {{--*/ $feedCommentsCnt = $CComponent::feedComents($feed->id, 'count') /*--}}
                    
                  @if($feedCommentsCnt > 5)   
                     <a data-url="{{ URL::to('network/ajxloadcomm')}}" data-pageno="1" data-feedid="{{$feed->id}}" href="javascript:void(0)" id="loadMoreComments{{$feed->id}}" class="load_more_comments">View previous comments</a>
                  @endif   

                  <div id="ajxLoadFeedCom{{$feed->id}}">
                     @if(count($feedComments) > 0)
                        <?php 
                           $feedComments = json_decode(json_encode($feedComments),TRUE);
                           $feedComments = array_reverse($feedComments, true);
                        ?>
                        @foreach($feedComments as $comm)
                           {{--*/ $comm = (object)$comm /*--}}
                        <div class="col-md-12 padding-none form-control-fld">
                           
                           <div class="user-pic pull-left">
                              @if($comm->lkp_role_id == 2)
               						{{--*/ $url = "uploads/seller/$comm->userid/" /*--}}
               					@else
               						{{--*/ $url = "uploads/buyer/$comm->userid/" /*--}}
               					@endif
               					{{--*/ $getprofile = $url.$comm->user_pic /*--}}
               					{{--*/ $userpic =  $commoncomponent::str_replace_last( '.' , '_40_40.' , $getprofile ) /*--}}
         			        
                             <a href="{{ URL::to('network/profile',$comm->comment_user_id)}}">
                              @if(file_exists($getprofile))
         			        	      <img src="{{url($userpic)}}"/>
                              @else
                             	 <i class="fa fa-user"></i>
                              @endif
                             </a>
                           </div>

                           <span class="user-name pull-left">
                              <strong><a href="{{ URL::to('network/profile',$comm->comment_user_id)}}">{{ucwords($comm->username)}}</a> : </strong>
                              <span id="commentid_{{$comm->id}}">{{ $comm->comments }}</span>
                           </span>

                           <div class="comment-details pull-right">
                              @if($feed->created_by == Auth::User()->id || $comm->created_by == Auth::User()->id )
                              <a href="javascript:void(0)" data-target="#editpostcomment" data-toggle="modal" onclick="editcomment({{$feed->id}},{{$comm->id}},{{$comm->created_by}})" ><i class="fa fa-edit" title="Edit"></i></a>&nbsp;&nbsp;
                              <a href="javascript:void(0)" data-target="#cancelpostcomment" data-toggle="modal" onclick="cancelcomment({{$comm->id}},{{$comm->created_by}})" ><i class="fa fa-trash" title="Delete"></i></a>&nbsp;&nbsp;
                              @elseif($feed->created_by == Auth::User()->id && $comm->created_by != Auth::User()->id )  
                                 <a href="javascript:void(0)" data-target="#cancelpostcomment" data-toggle="modal" onclick="cancelcomment({{$comm->id}},{{$comm->created_by}})" ><i class="fa fa-trash" title="Delete"></i></a>&nbsp;&nbsp;
                              @endif
                              {{ date('d/m/Y',strtotime($comm->created_at))}}
                           </div>
                           
                           </div>
                        @endforeach
                     @endif   
                  </div>
                  <div class="col-md-12 padding-none" >
                     <input type="text" placeholder="Add a comment" class="form-control form-control1" name="txtFeedComment" id="txtFeedComment{{ $feed->id }}" class="clsFeedComment" data-feedid="{{ $feed->id }}" data-curuser="{{ Auth::User()->username}}" data-url="{{ URL::to('network/ajxpostcomment') }}" maxlength="200"><label id="lblErrComment{{ $feed->id }}" class="error"></label>
                  </div>
               </div>
               {{-- Comments Section: End --}}
            </div>
          </div>
            
         @endforeach   
         {{-- ******************** End ********************* --}}
         </div>   
      @else
         <div class="text-center" id="noResults"> No results found </div>
      @endif   
      </div>

      <div class="article_navigation" style="display:none;">
         <?php
         $pagescount = $totalcount/5+1;
         if(request('q')):
            $currenturl = Request::fullUrl();
            $currenturl = $currenturl.'&ajax=1';
         else:
            $currenturl = Request::url();
            $currenturl = $currenturl.'?ajax=1';
         endif;
         
         for($iterator=1;$iterator<=$pagescount;$iterator++){
         ?>
         <a total="{{$totalcount}}" class="next" href="{{$currenturl}}&page=<?php echo $iterator; ?>">Next</a>
         <?php } ?>
      </div>
      <div id="loader" style="display: none;" style="text-align: center; display: none;" class="ias-spinner">
         <img src="data:image/gif;base64,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" style="display:inline">
         <em>Loading the next set of articles...</em>
      </div>

      <div class="clearfix"></div>
   </div>
</div>

<div class="modal fade" id="popupfeedShare" role="dialog">
   <div class="modal-dialog">
      <div class="modal-content"></div>
   </div>
</div>
<script type="text/javascript"><!-- 
var isFilterEnable = "{{request('q')}}";
//-->
</script>