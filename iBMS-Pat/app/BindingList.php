<?php
/*
* <System Name> iBMS
* <Program Name> BindingList.php
*
* <Create> 2018.07.26 TP Bryan
* <Update> 2018.07.27 TP Bryan    Fixed code structure
*
*/

namespace App;

use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoinTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> BindingList
 *
 * <Overview> Class that interacts with the database.
 */
class BindingList extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (0.0) Constructor
    // bindings                        (1.0) Get one-to-many hasMany relationship
    // sourceDevices                   (2.0) Get one-to-many hasMany relationship
    // targetDevices                   (3.0) Get one-to-many hasMany relationship

    use EloquentJoinTrait;

    /**
     * <Layer number> (0.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        parent::__construct();

        $this->primaryKey = 'BINDING_LIST_ID';      // The primary key for the model.
        $this->table = env('DB_BINDING_LIST');       // The table associated with the model.
    }

    /**
     * <Layer number> (1.0) Get one-to-many hasMany relationship
     * <Processing name> bindings
     * <Function> This binding list has (belongs to) many target devices
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function bindings()
    {
        return $this->hasMany('App\Binding', 'BINDING_LIST_ID',
                        'BINDING_LIST_ID');
    }

    /**
     * <Layer number> (2.0) Get one-to-many hasMany relationship
     * <Processing name> sourceDevices
     * <Function> This binding list has (belongs to) many target devices
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function sourceDevices()
    {
        return $this->hasManyThrough(
                        'App\Device',           // Has Many
                        'App\Binding',          // Through
                        'BINDING_LIST_ID',      // Foreign key on Binding
                        'DEVICE_ID',            // Foreign key on Device
                        'BINDING_LIST_ID',      // Primary key on (this) Device
                        'SOURCE_DEVICE_ID'      // Primary key on Binding
                    );
    }

    /**
     * <Layer number> (3.0) Get one-to-many hasMany relationship
     * <Processing name> targetDevices
     * <Function> This binding list has many target devices
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function targetDevices()
    {
        return $this->hasManyThrough(
                        'App\Device',           // Has Many
                        'App\Binding',          // Through
                        'BINDING_LIST_ID',      // Foreign key on Binding
                        'DEVICE_ID',            // Foreign key on Device
                        'BINDING_LIST_ID',      // Primary key on (this) Device
                        'TARGET_DEVICE_ID'      // Primary key on Binding
                    );
    }
}