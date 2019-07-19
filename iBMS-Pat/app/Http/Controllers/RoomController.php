<?php
/*
* <System Name> iBMS
* <Program Name> RoomController.php
*
* <Create> 2018.06.27 TP Yani
* <Update> 2018.06.29 TP Bryan    Added 5.0
*          2018.06.29 TP Bryan    Added 6.0
*          2018.07.02 TP Bryan    Added 7.0
*          2018.07.26 TP Bryan    Fixed code structure
*          2018.08.07 TP Bryan    Finalized(?) functions as endpoints
*
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
use App\ElectricMeter;
use App\Device;
use App\Traits\CommonFunctions;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * <Class Name> RoomController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class RoomController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // getRoomAll                      (1.0) Retrieve all rooms from database
    // searchRoom                     (2.0) Search all rooms based on given query string
    // getRoom                         (3.0) Retrieve room from database
    // getRoomFloor                    (4.0) Retrieve floor associated with the room
    // getRoomGateways                 (5.0) Retrieve all gateways associated with the room
    // getRoomDevices                  (6.0) Retrieve all devices associated with the room
    // getRoomUsers                    (7.0) Retrieve all users associated with the room
    // newRoom                         (8.0) Insert new room to database
    // updateRoom                      (9.0) Update room details
    // deleteRoom                      (10.0) Delete room from database
    // getRoomMeter                    (11.0) Get E-Meter on Specific Room

    use AuthenticatesUsers;
    use CommonFunctions;

    /**
     * <Layer number> (1.0) Retrieve all rooms from database
     * <Processing name> getRoomAll
     * <Function> Query strings:
     *                include -> retrieve relation along with model (Eager Load)
     *                    (ex. /rooms?include=devices)
     *                sortBy, sortVal -> sort collection
     *                    (ex. /rooms?sortBy=FLOOR_NAME&sortVal=desc)
     *                pageLength -> pagination option
     *                    (ex. /rooms?pageLength=10)
     *            URL: http://localhost/rooms
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getRoomAll(Request $request)
    {
        return $this->createGetResponse($request, (new Room)->newQuery());
    }

     /**
     * <Layer number> (2.0) Search all rooms based on given query string
     * <Processing name> searchRoom
     * <Function> Query strings:
     *                q -> search query
     *                    (ex. /rooms/search?q=Emergency%20Room)
     *            URL: http://localhost/rooms/search
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function searchRoom(Request $request)
    {
        if (!$request->q) {
            abort(400, "Invalid search query");
        }

        return $this->createGetResponse($request,
            Room::where('ROOM_NAME', 'like', '%' . $request->q . '%')
                ->orWhereHas('floor', function($query) use ($request){
                    $query->where('FLOOR_NAME', 'like', '%' . $request->q . '%');
                }));
    }

    /**
     * <Layer number> (3.0) Retrieve room from database
     * <Processing name> getRoom
     * <Function>
     *            URL: http://localhost/rooms/:id
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getRoom(Request $request, $id)
    {
        return $this->createGetResponse($request, (new Room)->newQuery(), $id);
    }

    /**
     * <Layer number> (4.0) Retrieve floor associated with the room
     * <Processing name> getRoomFloor
     * <Function>
     *            URL: http://localhost/rooms/:id/floor
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getRoomFloor(Request $request, $id)
    {
        return $this->createGetResponse($request, Room::findOrFail($id)
                                                      ->floor());
    }

    /**
     * <Layer number> (5.0) Retrieve all gateways associated with the room
     * <Processing name> getRoomGateways
     * <Function>
     *            URL: http://localhost/rooms/:id/gateways
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getRoomGateways(Request $request, $id)
    {
        return $this->createGetResponse($request, Room::findOrFail($id)
                                                      ->gateways());
    }

    /**
     * <Layer number> (6.0) Retrieve all devices associated with the room
     * <Processing name> getRoomDevices
     * <Function>
     *            URL: http://localhost/room/:id/devices
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getRoomDevices(Request $request, $id)
    {
        return $this->createGetResponse($request, Room::findOrFail($id)
                                                      ->devices()
                                                      ->with('deviceBindings')
                                                      ->where("REG_FLAG",1));
    }

    /**
     * <Layer number> (7.0) Retrieve all users associated with the room
     * <Processing name> getRoomUsers
     * <Function>
     *            URL: http://localhost/floor/:id/users
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getRoomUsers(Request $request, $id)
    {
        return $this->createGetResponse($request, Room::findOrFail($id)
                                                       ->users());
    }

    /**
     * <Layer number> (8.0) Insert new room to database
     * <Processing name> newRoom
     * <Function>
     *            URL: http://localhost/rooms/create
     *            METHOD: POST
     *            DATA: ROOM_ID, ROOM_NAME, ROOM_DATA
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function newRoom(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Floor Management';
        $instruction = 'Created a New Room';
        $this->auditLogs($ip,$host,$module,$instruction);
        $room = new Room;
        $room->ROOM_NAME = $request->ROOM_NAME;
        $room->FLOOR_ID = $request->FLOOR_ID;
        $room->ROOM_CATEGORY_ID = $request->ROOM_CATEGORY_ID;
        $room->DATA = json_encode($request->ROOM_DATA);
        $room->save();

        return $room;
    }

    /**
     * <Layer number> (9.0) Update room details
     * <Processing name> updateRoom
     * <Function>
     *            URL: http://localhost/rooms/update
     *            METHOD: POST
     *            DATA: ROOM_ID, ROOM_NAME
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function updateRoom(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Floor Management';
        $instruction = 'Updated a Room';
        $this->auditLogs($ip,$host,$module,$instruction);

        $room = Room::findOrFail($request->ROOM_ID);
        $room->ROOM_ID = $request->ROOM_ID ?
                        $request->ROOM_ID : $room->ROOM_ID;
        $room->ROOM_NAME = $request->ROOM_NAME ?
                        $request->ROOM_NAME : $room->ROOM_NAME;
        $room->save();

        return $room;
    }

    /**
     * <Layer number> (10.0) Delete room from database
     * <Processing name> deleteRoom
     * <Function>
     *            URL: http://localhost/rooms/delete
     *            METHOD: POST
     *            DATA: ROOM_ID
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function deleteRoom(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'Floor Management';

        $room = Room::findOrFail($request->ROOM_ID);
        $floor = Room::find($request->ROOM_ID)->floor()->first();
        $floorRoomData = $floor['FLOOR_MAP_DATA'];
        //remove room from floor json
        foreach ($floorRoomData['roomMap'] as $key => $floorRoom) {
            if ($floorRoom['roomMap'] == $room['ROOM_MAP_DATA']['ROOM_MAP']) {
                unset($floorRoomData['roomMap'][$key]);
                $floorRoomData['roomMap'] == array_merge($floorRoomData['roomMap']);
                $mappedRoom = Room::find($room['ROOM_ID'])->floor()->first();
                $mappedRoom->FLOOR_MAP_DATA = $floorRoomData;
                $mappedRoom->save();
                break;
            }
        }
        //unregister gateways in room
        foreach ($room->gateways()->get() as $gates => $gate) {
            $gate->REG_FLAG = 0;
            $gate->ONLINE_FLAG = 0;
            $gate->FLOOR_ID = null;
            $gate->ROOM_ID = null;
            $gate->save();
        }
        $room->devices()->delete();
        $room->delete();
        $instruction = 'Deleted a Room: '.$room->ROOM_NAME;
        $this->auditLogs($ip,$host,$module,$instruction);

        return 'success';
    }

    /**
     * <Layer number> (11.0) Get Room Meter in a Specific Room
     * <Processing name> getRoomMeter
     * <Function>
     *            URL: http://localhost/{id}/meters
     *            METHOD: POST
     *            DATA: ROOM_ID
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getRoomMeter(Request $request, $id){
        $meter = $this->createGetResponse($request, Room::findOrFail($id)->meters());
        $devices = Device::where('ROOM_ID', $id)->where('REG_FLAG', 1)->get();
        // array_unshift($device, $meter[0]);
        if (sizeof($meter) > 0) {
            $meter[0]->DEVICE_TYPE = 'electric_meter';
            $meter[0]->DEVICE_NAME = 'Electric Meter';
            $meterdevice = array();
            array_push($meterdevice, $meter[0]);
            foreach($devices as $device){
                array_push($meterdevice, $device);
            }
            return $meterdevice;
        }else{
            return $devices;
        }
    }
}
