<?php
/*
* <System Name> iBMS
* <Program Name> Logs.php
*
* <Create> 2018.12.17 TP Robert
*          2019.06.11 TP Harvey   Applying Coding Standard
*
*/
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> Logs
 *
 * <Overview> Class that interacts with the database.
 */

class Logs extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (0.0) Constructor

    /**
     * <Layer number> (0.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    const UPDATED_AT = null;

    public function __construct()
    {
        parent::__construct();

        $this->primaryKey = 'LOGS_ID';      // The primary key for the model.
        $this->table = env('DB_LOGS');      // The table associated with the model.
    }
}
