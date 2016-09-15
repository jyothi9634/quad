@extends('app')
@section('content')
@inject('common', 'App\Components\CommonComponent')
 <div class="main-container">
     <div class="container container-inner">
         @if(Session::has('message')  && Session::get('message')!='') <div class="alert alert-info"> {{Session::get('message')}} </div> @endif
         <!-- Left Nav Starts Here -->
            @include('partials.leftnav')
         <!-- Left Nav Ends Here -->
         <div class="col-lg-7 col-md-6 col-sm-12 col-xs-12 main-middle">

             <div class="block">
                 @if(isset($cart_items_count) && ($cart_items_count > 0))
                     <div class="col-md-12 col-sm-12 col-xs-12 padding-none margin-top">
							
                         {!! Form::open(['url' => 'confirmpayment','id'=>'checkout-form-lines']) !!}
                         <div class="col-md-12 col-sm-12 col-xs-12 padding-none border-bottom">
                             <h5>Step 2</h5>
                             

                             <div class="col-md-3 col-sm-3 col-xs-6 padding-top">Freight Amount Rs. </div>
                             <div class="col-md-6 col-sm-6 col-xs-6 padding-top">: {{$order_total}}</div>
                             <div class="clearfix"></div>

                             <div class="col-md-3 col-sm-3 col-xs-6 padding-top">Insurance Rs.</div>
                             <div class="col-md-6 col-sm-6 col-xs-6 padding-top">: 0.00</div>
                             <div class="clearfix"></div>
                             
                            @if(SHOW_SERVICE_TAX)
                                <div class="col-md-3 col-sm-3 col-xs-6 padding-top">Service Tax Rs.</div>
                                <div class="col-md-6 col-sm-6 col-xs-6 padding-top">: {{ $common->moneyFormat($orderServiceTax->order_service_tax_amount) }}</div>
                                <div class="clearfix"></div>
                            @endif

                             <div class="col-md-3 col-sm-3 col-xs-6 padding-top">Total Amount</div>
                             <div class="col-md-6 col-sm-6 col-xs-6 padding-top">: {{ $common->moneyFormat($orderServiceTax->order_total_amount) }}</div>
                             <div class="clearfix"></div>
                             
                            <br>
                            @if(!SHOW_SERVICE_TAX)
                            <span class="small serviceTax">(* Service Tax not included )</span>
                            @endif

                             <div class="col-md-12 col-sm-12 col-xs-12 padding-top">
                                 {!! Form::checkbox('agree_payment', 1, false,array('id'=>'agree_payment')) !!} Accept Terms & Conditions (Digital Contract)
                             </div>

                             <div class="clearfix"></div>
                             <div class="col-md-12 col-sm-12 col-xs-12 padding-top margin-bottom">
                                 {!! Form::hidden('total_amount_paid', $orderServiceTax->order_total_amount , array('id' => 'total_amount_paid')) !!}
                                 <input type="submit" value="Confirm Payment" class="btn">
                             </div>

                             <div class="clearfix"></div>



                         </div>
										
									</div>
							{!! Form::close() !!}	
                 @endif
             </div>
         </div>
         <!-- Right Starts Here -->
            @include('partials.right')
         <!-- Right Ends Here -->


 </div>
 </div>
@endsection