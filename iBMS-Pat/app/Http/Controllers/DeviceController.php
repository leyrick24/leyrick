<?php
/*
* <System Name> iBMS
* <Program Name> DeviceController.php
*
* <Create> 2018.06.21 TP Bryan
* <Update> 2018.06.25 TP Bryan    Added new functions 4.0, 5.0
*          2018.06.26 TP Bryan    Edited variable name, return values for all functions
*          2018.06.27 TP Bryan    Edited query for 1.0
*          2018.06.28 TP Bryan    Edited send message for 2.0, fixed comments
*                                 Renamed 6.0 to getRegisteredDevices
*          2018.06.29 TP Bryan    Edited 1.0 added socket timeout
*          2018.07.11 TP Bryan    Added 7.0
*          2018.07.12 TP Bryan    Edited 1.0 added event
*          2018.07.25 TP Bryan    Inserted new functions, Fixed code structure
*          2018.07.26 TP Bryan    Added 20.0, 21.0
*          2018.08.07 TP Bryan    Finalized(?) functions as endpoints
*          2018.08.20 TP Bryan    Fixed code structure
*          2018.10.10 TP Harvey   Fix Encryption Sending
*          2018.11.06 TP Robert   Modify the CRUD functions
*          2019.05.28 TP Harvey   Applying COnding Standard
*          2019.06.04 TP Jethro   Modified return for getDeviceBindingListDevices and getMultiDeviceBindingListDevices and deleted use of Illuminate/Paginate/LengthAwarePaginator
*          2019.06.11 TP Jethro   Checked coding standard and fixed mismatching code with comments
*
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gateway;
use App\Device;
use App\BindingList;
use App\Binding;
use App\ProcessedData;
use App\Traits\CommonFunctions;
use Validator;
use App\Events\NewDeviceEvent;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Events\OnlineDeviceEvent;
use Illuminate\Support\Facades\Log;

/**
 * <Class Name> DeviceController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class DeviceController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // scanDeviceAll                   (1.0) Scan network for available devices
    // scanDevice                      (2.0) Scan network for device
    // getDeviceAll                    (3.0) Retrieve all devices from database
    // getDevice                       (4.0) Retrieve device from database
    // searchDevices                   (5.0) Retrieve device from database
    // getUnregisteredDevices          (6.0) Retrieve all devices with "unregistered" flag
    // getRegisteredDevices            (7.0) Retrieve all devices with "registered" flag
    // getBlockedDevices               (8.0) Retrieve all devices with "blocked" flag
    // getDeletedDevices               (9.0) Retrieve all devices with "delete" flag
    // getDeviceFloor                  (10.0) Retrieve floor associated with the device
    // getDeviceRoom                   (11.0) Retrieve room associated with the gateway
    // getDeviceGateway                (12.0) Retrieve gateway associated with the device
    // getDeviceProcessedData          (13.0) Retrieve processed data associated with the device
    // getDeviceInstructions           (14.0) Retrieve instructions associated with the device
    // getDeviceBindings               (15.0) Retrieve bindings associated with the device
    // registerDevice                  (16.0) Update device flag to "registered"
    // updateDevice                    (17.0) Update device details
    // deleteDevice                    (18.0) Update device flag to "deleted"
    // undeleteDevice                  (19.0) Update device flag to "registered"
    // blockDevice                     (20.0) Update device flag to "blocked"
    // unblockDevice                   (21.0) Update device flag to "unregistered"
    // newDevice                       (22.0) Create new device entry in database (API)
    // getDevicesWithBindings          (23.0) Retrieve all devices with registered bindings
    // getDevicesBindingList           (24.0) Retrieve all binding list associated with the device
    // getDevicesBindingListCondition  (25.0) Retrieve all binding list condition for this device
    // getDeviceBindingListDevices     (26.0) Retrieve all binding list associated with the device
    // getMultiDeviceBindingListDevices(27.0) Retrieve all binding list associated with the many device
    // deleteDeviceFromMC              (28.0) Deleting Device from MC
    // offlineDevice                   (29.0) When MC sends and Offline Device Function
    // onlineDeviceByGateway           (30.0) Change Status of Device based on status of gateway


    use AuthenticatesUsers;
    use CommonFunctions;

    /**
     * <Layer number> (1.0) Scan network for available devices
     * <Processing name> scanDeviceAll
     * <Function>
     *            URL: http://localhost/devices/scan
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function scanDeviceAll(Request $request)
    {
        $gateways = Gateway::select("GATEWAY_IP")->where('MANUFACTURER_ID',1)->get();
        foreach ($gateways as $gateway) {
            // MC network details
            $remote_ip = $gateway->GATEWAY_IP;
            $remote_port = env('PORT_GATEWAY');

            $data = '{"mode":"scanAllDevices"}';
            $message = $this->encryptMessage($data);
            $timeout = array("sec"=>0,"usec"=>100000);

            $sRet = $this->sendToSocket($remote_ip, $remote_port, $message,
                $timeout);
        }
        return $sRet;
    }

    /**
     * <Layer number> (2.0) Scan network for specific device
     * <Processing name> scanDevice
     * <Function>
     *            URL: http://localhost/devices/:id/scan
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function scanDevice(Request $request, $id)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Scanned a Device';
        $this->auditLogs($ip,$host,$module,$instruction);

        $gateways = Gateway::all("GATEWAY_IP");
        $device = Device::select('DEVICE_SERIAL_NO')->find($id);
        if($device){
            foreach ($gateways as $gateway) {
                // MC network details
                $remote_ip = $gateway->GATEWAY_IP;
                $remote_port = env('PORT_GATEWAY');
                $data = '{"mode":"scanOneDevice","device_id":"' . $device->DEVICE_SERIAL_NO . '"}';
                $message = $this->encryptMessage($data);
                $timeout = array("sec"=>0,"usec"=>100000);

                $sRet = $this->sendToSocket($remote_ip, $remote_port, $message,
                    $timeout);
            }

        }

        return $sRet;
    }

    /**
     * <Layer number> (3.0) Retrieve all devices from database
     * <Processing name> getDeviceAll
     * <Function>
     *            URL: http://localhost/devices
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDeviceAll(Request $request)
    {
        //for audit logs
        try {
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Retrieved all the Devices';
        $this->auditLogs($ip,$host,$module,$instruction);

        return $this->createGetResponse($request, (new Device)->newQuery());
        } catch (\Throwable $e) {

                return $e->getMessage();
        }
    }

    /**
     * <Layer number> (4.0) Retrieve device from database
     * <Processing name> getDevice
     * <Function>
     *            URL: http://localhost/devices/:id
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */

    public function getDevice(Request $request, $id)
    {
        return $this->createGetResponse($request, (new Device)->with('deviceBindings')->newQuery(), $id);
    }

    /**
     * <Layer number> (5.0) Search all devices based on given query string
     * <Processing name> searchDevices
     * <Function> Query strings:
     *                q -> search query
     *                    (ex. /devices/search?q=Ground%20Floor)
     *            URL: http://localhost/devices/search
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function searchDevices(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Searched for a Device';
        $this->auditLogs($ip,$host,$module,$instruction);

        if (!$request->q) {
            abort(400, "Invalid search query");
        }

        return $this->createGetResponse($request,
            Devices::where('DEVICE_NAME', 'like', '%' . $request->q . '%'));
    }


    /**
     * <Layer number> (6.0) Retrieve all devices with "unregistered" flag
     * <Processing name> getUnregisteredDevices
     * <Function>
     *            URL: http://localhost/devices/unregistered
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getUnregisteredDevices(Request $request)
    {
        return $this->createGetResponse($request, Device::where('REG_FLAG', 0));
    }

     /**
     * <Layer number> (7.0) Retrieve all devices with "registered" flag
     * <Processing name> getRegisteredDevices
     * <Function>
     *            URL: http://localhost/devices/registered
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getRegisteredDevices(Request $request)
    {
        return $this->createGetResponse($request, Device::where('REG_FLAG', 1)->with('deviceBindings'));
    }
    public function getNewDev(Request $request)
    {
        return $this->createGetResponse($request, Device::where('REG_FLAG', 1));
    }
    /**
     * <Layer number> (8.0) Retrieve all devices with "blocked" flag
     * <Processing name> getBlockedDevices
     * <Function>
     *            URL: http://localhost/devices/blocked
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getBlockedDevices(Request $request)
    {
        return $this->createGetResponse($request, Device::where('REG_FLAG', 4));
    }

    /**
     * <Layer number> (9.0) Retrieve all devices with "delete" flag
     * <Processing name> getDeletedDevices
     * <Function>
     *            URL: http://localhost/devices/deleted
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDeletedDevices(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Retrieved Deleted Devices';
        $this->auditLogs($ip,$host,$module,$instruction);

        return $this->createGetResponse($request, Device::where('REG_FLAG', 9));
    }

    /**
     * <Layer number> (10.0) Retrieve floor associated with the device
     * <Processing name> getDeviceFloor
     * <Function>
     *            URL: http://localhost/devices/:id/floor
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDeviceFloor(Request $request, $id)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Retrieved a Device Floor';
        $this->auditLogs($ip,$host,$module,$instruction);

        return $this->createGetResponse($request, Device::findOrFail($id)
                                                         ->floor());
    }

    /**
     * <Layer number> (11.0) Retrieve room associated with the gateway
     * <Processing name> getDeviceRoom
     * <Function>
     *            URL: http://localhost/devices/:id/room
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDeviceRoom(Request $request, $id)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Retrieved a Room';
        $this->auditLogs($ip,$host,$module,$instruction);

        return $this->createGetResponse($request, Device::findOrFail($id)
                                                         ->room());
    }

    /**
     * <Layer number> (12.0) Retrieve gateway associated with the device
     * <Processing name> getDeviceGateway
     * <Function>
     *            URL: http://localhost/devices/:id/gateway
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDeviceGateway(Request $request, $id)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Retrieved Gateway associated with the Devices';
        $this->auditLogs($ip,$host,$module,$instruction);

        return $this->createGetResponse($request, Device::findOrFail($id)
                                                         ->gateway());
    }

    /**
     * <Layer number> (13.0) Retrieve processed data associated with the device
     * <Processing name> getDeviceProcessedData
     * <Function>
     *            URL: http://localhost/devices/:id/processed-data
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDeviceProcessedData(Request $request, $id)
    {
        return $this->createGetResponse($request, Device::findOrFail($id)
                                                         ->processedData());
    }

    /**
     * <Layer number> (14.0) Retrieve instructions associated with the device
     * <Processing name> getDeviceInstructions
     * <Function>
     *            URL: http://localhost/devices/:id/instructions
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDeviceInstructions(Request $request, $id)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Retrieved the Device Instructions';
        $this->auditLogs($ip,$host,$module,$instruction);

        return $this->createGetResponse($request, Device::findOrFail($id)
                                                         ->instructions());
    }

    /**
     * <Layer number> (15.0) Retrieve bindings associated with the device
     * <Processing name> getDeviceBindings
     * <Function>
     *            URL: http://localhost/devices/:id/bindings
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDeviceBindings(Request $request, $id)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Retrieved the Device Binding';
        $this->auditLogs($ip,$host,$module,$instruction);

        return $this->createGetResponse($request, Device::findOrFail($id)
                                                         ->bindings());
    }

    /**
     * <Layer number> (16.0) Update device flag to "registered"
     * <Processing name> registerDevice
     * <Function>
     *            URL: http://localhost/devices/register
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function registerDevice(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Updated a Device to be Registered';
        $this->auditLogs($ip,$host,$module,$instruction);
        // if (!isset($request->GATEWAY_ID, $request->FLOOR_ID,
        //     $request->GATEWAY_NAME)) {
        //     abort(400, 'Malformed syntax.');
        // }

        $this->validate($request,['DEVICE_NAME'=>'required','DEVICE_CATEGORY'=>'required']);
        $device = Device::findOrFail($request->DEVICE_ID);

        if ($device->REG_FLAG == 1) {
            abort(409, 'Entity is already registered');
        }
        else {
            $device->FLOOR_ID = $request->FLOOR_ID;
            $device->ROOM_ID = $request->ROOM_ID;
            $device->GATEWAY_ID = $request->GATEWAY_ID;
            $device->DEVICE_NAME = $request->DEVICE_NAME;
            $device->DEVICE_CATEGORY = $request->DEVICE_CATEGORY;
            $device->DATA = $request->DEVICE_DATA;
            $device->REG_FLAG = 1;
            $device->ONLINE_FLAG = 1;
            $device->save();
        }

        return response($device, 201);
    }

    /**
     * <Layer number> (17.0) Update device details
     * <Processing name> updateDevice
     * <Function>
     *            URL: http://localhost/devices/update
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function updateDevice(Request $request)
    {
    	try{
    		//for audit logs
	        $ip = $request->ip();
	        $username = auth()->user();
	        $host = $username->USERNAME;
	        $module = 'Device Management';
	        $instruction = 'Updated the Details of a Device';

	        $device = Device::findOrFail($request->DEVICE_ID);
	        // $device->FLOOR_ID = $request->FLOOR_ID ?
	        //                 $request->FLOOR_ID : $device->FLOOR_ID;
	        // $device->ROOM_ID = $request->ROOM_ID ?
	        //                 $request->ROOM_ID : $device->ROOM_ID;
	        // $device->GATEWAY_ID = $request->GATEWAY_ID ?
	        //                 $request->GATEWAY_ID : $device->GATEWAY_ID;
	        $device->DEVICE_NAME = $request->DEVICE_NAME ?
	                        $request->DEVICE_NAME : $device->DEVICE_NAME;
	        $device->DEVICE_CATEGORY = $request->DEVICE_CATEGORY ?
	                        $request->DEVICE_CATEGORY : $device->DEVICE_CATEGORY;
	        $device->DATA = $request->DEVICE_DATA ?
	                        $request->DEVICE_DATA : $device->DATA;
	        $device->DEVICE_MAP_NAME = $request->DEVICE_MAP_NAME ?
	                                    $request->DEVICE_MAP_NAME : $device->DEVICE_MAP_NAME;

	        $device->save();
	        $this->auditLogs($ip,$host,$module,$instruction);

	        // return $device;
	        return 'success';

    	}catch(\Exception $e){
    		return 'error';
    	}

    }

    /**
     * <Layer number> (18.0) Update device flag to "deleted"
     * <Processing name> deleteDevice
     * <Function>
     *            URL: http://localhost/devices/delete
     *            METHOD: POST
     *
     *            Note: If a device is manually deleted from the gateway/MC,
     *                  it should not be automatically deleted from the system's
     *                  database, rather, a notification should be made that a
     *                  device is forcefully deleted from the system
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              DEVICE_ID => int
     * @return Illuminate\Http\Response
     */
    public function deleteDevice(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Updated a Device to be Deleted';
        $this->auditLogs($ip,$host,$module,$instruction);

        $device = Device::findOrFail($request->DEVICE_ID);

        $this->deleteDevicePlot($device);

        //Send Delete Device command to MC
        $remote_ip = $device->gateway->GATEWAY_IP;
        $remote_port = env("PORT_GATEWAY");

        if($device){
            //Delete Plotted Device
            $this->deleteDevicePlot($device);

            //Delete to MC
            $data = '{"mode":"deleteDevice","device_id":"' . $device->DEVICE_SERIAL_NO . '"}';
            $message = $this->encryptMessage($data);
            $sRet = $this->sendToSocket($remote_ip,$remote_port,$message);

            $retArr = json_decode($sRet,true);
            $device->delete();
            // return $sRet;
            return 'success';
        }else{
            return 'Device not found';
        }
    }

    /**
     * <Layer number> (19.0) Update device flag to "registered"
     * <Processing name> undeleteDevice
     * <Function>
     *            URL: http://localhost/devices/delete
     *            METHOD: POST
     *
     *            Note: If a device is manually deleted from the gateway/MC,
     *                  it should not be automatically deleted from the system's
     *                  database, rather, a notification should be made that a
     *                  device is forcefully deleted from the system
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              DEVICE_ID => int
     * @return Illuminate\Http\Response
     */
    public function undeleteDevice(Request $request)
    {
    	try{
    		//for audit logs
	    	$ip = $request->ip();
	        $username = auth()->user();
	        $host = $username->USERNAME;
	        $module = 'Device Management';
	        $instruction = 'Updated a Device to be Undeleted';
	        $this->auditLogs($ip,$host,$module,$instruction);

	        $device = Device::findOrFail($request->DEVICE_ID);
	        $device->REG_FLAG = 1;
	        $device->save();

	        // return response("", 204);
	        return 'success';

    	}catch(\Exception $e){
    		return 'error';
    	}


    }

    /**
     * <Layer number> (20.0) Update device flag to "blocked"
     * <Processing name> blockDevice
     * <Function>
     *            URL: http://localhost/device/block
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function blockDevice(Request $request)
    {
    	try{
	        //for audit logs
	        $ip = $request->ip();
	        $username = auth()->user();
	        $host = $username->USERNAME;
	        $module = 'Device Management';
	        $instruction = 'Updated a Device to be Blocked';
	        $this->auditLogs($ip,$host,$module,$instruction);

	        $device = Device::findOrFail($request->DEVICE_ID);
	        $device->REG_FLAG = 4;
	        $device->save();

	        // return response("", 204);
	        return 'success';

    	}catch(\Exception $e){
    		return 'error';
    	}
    }

    /**
     * <Layer number> (21.0) Update device flag to "unregistered"
     * <Processing name> unblockDevice
     * <Function>
     *            URL: http://localhost/device/block
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function unblockDevice(Request $request)
    {
    	try{
    		//for audit logs
	        $ip = $request->ip();
	        $username = auth()->user();
	        $host = $username->USERNAME;
	        $module = 'Device Management';
	        $instruction = 'Update a Device to Unregistered';
	        $this->auditLogs($ip,$host,$module,$instruction);

	        $device = Device::findOrFail($request->DEVICE_ID);
	        $device->REG_FLAG = 0;
	        $device->save();

	        // return response("", 204);
	        return 'success';

    	}catch(\Exception $e){
    		return 'error';
    	}

    }

    /**
     * <Layer number> (22.0) Insert new device to database
     * <Processing name> newDevice
     * <Function> Insert new device to database (request coming from NodeJS)
     *            URL: http://localhost/api/newDevice
     *            METHOD: POST
     *
     *            Note: API routes need to have some security measure (OAuth tokens?)
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              GATEWAY_IP => str
     *              DEVICE_SERIAL_NO => str
     *              DEVICE_TYPE => json
     *              DEVICE_DATA => str
     * @return Illuminate\Http\Response
     */
    public function newDevice(Request $request)
    {
        $message = array(
            'content' => $request->MESSAGE,
            'iv' => $request->IV
        );
        $decrypted = $this->decryptMessage($message);
        $decryptedArray = json_decode($decrypted,true);


        $gateway = Gateway::where('GATEWAY_IP', $request->GATEWAY_IP)
                          ->select('GATEWAY_ID', 'FLOOR_ID','ROOM_ID','REG_FLAG','MANUFACTURER_ID')
                          ->first();
        $device = Device::where('DEVICE_SERIAL_NO', $decryptedArray['device_id'])
                        ->first();

        $processedData = new ProcessedData;
        $processedData->DEVICE_ID = 999;
        $processedData->DATA =  array_merge($message,json_decode($gateway,true));
        $processedData->SEND_FLAG = 0;
        $processedData->save();

        if (!$gateway) {
            return "Gateway not found";
        }
        else if ($gateway->REG_FLAG == 0) {
            return "Gateway not yet registered";
        }


        if (!$device) {

            $device = new Device;
            $device->FLOOR_ID = $gateway->floor->FLOOR_ID;
            $device->ROOM_ID = $gateway->ROOM_ID;
            $device->GATEWAY_ID = $gateway->GATEWAY_ID;
            $device->MANUFACTURER_ID = $gateway->MANUFACTURER_ID;
            $device->DEVICE_SERIAL_NO = $decryptedArray['device_id'];
            $device->DEVICE_TYPE = $this->convertDeviceType($decryptedArray['device_type']);
            $device->DATA = $this->convertDeviceData($device->DEVICE_TYPE,$decryptedArray['device_data'],'newDeviceData');
            $device->REG_FLAG = 0;
            $device->ONLINE_FLAG = 0;
            $device->save();

            event(new NewDeviceEvent($device));

            return $device;
        }else{

            //Make Device Online
            //Trigger Event Here Change Column online status
            $processedData = new ProcessedData;
            $processedData->DEVICE_ID = 333;
            $processedData->DATA = '{"ID":"' . $device->DEVICE_SERIAL_NO .'","DEVICE_TYPE":"' . $device->DEVICE_TYPE .'"}';
            $processedData->SEND_FLAG = 1;
            $processedData->save();


            $device->ONLINE_FLAG = 1;
            $device->save();

            if ($device->REG_FLAG == 9) {
                $device->REG_FLAG = 0;
                $device->save();

                event(new NewDeviceEvent($device));

                return $device;
            }
            else if ($device->REG_FLAG == 0) {

                event(new NewDeviceEvent($device));

                return $device;
            }
        }
    }

    /**
     * <Layer number> (23.0) Retrieve all devices with registered bindings
     * <Processing name> getDevicesWithBindings
     * <Function>
     *            URL: http://localhost/devices/with-bindings
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDevicesWithBindings(Request $request)
    {
        return $this->createGetResponse($request,
                    Device::with('bindings.bindingList','bindings.targetDevice:DEVICE_ID,DEVICE_TYPE,DEVICE_NAME')
                        ->has('bindings')
                        ->whereHas('bindings.bindingList', function ($query) use ($request){
                            $query->where('TARGET_DEVICE_CATEGORY',$request->BINDING_CATEGORY);
                        })
                    );
    }

    /**
     * <Layer number> (24.0) Retrieve all binding list associated with the device
     * <Processing name> getDeviceBindingList
     * <Function>
     *            URL: http://localhost/devices/:id/binding-list
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDeviceBindingList(Request $request, $id)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Retrieved all the Binding List';
        $this->auditLogs($ip,$host,$module,$instruction);

        $device = Device::select('DEVICE_TYPE')
                        ->findOrFail($id);

        return $this->createGetResponse($request,
            BindingList::where('SOURCE_DEVICE_TYPE', $device->DEVICE_TYPE));
    }

    /**
     * <Layer number> (25.0) Retrieve all binding list condition for this device
     * <Processing name> getDeviceBindingListCondition
     * <Function>
     *            URL: http://localhost/devices/:id/binding-list-condition
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDeviceBindingListCondition(Request $request, $id)
    {
		$arr = array();
        $deviceType = Device::select('DEVICE_TYPE')
                             ->findOrFail($id)
                             ->DEVICE_TYPE;

        $bindingList = BindingList::where('SOURCE_DEVICE_TYPE', $deviceType)
                                  ->select('SOURCE_DEVICE_CONDITION')
                                  ->groupBy('SOURCE_DEVICE_CONDITION')
                                  ->get();
        $bindingList = array(array("DATA"=>$bindingList));
		foreach($bindingList as $key => $obj){
			if($key == 0){
				$firstkey = $obj['DATA'][0]['SOURCE_DEVICE_CONDITION'];
			}
			$bindingList[$key] = array_push($arr,array("SELECTED"=>$firstkey,"DATA"=>$obj['DATA']));	;
		}
		return $arr;
    }

    /**
     * <Layer number> (26.0) Retrieve all binding list associated with the device
     * <Processing name> getDeviceBindingListDevices
     * <Function>
     *            URL: http://localhost/devices/:id/binding-list-devices
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getDeviceBindingListDevices(Request $request, $id, $condition, $devicetypeid)
    {
        $sourceDevice = Device::select('DEVICE_ID', 'GATEWAY_ID', 'DEVICE_TYPE')
                              ->findOrFail($id);

        $bindingList = BindingList::where('SOURCE_DEVICE_TYPE', $sourceDevice->DEVICE_TYPE)
                                  ->where('SOURCE_DEVICE_CONDITION', $condition)
                                  ->where('TARGET_DEVICE_CATEGORY', $devicetypeid)
                                  ->select('BINDING_LIST_ID',
                                           'TARGET_DEVICE_TYPE', 'TARGET_DEVICE_CONDITION',
                                           'SOURCE_DEVICE_TYPE', 'SOURCE_DEVICE_CONDITION',
                                           'TARGET_DEVICE_CATEGORY')
                                  ->get();

        $devices = array();

        foreach ($bindingList as $list) {
            $targetDevice = Device::where('DEVICE_TYPE', $list->TARGET_DEVICE_TYPE)
                                  ->where('ROOM_ID', $request->targetRoomId)
                                  ->where('REG_FLAG', 1)
                                  ->select('DEVICE_ID', 'DEVICE_TYPE', 'DEVICE_NAME')
                                  ->first();

            $binding = $targetDevice ? Binding::where('SOURCE_DEVICE_ID', $sourceDevice->DEVICE_ID)
                                              ->where('TARGET_DEVICE_ID', $targetDevice->DEVICE_ID)
                                              ->where('BINDING_LIST_ID', $list->BINDING_LIST_ID)
                                              ->first()
                                              : null;

            $arr = array();
            $arr['binding_list'] = $list;
            $arr['target_device'] = $targetDevice ? $targetDevice : null;
            $arr['binding'] = $binding ? $binding : null;

            if ($arr['target_device']) {
                array_push($devices, $arr);
            }
        }

        $itemCollection = collect($devices);

        return $itemCollection;
    }

    /**
     * <Layer number> (27.0) Retrieve all binding list associated with the many device
     * <Processing name> getMultiDeviceBindingListDevices
     * <Function>
     *            URL: http://localhost/devices/:id/binding-list-devices
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getMultiDeviceBindingListDevices(Request $request, $id, $device_type, $condition, $devicetypeid)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Retrieved multiple Device Binding List';
        $this->auditLogs($ip,$host,$module,$instruction);

        $sourceDevice = Device::select('DEVICE_ID', 'GATEWAY_ID', 'DEVICE_TYPE')
                              ->findOrFail($id);

        $bindingList = BindingList::where('SOURCE_DEVICE_TYPE', $device_type)
                                  ->where('SOURCE_DEVICE_CONDITION', $condition)
                                  ->where('TARGET_DEVICE_CATEGORY', $devicetypeid)
                                  ->select('BINDING_LIST_ID',
                                           'TARGET_DEVICE_TYPE', 'TARGET_DEVICE_CONDITION',
                                           'SOURCE_DEVICE_TYPE', 'SOURCE_DEVICE_CONDITION')
                                  ->get();

        $devices = array();

        foreach ($bindingList as $list) {
            $targetDevice = Device::where('DEVICE_TYPE', $list->TARGET_DEVICE_TYPE)
                                  ->where('GATEWAY_ID', $sourceDevice->GATEWAY_ID)
                                  ->where('REG_FLAG', 1)
                                  ->select('DEVICE_ID', 'DEVICE_TYPE', 'DEVICE_NAME')
                                  ->first();

            $binding = $targetDevice ? Binding::where('SOURCE_DEVICE_ID', $sourceDevice->DEVICE_ID)
                                              ->where('TARGET_DEVICE_ID', $targetDevice->DEVICE_ID)
                                              ->where('BINDING_LIST_ID', $list->BINDING_LIST_ID)
                                              ->first()
                                              : null;

            $arr = array();
            $arr['binding_list'] = $list;
            $arr['target_device'] = $targetDevice ? $targetDevice : null;
            $arr['binding'] = $binding ? $binding : null;

            if ($arr['target_device']) {
                array_push($devices, $arr);
            }
        }
        $itemCollection = collect($devices);
        return $itemCollection;
    }

  /**
     * <Layer number> (28.0) Deleting Device from MC
     * <Processing name> deleteDeviceFromMC
     * <Function>
     *            URL: http://localhost/api/delete/Device/MC
     *            METHOD: POST
     *
     *            Note: If a device is manually deleted from the gateway/MC,
     *                  it should not be automatically deleted from the system's
     *                  database, rather, a notification should be made that a
     *                  device is forcefully deleted from the system
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              DEVICE_ID => int
     * @return Illuminate\Http\Response
     */
    public function deleteDeviceFromMC(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Device Management';
        $instruction = 'Deleted a Device From MC';
        $this->auditLogs($ip,$host,$module,$instruction);

        $message = array(
            'content' => $request->MESSAGE,
            'iv' => $request->IV
        );

        $decrypted = $this->decryptMessage($message);
        $decryptedArray = json_decode($decrypted,true);

        $device = Device::where('DEVICE_SERIAL_NO', $decryptedArray['device_id'])
                        ->first();

        if($device){
            $device->delete();
            return "Device Deleted";
        }else{
            return "Device Not Deleted";
        }


    }
      /**
     * <Layer number> (29.0) When MC sends and Offline Device Function
     * <Processing name> offlineDevice
     * <Function>
     *            URL: http://localhost/api/offlineDevice
     *            METHOD: POST
     *
     *            Note: If a device is manually deleted from the gateway/MC,
     *                  it should not be automatically deleted from the system's
     *                  database, rather, a notification should be made that a
     *                  device is forcefully deleted from the system
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              DEVICE_ID => int
     * @return Illuminate\Http\Response
     */
    public function offlineDevice(Request $request)
    {
        $message = array(
            'content' => $request->MESSAGE,
            'iv' => $request->IV
        );

        $decrypted = $this->decryptMessage($message);
        $decryptedArray = json_decode($decrypted,true);

        $device = Device::where('DEVICE_SERIAL_NO', $decryptedArray['device_id'])
                        ->first();


        if($device){
            //Trigger Event Here Change Column online status
            $processedData = new ProcessedData;
            $processedData->DEVICE_ID = 333;
            $processedData->DATA = '{"ID":"' . $device->DEVICE_SERIAL_NO .'","DEVICE_TYPE":"' . $device->DEVICE_TYPE .'"}';
            $processedData->SEND_FLAG = 0;
            $processedData->save();

            $device->ONLINE_FLAG = 0;
            $device->save();

            return "Device Offline";
        }else{
            return "Device Not Deleted";
        }


    }
      /**
     * <Layer number> (30.0) Change Status of Device based on status of gateway
     * <Processing name> onlineDeviceByGateway
     * <Function> Change status of Device based on Status of gateway
     *            URL: http://localhost/api/onlineDeviceByGateway
     *            METHOD: POST
     *
     */
    public function onlineDeviceByGateway()
    {
        $gateways = Gateway::where([["ONLINE_FLAG","=","0"],["MANUFACTURER_ID","=","1"]])->get();

        foreach ($gateways as $key => $gateway) {
            $devices = Device::where("GATEWAY_ID",$gateway->GATEWAY_ID)->get();
            foreach ($devices as $keys => $device) {
                $device->ONLINE_FLAG = 0;
                $device->save();
            }
        }
        event(new OnlineDeviceEvent($devices));

    }

}
