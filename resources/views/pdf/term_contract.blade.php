 <head>
        <style type="text/css">
            @import url(https://fonts.googleapis.com/css?family=Lato:400,300,700);
            *{
                box-sizing: border-box;
            }
        </style>
        <title>NFCL</title>
    </head>
@inject('commonComponent', 'App\Components\CommonComponent')
@inject('termbuyerCommonComponent', 'App\Components\Term\TermBuyerComponent')
<table width="100%">
<tr><td width="100%">
<table width="100%" align="center">
<tr><td><img alt="" src="http://design.quad1test.com/newpage/images/logo.png"></td></tr>
<tr><td align="center" colspan="5">Contract<br></td></tr>
<tr><td align="center" colspan="5">Contract Validity {{ $commonComponent->convertDate($termContractsData[0]-> from_date) }} - {{ $commonComponent->convertDate($termContractsData[0]-> to_date) }}</td></tr>
<tr><td colspan="2">{{$termContractsData[0]->buyername}}</td>
<td colspan="2" align="right">{{$termContractsData[0]->sellername}}</td>
</tr>
<tr>
<td colspan="2">{{$commonComponent->getBuyerAddress($termContractsData[0]->buyer_id,$termContractsData[0]->buyerbusy)}}</td>
<td colspan="2" align="right">{{$commonComponent->getSellerAddress($termContractsData[0]->seller_id,$termContractsData[0]->sellerbusy)}}</td>
</tr>
</table>

<table width="100%" align="center" border="1">
@if($termContractsData[0]->lkp_service_id==1)
<tr>
<td>From Location</td>
<td>To Location</td>
<td>Quantity(MTs)</td>
<td>Freight(Rs. PMT)</td>
</tr>
@elseif($termContractsData[0]->lkp_service_id==15)
@if($termContractsData[0]->lkp_post_ratecard_type==1 || $termContractsData[0]->lkp_post_ratecard_type==0)
<tr>
<td>From Location</td>
<td>To Location</td>
<td>Volume(CFT)</td>
<td>Freight(Rs. CFT)</td>
</tr>
@else
<tr>
<td>From Location</td>
<td>To Location</td>
<td>Vehicle Type</td>
<td>Vehicle Size</td>
<td>Vehicle Model</td>
<td>Number of Vehicles</td>
<td>Storgae Charge</td>
<td>O&D Charge</td>
<td>Freight</td>
</tr>	
@endif
@elseif($termContractsData[0]->lkp_service_id==21)
<tr>
<td>From Location</td>
<td>To Location</td>
<td>Courier Type</td>
<td>Courier Deliver Type</td>
<td>Number of Packages</td>
<td>Volume (CFT)</td>
</tr>
@elseif($termContractsData[0]->lkp_service_id==19)
<tr>
<td>Location</td>
<td>Service</td>
<td>Numbers</td>
<td>Quotes</td>
</tr>
@elseif($termContractsData[0]->lkp_service_id==18)
<tr>
<td>From Location</td>
<td>To Location</td>
@if($termContractsData[0]->lkp_lead_type_id == 1)
<td>Freight Charges Upto 100 KG</td>
<td>Freight Charges Upto 300 KG</td>
<td>Freight Charges Upto 500 KG</td>
<td>O & D Charges (per CFT)</td>
@else
<td>O & D LCL(per CBM)</td>
<td>O & D 20 FT (per CBM)</td>
<td>O & D 40 FT (per CBM)</td>
<td>Freight LCL (per CBM)</td>
<td>Freight FCL 20 FT (Flat)</td>
<td>Freight FCL 40 FT (Flat)</td>
@endif
<td>Transit Days</td>
</tr>	
@else
<tr>
<td>From Location</td>
<td>To Location</td>
<td>Number of Packages</td>
<td>Volume (CFT)</td>
<td>Rate Per KG</td>
<td>Conversion KG per CFT</td>
</tr>	
@endif	
@if($termContractsData[0]->lkp_service_id==1)
@foreach ($termContractsData as $termContracts)
<tr>
<td>{{ $termContracts-> from_locationcity }}</td>
<td>{{ $termContracts-> to_locationcity }}</td>
<td>{{ $termContracts-> contract_quantity }}</td>
<td>{{ $termContracts-> contract_price }}</td>
</tr>
@endforeach
@elseif($termContractsData[0]->lkp_service_id==15)
@if($termContractsData[0]->lkp_post_ratecard_type==1 || $termContractsData[0]->lkp_post_ratecard_type==0)
@foreach ($termContractsData as $termContracts)
<tr>
<td>{{ $termContracts-> from_locationcity }}</td>
<td>{{ $termContracts-> to_locationcity }}</td>
<td>{{ $termContracts-> contract_quantity }}</td>
<td>{{ $termContracts-> contract_price }}</td>
</tr>
@endforeach
@else
@foreach ($termContractsData as $termContracts)
<tr>
<td>{{ $termContracts-> from_locationcity }}</td>
<td>{{ $termContracts-> to_locationcity }}</td>
<td>
@if($termContracts-> lkp_vehicle_category_id==1)
Car
@else
Bike / Scooter / Scooty	
@endif	
</td>
<td>
@if($termContracts-> lkp_vehicle_category_type_id==1)
Small
@elseif($termContracts-> lkp_vehicle_category_type_id==2)
Medium
@elseif($termContracts-> lkp_vehicle_category_type_id==3)
Big	
@else
N/A	
@endif
</td>
<td>{{ $termContracts-> vehicle_model }}</td>
<td>{{ $termContracts-> no_of_vehicles }}</td>
<td>{{ $termContracts-> contract_transport_charges }}</td>
<td>{{ $termContracts-> contract_od_charges }}</td>
<td>{{ $termContracts-> contract_price }}</td>
</tr>
@endforeach	
@endif
@elseif($termContractsData[0]->lkp_service_id==18)
@foreach ($termContractsData as $termContracts)
<tr>
<td>{{ $termContracts-> from_locationcity }}</td>
<td>{{ $termContracts-> to_locationcity }}</td>
@if($termContractsData[0]->lkp_lead_type_id == 1)
<td>{{ $termContracts-> fright_hundred }}</td>
<td>{{ $termContracts-> fright_three_hundred }}</td>
<td>{{ $termContracts-> fright_five_hundred }}</td>
<td>{{ $termContracts-> contract_od_charges }}</td>
@else
<td>{{ $termContracts-> odlcl_charges }}</td>
<td>{{ $termContracts-> odtwentyft_charges }}</td>
<td>{{ $termContracts-> odfortyft_charges }}</td>
<td>{{ $termContracts-> frieghtlcl_charges }}</td>
<td>{{ $termContracts-> frieghttwentft_charges }}</td>
<td>{{ $termContracts-> frieghtfortyft_charges }}</td>
@endif
<td>{{ $termContracts-> contract_transit_days }} Days</td>
</tr>	
@endforeach
@elseif($termContractsData[0]->lkp_service_id==19)
@foreach ($termContractsData as $termContracts)
<tr>
<td>{{ $termContracts-> from_locationcity }}</td>
<td>{{ $termContracts-> service_type }}</td>
<td>{{ $termContracts-> measurement }} {{ $termContracts-> measurement_units }}</td>
<td>{{ $termContracts-> contract_price }}</td>
</tr>	
@endforeach
@elseif($termContractsData[0]->lkp_service_id==21)
@foreach ($termContractsData as $termContracts)
<tr>
<td>{{ $termContracts-> from_locationcity }}</td>
<td>{{ $termContracts-> to_locationcity }}</td>
<td>@if($termContracts->lkp_courier_type_id == 1)
	Document
	@else
	Parcel
	@endif
	</td>
<td>@if($termContracts->lkp_courier_delivery_type_id ==1)
	Domestic
	@else
	International
	@endif</td>
<td>{{ $termContracts-> number_packages }}</td>
<td>{{ $termContracts-> volume }}</td>
</tr>
@endforeach
@else
@foreach ($termContractsData as $termContracts)
<tr>
<td>{{ $termContracts-> from_locationcity }}</td>
<td>{{ $termContracts-> to_locationcity }}</td>
<td>{{ $termContracts-> number_packages }}</td>
<td>{{ $termContracts-> volume }}</td>
<td>{{ $termContracts-> contract_rate_per_kg }}</td>
<td>{{ $termContracts-> contract_kg_per_cft }}</td>
</tr>
@endforeach
@endif
</table>


@if($termContractsData[0]->lkp_service_id==21)
	<br />
	<table width="100%" align="center" border="1">
	<tr>
		<td>Min</td>
		<td>Max</td>
		<td>Quote</td>
		
	</tr>
	{{--*/ $getTermBuyerQuoteSlabs = $termbuyerCommonComponent->getTermBuyerQuoteSlabs($termContractsData[0]->termbuyerquoteid,$termContractsData[0]->seller_id,21) /*--}}
	 @foreach($getTermBuyerQuoteSlabs as $key=>$pricelab)
	
	 	<tr>
			<td>{{ $pricelab->slab_min_rate }}</td>
			<td>{{ $pricelab->slab_max_rate }}</td>
			<td>{{ $pricelab->slab_rate }}</td>
			
			
		</tr>
	
	@endforeach
	</table>

	
	{{--*/ $getMaxWeightIncWeight = $termbuyerCommonComponent->getMaxWeightIncWeight($termContractsData[0]->termbuyerquoteid,21) /*--}}
 
	@if($getMaxWeightIncWeight[0]->incremental_weight != 0.00)
	<br />
		<table width="100%" align="center" border="1">
		<tr>
		<td>Incremental Weight</td>
		<td>Incremental Weight Price</td>
		</tr>
		<tr>
		<td>{{ $getMaxWeightIncWeight[0]->incremental_weight }} {{ $termbuyerCommonComponent->getMaxWeightAcceptedUnits($termContractsData[0]->termbuyerquoteid,21) }}</td>
		<td>{{ $getMaxWeightIncWeight[0]->incremental_weight_price }} /-</td>
		</tr>
		</table>	
	@endif

	{{--*/ $getQuoteAddtionalDetails = $commonComponent->getQuoteAddtionalDetails($termContractsData[0]->termbuyerquoteid,$termContractsData[0]->seller_id) /*--}}
	<br />
	<table width="100%" align="center" border="1">
		<tr>
		<td>Conversion Factor</td>
		<td>Transit Days</td>
		<td>Fuel Surcharge</td>
		<td>COD Charges</td>
		<td>Freight Collect</td>
		<td>Arc Charges</td>
		<td>Max value</td>
		</tr>
		<tr>
		<td>{{ $getQuoteAddtionalDetails[0]->conversion_factor }}</td>
		<td>{{ $getQuoteAddtionalDetails[0]->transit_days }}</td>
		<td>{{ $getQuoteAddtionalDetails[0]->fuel_charges }} %</td>
		<td>{{ $getQuoteAddtionalDetails[0]->cod_charges }} %</td>
		<td>{{ $getQuoteAddtionalDetails[0]->freight_charges }} /-</td>
		<td>{{ $getQuoteAddtionalDetails[0]->arc_charges }} %</td>
		<td>{{ $getQuoteAddtionalDetails[0]->max_value }} /-</td>
		</tr>
	</table>
@endif	



</td></tr>

 <tr><td align="right">Authorised</td></tr>
 <tr><td colspan="2" align="right">{{$termContractsData[0]->buyername}}</td></tr>
</table>