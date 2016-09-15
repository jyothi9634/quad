@extends('app')
@section('content')
@inject('common', 'App\Components\CommonComponent')

        <!-- Page top navigation Starts Here-->
@include('partials.page_top_navigation')


<div class="main">
    <div class="container">
        @if(Session::has('message') && Session::get('message')!='') <div class="alert alert-info"> {{Session::get('message')}} </div> @endif
        <span class="pull-left"><h1 class="page-title">Make Payment</h1></span>
        <div class="clearfix"></div>
        <div class="col-md-12 padding-none">
            <div class="main-inner">
                <!-- Right Section Starts Here -->
                @if(isset($cart_items_count) && ($cart_items_count > 0))
                    {!! Form::open(['url' => 'payment','id'=>'checkout-form-lines']) !!}
                    <div class="main-right">

                        <!-- Table Starts Here -->

                        <div class="table-div table-style1 padding-none">

                            <!-- Table Head Starts Here -->

                            <div class="table-heading inner-block-bg">
                                <div class="col-md-2 padding-left-none">Vendor Name</div>
                                @if(Session::get('service_id') == RELOCATION_OFFICE_MOVE || Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY)
								<div class="col-md-3 padding-left-none">City</div>
                                @else
                                <div class="col-md-2 padding-left-none">From</div>
                                @endif
                                @if(Session::get('service_id') != RELOCATION_OFFICE_MOVE &&  Session::get('service_id') != RELOCATION_GLOBAL_MOBILITY)
                                <div class="col-md-2 padding-left-none">To</div>
                                @endif
                                <div class="col-md-2 padding-left-none">Dispatch Date</div>
                                <div class="col-md-2 padding-left-none">Service</div>
                                <div class="col-md-1 padding-left-none">Price</div>
                            </div>

                            <!-- Table Head Ends Here -->

                            <div class="table-data">


                                <!-- Table Row Starts Here -->
                                @foreach($cart_items as $key=>$cart_item)
                                    <div class="table-row inner-block-bg">
                                        <div class="col-md-2 padding-left-none">{!! $cart_item->username !!}</div>
                                        @if(Session::get('service_id') != RELOCATION_GLOBAL_MOBILITY)
                                         @if(Session::get('service_id') == RELOCATION_OFFICE_MOVE)
		                                <div class="col-md-3 padding-left-none">{!! $cart_item->from_location !!}</div>
		                                @else
		                                <div class="col-md-2 padding-left-none">{!! $cart_item->from_location !!}</div>
		                                @endif
                                        @endif        
                                        @if(Session::get('service_id') != ROAD_TRUCK_LEASE && Session::get('service_id') != RELOCATION_OFFICE_MOVE)
                                        @if(Session::get('service_id') == RELOCATION_GLOBAL_MOBILITY)
                                        <div class="col-md-3 padding-left-none">{!! $cart_item->to_location !!}</div>
                                        @else
                                        <div class="col-md-2 padding-left-none">{!! $cart_item->to_location !!}</div>
                                        @endif
                                        @elseif(Session::get('service_id') != RELOCATION_OFFICE_MOVE)
                                         <div class="col-md-2 padding-left-none">N/A</div>
                                        @endif
                                        <div class="col-md-2 padding-left-none">{!! $common->checkAndGetDate($cart_item->dispatch_date) !!}</div>
                                        <div class="col-md-2 padding-left-none">{!! $cart_item->service_name !!}</div>
                                        <div class="col-md-1 padding-none">{!! $common->moneyFormat($cart_item->price) !!}/-</div>
                                    </div>
                                 @endforeach
                                            <!-- Table Row Ends Here -->

                            </div>

                            <div class="table-heading inner-block-bg total-payment">
                                <div class="col-md-12 padding-none text-right">
                                    Freight Amount <span class="big-value1"> Rs. {{$order_total}} /-</span>
                                </div>
                                <div class="col-md-12 padding-none text-right">
                                    Insurance <span class="big-value1"> Rs. 0.00 /-</span>
                                </div>
                                @if(SHOW_SERVICE_TAX)
                                <div class="col-md-12 padding-none text-right">
                                    Service Tax <span class="big-value1"> Rs. {{ $common->moneyFormat($orderServiceTax->order_service_tax_amount) }} /-</span>
                                </div>
                                @endif
                                <div class="col-md-12 padding-none text-right">
                                    Amount Payable <span class="big-value"> Rs. {{$common->moneyFormat($orderServiceTax->order_total_amount) }} /-
                                    
                                    <br>
                                    @if(!SHOW_SERVICE_TAX)
                                    <span class="small serviceTax">(* Service Tax not included )</span>
                                    @endif
                                        
                                    </span>
                                    
                                    {!! Form::hidden('lkp_payment_mode_id', $checkout_methods->lkp_payment_mode_id , array('id' => 'lkp_payment_mode_id')) !!}
                                    {!! Form::hidden('total_amount_paid', $orderServiceTax->order_total_amount , array('id' => 'total_amount_paid')) !!}
                                </div>
                            </div>
                        </div>

                        <!-- Table Starts Here -->
                        <h3 class="payment-head">Choose Payment Mode</h3>

                        <div class="payment-tabs">

                            <div class="tab-head">

                                <ul class="" role="tablist">
                                 {{--*/  $tab_active = ''  /*--}}
                                 {{--*/  $pay=1; $payMethod=''  /*--}}

                                  @if($is_contract == IS_CONTRACT || (isset($checkout_methods->lkp_payment_mode_id) && $checkout_methods->lkp_payment_mode_id == CREDIT && $is_contract == NOT_CONTRACT))
                                        @if($tab_active == '')
                                            {{--*/  $tab_active = 'active'  /*--}}
                                        @elseif($tab_active == 'active' || $tab_active == 'alreadyset')
                                        {{--*/  $tab_active = 'alreadyset'  /*--}}
                                        @else
                                            {{--*/  $tab_active = ''  /*--}}
                                        @endif
                                        @if($payMethod=='')
                                            {{--*/  $payMethod = 'C';  /*--}}
                                        @endif
                                        <li class="{!! $tab_active !!} paymentMode" data-value="C">
                                            <a href="#payment-type-6" role="tab" data-toggle="tab">
                                                <i class="fa fa-credit-card"></i> Credit
                                            </a>
                                        </li>
                                    @endif                                    
                                    @if((isset($checkout_methods->lkp_payment_mode_id) && isset($checkout_methods->accept_payment_credit) 
                                    && $checkout_methods->lkp_payment_mode_id == ADVANCED && $checkout_methods->accept_payment_credit == 1 
                                    && $is_contract == NOT_CONTRACT))
                                        @if($tab_active == '')
                                            {{--*/  $tab_active = 'active'  /*--}}
                                        @elseif($tab_active == 'active' || $tab_active == 'alreadyset')
                                        {{--*/  $tab_active = 'alreadyset';  /*--}}
                                        @else
                                            {{--*/  $tab_active = ''  /*--}}
                                        @endif
                                        @if($payMethod=='')
                                            {{--*/  $payMethod = 'CC';  /*--}}
                                        @endif
                                        <li class="{!! $tab_active !!} paymentMode" data-value="CC">
                                            <a href="#payment-type-1" role="tab" data-toggle="tab">
                                                <i class="fa fa-credit-card"></i> Credit Card
                                            </a>
                                        </li>
                                    @endif                                    
                                    @if((isset($checkout_methods->lkp_payment_mode_id) && isset($checkout_methods->accept_payment_debit) 
                                    && $checkout_methods->lkp_payment_mode_id == ADVANCED && $checkout_methods->accept_payment_debit == 1 
                                    && $is_contract == NOT_CONTRACT))
                                        @if($tab_active == '')
                                            {{--*/  $tab_active = 'active'  /*--}}
                                        @elseif($tab_active == 'active' || $tab_active == 'alreadyset')
                                        {{--*/  $tab_active = 'alreadyset'  /*--}}
                                        @else
                                            {{--*/  $tab_active = ''  /*--}}
                                        @endif
                                        @if($payMethod=='')
                                            {{--*/  $payMethod = 'DB';  /*--}}
                                        @endif
                                        <li class="{!! $tab_active !!} paymentMode" data-value="DB">
                                            <a href="#payment-type-2" role="tab" data-toggle="tab">
                                                <i class="fa fa-credit-card"></i> Debit Card
                                            </a>
                                        </li>
                                    @endif                                   
                                    @if((isset($checkout_methods->lkp_payment_mode_id) && isset($checkout_methods->accept_payment_netbanking)
                                    && $checkout_methods->lkp_payment_mode_id == ADVANCED && $checkout_methods->accept_payment_netbanking == 1 
                                    && $is_contract == NOT_CONTRACT))
                                        @if($tab_active == '')
                                            {{--*/  $tab_active = 'active'  /*--}}
                                        @elseif($tab_active == 'active' || $tab_active == 'alreadyset')
                                        {{--*/  $tab_active = 'alreadyset'  /*--}}
                                        @else
                                            {{--*/  $tab_active = ''  /*--}}
                                        @endif
                                        @if($payMethod=='')
                                            {{--*/  $payMethod = 'NB';  /*--}}
                                        @endif
                                        <li class="{!! $tab_active !!} paymentMode" data-value="NB">
                                            <a href="#payment-type-3" role="tab" data-toggle="tab" >
                                                <i class="fa fa-globe"></i> NEFT/RTGS
                                            </a>
                                        </li>
                                    @endif
                                    @if(isset($checkout_methods->lkp_payment_mode_id) && $checkout_methods->lkp_payment_mode_id == CASH_ON_DELIVERY )
                                        @if($tab_active == '')
                                            {{--*/  $tab_active = 'active'  /*--}}
                                        @elseif($tab_active == 'active' || $tab_active == 'alreadyset')
                                        {{--*/  $tab_active = 'alreadyset'  /*--}}
                                        @else
                                            {{--*/  $tab_active = ''  /*--}}
                                        @endif
                                        @if($payMethod=='')
                                            {{--*/  $payMethod = 'COD';  /*--}}
                                       @endif
                                       <li class="{!! $tab_active !!} paymentMode" data-value="COD">
                                            <a href="#payment-type-4" role="tab" data-toggle="tab" >
                                                <i class="fa fa-hand-stop-o"></i> Cash on Delivery
                                            </a>
                                        </li>
                                    @endif
                                    @if(isset($checkout_methods->lkp_payment_mode_id) && $checkout_methods->lkp_payment_mode_id == CASH_ON_PICKUP )
                                        @if($tab_active == '')
                                            {{--*/  $tab_active = 'active'  /*--}}
                                        @elseif($tab_active == 'active' || $tab_active == 'alreadyset')
                                        {{--*/  $tab_active = 'alreadyset'  /*--}}
                                        @else
                                            {{--*/  $tab_active = ''  /*--}}
                                        @endif
                                        @if($payMethod=='')
                                            {{--*/  $payMethod = 'COP';  /*--}}
                                        @endif
                                        <li class="{!! $tab_active !!} paymentMode" data-value="COP">
                                            <a href="#payment-type-5" role="tab" data-toggle="tab">
                                                <i class="fa fa-hand-stop-o"></i> Cash on PickUp
                                            </a>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                            <!-- Tab panes -->
                            <div class="tab-content">
                            	{{--*/  $tab_pane_active = ''  /*--}}
                            	@if($is_contract == IS_CONTRACT  || (isset($checkout_methods->lkp_payment_mode_id) && $checkout_methods->lkp_payment_mode_id == CREDIT && $is_contract == NOT_CONTRACT))
                                    @if($tab_pane_active == '')
                                            {{--*/  $tab_pane_active = 'active in'  /*--}}
                                    @elseif($tab_pane_active == 'active in' || $tab_pane_active == 'alreadyset')
                                    {{--*/  $tab_pane_active = 'alreadyset'  /*--}}
                                    @else
                                        {{--*/  $tab_pane_active = ''  /*--}}
                                    @endif
                                <div class="tab-pane fade {!! $tab_pane_active !!}" id="payment-type-6">
                                    <div class="col-md-8 padding-left-none payment-fld">
                                        <h2>Credit</h2>
                                    </div>                                    
                                </div>
                                @endif
                            	@if((isset($checkout_methods->lkp_payment_mode_id) && isset($checkout_methods->accept_payment_credit) 
                                    && $checkout_methods->lkp_payment_mode_id == ADVANCED && $checkout_methods->accept_payment_credit == 1 
                                    && $is_contract == NOT_CONTRACT) || $is_contract == IS_CONTRACT)
                                    @if($tab_pane_active == '')
                                            {{--*/  $tab_pane_active = 'active in'  /*--}}
                                    @elseif($tab_pane_active == 'active in' || $tab_pane_active == 'alreadyset')
                                    {{--*/  $tab_pane_active = 'alreadyset'  /*--}}
                                    @else
                                        {{--*/  $tab_pane_active = ''  /*--}}
                                    @endif
                                <div class="tab-pane fade {!! $tab_pane_active !!}" id="payment-type-1">
                                        <h4 class="padding-none">Select Payment Gateway </h4>
                                        {{--*/  $radio = 1;  /*--}}
                                        @foreach(unserialize(PAYMENT_GATEWAYS) as $gateway)
                                            @if($gateway['status']==1)
                                                <div class="input-fld col-md-4">
                                                    <input type="radio" id="bank_CC_{{$radio}}" name="gatewayName" value="{{$gateway['value']}}" {{($pay==1)?'checked':'checked'}}>     
                                                    <label for="bank_CC_{{$radio}}"><span></span>{{$gateway['title']}}</label>
                                                </div>
                                            @endif
                                            {{--*/  $pay++; $radio++ /*--}}
                                        @endforeach
                                </div>
                                @endif
                                @if((isset($checkout_methods->lkp_payment_mode_id) && isset($checkout_methods->accept_payment_debit) 
                                && $checkout_methods->lkp_payment_mode_id == ADVANCED && $checkout_methods->accept_payment_debit == 1 
                                && $is_contract == NOT_CONTRACT))
                                @if($tab_pane_active == '')
                                            {{--*/  $tab_pane_active = 'active in'  /*--}}
                                @elseif($tab_pane_active == 'active in' || $tab_pane_active == 'alreadyset')
                                {{--*/  $tab_pane_active = 'alreadyset'  /*--}}
                                @else
                                    {{--*/  $tab_pane_active = ''  /*--}}
                                @endif
                                <div class="tab-pane fade {!! $tab_pane_active !!}" id="payment-type-2">
                                        <h4 class="padding-none">Select Payment Gateway </h4>
                                        {{--*/  $radio = 1;  /*--}}
                                        @foreach(unserialize(PAYMENT_GATEWAYS) as $gateway)
                                            @if($gateway['status']==1)
                                                <div class="input-fld col-md-4">
                                                    <input type="radio" id="bank_DB_{{$radio}}" name="gatewayName" value="{{$gateway['value']}}" {{($pay==1)?'checked':''}}>     
                                                    <label for="bank_DB_{{$radio}}"><span></span>{{$gateway['title']}}</label>
                                                </div>
                                            @endif
                                            {{--*/  $pay++; $radio++; /*--}}
                                        @endforeach
                                </div>
                                @endif
                                @if((isset($checkout_methods->lkp_payment_mode_id) && isset($checkout_methods->accept_payment_netbanking)
                                && $checkout_methods->lkp_payment_mode_id == ADVANCED && $checkout_methods->accept_payment_netbanking == 1 
                                && $is_contract == NOT_CONTRACT) ||
                                (isset($checkout_methods->lkp_payment_mode_id) && $checkout_methods->lkp_payment_mode_id == CREDIT && $is_contract == NOT_CONTRACT))
                                    @if($tab_pane_active == '')
                                            {{--*/  $tab_pane_active = 'active in'  /*--}}
                                    @elseif($tab_pane_active == 'active in' || $tab_pane_active == 'alreadyset')
                                    {{--*/  $tab_pane_active = 'alreadyset'  /*--}}
                                    @else
                                        {{--*/  $tab_pane_active = ''  /*--}}
                                    @endif
                                <div class="tab-pane fade {!! $tab_pane_active !!}" id="payment-type-3">
                                    <div class="col-md-12 padding-none">
                                        <h4 class="padding-none">Please note our account details for NEFT/RTGS processing. </h4>
                                        {{--*/  $radio = 1;  /*--}}
                                        <?php /*@foreach(unserialize(PAYMENT_GATEWAYS) as $gateway)
                                            @if($gateway['status']==1)
                                                <div class="input-fld col-md-4">
                                                    <input type="radio" id="bank_NB_{{$radio}}" name="gatewayName" value="{{$gateway['value']}}" {{($pay==1)?'checked':''}}>     
                                                    <label for="bank_NB_{{$radio}}"><span></span>{{$gateway['title']}}</label>
                                                </div>
                                            @endif 
                                         */?>
                                            {{--*/  $pay++; $radio++; /*--}}
                                        <?php /*@endforeach */?>
                                        
                                        
                                    </div>
                                    <div class="neft_details">
                                        <div class="col-md-12 padding-none">
                                                <label>Name of the account: </label>
                                                <span>{{NEFT_ACCOUNT_NAME}}</span>
                                            </div>
                                        <div class="col-md-12 padding-none">
                                            <label>Account Number: </label>
                                            <span>{{NEFT_ACCOUNT_NUMBER}}</span>
                                        </div>
                                        <div class="col-md-12 padding-none">
                                            <label>Bank: </label>
                                            <span>{{NEFT_BANK}}</span>
                                        </div>
                                        <div class="col-md-12 padding-none">
                                            <label>Branch: </label>
                                            <span>{{NEFT_BRANCH}}</span>
                                        </div>
                                        <div class="col-md-12 padding-none">
                                            <label>IFSC code: </label>
                                            <span>{{NEFT_IFSC_CODE}}</span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if(isset($checkout_methods->lkp_payment_mode_id) && $checkout_methods->lkp_payment_mode_id == CASH_ON_DELIVERY)
                                @if($tab_pane_active == '')
                                    {{--*/  $tab_pane_active = 'active in'  /*--}}
                                @elseif($tab_pane_active == 'active in' || $tab_pane_active == 'alreadyset')
                                    {{--*/  $tab_pane_active = 'alreadyset'  /*--}}
                                @else
                                    {{--*/  $tab_pane_active = ''  /*--}}
                                @endif
                                <div class="tab-pane fade {!! $tab_pane_active !!}" id="payment-type-4">
                                    <div class="col-md-8 padding-left-none payment-fld">
                                        <h2>Cash on Delivery</h2>
                                    </div>
                                </div>
                                @endif
                                @if(isset($checkout_methods->lkp_payment_mode_id) && $checkout_methods->lkp_payment_mode_id == CASH_ON_PICKUP)
                                @if($tab_pane_active == '')
                                    {{--*/  $tab_pane_active = 'active in'  /*--}}
                                @elseif($tab_pane_active == 'active in' || $tab_pane_active == 'alreadyset')
                                    {{--*/  $tab_pane_active = 'alreadyset'  /*--}}
                                @else
                                    {{--*/  $tab_pane_active = ''  /*--}}
                                @endif
                                <div class="tab-pane fade {!! $tab_pane_active !!}" id="payment-type-5">
                                    <div class="col-md-8 padding-left-none payment-fld">
                                        <h2>Cash on PickUp</h2>
                                    </div>
                                </div>
                                @endif

                                <div class="clearfix"></div>
                                <br>


                                <div clas="col-md-12">
                                    <h3 class="margin-none">I agree to make a payment of Rs. {{$common->moneyFormat($orderServiceTax->order_total_amount) }} /- </h3>
                                    <br>
                                    <div class="col-md-12 padding-none margin-bottom">
                                        <div class="normal-checkbox">
	                                        {!! Form::checkbox('agree_payment', '', '',array('id'=>'agree_payment')) !!}
	                                        <span class="lbl padding-8"></span>Accept Terms & Conditions (Digital Contract)
	                                     </div>   
                                    </div>

                                    <div class="input-fld payment-amount margin-top neft_hide">
                                        Total Amount: <span class="amount">Rs. {{$common->moneyFormat($orderServiceTax->order_total_amount) }} /-</span>
                                    </div>
                                     
									 @if($checkout_methods->lkp_payment_mode_id == CASH_ON_DELIVERY || $checkout_methods->lkp_payment_mode_id == CASH_ON_PICKUP)
                                    	
                                        <span></span>
                                    
                                    @else
                                    	<div class="input-fld neft_hide">
                                        <span>You might be Redirected to partner site to verify your credentials before we proceed to authorize payment.</span>
                                  		</div>
                                  		<img src="../images/payment-pic.png" />
                                    @endif
 									
                                   
                                    
                                    <div class="clearfix"></div>
                                    {!! Form::hidden('payment_method', $payMethod , array('id' => 'payment_method')) !!}
                                    @if($checkout_methods->lkp_payment_mode_id == CASH_ON_DELIVERY || $checkout_methods->lkp_payment_mode_id == CASH_ON_PICKUP || $checkout_methods->lkp_payment_mode_id == CREDIT)
                                    	<input type="submit" class="btn theme-btn pull-right" value="Confirm & Continue">
                                    @else
                                    	<input type="submit" class="btn theme-btn pull-right" value="Make Payment">
                                    @endif
                                </div>

                            </div>

                        </div>



                    </div>
                    {!! Form::close() !!}
                    @endif

                            <!-- Right Section Ends Here -->


            </div> <!-- main inner -->
        </div> <!-- col-md-12 padding-none -->


    </div> <!-- container -->
</div> <!-- main -->

@include('partials.footer')
@endsection