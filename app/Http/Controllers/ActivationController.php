<?php

namespace App\Http\Controllers;

use Validator;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Components\CommonComponent;
use DB;
use Input;
use Auth;
use Config;
use File;
use Session;
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use App\Models\BuyerDetail;
use App\Models\BuyerBusinessDetail;
use App\Models\Seller;
use App\Models\User;
use Log;

class ActivationController extends Controller {
	/**
	 * Create a new authentication controller instance.
	 *
	 * @return void
	 */
	
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index() {
		
		
		// $userId = Auth::User()->user_id;
		$userId;
		
		
		$User = new User ();
		
		$person_id = '';
		$role_id = '';
		$key = '';
		$key_validated = '';
		// declaring the person id and key variable
		
		// get the email id from the url
		
		if (isset ( $_GET ['role_id'] ) && $_GET ['role_id'] != '') {
			$role_id = $_GET ['role_id'];
		}
		if (isset ( $_GET ['u_id'] ) && $_GET ['u_id'] != '') {
			$person_id = $_GET ['u_id'];
			Log::info('User has clicked on the activation link send to his / her email:'.$person_id,array('c'=>'1'));
		}
		
		if (isset ( $_GET ['key'] ) && $_GET ['key'] != '') {
			$key = $_GET ['key'];
		}
		
		
		if ($role_id == 1) {
			
			try {
				DB::table ( 'users' )->where ( 'id', $person_id )->update ( array (
						'is_confirmed' => 1,
						'is_active' => 0,
						'is_approved' => 1,
						'activation_key' => NULL 
				) );
			} catch ( Exception $ex ) {
				echo $ex;die();
			}
			Session::flush (); // unset $_SESSION variable for the run-time
			                    // destroy session data in storage
			
			return redirect ( '/individualRegistration?status=success&user_id='.$person_id )->with ( 'message', 'Congratulations ..!! Your account has been activated successfully.' );
		} 

		elseif ($role_id == 2) {
			
			$user = DB::table ( 'users' )->where ( 'id', $person_id )->first ();
			
			$stored_key = $user->activation_key;
			
			if (isset ( $stored_key ) && $stored_key != '') {
				
				if ($stored_key == $key) {
					$key_validated = 1;
				} else {
					$key_validated = 0;
				}
			}
			
			if ($key_validated == 1) {
				
				// try block to execute sql statements
				
				try {
					DB::table ( 'users' )->where ( 'id', $person_id )->update ( array (
							'is_confirmed' => 1,
							'is_approved' => 1,
							'is_active' => 1,
							'activation_key' => NULL 
					) );
					
					Session::flush (); // clears out all the exisiting sessions
					                   // session destroy
					
					return redirect ( '/auth/login' )->with ( 'message', 'Congratulations ..!! Your account has been activated successfully.' );
				} 

				catch ( Exception $e ) {
				}
			} 

			elseif ($key_validated == 0) {
				
				echo "<h1>Link Expired. Please try Again</h1><br><h2>Please click <a href='{{url()}}'>Here to redirect to Login Page</h2></a>";
				die ();
			}
		}
	}
	
	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
	}
	
	/**
	 * Store a newly created resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request        	
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request) {
		//
	}
	
	/**
	 * Display the specified resource.
	 *
	 * @param int $id        	
	 * @return \Illuminate\Http\Response
	 */
	public function show($id) {
		//
	}
	
	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param int $id        	
	 * @return \Illuminate\Http\Response
	 */
	public function edit($id) {
		//
	}
	
	/**
	 * Update the specified resource in storage.
	 *
	 * @param \Illuminate\Http\Request $request        	
	 * @param int $id        	
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id) {
		//
	}
	
	/**
	 * Remove the specified resource from storage.
	 *
	 * @param int $id        	
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id) {
		//
	}
}
