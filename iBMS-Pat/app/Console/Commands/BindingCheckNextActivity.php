<?php
/*
* <System Name> iBMS
* <Program Name> BindingCheckNextActivity.php
*
* <Create> 2018.XX.XX TP Yani
*
*/

namespace App\Console\Commands;

use Illuminate\Console\Command;

/**
 * <Class Name> BindingCheckNextActivity
 * <Overview> Check binding schedule
 */
class BindingCheckNextActivity extends Command
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                      (1.0) Create a new command instance
    // handle                           (2.0) Execute the console command
    // getActivity                      (3.0) Retrieve binding from database

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:nextActivity';

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
                $this->getActivity();
            }
            catch(\Exception $e) {
                $tryAgain = true;
                /* Do error reporting/archiving/logs here */
                app('App\Traits\CommonFunctions')->storeLogs(4, 'Automatic',
                    $e->getMessage(), '', '');
            }
        }while($tryAgain);
    }

    /**
     * <Layer number> (3.0) Retrieve all bindings from database
     * <Processing name> getActivity
     * <Function> 
     *
     * @return 
     */
    public function getActivity(){
        app('App\Http\Controllers\BindingController')->checkNextActivity();
    }
}
