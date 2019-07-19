<?php
/*
* <System Name> iBMS
* <Program Name> AuditLogs.php
*
* <Create> 2018.12.17 TP Yani
* <Update>
*
*/
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> Log
 *
 * <Overview> Configuration to access the Database for Log.
 */
class AuditLogs extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (0.0) Constructor

    /**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = 'CREATED_AT';

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = 'UPDATED_AT';

    /**
     * <Layer number> (0.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        parent::__construct();

        $this->primaryKey = 'AUDIT_LOGS_ID';       // The primary key for the model.
        $this->table = env('DB_AUDIT_LOGS');       // The table associated with the model.
    }

}
