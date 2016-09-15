@extends('app')
@section('content')
@include('partials.page_top_navigation')

@if(Session::has('relocationtransactionNumber') && Session::get('relocationtransactionNumber')!='')
{{--*/ $transactionId = Session::get('relocationtransactionNumber') /*--}}   
       
<script>
$(document).ready(function(){				
   $("#erroralertmodal .modal-body").html("Your request has been posted to the relevant sellers. Your transacton id is <?php echo $transactionId;?>. You would be getting the quotes from the sellers online.");				
   $("#erroralertmodal").modal({
       show: true
   }).one('click','.ok-btn',function (e){
           window.location="/buyerposts";	        	 
   });			

});
</script>
		
@endif

		<div class="main">

			<div class="container">
				<span class="pull-left"><h1 class="page-title">Post & Get Quote (Relocation Global Mobility)</h1><a href="#" class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
				
				@if ($url_search_search == 'byersearchresults')
					<span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">Back to Search</a></span>
			   	@endif
				

				<div class="clearfix"></div>


				<div class="col-md-12 inner-block-bg inner-block-bg1">

					
						<div class="col-md-12 form-control-fld margin-top">
							<div class="radio-block">
								<input type="radio" name="lead_type" id="relocationgm_spot" value="1" checked />
								<label for="relocationgm_spot"><span></span>Spot</label>
									
								<input type="radio" name="lead_type" id="relocationgm_term" value="2" />
								<label for="relocationgm_term"><span></span>Term</label>
							</div>
						</div>
				</div>

				<div class="relocation_global_spot_show">
    		     	 {!! Form::open(['url' => 'relocationbuyerpostcreation','id'=>'posts-form_buyer_relocation_gm', 'autocomplete'=>'off']) !!}
                  	 {!! Form::hidden('spot_term_value', '1', array('id' => 'spot_term_value')) !!}
						@include('relocationglobal.buyers.spot._form',array(
						'is_term' => false))

    		         {!! Form::close() !!}

				</div>


				<div class="relocation_global_term_show" style="display:none;" >
					@include('relocationglobal.buyers.term._form',array(
						'is_term' => true))
				</div>

			</div>
		</div>
@include('partials.footer')
@endsection		