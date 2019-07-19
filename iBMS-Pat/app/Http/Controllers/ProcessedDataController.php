<?php
/*
* <System Name> iBMS
* <Program Name> ProcessedDataController.php
*
* <Create> 2018.06.26 TP Bryan
* <Update> 2018.07.12 TP Bryan    Added sample event
*          2018.07.12 TP Bryan    Added binding
*          2018.09.26 TP Bryan    Added pull function
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProcessedData;
use App\Device;
use App\Traits\CommonFunctions;
use App\Events\IREvent;
use App\Events\LowBatteryDevice;

/**
 * <Class Name> ProcessedDataController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class ProcessedDataController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // newProcessedData                 (1.0) Insert new processed data to database
    // testFunction                     (2.0) Send function to MC Using POSTMAN
    //                                          (Manual Sending)
    // otherMCFunctions                 (3.0) Receive Uncommon MC Functions

    use CommonFunctions;

    /**
     * <Layer number> (1.0) Insert new processed data to database
     * <Processing name> newProcessedData
     * <Function> Insert new processed data to database that is received from MC
     *            URL: http://localhost/api/newData
     *            METHOD: POST
     *            DATA: DEVICE_SERIAL_NO, DEVICE_TYPE, DEVICE_DATA
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function newProcessedData(Request $request)
    {
        //create message from data of Gateway
        $message = array(
            'content' => $request->MESSAGE,
            'iv' => $request->IV
        );

        $decrypted = $this->decryptMessage($message);
        $decryptedArray = json_decode($decrypted,true);

        $retArr = array(
            "DEVICE_SERIAL_NO" => $decryptedArray['device_id'],
            "DEVICE_DATA" => $decryptedArray['device_data']
        );

        $device = Device::where('DEVICE_SERIAL_NO', $retArr['DEVICE_SERIAL_NO'])
                        ->firstOrFail();

        if ($device->REG_FLAG != 1) {
            return response(204);
        }

        $newData = $this->convertDeviceData($device->DEVICE_TYPE,
            $retArr['DEVICE_DATA'],'responseDeviceData');

        //Insert to Processed Data
        $processedData = new ProcessedData;
        $processedData->DEVICE_ID = $device->DEVICE_ID;
        $processedData->DATA = $newData;
        $processedData->SEND_FLAG = 0;
        $processedData->save();

        $device->DATA = $this->storeDeviceData($device->DEVICE_ID,
                                                $device->DEVICE_TYPE,
                                                $device->DATA,$newData);

        $device->ONLINE_FLAG = 1;
        $device->save();

        $this->insertDevicetoNotification($device);

        $this->broadcastNewData($device);

        try {
            app('App\Http\Controllers\BindingController')
                    ->checkBindings($processedData,$device);
        }
        catch(\Exception $e) {
            $this->storeLogs(4, 'Automatic', $e->getMessage(), '', '');
        }

        return response(204);
    }

    /**
     * <Layer number> (2.0) Send function to MC Using POSTMAN (Manual Sending)
     * <Processing name> testFunction
     * <Function> Send function to MC Manually
     *            URL: http://localhost/api/testFunction
     *            METHOD: POST
     *            DATA: FUNCTION, RAW_MESSAGE, GATEWAY_IP, COMMAND
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function testFunction(Request $request)
    {
        $remote_ip = $request->GATEWAY_IP;
        $remote_port = env('PORT_GATEWAY');

        $sRet = "";

        //Send Encrypted Data
        if($request->RAW_MESSAGE){
            $sRet = $this->sendToSocket($remote_ip, $remote_port,
                $request->RAW_MESSAGE);
            echo "Raw: \n";
            return $sRet;
        }
        //Collecting data to be send
        $msg = '{"mode":"'.$request->FUNCTION.'","device_id":"'.
            $request->DEVICE_ID.'","command":"'.$request->COMMAND.'"}';

        $message = $this->encryptMessage($msg);
        echo $message. "\n\n";
        $sRet = $this->sendToSocket($remote_ip, $remote_port, $message);
        return $message . "\n\n". $sRet . "\n\n";
    }

    /**
     * <Layer number> (3.0) Receive Uncommon MC Functions
     * <Processing name> otherMCFunctions
     * <Function> Receive Uncommon MC Functions
     *            URL: http://localhost/api/testFunction
     *            METHOD: POST
     *            DATA: MESSAGE, IV
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function otherMCFunctions(Request $request)
    {
        $message = array(
            'content' => $request->MESSAGE,
            'iv' => $request->IV
        );

        $decrypted = $this->decryptMessage($message);
        $decryptedArray = json_decode($decrypted,true);

        $processedData = new ProcessedData;
        $processedData->DEVICE_ID = 999999;
        $processedData->DATA = array_merge(
            json_decode('{"URL":"'.$request->URL.'"}',true),$decryptedArray);
        $processedData->SEND_FLAG = 0;
        $processedData->save();
        $decryptedArrayString = implode(",",$decryptedArray);
        $device = Device::where('DEVICE_SERIAL_NO',
            $decryptedArrayString)->first();

        //If device is Low Battery
        if ($request->URL == 'alarmDevice') {
            $this->storeLogs(0, 'Automatic',
                "Battery of device is low: ".$device['DEVICE_NAME'].' - '.
                $decryptedArrayString,  '', '');
            event(new LowBatteryDevice($processedData));
        }
         return response(204);
    }
}
