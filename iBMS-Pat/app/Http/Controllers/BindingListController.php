<?php
/*
* <System Name> iBMS
* <Program Name> BindingListController.php
*
* <Create> 2018.07.27 TP Bryan
* <Update> 2018.08.13 TP Bryan    Fixed coding standard, Added functions
*          2018.08.20 TP Bryan    Fixed code structure
*
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BindingList;
use App\Device;
use App\Traits\CommonFunctions;

/**
 * <Class Name> BindingListController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 *            * Note: Binding list is a predefined set of binding rules for a
 *                    sensor and device (or possibly device-to-device)
 */
class BindingListController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // getBindingListAll               (1.0) Retrieve all binding list from database
    // getBindingList                  (2.0) Retrieve binding list from database
    // getBindingListBindings          (3.0) Retrieve binding associated with the binding list
    // getBindingListSourceDevices     (4.0) Retrieve devices that match this binding list trigger condition
    // getBindingListTargetDevices     (5.0) Retrieve devices that match this binding list control condition

    use CommonFunctions;

    /**
     * <Layer number> (1.0) Retrieve all binding list from database
     * <Processing name> getBindingListAll
     * <Function>
     *            URL: http://localhost/binding-list
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $requestFZ
     * @return $bindingList
     */
    public function getBindingListAll(Request $request)
    {
        return $this->createGetResponse($request,
            BindingList::with('bindings.sourceDevice',
                    'bindings.targetDevice:DEVICE_ID,DEVICE_TYPE,DEVICE_NAME')
                  ->has('sourceDevices'));
        $bindingList = BindingList::all();
        return $bindingList;
    }

    /**
     * <Layer number> (2.0) Retrieve binding list from database
     * <Processing name> getBindingList
     * <Function>
     *            URL: http://localhost/binding-list/:id
     *            METHOD: GET
     *
     * @return $bindingList
     */
    public function getBindingList(Request $request, $id)
    {
        $bindingList = BindingList::findOrFail($id);
        return $bindingList;
    }

    /**
     * <Layer number> (3.0) Retrieve binding associated with the binding list
     * <Processing name> getBindingListBindings
     * <Function>
     *            URL: http://localhost/binding-list/:id/bindings
     * @param Illuminate\Http\Request $request
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return $bindingList
     */
    public function getBindingListBindings(Request $request, $id)
    {
        $bindingList = BindingList::with('bindings.sourceDevice',
                        'bindings.targetDevice')
                        ->findOrFail($id);
        return $bindingList;
    }

    /**
     * <Layer number> (4.0) Retrieve devices that match this binding list
     *                      trigger condition
     * <Processing name> getBindingListSourceDevices
     * <Function>
     *            URL: http://localhost/binding-list/:id/source-devices
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getBindingListSourceDevices(Request $request, $id)
    {
        return BindingList::findOrFail($id)->sourceDevices;
    }

    /**
     * <Layer number> (5.0) Retrieve devices that match this binding list
     *                      control condition
     * <Processing name> getBindingListTargetDevices
     * <Function>
     *            URL: http://localhost/binding-list/:id/target-devices
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getBindingListTargetDevices(Request $request, $id)
    {
        $bindingList = BindingList::findOrFail($id)->TARGET_DEVICE_TYPE;
        return $this->createGetResponse($request, Device::where('DEVICE_TYPE',
        	$bindingList));
    }
}
