@inject('common_component', 'App\Components\CommonComponent')

@if(isset($storage_data) && count($storage_data) > 0)
    {{--*/ $os = ($storage_data['orgin_storage'] == 1) ? 'checked' : ''; /*--}}
    {{--*/ $oh = ($storage_data['orgin_handyman'] == 1) ? 'checked' : ''; /*--}}
    {{--*/ $ins = ($storage_data['insurance'] == 1) ? 'checked' : ''; /*--}}
    {{--*/ $ds = ($storage_data['dest_storage'] == 1) ? 'checked' : ''; /*--}}
    {{--*/ $dh = ($storage_data['dest_handyman'] == 1) ? 'checked' : ''; /*--}}
<div class="col-md-3 form-control-fld">
        <div class="radio-block"><input type="checkbox" {{$os}} disabled/> <span class="lbl padding-8">Storage</span></div>
        <div class="radio-block"><input type="checkbox" {{$oh}} disabled/> <span class="lbl padding-8">Handyman Services</span></div>
        <div class="radio-block"><input type="checkbox" {{$ins}} disabled/> <span class="lbl padding-8">Insurance</span></div>

</div>
<div class="col-md-3 form-control-fld">
        <div class="radio-block"><input type="checkbox" {{$ds}} disabled/> <span class="lbl padding-8">Storage</span></div>
        <div class="radio-block"><input type="checkbox" {{$dh}} disabled/> <span class="lbl padding-8">Handyman Services</span></div>
</div>
@endif
<div class="clearfix"></div>
{{--*/ $particularsDataCount=$common_component->getBuyerInventoryParticularsDataInfo($buyerpost_id) /*--}}
                                    <!-- Table Starts Here -->                                    
                                    @if($particularsDataCount>0)
                                    <div class="col-md-12">
                                    {{--*/ $roomTypes=array();/*--}}
                                    {{--*/ $roomTypes=$common_component->getBuyerInventoryRoomsbyId($buyerpost_id) /*--}}

                                    @foreach($roomTypes as $roomType)
                                    <div class="col-md-12 padding-left-none data-fld">
                                    <span class="data-head">Room Type: {{$roomType->inventory_room_type}}</span>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="table-div">
                                    <div class="table-heading inner-block-bg">
                                            <div class="col-md-3 padding-left-none">
                                                    <span class="lbl padding-8">Particular
                                            </div>
                                            <div class="col-md-3 padding-left-none">Number of Items</div>
                                            <div class="col-md-3 padding-left-none">Crating</div>
                                            <div class="col-md-3 padding-left-none"></div>
                                    </div>
                                    {{--*/ $particularsData=array();/*--}}
                                    {{--*/ $particularsData=$common_component->getBuyerInventoryParticularsbyId($buyerpost_id,$roomType->lkp_inventory_room_id) /*--}}
                                    <div class="table-data">
                                            @foreach($particularsData as $particularData)

                                            <div class="table-row inner-block-bg">
                                            <div class="col-md-3 padding-left-none">{{$particularData->room_particular_type}}</div>
                                            <div class="col-md-3 padding-left-none">{{$particularData->number_of_items}}</div>
                                            <div class="col-md-3 padding-left-none">
                                            @if($particularData->crating_required==1)
                                            Yes
                                            @else
                                            No
                                            @endif
                                            </div>
                                            </div>
                                            @endforeach
                                    </div>
                                    </div>
                                    @endforeach

                                    </div>
                                    @else
                                    No Inventory Found
                                    @endif
                                    <div class="clearfix"></div>
                        