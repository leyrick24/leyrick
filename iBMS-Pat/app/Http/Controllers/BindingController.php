<?php
/*
* <System Name> iBMS
* <Program Name> BindingController.php
*
* <Create> 2018.07.27 TP Bryan
* <Update> 2018.08.20 TP Bryan    Fixed code structure
* <Update> 2018.09.25 TP Robert   Add code in checkBinding
* <Update> 2018.09.26 TP Robert   Add code in checkNextActivity
* <Update> 2018.09.27 TP Robert   Fixed code in checkBinding
* <Update> 2018.10.23 TP Robert   Fixed code in checkBinding - Sensor to sensor
* <Update> 2018.10.24 TP Robert   Fixed code in checkBinding - sensor to sensor range
* <Update> 2018.10.25 TP Robert   Fixed code in checkBinding - sensor to sensor range
* <Update> 2018.10.26 TP Robert   Fixed code in checkBinding - sensor to sensor / add comment
* <Update> 2018.11.21 TP Robert   Update the disable binding
* <Update> 2018.11.22 TP Robert   Update the disable and check next binding
*
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Binding;
use App\BindingList;
use App\Device;
use App\Room;
use App\Floor;
use App\Traits\CommonFunctions;
use App\ProcessedData;
use App\Events\testBinding;
use App\Events\DeviceCommandEvent;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;

/**
 * <Class Name> BindingController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class BindingController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // getBindingAll                   (1.0) Retrieve all bindings from database
    // getBinding                      (2.0) Retrieve binding from database
    // newBinding                      (3.0) Create new binding entry to database
    // deleteBinding                   (4.0) Delete Specific Device binding from database
    // deleteSensorBinding             (5.0) Delete binding from database
    // enableBinding                   (6.0) Enable binding from database
    // disableBinding                  (4.0) Disable binding from database
    // checkBindings                   (8.0) Execute instructions according to registered binding
    // checkNextActivity               (9.0) Check Next Interval of Binded and Execute instructions according to registered binding
    // setTimeInterval                 (10.0) Set time interval for the devices
    // enableAllBinding                (11.0) Enable all binding from database
    // disableAllBinding               (12.0) Disable all binding from database

    use AuthenticatesUsers;
    use CommonFunctions;

    /**
     * <Layer number> (1.0) Retrieve all bindings from database
     * <Processing name> getBindingAll
     * <Function> Retrieve all the bindings
     *            URL: http://localhost/bindings/
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getBindingAll(Request $request)
    {
        $bindings = $this->createGetResponse($request,
            (new Binding)->newQuery());

        return $bindings->groupBy('sourceDevice.DEVICE_ID');
    }

    /**
     * <Layer number> (2.0) Retrieve binding from database
     * <Processing name> getBinding
     * <Function>
     *            URL: http://localhost/bindings/:id
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getBinding(Request $request, $id)
    {
        $binding = Binding::findOrFail($id);

        return $binding;
    }

    /**
     * <Layer number> (3.0) Create new binding entry to database
     * <Processing name> newBinding
     * <Function>
     *            URL: http://localhost/bindings/new
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              SOURCE_DEVICE_ID => str
     *              TARGET_DEVICE_ID => str
     *              BINDING_LIST_ID => str
     * @return Illuminate\Http\Response
     */
    public function newBinding(Request $request)
    {
        // For audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'binding management';
        $instruction = 'created a new binding';
        $this->auditLogs($ip,$host,$module,$instruction);

        $response = array();
        $bindingDevices = json_decode(json_encode($request->TARGET_DEVICES),
            true);

        foreach($bindingDevices as $bindingDevice) {
            $existBinding = Binding::where('SOURCE_DEVICE_ID',
                                           $bindingDevice['SOURCE_DEVICE_ID'])
                                   ->where('TARGET_DEVICE_ID',
                                           $bindingDevice['TARGET_DEVICE_ID'])
                                   ->where('BINDING_LIST_ID',
                                           $bindingDevice['BINDING_LIST_ID'])
                                   ->count();
            if ($existBinding == 0){
                $time = $bindingDevice['TIME'] * 60;
                $bindingList = new Binding;
                $bindingList->SOURCE_DEVICE_ID =
                                    $bindingDevice['SOURCE_DEVICE_ID'];
                $bindingList->TARGET_DEVICE_ID =
                                    $bindingDevice['TARGET_DEVICE_ID'];
                $bindingList->BINDING_LIST_ID =
                                    $bindingDevice['BINDING_LIST_ID'];
                $bindingList->TIME_INTERVAL = $time;
                $bindingList->save();
                $bindingList["EXIST_STATUS"] = array("EXIST" => "NO",
                    "TARGET_DEVICE_ID" => $bindingDevice['TARGET_DEVICE_ID']);
                array_push($response,$bindingList);
                event(new testBinding($bindingList));
            } else{
                $bindingList["EXIST_STATUS"] = array("EXIST" => "YES",
                    "TARGET_DEVICE_ID" => $bindingDevice['TARGET_DEVICE_ID']);
                array_push($response,$bindingList);
            }
        }

        return $response;
    }

    /**
    * <Layer number> (4.0) Delete Specific Device binding from database
    * <Processing name> deleteBinding
    * <Function>
    *            URL: http://localhost/bindings/delete
    *            METHOD: POST
    *
    * @param Illuminate\Http\Request $request
    *        FORM-DATA:
    * @return Illuminate\Http\Response
    */
    public function deleteBinding(Request $request)
    {
        // //for audit logs
        $ip = $request->ip();
        $host = $request->username;
        $module = 'binding management';
        $instruction = 'deleted a binding data';
        $this->auditLogs($ip,$host,$module,$instruction);

        $binding = Binding::findOrFail($request->BINDING_ID);
        $binding->delete();

        // return $binding;
        return 'success';
    }
    /**
    * <Layer number> (5.0) Delete binding from database
    * <Processing name> deleteSensorBinding
    * <Function>
    *            URL: http://localhost/bindings/delete
    *            METHOD: POST
    *
    * @param Illuminate\Http\Request $request
    *        FORM-DATA:
    * @return Illuminate\Http\Response
    */
    public function deleteSensorBinding(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'binding management';
        $instruction = 'deleted a sensor binding';
        $this->auditLogs($ip,$host,$module,$instruction);

        $bindings = Binding::where('SOURCE_DEVICE_ID',$request->DEVICE_ID)
                        ->get();
        foreach ($bindings as $binding) {
            $binding->delete();
        }

        return 'success';
    }
    /**
    * <Layer number> (6.0) enable binding from database
    * <Processing name> enableBinding
    * <Function>
    *            URL: http://localhost/bindings/delete
    *            METHOD: POST
    *
    * @param Illuminate\Http\Request $request
    *        FORM-DATA:
    * @return Illuminate\Http\Response
    */
    public function enableBinding(Request $request)
    {
        // For audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'binding management';
        $instruction = 'enable binding';
        $this->auditLogs($ip,$host,$module,$instruction);

        $time = strtotime(date("Y-m-d H:i:s"));
        // Combine the time now and interval for new date
        $timeNow = date("Y-m-d H:i:s", $time);


        try
        {
            $binding = Binding::findOrFail($request->BINDING_ID);
            $binding->BINDING_STATUS = 1;
            $binding->MANUAL = 0;
            $binding->NEXT_ACTIVITY = $timeNow;
            $binding->save();
        }
        catch(Exception $e)
        {
            return $e->getMessage();
        }
        return 'success';
    }
    /**
    * <Layer number> (7.0) disable binding from database
    * <Processing name> disableBinding
    * <Function>
    *            URL: http://localhost/bindings/delete
    *            METHOD: POST
    *
    * @param Illuminate\Http\Request $request
    *        FORM-DATA:
    * @return Illuminate\Http\Response
    */
    public function disableBinding(Request $request)
    {
        // For audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'binding management';
        $instruction = 'disable binding';
        $this->auditLogs($ip,$host,$module,$instruction);

        if($request->TYPE == 1){
            $data = $request->BINDING_ID;
            //Get time now
            $timeNow = strtotime(date("Y-m-d H:i:s"));
            // Time interval when the binding need to be back for future need the System Settings
            $interVal = 120;
            // Combine the time now and interval for new date
            $timeInterVal = date("Y-m-d H:i:s", $timeNow + $interVal);
            try{
                foreach ($data as $id) {
                    $binding = Binding::findOrFail($id['BINDING_ID']);
                    $binding->BINDING_STATUS = 0;
                    $binding->MANUAL = 1;
                    $binding->NEXT_ACTIVITY = $timeInterVal;
                    $binding->save();
                }
            }catch(Exception $e){
                return $e->getMessage();
            }
        }else{
            try{
                $binding = Binding::findOrFail($request->BINDING_ID);
                $binding->BINDING_STATUS = 0;
                $binding->save();
            }catch(Exception $e){
                return $e->getMessage();
            }
        }

        return 'success';

    }

    /**
     * <Layer Number> (8.0) Execute instructions according to registered binding
     * <Processing Name> checkBindings
     * <Function> Check all the bindings
     *
     * @param string
     * @return
     */
    public function checkBindings($processedData,$devices)
    {
        // Get all the bindings we have
        $bindings = Binding::with('bindingList')
                           ->with('learningCommand')
                           ->where('SOURCE_DEVICE_ID',
                                    $processedData->DEVICE_ID)
                           ->get();
        // Loop all the bindings we readline_callback_handler_remove()
        $bindingsPriority = Binding::with('bindingList')
                           ->get();

        foreach($bindings as $binding) {
            // ProcessedData Value
            $str = $processedData->DATA;
            // Key of the Source device command e.g(status/status_lock/temp)
            $key = $binding->bindingList->SOURCE_DEVICE_COMMAND;
            // Value of the source device e.g(1/0/range)
            $val = $binding->bindingList->SOURCE_DEVICE_VALUE;
            // Source device type
            $type = $binding->bindingList->SOURCE_DEVICE_TYPE;


            // Check if the binding is Sensor to sensor or Sensor to Device
            // If Target Category is equal to "1" it's Sensor to Sensor
            if($binding->bindingList->TARGET_DEVICE_CATEGORY == "1"){
                // Check if the value of the source device is range or not
                $value = json_decode($val,true);
                if(is_array($value)){
                    // Check if the processedData value is range
                    $value = json_decode($val,true);

                    if(in_array($str[$key], range($value['value1'],
                                                    $value['value2']))){
                        continue;
                    }

                }else{
                    if($val != $str[$key]){
                        continue;
                    }
                }
                // Value of the parent sensor
                $targetSensor = $binding->bindingList->TARGET_DEVICE_VALUE;
                // Get binding of the Child Sensor if Target Sensor is binded
                $childBindings = Binding::with('bindingList')
                                        ->where('SOURCE_DEVICE_ID',
                                                    $binding->TARGET_DEVICE_ID)
                                        ->get();
                // Loop the child sensors
                $json = ['childBindings'=>$childBindings];
                foreach($childBindings as $childBinding) {
                    // Child Sensor Value
                    $childDeviceValue =
                            $childBinding->bindingList->SOURCE_DEVICE_VALUE;
                    // Child Target Device status
                    $targetCommand =
                            $childBinding->bindingList->TARGET_DEVICE_COMMAND;
                    // Child Target device value
                    $targetValue =
                            $childBinding->bindingList->TARGET_DEVICE_VALUE;

                        $json = ['targetSensor'=>$targetSensor];
                        $json = ['childDeviceValue'=>$childDeviceValue];

                    // Chekc if the sensor to sensor is enabled
                    if($binding->BINDING_STATUS == 1){

                        $json = ['targetSensor'=>$targetSensor];
                        $json = ['childDeviceValue'=>$childDeviceValue];
                        // Check if the Parent Sensor is equal to child sensor
                        if($targetSensor == $childDeviceValue){
                            // Set timenow
                            $timeNow = strtotime(date("Y-m-d H:i:s"));
                            // Get time interval of the binded device/sensor
                            $timeInterVal = $childBinding->TIME_INTERVAL;
                            // Check if the Binding Status of is Enable of Disable e.g(1/0)
                            if($childBinding->BINDING_STATUS == 1){
                                // Identify if the parent Sensor is equal to 0 and then instruct the device
                                if($targetSensor == 0){
                                    $learningVal = '';
                                    $device = Device::where('DEVICE_ID',
                                            $childBinding->TARGET_DEVICE_ID)
                                            ->first();
                                    $this->newInstructionRequest($device
                                            ->gateway->GATEWAY_ID,
                                            $device->DEVICE_ID,$targetCommand,
                                            $targetValue,$learningVal);
                                    $this->setTimeInterval($childBinding
                                            ->BINDING_ID,$timeNow,0);
                                }else{
                                    $learningVal = '';
                                    $device = Device::where('DEVICE_ID',
                                            $childBinding->TARGET_DEVICE_ID)
                                            ->first();
                                    $this->newInstructionRequest($device
                                            ->gateway->GATEWAY_ID,
                                            $device->DEVICE_ID,$targetCommand,
                                            $targetValue,$learningVal);
                                }
                            }
                        }
                    }
                }
            }else{
                // Check if the binding is enable or disable
                if($binding->BINDING_STATUS == 1){
                    // Identify if the type of sensor is range
                    if($type == 'dust_detector'){
                        // Timenow
                        $timeNow = strtotime(date("Y-m-d H:i:s"));
                        $timeNext = strtotime($binding->NEXT_ACTIVITY);
                        // Get time interval
                        $timeInterVal = $binding->TIME_INTERVAL;
                        // Check if the processedData value is range
                        $value = json_decode($val,true);
                        if(in_array($str[$key],
                            range($value['value1'],$value['value2']))){
                            $learningVal = '';
                            // Target device command
                            $command =
                                $binding->bindingList->TARGET_DEVICE_COMMAND;
                            // Target device value
                            $value =
                                $binding->bindingList->TARGET_DEVICE_VALUE;
                            // Get the device id of the target device
                            $device = Device::where('DEVICE_ID',
                                            $binding->TARGET_DEVICE_ID)
                                            ->first();
                            // Instruct the device
                            $this->newInstructionRequest($device->gateway
                                            ->GATEWAY_ID,$device->DEVICE_ID,
                                            $command,$value,$learningVal);
                            // Set the interval in binding
                            $this->setTimeInterval($binding->BINDING_ID,
                                            $timeNow,$timeInterVal);
                        }
                    }else if($type == 'light_detector'){

                        //---Priority Sensor (Dont Trigger if target device is binded to other sensor that has a high priority)---
                        $targetDevice = $binding->TARGET_DEVICE_ID;
                        $counter = 0;
                        foreach ($bindingsPriority as $bind) {
                            $timeNow = strtotime(date("Y-m-d H:i:s"));
                            $timeNext = strtotime($bind->NEXT_ACTIVITY);

                            if($binding->SOURCE_DEVICE_ID !=
                                $bind->SOURCE_DEVICE_ID &&
                                $bind->bindingList->SOURCE_DEVICE_TYPE ==
                                'motion_detector' && $targetDevice ==
                                $bind->TARGET_DEVICE_ID) {

                                if( $timeNow <= $timeNext){
                                    $counter++;

                                }
                            }
                        }
                        if($counter > 0){
                            continue;
                        }
                        //timenow
                        $timeNow = strtotime(date("Y-m-d H:i:s"));
                        $timeNext = strtotime($binding->NEXT_ACTIVITY);
                        //get time interval
                        $timeInterVal = $binding->TIME_INTERVAL;

                        $value = json_decode($val,true);
                        if(in_array($str[$key], range($value['value1'],
                            $value['value2']))){
                            $learningVal = '';
                            // Target device command
                            $command =
                                $binding->bindingList->TARGET_DEVICE_COMMAND;
                            // Target device value
                            $value =
                                $binding->bindingList->TARGET_DEVICE_VALUE;
                            // Get the device id of the target device
                            $device = Device::where('DEVICE_ID',
                                            $binding->TARGET_DEVICE_ID)
                                            ->first();
                            // Instruct the device
                            $this->newInstructionRequest($device->gateway
                                            ->GATEWAY_ID,$device->DEVICE_ID,
                                            $command,$value,$learningVal);
                            // Set the interval in binding
                            $this->setTimeInterval($binding->BINDING_ID,
                                            $timeNow,$timeInterVal);
                        }
                    }else if($type == 'temp_hum'){
                        //check if the processedData value is range
                        $value = json_decode($val,true);
                        $nVal1 = round($value['value1']);
                        $nVal2 = round($value['value2']);
                        $pData = round($str[$key],0,PHP_ROUND_HALF_DOWN);
                        //timenow
                        $timeNow = strtotime(date("Y-m-d H:i:s"));
                        $timeNext = strtotime($binding->NEXT_ACTIVITY);
                        //get time interval
                        $timeInterVal = $binding->TIME_INTERVAL;
                        if(in_array($pData, range($nVal1,$nVal2))){
                            $learns = $binding->learningCommand;
                            $targetType =
                                    $binding->bindingList->TARGET_DEVICE_TYPE;
                            if($targetType == 'ir_remote'){
                                foreach ($learns as $learn) {
                                    $brand = $learn->TARGET_BRAND;
                                    $learningName = $learn->LEARNING_NAME;
                                    $learningVal = $learn->LEARNING_VALUE;
                                    $targetCondition = $binding->bindingList
                                                    ->TARGET_DEVICE_CONDITION;
                                    //check binding list condition to IR learning name, brand is still fixed
                                    if($brand == 'Panasonic' && $learningName ==
                                        $targetCondition){
                                        if($timeNow >= $timeNext){
                                            $command = $binding->bindingList
                                                ->TARGET_DEVICE_COMMAND; // target device command
                                            $value = $binding->bindingList
                                                ->TARGET_DEVICE_VALUE; // target device value
                                            $device = Device::where('DEVICE_ID',
                                                $binding->TARGET_DEVICE_ID)
                                                ->first();//get the device id of the target device
                                            $this->newInstructionRequest($device
                                                ->gateway->GATEWAY_ID,
                                                $device->DEVICE_ID,$command,
                                                $value,$learningVal);//instruct the device
                                            $this->setTimeInterval($binding
                                                ->BINDING_ID,$timeNow,
                                                $timeInterVal); //set the interval in binding
                                        }
                                    }
                                }
                            }else{
                                $learningVal = '';
                                if($timeNow >= $timeNext){
                                    $command = $binding->bindingList
                                                ->TARGET_DEVICE_COMMAND; // target device command
                                    $value = $binding->bindingList
                                                ->TARGET_DEVICE_VALUE; // target device value
                                    $device = Device::where('DEVICE_ID',
                                                $binding->TARGET_DEVICE_ID)
                                                ->first();//get the device id of the target device
                                    $this->newInstructionRequest($device
                                                ->gateway->GATEWAY_ID,
                                                $device->DEVICE_ID,$command,
                                                $value,$learningVal);//instruct the device
                                }
                            }
                        }

                    }else if($type == 'panic_button'){
                        $learningVal = '';
                        // target device command
                        $command = $binding->bindingList->TARGET_DEVICE_COMMAND;
                        // target device value
                        $value = $binding->bindingList->TARGET_DEVICE_VALUE;
                        //get the device id of the target device
                        $device = Device::where('DEVICE_ID',
                                    $binding->TARGET_DEVICE_ID)->first();
                        //instruct the device
                        $this->newInstructionRequest($device->gateway
                                    ->GATEWAY_ID,$device->DEVICE_ID,$command,
                                    $value,$learningVal);
                    }else{
                        //Will go here for default Flow of Sensor
                        // Check if the value is equal to the processdata value
                        if($val == $str[$key]){
                            // Timenow
                            $timeNow = strtotime(date("Y-m-d H:i:s"));
                            //Get time interval
                            $timeInterVal = $binding->TIME_INTERVAL;
                            //Time next is the next activity of the binding
                            $timeNext = strtotime($binding->NEXT_ACTIVITY);
                            // Check if the value is equal to 1
                            if($val == 1){
                                $targetType = $binding->bindingList->TARGET_DEVICE_TYPE;
                                if($targetType == 'ir_remote'){
                                    $learns = $binding->learningCommand;
                                    foreach ($learns as $learn) {
                                        $brand = $learn->TARGET_BRAND;
                                        $learningName = $learn->LEARNING_NAME;
                                        $learningVal = $learn->LEARNING_VALUE;
                                        $targetCondition = $binding->bindingList
                                                            ->TARGET_DEVICE_CONDITION;
                                        //check binding list condition to IR learning name, brand is still fixed
                                        if($brand == 'Panasonic' && $learningName ==
                                            $targetCondition){
                                            if($timeNow >= $timeNext){
                                                // target device command
                                                $command = $binding
                                                    ->bindingList
                                                    ->TARGET_DEVICE_COMMAND;
                                                // target device value
                                                $value = $binding
                                                    ->bindingList
                                                    ->TARGET_DEVICE_VALUE;
                                                $device =
                                                    Device::where('DEVICE_ID',
                                                    $binding->TARGET_DEVICE_ID)
                                                    ->first();//get the device id of the target device
                                                $this->newInstructionRequest($device->gateway
                                                    ->GATEWAY_ID,$device->DEVICE_ID,$command,
                                                    $value,$learningVal);//instruct the device
                                                $this->setTimeInterval($binding->BINDING_ID,
                                                    $timeNow,$timeInterVal); //set the interval in binding
                                            }
                                        }
                                    }
                                }else{
                                    //check if the timenow is greater than time next
                                    if($timeNow >= $timeNext){
                                        $learningVal = '';
                                        // target device command
                                        $command = $binding->bindingList->TARGET_DEVICE_COMMAND;
                                        // target device value
                                        $value = $binding->bindingList->TARGET_DEVICE_VALUE;
                                        //get the device id of the target device
                                        $device = Device::where('DEVICE_ID',
                                                $binding->TARGET_DEVICE_ID)->first();
                                        //instruct the device
                                        $this->newInstructionRequest($device->gateway->GATEWAY_ID,
                                            $device->DEVICE_ID,$command,$value,$learningVal);
                                    }
                                    // Set next time
                                    $this->setTimeInterval($binding->BINDING_ID,$timeNow,
                                        $timeInterVal); //set the interval in binding

                                }
                            }else{
                                // Set next time
                                $this->setTimeInterval($binding->BINDING_ID,$timeNow,$timeInterVal); //set the interval in binding
                            }
                        }
                    }
                }

            }
            $device = Device::where('DEVICE_ID', $binding->TARGET_DEVICE_ID)
                            ->with('room')
                            ->with('floor')
                            ->first();
            if (isset($type)) {

                if ($val == 1) {
                    $this->storeLogs(0, 'Automatic', 'Device: '.$device->DEVICE_NAME.' Room: '.
                        $device->room->ROOM_NAME.' Floor: '.$device->floor->FLOOR_NAME.' Event: '.
                        $key.' - ON', "None", $type);
                }else if($val == 0) {
                     $this->storeLogs(0, 'Automatic', 'Device: '.$device->DEVICE_NAME.' Room: '.
                        $device->room->ROOM_NAME.' Floor: '.$device->floor->FLOOR_NAME.' Event: '.
                        $key.' - OFF', "None", $type);
                }else {
                    $this->storeLogs(0, 'Automatic', 'Device: '.$device->DEVICE_NAME.' Room: '.
                        $device->room->ROOM_NAME.' Floor: '.$device->floor->FLOOR_NAME.' Event: '.
                        $key.' - '.$val, "None", $type);
                }
            }
        }
    }

    /**
     * <Layer Number> (9.0) Check Next Interval of Binded and Execute instructions according to registered binding
     * <Processing Name> checkNextActivity
     * <Function> Check the next interval execution
     *
     * @param
     * @return
     */
    public function checkNextActivity()
    {
    	// Get all off binding
        $bindings = Binding::whereHas('bindingList', function ($query){
                $query->where('SOURCE_DEVICE_VALUE',0);
        })->with('bindingList')->where('MANUAL',0)->where('BINDING_STATUS',1)->get();
        // Loop the binding with off value
        foreach ($bindings as $binding) {
        	//check if the type of sensor is range or not
            if($binding->bindingList->SOURCE_DEVICE_TYPE == 'light_detector'){

            }else if($binding->bindingList->SOURCE_DEVICE_TYPE == 'temp_hum'){

            }else if($binding->bindingList->SOURCE_DEVICE_TYPE == 'dust_detector'){

            }else if($binding->bindingList->SOURCE_DEVICE_TYPE == 'co2_detector'){

            }else{
                $timeNow = strtotime(date("Y-m-d H:i:s"));
                $timeNext = strtotime($binding->NEXT_ACTIVITY);
                $timeInterVal = $binding->TIME_INTERVAL;
                if($timeNow >= $timeNext){
                    $targetType = $binding->bindingList->TARGET_DEVICE_TYPE;
                    if($targetType == 'ir_remote'){
                        $learns = $binding->learningCommand;
                        $targetCondition = $binding->bindingList->TARGET_DEVICE_CONDITION;
                        foreach ($learns as $learn) {
                            $brand = $learn->TARGET_BRAND;
                            $learningName = $learn->LEARNING_NAME;
                            $learningVal = $learn->LEARNING_VALUE;
                            //check binding list condition to IR learning name, brand is still fixed
                            if($brand == 'Panasonic' && $learningName == $targetCondition){
                                $command = $binding->bindingList->TARGET_DEVICE_COMMAND; // target device command
                                $value = $binding->bindingList->TARGET_DEVICE_VALUE; // target device value
                                $device = Device::where('DEVICE_ID', $binding->TARGET_DEVICE_ID)
                                            ->first();
                                $this->newInstructionRequest($device->gateway->GATEWAY_ID,$device
                                    ->DEVICE_ID,$command,$value,$learningVal);
                            }
                        }
                    }else{
                        $learningVal = '';
                        $command = $binding->bindingList->TARGET_DEVICE_COMMAND;
                        $value = $binding->bindingList->TARGET_DEVICE_VALUE;
                        $device = Device::where('DEVICE_ID', $binding->TARGET_DEVICE_ID)->first();
                        $this->newInstructionRequest($device->gateway->GATEWAY_ID,
                            $device->DEVICE_ID,$command,$value,$learningVal);
                    }
                }
            }
        }

        // Update the BINDING_STATUS and MANUAL to it normal after the time interval
        $manualBindings = Binding::where('MANUAL',1)->get();
        foreach ($manualBindings as $manualBinding) {
            $timeNow = strtotime(date("Y-m-d H:i:s"));
            $timeNext = strtotime($manualBinding->NEXT_ACTIVITY);
            if($timeNow >= $timeNext){
                $this->tests($manualBinding->TARGET_DEVICE_ID);
                $updateBinding = Binding::findOrFail($manualBinding->BINDING_ID);
                $updateBinding->BINDING_STATUS = 1;
                $updateBinding->MANUAL = 0;
                $updateBinding->save();
            }
        }

    }
    /**
     * <Layer Number> (10.0) set time interval for the devices
     * <Processing Name> setTimeInterval
     * <Function>
     *
     * @param string
     * @return
     */
    private function setTimeInterval($id, $timeNow, $timeInterVal)
    {

        $binding = Binding::where('BINDING_ID',$id)->first();
        $binding->LAST_ACTIVITY = date("Y-m-d H:i:s", $timeNow);
        $binding->NEXT_ACTIVITY = date("Y-m-d H:i:s", $timeNow + $timeInterVal);
        $binding->save();

        $bindingPartner = Binding::where('SOURCE_DEVICE_ID',$binding->SOURCE_DEVICE_ID)
                            ->where('TARGET_DEVICE_ID',$binding->TARGET_DEVICE_ID)
                            ->where('BINDING_ID','!=',$id)->first();
        if($bindingPartner){
            $bindingPartner->LAST_ACTIVITY = date("Y-m-d H:i:s", $timeNow);
            $bindingPartner->NEXT_ACTIVITY = date("Y-m-d H:i:s", $timeNow + $timeInterVal);
            $bindingPartner->save();
        }

    }
    /**
     * <Layer Number> (11.0) Enable all binding from database
     * <Processing Name> enableAllBinding
     * <Function> Enable all the binding
     *
     * @param Illuminate\Http\Request $request
     * @return 'success'
     */
    public function enableAllBinding(Request $request)
    {
        // For audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'binding management';
        $instruction = 'Enable Group binding';
        $this->auditLogs($ip,$host,$module,$instruction);

        $time = strtotime(date("Y-m-d H:i:s"));
        // Combine the time now and interval for new date
        $timeNow = date("Y-m-d H:i:s", $time);
        try{
            Binding::where('SOURCE_DEVICE_ID', $request->BINDING)
                    ->update(['BINDING_STATUS' => 1,
                         'MANUAL' => 0,
                         'NEXT_ACTIVITY' => $timeNow]);
        }catch(Exception $e){
            return $e->getMessage();
        }
        // Return success if no error
        return 'success';
    }
    /**
     * <Layer Number> (12.0) Disable all binding from database
     * <Processing Name> disableAllBinding
     * <Function> Disable all the binding
     *
     * @param Illuminate\Http\Request $request
     * @return 'success'
     */
    public function disableAllBinding(Request $request)
    {
        // For audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'binding management';
        $instruction = 'Disable Group binding';
        $this->auditLogs($ip,$host,$module,$instruction);

        $time = strtotime(date("Y-m-d H:i:s"));
        // Combine the time now and interval for new date
        $timeNow = date("Y-m-d H:i:s", $time);
        try{
            Binding::where('SOURCE_DEVICE_ID', $request->BINDING)
                   ->update(['BINDING_STATUS' => 0,
                             'MANUAL' => 0,
                             'NEXT_ACTIVITY' => $timeNow]);
        }catch(Exception $e){
            return $e->getMessage();
        }
        // Return success if no error
        return 'success';
    }
}
