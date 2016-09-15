@extends('app') @section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')

{!! Form::open(['url'=>'/updateBuyer','id' => 'buyer_quote_updateform']) !!}
{!!	Form::hidden('buyer_id',$buyer_post_id->id, ['id' => 'ftl_buyer_quoteid', 'class'=>'form-control'])!!}


	<div class="main">
    	<div class="container">
		
		@if(Session::has('sumsg1')) 
        <div class="flash">
			<p class="text-success col-sm-12 text-center flash-txt alert-success">
			{{ Session::get('sumsg1') }}
			</p>
		</div>
		@endif	
		<span class="pull-left"><h1 class="page-title"><b>Full Truck Load Post ID - {!! $buyer_post_id->transaction_id !!}</b></h1></span>
		@include('partials.content_top_navigation_links')
		<div class="clearfix"></div>
                
                <div class="col-md-12 padding-none">
                    <div class="main-inner"> 
                        <!-- Right Section Starts Here -->
                        <div class="main-right">
                        <?php foreach($buyer_post_edit as $buyer_item_data) { ?>
                            <div class="inner-block-bg inner-block-bg1">
                            	<div class="col-md-12 tab-modal-head">
                            	<h3>
                                    <i class="fa fa-map-marker"></i> 
                                    @if($buyer_item_data->from_locationcity)
                                    {!! $buyer_item_data->from_locationcity !!}
                                    @endif
                                </h3>
                                <input type="hidden" name="from_location[]"	value="{!! $buyer_item_data->from_city_id !!}" class="from_location">
								{!!	Form::hidden('buyer_items_id',$buyer_item_data->id)!!}
								<input type ="hidden" name="hidden_price" value="<?php echo $buyer_item_data->price; ?>">
								<input type ="hidden" name="hidden_price_typeid" value="<?php echo $buyer_item_data->lkp_quote_price_type_id; ?>">

								</div>

								<div class="col-md-8 data-div">
                                    @if(isset($buyer_item_data->from_date))
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">
                                                Dispatch Date
                                        </span>
                                        <span class="data-value">
                                            @if(isset($buyer_item_data->from_date))
                                            {!! $commonComponent->checkAndGetDate($buyer_item_data->from_date) !!}
                                            @else 
                                            &nbsp;
                                            @endif                                            
                                        </span>
                                    </div>
                                    @endif                                            
                                    @if(isset($buyer_item_data->to_date))
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Delivery Date<span>
                                        <span class="data-value">
                                            @if(isset($buyer_item_data->to_date))
                                            {!! $commonComponent->checkAndGetDate($buyer_item_data->to_date) !!}
                                            @else &nbsp;
                                                @endif
                                        </span>
                                    </div>
                                    @endif
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Vehicle Type</span>
                                        <span class="data-value">
                                            @if($buyer_item_data->vehicle_type)
                                            {!! $buyer_item_data->vehicle_type !!}
                                            @else &nbsp;
                                                @endif
                                        </span>
                                    </div>
                                    
                                     <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Driver</span>
                                        <span class="data-value">
                                            @if($buyer_item_data->driver_availability == 0)
                                            
                                            Without Driver
                                            @else
                                            
                                            With Driver
                                            
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Fuel</span>
                                        <span class="data-value">
                                            @if($buyer_item_data->fuel_included == 1)
                                            Include
                                            @else
                                            Not Include
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Vehicle Make & Model & Year</span>
                                        <span class="data-value">
                                            @if($buyer_item_data->vehicle_make_model_year)
                                             {!! $buyer_item_data->vehicle_make_model_year !!}
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="col-md-4 padding-left-none data-fld">
                                        <span class="data-head">Lease Term</span>
                                        <span class="data-value">
                                            @if($buyer_item_data->lkp_trucklease_lease_term_id == 1)
                                            Daily
                                            @elseif($buyer_item_data->lkp_trucklease_lease_term_id == 2)
                                            Weekly
                                            @elseif($buyer_item_data->lkp_trucklease_lease_term_id == 3)
                                            Monthly
                                            @else
                                            Yearly
                                            @endif
                                        </span>
                                    </div>
                                    
                                    <div class="clearfix"></div>                                   
                                   

                                </div>
                                <div class="col-md-4 order-detail-price-block">                              
                                    <div>
                                        <span class="data-head">Total Price</span>
                                        <span class="data-value big-value">Rs. {!! $buyer_item_data->price !!}</span>
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
											</li>
											<?php } ?>
										</ul>
										<input type="text" id="demo-input-local" name="seller_list[]" class="form-control form-control1"/>
									</div>
								</div>								
                            </div>

                            <div class="col-md-4 col-md-offset-4">
								{!! Form::submit('Update', ['class' => 'btn theme-btn btn-block','name' =>'update','id' => 'quote_update',  'onclick'=>'return val()']) !!}</div>
							</div>


                            </div>
                        <?php } ?>
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
        $("#erroralertmodal .modal-body").html("Please enter seller name");
        $("#erroralertmodal").modal({
            show: true
        });
        return false;
    }
}
$(document).ready(function() {	
			
			/*var from_location=$("#from_location").val();
			alert(from_location);*/
			var seller_id_list = new Array();
			$.each( $( ".from_location" ), function() {
				var from_location_value =$(this).val();
				seller_id_list.unshift(from_location_value);
			    
			});	
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
	            		//alert("No Sellers Available");
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
{!! Form::close() !!} 
@include('partials.footer')
@stop
@endsection


	