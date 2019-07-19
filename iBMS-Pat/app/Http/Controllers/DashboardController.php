<?php
/*
* <System Name> iBMS
* <Program Name> DashboardController.php
*
* <Create> 2018.10.24 TP Raymond
* <Update>
*
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Auth;
use Carbon\Carbon;
use App\Gateway;
use App\Device;
use App\Room;
use App\USER;
use App\SystemModule;
use App\ProcessedData;
use Illuminate\Support\Facades\Log;

/**
 * <Class Name> DashboardController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class DashboardController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // getGateway                       (1.0) Retrieve gateway status
    // gatewayStatus                    (2.0) Retrieve list of gateway status
    // deviceStatus                     (3.0) Retrieve list of device status
    // getDevice                        (4.0) Retrieve device status
    // getStatus                        (5.0) Retrieve list of status
    // modules                          (6.0) Retrieve all the modules
    // getUniqueDevices                 (7.0) Retrieve unique devices
    // getProcessedData                 (8.0) Retrieve processed data


    /**
     * <Layer number> (1.0) Retrieve gateway status
     * <Processing name> getGateway
     * <Function> Retrieve gateway status if online or offline
     *            URL: http://localhost/gateways/id
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getGateway(Request $request)
    {
        $onlineGateways = Gateway::whereIn('MANUFACTURER_ID',[1,2])
                            ->where('ONLINE_FLAG',1)->get()->count();
        $offlineGateways = Gateway::whereIn('MANUFACTURER_ID',[1,2])
                            ->where('ONLINE_FLAG',0)->get()->count();
        $gateway = array(
            ['status' => 'Online Gateway/s',
                'count' => $onlineGateways, 'color' => '#28a745'],
            ['status' => 'Offline Gateway/s',
                'count' => $offlineGateways, 'color' => '#6c757d'],
        );
        return $gateway;

    }

    /**
     * <Layer number> (2.0) Retrieve list of gateway status
     * <Processing name> gatewayStatus
     * <Function> Retrieve list of online and offline gateway status 
     *            URL: 
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function gatewayStatus(Request $request)
    {
        $list = [];
        $onlineGateways = Gateway::with('room')
            ->with('floor')
            ->whereIn('MANUFACTURER_ID',[1,2])
            ->where([['ONLINE_FLAG',1], ['REG_FLAG',1]])
            ->get();

        foreach($onlineGateways as $onlineGateway){
            $json = array(
                'onlineGatewayIP' => $onlineGateway->GATEWAY_IP,
                'onlineFloor' => $onlineGateway->floor->FLOOR_NAME,
                'onlineRoom' => $onlineGateway['room']['ROOM_NAME'],
            );
            array_push($list, $json);
        }
        $offlineGateways = Gateway::with('room')
            ->with('floor')
            ->whereIn('MANUFACTURER_ID',[1,2])
            ->where([['ONLINE_FLAG',0], ['REG_FLAG',1]])
            ->get();
        foreach($offlineGateways as $offlineGateway){
            $json = array(
                'offlineGatewayIP' => $offlineGateway->GATEWAY_IP,
                'offlineFloor' => $offlineGateway->floor->FLOOR_NAME,
                'offlineRoom' => $offlineGateway['room']['ROOM_NAME'],
            );
            array_push($list, $json);
        }
        return $list;
    }

    /**
     * <Layer number> (3.0) Retrieve list of device status
     * <Processing name> deviceStatus
     * <Function> Retrieve list of online and offline device status 
     *            URL: http://localhost/
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function deviceStatus(Request $request)
    {
        $list = [];
        $onlineDevices = DB::table('M006_DEVICE')
            ->where([['MANUFACTURER_ID',1], ['ONLINE_FLAG',1], ['REG_FLAG',1]])
            ->select('DEVICE_TYPE AS device', DB::raw('count(*) as count'))
            ->groupBy('DEVICE_TYPE')
            ->get();
        // Capitalize each word and replace "_" to space
        foreach($onlineDevices as $onlineDevice){
            $json = array(
                'onlineDevice' => ucwords(str_replace("_", " ",
                    $onlineDevice->device)),
                'count' => $onlineDevice->count,
            );
            array_push($list, $json);
        }

        $offlineDevices = Device::with('room')
            ->with('floor')
            ->where([['MANUFACTURER_ID',1], ['ONLINE_FLAG',0], ['REG_FLAG',1]])
            ->get();
        // Capitalize each word and replace "_" to space
        foreach($offlineDevices as $offlineDevice){
            $json = array(
                'offlineDevice' => ucwords(str_replace("_", " ",
                    $offlineDevice->DEVICE_TYPE)),
                'offlineFloor' => $offlineDevice->floor->FLOOR_NAME,
                'offlineRoom' => $offlineDevice->room->ROOM_NAME,
            );
            array_push($list, $json);
        }

        return $list;
    }

    /**
     * <Layer number> (4.0) Retrieve device status
     * <Processing name> getDevice
     * <Function> Retrieve device status if online or offline
     *            URL: http://localhost/devices/id
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getDevice(Request $request)
    {
        $onlineDevices = Device::where([['MANUFACTURER_ID',1],
            ['ONLINE_FLAG',1]])->get()->count();
        $offlineDevices = Device::where([['MANUFACTURER_ID',1],
            ['ONLINE_FLAG',0]])->get()->count();
        $devices = array(
            ['status' => 'Online Device/s', 'count' => $onlineDevices,
                'color' => '#28a745'],
            ['status' => 'Offline Device/s', 'count' => $offlineDevices,
                'color' => '#cc0000'],
        );
        return $devices;
    }

    /**
     * <Layer number> (5.0) Retrieve list of status
     * <Processing name> getStatus
     * <Function> Retrieve list of status if online or offline
     *            URL: http://localhost/
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getStatus(Request $request)
    {
        $list = [];
        $gateways = Gateway::whereIn('MANUFACTURER_ID',[1,2])
            ->where('REG_FLAG',1)->get()->count();
        $onlineGateways = Gateway::whereIn('MANUFACTURER_ID',[1,2])
            ->where([['ONLINE_FLAG',1], ['REG_FLAG',1]])->get()->count();
        $offlineGateways = Gateway::whereIn('MANUFACTURER_ID',[1,2])
            ->where([['ONLINE_FLAG',0], ['REG_FLAG',1]])->get()->count();
        $devices = Device::where([['MANUFACTURER_ID',1], ['REG_FLAG',1]])
            ->get()->count();
        $onlineDevices = Device::where([['MANUFACTURER_ID',1],
            ['ONLINE_FLAG',1], ['REG_FLAG',1]])->get()->count();
        $offlineDevices = Device::where([['MANUFACTURER_ID',1],
            ['ONLINE_FLAG',0], ['REG_FLAG',1]])->get()->count();

        $json = array(
            'totalGateway' => $gateways,
            'onlineGateway' => $onlineGateways,
            'offlineGateway' => $offlineGateways,
            'totalDevices' => $devices,
            'onlineDevices' => $onlineDevices,
            'offlineDevices' => $offlineDevices,
        );
        array_push($list, $json);
        return $list;
    }

    /**
     * <Layer number> (6.0) Retrieve all the modules
     * <Processing name> modules
     * <Function> Retrieve all the modules
     *            URL: http://localhost/
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function modules(){
        $userID = Auth::user()->USER_ID;
        $users = USER::where('USER_ID',$userID)->with('authModules')->first();
        $res['USERNAME'] = $users->USERNAME;
        $userModules = $users->authModules;
        foreach($userModules as $userModule){
            $userModuleID = $userModule->MODULE_ID;
            $modules = SystemModule::where('MODULE_ID', $userModuleID)->get();
            foreach($modules as $module){
                array_push($res,$module->MODULE_NAME);
            }
        }
        return $res;
    }

    /**
     * <Layer number> (7.0) Retrieve unique devices
     * <Processing name> getUniqueDevices
     * <Function> Retrieve unique devices
     *            URL: http://localhost/getUniqueDevices
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getUniqueDevices(Request $request){
        $arr = [];
        if (isset($request->FLOOR_ID)) {
            if (isset($request->ROOM_ID)) {
                $devices = Device::where([['MANUFACTURER_ID',1], ['REG_FLAG',1],
                    ['ROOM_ID',$request->ROOM_ID]])->select('DEVICE_ID',
                    'DEVICE_TYPE','DEVICE_NAME','ONLINE_FLAG',
                    'ROOM_ID', 'FLOOR_ID')->get();
            }else{
                $devices = Device::where([['MANUFACTURER_ID',1], ['REG_FLAG',1],
                    ['FLOOR_ID',$request->FLOOR_ID]])->select('DEVICE_ID',
                    'DEVICE_TYPE','DEVICE_NAME','ONLINE_FLAG', 'ROOM_ID',
                    'FLOOR_ID')->get();
            }
        }else{
            $devices = Device::where([['MANUFACTURER_ID',1], ['REG_FLAG',1]])
                ->select('DEVICE_ID','DEVICE_TYPE','DEVICE_NAME','ONLINE_FLAG',
                    'ROOM_ID', 'FLOOR_ID')->get();
        }
        foreach($devices->unique('DEVICE_TYPE') as $device){
            array_push($arr, $device);
        }
        return $arr;
    }

    /**
     * <Layer number> (8.0) Retrieve processed data
     * <Processing name> getProcessedData
     * <Function> Retrieve processed data
     *            URL: http://localhost/
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getProcessedData(Request $request){
        $deviceID = $request->DEVICE_ID;
        $floor = $request->FLOOR_ID;
        $room = $request->ROOM_ID;
        $deviceType = str_replace(' ', '_', strtolower($request->DEVICE_TYPE));
        $arrID = [];
        //change query to look for device ids with specific device type
        if ($room != '') {
            $deviceIDs = Device::where([['MANUFACTURER_ID',1], ['REG_FLAG',1],
                ['DEVICE_TYPE',$deviceType], ['ROOM_ID',$room]])
                ->select('DEVICE_ID')->get();
        }else{
            $deviceIDs = Device::where([['MANUFACTURER_ID',1], ['REG_FLAG',1],
                ['DEVICE_TYPE',$deviceType], ['FLOOR_ID',$floor]])
                ->select('DEVICE_ID')->get();
        }
        foreach($deviceIDs as $deviceID){
            array_push($arrID, $deviceID->DEVICE_ID);
        }
        //changed query to select elements in child table to optimize memory usage
        $data = ProcessedData::with(array('device'=>function($query){
            $query->select('DEVICE_ID', 'DEVICE_TYPE', 'DEVICE_NAME');
        }))->whereIn('DEVICE_ID',$arrID)->get();
        return $data;
    }
}
