<?php
/*
* <System Name> iBMS
* <Program Name> SaveElectricMeter.php
*
* <Create> 2018.XX.XX TP Yani
*
*/

namespace App\Console\Commands;
use App\ProcessedData;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

/**
 * <Class Name> SaveElectricMeter
 * <Overview> Save the electric meter data
 */
class SaveElectricMeter extends Command
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // __construct                      (1.0) Create a new command instance
    // handle                           (2.0) Execute the console command
    // getElectricData                  (3.0) Get the electric meter data

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'save:electricMeter';

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
                $data = array("Electric Meter"=>"Data Sent");
                $this->getElectricData();
            }
            catch(Exception $e) {
                $tryAgain = true;
                /* Do error reporting/archiving/logs here */
                $data = array("Electric Meter"=>$e);
            }
        }while($tryAgain);

        $processedData = new ProcessedData;
        $processedData->DEVICE_ID = 1;
        $processedData->DATA = $data;
        $processedData->SEND_FLAG = 0;
        $processedData->save();
    }

    /**
     * <Layer number> (3.0) Get the electric meter data
     * <Processing name> getElectricData
     * <Function> 
     *
     * @return 
     */
    public function getElectricData(){
        app('App\Http\Controllers\ModBusController')->saveModBusData();
    }
}
