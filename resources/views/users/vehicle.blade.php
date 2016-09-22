@extends('app') @section('content')
@include('partials.page_top_navigation') @if (Session::has('message') &&
Session::get('message')!='')
<div class="flash">
    <p class="text-success col-sm-12 text-center flash-txt alert-info">{{
		Session::get('message') }}</p>
</div>
@endif


<div class="main-container">
    <div class="login-head heading-margin-top">
        <h1 class="margin-top margin-bottom-none">
            <span>LOGISTIKS.COM</span>
            <p>Vehicle Registration</p>
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
                                        'vehicleregister','id'=>'vehicle-master-form','enctype'=>'multipart/form-data','class'=>'form-inline
                                        margin-top']) !!}

                                    
                                        <div class="clearform"></div>
                                        <h4>Vehicle form</h4>

                                        <div class="col-sm-6 form-control-fld">
                                            {!!	Form::text('vehicle_number',null, ['class'=>'form-control form-control1 clsVehicleno','id'=>'vehicleid', 'placeholder'=>'Vehicle Number *']) !!}
                                            @if ($errors->has('vehicle_number'))
                                            <p style="color: red;">{!! $errors->first('vehicle_number') !!}</p>
                                            @endif
                                        </div>
                                        <div class="col-sm-6 form-control-fld">
                                            <div class="normal-select">
                                                {!! Form::select('vehicle_type',array('' => 'Select Vehicle Type*') + $vehicle,null, ['class'=>'selectpicker','id'=>'vehicle_type']) !!}</div>
                                            @if ($errors->has('vehicle_type'))
                                            <p style="color: red;">{!! $errors->first('vehicle_type') !!}</p>
                                            @endif
                                        </div>

                                       
                                           <div class="col-sm-6 form-control-fld">
                                            {!!	Form::text('vehicle_capacity',null, ['class'=>'form-control form-control1 clsVehicleno', 'placeholder'=>'Vehicle Capacity *','id'=>'vehicle_capacity']) !!}
                                            @if ($errors->has('vehicle_capacity'))
                                            <p style="color: red;">{!! $errors->first('vehicle_capacity') !!}</p>
                                            @endif
                                        </div>

                                        <div class="col-md-6 form-control-fld">
                                            <div class="normal-select">{!!
                                                Form::select('load_type',array('' => 'Select Load Type') +
                                                $load_type,null,['class'=>'selectpicker']) !!}</div>
                                        </div>


                                        <div class="col-md-12 form-control-fld">
                                            <div class=" col-md-12 form-control-fld">
                                                {!! Form::label('vehicle_dimension', 'Dimension', array('class' => '')) !!}
                                            </div>

                                            <div class="col-md-4 form-control-fld">
                                                {!! Form::text('vehicle_length',null,['class'=>'form-control
                                                form-control1 clsLTL4LengthCM','id'=>'dimen_length','placeholder'=>' L *']) !!} 
                                                @if($errors->has('vehicle_length'))
                                                <p style="color: red;">{!! $errors->first('vehicle_length') !!}</p>
                                                @endif
                                            </div>

                                            <div class="col-md-4 form-control-fld">
                                                {!! Form::text('vehicle_width',null,['class'=>'form-control
                                                form-control1 clsLTL4LengthCM','id'=>'dimen_width','placeholder'=>' W *']) !!} 
                                                @if ($errors->has('vehicle_width'))
                                                <p style="color: red;">{!! $errors->first('vehicle_width') !!}</p>
                                                @endif
                                            </div>

                                            <div class="col-md-4 form-control-fld">
                                                {!! Form::text('vehicle_height',null,['class'=>'form-control
                                                form-control1 clsLTL4LengthCM','id'=>'dimen_height','placeholder'=>' H *']) !!} 
                                                @if($errors->has('vehicle_height'))
                                                <p style="color: red;">{!! $errors->first('vehicle_height') !!}</p>
                                                @endif
                                            </div>
                                        </div>                        

                                        <div class="col-sm-6 form-control-fld">
                                            <div class="normal-select">
                                                {!! Form::select('brand_id',array('' => 'Select Brand') + $brands,null, ['id'=>'brand_id','class'=>'selectpicker']) !!}</div>
                                            @if ($errors->has('brand_id'))
                                            <p style="color: red;">{!! $errors->first('brand_id') !!}</p>
                                            @endif
                                        </div>                                                                                

                                        <div class="col-sm-6 form-control-fld">                      
                                            <div class="normal-select" id="dynamicOptions">
                                                <select name="model_id" id="models_id" class="selectpicker">
                                                    <option value=""> Select Model</option>
                                                </select>                                                                                    

                                                @if ($errors->has('model_id'))
                                                <p style="color: red;">{!! $errors->first('model_id') !!}</p>
                                                @endif
                                            </div>
                                        </div>       

                                        <div class="col-md-6 form-control-fld">
                                            {!! Form::text('chasis_number',null,['class'=>'form-control
                                            form-control1 clsChassisNumber', 'placeholder'=>'Chassis Number *','id'=>'chasis_number','maxlength'=>17]) !!}
                                            @if($errors->has('chasis_number'))
                                            <p style="color: red;" >{!! $errors->first('chasis_number') !!}</p>
                                            @endif
                                            <p style="color: red;" id="error_chasis_number"></p>
                                        </div>

                                        <div class="col-md-6 form-control-fld">{!!
                                            Form::text('engine_number',null,['class'=>'form-control
                                            form-control1 clsEngineNumber', 'placeholder'=>'Engine Number','id'=>'engine_number','maxlength'=>12]) !!}
                                            <p style="color: red;" id="error_engine_number"></p>
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
                                             {!! Form::text('mfg_year',null,['class'=>'form-control
                                             form-control1 numericvalidation','placeholder'=>'Year of Manufacture *','maxlength'=>4]) !!}
                                             @if($errors->has('mfg_year'))
                                             <p style="color: red;">{!! $errors->first('mfg_year') !!}</p>
                                             @endif

                                         </div>
                                        
                                        <div class="clearfix"></div>     
                                        <div class="col-md-6 form-control-fld">{!!
                                            Form::text('odo_meter_reading',null,['class'=>'form-control
                                            form-control1 numericvalidation','placeholder'=>'ODO Meter Reading','maxlength' => 7]) !!}</div>

                                        <div class="col-md-6 form-control-fld">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                {!!
                                                Form::text('date_of_reading',null,['id'=>'date_of_reading','class'=>'form-control','placeholder'
                                                => 'Date Of Reading']) !!}
                                            </div>
                                        </div>

                                        <div class="col-md-12 form-control-fld">
                                            <div class="padding-top-8">
                                                <span class="pull-left margin-right-20">
                                                    {!!
                                                    Form::label('is_gps', 'GPS Available', array('class' => ''))
                                                    !!}
                                                </span>

                                                <div class="pull-left">
                                                    <div class="radio_inline">
                                                        <input type="radio" name="is_gps" id="spot_lead_type"
                                                               value="1" /> <label for="spot_lead_type"><span></span>Yes</label>
                                                    </div>
                                                    <div class="radio_inline">
                                                        <input type="radio" name="is_gps" id="term_lead_radio"
                                                               value="0" checked /> <label for="term_lead_radio"><span></span>No</label>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="col-md-12 form-control-fld margin-none padding-none displayNone" id="GPSFields">
                                            <div class="col-md-6 form-control-fld">	
                                                {!!Form::text('device_number',null,['class'=>'form-control
                                                form-control1 numericvalidation', 'placeholder'=>'Device Number *']) !!}											
                                            </div>
                                            <div class="col-md-6 form-control-fld">	
                                                {!!Form::text('sim_imsi_number',null,['class'=>'form-control
                                                form-control1 alphanumeric_strVal', 'placeholder'=>'Sim IMSI Number *']) !!}											
                                            </div>
                                            <div class="col-md-6 form-control-fld">	
                                                {!!Form::text('mobile_operator',null,['class'=>'form-control
                                                form-control1 alphaonly_strVal', 'placeholder'=>'Mobile Operator *']) !!}											
                                            </div>
                                            <div class="col-md-6 form-control-fld">	
                                                {!!Form::text('mobile_number',null,['class'=>'form-control
                                                form-control1 clsMobile', 'placeholder'=>'Mobile Number *']) !!}											
                                            </div>
                                            <div class="col-md-6 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                    {!!
                                                    Form::text('device_fixed_date',null,['class'=>'form-control','placeholder'
                                                    => 'Device Fixed Date']) !!}
                                                </div>
                                            </div>
                                        </div>


                                        <div class="col-md-6 form-control-fld">{!!
                                            Form::text('ins_policy_no',null,['class'=>'form-control
                                            form-control1 numericvalidation','placeholder'=>'Insurance Policy No','maxlength' => 9]) !!}</div>


                                        <div class="col-md-6 form-control-fld">
                                            {!! Form::text('ins_company',null,['class'=>'form-control
                                            form-control1 clsAlphaSpace','placeholder'=>'Insurance Company','maxlength' => 50]) !!} 
                                            @if($errors->has('ins_company'))
                                            <p style="color: red;">{!! $errors->first('ins_company') !!}</p>
                                            @endif
                                        </div>

                                        <div class="col-md-6 form-control-fld padding-none">
                                            <div class="input-prepend">
                                                <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                {!!
                                                Form::text('insurance_validity',null,['class'=>'form-control','placeholder'
                                                => 'Insurance Validity','id'=>'insurance_validity'])!!}
                                            </div>
                                        </div>                   
 <div class="clearfix"></div>

                                        <div class="col-md-6 form-control-fld">
                                            <!--div class=" col-md-12 form-control-fld">
                                            {!! Form::label('permit_type', 'Permit Type', array('class' => '')) !!}
                                            </div-->
                                            <div class="normal-select">
                                                {!! Form::select('permit_type',array('' => 'Select permit Type ') + $permittypes,null, ['class'=>'selectpicker']) !!}
                                            </div>
                                        </div>
 
       <div class="col-md-6 form-control-fld">
                                            <!--div class=" col-md-12 form-control-fld">
{!! Form::label('fc_validity', 'Fc Validity', array('class' => '')) !!}
</div-->
                                            <div class="col-md-12 form-control-fld">
                                                <div class="input-prepend">
                                                    <span class="add-on"><i class="fa fa-calendar-o"></i></span>
                                                    {!!
                                                    Form::text('fc_validity',null,['class'=>'form-control','placeholder'
                                                    => 'FC Validity']) !!}
                                                </div>
                                            </div>

                                        </div>
 

                                        <div class="clearfix"></div>

                                        <div class="col-md-12 form-control-fld">
                                            <div class="col-md-6 form-control-fld">
                                                <div id="insurance_file_name" class="text-break">

                                                    @if ($errors->has('insurance_file_name'))
                                                    <p style="color: red;">{!!$errors->first('insurance_file_name')!!}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-control-fld">
                                                <div id="permit_copy_file_name" class="text-break">

                                                    @if ($errors->has('permit_copy_file_name'))
                                                    <p style="color: red;">{!!$errors->first('permit_copy_file_name')!!}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="col-md-6 form-control-fld">
                                                <div id="fc_file_name" class="text-break">

                                                    @if ($errors->has('fc_file_name'))
                                                    <p style="color: red;">{!!$errors->first('fc_file_name')!!}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class=" col-md-6 form-control-fld">
                                                <div id="rc_file_name" class="text-break">

                                                    @if ($errors->has('rc_file_name'))
                                                    <p style="color: red;">{!!$errors->first('rc_file_name')!!}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-12 form-control-fld">
                                            <div class="col-sm-8 padding-none">
                                                <input type="checkbox" id="cdbaccept" name="cdbaccept"> <span
                                                    class="lbl padding-8"></span> Accept Term &amp; Conditions
                                                (Digital Contract) &nbsp; &nbsp;
                                            </div>
                                            <div class="clearfix"></div>
                                            <div class="col-sm-4 padding-none">{!! Form::submit('Confirm &
                                                Register', ['class' => 'btn register_submit post-btn
                                                margin-top']) !!}</div>


                                        </div>

                                        {!! Form::close() !!}
                                    </div>
                                    <div class="clearfix"></div>
                    
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
<script>
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
        //alert(vehicle);
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
                            {
                    
                                 var dimen = result[0].dimension;  
                                 var capa=result[0].capacity+" "+result[0].units;
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
<!-- Page Center Content Ends Here -->
<!-- Right Starts Here -->
@include('partials.footer')
<!-- Right Ends Here -->

</div>
@endsection