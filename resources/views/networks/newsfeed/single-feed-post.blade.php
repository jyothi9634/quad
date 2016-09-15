<div class="clsFeedSingleBlk">
   <div class="col-md-1 padding-none">
      <div class="profile-pic"><i class="fa fa-user"></i></div>
   </div>
   <div class="col-md-11 padding-right-none">
      <div class="col-md-12 feed-info">
         <span class="user-name">
            <strong><a href="{{ URL::to('network/profile',$feedInfo->user_id)}}">{{$feedInfo->username}}</a></strong><br>
            Course Director at DCC
         </span>
         <p>
         <?php
		$sanitized = htmlspecialchars($feedInfo->feed_description, ENT_QUOTES);
		echo str_replace(array("\r\n", "\n"), array("<br />", "<br />"), $sanitized);
		 ?>
         </p>
         <div class="feed-links" id="feedLinksBlk{{$feedInfo->id}}" data-id="{{$feedInfo->id}}">
            <span class="clsLike" data-url="{{ URL::to('network/ajxfeedlike') }}">Like</span> 
            <span class="clsShare" data-url="{{ URL::to('network/ajxsharefeed') }}" data-toggle="modal" data-target="#popupfeedShare">Share</span> 
            <span>Comment</span>
            <span class="likes" data-likecnt="0">
               <i class="fa fa-thumbs-o-up"></i> <span>0</span>
            </span>
            <span class="comments" data-comcnt="0">
               <i class="fa fa-comment-o"></i> <span>0</span>
            </span>
         </div>
      </div>
      {{-- Comments Section: Start --}}
      <div class="col-md-12 feed-info comments-block" id="feedComBlk{{$feedInfo->id}}">
         <div id="ajxLoadFeedCom{{$feedInfo->id}}"></div>
         <div class="col-md-12 padding-none" >
            <input type="text" placeholder="Add a comment" class="form-control form-control1" name="txtFeedComment" id="txtFeedComment{{$feedInfo->id}}" class="clsFeedComment" data-feedid="{{$feedInfo->id}}" data-curuser="{{$feedInfo->username}}" data-url="{{ URL::to('network/ajxpostcomment') }}">
         </div>
      </div>
      {{-- Comments Section: End --}}
   </div>
</div>