<?php
/*
* <System Name> iBMS
* <Program Name> Code.php
*
* <Create> 2018.06.20 TP Bryan
* <Update> 2018.06.25 TP Bryan
*          2018.07.10 TP Bryan    Defined table/primaryKey property through constructor
*          2018.08.20 TP Bryan    Fixed code structure
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> Code
 *
 * <Overview> Class that interacts with the database.
 */
class Code extends Model
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
    public function __construct()
    {
        $this->primaryKey = 'CODE_ID';      // The primary key for the model.
        $this->table = env('DB_CODE');      // The table associated with the model.
    }
}
