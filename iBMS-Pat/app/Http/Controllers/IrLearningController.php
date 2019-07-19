<?php
/*
* <System Name> iBMS
* <Program Name> IrLearningController.php
*
* <Create> 2018.09.27 TP Yani
* <Update> 
*
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\IrLearning;
use App\Device;
use App\Traits\CommonFunctions;
use App\ProcessedData;
use App\Events\testBinding;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

/**
 * <Class Name> IrLearningController
 *
 * <Overview> Class that manipulates the model in preparation for data
 *            presentation in the view
 */
class IrLearningController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // getAllIrLearningList             (1.0) Retrieve all ir list from database
    // getIrLearning                    (2.0) Retrieve ir list from database
    // newIrLearning                    (3.0) Create new learning entry to database
    // updateIrLearning                 (4.0) updated IR Learning entry to database
    // getIrTargetList                  (5.0) Retrieve all ir list from database
    // deleteIrRecord                   (6.0) Delete specific record from database

    use AuthenticatesUsers;
    use CommonFunctions;

    /**
     * <Layer number> (1.0) Retrieve all ir list from database
     * <Processing name> getAllIrLearningList
     * <Function>
     *            URL: http://localhost/learning-list/
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return $irList
     */
    public function getAllIrLearningList(Request $request)
    {
        $irList = $this->createGetResponse($request,
                    (new IrLearning)->newQuery());

        return $irList;
    }

    /**
     * <Layer number> (2.0) Retrieve ir list from database
     * <Processing name> getIrLearning
     * <Function>
     *            URL: http://localhost/learning-list/:id
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return $irLearningList
     */
    public function getIrLearning(Request $request, $id)
    {
        $irLearningList = IrLearning::findOrFail($id);

        return $irLearningList;
    }

    /**
     * <Layer number> (3.0) Create new learning entry to database
     * <Processing name> newIrLearning
     * <Function>
     *            URL: http://localhost/learning-list/new
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     * @return Illuminate\Http\Response
     */
    public function newIrLearning(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'IR Learning Management';
        $instruction = 'Created a New IR Learning';
        $this->auditLogs($ip,$host,$module,$instruction);

        $learningLists = $request->LEARNING_LIST;

        foreach($learningLists as $learningList) {
            $learningExist = IrLearning::where('SOURCE_DEVICE_ID',
                                           $learningLists['SOURCE_DEVICE_ID'])
                                   ->where('LEARNING_VALUE',
                                           $learningLists['LEARNING_VALUE'])
                                   ->count();
            if ($learningExist == 0){
                $irLearningList = new IrLearning;
                $irLearningList->SOURCE_DEVICE_ID =
                                    $learningLists['SOURCE_DEVICE_ID'];
                $irLearningList->TARGET_DEVICE =
                                    $learningLists['TARGET_DEVICE'];
                $irLearningList->TARGET_BRAND =
                                    $learningLists['TARGET_BRAND'];
                $irLearningList->LEARNING_NAME =
                                    $learningLists['LEARNING_NAME'];
                $irLearningList->LEARNING_VALUE =
                                    $learningLists['LEARNING_VALUE'];
                $irLearningList->save();
            } else{

            }
        }

        return 'ok';
    }

        /**
     * <Layer number> (4.0) updated IR Learning entry to database
     * <Processing name> updateIrLearning
     * <Function>
     *            URL: http://localhost/learning-list/update
     *            METHOD: POST
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     *              SOURCE_DEVICE_ID => str
     *              TARGET_DEVICE_ID => str
     *              BINDING_LIST_ID => str
     * @return Illuminate\Http\Response
     */
    public function updateIrLearning(Request $request)
    {
        //for audit logs
        $ip = $request->ip();
        $username = auth()->user();
        $host = $username->USERNAME;
        $module = 'IR Learning Management';
        $instruction = 'Updated an IR Learning';
        $this->auditLogs($ip,$host,$module,$instruction);

        $learningLists = $request->LEARNING_LIST;


        $updateLearning = IrLearning::where('SOURCE_DEVICE_ID',
                                       $learningLists['SOURCE_DEVICE_ID'])
                               ->where('LEARNING_VALUE',
                                       $learningLists['OLD_LEARNING_VALUE'])
                               //check what to get
                               ->first();
        $updateLearning->LEARNING_VALUE = $learningLists['LEARNING_VALUE'];
        $updateLearning->TARGET_BRAND = $learningLists['TARGET_BRAND'];
        $updateLearning->TARGET_DEVICE = $learningLists['TARGET_DEVICE'];
        $updateLearning->LEARNING_NAME = $learningLists['LEARNING_NAME'];
        $updateLearning->save();

        return 'ok';
    }

    /**
     * <Layer number> (5.0) Retrieve all ir list from database
     * <Processing name> getIrTargetList
     * <Function>
     *            URL: http://localhost/learning-list/targetList
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function getIrTargetList(Request $request)
    {
        $irList = $this->createGetResponse($request,
                        (new IrLearning)->newQuery());
    }

    /**
     * <Layer number> (6.0) Delete specific record from database
     * <Processing name> deleteIrRecord
     * <Function>
     *            URL: http://localhost/learning-list/targetList
     *            METHOD: GET
     *
     * @param Illuminate\Http\Request $request
     * @return App\Gateway
     */
    public function deleteIrRecord(Request $request)
    {
        $learningLists = $request->LEARNING_LIST;
        $updateLearning = IrLearning::where('SOURCE_DEVICE_ID',
                                       $learningLists['SOURCE_DEVICE_ID'])
                               ->where('TARGET_DEVICE',
                                       $learningLists['TARGET_DEVICE'])
                               ->where('TARGET_BRAND',
                                       $learningLists['TARGET_BRAND'])
                               ->where('LEARNING_NAME',
                                       $learningLists['LEARNING_NAME'])
                               ->first();

        $updateLearning->delete();
    }

}
