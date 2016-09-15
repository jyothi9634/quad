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
use Response;
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;
use App\Models\IctBuyerQuoteSellersQuotesPrice;
use App\Models\IctBuyerQuote;
use App\Models\IctBuyerQuoteItem;
use App\Models\Order;
use App\Models\LkpIctVehicle;
use App\Models\IctVehicleWalletTransaction;
use Log;

class MspController extends Controller {
	
	/**
	 * Displays a screen for creating Seller
	 * Quotes for Buyer posts under INTRACITY
	 */
	public function createSellerQuotes() {
		$buyerPostsList = array();
		$intracityVehiclesList = array();
		
		$buyerPostsList = CommonComponent::getBuyerIntracityPosts ();
		$intracityVehiclesList = CommonComponent::getIntracityVehiclesList ();
		if (! empty ( Input::all () )) {
			$data = Input::all ();
			$quote_id = $data ['buyer_quote_id'];
			
			$buyerId = MspController::getBuyerId ( $quote_id );
			$data ['buyer_id'] = $buyerId ['buyer_id'];
			$data ['buyer_quote_item_id'] = $buyerId ['item_id'];
			
			$isNewSellerQuote = MspController::checkSellerQuote ( $data );
			
			if ($isNewSellerQuote == 1) {
				
				$newSellerQuote = MspController::addSellerQuote ( $data );
				
				if ($newSellerQuote == 1) {
					
					return redirect ( 'msp/seller_quotes' )->with ( 'success_message', 'Seller quote is submitted successfully.' );
				} else {
					return redirect ( 'msp/seller_quotes' )->with ( 'error', 'Error occured while submitting seller quote' );
				}
			} else {
				return redirect ( 'msp/seller_quotes' )->with ( 'error', 'This vehicle has already submitted a quote' );
			}
		}
		
		return view ( 'msp.seller_quotes', array (
				'buyerPostsList' => $buyerPostsList,
				'vehiclesList' => $intracityVehiclesList 
		) );
	}
	/**
	 *
	 * Function to insert seller quotes in the database
	 *
	 * @param $data =>
	 *        	Posted Data through form by MSP
	 *        	
	 */
	public function addSellerQuote($data) {
		$SellersQuotesPrice = new IctBuyerQuoteSellersQuotesPrice ();
		
		$createdAt = date ( 'Y-m-d H:i:s' );
		$createdIp = $_SERVER ["REMOTE_ADDR"];
		
		$SellersQuotesPrice->buyer_id = $data ['buyer_id'];
		$SellersQuotesPrice->buyer_quote_item_id = $data ['buyer_quote_item_id'];
		$SellersQuotesPrice->initial_quote_price = $data ['initial_quote_price'];
		$SellersQuotesPrice->seller_acceptence = '0';
		$SellersQuotesPrice->initial_quote_created_at = $createdAt;
		$SellersQuotesPrice->lkp_ict_vehicle_id = $data ['lkp_ict_vehicle_id'];
		
		$SellersQuotesPrice->created_at = $createdAt;
		$SellersQuotesPrice->created_ip = $createdIp;
		$SellersQuotesPrice->created_by = '8';
		try {
			if ($SellersQuotesPrice->save ()) {
				return '1';
			} else {
				return '0';
			}
		} catch ( Exception $ex ) {
			return '0';
		}
	}
	
	/**
	 * Function to retrieve the buyer_id of the selected quote_id
	 *
	 * @param $quote_id(posted by
	 *        	create seller form)
	 */
	public function getBuyerId($quote_id = null) {
		$quoteDetails = IctBuyerQuote::where ( 'id', $quote_id )->first ();
		$quoteItemDetails = IctBuyerQuoteItem::where ( 'buyer_quote_id', $quote_id )->first ();
		
		$buyerQuoteDetails = [ 
				'buyer_id' => $quoteDetails->buyer_id,
				'item_id' => $quoteItemDetails->id 
		];
		
		return $buyerQuoteDetails;
	}
	
	/**
	 * Function to check for if selected vehicle has already quoted for the order
	 *
	 * @param $data(posted value)        	
	 */
	public function checkSellerQuote($data) {
		$sellerQuoteDetails = IctBuyerQuoteSellersQuotesPrice::where ( 'buyer_quote_item_id', $data ['buyer_quote_item_id'] )->where ( 'lkp_ict_vehicle_id', $data ['lkp_ict_vehicle_id'] )->first ();
		if (empty ( $sellerQuoteDetails )) {
			return '1';
		} else {
			return '0';
		}
	}
	
	/**
	 * |
	 * |
	 * |MSP ORDER CONFIRMATIONS
	 * |
	 * |
	 */
	public function confirmOrders() {
		$intracityOrdersList = CommonComponent::getIntracityOrders ();
		
		$intracityVehiclesList = CommonComponent::getIntracityVehiclesList ();
		if (! empty ( Input::all () )) {
			$data = Input::all ();
			
			$orderId = $data ['order_id'];
			$vehicleId = $data ['lkp_ict_vehicle_id'];
			
			// check if any vehicle is assigned already to the order
			$isVehicleExists = MspController::checkOrderConfirm ( $orderId );
			$isVehicleId = $isVehicleExists ['orderVehicle_id'];
			$buyerId = $isVehicleExists ['orderBuyer_id'];
			$data ['buyer_id'] = $buyerId;
			// check if vehicle has enough wallet balance to take order
			$isValidWallet = MspController::checkVehicleWallet ( $vehicleId );
			
			if (empty ( $isVehicleId )) { // Assign vehicle to orders if has balance more than 200 and orders don't have
			                              // any vehicles assigned already.
				if ($isValidWallet >= 200) {
					$data ['wallet_balance'] = $isValidWallet;
					
					$newSellerOrder = MspController::addSellerVehicle ( $data );
					
					if ($newSellerOrder == 1) {
						
						$currentValidWallet = MspController::checkVehicleWallet ( $vehicleId );
						
						$data ['current_wallet_balance'] = $currentValidWallet;
						
						// insert transaction details to vehicle_wallet_transactions table
						$newTransaction = MspController::addVehicleTransaction ( $data );
						
						if ($newTransaction == 1) {
							
							// Mail functionality to send email to buyer
							$userData = DB::table ( 'users' )
							->leftJoin ( 'orders', 'orders.buyer_id', '=', 'users.id' )
							->leftJoin ( 'lkp_ict_vehicles as liv', 'orders.lkp_ict_vehicle_id', '=', 'liv.id' )
							->where ( 'users.id', $data ['buyer_id'] )
							->where('orders.id',$orderId)
							->select ( 'orders.*','orders.order_no as Order_No', 'users.email', 'users.username', 'liv.vehicle_number' )
							->get ();
							
							if ($userData [0]->email) {
								
								CommonComponent::send_email ( INTRACITY_BUYER_ORDER_CONFIRMATION_MAIL, $userData );
							}
							
							return redirect ( 'msp/order_confirmation' )->with ( 'message', 'Vehicle has been assigned to order successfully.' );
						} else {
							return redirect ( 'msp/order_confirmation' )->with ( 'error', 'Error occured while saving transactional details' );
						}
					} else {
						return redirect ( 'msp/order_confirmation' )->with ( 'error', 'Error occured while assiging vehicle' );
					}
				} else {
					return redirect ( 'msp/order_confirmation' )->with ( 'error', 'Vehicle has wallet balance less than 200. Please recharge now.' );
				}
			} else {
				return redirect ( 'msp/order_confirmation' )->with ( 'error', 'Vehicle has already been assigned to this order.' );
			}
		}
		
		return view ( 'msp.order_confirmation', array (
				'ordersList' => $intracityOrdersList,
				'vehiclesList' => $intracityVehiclesList 
		) );
	}
	/**
	 * Update Orders table to assign vehicle no.
	 */
	public function addSellerVehicle($data) {
		$ict_vehicle_id = $data ['lkp_ict_vehicle_id'];
		$ict_order_id = $data ['order_id'];
		$walletAmount = $data ['wallet_balance'];
		
		$updatedAt = date ( 'Y-m-d H:i:s' );
		$updatedIp = $_SERVER ["REMOTE_ADDR"];
		
		try {
			
			if (Order::where ( "id", $ict_order_id )->update ( array (
					'lkp_order_status_id'=>ORDER_PICKUP_DUE,
					'lkp_ict_vehicle_id' => $ict_vehicle_id,
					'updated_at' => $updatedAt,
					'updated_by' => '8',
					'updated_ip' => $updatedIp 
			) )) {
				
				// Debit the order fees (200/-) from vehicle wallet
				$walletFinalAmount = ($walletAmount - 200);
				
				$vehicleNetAmount = $walletFinalAmount; // number_format ( $walletFinalAmount, 2 );
				
				LkpIctVehicle::where ( "id", $ict_vehicle_id )->update ( array (
						'wallet_net_amount' => $vehicleNetAmount 
				) );
				return '1';
			} else {
				return '0';
			}
		} catch ( Exception $ex ) {
			return '0';
		}
	}
	
	/**
	 * Check if any vehicle is assigned already to the order
	 */
	public function checkOrderConfirm($orderId) {
		$orderDetails = Order::where ( 'id', $orderId )->first ();
		$vehicle_id = $orderDetails->lkp_ict_vehicle_id;
		$orderBuyerId = $orderDetails->buyer_id;
		$ordersResult = [ 
				'orderVehicle_id' => $vehicle_id,
				'orderBuyer_id' => $orderBuyerId 
		];
		
		return $ordersResult;
	}
	
	/**
	 * check if vehicle has enough wallet balance to take order
	 */
	public function checkVehicleWallet($vehicleId) {
		$vehicleDetails = LkpIctVehicle::where ( 'id', $vehicleId )->first ();
		$walletBalance = $vehicleDetails->wallet_net_amount;
		
		return $walletBalance;
	}
	
	/**
	 *
	 * Inserting transactions in vehicle wallet transactions table
	 *
	 * @param
	 *        	posted data($data)
	 * @return boolean
	 */
	public function addVehicleTransaction($data) {
		$walletTransaction = new IctVehicleWalletTransaction ();
		
		$createdAt = date ( 'Y-m-d H:i:s' );
		$walletTransaction->lkp_ict_vehicle_id = $data ['lkp_ict_vehicle_id'];
		$walletTransaction->wallet_net_amount = $data ['current_wallet_balance'];
		$walletTransaction->buyer_id = $data ['buyer_id'];
		$walletTransaction->order_id = $data ['order_id'];
		$walletTransaction->transaction_amount = '200';
		$walletTransaction->created_at = $createdAt;
		
		try {
			if ($walletTransaction->save ()) {
				return '1';
			} else {
				return '0';
			}
		} catch ( Exception $ex ) {
			return '0';
		}
	}
	
	/**
	 * MSP will update the status of the order.
	 */
	public function viewUpdateOrders() {
		$intracityOrdersList = CommonComponent::getIntracityOrders ();
		
		return view ( 'msp.update_orders', array (
				'ordersList' => $intracityOrdersList 
		) );
	}
	public function updateOrders() {
		$data = Input::all ();
		if (! empty ( $data )) {
			$statusId = $data ['status'];
			$orderId = $data ['order_id'];
			$createdAt = date ( 'Y-m-d H:i:s' );
			$currentStatus = '';
			//get current order status
			$orderDetails = Order::where ( 'id', $orderId )->select('lkp_order_status_id')->first();
			$currentStatus = $orderDetails->lkp_order_status_id;
			
			if($statusId == '4'){
				if($currentStatus < 4){
					// update orders table
					Order::where ( "id", $orderId )->update ( array (
					'seller_pickup_date' => $createdAt,
					'lkp_order_status_id'=> $statusId
					) );
					echo "<span class='success-txt'>Order status has been changed to PICKED UP</span>";
				}else{echo "<span class='red'>Order has been already picked up</span>";}
			}
			
			if($statusId == '6'){
				if(($currentStatus > 3) && ($currentStatus < 6)){
					// update orders table
					Order::where ( "id", $orderId )->update ( array (
					'seller_pickup_date' => $createdAt,
					'lkp_order_status_id'=> $statusId
					) );
					echo "<span class='success-txt'>Order status has been changed to DELIVERED</span>";
				}elseif($currentStatus == 6){
					echo "<span class='red'>Order has already been DELIVERED</span>";
				}else{
					echo "<span class='red'>Firstly change the order status to 'PICKED UP'</span>";
				}
			}
			
		}
	}
}
