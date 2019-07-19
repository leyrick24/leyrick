<?php
/*
* <System Name> iBMS
* <Program Name> ImageUploadFloorController.php
*
* <Create> 2018.07.27 TP Harvey
* <Update>
*
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Room;
use App\Floor;
use App\Traits\CommonFunctions;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * <Class Name> ImageUploadFloorController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class ImageUploadFloorController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // imageUpload                      (1.0) Upload Floor and Rooms Images and upload to database
    // addRoom                          (2.0) Add new roomto floor and image

    use AuthenticatesUsers;
    use CommonFunctions;

    /**
     * <Layer number> (1.0) Upload Floor and Rooms Images and upload to database
     * <Processing name> imageUpload

     *            URL: http://localhost/api/imageUpload
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function imageUpload(Request $request){

        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $request->USERNAME;
        $module = 'Floor management';

        $roomPath="imgs/rooms";
        $floorPath="imgs/floors";

        $floorImageName = "";
        $floorImageFile = "";
        $floorName = "";

        $roomArray= array();
        $roomImageName = array();
        $roomImageFile = array();
        $roomName = array();

        $floor = array();

        //Combine all files to specific array
        foreach($request->all() as $key => $value){
            if(strpos($key,'roomImageName') !== FALSE){
                array_push($roomImageName,$value);
            }
            else if(strpos($key,'roomImageFile') !== FALSE){
                array_push($roomImageFile,$value);
            }
            else if(strpos($key,'roomName') !== FALSE){
                array_push($roomName,$value);
            }
            else if(strpos($key,'floorImageName') !==FALSE){
                $floorImageName = $value;
            }
            else if(strpos($key,'floorImageFile') !==FALSE){
                $floorImageFile = $value;
            }
            else if(strpos($key,'floorName') !==FALSE){
                $floorName = $value;
            }
        }

        //Check if Image Name has duplicates
        if(count($roomImageName) !== count(array_flip($roomImageName))){
            return 'duplicate';
        }

        //Upload Floor Image to specified location
        $floorImageFile->move(public_path($floorPath),$floorImageName);

        foreach($roomImageFile as $key => $file){
            //Upload Room Image to specified location
            $file->move(public_path($roomPath),$roomImageName[$key]);


            $roomMap = explode(".",$roomImageName[$key])[0];

            //Collecting data to be uploaded to database
            array_push($roomArray,array(
                "coor"      => "",
                "status"    => "hilight-empty",
                "roomMap"   => $roomMap,
                "roomImage" => $roomPath . "/" . $roomImageName[$key],
                "deviceCoor"=> []
            ));
        }

        //Save floor to database
        $floorMapData = array();
        $floorMapData['roomMap'] = $roomArray;
        $floorMapData['floorImage'] = $floorPath . "/" . $floorImageName;
        $floor = new FLoor();
        $floor->FLOOR_NAME = $floorName;
        $floor->FLOOR_MAP_DATA =  $floorMapData;
        $floor->save();

        $instruction = 'Added a Floor :'. $floorName;
        $this->auditLogs($ip,$host,$module,$instruction);

        //Save rooms to database
        foreach($roomArray as $key => $room){

            $roomMap = explode(".",$roomImageName[$key])[0];

            $room = new Room();
            $room->FLOOR_ID = $floor->FLOOR_ID;
            $room->ROOM_NAME = $roomName[$key];
            $room->ROOM_MAP_DATA = array('ROOM_MAP' => $roomMap);
            $room->save();
        }

        return $floor;
    }

    /**
     * <Layer number> (2.0) Add new room to floor and image
     * <Processing name> addRoom
     *            URL: http://localhost/api/
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function addRoom(Request $request){
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $request->USERNAME;
        $module = 'Floor management';
        $instruction = 'Added a Room: '.$request->roomName;
        $this->auditLogs($ip,$host,$module,$instruction);

        $floorid = $request->floorId;
        $roomPath="imgs/rooms";
        $floor = Floor::with('rooms')->where('FLOOR_ID',$floorid)->first();
        $floorRoomData = $floor['FLOOR_MAP_DATA'];
        $floorRooms = $floor['rooms'];
        $length = count($floorRooms) - 1;
        $validate = 0;

        for($i=0;$i<=$length;$i++){
            if ($floorRooms[$i]['ROOM_NAME'] == $request->roomName) {
                $validate++;
            }
        }

        if($validate == 0){
            // return $floorRoomData;
            $roomImageFile = $request->roomImageFile;
            // Upload Floor Image to specified location
            $roomImageFile->move(public_path($roomPath),
                $request->roomImageName);
            $roomDetails = array();
            $roomMap = explode(".",$request->roomImageName)[0];
            array_push($roomDetails,array(
                "coor"=> "",
                "status"=>"hilight-empty",
                "roomMap"=>$roomMap,
                "roomImage"=>$roomPath."/".$request->roomImageName,
                "deviceCoor"=>[]
            ));
            array_push($floorRoomData['roomMap'],$roomDetails[0]);
            $floor['FLOOR_MAP_DATA'] = $floorRoomData;
            $floor->save();

            $newRoom = new Room();
            $newRoom->FLOOR_ID = $floorid;
            $newRoom->ROOM_NAME = $request->roomName;
            $newRoom->ROOM_MAP_DATA = array('ROOM_MAP'=>$roomMap);
            $newRoom->save();
            return $newRoom;
        }else{
            return 'duplicate';
        }
    }
}
