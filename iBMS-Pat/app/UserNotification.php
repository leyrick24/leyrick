<?php
/*
* <System Name> iBMS
* <Program Name> UserNotification.php
*
* <Create> 2018.08.29 TP Robert
*
*/
namespace App;

use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoinTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> UserNotification
 *
 * <Overview> Class that interacts with the database.
 */
class UserNotification extends Model
{
	/*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (1.0) Constructor

    /**
     * <Layer number> (1.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        parent::__construct();

        $this->primaryKey = 'USER_NOTIFICATION_ID';       // The primary key for the model.
        $this->table = env('DB_USER_NOTIFICATION');       // The table associated with the model.
        $this->timestamps = false;
    }
}


