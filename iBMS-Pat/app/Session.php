<?php
/*
* <System Name> iBMS
* <Program Name> Session.php
*
* <Create> 2019.01.23 TP 
*/
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> Session
 *
 * <Overview> Class that interacts with the database.
 */
class Session extends Model
{
	/*******************************************************************/
	/* Processing Hierarchy                                            */
	/*******************************************************************/
	// __construct                     (1.0) Constructor
	
	/**
     * <Layer number> (1.0) Constructor
     * <Processing name> __construct
     * <Function> Reassign properties inherited from parent class
     */
	public function __construct()
	{
			$this->primaryKey = 'id';
			$this->table = env('DB_SESSION');
	}
}
