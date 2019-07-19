<?php
/*
* <System Name> iBMS
* <Program Name> Device.php
*
* <Create> 2018.06.20 TP Bryan
* <Update> 2018.06.25 TP Bryan    Added floor()
*          2018.06.29 TP Bryan    Fixed comments
*          2018.07.02 TP Bryan    Added "Processing Hierarchy"
*          2018.07.10 TP Bryan    Defined table/primaryKey property through constructor
*          2018.07.30 TP Bryan    Added "casts" property
*          2018.08.20 TP Bryan    Fixed code structure
*          2018.11.20 TP Robert   Add New Function for device bindings
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
class Device extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (0.0) Constructor
    // floor                           (1.0) Get one-to-one belongsTo relationship
    // room                            (2.0) Get one-to-one belongsTo relationship
    // gateway                         (3.0) Get one-to-one belongsTo relationship
    // processedData                   (4.0) Get one-to-many hasMany relationship
    // bindings                        (5.0) Get one-to-many hasMany relationship
    // deviceBindings                  (6.0) Get one-to-many hasMany relationship
    // getSearchableColumns            (7.0) Get columns that will be used for searching

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
        $this->primaryKey = 'DEVICE_ID';
        // The table associated with the model.
        $this->table = env('DB_DEVICE');
        // The attributes that are mass assignable.
        $this->fillable = [
            'ROOM_ID'
        ];
         // The attributes that should be cast to native types.
        $this->casts = [                       
            'DATA' => 'array'
        ];
    }

    /**
     * The columns that will be used for searching
     *
     * @var array
     */
    protected $searchableColumns = [
        'DEVICE_SERIAL_NO', 'DEVICE_TYPE', 'DEVICE_NAME'
    ];

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
     * <Layer number> (4.0) Get one-to-many hasMany relationship
     * <Processing name> processedData
     * <Function> This device has many processed data
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function processedData()
    {
        return $this->hasMany('App\ProcessedData', 'DEVICE_ID');
    }

    /**
     * <Layer number> (5.0) Get one-to-many hasMany relationship
     * <Processing name> bindings
     * <Function> This device has many bindings
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function bindings()
    {
        return $this->hasMany('App\Binding', 'SOURCE_DEVICE_ID');
    }

    /**
     * <Layer number> (6.0) Get one-to-many hasMany relationship
     * <Processing name> deviceBindings
     * <Function> This device has many bindings
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function deviceBindings()
    {
        return $this->hasMany('App\Binding', 'TARGET_DEVICE_ID');
    }

    /**
     * <Layer number> (7.0) Get columns that will be used for searching
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
