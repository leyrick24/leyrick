<?php
/*
* <System Name> iBMS
* <Program Name> ProcessedData.php
*
* <Create> 2018.06.26 TP Bryan
* <Update> 2018.07.10 TP Bryan    Defined table/primaryKey property through constructor
*          2018.08.20 TP Bryan    Fixed code structure
*
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> ProcessedData
 *
 * <Overview> Class that interacts with the database.
 */
class ProcessedData extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (1.0) Constructor
    // device                          (2.0) Get one-to-one belongsTo relationship

    /**
     * <Layer number> (1.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        $this->primaryKey = 'PROCESSED_DATA_ID';        // The primary key for the model.
        $this->table = env('DB_PROCESSED_DATA');        // The table associated with the model.

        $this->casts = [
            'DATA' => 'array'
        ];

    }

    /**
     * <Layer number> (2.0) Get one-to-one belongsTo relationship
     * <Processing name> device
     * <Function> This processed data belongs to device
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function device()
    {
    	return $this->belongsTo('App\Device', 'DEVICE_ID' ,'DEVICE_ID');
    }
}
