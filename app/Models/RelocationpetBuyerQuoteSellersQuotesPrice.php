<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;

class RelocationpetBuyerQuoteSellersQuotesPrice extends Model
{
    // Table
    protected $table = 'relocationpet_buyer_quote_sellers_quotes_prices';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at'];
    
}