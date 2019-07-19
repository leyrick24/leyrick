<?php
/*
* <System Name> iBMS
* <Program Name> DeviceGraphController.php
*
* <Create> 2018.07.27 TP Harvey
* <Update> 
*
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Device;
use App\ProcessedData;
use App\Traits\CommonFunctions;

/**
 * <Class Name> DeviceGraphController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class DeviceGraphController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // dataByMonth                      (1.0) Retrieve data by Month

    /**
     * <Layer number> (1.0) Retrieve data by Month
     * <Processing name> dataByMonth
     * <Function> Retrieve data by Month
     *            URL: 
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return $data
     */
    public function dataByMonth(Request $request, $id)
    {
        $deviceID = $id;
        $data = ProcessedData::where('DEVICE_ID', $deviceID)
            	->groupBy('date')
            	->orderBy('date', 'ASC')
            	->get(array(
                	DB::raw('Date(created_at) as date'),
                	DB::raw('COUNT(*) as "value"')
            ));
        return $data;
    }
}
