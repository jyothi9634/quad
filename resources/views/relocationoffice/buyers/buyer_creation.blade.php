@extends('app')
@section('content')
@include('partials.page_top_navigation')

  {{--*/ $from_date=''; /*--}}
  {{--*/ $to_date=''; /*--}}
  {{--*/ $from_loaction=''; /*--}}
  {{--*/ $from_loaction_id=''; /*--}}  
  {{--*/ $to_loaction=''; /*--}}
  {{--*/ $to_loaction_id=''; /*--}}  
  {{--*/ $distance=''; /*--}}  
  {{--*/ $search_particulars = array(); /*--}}
  {{--*/ $p_count = 0; /*--}}
 @if(Session::has('searchMod'))
  {{--*/ $searchrequest=Session::get('searchMod'); /*--}}
  	@if($searchrequest != "")
		  {{--*/ $from_loaction=$searchrequest['from_location_buyer']; /*--}}
		  {{--*/ $from_loaction_id=$searchrequest['from_city_id_buyer']; /*--}}
		  {{--*/ $from_date=$searchrequest['dispatch_date_buyer']; /*--}}
		  {{--*/ $to_date=$searchrequest['delivery_date_buyer']; /*--}}
		  {{--*/ $distance= $searchrequest['distance_buyer']; /*--}}
		  {{--*/ $search_particulars = $searchrequest['particulars_buyer']; /*--}}
		  {{--*/ $p_count = count($search_particulars); /*--}}
	@endif
@endif


@if(Session::has('relocationtransactionNumber') && Session::get('relocationtransactionNumber')!='')

	{{--*/ $transactionId = Session::get('relocationtransactionNumber') /*--}}        
	{{--*/ Session::get('postType') /*--}}
        
			<script>
			$(document).ready(function(){
                            var postType = {{ Session::get('postType') }}				
                            if (postType==1) {
                                $("#erroralertmodal .modal-body").html("Your request has been posted to the relevant sellers. Your transacton id is <?php echo $transactionId;?>. You would be getting the quotes from the sellers online.");
                                $("#erroralertmodal").modal({
                                    show: true
                                }).one('click','.ok-btn',function (e){
                                        window.location="/buyerposts";
                                });
                            } else {
                                $("#erroralertmodal .modal-body").html("Your request has been posted to the relevant sellers. Your transacton id is <?php echo $transactionId;?>. You would be getting the quotes from the sellers online.");						                                   			
                                $("#erroralertmodal").modal({
                                show: true
                                }).one('click','.ok-btn',function (e){
                                window.location="/buyerposts";	        	 
                                });	
                            }
                         });
</script>
				
		
@endif

		<div class="main">

			<div class="container">
			 {!! Form::open(['url' => 'relocationbuyerpostcreation','id'=>'posts-form_buyer_relocation_officemove', 'autocomplete'=>'off']) !!}
				<span class="pull-left"><h1 class="page-title">Post & Get Quote (Relocation Office)</h1><a href="#" class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
				@if ($url_search_search == 'byersearchresults')
				<span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">Back to Search</a></span>
		   		@endif

				<div class="clearfix"></div>


				<div class="col-md-12 inner-block-bg inner-block-bg1">
					<div class="clearfix margin-top"></div>
						<div class="col-md-3 form-control-fld">	
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-map-marker"></i></span>
									 {!! Form::text('from_location',$from_loaction , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'City *']) !!}
		                              {!! Form::hidden('from_location_id', $from_loaction_id, array('id' => 'from_location_id')) !!} 
								</div>
							</div>
							
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('valid_from', $from_date, ['id' => 'datepicker','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Pickup Date*']) !!}
								
								</div>
							</div>
							
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('valid_to', $to_date, ['id' => 'datepicker_to_location','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date*']) !!}
									{!! Form::hidden('to_location_id', $to_loaction_id, array('id' => 'to_location_id')) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
									<div class="input-prepend">
										<span class="add-on"><i class="fa fa-road"></i></span>
										
 										{!! Form::text('distance',$distance , ['id' => 'distance','class' => 'form-control clsROMDistanceKM', 'placeholder' => 'Distance *']) !!}

										<span class="add-on unit1 manage">
											KM
										</span>
									</div>

							</div>
							
						
							<div class="clearfix"></div>
							
							<div class="advanced-search-details-officemove">

								<div class="clearfix"></div>						
								<!-- Table Starts Here -->

							<div class="table-div table-style1 inventory-block-officemove margin-none">
								<div class="table-div table-style1 inventory-table padding-none">
									
									<!-- Table Head Starts Here -->

									<div class="table-heading inner-block-bg">
										<div class="col-md-6 padding-left-none">&nbsp;</div>
										<div class="col-md-6 padding-left-none text-center">No of Items</div>
																			
									</div>

									<!-- Table Head Ends Here -->
										<div class="table-data">
								
										<!-- Table Row Starts Here -->
											@foreach($particulars as $particular)
													<div class="table-row inner-block-bg">
														<div class="col-md-6 padding-left-none">{{$particular->office_particular_type}}</div>
																								
														 <div class="col-md-6 padding-left-none">
														 @if($p_count > 0 && array_key_exists($particular->id, $search_particulars)) 
															 {{--*/ $p_val = $search_particulars[$particular->id]; /*--}}
														 @else
															 {{--*/ $p_val = ''; /*--}}	
														 @endif	

	{!! Form::text('roomitems['.$particular->id.']',$p_val, ['id' => 'roomitems_'.$particular->id,'class' => 'form-control form-control1 roomitems clsROMNoOfItems','onblur' => 'javascript:valuecheck(this.value,this.id)'])  !!}
															<!-- <input type="text" name="roomitems[{{$particular->id}}]" id="roomitems_{{$particular->id}}" class="form-control form-control1 roomitems" onblur="javascript:valuecheck(this.value,this.id)"> -->
														</div>
													</div>
											@endforeach
										<!-- Table Row Ends Here -->
										</div>								
								</div>	

								</div>	
							</div>
							<div class="col-md-12 form-control-fld text-right margin-none">
								<span class="red spl-link advanced-search-link-officemove"><span class="more-search-officemove">+</span><span class="less-search-officemove">-</span> Inventory Details</span>
							</div>
				</div>

					
				<div class="col-md-12 inner-block-bg inner-block-bg1">

					
					<div class="col-md-12 form-control-fld margin-none">
						<div class="radio-block">
							<div class="radio_inline">
								<input type="radio" name="ptlQuoteaccessId" value="1" id="post-public" checked="checked" class="create-posttype-service crete-relocation" /> 
								<label for="post-public"><span></span>Post Public</label></div>
								<div class="radio_inline"><input type="radio" name="ptlQuoteaccessId" value="2" id="post-private" class="create-posttype-service crete-relocation"/> 
								<label for="post-private"><span></span>Post Private</label></div>
						</div>
					</div>

					<div class="col-md-3 form-control-fld" id="hideseller" style="display:none;">
								<input type="text" id="demo-input-local" class="form-control form-control1" name="seller_list" />
					</div>
		
					<div class="clearfix"></div>
					<div class="check-box form-control-fld">
							{!! Form::checkbox('agree', 1,false,array('id'=>'agree')) !!}<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
					</div>
				</div>

				<div class="clearfix"></div>

				<div class="container">
					<div class="col-md-4 col-md-offset-4">
						<button class="btn theme-btn btn-block">Get Quote</button>
					</div>
				</div>

  		{!! Form::close() !!}
			</div>
		</div>


		
<script type="text/javascript">
function valuecheck(str,itemid){
    if(str!="")  
    {
		if(isNaN(parseInt(str)))
		{
			$("#erroralertmodal .modal-body").html("Please enter numbers only");
			$("#erroralertmodal").modal({
			  	show: true
			});		
			$("#"+itemid).val("");
			$("#"+itemid).focus();
			return false;				
		}else if(parseInt(str) == 0){
			$("#erroralertmodal .modal-body").html("No of items should be greater than 0");
			$("#erroralertmodal").modal({
		      	show: true
		    });	
		    $("#"+itemid).focus();
			return false;			
		}
    }
}
</script>	
@include('partials.footer')
@endsection
