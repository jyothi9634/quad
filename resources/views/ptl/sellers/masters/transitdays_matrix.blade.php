@extends('app') @section('content')
@include('partials.page_top_navigation')
<div class="main">

	<div class="container">
		@if(Session::has('ptl_success_message') &&
		Session::get('ptl_success_message')!='')
		<div class="flash">
			<p class="text-success col-sm-12 text-center flash-txt alert-success">
				{{ Session::get('ptl_success_message') }}</p>
		</div>
		@endif @if(Session::has('ptl_error_message') &&
		Session::get('ptl_error_message')!='')
		<div class="flash">
			<p class="text-success col-sm-12 text-center flash-txt alert-danger">
				{{ Session::get('ptl_error_message') }}</p>
		</div>
		@endif <span class="pull-left"><h1 class="page-title">Transit Days Matrix
		@if(Session::get('service_id') == ROAD_PTL)
				(LTL)
				@elseif(Session::get('service_id') == RAIL)
				(RAIL)
				@elseif(Session::get('service_id') == AIR_DOMESTIC)
				(AIR DOMESTIC)
				@elseif(Session::get('service_id') == AIR_INTERNATIONAL)
				(AIR INTERNATIONAL)
				@elseif(Session::get('service_id') == COURIER)
				(COURIER)
				@elseif(Session::get('service_id') == OCEAN)
				(OCEAN)
				@endif
		</h1> </span>
			
		<ul id="master-tabs">
						<li><a href="zone">Zone</a></li>
						<li><a href="tier">Tier</a></li>
						<li><a href="transit_matrix">Transit Days Matrix</a></li>
						<li><a href="sector">Sector</a></li>
						<li class="active"><a href="pincode">Pincode</a></li>
					</ul>	



		<div class="col-md-12 padding-none">
		
			
<div class="tab-nav">
					
				</div>
				
				<div class="clearfix"></div>
		
			<div class="scroll_tab_div">
			
			<div class="main-inner">


				<!-- Right Section Starts Here -->

				<div class="main-right">





					<!-- Table Starts Here -->

					<div class="table-div">
						<div id="table-data" class="master-table-tier">	
			<?php
			$innertiers = $tiers;
			$matrixarray = array ();
			$matrixidsarray = array ();
			$tiernames = array ();
			foreach ( $tiers as $tier ) {
				$tiernames [$tier->id] = $tier->tier_name;
			}
			foreach ( $tiers as $tier ) {
				foreach ( $innertiers as $innertier ) {
					$transitdays = DB::table ( 'ptl_transitdays as pd' )->Where ( 'pd.from_tier_id', $tier->id )->Where ( 'pd.to_tier_id', $innertier->id )->select ( 'no_days' )->first ();
					
					$matrixarray [$tier->id] [$innertier->id] = (isset ( $transitdays->no_days )) ? $transitdays->no_days : '';
				}
			}
			
			// echo "<pre>";print_R($matrixarray);echo "</pre>";
			echo "<table class='table editable'><thead><th class='col-xs-1'></th>";
			foreach ( $matrixarray as $key => $matrixele ) {
				echo '<th>' . $tiernames [$key] . '</th>';
			}
			// echo "<th>Action</th></thead> <tbody>";
			
			foreach ( $matrixarray as $key => $matrixele ) {
				echo "<tr><th><strong> $tiernames[$key] </strong></th>";
				
				foreach ( $matrixele as $key1 => $matrixelesingle ) {
					
					echo '<td id="' . $key . '_' . $key1 . '">' . $matrixelesingle . '</td>';
				}
				// echo "<td><i onclick='deleteRow(2);' class='fa fa-trash-o red'></i></td></tr>";
			}
			echo " </tbody></table>";
			
			// echo "<pre>";print_r($fromTier); ?>
			 </div>
					</div>
				</div>
			</div>
			</div>

		</div>
	</div>
	
</div>

@include('partials.footer')

</div>



<script type="text/javascript">
$("document").ready(function(){  
//for designing purpose
$('#table-data tr').addClass('table table-heading inner-block-bg');
$('#table-data th').addClass('align-left col-md-1');
$('#table-data td').addClass('align-left col-md-1');
$('#table-data td:first-child').addClass('first-td');
$('.editablegrid-action').addClass('align-left');


$('.first-td').addClass('col-xs-1 align-left');
});
//designing ends
$(function() {
    $('table tr td').hover(function() {
        $(this).css('background-color', '#FFFFB0');
    },
    function() {
        $(this).css('background-color', '#F4F4F4');
    });
});			
</script>

@endsection
