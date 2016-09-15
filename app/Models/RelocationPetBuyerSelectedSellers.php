<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class RelocationPetBuyerSelectedSellers extends Model
{
    // Table
    protected $table = 'relocationpet_buyer_selected_sellers';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];

    protected $fillable = [
        'lkp_service_id', 'buyer_post_id', 'seller_id', 'created_ip','updated_ip','created_by','updated_by'
    ];
    
}