<?php
namespace App\Components\Community;
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
use Log;

class SearchComponent {
    
        /**
	 * Community Search
	 * @param $params
	 * result $query
	 */
	public static function search($type, $category=NAME,$params){
		
		
		switch ($type) {
			case INDIVIDUAL       :
				$queryBuilder = SearchComponent::getIndividualSearchResults($category,$params);
				break;
			case ORGANIZATION:
				$queryBuilder = SearchComponent::getOrganizationSearchResults($category,$params);
				break;
			case GROUP:
				$queryBuilder = SearchComponent::getGroupSearchResults($category,$params);
				break;
			
			default             :
				break;
		}


		return $queryBuilder;
	}
	/**
	 * Individual Search
	 * @param $params
	 * result $query
	 */
	public static function getIndividualSearchResults($category=NAME,$params){

		// Below script for individual search 
		
                //individual 
                $gridBuyer = DB::table ( 'users as u' );
                $gridBuyer->leftjoin ( 'seller_details as seller', 'u.id', '=', 'seller.user_id' );
                $gridBuyer->leftjoin ( 'seller_services as ss', 'ss.user_id', '=', 'seller.user_id');
                $gridBuyer->leftjoin ( 'lkp_industries as sindustry', 'sindustry.id', '=', 'seller.lkp_industry_id' );
                $gridBuyer->leftjoin ( 'buyer_details as bd', 'u.id', '=', 'bd.user_id' );
                $gridBuyer->leftjoin ( 'lkp_industries as bindustry', 'bindustry.id', '=', 'bd.lkp_industry_id' );

                switch ($category) {
                    case NAME :
                        //$gridBuyer->whereRaw('((seller.contact_firstname like "%'.$params['search'].'%" or `seller`.`contact_lastname` like "%'.$params['search'].'%" or CONCAT(seller.contact_firstname, " ", seller.contact_lastname) like "%'.$params['search'].'%") or (bd.firstname like "%'.$params['search'].'%" or `bd`.`lastname` like "%'.$params['search'].'%" or CONCAT(bd.firstname, " ", bd.lastname) like "%'.$params['search'].'%") or u.username like "%'.$params['search'].'%" )');
                    	$gridBuyer->whereRaw('u.username like "%'.$params['search'].'%" ');
                        break;
                    case COMPANY :
                        $gridBuyer->whereRaw('(seller.name like "%'.$params['search'].'%" or bd.name like "%'.$params['search'].'%")');
                        break;
                    case INDUSTRY :
                        $gridBuyer->whereRaw('(sindustry.industry_name like "%'.$params['search'].'%" or bindustry.industry_name like "%'.$params['search'].'%")');
                        break;
                    case LOCATION :
                        $gridBuyer->whereRaw('(seller.principal_place like "%'.$params['search'].'%" or bd.principal_place like "%'.$params['search'].'%")');
                        break;
                    default :
                            break;
                }
                // service filter
                /*if (isset ( $params ['service_id'] ) && $params ['service_id'] != '') {
                    $gridBuyer->where ( 'ss.lkp_service_id', $params ['service_id'] );
                }*/
                // Industry filter
                if (isset ( $params ['industry_id'] ) && $params ['industry_id'] != '') {
                    $gridBuyer->whereRaw ( '(seller.lkp_industry_id='. $params ['industry_id'].' or bd.lkp_industry_id='. $params ['industry_id'].')' );
                }
                // Location filter
                if (isset ( $params ['location'] ) && $params ['location'] != '') {
                    $gridBuyer->whereRaw ( '(seller.principal_place="'. $params ['location'].'" or bd.principal_place="'. $params ['location'].'")' );
                }
                // Speciality filter
                if (isset ( $params ['speciality'] ) && $params ['speciality'] != '') {
                    $gridBuyer->whereRaw ( '(seller.lkp_speciality_id='. $params ['speciality'].' )' );
                }    
                $gridBuyer->where('u.is_business','=',0);
                $name="u.username";
				$gridBuyer->select ('u.id','u.logo',DB::Raw("(case when `u`.`lkp_role_id` = 2 then seller.lkp_speciality_id end) as speciality_id"),
                        DB::Raw("$name as name") , 
                        DB::Raw("(case when `u`.`lkp_role_id` = 2 then sindustry.industry_name  else bindustry.industry_name end) as industry_name"),
                        DB::Raw("(case when `u`.`lkp_role_id` = 2 then sindustry.id  else bindustry.id end) as industry_id"),
                        DB::Raw("(case when `u`.`lkp_role_id` = 2 then seller.principal_place  else bd.principal_place end) as principal_place"),
                        DB::Raw("(case when `u`.`lkp_role_id` = 2 then seller.description  else bd.description end) as description")
                        );
		$gridBuyer->groupBy('u.id');

		return $gridBuyer;
	}
        
        /**
	 * Organization Search
	 * @param $params
	 * result $query
	 */
	public static function getOrganizationSearchResults($category=NAME,$params){

                $gridBuyer = DB::table ( 'users as u' );
                $gridBuyer->leftjoin ( 'sellers as seller', 'u.id', '=', 'seller.user_id' );
                $gridBuyer->leftjoin ( 'seller_services as ss', 'ss.user_id', '=', 'seller.user_id');
                $gridBuyer->leftjoin ( 'lkp_industries as sindustry', 'sindustry.id', '=', 'seller.lkp_industry_id' );
                $gridBuyer->leftjoin ( 'buyer_business_details as bd', 'u.id', '=', 'bd.user_id' );
                $gridBuyer->leftjoin ( 'lkp_industries as bindustry', 'bindustry.id', '=', 'bd.lkp_industry_id' );
                $name= "u.username";
                $bname="CONCAT(bd.contact_firstname,' ',bd.contact_lastname)";
                $sname="CONCAT(seller.contact_firstname,' ',seller.contact_lastname)";
                switch ($category) {
                    case NAME :
                        //$gridBuyer->whereRaw('((seller.contact_firstname like "%'.$params['search'].'%" or `seller`.`contact_lastname` like "%'.$params['search'].'%" or '.$sname.' like "%'.$params['search'].'%") or (bd.contact_firstname like "%'.$params['search'].'%" or `bd`.`contact_lastname` like "%'.$params['search'].'%" or '.$bname.' like "%'.$params['search'].'%") or u.username like "%'.$params['search'].'%")');
                    	$gridBuyer->whereRaw('u.username like "%'.$params['search'].'%"');
                        break;
                    case COMPANY :
                        $gridBuyer->whereRaw('(seller.name like "%'.$params['search'].'%" or bd.name like "%'.$params['search'].'%")');
                        break;
                    case INDUSTRY :
                        $gridBuyer->whereRaw('(sindustry.industry_name like "%'.$params['search'].'%" or bindustry.industry_name like "%'.$params['search'].'%")');
                        break;
                    case LOCATION :
                        $gridBuyer->whereRaw('(seller.principal_place like "%'.$params['search'].'%" or bd.principal_place like "%'.$params['search'].'%")');
                        break;
                    default :
                            break;
                }
                // service filter
                /*if (isset ( $params ['service_id'] ) && $params ['service_id'] != '') {
                    $gridBuyer->where ( 'ss.lkp_service_id', $params ['service_id'] );
                }*/
                // Industry filter
                if (isset ( $params ['industry_id'] ) && $params ['industry_id'] != '') {
                    $gridBuyer->whereRaw ( '(seller.lkp_industry_id='. $params ['industry_id'].' or bd.lkp_industry_id='. $params ['industry_id'].')' );
                }
                // Location filter
                if (isset ( $params ['location'] ) && $params ['location'] != '') {
                    $gridBuyer->whereRaw ( '(seller.principal_place="'. $params ['location'].'" or bd.principal_place="'. $params ['location'].'")' );
                }
                // Speciality filter
                if (isset ( $params ['speciality'] ) && $params ['speciality'] != '') {
                    $gridBuyer->whereRaw ( '(seller.lkp_speciality_id='. $params ['speciality'].' or bd.lkp_speciality_id='. $params ['speciality'].')' );
                }    
                $gridBuyer->where('u.is_business','=',1);
		$gridBuyer->select ('u.id','u.logo',DB::Raw("(case when `u`.`lkp_role_id` = 2 then seller.lkp_speciality_id end) as speciality_id"),
                        DB::Raw(" $name as name") , 
                        DB::Raw("(case when `u`.`lkp_role_id` = 2 then sindustry.industry_name  else bindustry.industry_name end) as industry_name"),
                        DB::Raw("(case when `u`.`lkp_role_id` = 2 then sindustry.id  else bindustry.id end) as industry_id"),
                        DB::Raw("(case when `u`.`lkp_role_id` = 2 then seller.principal_place  else bd.principal_place end) as principal_place"),
                        DB::Raw("(case when `u`.`lkp_role_id` = 2 then seller.description  else bd.description end) as description")
                        );
                $gridBuyer->groupBy('u.id');

		return $gridBuyer;
	}
        
        /**
	 * Group Search
	 * @param $params
	 * result $query
	 */
	public static function getGroupSearchResults($category=NAME,$params){
		$gridBuyer = DB::table ( 'community_groups as cg' );
        $gridBuyer->whereRaw('(cg.group_name like "%'.$params['search'].'%" )');
        $gridBuyer->select ('cg.created_by','cg.id','cg.group_name as name', 'cg.description as description','cg.is_private as is_private', 'cg.logo_file_name as logo');
		$gridBuyer->groupBy('cg.id');

		return $gridBuyer;
	}
        
        
        
}
