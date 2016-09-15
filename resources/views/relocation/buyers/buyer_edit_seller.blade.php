@extends('app') @section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
<!-- Page top navigation Starts Here-->

@include('partials.page_top_navigation')

{!! Form::open(['url'=>'/updaterelocationbuyer','id' => 'buyer_quote_updateform']) !!}
{!!	Form::hidden('ftl_buyer_quoteid',$buyer_post_details[0]->id, ['id' => 'ftl_buyer_quoteid', 'class'=>'form-control'])!!}


	<div class="main">
    	<div class="container">
		
		@if(Session::has('sumsg1')) 
        <div class="flash">
			<p class="text-success col-sm-12 text-center flash-txt alert-success">
			{{ Session::get('sumsg1') }}
			</p>
		</div>
		@endif	
		<span class="pull-left"><h1 class="page-title"><b>Relocation(Domestic) Post ID - {{$buyer_post_details[0]->transaction_id}}</b></h1></span>
		@include('partials.content_top_navigation_links')
		<div class="clearfix"></div>
                
                <div class="col-md-12 padding-none">
                    <div class="main-inner"> 
                        <!-- Right Section Starts Here -->
                        <div class="main-right">
                        
                            <div class="inner-block-bg inner-block-bg1">
                            	<div class="col-md-12 tab-modal-head">
                            	<h3>
                            	   {{$commonComponent->getCityName($buyer_post_details[0]->from_location_id)}}
                                    <i class="fa fa-map-marker"></i> 
                                   {{$commonComponent->getCityName($buyer_post_details[0]->to_location_id)}}
                                </h3>
                                
								<input type="hidden" name="from_location_id" id="from_location_id" value="{{$buyer_post_details[0]->from_location_id}}">
								</div>

								<div class="col-md-8 data-div">
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Dispatch Date</span>
                                        <span class="data-value">
                                           {{$commonComponent->convertDateDisplay($buyer_post_details[0]->dispatch_date)}}                                        
                                        </span>
                                    </div>
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Delivery Date<span>
                                        <span class="data-value">
                                         {{$commonComponent->convertDateDisplay($buyer_post_details[0]->delivery_date)}}  
                                        </span>
                                    </div>
                                     @if($buyer_post_details[0]->lkp_post_ratecard_type_id==1)                             
                                   <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Property Type<span>
                                        <span class="data-value">
                                       
                                         {{$commonComponent->getPropertyType($buyer_post_details[0]->lkp_property_type_id)}}  
                                        </span>
                                    </div> 
                                    @else
                                     <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Vehicle Type<span>
                                        <span class="data-value">
                                         {{$commonComponent->getVehicleCategoryById($buyer_post_details[0]->lkp_vehicle_category_id)}}  
                                        </span>
                                    </div> 
                                    @endif

                                </div>
                                <div class="col-md-8 data-div">
                                    
                                     @if($buyer_post_details[0]->lkp_post_ratecard_type_id==1)                             
                                   <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Load Type<span>
                                        <span class="data-value">
                                        @if($buyer_post_details[0]->lkp_load_category_id==1) 
                                         Full Load
                                        @else
                                        Part Load
                                        @endif   
                                        </span>
                                    </div> 
                                    @else
                                     <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Vehicle Category Type<span>
                                        <span class="data-value">
                                        @if($buyer_post_details[0]->lkp_vehicle_category_id==1)
                                         {{$commonComponent->getVehicleCategorytypeById($buyer_post_details[0]->lkp_vehicle_category_type_id)}}
                                        @else
                                        NA
                                        @endif   
                                        </span>
                                    </div> 
                                    
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Vehicle Model<span>
                                        <span class="data-value">
                                         {{$buyer_post_details[0]->vehicle_model}}  
                                        </span>
                                    </div> 
                                    @endif

                                </div>
                                <div class="col-md-4 order-detail-price-block">                              
                                    <div>
                                        <span class="data-head">Total Volume</span>
                                        @if($buyer_post_details[0]->lkp_post_ratecard_type_id==1) 
                                        <span class="data-value big-value">
                                        {{--*/ $volume_total = $commonComponent->getVolumeCft($buyer_post_details[0]->id)+$commonComponent->getCratingVolumeCft($buyer_post_details[0]->id) /*--}}
									    {{$volume_total}}
									    </span>
									    @else
									    <span class="data-value big-value">NA</span>
									    @endif
                                    </div>                                    
                                </div>
                            </div>

                            <div class="col-md-12 inner-block-bg inner-block-bg1">
                                <div class="col-md-4 padding-none">
									Selected Sellers :
									<div>
										<ul class="token-input-list">
										<?php foreach($buyer_post_edit_seller as $seller_list) { ?>
											<li class="token-input-token">
												<p>{!! $seller_list->username !!} {!! $seller_list->id !!}</p>
												<!-- <p>{!! Form::hidden('seller_id[]',$seller_list->id)!!}</p>  -->
											</li>
											<?php } ?>	
										</ul>
										<input type="text" id="demo-input-local" name="seller_list[]" class="form-control form-control1"/>
									</div>
								</div>								
                            </div>

                            <div class="col-md-4 col-md-offset-4">
								{!! Form::submit('Update', ['class' => 'btn theme-btn btn-block','name' =>'update','id' => 'quote_update',  'onclick'=>'return val1()']) !!}</div>
							</div>


                            </div>
                        
                        </div>
                    </div>
                </div>			
		</div>
	</div>
		

<script>
function val()
{
	var selerId = document.getElementById("demo-input-local").value;
	if (selerId == null || selerId == "") {
        alert("Please enter seller name");
        return false;
    }
}
$(document).ready(function() {	
			
			/*var from_location=$("#from_location").val();
			alert(from_location);*/
			var seller_id_list = new Array();
			
				var from_location_value =$("#from_location_id").val();
				seller_id_list.unshift(from_location_value);

				//alert(seller_id_list);
			
			//getting buyer quote id check dupliactes form selected sseller table.
			 var buyer_quote_id = $('#ftl_buyer_quoteid').val();
			
			$('.token-input-delete-token').click(function(){
					$(this).parent().remove();
				});						
			$.ajax({
	            url: '/getEditSellerslist',
	            type: "post",
	            data: {'seller_list':seller_id_list,'_token': $('input[name=_token]').val(),'buyer_quote_id':buyer_quote_id},
	            success: function(data){
	            //alert(data);
	            if(data!="")
	            	{
	            		$("#demo-input-local").tokenInput(data);
	            	}
	            else
	            	{
	            		alert("No Sellers Available");
	            		$('#post_private').prop('checked', false);
	            		$("#hideseller").css("display","none");	
	            		return false;
	            		
	            	}	            
	            },
	            error : function(request, status, error) {
	            $('#post_private').val(null);
	            alert(error);
	            },
	        });	
});
			
		
</script>
{!! Form::close() !!} @stop
@endsection


	