<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class SellerDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'seller_details';

    /**
     * Getting Complete Seller Individual details
     *
     * @return object
     */
    public static function getSellerIndividualDetails( $userid = 0)
    {
    	return Seller::from('seller_details as s')
    		->select( 
    			DB::raw("s.*, lkp_empst.employee_strength, lkp_spl.speciality_name, lkp_ind.industry_name
    			") 
    		)
    		->leftJoin('lkp_industries as lkp_ind', 'lkp_ind.id', '=', 's.lkp_industry_id')
    		->leftJoin('lkp_employee_strengths as lkp_empst', 'lkp_empst.id', '=', 's.lkp_employee_strength_id')
    		->leftJoin('lkp_specialities as lkp_spl', 'lkp_spl.id', '=', 's.lkp_speciality_id')
    		->where('s.user_id', '=', $userid)
    		->first();
    }

}
