<?php
/*
* <System Name> iBMS
* <Program Name> SessionController.php
*
* <Create> 2019.01.23 OJT Jethro
*
*
*/
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Session;
use Auth;
use Illuminate\session\DatabaseSessionHandler;
use App\SessionData;
use Illuminate\Support\Facades\URL;
use App\Events\Logout;

/**
 * <Class Name> SessionController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */

class SessionController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // getSession                   (1.0) Get the current session data
    // getId                        (2.0) Get the current id of the user
    // checkSession                 (3.0) Check the session time interval
    // changeLocale                 (4.0) Change the locale language

    /**
     * <Layer number> (1.0) Get the current session data
     * <Processing name> getSession
     * <Function> Get all the session data
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getSession(Request $request)
    {
        // Return request
        return $request->session()->all();
    }

    /**
     * <Layer number> (2.0) Get the current id of the user
     * <Processing name> getId
     * <Function> Get the id of the user
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function getId()
    {
        if (auth()->user()) {
            return auth()->user();
        }
    }

    /**
     * <Layer number> (3.0) Check the session time interval
     * <Processing name> checkSession
     * <Function> Check the session life time
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function checkSession($id)
    {
        $ip = request()->ip();
        $session = SessionData::where('USER_ID', $id)->where('ip_address', $ip)->first();
        // return $session[0]->last_activity - time();
        $idleTime = time() - $session['last_activity'];
        //create cookie of logged out user and store for 5 minutes
        if ($idleTime > (env('SESSION_LIFETIME'))*60) {
            return response('loggedOut')
                    ->cookie('test', 1, 1);
        }else{
            return $idleTime;
        }
        // Return the idletime
        return $idleTime;
    }

    /**
     * <Layer number> (4.0) Change the locale language
     * <Processing name> changeLocale
     * <Function> Change the current language
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function changeLocale(Request $request, $locale)
    {
        $request->session()->put('locale', $locale);
        return $request->session()->all();
    }
}
