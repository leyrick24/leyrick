<?php
/*
* <System Name> iBMS
* <Program Name> EMeterData.php
*
* <Create> 2018.09.24 TP Yani
*
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> EMeterData
 *
 * <Overview> Class that interacts with the database.
 */
class EMeterData extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (0.0) Constructor
    // gateway                         (1.0) Get one-to-one belongsTo relationship

    /**
     * <Layer number> (0.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        $this->primaryKey = 'EMETER_DATA_ID';        // The primary key for the model.
        $this->table = env('DB_EMETER_DATA');        // The table associated with the model.

        // $this->casts = [
        //     'DATA' => 'array'
        // ];

    }

    /**
     * <Layer number> (1.0) Get one-to-one belongsTo relationship
     * <Processing name> gateway
     * <Function> This device belongs to gateway
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function gateway()
    {
        return $this->belongsTo('App\Gateway', 'GATEWAY_ID' ,'GATEWAY_ID');
    }
}
