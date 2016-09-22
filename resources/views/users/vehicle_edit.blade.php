@extends('app') @section('content')
@include('partials.page_top_navigation') @if (Session::has('message') &&
Session::get('message')!='')
<div class="flash">
    <p class="text-success col-sm-12 text-center flash-txt alert-info">{{ Session::get('message') }}</p>
</div>
@endif


<div class="main-container">
    <div class="login-head heading-margin-top">
        <h1 class="margin-top margin-bottom-none">
            <span>LOGISTIKS.COM</span>
            <p>Update Vehicle Details</p>
        </h1>
    </div>
    <div class="main">
        <div class="container reg_crumb">


            <div class="home-block home-block-login">
                <div class="tabs">

                    <div class="tab-content">
                        <div id="buyer" class="tab-pane fade in active">
                            <div class="login-block">
                                <div class="login-form login-form-2">
                                    <div class="center-width">


                                        {!! Form::open(['url' =>
                                        'vehicle_update/'.$vehicles->id,'id'=>'vehicle-master-form','enctype'=>'multipart/form-data','class'=>'form-inline
                                        margin-top']) !!}

                                        <div class="clearform"></div>
                                        <h4>Vehicle form</h4>

                                        <div class="col-sm-6 form-control-fld">
                                            {!! Form::text('vehicle_number', $vehicles->vehicle_number
                                            ,['class'=>'form-control form-control1 alphanumeric_strVal','maxlength'=>20,
                                            'placeholder'=>'Vehicle Number *','maxlength'=>11]) !!}
                                            @if($errors->has('vehicle_number'))
                                            <p style="color: red;">{!! $errors->first('vehicle_number') !!}</p>
                                            @endif
                                        </div>
                                        <div class="col-sm-6 form-control-fld">
                                            <div class="normal-select">{!! Form::select('vehicle_type',array('' => 'Select Vehicle
                                                Type *') + $vehicle, $vehicles->lkp_vehicle_type_id
                                                ,['class'=>'form-control form-control1','id'=>'vehicle_type']) !!}
                                                @if($errors->has('vehicle_type'))
                                                <p style="color: red;">{!!$errors->first('vehicle_type')!!}</p>
                                                @endif
                                            </div>
                                        </div>

                                        
                                        <div class="col-sm-6 form-control-fld">
                                            {!!	Form::text('vehicle_capacity',$vehicles->vehicle_capacity, ['class'=>'form-control form-control1 clsVehicleno', 'placeholder'=>'Vehicle Capacity *','id'=>'vehicle_capacity']) !!}
                                            @if ($errors->has('vehicle_capacity'))
                                            <p style="color: red;">{!! $errors->first('vehicle_capacity') !!}</p>
                                            @endif
                                        </div>

                                        <div class="col-md-6 form-control-fld">
                                            <div class="normal-select">{!!
                                                Form::select('load_type',array('' => 'Select Load Type') +
                                                $load_type,$vehicles->lkp_load_type_id,['class'=>'selectpicker']) !!}</div>
                                        </div>

                                        <div class="col-md-12 form-control-fld">
                                            {{--*/ $dimensions = explode('*',
										$vehicles->vehicle_dimension) /*--}}
                                            <div class=" col-md-12 form-control-fld">{!!
                                                Form::label('vehicle_dimension', 'Dimension', array('class'
                                                => '')) !!}</div>

                                            <div class="col-md-4 form-control-fld">
                                                {!!
                                                Form::text('vehicle_length',$dimensions[2],['class'=>'form-control
                                                form-control1 threedigitstwodecimals_deciVal','placeholder'=>' L *','id'=>'dimen_length']) !!}
                                                @if($errors->has('vehicle_length'))
                                                <p style="color: red;">{!!$errors->first('vehicle_length')!!}</p>
                                                @endif
                                            </div>



                                            <div class="col-md-4 form-control-fld">
                                                {!!
                                                Form::text('vehicle_width',$dimensions[0],['class'=>'form-control
                                                form-control1 threedigitstwodecimals_deciVal', 'placeholder'=>' W *','id'=>'dimen_width']) !!} 
                                                @if($errors->has('vehicle_width'))
                                                <p style="color: red;">{!!$errors->first('vehicle_width')!!}</p>
                                                @endif
                                            </div>
                                            <div class="col-md-4 form-control-fld">
                                                {!!
                                                Form::text('vehicle_height',$dimensions[1],['class'=>'form-control
                                                form-control1 threedigitstwodecimals_deciVal ','placeholder'=>' H *','id'=>'dimen_height']) !!}
                                                @if($errors->has('vehicle_height'))
                                                <p style="color: red;">{!!$errors->first('vehicle_height')!!}</p>
                                                @endif
                                            </div>

                                        </div>


                                        <div class="col-md-12 form-control-fld">
                                            <div class="col-sm-6 form-control-fld">
                                                <div class="normal-select">
                                                    {!! Form::select('brand_id',array('' => 'Select Brand',) + $brands,$vehicles->brand_id, ['id'=>'brand_id','class'=>'selectpicker']) !!}</div>
                                                @if ($errors->has('brand_id'))
                                                <p style="color: red;">{!! $errors->first('brand_id') !!}</p>
                                                @endif
                                            </div>                                                                                

                                           
                                            <div class="col-sm-6 form-control-fld">
                                                <div class="normal-select">
                                                    {!! Form::select('model_id',array('' => 'Select Brand',) + $models,$vehicles->brand_id, ['id'=>'models_id','class'=>'selectpicker']) !!}</div>
                                                @if ($errors->has('model_id'))
                                                <p style="color: red;">{!! $errors->first('model_id') !!}</p>
                                                @endif
                                            </div>   
                                        </div>

                                        <div class="col-md-12 form-control-fld">
                                            <div class=" col-md-12 form-control-fld">{!!
                                                Form::label('reg_owner_fname', 'Registered Owner',
                                                array('class' => '')) !!}
                                            </div>
                                           
                                            <div class="col-md-6 form-control-fld">
                                                {!! Form::text('chasis_number', $vehicles->chasis_number
                                                ,['class'=>'form-control form-control1 alphanumeric_strVal',
                                                'placeholder'=>'Chassis Number *','maxlength'=>50]) !!}
                                                @if($errors->has('chasis_number'))
                                                <p style="color: red;">{!!$errors->first('chasis_number')!!}</p>
                                                @endif
                                            </div>

                                            <div class="col-md-6 form-control-fld">{!!
                                                Form::text('engine_number', $vehicles->engine_number
                                                ,['class'=>'form-control form-control1 clsEngineNumber',
                                                'placeholder'=>'Engine Number']) !!}
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-md-6 form-control-fld">
                                                {!! Form::text('pre_load_type',null,['class'=>'form-control
                                                form-control1 clsChassisNumber', 'placeholder'=>'Preferred Load Type','id'=>'chasis_number']) !!}
                                                @if($errors->has('pre_load_type'))
                                                <p style="color: red;" >{!! $errors->first('pre_load_type') !!}</p>
                                                @endif
                                                <p style="color: red;" id="error_chasis_number"></p>
                                            </div>

                                            <div class="col-md-6 form-control-fld">
                                                {!! Form::text('mfg_year', $vehicles->mfg_year ,
                                                ['class'=>'form-control form-control1 numericvalidation',
                                                'placeholder'=>'Manufactured On *','maxlength'=>4]) !!}
                                                @if($errors->has('mfg_year'))
                                                <p style="color: red;">{!!$errors->first('mfg_year')!!}</p>
                                                @endif
                                            </div>

                                            <div class="clearfix"></div>     
                                            <div class="col-md-6 form-control-fld">{!!
                                                Form::text('odo_meter_reading',$vehicles->odo_meter_reading,['class'=>'form-control
                                                form-control1 numericvalidation','placeholder'=>'ODO Meter Reading','maxlength' => 7]) !!}
                                            </div>

                                            <div class="col-md-6 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                    {!!
                                                    Form::text('date_of_reading',$vehicles->date_of_reading,['id'=>'date_of_reading','class'=>'form-control','placeholder'
                                                    => 'Date Of Reading']) !!}
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-6 form-control-fld">
                                            <div class="">
                                                <span class="pull-left" 
                                                      {!!
                                                      Form::label('is_gps', 'GPS Available', array('class' => ''))
                                                      !!}</span>
                                                <div class="pull-left">
                                                    <!--label> 
                                                    <?php //echo Form::radio('is_gps', '1', ($vehicles->is_gps == 1) ? true : false); ?> <span class="lbl padding-8"></span> Yes
                                                    </label> 
                                                    <label> 
                                                    <?php //echo Form::radio('is_gps', '0', ($vehicles->is_gps == 0) ? true : false); ?><span class="lbl padding-8"></span>  No
                                                    </label-->
                                                    <div class="radio-block">
                                                        <div class="radio_inline">
                                                            <input type="radio" name="is_gps" id="is_gps_yes" value="1"
                                                                   {!! ($vehicles->is_gps == 1) ? "checked" : "" !!} /> <label
                                                                   for="is_gps_yes"><span></span>Yes</label>
                                                        </div>
                                                        <div class="radio_inline">
                                                            <input type="radio" name="is_gps" id="is_gps_no" value="0"
                                                                   {!! ($vehicles->is_gps == 0) ? "checked" : "" !!} /> <label
                                                                   for="is_gps_no"><span></span>No</label>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        {{--*/ $displayNone = ''; /*--}}										
                                        @if($vehicles->is_gps==0) 
                                        {{--*/ $displayNone = 'displayNone'; /*--}}
                                        @endif
                                        <div class="col-md-12 form-control-fld margin-none padding-none {{$displayNone}}" id="GPSFields">
                                            <div class="col-md-6 form-control-fld">	
                                                {!!Form::text('device_number',$vehicles->device_number,['class'=>'form-control
                                                form-control1 numericvalidation', 'placeholder'=>'Device Number *']) !!}											
                                            </div>
                                            <div class="col-md-6 form-control-fld">	
                                                {!!Form::text('sim_imsi_number',$vehicles->sim_imsi_number,['class'=>'form-control
                                                form-control1 alphanumeric_strVal', 'placeholder'=>'Sim IMSI Number *']) !!}											
                                            </div>
                                            <div class="col-md-6 form-control-fld">	
                                                {!!Form::text('mobile_operator',$vehicles->mobile_operator,['class'=>'form-control
                                                form-control1 alphaonly_strVal', 'placeholder'=>'Mobile Operator *']) !!}											
                                            </div>
                                            <div class="col-md-6 form-control-fld">	
                                                {!!Form::text('mobile_number',$vehicles->mobile_number,['class'=>'form-control
                                                form-control1 numericvalidation', 'placeholder'=>'Mobile Number *','maxlength'=>10]) !!}											
                                            </div>
                                            <div class="col-md-6 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                    {!!
                                                    Form::text('device_fixed_date',date("d/m/Y",
                                                    strtotime($vehicles->device_fixed_date)),['class'=>'form-control','placeholder'
                                                    => 'Date of device fixed in vehicle *']) !!}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div> 
                                        <div class="col-md-6 form-control-fld">{!!
                                            Form::text('ins_policy_no',$vehicles->ins_policy_no,['class'=>'form-control
                                            form-control1 numericvalidation','placeholder'=>'Insurance Policy No','maxlength' => 9]) !!}</div>


                                        <div class="col-md-6 form-control-fld">
                                            {!! Form::text('ins_company',$vehicles->ins_company,['class'=>'form-control
                                            form-control1 clsAlphaSpace','placeholder'=>'Insurance Company','maxlength' => 50]) !!} 
                                            @if($errors->has('ins_company'))
                                            <p style="color: red;">{!! $errors->first('ins_company') !!}</p>
                                            @endif
                                        </div>       


                                        <div class="clearfix"></div>
                                        <div class="col-md-6 form-control-fld">
                                            <div class=" col-md-12 form-control-fld">
                                                @if ($vehicles->insurance_validity != '' &&	$vehicles->insurance_validity != '1970-01-01')
                                                <div class="col-md-12 form-control-fld padding-none">
                                                    <div class="input-prepend">
                                                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                        {!!
                                                        Form::text('insurance_validity',date("d/m/Y",
                                                        strtotime($vehicles->insurance_validity)),['class'=>'form-control','placeholder'
                                                        => 'Insurance Validity','id'=>'insurance_validity']) !!}
                                                    </div>
                                                </div>
                                                @else
                                                <div class="clearfix"></div>
                                                <div class="col-md-12 form-control-fld padding-none">
                                                    <div class="input-prepend">
                                                        <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                        {!!
                                                        Form::text('insurance_validity','',['class'=>'form-control',
                                                        'placeholder' => 'Insurance Validity']) !!}
                                                    </div>
                                                </div>
                                                @endif

                                            </div>
                                        </div>

                                        <div class="clearfix"></div>

                                        <div class="col-md-6 form-control-fld">
                                            <!--div class=" col-md-12 form-control-fld">
{!! Form::label('permit_type', 'Permit Type', array('class' => '')) !!}
</div-->
                                            <div class="col-md-12 form-control-fld">
                                                <div class="normal-select">
                                                    {!! Form::select('permit_type',array('' => 'Select permit Type ') + $permittypes,$vehicles->permit_type, ['class'=>'selectpicker']) !!}
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-6 form-control-fld">
                                            <!--div class=" col-md-12 form-control-fld">
{!! Form::label('fc_validity', 'FC Validity', array('class' => '')) !!}
</div-->
                                            <div class="col-md-12 form-control-fld">
                                                @if ($vehicles->fc_validity != '' && $vehicles->fc_validity
                                                != '1970-01-01')
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                    {!! Form::text('fc_validity',date("d/m/Y",
                                                    strtotime($vehicles->fc_validity)),['class'=>'form-control',
                                                    'placeholder' => 'FC Validity']) !!}
                                                </div>
                                                @else
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                    {!! Form::text('fc_validity','',['class'=>'form-control',
                                                    'placeholder' => 'FC Validity']) !!}
                                                </div>
                                                @endif
                                            </div>

                                        </div>



                                        <div class="col-md-12 form-control-fld">

                                            <div class="col-sm-3 padding-none">
                                                <div id="insurance_file_name" class="text-break">

                                                    @if ($errors->has('insurance_file_name'))
                                                    <p style="color: red;">{!!$errors->first('insurance_file_name')!!}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-3 padding-right-none">
                                                <div id="permit_copy_file_name" class="text-break">

                                                    @if ($errors->has('permit_copy_file_name'))
                                                    <p style="color: red;">{!!$errors->first('permit_copy_file_name')!!}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-3 padding-right-none">
                                                <div id="fc_file_name" class="text-break">

                                                    @if ($errors->has('fc_file_name'))
                                                    <p style="color: red;">{!!$errors->first('fc_file_name')!!}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-sm-3 padding-none">
                                                <div id="rc_file_name" class="text-break">

                                                    @if ($errors->has('rc_file_name'))
                                                    <p style="color: red;">{!!$errors->first('rc_file_name')!!}</p>
                                                    @endif
                                                </div>
                                            </div>

                                        </div>



                                        <div class="col-md-12 form-control-fld">
                                            <div class="col-sm-8 padding-none">
                                                <input type="checkbox" id="cdbaccept" checked="checked"
                                                       name="cdbaccept"> <span class="lbl padding-8"></span> Accept Term &amp; Conditions (Digital Contract) &nbsp; &nbsp;
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-sm-4 padding-none">{!! Form::submit('Confirm &
                                                Register', ['class' => 'btn register_submit post-btn
                                                margin-top']) !!}</div>


                                        </div>
                                        {!! Form::close() !!}

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<!-- Page Center Content Ends Here -->
<!-- Right Starts Here -->

<script type="text/javascript">
    $('#brand_id').on("change", function () {
        var brand = $(this).val();
        // alert(brand);
        var _options = " ";
        $.ajax
                (
                        {
                            url: '/getmodeldetails',
                            type: "GET",
                            data: "brand=" + brand,
                            dataType: 'json',
                            async: false,
                            cache: false,
                            dataType: 'text',
                                    success: function (result)
                                    {
                                        $("#models_id").html(result);
                                        $('.selectpicker').selectpicker('refresh');
                                    },
                            error: function ()
                            {
                                //console.log("AJAX request was a failure");
                            }
                        }
                );

    });


    $('#vehicle_type').on("change", function () {
        var vehicle = $(this).val();
       // alert(vehicle);
        $.ajax
                (
                        {
                            url: '/getvehicledimens',
                            type: "GET",
                            data: "vehicle=" + vehicle,
                            //dataType:'json',
//            async:false,
//            cache:false,
                            success: function (result)
                            {   var dimen = result[0].dimension;
                                var capa = result[0].capacity + " " + result[0].units;
                                var res = dimen.split("x");
                                $("#dimen_length").val(res[0]);
                                $("#dimen_width").val(res[1]);
                                $("#dimen_height").val(res[2]);                                
                                
                                $("#vehicle_capacity").val(capa);
                                },
                            error: function ()
                            {
                                //console.log("AJAX request was a failure");
                            }
                        }
                );
    });

</script>
@include('partials.footer')
<!-- Right Ends Here -->
</div>
@endsection