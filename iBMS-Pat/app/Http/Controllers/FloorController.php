<?php
/*
* <System Name> iBMS
* <Program Name> FloorController.php
*
* <Create> 2018.06.27 TP Yani
* <Update> 2018.06.28 TP Bryan    Fixed comments
*          2018.06.29 TP Bryan    Added 5.0, 6.0
*          2018.07.02 TP Bryan    Added 7.0
*          2018.07.25 TP Bryan    Fixed code structure
*          2018.08.01 TP Bryan    Finalized(?) functions as endpoints
*          2018.08.02 TP Bryan    Finalized(?) functions as endpoints
*          2018.10.05 TP Robert   Added 11.0
*          2019.06.06 TP Harvey   Applying Coding Standard
*
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Floor;
use App\User;
use App\Gateway;
use App\AuthLocation;
use App\Traits\CommonFunctions;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * <Class Name> FloorController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class FloorController extends Controller
{
    /**************************************************************************/
    /* Processing Hierarchy                                                   */
    /**************************************************************************/
    // getFloorAll                     (1.0) Retrieve all floors from database
    // getFloor                        (2.0) Retrieve floor from database
    // searchFloors                    (3.0) Search all floors based on given query string (UNUSED)
    // getFloorRooms                   (4.0) Retrieve all rooms associated with the floor
    // getFloorGateways                (5.0) Retrieve all gateways associated with the floor
    // getFloorDevices                 (6.0) Retrieve all devices associated with the floor
    // getFloorUsers                   (7.0) Retrieve all users associated with the floor
    // newFloor                        (8.0) Insert new floor to database
    // updateFloor                     (9.0) Update floor details
    // deleteFloor                     (10.0) Delete floor from database
    // getAuthFloor                    (11.0)  Retrieve selected floors from database

    use AuthenticatesUsers;
    use CommonFunctions;

    /**
     * <Layer number> (1.0) Retrieve all floors from database
     * <Processing name> getFloorAll
     * <Function> Query strings:
     *                include -> retrieve relation along with model (Eager Load)
     *                    (ex. /floors?include=rooms)
     *                sortBy, sortVal -> sort collection
     *                    (ex. /floors?sortBy=FLOOR_NAME&sortVal=desc)
     *                pageLength -> pagination option
     *                    (ex. /floors?pageLength=10)
     *            URL: http://localhost/floors
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getFloorAll(Request $request)
    {
        return $this->createGetResponse($request, (new Floor)->newQuery());
    }

    /**
     * <Layer number> (2.0) Retrieve floor from database
     * <Processing name> getFloor
     * <Function>
     *            URL: http://localhost/floors/:id
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getFloor(Request $request, $id)
    {
        return $this->createGetResponse($request, (new Floor)->newQuery(), $id);
    }

    /**
     * <Layer number> (3.0) Search all floors based on given query string
     * <Processing name> searchFloors
     * <Function> Query strings:
     *                q -> search query
     *                    (ex. /floors/search?q=Ground%20Floor)
     *            URL: http://localhost/floors/search
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function searchFloors(Request $request)
    {
        if (!$request->q) {
            abort(400, "Invalid search query");
        }

        return $this->createGetResponse($request, Floor::where('FLOOR_NAME', 'like', '%' . $request->q . '%'));
    }

    /**
     * <Layer number> (4.0) Retrieve all rooms associated with the floor
     * <Processing name> getFloorRooms
     * <Function>
     *            URL: http://localhost/floors/:id/rooms
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getFloorRooms(Request $request, $id)
    {
        return $this->createGetResponse($request, Floor::findOrFail($id)
                                                       ->rooms());
    }

    /**
     * <Layer number> (5.0) Retrieve all gateways associated with the floor
     * <Processing name> getFloorGateways
     * <Function>
     *            URL: http://localhost/floors/:id/gateways
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getFloorGateways(Request $request, $id)
    {
        return $this->createGetResponse($request, Floor::findOrFail($id)
                                                       ->gateways());
    }

    /**
     * <Layer number> (6.0) Retrieve all devices associated with the floor
     * <Processing name> getFloorDevices
     * <Function>
     *            URL: http://localhost/floors/:id/devices
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getFloorDevices(Request $request, $id)
    {
        return $this->createGetResponse($request, Floor::findOrFail($id)
                                                       ->devices());
    }

    /**
     * <Layer number> (7.0) Retrieve all users associated with the floor
     * <Processing name> getFloorUsers
     * <Function>
     *            URL: http://localhost/floor/:id/users
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getFloorUsers(Request $request, $id)
    {
        return $this->createGetResponse($request, Floor::findOrFail($id)
                                                       ->users());
    }

    /**
     * <Layer number> (8.0) Insert new floor to database
     * <Processing name> newFloor
     * <Function>
     *            URL: http://localhost/floors/create
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              FLOOR_NAME => str
     *              FLOOR_DATA => str
     * @return Illuminate\Http\Response
     */
    public function newFloor(Request $request)
    {
        //not used
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Floor Management';
        $instruction = 'Created a New Floor';
        $this->auditLogs($ip,$host,$module,$instruction);

        echo $request->DATA . PHP_EOL . json_encode(Floor::first()->DATA);
        return var_dump($request->DATA == Floor::first()->DATA);
        $validation = \Validator::make($request->all(), [
            'FLOOR_NAME' => 'required|unique:M005_FLOOR,FLOOR_NAME',
            'DATA' => 'required|unique:M005_FLOOR,DATA'
        ]);

        if ($validation->fails()) {
            return response($validation->errors(), 400);
        }

        return $validation;

        $floor = new Floor;
        $floor->FLOOR_NAME = $request->FLOOR_NAME;
        $floor->DATA = $request->FLOOR_DATA;
        $floor->save();

        return $floor;
    }

    /**
     * <Layer number> (9.0) Update floor details
     * <Processing name> updateFloor
     * <Function>
     *            URL: http://localhost/floors/update
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              FLOOR_ID => int
     *              FLOOR_NAME => str
     * @return Illuminate\Http\Response
     */
    public function updateFloor(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Floor Management';
        $instruction = 'Updated a Floor';
        $this->auditLogs($ip,$host,$module,$instruction);

        $floor = Floor::findOrFail($request->FLOOR_ID);
        $floor->FLOOR_NAME = $request->FLOOR_NAME ?
                        $request->FLOOR_NAME : $floor->FLOOR_NAME;
        $floor->FLOOR_MAP_DATA = $request->FLOOR_MAP_DATA ?
                        $request->FLOOR_MAP_DATA : $floor->FLOOR_MAP_DATA;
        $floor->save();

        return $floor;
    }

    /**
     * <Layer number> (10.0) Delete floor from database
     * <Processing name> deleteFloor
     * <Function>
     *            URL: http://localhost/floors/delete
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              FLOOR_ID => int
     * @return Illuminate\Http\Response
     */
    public function deleteFloor(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Floor Management';
        $instruction = 'Deleted a floor';
        $this->auditLogs($ip,$host,$module,$instruction);

        $floor = Floor::findOrFail($request->FLOOR_ID);
        //Uncomment for final production
        //$floor->devices()->get();
        //For Loop Devices()
        //Add this $this->deleteDevicePlot($device);

        foreach ($floor->devices()->get() as $key => $device) {
            $this->deleteDevicePlot($device);
        }

        foreach ($floor->gateways()->get() as $gates => $gate) {
            $gate->REG_FLAG = 0;
            $gate->ONLINE_FLAG = 0;
            $gate->FLOOR_ID = null;
            $gate->ROOM_ID = null;
            $gate->save();
        }

        $floor->rooms()->delete();
        $floor->devices()->delete();
        $floor->delete();

        return 'success';
    }

    /**
     * <Layer number> (10.0) Get Auth Location of the users
     * <Processing name> getAuthFloor
     * <Function> Get List of Floors tha is assigned to user
     *            URL: http://localhost/floors/delete
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              FLOOR_ID => int
     * @return Illuminate\Http\Response
     */
    public function getAuthFloor(Request $request)
    {

        $userid = $request->USERID;
        $arr = array();
        $user = User::where('USER_ID', $userid)->first();
        $locations = AuthLocation::select('FLOOR_ID')->where('USER_ID',$userid)->get();
        if(empty($locations) || $locations == null){
            $this->storeLogs(4, 'Automatic', 'User has no assigned floor/s',$request->ip(),$user['USERNAME']);
        }
        foreach ($locations as $location) {
            array_push($arr, $location->FLOOR_ID);
        }
        $floors = FLoor::whereIn('FLOOR_ID',$arr)->with('rooms')->get();

        return $floors;
    }


}
