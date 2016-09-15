{{--*/ $to_loaction=''; /*--}}
{{--*/ $to_loaction_id=''; /*--}}
{{--*/ $dispatch_date=''; /*--}}

@if(Session::has('searchMod.dispatch_date_buyer'))

  {{--*/ $dispatch_date=Session::get('searchMod.dispatch_date_buyer'); /*--}}
  {{--*/ $to_loaction_id=Session::get('searchMod.to_city_id_buyer'); /*--}}
  {{--*/ $to_loaction=Session::get('searchMod.to_location_buyer'); /*--}}
  
  @endif
<div class="col-md-12 inner-block-bg inner-block-bg1">
    <div class="col-md-3 form-control-fld">
            <div class="input-prepend">
                    <span class="add-on"><i class="fa fa-map-marker"></i></span>

                    {!! Form::text('to_location', $to_loaction, ['id' => 'to_location','class' => 'form-control', 'placeholder' => 'City *']) !!}

                    {!! Form::hidden('to_location_id', $to_loaction_id, array('id' => 'to_location_id')) !!}

            </div>
    </div>

    <div class="col-md-3 form-control-fld">
            <div class="input-prepend">
                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                    <input class="form-control" id="dispatch_date" name="dispatch_date" type="text" placeholder="Date *" value="{{$dispatch_date}}">
            </div>
    </div>	
</div>

@include('relocationglobal.buyers.buyer_services')		

<div class="col-md-12 inner-block-bg inner-block-bg1">
        <div class="col-md-12 form-control-fld margin-none">
                <div class="radio-block">
                        <input type="radio" name="ptlQuoteaccessId" value="1" id="post-public" checked="checked" class="create-posttype-service crete-relocation" />
                        <label for="post-public"><span></span>Post Public</label>

                        <input type="radio" name="ptlQuoteaccessId" value="2" id="post-private" class="create-posttype-service crete-relocation" />
                        <label for="post-private"><span></span>Post Private</label>
                </div>
        </div>


        <div class="col-md-3 form-control-fld" id="hideseller" style="display:none;">
                <input type="text" id="demo-input-local" class="form-control form-control1" name="seller_list" />
        </div>

        <div class="clearfix"></div>
        <div class="check-box form-control-fld">
        {!! Form::checkbox('agree', '', '',array('id'=>'agree')) !!} <span class="lbl padding-8">Accept Terms &amp; Conditions (Digital Contract)</span></div>
</div>

<div class="clearfix"></div>

<div class="container">
        <div class="col-md-4 col-md-offset-4">
                <input  type="submit" class="btn theme-btn btn-block"  name="getquote" id="getquote" value="Get Quote">
        </div>
</div>