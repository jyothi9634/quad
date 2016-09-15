<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\VehicleDetail;
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

class VehicleController extends Controller {
    /**
     * Create a new VehicleController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth', [
            'except' => 'getLogout'
        ]);
    }
    /**
     * Display a listing of the Vehicle.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        Log::info('Vehicle Listing is initiated by user: ' . Auth::id(), array('c' => '1'));
        $vehicle = VehicleDetail::all()->where('owner_id', Auth::id())->where('is_active','1');
        return view('users.vehicle_list', compact('vehicle'));
    }

    /**
     * Show the form for creating a new Vehicle.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {
        try {
            Log::info('Vehicle Create is initiated by user: ' . Auth::id(), array('c' => '1'));
            CommonComponent::activityLog("SELLER_ADDED_NEW_VEHICLE", SELLER_ADDED_NEW_VEHICLE, 0, HTTP_REFERRER, CURRENT_URL);
            $equipmentDocumentDirectory = 'uploads/users/';

            if (!empty(Input::all())) {

                $messages = [
                    'vehicle_number.required' => 'Vehicle number field is required!',
                    'vehicle_type.required' => 'Vehicle type ield is required',
                    'vehicle_width.required' => 'Vehicle width field is required',
                    'vehicle_height.required' => 'Vehicle height field is required',
                    'vehicle_length.required' => 'Vehicle length field is required',
                    'vehicle_capacity.required' => 'Vehicle Capacity field is required',
                    'reg_owner_fname.required' => 'Owner First Name field is required',
                    'reg_owner_lname.required' => 'Owner Last Name field is required',
                    'mfg_year.required' => 'Mfg Year field is required',
                    'chasis_number.required' => 'Chasis Number field is required',
                    'insurance_file_name.mimes' => 'Insurance_file format be a file of type:  doc,pdf,jpg,png,gif.',
                    'permit_copy_file_name.mimes' => 'Permit_copy format be a file of type:  doc,pdf,jpg,png,gif.',
                    'fc_file_name.mimes' => 'Fc format be a file of type:  doc,pdf,jpg,png,gif.',
                    'rc_file_name.mimes' => 'Rc format be a file of type:  doc,pdf,jpg,png,gif.',
                    'device_number'=>'Device Number filed is required',
                    'sim_imsi_number'=>'Sim IMSI Number field is required',
                    'mobile_operator'=>'Mobiel Operator field is required',
                    'mobile_number'=>'Mobile Number field is required'
                ];
                $rules = [
                    'vehicle_number' => 'required',
                    'vehicle_type' => 'required',
                    'vehicle_width' => 'required',
                    'vehicle_height' => 'required',
                    'vehicle_length' => 'required',
                    'vehicle_capacity' => 'required',
                    'reg_owner_fname' => 'required',
                    'reg_owner_lname' => 'required',
                    'mfg_year' => 'required',
                    'chasis_number' => 'required',
                    'insurance_file_name' => 'mimes:jpg,png,gif,doc,pdf',
                    'permit_copy_file_name' => 'mimes:jpg,png,gif,doc,pdf',
                    'fc_file_name' => 'mimes:jpg,png,gif,doc,pdf',
                    'rc_file_name' => 'mimes:jpg,png,gif,doc,pdf'
                ];

                if($request->is_gps==1) // GPS is Yes
                {
                    $messages = array_merge($messages,array(
                        'device_number'=>'Device Number filed is required',
                        'sim_imsi_number'=>'Sim IMSI Number field is required',
                        'mobile_operator'=>'Mobiel Operator field is required',
                        'mobile_number'=>'Mobile Number field is required',
                        'device_fixed_date'=>"Please select date of device fixed in vehicle",
                    ));
                    $rules = array_merge($rules,array(
                        'device_number'=>'required',
                        'sim_imsi_number'=>'required',
                        'mobile_operator'=>'required',
                        'mobile_number'=>'required',
                        'device_fixed_date'=>'required'
                    ));
                }

                $this->validate($request, $rules, $messages);
                $insurance_validity = str_replace('/', '-', $request->insurance_validity);
                $fc_validity = str_replace('/', '-', $request->fc_validity);

                $created_at = date('Y-m-d H:i:s');
                $createdIp = $_SERVER ['REMOTE_ADDR'];
                $warehouse_all = new VehicleDetail();
                $warehouse_all->created_at = $created_at;
                //in place of 1 ->Auth::User()->user_id
                $warehouse_all->created_by = Auth::id();
                $warehouse_all->created_ip = $createdIp;
                $warehouse_all->owner_id = Auth::id();

                $warehouse_all->first_year_turnover = $request->vehicle_owned;
                $warehouse_all->second_year_turnover = $request->vehicle_attatched;
                $warehouse_all->third_year_turnover = $request->vehicle_gps;


                $warehouse_all->vehicle_number = $request->vehicle_number;
                $warehouse_all->lkp_vehicle_type_id = $request->vehicle_type;
                $warehouse_all->vehicle_dimension = $request->vehicle_width . "*" . $request->vehicle_height . "*" . $request->vehicle_length;
                $warehouse_all->vehicle_capacity = $request->vehicle_capacity;
                $warehouse_all->lkp_load_type_id = $request->load_type;
                $warehouse_all->reg_owner_firstname = $request->reg_owner_fname;
                $warehouse_all->reg_owner_lastname = $request->reg_owner_lname;
                $warehouse_all->mfg_year = $request->mfg_year;
                $warehouse_all->chasis_number = $request->chasis_number;
                $warehouse_all->engine_number = $request->engine_number;
                $warehouse_all->is_gps = $request->is_gps;
                
                if($request->is_gps==1){
                    $device_fixed_date = str_replace('/', '-', $request->device_fixed_date);
                    $warehouse_all->device_number = $request->device_number;
                    $warehouse_all->device_fixed_date =  date("Y-m-d", strtotime($device_fixed_date));
                    $warehouse_all->mobile_number = $request->mobile_number;
                    $warehouse_all->mobile_operator = $request->mobile_operator;
                    $warehouse_all->sim_imsi_number = $request->sim_imsi_number;
                    //Send request GPS Registration 
                    $gpsParams = array(
                            "DIMEI" => $request->device_number,
                            "SIMEI"  => $request->sim_imsi_number,
                            "REGNO" => $request->vehicle_number,
                            "O" => $request->mobile_operator,
                            "M" => $request->mobile_number,
                            "VNAME" => CommonComponent::getVehicleType($warehouse_all->lkp_vehicle_type_id),
                            "DATE" => date("Y-m-d", strtotime($device_fixed_date)),
                            "TYPE" => ''
                        );
                    $volty_response = CommonComponent::gpsRegistration($gpsParams);
                    $volty_data = json_decode($volty_response);
                    if($volty_data->STATUS=='SUCCESS'){
                        $warehouse_all->volty_register = 1;
                    }else{
                        $string = $volty_data->INFO; 
                        $pos = strpos($string, "for key 'RegNo_UNIQUE'");
                        $dupl_pos = strpos($string, "for key 'PRIMARY'");
                        if($pos==true || $dupl_pos == true){
                            $warehouse_all->volty_register = 1;
                        }
                    }
                    $warehouse_all->volty_response = $volty_response;
                }
                $warehouse_all->is_insured = $request->is_insured;
                $warehouse_all->insurance_validity = date("Y-m-d", strtotime($insurance_validity));
                $warehouse_all->permit_type = $request->permit_type;
                $warehouse_all->fc_validity = date("Y-m-d", strtotime($fc_validity));
                $warehouse_all->transport_reg_id = $request->transport_reg_id;
                
                if (isset($_FILES['insurance_file_name']) && !empty($_FILES['insurance_file_name']['name'])) {
                    $error = $_FILES['insurance_file_name']['error'];
                    $fileName = $_FILES['insurance_file_name']['name'];
                    $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);

                    if (!is_array($fileName)) {
                        $fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter($uploadedFileName);
                        $uniqueFileName1 = time() . "_" . $fileNameWithoutSpecialCharacter . '.' . $extension;
                        $uploadedfile = move_uploaded_file($_FILES['insurance_file_name']['tmp_name'], $equipmentDocumentDirectory . $uniqueFileName1);
                        if($uploadedfile)
                        $warehouse_all->insurance_file_name = $uniqueFileName1;
                    }
                }
                if (isset($_FILES['permit_copy_file_name']) && !empty($_FILES['permit_copy_file_name']['name'])) {
                    $error = $_FILES['permit_copy_file_name']['error'];
                    $fileName = $_FILES['permit_copy_file_name']['name'];
                    $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    if (!is_array($fileName)) {
                        $fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter($uploadedFileName);
                        $uniqueFileName2 = time() . "_" . $fileNameWithoutSpecialCharacter . '.' . $extension;
                        $uploadedfile = move_uploaded_file($_FILES['permit_copy_file_name']['tmp_name'], $equipmentDocumentDirectory . $uniqueFileName2);
                        if($uploadedfile)
                        $warehouse_all->permit_copy_file_name = $uniqueFileName2;
                    }
                }
                if (isset($_FILES['fc_file_name']) && !empty($_FILES['fc_file_name']['name'])) {
                    $error = $_FILES['fc_file_name']['error'];
                    $fileName = $_FILES['fc_file_name']['name'];
                    $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    if (!is_array($fileName)) {
                        $fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter($uploadedFileName);
                        $uniqueFileName3 = time() . "_" . $fileNameWithoutSpecialCharacter . '.' . $extension;
                        $uploadedfile = move_uploaded_file($_FILES['fc_file_name']['tmp_name'], $equipmentDocumentDirectory . $uniqueFileName3);
                        if($uploadedfile)
                        $warehouse_all->fc_file_name = $uniqueFileName3;
                    }
                }
                if (isset($_FILES['rc_file_name']) && !empty($_FILES['rc_file_name']['name'])) {
                    $error = $_FILES['rc_file_name']['error'];
                    $fileName = $_FILES['rc_file_name']['name'];
                    $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    if (!is_array($fileName)) {
                        $fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter($uploadedFileName);
                        $uniqueFileName4 = time() . "_" . $fileNameWithoutSpecialCharacter . '.' . $extension;
                        $uploadedfile = move_uploaded_file($_FILES['rc_file_name']['tmp_name'], $equipmentDocumentDirectory . $uniqueFileName4);
                        if($uploadedfile)
                        $warehouse_all->rc_file_name = $uniqueFileName4;
                    }
                }
                if ($warehouse_all->save()) {
                    CommonComponent::auditLog($warehouse_all->id, 'vehicle_details');
                    return redirect('vehiclelist')
                                    ->with('message', 'Vehicle Details added successfully.');
                }
            } else {

                $load_type = CommonComponent::getAllLoadTypes();
                $vehicle = CommonComponent::getAllVehicleType();
                $permittypes = CommonComponent::getAllStates();
                $vehiclecapacities = CommonComponent::getAllVehicleTypeCapacity();
//echo "<pre>";print_R($vehiclecapacities);die;

                return view('users.vehicle', array('load_type' => $load_type, 'vehicle' => $vehicle, 'permittypes' => $permittypes,'vehiclecapacities' => $vehiclecapacities));
            }
        } catch (Exception $ex) {
            
        }
    }


    /**
     * Show the form for editing the specified Vehicle.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        Log::info('Vehicle Edit is initiated by user: ' . Auth::id(), array('c' => '1'));
        $vehicles = VehicleDetail::find($id);
        $load_type = CommonComponent::getAllLoadTypes();
        $vehicle = CommonComponent::getAllVehicleType();
        $permittypes = CommonComponent::getAllStates();
        $vehiclecapacities = CommonComponent::getAllVehicleTypeCapacity();
        return view('users.vehicle_edit', compact('vehicles'), array('load_type' => $load_type, 'vehicle' => $vehicle, 'permittypes' => $permittypes,'vehiclecapacities' => $vehiclecapacities));
    }

    /**
     * Update the specified Vehicle in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id) {
        try {
            Log::info('Vehicle Update is initiated by user: ' . Auth::id(), array('c' => '1'));
            CommonComponent::activityLog("SELLER_EDITED_VEHICLE", SELLER_EDITED_VEHICLE, 0, HTTP_REFERRER, CURRENT_URL);

            if (!empty(Input::all())) {

                $messages = [
                    'vehicle_number.required' => 'Vehicle number field is required!',
                    'vehicle_type.required' => 'Vehicle type ield is required',
                    'vehicle_width.required' => 'Vehicle width field is required',
                    'vehicle_height.required' => 'Vehicle height field is required',
                    'vehicle_length.required' => 'Vehicle length field is required',
                    'vehicle_capacity.required' => 'Vehicle Capacity field is required',
                    'reg_owner_fname.required' => 'Owner First Name field is required',
                    'reg_owner_lname.required' => 'Owner Last Name field is required',
                    'mfg_year.required' => 'Mfg Year field is required',
                    'chasis_number.required' => 'Chasis Number field is required',
                    'insurance_file_name.mimes' => 'Insurance_file format be a file of type:  doc,pdf,jpg,png,gif.',
                    'permit_copy_file_name.mimes' => 'Permit_copy format be a file of type:  doc,pdf,jpg,png,gif.',
                    'fc_file_name.mimes' => 'Fc format be a file of type:  doc,pdf,jpg,png,gif.',
                    'rc_file_name.mimes' => 'Rc format be a file of type:  doc,pdf,jpg,png,gif.',
                
                ];
                $rules = [
                    'vehicle_number' => 'required',
                    'vehicle_type' => 'required',
                    'vehicle_width' => 'required',
                    'vehicle_height' => 'required',
                    'vehicle_length' => 'required',
                    'vehicle_capacity' => 'required',
                    'reg_owner_fname' => 'required',
                    'reg_owner_lname' => 'required',
                    'mfg_year' => 'required',
                    'chasis_number' => 'required',
                    'insurance_file_name' => 'mimes:jpg,png,gif,doc,pdf',
                    'permit_copy_file_name' => 'mimes:jpg,png,gif,doc,pdf',
                    'fc_file_name' => 'mimes:jpg,png,gif,doc,pdf',
                    'rc_file_name' => 'mimes:jpg,png,gif,doc,pdf',
                ];

                if($request->is_gps==1) // GPS is Yes
                {
                    $messages = array_merge($messages,array(
                        'device_number'=>'Device Number filed is required',
                        'sim_imsi_number'=>'Sim IMSI Number field is required',
                        'mobile_operator'=>'Mobiel Operator field is required',
                        'mobile_number'=>'Mobile Number field is required',
                        'device_fixed_date'=>"Please select date of device fixed in vehicle",
                    ));
                    $rules = array_merge($rules,array(
                        'device_number'=>'required',
                        'sim_imsi_number'=>'required',
                        'mobile_operator'=>'required',
                        'mobile_number'=>'required',
                        'device_fixed_date'=>'required'
                    ));
                }

                $this->validate($request, $rules, $messages);
                $equipmentDocumentDirectory = 'uploads/users/';
                $insurance_validity = str_replace('/', '-', $request->insurance_validity);
                $fc_validity = str_replace('/', '-', $request->fc_validity);
                
                $created_at = date('Y-m-d H:i:s');
                $createdIp = $_SERVER ['REMOTE_ADDR'];

                $warehouse = array(
                    'first_year_turnover' => $request->vehicle_owned,
                    'second_year_turnover' => $request->vehicle_attatched,
                    'third_year_turnover' => $request->vehicle_gps,
                    'vehicle_number' => $request->vehicle_number,
                    'lkp_vehicle_type_id' => $request->vehicle_type,
                    'vehicle_dimension' => $request->vehicle_width . "*" . $request->vehicle_height . "*" . $request->vehicle_length,
                    'vehicle_capacity' => $request->vehicle_capacity,
                    'lkp_load_type_id' => $request->load_type,
                    'reg_owner_firstname' => $request->reg_owner_fname,
                    'reg_owner_lastname' => $request->reg_owner_lname,
                    'mfg_year' => $request->mfg_year,
                    'chasis_number' => $request->chasis_number,
                    'engine_number' => $request->engine_number,
                    'is_gps' => $request->is_gps,
                    'is_insured' => $request->is_insured,
                    'insurance_validity' => date("Y-m-d", strtotime($insurance_validity)),
                    'permit_type' => $request->permit_type,
                    'fc_validity' => date("Y-m-d", strtotime($fc_validity)),
                    'transport_reg_id' => $request->transport_reg_id,
                    'updated_by' => Auth::id(),
                    'updated_at' => $created_at,
                    'updated_ip' => $createdIp
                );
                if($request->is_gps==1){
                    $device_fixed_date = str_replace('/', '-', $request->device_fixed_date);
                    $warehouse['device_number'] = $request->device_number;
                    $warehouse['device_fixed_date'] = date("Y-m-d", strtotime($device_fixed_date));
                    $warehouse['mobile_number'] = $request->mobile_number;
                    $warehouse['mobile_operator'] = $request->mobile_operator;
                    $warehouse['sim_imsi_number'] = $request->sim_imsi_number;
                    //Send request GPS Registration 
                    $gpsParams = array(
                            "NDIMEI" => $request->device_number,
                            "SIMEI"  => $request->sim_imsi_number,
                            "REGNO" => $request->vehicle_number,
                            "O" => $request->mobile_operator,
                            "M" => $request->mobile_number,
                            //"VNAME" => CommonComponent::getVehicleType($warehouse['lkp_vehicle_type_id']),
                            "DATE" => date("Y-m-d", strtotime($device_fixed_date)),
                            "TYPE" => ''
                        );
                    $volty_response = CommonComponent::gpsStoreVehicleDevice($gpsParams);

                    $volty_data = json_decode($volty_response);
                    if($volty_data->STATUS=='SUCCESS'){
                        $warehouse_all['volty_register'] = 1;
                    }else{
                        $string = $volty_data->INFO; 

                        $pos = strpos($string, "for key 'RegNo_UNIQUE'");
                        $dupl_pos = strpos($string, "for key 'PRIMARY'");
                        if($pos==true || $dupl_pos == true){
                            $warehouse_all['volty_register'] = 1;
                        }
                    }
                    $warehouse['volty_update_response'] = $volty_response;
                }else{
                    $warehouse['device_number'] = NULL;
                    $warehouse['device_fixed_date'] = NULL;
                    $warehouse['mobile_number'] = NULL;
                    $warehouse['mobile_operator'] = NULL;
                    $warehouse['sim_imsi_number'] = NULL;                    
                }

                if (isset($_FILES['insurance_file_name']) && !empty($_FILES['insurance_file_name']['name'])) {
                    $error = $_FILES['insurance_file_name']['error'];
                    $fileName = $_FILES['insurance_file_name']['name'];
                    $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);

                    if (!is_array($fileName)) {
                        $fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter($uploadedFileName);
                        $uniqueFileName1 = time() . "_" . $fileNameWithoutSpecialCharacter . '.' . $extension;
                        $uploadedfile = move_uploaded_file($_FILES['insurance_file_name']['tmp_name'], $equipmentDocumentDirectory . $uniqueFileName1);
                        if($uploadedfile)
                        $warehouse['insurance_file_name'] = $uniqueFileName1;
                    }
                }
                if (isset($_FILES['permit_copy_file_name']) && !empty($_FILES['permit_copy_file_name']['name'])) {
                    $error = $_FILES['permit_copy_file_name']['error'];
                    $fileName = $_FILES['permit_copy_file_name']['name'];
                    $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    if (!is_array($fileName)) {
                        $fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter($uploadedFileName);
                        $uniqueFileName2 = time() . "_" . $fileNameWithoutSpecialCharacter . '.' . $extension;
                        $uploadedfile = move_uploaded_file($_FILES['permit_copy_file_name']['tmp_name'], $equipmentDocumentDirectory . $uniqueFileName2);
                        if($uploadedfile)
                        $warehouse['permit_copy_file_name'] = $uniqueFileName2;
                    }
                }
                if (isset($_FILES['fc_file_name']) && !empty($_FILES['fc_file_name']['name'])) {
                    $error = $_FILES['fc_file_name']['error'];
                    $fileName = $_FILES['fc_file_name']['name'];
                    $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    if (!is_array($fileName)) {
                        $fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter($uploadedFileName);
                        $uniqueFileName3 = time() . "_" . $fileNameWithoutSpecialCharacter . '.' . $extension;
                        $uploadedfile = move_uploaded_file($_FILES['fc_file_name']['tmp_name'], $equipmentDocumentDirectory . $uniqueFileName3);
                        if($uploadedfile)
                        $warehouse['fc_file_name'] = $uniqueFileName3;
                    }
                }
                if (isset($_FILES['rc_file_name']) && !empty($_FILES['rc_file_name']['name'])) {
                    $error = $_FILES['rc_file_name']['error'];
                    $fileName = $_FILES['rc_file_name']['name'];
                    $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    if (!is_array($fileName)) {
                        $fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter($uploadedFileName);
                        $uniqueFileName4 = time() . "_" . $fileNameWithoutSpecialCharacter . '.' . $extension;
                        $uploadedfile = move_uploaded_file($_FILES['rc_file_name']['tmp_name'], $equipmentDocumentDirectory . $uniqueFileName4);
                        if($uploadedfile)
                        $warehouse['rc_file_name'] = $uniqueFileName4;
                    }
                }

                VehicleDetail::where("id", $id)->update($warehouse);
                CommonComponent::auditLog($id, 'vehicle_details');
                return redirect('vehiclelist')->with('message', 'Vehicle Updated successfully.');
            }
        } catch (Exception $ex) {
            
        }
    }

    /**
     * Remove the specified Vehicle from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        try {
            Log::info('Vehicle Delete is initiated by user: ' . Auth::id(), array('c' => '1'));
            $vehicleDetails = VehicleDetail::where("id", $id)->select('id','vehicle_number')->first();
            // Checking duplicate vehicle count of active with volty gps register
            $checkDuplicateVehicle = VehicleDetail::whereRaw("id<>".$id)
                                        ->where('vehicle_number',$vehicleDetails->vehicle_number)
                                        ->where('volty_register',1)
                                        ->where('is_active',1)    
                                        ->count();

            if(empty($checkDuplicateVehicle)){
                // Delete Volty GPS request
               echo CommonComponent::gpsDestroy($vehicleDetails->vehicle_number);
             }

            CommonComponent::activityLog("SELLER_DELETED_VEHICLE", SELLER_DELETED_VEHICLE, 0, HTTP_REFERRER, CURRENT_URL);
            CommonComponent::auditLog($id, 'vehicle_details');
            //VehicleDetail::find($id)->delete();
            VehicleDetail::where("id", $id)->update(array(
                'is_active' => 0
            ));
            return redirect('vehiclelist');
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
        Log::info('Vehicle getCity is initiated by user: ' . Auth::id(), array('c' => '1'));
        if (!empty($_POST["state_id"])) {
            $cities = \DB::table('lkp_locations')->where('state_id', $_POST["state_id"])->orderBy('location_name', 'asc')->lists('location_name', 'id');

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
        Log::info('Vehicle getCity is initiated by user: ' . Auth::id(), array('c' => '1'));
        if (!empty($_POST["city_id"])) {
            $localities = \DB::table('tbl_intracity_master')->where('location_id', $_POST["city_id"])->orderBy('intracity_name', 'asc')->lists('intracity_name', 'id');
            $str = '<option value = "">Select Locality</option>';
            foreach ($localities as $k => $v) {
                $str.='<option value = "' . $k . '">' . $v . '</option>';
            }
            echo $str;
        }
    }

    /**
     * upload multiple VEHICLES into db.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request) {
        try {
            CommonComponent::activityLog("SELLER_ADDED_NEW_VEHICLE", SELLER_ADDED_NEW_VEHICLE, 0, HTTP_REFERRER, CURRENT_URL);
            $messages = [
                'vehicle_upload.required' => 'Bulk Upload File field is required',
                'vehicle_upload.mimes' => 'Bulk Upload should be a file of type: csv.',
            ];
            $rules = [
                'vehicle_upload' => 'required|mimes:csv,txt',
                    //'vehicle_upload' => 'mimes:csv',
            ];
            $this->validate($request, $rules, $messages);
            $errors = $_FILES['vehicle_upload']['error'];
            $flag = 0;
            if ($errors == 0) {
                $handle = fopen($_FILES['vehicle_upload']['tmp_name'], 'r');
                if ($handle) {
                    $find_header = 0;
                    $err = "";
                    while (($line = fgetcsv($handle, 1000, ",")) != FALSE) {
                        if ($find_header > 0) {
                            $i = $find_header;
                            $error[$i] = "";
                            $created_at = date('Y-m-d H:i:s');
                            $createdIp = $_SERVER ['REMOTE_ADDR'];
                            $warehouse_all[$i] = new VehicleDetail();
                            $warehouse_all[$i]->created_at = $created_at;
                            //in place of 1 ->Auth::User()->user_id
                            $warehouse_all[$i]->created_by = Auth::id();
                            $warehouse_all[$i]->created_ip = $createdIp;
                            $warehouse_all[$i]->owner_id = Auth::id();
                            $warehouse_all[$i]->is_active = 1;
                            $warehouse_all[$i]->first_year_turnover = $line[0];
                            $warehouse_all[$i]->second_year_turnover = $line[1];
                            $warehouse_all[$i]->third_year_turnover = $line[2];
                            if (!empty($line[3]))
                                $warehouse_all[$i]->vehicle_number = $line[3];
                            else
                                $error[$i].="Vehicle Number, ";
                            if (!empty($line[4]))
                                $warehouse_all[$i]->lkp_vehicle_type_id = $line[4];
                            else
                                $error[$i].="Vehicle Type, ";
                            if (!empty($line[5]) && !empty($line[6]) && !empty($line[7]))
                                $warehouse_all[$i]->vehicle_dimension = $line[5] . "*" . $line[6] . "*" . $line[7];
                            else if (empty($line[5]))
                                $error[$i].="Length, ";
                            else if (empty($line[6]))
                                $error[$i].="Width, ";
                            else if (empty($line[7]))
                                $error[$i].="Height, ";
                            if (!empty($line[8]))
                                $warehouse_all[$i]->vehicle_capacity = $line[8];
                            else
                                $error[$i].="Vehicle Capacity, ";
                            $warehouse_all[$i]->lkp_load_type_id = $line[9];
                            if (!empty($line[10]))
                                $warehouse_all[$i]->reg_owner_firstname = $line[10];
                            else
                                $error[$i].="Owner FirstName, ";
                            if (!empty($line[11]))
                                $warehouse_all[$i]->reg_owner_lastname = $line[11];
                            else
                                $error[$i].="Owner LastName, ";
                            if (!empty($line[12]))
                                $warehouse_all[$i]->mfg_year = $line[12];
                            else
                                $error[$i].="Mfg Year, ";
                            if (!empty($line[13]))
                                $warehouse_all[$i]->chasis_number = $line[13];
                            else
                                $error[$i].="Chasis Number, ";
                            $warehouse_all[$i]->engine_number = $line[14];
                            $warehouse_all[$i]->is_gps = $line[15];
                            $warehouse_all[$i]->is_insured = $line[16];
                            $warehouse_all[$i]->insurance_validity = date("Y-m-d", strtotime($line[17]));
                            $warehouse_all[$i]->permit_type = $line[18];
                            $warehouse_all[$i]->fc_validity = date("Y-m-d", strtotime($line[19]));
                            $warehouse_all[$i]->transport_reg_id = $line[20];
                            if ($error[$i] != "") {
                                ++$flag;
                                $err.=$error[$i] . " fields are missing at row " . $i . " \n ";
                            }
                            //$warehouse_all->save();
                        } $find_header++;
                    }
                    if ($flag != 0) {
                        return redirect('vehicleregister')->with('message', $err);
                    } else {
                        for ($i = 1; $i < $find_header; $i++) {

                            if ($warehouse_all[$i]->save()) {
                                CommonComponent::auditLog($warehouse_all[$i]->id, 'vehicle_details');
                            }
                        }
                    }
                }
                fclose($handle);

                return redirect('vehiclelist')->with('message', 'Upload file successfully.');
            } else {

                $load_type = \DB::table('lkp_load_types')->orderBy('load_type', 'asc')->lists('load_type', 'id');
                $vehicle = \DB::table('lkp_vehicle_types')->orderBy('vehicle_type', 'asc')->lists('vehicle_type', 'id');
                return view('users.vehicle', array('load_type' => $load_type, 'vehicle' => $vehicle));
            }
        } catch (Exception $ex) {
            
        }
    }
    
    protected function checkUniqueChasis($chasisNo = '') {
	try{
            Log::info ( 'Chasis uniqueness check for  user :' . Auth::id(), array ('c' => '1' ) );
        
		CommonComponent::activityLog ( "Chasis_UNIQUE", CHASIS_UNIQUE, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (isset ( $_POST ['chasisno'] ) && $_POST ['chasisno'] != '') {
			$chasisNo = $_POST ['chasisno'];
			$chasisNoExist = VehicleDetail::where ( 'chasis_number', $chasisNo )->get ();
			if (count ( $chasisNoExist ) > 0) {
				return "Chasis Number already exist";
			} else {
				return "200";
			}
		}
            } catch (Exception $ex) {

            }
		
	}
        protected function checkUniqueEngine($engineNo='') {
	try{
            Log::info ( 'Engine uniqueness check for  user :' . Auth::id(), array ('c' => '1' ) );
        
		CommonComponent::activityLog ( "ENGINE_UNIQUE", ENGINE_UNIQUE, 0, HTTP_REFERRER, CURRENT_URL );
		
		if (isset ( $_POST ['engineno'] ) && $_POST ['engineno'] != '') {
			$engineNo = $_POST ['engineno'];
                        $engineNoExist = VehicleDetail::where ( 'engine_number', $engineNo )->get ();
			if (count ( $engineNoExist ) > 0) {
				return "Engine Number already exist";
			} else {
				return "200";
			}
		}
		
            } catch (Exception $ex) {

            }
		
	}

}
