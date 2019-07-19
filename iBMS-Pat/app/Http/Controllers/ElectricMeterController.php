<?php
/*
* <System Name> iBMS
* <Program Name> ElectricMeterController.php
*
* <Create> 2018.11.07 TP Robert
*
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Gateway;
use App\Device;
use App\ElectricMeter;
use App\ProcessedData;
use App\Traits\CommonFunctions;
use Validator;
use App\Events\NewDeviceEvent;
use Illuminate\Pagination\LengthAwarePaginator;

class ElectricMeterController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // getRegisteredMeter            (1.0) Retrieve all meter with "registered" flag
    // getDeletedMeter            	 (2.0) Retrieve all meter with "deleted" flag
    // registerMeter            	 (3.0) create new meter data
    // updateMeter          		 (4.0) update meter data
    // deleteMeter          		 (5.0) delete meter data
    // unDeleteMeter                 (6.0) undelete meter data

    use CommonFunctions;

     /**
     * <Layer number> (1.0) Retrieve all meter with "registered" flag
     * <Processing name> getRegisteredMeter
     * <Function>
     *            URL: http://localhost/meters/registered
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */

    public function getRegisteredMeter(Request $request)
    {
        return $this->createGetResponse($request,
                        ElectricMeter::where('REG_FLAG', 1));
    }
     /**
     * <Layer number> (2.0) etrieve all meter with "deleted" flag
     * <Processing name> getDeletedMeter
     * <Function>
     *            URL: http://localhost/meters/deleted
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */

    public function getDeletedMeter(Request $request)
    {
        return $this->createGetResponse($request,
                        ElectricMeter::where('REG_FLAG', 9));
    }
     /**
     * <Layer number> (3.0) create new meter data
     * <Processing name> registerMeter
     * <Function>
     *            URL: http://localhost/meters/register
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */

    public function registerMeter(Request $request)
    {
        $customMessages = [
            'required' => 'This field is required.',
            'numeric' => 'This field number only.'
        ];

        $this->validate($request, [
            'DATA.*.roomID' => 'required',
            'DATA.*.serialNo' => 'required',
            'DATA.*.slaveID' => 'required|numeric',
        ], $customMessages);

        $newData = $request["DATA"];
        foreach ($newData as $data) {
            $meter = new ElectricMeter;
            $meter->FLOOR_ID = $request->FLOOR_ID;
            $meter->ROOM_ID = $data["roomID"];
            $meter->GATEWAY_ID = $request->GATEWAY_ID;
            $meter->SERIAL_NO = $data["serialNo"];
            $meter->SLAVE_ID = $data["slaveID"];
            $meter->REG_FLAG = 1;
            $meter->save();
        }

        return response($meter, 201);
    }
     /**
     * <Layer number> (4.0) update meter data
     * <Processing name> updateMeter
     * <Function>
     *            URL: http://localhost/meters/update
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */

    public function updateMeter(Request $request)
    {
        $meter = ElectricMeter::findOrFail($request->METER_ID);

        $meter->SERIAL_NO = $request->SERIAL_NO ? $request->SERIAL_NO : $meter->SERIAL_NO;
        $meter->SLAVE_ID = $request->SLAVE_ID ? $request->SLAVE_ID : $meter->SLAVE_ID;
        $meter->save();

        // return $meter;
        return 'success';
    }
     /**
     * <Layer number> (5.0) delete meter data
     * <Processing name> deleteMeter
     * <Function>
     *            URL: http://localhost/meters/delete
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */

    public function deleteMeter(Request $request)
    {
        $meter = ElectricMeter::findOrFail($request->DEVICE_ID);
        $meter->REG_FLAG = 9;
        $meter->save();

        return 'success';
    }
    /**
     * <Layer number> (6.0) undelete meter data
     * <Processing name> unDeleteMeter
     * <Function>
     *            URL: http://localhost/meters/undelete
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */

    public function unDeleteMeter(Request $request)
    {
        $meter = ElectricMeter::findOrFail($request->METER_ID);
        $meter->REG_FLAG = 1;
        $meter->save();

        return 'success';
    }
}
