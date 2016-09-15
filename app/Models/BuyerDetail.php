<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class BuyerDetail extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'buyer_details';

    /**
     * Getting Complete Buyer Individual details
     *
     * @return object
     */
    public static function getBuyerIndividualDetails( $userid = 0)
    {
    	return BuyerDetail::from('buyer_details as b')
    		->select(DB::raw("b.*, lkp_ind.industry_name"))
    		->leftJoin('lkp_industries as lkp_ind', 'lkp_ind.id', '=', 'b.lkp_industry_id')
    		->where('b.user_id', '=', $userid)
    		->first();
    }
    
}