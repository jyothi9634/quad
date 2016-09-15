@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
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
            <span class="pull-left"><h1 class="page-title">Post & Get Quote (Relocation International)</h1><a class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
            @if ($url_search_search == 'byersearchresults')
			<span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">Back to Search</a></span>
		   	@endif

                <div class="clearfix"></div>
		 
			<div class="col-md-12 inner-block-bg inner-block-bg1 margin-bottom-none border-bottom-none">
				<div class="col-md-12 form-control-fld margin-none ">				
					<div class="col-md-12 padding-none radio-block radio-devider margin-top">
						<div class="radio_inline"><input type="radio" name="lead_type" id="relocationint_spot"  value="1" checked /> <label for="relocationint_spot"><span></span>Spot</label></div>
						<div class="radio_inline"><input type="radio" name="lead_type" id="relocationint_term" value="2" /> <label for="relocationint_term"><span></span>Term</label></div>
					</div>
				</div>
        <div class="col-md-12 form-control-fld margin-none ">				
					<div class="col-md-12 padding-none radio-block margin-bttom">
                                            <div class="radio_inline"><input type="radio" name="post_type" id="relocation_air"  value="1" checked class="check_relint_type"/> <label for="relocation_air"><span></span>Air</label></div>
						<div class="radio_inline"><input type="radio" name="post_type" id="relocation_ocean" value="2" class="check_relint_type" /> <label for="relocation_ocean"><span></span>Ocean</label></div>
					</div>
				</div>
			</div> 
            
		   	<div class="relocation_spot_show">
				
    			<div class="relocation_air_show">
    		     	 {!! Form::open(['url' => 'relocationbuyerpostcreation','id'=>'posts-form_buyer_relocationair', 'autocomplete'=>'off']) !!}
                             {!! Form::hidden('post_type', '1', array('id' => 'post_type')) !!}
                                 @include('relocationint.airint.buyers.buyer_creation')		           					
    		         {!! Form::close() !!}
                            </div>
                            <div class="relocation_ocean_show" style="display:none">
                            {!! Form::open(['url' => 'relocationbuyerpostcreation','id'=>'relocationint_ocean_getquote', 'autocomplete'=>'off']) !!}
                            {!! Form::hidden('post_type', '2', array('id' => 'post_type')) !!}
                                @include('relocationint.ocean.buyers.buyer_creation')		           					
                            {!! Form::close() !!}
                             </div>
			</div>

		

<!-- ---------------------------------Start Relcoation Term ----------------- -->


          <div class="relocation_term_show" style="display:none">
      		  <div class="relocation_airterm_show">
            	@include('relocationint.airint.buyers.buyer_term_creation')
             </div>
            
          </div>
					
		</div>	
   </div>
@include('partials.footer')
@endsection


