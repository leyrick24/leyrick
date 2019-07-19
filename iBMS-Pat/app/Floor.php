<?php
/*
* <System Name> iBMS
* <Program Name> Floor.php
*
* <Create> 2018.06.25 TP Bryan
* <Update> 2018.06.29 TP Bryan    Added gateways()
*          2018.07.02 TP Bryan    Added "Processing Hierarchy"
*          2018.07.10 TP Bryan    Defined table/primaryKey property through constructor
*          2018.08.08 TP Bryan    Added 4.0
*          2018.08.20 TP Bryan    Fixed code structure
*          2019.06.11 TP Harvey   Applying Coding Standard
*
*/

namespace App;

use Fico7489\Laravel\EloquentJoin\Traits\EloquentJoinTrait;
use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> Floor
 *
 * <Overview> Class that interacts with the database.
 */
class Floor extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // rooms                            (1.0) Get one-to-many hasMany relationship to rooms
    // gateways                         (2.0) Get one-to-many hasMany relationship to gateways
    // devices                          (3.0) Get one-to-many hasMany relationship to devices
    // users                            (4.0) Get many-to-many hasMany relationship to user
    // getSearchableColumns             (5.0) Get columns that will be used for searching

    use EloquentJoinTrait;

    /**
     * <Layer number> (0.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        $this->primaryKey = 'FLOOR_ID';     // The primary key for the model.
        $this->table = env('DB_FLOOR');     // The table associated with the model.
        $this->casts = [                    // The attributes that should be cast to native types.
            'FLOOR_MAP_DATA' => 'array'
        ];
    }

    /**
     * The columns that will be used for searching
     *
     * @var array
     */
    protected $searchableColumns = [
        'FLOOR_NAME'
    ];

    //This will not input UPDATED_AT/CREATED_AT on our Database
    public $timestamps = false;

    /**
     * <Layer number> (1.0) Get one-to-many hasMany relationship to rooms
     * <Processing name> rooms
     * <Function> This floor has many rooms
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function rooms()
    {
        return $this->hasMany('App\Room', 'FLOOR_ID');
    }

    /**
     * <Layer number> (2.0) Get one-to-many hasMany relationship to gateways
     * <Processing name> gateways
     * <Function> This floor has many gateways
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function gateways()
    {
        return $this->hasMany('App\Gateway', 'FLOOR_ID');
    }

    /**
     * <Layer number> (3.0) Get one-to-many hasMany relationship to devices
     * <Processing name> devices
     * <Function> This floor has many devices
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function devices()
    {
        return $this->hasMany('App\Device', 'FLOOR_ID');
    }

    /**
     * <Layer number> (4.0) Get many-to-many hasMany relationship to user
     * <Processing name> users
     * <Function> Many floors has many users
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function users()
    {
        return $this->belongsToMany('App\User',
                                    env('DB_AUTH_LOCATION'),
                                    'FLOOR_ID',
                                    'USER_ID');
    }

    /**
     * <Layer number> (5.0) Get columns that will be used for searching
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
