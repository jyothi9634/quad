<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Warehouse;
use App\Components\CommonComponent;
use DB;
use Input;
use Auth;
use Session;
use Config;
use Zofe\Rapyd\Facades\DataGrid;
use App\Models\TxnUserDocument;
use Illuminate\Http\Request;
use Redirect;
use Illuminate\Support\Facades\Mail;
use Log;

class WarehouseController extends Controller {
    /**
     * Create a new WarehouseController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth', [
            'except' => 'getLogout'
        ]);
    }
    /**
     * Display a listing of the warehouse.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        Log::info('Warehouse Listing is initiated by user: ' . Auth::id(), array('c' => '1'));
        //$warehouse = Warehouse::all()->where('seller_id',Auth::id())->where('is_active','1');
        $warehouse = DB::table ( 'warehouses as w' )->leftjoin('lkp_warehouse_types as wt','wt.id','=','w.lkp_warehouse_type_id')
                ->where('w.seller_id', Auth::id())->where('w.is_active','1')->select('w.*','wt.warehouse_name')->get();
        return view('users.warehouse_list', compact('warehouse'));
    }

    /**
     * Show the form for creating a new warehouse.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        try {
            Log::info('Warehouse Create is initiated by user: ' . Auth::id(), array('c' => '1'));
            CommonComponent::activityLog("SELLER_ADDED_NEW_WAREHOUSE", SELLER_ADDED_NEW_WAREHOUSE, 0, HTTP_REFERRER, CURRENT_URL);
            //print_r(Input::all()); 
            if (!empty(Input::all())) {
//print_r($_POST); exit;
                $from = str_replace('/', '-', $request->from_dt);
                $to = str_replace('/', '-', $request->to_dt);

                $created_at = date('Y-m-d H:i:s');
                $createdIp = $_SERVER ['REMOTE_ADDR'];
                $warehouse_all = new Warehouse();
                $warehouse_all->created_at = $created_at;
                //in place of 1 ->Auth::User()->user_id
                $warehouse_all->created_by = Auth::id();
                $warehouse_all->created_ip = $createdIp;
                $warehouse_all->seller_id = Auth::id();
                $warehouse_all->lkp_warehouse_type_id = $request->wh_type;
                $warehouse_all->lkp_location_id = $request->city_id;
                $warehouse_all->lkp_district_id = $request->district_id;
                $warehouse_all->lkp_state_id = $request->state_id;
                $warehouse_all->pincode = $request->pincode;
                $warehouse_all->from_date = date("Y-m-d", strtotime($from));
                $warehouse_all->to_date = date("Y-m-d", strtotime($to));
                $warehouse_all->cargo_type = $request->cargo_type;
                $warehouse_all->space_min_feet = $request->space_min_ft;
                $warehouse_all->space_max_feet = $request->space_max_ft;
                $warehouse_all->capacity = $request->capacity;
                $warehouse_all->owner_firstname = $request->wh_owner_fist_name;
                $warehouse_all->owner_middlename = $request->wh_owner_middle_name;
                $warehouse_all->owner_lastname = $request->wh_owner_last_name;
                $warehouse_all->contact_firstname = $request->cp_first_name;
                $warehouse_all->contact_middlename = $request->cp_middle_name;
                $warehouse_all->contact_lastname = $request->cp_last_name;
                $warehouse_all->ownership_type = $request->ownership_type;
                $warehouse_all->mobile_number = $request->mobile_number;
                $warehouse_all->email = $request->email;
                $warehouse_all->address = $request->wh_address;
                $warehouse_all->short_name = $request->wh_short_name;
                $warehouse_all->transport_reg_id = $request->transport_reg_id;

                if (!empty($_POST['Warehouse']['infrastructure'])) {
                    $warehouse_all->infrastructure_available = implode(",", $_POST['Warehouse']['infrastructure']);
                }
                if (!empty($_POST['Warehouse']['amenities'])) {
                    $warehouse_all->amenities = implode(",", $_POST['Warehouse']['amenities']);
                }
                if (!empty($_POST['Warehouse']['additional_services'])) {
                    $warehouse_all->additional_services = implode(",", $_POST['Warehouse']['additional_services']);
                }
                if ($warehouse_all->save()) {
                    CommonComponent::auditLog($warehouse_all->id,'warehouses');
                    return redirect('warehouselist')
                                    ->with('message', 'Warehouse added successfully.');
                }
            }

            $warehouse = \DB::table('lkp_warehouse_types')->orderBy('warehouse_name', 'asc')->lists('warehouse_name', 'id');
            $state = \DB::table('lkp_states')->orderBy('state_name', 'asc')->lists('state_name', 'id');
            $district = \DB::table('lkp_districts')->orderBy('district_name', 'asc')->lists('district_name', 'id');
            $city = \DB::table('lkp_cities')->orderBy('city_name', 'asc')->lists('city_name', 'id');
            $load_type = CommonComponent::getAllLoadTypes();
            return view('users.warehouse', array('warehouse' => $warehouse, 'state' => $state, 'city' => $city, 'district' => $district,'load_type' => $load_type));
        } catch (Exception $ex) {
            
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id) {
        //
    }

    /**
     * Show the form for editing the specified warehouse.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        try {
        Log::info('Warehouse Edit is initiated by user: ' . Auth::id(), array('c' => '1'));
        $warehouses = Warehouse::find($id);
        $warehouse = \DB::table('lkp_warehouse_types')->orderBy('warehouse_name', 'asc')->lists('warehouse_name', 'id');
        $state = \DB::table('lkp_states')->orderBy('state_name', 'asc')->lists('state_name', 'id');
        $district = \DB::table('lkp_districts')->orderBy('district_name', 'asc')->lists('district_name', 'id');
        $city = \DB::table('lkp_cities')->orderBy('city_name', 'asc')->lists('city_name', 'id');

        //$locality = \DB::table('lkp_localities')->orderBy('locality_name', 'asc')->lists('locality_name', 'id');
        $load_type = CommonComponent::getAllLoadTypes();

        return view('users.warehouse_edit', compact('warehouses'), array('warehouse' => $warehouse, 'state' => $state, 'city' => $city, 'district' => $district,'load_type' => $load_type));
        }catch (Exception $ex) {
            
        }
    }

    /**
     * Update the specified warehouse in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        try {
            Log::info('Warehouse Update is initiated by user: ' . Auth::id(), array('c' => '1'));
            CommonComponent::activityLog("SELLER_EDITED_WAREHOUSE", SELLER_EDITED_WAREHOUSE, 0, HTTP_REFERRER, CURRENT_URL);
            //print_r(Input::all()); 
            if (!empty(Input::all())) {
//print_r($_POST); exit;
                $from = str_replace('/', '-', $request->from_dt);
                $to = str_replace('/', '-', $request->to_dt);
                $created_at = date('Y-m-d H:i:s');
                $createdIp = $_SERVER ['REMOTE_ADDR'];
                $infrastructure_available='';$amenities='';$additional_services='';
                if (!empty($_POST['Warehouse']['infrastructure'])) {
                    $infrastructure_available = implode(",", $_POST['Warehouse']['infrastructure']);
                }
                if (!empty($_POST['Warehouse']['amenities'])) {
                    $amenities = implode(",", $_POST['Warehouse']['amenities']);
                }
                if (!empty($_POST['Warehouse']['additional_services'])) {
                    $additional_services = implode(",", $_POST['Warehouse']['additional_services']);
                }

                Warehouse::where("id", $id)->update(array(
                    'lkp_warehouse_type_id' => $request->wh_type,
                    'lkp_location_id' => $request->city_id,
                    'lkp_district_id' => $request->district_id,
                    'lkp_state_id' => $request->state_id,
                    'pincode' => $request->pincode,
                    'from_date' => date("Y-m-d", strtotime($from)),
                    'to_date' => date("Y-m-d", strtotime($to)),
                    'cargo_type' => $request->cargo_type,
                    'space_min_feet' => $request->space_min_ft,
                    'space_max_feet' => $request->space_max_ft,
                    'capacity' => $request->capacity,
                    'owner_firstname' => $request->wh_owner_fist_name,
                    'owner_middlename' => $request->wh_owner_middle_name,
                    'owner_lastname' => $request->wh_owner_last_name,
                    'contact_firstname' => $request->cp_first_name,
                    'contact_middlename' => $request->cp_middle_name,
                    'contact_lastname' => $request->cp_last_name,
                    'ownership_type' => $request->ownership_type,
                    'mobile_number' => $request->mobile_number,
                    'email' => $request->email,
                    'address' => $request->wh_address,
                    'short_name' => $request->wh_short_name,
                    'infrastructure_available' => $infrastructure_available,
                    'amenities' => $amenities,
                    'additional_services' => $additional_services,
                    'transport_reg_id' => $request->transport_reg_id,
                    'updated_by' => Auth::id(),
                    'updated_at' => $created_at,
                    'updated_ip' => $createdIp
                ));
                CommonComponent::auditLog($id, 'warehouses');
                return redirect('warehouselist')->with('message', 'Warehouse Updated successfully.');
            }
        } catch (Exception $ex) {
            
        }
    }

    /**
     * Remove the specified warehouse from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        try {
        Log::info('Warehouse Delete is initiated by user: ' . Auth::id(), array('c' => '1'));
        CommonComponent::activityLog("SELLER_DELETED_WAREHOUSE", SELLER_DELETED_WAREHOUSE, 0, HTTP_REFERRER, CURRENT_URL);
        CommonComponent::auditLog($id, 'warehouses');
        //Warehouse::find($id)->delete();
        Warehouse::where("id", $id)->update(array(
                    'is_active' => 0
            ));
        return redirect('warehouselist');
        } catch (Exception $ex) {
            
        }
    }
    /**
     * get City based on district_id.
     *
     * @param  int  $district_id
     * @return \Illuminate\Http\Response
     */
    public function getCity() {
        Log::info('Warehouse getCity is initiated by user: ' . Auth::id(), array('c' => '1'));
        if (!empty($_POST["district_id"])) {
            $cities = \DB::table('lkp_cities')->where('lkp_district_id', $_POST["district_id"])->orderBy('city_name', 'asc')->lists('city_name', 'id');

            $str = '<option value = "">Select City</option>';
            foreach ($cities as $k => $v) {

                $str.='<option value = "' . $k . '">' . $v . '</option>';
            }
            echo $str;
        }
    }
    /**
     * get Locality based on city_id.
     *
     * @param  int  $city_id
     * @return \Illuminate\Http\Response
     */
    public function getLocality() {
        Log::info('Warehouse getLocality is initiated by user: ' . Auth::id(), array('c' => '1'));
        if (!empty($_POST["city_id"])) {
            $localities = \DB::table('lkp_localities')->where('lkp_city_id', $_POST["city_id"])->orderBy('locality_name', 'asc')->lists('locality_name', 'id');
            $str = '<option value = "">Select Locality</option>';
            foreach ($localities as $k => $v) {
                $str.='<option value = "' . $k . '">' . $v . '</option>';
            }
            echo $str;
        }
    }
    /**
     * get district based on stateid.
     *
     * @param  int  $state_id
     * @return \Illuminate\Http\Response
     */
    public function getDistrict() {
        Log::info('Warehouse getDistrict is initiated by user: ' . Auth::id(), array('c' => '1'));
        if (!empty($_POST["state_id"])) {
            $localities = \DB::table('lkp_districts')->where('lkp_state_id', $_POST["state_id"])->orderBy('district_name', 'asc')->lists('district_name', 'id');
            $str = '<option value = "">Select District</option>';
            foreach ($localities as $k => $v) {
                $str.='<option value = "' . $k . '">' . $v . '</option>';
            }
            echo $str;
        }
    }
    /**
     * upload multiple warehouses into db.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request) {
        try {
        CommonComponent::activityLog("SELLER_ADDED_NEW_WAREHOUSE", SELLER_ADDED_NEW_WAREHOUSE, 0, HTTP_REFERRER, CURRENT_URL);
        $messages = [
            'warehouse_upload.required' => 'Bulk Upload File field is required',
            'warehouse_upload.mimes' => 'File format be a file of type:  csv.',
        ];
        $rules = [
            'warehouse_upload' => 'required|mimes:csv,txt',
            //'warehouse_upload' => 'mimes:csv,xls',
        ];
        $this->validate($request, $rules, $messages);
        $errors = $_FILES['warehouse_upload']['error'];
        $flag = 0;
        if ($errors == 0) {
            $handle = fopen($_FILES['warehouse_upload']['tmp_name'], 'r');
            if ($handle) {
                $find_header = 0;
                $err = "";
                while (($line = fgetcsv($handle, 1000, ",")) != FALSE) {
                    if ($find_header > 0) {
                        $i = $find_header;
                        $error[$i] = "";
                        $created_at = date('Y-m-d H:i:s');
                        $createdIp = $_SERVER ['REMOTE_ADDR'];
                        $warehouse_all[$i] = new Warehouse();
                        $warehouse_all[$i]->created_at = $created_at;
                        $warehouse_all[$i]->created_by = Auth::id();
                        $warehouse_all[$i]->created_ip = $createdIp;
                        $warehouse_all[$i]->seller_id = Auth::id();
                        $warehouse_all[$i]->is_active = 1;
                        if (!empty($line[0]))
                            $warehouse_all[$i]->lkp_warehouse_type_id = $line[0];
                        else
                            $error[$i].="Warehouse Type, ";
                        if (!empty($line[3]))
                            $warehouse_all[$i]->lkp_location_id = $line[3];
                        else
                            $error[$i].="Location ID, ";
                        if (!empty($line[2]))
                            $warehouse_all[$i]->lkp_district_id = $line[2];
                        else
                            $error[$i].="District ID, ";
                        if (!empty($line[1]))
                            $warehouse_all[$i]->lkp_state_id = $line[1];
                        else
                            $error[$i].="State ID, ";
                        if (!empty($line[4]))
                            $warehouse_all[$i]->pincode = $line[4];
                        else
                            $error[$i].="Pincode, ";
                        if (!empty($line[5]))
                            $warehouse_all[$i]->from_date = date("Y-m-d", strtotime($line[5]));
                        else
                            $error[$i].="From date, ";
                        if (!empty($line[6]))
                            $warehouse_all[$i]->to_date = date("Y-m-d", strtotime($line[6]));
                        else
                            $error[$i].="To date, ";

                        $warehouse_all[$i]->cargo_type = $line[7];
                        if (!empty($line[8]))
                            $warehouse_all[$i]->space_min_feet = $line[8];
                        else
                            $error[$i].="Min Feet, ";
                        if (!empty($line[9]))
                            $warehouse_all[$i]->space_max_feet = $line[9];
                        else
                            $error[$i].="Max Feet, ";
                        if (!empty($line[10]))
                            $warehouse_all[$i]->capacity = $line[10];
                        else
                            $error[$i].="Capacity, ";
                        if (!empty($line[11]))
                            $warehouse_all[$i]->owner_firstname = $line[11];
                        else
                            $error[$i].="Owner First Name, ";

                        $warehouse_all[$i]->owner_middlename = $line[12];

                        if (!empty($line[13]))
                            $warehouse_all[$i]->owner_lastname = $line[13];
                        else
                            $error[$i].="Owner Last Name, ";
                        $warehouse_all[$i]->contact_firstname = $line[14];
                        $warehouse_all[$i]->contact_middlename = $line[15];
                        $warehouse_all[$i]->contact_lastname = $line[16];
                        $warehouse_all[$i]->ownership_type = $line[17];
                        if (!empty($line[18]))
                            $warehouse_all[$i]->mobile_number = $line[18];
                        else
                            $error[$i].="Mobile Number, ";
                        if (!empty($line[19]))
                            $warehouse_all[$i]->email = $line[19];
                        else
                            $error[$i].="Email, ";
                        if (!empty($line[20]))
                            $warehouse_all[$i]->address = $line[20];
                        else
                            $error[$i].="Address, ";
                        if (!empty($line[21]))
                            $warehouse_all[$i]->short_name = $line[21];
                        else
                            $error[$i].="Short Name, ";
                        $warehouse_all[$i]->infrastructure_available = $line[22];
                        $warehouse_all[$i]->amenities = $line[23];
                        $warehouse_all[$i]->additional_services = $line[24];
                        $warehouse_all[$i]->transport_reg_id = $line[25];
                        if ($error[$i] != "") {
                            ++$flag;
                            $err.=$error[$i] . " fields are missing at row " . $i . " \n ";
                        }
                        //$warehouse_all->save();
                    } $find_header++;
                }
                if ($flag != 0) {
                    return redirect('warehouseregister')->with('message', $err);
                } else {
                    for ($i = 1; $i < $find_header; $i++) {

                        if ($warehouse_all[$i]->save()) {
                            CommonComponent::auditLog($warehouse_all[$i]->id, 'warehouses');
                        }
                    }
                }
            }
            fclose($handle);

            return redirect('warehouselist')->with('message', 'Upload file successfully.');
        }else{
            $warehouse = \DB::table('lkp_warehouse_types')->orderBy('warehouse_name', 'asc')->lists('warehouse_name', 'id');
            $state = \DB::table('lkp_states')->orderBy('state_name', 'asc')->lists('state_name', 'id');
            $district = \DB::table('lkp_districts')->orderBy('district_name', 'asc')->lists('district_name', 'id');
            $city = \DB::table('lkp_cities')->orderBy('city_name', 'asc')->lists('city_name', 'id');

            return view('users.warehouse', array('warehouse' => $warehouse, 'state' => $state, 'city' => $city, 'district' => $district));
        }
        }catch (Exception $ex) {
            
        }
    }

}
