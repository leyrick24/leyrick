<?php
/*
* <System Name> iBMS
* <Program Name> Instruction.php
*
* <Create> 2018.06.20 TP Bryan
* <Update> 2018.07.10 TP Bryan    Defined table/primaryKey property through constructor
*          2018.08.20 TP Bryan    Fixed code structure
*          2019.06.11 TP Harvey   Applying Coding Standard
*
*/

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> Instruction
 *
 * <Overview> Class that interacts with the database.
 */
class Instruction extends Model
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                     (0.0) Constructor
    // device                          (1.0) Get one-to-many belongsTo relationship
    // user                            (2.0) Get one-to-many belongsTo relationship

    /**
     * <Layer number> (0.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
    public function __construct()
    {
        $this->primaryKey = 'INSTRUCTION_ID';        // The primary key for the model.
        $this->table = env('DB_INSTRUCTION');        // The table associated with the model.
    }

    /**
     * <Layer number> (1.0) Get one-to-many belongsTo relationship
     * <Processing name> device
     * <Function> This instruction belongs to device
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function device()
    {
        return $this->belongsTo('App\Device', 'DEVICE_ID' ,'DEVICE_ID');
    }

    /**
     * <Layer number> (2.0) Get one-to-many belongsTo relationship
     * <Processing name> user
     * <Function> This instruction belongs to user
     *
     * @return Illuminate\Database\Eloquent\Model
     */
    public function user()
    {
        return $this->belongsTo('App\User', 'USER_ID' ,'USER_ID');
    }
}