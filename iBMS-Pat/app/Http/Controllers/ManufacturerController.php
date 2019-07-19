<?php
/*
* <System Name> iBMS
* <Program Name> ManufacturerController.php
*
* <Create> 2018.11.05 TP Robert
*
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Manufacturer;
use App\Traits\CommonFunctions;

/**
 * <Class Name> ManufacturerController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class ManufacturerController extends Controller
{
    /**************************************************************************/
    /* Processing Hierarchy                                                   */
    /**************************************************************************/
    // getManufacturerAll                     (1.0) Retrieve all manufacturer from database

    use CommonFunctions;

    /**
     * <Layer number> (1.0) Retrieve all manufacturer from database
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
    public function getManufacturerAll(Request $request)
    {
        return $this->createGetResponse($request,
            (new Manufacturer)->newQuery());
    }
}
