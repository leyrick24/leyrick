<?php
/*
* <System Name> iBMS
* <Program Name> ModuleController.php
*
* <Create> 2018.11.15 TP Raymond
* <Update>
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SystemModule;
use App\AuthModule;
use App\Traits\CommonFunctions;

/**
 * <Class Name> ModuleController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class ModuleController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    //getModuleAll                      (1.0) Retrieve all Modules

    use CommonFunctions;

    /**
     * <Layer number> (1.0) Retrieve all Modules
     * <Processing name> getModuleAll
     * <Function> Retrieve all Modules
     *            URL: http://localhost/
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getModuleAll(Request $request)
    {
        return $this->createGetResponse($request,
        	(new SystemModule)->newQuery());
    }
}
