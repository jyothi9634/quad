@extends('app') @section('content')
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('relOceanSellerCComponent', 'App\Components\RelocationInt\OceanInt\RelocationOceanSellerComponent')
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
		<span class="pull-left"><h1 class="page-title"><b>Relocation(International) Post ID - {{$buyer_post_details[0]->transaction_id}}</b></h1></span>
		@include('partials.content_top_navigation_links')
		<div class="clearfix"></div>
                
                <div class="col-md-12 padding-none">
                    <div class="main-inner"> 
                        <!-- Right Section Starts Here -->
                        <div class="main-right">
                        
                            <div class="inner-block-bg inner-block-bg1">
                            	<div class="col-md-12 tab-modal-head">
                                    <h3>
                                        <i class="fa fa-map-marker"></i> 
                                       {{$commonComponent->getCityName($buyer_post_details[0]->from_location_id)}}    
                                       to
                                       {{$commonComponent->getCityName($buyer_post_details[0]->to_location_id)}}
                                    </h3>
                                    <input type="hidden" name="from_location_id" id="from_location_id" value="{{$buyer_post_details[0]->from_location_id}}">
				</div>

				<div class="col-md-8">
                                    <div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Dispatch Date</span>
                                        <span class="data-value">
                                           {{$commonComponent->convertDateDisplay($buyer_post_details[0]->dispatch_date)}}                                        
                                        </span>
                                    </div>
                                    <div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Delivery Date<span>
                                        <span class="data-value">
                                         @if($buyer_post_details[0]->delivery_date!='' && $buyer_post_details[0]->delivery_date!= '0000-00-00')
                                         {{$commonComponent->convertDateDisplay($buyer_post_details[0]->delivery_date)}}  
                                         @else
                                         NA
                                         @endif
                                        </span>
                                    </div>                                    
                                    <div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Volume (CBM)<span>
                                        <span class="data-value">                                            
                                         {{--*/ $totalCFT=$relOceanSellerCComponent->getVolumeCft($buyer_post_details[0]->id) /*--}} 
                                         {{--*/ $volume=round($totalCFT/35.5, 2) /*--}}                                  
                                         {{$volume}}
                                        </span>
                                    </div>                                    
                                    
                                    <div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Origin Storage <span>
                                        <span class="data-value">    
                                        @if($buyer_post_details[0]->origin_storage==1)
                                        Yes
                                        @else
                                        No
                                        @endif
                                        </span>
                                    </div>
                                    <div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Origin Handyman Services <span>
                                        <span class="data-value">                                           
                                        @if($buyer_post_details[0]->origin_handyman_services==1)
                                        Yes
                                        @else
                                        No
                                        @endif
                                        </span>
                                    </div>
                                    <div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Origin Insurance <span>
                                        <span class="data-value">                                           
                                        @if($buyer_post_details[0]->insurance==1)
                                        Yes
                                        @else
                                        No
                                        @endif
                                        </span>
                                    </div>
                                    <div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Destination Storage <span>
                                        <span class="data-value">                                           
                                        @if($buyer_post_details[0]->destination_storage==1)
                                        Yes
                                        @else
                                        No
                                        @endif
                                        </span>
                                    </div>
                                    <div class="col-md-3 padding-left-none data-fld">
                                        <span class="data-head">Destination Handyman Services <span>
                                        <span class="data-value">                                           
                                        @if($buyer_post_details[0]->destination_handyman_services==1)
                                        Yes
                                        @else
                                        No
                                        @endif
                                        </span>
                                    </div>

                                </div>
                                
                                
                            </div>
                            {{--*/ $particularsDataCount=$commonComponent->getBuyerInventoryParticularsDataInfo($buyer_post_details[0]->id) /*--}}
                            @if($particularsDataCount>0)
                            <div class="col-md-12 form-control-fld text-right margin-none">
                                <div class="info-links">
                                    <a class=" red spl-link transaction-details-expand"><span class="show-icon">+</span>
                                        <span class="hide-icon">-</span> Inventory Details
                                    </a>
                                </div>
                            </div>
                            @endif
                            
                            <div class="clearfix"></div>
                            <div class="show-trans-details-div-expand trans-details-expand">
                                
                                 <div class="expand-block">                                                           
                                     @include('relocationint.ocean.buyers.buyerpost_inventory_details',array('buyerpost_id'=>$buyer_post_details[0]->id))
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
		
	

        @include('partials.footer')	

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
			
        var seller_id_list = new Array();
        var from_location_value =$("#from_location_id").val();
        seller_id_list.unshift(from_location_value);

        //getting buyer quote id check dupliactes form selected sseller table.
         var buyer_quote_id = $('#ftl_buyer_quoteid').val();

        $('.token-input-delete-token').click(function(){
                        $(this).parent().remove();
                });						
            $.ajax({
                url: '/getEditSellerslist',
                type: "post",
                data: {
                    'post_type': 2,
                    'seller_list':seller_id_list,
                    '_token': $('input[name=_token]').val(),
                    'buyer_quote_id':buyer_quote_id},
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


	