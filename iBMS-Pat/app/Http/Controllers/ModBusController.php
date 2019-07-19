<?php
/*
* <System Name> iBMS
* <Program Name> ModBusController.php
*
* <Create> 2018.09.20 TP Robert
* <Update> 2018.09.26 TP Robert Fixed Code in Computation
* <Update> 2018.09.26 TP Robert Fixed Code in Computation
* <Update> 2018.11.08 TP Raymond Fixed Bug in ModBus Data
*/

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PHPModbus\ModbusMaster;
use PHPModbus\PhpType;
use App\Events\ModBusEvent;
use App\EMeterData;
use App\Gateway;
use App\Floor;
use App\ElectricMeter;
use Carbon\Carbon;
use App\Traits\CommonFunctions;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Log;

/**
 * <Class Name> ModBusController
 *
 * <Overview>
 */
class ModBusController extends Controller
{
    /*******************************************************************/
    /* Processing Hierarchy                                            */
    /*******************************************************************/
    // saveModBusData                     (1.0) save the data from modbus
    // monthlyReset                       (2.0) Display the dataa of each electric meter
    // displayMeterData                   (3.0) Display the dataa of each electric meter
    // monthlyConsumption                 (4.0) Get Electric Logs for the whole year
    // indivMeterData                     (5.0) Get the logs of specific e-meter
    // indivMonthly                       (6.0) Get the logs of specific e-meter

    use AuthenticatesUsers;
    use CommonFunctions;

    /**
     * <Layer number> (1.0) Get and save the data from modbus
     * <Processing name> saveModBusData
     * <Function> Get and save the data from modbus (pull request)
     *            URL: http://localhost/api/saveModBusData
     *
     *            Note: API routes need to have some security measure (OAuth tokens?)
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     * @return Illuminate\Http\Response
     */

    public function saveModBusData(){

        //for audit logs
        // $ip = $request->ip();
        // $host = $request->username;
        // $module = 'gateway/device management';
        // $instruction = 'saved Modbus data';
        // $this->auditLogs($ip,$host,$module,$instruction);

        $gateway = Gateway::where('MANUFACTURER_ID',2)->get();
        $modBusProtocol = "TCP";
        $modBusData = array();

        foreach ($gateway as $modBusIP) {
            $IP = $modBusIP->GATEWAY_IP;
            $gatewayID = $modBusIP->GATEWAY_ID;
            $modbus = new ModbusMaster($IP, $modBusProtocol);
            $meters = ElectricMeter::where('GATEWAY_ID',$gatewayID)
                                        ->where('REG_FLAG',1)->get();
            foreach ($meters as $key => $meter) {
                $meterID = $meter->SLAVE_ID;
                // GET Current Month Total Energy Data
                try{
                    // FC 1 Read Data
                    $totalEnergy = $modbus
                                ->readMultipleInputRegisters($meterID, 1560, 1);
                    $totalEnergyBeforeCurrentMonth = $modbus
                                ->readMultipleInputRegisters($meterID, 1802, 1);
                } catch (Exception $e) {
                    // Print error information if any
                }

                if(!is_array($totalEnergy)){
                    Log::info("totalEnergy: ". $totalEnergy);
                    $this->storeLogs(0,'Automatic',$totalEnergy,$IP,
                                        'MeterID '.$meterID);
                }else if(!is_array($totalEnergyBeforeCurrentMonth)){
                    Log::info("totalEnergyBeforeCurrentMonth: ".
                                    $totalEnergyBeforeCurrentMonth);
                    $this->storeLogs(0,'Automatic',
                                    $totalEnergyBeforeCurrentMonth,
                                    $IP,'MeterID '.$meterID);
                }else{

                    Log::info("totalEnergy Read: ");
                    Log::info($totalEnergy);
                    Log::info("totalEnergy Read: ");
                    Log::info($totalEnergyBeforeCurrentMonth);
                    //Convertion of Decimal to Float
                    // Chunk the data array to set of 4 bytes
                    // if you want to concert the data ot float use the
                    //4 = 32 bit if not use 2 = 16 bit
                    $totalEnergyValues = array_chunk($totalEnergy, 4);
                    $totalEnergyBeforeCurrentMonthValues =
                                array_chunk($totalEnergyBeforeCurrentMonth, 4);

                    //get the float data
                    // set parameter 1 or 0
                    foreach($totalEnergyValues as $bytes){
                        $totalEnergy = number_format(PhpType::bytes2float(
                                        $bytes,1), 4, '.', '');
                    }
                    foreach($totalEnergyBeforeCurrentMonthValues as $bytes){
                        $totalEnergyBeforeCurrentMonth =
                            number_format(PhpType::bytes2float($bytes,1),
                                4, '.', '');
                    }

                    //Define variables
                    $startOfMonth = Carbon::now()->startOfMonth();
                    $firstDayCM = Carbon::now()->startOfMonth()
                                    ->format('Y-m-d');
                    $currentDayCM = Carbon::now()->format('Y-m-d');
                    $currentTotalConsumption =  $totalEnergy;
                    $electricMeterData = EMeterData::where('METER_ID',$meterID)
                                    ->orderBy('EMETER_DATA_ID','desc')->first();
                    $firstDayData = EMeterData::where('METER_ID',$meterID)
                                    ->whereDate('created_at', '=',
                                        Carbon::now()->startOfMonth())
                                    ->orderBy('EMETER_DATA_ID','asc')->first();
                    $prevEMeterDataCM = EMeterData::where('METER_ID',$meterID)
                                    ->WhereDate('created_at', 'like',
                                        Carbon::now()->format('Y-m').'%' )
                                    ->whereDate('created_at', '<>',
                                        Carbon::now()->format('Y-m-d'))
                                    ->orderBy('EMETER_DATA_ID','desc')
                                    ->first();
                    $beforeCurrentDay = EMeterData::where('METER_ID',$meterID)
                        ->where('TOTAL_CONSUMPTION_KWH', '<>', '0')
                        ->whereDate('created_at', '<>',
                                        Carbon::now()->format('Y-m-d'))
                        ->orderBy('EMETER_DATA_ID','desc')
                        ->first();

                    Log::info("currentTotalConsumption Values: ");
                    Log::info($currentTotalConsumption);
                    Log::info("totalEnergyBeforeCurrentMonth Values: ");
                    Log::info($totalEnergyBeforeCurrentMonth);

                    //Computation
                    if(empty($currentTotalConsumption) ||
                            $currentTotalConsumption == 0){
                        $currentTotalConsumption = 0.00;
                        $currentDailyConsumption = 0.00;
                        $currentMonthlyConsumption = 0.00;
                    }else{
                        $currentDailyConsumption =
                                (float)$currentTotalConsumption -
                                    (float)$beforeCurrentDay
                                        ->TOTAL_CONSUMPTION_KWH;
                        $currentMonthlyConsumption =
                                (float)$currentTotalConsumption -
                                    (float)$totalEnergyBeforeCurrentMonth;
                    }


                    //Save to DB - EMeterData Table
                    $eMeterData = new EMeterData;
                    $eMeterData->METER_ID = $meterID;
                    $eMeterData->TOTAL_CONSUMPTION_KWH =
                        $currentTotalConsumption;
                    $eMeterData->CURRENT_MONTH_KWH = $currentMonthlyConsumption;

                    //Check if EMeterData Table is empty
                    if((empty($electricMeterData) || $electricMeterData == null)
                        || (empty($firstDayData) || $firstDayData == null)){
                        echo "Step 1\n";
                        //Insert data on the first day of current month
                        $eMeterData->CURRENT_MONTH_KWH = 0;
                        $eMeterData->DAILY_CONSUMPTION_KWH = 0;
                        $eMeterData->TOTAL_CONSUMPTION_KWH = 0;
                        $eMeterData->CREATED_AT = $startOfMonth;
                        $eMeterData->UPDATED_AT = $startOfMonth;
                        $eMeterData->timestamps = false;
                    }
                    //Check if Today is the first day of this current Month
                    else if($currentDayCM == $firstDayCM){
                        //Check if current day is the first record
                        if(empty($beforeCurrentDay) ||
                                    $beforeCurrentDay == null){
                            echo "Step 2.1\n";
                            $eMeterData->CURRENT_MONTH_KWH =
                                            $currentTotalConsumption;
                            $eMeterData->DAILY_CONSUMPTION_KWH =
                                            $currentTotalConsumption;
                        }
                        else {
                            echo "Step 2.2\n";
                            $eMeterData->DAILY_CONSUMPTION_KWH =
                                            $currentDailyConsumption;
                        }
                    }
                    else {
                        echo "Step 3\n";
                        $eMeterData->DAILY_CONSUMPTION_KWH =
                                            $currentDailyConsumption;
                    }
                    $eMeterData->save();
                    Log::info("SAVED METER ". $key . ": ". $eMeterData);
                }
            }
        }
    }

    /**
     * <Layer number> (2.0) Reset the data
     * <Processing name> monthlyReset
     * <Function> Reset the record on the first day of month
     *            URL: http://localhost/api/displayMeterData
     *
     *            Note: API routes need to have some security measure (OAuth tokens?)
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     * @return Illuminate\Http\Response
     */
    public function monthlyReset(){

        $gateway = Gateway::where('MANUFACTURER_ID',2)->get();
        $startOfMonth = Carbon::now()->startOfMonth();

        foreach ($gateway as $modBusIP) {
            $gatewayID = $modBusIP->GATEWAY_ID;
            $meters = ElectricMeter::where('GATEWAY_ID',$gatewayID)
                        ->where('REG_FLAG',1)->get();
            foreach ($meters as $meter) {
                $meterID = $meter->SLAVE_ID;

                $eMeterData = new EMeterData;
                $eMeterData->CURRENT_MONTH_KWH = 0;
                $eMeterData->DAILY_CONSUMPTION_KWH = 0;
                $eMeterData->TOTAL_CONSUMPTION_KWH = 0;
                $eMeterData->CREATED_AT = $startOfMonth;
                $eMeterData->UPDATED_AT = $startOfMonth;
                $eMeterData->METER_ID = $meterID;
                $eMeterData->timestamps = false;
                $eMeterData->save();
            }
        }
    }

    /**
     * <Layer number> (3.0) display daily consumption
     * <Processing name> displayMeterData
     * <Function> display the daily consumption from database
     *            URL: http://localhost/api/displayMeterData
     *
     *            Note: API routes need to have some security measure (OAuth tokens?)
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     * @return Illuminate\Http\Response
     */
    public function displayMeterData(Request $request){
        //Get all ModBus Gateway
        $gateways = Gateway::where('MANUFACTURER_ID',2)->get();
        $result = array();
        foreach ($gateways as $gateway) {
            $gatewayID = $gateway->GATEWAY_ID;
            $floorID = $gateway->FLOOR_ID;
            $perFloorConsumption = 0;
            //Get All Electric Meter per floor
            $meters = ElectricMeter::where('GATEWAY_ID',$gatewayID)
                        ->where('REG_FLAG',1)->get();
            foreach ($meters as $meter) {
                $meterID = $meter->SLAVE_ID;
                //Get the latest daily consumption of electric meter
                $res = EMeterData::where('METER_ID', $meterID)
                        ->latest()->first();
                //Accumalute the daily consumption of electric meter per floor
                $perFloorConsumption += $res->DAILY_CONSUMPTION_KWH;
            }
            $floor = Floor::select('FLOOR_NAME')->where('FLOOR_ID',$floorID)
                        ->first()->FLOOR_NAME;
            //Save the result per floor in json
            $json = array('FLOOR' => $floor, 'METER_DATA' =>
                        number_format((float)$perFloorConsumption, 4, '.', ''));
            array_push($result, $json);
            return $result;
        }
    }

    /**
     * <Layer number> (4.0) Get Electric Logs for the whole year per month
     * <Processing name> monthlyConsumption
     * <Function> Get Electric Logs for the whole year per month (pull request)
     *            URL: http://localhost/monthlyConsumption
     *
     *            Note: API routes need to have some security measure (OAuth tokens?)
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     * @return Illuminate\Http\Response
     */
    public function monthlyConsumption(Request $request){
        $year = date('Y');
        //List all registered ModBus Gateway
        $gateway = Gateway::where('MANUFACTURER_ID',2)->get();
        //Loop all registered ModBus Gateway
        foreach ($gateway as $modBusIP) {
            $gatewayID = $modBusIP->GATEWAY_ID;
            $floorID = $modBusIP->FLOOR_ID;
            //List all registered Electric Meters in a modbus gateway
            $meters = ElectricMeter::where('GATEWAY_ID',$gatewayID)
                                        ->where('REG_FLAG',1)->get();
            $months = [];
            //Loop 12times (January to December)
            for($i=1;$i<=12; $i++){
                $perMonthData = 0;
                // Get data of each electric meter per month
                foreach ($meters as $meter) {
                    $meterID = $meter->SLAVE_ID;
                    //Get the latest reading of electric meter in current month iteration
                    $res = EMeterData::where('METER_ID', $meterID)
                            ->whereMonth('CREATED_AT', '=', $i)
                            ->whereYear('CREATED_AT', '=', $year)
                            ->latest()->first();
                    if(!empty($res) || $res == !null ){
                        //Sum of electric meters
                        $perMonthData += $res->CURRENT_MONTH_KWH;
                        $perMonthData = round($perMonthData, 4);
                    }
                }
                //Random floating number of 1.00 to 10.00 for no month data
                if($perMonthData == 0){
                    // $perMonthData = number_format((float)(1 + mt_rand() / mt_getrandmax() * (10 - 1)), 2, '.', '');
                }
                $json = array('months' => substr(date('F', mktime(0, 0, 0,
                            $i, 1)),0,3), 'floor_data' => $perMonthData);
                array_push($months, $json);
            }
            return $months;
        }
    }

    /**
     * <Layer number> (5.0) Get all the logs of specific e-meter
     * <Processing name> indivMeterData
     * <Function> Get all the logs of specific e-meter
     *
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     * @return Illuminate\Http\Response
     */
    public function indivMeterData(Request $request){
        $id = $request->SLAVE_ID;
        $daily = EMeterData::where('METER_ID',$id)
                    ->orderBy('EMETER_DATA_ID', 'DESC')->get();
        return $daily;
    }

    /**
     * <Layer number> (6.0) Get the logs of specific e-meter on current month
     * <Processing name> indivMonthly
     * <Function> Get the logs of specific e-meter on current month
     *
     *
     * @param Illuminate\Http\Request $request
     *        FORM-DATA:
     * @return Illuminate\Http\Response
     */
    public function indivMonthly(Request $request){
        $id = $request->SLAVE_ID;
        $year = date('Y');
        $month = [];
        for($i = 1; $i<=12; $i++){
            $perMonth = 0;
            $res = EMeterData::where('METER_ID',$id)
                    ->whereMonth('CREATED_AT', '=', $i)
                    ->whereYear('CREATED_AT','=',$year)->latest()->first();
            if (!empty($res) || $res == !null) {
                $perMonth += $res->CURRENT_MONTH_KWH;
                $perMonth = round($perMonth, 4);
            }
            $json = array(
                'months' => date('D F d Y H:i:s O',mktime(0,0,0,$i,25)),
                'usage' => $perMonth,
            );
            array_push($month,$json);
        }
        return $month;
    }
}
