<?php
/*
* <System Name> iBMS
* <Program Name> MonthlyResetEMeterData.php
*
* <Create> 2018.XX.XX TP Yani
*
*/

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Request;

/**
 * <Class Name> MonthlyResetEMeterData
 * <Overview> Check Emeter Monthly data
 */
class MonthlyResetEMeterData extends Command
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                      (1.0) Create a new command instance
    // handle                           (2.0) Execute the console command
    // resetElectricData                (3.0) Reset electric meter data

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:resetEMeterData';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * <Layer number> (1.0) Create a new command instance.
     * <Processing name> __construct
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * <Layer number> (2.0) Execute the console command.
     * <Processing name> handle
     *
     * @return mixed
     */
    public function handle()
    {
        $this->resetElectricData();
    }

    /**
     * <Layer number> (3.0) Reset electric meter data
     * <Processing name> resetElectricData
     *
     * @return mixed
     */
    public function resetElectricData(){
        app('App\Http\Controllers\ModBusController')->monthlyReset();
    }
}
