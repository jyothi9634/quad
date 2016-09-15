@if(isset($tracking) && $tracking==1)
    @if($trackingExist==0 && !empty($pickExist) && $order->gsa_accepted==1)
        <div class="accordian-blocks">
            <div class="inner-block-bg inner-block-bg1 detail-head">
                <h2 class="filter-head1">Transit Details</h2>
            </div>
            <div class="detail-data">
                <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                    <div class="col-md-12 padding-none">
                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-tracklocation']) !!}
                        <input type='hidden' id='cpick' value="{{date("d/m/Y", strtotime($order->seller_pickup_date))}}">
                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-map-marker"></i></span>
                                {!! Form::text('location', '', ['id' => 'location','class'=>"form-control alphanumericspace_strVal",'required'=>'required', 'placeholder' => 'Location*']) !!}
                            </div>
                        </div>
                        <div class="col-md-3 form-control-fld">
                            <div class="input-prepend">
                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                                {!! Form::text('date', '', ['id' => 'date','class'=>"form-control calendar", 'placeholder' => 'Date*','readonly' => true]) !!}
                            </div>
                        </div>

                        <div class="col-md-6 form-control-fld text-right">
                            <input type="submit" class="btn add-btn" value="Add">
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
                
                <div class="table-div table-style1">
                    <div class="table-heading inner-block-bg">
                        <div class="col-md-4 padding-left-none">Location</div>
                        <div class="col-md-8 padding-left-none">Date</div>
                    </div>
                    <div class="table-data">
                        <div id="track_locations">
                            @foreach($locations as $loc)
                                <div class="table-row inner-block-bg">
                                    <div class="col-md-4 padding-left-none">{{$loc->tracking_location}}</div>
                                    <div class="col-md-4 padding-left-none loc_date">{{date("d/m/Y", strtotime($loc->tracking_date))}}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-md-12 text-right btn-block">
                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-track']) !!}
                                <!--button class="btn add-btn flat-btn">Confirm</button-->
                        <input type="hidden" name="tracking_confirm" value="1">
                        <input type="submit" class="btn add-btn flat-btn" id="track_confirm" value="Confirm">
                        {!! Form::close() !!}
                    </div>
                </div>

            </div>
        </div>
    @endif

    @if($trackingExist==1 && !empty($pickExist) && $order->gsa_accepted==1)
        <div class="accordian-blocks">
            <div class="inner-block-bg inner-block-bg1 detail-head">
                <h2 class="filter-head1">Transit Details</h2>
            </div>

            <div class="detail-data">
                <div>&nbsp;</div>
                
                <div class="table-div table-style1">
                    <div class="table-heading inner-block-bg">
                        <div class="col-md-4 padding-left-none">Location</div>
                        <div class="col-md-8 padding-left-none">Date</div>
                    </div>
                    <div class="table-data">
                        <div id="track_locations">
                            @foreach($locations as $loc)
                                <div class="table-row inner-block-bg">
                                    <div class="col-md-4 padding-left-none">{{$loc->tracking_location}}</div>
                                    <div class="col-md-4 padding-left-none loc_date">{{date("d/m/Y", strtotime($loc->tracking_date))}}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

@endif