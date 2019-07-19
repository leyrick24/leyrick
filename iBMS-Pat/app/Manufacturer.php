<?php
/*
* <System Name> iBMS
* <Program Name> Manufacturer.php
*
* <Create> 2018.08.08 TP Bryan
* <Update> 2018.08.20 TP Bryan    Fixed code structure
*          2019.06.11 TP Harvey   Applying Coding Standard
*
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> Manufacturer
 *
 * <Overview> Class that interacts with the database.
 */
class Manufacturer extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (0.0) Constructor
    // gateways                        (1.0) Get one-to-many hasMany relationship to gateway
    // devices                         (2.0) Get one-to-many hasMany relationship to device
    // codes                           (3.0) Get one-to-many hasMany relationship to code

    /**
     * <Layer number> (0.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        $this->primaryKey = 'MANUFACTURER_ID';        // The primary key for the model.
        $this->table = env('DB_MANUFACTURER');        // The table associated with the model.
    }

    /**
     * <Layer number> (1.0) Get one-to-many hasMany relationship
     * <Processing name> gateways
     * <Function> This manufacturer has many gateways
     */
    public function gateways()
    {
        return $this->hasMany('App\Device', 'MANUFACTURER_ID');
    }

    /**
     * <Layer number> (2.0) Get one-to-many hasMany relationship
     * <Processing name> devices
     * <Function> This manufacturer has many gateways
     */
    public function devices()
    {
        return $this->hasMany('App\Device', 'MANUFACTURER_ID');
    }

    /**
     * <Layer number> (3.0) Get one-to-many hasMany relationship
     * <Processing name> devices
     * <Function> This manufacturer has many codes
     */
    public function codes()
    {
        return $this->hasMany('App\Code', 'MANUFACTURER_ID');
    }
}
