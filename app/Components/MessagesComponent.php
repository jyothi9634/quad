<?php namespace App\Components;

use Auth;

use DB;
use Input;
use Session;

use App\Models\PtlSellerPostItemView;
use App\Models\SellerPostItemView;
use Illuminate\Support\Facades\Mail;

use Zofe\Rapyd\Facades\DataGrid;
use App\Models\FtlSearchTerm;
use App\Models\IctSearchTerm;
use App\Models\User;
use App\Models\OceanSellerPostItemView;
use App\Models\RailSellerPostItemView;
use App\Models\AirintSellerPostItemView;
use App\Models\UserMessage;
use App\Models\UserMessageUpload;
use App\Models\AirdomSellerPostItemView;
use App\Components\Search\SellerSearchComponent;

class MessagesComponent{
    public static function getAllMessages($receiverId) {
        try {
                $getAllMessages = DB::table('user_messages as um')
                    ->leftjoin('lkp_message_types as tmy', 'tmy.id', '=', 'um.lkp_message_type_id')
                    ->leftjoin('users as u', 'u.id', '=', 'um.sender_id')
                    ->where('um.recepient_id', '=',$receiverId)
                    ->select('um.id', 'um.lkp_service_id','um.recepient_id','um.sender_id','um.post_id','tmy.message_type',
                            'um.order_id', 'um.lkp_message_type_id', 'um.subject','um.message','um.created_at',
                            'um.is_read', 'um.is_reminder', 'um.is_notified','um.is_general','u.username')
                    ->get();
                return $getAllMessages;
            } catch(\Exception $e) {
                    //return $e->message;
            }
    }

    /**
    * Get all message type
    */
    public static function getMessageTypes() {
        try{
//            CommonComponent::activityLog("GET_STATE", GET_STATE, 0, HTTP_REFERRER, CURRENT_URL);
            $messageTypes = [];
            $messageTypes[0] = 'Messages (ALL)';
            $roleId = Auth::User()->lkp_role_id;
            $messageType = DB::table('lkp_message_types');
            if($roleId == BUYER){
                $messageType->where('is_buyer', '1');
            } else if($roleId == SELLER){
                $messageType->where('is_seller', '1');
            }
            $messageType->where('is_active', '1');
            $messageType->orderBy('message_type', 'asc');
            $messageType->where('is_active', '1');
            $messageType = $messageType->lists('message_type', 'id');
            foreach ($messageType as $id => $message) {
                $messageTypes[$id] = $message;
            }
            return $messageTypes;
        } catch (Exception $ex) {

        }
    }
    
    
    public static function listMessages($request=null,$message_types = GENERALMESSAGETYPE, $receiverId = null,$id=null,$term=0){
        try {
            if(isset($request['message_types']) && $request['message_types']!=""){
                Session::put('message_types',$request['message_types']);
            }if(isset($request['message_services']) && $request['message_services']!=""){
                Session::put('message_services',$request['message_services']);
            }if(isset($request['message_keywords']) && $request['message_keywords']!=""){
                Session::put('message_keywords',$request['message_keywords']);
            }if(isset($request['from_message']) && $request['from_message']!=""){
                Session::put('from_message',$request['from_message']);
            }if(isset($request['to_message']) && $request['to_message']!=""){
                Session::put('to_message',$request['to_message']);
            }
            if(empty($request)){
                Session::put('message_types','');
                Session::put('message_services','');
                Session::put('message_keywords','');
                Session::put('from_message','');
                Session::put('to_message','');
            }
            //if(user not selected any service ie. For home page){
            $roleId = Auth::User()->lkp_role_id;
            $serviceId = Session::get('service_id');
            if(empty(Auth::User()->id)) {
                return ['success' => '0','error' => 'Oops something went wrong.'];
            }
            $getAllMessagesQuery = DB::table('user_messages as um');
            $getAllMessagesQuery->leftjoin('lkp_message_types as lmy', 'lmy.id', '=', 'um.lkp_message_type_id');
            $getAllMessagesQuery->leftjoin('users as u', 'u.id', '=', 'um.sender_id');
            if($message_types!=GENERALMESSAGETYPE)
            $getAllMessagesQuery->where('um.is_term', $term);
            if (!empty($receiverId) && $receiverId != '0' && $receiverId != ''){
                $getAllMessagesQuery->where('um.recepient_id', $receiverId);
            }else{
                $getAllMessagesQuery->where('um.recepient_id', Auth::User()->id);
            }
            if (!empty($request['message_services']) && $request['message_services'] != '0' && $request['message_services'] != ''){
                $getAllMessagesQuery->where('um.lkp_service_id', $request['message_services']);
            }//echo "here".strpos($_SERVER['REQUEST_URI'],"messages");
            
            if(strpos($_SERVER['REQUEST_URI'],"messages")!=1){//echo "here";exit;
                $getAllMessagesQuery->where('um.lkp_service_id', $serviceId);
            }
            if (!empty($request['message_types']) && $request['message_types'] != '0' && $request['message_types'] != ''){
                //$getAllMessagesQuery->where('um.lkp_message_type_id', $request['message_types']);
                if($request['message_types']==POSTMESSAGETYPE )
                    $getAllMessagesQuery->whereRaw('(um.lkp_message_type_id ='.POSTQUOTEMESSAGETYPE.' or `um`.`lkp_message_type_id` = '.POSTENQURYMESSAGETYPE.' or `um`.`lkp_message_type_id` = '.LEADSMESSAGETYPE.')');
                else
                    $getAllMessagesQuery->where('um.lkp_message_type_id', $request['message_types']);
            }elseif (!empty($message_types) && $message_types != '1'  && $message_types != '2' && $message_types != '4' && $message_types != '5'){
                $getAllMessagesQuery->where('um.lkp_message_type_id', $message_types);
            }
            
            
            if($message_types==ORDERMESSAGETYPE){
                $getAllMessagesQuery->where('um.order_id', $id);
            }elseif($message_types==POSTMESSAGETYPE){
                if($serviceId==ROAD_FTL){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('seller_posts as sp')
                        ->leftjoin('seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                                ->where('sp.id','=',$id)->select('spi.id')->get();
                        //echo $id;print_r($getpostItems);exit;
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }elseif($serviceId==ROAD_PTL){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('ptl_seller_posts as sp')
                        ->leftjoin('ptl_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                                ->where('sp.id','=',$id)->select('spi.id')->get();
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }elseif($serviceId==RAIL){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('rail_seller_posts as sp')
                        ->leftjoin('rail_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                                ->where('sp.id','=',$id)->select('spi.id')->get();
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }elseif($serviceId==AIR_DOMESTIC){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('airdom_seller_posts as sp')
                        ->leftjoin('airdom_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                                ->where('sp.id','=',$id)->select('spi.id')->get();
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }elseif($serviceId==AIR_INTERNATIONAL){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('airint_seller_posts as sp')
                        ->leftjoin('airint_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                                ->where('sp.id','=',$id)->select('spi.id')->get();
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }elseif($serviceId==OCEAN){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('ocean_seller_posts as sp')
                        ->leftjoin('ocean_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                                ->where('sp.id','=',$id)->select('spi.id')->get();
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }elseif($serviceId==RELOCATION_DOMESTIC){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('relocation_seller_posts as sp')
                        ->leftjoin('relocation_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                                ->where('sp.id','=',$id)->select('sp.id')->get();
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }elseif($serviceId==RELOCATION_OFFICE_MOVE){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('relocationoffice_seller_posts as sp')
                                ->where('sp.id','=',$id)->select('sp.id')->get();
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }elseif($serviceId==RELOCATION_PET_MOVE){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('relocationpet_seller_posts as sp')
                        ->leftjoin('relocationpet_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                                ->where('sp.id','=',$id)->select('sp.id')->get();
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }elseif($serviceId==RELOCATION_INTERNATIONAL){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('relocationint_seller_posts as sp')
                                            ->where('sp.id','=',$id)->select('sp.id')->get();
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }elseif($serviceId==RELOCATION_GLOBAL_MOBILITY){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('relocationgm_seller_posts as sp')
                                            ->where('sp.id','=',$id)->select('sp.id')->get();
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }
                elseif($serviceId==COURIER){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('courier_seller_posts as sp')
                        ->leftjoin('courier_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                                ->where('sp.id','=',$id)->select('spi.id')->get();
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }elseif($serviceId==ROAD_TRUCK_HAUL){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('truckhaul_seller_posts as sp')
                        ->leftjoin('truckhaul_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                                ->where('sp.id','=',$id)->select('spi.id')->get();
                        //echo $id;print_r($getpostItems);exit;
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }elseif($serviceId==ROAD_TRUCK_LEASE){
                    if($roleId == BUYER){
                        $getAllMessagesQuery->where('um.enquiry_id', $id);
                    }elseif($roleId == SELLER){
                        $matchedIds = array();
                        $getpostItems = DB::table('trucklease_seller_posts as sp')
                        ->leftjoin('trucklease_seller_post_items as spi', 'spi.seller_post_id', '=', 'sp.id')
                                ->where('sp.id','=',$id)->select('spi.id')->get();
                        //echo $id;print_r($getpostItems);exit;
                        foreach($getpostItems as $getpostItem){
                        $matchedIds[] = $getpostItem->id;
                        }
                        $getAllMessagesQuery->whereIn('um.enquiry_id', $matchedIds);
                    }
                }
            }elseif($message_types==POSTENQURYMESSAGETYPE){
                
                $getAllMessagesQuery->whereRaw('(um.enquiry_id ='.$id.' or `um`.`lead_id` = '.$id.')');
                
            }
            if (!empty($request['message_keywords'])){
                
                $str='%'.$request['message_keywords'].'%';
                
                $getAllMessagesQuery->where(function ($getAllMessagesQuery)use ($str) {
             $getAllMessagesQuery->where('um.subject', 'LIKE', $str)
                   ->orWhere('um.message', 'LIKE', $str);
       });
            }
            
            if (isset ( $request ['from_message'] ) && $request ['from_message'] != '') {
                $getAllMessagesQuery->where ( 'um.created_at', '>=', CommonComponent::convertDateTimeForDatabase($request ['from_message'],'00:00:00') );

            }
            if (isset ( $request ['to_message'] ) && $request ['to_message'] != '') {
                $getAllMessagesQuery->where ( 'um.created_at', '<=', CommonComponent::convertDateTimeForDatabase($request ['to_message'],'23:59:59') );

            }
            
            $getAllMessagesQuery->select('um.id', 'um.lkp_service_id','um.recepient_id','um.sender_id','um.post_id','lmy.message_type',
                                'um.order_id', 'um.lkp_message_type_id', 'um.subject','um.message','um.created_at',
                                'um.is_read', 'um.is_reminder', 'um.is_notified','um.is_general','u.username','um.is_term');
            $getAllMessages = $getAllMessagesQuery->get();
            
            $grid = DataGrid::source($getAllMessagesQuery);
            $grid->attributes(array("class" => "table table-striped"));
            $grid->add('id', 'ID', false)->style('display:none');
            $grid->add('message_type', 'Type', 'message_type')->attributes(array("class" => "col-md-3 padding-left-none",'style'=>'display:none'));
            $grid->add('username', 'Sender', 'username')->attributes(array("class" => "col-md-3 padding-left-none"));
            $grid->add('subject', 'Subject', 'subject')->attributes(array("class" => "col-md-3 padding-left-none"));
            $grid->add('created_at', 'Date', 'created_at')->attributes(array("class" => "col-md-3 padding-left-none"));
            $grid->add('is_term', 'Action', 'is_term')->attributes(array("class" => "col-md-3 padding-left-none"));
            $grid->add('sender_id', 'sender_id', 'sender_id')->style('display:none');
            $grid->add('is_read', 'Is Read', 'is_read')->style('display:none');
            $grid->orderBy('id', 'desc');
            $grid->paginate(5);
            
            $grid->row(function ($row) {
            $msg_id = $row->cells [0]->value;
            
            $is_term = $row->cells [5]->value;
            $sender_id = $row->cells [6]->value;
            $subject = $row->cells [3]->value;
            $row->cells[0]->value = '<a href=/getmessagedetails/'.$msg_id.'/0/'.$is_term.'>';
            $uname  =     $row->cells[2]->value;
            $row->cells [0]->style('display:none');
            $row->cells [6]->style('display:none');
            $row->cells [7]->style('display:none');
            $isread = $row->cells [7]->value;
            $row->cells [1]->attributes(array("class" => "col-md-2 padding-left-none",'style'=>'display:none'));
            $row->cells [3]->attributes(array("class" => "col-md-3 padding-left-none"));
            $row->cells [4]->attributes(array("class" => "col-md-3 padding-left-none"));
            $row->cells [5]->attributes(array("class" => "col-md-3 padding-left-none"));
             
            if($isread == 0){
            	$row->cells[2]->value ='<div style="width:100%"><div class="table-row "><div class="col-md-3 padding-left-none"><b>
                '.$uname.'</b>';
            }
            else{
            	$row->cells[2]->value ='<div style="width:100%"><div class="table-row "><div class="col-md-3 padding-left-none">'.$uname;
            }
            
            if($isread == 0){
            	$row->cells [3]->value = '<b>'.$subject.'</b>';
            }else{
            	$row->cells [3]->value = $subject;
            }
            
            if($row->cells [4]->value=="0000-00-00 00:00:00")
            	if($isread == 1){
                	$row->cells [4]->value="NA";
	            }else{
	            	$row->cells [4]->value='NA';
	            }
            else
            	if($isread == 1){
                	$row->cells [4]->value = CommonComponent::checkAndGetDateTime($row->cells [4]->value);
            	}else{
            		$row->cells [4]->value = '<b>'.CommonComponent::checkAndGetDateTime($row->cells [4]->value).'</b>';
            	}
            $row->cells [4]->value .="</a>";
            
            if($isread == 1){
            		$row->cells [5]->value = '<a href="#" class="new_message" data-userid="'.$sender_id.'" data-subject="'.$subject.'" data-msgid="'.$msg_id.'">Reply</a></div></div></div>';
            }else{
            		$row->cells [5]->value = '<a href="#" class="new_message" data-userid="'.$sender_id.'" data-subject="'.$subject.'" data-msgid="'.$msg_id.'"><b>Reply</b></a></div></div></div>';
            }
            
            
            $row->attributes(array("class" => "","style"=>"width:100%"));
            });
            $result = array();
            $result['grid'] = $grid;
            $result['result'] = $getAllMessages;
            
            return $result;
            
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }

    public static function getPerticularMessageDetails($messageId=null,$ordid=null,$term=0){
        try {
            
        	if($messageId!= 0){
        	$updatefinal = DB::table('user_messages')
			        	 ->where('user_messages.id','=',$messageId)
			        	 ->where('user_messages.recepient_id','=',Auth::user()->id)
			        	 ->update(array('is_read' =>1));
        	}
        	if($ordid!= 0){
        		$updatefinal = DB::table('user_messages');
                        if($term==1)  
                            $updatefinal->where('user_messages.contract_id','=',$ordid);
                        else
        		$updatefinal->where('user_messages.order_id','=',$ordid);
        		$updatefinal->where('user_messages.recepient_id','=',Auth::user()->id)
        		->update(array('is_read' =>1));
        	}
        	
            $getMessagesQuery = DB::table('user_messages as um');
            $getMessagesQuery->leftjoin('lkp_message_types as lmy', 'lmy.id', '=', 'um.lkp_message_type_id');
            $getMessagesQuery->leftjoin('user_message_uploads as umu', 'umu.user_message_id', '=', 'um.id');
            $getMessagesQuery->leftjoin('users as u', 'u.id', '=', 'um.sender_id');
            $getMessagesQuery->leftjoin('users as ur', 'ur.id', '=', 'um.recepient_id');
            $getMessagesQuery->where('um.is_term', $term);
            if($ordid!=0 && $ordid!=""){
                if($term==1)
                    $getMessagesQuery->where('um.contract_id', $ordid);
                else
                    $getMessagesQuery->where('um.order_id', $ordid);
            }else{
                $getMessagesQuery->where('um.id', $messageId);
                $getMessagesQuery->orwhere('um.actual_parent_message_id', $messageId);
            }
            $getMessagesQuery->select('um.id', 'um.lkp_service_id','um.recepient_id','um.sender_id','um.post_id','lmy.message_type',
                                'um.order_id', 'um.lkp_message_type_id', 'um.subject','um.message','um.created_at',
                                'um.is_read', 'um.is_reminder', 'um.is_notified','um.is_general','u.username as from','ur.username as to','um.message_no','umu.filepath','umu.name','um.actual_parent_message_id')
                    ->groupBy('um.id');
            $messageDetails = $getMessagesQuery->get();
            foreach($messageDetails as $msg){
                if($msg->actual_parent_message_id!=0){
                    $messageId=$msg->actual_parent_message_id;
                    $getMessagesQuery = DB::table('user_messages as um');
                    $getMessagesQuery->leftjoin('lkp_message_types as lmy', 'lmy.id', '=', 'um.lkp_message_type_id');
                    $getMessagesQuery->leftjoin('user_message_uploads as umu', 'umu.user_message_id', '=', 'um.id');
                    $getMessagesQuery->leftjoin('users as u', 'u.id', '=', 'um.sender_id');
                    $getMessagesQuery->leftjoin('users as ur', 'ur.id', '=', 'um.recepient_id');
                    $getMessagesQuery->where('um.is_term', $term);
                    if($ordid!=0 && $ordid!=""){
                         if($term==1)
                            $getMessagesQuery->where('um.contract_id', $ordid);
                        else
                            $getMessagesQuery->where('um.order_id', $ordid);
                    }else{
                        $getMessagesQuery->whereRaw('(um.id ='.$messageId.' or `um`.`actual_parent_message_id` = '.$messageId.')');
                    }
                    $getMessagesQuery->select('um.id', 'um.lkp_service_id','um.recepient_id','um.sender_id','um.post_id','lmy.message_type',
                                        'um.order_id', 'um.lkp_message_type_id', 'um.subject','um.message','um.created_at',
                                        'um.is_read', 'um.is_reminder', 'um.is_notified','um.is_general','u.username as from','ur.username as to','um.message_no','umu.filepath','umu.name','um.parent_message_id')
                            ->groupBy('um.id');
                    $messageDetails = $getMessagesQuery->get();
                    return $messageDetails;
                }else{
                    return $messageDetails;
                }
            }
            
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    
    public static function getActualMessageDetails($messageId=null,$ordid=null,$term=0){
        try {
            
        	if($messageId!= 0){
        	$updatefinal = DB::table('user_messages')
			        	 ->where('user_messages.id','=',$messageId)
			        	 ->where('user_messages.recepient_id','=',Auth::user()->id)
			        	 ->update(array('is_read' =>1));
        	}
        	if($ordid!= 0){
        		$updatefinal = DB::table('user_messages');
        		if($term==1)
                            $updatefinal->where('user_messages.contract_id', $ordid);
                        else
                            $updatefinal->where('user_messages.order_id', $ordid);
        		$updatefinal->where('user_messages.recepient_id','=',Auth::user()->id)
        		->update(array('is_read' =>1));
        	}
        	
            $getMessagesQuery = DB::table('user_messages as um');
            $getMessagesQuery->leftjoin('lkp_message_types as lmy', 'lmy.id', '=', 'um.lkp_message_type_id');
            $getMessagesQuery->leftjoin('user_message_uploads as umu', 'umu.user_message_id', '=', 'um.id');
            $getMessagesQuery->leftjoin('users as u', 'u.id', '=', 'um.sender_id');
            $getMessagesQuery->leftjoin('users as ur', 'ur.id', '=', 'um.recepient_id');
            
            $getMessagesQuery->where('um.is_term', $term);
            if($ordid!=0 && $ordid!=""){
                if($term==1)
                    $getMessagesQuery->where('um.contract_id', $ordid);
                else
                    $getMessagesQuery->where('um.order_id', $ordid);
            }else{
                $getMessagesQuery->where('um.id', $messageId);
                $getMessagesQuery->orwhere('um.actual_parent_message_id', $messageId);
            }
            $getMessagesQuery->select('um.id', 'um.lkp_service_id','um.recepient_id','um.sender_id','um.post_id','lmy.message_type',
                                'um.order_id', 'um.lkp_message_type_id', 'um.subject','um.message','um.created_at',
                                'um.is_read', 'um.is_reminder', 'um.is_notified','um.is_general','u.username as from','ur.username as to','um.message_no','umu.filepath','umu.name','um.actual_parent_message_id')
                    ->groupBy('um.id');
            $messageDetails = $getMessagesQuery->get();
            
            return $messageDetails;
                
            
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    
    public static function getPerticularMessageDetailsCount($messageId=null,$ordid=null,$term=0){
        try {
            $getMessagesQuery = DB::table('user_messages as um');
            if($ordid!=0 && $ordid!=""){
                if($term==1)
                    $getMessagesQuery->where('um.contract_id', $ordid);
                else
                $getMessagesQuery->where('um.order_id', $ordid);
            }else{
                $getMessagesQuery->where('um.id', $messageId);
                $getMessagesQuery->orwhere('um.actual_parent_message_id', $messageId);
            }
            $getMessagesQuery->where('um.recepient_id', Auth::User()->id);
            $getMessagesQuery->where('um.is_term', $term);
            $getMessagesQuery->select('um.id');
            $messageDetails = $getMessagesQuery->get();
            
            if(!empty($messageDetails)){
            return count($messageDetails);
            }else{
                return '';
                }
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    
    
    public static function setSendMessageDetails($inputForMessage, $fileDetails, $messageType = GENERALMESSAGETYPE) {
       
         try{
            date_default_timezone_set("Asia/Kolkata");
            $roleId = Auth::User()->lkp_role_id;
            //Saving the user activity to the log table
            $recipient = $inputForMessage['message_to'];
            $recipientArray = explode(',', $recipient);
            $subject = $inputForMessage['message_subject'];
            $message = nl2br($inputForMessage['message_body']);
            $isDraft = $inputForMessage['is_draft'];
            $isTerm = $inputForMessage['is_term'];
            $postItemId=0;$str='';$postOrOrderId='';
            if(!empty($inputForMessage['buyer_quote_item'])) {
                $postOrOrderId = $inputForMessage['buyer_quote_item'];
                $postItemId = $inputForMessage['buyer_quote'];
            }elseif(!empty($inputForMessage['buyer_quote_item_leads'])) {
                $postOrOrderId = $inputForMessage['buyer_quote_item_leads'];
                $postItemId = $inputForMessage['buyer_quote'];
            }elseif(!empty($inputForMessage['order_id_for_model'])) {
                $postOrOrderId = $inputForMessage['order_id_for_model'];
            }elseif(!empty($inputForMessage['contract_id_for_model'])) {
                $postOrOrderId = $inputForMessage['contract_id_for_model'];
            }elseif(!empty($inputForMessage['buyer_quote_item_seller'])) {
                $postOrOrderId = $inputForMessage['buyer_quote_item_seller'];
                $postItemId = $inputForMessage['seller_post'];
            }elseif(!empty($inputForMessage['buyer_quote_item_seller_leads'])) {
                $postOrOrderId = $inputForMessage['buyer_quote_item_seller_leads'];
                $postItemId = $inputForMessage['seller_post'];
            }elseif(!empty($inputForMessage['buyer_quote_item_for_search_seller'])) {
                $postOrOrderId = $inputForMessage['buyer_quote_item_for_search_seller'];
            }elseif(!empty($inputForMessage['order_id_for_model_seller'])) {
                $postOrOrderId = $inputForMessage['order_id_for_model_seller'];
            }elseif(!empty($inputForMessage['buyer_quote_item_for_search'])) {
                $postOrOrderId = $inputForMessage['buyer_quote_item_for_search'];
            }
            
            $serviceId  =   Session::get('service_id');
            $msgid  =   CommonComponent::getMessageID();
            $created_year = date('Y');  
            switch ($serviceId) {           
            case ROAD_FTL :
                            $randString = 'FTL/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break;
            case ROAD_PTL :
                            $randString = 'LTL/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break;
            case RAIL :
                            $randString = 'RAIL/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break;
            case AIR_DOMESTIC :
                            $randString = 'AIRDOMESTIC/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break;
            case OCEAN :
                            $randString = 'OCEAN/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break;
            case AIR_INTERNATIONAL :
                            $randString = 'AIRINTERNATIONAL/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break;
            case ROAD_INTRACITY :
                            $randString = 'INTRA/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break;
            case COURIER :
                            $randString = 'COURIER/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break; 
            case RELOCATION_DOMESTIC :
                            $randString = 'RD/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break; 
            case RELOCATION_OFFICE_MOVE :
                            $randString = 'REL-OFF/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break;              
            case RELOCATION_PET_MOVE :
                            $randString = 'RELOCATIONPET/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break;
            case RELOCATION_INTERNATIONAL :
                            $randString = 'REL-INT/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break;        
            case RELOCATION_GLOBAL_MOBILITY :
                            $randString = 'RELOCATIONGM/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break;                       
            case ROAD_TRUCK_HAUL :
                            $randString = 'TRUCKHAUL/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break; 
            case ROAD_TRUCK_LEASE :
                            $randString = 'TRUCKLEASE/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            break;             
            default:
                            $randString = 'GENERAL/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $message_no = $randString;
                            $str='';
                            break;
        }
            //Save data into txnprojectinviteerequests
            foreach ($recipientArray as $recipientId) {
                if(!empty($recipientId)){
                    $userMessage = new UserMessage();
                    
                    $userMessage->sender_id = Auth::user()->id;
                    $userMessage->message_no=   $message_no;
                    $userMessage->recepient_id = $recipientId;
                    $userMessage->parent_message_id=   $inputForMessage['message_id'];
                    
                    if(isset($inputForMessage['message_id']) && $inputForMessage['message_id']!=""){
                        $userMessage->actual_parent_message_id=CommonComponent::getParentId($inputForMessage['message_id']);
                        $data=  CommonComponent::getMessageType($inputForMessage['message_id']);
                        
                        $messageType=   $data->lkp_message_type_id;
                        $userMessage->lkp_service_id = $data->lkp_service_id;
                        if($roleId == BUYER){
                            if($data->lead_id!="" && $data->lead_id!=0) {
                               $userMessage->quote_item_id = $data->lead_id;
                                $userMessage->lead_id =$data->post_item_id;
                           }
                           if($data->enquiry_id!="" && $data->enquiry_id!=0) {
                               $userMessage->quote_item_id = $data->enquiry_id;
                                $userMessage->enquiry_id =$data->post_item_id;
                           }
                           
                           
                        }elseif($roleId == SELLER){
                            if($data->lead_id!="" && $data->lead_id!=0) {
                               $userMessage->lead_id = $data->quote_item_id;
                                $userMessage->post_item_id =$data->lead_id;
                           }
                           if($data->enquiry_id!="" && $data->enquiry_id!=0) {
                               $userMessage->enquiry_id = $data->quote_item_id;
                                $userMessage->post_item_id =$data->enquiry_id;
                           }
                           
                        }
                        if($data->order_id!="" && $data->order_id!=0) {
                               $userMessage->order_id = $data->order_id;
                        }
                        if($data->is_term!="" && $data->is_term!=0) {
                               $isTerm = $data->is_term;
                        }
                        
                    }  else {
                        $userMessage->lkp_service_id = Session::get('service_id');
                    
                        if($messageType == LEADSMESSAGETYPE){
                            if($roleId == BUYER){
                                $userMessage->lead_id = $postOrOrderId;
                                $userMessage->quote_item_id =$postItemId;
                            }elseif($roleId == SELLER){
                                $userMessage->lead_id = $postOrOrderId;
                                $userMessage->post_item_id =$postItemId;
                            }
                        }else if( $messageType == POSTQUOTEMESSAGETYPE){
                            $userMessage->enquiry_id = $postOrOrderId;
                            $userMessage->quote_item_id =$postItemId;
                        }else if( $messageType == ORDERMESSAGETYPE){
                            $userMessage->order_id = $postOrOrderId;
                        }else if( $messageType == CONTRACTMESSAGETYPE){
                            $userMessage->contract_id = $postOrOrderId;
                        }else if( $messageType == POSTENQURYMESSAGETYPE){
                            $userMessage->enquiry_id = $postOrOrderId;
                            $userMessage->post_item_id =$postItemId;
                        }
                    }
                    $userMessage->lkp_message_type_id = $messageType;
                    $userMessage->subject = $subject;
                    $userMessage->message = $message;
                    $userMessage->is_draft = $isDraft;
                    $userMessage->is_term = $isTerm;
                    
                    $created_at = date('Y-m-d H:i:s');
                    $createdIp = $_SERVER['REMOTE_ADDR'];
                    $userMessage->created_by = Auth::id();
                    $userMessage->created_at = $created_at;
                    $userMessage->created_ip = $createdIp;
                    
                    if ($userMessage->save()) {
                        $insertedMessageId = $userMessage->id;
                        if(!empty($insertedMessageId)){
                        //CommonComponent::auditLog($userMessage->id, 'cart_items');
                            if(isset($fileDetails) && $fileDetails!='') {
                                $certificationDocumentDirectory = 'uploads/message_attachments/';
                                CommonComponent::createDirectory($certificationDocumentDirectory);
                                //echo "<pre>".count($fileDetails);print_r($fileDetails);exit;
                                for($i=0;$i<count($fileDetails['name']);$i++){
                                    $error = $fileDetails['error'][$i];
                                    $uploadDocument = $fileDetails['name'][$i];
                                    if($error==0 && !is_array($uploadDocument)) {
                                        $uploadedDocumentNameWithoutExtension = pathinfo($uploadDocument, PATHINFO_FILENAME);
                                        $fileExtension = pathinfo($uploadDocument, PATHINFO_EXTENSION);
                                        $fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter($uploadedDocumentNameWithoutExtension);
                                        $microTimeWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter(microtime());
                                        $uniqueFileName = $microTimeWithoutSpecialCharacter."_".$fileNameWithoutSpecialCharacter.'.'.$fileExtension;
                                        $moveUploadedFile = move_uploaded_file($fileDetails['tmp_name'][$i],$certificationDocumentDirectory.$uniqueFileName);
                                        $uploadedFileUrl = $certificationDocumentDirectory.$uniqueFileName;
                                        
                                        //attatchments saving
                                        $userMessageUpload = new UserMessageUpload();
                                        $userMessageUpload->user_message_id = $insertedMessageId;
                                        $userMessageUpload->name = $uploadDocument;
                                        $userMessageUpload->type = $fileExtension;
                                        $userMessageUpload->filepath = $uploadedFileUrl;
                                        $userMessageUpload->created_by = Auth::user()->id;
                                        $userMessageUpload->created_at = $created_at;
                                        $userMessageUpload->created_ip = $createdIp;
                                        $userMessageUpload->updated_at = $created_at;
                                        $userMessageUpload->save();
                                        
                                    }
    //                                else{
    //                                    $uploadedFileUrl = '';
    //                                    $uploadDocument = '';
    //                                    $fileExtension = '';
    //                                }
                                } 
                            }
//            else {
//                $uploadedFileUrl = '';
//                $uploadDocument = '';
//                $fileExtension = '';
//            }
                            
                            
                            
                        }
                    }
                }
            }
            //return $insertedMessageId;
        } catch (Exception $ex) {

        }
            
    }
    
    public static function getUserNameListAsPerCondition($term){
        try {
            //if(user not selected any service ie. For home page){
             $userList = DB::table('users')
                ->where(['users.is_active' => 1])
                ->where('username', 'LIKE', $term.'%')
                ->where('users.id', '!=', Auth::user()->id)
                ->orderby('users.id','asc')
                ->select('users.id','users.username')
                ->get();
            return $userList;
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
    
    public static function listSentMessages($request=null,$message_types = GENERALMESSAGETYPE, $receiverId = null,$id=null,$term=0){
        try {//echo "<pre>";print_r($request);exit;
           //echo "<pre>";print_r($_SERVER['REQUEST_URI']);exit;
            if(isset($request['message_types']) && $request['message_types']!=""){
                Session::put('message_types',$request['message_types']);
            }if(isset($request['message_services']) && $request['message_services']!=""){
                Session::put('message_services',$request['message_services']);
            }if(isset($request['message_keywords']) && $request['message_keywords']!=""){
                Session::put('message_keywords',$request['message_keywords']);
            }if(isset($request['from_message']) && $request['from_message']!=""){
                Session::put('from_message',$request['from_message']);
            }if(isset($request['to_message']) && $request['to_message']!=""){
                Session::put('to_message',$request['to_message']);
            }
            if(empty($request)){
                Session::put('message_types','');
                Session::put('message_services','');
                Session::put('message_keywords','');
                Session::put('from_message','');
                Session::put('to_message','');
            }
            //if(user not selected any service ie. For home page){
            $roleId = Auth::User()->lkp_role_id;
            $serviceId = Session::get('service_id');
            if(empty(Auth::User()->id)) {
                return ['success' => '0','error' => 'Oops something went wrong.'];
            }
            $getAllMessagesQuery = DB::table('user_messages as um');
            $getAllMessagesQuery->leftjoin('lkp_message_types as lmy', 'lmy.id', '=', 'um.lkp_message_type_id');
            $getAllMessagesQuery->leftjoin('users as u', 'u.id', '=', 'um.recepient_id');
            //if($message_types!=GENERALMESSAGETYPE)
            //$getAllMessagesQuery->where('um.is_term', $term);
            
            $getAllMessagesQuery->where('um.sender_id', Auth::User()->id);
           
            if (!empty($request['message_services']) && $request['message_services'] != '0' && $request['message_services'] != ''){
                $getAllMessagesQuery->where('um.lkp_service_id', $request['message_services']);
            }
            
            if (!empty($request['message_types']) && $request['message_types'] != '0' && $request['message_types'] != ''){
                if($request['message_types']==POSTMESSAGETYPE )
                    $getAllMessagesQuery->whereRaw('(um.lkp_message_type_id ='.POSTQUOTEMESSAGETYPE.' or `um`.`lkp_message_type_id` = '.POSTENQURYMESSAGETYPE.' or `um`.`lkp_message_type_id` = '.LEADSMESSAGETYPE.')');
                else
                    $getAllMessagesQuery->where('um.lkp_message_type_id', $request['message_types']);
            }/*elseif (!empty($message_types) && $message_types != '1'  && $message_types != '2' && $message_types != '4' && $message_types != '5'){
                $getAllMessagesQuery->where('um.lkp_message_type_id', $message_types);
            }*/
            
            
            
            if (!empty($request['message_keywords'])){
                
                $str='%'.$request['message_keywords'].'%';
                $getAllMessagesQuery->where(function ($getAllMessagesQuery)use ($str) {
                $getAllMessagesQuery->where('um.subject', 'LIKE', $str)
                   ->orWhere('um.message', 'LIKE', $str);
                });
            }
            
            if (isset ( $request ['from_message'] ) && $request ['from_message'] != '') {
                $getAllMessagesQuery->where ( 'um.created_at', '>=', CommonComponent::convertDateTimeForDatabase($request ['from_message'],'00:00:00') );

            }
            if (isset ( $request ['to_message'] ) && $request ['to_message'] != '') {
                $getAllMessagesQuery->where ( 'um.created_at', '<=', CommonComponent::convertDateTimeForDatabase($request ['to_message'],'23:59:59') );

            }
            
            $getAllMessagesQuery->select('um.id', 'um.lkp_service_id','um.recepient_id','um.sender_id','um.post_id','lmy.message_type',
                                'um.order_id', 'um.lkp_message_type_id', 'um.subject','um.message','um.created_at',
                                'um.is_read', 'um.is_reminder', 'um.is_notified','um.is_general','u.username','um.is_term');
            $getAllMessages = $getAllMessagesQuery->get();
            //$sqlquery = $getAllMessagesQuery->tosql();
            //echo "<pre>";print_r($sqlquery);exit;
            //grid for messages
            $grid = DataGrid::source($getAllMessagesQuery);
            $grid->attributes(array("class" => "table table-striped"));
            $grid->add('id', 'ID', false)->style('display:none');
            $grid->add('message_type', 'Type', 'message_type')->attributes(array("class" => "col-md-3 padding-left-none",'style'=>'display:none'));
            $grid->add('username', 'Recipient', 'username')->attributes(array("class" => "col-md-3 padding-left-none"));
            $grid->add('subject', 'Subject', 'subject')->attributes(array("class" => "col-md-3 padding-left-none"));
            $grid->add('created_at', 'Date', 'created_at')->attributes(array("class" => "col-md-3 padding-left-none"));
            $grid->add('is_term', 'Action', 'is_term')->attributes(array("class" => "col-md-3 padding-left-none"));
            $grid->add('recepient_id', 'recepient_id', 'recepient_id')->style('display:none');
            
            $grid->orderBy('id', 'desc');
            $grid->paginate(5);
            
            $grid->row(function ($row) {//echo "<pre>";print_r($row);exit;
                $msg_id = $row->cells [0]->value;
                $is_term = $row->cells [5]->value;
                $recepient_id = $row->cells [6]->value;
                $subject = $row->cells [3]->value;
                $row->cells[0]->value = '<a href=/getmessagedetails/'.$msg_id.'/0/'.$is_term.'>';
                //$mtype  =     $row->cells[1]->value;
                $uname  =     $row->cells[2]->value;
                $row->cells [0]->style('display:none');
                $row->cells [6]->style('display:none');

                $row->cells [1]->attributes(array("class" => "col-md-2 padding-left-none",'style'=>'display:none'));
                //$row->cells [2]->attributes(array("class" => "col-md-3 col-sm-2 col-xs-4 hidden-xs  padding-none"));
                $row->cells [3]->attributes(array("class" => "col-md-3 padding-left-none"));
                $row->cells [4]->attributes(array("class" => "col-md-3 padding-left-none"));
                $row->cells [5]->attributes(array("class" => "col-md-3 padding-left-none"));
                $row->cells[2]->value = '<div style="width:100%"><div class="table-row "><div class="col-md-3 padding-left-none">'.$uname;
                if($row->cells [4]->value=="0000-00-00 00:00:00")
                    $row->cells [4]->value="NA";
                else
                    $row->cells [4]->value = ''.CommonComponent::checkAndGetDateTime($row->cells [4]->value).'';
                $row->cells [4]->value .="</a>";
                $row->cells [5]->value = '<a href="#" class="new_message" data-userid="'.$recepient_id.'" data-subject="'.$subject.'" data-msgid="'.$msg_id.'">Reply</a></div></div></div>';

                $row->attributes(array("class" => "","style"=>"width:100%"));
            });
            $result = array();
            $result['grid'] = $grid;
            $result['result'] = $getAllMessages;//echo "here".count($getAllMessages);
            //print_r($result);exit;
            return $result;
           
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
}
