<?php
/*
* <System Name> iBMS
* <Program Name> ElectricMeter.php
*
* <Create> 2018.09.25 TP Robert
*
*/
namespace App;

use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoinTrait;
use Illuminate\Database\Eloquent\Model;
/**
 * <Class Name> ElectricMeter
 *
 * <Overview> Class that interacts with the database.
 */
class ElectricMeter extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (0.0) Constructor
    // floor                           (1.0) Get one-to-one belongsTo relationship
    // room                            (2.0) Get one-to-one belongsTo relationship
    // gateway                         (3.0) Get one-to-one belongsTo relationship
    // getSearchableColumns            (4.0) Get columns that will be used for searching
    use EloquentJoinTrait;
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
     * The columns that will be used for searching
     *
     * @var array
     */
    protected $searchableColumns = [
        'SERIAL_NO', 'SLAVE_ID'
    ];
    /**
     * <Layer number> (0.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        parent::__construct();

        $this->primaryKey = 'METER_ID';       // The primary key for the model.
        $this->table = env('DB_ELECTRIC_METER');       // The table associated with the model.
    }
    /**
     * <Layer number> (1.0) Get one-to-one belongsTo relationship
     * <Processing name> floor
     * <Function> This device belongs to floor
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function floor()
    {
        return $this->belongsTo('App\Floor', 'FLOOR_ID' ,'FLOOR_ID');
    }

    /**
     * <Layer number> (2.0) Get one-to-one belongsTo relationship
     * <Processing name> room
     * <Function> This device belongs to room
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function room()
    {
        return $this->belongsTo('App\Room', 'ROOM_ID' ,'ROOM_ID');
    }

    /**
     * <Layer number> (3.0) Get one-to-one belongsTo relationship
     * <Processing name> gateway
     * <Function> This device belongs to gateway
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function gateway()
    {
        return $this->belongsTo('App\Gateway', 'GATEWAY_ID' ,'GATEWAY_ID');
    }
    /**
     * <Layer number> (4.0) Get columns that will be used for searching
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
