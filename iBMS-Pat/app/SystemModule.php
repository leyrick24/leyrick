<?php
/*
* <System Name> iBMS
* <Program Name> SystemModule.php
*
* <Create> 2018.11.15 TP Raymond
* <Update> 
*
*/
namespace App;

use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoinTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> SystemModule
 *
 * <Overview> Class that interacts with the database.
 */
class SystemModule extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (1.0) Constructor
    // authModules                     (2.0) Constructor

    use EloquentJoinTrait;

    /**
     * <Layer number> (1.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        $this->primaryKey = 'MODULE_ID';     // The primary key for the model.
        $this->table = env('DB_SYSTEM_MODULE');     // The table associated with the model.
    }

    //This will not input UPDATED_AT/CREATED_AT on our Database
    public $timestamps = false;

    /**
     * <Layer number> (2.0) Get many-to-many hasMany relationship
     * <Processing name> authModules
     * <Function> Many modules has many users
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    
    public function authModules()
    {
        return $this->hasMany('App\AuthModule','MODULE_ID');
    }
}
