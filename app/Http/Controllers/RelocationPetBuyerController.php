<?php

namespace App\Http\Controllers;

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

class RelocationPetBuyerController extends Controller {

    public function __construct() {
        $this->middleware('auth');
    }
    
    /*
     * Create a new petmove buyer controller
     * Start
     * Create buyer quote here
     * @author Srinu Date:6th,May2016
     */
    public function CreateBuyerQuote() {

    }   
    

}

// Controller not required