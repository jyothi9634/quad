  <div class="col-md-4 form-control-fld">
                                    <div class="input-prepend">
                                            <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                            {!! Form::text('from_location', request('from_location'), ['id' => 'from_location', 'class'=>'form-control','placeholder' => 'From Location *']) !!}
                                            {!! Form::hidden('from_location_id', request('from_location_id'), array('id' => 'from_location_id')) !!}
                                    </div>
                            </div>
                            <div class="col-md-4 form-control-fld">
                                    <div class="input-prepend">
                                            <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                            {!! Form::text('to_location', request('to_location'), ['id' => 'to_location', 'class'=>'form-control', 'placeholder' => 'To Location *']) !!}
                                            {!! Form::hidden('to_location_id', request('to_location_id'), array('id' => 'to_location_id')) !!}
                                    </div>
                            </div>
                                <div>
                                    <div class="col-md-4 form-control-fld">
                                        <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                {!! Form::text('from_date', request('from_date'), ['id' => 'dispatch_date1','class' => 'calendar form-control from-date-control', 'placeholder' => 'Dispatch Date *','readonly'=>"readonly"]) !!}
                                                <input type="hidden" name="dispatch_flexible_hidden" id="dispatch_flexible_hidden" value="{{request('dispatch_flexible_hidden')}}">
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-4 form-control-fld">
                                        <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                {!! Form::text('to_date', Session::get('session_delivery_date_buyer'), ['id' => 'delivery_date1','class' => 'calendar form-control to-date-control', 'placeholder' => 'Delivery Date','readonly'=>"readonly"]) !!}
                                                <input type="hidden" name="delivery_flexible_hidden" id="delivery_flexible_hidden" value="{{request('delivery_flexible_hidden')}}">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-paw"></i></span>
                                                {!! Form::select('selPettype',(['' => 'Pet Types *'] +$getAllPetTypes), request('selPettype'), ['class' =>'selectpicker','id'=>'selPettype','data-purl' => URL::to('relocationpet/ajxbreedtypes') ]) !!}
                                        </div>
                                    </div>
                                   
                                    <div class="col-md-3 form-control-fld">
                                        <div class="input-prepend">
                                            <span class="add-on"><i class="fa fa-paw"></i></span>
                                            {!! Form::select('selBreedtype',(['' => 'Breed'] +$getAllBreedTypes), request('selBreedtype'), ['class' =>'selectpicker','id'=>'selBreedtype' ]) !!}                                            
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-2 form-control-fld">
                                        <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-chain"></i></span>
                                                {!! Form::select('selCageType',(['' => 'Cage Type'] +$getAllCageTypes), request('selCageType') ,['class' =>'selectpicker','id'=>'selCageType']) !!}
                                        </div>
                                    </div>                                    
                                    
                                    {!! Form::hidden('is_search',1,array('class'=>'form-control')) !!}
                                    
                            </div>