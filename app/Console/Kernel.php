<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Components\Matching\BuyerMatchingComponent;
use App\Components\Matching\SellerMatchingComponent;
use DB;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\Inspire::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->everyMinute();
                 //** cancelling posts which are lessthan todays dates **//
        /*DB::table('buyer_quote_items as bqi')
            ->where('lkp_post_status_id','=',OPEN)
            ->whereRaw('dispatch_date<CURDATE()' )
            ->update(array('lkp_post_status_id' =>CLOSED));

        DB::table('ptl_buyer_quote_items as bqi')
            ->join('ptl_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
            ->where('bqi.lkp_post_status_id','=',OPEN)
            ->where('bq.lkp_post_status_id','=',OPEN)
            ->whereRaw('bq.dispatch_date<CURDATE()' )
            ->update(array(
                'bq.lkp_post_status_id' =>CLOSED,
                'bqi.lkp_post_status_id' =>CLOSED));

        DB::table('rail_buyer_quote_items as bqi')
            ->join('rail_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
            ->where('bqi.lkp_post_status_id','=',OPEN)
            ->where('bq.lkp_post_status_id','=',OPEN)
            ->whereRaw('bq.dispatch_date<CURDATE()' )
            ->update(array(
                'bq.lkp_post_status_id' =>CLOSED,
                'bqi.lkp_post_status_id' =>CLOSED));

        DB::table('airdom_buyer_quote_items as bqi')
            ->join('airdom_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
            ->where('bqi.lkp_post_status_id','=',OPEN)
            ->where('bq.lkp_post_status_id','=',OPEN)
            ->whereRaw('bq.dispatch_date<CURDATE()' )
            ->update(array(
                'bq.lkp_post_status_id' =>CLOSED,
                'bqi.lkp_post_status_id' =>CLOSED));

        DB::table('airint_buyer_quote_items as bqi')
            ->join('airint_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
            ->where('bqi.lkp_post_status_id','=',OPEN)
            ->where('bq.lkp_post_status_id','=',OPEN)
            ->whereRaw('bq.dispatch_date<CURDATE()' )
            ->update(array(
                'bq.lkp_post_status_id' =>CLOSED,
                'bqi.lkp_post_status_id' =>CLOSED));

        DB::table('ocean_buyer_quote_items as bqi')
            ->join('ocean_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
            ->where('bqi.lkp_post_status_id','=',OPEN)
            ->where('bq.lkp_post_status_id','=',OPEN)
            ->whereRaw('bq.dispatch_date<CURDATE()' )
            ->update(array(
                'bq.lkp_post_status_id' =>CLOSED,
                'bqi.lkp_post_status_id' =>CLOSED));


        //Relocation buyer posts deleting
        DB::table('relocation_buyer_posts as bq')
            ->where('bq.lkp_post_status_id','=',OPEN)
            ->whereRaw('bq.dispatch_date<CURDATE()' )
            ->update(array(
                'bq.lkp_post_status_id' =>CLOSED));

        //courier buyer posts deleting
        DB::table('courier_buyer_quote_items as bqi')
            ->join('courier_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
            ->where('bqi.lkp_post_status_id','=',OPEN)
            ->where('bq.lkp_post_status_id','=',OPEN)
            ->whereRaw('bq.dispatch_date<CURDATE()' )
            ->update(array(
                'bq.lkp_post_status_id' =>CLOSED,
                'bqi.lkp_post_status_id' =>CLOSED));

        //truckhaul buyer posts deleting
        DB::table('truckhaul_buyer_quote_items as bqi')
            ->join('truckhaul_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
            ->where('bqi.lkp_post_status_id','=',OPEN)
            ->whereRaw('bqi.dispatch_date<CURDATE()' )
            ->update(array('bqi.lkp_post_status_id' =>CLOSED));
        //truckLease buyer posts deleting
        DB::table('trucklease_buyer_quote_items as bqi')
            ->join('trucklease_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
            ->where('bqi.lkp_post_status_id','=',OPEN)
            ->whereRaw('bqi.from_date<CURDATE()' )
            ->update(array('bqi.lkp_post_status_id' =>CLOSED));

        //Relocation pet buyer posts deleting
        DB::table('relocationpet_buyer_posts as bq')
            ->where('bq.lkp_post_status_id','=',OPEN)
            ->whereRaw('bq.dispatch_date<CURDATE()' )
            ->update(array(
                'bq.lkp_post_status_id' =>CLOSED));
        //Relocation office buyer posts deleting
        DB::table('relocationoffice_buyer_posts as bq')
            ->where('bq.lkp_post_status_id','=',OPEN)
            ->whereRaw('bq.dispatch_date<CURDATE()' )
            ->update(array(
                'bq.lkp_post_status_id' =>CLOSED));
        //Relocation int buyer posts deleting
        DB::table('relocationint_buyer_posts as bq')
            ->where('bq.lkp_post_status_id','=',OPEN)
            ->whereRaw('bq.dispatch_date<CURDATE()' )
            ->update(array(
                'bq.lkp_post_status_id' =>CLOSED));

        //intracity buyer posts deleting
        DB::table('ict_buyer_quote_items as bqi')
            ->join('ict_buyer_quotes as bq','bq.id','=','bqi.buyer_quote_id')
            ->where('bqi.lkp_post_status_id','=',OPEN)
            ->whereRaw( "CONCAT( 'bqi.pickup_date',' ', 'bqi.pickup_time')>=DATE_FORMAT( (NOW( ) - INTERVAL 1 DAY ) ,  '%Y-%m-%d %H:%i:%s') ")
            ->update(array('bqi.lkp_post_status_id' =>CLOSED));

        //ftl seller post deleting
        DB::table('seller_posts')
            ->join('seller_post_items','seller_posts.id','=','seller_post_items.seller_post_id')
            ->where('seller_posts.lkp_post_status_id','=',OPEN)
            ->where('seller_post_items.lkp_post_status_id','=',OPEN)
            ->whereRaw('seller_posts.to_date<CURDATE()')
            ->update(array(
                'seller_posts.lkp_post_status_id' =>CLOSED,
                'seller_post_items.lkp_post_status_id' =>CLOSED));
        //ptl seller post deleting
        DB::table('ptl_seller_post_items as spi')
            ->join('ptl_seller_posts as sp','sp.id','=','spi.seller_post_id')
            ->where('sp.lkp_post_status_id','=',OPEN)
            ->where('spi.lkp_post_status_id','=',OPEN)
            ->whereRaw('sp.to_date<CURDATE()' )
            ->update(array(
                'sp.lkp_post_status_id' =>CLOSED,
                'spi.lkp_post_status_id' =>CLOSED));

        //rail seller post deleting
        DB::table('rail_seller_post_items as spi')
            ->join('rail_seller_posts as sp','sp.id','=','spi.seller_post_id')
            ->where('sp.lkp_post_status_id','=',OPEN)
            ->where('spi.lkp_post_status_id','=',OPEN)
            ->whereRaw('sp.to_date<CURDATE()' )
            ->update(array(
                'sp.lkp_post_status_id' =>CLOSED,
                'spi.lkp_post_status_id' =>CLOSED));
        //airdom seller post deleting
        DB::table('airdom_seller_post_items as spi')
            ->join('airdom_seller_posts as sp','sp.id','=','spi.seller_post_id')
            ->where('sp.lkp_post_status_id','=',OPEN)
            ->where('spi.lkp_post_status_id','=',OPEN)
            ->whereRaw('sp.to_date<CURDATE()' )
            ->update(array(
                'sp.lkp_post_status_id' =>CLOSED,
                'spi.lkp_post_status_id' =>CLOSED));
        //airint seller post deleting
        DB::table('airint_seller_post_items as spi')
            ->join('airint_seller_posts as sp','sp.id','=','spi.seller_post_id')
            ->where('sp.lkp_post_status_id','=',OPEN)
            ->where('spi.lkp_post_status_id','=',OPEN)
            ->whereRaw('sp.to_date<CURDATE()' )
            ->update(array(
                'sp.lkp_post_status_id' =>CLOSED,
                'spi.lkp_post_status_id' =>CLOSED));

        //ocean seller post deleting
        DB::table('ocean_seller_post_items as spi')
            ->join('ocean_seller_posts as sp','sp.id','=','spi.seller_post_id')
            ->where('sp.lkp_post_status_id','=',OPEN)
            ->where('spi.lkp_post_status_id','=',OPEN)
            ->whereRaw('sp.to_date<CURDATE()' )
            ->update(array(
                'sp.lkp_post_status_id' =>CLOSED,
                'spi.lkp_post_status_id' =>CLOSED));
        //relocation seller post deleting
        DB::table('relocation_seller_post_items as spi')
            ->join('relocation_seller_posts as sp','sp.id','=','spi.seller_post_id')
            ->where('sp.lkp_post_status_id','=',OPEN)
            ->whereRaw('sp.to_date<CURDATE()' )
            ->update(array(
                'sp.lkp_post_status_id' =>CLOSED));
        //courier seller post deleting
        DB::table('courier_seller_post_items as spi')
            ->join('courier_seller_posts as sp','sp.id','=','spi.seller_post_id')
            ->where('sp.lkp_post_status_id','=',OPEN)
            ->where('spi.lkp_post_status_id','=',OPEN)
            ->whereRaw('sp.to_date<CURDATE()' )
            ->update(array(
                'sp.lkp_post_status_id' =>CLOSED,
                'spi.lkp_post_status_id' =>CLOSED));

        //truckhaul seller posts deleting
        DB::table('truckhaul_seller_post_items as spi')
            ->join('truckhaul_seller_posts as sp','sp.id','=','spi.seller_post_id')
            ->where('sp.lkp_post_status_id','=',OPEN)
            ->where('spi.lkp_post_status_id','=',OPEN)
            ->whereRaw('sp.to_date<CURDATE()' )
            ->update(array(
                'sp.lkp_post_status_id' =>CLOSED,
                'spi.lkp_post_status_id' =>CLOSED));
        //truckLease seller posts deleting
        DB::table('trucklease_seller_post_items as spi')
            ->join('trucklease_seller_posts as sp','sp.id','=','spi.seller_post_id')
            ->where('sp.lkp_post_status_id','=',OPEN)
            ->where('spi.lkp_post_status_id','=',OPEN)
            ->whereRaw('sp.to_date<CURDATE()' )
            ->update(array(
                'sp.lkp_post_status_id' =>CLOSED,
                'spi.lkp_post_status_id' =>CLOSED));
        //relocation pet seller post deleting
        DB::table('relocationpet_seller_posts as sp')
            ->where('sp.lkp_post_status_id','=',OPEN)
            ->whereRaw('sp.to_date<CURDATE()' )
            ->update(array(
                'sp.lkp_post_status_id' =>CLOSED));
        //relocation office seller post deleting
        DB::table('relocationoffice_seller_posts as sp')
            ->where('sp.lkp_post_status_id','=',OPEN)
            ->whereRaw('sp.to_date<CURDATE()' )
            ->update(array(
                'sp.lkp_post_status_id' =>CLOSED));
        //relocation int seller post deleting
        DB::table('relocationint_seller_posts as sp')
            ->where('sp.lkp_post_status_id','=',OPEN)
            ->whereRaw('sp.to_date<CURDATE()' )
            ->update(array(
                'sp.lkp_post_status_id' =>CLOSED));


        //term buyer quotes closing
        DB::table('term_buyer_quote_items as bqi')
            ->join('term_buyer_quotes as bq','bq.id','=','bqi.term_buyer_quote_id')
            //->where('bqi.lkp_post_status_id','=',OPEN)
            ->where('bq.lkp_post_status_id','=',OPEN)
            ->whereRaw('bq.from_date<CURDATE()' )
            ->update(array('bq.lkp_post_status_id' =>CLOSED));*/

    }
}
