<?php
/*
* <System Name> iBMS
* <Program Name> GatewayController.php
*
* <Create> 2018.06.26 TP Bryan
* <Update> 2018.06.27 TP Bryan    Added new functions
*          2018.06.28 TP Bryan    Fixed comments
*          2018.06.29 TP Bryan    Edited 1.0, Added 6.0, 7.0, 8.0
*          2018.07.02 TP Bryan    Added 9.0, 10.0
*          2018.07.20 TP Bryan    Rearranged function sequence
*          2018.07.23 TP Bryan    Inserted new functions, Fixed code structure
*          2018.07.24 TP Bryan    Added "Eager load" functions for get methods
*          2018.08.07 TP Bryan    Finalized(?) functions as endpoints
*          2018.08.08 TP Bryan    Added relation to MANUFACTURER to functions
*          2018.10.10 TP Harvey   Fix Encryption Sending
*          2018.11.05 TP Robert   Add Register function for MODBUS
*          2018.11.06 TP Robert   Update the update function
*          2018.11.09 TP Harvey   Update the updateGateway function
*          2018.12.20 TP Raymond  Add onlineGateway function
*          2019.01.16 TP Harvey   Add Enable Join Function
*
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gateway;
use App\Device;
use App\Manufacturer;
use App\Traits\CommonFunctions;
use App\ProcessedData;
use App\Events\testBinding;
use App\Events\OnlineStatusEvent;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;

/**
 * <Class Name> GatewayController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class GatewayController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // scanGatewayAll                  (1.0) Scan network for available gateways
    // scanGateway                     (2.0) Scan network for gateway
    // getGatewayAll                   (3.0) Retrieve all gateways from database
    // getGateway                      (4.0) Retrieve gateway from database
    // searchGateways                  (5.0) Search all gateways based on given query string (UNUSED)
    // getUnregisteredGateways         (6.0) Retrieve all gateways with "unregistered" flag
    // getRegisteredGateways           (7.0) Retrieve all gateways with "registered" flag
    // getBlockedGateways              (8.0) Retrieve all gateways with "blocked" flag
    // getDeletedGateways              (9.0) Retrieve all gateways with "delete" flag
    // getGatewayDevices               (10.0) Retrieve devices associated with the gateway
    // getGatewayFloor                 (11.0) Retrieve floor associated with the gateway
    // getGatewayRoom                  (12.0) Retrieve room associated with the gateway
    // registerGateway                 (13.0) Update gateway flag to "registered"
    // updateGateway                   (14.0) Update gateway details
    // deleteGateway                   (15.0) Update gateway flag to "deleted"
    // deleteGatewayManual             (16.0) Delete Gateway manually
    // undeleteGateway                 (17.0) Update gateway flag to "registered"
    // blockGateway                    (18.0) Update gateway flag to "blocked"
    // unblockGateway                  (19.0) Update gateway flag to "unregistered"
    // onlineGateway                   (20.0) Check online gateways and log offline gateway
    // registerGatewayModbus           (21.0) Update gateway flag to "registered"
    // enableJoin                      (22.0) Enable Join Mode of MC
    // disableJoin                     (23.0) Disable Join Mode of MC
    // resetGatewayForce               (24.0) Reset Gateway Configuration by Force

    use AuthenticatesUsers;
    use CommonFunctions;

    /**
     * <Layer number> (1.0) Scan network for available gateways
     * <Processing name> scanGatewayAll
     * <Function> The server sends a message to all available addresses in the LAN
     *            in the following format: [SERVER_IP_ADDR]|[APP_KEY]
     *            SERVER_IP_ADDR is the IP address of the server from where this
     *            application is running
     *            APP_KEY is the secret key of this application (see .env), used
     *            for encrypting messages
     *            For each scanned gateway, a new database record is created
     *            The response to the client will be the newly scanned gateway/s
     *            URL: http://localhost/gateways/scan
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function scanGatewayAll(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Gateway Management';
        $instruction = 'Scanned All the Gateway';
        $this->auditLogs($ip,$host,$module,$instruction);

        $local_ip = $_SERVER['SERVER_ADDR'];
        // Convert ip to array
        $ip_arr = explode(".", $local_ip);
        $scan_arr = array();
        $res_arr = array();



        // "Scan" network for gateways
        for ($i = 1; $i <= 254; $i++) {
            $ip_arr[3] = $i;            // Set last octet of ip

            // MC network details
            // Convert ip array back to string
            $remote_ip = implode(".", $ip_arr);
            $remote_port = env('PORT_SCAN_CLIENT');

            //Collect JSON Data
            $message = '{"message":{
                             "mode"     : "registerGateway",
                             "local_ip" : "' . $local_ip.'",
                             "app_key"  : "' . substr(env('APP_KEY'), 7).'"
                                },
                        "iv_key":"",
                        "length":""
                        }';
            $message = base64_encode($message);
            $timeout = array("sec"=>0,"usec"=>10000);

            // sRet is assumed to be in mac address format
            // sRet is then converted to a standard mac address format
            // (e.g. Aa:Bb:Cc:11:22:33 => AA:BB:CC:11:22:33)
            //
            // For future reference, there might be a need to change the processing
            // of sRet in cases where sRet is not in mac address format
            // (i.e. hardware is not from Wulian)
            $sRet = $this->sendToSocket($remote_ip, $remote_port, $message,
                        $timeout);
            //Remove New Line and White Spaces
            $sRet = preg_replace('/\s+/',' ',trim($sRet));


            //check if not null and a valid JSON
            if($sRet==null || $this->isJson($sRet)==false){
                continue;
            }
            //Parse JSON
            $sRet = json_decode($sRet,true);
            //Check if not null
            if ($sRet["mac_address"] != null && $sRet["company_name"] != null) {

                $serial_number = substr($sRet["mac_address"], 0, 17);
                $serial_number = strtoupper($serial_number);

                $manufacturer_id = Manufacturer::where('MANUFACTURER_NAME',
                                               $sRet["company_name"])
                                               ->first()
                                               ->MANUFACTURER_ID;

                $temp_arr = [
                    'GATEWAY_IP' => $remote_ip,
                    'GATEWAY_SERIAL_NO' => $serial_number,
                    'MANUFACTURER_ID' => $manufacturer_id
                ];
                array_push($scan_arr, $temp_arr);

            }
        }

        // Insert all scanned gateways to database
        foreach ($scan_arr as $arr) {

            $gateway = Gateway::where('GATEWAY_IP', $arr['GATEWAY_IP'])
                              ->where('GATEWAY_SERIAL_NO',
                                        $arr['GATEWAY_SERIAL_NO'])
                              ->first();



            if (!$gateway) {
                try {
                    $gateway = new Gateway;
                    $gateway->MANUFACTURER_ID = $arr['MANUFACTURER_ID'];
                    $gateway->GATEWAY_SERIAL_NO = $arr['GATEWAY_SERIAL_NO'];
                    $gateway->GATEWAY_IP = $arr['GATEWAY_IP'];
                    $gateway->REG_FLAG = 0;
                    $gateway->ONLINE_FLAG = 0;
                    $gateway->save();
                } catch (\Exception $e) {
                    //abort(500);
                    return $e;
                }
                // Response to request
                array_push($res_arr, $gateway);
            }else{
                try {
                    $gateway->GATEWAY_SERIAL_NO = $arr['GATEWAY_SERIAL_NO'];
                    $gateway->save();
                } catch (\Exception $e) {
                    abort(500);
                }
            }
        }

        return $res_arr;
    }

    /**
     * <Layer number> (2.0) Scan network for gateway
     * <Processing name> scanGateway
     * <Function> Search the network for a gateway given an ip address and a
     *            serial number (unique identifier). This will create a new
     *            entry marked as "unregistered" in the database if the search
     *            is successful.
     *            URL: http://localhost/gateways/:id/scan
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              GATEWAY_IP => str
     *              GATEWAY_SERIAL_NO => str
     * @return App\Gateway
     */
    public function scanGateway(Request $request)
    {
            //
    }

    /**
     * <Layer number> (3.0) Retrieve all gateways from database
     * <Processing name> getGatewayAll
     * <Function> Query strings:
     *                include -> retrieve relation along with model (Eager Load)
     *                    (ex. /gateways?include=devices)
     *                sortBy, sortVal -> sort collection
     *                    (ex. /gateways?sortBy=GATEWAY_NAME&sortVal=desc)
     *                pageLength -> pagination option
     *                    (ex. /gateways?pageLength=10)
     *            URL: http://localhost/gateway
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getGatewayAll(Request $request)
    {
        return $this->createGetResponse($request, (new Gateway)->newQuery());
    }

    /**
     * <Layer number> (4.0) Retrieve gateway from database
     * <Processing name> getGateway
     * <Function> Retrieve a single instance of gateway given an ID
     *            URL: http://localhost/gateway/id
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getGateway(Request $request, $id)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Gateway Management';
        $instruction = 'Retrieved Gateway';
        $this->auditLogs($ip,$host,$module,$instruction);

        return $this->createGetResponse($request,
                        (new Gateway)->newQuery(), $id);
    }

    /**
     * <Layer number> (5.0) Search all gateways based on given query string
     * <Processing name> searchGateways
     * <Function> Query strings:
     *                q -> search query
     *                    (ex. /gateways/search?q=Gateway%201)
     *            URL: http://localhost/gateways/search
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function searchGateways(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Gateway Management';
        $instruction = 'Searched for a Specific Gateway';
        $this->auditLogs($ip,$host,$module,$instruction);

        if (!$request->q) {
            abort(400, "Invalid search query");
        }

        return $this->createGetResponse($request,
            Gateway::where('GATEWAY_NAME', 'like', '%' . $request->q . '%'));
    }

    /**
     * <Layer number> (6.0) Retrieve all gateways with "unregistered" flag
     * <Processing name> getUnregisteredGateways
     * <Function>
     *            URL: http://localhost/gateways/unregistered
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getUnregisteredGateways(Request $request)
    {
        return $this->createGetResponse($request,Gateway::where('REG_FLAG', 0));
    }

    /**
     * <Layer number> (7.0) Retrieve all gateways with "registered" flag
     * <Processing name> getRegisteredGateways
     * <Function>
     *            URL: http://localhost/gateways/registered
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getRegisteredGateways(Request $request)
    {
        return $this->createGetResponse($request,Gateway::where('REG_FLAG', 1));
    }

    /**
     * <Layer number> (8.0) Retrieve all gateways with "blocked" flag
     * <Processing name> getBlockedGateways
     * <Function>
     *            URL: http://localhost/gateways/blocked
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getBlockedGateways(Request $request)
    {
        return $this->createGetResponse($request,Gateway::where('REG_FLAG', 4));
    }

    /**
     * <Layer number> (9.0) Retrieve all gateways with "delete" flag
     * <Processing name> getDeletedGateways
     * <Function>
     *            URL: http://localhost/gateways/deleted
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getDeletedGateways(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Gateway Management';
        $instruction = 'Retrieved the Deleted Gateway';
        $this->auditLogs($ip,$host,$module,$instruction);

        return $this->createGetResponse($request,Gateway::where('REG_FLAG', 9));
    }

    /**
     * <Layer number> (10.0) Retrieve devices associated with the gateway
     * <Processing name> getGatewayDevices
     * <Function>
     *            URL: http://localhost/gateways/:id/devices
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getGatewayDevices(Request $request, $id)
    {
        return $this->createGetResponse($request, Gateway::findOrFail($id)
                                                         ->devices());
    }

    /**
     * <Layer number> (11.0) Retrieve floor associated with the gateway
     * <Processing name> getGatewayFloor
     * <Function>
     *            URL: http://localhost/gateways/:id/floor
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getGatewayFloor(Request $request, $id)
    {
        return $this->createGetResponse($request, Gateway::findOrFail($id)
                                                         ->floor());
    }

    /**
     * <Layer number> (12.0) Retrieve room associated with the gateway
     * <Processing name> getGatewayRoom
     * <Function>
     *            URL: http://localhost/gateways/:id/room
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getGatewayRoom(Request $request, $id)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Gateway Management';
        $instruction = 'Retrieved the Gateway Room';
        $this->auditLogs($ip,$host,$module,$instruction);

        return $this->createGetResponse($request, Gateway::findOrFail($id)
                                                         ->room());
    }

    /**
     * <Layer number> (13.0) Update gateway flag to "registered"
     * <Processing name> registerGateway
     * <Function>
     *            URL: http://localhost/gateways/register
     *            METHOD: POST
     *            FORM-DATA:
     *                GATEWAY_ID => int
     *                FLOOR_ID => int
     *                ROOM_ID => int
     *                GATEWAY_NAME => str
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function registerGateway(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Gateway Management';
        $instruction = 'Registered a Gateway';
        $this->auditLogs($ip,$host,$module,$instruction);

        $this->validate($request,['GATEWAY_NAME'=>'required']);
        if (!isset($request->GATEWAY_ID, $request->FLOOR_ID,
            $request->GATEWAY_NAME)) {
            abort(400, 'Malformed syntax.');
        }

        $gateway = Gateway::findOrFail($request->GATEWAY_ID);

        if ($gateway->REG_FLAG == 1) {
        	abort(409, 'Entity is already registered');
        }
        else {
        	$gateway->FLOOR_ID = $request->FLOOR_ID;
	        $gateway->ROOM_ID = $request->ROOM_ID;
	        $gateway->GATEWAY_NAME = $request->GATEWAY_NAME;
	        $gateway->REG_FLAG = 1;
	        $gateway->save();
        }

        return response($gateway, 201);
    }

    /**
     * <Layer number> (14.0) Update gateway details
     * <Processing name> updateGateway
     * <Function>
     *            URL: http://localhost/gateways/update
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function updateGateway(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Gateway Management';
        $instruction = 'Updated Gateway Details';
        $this->auditLogs($ip,$host,$module,$instruction);

        //Wulian Gateway

        $this->validate($request,
                            ['GATEWAY_NAME'=>'unique:M005_GATEWAY|required']);
        if($request->KEY == 'gateway'){
            $gateway = Gateway::findOrFail($request->GATEWAY_ID);
            $gateway->FLOOR_ID = $request->FLOOR_ID ?
                            $request->FLOOR_ID : $gateway->FLOOR_ID;
            $gateway->ROOM_ID = $request->ROOM_ID ?
                            $request->ROOM_ID : $gateway->ROOM_ID;
            $gateway->REG_FLAG = $request->REG_FLAG ?
                            $request->REG_FLAG : $gateway->REG_FLAG;
            $gateway->ONLINE_FLAG = $request->ONLINE_FLAG ?
                            $request->ONLINE_FLAG : $gateway->ONLINE_FLAG;
            $gateway->GATEWAY_NAME = $request->GATEWAY_NAME ?
                        $request->GATEWAY_NAME : $gateway->GATEWAY_NAME;
            $gateway->save();
            return response($gateway, 201);
        }else{
            $gateway = Gateway::findOrFail($request->GATEWAY_ID);

            $gateway->FLOOR_ID = $request->FLOOR_ID ?
                            $request->FLOOR_ID : $gateway->FLOOR_ID;
            $gateway->GATEWAY_SERIAL_NO = $request->GATEWAY_SERIAL_NO ?
                        $request->GATEWAY_SERIAL_NO : $gateway->GATEWAY_SERIAL_NO;
            $gateway->GATEWAY_NAME = $request->GATEWAY_NAME ?
                        $request->GATEWAY_NAME : $gateway->GATEWAY_NAME;
            $gateway->GATEWAY_IP = $request->GATEWAY_IP ?
                        $request->GATEWAY_IP : $gateway->GATEWAY_IP;
            $gateway->save();
            return response($gateway, 201);
        }
    }

    /**
     * <Layer number> (15.0) Delete Gateway on Table
     * <Processing name> deleteGateway
     * <Function>
     *            URL: http://localhost/gateways/delete
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              GATEWAY_IP => str
     *              GATEWAY_SERIAL_NO => str
     * @return App\Gateway
     */
    public function deleteGateway(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Gateway Management';
        $instruction = 'Deleted a Gateway';

        //Find Gateway from DB
        $gateway = Gateway::findorFail($request->GATEWAY_ID);



        if($request->KEY == 'gateway'){
            $remote_ip = $gateway->GATEWAY_IP;
            $remote_port = env("PORT_GATEWAY");

            if($gateway){
                //Get Devices based on Gateway
                $devices = $gateway->devices()->get();

                //Uncomment for Production
                //Delete Plotted Device on Floor Map
                foreach ($gateway->devices()->get() as $key => $device) {
                    $this->deleteDevicePlot($device);
                }

                //if request has "FORCE" parameter, directly delete gateway form db
                if ($request->FORCE == true) {

                    $gateway->devices()->delete();
                    $gateway->delete();
                    $this->auditLogs($ip,$host,$module,$instruction);

                    return "success";
                }
                //Unregister Gateway to OPS
                $data = '{"mode":"deleteGateway"}';
                $message = $this->encryptMessage($data);
                $sRet = $this->sendToSocket($remote_ip,$remote_port,$message);

                $retArr = json_decode($sRet,true);
                if($retArr["function"] == 'gatewayDeleted'){
                    //Uncomment for Production
                    //Delete Devices based on Gateway
                    $gateway->devices()->delete();
                    $gateway->delete();
                    $this->auditLogs($ip,$host,$module,$instruction);
                }else{
                    $this->auditLogs($ip,$host,$module,'Cannot contact Gateway to be:'.$gateway->GATEWAY_NAME);
                    return 'gateway';
                }

                return "success";
            }
        }else{
            $gateway->REG_FLAG = 9;
            $gateway->save();
            return "success";
        }

    }

    /**
     * <Layer number> (16.0) Delete Gateway Manual
     * <Processing name> deleteGatewayManual
     * <Function>
     *            URL: http://localhost/gateways/delete/manual
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              GATEWAY_IP => str
     * @return App\Gateway
     */
    public function deleteGatewayManual(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Gateway Management';
        $instruction = 'Deleted Gateway Manually';
        $this->auditLogs($ip,$host,$module,$instruction);

        $remote_port = env("PORT_GATEWAY");

        //Unregister Gateway to OPS
        $data = '{"mode":"deleteGateway"}';
        $message = $this->encryptMessage($data);
        $sRet = $this->sendToSocket($request->GATEWAY_IP,$remote_port,$message);

        $retArr = json_decode($sRet,true);


        return $retArr;

    }

    /**
     * <Layer number> (17.0) Update gateway flag to "registered"
     * <Processing name> undeleteGateway
     * <Function>
     *            URL: http://localhost/gateways/delete
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              GATEWAY_IP => str
     *              GATEWAY_SERIAL_NO => str
     * @return App\Gateway
     */
    public function undeleteGateway(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Gateway Management';
        $instruction = 'Updated Gateway to be Registered';
        $this->auditLogs($ip,$host,$module,$instruction);

        $gateway = Gateway::findOrFail($request->GATEWAY_ID);
        $gateway->REG_FLAG = 1;
        $gateway->save();

        return "success";
    }

    /**
     * <Layer number> (18.0) Update gateway flag to "blocked"
     * <Processing name> blockGateway
     * <Function>
     *            URL: http://localhost/gateways/block
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              GATEWAY_IP => str
     *              GATEWAY_SERIAL_NO => str
     * @return App\Gateway
     */
    public function blockGateway(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Gateway Management';
        $instruction = 'Updated Gateway to be Blocked';
        $this->auditLogs($ip,$host,$module,$instruction);

        $gateway = Gateway::findOrFail($request->GATEWAY_ID);
        $gateway->REG_FLAG = 4;
        $gateway->save();

        return "success";
    }

    /**
     * <Layer number> (19.0) Update gateway flag to "unregistered"
     * <Processing name> unblockGateway
     * <Function>
     *            URL: http://localhost/gateways/block
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              GATEWAY_IP => str
     *              GATEWAY_SERIAL_NO => str
     * @return App\Gateway
     */
    public function unblockGateway(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Gateway Management';
        $instruction = 'Updated Gateway to be Unregistered';
        $this->auditLogs($ip,$host,$module,$instruction);

        $gateway = Gateway::findOrFail($request->GATEWAY_ID);
        $gateway->REG_FLAG = 0;
        $gateway->save();

        return "success";
    }

    /**
     * <Layer number> (20.0) Online gateway flag to "1"
     * <Processing name> onlineGateway
     * <Function>
     *            URL: http://localhost/gateways/status
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              GATEWAY_IP => str
     *              GATEWAY_SERIAL_NO => str
     * @return App\Gateway
     */
    // public function onlineGateway(Request $request)
    public function onlineGateway()
    {
        $waitTimeout = 2;
        $gatewayIPs = Gateway::where('REG_FLAG',1)->get();
        // Loop addresses and create a socket for each
        $socks = array();
        $list = array();
        foreach ($gatewayIPs as $address) {
            $address["ONLINE_FLAG"] = 0;
            $address->save();
            $testport = $address["MANUFACTURER_ID"] == 2 ? $testport = 502 : $testport = 80;
            if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
                try{
                    $connect = socket_connect($sock, $address["GATEWAY_IP"], $testport);
                    if($connect){
                        $socks[$address["GATEWAY_IP"]] = $sock;
                    }
                }catch(\ErrorException $e){
                    continue;
                }
            } else {
                continue;
            }
        }
        // Sleep to allow sockets to respond
        // In theory you could just pass $waitTimeout to socket_select() but this can
        // be buggy with non blocking sockets
        sleep($waitTimeout);

        // Check the sockets that have connected
        $w = $socks;
        $r = $e = NULL;
        $count = socket_select($r, $w, $e, 0);
        if($count === false){
            Log::info("sockets connected: Something Happened \n");
        }else if($count > 0){
            Log::info("sockets connected: ". $count . "\n");
        }

        // Loop connected sockets and retrieve the addresses that connected
        foreach ($w as $sock) {
            $address = array_search($sock, $socks);
            $changeStatus = Gateway::where('GATEWAY_IP',$address)->first();
            $changeStatus->ONLINE_FLAG = 1;

            $changeStatus->save();
            Log::info($address. "<-->" . $changeStatus->ONLINE_FLAG . "\n");
            socket_close($sock);
        }

        $gateways = Gateway::whereIn('MANUFACTURER_ID',[1,2])->where('REG_FLAG',1)->get()->count();
        $onlineGateways = Gateway::whereIn('MANUFACTURER_ID',[1,2])->where([['ONLINE_FLAG',1], ['REG_FLAG',1]])->get()->count();
        $offlineGateways = Gateway::whereIn('MANUFACTURER_ID',[1,2])->where([['ONLINE_FLAG',0], ['REG_FLAG',1]])->get()->count();
        $devices = Device::where([['MANUFACTURER_ID',1], ['REG_FLAG',1]])->get()->count();
        $onlineDevices = Device::where([['MANUFACTURER_ID',1], ['ONLINE_FLAG',1], ['REG_FLAG',1]])->get()->count();
        $offlineDevices = Device::where([['MANUFACTURER_ID',1],['ONLINE_FLAG',0], ['REG_FLAG',1]])->get()->count();

        $json = array(
            'totalGateway' => $gateways,
            'onlineGateway' => $onlineGateways,
            'offlineGateway' => $offlineGateways,
            'totalDevices' => $devices,
            'onlineDevices' => $onlineDevices,
            'offlineDevices' => $offlineDevices,
        );
        array_push($list, $json);
        event(new OnlineStatusEvent($list));
    }

    /**
     * <Layer number> (21.0) Update gateway flag to "registered"
     * <Processing name> registerGatewayModbus
     * <Function>
     *            URL: http://localhost/gateways/register
     *            METHOD: POST
     *            FORM-DATA:
     *                GATEWAY_ID => int
     *                FLOOR_ID => int
     *                ROOM_ID => int
     *                GATEWAY_NAME => str
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function registerGatewayModbus(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Gateway Management';
        $instruction = 'Added a New Gateway';
        $this->auditLogs($ip,$host,$module,$instruction);

        $this->validate($request,['GATEWAY_SERIAL_NO'=>'unique:M005_GATEWAY|required','GATEWAY_NAME'=>'unique:M005_GATEWAY|required','GATEWAY_IP'=>'unique:M005_GATEWAY|required']);
        if (!isset($request->GATEWAY_IP, $request->FLOOR_ID,
            $request->GATEWAY_NAME,$request->GATEWAY_SERIAL_NO)) {
            abort(400, 'Malformed syntax.');
        }

        $newGateway = new Gateway;
        $newGateway->FLOOR_ID = $request->FLOOR_ID;
        $newGateway->ROOM_ID = $request->ROOM_ID;
        $newGateway->MANUFACTURER_ID = $request->MANUFACTURER_ID;
        $newGateway->GATEWAY_SERIAL_NO = $request->GATEWAY_SERIAL_NO;
        $newGateway->GATEWAY_NAME = $request->GATEWAY_NAME;
        $newGateway->GATEWAY_IP = $request->GATEWAY_IP;
        $newGateway->REG_FLAG = 1;
        $newGateway->ONLINE_FLAG = 1;
        $newGateway->save();

        return response($newGateway, 201);
    }

    //**************************************************************************
    //
    //  OPS COMMAND TO MC
    //
    //**************************************************************************

    /**
     * <Layer number> (22.0) Enable Join Mode of MC
     * <Processing name> enableJoin
     * <Function>
     *            URL: http://localhost/gateways/register
     *            METHOD: POST
     *            FORM-DATA:
     *                GATEWAY_ID => int
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function enableJoin(Request $request){

        $gateway = Gateway::where('GATEWAY_ID',$request->GATEWAY_ID)
                            ->select('GATEWAY_IP')
                            ->firstOrFail();

        $remote_ip = $gateway->GATEWAY_IP;
        $remote_port = env('PORT_GATEWAY');

        $data = '{"mode":"enableJoin","device_id":"","command":""}';

        $message = $this->encryptMessage($data);
        $sRet = $this->sendToSocket($remote_ip,$remote_port,$message);

        return $sRet;
    }

    /**
     * <Layer number> (23.0) Disable Join Mode of MC
     * <Processing name> disableJoin
     * <Function>
     *            URL: http://localhost/gateways/register
     *            METHOD: POST
     *            FORM-DATA:
     *                GATEWAY_ID => int
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function disableJoin(Request $request){

        $gateway = Gateway::where('GATEWAY_ID',$request->GATEWAY_ID)
                            ->select('GATEWAY_IP')
                            ->firstOrFail();

        $remote_ip = $gateway->GATEWAY_IP;
        $remote_port = env('PORT_GATEWAY');

        $data = '{"mode":"disableJoin","device_id":"","command":""}';

        $message = $this->encryptMessage($data);
        $sRet = $this->sendToSocket($remote_ip,$remote_port,$message);

        return $sRet;
    }

    /**
     * <Layer number> (24.0) Delete Gateway Force
     * <Processing name> deleteGatewayForce
     * <Function> Resetting gateway without Encryption
     *            URL: http://localhost/gateways/reset/force
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              GATEWAY_IP => str
     * @return App\Gateway
     */
    public function resetGatewayForce(Request $request)
    {
        // //for audit logs
        // $ip = $request->ip();
        // $username = auth()->user();
        // $host = $username->USERNAME;
        // $module = 'Gateway Management';
        // $instruction = 'Forced the Gateway to Reset';
        // //Fix for Future
        // $this->auditLogs($ip,$host,$module,$instruction);

        $remote_port = env("PORT_GATEWAY");

        //Unregister Gateway to OPS
        //Collect JSON Data
        $message = '{"message":{
                         "mode":"resetGatewayForce"
                            },
                    "iv_key":"",
                    "length":""
                    }';
        //$message = $this->encryptMessage($data);
        $message = base64_encode($message);
        $sRet = $this->sendToSocket($request->GATEWAY_IP,$remote_port,$message);

        $retArr = json_decode($sRet,true);

        return $retArr;

    }


}
