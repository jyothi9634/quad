<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class BuyerBusinessDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'buyer_business_details';


    /**
     * Getting Complete Buyer Business details
     *
     * @return object
     */
    public static function getBuyerBusinessDetails( $userid = 0)
    {
    	return BuyerBusinessDetail::from( 'buyer_business_details as buyerbd')
    		->select( 
    			DB::raw("buyerbd.*, lkp_btps.business_type_name, lkp_c.country_name, lkp_st.state_name,
    				lkp_empst.employee_strength, lkp_spl.speciality_name, lkp_ind.industry_name
    			") 
    		)
    		->leftJoin('lkp_business_types as lkp_btps', 'lkp_btps.id', '=', 'buyerbd.lkp_business_type_id')
    		->leftJoin('lkp_industries as lkp_ind', 'lkp_ind.id', '=', 'buyerbd.lkp_industry_id')
    		->leftJoin('lkp_countries as lkp_c', 'lkp_c.id', '=', 'buyerbd.lkp_country_id')
    		->leftJoin('lkp_states as lkp_st', 'lkp_st.id', '=', 'buyerbd.lkp_state_id')
    		->leftJoin('lkp_employee_strengths as lkp_empst', 'lkp_empst.id', '=', 'buyerbd.lkp_employee_strength_id')
    		->leftJoin('lkp_specialities as lkp_spl', 'lkp_spl.id', '=', 'buyerbd.lkp_speciality_id')
    		->where('buyerbd.user_id', '=', $userid)
    		->first();
    }
}

