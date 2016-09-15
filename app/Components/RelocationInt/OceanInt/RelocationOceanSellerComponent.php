<?php

namespace App\Components\RelocationInt\OceanInt;

use App\Components\Matching\SellerMatchingComponent;
use App\Models\RelocationBuyerQuoteSellersQuotesPrice;
use App\Models\RelocationSellerPost;
use App\Models\RelocationSellerPostItem;
use App\Models\RelocationSellerSelectedBuyer;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use App\Components\CommonComponent;
use App\Models\User;
use App\Models\PtlSearchTerm;
use App\Components\SellerComponent;
use App\Components\Search\SellerSearchComponent;
use App\Models\PtlZone;
use App\Models\PtlTier;
use App\Models\PtlTransitday;
use App\Models\PtlSector;
use App\Models\PtlPincodexsector;
use App\Models\RelocationpetSellerPost;
use App\Models\RelocationpetSellerPostItem;
use App\Models\RelocationpetSellerSelectedBuyer;
use App\Models\RelocationpetBuyerQuoteSellersQuotesPrice;
use App\Components\MessagesComponent;

class RelocationOceanSellerComponent
{

    /**
     * Relocation Seller Posts List Page - Grid and filters
     * Retrieval of data related to seller posts list items to populate in the seller list widget
     * Displays a grid with a list of all seller posts
     */
    public static function RelocationPetSpotSellerPosts($statusId, $serviceId, $roleId, $type)
    {
        //echo "<pre>";echo $statusId;exit;
        if (isset($_REQUEST['page'])) {//echo $_REQUEST['page'];
        }
        $from_locations = array("" => "From Location");
        $to_locations = array("" => "To Location");
        $post_for = array("" => "Post For");

        // query to retrieve seller posts list and bind it to the grid
        $Query = DB::table('relocationpet_seller_posts as rsp');
        $Query->leftjoin('relocationpet_seller_post_items as rspi', 'rspi.seller_post_id', '=', 'rsp.id');
        $Query->leftjoin('lkp_post_statuses as ps', 'ps.id', '=', 'rsp.lkp_post_status_id');
        $Query->leftjoin('lkp_cities as cf', 'rsp.from_location_id', '=', 'cf.id');
        $Query->leftjoin('lkp_cities as ct', 'rsp.to_location_id', '=', 'ct.id');
        //$Query->join ( 'lkp_post_ratecard_types as prct', 'rsp.rate_card_type', '=', 'prct.id' );
        $Query->leftjoin('lkp_quote_accesses as qa', 'rsp.lkp_access_id', '=', 'qa.id');
        if (isset($_REQUEST['lead_name']) && ($_REQUEST['lead_name'] == 2)) {
            Session::put('leads', '2');
            $Query->where('rsp.lkp_access_id', 1);
        } else {
            Session::put('leads', '1');
            $Query->leftjoin('relocationpet_buyer_selected_sellers as rbqss', 'rbqss.seller_id', '=', 'rspi.created_by');
        }
        $Query->where('rsp.seller_id', Auth::user()->id);
        //conditions to make search
        if (isset($statusId) && $statusId != '') {
            $Query->where('rsp.lkp_post_status_id', $statusId);
        }
        if (isset($type) && $type != '') {
            if ($type == 1) {
                $Query->where('rsp.created_by', Auth::user()->id);
            }
        }
        if (isset($_REQUEST['search']) && $_REQUEST['from_date'] != '') {
            $from = CommonComponent::convertDateForDatabase($_REQUEST['from_date']);
            $Query->whereRaw('rsp.from_date >= "' . $from . '"');
        }

        if (isset($_REQUEST['search']) && $_REQUEST['to_date'] != '') {
            $to = CommonComponent::convertDateForDatabase($_REQUEST['to_date']);
            if ($_REQUEST['from_date'] != '') {
                $Query->whereBetween('rsp.to_date', array($from, $to));
            } else {
                $Query->where('rsp.to_date', $to);
            }
        }


        $sellerresults = $Query->select('rsp.id', 'rsp.from_date',
            'rsp.to_date', 'rsp.lkp_access_id', 'rsp.lkp_post_status_id', 'rsp.from_location_id',
            'rsp.to_location_id', 'cf.city_name as fromLocation', 'ct.city_name as toLocation',
            'qa.quote_access as quoteAccessType', 'ps.post_status as postStatus'
        )
            ->groupBy('rsp.id')
            ->get();
        //Functionality to handle filters based on the selection starts
        foreach ($sellerresults as $seller) {

            if (!isset($from_locations[$seller->from_location_id])) {
                $from_locations[$seller->from_location_id] = DB::table('lkp_cities')->where('id', $seller->from_location_id)->pluck('city_name');
            }
            if (!isset($to_locations[$seller->to_location_id])) {
                $to_locations[$seller->to_location_id] = DB::table('lkp_cities')->where('id', $seller->to_location_id)->pluck('city_name');
            }

        }
        //echo "<pre>"; print_r($sellerresults); die;
        $grid = DataGrid::source($Query);
        $grid->add('id', 'ID', true)->style("display:none");
        $grid->add('fromLocation', 'From', 'fromLocation')->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add('toLocation', 'To', 'toLocation')->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add('from_date', 'Valid From', 'from_date')->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add('to_date', 'Valid To', 'to_date')->attributes(array("class" => "col-md-2 padding-left-none"));
        //$grid->add ( 'rateCatdType', 'Post For', 'rateCatdType' )->attributes(array("class" => "col-md-1 padding-left-none"));
        $grid->add('quoteAccessType', 'Post Type', 'quoteAccessType')->attributes(array("class" => "col-md-1 padding-left-none"));
        $grid->add('postStatus', 'Status', 'postStatus')->attributes(array("class" => "col-md-1 padding-left-none"));
        $grid->add('below_grid', 'Below Grid', true)->style("display:none");
        $grid->orderBy('id', 'desc');
        $grid->paginate(5);

        $grid->row(function ($row) {

            $sellerPostId = $row->cells [0]->value;
            $fromLcoation = $row->cells [1]->value;
            $toLcoation = $row->cells [2]->value;
            $fromDate = $row->cells [3]->value;
            $toDate = $row->cells [4]->value;
            //$postFor = $row->cells [5]->value;
            $postType = $row->cells [5]->value;
            $status = $row->cells [6]->value;

            $row->cells [0]->style('display:none');
            $row->cells [1]->style('display:none');
            $row->cells [2]->style('display:none');
            $row->cells [3]->style('display:none');
            $row->cells [4]->style('display:none');
            $row->cells [5]->style('display:none');
            $row->cells [6]->style('display:none');
            //$row->cells [7]->style ( 'display:none' );

            if ($status == 'Draft')
                $data_link = url() . "/relocation/updatesellerpost/$sellerPostId";
            else
                $data_link = url() . "/sellerpostdetail/$sellerPostId";

            $row->cells [7]->value .= "<div class=''><a href='$data_link'>										
										<div class='col-md-2 padding-left-none'>$fromLcoation</div>
										<div class='col-md-2 padding-left-none'>$toLcoation</div>
										<div class='col-md-2 padding-left-none'>" . CommonComponent::checkAndGetDate($fromDate) . "</div>
										<div class='col-md-2 padding-left-none'>" . CommonComponent::checkAndGetDate($toDate) . "</div>
										
										<div class='col-md-1 padding-none'>$postType</div>
										<div class='col-md-1 padding-none'> $status </div></a>";
            if ($status == 'Open' || $status == 'Draft') {
                $str = 'setcancelpostid("posts", ' . $sellerPostId . ')';
                $row->cells [7]->value .= "<div class='padding-none pull-right'>
										<a onclick='" . $str . "' data-toggle='modal' data-target='#cancelsellerpostmodal' href='javascript:void(0)' >"
                    . "<i class='fa fa-trash' title='Delete'></i></a>
										</div>";
            }
            $getpostitemids = DB::table('relocationpet_seller_post_items')
                ->where('relocationpet_seller_post_items.seller_post_id', '=', $sellerPostId)
                ->select('relocationpet_seller_post_items.id')
                ->get();
            $allcountview = 0;
            $viewcount = 0;
            if (count($getpostitemids) > 0) {
                for ($i = 0; $i < count($getpostitemids); $i++) {
                    //view count for sellers
                    $viewcount += CommonComponent::getSellersViewcountFromTable($getpostitemids[$i]->id, 'relocationpet_seller_post_views');
                }
            }
            $enquiriesCount = SellerMatchingComponent::getMatchedResults(RELOCATION_PET_MOVE, $sellerPostId);
            $leadsCount = SellerMatchingComponent::getSellerLeads(RELOCATION_PET_MOVE, $sellerPostId);
            $msg_count = MessagesComponent::listMessages(null, POSTENQURYMESSAGETYPE, null, $sellerPostId);

            $row->cells [7]->value .= "<div class='clearfix'></div>
										<div class='pull-left'>
											<div class='info-links'>												
												<a href='/sellerpostdetail/$sellerPostId?type=messages'><i class='fa fa-envelope-o'></i> Messages<span class='badge'>" . count($msg_count['result']) . "</span></a>
												<a href='#'><i class='fa fa-file-text-o'></i> Enquiries<span class='badge'>" . count($enquiriesCount) . "</span></a>
												<a href='#'><i class='fa fa-bullseye'></i> Leads<span class='badge'>" . count($leadsCount) . "</span></a>
												<a href='#'><i class='fa fa-line-chart'></i> Market Analytics<span class='badge'>0</span></a>
												<a href='#'><i class='fa fa-file-text-o'></i> Documentation<span class='badge'>0</span></a>												
											</div>
										</div>
										<div class='pull-right text-right'>
											<div class='info-links'>
												<a><span class='views red'><i class='fa fa-eye' title='Views'></i> $viewcount </span></a>
											</div>
										</div>
									</div>";

            $row->attributes(array("class" => ""));

        });
        //Functionality to build filters in the page starts
        $filter = DataFilter::source($Query);
        $filter->add('rsp.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
        $filter->add('rsp.to_location_id', 'From Location', 'select')->options($to_locations)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
        $filter->submit('search');
        $filter->reset('reset');
        $filter->build();
        //Functionality to build filters in the page ends
        $result = array();
        $result['grid'] = $grid;
        $result['filter'] = $filter;
        return $result;
    }


    public static function RelocationPetSellerMarketLeads($statusId, $serviceId, $roleId, $type)
    {
        if (isset($_REQUEST['page'])) {//echo $_REQUEST['page'];
        }
        $from_locations = array("" => "From Location");
        $to_locations = array("" => "To Location");
        $cage_types = array("" => "Cage Type");
        $pet_types = array("" => "Pet Type");
        //echo "<pre>"; print_r($_REQUEST); die;
        // query to retrieve seller posts list and bind it to the grid
        $Query = DB::table('relocationpet_buyer_posts as rbp');
        $Query->leftjoin('relocationpet_buyer_selected_sellers as rbss', 'rbss.buyer_post_id', '=', 'rbp.id');
        $Query->leftjoin('lkp_post_statuses as ps', 'ps.id', '=', 'rbp.lkp_post_status_id');
        $Query->join('lkp_cities as cf', 'rbp.from_location_id', '=', 'cf.id');
        $Query->join('lkp_cities as ct', 'rbp.to_location_id', '=', 'ct.id');
        $Query->join('lkp_pet_types as lkpt', 'rbp.lkp_pet_type_id', '=', 'lkpt.id');
        $Query->join('lkp_cage_types as lkct', 'rbp.lkp_cage_type_id', '=', 'lkct.id');
        $Query->leftjoin('lkp_breed_types as lkbt', 'rbp.lkp_breed_type_id', '=', 'lkbt.id');
        $Query->join('lkp_quote_accesses as qa', 'rbp.lkp_quote_access_id', '=', 'qa.id');
        $Query->leftjoin('users as us', 'us.id', '=', 'rbp.buyer_id');
        $Query->where('rbss.seller_id', Auth::user()->id);
        $Query->where('rbp.lkp_post_status_id', OPEN);
        $Query->where('rbp.lkp_quote_access_id', 2);
        //conditions to make search
        if (isset($statusId) && $statusId != '') {
            $Query->where('rbp.lkp_post_status_id', $statusId);
        }
        if (isset($type) && $type != '') {
            if ($type == 1) {
                $Query->where('rbp.created_by', Auth::user()->id);
            }
        }
        if (isset($_REQUEST['search']) && $_REQUEST['from_date'] != '') {
            $from = CommonComponent::convertDateForDatabase($_REQUEST['from_date']);
            $Query->whereRaw('rbp.dispatch_date >= "' . $from . '"');
        }

        if (isset($_REQUEST['search']) && $_REQUEST['from_date'] != '') {
            $from = CommonComponent::convertDateForDatabase($_REQUEST['from_date']);
            $Query->whereRaw('rbp.dispatch_date >= "' . $from . '"');
        }

        if (isset($_REQUEST['search']) && $_REQUEST['to_date'] != '') {
            $to = CommonComponent::convertDateForDatabase($_REQUEST['to_date']);
            if ($_REQUEST['from_date'] != '') {
                $Query->whereBetween('rbp.delivery_date', array($from, $to));
            } else {
                $Query->where('rbp.delivery_date', $to);
            }
        }

        //Filters for from and to lcoations
        if (isset($_REQUEST['search']) && $_REQUEST['from_location_id'] != '') {
            if ($_REQUEST['from_location_id'] != '') {
                $Query->where('rbp.from_location_id', $_REQUEST['from_location_id']);
            }
        }

        if (isset($_REQUEST['search']) && $_REQUEST['to_location_id'] != '') {
            if ($_REQUEST['to_location_id'] != '') {
                $Query->where('rbp.to_location_id', $_REQUEST['to_location_id']);
            }
        }

        if (isset($_REQUEST['search']) && $_REQUEST['lkp_cage_type_id'] != '') {
            if ($_REQUEST['lkp_cage_type_id'] != '') {
                $Query->where('rbp.lkp_cage_type_id', $_REQUEST['lkp_cage_type_id']);
            }
        }

        if (isset($_REQUEST['search']) && $_REQUEST['lkp_pet_type_id'] != '') {
            if ($_REQUEST['lkp_pet_type_id'] != '') {
                $Query->where('rbp.lkp_pet_type_id', $_REQUEST['lkp_pet_type_id']);
            }
        }

        $sellerresults = $Query->select('rbp.*', 'lkbt.breed_type', 'lkpt.pet_type', 'lkct.cage_type', 'lkct.cage_weight', 'us.username', 'cf.city_name as fromcity', 'ct.city_name as tocity'
        )
            ->groupBy('rbp.id')
            ->get();
        //echo "<pre>"; print_r($sellerresults); die;
        //Functionality to handle filters based on the selection starts
        foreach ($sellerresults as $seller) {
            $seller_post_items = DB::table('relocationpet_buyer_posts')
                ->where('relocationpet_buyer_posts.id', $seller->id)
                ->select('*')
                ->get();
            foreach ($seller_post_items as $seller_post_item) {
                if (!isset($from_locations[$seller_post_item->from_location_id])) {
                    $from_locations[$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
                }
                if (!isset($to_locations[$seller_post_item->to_location_id])) {
                    $to_locations[$seller_post_item->to_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->to_location_id)->pluck('city_name');
                }
                if (!isset($pet_types[$seller_post_item->lkp_pet_type_id])) {
                    $pet_types[$seller_post_item->lkp_pet_type_id] = DB::table('lkp_pet_types')->where('id', $seller_post_item->lkp_pet_type_id)->pluck('pet_type');
                }
                if (!isset($cage_types[$seller_post_item->lkp_cage_type_id])) {
                    $cage_types[$seller_post_item->lkp_cage_type_id] = DB::table('lkp_cage_types')->where('id', $seller_post_item->lkp_cage_type_id)->pluck('cage_type');
                }
            }
        }
        Session::put('RelcoationRequestData', $sellerresults);
        $grid = DataGrid::source($sellerresults);
        $grid->add('id', 'ID', true)->style("display:none");
        $grid->add('username', 'Buyer Name', true)->attributes(array("class" => "col-md-3 padding-left-none"));
        $grid->add('dispatch_date', 'Dispatch Date', true)->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add('test', 'Below Grid', true)->style("display:none");
        $grid->add('transaction_id', 'transaction_id', 'transaction_id')->style("display:none");
        $grid->add('created_by', 'Created By', 'created_by')->style("display:none");
        $grid->add('from_location_id', 'From Location', 'from_location_id')->style("display:none");
        $grid->add('to_location_id', 'To Location', 'to_location_id')->style("display:none");
        $grid->add('pet_type', 'Pet Type', 'pet_type')->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add('cage_weight', 'Pet Weight', 'cage_weight')->attributes(array("class" => "col-md-2 padding-left-none"));
        $grid->add('cage_type', 'Cage Type', 'cage_type')->style("display:none");
        $grid->add('breed_type', 'Breed Type', 'breed_type')->style("display:none");
        $grid->add('fromcity', 'From City', 'fromcity')->style("display:none");
        $grid->add('tocity', 'To City ', 'tocity')->style("display:none");
        $grid->orderBy('id', 'desc');
        $grid->paginate(5);

        $grid->row(function ($row) {
            $row->cells [0]->style('display:none');
            $row->cells [1]->style('display:none');
            $row->cells [2]->style('display:none');
            $row->cells [4]->style('display:none');
            $row->cells [5]->style('display:none');
            $row->cells [6]->style('display:none');
            $row->cells [7]->style('display:none');
            $row->cells [8]->style('display:none');
            $row->cells [9]->style('display:none');
            $row->cells [10]->style('display:none');
            $row->cells [11]->style('display:none');
            $row->cells [12]->style('display:none');
            $row->cells [13]->style('display:none');

            $id = $row->cells [0]->value;
            $buyerbussinessname = $row->cells [1]->value;
            $dispatchdate = $row->cells [2]->value;
            $transaction_id = $row->cells [4]->value;
            $petType = $row->cells [8]->value;
            $petWeight = $row->cells [9]->value;
            $cageType = $row->cells [10]->value;
            $created_by = $row->cells [5]->value;
            $breedType = $row->cells [11]->value;
            $fromCity = $row->cells [12]->value;
            $toCity = $row->cells [13]->value;
            if ($breedType != '') {
                $breedTypeDisplay = $row->cells [11]->value;
            } else {
                $breedTypeDisplay = 'NA';
            }

            $requestSessiondata = Session::get('RelcoationRequestData');


            $sellercomponent = new RelocationPetSellerComponent();
            $submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User()->id, $id);
            $enquiry = $sellercomponent::getBuyerpostById($id);
            //echo "<pre>"; print_r($enquiry); die;
            $submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted";
            $row->cells [3]->value .= '<div class="table-div">  

                                            <div class="table-data">

                                                    <!-- Table Row Starts Here -->

                                                    <div class="table-row ">
                                                            <div class="col-md-3 padding-left-none">
                                                                 ' . $buyerbussinessname . '											
                                                                    <div class="red">
                                                                            <i class="fa fa-star"></i>
                                                                            <i class="fa fa-star"></i>
                                                                            <i class="fa fa-star"></i>
                                                                    </div>
                                                            </div>
                                                            <div class="col-md-2 padding-left-none">' . CommonComponent::checkAndGetDate($dispatchdate) . '</div>
                                                            <div class="col-md-2 padding-left-none">' . $petType . '</div>
                                                            <div class="col-md-2 padding-left-none">' . $petWeight . ' KGs</div>
                                                            <div class="col-md-3 padding-none">
                                                                    <button class="btn red-btn detailsslide-term pull-right submit-data" id =' . $id . '>' . $submitedquotetext . '</button>
                                                            </div>

                                                            <div class="clearfix"></div>

                                                            <div class="pull-right text-right">
                                                                    <div class="info-links">
                                                                            <span class="show-data-link detailsslide underline_link" id=' . $id . ' data-buyersearchlistid=' . $created_by . '_' . $id . '><span class="show-icon">+</span><span class="hide-icon">-</span> Details</span> |
                                                                            <span href="#" class="new_message" data-transaction_no=' . $transaction_id . ' data-userid=' . $created_by . ' data-buyerquoteitemid=' . $id . '><i class="fa fa-envelope-o"></i></span>
                                                                    </div>
                                                            </div>';


            $buyerpostdata = array();
            $buyerpostdata['from_location_id'] = $enquiry->from_location_id;
            $buyerpostdata['to_location_id'] = $enquiry->to_location_id;
            $buyerpostdata['valid_from'] = $enquiry->dispatch_date;
            $buyerpostdata['valid_to'] = date('Y-m-d', strtotime($enquiry->dispatch_date . " + 1 days"));

            $SubmitquotePartial = view('relocationpet.sellers.submit_quote')->with([
                'submittedquote' => $submittedquote,
                'enquiry' => $enquiry,
                'id' => $id,
                'is_search' => 1,
                'search_params' => $buyerpostdata
            ])->render();


            $row->cells [3]->value .= '<div class="col-md-12  padding-top term_quote_details_' . $id . '" style="display:none">
                                                                    ' . $SubmitquotePartial . '
                                                            </div>

                                                                <div class="col-md-12  show-data-div spot_transaction_details_view_list padding-none padding-top">
                                                                
                                                                <div>
                                                                        <h3>
                                                                            <i class="fa fa-map-marker"></i> ' . $fromCity . ' to ' . $toCity . '
                                                                            <span class="close-icon">x</span>
                                                                        </h3>
                                                                </div>

                                                                    <div class="table-div table-style1 margin-top">

                                                                            <!-- Table Head Starts Here -->

                                                                            <div class="table-heading inner-block-bg">
                                                                                    <div class="col-md-3 padding-left-none">Pet Type</div>
                                                                                    <div class="col-md-3 padding-left-none">Breed</div>
                                                                                    <div class="col-md-3 padding-left-none">Cage Type</div>
                                                                                    <div class="col-md-3 padding-none">Cage Weight</div>
                                                                            </div>

                                                                            <!-- Table Head Ends Here -->

                                                                            <div class="table-data">

                                                                                    <!-- Table Row Starts Here -->

                                                                                    <div class="table-row inner-block-bg">
                                                                                            <div class="col-md-3 padding-left-none">' . $petType . '</div>
                                                                                            <div class="col-md-3 padding-left-none">' . $breedTypeDisplay . '</div>
                                                                                            <div class="col-md-3 padding-left-none">' . $cageType . '</div>
                                                                                            <div class="col-md-3 padding-none">' . $petWeight . ' KGs</div>
                                                                                    </div>

                                                                                    <!-- Table Row Ends Here -->
                                                                            </div>

                                                                    <!-- Table Ends Here -->

                                                                    </div>
                                                                    <!-- Table Ends Here -->

                                                            </div>
                                                    </div>
                                            </div>
                                    </div>';
        });

        //Functionality to build filters in the page starts
        $filter = DataFilter::source($Query);
        $filter->add('rbp.from_location_id', 'From Location', 'select')->options($from_locations)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
        $filter->add('rbp.to_location_id', 'From Location', 'select')->options($to_locations)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
        $filter->add('rbp.lkp_pet_type_id', 'Pet Type', 'select')->options($pet_types)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
        $filter->add('rbp.lkp_cage_type_id', 'Cage Type', 'select')->options($cage_types)->attr("class", "selectpicker")->attr("onchange", "this.form.submit()");
        $filter->submit('search');
        $filter->reset('reset');
        $filter->build();
        //Functionality to build filters in the page ends
        $result = array();
        $result['grid'] = $grid;
        $result['filter'] = $filter;
        return $result;
    }

    public static function getVolumeCft($buyer_post_id){
        try{
            $buyer_post_details = DB::table ( 'relocationint_buyer_posts' )->where ( 'id', $buyer_post_id )->get ();
            $buyer_post_inventory_details = DB::table ( 'relocationint_buyer_post_inventory_particulars' )->where ( 'buyer_post_id', $buyer_post_id )->get ();

            if(count($buyer_post_inventory_details)>0){
                $totalcft=0;
                $getroomsdata  = DB::table('relocationint_buyer_post_inventory_particulars as rebip')
                                    ->where('rebip.buyer_post_id',$buyer_post_id)
                                    ->select('rebip.lkp_inventory_room_particular_id','rebip.number_of_items')
                                    ->get();
                foreach($getroomsdata as $getdata){
                    $itemvolume = DB::table('lkp_inventory_room_particulars as lirp')
                        ->where('lirp.id',$getdata->lkp_inventory_room_particular_id)
                        ->select('lirp.*')
                        ->get();
                    $totalcft=$totalcft+$getdata->number_of_items*$itemvolume[0]->volume;
                }
                $volume=$totalcft;
            }else{
                $volume=CommonComponent::getPropertyCft($buyer_post_details[0]->lkp_property_type_id);
            }
            return $volume;
        }catch(\Exception $e){
            //return $e->message;
        }
    }

    public static function getTotalNoofItems($buyer_post_id){
        try{
            $itemscount = 0;
            $buyer_post_inventory_details = DB::table ( 'relocationint_buyer_post_inventory_particulars' )->where ( 'buyer_post_id', $buyer_post_id )->get ();
            if(count($buyer_post_inventory_details)>0){
                foreach($buyer_post_inventory_details as $getdata){
                    $itemscount = $itemscount + $getdata->number_of_items;
                }
            }else{
                $itemscount = "NA";
            }
            return $itemscount;
        }catch(\Exception $e){
            //return $e->message;
        }
    }
    public static function getBuyerInventoryParticularsDataInfo($buyer_post_id){
        try {
            $getparticularsdata  = DB::table('relocationint_buyer_post_inventory_particulars as rebip')
                ->where('rebip.buyer_post_id',$buyer_post_id)
                ->select('rebip.id')
                ->get();
            return count($getparticularsdata);
        } catch ( Exception $exc ) {
        }

    }


    public static function getBuyerInventoryRoomsbyId($buyer_post_id){
        try {
            $getroomsdata  = DB::table('relocationint_buyer_post_inventory_particulars as rebip')
                ->join('lkp_inventory_rooms as rooms','rooms.id','=','rebip.lkp_inventory_room_id')
                ->where('rebip.buyer_post_id',$buyer_post_id)
                ->groupBy('rebip.lkp_inventory_room_id')
                ->select('rooms.inventory_room_type','rebip.lkp_inventory_room_id')
                ->get();
            return $getroomsdata;
        } catch ( Exception $exc ) {
        }

    }

    public static function getBuyerInventoryParticularsbyId($buyer_post_id,$room_id){
        try {
            $getparticularsdata  = DB::table('relocationint_buyer_post_inventory_particulars as rebip')
                ->join('lkp_inventory_room_particulars as particulars','particulars.id','=','rebip.lkp_inventory_room_particular_id')
                ->where('rebip.buyer_post_id',$buyer_post_id)
                ->where('rebip.lkp_inventory_room_id',$room_id)
                ->select('rebip.lkp_inventory_room_particular_id','rebip.number_of_items','rebip.crating_required','particulars.room_particular_type')
                ->get();
            return $getparticularsdata;
        } catch ( Exception $exc ) {
        }

    }


    /**
     * Seller search buyer posts
     * @author Shriram
     * @param mixed $request
     * @param int $serviceId
     * @return
     */
    public static function getRelocationInternationSellerSearchResults($request, $serviceId)
    {
        try {
            $buyerNames = array();
            $inputparams = $request->all();
            $Query_buyers_for_sellers = SellerSearchComponent::search($roleId = null, $serviceId, $statusId = null, $request);
            $Query_buyers_for_sellers_filter = $Query_buyers_for_sellers->get();
            
            session()->put([
                'searchMod' => [
                    'from_location_relocation'  => $request->from_location,
                    'from_location_id_relocation' => $request->from_location_id,
                    'to_location_relocation'    => $request->to_location,
                    'to_location_id_relocation' => $request->to_location_id,
                    'valid_from_relocation'     => $request->valid_from,
                    'valid_to_relocation'       => $request->valid_to,
                ]
            ]);
            
            if(isset($_REQUEST['seller_district_id']))
                session()->push('searchMod.seller_district_id_relocation', 
                    $_REQUEST['seller_district_id']
                );

            if (empty ($Query_buyers_for_sellers_filter)) {
                //CommonComponent::searchTermsSendMail ();
                Session::put('layered_filter', '');
                Session::put('layered_filter_payments', '');
                Session::put('show_layered_filter', '');
                Session::put('layered_filter_loadtype', '');
            }
            if (!isset($_REQUEST['filter_set'])) {
                // Below script for filter data getting from queries --for filters
                foreach ($Query_buyers_for_sellers_filter as $seller_post_item) {
                    if (!isset ($from_locations [$seller_post_item->from_location_id])) {
                        $from_locations [$seller_post_item->from_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->from_location_id)->pluck('city_name');
                    }
                    if (!isset ($to_locations [$seller_post_item->to_location_id])) {
                        $to_locations [$seller_post_item->to_location_id] = DB::table('lkp_cities')->where('id', $seller_post_item->to_location_id)->pluck('city_name');
                    }
                    if (!isset ($buyerNames [$seller_post_item->buyer_id])) {
                        $buyerNames[$seller_post_item->buyer_id] = $seller_post_item->username;
                        Session::put('layered_filter', $buyerNames);
                    }
                }
            }

            //echo "<pre>"; print_R(Session::get('layered_filter_loadtype')); die;

            //$result = $Query_buyers_for_sellers->get ();

            $gridBuyer = \DataGrid::source($Query_buyers_for_sellers);
            $gridBuyer->add('username', 'Buyer Name', true)->attributes(array("class" => "col-md-2 padding-left-none"));
            $gridBuyer->add('dispatch_date', 'Dispatch Date', true)->attributes(array("class" => "col-md-2 padding-left-none"));
            $gridBuyer->add('property_type', 'Property Type', true)->attributes(array("class" => "col-md-2 padding-left-none"));
            $gridBuyer->add('volume', 'Volume', true)->attributes(array("class" => "col-md-2 padding-left-none"));
            $gridBuyer->add('no_of_items', 'No of Items', true)->attributes(array("class" => "col-md-2 padding-left-none"));
            $gridBuyer->add('grid_actions', 'Grid Actions', true)->attributes(array("class" => "col-md-2 padding-none"))->style("display:none");
            $gridBuyer->add('empty_div', 'Empty', true)->attributes(array("class" => ""))->style("display:none");
            $gridBuyer->add('dom_row', 'Dom Actions', true)->attributes(array("class" => "col-md-3 padding-none"))->style("display:none");
            $gridBuyer->add('quote_submit', 'Dom Action 1', true)->attributes(array("class" => "col-md-3 padding-none"))->style("display:none");
            $gridBuyer->add('quote_details', 'Dom Action 2', true)->attributes(array("class" => "col-md-3 padding-none"))->style("display:none");
            $gridBuyer->add('origin_storage', 'origin_storage', true)->style("display:none");
            $gridBuyer->add('destination_storage', 'destination_storage', true)->style("display:none");
            $gridBuyer->add('origin_handyman_services', 'origin_handyman_services', true)->style("display:none");
            $gridBuyer->add('destination_handyman_services', 'destination_handyman_services', true)->style("display:none");
            $gridBuyer->add('insurance', 'insurance', true)->style("display:none");

            $gridBuyer->row(function ($row) {
                $row->cells[10]->style("display:none");
                $row->cells[11]->style("display:none");
                $row->cells[12]->style("display:none");
                $row->cells[13]->style("display:none");
                $row->cells[14]->style("display:none");
                //dd($row);
                $id = $row->data->id;
                $buyer_id = $row->data->buyer_id;
                $transaction_id = $row->data->transaction_id;

                $sellercomponent = new RelocationOceanSellerComponent();
                $submittedquote = $sellercomponent::getSellerSubmittedQuote(Auth::User()->id, $id);
                $enquiry = $sellercomponent::getBuyerpostById($id);
                $submitedquotetext = (count($submittedquote) == 0) ? "Submit Quote" : "Quote Submitted";

                // Buyer Business name
                $row->cells[0]->attributes(array('class' => 'col-md-2 padding-left-none'))
                    ->value(ucfirst($row->data->username));

                // Dispatch date
                $row->cells[1]->attributes(array('class' => 'col-md-2 padding-left-none'))
                    ->value(CommonComponent::checkAndGetDate($row->data->dispatch_date));
                // Dispatch date
                $row->cells[2]->attributes(array('class' => 'col-md-2 padding-left-none'))
                    ->value($row->data->property_type);

                // Dispatch date
                $volume = $sellercomponent::getVolumeCft($id);
                $row->cells[3]->attributes(array('class' => 'col-md-2 padding-left-none'))
                    ->value($volume." CFT");

                $noOfItems = $sellercomponent::getTotalNoofItems($id);
                $row->cells[4]->attributes(array('class' => 'col-md-2 padding-left-none'))
                    ->value($noOfItems);

                // Action Button
                $row->cells[5]->attributes(array('class' => 'col-md-2 padding-left-none'))
                    ->value("<button class='detailsslide-term btn red-btn pull-right submit-data' id ='$id'>" . $submitedquotetext . "</button>");

                // Empty Div
                $row->cells[6]->attributes(array('class' => 'clearfix'))
                    ->value('');

                // Details & Message Div       
                $row->cells[7]->attributes(array('class' => 'pull-right text-right'))
                    ->value = '<div class="info-links">	
                        <span data-buyersearchlistid="' . $buyer_id . '_' . $id . '" id="' . $id . '" class="show-data-link detailsslide underline_link"><span class="show-icon">+</span><span class="hide-icon">-</span> Details</span>
                        <a href="#" data-userid="' . $buyer_id . '" data-buyer-transaction="' . $transaction_id . '" class="new_message" data-buyerquoteitemidforseller="' . $id . '"><i class="fa fa-envelope-o"></i></a>
                    	</div>';


                $buyerpostdata = array();
                $buyerpostdata['from_location_id'] = $enquiry->from_location_id;
                $buyerpostdata['to_location_id'] = $enquiry->to_location_id;
                $buyerpostdata['valid_from'] = $enquiry->dispatch_date;
                $buyerpostdata['valid_to'] = date('Y-m-d', strtotime($enquiry->dispatch_date. " + 1 days"));
                $buyerpostdata['nquiry_volume'] = $volume;
               
                $SubmitquotePartial = view('relocationint.sellers.seller_search_submit_quote')->with([
                		'submittedquote' => $submittedquote,
                		'enquiry'=>$enquiry,
                		'id' => $id,
                		'is_search' => 1,
                		'search_params' => $buyerpostdata,
                		'international_type' => $enquiry->lkp_international_type_id,
                        'lkp_international_type_id' => $enquiry->lkp_international_type_id
                ])->render();
                

                $row->cells[8]->attributes(array('class' => 'col-md-12 submit-data-div padding-none padding-top'))->value = $SubmitquotePartial;

                $row->cells[9]->attributes(array('class' => 'col-md-12 show-data-div padding-none padding-top'));
                $originstoreatechecked = ($row->cells[10]->value == 1) ? 'checked="checked"' : '';
                $originhandymanatechecked = ($row->cells[11]->value == 1) ? 'checked="checked"' : '';
                $destinationstoreatechecked = ($row->cells[12]->value == 1) ? 'checked="checked"' : '';
                $destinationhandymanechecked = ($row->cells[13]->value == 1) ? 'checked="checked"' : '';
                $insurancechecked = ($row->cells[14]->value == 1) ? 'checked="checked"' : '';
                $particularsDataCount=$sellercomponent::getBuyerInventoryParticularsDataInfo($id);
                if($particularsDataCount>0) {
                    $roomTypes = $sellercomponent->getBuyerInventoryRoomsbyId($id);
                    foreach ($roomTypes as $roomType) {
                        $particularsData = $sellercomponent::getBuyerInventoryParticularsbyId($id, $roomType->lkp_inventory_room_id);
                        $roomdata = '<div class="col-md-12 padding-left-none data-fld">
							                <span class="data-head">Room Type: ' . $roomType->inventory_room_type . '</span>
							            </div>
							            <div class="clearfix"></div>
							            <div class="table-div">
							                <div class="table-heading inner-block-bg">
								                <div class="col-md-3 padding-left-none">Particular</div>
								                <div class="col-md-3 padding-left-none">Number of Items</div>
								                <div class="col-md-3 padding-left-none">Crating</div>
								                <div class="col-md-3 padding-left-none"></div>
							                </div>
							<div class="table-data">';
                        foreach ($particularsData as $particularData) {
                            $cratingRequired = ($particularData->crating_required == 1) ? "Yes" : "No";
                            $roomdata .= '<div class="table-row inner-block-bg">
                                            <div class="col-md-3 padding-left-none">'.$particularData->room_particular_type.'</div>
                                            <div class="col-md-3 padding-left-none">'.$particularData->number_of_items.'</div>
                                            <div class="col-md-3 padding-left-none">'.$cratingRequired.'</div>
                                            <div class="col-md-3 padding-left-none"></div>
                                        </div>';
                        }
                        $roomdata .= '</div>
							</div>';
                    }
                    $particulars = '<div class="col-md-12">'.$roomdata.'</div>';
                }else{
                    $particulars = '<div class="col-md-12">NA</div>';
                }

                $row->cells[9]->value = '<div class="col-md-12 padding-none">
                                            <div class="col-md-4 form-control-fld">
                                                <div class="radio-block"><input type="checkbox" '.$originstoreatechecked.' readonly> <span class="lbl padding-8">Storage</span></div>
                                                <div class="radio-block"><input type="checkbox" '.$originhandymanatechecked.' readonly> <span class="lbl padding-8">Handyman Services</span></div>
                                                <div class="radio-block"><input type="checkbox" '.$insurancechecked.' readonly> <span class="lbl padding-8">Insurance</span></div>
                                            </div>
                                            <div class="col-md-4 form-control-fld">
                                                <div class="radio-block"><input type="checkbox" '.$destinationstoreatechecked.' readonly> <span class="lbl padding-8">Storage</span></div>
                                                <div class="radio-block"><input type="checkbox" '.$destinationhandymanechecked.' readonly> <span class="lbl padding-8">Handyman Services</span></div>
                                            </div>

								<div class="col-md-12 padding-none">
                                    <h2 class="filter-head1 margin-bottom">Complete Inventory</h2>
                                    '.$particulars.'
                                    <div class="clearfix"></div>
								</div>	
							</div>';


                $row->attributes(array("class" => ""));

            });

            $gridBuyer->orderBy('id', 'desc');
            $gridBuyer->paginate(5);

            $result = array();
            $result ['gridBuyer'] = $gridBuyer;
            //$result ['filter'] = $filter;
            return $result;

        } catch (Exception $exc) {
        }
    }

    public static function getAirBuyerPostCartons($postId)
    {
        $postCartons = DB::table('relocationint_buyer_post_air_cartons as rbac')
            ->leftjoin('lkp_air_carton_types as lact', 'lact.id', '=', 'rbac.lkp_air_carton_type_id')
            ->select('lact.carton_type', 'lact.carton_description', 'lact.weight', 'rbac.number_of_cartons')->where('rbac.buyer_post_id', $postId)->get();
        return $postCartons;
    }

    public static function getAirBuyerPostCartonsCFT($postId)
    {
        $postCartons = RelocationOceanSellerComponent::getAirBuyerPostCartons($postId);
        if (count($postCartons) > 0) {
            $weight = 0;
            foreach ($postCartons as $postCarton) {
                $weight += $postCarton->number_of_cartons * $postCarton->weight;
            }
            $cft = ($weight * 3000) / 1728;
            return number_format($cft, 2) . " CFT";
        }
        return "0 CFT";
    }

    /**
     * @param $id
     */
    public static function SellerPostDetails($id)
    {
        Session::put('seller_post_item', $id);
        $postinfo = array();
        $postDetails = DB::table('relocationpet_seller_posts')->select('*')->where('id', $id)->get();
        $postItemDetails = DB::table('relocationpet_seller_post_items')->select('*')->where('seller_post_id', $id)->get();
        
        $postinfo['seller_post'] = $postDetails;
        $postinfo['seller_post_items'] = $postItemDetails;
        return $postinfo;
    }

    public static function getPrivateBuyers($id, $lkp_access_id)
    {
        if ($lkp_access_id == 2 || $lkp_access_id == 3) {
            $privatebuyers = DB::table('relocationpet_seller_selected_buyers as rssb')
                ->leftjoin('relocationpet_seller_posts as rsp', 'rsp.id', '=', 'rssb.seller_post_id')
                ->leftjoin('users', 'users.id', '=', 'rssb.buyer_id')
                ->leftjoin('buyer_details', 'buyer_details.user_id', '=', 'users.id')
                ->where('rssb.created_by', Auth::user()->id)
                ->where('rssb.seller_post_id', $id)
                ->select('users.username')
                ->get();
            return $privatebuyers;
        } else {
            return array();
        }
    }

    /**
     * Submitting Seller Initial Quote
     *
     * @param  $request
     * @return Response
     */
    public static function DomesticSellerQuoteSubmit($request)
    {

        try {
            $roleId = Auth::User()->lkp_role_id;
            if ($roleId == SELLER) {
                CommonComponent::activityLog("SELLER_SUBMIT_QUOTE",
                    SELLER_SUBMIT_QUOTE, 0,
                    HTTP_REFERRER, CURRENT_URL);
            }
            $sellerInput = Input::all();
            //print_r($sellerInput);exit;
            //if(isset($_REQUEST['from_location_id'])){
            $buyerId = $_REQUEST['buyerid'];


            if (isset($sellerInput['seller_post_item_id'])) {
                $getSellerpostdetails = DB::table('relocationpet_seller_post_items as rspi')
                    ->leftjoin('relocationpet_seller_posts as rsp', 'rsp.id', '=', 'rspi.seller_post_id')
                    ->where('rsp.id', '=', $sellerInput['seller_post_item_id'])
                    ->where('rsp.created_by', '=', Auth::user()->id)
                    ->select('rsp.*', 'rspi.*')
                    ->get();
            }

            //Seller post create
            $postid = CommonComponent::getSellerPostID(Session::get('service_id'));
            $created_year = date('Y');
            $randnumber = 'RELOCATIONPET/' . $created_year . '/' . str_pad($postid, 6, "0", STR_PAD_LEFT);


            date_default_timezone_set("Asia/Kolkata");
            $created_at = date('Y-m-d H:i:s');
            $nowdate = date('Y-m-d');
            $Date1 = date('Y-m-d', strtotime($nowdate . " + 1 days"));
            $createdIp = $_SERVER['REMOTE_ADDR'];
            $createsellerpost = new RelocationpetSellerPost();

            $createsellerpost->lkp_service_id = RELOCATION_PET_MOVE;
            $createsellerpost->from_date = $nowdate;
            $createsellerpost->to_date = $Date1;
            //$createsellerpost->rate_card_type =$sellerInput ['post_rate_card_type'];
            $createsellerpost->from_location_id = $_POST ['from_location_id'];
            $createsellerpost->to_location_id = $_POST ['to_location_id'];
            $createsellerpost->seller_district_id = CommonComponent::getDistrict($_POST ['from_location_id'], RELOCATION_DOMESTIC);

            if (!isset($sellerInput['seller_post_item_id'])) {
                $createsellerpost->tracking = $sellerInput['tracking'];
                if ($sellerInput['paymentoptions'] == 1) {
                    $createsellerpost->lkp_payment_mode_id = 1;
                    $createsellerpost->accept_payment_netbanking = 1;
                    $createsellerpost->accept_payment_credit = 1;
                    $createsellerpost->accept_payment_debit = 1;
                } else if ($sellerInput['paymentoptions'] == 2) {
                    $createsellerpost->lkp_payment_mode_id = 2;
                } else if ($sellerInput['paymentoptions'] == 3) {
                    $createsellerpost->lkp_payment_mode_id = 3;
                } else {
                    if (!isset($sellerInput['credit_peroid'])) {
                        $createsellerpost->lkp_payment_mode_id = 4;
                        $createsellerpost->accept_credit_netbanking = 1;
                    } else {
                        $createsellerpost->lkp_payment_mode_id = 4;
                        $createsellerpost->accept_credit_netbanking = 1;
                        $createsellerpost->accept_credit_cheque = 1;
                        $createsellerpost->credit_period = $sellerInput['credit_peroid'];
                        $createsellerpost->credit_period_units = $sellerInput['credit_period_units'];
                    }
                }
                $createsellerpost->terms_conditions = "";
            } else {

                $createsellerpost->tracking = $getSellerpostdetails[0]->tracking;
                $createsellerpost->terms_conditions = $getSellerpostdetails[0]->terms_conditions;
                $createsellerpost->lkp_payment_mode_id = $getSellerpostdetails[0]->lkp_payment_mode_id;
                $createsellerpost->accept_payment_netbanking = $getSellerpostdetails[0]->accept_payment_netbanking;
                $createsellerpost->accept_payment_credit = $getSellerpostdetails[0]->accept_payment_credit;
                $createsellerpost->accept_payment_debit = $getSellerpostdetails[0]->accept_payment_debit;
                $createsellerpost->credit_period = $getSellerpostdetails[0]->credit_period;
                $createsellerpost->credit_period_units = $getSellerpostdetails[0]->credit_period_units;
                $createsellerpost->accept_credit_netbanking = $getSellerpostdetails[0]->accept_credit_netbanking;
                $createsellerpost->accept_credit_cheque = $getSellerpostdetails[0]->accept_credit_cheque;


            }
            $createsellerpost->seller_id = Auth::user()->id;
            $createsellerpost->lkp_post_status_id = 2;
            $createsellerpost->cancellation_charge_price = '0.00';
            $createsellerpost->docket_charge_price = '0.00';
            $createsellerpost->transaction_id = $randnumber;
            $createsellerpost->lkp_access_id = 2;
            $createsellerpost->created_at = $created_at;
            $createsellerpost->created_by = Auth::user()->id;
            $createsellerpost->created_ip = $createdIp;
            $createsellerpost->save();

            //Post Item create
            $sellerpost_lineitem = new RelocationpetSellerPostItem();
            $sellerpost_lineitem->seller_post_id = $createsellerpost->id;
            //$sellerpost_lineitem->rate_card_type = $_POST['post_rate_card_type'];

            $sellerpost_lineitem->lkp_pet_type_id = $_REQUEST['pet_type'];
            $sellerpost_lineitem->lkp_cage_type_id = $_REQUEST['cage_type'];
            $sellerpost_lineitem->od_charges = $_REQUEST['od_charges'];
            $sellerpost_lineitem->rate_per_cft = $_REQUEST['freight'];

            $sellerpost_lineitem->transitdays = $_REQUEST['transport_days'];
            $sellerpost_lineitem->units = "Days";
            $sellerpost_lineitem->od_charges = $_REQUEST['od_charges'];
            $sellerpost_lineitem->is_private = 1;
            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER ['REMOTE_ADDR'];
            $sellerpost_lineitem->created_by = Auth::id();
            $sellerpost_lineitem->created_at = $created_at;
            $sellerpost_lineitem->created_ip = $createdIp;
            $sellerpost_lineitem->save();
            SellerMatchingComponent::insetOrUpdateMatches(RELOCATION_PET_MOVE, $createsellerpost->id, 2, array($sellerInput['buyerquote_id']));

            //Private buyer selection
            $created_at = date('Y-m-d H:i:s');
            $createdIp = $_SERVER['REMOTE_ADDR'];
            $sellerbuyerselect = new RelocationpetSellerSelectedBuyer();
            $sellerbuyerselect->seller_post_id = $createsellerpost->id;
            $sellerbuyerselect->buyer_id = $buyerId;
            $sellerbuyerselect->created_by = Auth::user()->id;
            $sellerbuyerselect->created_at = $created_at;
            $sellerbuyerselect->created_ip = $createdIp;
            $sellerbuyerselect->save();
            $_REQUEST['seller_post_item_id'] = $createsellerpost->id;
            //}
            //}


            if (isset($sellerInput['buyerquote_id']) && !empty($sellerInput['buyerquote_id'])) {

                date_default_timezone_set("Asia/Kolkata");
                $created_at = date('Y-m-d H:i:s');
                $initial_cretaed = date('Y-m-d H:i:s');
                $createdIp = $_SERVER['REMOTE_ADDR'];
                $sellerinitialquote = new RelocationpetBuyerQuoteSellersQuotesPrice();
                $sellerinitialquote->lkp_service_id = RELOCATION_PET_MOVE;
                $sellerinitialquote->buyer_id = $_REQUEST['buyerid'];;
                $sellerinitialquote->buyer_quote_id = $_REQUEST['buyerquote_id'];
                $sellerinitialquote->seller_id = Auth::user()->id;
                $sellerinitialquote->seller_post_id = (isset($_REQUEST['seller_post_item_id']) ? $_REQUEST['seller_post_item_id'] : 0);
                $sellerinitialquote->private_seller_quote_id = $sellerpost_lineitem->id;
                $sellerinitialquote->post_lead_type_id = 1;
                $sellerinitialquote->rate_per_cft = $_REQUEST['freight'];
                $sellerinitialquote->doortodoor_charges = $_REQUEST['od_charges'];
                $sellerinitialquote->transit_days = $_REQUEST['transport_days'];
                $sellerinitialquote->total_price = $_REQUEST['total_price'];

                $sellerinitialquote->save();
                //CommonComponent::auditLog($sellerinitialquote->id,'courier_buyer_quote_sellers_quotes_prices');

                $seller_initial_quote_email = DB::table('users')->where('id', $_REQUEST['buyerid'])->get();
                $seller_initial_quote_email[0]->sellername = Auth::User()->username;

                CommonComponent::send_email(INITIAL_COUNT_BY_SELLER, $seller_initial_quote_email);

                //*******Send Sms to the buyers,from seller submit a quote ***********************//
                if (isset($getSellerpostdetails[0]->transaction_id)) {
                    $msg_params = array(
                        'randnumber' => $getSellerpostdetails[0]->transaction_id,
                        'sellername' => Auth::User()->username,
                        'servicename' => 'RELOCATION PET MOVE'
                    );
                    $getMobileNumber = CommonComponent::getMobleNumber($buyerId);
                    CommonComponent::sendSMS($getMobileNumber, SELLER_SUBMITT_QOUTE_SMS, $msg_params);
                }
                //*******Send Sms to the buyers,from seller submit a quote ***********************//

            }

            Session::put('message', 'Quote submitted successfully');

        } catch (Exception $e) {
            //return $e->message;
        }
        return true;
        //return Redirect::back();
    }

    public static function getSellerpostEnquiries($postId, $type)
    {
        if ($type == 1) {
            $matchedposts = SellerMatchingComponent::getMatchedResults(RELOCATION_PET_MOVE, $postId);
        } else {
            $matchedposts = SellerMatchingComponent::getSellerLeads(RELOCATION_PET_MOVE, $postId);
        }

        if (count($matchedposts) > 0) {
            $buyerposts = array();
            foreach ($matchedposts as $matchedpost) {
                $buyerposts[] = $matchedpost->buyer_quote_id;
            }

            $Query_buyers_for_sellers = DB::table('relocationpet_buyer_posts as rbq');
            $Query_buyers_for_sellers->leftjoin('lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id');
            $Query_buyers_for_sellers->leftjoin('lkp_cities as ct', 'rbq.to_location_id', '=', 'ct.id');
            $Query_buyers_for_sellers->leftjoin('users as us', 'us.id', '=', 'rbq.buyer_id');
            $Query_buyers_for_sellers->leftjoin('lkp_pet_types as pt', 'pt.id', '=', 'rbq.lkp_pet_type_id');
            $Query_buyers_for_sellers->leftjoin('lkp_cage_types as lct', 'lct.id', '=', 'rbq.lkp_cage_type_id');
            $Query_buyers_for_sellers->leftjoin('relocationpet_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id');
            //$Query_buyers_for_sellers->where('rbq.lkp_post_status_id', OPEN);
            $Query_buyers_for_sellers->whereIn('rbq.id', $buyerposts);
            $Query_buyers_for_sellers->select('rbq.*', 'us.username', 'cf.city_name as fromcity', 'ct.city_name as tocity', 'pt.pet_type', 'lct.cage_type');
            $Query_buyers_for_sellers->groupBy('rbq.id');
            $results = $Query_buyers_for_sellers->get();

            return $results;
        }
    }

    public static function getBuyerpostById($postId)
    {

        $Query_buyers_for_sellers = DB::table('relocationint_buyer_posts as rbq');
        $Query_buyers_for_sellers->join('lkp_cities as cf', 'rbq.from_location_id', '=', 'cf.id');
        $Query_buyers_for_sellers->join('lkp_cities as ct', 'rbq.to_location_id', '=', 'ct.id');
        $Query_buyers_for_sellers->join('users as us', 'us.id', '=', 'rbq.buyer_id');
        $Query_buyers_for_sellers->leftjoin('relocationint_buyer_selected_sellers as pbqss', 'pbqss.buyer_post_id', '=', 'rbq.id');
        $Query_buyers_for_sellers->where('rbq.lkp_post_status_id', OPEN);
        $Query_buyers_for_sellers->where('rbq.id', $postId);
        $Query_buyers_for_sellers->select('rbq.*', 'us.username', 'cf.city_name as fromcity', 'ct.city_name as tocity');
        $Query_buyers_for_sellers->groupBy('rbq.id');
        $results = $Query_buyers_for_sellers->get();
        return $Query_buyers_for_sellers->first();

    }

    public static function getSellerSubmittedQuote($seller_id, $buyerquoteId, $sellerPostId = 0)
    {
        $Query_buyers_for_sellers = DB::table('relocationint_buyer_quote_sellers_quotes_prices as sqbqp')
            ->where("sqbqp.seller_id", $seller_id)
            ->where("sqbqp.total_price", '>', 0)
            ->where("sqbqp.buyer_quote_id", $buyerquoteId);
        if ($sellerPostId != 0) {
            //$Query_buyers_for_sellers->where("sqbqp.seller_post_id",$sellerPostId);
        }
        $data = $Query_buyers_for_sellers->get();
        return $data;
    }

}
