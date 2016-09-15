<!doctype html>
<html>
    <head>
        <style type="text/css">
            @import url(https://fonts.googleapis.com/css?family=Lato:400,300,700);
            *{
                box-sizing: border-box;
            }
        </style>
        <title>NFCL</title>
    </head>
    @if((Auth::user()->lkp_role_id == SELLER && Session::get('last_login_role_id')== 0) || (Session::get('last_login_role_id')== SELLER))
    {{--*/ $roleId = SELLER /*--}}  
    @else
    {{--*/ $roleId = BUYER /*--}}  
    @endif
    <body style="font-family: 'Arial'; color: #555555; font-size: 13px; margin: 0; padding: 0;">
        <table cellspacing="0" cellpadding="0" width="100%" style="border-bottom: 1px solid #dadada;">
            <tr>
                <td style="padding: 5px 25px;">
                    <table cellspacing="0" cellpadding="0" width="500" style="margin: auto;">
                        <tr>
                            <td>
                                <img alt="" src="http://design.quad1test.com/newpage/images/logo.png">
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table cellspacing="0" cellpadding="0" width="500" style="margin: auto;">
            <tr>
                <td colspan="2" style="padding: 10px 25px;">
                    <h4 style="font-size: 17px; font-weight: 500; margin: 10px 0; text-align: center">Proforma Invoice for RELOCATION DOMESTIC</h4>
                </td>
            </tr>
            <tr>
                <td style="width: 50%; padding: 10px 25px;">
                    <label style="display: block; clear: both">Vendor Name</label>
                    @if($roleId == 2)
                    <input type="text" placeholder="Vendor Name" style="border: 1px solid #cecece;border-radius: 4px;font-size: 12px;padding: 7px 5px; margin: 3px 0; width: 100%;" value="Cost wise" readonly />
                    @else
                    <input type="text" placeholder="Vendor Name" style="border: 1px solid #cecece;border-radius: 4px;font-size: 12px;padding: 7px 5px; margin: 3px 0; width: 100%;" value="{{ $invoiceData[0]->name }}" readonly />
                    @endif
                </td>
                <td style="width: 50%; padding: 10px 25px;">
                    <label style="display: block; clear: both">Invoice Number</label>
                    <input type="text" placeholder="Invoice Number" style="border: 1px solid #cecece;border-radius: 4px;font-size: 12px;padding: 7px 5px; margin: 3px 0; width: 100%;" value="123334"/>
                </td>
            </tr>
            <tr>
                <td style="width: 50%; padding: 10px 25px;">
                    <label style="display: block; clear: both">Vendor Address</label>
                    <textarea type="text" placeholder="Vendor Address" style="border: 1px solid #cecece;border-radius: 4px;font-size: 12px;padding: 7px 5px; margin: 3px 0; width: 100%;font-family: 'Arial';">Cost wise<br>Hyderabad</textarea>
                </td>
                <td style="width: 50%; padding: 10px 25px;">&nbsp;</td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px 25px;">
                    <table cellspacing="0" cellpadding="0" width="500" style="margin: auto;">
                        <tr>
                            <td style="width: 33.3333%; padding: 0 13px 0 0;">
                                <label style="display: block; clear: both">VAT / TIN Number</label>
                                @if($roleId == SELLER)
                                <input type="text" placeholder="VAT / TIN Number" style="border: 1px solid #cecece;border-radius: 4px;font-size: 12px;padding: 7px 5px; margin: 3px 0; width: 100%;" value="23344"/>        
                                @else
                                <input type="text" placeholder="VAT / TIN Number" style="border: 1px solid #cecece;border-radius: 4px;font-size: 12px;padding: 7px 5px; margin: 3px 0; width: 100%;" value="{{ $invoiceData[0]->tin }}"/>        
                                @endif
                            </td>
                            <td style="width: 33.3333%; padding: 0 12px;">
                                <label style="display: block; clear: both">CST Number</label>
                                @if($roleId == SELLER)
                                <input type="text" placeholder="CST Number" style="border: 1px solid #cecece;border-radius: 4px;font-size: 12px;padding: 7px 5px; margin: 3px 0; width: 100%;" value="23344"/>          
                                @else
                                <input type="text" placeholder="CST Number" style="border: 1px solid #cecece;border-radius: 4px;font-size: 12px;padding: 7px 5px; margin: 3px 0; width: 100%;" value="{{ $invoiceData[0]->service_tax_number }}"/>          
                                @endif
                            </td>
                            <td style="width: 33.3333%; padding: 0 0 0 13px;">
                                <label style="display: block; clear: both">GST Number</label>
                                @if($roleId == SELLER)
                                <input type="text" placeholder="GST Number" style="border: 1px solid #cecece;border-radius: 4px;font-size: 12px;padding: 7px 5px; margin: 3px 0; width: 100%;" value="23344"/>          
                                @else
                                <input type="text" placeholder="GST Number" style="border: 1px solid #cecece;border-radius: 4px;font-size: 12px;padding: 7px 5px; margin: 3px 0; width: 100%;" value="{{ $invoiceData[0]->service_tax_number }}"/>          
                                @endif
                            </td>
                        </tr>
                    </table>    
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px 25px 15px;">
                    <span>Reference Order Number : </span> <b>{{ $invoiceData[0]->order_no }}</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 0 0 25px 0;">
                    <table cellspacing="0" cellpadding="0" width="100%">
                        <tr>
                            <td style="padding: 5px 25px; width: 50%;">
                                <label style="display: block; clear: both">Billing Address (As Specified by Buyer)</label>
                                <textarea type="text" placeholder="Billing Address (As Specified by Buyer)" style="border: 1px solid #cecece;border-radius: 4px;font-size: 12px;padding: 7px 5px; margin: 3px 0; width: 100%;font-family: 'Arial';">{{ $invoiceData[0]->buyer_consignor_address }}</textarea>   
                            </td>
                            <td style="padding: 5px 25px; width: 50%;">
                                <label style="display: block; clear: both">Shipping Address</label>
                                <textarea type="text" placeholder="Shipping Address" style="border: 1px solid #cecece;border-radius: 4px;font-size: 12px;padding: 7px 5px; margin: 3px 0; width: 100%;font-family: 'Arial';">{{ $invoiceData[0]->buyer_consignor_address }}</textarea> 
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 10px 25px 5px;">
                    <b>Road - RELOCATION DOMESTIC</b>
                    <div style="display: block; height: 10px;"></div>
                    <span>From : </span> <b>{{ $invoiceData[0]->city_from }}</b> &nbsp;&nbsp; <span>To : </span> <b>{{ $invoiceData[0]->city_to }}</b>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding: 5px 25px 15px;">
                    <table cellspacing="0" cellpadding="0" width="500" style="margin: 0 auto 25px;">
                        <tr>
                            <td style="width: 33.333%;  padding: 10px;border-top: 1px solid #eeeeee;border-bottom: 1px solid #eeeeee;">
                                <label style="font-weight: bold">Load Type</label>
                            </td>
                            
                            <td style="width: 33.333%; padding: 10px;;border-top: 1px solid #eeeeee;border-bottom: 1px solid #eeeeee;">
                                <label style="font-weight: bold;">Vehicle Type</label>
                            </td>                            
                        </tr>                        
                        <tr>
                            <td style="width: 33.333%; padding: 10px;border-bottom: 1px solid #eeeeee;">
                                <label style="font-weight: 500">{{ $invoiceData[0]->load_type }}</label>
                            </td>
                            
                            <td style="width: 33.333%; padding: 10px;border-bottom: 1px solid #eeeeee;">
                                <label style="font-weight: 500; ">{{ $invoiceData[0]->vehicle_type }}</label>
                            </td>                            
                        </tr>
                        <tr>
                            <td colspan="2">&nbsp;</td>
                            <td style="width: 33.333%; padding: 8px 10px;">
                                <label style="font-weight: 500; ">Sub Total</label>
                            </td>
                            <td style="width: 33.333%;padding: 8px 10px;">
                                <label style="font-weight: 500; ">{{ $invoiceData[0]->price }} /-</label>
                            </td>
                        </tr>
                        @if(SHOW_SERVICE_TAX)
                        <tr>
                            <td colspan="2">&nbsp;</td>                         
                                                     
                            <td style="width: 33.333%; padding: 8px 10px;">
                                <label style="font-weight: 500; ">Service Tax @if($roleId == 2) [14.5%] @else  [14.5%](40%(Price)) @endif</label>
                            </td>
                            <td style="width: 33.333%;padding: 8px 10px;">
                                <label style="font-weight: 500; ">{{ $invoiceData[0]->service_tax_amount }} /-</label>
                            </td>
                        </tr>
                        @endif
                        <tr>
                            <td  colspan="2" style="border-bottom: 1px solid #eeeeee;">&nbsp;</td>
                            <td style="width: 33.333%; padding: 8px 10px;border-bottom: 1px solid #eeeeee;">
                                <label style="font-weight: 500; ">Total Amount</label>
                            </td>
                            <td style="width: 33.333%;padding: 8px 10px;border-bottom: 1px solid #eeeeee;">
                                <label style="font-weight: 500; ">{{ $invoiceData[0]->total_amt }} /-</label>
                            </td>
                        </tr>
                        <tr>
                            <td  colspan="2" style="border-bottom: 0px solid #eeeeee;">&nbsp;</td>
                            <td style="width: 33.333%; padding: 1px 10px;border-bottom: 1px solid #eeeeee;">
                                &nbsp;
                            </td>
                            <td style="width: 33.333%;padding: 1px 10px;border-bottom: 1px solid #eeeeee;">
                                @if(!SHOW_SERVICE_TAX)
                                    * Service Tax not included
                                @endif
                            </td>
                        </tr>
                    </table>      
                </td>
            </tr>
        </table>  
    </body>
</html>