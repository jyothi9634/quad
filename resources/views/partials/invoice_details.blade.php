@if(!empty($invoiceExist) && $order->gsa_accepted==1 && !empty($deliveryExist) && ($trackingExist==1 ||  (isset($tracking) && $tracking==2)) && !empty($pickExist) )

    <div class="accordian-blocks">
        <div class="inner-block-bg inner-block-bg1 detail-head">
            <h2 class="filter-head1">Invoice Generation</h2>
        </div>
        <div class="detail-data">
            <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                <div class="col-md-6 data-div order-detail-price-block">
                    <div><span class="vendor-name">Invoice</span></div>
                    <div class="col-md-4 padding-left-none data-fld">
                        <span class="data-head">Invoice No</span>
                        <span class="data-value">{{$invoice->invoice_no}}</span>
                    </div>
                    @if(SHOW_SERVICE_TAX)
                    <div class="col-md-4 padding-left-none data-fld">
                        <span class="data-head">Services Charges</span>
                        <span class="data-value">{{number_format($invoice->service_charge_amount,2)}}</span>
                    </div>
                    <div class="col-md-4 padding-left-none data-fld">
                        <span class="data-head">Service Tax</span>
                        <span class="data-value">{{number_format($invoice->service_tax_amount,2)}}</span>
                    </div>
                    @endif
                    <div class="clearfix"></div>
                    <div class="col-md-4 padding-left-none data-fld">
                        <span class="data-head">Total Price</span>
                        <span class="data-value big-value">{{number_format($invoice->total_amount,2)}}
                        <br>
                        @if(!SHOW_SERVICE_TAX)
                        <span class="small serviceTax">(* Service Tax not included )</span>
                        @endif
                        </span>
                    </div>
                    @if(empty($receiptExist) && $invoice->total_amount!=0)
                    <div class="col-md-8 padding-none data-fld text-right">
                        <br>
                        {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-receipt']) !!}
                        <input type="button" name="receipt" id="receipt" class="btn add-btn flat-btn" data-toggle="modal" data-target="#payment" value="Make Payment">
                        <input type="hidden" name="receipts" value="receipts">

                        {!! Form::close() !!}
                    </div>
                @endif	
                </div>
                @if(!empty($receiptExist))
                    <div class="col-md-6 order-detail-price-block">
                        <div><span class="vendor-name">Reciept</span></div>
                        <div class="col-md-4 padding-left-none data-fld">
                            <span class="data-head">Receipt No</span>
                            <span class="data-value">{{$receipt->receipt_no}}</span>
                        </div>
                        <div class="col-md-4 padding-left-none data-fld">
                            <span class="data-head">Freight Amount</span>
                            <span class="data-value">{{number_format($receipt->frieght_amount,2)}}/-</span>
                        </div>
                        <div class="col-md-4 padding-left-none data-fld">
                            <span class="data-head">Insurance</span>
                            <span class="data-value">{{number_format($receipt->insurance,2)}}/-</span>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-8 padding-left-none data-fld">
                            <span class="data-head">Payment Mode</span>
                            <span class="data-value">{{$payment_mode}} xxxx xxxx xxxx 7377 on {{date("d/m/Y", strtotime($receipt->created_at))}}</span>
                        </div>
                        <div class="col-md-4 padding-left-none data-fld">
                            <span class="data-head">Total Price</span>
                            <span class="data-value big-value">{{number_format($receipt->total_amount,2)}}/-</span>
                        </div>
                    </div>
                    <div class="col-md-12 text-right btn-block">
                        <button class="btn add-btn flat-btn">Reverse</button>
                        <button class="btn red-btn flat-btn">Confirm</button>
                    </div>
                @endif

                <div class="col-md-12 text-right btn-block">
                    <!--button class="btn red-btn flat-btn">Confirm</button-->
                    <!-- input type="button" class="btn red-btn flat-btn" value="Confirm" -->
                </div>
            </div>
        </div>
    </div>
@endif