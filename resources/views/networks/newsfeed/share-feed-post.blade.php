<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal">&times;</button>
   <h4 class="modal-title">Share</h4>
</div>
<div class="modal-body">
{!! Form::open(['url' => 'network/ajxsharefeed', 'name="frmShrPost" id="frmShrPost"']) !!}
{!! Form::hidden('feedId', $feedInfo->id) !!}
{!! Form::hidden('feedtype', 'action') !!}
{!! Form::textarea('txtFeedShare', '', [ 
  'id'=>'txtFeedShare', 'class' => 'form-control form-control1 message-body', 
  'rows' => 3, 'cols' => '50'
]) !!}
	
   <p>{{ $feedInfo->feed_title }}</p>	
   <p>{{ str_limit( $feedInfo->feed_description, $limit = 250, $end = '...') }}</p>

{!! Form::close() !!}
</div>
<div class="modal-footer">
   <button type="button" class="btn add-btn flat-btn cancel-btn" data-dismiss="modal">Cancel</button>
   <button type="button" class="btn red-btn flat-btn" id="btnFeedShare">Share</button>
</div>