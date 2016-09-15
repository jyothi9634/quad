<?php
//neft constants
define('NEFT_ACCOUNT_NAME', 'Cost Wise Supply Chain Private Limited');
define('NEFT_ACCOUNT_NUMBER', '50200019904704');
define('NEFT_BANK', 'HDFC');
define('NEFT_BRANCH', 'Banjara Hills');
define('NEFT_IFSC_CODE', 'HDFC0003860'); 

//term or spot
define('SPOT', 1);
define('TERM', 2);
define('IS_TERM', 1);
//Definition of all user roles as in the database
define('SELLER', '2');
define('BUYER', '1');
define('ADMIN', '1');
//Definition of various Services
define('ROAD_FTL', 1);
define('ROAD_PTL', 2);
define('ROAD_INTRACITY', 3);
define('ROAD_TRUCK_HAUL', 4);
define('ROAD_TRUCK_LEASE', 5);  
define('RAIL', 6);
define('AIR_DOMESTIC', 7);
define('AIR_INTERNATIONAL', 8);
define('OCEAN', 9);
define('MULTIMODEL', 10);
define('HANDLING_SERVICES', 11);
define('EQUIPMENT_LEASE', 12);
define('PACKAGING_SERVICES', 13);
define('WAREHOUSE', 14);
define('RELOCATION_DOMESTIC', 15);
define('THIRD_PARTY_LOGISTICS', 16);
define('RELOCATION_PET_MOVE', 17);
define('RELOCATION_INTERNATIONAL', 18);
define('RELOCATION_GLOBAL_MOBILITY', 19);
define('RELOCATION_OFFICE_MOVE', 20);

define('COURIER', 21);

define('ROAD', 160);
define('ORDERSCOUNT', 170);
define('ORDERSINVUDUAL', 180);
define('AIR', 'airtotal');
define('INTERNATIONAL_TYPE_AIR',1);
define('INTERNATIONAL_TYPE_OCEAN',2);


//Define public or private
define('PUBLIC', 0);
define('PRIVATE', 1);
define('IS_ACTIVE',1);
define('IS_ACCESS_PRIVATE', 2);
define('COMPETITIVE', 2);
define('FIRM', 2);
//Define lead types
define('FTL_SPOT', 1);
define('FTL_LEAD', 2);

//contracts & orders
define('ORDERS', 1);
define('CONTRACTS', 2);

//Load Type constant

define('LOADTYPE_ALL', 11);
define('VEHICLETYPE_ALL', 20);


//Define post types for PTL
define('PTL_ZONE', 1);
define('PTL_LOCATION', 2);

//Define post types for PTL
define('GENERALMESSAGETYPE', 1);
define('POSTMESSAGETYPE', 2);
define('ORDERMESSAGETYPE', 3);
define('POSTQUOTEMESSAGETYPE', 4);
define('POSTENQURYMESSAGETYPE', 5);
define('LEADSMESSAGETYPE', 6);
define('CONTRACTMESSAGETYPE', 7);

//community
define('INDIVIDUAL', 1);
define('ORGANIZATION', 2);
define('GROUP', 3);
define('NAME', 1);
define('COMPANY', 2);
define('LOCATION', 3);
define('INDUSTRY', 4);

//Courier delivery types
define('COURIER_DOMESTIC_DELIVERY', 1);
define('COURIER_INTER_DELIVERY', 2);
define('COURIER_TYPE_DOCS', 1);
define('COURIER_TYPE_PARCEL', 2);

//Define different directories

//Definition of all activities by various roles
define('SELLER_LISTED_POSTS', 'Seller listed Posts');
define('SELLER_LISTED_POST_ITEMS', 'Seller listed Post items');
define('PTL_SELLER_LISTED_POSTS', 'PTL Seller listed Posts');
define('RAIL_SELLER_LISTED_POSTS', 'Rail Seller listed Posts');
define('AIRDOM_SELLER_LISTED_POSTS', 'Air Domestic Seller listed Posts');
define('AIRINT_SELLER_LISTED_POSTS', 'Air International Seller listed Posts');
define('OCCEAN_SELLER_LISTED_POSTS', 'Occean Seller listed Posts');
define('COURIER_SELLER_LISTED_POSTS', 'Courier Seller listed Posts');
define('PTL_SELLER_LISTED_POST_ITEMS', 'PTL Seller listed Post items');
define('RAIL_SELLER_LISTED_POST_ITEMS', 'Rail Seller listed Post items');
define('AIRDOM_SELLER_LISTED_POST_ITEMS', 'Air Domestic Seller listed Post items');
define('AIRINT_SELLER_LISTED_POST_ITEMS', 'Air International Seller listed Post items');
define('OCCEAN_SELLER_LISTED_POST_ITEMS', 'Occean Seller listed Post items');
define('COURIER_SELLER_LISTED_POST_ITEMS', 'Courier Seller listed Post items');
define('FTL_SELLER_SEARCHED_BUYER_POSTS', 'FTL Seller search for buyer posts');
define('PTL_SELLER_SEARCHED_BUYER_POSTS', 'PTL Seller search for buyer posts');
define('RAIL_SELLER_SEARCHED_BUYER_POSTS', 'Rail Seller search for buyer posts');
define('AIR_DOMESTIC_SELLER_SEARCHED_BUYER_POSTS', 'Air Domestic Seller search for buyer posts');
define('COURIER_SELLER_SEARCHED_BUYER_POSTS', 'Courier Seller search for buyer posts');
define('AIR_INTERNATIONAL_SELLER_SEARCHED_BUYER_POSTS', 'Air International Seller search for buyer posts');
define('OCEAN_SELLER_SEARCHED_BUYER_POSTS', 'Ocean Seller search for buyer posts');
define('RELOCATION_SELLER_SEARCHED_BUYER_POSTS', 'Relocation Seller search for buyer posts');
define('RELOCATION_GM_SELLER_SEARCHED_BUYER_POSTS', 'Relocation Global Mobility Seller search for buyer posts');
define('TRUCKHAUL_SELLER_SEARCHED_BUYER_POSTS', 'Truck Haul Seller search for buyer posts');
define('TRUCKLEASE_SELLER_SEARCHED_BUYER_POSTS', 'Truck Lease Seller search for buyer posts');
define('TRUCKHAUL_SELLER_LISTED_POSTS', 'Truck Haul Seller listed Posts');
define('TRUCKHAUL_SELLER_LISTED_POST_ITEMS', 'Truck Haul Seller listed Post items');
define('TRUCKHAUL_SELLER_MARKET_LISTED_POST_ITEMS_VIEW', 'Truck Haul Seller Market leads view');
define('TRUCKLEASE_SELLER_MARKET_LISTED_POST_ITEMS_VIEW', 'Truck Lease Seller Market leads view');

define('FTL_SELLER_SEARCH_FORM_RESULTS', 'FTL Seller search form results');
define('PTL_SELLER_SEARCH_FORM_RESULTS', 'PTL Seller search form results');
define('RAIL_SELLER_SEARCH_FORM_RESULTS', 'Rail Seller search form results');
define('AIR_DOMESTIC_SELLER_SEARCH_FORM_RESULTS', 'Air Domestic Seller search form results');
define('COURIER_SELLER_SEARCH_FORM_RESULTS', 'Courier Seller search form results');
define('AIR_INTERNATIONAL_SELLER_SEARCH_FORM_RESULTS', 'Air International Seller search form results');
define('OCEAN_SELLER_SEARCH_FORM_RESULTS', 'Ocean Seller search form results');
define('RELOCATION_SELLER_SEARCH_FORM_RESULTS', 'Relocation Seller search form results');
define('RELOCATION_DOMESTIC_SELLER_LISTED_POSTS', 'Relocation Domestic Seller listed Posts');
define('RELOCATION_OFFICE_MOVE_SELLER_LISTED_POSTS', 'Relocation Domestic Seller listed Posts');
define('TRUCKHAUL_SELLER_SEARCH_FORM_RESULTS', 'Truck Haul Seller search form results');
define('TRUCKLEASE_SELLER_SEARCH_FORM_RESULTS', 'Truck Lease Seller search form results');

define('FTL_SELLER_DETAIL', 'Seller details');

define('SELLER_SEARCHED_POSTS', 'Seller searched Posts');
define('FTL_BUYER_LISTED_POSTS', 'Ftl Buyer listed Posts');
define('BUYER_SEARCHED_POSTS', 'Buyer searched Posts');
define('BUYER_ADDED_NEW_QUOTE', 'Buyer add new Quote');
define('BUYER_UPDATE_QUOTE', 'Buyer update quote');
define('BUYER_COUNT_NOOFLOADS', 'Buyer count no of loads from vehicle type');
define('BUYER_CAPACITY', 'Buyer get capacity from load type');
define('BUYER_SELLERLIST', 'Buyer get capacity from load type');
define('BUYER_SEARCH_FORM', 'Buyer search form for sellers');
define('FTL_BUYER_SEARCH_FORM_RESULTS', 'Ftl Buyer search form for sellers results');

define('TRUCKHAUL_BUYER_LISTED_POSTS', 'Truck Haul Buyer listed Posts');
define('RELOCATION_OFFICE_MOVE_BUYER_LISTED_POSTS', 'Relocation Office Move Buyer listed Posts');


define('SELLER_CREATED_POSTS', 'Seller created Posts');
define('SELLER_UPDATED_POSTS', 'Seller updated Posts');
define('SELLER_SUBMIT_QUOTE', 'Seller submit a quote');

define('BUYER_FETCHED_SELLER_POST', 'Buyer fetched seller post');
define('BUYER_INSERTED_COUNTER_OFFER', 'Buyer inserted counter offer');
define('BUYER_CALCULATED_FREIGHT_AMOUNT', 'Buyer calculated freight amount');

define('BUYER_INSERTED_ADDTOCART', 'Buyer inserted data for add to cart');

define('BUYER_CANCELED_ENQUIRY', 'Buyer canceled enquiry');
//equipment, vehicle, warehouse activities
define('SELLER_ADDED_NEW_EQUIPMENT', 'Seller added Equipment');
define('SELLER_EDITED_EQUIPMENT', 'Seller edited Equipment');
define('SELLER_DELETED_EQUIPMENT', 'Seller deleted Equipment');
define('SELLER_ADDED_NEW_WAREHOUSE', 'Seller added Warehouse');
define('SELLER_EDITED_WAREHOUSE', 'Seller edited Warehouse');
define('SELLER_DELETED_WAREHOUSE', 'Seller deleted Warehouse');
define('SELLER_ADDED_NEW_VEHICLE', 'Seller added Vehicle');
define('SELLER_EDITED_VEHICLE', 'Seller edited Vehicle');
define('SELLER_DELETED_VEHICLE', 'Seller deleted Vehicle');

define('PTL_BUYER_LISTED_POSTS', 'Buyer listed Posts');
define('INTRA_BUYER_LISTED_POSTS', 'Intracity Buyer listed Posts');
define('THAUL_BUYER_LISTED_POSTS', 'Truck Haul Buyer listed Posts');
define('RAIL_BUYER_LISTED_POSTS', 'RAIL Buyer listed Posts');
define('AIR_DOMESTIC_BUYER_LISTED_POSTS', 'AIRDOMESTIC Buyer listed Posts');
define('RELOCATION_INTERNATIONAL_BUYER_LISTED_POSTS', 'Relocation International Buyer listed Posts');
define('RELOCATION_GM_BUYER_LISTED_POSTS', 'Relocation Global Mobility Buyer listed Posts');

define('RAIL_BUYER_POST_DETAILS', 'Buyer post details page has been viewed');
define('AIRDOMESTIC_BUYER_POST_DETAILS', 'Buyer post details page has been viewed');

define('FTL_BUYER_SEARCHED_SELLER_POSTS', 'FTL buyer searched seller posts');
define('PTL_BUYER_SEARCHED_SELLER_POSTS', 'buyer searched seller posts');
define('RELOCATIONPET_BUYER_SEARCHED_SELLER_POSTS', 'Recolaction pet buyer search form for sellers');

define('INTRA_BUYER_SEARCHED_SELLER_POSTS', 'buyer searched seller posts');
define('THAUL_BUYER_SEARCHED_SELLER_POSTS', 'buyer searched seller posts');
define('RELOCATION_DOMESTIC_BUYER_SEARCHED_SELLER_POSTS', 'buyer relocation domestic searched seller posts');
define('TRUCKHAUL_BUYER_SEARCHED_SELLER_POSTS', 'Truck Haul buyer searched seller posts');
define('TRUCKLEASE_BUYER_SEARCHED_SELLER_POSTS', 'Truck Lease buyer searched seller posts');
define('RELOCATION_INTERNATIONAL_BUYER_SEARCHED_SELLER_POSTS', 'buyer relocation international searched seller posts');


define('BUYER_SEARCH_FORM_RESULTS', 'Buyer search form for sellers results');
define('PTL_BUYER_SEARCH_FORM_RESULTS', 'Ptl Buyer search form for sellers results');
define('RAIL_BUYER_SEARCH_FORM_RESULTS', 'Rail Buyer search form for sellers results');
define('AIRDOMESTIC_BUYER_SEARCH_FORM_RESULTS', 'AirDomestic Buyer search form for sellers results');
define('INTRA_BUYER_SEARCH_FORM_RESULTS', 'Intracity Buyer search form for sellers results');
define('TRUCKHAUL_BUYER_SEARCH_FORM_RESULTS', 'Truck Haul Buyer search form for sellers results');
define('TRUCKLEASE_BUYER_SEARCH_FORM_RESULTS', 'Truck Lease Buyer search form for sellers results');
define('RELOCATION_DOMESTIC_BUYER_SEARCH_FORM_RESULTS', 'Relocation domestic Buyer search form for sellers results');
define('RELOCATION_PET_BUYER_SEARCH_FORM_RESULTS', 'Relocation pet Buyer search form for sellers results');
define('RELOCATION_PET_SELLER_SEARCH_FORM_RESULTS', 'Relocation pet Seller search form for buyer results');

define('INTRA_BUYER_FETCHED_SELLER_POST', 'Intracity Buyer fetched seller post');

define('BUYER_VEHICLES', 'Buyer get Vehicles from weight');

//Definitions of notification messages

//Definitions of various statuses for posts
define('APPROVE_INVITEE','1');
define('REJECT_INVITEE','2');

//Definition of current page url 

define('CURRENT_URL','http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
if(isset($_SERVER['HTTP_REFERER'])){
	define('HTTP_REFERRER', $_SERVER['HTTP_REFERER']);
}else{
	define('HTTP_REFERRER', 'No Page');
}

//Define Date formats
define('DATE_FORMAT','Y-m-d H:i:s');

//Definitions of various mails constants
define('BUYER_ACCOUNT_ACTIVATION_MAIL', '1');
define('SELLER_PAYMENT_INFO_MAIL','2');
define('CANCEL_ENQUIRY_INFO_MAIL','3');
define('INVOICE_CONFIRMATION_MAIL','4');
define('CANCEL_ORDER_INFO_MAIL','5');

define('FTL_SEARCH_KEYWORDS','11');
define('PTL_SEARCH_KEYWORDS','33');
define('TRUCKLEASE_SEARCH_KEYWORDS','34');
define('AIRINTERNATIONAL_SEARCH_KEYWORDS',35);
define('OCEAN_SEARCH_KEYWORDS',36);

define('INITIAL_COUNT_BY_SELLER', '7');
define('FINAL_COUNT_BY_SELLER', '8');
define('ACCEPTED_COUNTER_QUOTE', '9');
define('FIRM_PRICE_ACCEPTED_BY_SELLER', '10');
define('FTL_ORDER_INVOICE','13');
define('FTL_SELLER_ORDER_INVOICE','14');
define('FTL_SELLER_ORDER_RECEIPT','15');
define('COUNTER_OFFER_BY_BUYER', '16');
define('CHECKOUT_EMAIL_FOR_SELLER', '17');
define('INTRACITY_BUYER_ORDER_CONFIRMATION_MAIL', '18');
define('FTL_BUYER_GENERATE_CONTRACT',19);
define('PARTNER_REQUEST',23);
define('MEMBER_REQUEST',24);
define('NEW_GROUP_MEMBER',25);
define('SHARE_REQUEST',26);
define('PROFILE_MESSAGE',27);
define('MEMBER_INVITED',28);
define('DEACTIVATED_GROUP',29);
define('NEW_POST_COMMENT',30);
define('MEMBER_EXIT_GROUP',31);
define('RECOMENDATION',32);

/****************************************************************************************************/
/**
 * Registration process Constants
 * 
 */  
define('USER_CREATE', 'User create function triggers');
define('USER_REGISTER', 'User submits the registration form');
define('USER_REGISTERATION_DISPLAY', 'User viewed the registration page');
define('USER_UNIQUE', 'User Email/Phone is being checked for their uniqueness');
define('CHASIS_UNIQUE', 'User Vehicle Chasis Number is being checked for uniqueness');
define('ENGINE_UNIQUE', 'User Vehicle Engine Number is being checked for uniqueness');
define('VALIDATE_OTP', 'Validating the User given Otp');
define('DISPALY_BUYER', 'User lands on buyer individual page');
define('CREATE_BUYER', 'Buyer create function triggers');
define('BUYER_REGISTRATION', 'User submits the buyer details form');
define('DISPLAY_BUYER', 'User lands on individual buyer edit form');
define('EDIT_BUYER', 'User submits the edit-buyer form');
define('DISPLAY_BUYER_BUSINESS', 'User viewed buyer_business page');
define('BUYER_BUSINESS_REGISTRATION', 'User submits the buyer business form');
define('CHECK_UPLOADS', 'User is uploading files');
define('CREATE_BUSINESS_BUYER', 'Business_buyer create function triggers');
define('DISPLAY_SELECT_ROLE', 'User viewed the select role page');
define('USER_SELECT_ROLE', 'User selected a role');
define('GET_STATE', 'Selecting statelist from DB for business registration form');
define('GET_INTRACITY_LOCALITY', 'Selecting localityList from DB for business-seller registration form');
define('GET_PM_CITY', 'Selecting CityList from DB for business-seller registration form');
define('DISPALY_SELLER_BUSINESS', 'Seller viewed seller_business registration page');
define('SELLER_BUSINESS_REGISTRATION', 'Seller_Submits the seller_business registration form');
define('CREATE_SELLER_BUSINESS', 'Seller_business create function triggers');
define('DIAPLAY_EDIT_BUYER_BUSINESS', 'Buyer has viewed buyer_business edit profile page');
define('EDIT_BUYER_BUSINESS', 'Buyer submits the edit buyer_business profile form');
define('CREATE_EDIT_BUYER_BUSINESS', 'Buyer_business_edit create function triggers');
define('CREATE_EDIT_SELLER_BUSINESS', 'Seller has viewed edit_seller_business edit profile page');
define('EDIT_SELLER_BUSINESS', 'Seller has posted the edit_seller_business form');
define('DISPLAY_EDIT_SELLER_BUSINESS', 'User create function triggers');
define('DISPALY_SELLER_INDIVIDUAL', 'Seller viewed seller_individual registration page');
define('DISPLAY_EDIT_SELLER_INDIVIDUAL', 'Seller viewed seller_individual edit_profile page');
define('EDIT_SELLER_INDIVIDUAL', 'Seller has posted the edit_seller_individual form');
define('CREATE_EDIT_SELLER_INDIVIDUAL', 'create_seller_individual_edit  function triggers');
define('CREATE_SELLER_INDIVIDUAL', 'create_seller_individual  function triggers');
define('SELLER_CONFIRM_PAYMENT', 'Seller has confirmed the payment');
define('GET_INTRACITY_FROM_LOCALITY', 'Selecting localityList from DB for creating seller intracity post');


/*************************************************************************************************/

/*
|--------------------------------------------------------------------------
| PTL Buyer Constants Starts here
|--------------------------------------------------------------------------
|
*/

define('PTL_BUYER_ADDED_NEW_QUOTE', 'Buyer add new Quote in PTL Quote creation');

//End PTL constants

define('LTL_BUYER_EDIT_FOR_BUYER_POSTS', 'seller edit for buyers');
define('RAIL_BUYER_EDIT_FOR_BUYER_POSTS', 'seller edit for buyers');
define('AIRDOMESTIC_BUYER_EDIT_FOR_BUYER_POSTS', 'seller edit for buyers');

//Define Business or Individual
define('IS_INDIVIDUAL', 0);
define('IS_BUSINESS', 1);

//Definitions of email component constants
define('FROM_EMAIL', 'info@logistiks.com');
define('APPL_TITLE', 'LOGISTIKS');

//checkout
define('CART_ITEM_DELETED', 'Item was deleted from shopping cart.');
define('CART_CLEARED', 'Shopping cart cleared.');
define('NO_ITEMS_TO_CHECKOUT', 'You have no items to checkout.');

//order page constants
define('SELLER_CONSIGNMENT_PICKUP', 'Seller consignment page');
define('ADD_VEHICLE', 'Adding vehicle from seller consignment page');
define('ADD_LOCATION', 'Adding Location from seller consignment page');
define('ADD_INVOICE', 'Create Invoice from seller consignment page');
define('ADD_SELLER_INVOICE', 'Create Seller Invoice from seller consignment page');
define('ADD_RECEIPT', 'Create Payment Receipt from seller consignment page');
define('BUYER_CANCELED_ORDER', 'Buyer Cancelled Order from Order detail page');
define('BUYER_CANCELED_TERM_ORDER', 'Buyer Cancelled buyer Term quote order');
//define('CANCEL_ORDER_INFO_MAIL', 'Buyer Cancelled Order');

//Define Payment Modes
define('ADVANCED', 1);
define('CASH_ON_DELIVERY', 2);
define('CASH_ON_PICKUP', 3);
define('CREDIT', 4);
//payment methods
define('CASH_ON_DELIVERY_METHOD', 4);

//Define Post Statuses
define('SAVEDASDRAFT', 1);
define('OPEN', 2);
define('CLOSED', 3);
define('BOOKED', 4);
define('CANCELLED', 5);
define('INCART', 6);
define('ORDERED', 7);
define('ABANDONED', 8);
define('SELLER_CREATED_POST_FOR_BUYERS', 6);
define('BUYER_CREATED_POST_FOR_SELLERS', 12);
define('BUYER_UPDATED_BIDCLOSE_DATE', 21);
define('SELLER_QUOTE_SUBMITTED_TERM', 22);

//Define Payment Methods
define('NETBANKING', 1);
define('DEBITCARD', 2);
define('CREDITCARD', 3);
define('CASHONDELIVERY', 4);

//Define Order Types
define('SPOTORDER', 1);
define('TERMSORDER', 2);


/**
 * Orders Constant for orders page grid
 * Only for FTL"Will get changed as new service will come"
 */
define('SELLER_VIEWED_ORDERS', 'Seller viewed Orders listing page');
define('PTL_SELLER_VIEWED_ORDERS', 'Seller viewed Orders listing page');
define('RAIL_SELLER_VIEWED_ORDERS', 'Seller viewed Orders listing page');
define('AIRDOMESTIC_SELLER_VIEWED_ORDERS', 'Seller viewed Orders listing page');
define('INTRA_SELLER_VIEWED_ORDERS', 'Seller viewed Orders listing page');
define('THAUL_SELLER_VIEWED_ORDERS', 'Seller viewed Orders listing page');


/**
 * Orders Constant for Buyer orders page grid
 * 
 */
define('FTL_BUYER_VIEWED_ORDERS', 'Buyer viewed Orders listing page');
define('BUYER_VIEWED_ORDERS', 'Buyer viewed Orders listing page');
define('PTL_BUYER_VIEWED_ORDERS', 'Buyer viewed Orders listing page');
define('RAIL_BUYER_VIEWED_ORDERS', 'Buyer viewed Orders listing page');
define('AIRDOMESTIC_BUYER_VIEWED_ORDERS', 'Buyer viewed Orders listing page');
define('INTRA_BUYER_VIEWED_ORDERS', 'Buyer viewed Orders listing page');
define('THAUL_BUYER_VIEWED_ORDERS', 'Buyer viewed Orders listing page');
/**
 * Orders Constant for Buyer orders detail page
 * 
 */
define('FTL_BUYER_ORDER_DETAIL', 'Buyer Orders Detail page');
define('PTL_BUYER_ORDER_DETAIL', 'Buyer Orders Detail page');
define('RAIL_BUYER_ORDER_DETAIL', 'Buyer Orders Detail page');
define('AIRDOMESTIC_BUYER_ORDER_DETAIL', 'Buyer Orders Detail page');
define('INTRA_BUYER_ORDER_DETAIL', 'Buyer Orders Detail page');
define('THAUL_BUYER_ORDER_DETAIL', 'Buyer Orders Detail page');
define('COURIER_BUYER_ORDER_DETAIL', 'Buyer Orders Detail page');
define('RELOCATION_BUYER_ORDER_DETAIL', 'Relcoation Buyer Orders Detail page');
define('RELOCATION_OFFICE_MOVE_BUYER_ORDER_DETAIL', 'Relcoation Office Move Buyer Orders Detail page');
define('RELOCATION_PET_MOVE_BUYER_ORDER_DETAIL', 'Relcoation Pet Move Buyer Orders Detail page');
define('RELOCATION_INT_BUYER_ORDER_DETAIL', 'Relcoation international air and ocean Buyer Orders Detail page');
define('RELOCATION_GLOBAL_MOBILITY_BUYER_ORDER_DETAIL', 'Relcoation global Buyer Orders Detail page');

/**
 * INTRACITY CONSTANTS
 * 
 */
define('INTRACITY_BUYER_POST', 'Buyer post an intracity requirement');
define('INTRACITY_BUYER_POST_CREATION', 'Buyer intracity post creation is under process');
define('INTRA_BUYER_POST_DETAILS', 'Buyer post details page has been viewed');
define('INTRA_BUYER_POST_LIST', 'Buyer post list page has been viewed');

define('RELOCATION_BUYER_POST_DETAILS', 'Buyer post details page has been viewed');
define('RELOCATION_INTERNATIONAL_BUYER_POST_DETAILS', 'Relocation International Buyer post details page has been viewed');
//wallet
define('INSUFFICIENT_WALLET', 'Wallet balance is insufficient to proceed.');


define('RELOCATION_GM_BUYER_POST_DETAILS', 'Relocation Global Mobility Buyer post details page has been viewed');



//order statuses
define('ORDER_BOOKED', 1);
define('ORDER_PICKUP_DUE', 2);
define('ORDER_CONSIGNMENT_PICKUP', 3);
define('ORDER_INTRANSIT', 4);
define('ORDER_REACHED_DESTINATION', 5);
define('ORDER_DELIVERED', 6);
define('ORDER_CLOSED', 7);
define('ORDER_CANCELLED', 8);
define('ORDER_INVOICED', 9);
define('ORDER_PENDING', 13);


define('PENDING_ACCEPTANCE',10);
define('CONTRACT_ACCEPTED',11);
define('CONTRACT_CANCELLED',12);


//Definition of breadcrumbs constants
define('FTLCREATEPOST', 'createseller');
define('LTLCREATEPOST', 'createsellerpost');
define('FTLEDITPOST', 'updateseller');
define('LTLEDITPOST', 'updatesellerpost');
define('FTLCREATEQUOTE', 'createbuyerquote');
define('LTLCREATEQUOTE', 'createbuyerquote');
define('INTRACREATEQUOTE', 'buyer_post');
define('BUYERPOSTS', 'buyerposts');
define('BUYERPOSTDETAIL', 'getbuyercounteroffer');
define('BUYERSEARCH', 'buyersearch');
define('BUYERSEARCHRESULTS', 'byersearchresults');
define('SELLERSEARCH', 'sellersearchbuyers');
define('SELLERSEARCHRESULTS', 'buyersearchresults');
define('SELLERPOSTLIST', 'sellerlist');
define('SELLERPOSTS', 'sellerposts');
define('SELLERPOSTDETAILS', 'sellerpostdetail');
define('SELLERORDERS', 'seller_orders');
define('SELLERORDERDETAILS', 'details');
define('BUYERORDERS', 'buyer_orders');
define('BUYERORDERDETAILS', 'buyer_orderdetails');
define('CART', 'cart');
define('CHECKOUT', 'checkout');
define('MESSAGESINDEX', 'index');
define('SELLERCONSIGNMENT', 'consignment_pickup');
define('SELLERORDERSEARCH', 'sellerorderSearch');
define('BUYERORDERSEARCH', 'buyerordersearch');
define('RELOCATIONCREATEPOST', 'creatbuyerrpost');
define('MESSAGES', 'messages');
define('BUYERTERMPOSTDETAIL', 'gettermbuyercounteroffer');
define('BUYERDETAILS', 'buyerdetails');
define('GETMESSAGEDETAILS', 'getmessagedetails');
define('SENTMESSAGES', 'sentmessages');
define('EDITSELLER', 'edit_seller');
define('VEHICLELIST', 'vehiclelist');
define('WAREHOUSELIST', 'warehouselist');
define('EQPLIST', 'list');
define('SELLERTERMSEARCHRESULTS', 'termsellersearchresults');
define('PTLZONE', 'zone');
define('PTLTIER', 'tier');
define('PTLTMATRIX', 'transit_matrix');
define('PTLSECTOR', 'sector');
define('PTLPINCODE', 'pincode');
define('PTLEQPREGISTER', 'equipmentregister');
define('PTLEQPEDIT', 'equip_edit');
define('PTLWAREHOUSEREGISTER', 'warehouseregister');
define('PTLWAREHOUSEEDIT', 'warehouse_edit');
define('PTLVEHICLEREGISTER', 'vehicleregister');
define('PTLVEHICLEEDIT', 'vehicle_edit');
define('PTLSELLERBUSINESS', 'seller_business');
define('BUYEREDIT', 'buyer');
define('BUYERBUSINESS', 'buyer_business');

define('FTLEDITQUOTE', 'createbuyerquote');
define('LTLEDITQUOTE', 'createbuyerquote');
define('INTRAEDITQUOTE', 'buyer_post');
define('RELOCATIONEDITQUOTE', 'buyer_post');
define('CHANGEPASSWORD', 'changepassword');
define('FTLEDITPOSTBUYER', 'editbuyerquote');
define('FTLEDITPOSTBUYERTERM', 'termdraftedit');
define('EDITPOSTBUYERSPOT', 'editseller');

//Invoice service groups
define('TRANSPORT', 1);
define('OTHERS', 2);
define('PERCENT40', 40);
define('PERCENT14', 14.5);
define('PERCENT4', 4);

define('ROADTFTL', 'Road FTL');
define('ROADTLTL', 'Road LTL');
define('ROADTINTRA', 'Road Intracity');


//Define Length Units
define('FEET', 1);
define('INCHES', 2);
define('METER', 3);
define('CENTIMETER', 4);

//contract or not
define('IS_CONTRACT',1);
define('NOT_CONTRACT',0);

//Define Domestic or International
define('IS_DOMESTIC', 1);
define('IS_INTERNATIONAL', 2);

//Define Document or parcel
define('IS_DOCUMENT', 1);
define('IS_PARCEL', 2);


//SMS Gateway Configurations 
define('SMS_GATEWAY_ENABLED',1);

//Payment Gateway Configarations
define('PAYMENT_GATEWAYS',serialize(array(
			'HDFC'=>array(
					'title'=>'HDFC',
					'value'=>'HDFC',
					'status'=>1
				),
			/*'SBI'=>array(
					'title'=>'SBI',
					'value'=>'SBI',
					'status'=>1
				),
			'ICICI'=>array(
					'title'=>'ICICI',
					'value'=>'ICICI',
					'status'=>1
				),*/
		))
	);
define('CURRENCY_COUNTRY','IND');
define('CURRENCY_TYPE','INR');
	
	//HDFC Detials
	define('HDFC_HASHING_METHOD','sha512');
	define('HDFC_PAYMENT_GATEWAY_URL','https://secure.ebs.in/pg/ma/payment/request/');
	define('HDFC_PAYMENT_GATEWAY_ACCOUNT_ID',20150);
	define('HDFC_PAYMENT_GATEWAY_ACCOUNT_SECRET_KEY','209b9b0bffc0ddafbbd0047a892b9dd3');
	define('HDFC_PAYMENT_GATEWAY_RETURN_URL_ACTION','/hdfcconfirm');
	define('HDFC_PAYMENT_GATEWAY_MODE','LIVE');

//Ajax load Configurations
define('AJAX_LOAD_LIMIT', 5);

/*return [
        'INVESTIGATOR_SUBMISSION_STATUS' => [
                        "0" => "Select Status",
                        "2" => "Draft",
                        "3" => "Awaiting PI Sign off",
                        "6" => "Submitted(for IEC Review)",
                        "4" => "Query",
                        "13" => "Approved",
                        "14" => "Noted",
                        "5" => "Returned",
                        "15" => "Rejected / Resubmission required",	
						"20" => "Suspended",
        ],
];*/



//Define Post Statuses
/*define('SAVEDASDRAFT', 1);
define('OPEN', 2);
define('CLOSED', 3);
define('BOOKED', 4);
define('CANCELLED', 5);*/

// VOLTY GPS API Constants
//define('VOLTY_GPS_API_URL','http://transync.in:8044/logistics');
define('VOLTY_GPS_API_URL','http://volty.logistiks.com:8044/logistics');
define('VOLTY_GPS_OPTION','VEH');
define('VOLTY_GPS_SUB_USER','logisticaadmin');
define('VOLTY_GPS_SUPER_USER','logisticaadmin');
define('VOLTY_GPS_SUPER_PASS','logissafe@365');

//Definition of Sms  constants
define('SELLER_CREATED_POST_FOR_BUYERS_SMS', 2);
define('BUYER_CREATED_POST_FOR_SELLERS_SMS', 3);
define('SELLER_SUBMITT_QOUTE_SMS', 4);
define('BUYER_BOOKS_CONSIGNMENT_SPOT_TERM', 5);
define('SELLER_SUBMITT_QOUTE_TERM_SMS', 6);
define('TRUCK_PLACEMENT', 7);
define('CONSIGNMENT_PICK_UP', 8);
define('CONSIGNMENT_DELIVERED', 9);
define('BUYER_COUNTER_OFFER_SMS', 10);
define('CONTRACT_ISSUANCE', 12);
define('CONTRACT_ACCEPTANCE_REJECTION', 13);
define('BUYER_CREATED_POST_FOR_SELLERS_TERM_SMS', 14);
define('SELLER_UPDATED_POST_FOR_BUYERS_SMS', 15);
define('INTRACITY_BOOKED_POST_SMS', 16);
define('INTRACITY_BOOKED_POST_ACKOWLEDGEMENT_SMS', 17);
define('REGISTRATION_OTP_SMS', 18);


/*** TRUCK HAUL Constants ***/
define('TRUCKHAUL_BUYER_ADDED_NEW_QUOTE', 'Buyer add new Truckhaul Quote');
define('TRUCKHAUL_BUYER_CREATED_POST_FOR_SELLERS', 12);
define('TRUCKHAUL_BUYER_CREATED_POST_FOR_SELLERS_SMS', 3);
define('TRUCKHAUL_SELLER_CREATED_POSTS', 'Seller created Truck Haul Posts');
define('TRUCKHAUL_SELLER_UPDATED_POSTS', 'Seller updated Truck Haul Posts');
define('TRUCKHAUL_SELLER_SUBMIT_QUOTE', 'Seller submit a Truck Haul quote');
define('TRUCKHAUL_SELLER_CREATED_POST_FOR_BUYERS', 6);


define('FTL_SELLER_LISTED_POSTS', 'Ftl Seller listed Posts');

define('TRUCKLEASE_BUYER_ADDED_NEW_QUOTE', 'Buyer add new Trucklease Quote');
define('TRUCKLEASE_BUYER_CREATED_POST_FOR_SELLERS', 12);
define('TRUCKLEASE_BUYER_CREATED_POST_FOR_SELLERS_SMS', 3);
define('TRUCKLEASE_SELLER_CREATED_POSTS', 'Seller created Truck Lease Posts');
define('TRUCKLEASE_SELLER_UPDATED_POSTS', 'Seller updated Truck Lease Posts');
define('TRUCKLEASE_SELLER_SUBMIT_QUOTE', 'Seller submit a Truck Lease quote');
define('TRUCKLEASED_SELLER_CREATED_POST_FOR_BUYERS', 6);

//@Shriram
define('RELOCATIONPETMOVE_BUYER_SMS_SERVICENAME','RELOCATION PET MOVE');
/**
 * SendSMS Service Name Constant for Relocation Office Move
 * Start
 * @ Kalyani / 10052016
*/
define('RELOCATIONOFFICE_BUYER_SMS_SERVICENAME','RELOCATION OFFICE MOVE');
define('RELOCATIONOFFICE_SELLER_SMS_SERVICENAME','RELOCATION OFFICE MOVE');

define('RELOCATIONINT_BUYER_SMS_SERVICENAME','RELOCATION INTERNATIONAL');
/**
 * End 
 * @ Kalyani / 10052016
 */

/**
 * Year of established constant
 * Start
 * @ Jagadeesh / 29042016
 */
	define('YEAROFESTABLISHEDSTART',1900);
	define('YEAROFESTABLISHEDEND', date('Y'));
/**
 * End 
 * @ Jagadeesh / 29042016
 */

/**
 * Buyer / Seller Upload Path
 * Start
 * @ Jagadeesh / 02052016
 */
	define('BUYERUPLOADPATH','uploads/buyer/');
	define('SELLERUPLOADPATH','uploads/seller/');
/**
 * End 
 * @ Jagadeesh / 02052016
 */


/**
 * Seller Buyer search form constant
 * Start
 * @ Jagadeesh / 12052016
 */
	define('RELOCATION_OFFICE_MOVE_BUYER_SEARCH_FORM_RESULTS', 'Relocation office move Buyer search form for sellers results');
	define('RELOCATION_OFFICE_MOVE_SELLER_SEARCH_FORM_RESULTS', 'Relocation office move Seller search form for Buyers results');
/**
 * End 
 * @ Jagadeesh / 12052016
 */

define('RELOCATION_OFFICEMOVE_BUYER_POST_DETAILS', 'Relocation Office Move Buyer post details page has been viewed');
//Relcoation office move delivery days
define('RELOCAITON_OFFICE_MOVE_DELIVERDAYS',1);


define('RELOCATION_INTERNATIONAL_BUYER_SEARCH_FORM_RESULTS', 'Relocation International Buyer search form for sellers results');
define('RELOCATION_INTERNATIONAL_SELLER_SEARCH_FORM_RESULTS', 'Relocation International Seller search form for Buyers results');

define('RELOCATION_GM_BUYER_SEARCH_FORM_RESULTS', 'Relocation Global Mobility Buyer search form for sellers results');
define('RELOCATION_GM_SELLER_SEARCH_FORM_RESULTS', 'Relocation Global Mobility Seller search form for Buyers results');


//Relocation International Air/Ocean Seller Posts Constants
define('RELOCATION_INTERNATIONAL_SELLER_LISTED_POSTS', 'Relocation International Seller listed Posts');


define('SHOW_SERVICE_TAX', 0);

define('RELOCATION_GM_BUYER_SERVICE_MEASUREMENT','Relocation Global Mobility Buyer Selected Service Type');
//Tracking types
define('TRACKING_MILE_STONE', "Milestone");
define('TRACKING_REAL_TIME', "Real time");



/*
 * Constents for All imges titles
 * @srinivas dantha , date : 05th july,2016.
 * 
 */

define('FTL_IMAGE_TITLE', "FTL");
define('LTL_IMAGE_TITLE', "LTL");
define('RAIL_IMAGE_TITLE', "RAIL");
define('AIRDOM_IMAGE_TITLE', "AIR DOMESTIC");
define('AIRINT_IMAGE_TITLE', "AIR INTERNATIONAL");
define('OCEAN_IMAGE_TITLE', "OCEAN");
define('INTRACITY_HYPERLOCAL_IMAGE_TITLE', "INTRACITY HYPER LOCAL");
define('INTRACITY_IMAGE_TITLE', "INTRACITY");
define('COURIER_IMAGE_TITLE', "COURIER");
define('TRUCK_HAUL_IMAGE_TITLE', "TRUCK HAUL");
define('TRUCK_LEASE_IMAGE_TITLE', "TRUCK LEASE");
define('RELOCATION_DOMESTIC_IMAGE_TITLE', "RELOCATION DOMESTIC");
define('RELOCATION_PETMOVE_IMAGE_TITLE', "RELOCATION PETMOVE");
define('RELOCATION_INTERNATIONAL_IMAGE_TITLE', "RELOCATION INTERNATIONAL");
define('RELOCATION_GLOBAL_MOBILITY_IMAGE_TITLE', "RELOCATION GLOBAL MOBILITY");
define('RELOCATION_OFFICE_DOMESTIC_IMAGE_TITLE', "RELOCATION OFFICE DOMESTIC");
define('DEFAULT_NOOFLOADS', 1);

/**
 * End Srinivas dantha all images title
 */

/**
 * Live Tracking End Time
 * Start
 * @ Jagadeesh / 29062016
 */
	define('LIVE_CURRENT_END_DATE', '23:59');
/**
 * End 
 * @ Jagadeesh / 29062016
 */
/**
 * FRAPI Config URL
 * Start
 * @ Jagadeesh / 05072016
 */
	define('FRAPI_REDIRECT_URL', 'http://api.logistiks.com');
/**
 * End 
 * @ Jagadeesh / 05072016
 */

return [
        'BUYER_POST_COUNTER_OFFER_COMPARISON_TYPES' => [
                        "0" => "Select compare type",
                        "1" => "Lowest Transit Time",
                        "2" => "Lowest Price",
        ],
];


