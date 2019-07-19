<?php
/*
* <System Name> iBMS
* <Program Name> InstructionController.php
*           インストラクションコントロール
* <Create> 2018.06.20 TP Bryan
* <Update> 2018.06.21 TP Bryan    Edited variable name for 1.0
*          2018.06.26 TP Bryan    Edited variable name for 1.0
*          2018.06.27 TP Bryan    Edited variable name for 1.0
*          2018.06.28 TP Bryan    Edited send message for 1.0, fixed comments
*          2018.06.29 TP Bryan    Edited send message for 1.0 added socket timeout
*          2018.07.19 TP Bryan    Added sample encryption
*          2018.07.20 TP Bryan    Renamed "Instructions" to "Instruction"
*          2018.12.18 OJT Jethro  Added code for Logs
*
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Device;
use App\Gateway;
use App\Room;
use App\Floor;
use App\Instruction;
use App\Traits\CommonFunctions;
use App\Code;
use App\IrLearning;
use App\ProcessedData;

/**
 * <Class Name> InstructionController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class InstructionController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // sendInstruction                 (1.0) Send instruction to gateway

    use CommonFunctions;

    /**
     * <Layer number> (1.0) Send instruction to gateway
     * <Processing name> sendInstruction
     * <Function> Send instruction to gateway
     *            URL: http://localhost/sendInstruction
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              GATEWAY_ID => int
     *              DEVICE_ID => str
     *              DATA => json
     * @return Illuminate\Http\Response
     */
    public function sendInstruction(Request $request)
    {
        $gateway = Gateway::where('GATEWAY_ID', $request->GATEWAY_ID)
                          ->select('GATEWAY_IP')
                          ->firstOrFail();
        $device = Device::where('DEVICE_ID', $request->DEVICE_ID)
                        ->select('DEVICE_TYPE', 'DEVICE_SERIAL_NO',
                            'DEVICE_NAME', 'ROOM_ID', 'FLOOR_ID')
                        ->firstOrFail();
        //For room name
        $room = Room::where('ROOM_ID', $device->ROOM_ID)
                        ->select('ROOM_NAME')
                        ->firstOrFail();
        //For floor name
        $floor = Floor::where('FLOOR_ID', $device->FLOOR_ID)
                        ->select('FLOOR_NAME')
                        ->firstOrFail();

        // MC network details
        $remote_ip = $gateway->GATEWAY_IP;
        $remote_port = env('PORT_GATEWAY');

        // Device data
        $deviceId = $device->DEVICE_SERIAL_NO;
        $deviceType = $device->DEVICE_TYPE;
        $addValue = $request->addlValue;
        $deviceName = $device->DEVICE_NAME;

        $deviceInstruction = $this->convertInstruction($deviceType,
            $request->COMMAND, $request->VALUE, $addValue);

        $data = '{"mode":"sendInstruction","device_id":"'.
            $deviceId.'","command":"'.$deviceInstruction.'"}';
        $message = $this->encryptMessage($data);
        $sRet = $this->sendToSocket($remote_ip, $remote_port, $message);

        //Log content from here
        $event = $request->event;       //Event
        //Automated or Manual
        $instructionType = $request->TYPE;
        //Log Content
        $logMessage = 'Device: '.$deviceName.' Room: '.
            $room->ROOM_NAME.' Floor: '.$floor->FLOOR_NAME.' Event: '.$event;
        $username = auth()->user();     //Fetch user data from session

        // Probably need to change second condition & GCP Pending
        // if ($sRet != null && $sRet == "Message Sent") {
        //      $instruction->SEND_FLAG = 1;
        //     $instruction->save();
        // }

        //Store Type, Instruction Type, Content, IP and Username
        $this->storeLogs(0, $instructionType, $logMessage, $request->ip(),
            $username->USERNAME);
        return $sRet;
    }
}