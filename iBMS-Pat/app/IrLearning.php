<?php
/*
* <System Name> iBMS
* <Program Name> Device.php
*
* <Create> 2018.10.23 TP Yani
* <Update> 2018.10.23 TP Yani
*          2019.06.11 TP Harvey   Applying Coding Standard
*
*/

namespace App;

use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoinTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> Device
 *
 * <Overview> Class that interacts with the database.
 */
class IrLearning extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (0.0) Constructor
    // device                          (1.0) Get one-to-one belongsTo relationship
    // getSearchableColumns            (2.0) Get columns that will be used for searching

    use EloquentJoinTrait;

    /**
     * <Layer number> (0.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        parent::__construct();
        $this->primaryKey = 'IR_LEARNING_LIST_ID';        // The primary key for the model.
        $this->table = env('DB_IR_LEARNING_LIST');        // The table associated with the model.
    }

    /**
     * The columns that will be used for searching
     *
     * @var array
     */
    protected $searchableColumns = [
        'LEARNING_NAME', 'LEARNING_VALUE'
    ];

    /**
     * <Layer number> (1.0) Get one-to-one belongsTo relationship
     * <Processing name> device
     * <Function> This IrLearning belongs to device
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function device()
    {
        return $this->belongsTo('App\Device', 'DEVICE_ID' ,'DEVICE_ID');
    }

    /**
     * <Layer number> (2.0) Get columns that will be used for searching
     * <Processing name> getSearchableColumns
     * <Function> *Note: Some columns may not be used for search queries, such
     *                   as primary keys
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function getSearchableColumns()
    {
        return $this->searchableColumns;
    }
}
