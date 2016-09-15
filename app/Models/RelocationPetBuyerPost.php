<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class RelocationPetBuyerPost extends Model
{
    // Table
    protected $table = 'relocationpet_buyer_posts';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = [
        'from_location_id', 
        'to_location_id', 'lkp_service_id', 'buyer_id', 'transaction_id', 'lkp_lead_type_id',
        'lkp_post_status_id', 'lkp_quote_access_id', 'dispatch_date', 'delivery_date',
        'is_delivery_flexible', 'lkp_pet_type_id', 'lkp_breed_type_id', 'lkp_cage_type_id',
        'created_ip','updated_ip','created_by','updated_by'
    ];

    public function getDispatchDateAttribute($value){
        return date('d/m/Y', strtotime($value));
    }

    public function getDeliveryDateAttribute($value){
        if($value=="" || $value=="0000-00-00")
            return "N/A";
        else
            return date('d/m/Y', strtotime($value));
    }

    /**
    * Get Buyer pet move post details based on posts id
    * @author Shriram
    */
    public function getPetmovePostDetails($buyer_id =0, $post_id = 0)
    {
        return RelocationPetBuyerPost::from('relocationpet_buyer_posts as rpetm')
            ->select( DB::raw("
                rpetm.*, lkp_c1.city_name as from_location_name, lkp_c2.city_name as to_location_name,
                lkp_ps.post_status, lkp_qc.quote_access, lkp_ptype.pet_type, lkp_ctype.cage_type, 
                lkp_ctype.cage_weight, lkp_btype.breed_type
            "))
            ->leftJoin('lkp_cities as lkp_c1', 'lkp_c1.id', '=', 'rpetm.from_location_id')
            ->leftJoin('lkp_cities as lkp_c2', 'lkp_c2.id', '=', 'rpetm.to_location_id')
            ->leftJoin('lkp_post_statuses as lkp_ps', 'lkp_ps.id', '=', 'rpetm.lkp_post_status_id')
            ->leftJoin('lkp_quote_accesses as lkp_qc', 'lkp_qc.id', '=', 'rpetm.lkp_quote_access_id')
            ->leftJoin('lkp_pet_types as lkp_ptype', 'lkp_ptype.id', '=', 'rpetm.lkp_pet_type_id')
            ->leftJoin('lkp_breed_types as lkp_btype', 'lkp_btype.id', '=', 'rpetm.lkp_breed_type_id')
            ->leftJoin('lkp_cage_types as lkp_ctype', 'lkp_ctype.id', '=', 'rpetm.lkp_cage_type_id')
            ->where([
                'rpetm.lkp_service_id' => RELOCATION_PET_MOVE,
                'rpetm.buyer_id' => $buyer_id,
                'rpetm.id'  => $post_id
            ])->first();
    }

}	