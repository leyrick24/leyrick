<?php
/*
* <System Name> iBMS
* <Program Name> User.php
*
* <Create> 2018.07.09 TP Bryan
* <Update> 2018.07.10 TP Bryan    Defined table/primaryKey property through constructor
* <Update> 2018.11.15 TP Raymond    Add coded for modules
*
*/

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * <Class Name> User
 *
 * <Overview> Class that interacts with the database.
 */
class User extends Authenticatable
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // getAuthPassword                  (1.0) Get password
    // authUserFloor                    (2.0) Get one-to-many hasMany relationship
    // authModules                      (3.0) Get one-to-many hasMany relationship

    use Notifiable;

    public function __construct()
    {
        $this->primaryKey = 'USER_ID';
        $this->table = env('DB_USER');
    }

    /**
     * The primary key for the model.
     * (Extended from Model class)
     *
     * @var string
     */
    // protected $primaryKey = 'USER_ID';

    /**
     * The table associated with the model.
     * (Extended from Model class)
     *
     * @var string
     */
    // protected $table = 'M001_USERS';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'PASSWORD', 'REMEMBER_TOKEN',
    ];

    /**
     * <Layer number> (1.0) Get password
     * <Processing name> getAuthPassword
     * <Function> 
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->PASSWORD;
    }
    /**
     * <Layer number> (2.0) Get one-to-many hasMany relationship
     * <Processing name> authUserFloor
     * <Function> 
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function authUserFloor()
    {
        return $this->hasMany('App\AuthLocation', 'USER_ID');
    }


    /**
     * <Layer number> (3.0) Get one-to-many hasMany relationship
     * <Processing name> authModules
     * <Function> This device has many processed data
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function authModules()
    {
        return $this->hasMany('App\AuthModule','USER_ID');
    }


}
