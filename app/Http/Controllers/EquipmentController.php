<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Equipments;
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
use Excel;
use App\Models\Equipment;

class EquipmentController extends Controller {
    /**
     * Create a new EquipmentController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth', [
            'except' => 'getLogout'
        ]);
    }
   
    /**
     * Display a listing of the Equipment.
     *
     * @return \Illuminate\Http\Response
     */
    public function index() {
        Log::info('Equipment Listing is initiated by user: ' . Auth::id(), array('c' => '1'));
        $equipments = DB::table ( 'equipments as e' )->leftjoin('lkp_equipment_types as et','et.id','=','e.lkp_equipment_type_id')
                ->where('e.seller_id', Auth::id())->where('e.is_active','1')->select('e.*','et.equipment_type_name')->get();
       
        return view('users.equipment_list', compact('equipments'));
    }

    /**
     * Show the form for creating a new Equipment.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request) {

        try {
            Log::info('Equipment create is initiated by user: ' . Auth::id(), array('c' => '1'));
            CommonComponent::activityLog("SELLER_ADDED_NEW_EQUIPMENT", SELLER_ADDED_NEW_EQUIPMENT, 0, HTTP_REFERRER, CURRENT_URL);
            $equipmentDocumentDirectory = 'uploads/users/';
           
            if (!empty(Input::all())) {
             
                $messages = [

                    'is_driver.required' => 'Driver field is required',
                    'equipment_type_id.required' => 'Equipment Type field is required',
                    'state_id.required' => 'State field is required',
                    'city_id.required' => 'City field is required',
                    'district_id.required' => 'District field is required',
                    'equipment_specs.required' => 'Equipment Specification field is required',
                    
                    'pincode.required' => 'Pincode field is required',
                    'equipment_image.required' => 'Equipment Image field is required',
                    'equipment_image.mimes' => 'Equipment Image format be a file of type:  jpg,jpeg,png,gif.',
                ];
                $rules = [
                    'is_driver' => 'required',
                    'equipment_type_id' => 'required',
                    'state_id' => 'required',
                    'city_id' => 'required',
                    'district_id' => 'required',
                    'equipment_specs' => 'required',
                    'equipment_image' => 'required',
                    'pincode' => 'required',
                    'equipment_image' => 'mimes:jpg,jpeg,png,gif.',
                ];
                $this->validate($request, $rules, $messages);
                $fileName="";
                if (isset($_FILES['equipment_image']) && !empty($_FILES['equipment_image']['name'])) {
                    
                    $fileName = $_FILES['equipment_image']['name'];
                    $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    //You need to handle  both cases
                    //If Any browser does not support serializing of multiple files using FormData()
                    if (!is_array($fileName)) {
                        $fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter($uploadedFileName);
                        $uniqueFileName = time() . "_" . $fileNameWithoutSpecialCharacter . '.' . $extension;
                        if(move_uploaded_file($_FILES['equipment_image']['tmp_name'], $equipmentDocumentDirectory . $uniqueFileName)){
                            $fileName   =   $uniqueFileName;
                        }
                    }
                }
              
                $created_at = date('Y-m-d H:i:s');
                $createdIp = $_SERVER ['REMOTE_ADDR'];
                $equipments_all = new Equipments();
                $equipments_all->created_at = $created_at;
              
                $equipments_all->created_by = Auth::id();
                $equipments_all->created_ip = $createdIp;
                $equipments_all->seller_id = Auth::id();
                $equipments_all->lkp_equipment_type_id = $request->equipment_type_id;
                $equipments_all->lkp_state_id = $request->state_id;
                $equipments_all->lkp_city_id = $request->city_id;
                $equipments_all->lkp_district_id = $request->district_id;
                $equipments_all->pincode = $request->pincode;
                $equipments_all->is_driver = $request->is_driver;
                $equipments_all->equipment_specifications = $request->equipment_specs;
                $equipments_all->equipment_info = $request->equipment_info;
                if($fileName!="")
                $equipments_all->equipment_image = $uniqueFileName;
                $equipments_all->transport_reg_id = $request->transport_reg_id;
                if ($equipments_all->save()) {
                    CommonComponent::auditLog($equipments_all->id, 'equipments');
                    return redirect('list/')
                                    ->with('message', 'Equipment added successfully.');
                } else {
                    if ($equipments_all->equipment_image != '') {
                        $url = getcwd() . $equipmentDocumentDirectory . $uniqueFileName;
                        // deleting image from folder
                        unlink($url);
                    }
                }
            }



            $equipment = \DB::table('lkp_equipment_types')->orderBy('equipment_type_name', 'asc')->lists('equipment_type_name', 'id');
            $state = \DB::table('lkp_states')->orderBy('state_name', 'asc')->lists('state_name', 'id');
            $district = \DB::table('lkp_districts')->orderBy('district_name', 'asc')->lists('district_name', 'id');
            $city = \DB::table('lkp_cities')->orderBy('city_name', 'asc')->lists('city_name', 'id');

            return view('users.equipment', array('equipment' => $equipment, 'state' => $state, 'city' => $city, 'district' => $district));
        } catch (Exception $ex) {
            
        }
    }

    /**
     * Show the form for editing the specified Equipment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id) {
        Log::info('Equipment Edit is initiated by user: ' . Auth::id(), array('c' => '1'));
        $equipments = Equipments::find($id);
        $equipment = \DB::table('lkp_equipment_types')->orderBy('equipment_type_name', 'asc')->lists('equipment_type_name', 'id');
        $state = \DB::table('lkp_states')->orderBy('state_name', 'asc')->lists('state_name', 'id');
        $district = \DB::table('lkp_districts')->orderBy('district_name', 'asc')->lists('district_name', 'id');
        $city = \DB::table('lkp_cities')->orderBy('city_name', 'asc')->lists('city_name', 'id');

        return view('users.equipment_edit', compact('equipments'), array('equipment' => $equipment, 'state' => $state, 'city' => $city, 'district' => $district));
    }

    /**
     * Update the specified Equipment in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request) {

        try {
            Log::info('Equipment Update is initiated by user: ' . Auth::id(), array('c' => '1'));
            CommonComponent::activityLog("SELLER_EDITED_EQUIPMENT", SELLER_EDITED_EQUIPMENT, 0, HTTP_REFERRER, CURRENT_URL);
            $equipmentDocumentDirectory = 'uploads/users/';
            //print_r(Input::all()); 
            if (!empty(Input::all())) {
                $messages = [

                    'is_driver.required' => 'Driver field is required',
                    'equipment_type_id.required' => 'Equipment Type field is required',
                    'state_id.required' => 'State field is required',
                    'city_id.required' => 'City field is required',
                    'district_id.required' => 'District field is required',
                    'equipment_specs.required' => 'Equipment Specification field is required',
                    'equipment_image.required' => 'Equipment Image field is required',
                    'pincode.required' => 'Pincode field is required',
                   
                    'equipment_image.mimes' => 'Equipment Image format be a file of type:  jpg,jpeg,png,gif.',
                ];
                $rules = [
                    'is_driver' => 'required',
                    'equipment_type_id' => 'required',
                    'state_id' => 'required',
                    'city_id' => 'required',
                    'district_id' => 'required',
                    'equipment_specs' => 'required',
                    'equipment_image' => 'required',
                    'pincode' => 'required',
                    'equipment_image' => 'mimes:jpg,jpeg,png,gif,',
                ];

                $this->validate($request, $rules, $messages);

                $created_at = date('Y-m-d H:i:s');
                $createdIp = $_SERVER ['REMOTE_ADDR'];
              
                $equipment = array(
                    'lkp_equipment_type_id' => $request->equipment_type_id,
                    'lkp_state_id' => $request->state_id,
                    'lkp_city_id' => $request->city_id,
                    'lkp_district_id' => $request->district_id,
                    'pincode' => $request->pincode,
                    'is_driver' => $request->is_driver,
                    'equipment_specifications' => $request->equipment_specs,
                    'equipment_info' => $request->equipment_info,
                    'transport_reg_id' => $request->transport_reg_id,
                    'updated_by' => Auth::id(),
                    'updated_at' => $created_at,
                    'updated_ip' => $createdIp
                );
                if (isset($_FILES['equipment_image']) && !empty($_FILES['equipment_image']['name'])) {
                    
                    $fileName = $_FILES['equipment_image']['name'];
                    $uploadedFileName = pathinfo($fileName, PATHINFO_FILENAME);
                    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
                    if (!is_array($fileName)) {
                        $fileNameWithoutSpecialCharacter = CommonComponent::removeSpecialCharacter($uploadedFileName);
                        $uniqueFileName = time() . "_" . $fileNameWithoutSpecialCharacter . '.' . $extension;
                        if(move_uploaded_file($_FILES['equipment_image']['tmp_name'], $equipmentDocumentDirectory . $uniqueFileName)){
                            $equipment['equipment_image'] = $uniqueFileName;
                        }
                 
                        
                    }
                }


               
                Equipments::where("id", $id)->update($equipment);
                CommonComponent::auditLog($id, 'equipments');
                return redirect('list')->with('message', 'Equipment updated successfully.');
            }
        } catch (Exception $ex) {
            
        }
    }

    /**
     * Remove the specified Equipment from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id) {
        try {
            Log::info('Equipment Delete is initiated by user: ' . Auth::id(), array('c' => '1'));
            CommonComponent::activityLog("SELLER_DELETED_EQUIPMENT", SELLER_DELETED_EQUIPMENT, 0, HTTP_REFERRER, CURRENT_URL);
            CommonComponent::auditLog($id, 'equipments');
           
            Equipments::where("id", $id)->update(array(
                'is_active' => 0
            ));
            return redirect('list')->with('message', 'Equipment deleted successfully.');
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
        Log::info('Equipment getCity is initiated by user: ' . Auth::id(), array('c' => '1'));
        if (!empty($_POST["district_id"])) {
            $cities = \DB::table('lkp_cities')->where('lkp_district_id', $_POST["district_id"])->orderBy('city_name', 'asc')->lists('city_name', 'id');

            $str = '<option value = "">Select City *</option>';
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
        Log::info('Equipment getLocality is initiated by user: ' . Auth::id(), array('c' => '1'));
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
        Log::info('Equipment getDistrict is initiated by user: ' . Auth::id(), array('c' => '1'));
        if (!empty($_POST["state_id"])) {
            $localities = \DB::table('lkp_districts')->where('lkp_state_id', $_POST["state_id"])->orderBy('district_name', 'asc')->lists('district_name', 'id');
            $str = '<option value = "">Select District *</option>';
            foreach ($localities as $k => $v) {
                $str.='<option value = "' . $k . '">' . $v . '</option>';
            }
            echo $str;
        }
    }

    /**
     * upload multiple equipments into db.
     *
     * @return \Illuminate\Http\Response
     */
    public function upload(Request $request) {
        try {//print_r($_FILES);exit;
            CommonComponent::activityLog("SELLER_ADDED_NEW_EQUIPMENT", SELLER_ADDED_NEW_EQUIPMENT, 0, HTTP_REFERRER, CURRENT_URL);
            $messages = [
                'equipment_upload.required' => 'Bulk Upload File field is required',
                'equipment_upload.mimes' => 'Bulk Upload should be a file of type: csv.',
            ];
            $rules = [
                'equipment_upload' => 'required|mimes:csv,txt',
                    //'equipment_upload' => 'mimes:csv,xls',
            ];
            $this->validate($request, $rules, $messages);

            $errors = $_FILES['equipment_upload']['error'];
            $flag = 0;
            if ($errors == 0) {

                $handle = fopen($_FILES['equipment_upload']['tmp_name'], 'r');
                if ($handle) {
                    $find_header = 0;
                    $err = "";
                    while (($line = fgetcsv($handle, 1000, ",")) != FALSE) {
                        if ($find_header > 0) {
                            $i = $find_header;
                            $error[$i] = "";
                            $created_at = date('Y-m-d H:i:s');
                            $createdIp = $_SERVER ['REMOTE_ADDR'];
                            $equipments_all[$i] = new Equipments();
                            $equipments_all[$i]->created_at = $created_at;
                            $equipments_all[$i]->created_by = Auth::id();
                            $equipments_all[$i]->created_ip = $createdIp;
                            $equipments_all[$i]->seller_id = Auth::id();
                            $equipments_all[$i]->is_active = 1;
                            if (!empty($line[0]))
                                $equipments_all[$i]->lkp_equipment_type_id = $line[0];
                            else
                                $error[$i].="Equipment Type, ";
                            if (!empty($line[2]))
                                $equipments_all[$i]->lkp_state_id = $line[2];
                            else
                                $error[$i].="State, ";
                            if (!empty($line[3]))
                                $equipments_all[$i]->lkp_city_id = $line[3];
                            else
                                $error[$i].="City, ";
                            if (!empty($line[4]))
                                $equipments_all[$i]->lkp_district_id = $line[4];
                            else
                                $error[$i].="District, ";
                            if (!empty($line[5]))
                                $equipments_all[$i]->pincode = $line[5];
                            else
                                $error[$i].="Pincode, ";
                            if ($line[6] != "")
                                $equipments_all[$i]->is_driver = $line[6];
                            else
                                $error[$i].="Is Driver,";
                            $equipments_all[$i]->equipment_specifications = $line[1];
                            $equipments_all[$i]->equipment_info = $line[7];
                            $equipments_all[$i]->transport_reg_id = $line[8];
                            if ($error[$i] != "") {
                                ++$flag;
                                $err.=$error[$i] . " fields are missing at row " . $i . " \n ";
                                //return redirect('equipmentRegister')->with('message', $error[$i]." fields are missing in Uploaded file.");
                            }
                        } $find_header++;
                    }

                    if ($flag != 0) {
                        return redirect('equipmentregister')->with('message', $err);
                    } else {
                        for ($i = 1; $i < $find_header; $i++) {
                         
                            if ($equipments_all[$i]->save()) {
                                CommonComponent::auditLog($equipments_all[$i]->id, 'equipments');
                            }
                        }
                    }
                }
                fclose($handle);


                return redirect('list')->with('message', 'Upload file successfully.');
            } else {
               
                $equipment = \DB::table('lkp_equipment_types')->orderBy('equipment_type_name', 'asc')->lists('equipment_type_name', 'id');
                $state = \DB::table('lkp_states')->orderBy('state_name', 'asc')->lists('state_name', 'id');
                $district = \DB::table('lkp_districts')->orderBy('district_name', 'asc')->lists('district_name', 'id');
                $city = \DB::table('lkp_cities')->orderBy('city_name', 'asc')->lists('city_name', 'id');

                return view('users.equipment', array('equipment' => $equipment, 'state' => $state, 'city' => $city, 'district' => $district));
            }

           
        } catch (Exception $ex) {
            
        }
    }

}
