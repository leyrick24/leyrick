<?php
/*
* <System Name> iBMS
* <Program Name> LogController.php
*
* <Create>
* <Update> 2018.12.18 TP Harvey 	Method Functions
* <Update> 2018.12.21 TP Harvey 	Add Functions
*
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Traits\CommonFunctions;
use App\Logs;
use App\AuditLogs;
use Validator;

class LogController extends Controller
{
	/**************************************************************************/
    /* Processing Hierarchy                                                   */
    /**************************************************************************/
    // getAll                     			(1.0) Retrieve all logs from database
    // getSystemLogs                        (2.0) Retrieve System Logs from database
	// getAuditTrailLogs                    (3.0) Retrieve AuditTrail Logs from database
	// addSystemLogs                        (4.0) Save System Logs to database

	use CommonFunctions;

    /**
     * <Layer number> (1.0) Retrieve All Logs from database
     * <Processing name> getAll
     * <Function>
     *            URL: http://localhost/logs
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
 	public function getAll(Request $request)
 	{
 		return $this->createGetResponse($request, (new Logs)->newQuery());
 	}

    /**
     * <Layer number> (2.0) Retrieve System Logs from database
     * <Processing name> getSystemLogs
     * <Function>
     *            URL: http://localhost/getSystemLogs
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
 	public function getSystemLogs(Request $request){
 		return $this->createGetResponse($request,(new Logs)->newQuery()
            ->orderBy('LOGS_ID','DESC'));
 	}

    /**
     * <Layer number> (3.0) Retrieve AuditTrail Logs from database
     * <Processing name> getAuditTrailLogs
     * <Function>
     *            URL: http://localhost/getAuditTrailLogs
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
 	public function getAuditTrailLogs(Request $request){
 		return $this->createGetResponse($request,(new AuditLogs)->newQuery()
            ->orderBy('AUDIT_LOGS_ID','DESC'));
 	}

     /**
      * <Layer number> (4.0) Save to System Logs database
      * <Processing name> addSystemLogs
      * <Function>
      *            URL: http://localhost/addSystemLogs
      *            METHOD: POST
      *
      * @param Illuminate\Http\Request $request
      * @return Illuminate\Http\Response
      */
    public function addSystemLogs(Request $request){
        $errorMsgs = $request->ERROR_MESSAGE;
        $ip = $request->ip();
        $username = $request->USERNAME;
        foreach($errorMsgs as $errorMsg){
            if($errorMsg != null){
                $this->storeLogs(4, 'Manual', $errorMsg, $ip, $username);
                echo 'saved system logs';
            }
        }
    }

}
