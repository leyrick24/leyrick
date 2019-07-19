<?php
/*
* <System Name> iBMS
* <Program Name> SessionData.php
*
* <Create> 2019.01.23 OJT Jethro
*/
namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * <Class Name> SessionData
 *
 * <Overview> Class that interacts with the database.
 */
class SessionData extends Model
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
