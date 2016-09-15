<div class="table-data">
										

										<!-- Table Row Starts Here -->
								@foreach($roomtypes as $roomtype)
										<div class="table-row inner-block-bg checkbox-alignment">
											<div class="col-md-6 padding-left-none">{{$roomtype->room_particular_type}}</div>
											
											@if($room_id==1)
											@if(Session::has('masterBedRoom'))
											{{--*/ $masterbedroom=array(); /*--}}
											{{--*/ $masterbedroom=Session::get('masterBedRoom'); /*--}}
											{{--*/ $masterid= 'number_items_'.$roomtype->id; /*--}}
											{{--*/ $mastercheck= 'crating_'.$roomtype->id; /*--}}
											<div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" value="{{ $masterbedroom[$masterid] }}" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											@if($masterbedroom[$mastercheck]==1)
												<input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}" checked><span class="lbl padding-8"></span>
											@else
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											@endif	
											</div>
											@else
											 <div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											
											</div>
											@endif
											@endif
											
											@if($room_id==2)
											@if(Session::has('masterBedRoom'))
											{{--*/ $masterbedroom=array(); /*--}}
											{{--*/ $masterbedroom=Session::get('masterBedRoom1'); /*--}}
											{{--*/ $masterid= 'number_items_'.$roomtype->id; /*--}}
											{{--*/ $mastercheck= 'crating_'.$roomtype->id; /*--}}
											<div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" value="{{ $masterbedroom[$masterid] }}" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											@if($masterbedroom[$mastercheck]==1)
												<input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}" checked><span class="lbl padding-8"></span>
											@else
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											@endif	
											</div>
											@else
											 <div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											
											</div>
											@endif
											@endif
											
											@if($room_id==3)
											@if(Session::has('masterBedRoom2'))
											{{--*/ $masterbedroom=array(); /*--}}
											{{--*/ $masterbedroom=Session::get('masterBedRoom2'); /*--}}
											{{--*/ $masterid= 'number_items_'.$roomtype->id; /*--}}
											{{--*/ $mastercheck= 'crating_'.$roomtype->id; /*--}}
											<div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" value="{{ $masterbedroom[$masterid] }}" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											@if($masterbedroom[$mastercheck]==1)
												<input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}" checked><span class="lbl padding-8"></span>
											@else
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											@endif	
											</div>
											@else
											 <div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											
											</div>
											@endif
											@endif
											
											@if($room_id==4)
											@if(Session::has('masterBedRoom3'))
											{{--*/ $masterbedroom=array(); /*--}}
											{{--*/ $masterbedroom=Session::get('masterBedRoom3'); /*--}}
											{{--*/ $masterid= 'number_items_'.$roomtype->id; /*--}}
											{{--*/ $mastercheck= 'crating_'.$roomtype->id; /*--}}
											<div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" value="{{ $masterbedroom[$masterid] }}" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											@if($masterbedroom[$mastercheck]==1)
												<input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}" checked><span class="lbl padding-8"></span>
											@else
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											@endif	
											</div>
											@else
											 <div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											
											</div>
											@endif
											@endif
											
											@if($room_id==5)
											@if(Session::has('lobby'))
											{{--*/ $masterbedroom=array(); /*--}}
											{{--*/ $masterbedroom=Session::get('lobby'); /*--}}
											{{--*/ $masterid= 'number_items_'.$roomtype->id; /*--}}
											{{--*/ $mastercheck= 'crating_'.$roomtype->id; /*--}}
											<div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" value="{{ $masterbedroom[$masterid] }}" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											@if($masterbedroom[$mastercheck]==1)
												<input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}" checked><span class="lbl padding-8"></span>
											@else
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											@endif	
											</div>
											@else
											 <div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											
											</div>
											@endif
											@endif
											
											@if($room_id==6)
											@if(Session::has('kitchen'))
											{{--*/ $masterbedroom=array(); /*--}}
											{{--*/ $masterbedroom=Session::get('kitchen'); /*--}}
											{{--*/ $masterid= 'number_items_'.$roomtype->id; /*--}}
											{{--*/ $mastercheck= 'crating_'.$roomtype->id; /*--}}
											<div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" value="{{ $masterbedroom[$masterid] }}" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											@if($masterbedroom[$mastercheck]==1)
												<input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}" checked><span class="lbl padding-8"></span>
											@else
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											@endif	
											</div>
											@else
											 <div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											
											</div>
											@endif
											@endif
											
											@if($room_id==7)
											@if(Session::has('bathroom'))
											{{--*/ $masterbedroom=array(); /*--}}
											{{--*/ $masterbedroom=Session::get('bathroom'); /*--}}
											{{--*/ $masterid= 'number_items_'.$roomtype->id; /*--}}
											{{--*/ $mastercheck= 'crating_'.$roomtype->id; /*--}}
											<div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" value="{{ $masterbedroom[$masterid] }}" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											@if($masterbedroom[$mastercheck]==1)
												<input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}" checked><span class="lbl padding-8"></span>
											@else
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											@endif	
											</div>
											@else
											 <div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											
											</div>
											@endif
											@endif
											
											@if($room_id==8)
											@if(Session::has('living'))
											{{--*/ $masterbedroom=array(); /*--}}
											{{--*/ $masterbedroom=Session::get('living'); /*--}}
											{{--*/ $masterid= 'number_items_'.$roomtype->id; /*--}}
											{{--*/ $mastercheck= 'crating_'.$roomtype->id; /*--}}
											<div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" value="{{ $masterbedroom[$masterid] }}" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											@if($masterbedroom[$mastercheck]==1)
												<input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}" checked><span class="lbl padding-8"></span>
											@else
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											@endif	
											</div>
											@else
											 <div class="col-md-2 padding-left-none">
												<input type="text" name="roomitems_{{$roomtype->id}}" id="roomitems_{{$roomtype->id}}" class="form-control form-control1 roomitems" onblur="javascript:valuecheck(this.value,this.id)">
											
											</div>
											<div class="col-md-2 padding-left-none text-center">
												<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
											</div>
											<div class="col-md-2 padding-left-none text-center">
											
											    <input type="checkbox" name="roomcrating_{{$roomtype->id}}" id="roomcrating_{{$roomtype->id}}"><span class="lbl padding-8"></span>
											
											</div>
											@endif
											@endif
											
										</div>
								@endforeach
										<!-- Table Row Ends Here -->

										<!-- Table Row Starts Here -->

									</div>
<script type="text/javascript">

function valuecheck(str,itemid){
    if(str!=""){
	if(isNaN(parseInt(str))){
	alert("Please enter numbers only");	
	$("#"+itemid).val("");
		
	return false;
	}
    }
}
</script>									