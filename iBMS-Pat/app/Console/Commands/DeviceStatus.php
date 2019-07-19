<?php
/*
* <System Name> iBMS
* <Program Name> DeviceStatus.php
*
* <Create> 2018.XX.XX TP Yani
*
*/

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * <Class Name> DeviceStatus
 * <Overview> Check device status
 */
class DeviceStatus extends Command
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                      (1.0) Create a new command instance
    // handle                           (2.0) Execute the console command

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'device:Status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change Status of Device based on Gateway Status';

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
        app('App\Http\Controllers\DeviceController')->onlineDeviceByGateway();
    }
}
