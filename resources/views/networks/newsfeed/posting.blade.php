<div class="clearfix"></div>
<ul class="nav-tabs inner-tabs">
   <li class="active"><a data-toggle="tab" href="#feed-menu1">Post Feed</a></li>
   <li><a data-toggle="tab" href="#feed-menu2">Post Job</a></li>
   <li><a data-toggle="tab" href="#feed-menu3">Publish Article</a></li>
</ul>
<div class="clearfix"></div>

<div class="tab-content feed-tab-content">
   
   <div id="feed-menu1" class="tab-pane fade in active">
      {!! Form::open([ 'url' => 'network/ajxpostfeed', 'id'=>'frmfeed', 'class'=>'clsPosting' ]) !!}
      <input type="text" style="display: none;">
      <div class="inner-block-bg padding-10">
         <div class="col-md-12 form-control-fld padding-none">
            <textarea name="txtDesc" id="txtNewsDesc" rows="4" class="form-control form-control1"></textarea>
            <span id="postFeedErr" style="color:red"></span>
         </div>
         <button type="button" data-url="{{ URL::to('network/ajxpostfeed') }}" data-type="feed" data-prefix="News" id="btnPostFeed" class="btn red-btn pull-right">Submit</button>
      </div>
      {!! Form::close() !!}
   </div>
   
   <div id="feed-menu2" class="tab-pane fade">
      {!! Form::open([ 'url' => 'network/ajxpostfeed', 'id'=>'frmjob', 'class'=>'clsPosting' ]) !!}
      <input type="text" style="display: none;">
      <div class="inner-block-bg padding-10">
         <div class="col-md-12 form-control-fld padding-none">
            <div class="margin-bottom">
               <input id="txtJobTitle" name="txtTitle" placeholder="Title *" class="form-control form-control1" autofocus />
               <span id="postJobTitleErr" style="color:red"></span>
            </div>
            <div>
               <textarea id="txtJobDesc" placeholder="Description *" name="txtDesc" rows="4" class="form-control form-control1"></textarea>
               <span id="postJobDescErr" style="color:red"></span>
            </div>
         </div>
         <button type="button" data-url="{{ URL::to('network/ajxpostfeed') }}" id="btnPostJob" data-type="job" data-prefix="Job" class="btn red-btn pull-right">Submit</button>
      </div>
      {!! Form::close() !!}
   </div>
   
   <div id="feed-menu3" class="tab-pane fade">
      {!! Form::open([ 'url' => 'network/ajxpostfeed', 'id'=>'frmarticle', 'class'=>'clsPosting' ]) !!}
      <input type="text" style="display: none;">
      <div class="inner-block-bg padding-10">
         <div class="col-md-12 form-control-fld padding-none">
            <div class="margin-bottom">
               <input id="txtArtTitle" name="txtTitle" placeholder="Title *" class="form-control form-control1" autofocus />
               <span id="postArtTitleErr" style="color:red"></span>
            </div>
            <div>
               <textarea id="txtArtDesc" placeholder="Description *" name="txtDesc" rows="4" class="form-control form-control1"></textarea>
               <span id="postArtDescErr" style="color:red"></span>
            </div>
         </div>
         <button type="button" data-url="{{ URL::to('network/ajxpostfeed') }}" id="btnPublishArticle" data-type="article" data-prefix="Art" class="btn red-btn pull-right">Submit</button>
      </div>
      {!! Form::close() !!}
   </div>
</div>