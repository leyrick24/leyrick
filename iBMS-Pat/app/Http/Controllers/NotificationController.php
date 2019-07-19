<?php
/*
* <System Name> iBMS
* <Program Name> NotificationController.php
*
* <Create> 2018.08.29 TP Robert
*
*/
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Events\NotificationEvent;
use App\Notification;
use App\User;
use App\UserNotification;

/**
 * <Class Name> NotificationController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class NotificationController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // insertNotification          		(1.0) Insert new data
    // getNotification		            (2.0) Retrieve data
    // updateNotification		        (3.0) Update Notification
    // logsNotification		            (4.0) Show Notification Logs

    /**
     * <Layer number> (1.0) Insert new data
     * <Processing name> insertNotification
     * <Function>
     *            URL: http://localhost/devices/scan
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function insertNotification(Request $request)
    {
        $notif = new Notification;
        $notif->OBJECT_ID = 1;
        $notif->OBJECT_NAME = $request->OBJECT_NAME;
        $notif->SUBJECT = $request->SUBJECT;
        $notif->CONTENT = $request->CONTENT;
        $notif->ERROR_FLAG = $request->ERROR_FLAG;
        $notif->NOTIFICATION_LINK = $request->NOTIFICATION_LINK;
        $notif->save();

    	$users = User::select('USER_ID')->get();

    	foreach($users as $user){
    		$usernotif = new UserNotification;
    		$usernotif->USER_ID = $user->USER_ID;
    		$usernotif->NOTIFICATION_ID = $notif->NOTIFICATION_ID;
    		$usernotif->SEEN_FLAG = 0;
    		$usernotif->save();
    	}
        $seen = '{"SEEN_FLAG": 0}';
        $push_notif = array("NOTIFICATION" => array_merge(json_decode($notif,
        				true),json_decode($seen, true)));
        event(new NotificationEvent($push_notif));
        return $push_notif;
    }
    /**
     * <Layer number> (2.0) Retrieve data
     * <Processing name> getNotification
     * <Function> Retrieve data
     *            URL: http://localhost/devices/scan
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getNotification(Request $request, $id)
    {
        $date = date('yyyy-mm-dd H:i:s', strtotime("-1 month"));
    	$notif = DB::table('T003_NOTIFICATION')
            ->join('T004_USER_NOTIFICATION',
            	'T003_NOTIFICATION.NOTIFICATION_ID', '=',
            	'T004_USER_NOTIFICATION.NOTIFICATION_ID')
            ->select('T003_NOTIFICATION.*', 'T004_USER_NOTIFICATION.*')
            ->where('T004_USER_NOTIFICATION.USER_ID', $id)
            // ->where('T003_NOTIFICATION.ERROR_FLAG', 4)
            ->where('T003_NOTIFICATION.CREATED_AT', ">" , $date)
            ->orderBy('T003_NOTIFICATION.NOTIFICATION_ID','desc')
            ->get();

        return $notif;
    }
    /**
     * <Layer number> (3.0) Update Notification
     * <Processing name> updateNotification
     * <Function> Update Notification
     *            URL: http://localhost/devices/scan
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function updateNotification(Request $request)
    {
        $updateNotif = UserNotification::where('NOTIFICATION_ID',
        					$request->NOTIFICATION_ID)
                            ->where('USER_ID', $request->USER_ID)->first();

        $updateNotif->SEEN_FLAG = 1;
        try{
            $updateNotif->save();
            return 'success';
        }catch(Exception $e){
            return $e;
        }
    }

    /**
     * <Layer number> (4.0) Show Notification Logs
     * <Processing name> logsNotification
     * <Function> Show Notification Logs
     *            URL: http://localhost/devices/scan
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function logsNotification(Request $request)
    {
        $notif = DB::table('T003_NOTIFICATION')
            ->join('T004_USER_NOTIFICATION',
            	'T003_NOTIFICATION.NOTIFICATION_ID', '=',
            	'T004_USER_NOTIFICATION.NOTIFICATION_ID')
            ->select('T003_NOTIFICATION.*', 'T004_USER_NOTIFICATION.*')
            ->orderBy('T003_NOTIFICATION.NOTIFICATION_ID','desc')
            ->paginate($request->pageLength);

        return $notif;
    }
}
