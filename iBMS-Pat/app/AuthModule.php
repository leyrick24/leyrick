<?php
/*
* <System Name> iBMS
* <Program Name> AuthModule.php
*
* <Create> 2018.11.15 TP Raymond
*
*/
namespace App;

use Illuminate\Database\Eloquent\Model;
use App\SystemModule;
/**
 * <Class Name> AuthLocation
 *
 * <Overview> Class that interacts with the database.
 */
class AuthModule extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (0.0) Constructor
    // users                           (1.0) Get one-to-one belongsTo relationship
    // modules                         (2.0) Get one-to-one belongsTo relationship

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
        $this->primaryKey = 'AUTH_MODULE_ID';
         // The table associated with the model.
        $this->table = env('DB_AUTH_MODULE');
    }

     /**
     * <Layer number> (1.0) Get one-to-one belongsTo relationship
     * <Processing name> users
     * <Function> This is belongs to auth users
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    
    public function users()
    {
        return $this->belongsTo('App\USER','USER_ID');
    }

    /**
     * <Layer number> (2.0) Get one-to-one belongsTo relationship
     * <Processing name> modules
     * <Function> This is belongs to auth modules
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function modules()
    {
        return $this->belongsTo('App\SystemModule','MODULE_ID');
    }
}
