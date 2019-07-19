<?php
/*
* <System Name> iBMS
* <Program Name> GatewayStatus.php
*
* <Create> 2018.XX.XX TP Yani
*
*/

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * <Class Name> GatewayStatus
 * <Overview> Check gateway status
 */
class GatewayStatus extends Command
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
    protected $signature = 'gateway:Status';

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
        app('App\Http\Controllers\GatewayController')->onlineGateway();
    }
}
