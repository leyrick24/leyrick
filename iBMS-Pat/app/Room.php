<?php
/*
* <System Name> iBMS
* <Program Name> Room.php
*
* <Create> 2018.06.25 TP Bryan
* <Update> 2018.07.02 TP Bryan    Added "Processing Hierarchy"
*          2018.07.10 TP Bryan    Defined table/primaryKey property through constructor
*          2018.08.20 TP Bryan    Fixed code structure
*          2018.12.13 TP Robert   add new fucntion for register gateway and devices
*
*/

namespace App;

use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoinTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> Room
 *
 * <Overview> Class that interacts with the database.
 */
class Room extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (1.0) Constructor
    // floor                           (2.0) Get one-to-many belongsTo relationship
    // gateways                        (3.0) Get one-to-many hasMany relationship
    // sysRegGateways                  (4.0) Get one-to-many hasMany relationship where gateways is register and manufacturer is wulian
    // devices                         (5.0) Get one-to-many hasMany relationship
    // regDevices                      (6.0) Get one-to-many hasMany relationship where device is register
    // getSearchableColumns            (7.0) Get columns that will be used for searching
    // onDevices                       (8.0) Get one to many hasMany relationship where Device is online
    // meters                          (9.0) Get one-to-many hasMany relationship where E Meter is registered

    use EloquentJoinTrait;

    /**
     * <Layer number> (1.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        $this->primaryKey = 'ROOM_ID';        // The primary key for the model.
        $this->table = env('DB_ROOM');        // The table associated with the model.
        $this->casts = [                      // The attributes that should be cast to native types.
            'ROOM_MAP_DATA' => 'array'
        ];
    }

    /**
     * The columns that will be used for searching
     *
     * @var array
     */
    protected $searchableColumns = [
        'ROOM_NAME'
    ];


    //This will not input UPDATED_AT/CREATED_AT on our Database
    public $timestamps = false;


    /**
     * <Layer number> (2.0) Get one-to-many belongsTo relationship
     * <Processing name> floor
     * <Function> This room belongs to floor
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function floor()
    {
        return $this->belongsTo('App\Floor', 'FLOOR_ID', 'FLOOR_ID');
    }

    /**
     * <Layer number> (3.0) Get one-to-many hasMany relationship
     * <Processing name> gateways
     * <Function> This room has (belongs to) many gateways
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function gateways()
    {
        return $this->hasMany('App\Gateway', 'ROOM_ID');
    }

    /**
     * <Layer number> (4.0) Get one-to-many hasMany relationship where gateways is register and manufacturer is wulian
     * <Processing name> gateways
     * <Function> This room has (belongs to) many gateways
     * <Parameter> REG_FLAG = 1, MANUFACTURER_ID =1
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function sysRegGateways()
    {
        return $this->hasMany('App\Gateway', 'ROOM_ID')->where([['REG_FLAG',1],['MANUFACTURER_ID',1]]);
    }
    /**
     * <Layer number> (5.0) Get one-to-many hasMany relationship
     * <Processing name> devices
     * <Function> This room has many devices
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function devices()
    {
        return $this->hasMany('App\Device', 'ROOM_ID');
    }
    /**
     * <Layer number> (6.0) Get one-to-many hasMany relationship where device is register
     * <Processing name> devices
     * <Function> This room has many devices
     * <Parameter> REG_FLAG = 1
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function regDevices()
    {
        return $this->hasMany('App\Device', 'ROOM_ID')->where('REG_FLAG',1);
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

    /**
     * <Layer number> (8.0) Get one-to-many hasMany relationship where device is online
     * <Processing name> onDevices
     * <Function> This room has many devices
     * <Parameter> ONLINE_FLAG = 1
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function onDevices()
    {
        return $this->hasMany('App\Device', 'ROOM_ID')->where('ONLINE_FLAG',1);
    }

    /**
     * <Layer number> (9.0) Get one-to-many hasMany relationship where E Meter is registered to Room
     * <Processing name> meters
     * <Function> This room has many meters
     * <Parameter> REG_FLAG = 1
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function meters()
    {
        return $this->hasMany('App\ElectricMeter', 'ROOM_ID')->where('REG_FLAG',1);
    }
}
