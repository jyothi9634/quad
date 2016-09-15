@extends('app')
@section('content')
<!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')
	
		<!-- Header Starts Here -->		
		<div class="clearfix"></div>
		<div class="main">
                    
			<div class="container">
                            {!! Form::open(['url' =>'byersearchresults','id' => 'posts-form_buyer_relocationpet' , 'autocomplete'=>'off','method'=>'get']) !!}	
				<div class="home-search gray-bg margin-top-none">

					<div class="col-md-12 padding-none">
                            <div class="col-md-4 form-control-fld">
                                    <div class="input-prepend">
                                            <span class="add-on"><i class="fa fa-map-marker"></i></span>								
                                            {!! Form::text('from_location', '', ['id' => 'from_location', 'class'=>'form-control','placeholder' => 'From City *']) !!}
                                            {!! Form::hidden('from_location_id', '', array('id' => 'from_location_id')) !!}
                                    </div>
                            </div>
                            <div class="col-md-4 form-control-fld">
                                    <div class="input-prepend">
                                            <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                            {!! Form::text('to_location', '', ['id' => 'to_location', 'class'=>'form-control', 'placeholder' => 'To City *']) !!}
                                            {!! Form::hidden('to_location_id', '', array('id' => 'to_location_id')) !!}
                                    </div>  {!!	Form::hidden('is_search',1,array('class'=>'form-control')) !!}
                            </div>
	
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('from_date', '' , ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control','readonly' => true, 'placeholder' => 'Dispatch Date *']) !!}
                                                                        <input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="0">
								</div>
							</div>
                                                        <div class="clearfix"></div>
							<div class="col-md-4 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-calendar-o"></i></span>
									{!! Form::text('to_date', '', ['id' => 'datepicker_to_location','class' => 'calendar form-control to-date-control','readonly' => true, 'placeholder' => 'Delivery Date ']) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-paw"></i></span>
									{!! Form::select('selPettype',(['' => 'Pet Type *'] +$getAllPetTypes), '' ,['class' =>'selectpicker','id'=>'selPettype','data-purl' => URL::to('relocationpet/ajxbreedtypes') ]) !!}
								</div>
							</div>
							<div class="col-md-3 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-paw"></i></span>
									<select id="selBreedtype" name="selBreedtype" class="selectpicker">
                                                                               <option value="0">Breed</option>
                                                                        </select>
								</div>
							</div>
							<div class="col-md-2 form-control-fld">
								<div class="input-prepend">
									<span class="add-on"><i class="fa fa-chain"></i></span>
									{!! Form::select('selCageType',(['' => 'Cage Type *'] +$getAllCageTypes), '' ,['class' =>'selectpicker','id'=>'selCageType']) !!}
								</div>
							</div>
				
					</div>
					
				
				</div>
				
				<div class="col-md-4 col-md-offset-4">
					<button class="btn theme-btn btn-block">Search</button>
				</div>

			</div>
                    {!! Form::close() !!}
			</div>
			<div class="clearfix"></div>
			
@include('partials.footer')
@endsection