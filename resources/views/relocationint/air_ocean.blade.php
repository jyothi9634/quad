{{--*/ $air_check = 'checked'; /*--}}
{{--*/ $ocean_check = ''; /*--}}
@if(Session::has('seller_searchrequest_relocationint_type') && Session::get('seller_searchrequest_relocationint_type') != "")
    {{--*/ $search_relocation_inttype = Session::get('seller_searchrequest_relocationint_type'); /*--}}
	@if($search_relocation_inttype == 1)
		{{--*/ $air_check = 'checked'; /*--}}
		{{--*/ $ocean_check = ''; /*--}}
	@else
		{{--*/ $air_check = ''; /*--}}
		{{--*/ $ocean_check = 'checked'; /*--}}		
	@endif
@endif
<div class="radio-block">
	<input type="radio" name="int_air_spot" id="int_air_spot" {{$air_check}}>
	<label for="int_air_spot"><span></span>Air</label>
		
	<input type="radio" name="int_ocean_spot" id="int_ocean_spot" {{$ocean_check}}/>
	<label for="int_ocean_spot"><span></span>Ocean</label>
</div>
