<?php /* @if($commonComponent->getCommercial($order->id)==1)  */ ?>    
                            @if($order->gsa_accepted==0)
                                <div class="accordian-blocks">
                                    <div class="inner-block-bg inner-block-bg1 detail-head">
                                        <h2 class="filter-head1">GSA Terms</h2>
                                    </div>
                                    <div class="detail-data">
                                        <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                            <div class="col-md-12 text-right btn-block border-none padding-none">
                                                {!! Form::open(['url' => 'consignment_pickup/'.$order->id,'id'=>'posts-form-gsa']) !!}
                                                <input type="hidden" name="order_id" id="order_id" value="{{$order->id}}">
                                                <input type="button" class="btn add-btn flat-btn gsa_accept"  value="Accept GSA Terms">
                                                {!! Form::close() !!}
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            @endif

                            @if($order->gsa_accepted==1)
                                <div class="accordian-blocks">
                                    <div class="inner-block-bg inner-block-bg1 detail-head">
                                        <h2 class="filter-head1">GSA Terms</h2>
                                    </div>
                                    <div class="detail-data">
                                        <div class="inner-block-bg inner-block-bg1 padding-bottom-none border-none">
                                            <div class="col-md-12 padding-none border-none">
                                                <div class="col-md-12">
                                                    <div class="col-md-2 padding-left-none data-fld">
                                                        <span class="data-head">Accepted On</span>
                                                        <span class="data-value">{{date("d/m/Y h:i A", strtotime($order->gsa_accepted_on))}}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        <?php /* @else
                             $order->gsa_accepted=1; 
                        @endif */ ?>     