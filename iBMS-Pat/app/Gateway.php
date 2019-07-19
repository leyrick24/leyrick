<?php
/*
* <System Name> iBMS
* <Program Name> Gateway.php
*
* <Create> 2018.06.20 TP Bryan
* <Update> 2018.06.25 TP Bryan    Fixed comments
*          2018.07.02 TP Bryan    Added "Processing Hierarchy"
*          2018.07.10 TP Bryan    Defined table/primaryKey property through constructor
*          2018.07.10 TP Bryan    Added
*          2018.08.20 TP Bryan    Fixed code structure
*          2019.06.11 TP Harvey   Applying Coding Standard
*
*/

namespace App;

use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoinTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> Gateway
 *
 * <Overview> Class that interacts with the database.
 */
class Gateway extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (0.0) Constructor
    // floor                           (1.0) Get one-to-one belongsTo relationship
    // room                            (2.0) Get one-to-one belongsTo relationship
    // devices                         (3.0) Get one-to-many hasMany relationship
    // getSearchableColumns            (4.0) Get columns that will be used for searching

    use EloquentJoinTrait;

    /**
     * <Layer number> (0.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        $this->primaryKey = 'GATEWAY_ID';        // The primary key for the model.
        $this->table = env('DB_GATEWAY');        // The table associated with the model.
    }

    /**
     * The columns that will be used for searching
     *
     * @var array
     */
    protected $searchableColumns = [
        'GATEWAY_SERIAL_NO', 'GATEWAY_IP', 'GATEWAY_NAME'
    ];

    /**
     * <Layer number> (1.0) Get one-to-one belongsTo relationship
     * <Processing name> floor
     * <Function> This gateway belongs to floor
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function floor()
    {
        return $this->belongsTo('App\Floor', 'FLOOR_ID', 'FLOOR_ID');
    }

    /**
     * <Layer number> (2.0) Get one-to-one belongsTo relationship
     * <Processing name> room
     * <Function> This gateway belongs to room
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function room()
    {
        return $this->belongsTo('App\Room', 'ROOM_ID', 'ROOM_ID');
    }

    /**
     * <Layer number> (3.0) Get one-to-many hasMany relationship
     * <Processing name> devices
     * <Function> This gateways has many devices
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function devices()
    {
        return $this->hasMany('App\Device', 'GATEWAY_ID');
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
