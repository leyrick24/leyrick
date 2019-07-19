<?php
/*
* <System Name> iBMS
* <Program Name> CommonFunctions.php
*
* <Create> 2018.06.21 TP Bryan
* <Update> 2018.06.29 TP Bryan    Edited 1.0 Fixed Timeout -> Variable Timeout
*          2018.07.09 TP Bryan    Added 5.0
*          2018.07.19 TP Bryan    Added 6.0
*          2018.11.05 TP Robert   Modify createResponse Add Manufacturer ID
*          2018.12.17 TP Robert   Create Function for Logs
*          2018.12.18 OJT Jethro  Added line 932 to differentiate Automated or Manual command for Logs
*          2019.05.30 TP Harvey   Applying Coding Standard
*
*/

namespace App\Traits;

use Illuminate\Http\Request;
use App\IrLearning;
use App\Code;
use App\Device;
use App\Room;
use Illuminate\Support\Facades\Crypt;
use App\Events\DeviceCommandEvent;
use App\Events\IREvent;
use App\Binding;
use App\Logs;
use App\AuditLogs;
use Illuminate\Support\Facades\Log;

/**
 * <Class Name> CommonFunctions
 *
 * <Overview> Utility functions
 */
trait CommonFunctions
{
    /*************************************************************************/
    /* Processing Hierarchy                                                  */
    /*************************************************************************/
    // sendToSocket                    (1.0) Send message to UDP socket
    // convertDeviceType               (2.0) Convert device type to human-readable format
    // convertInstruction              (3.0) Convert instruction to machine-readable format
    // convertDeviceData               (4.0) Convert device data to human-readable format
    // generatePassword                (5.0) Generate a unique alphanumeric password
    // encryptMessage                  (6.0) Encrypt a message
    // createGetResponse               (7.0) Create response to GET request
    // broadcastNewData                (8.0) Broadcast new data to frontend
    // insertDevicetoNotification      (9.0) Execute instructions according to registered binding
    // processNotification             (10.0) Process Notification command from insertDevicetoNotification
    // decryptMessage           	   (11.0) Decrypts Message From AES-CBC-256
    // isJson           	   		   (12.0) Check if string is in JSON Format
    // newInstructionRequest           (13.0) Set new Instruction to devices
    // deleteDevicePlot                (14.0) Device Plotting from Floor Table
    // storeDeviceData                 (15.0) Save Device data
    // storeLogs                       (16.0) Store System Logs
    // auditLogs                       (17.0) Store Audit Trail Logs
    // currentTime                     (18.0) Get Current Time


    /**
     * <Layer Number> (1.0) Send message to UDP socket
     * <Processing Name> sendToSocket
     * <Function>
     *
     * @param string, string, string, $array
     * @return string
     */
    public function sendToSocket($remote_ip, $remote_port, $message,
        						 $timeout = array("sec"=>0,"usec"=>10000))
    {
            $recvBool = false;  //Bool for the socketrecvfrom
            $try = 1;           //Number of tries
            $tryCounter = 0;    //Try counter
            $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            // Maximum execution time of 10 secs
            socket_set_option($sock, SOL_SOCKET, SO_RCVTIMEO, $timeout);
            socket_sendto($sock, $message, strlen($message), 0, $remote_ip, $remote_port);

            while($recvBool == false){
                 $recvBool = socket_recvfrom($sock, $buf, 1024, 0, $remote_ip, $remote_port);
                 if($tryCounter > $try){
                    break;
                 }
                 $tryCounter++;
            }

            return rtrim($buf); // Remove trailing null-terminator/s


    }

    /**
     * <Layer Number> (2.0) Convert device type to human-readable format
     * <Processing Name> convertDeviceType
     * <Function> Convert Device Type(Code) to readable Type(e.g. embedded_switch_2)
     *
     * @param string
     * @return string
     */
    public function convertDeviceType($devType)
    {
        $code = Code::where('DEVICE_TYPE_CODE', $devType)
                    ->select('DEVICE_TYPE_VALUE')
                    ->first();

        return $code ? $code->DEVICE_TYPE_VALUE : "Code does not exist";
    }

    /**
     * <Layer Number> (3.0) Convert instruction to machine-readable format
     * <Processing Name> convertDevType
     * <Function>
     *
     * @param string, string, string, string
     * @return string
     */
    public function convertInstruction($devType, $command, $value, $extVal)
    {
        $getVal = Code::where('DEVICE_TYPE_VALUE', $devType)
                             ->where('STATUS_VALUE', $command)
                             ->where('COMMAND_CODE', $value)
                             ->first()->COMMAND_VALUE;

        switch ($devType) {
            //Special conversion of command for Door Lock
            case 'door_lock':
                $value = $getVal . $extVal;
                break;
            //Special conversion of command for IR Remote
            case 'ir_remote':
                $newValue = str_pad($extVal,3,"0", STR_PAD_LEFT);
                $value = $getVal . $newValue;
                break;
            // Device/Sensor command
            default:
                $value = $getVal;
                break;
        }
        return $value;
    }

    /**
     * <Layer Number> (4.0) Convert device data to human-readable format
     * <Processing Name> convertDevData
     * <Function> Convert to readable data that will be inputted to DB
     * $mode = newDevice or Normal
     * @param string, string
     * @return string
     */
    public function convertDeviceData($devType, $data,$mode)
    {
        $retArr = array();
        $tempArr = str_split($data, 2);

        switch ($devType) {
            case 'light':
                /**
                 *  Format is as follows:
                 *  08rrggbbxxmm
                 *  rr means red parameters, 00~FF;
                 *  gg means green parameters, 00~FF;
                 *  bb means blue parameters, 00~FF;
                 *  xx means brightness parameters, 00~FF (RGB maximum);
                 *  mm indicates mode, 01 normal mode, 02 colorful mode.
                 */

                $retArr['gradation'] = (int)($tempArr[1] . $tempArr[2] . $tempArr[3]);
                $retArr['dimming'] = hexdec($tempArr[4]);
                $retArr['mode'] = (int)$tempArr[5];
                break;
            case 'temp_light':
                /**
                 *  Format is as follows:
                 *  03ccccxx
                 *  cccc indicates the color temperature parameter;
                 *  xx indicates the luminance parameter, 0x00~0xFF
                 */

                $retArr['temp_color'] = hexdec($tempArr[1] . $tempArr[2]);
                $retArr['brightness'] = hexdec($tempArr[3]);
                if( $retArr['brightness']!=0){
                    $retArr['status'] = 1;
                }
                else{
                    $retArr['status'] = 0;
                }
                break;
            case 'curtain_1':
                if($mode == 'responseDeviceData')
                    $retArr['status'] = hexdec($tempArr[1]);
                else
                    $retArr['status'] = hexdec($tempArr[0]);
                break;
            case 'temp_hum':
                list($temp, $hum) = explode(",", $data);
                $retArr['temp'] = (float)$temp;
                $retArr['hum'] = (float)$hum;
                break;
            case 'ir_remote':
                if($data == "00"){
                    $retArr = [
                        ['type' => 'AC', 'brand' => 'Panasonic', 'status' => '0', 'temp_value' => '25', 'aircon_power' => true],
                        ['type' => 'AC', 'brand' => 'Midea', 'status' => '0', 'temp_value' => '25', 'aircon_power' => true],
                        ['type' => 'TV', 'brand' => 'Panasonic', 'status' => '0', 'tv_power' => true],
                        ['type' => 'TV', 'brand' => 'Midea', 'status' => '0', 'tv_power' => true],
                    ];
                }else{
                    $storeVal = hexdec($tempArr[1] . $tempArr[2]);

                    $learnedName = IrLearning::where('LEARNING_VALUE', $storeVal)
                                         ->first()->LEARNING_NAME;
                    $brand = IrLearning::where('LEARNING_VALUE', $storeVal)
                                         ->first()->TARGET_BRAND;
                    $appliance = IrLearning::where('LEARNING_VALUE', $storeVal)
                                         ->first()->TARGET_DEVICE;

                    if($learnedName == 'POWER_OFF'){
                        if($appliance == 'Aircon'){
                            $appliance = 'AC';
                            $retArr = [
                                ['type' => $appliance, 'brand' => $brand, 'status' => '0']
                            ];
                        }
                    }else if($learnedName == 'POWER_ON'){
                        if($appliance == 'Aircon'){
                            $appliance = 'AC';
                            $retArr = [
                                ['type' => $appliance, 'brand' => $brand, 'status' => '1']
                            ];
                        }

                    }
                    else if($learnedName == 'POWER'){
                            $retArr = [
                                ['type' => $appliance, 'brand' => $brand, 'status' => null]
                            ];

                    }
                    else{
                        if($appliance == 'Aircon'){
                            $appliance = 'AC';
                            $irTempName = str_ireplace("TEMP_","",$learnedName);
                            $retArr = [
                                [
                                 'type' => $appliance,
                                 'brand' => $brand,
                                 'status' => null,
                                 'temp_value' => $irTempName
                                ]
                            ];
                        }

                    }

                }
                break;
            case 'gas_valve':
                if ($data == "02") {
                    $retArr['status'] = 1;
                }
                else if ($data == "03") {
                    $retArr['status'] = 0;
                }
                break;
            case 'wall_switch_1':
                $retArr = [
                    [ "status" => $data[0] ]
                ];
                break;
            case 'wall_switch_2':
                $retArr = [
                    [ "status" => $data[0] ],
                    [ "status" => $data[1] ]
                ];
                break;
            case 'wall_switch_3':
                $retArr = [
                    [ "status" => $data[0] ],
                    [ "status" => $data[1] ],
                    [ "status" => $data[2] ]
                ];
                break;
            case 'embedded_switch_1':
                if($data == "4174"){
                    $retArr = [
                        [ "status" => (int)$tempArr[0] ]
                    ];
                }else{
                    $retArr = [
                        [ "status" => (int)$tempArr[1] ]
                    ];
                }
                break;
            case 'embedded_switch_2':
                if($data == "4174"){
                    $retArr = [
                        [ "status" => (int)$tempArr[0] ],
                        [ "status" => (int)$tempArr[1] ]
                    ];
                }else{
                    $retArr = [
                        [ "status" => (int)$tempArr[1] ],
                        [ "status" => (int)$tempArr[2] ]
                    ];
                }
                break;
            case 'embedded_switch_3':
                if($data == "4174"){
                    $retArr = [
                        [ "status" => (int)$tempArr[0] ],
                        [ "status" => (int)$tempArr[1] ],
                        [ "status" => (int)$tempArr[0] ]
                    ];
                }else{
                    $retArr = [
                        [ "status" => (int)$tempArr[1] ],
                        [ "status" => (int)$tempArr[2] ],
                        [ "status" => (int)$tempArr[3] ]
                    ];
                }
                break;
            case 'motion_detector':
            case 'h2o_detector':
            case 'gas_detector':
            case 'smoke_detector':
            case 'panic_button':
            case 'co2_detector':
            case 'panic_button':
            case 'water_valve':
            case 'light_detector':
                $retArr['status'] = hexdec($data);
                break;
            case 'dust_detector':
                $retArr['status'] = hexdec($data);
                break;

            case 'multi_detector':
                if ($tempArr[0] == "06") {
                    $retArr["status_motion"] = $tempArr[3];
                }
                else if ($tempArr[0] == "0A") {
                    // If return data from MC also includes humidity and light
                    if ($tempArr[6]) {

                        $temp = hexdec($tempArr[4].$tempArr[5]);        //Get Temperature
                        $hum = hexdec($tempArr[10].$tempArr[11]);       //Get Humidity
                        $light = hexdec($tempArr[16].$tempArr[17]);     //Get Humidity

                        //Temperature
                        switch ($tempArr[3]) {
                            case 1:
                            	// Positive number w/ 0 decimal
                                $retArr["temp"] = $temp;
                                break;
                            case 2:
                            	// Positive number w/ 1 decimal
                                $retArr["temp"] = $temp / 10;
                                break;
                            case 3:
                               	// Positive number w/ 2 decimals
                                $retArr["temp"] = $temp / 100;
                                break;
                            case 4:
                            	// Negative number w/ 0 decimal
                                $retArr["temp"] = $temp / -1;
                                break;
                            case 5:
                            	// Negative number w/ 1 decimal
                                $retArr["temp"] = $temp / -10;
                                break;
                            case 6:
                            	// Negative number w/ 2 decimals
                                $retArr["temp"] = $temp / -100;
                                break;
                            default:
                                # code...
                                break;
                        }

                        //Humidity
                        switch ($tempArr[9]){
                            case 1:
                            	// Positive number w/ 0 decimal
                                $retArr["hum"] = $hum;
                                break;
                            case 2:
                            	// Positive number w/ 1 decimal
                                $retArr["hum"] = $hum / 10;
                                break;
                            case 3:
                            	// Positive number w/ 2 decimals
                                $retArr["hum"] = $hum / 100;
                                break;
                            default:
                                # code...
                                break;
                        }

                        //Save Light Intensity LUX
                        $retArr["status_light"] = $light;
                    }
                    else {
                        switch ($tempArr[1]) {
                            case 'D1':
                                $val = hexdec($tempArr[3]);
                                $temp = hexdec($tempArr[4].$tempArr[5]);
                                switch ($val) {
                                    case 1:
                                    	// Positive number w/ 0 decimal
                                        $retArr["temp"] = $temp;
                                        break;
                                    case 2:
                                    	// Positive number w/ 1 decimal
                                        $retArr["temp"] = $temp / 10;
                                        break;
                                    case 3:
                                    	// Positive number w/ 2 decimals
                                        $retArr["temp"] = $temp / 100;
                                        break;
                                    case 4:
                                    	// Negative number w/ 0 decimal
                                        $retArr["temp"] = $temp / -1;
                                        break;
                                    case 5:
                                    	// Negative number w/ 1 decimal
                                        $retArr["temp"] = $temp / -10;
                                        break;
                                    case 6:
                                    	// Negative number w/ 2 decimals
                                        $retArr["temp"] = $temp / -100;
                                        break;
                                    default:
                                        # code...
                                        break;
                                }
                                break;
                            case 'D2':
                                $val = hexdec($tempArr[3]);
                                $hum = hexdec($tempArr[4].$tempArr[5]);
                                switch ($val) {
                                    case 1:
                                    	// Positive number w/ 0 decimal
                                        $retArr["hum"] = $hum;
                                        break;
                                    case 2:
                                    	 // Positive number w/ 1 decimal
                                        $retArr["hum"] = $hum / 10;
                                        break;
                                    case 3:
                                    	// Positive number w/ 2 decimals
                                        $retArr["hum"] = $hum / 100;
                                        break;
                                    default:
                                        # code...
                                        break;
                                }
                            case 'D3':
                                $retArr["status_light"] = hexdec($tempArr[4].$tempArr[5]);
                                break;
                            default:
                                # code...
                                break;
                        }
                    }
                }
                break;
            case 'door_lock':
                if ($data == '022C') {
                    $retArr['status_exit'] = 1;
                    $retArr['status_lock'] = 1;
                }
                else if ($data == '0102') {
                    $retArr['status_exit'] = 0;
                    $retArr['status_lock'] = 0;
                }
                else if ($data == '0A10') {
                    $retArr['status_exit'] = 0;
                    $retArr['status_lock'] = 'unlock password error/wrong password';
                }
                else if ($data == '021D') {
                    $retArr['status_exit'] = 0;
                    $retArr['status_lock'] = 'door to wall dmg';
                }
                else if ($data == '021F') {
                    $retArr['status_exit'] = 0;
                    $retArr['status_lock'] = 'system lock/wrong pass x3';
                }
                else if ($data == '021E') {
                    $retArr['status_exit'] = 0;
                    $retArr['status_lock'] = 'system lock released/system returned to normal';
                }
                else if (strlen($data) == '12'){
                    $retArr['status_exit'] = 0;
                    $retArr['status_lock'] = '0808';
                    $retArr['user_Attribute'] = '00';
                    $retArr['user'] = '01';
                    $retArr['access_mode'] = '03';
                    $retArr['cName'] = '01';
                }
                break;
            default:
                break;
        }

        return $retArr;
    }

    /**
     * <Layer Number> (5.0) Generate a unique 8-character alphanumeric password
     * <Processing Name> generatePassword
     * <Function>
     *
     * @param int
     * @return string
     */
    public function generatePassword($length)
    {
        $password = str_random($length);

        return $password;
    }

    /**
     * <Layer Number> (6.0) Encrypts a message
     * <Processing Name> encryptMessage
     * <Function> Encrypt a given message using Laravel's default encrypter
     *            Algorithm used is AES-CBC-256 with the APP_KEY used as
     *			  encryption key
     *
     * @param string
     * @return string
     */
    public function encryptMessage($message)
    {
        // Do not use encrypt() because it will serialize $message
        $encrypted = Crypt::encryptString($message);
        $data = json_decode(base64_decode($encrypted), true);

        $iv = $data['iv'];
        $value = $data['value'];
        $length = strlen(base64_decode($value));

        $encryptedMessage ='{"message":"'. $value .'","iv_key":"'.$iv.'","length":"'.$length.'"}'."\n";

        return base64_encode($encryptedMessage);
    }

    /**
     * <Layer Number> (7.0) Create response to GET request
     * <Processing Name> createGetResponse
     * <Function> Read and Process all the parameter from the URL
     *
     * @param string, object, int
     * @return object
     */
    public function createGetResponse($request, $model, $id = null)
    {
        // Check include parameters
        if ($request->include) {
            $data = explode('>', $request->include);

            try {
                $model->with($data);
            } catch (\Exception $e) {
                abort(400, "Invalid include parameters");
            }
        }

        // Check search parameters
        if($request->advSearch == 'true'){
            if($request->floorID){
                $model = $model->where(function($query) use ($request) {
                	//List of relations that will be used for searching
                	//(e.g. searching FLOOR_NAME from a "belongs to floor"
                	//relation)
                    $relations = explode('>', $request->include);
                    $query = $query->orWhereHas($relations[0], function($query2)
                    use ($request){
                        if($request->floorID && $request->roomID){
                            $query2->where('FLOOR_ID',$request->floorID)
                            		->where('ROOM_ID',$request->roomID);
                            $query2->where(function ($query3) use ($request){
                                if ($request->search) {
                                    // Search columns within the table
                                    $dev = new Device();
                                    $columns = $dev->getSearchableColumns();
                                    foreach ($columns as $column) {
                                        $query3 = $query3
                                        ->orWhere($column, 'like', '%' . $request->search . '%');
                                    }
                                }
                            });
                        }else if($request->floorID){
                            $query2->where('FLOOR_ID',$request->floorID);
                        }
                    });
                });
            }
        }else{
            if ($request->search) {
                try {
                    $model = $model->where(function($query) use ($request) {
                    	// List of searchable columns for a specific model
                    	//(i.e. primary/foreign keys will not be included
                    	//in the search)
                        $columns = $query->getModel()->getSearchableColumns();
                        // List of relations that will be used for searching
                        //(e.g. searching FLOOR_NAME from a "belongs to floor"
                        //relation)
                        $relations = explode('>', $request->include);

                        // Search columns within the table
                        foreach ($columns as $column) {
                            $query = $query->orWhere($column, 'like', '%' .
                            						 $request->search . '%');
                        }

                        // Search for relations
                        foreach ($relations as $relation) {
                            $query = $query->orWhereHas($relation, function($q) use ($request) {
                                $columns = $q->getModel()->getSearchableColumns();

                                // Search columns from another table (i.e. from
                                //the relation)
                                foreach ($columns as $column) {
                                    $q->where($column, 'like', '%' . $request->search . '%');
                                }
                            });
                        }
                    });
                } catch (\Exception $e) {
                    abort(400, "Invalid search parameters");
                }
            }
        }

        // Check sorting parameters for Table
        if ($request->sortBy && $request->sortVal) {
        	// List of searchable columns for a specific model (i.e.
        	//primary/foreign keys will not be included in the search)
            $columns = $model->getModel()->getSearchableColumns();
            // List of relations that will be used for searching (e.g. searching
            //FLOOR_NAME from a "belongs to floor" relation)
            $relations = explode('>', $request->include);

            if (in_array($request->sortBy, $columns)) {
                $model = $model->orderBy($request->sortBy, $request->sortVal);
            }
            else {
                // Search sort parameter through each relation
                foreach ($relations as $relation) {
                    if(!$relation){
                        $columns = ($model->getModel()->first())->$relation
                        			->getSearchableColumns();
                    }

                    if (in_array($request->sortBy, $columns)) {
                        $model = $model->orderByJoin($relation . '.' .
                        				$request->sortBy, $request->sortVal);
                    }
                }
            }
        }

        //Input Limit af the data needed
        if($request->LIMIT){
            try{
                $model = $model->take($request->LIMIT);
            }catch(\Exception $e){
                abort(400,"Invalid Limit Parameter");
            }
        }

        // Check REG_FLAG
        if ($request->REG_FLAG) {
            try {
                $model = $model->where('REG_FLAG',$request->REG_FLAG);
            } catch (\Exception $e) {
                abort(400, "Invalid FLAG parameters");
            }
        }

        // Check the Manufacturer
        if ($request->manufacturerID) {
            try {
                $model = $model->where('MANUFACTURER_ID',$request->manufacturerID);
            } catch (\Exception $e) {
                abort(400, "Invalid MANUFACTURER parameters");
            }
        }
        //Create a simple query for the to show specific data
        if($request->filter){
            $urlData=explode("|",$request->filter);

            foreach ($urlData as $value){
                $value = explode(":" , $value);
                foreach ($value as $mValue){
                    $mValue = explode("," , $mValue);
                }
                $model = $model->whereIn($value[0],$mValue);
            }
        }

        // Check pagination parameters
        if ($request->pageLength) {
            try {
                $model = $model->paginate($request->pageLength);
            } catch (\Exception $e) {
                abort(400, "Invalid pagination parameters");
            }
        }
        else {
            // Check if single document or collection
            $model = $id ? $model->findOrFail($id) : $model = $model->get();
        }

        return $model;
    }



    /**
     * <Layer Number> (8.0) Broadcast new data to frontend
     * <Processing Name> broadcastNewData
     * <Function>Broadcast a Device Event to all iBMS Client PC
     *
     * @param object
     * @return
     */
    public function broadcastNewData($device)
    {
        $eventData = Device::with('deviceBindings')
        			->findOrFail($device->DEVICE_ID);

        event(new DeviceCommandEvent($eventData));
    }

    /**
     * <Layer Number> (9.0) Insert Device Command to Notification
     * <Processing Name> insertDevicetoNotification
     * <Function>Insert Notification Data to Notification Table
     *
     * @param object
     * @return
     */
    public function insertDevicetoNotification($device)
    {

        //Notification for Sensor
        //Check for Sensor with Status Data

        //Notification for Device Command
        $deviceType = $device->DEVICE_TYPE;
        switch($device->DEVICE_TYPE) {
            case 'smoke_detector':
            case 'motion_detector':
            case 'h2o_detector':
            case 'gas_detector':
            case 'panic_button':
                if(isset($device->DATA['status'])){
                    if($device->DATA['status']==1) {
                        Self::processNotification($device,'detected','');
                    }
                }
                break;
            case 'multi_detector':
                if(isset($device->DATA['status_motion'])){
                    if($device->DATA['status_motion']==1) {
                         Self::processNotification($device,'detected','');
                    }
                }
                break;
            case 'embedded_switch_1':
            case 'embedded_switch_2':
            case 'embedded_switch_3':
            case 'wall_switch_1':
            case 'wall_switch_2':
            case 'wall_switch_3':
                Self::processNotification($device,'command',2);
            case 'temp_light':
            case 'gas_valve':
            case 'water_valve':
            case 'door_lock':
                if(isset($device->DATA['status'])){
                    Self::processNotification($device,'command',
                    						  $device->DATA['status']);
                }

                break;
            case 'curtain_1':
                Self::processNotification($device,'command',2);
                break;
        }
    }
    /**
     * <Layer Number> (10.0) Process Notification command from
     * 						 insertDevicetoNotification
     * <Processing Name> processNotification
     * <Function>
     *
     * @param object, string, int
     * @return
     */
    public function processNotification($device,$mode,$commandStatus){

        $objectName = "";
        $subject = "";
        $content = "";
        $errorFlag = "";
        $url = request()->getSchemeAndHttpHost() . '/floor-view' . '?floor=' .
        		$device->FLOOR_ID . '&room=' . $device->ROOM_ID . '&device=' .
        		$device->DEVICE_ID;

        $newRequest = new Request();
        $room = Room::where('ROOM_ID', $device->ROOM_ID)
                    ->with('floor')
                    ->first();
        if($mode == "detected"){

            $objectName = "Sensor Detected";
            $subject = $device->DEVICE_NAME . " was triggered.";
            $content = 'in ' . $room->ROOM_NAME . ' on the ' .
            			$room->floor->FLOOR_NAME;
            $errorFlag = 4;

        }else if ($mode == "command"){

            if($commandStatus == 1){
                $subject = $device->DEVICE_NAME . " has been turned on.";
            }else if ($commandStatus == 0){
                $subject = $device->DEVICE_NAME . " has been turned off.";
            }else if ($commandStatus == 2){
                 $subject = $device->DEVICE_NAME . " has been commanded.";
            }
            $objectName = "Device Controlled";
            $content = $room->floor->FLOOR_NAME. ' / ' . $room->ROOM_NAME;

            $errorFlag = 1;
        }

        //Collect Data to send in Notification
        $newRequest->replace([
            "OBJECT_NAME" => $objectName,
            "SUBJECT" => $subject,
            "CONTENT" => $content,
            "ERROR_FLAG" => $errorFlag,
            "NOTIFICATION_LINK" => $url
        ]);

        try {
            app('App\Http\Controllers\NotificationController')
            ->insertNotification($newRequest);

            if ($mode == "detected") {
                $this->storeLogs(0,'Automatic', $subject . ' ' . $content,
                				request()->url(), $device->DEVICE_NAME);
            }
        } catch (\Exception $e) {
        }

    }

    /**
     * <Layer Number> (11.0) Decrypts a message
     * <Processing Name> decryptMessage
     * <Function> Decrypt a given message using Laravel's default encrypter
     *            Algorithm used is AES-CBC-256 with the APP_KEY used as
     *			  encryption key
     *
     * @param string
     * @return string
     */
    public function decryptMessage($message)
    {

        $decrypted = \openssl_decrypt(
            $message['content'],
            'AES-256-CBC',
            base64_decode(substr(env('APP_KEY'), 7)),
            0,
            base64_decode($message['iv'])
        );

        if ($decrypted === false) {
            return "Failed";
        }

        return $decrypted;
    }

    /**
     * <Layer Number> (12.0) Check if Valid JSON
     * <Processing Name> isJson
     * <Function> Check if json String is valid
     *
	 * @param string
     * @return boolean
	 *
     */
    public function isJson($str) {

        $json = json_decode($str);
        return $json && $str != $json;

    }

     /**
     * <Layer Number> (13.0) set new Instruction to devices
     * <Processing Name> newInstructionRequest
     * <Function> set new Instruction to devices
     *
     * @param int, int, string, string, string
     * @return
	 *
     */
    public function newInstructionRequest($gateway_id, $device_id, $command, $value, $addVal) {
        $newRequest = new Request();
        $newRequest->replace([
            'GATEWAY_ID' => $gateway_id,
            'DEVICE_ID' => $device_id,
            'COMMAND' => $command,
            'VALUE' => $value,
            'TYPE' => "Automatic",
            'addlValue' => $addVal
        ]);
        try {
            app('App\Http\Controllers\InstructionController')->sendInstruction($newRequest);
        } catch (\Exception $e) {
           return "";
        }
    }
     /**
     * <Layer Number> (14.0) Delete Device Plotting from Floor Table Data
     * <Processing Name> deletePlotDevice
     * <Function> Device Plotting from Floor Table
     *
     * @param object
     * @return
     *
     */
    public function deleteDevicePlot($device){
        //Get Rooms and Floors based on Device
        $query = Device::find($device['DEVICE_ID'])->room()->with('floor')->first();
        $floorMapData = $query['floor']['FLOOR_MAP_DATA'];
        $rooms = '';
        //Delete Floor Device Coordinates on Floor Table
        foreach($query['floor']['FLOOR_MAP_DATA']['roomMap'] as $key => $floorRooms){
            if($query['ROOM_MAP_DATA']['ROOM_MAP'] == $floorRooms['roomMap'])
            {
                foreach($floorRooms['deviceCoor'] as $deviceCoorKey => $deviceCoordinates){
                    if($deviceCoordinates['name'] == $device['DEVICE_MAP_NAME']){
                        unset($floorMapData['roomMap'][$key]['deviceCoor'][$deviceCoorKey]);
                        $floorMapData['roomMap'][$key]['deviceCoor'] = array_merge($floorMapData['roomMap'][$key]['deviceCoor']);
                        $floor =  Device::find($device['DEVICE_ID'])->floor()->first();
                        $floor->FLOOR_MAP_DATA = $floorMapData;
                        $floor->save();
                        break;
                    }
                }
            }

        }
    }

     /**
     * <Layer Number> (15.0) Store Device Data
     * <Processing Name> storeDeviceData
     * <Function> Save Device data
     *
     * @param int,string,object,object
     * @return
     *
     */
    public function storeDeviceData($devId,$type,$deviceData,$data){

        $retArr = array();

        if($type == 'ir_remote'){
            $device = Device::where('DEVICE_ID', $devId)
                ->get();
            $devJson = json_decode($device, true);
            foreach ($devJson as $devData) {
                foreach ($devData['DATA'] as $nData) {
                    foreach ($data as $sData) {
                        if($nData['type'] == $sData['type'] && $nData['brand'] == $sData['brand']){
                            if($sData['status'] != $nData['status']){
                                if($sData['type'] == 'AC'){
                                    if($sData['status'] == null){
                                        $nData['temp_value'] = $sData['temp_value'];
                                        $nData['status'] = 1;
                                        array_push($retArr, $nData);
                                    }else{
                                        $nData['status'] = $sData['status'];
                                        array_push($retArr, $nData);
                                    }

                                }else{
                                    array_push($retArr, $nData);
                                }
                            }else{
                                array_push($retArr, $nData);
                            }
                        }else{
                            array_push($retArr, $nData);
                        }
                    }
                }
                return $retArr;
            }

        }else{
            return array_replace_recursive($deviceData,$data);
        }
    }

    /**
     * <Layer Number> (16.0) Store System Logs
     * <Processing Name> storeLogs
     * <Function> SaveLogs data
     *
     * @param int, string, string, string, string
     * @return
     *
     */
    public function storeLogs($type,$instruction_type,$content,$ip,$host){
        $logs = new Logs;
        $logs->TYPE = $type;
        $logs->INSTRUCTION_TYPE = $instruction_type;
        $logs->CONTENT = $content;
        $logs->IP = $ip;
        $logs->HOST = $host;
        $logs->save();
    }

    /**
     * <Layer Number> (17.0) Audit Trail Logs
     * <Processing Name> auditLogs
     * <Function> Audit Trail data
     *
     * @param int, string, string, string
     * @return
     *
     */
    public function auditLogs($ip,$host,$module,$instruction){
        $auditLogs = new AuditLogs;
        $auditLogs->IP = $ip;
        $auditLogs->HOST = $host;
        $auditLogs->MODULE = $module;
        $auditLogs->INSTRUCTION = $instruction;
        $auditLogs->save();
    }

    /**
     * <Layer Number> (18.0) Set Current Time
     * <Processing Name> currentTime
     * <Function>Get Current Time
     *
     * @param
     * @return
     *
     */
    public function currentTime(){
        return strtotime(date("Y-m-d H:i:s"));
    }


}


