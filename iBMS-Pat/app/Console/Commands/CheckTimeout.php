<?php
/*
* <System Name> iBMS
* <Program Name> CheckTimeout.php
*
* <Create> 2018.XX.XX TP Yani
*
*/

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * <Class Name> CheckTimeout
 * <Overview> Check timeout
 */
class CheckTimeout extends Command
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                      (1.0) Create a new command instance
    // handle                           (2.0) Execute the console command
    // checkSession                     (3.0) Check the session

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:Timeout';

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
        do{
            try {
                $tryAgain = false;
                /* Do everything that throws error here */
                $this->checkSession();
            }
            catch(\Exception $e) {
                $tryAgain = true;
                /* Do error reporting/archiving/logs here */
                app('App\Traits\CommonFunctions')->storeLogs(4, 'Automatic', $e->getMessage(), '', '');
            }
        }while($tryAgain);
    }

    /**
     * <Layer number> (3.0) Check the session
     * <Processing name> checkSession
     *
     * @return 
     */
    public function checkSession(){
        app('App\Http\Controllers\SessionController')->expireSession();
    }
}
