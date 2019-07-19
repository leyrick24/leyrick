<?php
/*
* <System Name> iBMS
* <Program Name> Binding.php
*
* <Create> 2018.07.26 TP Bryan
* <Update> 2018.07.27 TP Bryan    Fixed code structure
*          2018.08.20 TP Bryan    Fixed code structure
*
*/

namespace App;

use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoinTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> Binding
 *
 * <Overview> Class that interacts with the database.
 */
class Binding extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (0.0) Constructor
    // sourceDevice                    (1.0) Get one-to-one belongsTo relationship
    // targetDevice                    (2.0) Get one-to-one hasOne relationship
    // bindingList                     (3.0) Get one-to-one hasOne relationship
    // learningCommand                 (4.0) Get one-to-one hasOne relationship

    use EloquentJoinTrait;

    /**
     * <Layer number> (0.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        parent::__construct();
        // The primary key for the model.
        $this->primaryKey = 'BINDING_ID';
        // The table associated with the model.
        $this->table = env('DB_BINDING');
    }

    /**
     * <Layer number> (1.0) Get one-to-one belongsTo relationship
     * <Processing name> sourceDevice
     * <Function> This binding belongs to one device
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function sourceDevice()
    {
        return $this->belongsTo('App\Device', 'SOURCE_DEVICE_ID', 'DEVICE_ID');
    }

    /**
     * <Layer number> (2.0) Get one-to-one hasOne relationship
     * <Processing name> targetDevice
     * <Function> This binding has one device
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function targetDevice()
    {
        return $this->hasOne('App\Device', 'DEVICE_ID', 'TARGET_DEVICE_ID');
    }

    /**
     * <Layer number> (3.0) Get one-to-one hasOne relationship
     * <Processing name> bindingList
     * <Function> This binding has one binding list
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function bindingList()
    {
        return $this->hasOne('App\BindingList', 'BINDING_LIST_ID',
                'BINDING_LIST_ID');
    }

    /**
     * <Layer number> (4.0) Get one-to-one hasOne relationship
     * <Processing name> learningCommand
     * <Function> This binding has one learning command
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function learningCommand()
    {
        return $this->hasMany('App\IrLearning', 'SOURCE_DEVICE_ID','TARGET_DEVICE_ID');
    }
}
