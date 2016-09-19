<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class Seller extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sellers';


    /**
     * Getting Complete Seller Business details
     *
     * @return object
     */
    public static function getSellerBusinessDetails( $userid = 0)
    {
    	return Seller::from('sellers as s')
    		->select( 
    			DB::raw("s.*, lkp_btps.business_type_name, lkp_c.country_name, lkp_st.state_name,
    				lkp_empst.employee_strength, lkp_spl.speciality_name, lkp_ind.industry_name
    			") 
    		)
    		->leftJoin('lkp_business_types as lkp_btps', 'lkp_btps.id', '=', 's.lkp_business_type_id')
    		->leftJoin('lkp_industries as lkp_ind', 'lkp_ind.id', '=', 's.lkp_industry_id')
    		->leftJoin('lkp_countries as lkp_c', 'lkp_c.id', '=', 's.lkp_country_id')
    		->leftJoin('lkp_states as lkp_st', 'lkp_st.id', '=', 's.lkp_state_id')
    		->leftJoin('lkp_employee_strengths as lkp_empst', 'lkp_empst.id', '=', 's.lkp_employee_strength_id')
    		->leftJoin('lkp_specialities as lkp_spl', 'lkp_spl.id', '=', 's.lkp_speciality_id')
    		->where('s.user_id', '=', $userid)
    		->first();
    }

    public function userSales($id,$inputs){
        
        $base_query = DB::table('orders as od')
                       ->leftJoin('seller_details as sd','sd.id','=','od.seller_id') 
                       ->leftJoin('buyer_details as bd','bd.id','=','od.buyer_id')   
                       ->leftJoin('lkp_services as lkps','lkps.id','=','od.lkp_service_id')   
                       ->select('bd.firstname','lkps.service_name','od.created_at','od.price','od.order_payment_id');
        
                       
        if(empty($inputs)){

          $sub_query = $base_query->where('sd.user_id',$id);
        
        }
        else{
          
          if(!empty($inputs['seller_name']))
          $sub_query = $base_query->where('bd.id',$inputs['seller_name']);
          
        }               

        $final_query = $sub_query->orderBy('od.created_at','desc')->get();

        return $final_query;
    }

   public function getBuyers($id){


        return DB::table('orders as od')
               ->leftJoin('seller_details as sd','sd.id','=','od.seller_id') 
               ->leftJoin('buyer_details as bd','bd.id','=','od.buyer_id')   
               ->leftJoin('lkp_services as lkps','lkps.id','=','od.lkp_service_id')   
               ->select('bd.firstname as buyer','bd.id','lkps.service_name','lkps.id as service_id')
               ->where('sd.user_id',$id)
               ->orderBy('od.created_at','desc')
               ->get();

               

   } 

}
