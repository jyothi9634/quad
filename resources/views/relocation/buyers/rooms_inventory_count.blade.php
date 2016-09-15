<?php $totalParticularsCount = 0; ?>
<div class="table-heading inner-block-bg">
	<div class="col-md-3 padding-left-none">Particulars</div>
	<div class="col-md-2 padding-left-none text-center">Master Bedroom</div>
	<div class="col-md-1 padding-left-none text-center">Bedroom - 1</div>
	<div class="col-md-1 padding-left-none text-center">Bedroom - 2</div>
	<div class="col-md-1 padding-left-none text-center">Bedroom - 3</div>
	<div class="col-md-1 padding-left-none text-center">Lobby / Garrage / Store Room</div>
	<div class="col-md-1 padding-left-none text-center">Kitchen / Dinning</div>
	<div class="col-md-1 padding-left-none text-center">Bathroom</div>
	<div class="col-md-1 padding-left-none text-center">Living / Drawing Room</div>
</div>
<!-- Table Head Ends Here -->
<div class="table-data">
	<!-- Table Row Starts Here -->
	<div class="table-row inner-block-bg">
		<div class="col-md-3 padding-left-none medium-text">No of Items</div>
		<div class="col-md-2 padding-left-none text-center">
		{{--*/ $masterbedroom=array(); /*--}}
		{{--*/ $masterbedroom=Session::get('masterBedRoom'); /*--}}
		
		@if($masterbedroom['total']!=0)
			{{--*/ $totalParticularsCount += $masterbedroom['total']  /*--}}
			{{ $masterbedroom['total'] }}
		@else
		--
		@endif
		</div>
		<div class="col-md-1 padding-left-none text-center">
		{{--*/ $masterbedroom1=array(); /*--}}
		{{--*/ $masterbedroom1=Session::get('masterBedRoom1'); /*--}}
		
		@if($masterbedroom1['total']!=0)
			{{--*/ $totalParticularsCount += $masterbedroom1['total']  /*--}}
			{{ $masterbedroom1['total'] }}
		@else
		--
		@endif
		
		</div>
		<div class="col-md-1 padding-left-none text-center">
		{{--*/ $masterbedroom2=array(); /*--}}
		{{--*/ $masterbedroom2=Session::get('masterBedRoom2'); /*--}}
		
		@if($masterbedroom2['total']!=0)
			{{--*/ $totalParticularsCount += $masterbedroom2['total']  /*--}}
			{{ $masterbedroom2['total'] }}
		@else
		--
		@endif
		
		</div>
		<div class="col-md-1 padding-left-none text-center">
		{{--*/ $masterbedroom3=array(); /*--}}
		{{--*/ $masterbedroom3=Session::get('masterBedRoom3'); /*--}}
		
		@if($masterbedroom3['total']!=0)
			{{--*/ $totalParticularsCount += $masterbedroom3['total']  /*--}}
			{{ $masterbedroom3['total'] }}
		@else
		--
		@endif
		
		</div>
		<div class="col-md-1 padding-left-none text-center">
		{{--*/ $lobbycount=array(); /*--}}
		{{--*/ $lobbycount=Session::get('lobby'); /*--}}
		
		@if($lobbycount['total']!=0)
			{{--*/ $totalParticularsCount += $lobbycount['total']  /*--}}
			{{ $lobbycount['total'] }}
		@else
		--
		@endif
		
		</div>
		<div class="col-md-1 padding-left-none text-center">
		{{--*/ $kitchencount=array(); /*--}}
		{{--*/ $kitchencount=Session::get('kitchen'); /*--}}
		
		@if($kitchencount['total']!=0)
			{{--*/ $totalParticularsCount += $kitchencount['total']  /*--}}
			{{ $kitchencount['total'] }}
		@else
		--
		@endif
		
		</div>
		<div class="col-md-1 padding-left-none text-center">
		
		{{--*/ $bathroomcount=array(); /*--}}
		{{--*/ $bathroomcount=Session::get('bathroom'); /*--}}
		
		@if($bathroomcount['total']!=0)
			{{--*/ $totalParticularsCount += $bathroomcount['total']  /*--}}
			{{ $bathroomcount['total'] }}
		@else
		--
		@endif
		
		</div>
		<div class="col-md-1 padding-left-none text-center">
		{{--*/ $livingcount=array(); /*--}}
		{{--*/ $livingcount=Session::get('living'); /*--}}
		
		@if($livingcount['total']!=0)
			{{--*/ $totalParticularsCount += $livingcount['total']  /*--}}
			{{ $livingcount['total'] }}
		@else
		--
		@endif
		
		</div>
		
	</div>
	<!-- Table Row Ends Here -->
	<input type="hidden" name="tot_items" value="{{$totalParticularsCount}}" />
</div>