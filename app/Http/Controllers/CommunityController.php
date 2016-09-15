<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Components\MessagesComponent;
use App\Components\CommonComponent;
use App\Components\Community\SearchComponent;
use App\Components\Community\CommunityComponent;
use App\Models\CommunityGroup;
use App\Models\CommunityGroupComment;
use App\Models\CommunityGroupMember;
use App\Models\CommunityGroupLike;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\Mail;
use Response;
use Log;
use Zofe\Rapyd\Facades\DataGrid;
use Zofe\Rapyd\Facades\DataFilter;
use App\Models\UserMessage;

class CommunityController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }    

    /**
     * Community Landing Page.
     * Date- 6-04-2016 (srinu start here)
     */    
	public static function communityHome() {		
		Log::info('Community Landing Home page:'.Auth::id(),array('c'=>'1'));
		try {			
			return view('community.community_home');						
		} catch (Exception $e) {
			
		}		
	}
	
	/**
	 * Community Create Group Form.
	 * Date- 6-04-2016 (srinu start here)
	 */
	public static function communityCreateGroupForm() {	
		Log::info('Community Greate Group:'.Auth::id(),array('c'=>'1'));
		try {				
			return view('community.community_create_group');				
		} catch (Exception $e) {
				
		}
	}

        
        /**
	 * Community Individual search Form.
	 *
	 */
	public static function communityIndividualSearch() {
	
		Log::info('Community Individual Search:'.Auth::id(),array('c'=>'1'));
		try {
                        $services = array("" => "Services");
                        $speciality = array("" => "Speciality");
                        $location = array("" => "Location");
                        $industry = array("" => "Industry");
                        $user_services=array();
                        
                       
                        if(isset($_REQUEST['category'])){
                            $category=$_REQUEST['category'];
                            $query  =   SearchComponent::search(INDIVIDUAL,$category,$_REQUEST);
                            $results    =   $query->get();
                            //echo "<pre>";print_r($results);exit;
                            foreach ($results as $result) {
                                if(!isset($_REQUEST['service_id']) && !isset($_REQUEST['speciality']) && !isset($_REQUEST['location']) && !isset($_REQUEST['industry_id'])){
                                $user_services[] = CommonComponent::getUserServices($result->id);
                                $speciality[$result->speciality_id] = DB::table('lkp_specialities')->where('id', $result->speciality_id)->pluck('speciality_name');
                                $location[$result->principal_place] = $result->principal_place;	
                                $industry[$result->industry_id] = DB::table('lkp_industries')->where('id', $result->industry_id)->pluck('industry_name');	
                                Session::put('filter_speciality', $speciality);
                                Session::put('filter_location', $location);
                                Session::put('filter_industry', $industry);
                                }
                            }
                            if(!isset($_REQUEST['service_id']) && !isset($_REQUEST['speciality']) && !isset($_REQUEST['location']) && !isset($_REQUEST['industry_id'])){
                                foreach($user_services as $res){
                                    if(!empty($res)){
                                        foreach($res as $key => $resservice){
                                         $services[$key] = $resservice;
                                        }
                                    }
                                }
                                ksort($services);
                                Session::put('filter_services', $services);
                            }
                            $Query_new = array();
                            foreach($results as $qry){
                                $resp = CommonComponent::getUserServices($qry->id);
                                $qry->service_id=$resp;$Query_new[] = $qry;
                            }
                            if(isset($_REQUEST['service_id']) && $_REQUEST['service_id']!=''){
                                $service_id = $_REQUEST['service_id'];
                                foreach($Query_new as $key => $Query_newrow){
                                    if(!empty($Query_newrow->service_id)){
                                        if(in_array($service_id,array_keys($Query_newrow->service_id))){}
                                        else{
                                            unset($Query_new[$key]);
                                        }
                                    }else{
                                        unset($Query_new[$key]);
                                    }
                                }
                                $query = $Query_new;
                            }

                            
                            $grid = DataGrid::source($query);		
                            $grid->attributes(array("class" => "table table-striped"));		
                            $grid->add('id', 'ID', false)->style('display:none');
                            $grid->add('name', 'name', false)->style('display:none');
                            $grid->add('principal_place', '', false)->style('display:none');
                            $grid->add('industry_name', '', false)->style('display:none');
                            $grid->add('logo', '', 'logo')->style('display:none');
                            $grid->add('description', '', 'description')->style('display:none');
                            $grid->orderBy('id', 'desc');
                            $grid->paginate(5);

                            $grid->row(function ($row) {
                               
                                $row->cells [1]->style('display:none');			
                                $row->cells [2]->style('display:none');
                                $row->cells [3]->style ( 'display:none' );
                                $row->cells [4]->style ( 'display:none' );
                                $row->cells [5]->style ( 'display:none' );
                                 $id = $row->cells [0]->value;
                                 $name = $row->cells [1]->value;
                                 $place = $row->cells [2]->value;
                                 $industry = $row->cells [3]->value;
                                 //$logo = $row->cells [4]->value;
                                 
                                 $description = $row->cells [5]->value;
                                 $partners  =   CommonComponent::getPartners($id);
                                 $followers  =   CommonComponent::getFollowers($id);
                                 $row->cells [0]->value='<div class="col-md-1 padding-left-none">
                        <div class="profile-pic"><a href="/network/profile/'.$id.'">';
                        $profiledetails = CommonComponent::getUserDetails($id);
                        if($profiledetails->lkp_role_id == 2){
                            $url = "uploads/seller/$profiledetails->id/";
                        }else{
                            $url = "uploads/buyer/$profiledetails->id/";
                        }
                        $getlogo = $url.$profiledetails->user_pic;
                        $logo =  CommonComponent::str_replace_last( '.' , '_94_92.' , $getlogo );           
                        if(isset($logo)&& $profiledetails->user_pic!='' && file_exists($logo)){
                            $row->cells [0]->value.='<img src="'.asset($logo).'">';
                        }else{
                            $row->cells [0]->value.='<i class="fa fa-user"></i>';
                        }
                        $row->cells [0]->value.='</a></div>
                     </div>
                     <div class="col-md-2 padding-left-none">
                        <span class="lbl padding-8"></span><strong><a href="/network/profile/'.$id.'">'.$name.'</strong></a>
                        <div class="red">
                           <i class="fa fa-star"></i>
                           <i class="fa fa-star"></i>
                           <i class="fa fa-star"></i>
                        </div>
                     </div>
                     <div class="col-md-2 padding-left-none">'.$place.'</div>
                     <div class="col-md-2 padding-left-none">'.$industry.'</div>
                     <div class="col-md-2 padding-left-none">Partners <strong>('.count($partners).')</strong></div>
                     <div class="col-md-2 padding-left-none">Followers <strong>('.count($followers).')</strong></div>
                     <div class="col-md-10 padding-left-none">'.$description.'</div>';
                            });
                            
                            return view('community.individual_search',array(
                                'grid'=>$grid,
                                
                               
                                ));
                        }
                        else{
                            return view('community.individual_search');
                        }
			
				
		} catch (Exception $e) {
				
                }
        }
	
	/**
	 * Community Create Group Form Data insertion.
	 * Date- 7-04-2016 (srinu start here)
	 */
	public static function createCommunityNewGroup() {	
		Log::info('Community Greate New Group Insertion:'.Auth::id(),array('c'=>'1'));
		try {			
			if (!empty(Input::all())) {						
				$all_var = Input::all();				
				if(isset($all_var['community_private_check']) && $all_var['community_private_check']!='') {
					$is_private_check= $all_var['community_private_check'];
				} else {
					$is_private_check=0;
				}
				//Group logo move folder and create directory here.
				$target_dir = 'uploads/community/groups/'.Auth::id()."/" ;
				if (!is_dir ( $target_dir )) {
					mkdir ( $target_dir, 0777, true );
				}
				$target =  CommonComponent::removeSpecialCharacter($_FILES["community_group_logo"]["name"]);
				$target_file1 =  time() . "_" .basename($target);
				$target_file = $target_dir. $target_file1;
				$_FILES["community_group_logo"]["size"];
				$_FILES["community_group_logo"]["name"];
				move_uploaded_file($_FILES["community_group_logo"]["tmp_name"], $target_file);				
				//Insert groups data into table.
				$created_at = date('Y-m-d H:i:s');
				$createdIp = $_SERVER ['REMOTE_ADDR'];
				$communitynewgroup = new CommunityGroup();
				$communitynewgroup->group_name = $all_var['community_group_name'];
				$communitynewgroup->description = $all_var['community_description'];
				$communitynewgroup->is_acknowledged = 1;
				$communitynewgroup->is_private = $is_private_check;
				$communitynewgroup->logo_file_name = $target_file1;
				$communitynewgroup->created_by = Auth::id();
				$communitynewgroup->created_at = $created_at;
				$communitynewgroup->created_ip = $createdIp;
				if ($communitynewgroup->save()) {
					$LastInsertId = $communitynewgroup->id;					
					$communitygroupMember = new CommunityGroupMember();
					$communitygroupMember->community_group_id = $LastInsertId;
					$communitygroupMember->is_approved = 1;			
					$communitygroupMember->user_id    = Auth::id();
					$communitygroupMember->created_by = Auth::id();
					$communitygroupMember->created_at = $created_at;
					$communitygroupMember->created_ip = $createdIp;	
					$communitygroupMember->save();
				 }
				
				return redirect('community/groupdetails/'.$LastInsertId)->with('gsumsg', "New Group Created Successfully");
			}			
	
		} catch (Exception $e) {
	
		}
	}
	
	/**
	 * Community Edit Group Form.
	 * Date- 7-04-2016 (srinu start here)
	 */
	public static function editCommunityNewGroupForm($id) {	
		Log::info('Community Greate Group:'.Auth::id(),array('c'=>'1'));
		try {										
			$getCommunityData = CommunityComponent :: getCommunityGroupData ($id);				
			return view('community.community_edit_group', ['getGroupcommunityData' =>  $getCommunityData] );	
		} catch (Exception $e) {
	
		}
	}
		
	/**
	 * Community  New Group Edit depends on group id.
	 * Date- 7-04-2016 (srinu start here)
	 * params {{ $id }}
	 */
	public static function updateCommunityGroup() {	
		Log::info('Community  Edit Group Updation:'.Auth::id(),array('c'=>'1'));
		try {				
			if (!empty(Input::all())) {	
				$all_var = Input::all();		
                                //echo "<pre>"; print_r($all_var); die;
				if(isset($all_var['community_private_check']) && $all_var['community_private_check']!='') {
					$is_private_check= $all_var['community_private_check'];
				} else {
					$is_private_check=0;
				}	
                                if(isset($all_var['community_group_logo1']) && $all_var['community_group_logo1']) {
                                    $target_dir = 'uploads/community/groups/'.Auth::id()."/" ;
                                    if (!is_dir ( $target_dir )) {
                                            mkdir ( $target_dir, 0777, true );
                                    }
                                    $target =  CommonComponent::removeSpecialCharacter($_FILES["community_group_logo1"]["name"]);
                                    $target_file1 =  time() . "_" .basename($target);
                                    $target_file = $target_dir. $target_file1;
                                    $_FILES["community_group_logo1"]["size"];
                                    $_FILES["community_group_logo1"]["name"];
                                    move_uploaded_file($_FILES["community_group_logo1"]["tmp_name"], $target_file);	
                                } else {
                                    $target_file1 = $all_var['group_logo'];
                                }
				
				$updatedAt = date('Y-m-d H:i:s');
				$updatedIp = $_SERVER ['REMOTE_ADDR'];				
				CommunityGroup::where ( "id", $all_var['group_id'] )->update ( array (   							
   							'group_name' => $all_var['community_group_name'],
   							'description' => $all_var['community_description'],
   							'is_private' => $is_private_check,
   							'logo_file_name' => $target_file1,					
   							'updated_at' => $updatedAt,
   							'updated_by' => Auth::User ()->id,
   							'updated_ip' => $updatedIp
   					)); 
			}			
			return redirect('community/groupdetails/' . $all_var['group_id'])->with('gcmsg', "Group Updated Successfully");
	
		} catch (Exception $e) {
	
		}
	}
	
	
	/**
	 * Community Check Group Name Already exists or not.
	 * Date- 7-04-2016 (srinu start here)
	 */
	public static function checkGroupNameExists() {
		Log::info('Community Check groupname exists or not:'.Auth::id(),array('c'=>'1'));
		try {
			$groupName = $_GET['groupname'];
			$getgroupName = DB::table('community_groups')			
			->where('group_name', '=', $groupName)
			//->where('created_by', '=', Auth::User ()->id)
			->select('community_groups.id')
			->first();				
			echo $getgroupName->id;			
			
		} catch (Exception $e) {
	
		}
	}
	
	/**
	 * Community Display Group Details.
	 * Date- 7-04-2016 (srinu start here)
	 */

	public static function displayGroupDetails($id) {

            Log::info('Community Greate Group:'.Auth::id(),array('c'=>'1'));
            try {
                   if(isset($_REQUEST['ajax'])){
                       $page = isset($_REQUEST['page']) ? $_REQUEST['page']-1 : 1;
                       $skip = $page * AJAX_LOAD_LIMIT;
                       $conversationGroupsData = CommunityComponent :: getGroupConversationData ($id,$skip);
                       $displayGroupDetails = CommunityComponent :: getCommunityGroupData($id);
                       $returnHTML = view('community.community_group_details_ajax')->with(['getconversationGroupsData' =>  $conversationGroupsData,'displayGroupDetails' => $displayGroupDetails])->render();
                       return response()->json(array('success' => true, 'html'=>$returnHTML));
                   }else{
                        $displayGroupDetails = CommunityComponent :: getCommunityGroupData($id);
                        $members = CommonComponent::getMemberCheck($id);
                        $is_admin = CommonComponent::getMemberAdminCheck($id);
                      
                        $conversationGroupsData = CommunityComponent :: getGroupConversationData($id);
                        $conversationGroupsDataCount = CommunityComponent :: getGroupConversationData($id,0,false);
                        $getAllManageMembers = CommunityComponent :: getAllManageMembers($id);                       
                        $grpmemberpartners   =   CommunityComponent::getGroupMemberPartners($id);
                      
                        if($displayGroupDetails->is_confirmed==0){
                        	return view('community.community_deactivated_group_details',
                        				['displayGroupDetails' => $displayGroupDetails,
                        				 'getconversationGroupsData' =>  $conversationGroupsData,
                        				  //'getAllManageMembers'=>$getAllManageMembers,
                        				 'totalcount'=>count($conversationGroupsDataCount),
                                                         'grpmemberpartners'=>   $grpmemberpartners]);
                        }else{
	                        if((!empty($members) && $members->is_approved==1) || !empty($is_admin) && ($is_admin->id!='') ){
	                        return view('community.community_group_details', 
	                        		['displayGroupDetails' => $displayGroupDetails,
	                        		'getconversationGroupsData' =>  $conversationGroupsData, 
	                        		'getAllManageMembers'=>$getAllManageMembers,
	                        		'totalcount'=>count($conversationGroupsDataCount),
                                                'grpmemberpartners'=>   $grpmemberpartners]);
	
	                        }else{
	                        return view('community.public_community_group_details', [
                                    'displayGroupDetails' =>     $displayGroupDetails,
                                    'grpmemberpartners'=>   $grpmemberpartners]);
	                        }
                        }
                       }

            } catch (Exception $e) {

            }

        }

    public function loadMoreComments(){
        $page = isset($_REQUEST['iteration']) ? $_REQUEST['iteration'] : 1;
        $skip = $page * AJAX_LOAD_LIMIT;
        $getAllPostCommentscount = count(CommunityComponent ::getMainPostComments( $_REQUEST['group_id'],$_REQUEST['post_id'],0,false ));
        $getAllPostComments = CommunityComponent ::getMainPostComments( $_REQUEST['group_id'],$_REQUEST['post_id'],$skip );
    
        $moreexists = ($getAllPostCommentscount > $skip+AJAX_LOAD_LIMIT) ? true: false;
        $returnHTML = view('community.community_conversation_load_more')->with(['getAllPostComments' =>  $getAllPostComments])->render();
        return response()->json(array('success' => true, 'html'=>$returnHTML,'more'=>$moreexists));
      
    }

	
	/**
	 * Community Insert Group Conversation Data.
	 * Date- 7-04-2016 (srinu start here)
	 * Params { $_REQUEST['community_group_conversation_id'] -> Group Main id} 
	 */
	public static function insertCommunityGroupCoversation() {
		Log::info('Community Insert Group conversation:'.Auth::id(),array('c'=>'1'));
		try {					
			$created_at = date('Y-m-d H:i:s');
			$createdIp = $_SERVER ['REMOTE_ADDR'];
			$communityconversation = new CommunityGroupComment();
			$communityconversation->title = $_REQUEST['community_group_conversation_title'];
			$communityconversation->comments = $_REQUEST['community_group_conversation_comments'];
			$communityconversation->community_group_id = $_REQUEST['community_group_conversation_id'];			
			$communityconversation->user_id = 	 Auth::id();
			$communityconversation->created_by = Auth::id();
			$communityconversation->created_at = $created_at;
			$communityconversation->created_ip = $createdIp;
			$communityconversation->save();
			return redirect('community/groupdetails/' . $_REQUEST['community_group_conversation_id'])->with('gcmsg', "Conversation Created Successfully");
		} catch (Exception $e) {
	
		}
	}
        
        
        /**
	 * Community Group search Form.
	 *
	 */
	public static function communityGroupSearch() {
	
		Log::info('Community Group Search:'.Auth::id(),array('c'=>'1'));
		try {
                       
                        if(isset($_REQUEST['search'])){
                            $category=NAME;
                            $query  =   SearchComponent::search(GROUP,$category,$_REQUEST);
                            $query->get();
                           
                            
                            $grid = DataGrid::source($query);		
                            $grid->attributes(array("class" => "table table-striped"));		
                            $grid->add('id', 'ID', false)->style('display:none');
                            $grid->add('name', 'name', false)->style('display:none');
                            //$grid->add('user_id', '', false)->style('display:none');
                            $grid->add('logo', '', 'logo')->style('display:none');
                            $grid->add('description', '', 'description')->style('display:none');
                            $grid->add('created_by', '', false)->style('display:none');
                            $grid->orderBy('id', 'desc');
                            $grid->paginate(5);

                            $grid->row(function ($row) {
                                //$row->cells [0]->style('display:none');
                                $row->cells [1]->style('display:none');			
                                $row->cells [2]->style('display:none');
                                $row->cells [3]->style ( 'display:none' );
                                $row->cells [4]->style ( 'display:none' );
                                
                                 $id = $row->cells [0]->value;
                                 $name = $row->cells [1]->value;
                                 //$user_id = $row->cells [2]->value;
                                 $logo = $row->cells [2]->value;
                                 $description = $row->cells [3]->value;
                                 $userId = $row->cells [4]->value;
                                  
                                 $members  =   CommonComponent::getMembers($id);
                                 $memberExist   =   CommonComponent::getMemberCheck($id);
                                 $is_admin = CommonComponent::getMemberAdminCheck($id);
                                 $str_cls='member_button';$href='';
                                 //print_r($memberExist);
                                 if((!empty($memberExist) && $memberExist->is_invited==1) || (!empty($is_admin) && $is_admin->id!='')   ){
                                     if(isset($memberExist->is_approved) && $memberExist->is_approved==0 && $memberExist->is_approved!=''){
                                        
                                         $str='Request Pending';
                                         $str_cls='request sent';
                                     }elseif((isset($memberExist->is_approved) && $memberExist->is_approved==1) || $is_admin->id!='' ){
                                         
                                         $str='Post Message';
                                         $str_cls='post_message';
                                         $href="/community/groupdetails/$id";
                                     }
                                     
                                 }else{
                                     $str='Become a Member';
                                 }
                                 
                                 $row->cells [0]->value='<div class="col-md-1 padding-left-none">
                        <div class="profile-pic"><a class="group_detail_page" href="/community/groupdetails/'.$id.'">';
                        if(isset($logo)&& $logo!='' && file_exists("uploads/community/groups/".$userId."/".$logo)){
                            $row->cells [0]->value.='<img class="img-responsive" src="'.asset("uploads/community/groups/".$userId."/".$logo).'">';
                        }else{
                            $row->cells [0]->value.='<img class="img-responsive" src="'.asset('images/org-logo.png').'">';
                        }         
                        
                        $row->cells [0]->value.='</a></div>
                     </div>
                     <div class="col-md-11 padding-none">
                     <div class="col-md-2 padding-left-none">
                        <span class="lbl padding-8"></span><strong><a class="group_detail_page" href="/community/groupdetails/'.$id.'">'.$name.'</strong></a>
                        <div class="red">
                           <i class="fa fa-star"></i>
                           <i class="fa fa-star"></i>
                           <i class="fa fa-star"></i>
                        </div>
                     </div>
                     <div class="col-md-2 padding-left-none">Members <strong>('.count($members).')</strong></div>
                     <div class="clearfix"></div><div class="col-md-10 padding-left-none post-message-text">'.$description.'</div>'
                                         . '<div class="col-md-2 text-right padding-none post-message-btn"><button class="btn red-btn '.$str_cls.'" id="'.$id.'" href="'.$href.'">'.$str.'</button></div></div>';
                            });
                            
                            return view('community.group_search',array(
                                'grid'=>$grid,
                               
                                ));
                        }
                        else{
                            return view('community.group_search');
                        }
			
				
		} catch (Exception $e) {
				
                }
        }
        
        /**
	 * Community Organization search Form.
	 *
	 */
	public static function communityOrganizationSearch() {
	
		Log::info('Community Organization Search:'.Auth::id(),array('c'=>'1'));
		try {
                        $services = array("" => "Services");
                        $speciality = array("" => "Speciality");
                        $location = array("" => "Location");
                        $industry = array("" => "Industry");
                        $user_services=array();
                        
                        if(isset($_REQUEST['category'])){
                            $category=$_REQUEST['category'];
                            $query  =   SearchComponent::search(ORGANIZATION,$category,$_REQUEST);
                            $results    =   $query->get();
                            //echo "<pre>";print_r($results);
                            foreach ($results as $result) {
                                if(!isset($_REQUEST['service_id']) && !isset($_REQUEST['speciality']) && !isset($_REQUEST['location']) && !isset($_REQUEST['industry_id'])){
                                $user_services[] = CommonComponent::getUserServices($result->id);
                                $speciality[$result->speciality_id] = DB::table('lkp_specialities')->where('id', $result->speciality_id)->pluck('speciality_name');
                                $location[$result->principal_place] = $result->principal_place;	
                                $industry[$result->industry_id] = DB::table('lkp_industries')->where('id', $result->industry_id)->pluck('industry_name');	
                                Session::put('filter_speciality', $speciality);
                                Session::put('filter_location', $location);
                                Session::put('filter_industry', $industry);
                                
                                }
                            }
                            if(!isset($_REQUEST['service_id']) && !isset($_REQUEST['speciality']) && !isset($_REQUEST['location']) && !isset($_REQUEST['industry_id'])){
                                foreach($user_services as $res){
                                    if(!empty($res)){
                                        foreach($res as $key => $resservice){
                                         $services[$key] = $resservice;
                                        }
                                    }
                                }
                                ksort($services);
                                Session::put('filter_services', $services);
                            }//print_r($services);exit;
                         $Query_new = array();
                        foreach($results as $qry){
                            $resp = CommonComponent::getUserServices($qry->id);
                            $qry->service_id=$resp;$Query_new[] = $qry;
                        }
                        if(isset($_REQUEST['service_id']) && $_REQUEST['service_id']!=''){
                            $service_id = $_REQUEST['service_id'];
                            foreach($Query_new as $key => $Query_newrow){
                                if(!empty($Query_newrow->service_id)){
                                    if(in_array($service_id,array_keys($Query_newrow->service_id))){}
                                    else{
                                        unset($Query_new[$key]);
                                    }
                                }else{
                                    unset($Query_new[$key]);
                                }
                            }
                            $query = $Query_new;
                        }//echo "<pre>";print_r($Query_new);exit;
                        
                            
                            $grid = DataGrid::source($query);		
                            $grid->attributes(array("class" => "table table-striped"));		
                            $grid->add('id', 'ID', false)->style('display:none');
                            $grid->add('name', 'name', false)->style('display:none');
                            $grid->add('principal_place', '', false)->style('display:none');
                            $grid->add('industry_name', '', false)->style('display:none');
                            $grid->add('logo', '', 'logo')->style('display:none');
                            $grid->add('description', '', 'description')->style('display:none');
                            $grid->orderBy('id', 'desc');
                            $grid->paginate(5);

                            $grid->row(function ($row) {
                                //$row->cells [0]->style('display:none');
                                $row->cells [1]->style('display:none');			
                                $row->cells [2]->style('display:none');
                                $row->cells [3]->style ( 'display:none' );
                                $row->cells [4]->style ( 'display:none' );
                                $row->cells [5]->style ( 'display:none' );
                                 $id = $row->cells [0]->value;
                                 $name = $row->cells [1]->value;
                                 $place = $row->cells [2]->value;
                                 $industry = $row->cells [3]->value;
                                 //$logo = $row->cells [4]->value;
                                 $description = $row->cells [5]->value;
                                 $partners  =   CommonComponent::getPartners($id);
                                 $followers  =   CommonComponent::getFollowers($id);
                                 $row->cells [0]->value='<div class="col-md-1 padding-left-none">
                        <div class="profile-pic"><a href="/network/profile/'.$id.'">';
                        $profiledetails = CommonComponent::getUserDetails($id);
                        if($profiledetails->lkp_role_id == 2){
                            $url = "uploads/seller/$profiledetails->id/";
                        }else{
                            $url = "uploads/buyer/$profiledetails->id/";
                        }
                        $getlogo = $url.$profiledetails->user_pic;
                        $logo =  CommonComponent::str_replace_last( '.' , '_94_92.' , $getlogo );      
                        if(isset($logo)&& $profiledetails->user_pic!='' && file_exists($logo)){
                            $row->cells [0]->value.='<img src="'.asset($logo).'">';
                        }else{
                            $row->cells [0]->value.='<i class="fa fa-user"></i>';
                        }         
                        $row->cells [0]->value.='</a></div>
                     </div>
                     <div class="col-md-2 padding-left-none">
                        <span class="lbl padding-8"></span><a href="/network/profile/'.$id.'"><strong>'.$name.'</strong></a>
                        <div class="red">
                           <i class="fa fa-star"></i>
                           <i class="fa fa-star"></i>
                           <i class="fa fa-star"></i>
                        </div>
                     </div>
                     <div class="col-md-2 padding-left-none">'.$place.'</div>
                     <div class="col-md-2 padding-left-none">'.$industry.'</div>
                     <div class="col-md-2 padding-left-none">Partners <strong>('.count($partners).')</strong></div>
                     <div class="col-md-2 padding-left-none">Followers <strong>('.count($followers).')</strong></div>
                     <div class="col-md-10 padding-left-none">'.$description.'</div>';
                            });
                            
                            return view('community.organization_search',array(
                                'grid'=>$grid,
                               
                                ));
                        }
                        else{
                            return view('community.organization_search');
                        }
			
				
		} catch (Exception $e) {
				
                }
        }
        
	public static function becomeMember(){
            Log::info('Group Member Request:'.Auth::id(),array('c'=>'1'));
		try {
                    $groupid    =   $_REQUEST['groupid'];
                    $data   =   CommunityComponent::getCommunityGroupData($groupid);
                    $data_member   =   CommonComponent::getMemberCheck($groupid);
                    $msgid  =   CommonComponent::getMessageID();
                    $created_year = date('Y');
                    $randnumber   = 'COMMUNITY/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                    $created_at = date('Y-m-d H:i:s');
                    $createdIp = $_SERVER ['REMOTE_ADDR'];
                    if($data->is_private==1){
                        if(!empty($data_member) && $data_member->is_invited==0){
                            DB::table ( 'community_group_members' )
                                        ->where ( 'user_id', Auth::id() )
                                    ->where ( 'community_group_id', $groupid )
                                    ->update ( array (
						'is_invited' => 1,
                                                'updated_at' => $created_at,
                                                'updated_by' => Auth::id(),
                                                'updated_ip' => $createdIp,
				) );
                        }else{
                        
			$communitygroupMember = new CommunityGroupMember();
			$communitygroupMember->community_group_id = $groupid;
			$communitygroupMember->is_approved = 0;			
			$communitygroupMember->user_id    = Auth::id();
			$communitygroupMember->created_by = Auth::id();
			$communitygroupMember->created_at = $created_at;
			$communitygroupMember->created_ip = $createdIp;
			$communitygroupMember->save();
                        }
                        $activation_url = '<a href="'.url () . '/member_activation?g_id=' . $groupid . '&u_id=' . Auth::id().'">Activation Link</a>';
					
                        $partnermessage = new UserMessage();
                        $partnermessage->lkp_service_id = 0;
                        $partnermessage->sender_id = Auth::User()->id;
                        $partnermessage->recepient_id = $data->created_by;
                        $partnermessage->lkp_message_type_id = 8;
                        $partnermessage->message_no = $randnumber;
                        $partnermessage->subject = "Group Member Request";
                        $partnermessage->message = "Recieved New Group Member Request <br> $activation_url";
                        $partnermessage->is_read = 0;
                        $partnermessage->created_at = $created_at;
                        $partnermessage->created_ip = $createdIp;
                        $partnermessage->created_by = Auth::User()->id;
                        $partnermessage->save();
                        
                        $memberrequestmail = DB::table('users')->where('id', $data->created_by)->get();
                        $memberrequestmail[0]->sender = Auth::User()->username;
                        CommonComponent::send_email(MEMBER_REQUEST,$memberrequestmail);
			echo "Request Pending";
                        
                    }else{
                        if(!empty($data_member) && $data_member->is_invited==0){
                            DB::table ( 'community_group_members' )
                                        ->where ( 'user_id', Auth::id() )
                                    ->where ( 'community_group_id', $groupid )
                                    ->update ( array (
						'is_invited' => 1,
                                                'is_approved' => 1,
                                                'updated_at' => $created_at,
                                                'updated_by' => Auth::id(),
                                                'updated_ip' => $createdIp,
				) );
                        }else{
                            
                            $communitygroupMember = new CommunityGroupMember();
                            $communitygroupMember->community_group_id = $groupid;
                            $communitygroupMember->is_approved = 1;			
                            $communitygroupMember->user_id    = Auth::id();
                            $communitygroupMember->created_by = Auth::id();
                            $communitygroupMember->created_at = $created_at;
                            $communitygroupMember->created_ip = $createdIp;
                            $communitygroupMember->save();
                        }
                        //message to admin
                        $partnermessage = new UserMessage();
                        $partnermessage->lkp_service_id = 0;
                        $partnermessage->sender_id = Auth::User()->id;
                        $partnermessage->recepient_id = $data->created_by;
                        $partnermessage->lkp_message_type_id = 8;
                        $partnermessage->message_no = $randnumber;
                        $partnermessage->subject = "New Group Member";
                        $partnermessage->message = "New Member ADDED TO THE GROUP";
                        $partnermessage->is_read = 0;
                        $partnermessage->created_at = $created_at;
                        $partnermessage->created_ip = $createdIp;
                        $partnermessage->created_by = Auth::User()->id;
                        $partnermessage->save();
                        
                        $memberrequestmail = DB::table('users')->where('id', $data->created_by)->get();
                        $memberrequestmail[0]->sender = Auth::User()->username;
                        CommonComponent::send_email(NEW_GROUP_MEMBER,$memberrequestmail);
			echo "Post Message";
                    }
                } catch (Exception $ex) {

                }
        }
        
        /**
         * Community Insert Main Post comment Data.
         * Date- 11-04-2016 (srinu start here)
         * Params { $_REQUEST['community_post_id'] -> Post Main id}
         */
        public static function insertCommunityPostMainComment() {        	
        	Log::info('Community Insert Post comments:'.Auth::id(),array('c'=>'1'));
        	try {
        		
                        $created_at = date('Y-m-d H:i:s');
        		$createdIp = $_SERVER ['REMOTE_ADDR'];
                        if(!isset($_REQUEST['commentId']) ){
                            $postComment = new CommunityGroupComment();        		
                            $postComment->comments = $_REQUEST['postCommentDesc'];
                            $postComment->community_group_id = $_REQUEST['groupId'];
                            $postComment->is_reply = 1;
                            $postComment->reply_to_comment_id = $_REQUEST['postcommentId'];
                            $postComment->user_id =    Auth::id();
                            $postComment->created_by = Auth::id();
                            $postComment->created_at = $created_at;
                            $postComment->created_ip = $createdIp;
                            $postComment->save(); 

                            //Message stored process in db
                            $replyPostCommentId = $_REQUEST['postcommentId'];
                            $getPostUserId = DB::table('community_group_comments')			
                            ->join  ( 'users as us', 'community_group_comments.user_id', '=', 'us.id' )
                            ->where('reply_to_comment_id', '=', $replyPostCommentId)                            
                            ->select('community_group_comments.user_id','us.username')
                            ->first();
                            
                            $msgid          =   CommonComponent::getMessageID();
                            $created_year   = date('Y');
                            $randnumber     = 'COMMUNITY/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                            $partnermessage = new UserMessage();
                            $partnermessage->lkp_service_id = 0;
                            $partnermessage->sender_id = Auth::User()->id;
                            $partnermessage->recepient_id = $getPostUserId->user_id;
                            $partnermessage->lkp_message_type_id = 8;
                            $partnermessage->message_no = $randnumber;
                            $partnermessage->subject = "New Post Comment";
                            $partnermessage->message = "$getPostUserId->username posted comment on your post";
                            $partnermessage->is_read = 0;
                            $partnermessage->created_at = $created_at;
                            $partnermessage->created_ip = $createdIp;
                            $partnermessage->created_by = Auth::User()->id;
                            $partnermessage->save();                            
                            //send mail for post user
                            $memberrequestmail = DB::table('users')->where('id', $getPostUserId->user_id)->get();
                            $memberrequestmail[0]->sender = Auth::User()->username;
                            CommonComponent::send_email(NEW_POST_COMMENT,$memberrequestmail);                                                        
                            
                            $getAllPostCommentIds = DB::table('community_group_comments')		
                            ->join  ( 'users as us', 'community_group_comments.user_id', '=', 'us.id' )
                            ->where('reply_to_comment_id', '=', $replyPostCommentId) 
                            ->where('user_id', '!=', Auth::id())
                            ->where('user_id', '!=', $getPostUserId->user_id)
                            ->distinct('community_group_comments.user_id')
                            ->select('community_group_comments.user_id','us.username')
                            ->get();
                            foreach($getAllPostCommentIds as $getallIds) {
                            //Send Message 
                                $partnermessage = new UserMessage();
                                $partnermessage->lkp_service_id = 0;
                                $partnermessage->sender_id = Auth::User()->id;
                                $partnermessage->recepient_id = $getallIds->user_id;
                                $partnermessage->lkp_message_type_id = 8;
                                $partnermessage->message_no = $randnumber;
                                $partnermessage->subject = "New Post Comment";
                                $partnermessage->message = "$getPostUserId->username has commented";
                                $partnermessage->is_read = 0;
                                $partnermessage->created_at = $created_at;
                                $partnermessage->created_ip = $createdIp;
                                $partnermessage->created_by = Auth::User()->id;
                                $partnermessage->save(); 
                                //Send Mail 
                                $memberrequestmail = DB::table('users')->where('id', $getallIds->user_id)->get();
                                $memberrequestmail[0]->sender = Auth::User()->username;
                                CommonComponent::send_email(NEW_POST_COMMENT,$memberrequestmail);                                                                                        
                            }
                            
                            //End Messages here

                            $timestamp = $postComment->created_at;
                            $splits =  explode(" ",$timestamp);
                            $get_craetd_date = $splits[0];
                            $getPostCreationDate    =   CommonComponent::checkAndGetDate($get_craetd_date);
                            $txt='<div class="col-md-12 padding-none form-control-fld" id="hide_delete_post_dev_'.$postComment->id.'">
                                                    <div class="pull-right gray">'.$getPostCreationDate;
                            $txt.= '<a id="edit_comment_text_'.$postComment->id.'" edit-id="'.$postComment->id.'" edit_tect_val="'.$postComment->reply_to_comment_id.'" class="edit_cmnt_text"><i class="fa fa-edit red" title="Edit"></i></a> 
                                                 <a data-target="#deletepostcomment" data-toggle="modal" id="post_cmnt_delete_'.$postComment->id.'" class="delete_post_comment" del-id="'.$postComment->id.'" ><i class="fa fa-trash red" title="Delete"></i></a> ';
                            $txt.= '</div><div class="user-pic pull-left">';
                            $profiledetails = CommonComponent::getUserDetails(Auth::User()->id);
                            if($profiledetails->lkp_role_id == 2){
                                $url = "uploads/seller/$profiledetails->id/";
                            }else{
                                $url = "uploads/buyer/$profiledetails->id/";
                            }
                            $getlogo = $url.$profiledetails->user_pic;
                            $logo =  CommonComponent::str_replace_last( '.' , '_94_92.' , $getlogo ); 
                            if($profiledetails->user_pic!='' && file_exists($logo)){
                                $txt.='<img class="img-responsive" src="'.url($logo).'" >';
                            }else{
                                $txt.='<i class="fa fa-user"></i>';
                            }
                            $txt.='</div><span class="user-name pull-left"><strong>'.Auth::User()->username.'</strong> <span id="update_cmnt_'.$postComment->id.'">';
                            $txt.=$postComment->comments.'</span> <div class="feed-links"><span id="post_like_'.$postComment->id.'" class="post_likes change_likes_text" like-id="'.$postComment->id.'">Like</span>
                                <span class="likes"><i class="fa fa-thumbs-o-up"></i> <span id="post_likes_count_'.$postComment->id.'" >0</span></span>'
                                    . '<span class="time">-'.date('j F \a\t G:i', strtotime($postComment->created_at)).''
                                    . '</span>
                                    </div></span></div>';
                            echo $txt;
                        }else{
                            DB::table ( 'community_group_comments' )
                                        ->where ( 'user_id', Auth::id() )
                                    ->where ( 'community_group_id', $_REQUEST['groupId'] )
                                    ->where ( 'id', $_REQUEST['commentId'])
                                        ->update ( array (
						'comments' => $_REQUEST['postCommentDesc'],
                                                'updated_at' => $created_at,
                                                'updated_by' => Auth::id(),
                                                'updated_ip' => $createdIp,
				) );
                            echo $_REQUEST['postCommentDesc'];
                        }
        	} catch (Exception $e) {
        
        	}
        }
        

        /**
         * Community Insert Post Likes
         * Date- 11-04-2016 (srinu start here)
         * Params { $_REQUEST['community_post_id'] -> Post Main id}
         */
        public static function insertPostLikes() {
        	Log::info('Community Insert Post likes:'.Auth::id(),array('c'=>'1'));
        	try {
        		
        		$postLikesCount=DB::table ( 'community_group_likes' )
        		->where ( 'user_id', Auth::id() )->where ( 'community_group_comment_id', $_REQUEST['postId'] )
        		 ->select('id')->count(); 
        		if ($postLikesCount==0) {
        			$created_at = date('Y-m-d H:i:s');
        			$createdIp = $_SERVER ['REMOTE_ADDR'];
        			$postPostLikes = new CommunityGroupLike();
        			$postPostLikes->community_group_comment_id = $_REQUEST['postId'];
        			$postPostLikes->user_id =    Auth::id();
        			$postPostLikes->created_by = Auth::id();
        			$postPostLikes->created_at = $created_at;
        			$postPostLikes->created_ip = $createdIp;
        			$postPostLikes->save();        			      			
        		} else {
        			$postPostLikes = DB::table('community_group_likes')->where('community_group_comment_id','=',$_REQUEST['postId'])
        			->where('user_id','=',Auth::id())->delete();
        		} 
        		$postLikesCnt=CommunityComponent::postLikesCount($_REQUEST['postId']);        				
        		return $postLikesCnt;        		
        		
        	} catch (Exception $e) {
        
        	}
        }
	
        
        public function activate_member() {
		
		$userId = Auth::id();
		
		$person_id = '';
		// get the user id from the url
		if (isset ( $_GET ['u_id'] ) && $_GET ['u_id'] != '') {
			$person_id = $_GET ['u_id'];
			Log::info('Admin has clicked on the activation link send to his / her message:'.$person_id,array('c'=>'1'));
		}
		if (isset ( $_GET ['g_id'] ) && $_GET ['g_id'] != '') {
			$group_id = $_GET ['g_id'];
		}
		$data   =   CommunityComponent::getCommunityGroupData($group_id);
                //print_r($data);
                if($data->created_by==$userId){
			try {
                            $dat=DB::table ( 'community_group_members' )
                                        ->where ( 'user_id', $person_id )->where ( 'community_group_id', $group_id )->select('id','is_approved')->first();
                            if(!empty($dat) && $dat->is_approved==0){
                            DB::table ( 'community_group_members' )
                                        ->where ( 'user_id', $person_id )->where ( 'community_group_id', $group_id )
                                        ->update ( array (
						'is_approved' => 1
				) );
                            }else{
                                return redirect ( '/community/groupdetails/'.$group_id)->with ( 'message', 'Member already activated.' );
                            }
			} catch ( Exception $ex ) {
				echo $ex;die();
			}
			
			return redirect ( '/community/groupdetails/'.$group_id )->with ( 'message', 'Member activated successfully.' );
                }
		 
        }
        
        public function adminMemberActive($gid,$uid) {
        
	        try {	        	
   	
        		DB::table ( 'community_group_members' )
        		->where ( 'user_id', $uid )->where ( 'community_group_id', $gid )
        		->update ( array (
        		'is_approved' => 1
        		) );
        		return redirect ( '/community/groupdetails/'.$gid )->with ( 'message', 'Member activated successfully.' );
	        	
	        } catch ( Exception $ex ) {
	        	echo $ex;die();
	        }
        }
        
        /**
         * Community Update post deactivitated
         * Date- 12-04-2016 (srinu start here)
         * Params { $_REQUEST['community_post_id'] -> Post Main id}
         */
        public function GroupDeActivated($gid,$uid,$gstatus) {
        
        	try {
        		$msgid          =   CommonComponent::getMessageID();
                        $created_year   = date('Y');
                        $randnumber     = 'COMMUNITY/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                        $created_at     = date('Y-m-d H:i:s');
			$createdIp      = $_SERVER ['REMOTE_ADDR'];
                        DB::table ( 'community_groups' )
        		->where ( 'created_by', $uid )->where ( 'id', $gid )
        		->update ( array (
        		'is_confirmed' => $gstatus
        		) );
                        $members    =   CommunityComponent::getAllActiveMembers($gid);
        		if ($gstatus==0) {
                            foreach($members as $member){
                                $partnermessage = new UserMessage();
                                $partnermessage->lkp_service_id = 0;
                                $partnermessage->sender_id = Auth::User()->id;
                                $partnermessage->recepient_id = $member->user_id;
                                $partnermessage->lkp_message_type_id = 8;
                                $partnermessage->message_no = $randnumber;
                                $partnermessage->subject = "Group Deactivated";
                                $activation_url = '<a href="'.url () . '/community/groupdetails/' . $gid .'">click here</a>';
                                $partnermessage->message = "Admin Deactivated the Group <br>Please click on below link to proceed further<br>".$activation_url;
                                $partnermessage->is_read = 0;
                                $partnermessage->created_at = $created_at;
                                $partnermessage->created_ip = $createdIp;
                                $partnermessage->created_by = Auth::User()->id;
                                $partnermessage->save();
                                
                                //send amil
                                $memberrequestmail = DB::table('users')->where('id', $member->user_id)->get();
                                $memberrequestmail[0]->sender = Auth::User()->username;
                                CommonComponent::send_email(DEACTIVATED_GROUP,$memberrequestmail);
                            
                            }
                            
                            
        			return redirect ( '/community/groupdetails/'.$gid )->with ( 'message', 'Group Deactivated Successfully.' );
        		} else {
        			return redirect ( '/community/groupdetails/'.$gid )->with ( 'message', 'Group Activated Successfully.' );
        		}        		
        
        	} catch ( Exception $ex ) {
        		echo $ex;die();
        	}
        }
        
        public function getPartnerList() {
		///Log::info('get the buyer list in creating a seller post for post public:'.Auth::id(),array('c'=>'1'));
            try {
                
                $term = Input::get('search');
                $displayListArray = array();
                $nameListArray = array();
                $allNameList = CommonComponent::getPartnersList(Auth::id(),$term);
                $cnt = count($allNameList);
                if($cnt>0){
                    for($i=0; $i<$cnt; $i++ ){
                        $nameListArray["value"] = $allNameList[$i]->id;
                        $nameListArray["text"] = $allNameList[$i]->username." ".$allNameList[$i]->id;
                        array_push($displayListArray, $nameListArray);
                    }
                }else{
                    $nameListArray["value"] = "";
                    $nameListArray["text"] = "No results Found";
                    array_push($displayListArray, $nameListArray);
                }
                return $displayListArray;
		}
            catch (Exception $e) {

            }
	}
        
        public function inviteMember(){
            try {
                    $groupid    =   $_REQUEST['group_id'];
                    $recipient = $_REQUEST['message_to'];
                    $recipientArray = explode(',', $recipient);
                    $data   =   CommunityComponent::getCommunityGroupData($groupid);
                    $msgid  =   CommonComponent::getMessageID();
                    $created_year = date('Y');
                    $randnumber   = 'COMMUNITY/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 
                    
                        $created_at = date('Y-m-d H:i:s');
			$createdIp = $_SERVER ['REMOTE_ADDR'];
                        foreach ($recipientArray as $recipientId) {
                            if($recipientId!=''){
                            $data_mem=  CommunityComponent::getMemberData($groupid,$recipientId);
                            if(!empty($data_mem) && $data_mem->is_approved==0){
                                return redirect ( '/community/groupdetails/'.$groupid )->with ( 'message', 'Invitation Already sent.' );
                            }elseif(!empty($data_mem) && $data_mem->is_approved==1){
                                return redirect ( '/community/groupdetails/'.$groupid )->with ( 'message', 'Already Member in this Group.' );
                            }
                            else{
                                if(!empty($recipientId)){
                                    $communitygroupMember = new CommunityGroupMember();
                                    $communitygroupMember->community_group_id = $groupid;
                                    $communitygroupMember->is_approved = 0;	
                                    $communitygroupMember->is_invited  = 0;	
                                    $communitygroupMember->user_id    = $recipientId;
                                    $communitygroupMember->created_by = Auth::id();
                                    $communitygroupMember->created_at = $created_at;
                                    $communitygroupMember->created_ip = $createdIp;
                                    $communitygroupMember->save();

                                    //send messages to users
                                    $activation_url = '<a href="'.url () . '/community/groupdetails/' . $groupid .'">click here</a>';

                                    $partnermessage = new UserMessage();
                                    $partnermessage->lkp_service_id = 0;
                                    $partnermessage->sender_id = Auth::User()->id;
                                    $partnermessage->recepient_id = $recipientId;
                                    $partnermessage->lkp_message_type_id = 8;
                                    $partnermessage->message_no = $randnumber;
                                    //$partnermessage->subject = "Group Member Invitation";
                                    //$partnermessage->message = "Group Member Invitation given by ".Auth::id()." <br> $activation_url";
                                    $partnermessage->subject =$_REQUEST['message_subject'];
                                    $partnermessage->message =$_REQUEST['message_body'].'<br>Group Name:'.$data->group_name.'<br>Please click on below link to proceed further<br>'.$activation_url;
                                    $partnermessage->is_read = 0;
                                    $partnermessage->created_at = date('Y-m-d H:i:s');
                                    $partnermessage->created_ip = $_SERVER['REMOTE_ADDR'];
                                    $partnermessage->created_by = Auth::User()->id;
                                    $partnermessage->save();

                                    $memberrequestmail = DB::table('users')->where('id', $recipientId)->get();
                                    $memberrequestmail[0]->sender = Auth::User()->username;
                                    CommonComponent::send_email(MEMBER_INVITED,$memberrequestmail);
                                    return redirect ( '/community/groupdetails/'.$groupid )->with ( 'message', 'Invitation sent Successfully.' );
                                }   
                            }
                            }
                        }
            } catch (Exception $ex) {

            }
        }
        
        /**
         * Get Community Post Like TExt like or unlike
         * Date : 12-04-2016 (srinu code start here)
         * Params {{ $postId and $UserId }}
         */
        public static function postLikesTextChange(){
        
        	try {
        		$getPostLikesTextChange= CommunityComponent::postLikesTextChangeFun($_REQUEST['postId']);
        		return $getPostLikesTextChange;
        
        	} catch (Exception $e) {
        
        	}
        }
        
     /**
     * Delete member from Community
     * @input group member id
     * @return \Illuminate\Http\Response
     */
    public function delete($id,$uid) {
        Log::info('Delete group member id : ' . $id, array('c' => '1'));
        $msgid  =   CommonComponent::getMessageID();
        $created_year = date('Y');
        $randnumber   = 'COMMUNITY/' .$created_year .'/'. str_pad($msgid, 6, "0", STR_PAD_LEFT); 

        $data   =   CommunityComponent::getCommunityGroupData($id);
        DB::table('community_group_members')
                ->where('community_group_id','=',$id)
        			->where('user_id','=',$uid)->delete();
        $created_at     = date('Y-m-d H:i:s');
	$createdIp      = $_SERVER ['REMOTE_ADDR'];
        //send message
        $partnermessage = new UserMessage();
        $partnermessage->lkp_service_id = 0;
        $partnermessage->sender_id = Auth::User()->id;
        $partnermessage->recepient_id = $data->created_by;
        $partnermessage->lkp_message_type_id = 8;
        $partnermessage->message_no = $randnumber;
        $partnermessage->subject = "Member Exited from Group";
        $partnermessage->message = "Member Exited from Group";
        $partnermessage->is_read = 0;
        $partnermessage->created_at = $created_at;
        $partnermessage->created_ip = $createdIp;
        $partnermessage->created_by = Auth::User()->id;
        $partnermessage->save();
        //send amil
        $memberrequestmail = DB::table('users')->where('id', $data->created_by)->get();
        $memberrequestmail[0]->sender = Auth::User()->username;
        CommonComponent::send_email(MEMBER_EXIT_GROUP,$memberrequestmail);
        return redirect ( '/community/groupdetails/'.$id )->with ( 'message', 'Member Deleted successfully.' );
    }
    
    /**
     * Delete member from Community
     * @input group member id
     * @return \Illuminate\Http\Response
     */
    public function deleteGroupComment() {
    	Log::info('Delete Comment for post : ' . $_REQUEST['postId'], array('c' => '1'));
    	try {
    		$groupMemberDelete = DB::table('community_group_comments')
    		->where('id','=',$_REQUEST['postId'])->delete();
    		
    		return $groupMemberDelete;
    		
    	} catch (Exception $e) {
        
        }
    	
    }
        
    
    
        
}
