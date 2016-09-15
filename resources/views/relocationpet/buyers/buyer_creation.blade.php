@extends('app')
@section('content')
	
{{--*/ $searchrequest=array(); /*--}}
  {{--*/ $from_loaction=''; /*--}}
  {{--*/ $to_loaction=''; /*--}}
  {{--*/ $from_loaction_id=''; /*--}}
  {{--*/ $to_loaction_id=''; /*--}}
  {{--*/ $from_date=''; /*--}}
  {{--*/ $to_date=''; /*--}}
  {{--*/ $pet_type=''; /*--}}
  {{--*/ $cage_type=''; /*--}}
  {{--*/ $breed_type=''; /*--}}
@if(Session::has('searchMod'))
	{{--*/ $searchrequest=Session::get('searchMod'); /*--}}
	{{--*/ $from_loaction=$searchrequest['from_location_buyer']; /*--}}
	{{--*/ $to_loaction=$searchrequest['to_location_buyer']; /*--}}
	{{--*/ $from_loaction_id=$searchrequest['from_city_id_buyer']; /*--}}
	{{--*/ $to_loaction_id=$searchrequest['to_city_id_buyer']; /*--}}
	{{--*/ $from_date=$searchrequest['from_location_buyer']; /*--}}
	{{--*/ $to_date=$searchrequest['delivery_date_buyer']; /*--}}
	{{--*/ $pet_type=$searchrequest['pet_type_reslocation']; /*--}}
	{{--*/ $cage_type=$searchrequest['cage_type_reslocation']; /*--}}
	{{--*/ $breed_type=$searchrequest['breed_type_reslocation']; /*--}}
@endif

@include('partials.page_top_navigation')

@if(Session::has('transactionId') && Session::get('transactionId')!='')

	{{--*/ $transactionId = Session::get('transactionId') /*--}}        
	{{--*/ Session::get('postType') /*--}}
        
			<script>
			$(document).ready(function(){
                            var postType = {{ Session::get('postType') }}			
                            
                                $("#erroralertmodal .modal-body").html("Your request has been posted to the relevant sellers. Your transacton id is <?php echo $transactionId;?>. You would be getting the quotes from the sellers online.");
                                $("#erroralertmodal").modal({
                                    show: true
                                }).one('click','.ok-btn',function (e){
                                        window.location="/buyerposts";
                                });
                            
                         });
</script>				
		
@endif


<div class="main">
	<div class="container">
		
		<span class="pull-left"><h1 class="page-title">Post & Get Quote (Relocation Pet)</h1><a href="#" class="change-service" data-toggle="modal" data-target="#change-service">Change Service</a></span>
		@if ($url_search_search == 'byersearchresults')
		<span class="pull-right"><a href="{{ $serverpreviUrL  }}" class="back-link">
			Back to Search</a>
		</span>
	   	@endif

		<div class="clearfix"></div>
		
		{!! Form::open(['url' => 'relocationbuyerpostcreation','id'=>'posts-form_buyer_relocationpet', 'autocomplete'=>'off']) !!}
		 
		<div class="col-md-12 inner-block-bg inner-block-bg1">
			<div class="clearfix margin-top"></div>
				<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('from_location',$from_loaction , ['id' => 'from_location','class' => 'form-control', 'placeholder' => 'From City *']) !!}
							{!! Form::hidden('from_location_id', $from_loaction_id, array('id' => 'from_location_id')) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-map-marker"></i></span>
							{!! Form::text('to_location', $to_loaction, ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'To City *']) !!}
                                                        {!! Form::hidden('to_location_id', $to_loaction_id, array('id' => 'to_location_id')) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('valid_from', $from_date, ['id' => 'datepicker','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date *']) !!}
						</div>
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-calendar-o"></i></span>
							{!! Form::text('valid_to', $to_date, ['id' => 'datepicker_to_location','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date ']) !!}
						</div>
					</div>
                                <div class="clearfix"></div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-paw"></i></span>
							{!!	Form::select('selPettype',(['' => 'Pet Type *'] +$getAllPetTypes), $pet_type ,['class' =>'selectpicker','id'=>'selPettype','data-purl' => URL::to('relocationpet/ajxbreedtypes') ]) !!}
						</div>
						
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-paw"></i></span>							
                                                        {!! Form::select('selBreedtype',(['' => 'Breed'] +$getAllBreedTypes), $breed_type, ['class' =>'selectpicker','id'=>'selBreedtype' ]) !!}  
						</div>
						
					</div>
					<div class="col-md-3 form-control-fld">
						<div class="input-prepend">
							<span class="add-on"><i class="fa fa-link"></i></span>
							{!!	Form::select('selCageType',(['' => 'Cage Type *'] +$getAllCageTypes), $cage_type ,['class' =>'selectpicker','id'=>'selCageType']) !!}
						</div>
					</div>
			
		</div>

		<div class="col-md-12 inner-block-bg inner-block-bg1">
			<div class="col-md-12 form-control-fld margin-top margin-bottom-none">
				<div class="radio-block">
					<div class="radio_inline">
						<input type="radio" name="ptlQuoteaccessId" value="1" id="post-public" checked="checked" class="create-posttype-service crete-relocation" />
						<label for="post-public"><span></span>Post Public</label>
					</div>
					<div class="radio_inline">
						<input type="radio" name="ptlQuoteaccessId" value="2" id="post-private" class="create-posttype-service-petmove crete-relocation"/> 
						<label for="post-private"><span></span>Post Private</label>
					</div>
				</div>
			</div>

			<div class="col-md-3 form-control-fld" id="hideseller" style="display:none;">
				<input type="text" id="demo-input-local" class="form-control form-control1" name="seller_list" />
			</div>

			<div class="clearfix"></div>
			<div class="check-box form-control-fld">
				{!! Form::checkbox('agree', 1, false,array('id'=>'agree')) !!}
				<span class="lbl padding-8">Accept Terms & Conditions (Digital Contract)</span>
			</div>

		</div>

		<div class="clearfix"></div>

		<div class="container">
			<div class="col-md-4 col-md-offset-4">
				<button class="btn theme-btn btn-block" name="getquote" id="getquote">Get Quote</button>
			</div>
		</div>

	{!! Form::close() !!}	

	</div>
</div>

 
@include('partials.footer')

@endsection