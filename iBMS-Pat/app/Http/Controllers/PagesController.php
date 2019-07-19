<?php
/*
* <System Name> iBMS
* <Program Name> PagesController.php
*
* <Create> 2018.07.02 TP Bryan
* <Update> 2018.11.21 TP Raymond Add code for modules
*
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * <Class Name> PagesController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class PagesController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // dashboard                        (1.0) Redirect to dashboard page
    // monitoring                       (2.0) Redirect to monitoring page
    // users                            (3.0) Redirect to user management page
    // gateway                          (4.0) Redirect to gateway management page
    // device                           (5.0) Redirect to device page
    // binding                          (6.0) Redirect to binding page
    // infrared                         (7.0) Redirect to infrared page
    // logs                             (8.0) Redirect to logs page
    // notifications                    (9.0) Redirect to notifications page
    // displayview                      (10.0) Redirect to displayview page
    // floor                            (11.0) Redirect to floor management page
    // about                            (12.0) Redirect to about page
    // help                             (13.0) Redirect to help page

    /**
     * <Layer number> (1.0) Redirect to dashboard page
     * <Processing name> dashboard
     * <Function> Display dashboard page
     *            URL: http://localhost/dashboard
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function dashboard(Request $request)
    {
        return view('home')->with('modules',
            app('App\Http\Controllers\DashboardController')->modules());
    }

    /**
     * <Layer number> (2.0) Redirect to monitoring page
     * <Processing name> monitoring
     * <Function> Display "locations" page
     *            URL: http://localhost/monitoring
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function monitoring(Request $request)
    {

    }

    /**
     * <Layer number> (3.0) Redirect to user management page
     * <Processing name> users
     * <Function>
     *            URL: http://localhost/users
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function users(Request $request)
    {
        try {
            return view('users.index')->with('modules',
                app('App\Http\Controllers\DashboardController')->modules());
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }

    /**
     * <Layer number> (4.0) Redirect to gateway management page
     * <Processing name> gateway
     * <Function>
     *            URL: http://localhost/gateway-management
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function gateway(Request $request)
    {
        return view('hardware.gateway')->with('modules',
            app('App\Http\Controllers\DashboardController')->modules());
    }
    /**
     * <Layer number> (5.0) Redirect to device management page
     * <Processing name> device
     * <Function>
     *            URL: http://localhost/device-management
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function device(Request $request)
    {
        return view('hardware.device')->with('modules',
            app('App\Http\Controllers\DashboardController')->modules());
    }
    /**
     * <Layer number> (6.0) Redirect to binding management page
     * <Processing name> binding
     * <Function>
     *            URL: http://localhost/binding-management
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function binding(Request $request)
    {
        return view('hardware.binding')->with('modules',
            app('App\Http\Controllers\DashboardController')->modules());
    }
    /**
     * <Layer number> (7.0) Redirect to infrared page
     * <Processing name> infrared
     * <Function>
     *            URL: http://localhost/ir-management
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function infrared(Request $request)
    {
        return view('hardware.infrared')->with('modules',
            app('App\Http\Controllers\DashboardController')->modules());
    }

    /**
     * <Layer number> (8.0) Redirect to logs page
     * <Processing name> logs
     * <Function>
     *            URL: http://localhost/logs
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function logs(Request $request)
    {
        return view('logs.index')->with('modules',
            app('App\Http\Controllers\DashboardController')->modules());
    }

    /**
     * <Layer number> (9.0) Redirect to notifications page
     * <Processing name> notifications
     * <Function>
     *            URL: http://localhost/notifications
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function notifications(Request $request)
    {

    }

    /**
     * <Layer number> (10.0) Redirect to Display View page
     * <Processing name> displayview
     * <Function>
     *            URL: http://localhost/displayview
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function displayview(Request $request)
    {
        return view('displayview.index')->with('modules',
            app('App\Http\Controllers\DashboardController')->modules());
    }

    /**
     * <Layer number> (11.0) Redirect to floor View page
     * <Processing name> floor
     * <Function>
     *            URL: http://localhost/floor
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function floor(Request $request)
    {
        return view('floor.index')->with('modules',
            app('App\Http\Controllers\DashboardController')->modules());
    }

    /**
     * <Layer number> (12.0) Redirect to about page
     * <Processing name> about
     * <Function>
     *            URL: http://localhost/about
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function about(Request $request)
    {
        return view('about.index')->with('modules',
            app('App\Http\Controllers\DashboardController')->modules());
    }

    /**
     * <Layer number> (13.0) Redirect to help page
     * <Processing name> help
     * <Function>
     *            URL: http://localhost/help
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\View\View
     */
    public function help(Request $request)
    {
        return view('help.index')->with('modules',
            app('App\Http\Controllers\DashboardController')->modules());
    }

}
