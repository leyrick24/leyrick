<?php
/*
* <System Name> iBMS
* <Program Name> Notification.php
*
* <Create> 2018.08.29 TP Robert
*          2019.06.11 TP Harvey   Applying Coding Standard
*
*/
namespace App;

use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoinTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> Notification
 *
 * <Overview> Class that interacts with the database.
 */
class Notification extends Model
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

        $this->primaryKey = 'NOTIFICATION_ID';       // The primary key for the model.
        $this->table = env('DB_NOTIFICATION');       // The table associated with the model.
    }
}


