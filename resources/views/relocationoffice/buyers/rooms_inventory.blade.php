<div class="table-data">
<!-- Table Row Starts Here -->
	@foreach($particulars as $particular)
			<div class="table-row inner-block-bg">
				<div class="col-md-6 padding-left-none">{{$particular->office_particular_type}}</div>
														
				 <div class="col-md-2 padding-left-none">
					<input type="text" name="roomitems_{{$particular->id}}" id="roomitems_{{$particular->id}}" class="form-control form-control1 roomitems" onblur="javascript:valuecheck(this.value,this.id)">
				
				</div>
				<div class="col-md-2 padding-left-none text-center">
					<input type="checkbox" checked disabled><span class="lbl padding-8"></span>
				</div>
				<div class="col-md-2 padding-left-none text-center">
				    <input type="checkbox" name="roomcrating_{{$particular->id}}" id="roomcrating_{{$particular->id}}"><span class="lbl padding-8"></span>
				</div>											
			</div>
	@endforeach
<!-- Table Row Ends Here -->
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