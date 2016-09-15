@inject('commonComponent', 'App\Components\CommonComponent')
@inject('buyerCommonComponent', 'App\Components\BuyerComponent')
@inject('termbuyer', 'App\Components\Term\TermBuyerComponent')
@inject('commonforbuyername', 'App\Components\CommonComponent')
@extends('app')
@section('content')
{{--*/ $serviceId = Session::get('service_id') /*--}} 
<?php //echo "<pre>"; print_r($getBuyerTermQuotesdata); die;
//check the conditions for multi or not items
//$loadtype = $termbuyer->checkMulti($serviceId,$termQuotes->id,"lkp_load_type_id");
$vehicletype = 	$termbuyer->checkMulti($serviceId,$termQuotes->id,"lkp_vehicle_type_id");
$from = $termbuyer->checkMulti($serviceId,$termQuotes->id,"from_location_id");
$to = 	$termbuyer->checkMulti($serviceId,$termQuotes->id,"to_location_id");
//if($serviceId != COURIER){
//if($loadtype == "multi"){
//	$displayLoadType = "Many";
//} else {
//	$displayLoadType = $getBuyerTermQuotesdata[0]->load_type;
//}
//}
if ($serviceId!=RELOCATION_GLOBAL_MOBILITY) {
      if($vehicletype == "multi"){
	$displayVehicleType = "Many";
      }else {
           if($serviceId==ROAD_FTL || $serviceId==RELOCATION_DOMESTIC){
           $displayVehicleType = $getBuyerTermQuotesdata[0]->vehicle_type;
           }
      }
      if($from == "multi"){
           $displayFromLocationType = "Many";
      }else {
           if($serviceId==ROAD_FTL || $serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC || $serviceId==RELOCATION_DOMESTIC || $serviceId==COURIER ||  $serviceId==RELOCATION_INTERNATIONAL){
           $displayFromLocationType = $getBuyerTermQuotesdata[0]->from_locationcity;
          }
          if($serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN){
          $displayFromLocationType = $getBuyerTermQuotesdata[0]->from_postofficename;
          }


      }
}
if ($serviceId ==RELOCATION_GLOBAL_MOBILITY) {
    $displayFromLocationType = $getBuyerTermQuotesdata[0]->from_locationcity;  
}


if ($serviceId!=RELOCATION_GLOBAL_MOBILITY) {
      if($to == "multi"){
           $displayToLocationType = "Many";
      }else {
      if($serviceId==ROAD_FTL || $serviceId==ROAD_PTL || $serviceId==RAIL || $serviceId==AIR_DOMESTIC  || $serviceId==RELOCATION_DOMESTIC || $serviceId==COURIER ||  $serviceId==RELOCATION_INTERNATIONAL){
           $displayToLocationType = $getBuyerTermQuotesdata[0]->to_locationcity;
      }
      if($serviceId==AIR_INTERNATIONAL || $serviceId==OCEAN){
           $displayToLocationType = $getBuyerTermQuotesdata[0]->to_postofficename;
      }
      }
}

?>
@if(URL::previous() == URL::current())      
      {{--*/  $backToPostsUrl = url('buyerposts/') /*--}}
@else
      {{--*/  $backToPostsUrl = URL::previous() /*--}}
@endif
  	 <!-- Page top navigation Starts Here-->
	@include('partials.page_top_navigation')
	<!-- Page top navigation ends Here-->

    <div class="main">
      <div class="container">
       @include('partials.content_top_navigation_links')
        <div class="clearfix"></div>
        <span class="pull-left">
        <h1 class="page-title">Term Transaction - {!! $termQuotes->transaction_id !!}</h1>
        </span> <span class="pull-right">  
        
        <a href="{{ $backToPostsUrl }}" class="back-link1">Back to Posts</a> </span>
        {!! Form::open(['url'=>'/termupdateBiddate','id' => 'term_bid_date_edit', 'autocomplete'=>'off']) !!}
		<input type="hidden" name="quoteid" value="{!! $quoteId; !!}" >
		<input type="hidden" name="serviceid" value="{!! $serviceId !!}" >
        <!-- Search Block Starts Here -->
        <div class="col-md-12 padding-none">
          <div class="search-block inner-block-bg">
            <div class="from-to-area">           
             {{ Auth::user()->username }}          
            </div>
            <div class="from-to-area">
              @if($serviceId!=RELOCATION_GLOBAL_MOBILITY)    
              <span class="search-result ">
                <i class="fa fa-map-marker"></i> <span class="location-text">{!! $displayFromLocationType !!} to {!! $displayToLocationType !!}</span> 
              </span>
              @else
              <span class="search-result ">
                <i class="fa fa-map-marker"></i> <span class="location-text">{!! $displayFromLocationType !!} </span> 
              </span>
              @endif
                  
            </div>
            <div class="date-area">
              <div class="col-md-6 padding-none">
                <p class="search-head">Valid From</p>
                <span class="search-result"> <i class="fa fa-calendar-o"></i>                 
                @if(isset($getBuyerTermQuotesdata[0]->from_date) && $getBuyerTermQuotesdata[0]->from_date != '0000-00-00')
                {{date("d/m/Y", strtotime($getBuyerTermQuotesdata[0]->from_date))}}  
                @else &nbsp;
                @endif 
                </span> </div>
             	 <div class="col-md-6 padding-none">
                <p class="search-head">Valid To</p>
                <span class="search-result"> <i class="fa fa-calendar-o"></i>                
                 @if(isset($getBuyerTermQuotesdata[0]->to_date) && $getBuyerTermQuotesdata[0]->to_date != '0000-00-00')
               	 {{date("d/m/Y", strtotime($getBuyerTermQuotesdata[0]->to_date))}}
              	 @else &nbsp;
              	 @endif  
                 </span> </div>
            </div>
            
            <div>
              <p class="search-head">Status</p>
              <span class="search-result">
              @if($termQuotes->lkp_post_status_id==1)
              Draft
              @else
              Open
              @endif</span> </div>
            <div class="text-right filter-details">
              <div class="info-links"> <a class="transaction-details"><span class="show-icon">+</span> <span class="hide-icon">-</span> Details</a> </a> </div>
            </div>
          </div>
          
          
          
          <div class="col-md-12 show-trans-details-div padding-none">
            <!-- Table Starts Here -->
            <div class="table-div table-style1 padding-none">
              <!-- Table Head Starts Here -->
              <div class="table-heading inner-block-bg">
              @if($serviceId == ROAD_FTL)
                <div class="col-md-2 padding-left-none"> From Location </div>
                <div class="col-md-2 padding-left-none">To Location</div>
                <div class="col-md-2 padding-left-none">Vehicle Type</div>
                <div class="col-md-2 padding-left-none">Load Type</div>    
                <div class="col-md-2 padding-left-none">Quantity</div>             
                <div class="col-md-2 padding-left-none">&nbsp;</div>
               @elseif($serviceId == ROAD_PTL || $serviceId== RAIL || $serviceId== AIR_DOMESTIC)
               <div class="col-md-2 padding-left-none"> From Postoffice</div>
                <div class="col-md-2 padding-left-none">To Postoffice</div>                
                <div class="col-md-2 padding-left-none">Load Type</div>    
                <div class="col-md-2 padding-left-none">Volume</div>             
                <div class="col-md-2 padding-left-none">&nbsp;</div>
                 @elseif($serviceId == AIR_INTERNATIONAL)
               <div class="col-md-2 padding-left-none"> From</div>
                <div class="col-md-2 padding-left-none">To</div>                
                <div class="col-md-2 padding-left-none">Load Type</div>    
                <div class="col-md-2 padding-left-none">Volume</div>             
                <div class="col-md-2 padding-left-none">&nbsp;</div>
                 @elseif($serviceId == OCEAN)
               <div class="col-md-2 padding-left-none"> From</div>
                <div class="col-md-2 padding-left-none">To</div>                
                <div class="col-md-2 padding-left-none">Load Type</div>    
                <div class="col-md-2 padding-left-none">Volume</div>             
                <div class="col-md-2 padding-left-none">&nbsp;</div>	
                @elseif($serviceId == COURIER)
                <div class="col-md-3 padding-left-none"> From Location </div>
                <div class="col-md-3 padding-left-none">To Location</div>
                <div class="col-md-3 padding-left-none">Volume</div>
                <div class="col-md-3 padding-left-none">Number of Packages</div>            
                @elseif($serviceId == RELOCATION_DOMESTIC)                
                    @if($termQuotes->lkp_post_ratecard_type==1)
                    <div class="col-md-3 padding-left-none"> From Location </div>
                    <div class="col-md-3 padding-left-none">To Location</div>
                    <div class="col-md-3 padding-left-none">Volume</div>
                    <div class="col-md-3 padding-left-none">Number of Packages</div>  
                    @else
                    <div class="col-md-2 col-sm-3 col-xs-5 padding-left-none">From</div>
                    <div class="col-md-2 col-sm-3 col-xs-5 padding-left-none">To</div>
                    <div class="col-md-2 col-sm-3 col-xs-5 padding-left-none">Vehicle Category</div>
                    <div class="col-md-2 col-sm-3 col-xs-4 padding-none">Vehicle Type</div>
                    <div class="col-md-2 col-sm-3 col-xs-4 padding-none">Vehicle Model</div>
                    <div class="col-md-2 col-sm-3 col-xs-4 padding-none">No of Vehicles</div> 
                   @endif
               @elseif($serviceId == RELOCATION_INTERNATIONAL)
                <div class="col-md-3 padding-left-none"> From Location </div>
                <div class="col-md-3 padding-left-none">To Location</div>                
                <div class="col-md-3 padding-left-none">No of Moves</div>    
                <div class="col-md-3 padding-left-none">
                    @if($termQuotes->lkp_lead_type_id==1)    
                        Average KG/Move
                    @else
                        Average CBM/Move
                    @endif
                </div>  
               @endif	
              </div>
              <!-- Table Head Ends Here -->
              <div class="table-data">
                <!-- Table Row Starts Here -->
                @if(isset($getBuyerTermQuotesdata) && !empty($getBuyerTermQuotesdata))                
                @foreach($getBuyerTermQuotesdata as $key=>$getBuyerTermQuotesdata)
                <div class="table-row inner-block-bg">
                @if($serviceId == ROAD_FTL)
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->from_locationcity !!} </div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->to_locationcity !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->vehicle_type !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->load_type !!}</div>  
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->quantity !!}</div>
                  @elseif($serviceId == ROAD_PTL || $serviceId == RAIL || $serviceId == AIR_DOMESTIC)              
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->from_pincode !!} - {!! $getBuyerTermQuotesdata->from_postofficename !!} </div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->to_pincode !!} - {!! $getBuyerTermQuotesdata->to_postofficename !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->load_type !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->volume !!}</div>
                   @elseif($serviceId == AIR_INTERNATIONAL)              
                  <div class="col-md-2 padding-left-none"> {!! $getBuyerTermQuotesdata->from_postofficename !!} </div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->to_postofficename !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->load_type !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->volume !!}</div>  
                   @elseif($serviceId == OCEAN)              
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->from_postofficename !!} </div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->to_postofficename !!}</div>
                  <div class="col-md-2 padding-left-none">{!! $getBuyerTermQuotesdata->load_type !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->volume !!}</div>
                  @elseif($serviceId == COURIER)              
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->from_locationcity !!} </div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->to_locationcity !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->volume !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->number_packages !!}</div>
                  @elseif($serviceId == RELOCATION_INTERNATIONAL)              
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->from_locationcity !!} </div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->to_locationcity !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->number_loads !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->avg_kg_per_move !!}</div>                  
                  @elseif($serviceId == RELOCATION_DOMESTIC) 
                   @if($termQuotes->lkp_post_ratecard_type==1)             
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->from_locationcity !!} </div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->to_locationcity !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->volume !!}</div>
                  <div class="col-md-3 padding-left-none">{!! $getBuyerTermQuotesdata->number_packages !!}</div>
                  @else
                   <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">{{ $getBuyerTermQuotesdata->from_locationcity }}</div>
                   <div class="col-md-2 col-sm-2 col-xs-5 padding-left-none">{{ $getBuyerTermQuotesdata->to_locationcity }}</div>
                   <div class="col-md-2 col-sm-3 col-xs-5 padding-left-none">{{$commonComponent->getVehicleCategoryById($getBuyerTermQuotesdata->lkp_vehicle_category_id)}}</div>
	               <div class="col-md-2 col-sm-3 col-xs-4 padding-none">
	                @if($getBuyerTermQuotesdata->lkp_vehicle_category_id==1)
	                    {{$commonComponent->getVehicleCategorytypeById($getBuyerTermQuotesdata->lkp_vehicle_category_type_id)}}
	                    @else
	                    N/A
	                    @endif
	                </div>
	                <div class="col-md-2 col-sm-3 col-xs-4 padding-none">{{ $getBuyerTermQuotesdata->vehicle_model }}</div>
	                <div class="col-md-2 col-sm-3 col-xs-4 padding-none">{{ $getBuyerTermQuotesdata->no_of_vehicles }}</div>
                    
                    @endif
                  @endif   
                </div>                
                  @endforeach
                  
                  @if($serviceId == RELOCATION_GLOBAL_MOBILITY)
                  <div class="table-row inner-block-bg">
                       @include('relocationglobal.buyers._buyerserviceslist',['buyerpost_id' => $termQuotes->id, 'leadtype' => 'term'])
                  </div>
                  @endif
                  @endif           
              </div>
            </div>
            <!-- Table Ends Here -->
          </div> 
          
          
          
          <!-- ----Bi Dates Section Start here -->          
          <div class="col-md-12 padding-none">
            <!-- Table Starts Here -->
            <div class="table-div table-style1 padding-none">
              <!-- Table Head Starts Here -->
              <div class="table-heading inner-block-bg">
<!--                <div class="col-md-2 padding-left-none"> S. No</div>-->
                <div class="col-md-3 padding-left-none">Bid End Date</div>
                <div class="col-md-3 padding-left-none">Bid End Time</div>
                @if($serviceId != RELOCATION_DOMESTIC && $serviceId != RELOCATION_INTERNATIONAL)
                <div class="col-md-1 padding-left-none">Bid Type</div>
                @endif                
                <div class="col-md-2 padding-left-none">&nbsp;</div>
              </div>
              <!-- Table Head Ends Here -->
              <div class="table-data">
                <!-- Table Row Starts Here -->               
                 @if(isset($bidEndDates) && !empty($bidEndDates))
                 {{--*/ $slNumber = '1' /*--}}
                 @foreach($bidEndDates as $key=>$bidEndDateslist)
                <div class="table-row inner-block-bg">
<!--                  <div class="col-md-2 padding-left-none"> {!! $slNumber !!}</div>-->
                  <div class="col-md-3 padding-left-none">{{date("d/m/Y", strtotime($bidEndDateslist->bid_end_date))}}</div>                 
                  {!! Form::hidden('term_end_min_close_time_hidden',$bidEndDateslist->bid_end_time , ['id' => 'term_end_min_close_time_hidden', 'class'=>'form-control']) !!}
                  <div class="col-md-3 padding-left-none"> {!! $bidEndDateslist->bid_end_time !!}</div>
                  <div class="col-md-2 padding-left-none"> {!! $termQuotes->bid_type !!}</div>              
                </div>   
                 {{--*/ $slNumber++ /*--}}
                  @endforeach
                  @endif           
              </div>
            </div>
            <!-- Table Ends Here -->
          </div>
          <!-- ----Bid Dates Section End here -->
          
          
         
      <?php 
        date_default_timezone_set('Asia/Calcutta');
		$today = date("Y-m-d H:i:00");
		?>
        {{--*/ $bidDateTimes = $bidEndDates[0]->bid_end_date ." ". $bidEndDates[0]->bid_end_time  /*--}}
        
       @if($today < $bidDateTimes)       	
          <div class="col-md-12 col-sm-12 padding-none" id="totla_tab_hide3">				
			<div class="col-md-2 padding-left-none">
                      <div class="input-prepend">
                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                    {!! Form::text('last_bid_date', '' , ['id' => 'last_edit_bid_date', 'class'=>'form-control calendar','placeholder' => 'Bid Closure Date *', 'readonly'=>'readonly']) !!}                    						
					{!! Form::hidden('term_end_max_close_date_hidden',date("d/m/Y", strtotime($termQuotes->from_date)) , ['id' => 'term_end_max_close_date_hidden', 'class'=>'form-control']) !!}
                    </div>
             </div>		
            <div class="col-md-2 padding-left-none">
                    <div id="bid_time_icon" class="input-prepend clsbid_close_time date">    
                        <span class="add-on" ><i class="fa fa-clock-o"></i></span>
                   {!! Form::text('bid_close_time', '' , ['id' => 'bid_edit_close_time', 'class'=>'form-control form-control1 disable-bg-white','placeholder' => 'Bid Closure Time *', 'readonly'=>'readonly']) !!}
                   
                    </div>
                <label for="bid_close_time" id="err_bid_close_time" class="error"></label>
             </div>             
		     <div>
		          {!! Form::submit('Update', ['class' => 'btn post-btn','name' =>'update','id' => 'term_quote_update']) !!}
		     </div>  		
		</div>			
		@endif
		<div class="clearfix"></div>            
      </div>
      {!! Form::close() !!}
    </div>    
  </div>
<!-- Modal -->
	
@include('partials.footer')
@endsection