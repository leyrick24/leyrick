<?php
/*
* <System Name> iBMS
* <Program Name> AuthLocation.php
*
* <Create> 2018.10.5 TP Robert
*
*/
namespace App;

// use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoinTrait;
use Illuminate\Database\Eloquent\Model;
/**
 * <Class Name> AuthLocation
 *
 * <Overview> Class that interacts with the database.
 */
class AuthLocation extends Model
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
        // The primary key for the model.
        $this->primaryKey = 'LOCATION_ID';
        // The table associated with the model.
        $this->table = env('DB_AUTH_LOCATION');
    }
}
